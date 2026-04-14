<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NominaDetalle extends Model
{
    protected $table = 'nomina_detalle';

    protected $fillable = [
        'nomina_id',
        'employee_detail_id',
        'dias_trabajados',
        'observacion',
        'salario_base',
        'salario_devengado',
        'salario_extra_ordinario',
        'bonificacion_decreto',
        'bono_variable',
        'total_bonificaciones',
        'total_devengado',
        'igss',
        'isr',
        'anticipo_40',
        'prestamos',
        'ayuvi',
        'descuento_judicial',
        'faltante_inventario',
        'uniforme',
        'cxc',
        'total_otros_descuentos',
        'total_deducciones',
        'liquido_a_recibir',
        'cuenta_banco',
        'referencia_banco',
        'isr_editado',
        'uniforme_id',
    ];

    protected $casts = [
        'salario_base'            => 'decimal:2',
        'salario_devengado'       => 'decimal:2',
        'salario_extra_ordinario' => 'decimal:2',
        'bonificacion_decreto'    => 'decimal:2',
        'bono_variable'           => 'decimal:2',
        'total_bonificaciones'    => 'decimal:2',
        'total_devengado'         => 'decimal:2',
        'igss'                    => 'decimal:2',
        'isr'                     => 'decimal:2',
        'anticipo_40'             => 'decimal:2',
        'prestamos'               => 'decimal:2',
        'ayuvi'                   => 'decimal:2',
        'descuento_judicial'      => 'decimal:2',
        'faltante_inventario'     => 'decimal:2',
        'uniforme'                => 'decimal:2',
        'cxc'                     => 'decimal:2',
        'total_otros_descuentos'  => 'decimal:2',
        'total_deducciones'       => 'decimal:2',
        'liquido_a_recibir'       => 'decimal:2',
        'isr_editado'             => 'boolean',
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function empleado()
    {
        return $this->belongsTo(EmployeeDetail::class, 'employee_detail_id');
    }

    public function uniformeEntrega()
    {
        return $this->belongsTo(Uniforme::class, 'uniforme_id');
    }

    // Recalcular todos los totales
    public function recalcular(): void
    {
        // Salario proporcional a días
        $this->salario_devengado = round(
            $this->salario_base * $this->dias_trabajados / 30, 2
        );

        // Bonificación proporcional (Q250 × días/30)
        $this->bonificacion_decreto = round(8.333333333 * $this->dias_trabajados, 2);
        $this->total_bonificaciones = $this->bonificacion_decreto + ($this->bono_variable ?? 0);
        $this->total_devengado = $this->salario_devengado
            + ($this->salario_extra_ordinario ?? 0)
            + $this->total_bonificaciones;

        // ─────────────────────────────────────────────
        // PRIMERA QUINCENA: solo el 40%, sin deducciones
        // ─────────────────────────────────────────────
        $tipo = $this->nomina?->tipo ?? 'SEGUNDA_QUINCENA';

        if ($tipo === 'PRIMERA_QUINCENA') {
            $this->igss                  = 0;
            $this->isr                   = 0;
            $this->prestamos             = 0;
            $this->ayuvi                 = 0;
            $this->descuento_judicial    = 0;
            $this->faltante_inventario   = 0;
            $this->uniforme              = 0;
            $this->cxc                   = 0;
            $this->anticipo_40           = 0;
            $this->total_otros_descuentos = 0;
            $this->total_deducciones     = 0;
            $this->liquido_a_recibir     = round($this->total_devengado * 0.40, 2);
            return; // ← salimos, no calculamos nada más
        }

        // ─────────────────────────────────────────────
        // SEGUNDA QUINCENA: cálculo normal con deducciones
        // ─────────────────────────────────────────────

        // IGSS: 4.83% sobre salario devengado (bonificación NO aplica)
        $this->igss = round(($this->salario_devengado + ($this->salario_extra_ordinario ?? 0)) * 0.0483, 2);

        // ISR: solo si no fue editado manualmente
        if (!$this->isr_editado) {
            $this->isr = $this->calcularISR();
        }

        // Total otros descuentos
        $this->total_otros_descuentos = round(
            ($this->prestamos ?? 0) +
            ($this->ayuvi ?? 0) +
            ($this->descuento_judicial ?? 0) +
            ($this->faltante_inventario ?? 0) +
            ($this->uniforme ?? 0) +
            ($this->cxc ?? 0),
            2
        );

        // Total deducciones
        $this->total_deducciones = round(
            $this->igss +
            $this->isr +
            ($this->anticipo_40 ?? 0) +
            $this->total_otros_descuentos,
            2
        );

        // Líquido
        $this->liquido_a_recibir = round(
            $this->total_devengado - $this->total_deducciones, 2
        );
    }

    // Cálculo ISR mensual
    private function calcularISR(): float
    {
        // Base: salario devengado + horas extra, menos IGSS
        $baseNeta = ($this->salario_devengado + ($this->salario_extra_ordinario ?? 0)) - $this->igss;
        $proyeccionAnual = $baseNeta * 12;

        if ($proyeccionAnual <= 48000) {
            return 0.00;
        }

        $excedente = $proyeccionAnual - 48000;
        return round(($excedente * 0.05) / 12, 2);
    }
}