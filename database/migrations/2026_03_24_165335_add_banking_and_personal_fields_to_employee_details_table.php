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
    Schema::table('employee_details', function (Blueprint $table) {
        $table->string('personal_email')->nullable()->after('nationality');
        $table->string('immediate_supervisor_name')->nullable()->after('supervisor_id');
        $table->string('payment_method')->nullable()->after('work_hours_per_week'); // cheque/cuenta
        $table->string('bank_name')->nullable()->after('payment_method');
        $table->string('bank_account_number')->nullable()->after('bank_name');
        $table->string('bank_account_type')->nullable()->after('bank_account_number'); // monetaria/ahorro
    });
}

public function down(): void
{
    Schema::table('employee_details', function (Blueprint $table) {
        $table->dropColumn([
            'personal_email',
            'immediate_supervisor_name',
            'payment_method',
            'bank_name',
            'bank_account_number',
            'bank_account_type',
        ]);
    });
}
};
