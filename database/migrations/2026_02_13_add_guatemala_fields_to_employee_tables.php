<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar campos a employee_details
        Schema::table('employee_details', function (Blueprint $table) {
            // Documentos Guatemala
            $table->string('dpi_number')->nullable()->after('emp_id');
            $table->string('dpi_issued_place')->nullable()->after('dpi_number');
            $table->string('nit_number')->nullable()->after('dpi_issued_place');
            
            // Información adicional
            $table->text('disability')->nullable()->after('no_of_children');
            $table->boolean('worked_abroad')->default(false)->after('disability');
            $table->string('foreign_job_title')->nullable()->after('worked_abroad');
            $table->string('foreign_country')->nullable()->after('foreign_job_title');
            $table->string('foreign_company')->nullable()->after('foreign_country');
            
            // Educación e idiomas
            $table->json('languages')->nullable()->after('foreign_company');
            $table->string('birth_place')->nullable()->after('dob');
            $table->string('academic_level')->nullable()->after('birth_place');
            $table->string('degree_title')->nullable()->after('academic_level');
            
            // Información laboral
            $table->date('termination_date')->nullable()->after('date_joined');
            $table->text('termination_reason')->nullable()->after('termination_date');
            $table->string('contract_type')->nullable()->after('termination_reason'); // fijo, temporal, eventual, vacacionista
            
            // Afiliaciones
            $table->string('igss_number')->nullable()->after('contract_type');
            $table->string('irtra_number')->nullable()->after('igss_number');
            $table->string('driver_license')->nullable()->after('irtra_number');
            
            // Estructura organizacional
            $table->unsignedBigInteger('store_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('store_id');
            
            // Horario
            $table->string('work_schedule')->nullable()->after('supervisor_id');
            $table->decimal('work_hours_per_week', 5, 2)->nullable()->after('work_schedule');
        });
        
        // Agregar campos a employee_salary_details
        Schema::table('employee_salary_details', function (Blueprint $table) {
            // Información bancaria
            $table->string('bank_name')->nullable()->after('payment_method');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_type')->nullable()->after('bank_account_number'); // monetaria, ahorro
            
            // Bonificaciones Guatemala
            $table->decimal('bonificacion_decreto', 10, 2)->default(250.00)->after('base_salary');
            $table->decimal('variable_bonus', 10, 2)->nullable()->after('bonificacion_decreto');
            $table->boolean('bonus_subject_to_benefits')->default(true)->after('variable_bonus');
            $table->string('award_category')->nullable()->after('bonus_subject_to_benefits'); // supervisor, jefe, ventas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn([
                'dpi_number',
                'dpi_issued_place',
                'nit_number',
                'disability',
                'worked_abroad',
                'foreign_job_title',
                'foreign_country',
                'foreign_company',
                'languages',
                'birth_place',
                'academic_level',
                'degree_title',
                'termination_date',
                'termination_reason',
                'contract_type',
                'igss_number',
                'irtra_number',
                'driver_license',
                'store_id',
                'supervisor_id',
                'work_schedule',
                'work_hours_per_week'
            ]);
        });
        
        Schema::table('employee_salary_details', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_account_number',
                'bank_account_type',
                'bonificacion_decreto',
                'variable_bonus',
                'bonus_subject_to_benefits',
                'award_category'
            ]);
        });
    }
};