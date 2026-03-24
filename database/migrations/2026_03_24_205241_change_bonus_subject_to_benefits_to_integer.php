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
    DB::statement('ALTER TABLE employee_salary_details ALTER COLUMN bonus_subject_to_benefits DROP DEFAULT');
    DB::statement('ALTER TABLE employee_salary_details ALTER COLUMN bonus_subject_to_benefits TYPE integer USING CASE WHEN bonus_subject_to_benefits THEN 1 ELSE 0 END');
    DB::statement('ALTER TABLE employee_salary_details ALTER COLUMN bonus_subject_to_benefits SET DEFAULT NULL');
}

public function down(): void
{
    DB::statement('ALTER TABLE employee_salary_details ALTER COLUMN bonus_subject_to_benefits TYPE boolean USING CASE WHEN bonus_subject_to_benefits = 1 THEN true ELSE false END');
}
};
