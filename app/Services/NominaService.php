<?php

namespace App\Services;

use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\NominaAnticipo;
use App\Models\EmployeeDetail;
use App\Models\Uniforme;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NominaService
{
    /**
     * Generar Primera Quincena (anticipo 40%)
     */
    public function generarPrimeraQuincena(int $mes, int $anio): Nomina
    {
        // Verificar que no exista ya
        $existe = Nomina::where('mes', $mes)
            ->where('anio', $anio)
            ->where('tipo', 'PRIMERA_QUINCENA')
            ->first();

        if ($existe) {
            throw new \Exception("Ya existe una primera quincena para {$mes}/{$anio}");
        }

        return DB::transaction(function () use ($mes, $anio) {
            // Fecha de pago (día 15, ajustando fines de semana)
            $fechaPago = Nomina::calcularFechaPago(15, $mes, $anio);

            // Crear cabecera
            $nomina = Nomina::create([
                'numero_planilla'      => Nomina::siguienteNumero(),
                'mes'                  => $mes,
                'anio'                 => $anio,
                'tipo'                 => 'PRIMERA_QUINCENA',
                'fecha_pago'           => $fechaPago,
                'fecha_inicio_periodo' => Carbon::create($anio, $mes, 1),
                'fecha_fin_periodo'    => Carbon::create($anio, $mes, 15),
                'estado'               => 'BORRADOR',
                'created_by'           => auth()->id(),
            ]);

            // Obtener todos los empleados activos
            $empleados = EmployeeDetail::with(['salaryDetails', 'user'])
                ->where('oracle_active', true)
                ->whereNotIn('status', ['DAR_DE_BAJA', 'INACTIVO'])
                ->get();

            $totalDevengado  = 0;
            $totalDeducciones = 0;
            $totalLiquido    = 0;

            foreach ($empleados as $empleado) {
                $salarioBase   = $empleado->salaryDetails->base_salary ?? 0;
                $salarioMinimo = 4002.28;
                $sinExpediente = !$empleado->salaryDetails || $salarioBase <= 0;
                $salarioBase   = $sinExpediente ? $salarioMinimo : $salarioBase;

                $diasTrabajados = $this->calcularDiasTrabajados($empleado, $mes, $anio);
                $salarioDevengado  = $sinExpediente ? 0 : round($salarioBase * $diasTrabajados / 30, 2);
                $bonificacion      = round(8.333333333 * $diasTrabajados, 2);
                $totalDevengadoEmp = $sinExpediente ? 0 : ($salarioDevengado + $bonificacion);
                $anticipo          = $sinExpediente ? 0 : round($totalDevengadoEmp * 0.40, 2);

                NominaDetalle::create([
                    'nomina_id'            => $nomina->id,
                    'employee_detail_id'   => $empleado->id,
                    'dias_trabajados'      => $diasTrabajados,
                    'salario_base'         => $salarioBase,
                    'salario_devengado'    => $salarioDevengado,
                    'bonificacion_decreto' => $bonificacion,
                    'total_bonificaciones' => $bonificacion,
                    'total_devengado'      => $totalDevengadoEmp,  // lo que ganó
                    'anticipo_40'          => $anticipo,            // el 40% que se paga
                    'igss'                 => 0,
                    'isr'                  => 0,
                    'prestamos'            => 0,
                    'ayuvi'                => 0,
                    'descuento_judicial'   => 0,
                    'faltante_inventario'  => 0,
                    'uniforme'             => 0,
                    'cxc'                  => 0,
                    'total_otros_descuentos' => 0,
                    'total_deducciones'    => 0,
                    'liquido_a_recibir'    => $anticipo,            // lo que se paga
                    'cuenta_banco'         => $empleado->bank_account_number,
                    'referencia_banco'     => $empleado->bank_account_number,
                    'isr_editado'          => false,
                    'observacion'          => $sinExpediente ? 'PENDIENTE — Expediente incompleto' : null,
                ]);

                $totalDevengado += $totalDevengadoEmp;
                $totalLiquido   += $anticipo;
            }

            // Actualizar totales en cabecera
            $nomina->update([
                'total_devengado'  => $totalDevengado,
                'total_deducciones' => 0,
                'total_liquido'    => $totalLiquido,
                'total_empleados'  => $empleados->count(),
            ]);

            return $nomina;
        });
    }

    /**
     * Generar Segunda Quincena (60% + deducciones)
     */
    public function generarSegundaQuincena(int $mes, int $anio): Nomina
    {
        // Verificar que exista la primera quincena
        $primeraQuincena = Nomina::where('mes', $mes)
            ->where('anio', $anio)
            ->where('tipo', 'PRIMERA_QUINCENA')
            ->first();

        if (!$primeraQuincena) {
            throw new \Exception("Primero debe generarse la primera quincena de {$mes}/{$anio}");
        }

        // Verificar que no exista ya la segunda
        $existe = Nomina::where('mes', $mes)
            ->where('anio', $anio)
            ->where('tipo', 'SEGUNDA_QUINCENA')
            ->first();

        if ($existe) {
            throw new \Exception("Ya existe una segunda quincena para {$mes}/{$anio}");
        }

        return DB::transaction(function () use ($mes, $anio, $primeraQuincena) {
            // Último día del mes
            $ultimoDia = Carbon::create($anio, $mes)->daysInMonth;
            $fechaPago = Nomina::calcularFechaPago($ultimoDia, $mes, $anio);

            $nomina = Nomina::create([
                'numero_planilla'      => Nomina::siguienteNumero(),
                'mes'                  => $mes,
                'anio'                 => $anio,
                'tipo'                 => 'SEGUNDA_QUINCENA',
                'fecha_pago'           => $fechaPago,
                'fecha_inicio_periodo' => Carbon::create($anio, $mes, 16),
                'fecha_fin_periodo'    => Carbon::create($anio, $mes, $ultimoDia),
                'estado'               => 'BORRADOR',
                'created_by'           => auth()->id(),
            ]);

            $empleados = EmployeeDetail::with([
                    'salaryDetails',
                    'user',
                    'uniformeActivo',
                    'store',
                    'department',
                    'designation'
                ])
                ->where('oracle_active', true)
                ->whereNotIn('status', ['DAR_DE_BAJA', 'INACTIVO'])
                ->get();

            // Anticipos de primera quincena indexados por employee_detail_id
            $anticipos = NominaDetalle::where('nomina_id', $primeraQuincena->id)
                ->pluck('liquido_a_recibir', 'employee_detail_id');

            $totalDevengado   = 0;
            $totalDeducciones = 0;
            $totalLiquido     = 0;

            foreach ($empleados as $empleado) {
                $salarioBase = $empleado->salaryDetails->base_salary ?? 0;
                $salarioMinimo = 4002.28;
                // Si no tiene salario registrado, usar salario mínimo pero marcar como pendiente
                $sinExpediente = !$empleado->salaryDetails || $salarioBase <= 0;
                $salarioBase = $sinExpediente ? $salarioMinimo : $salarioBase;

                $diasTrabajados   = $this->calcularDiasTrabajados($empleado, $mes, $anio);
                $salarioDevengado = $sinExpediente ? 0 : round($salarioBase * $diasTrabajados / 30, 2);
                $bonificacion     = round(8.333333333 * $diasTrabajados, 2);
                $bonoVariable     = $sinExpediente ? 0 : ($empleado->salaryDetails->variable_bonus ?? 0);
                $totalBonif       = $bonificacion + $bonoVariable;
                $totalDevengadoEmp = $salarioDevengado + $totalBonif;

                // IGSS aplica sobre salario + horas extra (NO sobre bonificación)
                $igss = round(($salarioDevengado + 0) * 0.0483, 2); // horas extra se agregan al editar
                $isr  = $sinExpediente ? 0 : $this->calcularISR($salarioDevengado, $igss);

                $anticipo = $anticipos[$empleado->id] ?? 0;

                $descuentoUniforme = 0;
                $uniformeId        = null;
                if (!$sinExpediente && $empleado->uniformeActivo) {
                    $uniforme          = $empleado->uniformeActivo;
                    $descuentoUniforme = $uniforme->monto_cuota;
                    $uniformeId        = $uniforme->id;
                }

                $totalOtros  = $descuentoUniforme;
                $totalDeducc = $igss + $isr + $anticipo + $totalOtros;
                $liquido     = $sinExpediente ? 0 : round($totalDevengadoEmp - $totalDeducc, 2);

                NominaDetalle::create([
                    'nomina_id'               => $nomina->id,
                    'employee_detail_id'      => $empleado->id,
                    'dias_trabajados'         => $diasTrabajados,
                    'observacion'             => $sinExpediente ? 'PENDIENTE — Expediente incompleto' : null,
                    'salario_base'            => $salarioBase,
                    'salario_devengado'       => $salarioDevengado,
                    'salario_extra_ordinario' => 0,
                    'bonificacion_decreto'    => $bonificacion,
                    'bono_variable'           => $bonoVariable,
                    'total_bonificaciones'    => $totalBonif,
                    'total_devengado'         => $totalDevengadoEmp,
                    'igss'                    => $igss,
                    'isr'                     => $isr,
                    'anticipo_40'             => $anticipo,
                    'uniforme'                => $descuentoUniforme,
                    'prestamos'               => 0,
                    'ayuvi'                   => 0,
                    'descuento_judicial'      => 0,
                    'faltante_inventario'     => 0,
                    'cxc'                     => 0,
                    'total_otros_descuentos'  => $totalOtros,
                    'total_deducciones'       => $totalDeducc,
                    'liquido_a_recibir'       => $liquido,
                    'cuenta_banco'            => $empleado->bank_account_number,
                    'referencia_banco'        => $empleado->bank_account_number,
                    'isr_editado'             => false,
                    'uniforme_id'             => $uniformeId,
                ]);

                $totalDevengado   += $totalDevengadoEmp;
                $totalDeducciones += $totalDeducc;
                $totalLiquido     += $liquido;
            }

            $nomina->update([
                'total_devengado'   => $totalDevengado,
                'total_deducciones' => $totalDeducciones,
                'total_liquido'     => $totalLiquido,
                'total_empleados'   => $empleados->count(),
            ]);

            return $nomina;
        });
    }

    /**
     * Calcular ISR mensual
     */
    public function calcularISR(float $salarioDevengado, float $igss, float $horasExtra = 0): float
    {
        $baseNeta = ($salarioDevengado + $horasExtra) - $igss;
        $proyeccionAnual = $baseNeta * 12;

        if ($proyeccionAnual <= 48000) {
            return 0.00;
        }

        $excedente = $proyeccionAnual - 48000;
        return round(($excedente * 0.05) / 12, 2);
    }

    /**
     * Recalcular totales de una nómina completa
     */
    public function recalcularTotales(Nomina $nomina): void
    {
        $totales = NominaDetalle::where('nomina_id', $nomina->id)
            ->selectRaw('
                SUM(total_devengado) as total_devengado,
                SUM(total_deducciones) as total_deducciones,
                SUM(liquido_a_recibir) as total_liquido,
                COUNT(*) as total_empleados
            ')
            ->first();

        $nomina->update([
            'total_devengado'   => $totales->total_devengado ?? 0,
            'total_deducciones' => $totales->total_deducciones ?? 0,
            'total_liquido'     => $totales->total_liquido ?? 0,
            'total_empleados'   => $totales->total_empleados ?? 0,
        ]);
    }

    /**
     * Cerrar nómina (ya no editable)
     */
    public function cerrarNomina(Nomina $nomina): void
    {
        if ($nomina->estado === 'CERRADA') {
            throw new \Exception("La nómina ya está cerrada");
        }

        // Al cerrar segunda quincena, marcar cuotas de uniforme como pagadas
        if ($nomina->tipo === 'SEGUNDA_QUINCENA') {
            $this->procesarCuotasUniforme($nomina);
        }

        $nomina->update([
            'estado'     => 'CERRADA',
            'cerrada_by' => auth()->id(),
            'cerrada_at' => now(),
        ]);

        // Generar boletas automáticamente al cerrar
        $boletaController = app(\App\Http\Controllers\BoletaController::class);
        $boletaController->generarBoletas($nomina);
    }

    /**
     * Procesar cuotas de uniforme al cerrar nómina
     */
    private function procesarCuotasUniforme(Nomina $nomina): void
    {
        $detallesConUniforme = NominaDetalle::where('nomina_id', $nomina->id)
            ->whereNotNull('uniforme_id')
            ->where('uniforme', '>', 0)
            ->get();

        foreach ($detallesConUniforme as $detalle) {
            $uniforme = Uniforme::find($detalle->uniforme_id);
            if (!$uniforme) continue;

            $uniforme->cuotas_pagadas++;
            $uniforme->saldo_pendiente = round(
                $uniforme->saldo_pendiente - $detalle->uniforme, 2
            );

            // Si ya pagó todas las cuotas, marcar como PAGADO
            if ($uniforme->cuotas_pagadas >= $uniforme->num_cuotas) {
                $uniforme->estado          = 'PAGADO';
                $uniforme->saldo_pendiente = 0;
            }

            $uniforme->save();
        }
    }

    /**
 * Calcular días trabajados según fecha de ingreso
 */
    public function calcularDiasTrabajados(EmployeeDetail $empleado, int $mes, int $anio): int
    {
        $fechaIngreso = $empleado->date_joined;

        // Si no tiene fecha de ingreso, asumimos mes completo
        if (!$fechaIngreso) {
            return 30;
        }

        $ingreso = Carbon::parse($fechaIngreso);

        // Si ingresó antes del mes actual, trabaja los 30 días completos
        if ($ingreso->year < $anio || ($ingreso->year == $anio && $ingreso->month < $mes)) {
            return 30;
        }

        // Si ingresó durante el mes actual, calcular días restantes
        if ($ingreso->year == $anio && $ingreso->month == $mes) {
            $ultimoDia = Carbon::create($anio, $mes)->daysInMonth;
            $dias = $ultimoDia - $ingreso->day + 1;
            return max(1, $dias); // mínimo 1 día
        }

        // Si ingresó en un mes futuro (no debería pasar), 0 días
        return 0;
    }
}