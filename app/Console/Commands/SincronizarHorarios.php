<?php

namespace App\Console\Commands;

use App\Models\EmployeeDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SincronizarHorarios extends Command
{
    protected $signature   = 'horarios:sincronizar';
    protected $description = 'Sincroniza horarios de tienda y admin desde la base de datos Roy';

    public function handle()
    {
        $this->info('Iniciando sincronización de horarios...');

        $actualizadosTienda = 0;
        $actualizadosAdmin  = 0;
        $sinHorario         = 0;

        // ── TIENDAS ──
        $empleadosTienda = EmployeeDetail::whereNotNull('store_id')
            ->whereNotNull('oracle_emp_code')
            ->where('oracle_active', true)
            ->whereNotIn('status', ['DAR_DE_BAJA', 'INACTIVO'])
            ->with('store')
            ->get();

        foreach ($empleadosTienda as $empleado) {
            if (!$empleado->store) continue;

            $numeroTienda = $empleado->store->oracle_store_no;

            $horarios = DB::connection('mysql_roy')
                ->select("SELECT APERTURA, CIERRE FROM roy_horarios_cc_tiendas WHERE TIENDA = ?", [$numeroTienda]);

            if (empty($horarios)) {
                $sinHorario++;
                continue;
            }

            $aperturas   = array_column($horarios, 'APERTURA');
            $cierres     = array_column($horarios, 'CIERRE');
            $aperturaMin = min($aperturas);
            $cierreMax   = max($cierres);

            $empleado->update([
                'work_schedule'       => "{$aperturaMin} - {$cierreMax}",
                'work_hours_per_week' => 44,
            ]);

            $actualizadosTienda++;
        }

        // ── ADMIN ──
        $empleadosAdmin = EmployeeDetail::whereNotNull('department_id')
            ->whereNull('store_id')
            ->whereNotNull('oracle_emp_code')
            ->where('oracle_active', true)
            ->whereNotIn('status', ['DAR_DE_BAJA', 'INACTIVO'])
            ->get();

        foreach ($empleadosAdmin as $empleado) {
            $horario = DB::connection('mysql_roy')
                ->select("
                    SELECT HORA_ENTRADA, HORA_SALIDA
                    FROM roy_horarios_admon
                    WHERE CODIGO_EMPLEADO = ?
                    LIMIT 1
                ", [$empleado->oracle_emp_code]);

            if (empty($horario)) continue;

            $horarioTexto = $horario[0]->HORA_ENTRADA . ' - ' . $horario[0]->HORA_SALIDA;

            $empleado->update([
                'work_schedule' => $horarioTexto,
            ]);

            $actualizadosAdmin++;
        }

        $this->info('═══════════════════════════════');
        $this->info("Tiendas actualizadas:  {$actualizadosTienda}");
        $this->info("Admin actualizados:    {$actualizadosAdmin}");
        $this->info("Sin horario asignado:  {$sinHorario}");
        $this->info('═══════════════════════════════');

        return 0;
    }
}