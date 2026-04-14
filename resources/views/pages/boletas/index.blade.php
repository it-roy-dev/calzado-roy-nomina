@extends('layouts.app')

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }
.bol-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%);
    border-radius: 18px; padding: 0; margin-bottom: 22px;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35); overflow: hidden;
}
.bol-hero-inner { padding: 24px 32px; }
.bol-hero-stats {
    background: rgba(0,0,0,0.18); border-top: 1px solid rgba(255,255,255,0.08);
    padding: 14px 32px; display: flex; gap: 0;
}
.hero-stat { flex:1; text-align:center; padding:0 16px; border-right:1px solid rgba(255,255,255,0.1); }
.hero-stat:last-child { border-right: none; }
.hero-stat .hs-val { font-size:22px; font-weight:800; color:#fff; display:block; }
.hero-stat .hs-label { font-size:10px; font-weight:600; color:rgba(255,255,255,0.45); text-transform:uppercase; letter-spacing:0.8px; }
.filter-card {
    background:#fff; border-radius:14px; border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05); padding:20px; margin-bottom:20px;
}
.bol-table { width:100%; border-collapse:collapse; font-size:13px; }
.bol-table thead th {
    font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:0.5px; color:#94a3b8; padding:10px 14px;
    background:#f8fafc; border-bottom:1px solid #eef2f7; white-space:nowrap;
}
.bol-table tbody td { padding:10px 14px; border-bottom:1px solid #f8fafc; color:#1e293b; vertical-align:middle; }
.bol-table tbody tr:hover td { background:#f8fbff; }
.badge-firmada { background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; }
.badge-pendiente { background:#fef3c7; color:#92400e; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; }
.bol-section { background:#fff; border-radius:14px; border:1px solid #eef2f7; box-shadow:0 2px 8px rgba(0,0,0,0.05); overflow:hidden; }
.bol-section-header { padding:14px 20px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; background:linear-gradient(to right,#f8faff,#fff); }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Recibos de Pago</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Recibos de Pago</li>
        </ul>
    </x-breadcrumb>

    {{-- Hero --}}
    <div class="bol-hero">
        <div class="bol-hero-inner">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:6px">
                        Módulo de Recibos
                    </div>
                    <div style="font-size:22px;font-weight:800;color:#fff">
                        Recibos de Pago — {{ $meses[$mes] }} {{ $anio }}
                    </div>
                </div>
            </div>
        </div>
        <div class="bol-hero-stats">
            <div class="hero-stat">
                <span class="hs-val">{{ $stats['total'] }}</span>
                <span class="hs-label">Total recibos</span>
            </div>
            <div class="hero-stat">
                <span class="hs-val" style="color:#10b981">{{ $stats['firmadas'] }}</span>
                <span class="hs-label">Firmados</span>
            </div>
            <div class="hero-stat">
                <span class="hs-val" style="color:#f59e0b">{{ $stats['pendientes'] }}</span>
                <span class="hs-label">Pendientes</span>
            </div>
            <div class="hero-stat">
                <span class="hs-val">
                    {{ $stats['total'] > 0 ? round(($stats['firmadas'] / $stats['total']) * 100) : 0 }}%
                </span>
                <span class="hs-label">Completado</span>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('boletas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:12px;color:#64748b;font-weight:600">Mes</label>
                <select name="mes" class="form-select form-select-sm">
                    @foreach($meses as $num => $nombre)
                        <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-600" style="font-size:12px;color:#64748b;font-weight:600">Año</label>
                <select name="anio" class="form-select form-select-sm">
                    @foreach([2024,2025,2026,2027] as $y)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:12px;color:#64748b;font-weight:600">Tipo</label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Ambas quincenas</option>
                    <option value="PRIMERA_QUINCENA" {{ $tipo === 'PRIMERA_QUINCENA' ? 'selected' : '' }}>Primera Quincena</option>
                    <option value="SEGUNDA_QUINCENA" {{ $tipo === 'SEGUNDA_QUINCENA' ? 'selected' : '' }}>Segunda Quincena</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:12px;color:#64748b;font-weight:600">Tienda</label>
                <select name="tienda" class="form-select form-select-sm">
                    <option value="">Todas las tiendas</option>
                    @foreach($tiendas as $t)
                        <option value="{{ $t->id }}" {{ $tienda == $t->id ? 'selected' : '' }}>
                            {{ $t->oracle_store_no }} — {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bol-section">
        <div class="bol-section-header">
            <h6 style="font-size:13px;font-weight:700;color:#0f2456;margin:0;display:flex;align-items:center;gap:10px">
                <span style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#1e3c72,#2a5298);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px">
                    <i class="fa-solid fa-file-invoice fa-xs"></i>
                </span>
                Detalle de Recibos
            </h6>
        </div>
        <div style="overflow-x:auto">
            <table class="bol-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empleado</th>
                        <th>Tienda / Depto</th>
                        <th>Puesto</th>
                        <th>Quincena</th>
                        <th>Estado</th>
                        <th>Firmado por</th>
                        <th>Fecha firma</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletas as $i => $boleta)
                    @php
                        $emp  = $boleta->empleado;
                        $user = $emp->user ?? null;
                        $ubic = $emp->store->name ?? ($emp->department->name ?? '—');
                    @endphp
                    <tr>
                        <td style="color:#94a3b8;font-weight:600">{{ $boletas->firstItem() + $i }}</td>
                        <td style="font-weight:600">{{ $user->fullname ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $ubic }}</td>
                        <td style="font-size:12px;color:#64748b">{{ $emp->designation->name ?? '—' }}</td>
                        <td>
                            <span style="font-size:11px;background:{{ $boleta->tipo === 'PRIMERA_QUINCENA' ? '#dbeafe' : '#f0fdf4' }};color:{{ $boleta->tipo === 'PRIMERA_QUINCENA' ? '#1e40af' : '#166534' }};padding:3px 8px;border-radius:5px;font-weight:600">
                                {{ $boleta->tipo === 'PRIMERA_QUINCENA' ? '1ra Quincena' : '2da Quincena' }}
                            </span>
                        </td>
                        <td>
                            @if($boleta->estado === 'FIRMADA')
                                <span class="badge-firmada"><i class="fa-solid fa-check me-1"></i>Firmado</span>
                            @else
                                <span class="badge-pendiente"><i class="fa-solid fa-clock me-1"></i>Pendiente</span>
                            @endif
                        </td>
                        <td style="font-size:12px">{{ $boleta->firmadaPor->fullname ?? '—' }}</td>
                        <td style="font-size:12px;color:#64748b">
                            {{ $boleta->firmada_at ? $boleta->firmada_at->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td>
                            <a href="{{ route('boletas.ver', $boleta->id) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary" style="font-size:11px;border-radius:6px">
                                <i class="fa-solid fa-eye me-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">
                            <i class="fa-solid fa-file-invoice" style="font-size:32px;opacity:0.2;display:block;margin-bottom:10px"></i>
                            No hay recibos para este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($boletas->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f1f5f9">
            {{ $boletas->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection