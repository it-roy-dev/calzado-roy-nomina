@extends('layouts.app')

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }

/* ══════════════════════════════════════
   ANIMACIONES DE ENTRADA
══════════════════════════════════════ */
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}
@keyframes countUp {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
@keyframes pulseGreen {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
    50%       { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
}

.anim-1 { animation: fadeSlideUp 0.45s ease both; }
.anim-2 { animation: fadeSlideUp 0.45s 0.08s ease both; }
.anim-3 { animation: fadeSlideUp 0.45s 0.16s ease both; }
.anim-4 { animation: fadeSlideUp 0.45s 0.24s ease both; }
.anim-5 { animation: fadeSlideUp 0.45s 0.32s ease both; }
.anim-6 { animation: fadeSlideUp 0.45s 0.40s ease both; }

/* ══════════════════════════════════════
   HERO HEADER
══════════════════════════════════════ */
.emp-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%) !important;
    border-radius: 18px !important;
    padding: 0 !important;
    margin-bottom: 22px !important;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35) !important;
    position: relative;
}
.emp-hero-inner {
    padding: 28px 32px;
    position: relative;
    z-index: 2;
}
/* Decoración geométrica de fondo */
.emp-hero-deco {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    overflow: hidden;
    z-index: 1;
}
.emp-hero-deco span {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}
.emp-hero-deco span:nth-child(1) { width:320px;height:320px;top:-100px;right:-60px; }
.emp-hero-deco span:nth-child(2) { width:180px;height:180px;bottom:-60px;right:25%; background:rgba(255,255,255,0.03); }
.emp-hero-deco span:nth-child(3) { width:80px;height:80px;top:20px;right:38%; background:rgba(255,255,255,0.05); }

/* Franja inferior de stats */
.emp-hero-stats {
    background: rgba(0,0,0,0.18);
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 14px 32px;
    display: flex;
    gap: 0;
    position: relative;
    z-index: 2;
}
.hero-stat {
    flex: 1;
    text-align: center;
    padding: 0 16px;
    border-right: 1px solid rgba(255,255,255,0.1);
}
.hero-stat:last-child { border-right: none; }
.hero-stat .hs-val {
    font-size: 20px;
    font-weight: 800;
    color: #fff;
    display: block;
    animation: countUp 0.6s 0.5s ease both;
}
.hero-stat .hs-label {
    font-size: 10px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.emp-avatar {
    width: 86px !important; height: 86px !important;
    border-radius: 16px !important;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.25) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3), 0 0 0 4px rgba(255,255,255,0.06) !important;
    flex-shrink: 0;
}
.emp-avatar-online {
    position: relative;
    flex-shrink: 0;
}
.emp-avatar-online::after {
    content: '';
    position: absolute;
    bottom: -2px; right: -2px;
    width: 14px; height: 14px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid #1e3c72;
    animation: pulseGreen 2s infinite;
}
.emp-name {
    color: #fff !important;
    font-size: 22px !important;
    font-weight: 800 !important;
    margin: 0 0 8px !important;
    letter-spacing: -0.5px;
    line-height: 1.2;
}
.emp-meta {
    color: rgba(255,255,255,0.7) !important;
    font-size: 13px !important;
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 5px;
    font-weight: 500;
}
.emp-meta i { font-size: 11px; opacity: 0.8; }
.emp-chip {
    display: inline-flex;
    align-items: center;
    font-size: 11px !important;
    padding: 4px 11px !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
    letter-spacing: 0.2px;
}
.hero-action-btn {
    font-size: 13px !important;
    font-weight: 600 !important;
    border-radius: 10px !important;
    padding: 9px 18px !important;
    transition: all 0.2s !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.hero-action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.25) !important; }

/* ══════════════════════════════════════
   TABS
══════════════════════════════════════ */
.emp-tabs-wrap {
    background: #fff !important;
    border-radius: 14px !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.04) !important;
    margin-bottom: 20px !important;
    overflow: hidden;
}
.emp-tabs .nav-link {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #64748b !important;
    padding: 15px 20px !important;
    border: none !important;
    border-bottom: 3px solid transparent !important;
    border-radius: 0 !important;
    display: inline-flex !important;
    align-items: center;
    gap: 7px;
    transition: all 0.2s !important;
    white-space: nowrap;
}
.emp-tabs .nav-link i { font-size: 12px; }
.emp-tabs .nav-link:hover {
    color: #1e3c72 !important;
    background: #f8fafc !important;
}
.emp-tabs .nav-link.active {
    color: #1e3c72 !important;
    border-bottom-color: #1e3c72 !important;
    background: transparent !important;
}

