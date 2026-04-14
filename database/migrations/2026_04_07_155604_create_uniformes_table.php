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
        Schema::create('uniformes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_detail_id')->constrained('employee_details')->onDelete('cascade');
            $table->date('fecha_entrega');
            $table->decimal('monto_total', 10, 2);
            $table->integer('num_cuotas');
            $table->integer('cuotas_pagadas')->default(0);
            $table->decimal('monto_cuota', 10, 2);
            $table->decimal('saldo_pendiente', 10, 2);
            $table->enum('estado', ['ACTIVO', 'PAGADO', 'ANULADO'])->default('ACTIVO');
            $table->text('descripcion')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }
};
