<?php

namespace App\Models;

use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;


    protected $fillable = [
        'emp_id',
        'user_id',
        'department_id',
        'designation_id',
        
        // Documentos Guatemala
        'dpi_number',
        'dpi_issued_place',
        'nit_number',
        
        // Pasaporte
        'passport_no',
        'passport_expiry_date',
        'passport_tel',
        
        // Información personal
        'nationality',
        'religion',
        'ethnicity',
        'marital_status',
        'spouse_occupation',
        'no_of_children',
        'disability',
        'gender',
        'phone_secondary',
        'disability_description',
        'personal_email',
        'immediate_supervisor_name',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'bank_account_type',
        'no_aplica_familia',
        'no_aplica_experiencia',
        
        // Trabajo en extranjero
        'worked_abroad',
        'foreign_job_title',
        'foreign_country',
        'foreign_company',
        
        // Educación
        'languages',
        'birth_place',
        'academic_level',
        'degree_title',
        
        // Emergencia
        'emergency_contacts',
        
        // Fechas laborales
        'date_joined',
        'termination_date',
        'termination_reason',
        'contract_type',
        
        // Afiliaciones
        'igss_number',
        'irtra_number',
        'driver_license',
        
        // Estructura organizacional
        'store_id',
        'supervisor_id',
        
        // Horario
        'work_schedule',
        'work_hours_per_week',
        
        // Fechas sistema
        'dob',
        'created_at',
        'updated_at',



        // Oracle / Códigos
        'oracle_employee_id',
        'oracle_emp_code',
        'emp_code',
        'status',
        'oracle_active',
    ];

    protected $casts = [
        'emergency_contacts' => 'array',
        'languages' => 'array',
        'worked_abroad' => 'boolean',
        'date_joined' => 'date',
        'termination_date' => 'date',
        'dob' => 'date',
    ];

    public function education()
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function workExperience()
    {
        return $this->hasMany(EmployeeWorkExperience::class, 'employee_detail_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function salaryDetails()
    {
        return $this->hasOne(EmployeeSalaryDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function allowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }

    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

        /**
     * Relación con Supervisor
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relación con Tienda/Sucursal
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    
    public function uniformes()
    {
        return $this->hasMany(Uniforme::class, 'employee_detail_id');
    }

    public function uniformeActivo()
    {
        return $this->hasOne(Uniforme::class, 'employee_detail_id')
                    ->where('estado', 'ACTIVO');
    }

    public function nominaDetalles()
    {
        return $this->hasMany(NominaDetalle::class, 'employee_detail_id');
    }
    /**
     * Asignar código automáticamente basado en store_id o department_id
     * Se llama cuando RH guarda nombre + (store_id o department_id)
     */
    public function assignCode(): bool
    {
        if (!empty($this->emp_code)) {
            return false;
        }

        if (!$this->user || empty($this->user->firstname)) {
            return false;
        }

        if (empty($this->store_id) && empty($this->department_id)) {
            return false;
        }

        // Es ADMIN si tiene department_id
        if (!empty($this->department_id)) {
            $lastCode = EmployeeDetail::where('emp_code', 'LIKE', 'A-%')
                ->whereNotNull('emp_code')
                ->orderByRaw("LENGTH(emp_code) DESC, emp_code DESC")
                ->value('emp_code');

            $next = 1;
            if ($lastCode) {
                $next = ((int) substr($lastCode, 2)) + 1;
            }

            $this->emp_code = 'A-' . str_pad($next, 2, '0', STR_PAD_LEFT);

        // Es TIENDA si tiene store_id
        } else {
            $store = $this->store()->first();
            $storeNo = $store->oracle_store_no ?? $this->store_id;
            $prefix  = "T-{$storeNo}-";

            $lastCode = EmployeeDetail::where('emp_code', 'LIKE', "{$prefix}%")
                ->whereNotNull('emp_code')
                ->orderByRaw("LENGTH(emp_code) DESC, emp_code DESC")
                ->value('emp_code');

            $next = 1;
            if ($lastCode) {
                $next = ((int) substr($lastCode, strlen($prefix))) + 1;
            }

            $this->emp_code = $prefix . str_pad($next, 2, '0', STR_PAD_LEFT);
        }

        $this->save();
        return true;
    }

    /**
     * Verificar si el empleado tiene los campos mínimos para asignar código
     */
    public function canAssignCode(): bool
    {
        return empty($this->emp_code)
            && $this->user
            && !empty($this->user->firstname)
            && (!empty($this->store_id) || !empty($this->department_id));
    }

    /**
     * Obtener etiqueta de status para la UI
     */
    public function getStatusLabelAttribute(): array
    {
        return match($this->status) {
            'PENDIENTE'    => ['label' => 'Pendiente',     'color' => 'yellow'],
            'COMPLETO'     => ['label' => 'Completo',      'color' => 'green'],
            'DAR_DE_BAJA'  => ['label' => 'Dar de baja',   'color' => 'red'],
            'INACTIVO'     => ['label' => 'Inactivo',      'color' => 'gray'],
            default        => ['label' => 'Sin estado',    'color' => 'gray'],
        };
    }
}
