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
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_planilla')->unique();
            $table->integer('mes');
            $table->integer('anio');
            $table->enum('tipo', ['PRIMERA_QUINCENA', 'SEGUNDA_QUINCENA']);
            $table->date('fecha_pago');
            $table->date('fecha_inicio_periodo');
            $table->date('fecha_fin_periodo');
            $table->enum('estado', ['BORRADOR', 'CERRADA', 'PAGADA'])->default('BORRADOR');
            $table->decimal('total_devengado', 12, 2)->default(0);
            $table->decimal('total_deducciones', 12, 2)->default(0);
            $table->decimal('total_liquido', 12, 2)->default(0);
            $table->integer('total_empleados')->default(0);
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('cerrada_by')->nullable()->constrained('users');
            $table->timestamp('cerrada_at')->nullable();
            $table->timestamps();
        });
    }
};
