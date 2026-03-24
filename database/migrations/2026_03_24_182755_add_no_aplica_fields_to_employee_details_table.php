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
            $table->boolean('no_aplica_familia')->default(false)->after('bank_account_type');
            $table->boolean('no_aplica_experiencia')->default(false)->after('no_aplica_familia');
        });
    }

    public function down(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn(['no_aplica_familia', 'no_aplica_experiencia']);
        });
    }
};
