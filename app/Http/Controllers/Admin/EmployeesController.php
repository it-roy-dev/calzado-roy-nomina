<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\EmployeeDataTable;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\User;
use App\Models\UserFamilyInfo;
use Chatify\Facades\ChatifyMessenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = __("Empleados");
        $employees = User::where('type', UserType::EMPLOYEE)
            ->where(function($q) {
                $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'Tienda'))
                ->orWhereHas('employeeDetail', fn($r) => $r->whereNotNull('oracle_emp_code'));
            })
            ->with(['employeeDetail.designation', 'employeeDetail.store', 'employeeDetail.department'])
            ->get();

        $counts = [
            'total'       => $employees->count(),
            'pendiente'   => $employees->filter(fn($e) => ($e->employeeDetail->status ?? 'PENDIENTE') === 'PENDIENTE')->count(),
            'completo'    => $employees->filter(fn($e) => ($e->employeeDetail->status ?? '') === 'COMPLETO')->count(),
            'dar_de_baja' => $employees->filter(fn($e) => ($e->employeeDetail->status ?? '') === 'DAR_DE_BAJA')->count(),
        ];

        return view('pages.employees.index', compact('pageTitle', 'employees', 'counts'));
    }

    /**
     * Display a listing of the resource.
     */
    public function list(EmployeeDataTable $dataTable)
    {
        $pageTitle = __("Empleados");
        $employees = User::where('type', UserType::EMPLOYEE)
            ->where(function($q) {
                $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'Tienda'))
                ->orWhereHas('employeeDetail', fn($r) => $r->whereNotNull('oracle_emp_code'));
            })
            ->get();

        $counts = [
            'total'       => $employees->count(),
            'pendiente'   => $employees->filter(fn($e) => ($e->employeeDetail->status ?? 'PENDIENTE') === 'PENDIENTE')->count(),
            'completo'    => $employees->filter(fn($e) => ($e->employeeDetail->status ?? '') === 'COMPLETO')->count(),
            'dar_de_baja' => $employees->filter(fn($e) => ($e->employeeDetail->status ?? '') === 'DAR_DE_BAJA')->count(),
        ];

        return $dataTable->render('pages.employees.list', compact('pageTitle', 'counts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::get();
        $designations = Designation::get();
        return view('pages.employees.create', compact(
            'departments',
            'designations'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'middlename' => 'nullable|string',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email,except,id',
            'password' => 'required|string|confirmed',
            'status' => 'required',
        ]);
        $imageName = null;
        if ($request->hasFile('avatar')) {
            $imageName = time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('storage/users'), $imageName);
        }
        $user = User::create([
            'type' => UserType::EMPLOYEE,
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'username' => $request->username,
            'address' => $request->address,
            'country' => $request->country_name,
            'country_code' => $request->country_code,
            'dial_code' => $request->dial_code,
            'phone' => $request->phone,
            'avatar' => $imageName,
            'created_by' => auth()->user()->id,
            'is_active' => !empty($request->status),
            'password' => Hash::make($request->password)
        ]);
        if (!empty($user)) {
            $user->assignRole(UserType::EMPLOYEE);
            $totalEmployees = User::where('type', UserType::EMPLOYEE)->where('is_active', true)->count();
            $empId = "EMP-" . pad_zeros(($totalEmployees + 1));
            
            $detail = EmployeeDetail::create([
                'emp_id'         => $empId,
                'user_id'        => $user->id,
                'department_id'  => $request->department,
                'designation_id' => $request->designation,
                'store_id'       => $request->store_id ?? null,
            ]);

            // Intentar asignar código si tiene los datos mínimos
            $detail->refresh();
            $detail->assignCode();
        }
        $notification = notify(__('Employee has been added'));
        return back()->with($notification);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $employee)
    {
        $id = Crypt::decrypt($employee);
        $user = User::findOrFail($id);
        $employee = $user->employeeDetail;
        $pageTitle = __('Employee Profile');
        return view('pages.employees.show', compact(
            'employee',
            'user',
            'pageTitle'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $employee)
    {
        $userId = Crypt::decrypt($employee);
        $employee = User::findOrFail($userId);
        $departments = Department::get();
        $designations = Designation::get();
        return view('pages.employees.edit', compact(
            'departments',
            'designations',
            'employee'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, User $employee)
        {
            $request->validate([
                'firstname' => 'required',
                'lastname'  => 'required',
                'password'  => 'nullable|string|confirmed',
                'status'    => 'required',
            ]);

            $user      = $employee;
            $imageName = $user->avatar;

            if ($request->hasFile('avatar')) {
                $imageName = time() . '.' . $request->avatar->extension();
                $request->avatar->move(public_path('storage/users'), $imageName);
            }

            $user->update([
                'firstname'    => $request->firstname   ?? $user->firstname,
                'middlename'   => $request->middlename  ?? $user->middlename,
                'lastname'     => $request->lastname    ?? $user->lastname,
                'email'        => $request->email       ?? $user->email,
                'username'     => $request->username    ?? $user->username,
                'address'      => $request->address     ?? $user->address,
                'country'      => $request->country_name ?? $user->country,
                'country_code' => $request->country_code ?? $user->country_code,
                'dial_code'    => $request->dial_code   ?? $user->dial_code,
                'phone'        => $request->phone       ?? $user->phone,
                'avatar'       => $imageName,
                'is_active'    => !empty($request->status) ?? $user->is_active,
                'password'     => !empty($request->password) ? Hash::make($request->password) : $user->password,
            ]);

            if (!empty($user)) {
                if (!$user->hasRole(UserType::EMPLOYEE)) {
                    $user->assignRole(UserType::EMPLOYEE);
                }

                $employeeDetails = $user->employeeDetail;

                if (!empty($employeeDetails) && empty($employeeDetails->emp_id)) {
                    $totalEmployees = User::where('type', UserType::EMPLOYEE)->where('is_active', true)->count();
                    $empId = "EMP-" . pad_zeros(($totalEmployees + 1));
                }

                $detail = EmployeeDetail::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'emp_id'         => $empId ?? ($employeeDetails->emp_id ?? null),
                        'user_id'        => $user->id,
                        'department_id'  => $request->department,
                        'designation_id' => $request->designation,
                    ]
                );

                // Intentar asignar código si aún no tiene
                $detail->refresh();
                $detail->assignCode();
            }

            $notification = notify(__("Employee has been updated"));
            return back()->with($notification);
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        $employee->delete();
        $notification = notify(__("Employee has been deleted"));
        return back()->with($notification);
    }

    public function sync()
    {
        try {
            $output = new \Symfony\Component\Console\Output\BufferedOutput();
            \Illuminate\Support\Facades\Artisan::call('employees:sync', [], $output);
            $result = $output->fetch();

            // Recuperar empleados DAR_DE_BAJA del cache
            $bajaEmployees = cache()->pull('sync_baja_employees', []);

            return response()->json([
                'success'        => true,
                'message'        => 'Sincronización completada',
                'output'         => nl2br(e($result)),
                'baja_employees' => $bajaEmployees,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function expediente(string $employee)
    {
        $userId      = Crypt::decrypt($employee);
        $user        = User::findOrFail($userId);
        $detail      = $user->employeeDetail;
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $stores      = \App\Models\Store::where('is_active', true)
                        ->where('type', 'tienda')
                        ->orderBy('oracle_store_no')
                        ->get();
        $pageTitle   = 'Expediente — ' . $user->fullname;

        // ── Horario en tiempo real ──
        if ($detail->store_id && $detail->oracle_emp_code) {
            // Empleado de tienda — jalar horario del centro comercial
            $numeroTienda = $detail->store->oracle_store_no ?? null;
            if ($numeroTienda) {
                $horarios = \Illuminate\Support\Facades\DB::connection('mysql_roy')
                    ->select("SELECT APERTURA, CIERRE FROM roy_horarios_cc_tiendas WHERE TIENDA = ? ORDER BY APERTURA ASC", [$numeroTienda]);

                if (!empty($horarios)) {
                    $aperturas   = array_column($horarios, 'APERTURA');
                    $cierres     = array_column($horarios, 'CIERRE');
                    $aperturaMin = min($aperturas);
                    $cierreMax   = max($cierres);
                    $detail->work_schedule       = "{$aperturaMin} - {$cierreMax}";
                    $detail->work_hours_per_week = 44;
                }
            }
        } elseif ($detail->department_id && $detail->oracle_emp_code) {
            $horarioAdmin = \Illuminate\Support\Facades\DB::connection('mysql_roy')
                ->select("SELECT HORA_ENTRADA, HORA_SALIDA FROM roy_horarios_admon WHERE CODIGO_EMPLEADO = ? LIMIT 1",
                [$detail->oracle_emp_code]);

            if (!empty($horarioAdmin)) {
                $detail->work_schedule = $horarioAdmin[0]->HORA_ENTRADA . ' - ' . $horarioAdmin[0]->HORA_SALIDA;
                $tieneHorarioAdmin = true;
            } else {
                $tieneHorarioAdmin = false;
            }
        } else {
            $tieneHorarioAdmin = false;
        }

        $tieneHorarioAdmin = $tieneHorarioAdmin ?? false;
        return view('pages.employees.expediente', compact(
            'user', 'detail', 'departments', 'designations', 'stores', 'pageTitle', 'tieneHorarioAdmin'
        ));
        
    }

    public function saveExpediente(Request $request, string $employee)
    {
        $userId = Crypt::decrypt($employee);
        $user   = User::findOrFail($userId);
        $detail = $user->employeeDetail;

        if (!$detail) {
            return back()->with(notify(__('No se encontró el expediente del empleado')));
        }

        // ── Validación de campos únicos ──
        $camposUnicos = [
            'dpi_number'  => ['label' => 'DPI',        'valor' => $request->dpi_number],
            'nit_number'  => ['label' => 'NIT',        'valor' => $request->nit_number],
            'igss_number' => ['label' => 'No. IGSS',   'valor' => $request->igss_number],
            'irtra_number'=> ['label' => 'No. IRTRA',  'valor' => $request->irtra_number],
        ];

        $duplicados = [];

        foreach ($camposUnicos as $campo => $info) {
            if (empty($info['valor'])) continue;

            $duplicado = EmployeeDetail::where($campo, $info['valor'])
                ->where('id', '!=', $detail->id) // excluir el mismo empleado
                ->with('user')
                ->first();

            if ($duplicado) {
                $nombre = $duplicado->user->fullname ?? 'Empleado desconocido';
                $codigo = $duplicado->oracle_emp_code ?? $duplicado->emp_code ?? 'Sin código';
                $duplicados[] = "{$info['label']}: <strong>{$info['valor']}</strong> — ya registrado por: <strong>{$nombre}</strong> (Código: {$codigo})";
            }
        }

        if (!empty($duplicados)) {
            return back()->withInput()->with('duplicados_error', $duplicados);
        }

        // Actualizar datos del expediente
        $detail->update([
            'dpi_number'                => $request->dpi_number,
            'dpi_issued_place'          => $request->dpi_issued_place,
            'nit_number'                => $request->nit_number,
            'igss_number'               => $request->igss_number,
            'irtra_number'              => $request->irtra_number,
            'driver_license'            => $request->driver_license,
            'dob'                       => $request->dob,
            'birth_place'               => $request->birth_place,
            'nationality'               => $request->nationality,
            'marital_status'            => $request->marital_status,
            'religion'                  => $request->religion,
            'ethnicity'                 => $request->ethnicity,
            'no_of_children'            => $request->no_of_children,
            'disability'                => $request->has('disability'),
            'disability_description'    => $request->disability_description,
            'gender'                    => $request->gender,
            'phone_secondary'           => $request->phone_secondary,
            'personal_email'            => $request->personal_email,
            'department_id'             => $request->department_id ?: null,
            'store_id'                  => $request->store_id ?: null,
            'designation_id'            => $request->designation_id ?: null,
            'immediate_supervisor_name' => $request->immediate_supervisor_name,
            'date_joined'               => $request->date_joined,
            'contract_type'             => $request->contract_type,
            'work_schedule'             => $request->work_schedule,
            'work_hours_per_week'       => $request->work_hours_per_week,
            'termination_date'          => $request->termination_date ?: null,
            'termination_reason'        => $request->termination_reason,
            'no_aplica_familia'     => $request->has('no_aplica_familia'),
            'no_aplica_experiencia' => $request->has('no_aplica_experiencia'),
            'emergency_contacts'        => [
                'primary'   => $request->primary ?? null,
                'secondary' => $request->secondary ?? null,
            ],
            'academic_level'            => $request->academic_level,
            'degree_title'              => $request->degree_title,
            'languages'                 => $request->input('languages') ? array_map('trim', explode(',', $request->input('languages'))) : null,
            'worked_abroad'             => $request->has('worked_abroad'),
            'foreign_country'           => $request->foreign_country,
            'foreign_company'           => $request->foreign_company,
            'foreign_job_title'         => $request->foreign_job_title,
            'payment_method'            => $request->payment_method,
            'bank_name'                 => $request->bank_name,
            'bank_account_number'       => $request->bank_account_number,
            'bank_account_type'         => $request->bank_account_type,
        ]);

        // Guardar familiares
        if ($request->familiares) {
            foreach ($request->familiares as $familiarData) {
                if (empty($familiarData['name'])) continue;
                UserFamilyInfo::updateOrCreate(
                    [
                        'id'      => $familiarData['id'] ?? null,
                        'user_id' => $user->id,
                    ],
                    [
                        'user_id'      => $user->id,
                        'name'         => $familiarData['name'],
                        'relationship' => $familiarData['relationship'] ?? null,
                        'dob'          => $familiarData['dob'] ?: null,
                        'phone'        => $familiarData['phone'] ?? null,
                    ]
                );
            }
        }

        // Guardar experiencia laboral
        if ($request->experiencias) {
            foreach ($request->experiencias as $expData) {
                if (empty($expData['company'])) continue;
                \App\Models\EmployeeWorkExperience::updateOrCreate(
                    [
                        'id'                 => $expData['id'] ?? null,
                        'employee_detail_id' => $detail->id,
                    ],
                    [
                        'employee_detail_id' => $detail->id,
                        'company'            => $expData['company'],
                        'position'           => $expData['position'] ?? null,
                        'start_date'         => $expData['start_date'] ?: null,
                        'end_date'           => $expData['end_date'] ?: null,
                        'location'           => $expData['location'] ?? null,
                    ]
                );
            }
        }

        // Guardar salario
        $salaryBasis = $request->salary_basis 
            ? \App\Enums\Payroll\SalaryType::from($request->salary_basis) 
            : \App\Enums\Payroll\SalaryType::from('monthly');

        \App\Models\EmployeeSalaryDetail::updateOrCreate(
            ['employee_detail_id' => $detail->id],
            [
                'employee_detail_id'        => $detail->id,
                'basis'                     => $salaryBasis,
                'base_salary'               => $request->base_salary ?? 0,
                'bonificacion_decreto'      => $request->bonificacion_decreto ?? 250,
                'variable_bonus'            => $request->input('bonificacion_variable') ?? 0,
                'bonus_subject_to_benefits' => $request->bonificacion_variable_prestaciones ?? 0,
                'award_category'            => $request->categoria_premios,
                'payment_method'            => $request->salary_payment_method ? \App\Enums\Payroll\PaymentMethod::from($request->salary_payment_method) : null,
            ]
            
        );
        // Asignar código si aplica
        $detail->refresh();
        $detail->assignCode();

        // Marcar como COMPLETO si tiene TODOS los campos requeridos
        $familiaOk     = $user->family->count() > 0 || $request->has('no_aplica_familia');
        $experienciaOk = $detail->workExperience->count() > 0 || $request->has('no_aplica_experiencia');

        // Verificar campos requeridos y notificar cuáles faltan
        $camposRequeridos = [
            'dpi_number'               => 'DPI',
            'nit_number'               => 'NIT',
            'dob'                      => 'Fecha de nacimiento',
            'birth_place'              => 'Lugar de nacimiento',
            'nationality'              => 'Nacionalidad',
            'marital_status'           => 'Estado civil',
            'gender'                   => 'Género',
            'igss_number'              => 'No. IGSS',
            'date_joined'              => 'Fecha de ingreso',
            'contract_type'            => 'Tipo de contrato',
            'work_schedule'            => 'Horario de trabajo',
            'work_hours_per_week'      => 'Horas por semana',
            'immediate_supervisor_name'=> 'Jefe inmediato',
            'payment_method'           => 'Forma de pago',
            'bank_name'                => 'Banco',
            'bank_account_number'      => 'Número de cuenta',
            'bank_account_type'        => 'Tipo de cuenta',
        ];

        $camposFaltantes = [];
        foreach ($camposRequeridos as $campo => $etiqueta) {
            if (empty($detail->$campo)) {
                $camposFaltantes[] = $etiqueta;
            }
        }

        // Verificar ubicación (tienda o departamento)
        if (empty($detail->store_id) && empty($detail->department_id)) {
            $camposFaltantes[] = 'Tienda o Departamento';
        }

        $familiaOk     = $user->family->count() > 0 || $detail->no_aplica_familia;
        $experienciaOk = $detail->workExperience->count() > 0 || $detail->no_aplica_experiencia;

        if (!$familiaOk) $camposFaltantes[] = 'Información familiar';
        if (!$experienciaOk) $camposFaltantes[] = 'Experiencia laboral';

        $isComplete = empty($camposFaltantes);

        $detail->update(['status' => $isComplete ? 'COMPLETO' : 'PENDIENTE']);

        if ($isComplete) {
            $notification = notify(__('Expediente actualizado correctamente'));
        } else {
            $faltanTexto = implode(', ', $camposFaltantes);
            $notification = notify(__("Expediente guardado pero incompleto. Campos faltantes: {$faltanTexto}"), 'warning');
        }

        // ── Sincronizar automáticamente con nóminas en BORRADOR del mes actual ──
        $nominasBorrador = \App\Models\Nomina::where('estado', 'BORRADOR')
            ->where('mes', now()->month)
            ->where('anio', now()->year)
            ->get();

        foreach ($nominasBorrador as $nomina) {
            $detalle = \App\Models\NominaDetalle::where('nomina_id', $nomina->id)
                ->where('employee_detail_id', $detail->id)
                ->first();

            if (!$detalle) continue;

            // Actualizar datos bancarios
            $detalle->cuenta_banco     = $detail->bank_account_number;
            $detalle->referencia_banco = $detail->bank_account_number;

            // Si el expediente está completo, limpiar la observación de pendiente
            if ($isComplete && str_contains($detalle->observacion ?? '', 'PENDIENTE')) {
                $detalle->observacion = null;
            }

            // Recalcular días trabajados según fecha de ingreso
            $nominaService = app(\App\Services\NominaService::class);
            $detalle->dias_trabajados = $nominaService->calcularDiasTrabajados(
                $detail, 
                $nomina->mes, 
                $nomina->anio
            );

            // Actualizar salario base y bono variable si cambiaron
            $nuevoSalario     = $detail->salaryDetails->base_salary ?? 0;
            $nuevoBono        = $detail->salaryDetails->variable_bonus ?? 0;

            if ($nuevoSalario > 0 && $detalle->salario_base != $nuevoSalario) {
                $detalle->salario_base = $nuevoSalario;
            }

            $detalle->bono_variable = $nuevoBono;

            // Recalcular con los nuevos datos
            $detalle->load('nomina');
            $detalle->recalcular();
            $detalle->save();

            // Actualizar totales de la cabecera
            app(\App\Services\NominaService::class)->recalcularTotales($nomina);
        }

        return redirect()->route('employees.show', $employee)->with($notification);
    }

    public function saveHorarioAdmin(Request $request, string $employee)
    {
        $userId = Crypt::decrypt($employee);
        $user   = User::findOrFail($userId);
        $detail = $user->employeeDetail;

        $dias = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES'];

        foreach ($dias as $dia) {
            $entrada = $request->input('horario.' . $dia . '.entrada');
            $salida  = $request->input('horario.' . $dia . '.salida');

            if (empty($entrada) || empty($salida)) continue;

            // Verificar si ya existe
            $existe = \Illuminate\Support\Facades\DB::connection('mysql_roy')
                ->select("SELECT ID FROM roy_horarios_admon WHERE CODIGO_EMPLEADO = ? AND DIA = ?",
                [$detail->oracle_emp_code, $dia]);

            if ($existe) {
                \Illuminate\Support\Facades\DB::connection('mysql_roy')
                    ->update("UPDATE roy_horarios_admon SET HORA_ENTRADA = ?, HORA_SALIDA = ? WHERE CODIGO_EMPLEADO = ? AND DIA = ?",
                    [$entrada, $salida, $detail->oracle_emp_code, $dia]);
            } else {
                \Illuminate\Support\Facades\DB::connection('mysql_roy')
                    ->insert("INSERT INTO roy_horarios_admon (TIENDA, CODIGO_EMPLEADO, DEPARTAMENTO, DIA, HORA_ENTRADA, HORA_SALIDA, PUESTO) VALUES (0, ?, ?, ?, ?, ?, ?)",
                    [
                        $detail->oracle_emp_code,
                        $detail->department->name ?? 'ADMIN',
                        $dia,
                        $entrada,
                        $salida,
                        $detail->designation->name ?? 'ADMIN',
                    ]);
            }
        }

        // Actualizar work_schedule en el expediente
        $detail->update(['work_schedule' => $request->input('horario.LUNES.entrada') . ' - ' . $request->input('horario.VIERNES.salida')]);

        $horarioTexto = $request->input('horario.LUNES.entrada') . ' - ' . $request->input('horario.VIERNES.salida');

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'horario' => $horarioTexto]);
            }

            return redirect()->route('employees.expediente', \Crypt::encrypt($user->id))
                ->with(notify('Horario guardado correctamente.'));
    }

}
