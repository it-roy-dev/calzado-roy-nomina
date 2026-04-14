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
        Schema::create('nomina_anticipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')->constrained('nominas')->onDelete('cascade');
            $table->foreignId('employee_detail_id')->constrained('employee_details')->onDelete('cascade');
            $table->integer('mes');
            $table->integer('anio');
            $table->integer('dias_trabajados')->default(30);
            $table->decimal('salario_devengado', 10, 2)->default(0);
            $table->decimal('monto_anticipo', 10, 2)->default(0); // 40%
            $table->date('fecha_pago');
            $table->enum('estado', ['PENDIENTE', 'PAGADO'])->default('PENDIENTE');
            $table->timestamps();
        });
    }
};