/* ══════════════════════════════════════
   SECCIONES / CARDS
══════════════════════════════════════ */
.info-section {
    background: #fff !important;
    border-radius: 14px !important;
    border: 1px solid #eef2f7 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
    margin-bottom: 20px !important;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.info-section:hover {
    box-shadow: 0 4px 16px rgba(30,60,114,0.1) !important;
}
.info-sec-header {
    padding: 15px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to right, #f8faff, #fff);
}
.info-sec-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: #0f2456 !important;
    margin: 0 !important;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sec-icon {
    width: 32px; height: 32px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.edit-btn-sm {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #94a3b8;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none !important;
    font-size: 11px;
}
.edit-btn-sm:hover {
    background: #1e3c72 !important;
    color: #fff !important;
    border-color: #1e3c72 !important;
    transform: scale(1.05);
}

/* ══════════════════════════════════════
   FILAS DE INFORMACIÓN
══════════════════════════════════════ */
.i-row {
    display: grid !important;
    grid-template-columns: 165px 1fr !important;
    align-items: center;
    padding: 11px 20px !important;
    border-bottom: 1px solid #f8fafc;
    gap: 16px;
    transition: background 0.15s, transform 0.15s;
    position: relative;
}
.i-row::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: transparent;
    transition: background 0.15s;
    border-radius: 0 2px 2px 0;
}
.i-row:hover { background: #f8fbff !important; }
.i-row:hover::before { background: #1e3c72; }
.i-row:last-child { border-bottom: none !important; }
.i-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #94a3b8 !important;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.i-label i { font-size: 11px; width: 12px; text-align: center; }
.i-val {
    font-size: 13.5px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
}

/* ══════════════════════════════════════
   ORACLE CARD
══════════════════════════════════════ */
.oracle-card {
    background: linear-gradient(135deg, #0a0f1e 0%, #0f2456 100%) !important;
    border-radius: 14px !important;
    padding: 22px !important;
    margin-bottom: 20px;
    border: 1px solid rgba(255,255,255,0.06) !important;
    box-shadow: 0 4px 20px rgba(10,15,30,0.4) !important;
    position: relative;
    overflow: hidden;
}
.oracle-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(42,82,152,0.3);
}
.oracle-card .oc-title {
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
}
.oracle-card .oc-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.08);
}
.oracle-card .oc-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    position: relative;
}
.oracle-card .oc-item .oc-label {
    font-size: 10px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    margin-bottom: 4px;
}
.oracle-card .oc-item .oc-val {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
}

/* ══════════════════════════════════════
   SALARY GRID
══════════════════════════════════════ */
.sal-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    position: relative;
}
.sal-cell {
    padding: 18px 22px;
    border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}
