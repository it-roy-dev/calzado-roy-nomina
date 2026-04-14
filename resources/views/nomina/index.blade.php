@extends('layouts.app')

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }

.nom-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 24px;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35);
    position: relative;
    overflow: hidden;
}
.nom-hero::before {
    content:'';position:absolute;top:-60px;right:-60px;
    width:280px;height:280px;border-radius:50%;
    background:rgba(255,255,255,0.04);
}
.nom-hero h4 { color:#fff;font-weight:800;font-size:22px;margin:0 0 6px; }
.nom-hero p  { color:rgba(255,255,255,0.7);font-size:13px;margin:0; }

.stat-card {
    background:#fff;border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    padding:20px 24px;
    display:flex;align-items:center;gap:16px;
}
.stat-card .stat-icon {
    width:48px;height:48px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:20px;color:#fff;flex-shrink:0;
}
.stat-card .stat-val { font-size:22px;font-weight:800;color:#0f2456; }
.stat-card .stat-label { font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px; }

.nom-card {
    background:#fff;border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    margin-bottom:12px;
    padding:18px 24px;
    display:flex;align-items:center;gap:16px;
    transition:box-shadow 0.2s;
    text-decoration:none;
}
.nom-card:hover { box-shadow:0 4px 20px rgba(30,60,114,0.12);text-decoration:none; }
.nom-card .nom-num {
    width:48px;height:48px;border-radius:12px;
    background:linear-gradient(135deg,#1e3c72,#2a5298);
    color:#fff;display:flex;align-items:center;justify-content:center;
    font-size:11px;font-weight:700;flex-shrink:0;text-align:center;
    line-height:1.2;
}
.nom-card .nom-tipo {
    font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;
}
.badge-borrador  { background:#fef3c7;color:#92400e; }
.badge-cerrada   { background:#d1fae5;color:#065f46; }
.badge-pagada    { background:#dbeafe;color:#1e40af; }

.generar-card {
    background:#fff;border-radius:14px;
    border:2px dashed #cbd5e1;
    padding:24px;margin-bottom:20px;
}
.generar-card h6 { font-weight:700;color:#0f2456;margin-bottom:16px;font-size:14px; }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Nómina</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Nómina</li>
        </ul>
    </x-breadcrumb>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Hero --}}
    <div class="nom-hero">
        <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:1">
            <div>
                <h4><i class="fa-solid fa-money-bill-wave me-2"></i>Módulo de Nómina</h4>
                <p>Internacional de Calzado S.A. — Guatemala</p>
            </div>
            <div class="text-end">
                <div style="color:rgba(255,255,255,0.6);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px">Total nóminas</div>
                <div style="color:#fff;font-size:28px;font-weight:800">{{ $nominas->total() }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $ultimaNomina = $nominas->first();
        @endphp
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <div class="stat-val">{{ $nominas->where('estado','CERRADA')->count() }}</div>
                    <div class="stat-label">Cerradas</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="stat-val">{{ $nominas->where('estado','BORRADOR')->count() }}</div>
                    <div class="stat-label">En borrador</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="stat-val">{{ $ultimaNomina->total_empleados ?? '—' }}</div>
                    <div class="stat-label">Últ. empleados</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <div class="stat-val" style="font-size:16px">
                        GTQ {{ $ultimaNomina ? number_format($ultimaNomina->total_liquido, 0) : '—' }}
                    </div>
                    <div class="stat-label">Últ. líquido</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Generar nómina --}}
        <div class="col-md-4">
            <div class="generar-card">
                <h6><i class="fa-solid fa-plus-circle me-2 text-primary"></i>Generar Nueva Nómina</h6>
                <form action="{{ route('nomina.generar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" style="font-size:12px;font-weight:600;color:#64748b">Tipo</label>
                        <select name="tipo" class="form-select form-select-sm">
                            <option value="PRIMERA_QUINCENA">Primera Quincena (40%)</option>
                            <option value="SEGUNDA_QUINCENA">Segunda Quincena (60%)</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:#64748b">Mes</label>
                            <select name="mes" class="form-select form-select-sm">
                                @foreach([1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'] as $num => $nombre)
                                <option value="{{ $num }}" {{ $num == date('n') ? 'selected' : '' }}>{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:12px;font-weight:600;color:#64748b">Año</label>
                            <select name="anio" class="form-select form-select-sm">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm" style="border-radius:8px;font-weight:600">
                        <i class="fa-solid fa-play me-1"></i> Generar Nómina
                    </button>
                </form>
            </div>
        </div>

        {{-- Listado --}}
        <div class="col-md-8">
            <div style="font-size:13px;font-weight:700;color:#0f2456;margin-bottom:12px">
                Historial de Nóminas
            </div>

            @forelse($nominas as $nomina)
            <a href="{{ route('nomina.show', $nomina->id) }}" class="nom-card d-flex">
                <div class="nom-num">
                    #{{ $nomina->numero_planilla }}
                </div>
                <div class="flex-fill">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="font-size:14px;font-weight:700;color:#0f2456">
                            {{ $nomina->titulo }}
                        </span>
                        <span class="nom-tipo badge-{{ strtolower($nomina->estado) }}">
                            {{ $nomina->estado === 'BORRADOR' ? 'Borrador' : ($nomina->estado === 'CERRADA' ? 'Cerrada' : 'Pagada') }}
                        </span>
                    </div>
                    <div style="font-size:12px;color:#94a3b8">
                        <i class="fa-solid fa-calendar fa-xs me-1"></i>
                        Pago: {{ $nomina->fecha_pago->format('d/m/Y') }}
                        &nbsp;·&nbsp;
                        <i class="fa-solid fa-users fa-xs me-1"></i>
                        {{ $nomina->total_empleados }} empleados
                    </div>
                </div>
                <div class="text-end flex-shrink-0">
                    <div style="font-size:15px;font-weight:800;color:#10b981">
                        GTQ {{ number_format($nomina->total_liquido, 2) }}
                    </div>
                    <div style="font-size:11px;color:#94a3b8">Líquido total</div>
                </div>
            </a>
            @empty
            <div style="text-align:center;padding:40px;color:#94a3b8">
                <i class="fa-solid fa-money-bill-wave" style="font-size:36px;opacity:0.2;display:block;margin-bottom:10px"></i>
                <p style="font-size:13px;margin:0">No hay nóminas generadas aún</p>
            </div>
            @endforelse

            {{ $nominas->links() }}
        </div>
    </div>
</div>
@endsection