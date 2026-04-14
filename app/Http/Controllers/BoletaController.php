<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\Nomina;
use App\Models\NominaDetalle;
use App\Models\FirmaDigital;
use App\Models\StoreUser;
use App\Models\EmployeeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BoletaController extends Controller
{
    /**
     * Vista principal — Conta ve todas las boletas por tienda/admin
     */
    public function index(Request $request)
    {
        $mes    = $request->mes    ?? now()->month;
        $anio   = $request->anio   ?? now()->year;
        $tipo   = $request->tipo   ?? null; // PRIMERA_QUINCENA o SEGUNDA_QUINCENA
        $tienda = $request->tienda ?? null;

        $query = Boleta::with([
            'empleado.user',
            'empleado.store',
            'empleado.department',
            'nomina',
            'firmadaPor'
        ])
        ->whereHas('nomina', fn($q) => $q->where('mes', $mes)->where('anio', $anio));

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if ($tienda) {
            $query->whereHas('empleado', fn($q) => $q->where('store_id', $tienda));
        }

        $boletas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Estadísticas
        $stats = [
            'total'    => $query->count(),
            'firmadas' => $query->where('estado', 'FIRMADA')->count(),
            'pendientes' => $query->where('estado', 'PENDIENTE')->count(),
        ];

        $meses = [
            1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril',
            5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
            9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'
        ];

        $tiendas = \App\Models\Store::where('is_active', true)
            ->whereNotIn('oracle_store_no', [0, 99, 100])
            ->orderBy('oracle_store_no')
            ->get();

        return view('pages.boletas.index', compact(
            'boletas', 'stats', 'mes', 'anio', 'tipo', 'tienda', 'meses', 'tiendas'
        ));
    }

    /**
     * Vista para usuario de tienda — ve boletas de su tienda
     */
    public function misTienda(Request $request)
    {
        $user = Auth::user();

        // Obtener tienda del usuario
        $storeUser = StoreUser::where('user_id', $user->id)->first();

        if (!$storeUser) {
            abort(403, 'No tienes una tienda asignada.');
        }

        $mes  = $request->mes  ?? now()->month;
        $anio = $request->anio ?? now()->year;

        $boletas = Boleta::with(['empleado.user', 'nomina'])
            ->whereHas('empleado', fn($q) => $q->where('store_id', $storeUser->store_id))
            ->whereHas('nomina', fn($q) => $q->where('mes', $mes)->where('anio', $anio))
            ->orderBy('estado')
            ->orderBy('created_at', 'desc')
            ->get();

        $meses = [
            1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril',
            5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
            9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'
        ];

        return view('pages.boletas.tienda', compact('boletas', 'mes', 'anio', 'meses', 'storeUser'));
    }

    /**
     * Vista para empleado admin — ve solo sus boletas
     */
    public function misRecibos(Request $request)
    {
        $user   = Auth::user();
        $empleo = $user->employeeDetail;

        if (!$empleo) {
            abort(403, 'No tienes un expediente asignado.');
        }

        $mes  = $request->mes  ?? now()->month;
        $anio = $request->anio ?? now()->year;

        $boletas = Boleta::with(['nomina'])
            ->where('employee_detail_id', $empleo->id)
            ->whereHas('nomina', fn($q) => $q->where('mes', $mes)->where('anio', $anio))
            ->orderBy('created_at', 'desc')
            ->get();

        $meses = [
            1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril',
            5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
            9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'
        ];

        return view('pages.boletas.mis-recibos', compact('boletas', 'mes', 'anio', 'meses'));
    }

    /**
     * Ver boleta individual (inline PDF)
     */
    public function ver(Boleta $boleta)
    {
        $this->autorizarVer($boleta);

        $boleta->load(['detalle', 'empleado.user', 'empleado.store', 'empleado.department', 'empleado.designation', 'nomina']);

        $detalle  = $boleta->detalle;
        $empleado = $boleta->empleado;
        $nomina   = $boleta->nomina;
        $firma    = null;

        if ($boleta->estado === 'FIRMADA' && $boleta->firmada_by) {
            $firma = FirmaDigital::where('user_id', $boleta->firmada_by)->first();
        }

        $meses = [
            1=>'ENERO', 2=>'FEBRERO', 3=>'MARZO', 4=>'ABRIL',
            5=>'MAYO', 6=>'JUNIO', 7=>'JULIO', 8=>'AGOSTO',
            9=>'SEPTIEMBRE', 10=>'OCTUBRE', 11=>'NOVIEMBRE', 12=>'DICIEMBRE'
        ];

        $logoPath = public_path('images/logo3.png');
        $logoSrc  = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.boletas.pdf', compact(
            'boleta', 'detalle', 'empleado', 'nomina', 'firma', 'meses', 'logoSrc'
        ))->setPaper([0, 0, 226.77, 600], 'portrait'); // Tamaño personalizado para el recibo

        return $pdf->stream('boleta-' . $boleta->id . '.pdf');
    }

    /**
     * Registrar firma digital del usuario
     */
    public function registrarFirma(Request $request)
    {
        $request->validate([
            'firma_svg' => 'required|string',
        ]);

        $user = Auth::user();

        FirmaDigital::updateOrCreate(
            ['user_id' => $user->id],
            [
                'firma_svg'       => $request->firma_svg,
                'nombre_firmante' => $user->fullname,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Firma registrada correctamente']);
    }

    /**
     * Firmar una boleta
     */
    public function firmar(Request $request, Boleta $boleta)
    {
        $this->autorizarFirmar($boleta);

        $user  = Auth::user();
        $firma = FirmaDigital::where('user_id', $user->id)->first();

        if (!$firma) {
            return response()->json([
                'success' => false,
                'message' => 'Debes registrar tu firma primero.'
            ], 422);
        }

        if ($boleta->estado === 'FIRMADA') {
            return response()->json([
                'success' => false,
                'message' => 'Esta boleta ya fue firmada.'
            ], 422);
        }

        // Generar PDF con firma estampada
        $pdfFirmadoPath = $this->estamparFirma($boleta, $firma);

        $boleta->update([
            'estado'          => 'FIRMADA',
            'pdf_firmado_path' => $pdfFirmadoPath,
            'firmada_at'      => now(),
            'firmada_by'      => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento firmado exitosamente'
        ]);
    }

    /**
     * Generar boletas al cerrar una nómina
     */
    public function generarBoletas(Nomina $nomina): void
    {
        $detalles = NominaDetalle::where('nomina_id', $nomina->id)
            ->whereNull('observacion') // excluir pendientes
            ->get();

        foreach ($detalles as $detalle) {
            // Evitar duplicados
            $existe = Boleta::where('nomina_detalle_id', $detalle->id)->first();
            if ($existe) continue;

            Boleta::create([
                'nomina_id'          => $nomina->id,
                'nomina_detalle_id'  => $detalle->id,
                'employee_detail_id' => $detalle->employee_detail_id,
                'tipo'               => $nomina->tipo,
                'estado'             => 'PENDIENTE',
            ]);
        }
    }

    /**
     * Estampar firma en el PDF
     */
    private function estamparFirma(Boleta $boleta, FirmaDigital $firma): string
    {
        // Por ahora guardamos referencia — el PDF se genera en la vista
        // En siguiente iteración integramos con la librería PDF
        return $boleta->pdf_path ?? '';
    }

    /**
     * Verificar autorización para ver boleta
     */
    private function autorizarVer(Boleta $boleta): void
    {
        $user = Auth::user();

        if ($user->hasRole(['Administrador', 'Contabilidad', 'Nómina'])) return;

        // Usuario de tienda — verificar que la boleta sea de su tienda
        $storeUser = StoreUser::where('user_id', $user->id)->first();
        if ($storeUser) {
            $empStore = $boleta->empleado->store_id ?? null;
            if ($empStore === $storeUser->store_id) return;
        }

        // Empleado admin — solo sus propias boletas
        $empleo = $user->employeeDetail;
        if ($empleo && $boleta->employee_detail_id === $empleo->id) return;

        abort(403);
    }

    /**
     * Verificar autorización para firmar boleta
     */
    private function autorizarFirmar(Boleta $boleta): void
    {
        $user = Auth::user();

        // Usuario de tienda
        $storeUser = StoreUser::where('user_id', $user->id)->first();
        if ($storeUser) {
            $empStore = $boleta->empleado->store_id ?? null;
            if ($empStore === $storeUser->store_id) return;
        }

        // Empleado admin
        $empleo = $user->employeeDetail;
        if ($empleo && $boleta->employee_detail_id === $empleo->id) return;

        abort(403);
    }

    public function verificarFirma()
    {
        $firma = FirmaDigital::where('user_id', Auth::id())->first();
        return response()->json([
            'tiene_firma' => !!$firma,
            'firma_svg'   => $firma?->firma_svg ?? null,
        ]);
    }
}