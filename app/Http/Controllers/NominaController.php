<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Services\NominaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NominaController extends Controller
{
    public function __construct(protected NominaService $nominaService) {}

    /**
     * Lista de nóminas
     */
    public function index()
    {
        $nominas = Nomina::orderByDesc('anio')
            ->orderByDesc('mes')
            ->orderByDesc('tipo')
            ->paginate(20);

        return view('nomina.index', compact('nominas'));
    }

    /**
     * Generar nueva nómina
     */
    public function generar(Request $request)
    {
        $request->validate([
            'mes'  => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2024',
            'tipo' => 'required|in:PRIMERA_QUINCENA,SEGUNDA_QUINCENA',
        ]);

        try {
            if ($request->tipo === 'PRIMERA_QUINCENA') {
                $nomina = $this->nominaService->generarPrimeraQuincena(
                    $request->mes,
                    $request->anio
                );
            } else {
                $nomina = $this->nominaService->generarSegundaQuincena(
                    $request->mes,
                    $request->anio
                );
            }

            return redirect()
                ->route('nomina.show', $nomina->id)
                ->with('success', "Nómina {$nomina->titulo} generada con {$nomina->total_empleados} empleados.");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Ver detalle de nómina
     */
    public function show(Nomina $nomina)
    {
        $detalle = NominaDetalle::where('nomina_id', $nomina->id)
            ->with([
                'empleado.user',
                'empleado.store',
                'empleado.department',
                'empleado.designation',
            ])
            ->join('employee_details', 'nomina_detalle.employee_detail_id', '=', 'employee_details.id')
            ->leftJoin('stores', 'employee_details.store_id', '=', 'stores.id')
            ->orderByRaw("COALESCE(stores.oracle_store_no::text, '9999')")
            ->orderBy('nomina_detalle.id')
            ->select('nomina_detalle.*')
            ->get();

        return view('nomina.show', compact('nomina', 'detalle'));
    }

    /**
     * Actualizar una fila editable (ISR, préstamos, etc.)
     */
    public function updateDetalle(Request $request, NominaDetalle $detalle)
    {
        // No permitir editar si la nómina está cerrada
        if ($detalle->nomina->estado === 'CERRADA') {
            return response()->json(['error' => 'La nómina está cerrada'], 403);
        }

        \Log::info('updateDetalle called', ['field' => $request->all(), 'id' => $detalle->id]);

        $campos = $request->validate([
            'dias_trabajados'    => 'sometimes|integer|min:0|max:31',
            'observacion'        => 'sometimes|nullable|string',
            'salario_extra_ordinario' => 'sometimes|numeric|min:0',
            'bono_variable'      => 'sometimes|numeric|min:0',
            'isr'                => 'sometimes|numeric|min:0',
            'prestamos'          => 'sometimes|numeric|min:0',
            'ayuvi'              => 'sometimes|numeric|min:0',
            'descuento_judicial' => 'sometimes|numeric|min:0',
            'faltante_inventario'=> 'sometimes|numeric|min:0',
            'uniforme'           => 'sometimes|numeric|min:0',
            'cxc'                => 'sometimes|numeric|min:0',
        ]);

        // Si editaron ISR manualmente, marcarlo
        if (isset($campos['isr'])) {
            $campos['isr_editado'] = true;
        }

        // Si cambiaron días, recalcular salario devengado
        if (isset($campos['dias_trabajados'])) {
            $detalle->dias_trabajados = $campos['dias_trabajados'];
            $detalle->salario_devengado = round(
                $detalle->salario_base * $detalle->dias_trabajados / 30, 2
            );
            $detalle->bonificacion_decreto = round(
                250 * $detalle->dias_trabajados / 30, 2
            );
            $detalle->igss = round($detalle->salario_devengado * 0.0483, 2);

            // Recalcular ISR si no fue editado manualmente
            if (!$detalle->isr_editado) {
                $detalle->isr = $this->nominaService->calcularISR(
                    $detalle->salario_devengado,
                    $detalle->igss
                );
            }
        }

        $detalle->fill($campos);

        // Recalcular totales bonificaciones
        $detalle->total_bonificaciones = $detalle->bonificacion_decreto
            + ($detalle->bono_variable ?? 0);

        $detalle->total_devengado = $detalle->salario_devengado
            + ($detalle->salario_extra_ordinario ?? 0)
            + $detalle->total_bonificaciones;

        // Recalcular total otros descuentos
        $detalle->total_otros_descuentos = round(
            ($detalle->prestamos ?? 0) +
            ($detalle->ayuvi ?? 0) +
            ($detalle->descuento_judicial ?? 0) +
            ($detalle->faltante_inventario ?? 0) +
            ($detalle->uniforme ?? 0) +
            ($detalle->cxc ?? 0),
            2
        );

        // Recalcular total deducciones y líquido
        $detalle->total_deducciones = round(
            $detalle->igss +
            $detalle->isr +
            ($detalle->anticipo_40 ?? 0) +
            $detalle->total_otros_descuentos,
            2
        );

        $detalle->liquido_a_recibir = round(
            $detalle->total_devengado - $detalle->total_deducciones, 2
        );

        $detalle->save();

        // Recalcular totales de la nómina
        $this->nominaService->recalcularTotales($detalle->nomina);

        $nomina = $detalle->nomina->fresh();
        return response()->json([
            'success' => true,
            'row'     => $detalle->fresh(),
            'nomina'  => [
                'total_devengado'  => $nomina->total_devengado,
                'total_deducciones'=> $nomina->total_deducciones,
                'total_liquido'    => $nomina->total_liquido,
            ],
        ]);
    }

    /**
     * Cerrar nómina
     */
    public function cerrar(Nomina $nomina)
    {
        try {
            $this->nominaService->cerrarNomina($nomina);
            return back()->with('success', 'Nómina cerrada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Eliminar nómina en borrador
     */
    public function destroy(Nomina $nomina)
    {
        if ($nomina->estado !== 'BORRADOR') {
            return back()->with('error', 'Solo se pueden eliminar nóminas en borrador.');
        }

        $nomina->delete();
        return redirect()
            ->route('nomina.index')
            ->with('success', 'Nómina eliminada.');
    }

    /**
 * Exportar a Excel
 */
public function exportExcel(Nomina $nomina)
{
    $filename = 'nomina_' . $nomina->numero_planilla . '_' .
        strtolower(str_replace(' ', '_', $nomina->titulo)) . '.xlsx';

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\NominaExport($nomina),
        $filename
    );
}

/**
 * Exportar a PDF
 */
public function exportPdf(Nomina $nomina)
{
    ini_set('max_execution_time', 300);
    ini_set('memory_limit', '512M');

    $detalle = NominaDetalle::where('nomina_id', $nomina->id)
        ->with(['empleado.user', 'empleado.store', 'empleado.department', 'empleado.designation'])
        ->join('employee_details', 'nomina_detalle.employee_detail_id', '=', 'employee_details.id')
        ->leftJoin('stores', 'employee_details.store_id', '=', 'stores.id')
        ->orderByRaw("COALESCE(stores.oracle_store_no::text, '9999')")
        ->orderBy('nomina_detalle.id')
        ->select('nomina_detalle.*')
        ->get();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('nomina.pdf', compact('nomina', 'detalle'))
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont'          => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'dpi'                  => 96,
            'defaultMediaType'     => 'print',
            'isFontSubsettingEnabled' => true,
            'isPhpEnabled'         => false,
        ]);

    $filename = 'nomina_' . $nomina->numero_planilla . '.pdf';
    return $pdf->download($filename);
}
}