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
        Schema::create('nomina_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')->constrained('nominas')->onDelete('cascade');
            $table->foreignId('employee_detail_id')->constrained('employee_details')->onDelete('cascade');

            // Días
            $table->integer('dias_trabajados')->default(30);
            $table->text('observacion')->nullable();

            // Devengado
            $table->decimal('salario_base', 10, 2)->default(0);
            $table->decimal('salario_devengado', 10, 2)->default(0);
            $table->decimal('salario_extra_ordinario', 10, 2)->default(0);
            $table->decimal('bonificacion_decreto', 10, 2)->default(0);
            $table->decimal('bono_variable', 10, 2)->default(0);
            $table->decimal('total_bonificaciones', 10, 2)->default(0);
            $table->decimal('total_devengado', 10, 2)->default(0);

            // Deducciones fijas (calculadas)
            $table->decimal('igss', 10, 2)->default(0);
            $table->decimal('isr', 10, 2)->default(0);

            // Deducciones variables (editables)
            $table->decimal('anticipo_40', 10, 2)->default(0);
            $table->decimal('prestamos', 10, 2)->default(0);
            $table->decimal('ayuvi', 10, 2)->default(0);
            $table->decimal('descuento_judicial', 10, 2)->default(0);
            $table->decimal('faltante_inventario', 10, 2)->default(0);
            $table->decimal('uniforme', 10, 2)->default(0);
            $table->decimal('cxc', 10, 2)->default(0);

            // Totales
            $table->decimal('total_otros_descuentos', 10, 2)->default(0);
            $table->decimal('total_deducciones', 10, 2)->default(0);
            $table->decimal('liquido_a_recibir', 10, 2)->default(0);

            // Banco
            $table->string('cuenta_banco')->nullable();
            $table->string('referencia_banco')->nullable();

            // Control
            $table->boolean('isr_editado')->default(false);
            $table->foreignId('uniforme_id')->nullable()->constrained('uniformes');
            $table->timestamps();
        });
    }
};
