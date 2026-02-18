<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            // Campos de Oracle
            $table->string('oracle_employee_id')->unique()->nullable()->after('id');
            $table->string('oracle_emp_code')->nullable()->after('oracle_employee_id');
            
            // Codigo nuevo limpio (PRIMARY)
            $table->string('emp_code', 10)->unique()->nullable()->after('oracle_emp_code');
            
            // Status del expediente
            $table->string('status', 50)->default('PENDIENTE')->after('emp_code');
        });
    }

    public function down(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn([
                'oracle_employee_id',
                'oracle_emp_code',
                'emp_code',
                'status',
            ]);
        });
    }
};