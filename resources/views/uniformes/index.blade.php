@extends('layouts.app')

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }

.uni-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%);
    border-radius: 18px; padding: 24px 32px;
    margin-bottom: 24px;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35);
    position: relative; overflow: hidden;
}
.uni-hero::before {
    content:'';position:absolute;top:-60px;right:-60px;
    width:280px;height:280px;border-radius:50%;
    background:rgba(255,255,255,0.04);
}
.uni-hero h4 { color:#fff;font-weight:800;font-size:20px;margin:0; }
.uni-hero p  { color:rgba(255,255,255,0.65);font-size:13px;margin:0; }

.stat-card {
    background:#fff;border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    padding:18px 20px;
    display:flex;align-items:center;gap:14px;
}
.stat-icon {
    width:46px;height:46px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:18px;color:#fff;flex-shrink:0;
}
.stat-val { font-size:22px;font-weight:800;color:#0f2456;line-height:1; }
.stat-label { font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px; }

.uni-card {
    background:#fff;border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    overflow:hidden;margin-bottom:20px;
}
.uni-card-header {
    padding:15px 20px;border-bottom:1px solid #f1f5f9;
    display:flex;align-items:center;justify-content:space-between;
    background:linear-gradient(to right,#f8faff,#fff);
}
.uni-card-title {
    font-size:13px;font-weight:700;color:#0f2456;
    margin:0;display:flex;align-items:center;gap:10px;
}
.sec-icon {
    width:32px;height:32px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;color:#fff;flex-shrink:0;
}

table.uni-table { width:100%;border-collapse:collapse; }
.uni-table thead th {
    font-size:10px;font-weight:700;text-transform:uppercase;
    letter-spacing:0.5px;color:#94a3b8;
    padding:11px 16px;background:#f8fafc;
    border-bottom:1px solid #eef2f7;white-space:nowrap;
}
.uni-table tbody td {
    padding:11px 16px;border-bottom:1px solid #f8fafc;
    font-size:13px;color:#1e293b;vertical-align:middle;
}
.uni-table tbody tr:hover td { background:#f8fbff; }
.uni-table tbody tr:last-child td { border-bottom:none; }

.progress-bar-wrap {
    background:#f1f5f9;border-radius:20px;height:6px;
    overflow:hidden;width:80px;display:inline-block;
}
.progress-bar-fill {
    height:100%;border-radius:20px;
    background:linear-gradient(90deg,#10b981,#059669);
    transition:width 0.3s;
}
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Uniformes</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Uniformes</li>
        </ul>
    </x-breadcrumb>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Hero --}}
    <div class="uni-hero">
        <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:2">
            <div>
                <h4><i class="fa-solid fa-shirt me-2"></i>Control de Uniformes</h4>
                <p>Registro de entregas y descuentos automáticos en nómina</p>
            </div>
            <a href="{{ route('uniformes.create') }}"
                class="btn" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:10px;font-size:13px;font-weight:600;padding:9px 18px">
                <i class="fa-solid fa-plus me-1"></i> Registrar Entrega
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="stat-val">{{ $stats['activos'] }}</div>
                    <div class="stat-label">En descuento</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="stat-val">{{ $stats['pagados'] }}</div>
                    <div class="stat-label">Pagados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <div class="stat-val" style="font-size:16px">GTQ {{ number_format($stats['pendiente'], 2) }}</div>
                    <div class="stat-label">Saldo pendiente</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <div class="stat-val" style="font-size:16px">GTQ {{ number_format($stats['total'], 2) }}</div>
                    <div class="stat-label">Total entregado</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="uni-card">
        <div class="uni-card-header">
            <h6 class="uni-card-title">
                <span class="sec-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                    <i class="fa-solid fa-shirt fa-xs"></i>
                </span>
                Registro de Uniformes
            </h6>
        </div>
        <div style="overflow-x:auto">
            <table class="uni-table">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Código</th>
                        <th>Ubicación</th>
                        <th>Fecha entrega</th>
                        <th>Descripción</th>
                        <th>Monto total</th>
                        <th>Cuota mensual</th>
                        <th>Progreso</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uniformes as $u)
                    @php
                        $emp  = $u->empleado;
                        $user = $emp->user ?? null;
                        $ubi  = $emp->store->name ?? ($emp->department->name ?? '—');
                        $pct  = $u->num_cuotas > 0 ? round(($u->cuotas_pagadas / $u->num_cuotas) * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;color:#0f2456">{{ $user->fullname ?? '—' }}</div>
                        </td>
                        <td>
                            @if($emp->emp_code)
                            <span class="badge" style="background:{{ str_starts_with($emp->emp_code,'A-') ? '#3b82f6' : '#1e293b' }};color:#fff;font-size:11px">
                                {{ $emp->emp_code }}
                            </span>
                            @else
                            <span style="color:#94a3b8;font-size:12px">{{ $emp->oracle_emp_code ?? '—' }}</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#64748b">{{ $ubi }}</td>
                        <td style="font-size:12px">{{ $u->fecha_entrega->format('d/m/Y') }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $u->descripcion ?? '—' }}</td>
                        <td style="font-weight:600">GTQ {{ number_format($u->monto_total, 2) }}</td>
                        <td>GTQ {{ number_format($u->monto_cuota, 2) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-bar-wrap">
                                    <div class="progress-bar-fill" style="width:{{ $pct }}%"></div>
                                </div>
                                <span style="font-size:11px;color:#64748b;font-weight:600">
                                    {{ $u->cuotas_pagadas }}/{{ $u->num_cuotas }}
                                </span>
                            </div>
                        </td>
                        <td style="font-weight:700;color:{{ $u->saldo_pendiente > 0 ? '#ef4444' : '#10b981' }}">
                            GTQ {{ number_format($u->saldo_pendiente, 2) }}
                        </td>
                        <td>
                            @if($u->estado === 'ACTIVO')
                            <span class="badge bg-warning text-dark">En descuento</span>
                            @elseif($u->estado === 'PAGADO')
                            <span class="badge bg-success">Pagado</span>
                            @else
                            <span class="badge bg-secondary">Anulado</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            @if($u->estado === 'ACTIVO')
                            <form action="{{ route('uniformes.anular', $u->id) }}" method="POST"
                                onsubmit="return confirm('¿Anular este uniforme?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:11px;border-radius:7px">
                                    <i class="fa-solid fa-ban me-1"></i> Anular
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:40px;color:#94a3b8">
                            <i class="fa-solid fa-shirt" style="font-size:32px;opacity:0.2;display:block;margin-bottom:10px"></i>
                            <p style="font-size:13px;margin:0">Sin uniformes registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($uniformes->hasPages())
        <div style="padding:12px 20px;background:#f8fafc;border-top:1px solid #f1f5f9">
            {{ $uniformes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection