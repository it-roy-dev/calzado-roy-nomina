@extends('layouts.app')

@push('page-styles')
<style>
    :root {
        --primary: #1e3a5f;
        --primary-light: #2d6a9f;
        --primary-ultra-light: #e8f0fe;
        --accent: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --surface-3: #f1f5f9;
        --border: #e2e8f0;
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
        --shadow-lg: 0 10px 30px rgba(0,0,0,0.1), 0 4px 8px rgba(0,0,0,0.05);
        --radius: 12px;
        --radius-sm: 8px;
    }

    /* ── Layout ── */
    .exp-wrapper { background: var(--surface-2); min-height: 100vh; }

    /* ── Header ── */
    .exp-hero {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #3b82f6 100%);
        border-radius: var(--radius);
        padding: 28px 32px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .exp-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .exp-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 30%;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .exp-hero-avatar {
        width: 68px; height: 68px;
        border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.4);
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .exp-hero h4 { color: #fff; margin: 0; font-weight: 700; font-size: 20px; letter-spacing: -0.3px; }
    .exp-hero .sub { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 4px; }
    .exp-status-badge {
        font-size: 12px; font-weight: 700;
        padding: 6px 16px; border-radius: 20px;
        letter-spacing: 0.5px; text-transform: uppercase;
    }

    /* ── Nav lateral ── */
    .exp-nav-wrapper {
        position: sticky;
        top: 20px;
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .exp-nav-header {
        padding: 16px 20px;
        background: var(--surface-3);
        border-bottom: 1px solid var(--border);
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .exp-nav-list { padding: 8px; }
    .exp-nav-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        margin-bottom: 2px;
    }
    .exp-nav-item:hover { background: var(--primary-ultra-light); color: var(--primary); text-decoration: none; }
    .exp-nav-item.active { background: var(--primary); color: #fff; }
    .exp-nav-item .nav-icon {
        width: 28px; height: 28px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        background: rgba(255,255,255,0.15);
        flex-shrink: 0;
    }
    .exp-nav-item:not(.active) .nav-icon { background: var(--surface-3); }
    .exp-nav-divider { height: 1px; background: var(--border); margin: 8px 0; }
    .exp-nav-actions { padding: 12px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }

    /* ── Secciones ── */
    .exp-section {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin-bottom: 20px;
        overflow: hidden;
        scroll-margin-top: 20px;
    }
    .exp-section-header {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .exp-section-icon {
        width: 36px; height: 36px;
        border-radius: 9px;
        background: var(--primary-ultra-light);
        color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .exp-section-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .exp-section-subtitle { font-size: 12px; color: var(--text-muted); margin: 0; }
    .exp-section-body { padding: 24px; }

    /* ── Form elements ── */
    .exp-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.3px;
    }
    .exp-label .required { color: var(--danger); }
    .exp-label .hint { font-weight: 400; color: var(--text-muted); font-size: 11px; }

    .exp-input, .exp-select {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 13px;
        color: var(--text-primary);
        background: var(--surface);
        transition: all 0.15s;
        outline: none;
        -webkit-appearance: none;
    }
    .exp-input:focus, .exp-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }
    .exp-input[readonly] {
        background: var(--surface-3);
        color: var(--text-secondary);
        cursor: default;
    }
    .exp-input::placeholder { color: var(--text-muted); }
    .exp-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; cursor: pointer; }

    .exp-input-group { display: flex; }
    .exp-input-prefix {
        padding: 9px 12px;
        background: var(--surface-3);
        border: 1.5px solid var(--border);
        border-right: none;
        border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
    }
    .exp-input-group .exp-input { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }

    /* ── Repeater rows ── */
    .repeater-card {
        background: var(--surface-2);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 16px;
        margin-bottom: 12px;
        position: relative;
    }
    .repeater-card-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    /* ── Checkbox ── */
    .exp-check { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .exp-check input[type="checkbox"] {
        width: 16px; height: 16px;
        border-radius: 4px;
        border: 1.5px solid var(--border);
        cursor: pointer;
        accent-color: var(--primary);
    }
    .exp-check-label { font-size: 13px; color: var(--text-secondary); }

    /* ── Horario admin ── */
    .horario-card {
        background: #fffbeb;
        border: 2px solid #fcd34d;
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .horario-card-header {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #fde68a;
    }
    .horario-card-icon {
        width: 32px; height: 32px;
        background: #fde68a;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #92400e;
        font-size: 13px;
    }
    .horario-table { width: 100%; border-collapse: collapse; }
    .horario-table th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #fef3c7;
        border-bottom: 1px solid #fde68a;
        text-align: left;
    }
    .horario-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #fde68a;
    }
    .horario-table tr:last-child td { border-bottom: none; }
    .horario-dia { font-size: 13px; font-weight: 600; color: #92400e; }
    .horario-time {
        padding: 7px 10px;
        border: 1.5px solid #fcd34d;
        border-radius: 6px;
        font-size: 13px;
        background: #fff;
        outline: none;
        width: 100%;
    }
    .horario-time:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }

    /* ── Buttons ── */
    .btn-exp-primary {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .btn-exp-primary:hover { background: var(--primary-light); transform: translateY(-1px); box-shadow: var(--shadow-md); }
    .btn-exp-secondary {
        background: transparent;
        color: var(--text-secondary);
        border: 1.5px solid var(--border);
        padding: 9px 20px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        text-decoration: none;
    }
    .btn-exp-secondary:hover { border-color: var(--primary); color: var(--primary); text-decoration: none; }
    .btn-exp-warning {
        background: #f59e0b;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .btn-exp-warning:hover { background: #d97706; transform: translateY(-1px); }
    .btn-exp-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 6px;
    }
    .btn-exp-add {
        background: var(--primary-ultra-light);
        color: var(--primary);
        border: 1.5px dashed var(--accent);
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-exp-add:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
    .btn-exp-danger {
        background: #fee2e2;
        color: var(--danger);
        border: none;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-exp-danger:hover { background: var(--danger); color: #fff; }

    /* ── Empty states ── */
    .empty-state {
        text-align: center;
        padding: 24px;
        color: var(--text-muted);
        font-size: 13px;
    }
    .empty-state i { font-size: 24px; margin-bottom: 8px; display: block; }

    /* ── Grid ── */
    .exp-grid { display: grid; gap: 16px; }
    .exp-grid-2 { grid-template-columns: 1fr 1fr; }
    .exp-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .exp-grid-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }
    @media(max-width: 768px) {
        .exp-grid-2, .exp-grid-3, .exp-grid-4 { grid-template-columns: 1fr; }
    }
    .exp-col-span-2 { grid-column: span 2; }

    /* ── Misc ── */
    .exp-divider { height: 1px; background: var(--border); margin: 20px 0; }
    .exp-tag {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: var(--primary-ultra-light);
        color: var(--primary);
    }
</style>
@endpush

@section('page-content')
<div class="content container-fluid exp-wrapper">

    <x-breadcrumb>
        <x-slot name="title">Expediente</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.list') }}">Empleados</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.show', \Crypt::encrypt($user->id)) }}">{{ $user->fullname }}</a></li>
            <li class="breadcrumb-item active">Expediente</li>
        </ul>
    </x-breadcrumb>

    {{-- Hero --}}
    <div class="exp-hero">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ !empty($user->avatar) ? asset('storage/users/'.$user->avatar) : asset('images/user.jpg') }}"
                class="exp-hero-avatar" alt="{{ $user->fullname }}">
            <div style="flex:1">
                <h4>{{ $user->fullname }}</h4>
                <div class="sub">
                    @if($detail->emp_code)
                        <span style="background:rgba(255,255,255,0.15);padding:2px 8px;border-radius:4px;font-weight:600;margin-right:6px">{{ $detail->emp_code }}</span>
                    @endif
                    {{ $detail->designation->name ?? 'Sin puesto' }}
                    @if($detail->store) — {{ $detail->store->name }}
                    @elseif($detail->department) — {{ $detail->department->name }}
                    @endif
                </div>
            </div>
            @php
                $status = $detail->status ?? 'PENDIENTE';
                $statusMap = [
                    'PENDIENTE'   => ['label' => 'Pendiente',   'bg' => 'rgba(245,158,11,0.2)',  'color' => '#fbbf24'],
                    'COMPLETO'    => ['label' => 'Completo',    'bg' => 'rgba(16,185,129,0.2)',  'color' => '#34d399'],
                    'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'bg' => 'rgba(239,68,68,0.2)',   'color' => '#f87171'],
                    'INACTIVO'    => ['label' => 'Inactivo',    'bg' => 'rgba(148,163,184,0.2)', 'color' => '#94a3b8'],
                ];
                $s = $statusMap[$status] ?? $statusMap['PENDIENTE'];
            @endphp
            <span class="exp-status-badge" style="background:{{ $s['bg'] }};color:{{ $s['color'] }}">
                {{ $s['label'] }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        {{-- Navegación lateral --}}
        <div class="col-md-3">
            <div class="exp-nav-wrapper">
                <div class="exp-nav-header">Secciones</div>
                <div class="exp-nav-list" id="exp-nav">
                    <a class="exp-nav-item active" href="#sec-personal">
                        <span class="nav-icon"><i class="fa-solid fa-user fa-xs"></i></span>
                        Información Personal
                    </a>
                    <a class="exp-nav-item" href="#sec-documentos">
                        <span class="nav-icon"><i class="fa-solid fa-id-card fa-xs"></i></span>
                        Documentos
                    </a>
                    <a class="exp-nav-item" href="#sec-laboral">
                        <span class="nav-icon"><i class="fa-solid fa-briefcase fa-xs"></i></span>
                        Datos Laborales
                    </a>
                    @if(isset($tieneHorarioAdmin) && !$tieneHorarioAdmin && $detail->department_id)
                    <a class="exp-nav-item" href="#sec-horario-admin" style="color:#92400e">
                        <span class="nav-icon" style="background:#fde68a;color:#92400e"><i class="fa-solid fa-clock fa-xs"></i></span>
                        Asignar Horario
                    </a>
                    @endif
                    <a class="exp-nav-item" href="#sec-emergencia">
                        <span class="nav-icon"><i class="fa-solid fa-phone fa-xs"></i></span>
                        Contacto Emergencia
                    </a>
                    <a class="exp-nav-item" href="#sec-familia">
                        <span class="nav-icon"><i class="fa-solid fa-people-group fa-xs"></i></span>
                        Familiares
                    </a>
                    <a class="exp-nav-item" href="#sec-educacion">
                        <span class="nav-icon"><i class="fa-solid fa-graduation-cap fa-xs"></i></span>
                        Educación
                    </a>
                    <a class="exp-nav-item" href="#sec-experiencia">
                        <span class="nav-icon"><i class="fa-solid fa-timeline fa-xs"></i></span>
                        Experiencia Laboral
                    </a>
                    <a class="exp-nav-item" href="#sec-extranjero">
                        <span class="nav-icon"><i class="fa-solid fa-plane fa-xs"></i></span>
                        Trabajo Extranjero
                    </a>
                    <a class="exp-nav-item" href="#sec-bancario">
                        <span class="nav-icon"><i class="fa-solid fa-building-columns fa-xs"></i></span>
                        Datos Bancarios
                    </a>
                    <a class="exp-nav-item" href="#sec-salario">
                        <span class="nav-icon"><i class="fa-solid fa-money-bill fa-xs"></i></span>
                        Salario
                    </a>
                </div>
                <div class="exp-nav-actions">
                    <button type="button" class="btn-exp-primary" onclick="validarYGuardar()">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Expediente
                    </button>
                    <a href="{{ route('employees.show', \Crypt::encrypt($user->id)) }}" class="btn-exp-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Volver al Perfil
                    </a>
                </div>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="col-md-9">

            {{-- FORM PRINCIPAL --}}
            <form id="form-expediente" action="{{ route('employees.expediente.save', \Crypt::encrypt($user->id)) }}" method="POST">
                @csrf

                {{-- Información Personal --}}
                <div class="exp-section" id="sec-personal">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <div class="exp-section-title">Información Personal</div>
                            <div class="exp-section-subtitle">Datos personales y de contacto</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div class="exp-grid exp-grid-3">
                            <div>
                                <label class="exp-label">Fecha de Nacimiento</label>
                                <input type="date" name="dob" class="exp-input"
                                    value="{{ $detail->dob ? $detail->dob->format('Y-m-d') : '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Lugar de Nacimiento</label>
                                <input type="text" name="birth_place" class="exp-input"
                                    value="{{ $detail->birth_place ?? '' }}" placeholder="Ciudad, País">
                            </div>
                            <div>
                                <label class="exp-label">Nacionalidad</label>
                                <input type="text" name="nationality" class="exp-input"
                                    value="{{ $detail->nationality ?? '' }}" placeholder="Guatemalteca">
                            </div>
                            <div>
                                <label class="exp-label">Género</label>
                                <select name="gender" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Masculino" {{ ($detail->gender ?? '') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ ($detail->gender ?? '') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ ($detail->gender ?? '') === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Estado Civil</label>
                                <select name="marital_status" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Soltero/a','Casado/a','Divorciado/a','Viudo/a','Unión libre'] as $ms)
                                        <option value="{{ $ms }}" {{ ($detail->marital_status ?? '') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Religión</label>
                                <input type="text" name="religion" class="exp-input" value="{{ $detail->religion ?? '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Etnia</label>
                                <input type="text" name="ethnicity" class="exp-input" value="{{ $detail->ethnicity ?? '' }}">
                            </div>
                            <div>
                                <label class="exp-label">No. de Hijos</label>
                                <input type="number" name="no_of_children" class="exp-input" min="0"
                                    value="{{ $detail->no_of_children ?? '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Teléfono Adicional</label>
                                <input type="text" name="phone_secondary" class="exp-input"
                                    value="{{ $detail->phone_secondary ?? '' }}" placeholder="+502 0000-0000">
                            </div>
                            <div class="exp-col-span-2">
                                <label class="exp-label">Correo Personal</label>
                                <input type="email" name="personal_email" class="exp-input"
                                    value="{{ $detail->personal_email ?? '' }}" placeholder="correo@personal.com">
                            </div>
                            <div>
                                <label class="exp-label">Discapacidad</label>
                                <label class="exp-check mt-1">
                                    <input type="checkbox" name="disability" id="disability"
                                        {{ $detail->disability ? 'checked' : '' }}
                                        onchange="document.getElementById('disability-desc').style.display=this.checked?'block':'none'">
                                    <span class="exp-check-label">Tiene discapacidad</span>
                                </label>
                                <div id="disability-desc" style="display:{{ $detail->disability ? 'block' : 'none' }};margin-top:8px">
                                    <input type="text" name="disability_description" class="exp-input"
                                        value="{{ $detail->disability_description ?? '' }}"
                                        placeholder="Describa el tipo de discapacidad">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documentos --}}
                <div class="exp-section" id="sec-documentos">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-id-card"></i></div>
                        <div>
                            <div class="exp-section-title">Documentos Guatemala</div>
                            <div class="exp-section-subtitle">DPI, NIT, IGSS e IRTRA</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div class="exp-grid exp-grid-2">
                            <div>
                                <label class="exp-label">DPI <span class="required">*</span></label>
                                <input type="text" name="dpi_number" class="exp-input"
                                    value="{{ $detail->dpi_number ?? '' }}" placeholder="0000 00000 0000">
                            </div>
                            <div>
                                <label class="exp-label">Lugar de Emisión DPI</label>
                                <input type="text" name="dpi_issued_place" class="exp-input"
                                    value="{{ $detail->dpi_issued_place ?? '' }}" placeholder="Guatemala, Mixco">
                            </div>
                            <div>
                                <label class="exp-label">NIT <span class="required">*</span></label>
                                <input type="text" name="nit_number" class="exp-input"
                                    value="{{ $detail->nit_number ?? '' }}" placeholder="0000000-0">
                            </div>
                            <div>
                                <label class="exp-label">No. IGSS <span class="required">*</span></label>
                                <input type="text" name="igss_number" class="exp-input"
                                    value="{{ $detail->igss_number ?? '' }}" placeholder="0000000000000">
                            </div>
                            <div>
                                <label class="exp-label">No. IRTRA</label>
                                <input type="text" name="irtra_number" class="exp-input"
                                    value="{{ $detail->irtra_number ?? '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Licencia de Conducir</label>
                                <input type="text" name="driver_license" class="exp-input"
                                    value="{{ $detail->driver_license ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos Laborales --}}
                <div class="exp-section" id="sec-laboral">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-briefcase"></i></div>
                        <div>
                            <div class="exp-section-title">Datos Laborales</div>
                            <div class="exp-section-subtitle">Ubicación, cargo, horario y contrato</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div class="exp-grid exp-grid-2">
                            <div>
                                <label class="exp-label">Tienda <span class="hint">(si es empleado de tienda)</span></label>
                                <select name="store_id" class="exp-select">
                                    <option value="">Sin tienda</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ ($detail->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Departamento <span class="hint">(si es admin)</span></label>
                                <select name="department_id" class="exp-select">
                                    <option value="">Sin departamento</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ ($detail->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Puesto / Designación</label>
                                <select name="designation_id" class="exp-select">
                                    <option value="">Seleccionar puesto</option>
                                    @foreach($designations as $desig)
                                        <option value="{{ $desig->id }}" {{ ($detail->designation_id ?? '') == $desig->id ? 'selected' : '' }}>
                                            {{ $desig->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Nombre de Jefe Inmediato</label>
                                <input type="text" name="immediate_supervisor_name" class="exp-input"
                                    value="{{ $detail->immediate_supervisor_name ?? '' }}" placeholder="Nombre completo del jefe">
                            </div>
                            <div>
                                <label class="exp-label">Fecha de Ingreso</label>
                                <input type="date" name="date_joined" class="exp-input"
                                    value="{{ $detail->date_joined ? $detail->date_joined->format('Y-m-d') : '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Tipo de Contrato</label>
                                <select name="contract_type" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Posición fija','Temporal','Eventual','Vacacionista'] as $ct)
                                        <option value="{{ $ct }}" {{ ($detail->contract_type ?? '') === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Horario</label>
                                <input type="text" name="work_schedule" class="exp-input"
                                    value="{{ $detail->work_schedule ?? '' }}" placeholder="Ej: 07:00 - 16:30"
                                    {{ isset($tieneHorarioAdmin) && $tieneHorarioAdmin ? 'readonly' : '' }}>
                            </div>
                            <div>
                                <label class="exp-label">Horas por Semana</label>
                                <input type="number" name="work_hours_per_week" class="exp-input" min="0" max="80"
                                    value="{{ $detail->work_hours_per_week ?? '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Fecha de Baja</label>
                                <input type="date" name="termination_date" class="exp-input"
                                    value="{{ $detail->termination_date ? $detail->termination_date->format('Y-m-d') : '' }}">
                            </div>
                            <div>
                                <label class="exp-label">Motivo de Retiro</label>
                                <input type="text" name="termination_reason" class="exp-input"
                                    value="{{ $detail->termination_reason ?? '' }}" placeholder="Ej: Renuncia voluntaria">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contacto de Emergencia --}}
                <div class="exp-section" id="sec-emergencia">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <div class="exp-section-title">Contacto de Emergencia</div>
                            <div class="exp-section-subtitle">Al menos un contacto requerido</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        @php
                            $primary   = $detail->emergency_contacts['primary'] ?? [];
                            $secondary = $detail->emergency_contacts['secondary'] ?? [];
                        @endphp
                        <div class="repeater-card">
                            <div class="repeater-card-label">Contacto Primario</div>
                            <div class="exp-grid exp-grid-3">
                                <div>
                                    <label class="exp-label">Nombre</label>
                                    <input type="text" name="primary[name]" class="exp-input" value="{{ $primary['name'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="exp-label">Parentesco</label>
                                    <input type="text" name="primary[relationship]" class="exp-input" value="{{ $primary['relationship'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="exp-label">Teléfono</label>
                                    <input type="text" name="primary[phone]" class="exp-input" value="{{ $primary['phone'] ?? '' }}">
                                </div>
                                <div class="exp-col-span-2">
                                    <label class="exp-label">Dirección</label>
                                    <input type="text" name="primary[address]" class="exp-input" value="{{ $primary['address'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="repeater-card">
                            <div class="repeater-card-label">Contacto Secundario</div>
                            <div class="exp-grid exp-grid-3">
                                <div>
                                    <label class="exp-label">Nombre</label>
                                    <input type="text" name="secondary[name]" class="exp-input" value="{{ $secondary['name'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="exp-label">Parentesco</label>
                                    <input type="text" name="secondary[relationship]" class="exp-input" value="{{ $secondary['relationship'] ?? '' }}">
                                </div>
                                <div>
                                    <label class="exp-label">Teléfono</label>
                                    <input type="text" name="secondary[phone]" class="exp-input" value="{{ $secondary['phone'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Familiares --}}
                <div class="exp-section" id="sec-familia">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-people-group"></i></div>
                        <div style="flex:1">
                            <div class="exp-section-title">Datos Familiares</div>
                            <div class="exp-section-subtitle">Padres, hermanos, cónyuge e hijos</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px">
                            <label class="exp-check">
                                <input type="checkbox" name="no_aplica_familia" id="no_aplica_familia"
                                    {{ ($detail->no_aplica_familia ?? false) ? 'checked' : '' }}
                                    onchange="toggleSection('familiares-container','btn-agregar-familiar',this.checked)">
                                <span class="exp-check-label" style="font-size:12px">No aplica</span>
                            </label>
                            <button type="button" id="btn-agregar-familiar" class="btn-exp-add btn-exp-sm" onclick="addFamiliar()"
                                {{ ($detail->no_aplica_familia ?? false) ? 'disabled' : '' }}>
                                <i class="fa-solid fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div id="familiares-container">
                            @php $familiares = $user->family ?? collect(); @endphp
                            @if($familiares->count() > 0)
                                @foreach($familiares as $i => $familiar)
                                <div class="repeater-card familiar-row">
                                    <input type="hidden" name="familiares[{{ $i }}][id]" value="{{ $familiar->id }}">
                                    <div class="exp-grid exp-grid-3">
                                        <div>
                                            <label class="exp-label">Nombre completo</label>
                                            <input type="text" name="familiares[{{ $i }}][name]" class="exp-input" value="{{ $familiar->name }}">
                                        </div>
                                        <div>
                                            <label class="exp-label">Parentesco</label>
                                            <select name="familiares[{{ $i }}][relationship]" class="exp-select">
                                                <option value="">Seleccionar</option>
                                                @foreach(['Padre','Madre','Hermano/a','Cónyuge','Hijo/a','Otro'] as $rel)
                                                    <option value="{{ $rel }}" {{ $familiar->relationship === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="exp-label">Fecha de Nacimiento</label>
                                            <input type="date" name="familiares[{{ $i }}][dob]" class="exp-input"
                                                value="{{ $familiar->dob ? \Carbon\Carbon::parse($familiar->dob)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div>
                                            <label class="exp-label">Teléfono</label>
                                            <input type="text" name="familiares[{{ $i }}][phone]" class="exp-input" value="{{ $familiar->phone }}">
                                        </div>
                                        <div style="display:flex;align-items:flex-end">
                                            <button type="button" class="btn-exp-danger" onclick="removeFamiliar(this)">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="empty-state" id="no-familiares">
                                    <i class="fa-solid fa-people-group"></i>
                                    Sin familiares registrados. Haz clic en Agregar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Educación --}}
                <div class="exp-section" id="sec-educacion">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div>
                            <div class="exp-section-title">Educación</div>
                            <div class="exp-section-subtitle">Nivel académico e idiomas</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div class="exp-grid exp-grid-2">
                            <div>
                                <label class="exp-label">Nivel Académico</label>
                                <select name="academic_level" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Primaria','Básico','Diversificado','Técnico','Universidad','Postgrado','Maestría','Doctorado'] as $al)
                                        <option value="{{ $al }}" {{ ($detail->academic_level ?? '') === $al ? 'selected' : '' }}>{{ $al }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Título / Carrera</label>
                                <input type="text" name="degree_title" class="exp-input"
                                    value="{{ $detail->degree_title ?? '' }}" placeholder="Ej: Bachiller en Ciencias">
                            </div>
                            <div class="exp-col-span-2">
                                <label class="exp-label">Idiomas <span class="hint">(separados por coma)</span></label>
                                <input type="text" name="languages" class="exp-input"
                                    value="{{ !empty($detail->languages) ? implode(', ', $detail->languages) : '' }}"
                                    placeholder="Español, Inglés, Maya">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Experiencia Laboral --}}
                <div class="exp-section" id="sec-experiencia">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-timeline"></i></div>
                        <div style="flex:1">
                            <div class="exp-section-title">Experiencia Laboral Anterior</div>
                            <div class="exp-section-subtitle">Empleos previos</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px">
                            <label class="exp-check">
                                <input type="checkbox" name="no_aplica_experiencia" id="no_aplica_experiencia"
                                    {{ ($detail->no_aplica_experiencia ?? false) ? 'checked' : '' }}
                                    onchange="toggleSection('experiencia-container','btn-agregar-exp',this.checked)">
                                <span class="exp-check-label" style="font-size:12px">No aplica</span>
                            </label>
                            <button type="button" id="btn-agregar-exp" class="btn-exp-add btn-exp-sm" onclick="addExperiencia()"
                                {{ ($detail->no_aplica_experiencia ?? false) ? 'disabled' : '' }}>
                                <i class="fa-solid fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div id="experiencia-container">
                            @php $experiencias = $detail->workExperience ?? collect(); @endphp
                            @if($experiencias->count() > 0)
                                @foreach($experiencias as $i => $exp)
                                <div class="repeater-card exp-row">
                                    <input type="hidden" name="experiencias[{{ $i }}][id]" value="{{ $exp->id }}">
                                    <div class="exp-grid exp-grid-3">
                                        <div>
                                            <label class="exp-label">Empresa</label>
                                            <input type="text" name="experiencias[{{ $i }}][company]" class="exp-input" value="{{ $exp->company }}">
                                        </div>
                                        <div>
                                            <label class="exp-label">Puesto</label>
                                            <input type="text" name="experiencias[{{ $i }}][position]" class="exp-input" value="{{ $exp->position }}">
                                        </div>
                                        <div>
                                            <label class="exp-label">País/Ciudad</label>
                                            <input type="text" name="experiencias[{{ $i }}][location]" class="exp-input" value="{{ $exp->location }}">
                                        </div>
                                        <div>
                                            <label class="exp-label">Fecha inicio</label>
                                            <input type="date" name="experiencias[{{ $i }}][start_date]" class="exp-input"
                                                value="{{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div>
                                            <label class="exp-label">Fecha fin</label>
                                            <input type="date" name="experiencias[{{ $i }}][end_date]" class="exp-input"
                                                value="{{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div style="display:flex;align-items:flex-end">
                                            <button type="button" class="btn-exp-danger" onclick="removeExperiencia(this)">
                                                <i class="fa-solid fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="empty-state" id="no-experiencia">
                                    <i class="fa-solid fa-timeline"></i>
                                    Sin experiencia registrada. Haz clic en Agregar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Trabajo en el Extranjero --}}
                <div class="exp-section" id="sec-extranjero">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-plane"></i></div>
                        <div>
                            <div class="exp-section-title">Trabajo en el Extranjero</div>
                            <div class="exp-section-subtitle">Experiencia internacional</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <label class="exp-check" style="margin-bottom:16px">
                            <input type="checkbox" name="worked_abroad" id="worked_abroad"
                                {{ $detail->worked_abroad ? 'checked' : '' }}
                                onchange="document.getElementById('sec-extranjero-fields').style.display=this.checked?'block':'none'">
                            <span class="exp-check-label">Ha trabajado en el extranjero</span>
                        </label>
                        <div id="sec-extranjero-fields" style="display:{{ $detail->worked_abroad ? 'block' : 'none' }}">
                            <div class="exp-grid exp-grid-3">
                                <div>
                                    <label class="exp-label">País</label>
                                    <input type="text" name="foreign_country" class="exp-input" value="{{ $detail->foreign_country ?? '' }}">
                                </div>
                                <div>
                                    <label class="exp-label">Empresa</label>
                                    <input type="text" name="foreign_company" class="exp-input" value="{{ $detail->foreign_company ?? '' }}">
                                </div>
                                <div>
                                    <label class="exp-label">Puesto</label>
                                    <input type="text" name="foreign_job_title" class="exp-input" value="{{ $detail->foreign_job_title ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos Bancarios --}}
                <div class="exp-section" id="sec-bancario">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-building-columns"></i></div>
                        <div>
                            <div class="exp-section-title">Datos Bancarios</div>
                            <div class="exp-section-subtitle">Cuenta para acreditación de salario</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div class="exp-grid exp-grid-2">
                            <div>
                                <label class="exp-label">Forma de Pago</label>
                                <select name="payment_method" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Cuenta monetaria" {{ ($detail->payment_method ?? '') === 'Cuenta monetaria' ? 'selected' : '' }}>Cuenta monetaria</option>
                                    <option value="Cheque" {{ ($detail->payment_method ?? '') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Banco</label>
                                <select name="bank_name" class="exp-select">
                                    <option value="">Seleccionar banco</option>
                                    @foreach(['Banrural','Banco Industrial','BAC Credomatic','Banco Agromercantil','G&T Continental','Banco Promerica','Banco Azteca','Bantrab','Banpaís','Otro'] as $banco)
                                        <option value="{{ $banco }}" {{ ($detail->bank_name ?? '') === $banco ? 'selected' : '' }}>{{ $banco }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Número de Cuenta</label>
                                <input type="text" name="bank_account_number" class="exp-input"
                                    value="{{ $detail->bank_account_number ?? '' }}" placeholder="000-000000-0">
                            </div>
                            <div>
                                <label class="exp-label">Tipo de Cuenta</label>
                                <select name="bank_account_type" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Monetaria" {{ ($detail->bank_account_type ?? '') === 'Monetaria' ? 'selected' : '' }}>Monetaria</option>
                                    <option value="Ahorro" {{ ($detail->bank_account_type ?? '') === 'Ahorro' ? 'selected' : '' }}>Ahorro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Salario --}}
                <div class="exp-section" id="sec-salario">
                    <div class="exp-section-header">
                        <div class="exp-section-icon"><i class="fa-solid fa-money-bill"></i></div>
                        <div>
                            <div class="exp-section-title">Información Salarial</div>
                            <div class="exp-section-subtitle">Salario, bonificaciones y método de pago</div>
                        </div>
                    </div>
                    <div class="exp-section-body">
                        <div class="exp-grid exp-grid-2">
                            <div>
                                <label class="exp-label">Sueldo Ordinario</label>
                                <div class="exp-input-group">
                                    <span class="exp-input-prefix">GTQ</span>
                                    <input type="number" step="0.01" name="base_salary" class="exp-input"
                                        value="{{ $detail->salaryDetails->base_salary ?? '' }}" placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label class="exp-label">Bonificación Decreto 37-2001</label>
                                <div class="exp-input-group">
                                    <span class="exp-input-prefix">GTQ</span>
                                    <input type="number" step="0.01" name="bonificacion_decreto" class="exp-input"
                                        value="{{ $detail->salaryDetails->bonificacion_decreto ?? '250.00' }}" placeholder="250.00">
                                </div>
                            </div>
                            <div>
                                <label class="exp-label">Bonificación Variable</label>
                                <div class="exp-input-group">
                                    <span class="exp-input-prefix">GTQ</span>
                                    <input type="number" step="0.01" name="bonificacion_variable" class="exp-input"
                                        value="{{ $detail->salaryDetails->variable_bonus ?? '' }}" placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label class="exp-label">Bonificación Variable sujeta a prestaciones</label>
                                <select name="bonificacion_variable_prestaciones" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    <option value="1" {{ ($detail->salaryDetails->bonus_subject_to_benefits ?? '') == 1 ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ isset($detail->salaryDetails->bonus_subject_to_benefits) && $detail->salaryDetails->bonus_subject_to_benefits == 0 && $detail->salaryDetails->bonus_subject_to_benefits !== null ? 'selected' : '' }}>No</option>
                                    <option value="2" {{ ($detail->salaryDetails->bonus_subject_to_benefits ?? '') == 2 ? 'selected' : '' }}>No aplica</option>
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Categoría de Premios</label>
                                <select name="categoria_premios" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Supervisor','Jefe de tienda','Ventas','No aplica'] as $cat)
                                        <option value="{{ $cat }}" {{ ($detail->salaryDetails->award_category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="exp-label">Tipo de Salario</label>
                                <select name="salary_basis" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    <option value="monthly" {{ ($detail->salaryDetails->basis->value ?? '') === 'monthly' ? 'selected' : '' }}>Mensual</option>
                                    <option value="weekly" {{ ($detail->salaryDetails->basis->value ?? '') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                                    <option value="hourly" {{ ($detail->salaryDetails->basis->value ?? '') === 'hourly' ? 'selected' : '' }}>Por hora</option>
                                    <option value="contract" {{ ($detail->salaryDetails->basis->value ?? '') === 'contract' ? 'selected' : '' }}>Por contrato</option>
                                </select>
                            </div>
                            <div class="exp-col-span-2">
                                <label class="exp-label">Método de Pago de Nómina</label>
                                <select name="salary_payment_method" class="exp-select">
                                    <option value="">Seleccionar</option>
                                    <option value="bank" {{ ($detail->salaryDetails->payment_method?->value ?? '') === 'bank' ? 'selected' : '' }}>Transferencia bancaria</option>
                                    <option value="cheque" {{ ($detail->salaryDetails->payment_method?->value ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="cash" {{ ($detail->salaryDetails->payment_method?->value ?? '') === 'cash' ? 'selected' : '' }}>Efectivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botón final --}}
                <div style="display:flex;justify-content:flex-end;gap:12px;margin-bottom:24px">
                    <a href="{{ route('employees.show', \Crypt::encrypt($user->id)) }}" class="btn-exp-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="button" class="btn-exp-primary" onclick="validarYGuardar()">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Expediente
                    </button>
                </div>

            </form>
            {{-- FIN FORM PRINCIPAL --}}

            {{-- ASIGNAR HORARIO ADMIN — fuera del form principal --}}
            @if(isset($tieneHorarioAdmin) && !$tieneHorarioAdmin && $detail->department_id)
            <div class="horario-card" id="sec-horario-admin">
                <div class="horario-card-header">
                    <div class="horario-card-icon"><i class="fa-solid fa-clock"></i></div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#92400e">Asignar Horario</div>
                        <div style="font-size:12px;color:#a16207">Este empleado no tiene horario registrado en el sistema</div>
                    </div>
                </div>
                <div style="padding:20px">
                    <form id="form-horario" action="{{ route('employees.horario.admin', \Crypt::encrypt($user->id)) }}" method="POST">
                        @csrf
                        <table class="horario-table">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th>Hora Entrada</th>
                                    <th>Hora Salida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['LUNES','MARTES','MIERCOLES','JUEVES','VIERNES'] as $dia)
                                <tr>
                                    <td><span class="horario-dia">{{ $dia }}</span></td>
                                    <td><input type="time" name="horario[{{ $dia }}][entrada]" class="horario-time" value="07:00"></td>
                                    <td><input type="time" name="horario[{{ $dia }}][salida]" class="horario-time" value="16:30"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="display:flex;justify-content:flex-end;margin-top:16px">
                            <button type="button" class="btn-exp-warning" onclick="guardarHorario()"><i class="fa-solid fa-floppy-disk"></i> Guardar Horario</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('duplicados_error'))
document.addEventListener('DOMContentLoaded', function() {
    const duplicados = @json(session('duplicados_error'));
    const lista = duplicados.map(d => `<li style="text-align:left;padding:4px 0;font-size:13px;color:#1e293b">${d}</li>`).join('');
    Swal.fire({
        icon: 'error',
        title: 'Datos duplicados encontrados',
        html: `<p style="color:#64748b;font-size:13px;margin-bottom:12px">No se puede guardar. Los siguientes datos ya están registrados por otro empleado:</p>
               <ul style="list-style:none;padding:0;margin:0">${lista}</ul>`,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#ef4444',
        width: 520,
    });
});
@endif

let familiarIndex = {{ $user->family ? $user->family->count() : 0 }};
let expIndex = {{ $detail->workExperience ? $detail->workExperience->count() : 0 }};

function addFamiliar() {
    const el = document.getElementById('no-familiares');
    if (el) el.style.display = 'none';
    const html = `
    <div class="repeater-card familiar-row">
        <div class="exp-grid exp-grid-3">
            <div>
                <label class="exp-label">Nombre completo</label>
                <input type="text" name="familiares[${familiarIndex}][name]" class="exp-input">
            </div>
            <div>
                <label class="exp-label">Parentesco</label>
                <select name="familiares[${familiarIndex}][relationship]" class="exp-select">
                    <option value="">Seleccionar</option>
                    <option>Padre</option><option>Madre</option><option>Hermano/a</option>
                    <option>Cónyuge</option><option>Hijo/a</option><option>Otro</option>
                </select>
            </div>
            <div>
                <label class="exp-label">Fecha de Nacimiento</label>
                <input type="date" name="familiares[${familiarIndex}][dob]" class="exp-input">
            </div>
            <div>
                <label class="exp-label">Teléfono</label>
                <input type="text" name="familiares[${familiarIndex}][phone]" class="exp-input">
            </div>
            <div style="display:flex;align-items:flex-end">
                <button type="button" class="btn-exp-danger" onclick="removeFamiliar(this)">
                    <i class="fa-solid fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>`;
    document.getElementById('familiares-container').insertAdjacentHTML('beforeend', html);
    familiarIndex++;
}

function removeFamiliar(btn) { btn.closest('.familiar-row').remove(); }

function addExperiencia() {
    const el = document.getElementById('no-experiencia');
    if (el) el.style.display = 'none';
    const html = `
    <div class="repeater-card exp-row">
        <div class="exp-grid exp-grid-3">
            <div>
                <label class="exp-label">Empresa</label>
                <input type="text" name="experiencias[${expIndex}][company]" class="exp-input">
            </div>
            <div>
                <label class="exp-label">Puesto</label>
                <input type="text" name="experiencias[${expIndex}][position]" class="exp-input">
            </div>
            <div>
                <label class="exp-label">País/Ciudad</label>
                <input type="text" name="experiencias[${expIndex}][location]" class="exp-input">
            </div>
            <div>
                <label class="exp-label">Fecha inicio</label>
                <input type="date" name="experiencias[${expIndex}][start_date]" class="exp-input">
            </div>
            <div>
                <label class="exp-label">Fecha fin</label>
                <input type="date" name="experiencias[${expIndex}][end_date]" class="exp-input">
            </div>
            <div style="display:flex;align-items:flex-end">
                <button type="button" class="btn-exp-danger" onclick="removeExperiencia(this)">
                    <i class="fa-solid fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>`;
    document.getElementById('experiencia-container').insertAdjacentHTML('beforeend', html);
    expIndex++;
}

function removeExperiencia(btn) { btn.closest('.exp-row').remove(); }

// Nav scroll suave
document.querySelectorAll('.exp-nav-item[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.exp-nav-item').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

function toggleSection(containerId, btnId, disabled) {
    const container = document.getElementById(containerId);
    const btn = document.getElementById(btnId);
    container.style.opacity = disabled ? '0.4' : '1';
    container.style.pointerEvents = disabled ? 'none' : 'auto';
    btn.disabled = disabled;
}

function validarYGuardar() {
    const campos = [
        { name: 'dob',                       label: 'Fecha de nacimiento' },
        { name: 'birth_place',               label: 'Lugar de nacimiento' },
        { name: 'nationality',               label: 'Nacionalidad' },
        { name: 'gender',                    label: 'Género' },
        { name: 'marital_status',            label: 'Estado civil' },
        { name: 'dpi_number',                label: 'DPI' },
        { name: 'nit_number',                label: 'NIT' },
        { name: 'igss_number',               label: 'No. IGSS' },
        { name: 'date_joined',               label: 'Fecha de ingreso' },
        { name: 'contract_type',             label: 'Tipo de contrato' },
        { name: 'work_schedule',             label: 'Horario de trabajo' },
        { name: 'work_hours_per_week',       label: 'Horas por semana' },
        { name: 'immediate_supervisor_name', label: 'Jefe inmediato' },
        { name: 'payment_method',            label: 'Forma de pago' },
        { name: 'bank_name',                 label: 'Banco' },
        { name: 'bank_account_number',       label: 'Número de cuenta' },
        { name: 'bank_account_type',         label: 'Tipo de cuenta' },
    ];

    const faltantes = [];
    campos.forEach(c => {
        const el = document.querySelector('[name="' + c.name + '"]');
        if (!el || !el.value.trim()) faltantes.push(c.label);
    });

    const tienda = document.querySelector('[name="store_id"]');
    const depto  = document.querySelector('[name="department_id"]');
    if ((!tienda || !tienda.value) && (!depto || !depto.value)) faltantes.push('Tienda o Departamento');

    const noFam = document.querySelector('[name="no_aplica_familia"]');
    if ((!noFam || !noFam.checked) && document.querySelectorAll('.familiar-row').length === 0)
        faltantes.push('Información familiar (o marcar "No aplica")');

    const noExp = document.querySelector('[name="no_aplica_experiencia"]');
    if ((!noExp || !noExp.checked) && document.querySelectorAll('.exp-row').length === 0)
        faltantes.push('Experiencia laboral (o marcar "No aplica")');

    // Verificar si es admin sin horario
    const horarioField = document.querySelector('[name="work_schedule"]');
    const deptoField = document.querySelector('[name="department_id"]');
    if (deptoField && deptoField.value && horarioField && (!horarioField.value.trim() || horarioField.value.includes('sin horario'))) {
        Swal.fire({
            icon: 'warning',
            title: 'Horario requerido',
            html: '<p style="font-size:13px;color:#64748b">Debes guardar el horario del empleado antes de guardar el expediente.</p>',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#f59e0b',
            width: 420,
        });
        document.getElementById('sec-horario-admin')?.scrollIntoView({ behavior: 'smooth' });
        return;
    }    

    if (faltantes.length > 0) {
        const lista = faltantes.map(f => `<li style="padding:3px 0;font-size:13px;color:#1e293b">${f}</li>`).join('');
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos faltantes',
            html: `<p style="color:#64748b;font-size:13px;margin-bottom:12px">Completa los siguientes campos antes de guardar:</p>
                   <ul style="text-align:left;padding-left:10px;list-style:disc">${lista}</ul>`,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#1e3c72',
            width: 480,
        });
        return;
    }

    document.getElementById('form-expediente').submit();
}

function guardarHorario() {
    const form = document.getElementById('form-horario');
    const data = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: data
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Actualizar el campo horario en el formulario principal
            document.querySelector('[name="work_schedule"]').value = data.horario;
            // Ocultar el bloque de asignar horario
            document.getElementById('sec-horario-admin').style.display = 'none';
            Swal.fire({
                icon: 'success',
                title: 'Horario guardado',
                text: 'Ahora puedes guardar el expediente.',
                confirmButtonColor: '#1e3a5f',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar el horario.' });
    });
}

</script>
@endpush