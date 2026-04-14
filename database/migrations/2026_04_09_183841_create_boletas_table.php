<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('boletas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('nomina_id')->constrained('nominas')->onDelete('cascade');
        $table->foreignId('nomina_detalle_id')->constrained('nomina_detalle')->onDelete('cascade');
        $table->foreignId('employee_detail_id')->constrained('employee_details')->onDelete('cascade');
        $table->string('numero_correlativo')->nullable();
        $table->enum('tipo', ['PRIMERA_QUINCENA', 'SEGUNDA_QUINCENA']);
        $table->string('pdf_path')->nullable();           // ruta del PDF sin firma
        $table->string('pdf_firmado_path')->nullable();   // ruta del PDF con firma
        $table->enum('estado', ['PENDIENTE', 'FIRMADA'])->default('PENDIENTE');
        $table->timestamp('firmada_at')->nullable();
        $table->foreignId('firmada_by')->nullable()->constrained('users');
        $table->timestamps();
    });
}
};