.sal-cell:hover { background: #f8fbff; }
.sal-cell:nth-child(3n) { border-right: none; }
.sal-cell:nth-last-child(-n+3) { border-bottom: none; }
.sal-cell .sc-label {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 6px;
}
.sal-cell .sc-val {
    font-size: 18px;
    font-weight: 800;
    color: #0f2456;
}
.sal-cell .sc-val.money {
    color: #10b981;
}
.sal-cell .sc-val.money::before {
    content: 'GTQ ';
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
}

/* ══════════════════════════════════════
   FAMILY TABLE
══════════════════════════════════════ */
.fam-table { width: 100%; border-collapse: collapse; }
.fam-table th {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #94a3b8;
    padding: 11px 18px;
    background: #f8fafc;
    border-bottom: 1px solid #eef2f7;
}
.fam-table td {
    padding: 12px 18px;
    border-bottom: 1px solid #f8fafc;
    font-size: 13px;
    color: #1e293b;
    transition: background 0.12s;
}
.fam-table tr:last-child td { border-bottom: none; }
.fam-table tr:hover td { background: #f8fbff; }

/* ══════════════════════════════════════
   EMPTY STATE
══════════════════════════════════════ */
.emp-empty {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}
.emp-empty i {
    font-size: 36px;
    margin-bottom: 12px;
    opacity: 0.2;
    display: block;
}
.emp-empty p { font-size: 13px; margin: 0; font-weight: 500; }

/* Etiqueta de subsección */
.sub-label {
    padding: 8px 20px 3px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #cbd5e1;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
}
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Perfil de Empleado</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.list') }}">Empleados</a></li>
            <li class="breadcrumb-item active">{{ $user->fullname }}</li>
        </ul>
    </x-breadcrumb>

    @php
        $empCode = $employee->emp_code ?? null;
        $status  = $employee->status ?? 'PENDIENTE';
        $statusMap = [
            'PENDIENTE'   => ['label' => 'Pendiente',   'bg' => '#f59e0b', 'text' => '#000'],
            'COMPLETO'    => ['label' => 'Completo',    'bg' => '#10b981', 'text' => '#fff'],
            'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'bg' => '#ef4444', 'text' => '#fff'],
            'INACTIVO'    => ['label' => 'Inactivo',    'bg' => '#64748b', 'text' => '#fff'],
        ];
        $s = $statusMap[$status] ?? $statusMap['PENDIENTE'];
        $codeBg = $empCode ? (str_starts_with($empCode,'A-') ? '#3b82f6' : '#334155') : '#64748b';

        if (!empty($employee->department_id) && $employee->department) {
            $ubicIcon = 'fa-building'; $ubicNombre = $employee->department->name;
        } elseif (!empty($employee->store_id) && $employee->store) {
            $ubicIcon = 'fa-store'; $ubicNombre = $employee->store->name;
        } else {
            $ubicIcon = 'fa-circle-question'; $ubicNombre = 'Sin asignar';
        }

        $emerP = $employee->emergency_contacts['primary'] ?? null;
        $emerS = $employee->emergency_contacts['secondary'] ?? null;

        // Estadísticas para el hero
        $diasTrabajados = $employee->date_joined
            ? \Carbon\Carbon::parse($employee->date_joined)->diffInDays(now())
            : null;

        // Antigüedad legible
        $antiguedad = null;
        $antiguedadLabel = 'Antigüedad';
        if ($employee->date_joined) {
            $diff = \Carbon\Carbon::parse($employee->date_joined)->diff(now());
            if ($diff->y >= 1) {
                $antiguedad = $diff->y . ($diff->m > 0 ? '.' . $diff->m : '');
                $antiguedadLabel = $diff->y == 1 ? '1 año' : $diff->y . ' años';
                if ($diff->m > 0) $antiguedadLabel .= ' ' . $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
            } elseif ($diff->m >= 1) {
                $antiguedad = $diff->m;
                $antiguedadLabel = $diff->m == 1 ? '1 mes' : $diff->m . ' meses';
                if ($diff->d > 0) $antiguedadLabel .= ' ' . $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
            } elseif ($diff->d >= 1) {
                $antiguedad = $diff->d;
                $antiguedadLabel = $diff->d == 1 ? '1 día' : $diff->d . ' días';
            } else {
                $antiguedad = $diff->h;
                $antiguedadLabel = $diff->h . ' hora' . ($diff->h != 1 ? 's' : '');
            }
        }
        $edad = $employee->dob
            ? \Carbon\Carbon::parse($employee->dob)->age
            : null;

        $basisLabels = ['monthly'=>'Mensual','weekly'=>'Semanal','hourly'=>'Por hora','contract'=>'Por contrato'];
        $pmLabels    = ['bank'=>'Transferencia bancaria','cheque'=>'Cheque','cash'=>'Efectivo'];
    @endphp

    {{-- ══ HERO ══ --}}
    <div class="emp-hero anim-1">
        <div class="emp-hero-deco">
            <span></span><span></span><span></span>
        </div>
        <div class="emp-hero-inner">
            <div class="d-flex align-items-center gap-4">
                <div class="emp-avatar-online">
                    <img src="{{ !empty($user->avatar) ? asset('storage/users/'.$user->avatar) : asset('images/user.jpg') }}"
                        alt="Avatar" class="emp-avatar">
                </div>
                <div class="flex-fill">
                    <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                        <span class="emp-name">{{ $user->fullname }}</span>
                        @if($empCode)
                        <span class="emp-chip" style="background:{{ $codeBg }};color:#fff">
                            <i class="fa-solid fa-tag fa-xs"></i> {{ $empCode }}
                        </span>
                        @endif
                        <span class="emp-chip" style="background:{{ $s['bg'] }};color:{{ $s['text'] }}">
                            {{ $s['label'] }}
                        </span>
                    </div>
                    <div class="emp-meta">
                        <i class="fa-solid fa-briefcase"></i>
                        {{ $employee->designation->name ?? 'Sin puesto asignado' }}
                        <span style="color:rgba(255,255,255,0.2)">|</span>
                        <i class="fa-solid {{ $ubicIcon }}"></i>
                        {{ $ubicNombre }}
                    </div>
                    @if($user->email)
                    <div class="emp-meta">
                        <i class="fa-solid fa-envelope"></i> {{ $user->email }}
                        @if($user->phone)
                        <span style="color:rgba(255,255,255,0.2)">|</span>
                        <i class="fa-solid fa-phone"></i> {{ $user->phoneNumber }}
                        @endif
                    </div>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    @if($status === 'PENDIENTE' || !$status)
                    <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}"
                        class="hero-action-btn btn btn-warning" style="color:#000">
                        <i class="fa-solid fa-file-pen"></i> Completar Expediente
                    </a>
                    @else
                    <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}"
                        class="hero-action-btn btn" style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2)">
                        <i class="fa-solid fa-file-pen"></i> Editar Expediente
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats bar --}}
        <div class="emp-hero-stats">
            @if($antiguedad !== null)
            <div class="hero-stat">
                <span class="hs-val" style="font-size:16px">{{ $antiguedadLabel }}</span>
                <span class="hs-label">Antigüedad</span>
            </div>
            @endif
            @if($diasTrabajados !== null)
            <div class="hero-stat">
                <span class="hs-val">{{ number_format($diasTrabajados) }}</span>
                <span class="hs-label">Días trabajados</span>
            </div>
            @endif
            @if($edad !== null)
            <div class="hero-stat">
                <span class="hs-val">{{ $edad }}</span>
                <span class="hs-label">Edad</span>
            </div>
            @endif
            @if($employee->salaryDetails?->base_salary)
            <div class="hero-stat">
                <span class="hs-val" style="font-size:16px">GTQ {{ number_format($employee->salaryDetails->base_salary, 2) }}</span>
                <span class="hs-label">Salario mensual</span>
            </div>
            @endif
            <div class="hero-stat">
                <span class="hs-val" style="font-size:15px">{{ $employee->contract_type ?? '—' }}</span>
                <span class="hs-label">Tipo de contrato</span>
            </div>
        </div>
    </div>

    {{-- ══ TABS ══ --}}
    <div class="emp-tabs-wrap anim-2">
        <ul class="nav emp-tabs" style="border-bottom:1px solid #f1f5f9;flex-wrap:nowrap;overflow-x:auto">
            <li class="nav-item">
                <a href="#tp-perfil" data-bs-toggle="tab" class="nav-link active">
                    <i class="fa-solid fa-user"></i> Perfil
                </a>
            </li>
            <li class="nav-item">
                <a href="#tp-laboral" data-bs-toggle="tab" class="nav-link">
                    <i class="fa-solid fa-briefcase"></i> Datos Laborales
                </a>
            </li>
            <li class="nav-item">
                <a href="#tp-educacion" data-bs-toggle="tab" class="nav-link">
                    <i class="fa-solid fa-graduation-cap"></i> Educación
                </a>
            </li>
            <li class="nav-item">
                <a href="#tp-familia" data-bs-toggle="tab" class="nav-link">
                    <i class="fa-solid fa-people-group"></i> Familia
                </a>
            </li>
            @superadmin
            <li class="nav-item">
                <a href="#tp-salario" data-bs-toggle="tab" class="nav-link">
                    <i class="fa-solid fa-money-bill"></i> Salario
                </a>
            </li>
            @endsuperadmin
        </ul>
    </div>

    <div class="tab-content">

        {{-- ══ TAB PERFIL ══ --}}
        <div id="tp-perfil" class="tab-pane fade show active">
            <div class="row g-3">
                <div class="col-lg-6 anim-3">
                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                                    <i class="fa-solid fa-user fa-xs"></i>
                                </span>
                                Información Personal
                            </h6>
                            <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}" class="edit-btn-sm">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                        </div>
                        @if($user->phone)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-phone"></i> Teléfono</span>
                            <span class="i-val">{{ $user->phoneNumber }}</span>
                        </div>
                        @endif
                        @if($employee->phone_secondary)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-mobile"></i> Tel. adicional</span>
                            <span class="i-val">{{ $employee->phone_secondary }}</span>
                        </div>
                        @endif
                        @if($employee->personal_email)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-envelope-open"></i> Correo personal</span>
                            <span class="i-val">{{ $employee->personal_email }}</span>
                        </div>
                        @endif
                        @if($user->address)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-location-dot"></i> Dirección</span>
                            <span class="i-val">{{ $user->address }}</span>
                        </div>
                        @endif
                        @if($employee->dob)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-cake-candles"></i> Fecha nac.</span>
                            <span class="i-val">{{ format_date($employee->dob) }}</span>
                        </div>
                        @endif
                        @if($employee->birth_place)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-map-pin"></i> Lugar nac.</span>
                            <span class="i-val">{{ $employee->birth_place }}</span>
                        </div>
                        @endif
                        @if($employee->nationality)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-flag"></i> Nacionalidad</span>
                            <span class="i-val">{{ $employee->nationality }}</span>
                        </div>
                        @endif
                        @if($employee->gender)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-venus-mars"></i> Género</span>
                            <span class="i-val">{{ $employee->gender }}</span>
                        </div>
                        @endif
                        @if($employee->marital_status)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-heart"></i> Estado civil</span>
                            <span class="i-val">{{ $employee->marital_status }}</span>
                        </div>
                        @endif
                        @if($employee->religion)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-church"></i> Religión</span>
                            <span class="i-val">{{ $employee->religion }}</span>
                        </div>
                        @endif
                        @if($employee->ethnicity)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-people-group"></i> Etnia</span>
                            <span class="i-val">{{ $employee->ethnicity }}</span>
                        </div>
                        @endif
                        @if($employee->no_of_children)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-child"></i> No. de hijos</span>
                            <span class="i-val">{{ $employee->no_of_children }}</span>
                        </div>
                        @endif
                        @if($employee->disability !== null)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-wheelchair"></i> Discapacidad</span>
                            <span class="i-val">
                                @if($employee->disability)
                                    <span class="badge bg-warning text-dark">Sí</span>
                                    @if($employee->disability_description) — {{ $employee->disability_description }} @endif
                                @else No @endif
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6 anim-4">
                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
                                    <i class="fa-solid fa-id-card fa-xs"></i>
                                </span>
                                Documentos Guatemala
                            </h6>
                            <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}" class="edit-btn-sm">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                        </div>
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-id-badge"></i> DPI <span style="color:#ef4444;font-size:13px;line-height:1">*</span></span>
                            <span class="i-val">
                                @if($employee->dpi_number)
                                    {{ $employee->dpi_number }}
                                @else
                                    <span style="color:#ef4444;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:5px">
                                        <i class="fa-solid fa-circle-exclamation fa-xs"></i> Pendiente — requerido
                                    </span>
                                @endif
                            </span>
                        </div>
                        @if($employee->dpi_issued_place)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-location-dot"></i> Emisión DPI</span>
                            <span class="i-val">{{ $employee->dpi_issued_place }}</span>
                        </div>
                        @endif
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-receipt"></i> NIT <span style="color:#ef4444;font-size:13px;line-height:1">*</span></span>
                            <span class="i-val">
                                @if($employee->nit_number)
                                    {{ $employee->nit_number }}
                                @else
                                    <span style="color:#ef4444;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:5px">
                                        <i class="fa-solid fa-circle-exclamation fa-xs"></i> Pendiente — requerido
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-shield-halved"></i> No. IGSS <span style="color:#ef4444;font-size:13px;line-height:1">*</span></span>
                            <span class="i-val">
                                @if($employee->igss_number)
                                    {{ $employee->igss_number }}
                                @else
                                    <span style="color:#ef4444;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:5px">
                                        <i class="fa-solid fa-circle-exclamation fa-xs"></i> Pendiente — requerido
                                    </span>
                                @endif
                            </span>
                        </div>
                        @if($employee->irtra_number)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-ticket"></i> No. IRTRA</span>
                            <span class="i-val">{{ $employee->irtra_number }}</span>
                        </div>
                        @endif
                        @if($employee->driver_license)
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-car"></i> Licencia</span>
                            <span class="i-val">{{ $employee->driver_license }}</span>
                        </div>
                        @endif
                        @if(!$employee->dpi_number && !$employee->nit_number)
                        <div class="emp-empty"><i class="fa-solid fa-id-card"></i><p>Sin documentos registrados</p></div>
                        @endif
                    </div>

                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                                    <i class="fa-solid fa-phone fa-xs"></i>
                                </span>
                                Contacto de Emergencia
                            </h6>
                        </div>
                        @if($emerP && !empty($emerP['name']))
                            <div class="sub-label">Primario</div>
                            <div class="i-row"><span class="i-label"><i class="fa-solid fa-user"></i> Nombre</span><span class="i-val">{{ $emerP['name'] }}</span></div>
                            <div class="i-row"><span class="i-label"><i class="fa-solid fa-heart"></i> Parentesco</span><span class="i-val">{{ $emerP['relationship'] ?? '—' }}</span></div>
                            <div class="i-row"><span class="i-label"><i class="fa-solid fa-phone"></i> Teléfono</span><span class="i-val">{{ $emerP['phone'] ?? '—' }}</span></div>
                        @endif
                        @if($emerS && !empty($emerS['name']))
                            <div class="sub-label" style="margin-top:4px">Secundario</div>
                            <div class="i-row"><span class="i-label"><i class="fa-solid fa-user"></i> Nombre</span><span class="i-val">{{ $emerS['name'] }}</span></div>
                            <div class="i-row"><span class="i-label"><i class="fa-solid fa-heart"></i> Parentesco</span><span class="i-val">{{ $emerS['relationship'] ?? '—' }}</span></div>
                            <div class="i-row"><span class="i-label"><i class="fa-solid fa-phone"></i> Teléfono</span><span class="i-val">{{ $emerS['phone'] ?? '—' }}</span></div>
                        @endif
                        @if((!$emerP || empty($emerP['name'])) && (!$emerS || empty($emerS['name'])))
                        <div class="emp-empty"><i class="fa-solid fa-phone-slash"></i><p>Sin contactos de emergencia</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ TAB DATOS LABORALES ══ --}}
        <div id="tp-laboral" class="tab-pane fade">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                                    <i class="fa-solid fa-briefcase fa-xs"></i>
                                </span>
                                Datos Laborales
                            </h6>
                        </div>
                        @if($employee->date_joined)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-calendar-check"></i> Fecha ingreso</span><span class="i-val">{{ format_date($employee->date_joined) }}</span></div>
                        @endif
                        @if($employee->contract_type)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-file-contract"></i> Contrato</span><span class="i-val">{{ $employee->contract_type }}</span></div>
                        @endif
                        @if($employee->work_schedule)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-clock"></i> Horario</span><span class="i-val">{{ $employee->work_schedule }}</span></div>
                        @endif
                        @if($employee->work_hours_per_week)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-hourglass-half"></i> Horas/semana</span><span class="i-val">{{ $employee->work_hours_per_week }} horas</span></div>
                        @endif
                        @if($employee->immediate_supervisor_name)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-user-tie"></i> Jefe inmediato</span><span class="i-val">{{ $employee->immediate_supervisor_name }}</span></div>
                        @endif
                        @if($employee->termination_date)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-calendar-xmark"></i> Fecha baja</span><span class="i-val" style="color:#ef4444;font-weight:600">{{ format_date($employee->termination_date) }}</span></div>
                        @endif
                        @if($employee->termination_reason)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-circle-info"></i> Motivo retiro</span><span class="i-val">{{ $employee->termination_reason }}</span></div>
                        @endif
                    </div>

                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
                                    <i class="fa-solid fa-building-columns fa-xs"></i>
                                </span>
                                Datos Bancarios
                            </h6>
                        </div>
                        @if($employee->payment_method)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-money-bill-transfer"></i> Forma de pago</span><span class="i-val">{{ $employee->payment_method }}</span></div>
                        @endif
                        @if($employee->bank_name)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-landmark"></i> Banco</span><span class="i-val">{{ $employee->bank_name }}</span></div>
                        @endif
                        @if($employee->bank_account_number)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-hashtag"></i> No. de cuenta</span><span class="i-val">{{ $employee->bank_account_number }}</span></div>
                        @endif
                        @if($employee->bank_account_type)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-wallet"></i> Tipo de cuenta</span><span class="i-val">{{ $employee->bank_account_type }}</span></div>
                        @endif
                        @if(!$employee->payment_method && !$employee->bank_name)
                        <div class="emp-empty"><i class="fa-solid fa-building-columns"></i><p>Sin datos bancarios</p></div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="oracle-card">
                        <div class="oc-title">
                            <i class="fa-solid fa-database fa-xs"></i> Oracle PRISMA
                        </div>
                        <div class="oc-grid">
                            <div class="oc-item"><div class="oc-label">Código Oracle</div><div class="oc-val">{{ $employee->oracle_emp_code ?? '—' }}</div></div>
                            <div class="oc-item"><div class="oc-label">Código SmartHR</div><div class="oc-val">{{ $employee->emp_code ?? 'Sin asignar' }}</div></div>
                            <div class="oc-item">
                                <div class="oc-label">Estado Oracle</div>
                                <div class="oc-val">
                                    @if($employee->oracle_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="oc-item">
                                <div class="oc-label">Estado expediente</div>
                                <div class="oc-val">
                                    <span class="badge" style="background:{{ $s['bg'] }};color:{{ $s['text'] }}">{{ $s['label'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($employee->worked_abroad)
                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                                    <i class="fa-solid fa-plane fa-xs"></i>
                                </span>
                                Experiencia en el Extranjero
                            </h6>
                        </div>
                        @if($employee->foreign_country)<div class="i-row"><span class="i-label"><i class="fa-solid fa-earth-americas"></i> País</span><span class="i-val">{{ $employee->foreign_country }}</span></div>@endif
                        @if($employee->foreign_company)<div class="i-row"><span class="i-label"><i class="fa-solid fa-building"></i> Empresa</span><span class="i-val">{{ $employee->foreign_company }}</span></div>@endif
                        @if($employee->foreign_job_title)<div class="i-row"><span class="i-label"><i class="fa-solid fa-briefcase"></i> Puesto</span><span class="i-val">{{ $employee->foreign_job_title }}</span></div>@endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══ TAB EDUCACIÓN ══ --}}
        <div id="tp-educacion" class="tab-pane fade">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                                    <i class="fa-solid fa-graduation-cap fa-xs"></i>
                                </span>
                                Educación
                            </h6>
                        </div>
                        @if($employee->academic_level)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-school"></i> Nivel académico</span><span class="i-val">{{ $employee->academic_level }}</span></div>
                        @endif
                        @if($employee->degree_title)
                        <div class="i-row"><span class="i-label"><i class="fa-solid fa-scroll"></i> Título</span><span class="i-val">{{ $employee->degree_title }}</span></div>
                        @endif
                        @if(!empty($employee->languages))
                        <div class="i-row">
                            <span class="i-label"><i class="fa-solid fa-language"></i> Idiomas</span>
                            <span class="i-val">
                                @foreach($employee->languages as $lang)
                                <span class="badge me-1" style="background:#eef2ff;color:#4338ca;font-weight:600;font-size:11px;padding:4px 8px;border-radius:6px">{{ $lang }}</span>
                                @endforeach
                            </span>
                        </div>
                        @endif
                        @if(!$employee->academic_level && !$employee->degree_title)
                        <div class="emp-empty"><i class="fa-solid fa-graduation-cap"></i><p>Sin información educativa</p></div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="info-section">
                        <div class="info-sec-header">
                            <h6 class="info-sec-title">
                                <span class="sec-icon" style="background:linear-gradient(135deg,#06b6d4,#0891b2)">
                                    <i class="fa-solid fa-timeline fa-xs"></i>
                                </span>
                                Experiencia Laboral
                            </h6>
                        </div>
                        @forelse($employee->workExperience as $exp)
                        <div class="i-row" style="grid-template-columns:1fr;gap:3px;padding:14px 20px">
                            <div style="font-weight:700;color:#0f2456;font-size:13px">{{ $exp->position }}</div>
                            <div style="font-size:13px;color:#475569;font-weight:500">{{ $exp->company }}</div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px">
                                <i class="fa-regular fa-calendar fa-xs me-1"></i>
                                {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '' }}
                                @if($exp->end_date) — {{ \Carbon\Carbon::parse($exp->end_date)->format('M Y') }} @endif
                                @if(!empty($exp->location)) &nbsp;·&nbsp; <i class="fa-solid fa-location-dot fa-xs"></i> {{ $exp->location }} @endif
                            </div>
                        </div>
                        @empty
                        <div class="emp-empty"><i class="fa-solid fa-timeline"></i><p>Sin experiencia laboral registrada</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ TAB FAMILIA ══ --}}
        <div id="tp-familia" class="tab-pane fade">
            <div class="info-section">
                <div class="info-sec-header">
                    <h6 class="info-sec-title">
                        <span class="sec-icon" style="background:linear-gradient(135deg,#ec4899,#db2777)">
                            <i class="fa-solid fa-people-group fa-xs"></i>
                        </span>
                        Información Familiar
                    </h6>
                    <a href="javascript:void(0)"
                        data-url="{{ route('family-information.create', ['user' => $user->id]) }}"
                        data-ajax-modal="true" data-title="Agregar Familiar" data-size="lg"
                        class="btn btn-sm btn-outline-primary" style="font-size:12px;border-radius:7px;font-weight:600">
                        <i class="fa-solid fa-plus me-1"></i> Agregar familiar
                    </a>
                </div>
                @php $familiaList = $user->family ?? collect(); @endphp
                @if($familiaList->count() > 0)
                <table class="fam-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Parentesco</th>
                            <th>Fecha nac.</th>
                            <th>Teléfono</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($familiaList as $member)
                        <tr>
                            <td><strong style="color:#0f2456">{{ $member->name }}</strong></td>
                            <td><span class="badge" style="background:#f0f4ff;color:#3b5bdb;font-weight:600;font-size:11px;padding:4px 9px;border-radius:6px">{{ $member->relationship }}</span></td>
                            <td style="color:#64748b">{{ $member->dob ? format_date(\Carbon\Carbon::parse($member->dob)) : '—' }}</td>
                            <td style="color:#64748b">{{ $member->phone ?? '—' }}</td>
                            <td style="text-align:right">
                                <x-table-action>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        data-url="{{ route('family-information.edit', $member->id) }}"
                                        data-ajax-modal="true" data-title="Editar Familiar" data-size="lg">
                                        <i class="fa-solid fa-pencil m-r-5"></i> Editar
                                    </a>
                                    <a class="dropdown-item deleteBtn"
                                        data-route="{{ route('family-information.destroy', $member->id) }}"
                                        data-question="¿Eliminar este familiar?" href="javascript:void(0)">
                                        <i class="fa-regular fa-trash-can m-r-5"></i> Eliminar
                                    </a>
                                </x-table-action>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="emp-empty"><i class="fa-solid fa-people-group"></i><p>Sin familiares registrados</p></div>
                @endif
            </div>
        </div>

        {{-- ══ TAB SALARIO ══ --}}
        @superadmin
        <div id="tp-salario" class="tab-pane fade">
            <div class="info-section">
                <div class="info-sec-header">
                    <h6 class="info-sec-title">
                        <span class="sec-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
                            <i class="fa-solid fa-money-bill fa-xs"></i>
                        </span>
                        Información Salarial
                    </h6>
                    <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}"
                        class="btn btn-sm btn-outline-primary" style="font-size:12px;border-radius:7px;font-weight:600">
                        <i class="fa-solid fa-pencil me-1"></i> Editar
                    </a>
                </div>
                @if($employee->salaryDetails)
                <div class="sal-grid">
                    <div class="sal-cell">
                        <div class="sc-label">Tipo de salario</div>
                        <div class="sc-val">{{ $basisLabels[$employee->salaryDetails->basis?->value ?? ''] ?? '—' }}</div>
                    </div>
                    <div class="sal-cell">
                        <div class="sc-label">Sueldo ordinario</div>
                        <div class="sc-val money">{{ number_format($employee->salaryDetails->base_salary ?? 0, 2) }}</div>
                    </div>
                    <div class="sal-cell">
                        <div class="sc-label">Método de pago</div>
                        <div class="sc-val" style="font-size:15px">{{ $pmLabels[$employee->salaryDetails->payment_method?->value ?? ''] ?? '—' }}</div>
                    </div>
                    <div class="sal-cell">
                        <div class="sc-label">Bonif. Decreto 37-2001</div>
                        <div class="sc-val money">{{ number_format($employee->salaryDetails->bonificacion_decreto ?? 0, 2) }}</div>
                    </div>
                    <div class="sal-cell">
                        <div class="sc-label">Bonificación variable</div>
                        <div class="sc-val money">{{ number_format($employee->salaryDetails->variable_bonus ?? 0, 2) }}</div>
                    </div>
                    <div class="sal-cell">
                        <div class="sc-label">Bonif. c/ prestaciones</div>
                        <div class="sc-val" style="font-size:15px">
                            @php $bsp = $employee->salaryDetails->bonus_subject_to_benefits; @endphp
                            @if($bsp === null || $bsp == 2) No aplica
                            @elseif($bsp == 1) <span class="badge bg-success">Sí</span>
                            @else <span class="badge bg-secondary">No</span>
                            @endif
                        </div>
                    </div>
                    <div class="sal-cell" style="grid-column:span 3">
                        <div class="sc-label">Categoría de premios</div>
                        <div class="sc-val" style="font-size:15px">{{ $employee->salaryDetails->award_category ?? '—' }}</div>
                    </div>
                </div>
                @else
                <div class="emp-empty">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <p>Sin información salarial — <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}">completar expediente</a></p>
                </div>
                @endif
            </div>
        </div>
        @endsuperadmin

    </div>
</div>
@endsection