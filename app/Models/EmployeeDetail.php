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
    
}
