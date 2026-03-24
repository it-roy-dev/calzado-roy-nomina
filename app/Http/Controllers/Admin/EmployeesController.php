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
        $employees = User::where('type', UserType::EMPLOYEE)->get();

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

            return response()->json([
                'success' => true,
                'message' => 'Sincronización completada',
                'output'  => nl2br(e($result)),
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
        $userId     = Crypt::decrypt($employee);
        $user       = User::findOrFail($userId);
        $detail     = $user->employeeDetail;
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('name')->get();
        $stores      = \App\Models\Store::where('is_active', true)
                        ->where('type', 'tienda')
                        ->orderBy('oracle_store_no')
                        ->get();
        $pageTitle   = 'Expediente — ' . $user->fullname;

        return view('pages.employees.expediente', compact(
            'user', 'detail', 'departments', 'designations', 'stores', 'pageTitle'
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
                'variable_bonus'            => $request->bonificacion_variable ?? 0,
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

        $isComplete = !empty($detail->dpi_number)
            && !empty($detail->nit_number)
            && !empty($detail->dob)
            && !empty($detail->birth_place)
            && !empty($detail->nationality)
            && !empty($detail->marital_status)
            && !empty($detail->gender)
            && !empty($detail->igss_number)
            && !empty($detail->irtra_number)
            && !empty($detail->date_joined)
            && !empty($detail->contract_type)
            && !empty($detail->work_schedule)
            && !empty($detail->work_hours_per_week)
            && !empty($detail->immediate_supervisor_name)
            && !empty($detail->payment_method)
            && !empty($detail->bank_name)
            && !empty($detail->bank_account_number)
            && !empty($detail->bank_account_type)
            && (!empty($detail->store_id) || !empty($detail->department_id))
            && $familiaOk
            && $experienciaOk;

        if ($isComplete) {
            $detail->update(['status' => 'COMPLETO']);
        } else {
            // Si estaba COMPLETO y le quitaron datos, vuelve a PENDIENTE
            if ($detail->status === 'COMPLETO') {
                $detail->update(['status' => 'PENDIENTE']);
            }
        }

        $notification = notify(__('Expediente actualizado correctamente'));

        if (!$isComplete && $detail->status === 'PENDIENTE') {
            $notification = notify(__('Expediente guardado pero incompleto. Verifica que todos los campos requeridos estén llenos.'), 'warning');
        }

        return redirect()->route('employees.show', $employee)->with($notification);
    }

}
