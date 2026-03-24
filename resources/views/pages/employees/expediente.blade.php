@extends('layouts.app')

@push('page-style')
<style>
    .exp-header { background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%); color: #fff; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; }
    .exp-header h4 { color: #fff; margin: 0; font-weight: 700; }
    .exp-header .sub { color: rgba(255,255,255,0.8); font-size: 13px; }
    .section-card { border: none; border-radius: 10px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); margin-bottom: 20px; }
    .section-card .card-header { background: #f8f9ff; border-radius: 10px 10px 0 0 !important; border-bottom: 2px solid #e8f0fe; padding: 14px 20px; }
    .section-card .card-header h6 { margin: 0; font-weight: 700; color: #1e3a5f; font-size: 14px; }
    .section-card .card-body { padding: 20px; }
    .form-label { font-size: 13px; font-weight: 600; color: #555; }
    .form-control, .form-select { font-size: 13px; }
    .nav-pills .nav-link { font-size: 13px; font-weight: 600; color: #555; border-radius: 8px; padding: 8px 16px; margin-bottom: 4px; }
    .nav-pills .nav-link.active { background-color: #1e3a5f; color: #fff; }
    .nav-pills .nav-link:hover { background-color: #e8f0fe; color: #1e3a5f; }
    .sticky-nav { position: sticky; top: 20px; }
    .repeater-row { background: #f8f9ff; border-radius: 8px; padding: 16px; margin-bottom: 12px; border: 1px solid #e8f0fe; }
    .section-divider { border: 0; border-top: 2px dashed #e8f0fe; margin: 16px 0; }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Expediente</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.list') }}">Empleados</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.show', \Crypt::encrypt($user->id)) }}">{{ $user->fullname }}</a></li>
            <li class="breadcrumb-item active">Expediente</li>
        </ul>
    </x-breadcrumb>

    {{-- Header --}}
    <div class="exp-header">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ !empty($user->avatar) ? asset('storage/users/'.$user->avatar) : asset('images/user.jpg') }}"
                style="width:60px;height:60px;border-radius:50%;border:2px solid rgba(255,255,255,0.5);object-fit:cover">
            <div>
                <h4>{{ $user->fullname }}</h4>
                <div class="sub">
                    {{ $detail->oracle_emp_code ?? '' }} —
                    {{ $detail->designation->name ?? 'Sin puesto' }} —
                    @if($detail->store) {{ $detail->store->name }}
                    @elseif($detail->department) {{ $detail->department->name }}
                    @else Sin ubicación
                    @endif
                </div>
            </div>
            @php
                $status = $detail->status ?? 'PENDIENTE';
                $statusMap = [
                    'PENDIENTE'   => ['label' => 'Pendiente',   'color' => '#ffc107', 'text' => '#000'],
                    'COMPLETO'    => ['label' => 'Completo',    'color' => '#198754', 'text' => '#fff'],
                    'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'color' => '#dc3545', 'text' => '#fff'],
                    'INACTIVO'    => ['label' => 'Inactivo',    'color' => '#6c757d', 'text' => '#fff'],
                ];
                $s = $statusMap[$status] ?? $statusMap['PENDIENTE'];
            @endphp
            <span class="ms-auto badge" style="background-color:{{ $s['color'] }};color:{{ $s['text'] }};font-size:13px;padding:6px 14px;border-radius:20px;font-weight:700">
                {{ $s['label'] }}
            </span>
        </div>
    </div>

    <form action="{{ route('employees.expediente.save', \Crypt::encrypt($user->id)) }}" method="POST">
        @csrf

        <div class="row">
            {{-- Navegación lateral --}}
            <div class="col-md-3">
                <div class="sticky-nav">
                    <div class="nav flex-column nav-pills" id="exp-nav">
                        <a class="nav-link active" href="#sec-personal"><i class="fa-solid fa-user fa-xs me-2"></i>Información Personal</a>
                        <a class="nav-link" href="#sec-documentos"><i class="fa-solid fa-id-card fa-xs me-2"></i>Documentos</a>
                        <a class="nav-link" href="#sec-laboral"><i class="fa-solid fa-briefcase fa-xs me-2"></i>Datos Laborales</a>
                        <a class="nav-link" href="#sec-emergencia"><i class="fa-solid fa-phone fa-xs me-2"></i>Contacto Emergencia</a>
                        <a class="nav-link" href="#sec-familia"><i class="fa-solid fa-people-group fa-xs me-2"></i>Familiares</a>
                        <a class="nav-link" href="#sec-educacion"><i class="fa-solid fa-graduation-cap fa-xs me-2"></i>Educación</a>
                        <a class="nav-link" href="#sec-experiencia"><i class="fa-solid fa-timeline fa-xs me-2"></i>Experiencia Laboral</a>
                        <a class="nav-link" href="#sec-extranjero"><i class="fa-solid fa-plane fa-xs me-2"></i>Trabajo Extranjero</a>
                        <a class="nav-link" href="#sec-bancario"><i class="fa-solid fa-building-columns fa-xs me-2"></i>Datos Bancarios</a>
                        <a class="nav-link" href="#sec-salario"><i class="fa-solid fa-money-bill fa-xs me-2"></i>Salario</a>
                    </div>
                    <div class="mt-3 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Expediente
                        </button>
                        <a href="{{ route('employees.show', \Crypt::encrypt($user->id)) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Volver al Perfil
                        </a>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="col-md-9">

                {{-- Información Personal --}}
                <div class="section-card card" id="sec-personal">
                    <div class="card-header"><h6><i class="fa-solid fa-user me-2"></i>Información Personal</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="dob" class="form-control"
                                    value="{{ $detail->dob ? $detail->dob->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lugar de Nacimiento</label>
                                <input type="text" name="birth_place" class="form-control"
                                    value="{{ $detail->birth_place ?? '' }}" placeholder="Ciudad, País">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nacionalidad</label>
                                <input type="text" name="nationality" class="form-control"
                                    value="{{ $detail->nationality ?? '' }}" placeholder="Guatemalteca">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Género</label>
                                <select name="gender" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Masculino" {{ ($detail->gender ?? '') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ ($detail->gender ?? '') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ ($detail->gender ?? '') === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Estado Civil</label>
                                <select name="marital_status" class="form-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Soltero/a','Casado/a','Divorciado/a','Viudo/a','Unión libre'] as $ms)
                                        <option value="{{ $ms }}" {{ ($detail->marital_status ?? '') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Religión</label>
                                <input type="text" name="religion" class="form-control"
                                    value="{{ $detail->religion ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Etnia</label>
                                <input type="text" name="ethnicity" class="form-control"
                                    value="{{ $detail->ethnicity ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No. de Hijos</label>
                                <input type="number" name="no_of_children" class="form-control" min="0"
                                    value="{{ $detail->no_of_children ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Teléfono Adicional</label>
                                <input type="text" name="phone_secondary" class="form-control"
                                    value="{{ $detail->phone_secondary ?? '' }}" placeholder="+502 0000-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Correo Personal</label>
                                <input type="email" name="personal_email" class="form-control"
                                    value="{{ $detail->personal_email ?? '' }}" placeholder="correo@personal.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Discapacidad</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="disability" class="form-check-input" id="disability"
                                        {{ $detail->disability ? 'checked' : '' }}
                                        onchange="document.getElementById('disability-desc').style.display = this.checked ? 'block' : 'none'">
                                    <label class="form-check-label" for="disability">Tiene discapacidad</label>
                                </div>
                                <div id="disability-desc" style="display:{{ $detail->disability ? 'block' : 'none' }}" class="mt-2">
                                    <input type="text" name="disability_description" class="form-control"
                                        value="{{ $detail->disability_description ?? '' }}"
                                        placeholder="Describa el tipo de discapacidad">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documentos Guatemala --}}
                <div class="section-card card" id="sec-documentos">
                    <div class="card-header"><h6><i class="fa-solid fa-id-card me-2"></i>Documentos Guatemala</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">DPI <span class="text-danger">*</span></label>
                                <input type="text" name="dpi_number" class="form-control"
                                    value="{{ $detail->dpi_number ?? '' }}" placeholder="0000 00000 0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lugar de Emisión DPI</label>
                                <input type="text" name="dpi_issued_place" class="form-control"
                                    value="{{ $detail->dpi_issued_place ?? '' }}" placeholder="Guatemala">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">NIT <span class="text-danger">*</span></label>
                                <input type="text" name="nit_number" class="form-control"
                                    value="{{ $detail->nit_number ?? '' }}" placeholder="0000000-0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No. IGSS</label>
                                <input type="text" name="igss_number" class="form-control"
                                    value="{{ $detail->igss_number ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No. IRTRA</label>
                                <input type="text" name="irtra_number" class="form-control"
                                    value="{{ $detail->irtra_number ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Licencia de Conducir</label>
                                <input type="text" name="driver_license" class="form-control"
                                    value="{{ $detail->driver_license ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos Laborales --}}
                <div class="section-card card" id="sec-laboral">
                    <div class="card-header"><h6><i class="fa-solid fa-briefcase me-2"></i>Datos Laborales</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tienda <small class="text-muted">(si es empleado de tienda)</small></label>
                                <select name="store_id" class="form-select">
                                    <option value="">Sin tienda</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ ($detail->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Departamento <small class="text-muted">(si es admin)</small></label>
                                <select name="department_id" class="form-select">
                                    <option value="">Sin departamento</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ ($detail->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Puesto / Designación</label>
                                <select name="designation_id" class="form-select">
                                    <option value="">Seleccionar puesto</option>
                                    @foreach($designations as $desig)
                                        <option value="{{ $desig->id }}" {{ ($detail->designation_id ?? '') == $desig->id ? 'selected' : '' }}>
                                            {{ $desig->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre de Jefe Inmediato</label>
                                <input type="text" name="immediate_supervisor_name" class="form-control"
                                    value="{{ $detail->immediate_supervisor_name ?? '' }}" placeholder="Nombre completo del jefe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Ingreso</label>
                                <input type="date" name="date_joined" class="form-control"
                                    value="{{ $detail->date_joined ? $detail->date_joined->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Contrato</label>
                                <select name="contract_type" class="form-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Posición fija','Temporal','Eventual','Vacacionista'] as $ct)
                                        <option value="{{ $ct }}" {{ ($detail->contract_type ?? '') === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Horario</label>
                                <input type="text" name="work_schedule" class="form-control"
                                    value="{{ $detail->work_schedule ?? '' }}" placeholder="Ej: 8am - 5pm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Horas por Semana</label>
                                <input type="number" name="work_hours_per_week" class="form-control" min="0" max="80"
                                    value="{{ $detail->work_hours_per_week ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Baja</label>
                                <input type="date" name="termination_date" class="form-control"
                                    value="{{ $detail->termination_date ? $detail->termination_date->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Motivo de Retiro</label>
                                <input type="text" name="termination_reason" class="form-control"
                                    value="{{ $detail->termination_reason ?? '' }}" placeholder="Ej: Renuncia voluntaria">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contacto de Emergencia --}}
                <div class="section-card card" id="sec-emergencia">
                    <div class="card-header"><h6><i class="fa-solid fa-phone me-2"></i>Contacto de Emergencia</h6></div>
                    <div class="card-body">
                        @php
                            $primary   = $detail->emergency_contacts['primary'] ?? [];
                            $secondary = $detail->emergency_contacts['secondary'] ?? [];
                        @endphp
                        <p class="text-muted mb-3" style="font-size:12px">Ingresa al menos un contacto en caso de emergencia.</p>
                        <div class="repeater-row">
                            <div class="fw-bold text-muted mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:1px">Contacto Primario</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="primary[name]" class="form-control" value="{{ $primary['name'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Parentesco</label>
                                    <input type="text" name="primary[relationship]" class="form-control" value="{{ $primary['relationship'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="primary[phone]" class="form-control" value="{{ $primary['phone'] ?? '' }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="primary[address]" class="form-control" value="{{ $primary['address'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="repeater-row">
                            <div class="fw-bold text-muted mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:1px">Contacto Secundario</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="secondary[name]" class="form-control" value="{{ $secondary['name'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Parentesco</label>
                                    <input type="text" name="secondary[relationship]" class="form-control" value="{{ $secondary['relationship'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="secondary[phone]" class="form-control" value="{{ $secondary['phone'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Familiares --}}
                <div class="section-card card" id="sec-familia">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fa-solid fa-people-group me-2"></i>Datos Familiares</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="form-check mb-0">
                                <input type="checkbox" name="no_aplica_familia" class="form-check-input" id="no_aplica_familia"
                                    {{ ($detail->no_aplica_familia ?? false) ? 'checked' : '' }}
                                    onchange="toggleSection('familiares-container', 'btn-agregar-familiar', this.checked)">
                                <label class="form-check-label text-muted" style="font-size:12px" for="no_aplica_familia">No aplica</label>
                            </div>
                            <button type="button" id="btn-agregar-familiar" class="btn btn-sm btn-outline-primary" onclick="addFamiliar()"
                                {{ ($detail->no_aplica_familia ?? false) ? 'disabled' : '' }}>
                                <i class="fa-solid fa-plus me-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3" style="font-size:12px">Ingresa datos de padres, hermanos, cónyuge e hijos.</p>
                        <div id="familiares-container">
                            @php $familiares = $user->family ?? collect(); @endphp
                            @if($familiares->count() > 0)
                                @foreach($familiares as $i => $familiar)
                                <div class="repeater-row familiar-row">
                                    <input type="hidden" name="familiares[{{ $i }}][id]" value="{{ $familiar->id }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Nombre completo</label>
                                            <input type="text" name="familiares[{{ $i }}][name]" class="form-control" value="{{ $familiar->name }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Parentesco</label>
                                            <select name="familiares[{{ $i }}][relationship]" class="form-select">
                                                <option value="">Seleccionar</option>
                                                @foreach(['Padre','Madre','Hermano/a','Cónyuge','Hijo/a','Otro'] as $rel)
                                                    <option value="{{ $rel }}" {{ $familiar->relationship === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Fecha de Nacimiento</label>
                                            <input type="date" name="familiares[{{ $i }}][dob]" class="form-control"
                                                value="{{ $familiar->dob ? \Carbon\Carbon::parse($familiar->dob)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeFamiliar(this)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="familiares[{{ $i }}][phone]" class="form-control" value="{{ $familiar->phone }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div id="no-familiares" class="text-muted text-center py-3" style="font-size:13px">
                                    <i class="fa-solid fa-people-group me-2"></i>Sin familiares registrados. Haz clic en Agregar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Educación --}}
                <div class="section-card card" id="sec-educacion">
                    <div class="card-header"><h6><i class="fa-solid fa-graduation-cap me-2"></i>Educación</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nivel Académico</label>
                                <select name="academic_level" class="form-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Primaria','Básico','Diversificado','Técnico','Universidad','Postgrado','Maestría','Doctorado'] as $al)
                                        <option value="{{ $al }}" {{ ($detail->academic_level ?? '') === $al ? 'selected' : '' }}>{{ $al }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Título / Carrera</label>
                                <input type="text" name="degree_title" class="form-control"
                                    value="{{ $detail->degree_title ?? '' }}" placeholder="Ej: Bachiller en Ciencias">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Idiomas <small class="text-muted">(separados por coma)</small></label>
                                <input type="text" name="languages" class="form-control"
                                    value="{{ !empty($detail->languages) ? implode(', ', $detail->languages) : '' }}"
                                    placeholder="Español, Inglés, Maya">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Experiencia Laboral --}}
                <div class="section-card card" id="sec-experiencia">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fa-solid fa-timeline me-2"></i>Experiencia Laboral Anterior</h6>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="form-check mb-0">
                                <input type="checkbox" name="no_aplica_experiencia" class="form-check-input" id="no_aplica_experiencia"
                                    {{ ($detail->no_aplica_experiencia ?? false) ? 'checked' : '' }}
                                    onchange="toggleSection('experiencia-container', 'btn-agregar-exp', this.checked)">
                                <label class="form-check-label text-muted" style="font-size:12px" for="no_aplica_experiencia">No aplica</label>
                            </div>
                            <button type="button" id="btn-agregar-exp" class="btn btn-sm btn-outline-primary" onclick="addExperiencia()"
                                {{ ($detail->no_aplica_experiencia ?? false) ? 'disabled' : '' }}>
                                <i class="fa-solid fa-plus me-1"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="experiencia-container">
                            @php $experiencias = $detail->workExperience ?? collect(); @endphp
                            @if($experiencias->count() > 0)
                                @foreach($experiencias as $i => $exp)
                                <div class="repeater-row exp-row">
                                    <input type="hidden" name="experiencias[{{ $i }}][id]" value="{{ $exp->id }}">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Empresa</label>
                                            <input type="text" name="experiencias[{{ $i }}][company]" class="form-control" value="{{ $exp->company }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Puesto</label>
                                            <input type="text" name="experiencias[{{ $i }}][position]" class="form-control" value="{{ $exp->position }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Fecha inicio</label>
                                            <input type="date" name="experiencias[{{ $i }}][start_date]" class="form-control"
                                                value="{{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Fecha fin</label>
                                            <input type="date" name="experiencias[{{ $i }}][end_date]" class="form-control"
                                                value="{{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">País</label>
                                            <input type="text" name="experiencias[{{ $i }}][location]" class="form-control" value="{{ $exp->location }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeExperiencia(this)">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div id="no-experiencia" class="text-muted text-center py-3" style="font-size:13px">
                                    <i class="fa-solid fa-timeline me-2"></i>Sin experiencia registrada. Haz clic en Agregar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Trabajo en el Extranjero --}}
                <div class="section-card card" id="sec-extranjero">
                    <div class="card-header"><h6><i class="fa-solid fa-plane me-2"></i>Trabajo en el Extranjero</h6></div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="worked_abroad" class="form-check-input" id="worked_abroad"
                                {{ $detail->worked_abroad ? 'checked' : '' }}
                                onchange="document.getElementById('sec-extranjero-fields').style.display = this.checked ? 'block' : 'none'">
                            <label class="form-check-label" for="worked_abroad">Ha trabajado en el extranjero</label>
                        </div>
                        <div id="sec-extranjero-fields" style="display:{{ $detail->worked_abroad ? 'block' : 'none' }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">País</label>
                                    <input type="text" name="foreign_country" class="form-control" value="{{ $detail->foreign_country ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Empresa</label>
                                    <input type="text" name="foreign_company" class="form-control" value="{{ $detail->foreign_company ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Puesto</label>
                                    <input type="text" name="foreign_job_title" class="form-control" value="{{ $detail->foreign_job_title ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Datos Bancarios --}}
                <div class="section-card card" id="sec-bancario">
                    <div class="card-header"><h6><i class="fa-solid fa-building-columns me-2"></i>Datos Bancarios</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Forma de Pago</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Cuenta monetaria" {{ ($detail->payment_method ?? '') === 'Cuenta monetaria' ? 'selected' : '' }}>Cuenta monetaria</option>
                                    <option value="Cheque" {{ ($detail->payment_method ?? '') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Banco</label>
                                <select name="bank_name" class="form-select">
                                    <option value="">Seleccionar banco</option>
                                    @foreach(['Banrural','Banco Industrial','BAC Credomatic','Banco Agromercantil','G&T Continental','Banco Promerica','Banco Azteca','Bantrab','Banpaís','Otro'] as $banco)
                                        <option value="{{ $banco }}" {{ ($detail->bank_name ?? '') === $banco ? 'selected' : '' }}>{{ $banco }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número de Cuenta</label>
                                <input type="text" name="bank_account_number" class="form-control"
                                    value="{{ $detail->bank_account_number ?? '' }}" placeholder="000-000000-0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Cuenta</label>
                                <select name="bank_account_type" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Monetaria" {{ ($detail->bank_account_type ?? '') === 'Monetaria' ? 'selected' : '' }}>Monetaria</option>
                                    <option value="Ahorro" {{ ($detail->bank_account_type ?? '') === 'Ahorro' ? 'selected' : '' }}>Ahorro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Salario --}}
                <div class="section-card card" id="sec-salario">
                    <div class="card-header"><h6><i class="fa-solid fa-money-bill me-2"></i>Información Salarial</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sueldo Ordinario</label>
                                <div class="input-group">
                                    <span class="input-group-text">GTQ</span>
                                    <input type="number" step="0.01" name="base_salary" class="form-control"
                                        value="{{ $detail->salaryDetails->base_salary ?? '' }}" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bonificación Decreto 37-2001</label>
                                <div class="input-group">
                                    <span class="input-group-text">GTQ</span>
                                    <input type="number" step="0.01" name="bonificacion_decreto" class="form-control"
                                        value="{{ $detail->salaryDetails->bonificacion_decreto ?? '250.00' }}" placeholder="250.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bonificación Variable</label>
                                <div class="input-group">
                                    <span class="input-group-text">GTQ</span>
                                    <input type="number" step="0.01" name="bonificacion_variable" class="form-control"
                                        value="{{ $detail->salaryDetails->bonificacion_variable ?? '' }}" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bonificación Variable sujeta a prestaciones</label>
                                <select name="bonificacion_variable_prestaciones" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="1" {{ ($detail->salaryDetails->bonus_subject_to_benefits ?? '') == 1 ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ isset($detail->salaryDetails->bonus_subject_to_benefits) && $detail->salaryDetails->bonus_subject_to_benefits == 0 && $detail->salaryDetails->bonus_subject_to_benefits !== null ? 'selected' : '' }}>No</option>
                                    <option value="2" {{ ($detail->salaryDetails->bonus_subject_to_benefits ?? '') == 2 ? 'selected' : '' }}>No aplica</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría de Premios</label>
                                <select name="categoria_premios" class="form-select">
                                    <option value="">Seleccionar</option>
                                    @foreach(['Supervisor','Jefe de tienda','Ventas','No aplica'] as $cat)
                                        <option value="{{ $cat }}" {{ ($detail->salaryDetails->categoria_premios ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Salario</label>
                                <select name="salary_basis" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="monthly" {{ ($detail->salaryDetails->basis->value ?? '') === 'monthly' ? 'selected' : '' }}>Mensual</option>
                                    <option value="weekly" {{ ($detail->salaryDetails->basis->value ?? '') === 'weekly' ? 'selected' : '' }}>Semanal</option>
                                    <option value="hourly" {{ ($detail->salaryDetails->basis->value ?? '') === 'hourly' ? 'selected' : '' }}>Por hora</option>
                                    <option value="contract" {{ ($detail->salaryDetails->basis->value ?? '') === 'contract' ? 'selected' : '' }}>Por contrato</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Método de Pago de Nómina</label>
                                <select name="salary_payment_method" class="form-select">
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
                <div class="d-flex gap-2 justify-content-end mb-4">
                    <a href="{{ route('employees.show', \Crypt::encrypt($user->id)) }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Expediente
                    </button>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection

@push('page-scripts')
<script>
let familiarIndex = {{ $user->family ? $user->family->count() : 0 }};
let expIndex = {{ $detail->workExperience ? $detail->workExperience->count() : 0 }};

function addFamiliar() {
    document.getElementById('no-familiares') && (document.getElementById('no-familiares').style.display = 'none');
    const html = `
    <div class="repeater-row familiar-row">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="familiares[${familiarIndex}][name]" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Parentesco</label>
                <select name="familiares[${familiarIndex}][relationship]" class="form-select">
                    <option value="">Seleccionar</option>
                    <option value="Padre">Padre</option>
                    <option value="Madre">Madre</option>
                    <option value="Hermano/a">Hermano/a</option>
                    <option value="Cónyuge">Cónyuge</option>
                    <option value="Hijo/a">Hijo/a</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" name="familiares[${familiarIndex}][dob]" class="form-control">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeFamiliar(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input type="text" name="familiares[${familiarIndex}][phone]" class="form-control">
            </div>
        </div>
    </div>`;
    document.getElementById('familiares-container').insertAdjacentHTML('beforeend', html);
    familiarIndex++;
}

function removeFamiliar(btn) {
    btn.closest('.familiar-row').remove();
}

function addExperiencia() {
    document.getElementById('no-experiencia') && (document.getElementById('no-experiencia').style.display = 'none');
    const html = `
    <div class="repeater-row exp-row">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Empresa</label>
                <input type="text" name="experiencias[${expIndex}][company]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Puesto</label>
                <input type="text" name="experiencias[${expIndex}][position]" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="experiencias[${expIndex}][start_date]" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="experiencias[${expIndex}][end_date]" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">País/Ciudad</label>
                <input type="text" name="experiencias[${expIndex}][location]" class="form-control">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeExperiencia(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>`;
    document.getElementById('experiencia-container').insertAdjacentHTML('beforeend', html);
    expIndex++;
}

function removeExperiencia(btn) {
    btn.closest('.exp-row').remove();
}

// Scroll suave al hacer clic en nav
document.querySelectorAll('.nav-pills .nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.nav-pills .nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});

function toggleSection(containerId, btnId, disabled) {
    const container = document.getElementById(containerId);
    const btn = document.getElementById(btnId);
    if (disabled) {
        container.style.opacity = '0.4';
        container.style.pointerEvents = 'none';
        btn.disabled = true;
    } else {
        container.style.opacity = '1';
        container.style.pointerEvents = 'auto';
        btn.disabled = false;
    }
}
</script>
@endpush