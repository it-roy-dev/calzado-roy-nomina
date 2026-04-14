<?php

namespace App\Http\Controllers;

use App\Models\Uniforme;
use App\Models\EmployeeDetail;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Http\Request;

class UniformeController extends Controller
{
    public function index()
    {
        $uniformes = Uniforme::with(['empleado.user', 'empleado.store', 'empleado.department'])
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = [
            'activos'  => Uniforme::where('estado', 'ACTIVO')->count(),
            'pagados'  => Uniforme::where('estado', 'PAGADO')->count(),
            'anulados' => Uniforme::where('estado', 'ANULADO')->count(),
            'total'    => Uniforme::sum('monto_total'),
            'pendiente'=> Uniforme::where('estado', 'ACTIVO')->sum('saldo_pendiente'),
        ];

        return view('uniformes.index', compact('uniformes', 'stats'));
    }

    public function create()
    {
        $empleados = User::where('type', UserType::EMPLOYEE)
            ->with(['employeeDetail.store', 'employeeDetail.department'])
            ->whereHas('employeeDetail', function($q) {
                $q->where('oracle_active', true)
                  ->whereNotIn('status', ['DAR_DE_BAJA', 'INACTIVO']);
            })
            ->orderBy('firstname')
            ->get();

        return view('uniformes.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_detail_id' => 'required|exists:employee_details,id',
            'fecha_entrega'      => 'required|date',
            'monto_total'        => 'required|numeric|min:1',
            'num_cuotas'         => 'required|integer|min:1|max:24',
            'descripcion'        => 'nullable|string|max:255',
        ]);

        $monto_cuota = round($request->monto_total / $request->num_cuotas, 2);

        Uniforme::create([
            'employee_detail_id' => $request->employee_detail_id,
            'fecha_entrega'      => $request->fecha_entrega,
            'monto_total'        => $request->monto_total,
            'num_cuotas'         => $request->num_cuotas,
            'cuotas_pagadas'     => 0,
            'monto_cuota'        => $monto_cuota,
            'saldo_pendiente'    => $request->monto_total,
            'estado'             => 'ACTIVO',
            'descripcion'        => $request->descripcion,
            'created_by'         => auth()->id(),
        ]);

        return redirect()
            ->route('uniformes.index')
            ->with('success', 'Uniforme registrado correctamente.');
    }

    public function anular(Uniforme $uniforme)
    {
        if ($uniforme->estado === 'PAGADO') {
            return back()->with('error', 'No se puede anular un uniforme ya pagado.');
        }

        $uniforme->update(['estado' => 'ANULADO']);

        return back()->with('success', 'Uniforme anulado correctamente.');
    }
}