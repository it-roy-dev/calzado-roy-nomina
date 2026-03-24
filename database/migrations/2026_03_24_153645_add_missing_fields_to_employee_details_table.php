<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('ethnicity');
            $table->string('phone_secondary')->nullable()->after('gender');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn(['gender', 'phone_secondary']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};