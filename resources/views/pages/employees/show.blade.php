@extends('layouts.app')

@push('page-style')
<style>
    .profile-header { background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%); color: #fff; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
    .profile-header .emp-avatar { width: 90px; height: 90px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.5); object-fit: cover; }
    .profile-header h3 { color: #fff; margin: 0 0 4px; font-size: 20px; font-weight: 700; }
    .profile-header .sub { color: rgba(255,255,255,0.8); font-size: 13px; }
    .info-card { border: none; border-radius: 10px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); margin-bottom: 20px; }
    .info-card .card-title { font-size: 14px; font-weight: 700; color: #1e3a5f; border-bottom: 2px solid #e8f0fe; padding-bottom: 10px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; }
    .info-row { display: flex; padding: 7px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #888; width: 160px; flex-shrink: 0; font-weight: 500; }
    .info-value { color: #333; font-weight: 500; }
    .badge-code { font-size: 13px; padding: 5px 12px; border-radius: 20px; font-weight: 700; }
    .nav-tabs .nav-link { font-size: 13px; font-weight: 600; color: #666; }
    .nav-tabs .nav-link.active { color: #1e3a5f; border-bottom: 2px solid #1e3a5f; }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Perfil de Empleado</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('employees.list') }}">Empleados</a></li>
            <li class="breadcrumb-item active">Perfil</li>
        </ul>
    </x-breadcrumb>

    @php
        $empCode  = $employee->emp_code ?? null;
        $status   = $employee->status ?? 'PENDIENTE';
        $statusMap = [
            'PENDIENTE'   => ['label' => 'Pendiente',   'color' => 'warning',   'text' => '#000'],
            'COMPLETO'    => ['label' => 'Completo',    'color' => 'success',   'text' => '#fff'],
            'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'color' => 'danger',    'text' => '#fff'],
            'INACTIVO'    => ['label' => 'Inactivo',    'color' => 'secondary', 'text' => '#fff'],
        ];
        $s = $statusMap[$status] ?? $statusMap['PENDIENTE'];
        $codeColor = $empCode ? (str_starts_with($empCode, 'A-') ? 'primary' : 'dark') : 'secondary';
        if (!empty($employee->department_id) && $employee->department) {
            $ubicIcon = 'fa-building';
            $ubicNombre = $employee->department->name;
        } elseif (!empty($employee->store_id) && $employee->store) {
            $ubicIcon = 'fa-store';
            $ubicNombre = $employee->store->name;
        } else {
            $ubicIcon = 'fa-question';
            $ubicNombre = 'Sin asignar';
        }
        $bgColor = match($s['color']) {
            'warning'   => '#ffc107',
            'success'   => '#198754',
            'danger'    => '#dc3545',
            'secondary' => '#6c757d',
            default     => '#6c757d'
        };
    @endphp

    {{-- HEADER --}}
    <div class="profile-header">
        <div class="d-flex align-items-center gap-4">
            <img src="{{ !empty($user->avatar) ? asset('storage/users/'.$user->avatar) : asset('images/user.jpg') }}"
                alt="Avatar" class="emp-avatar">
            <div class="flex-fill">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3>{{ $user->fullname }}</h3>
                    <span class="badge bg-{{ $codeColor }} badge-code">{{ $empCode ?? 'Sin código' }}</span>
                    <span class="badge badge-code" style="background-color:{{ $bgColor }};color:{{ $s['text'] }}">
                        {{ $s['label'] }}
                    </span>
                </div>
                <div class="sub mb-1">
                    <i class="fa-solid fa-briefcase fa-xs me-1"></i>{{ $employee->designation->name ?? '—' }}
                </div>
                <div class="sub">
                    <i class="fa-solid {{ $ubicIcon }} fa-xs me-1"></i>{{ $ubicNombre }}
                </div>
            </div>
            <div class="ms-auto d-flex gap-2">
                @if($status === 'PENDIENTE' || $status === null)
                <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}"
                    class="btn btn-sm btn-warning fw-bold" style="color:#000">
                    <i class="fa-solid fa-file-pen me-1"></i> Completar Expediente
                </a>
                @elseif($status === 'COMPLETO')
                <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}"
                    class="btn btn-sm btn-success">
                    <i class="fa-solid fa-file-pen me-1"></i> Editar Expediente
                </a>
                @elseif($status === 'DAR_DE_BAJA')
                <a href="{{ route('employees.expediente', \Crypt::encrypt($user->id)) }}"
                    class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-file-pen me-1"></i> Procesar Liquidación
                </a>
                @endif
                <a href="javascript:void(0)"
                    data-url="{{ route('employees.edit', ['employee' => \Crypt::encrypt($user->id)]) }}"
                    data-ajax-modal="true" data-title="Editar Empleado" data-size="lg"
                    class="btn btn-sm btn-light">
                    <i class="fa-solid fa-pencil me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="card mb-3" style="border-radius:10px;border:none;box-shadow:0 1px 6px rgba(0,0,0,0.07)">
        <div class="card-body py-0">
            <ul class="nav nav-tabs border-0">
                <li class="nav-item"><a href="#tab-perfil" data-bs-toggle="tab" class="nav-link active">Perfil</a></li>
                <li class="nav-item"><a href="#tab-laboral" data-bs-toggle="tab" class="nav-link">Datos Laborales</a></li>
                <li class="nav-item"><a href="#tab-educacion" data-bs-toggle="tab" class="nav-link">Educación</a></li>
                <li class="nav-item"><a href="#tab-familia" data-bs-toggle="tab" class="nav-link">Familia</a></li>
                @superadmin
                <li class="nav-item"><a href="#tab-salario" data-bs-toggle="tab" class="nav-link">Salario</a></li>
                @endsuperadmin
            </ul>
        </div>
    </div>

    <div class="tab-content">

        {{-- TAB PERFIL --}}
        <div id="tab-perfil" class="tab-pane fade show active">
            <div class="row">
                <div class="col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">
                                Información Personal
                                <a href="javascript:void(0)" data-url="{{ route('employee.personal-info', $employee->id) }}"
                                    data-ajax-modal="true" data-title="Información Personal" data-size="lg">
                                    <i class="fa-solid fa-pencil fa-xs text-muted"></i>
                                </a>
                            </div>
                            @if($employee->dob)
                            <div class="info-row"><span class="info-label">Fecha de nacimiento</span><span class="info-value">{{ format_date($employee->dob) }}</span></div>
                            @endif
                            @if($employee->birth_place)
                            <div class="info-row"><span class="info-label">Lugar de nacimiento</span><span class="info-value">{{ $employee->birth_place }}</span></div>
                            @endif
                            @if($employee->nationality)
                            <div class="info-row"><span class="info-label">Nacionalidad</span><span class="info-value">{{ $employee->nationality }}</span></div>
                            @endif
                            @if($employee->marital_status)
                            <div class="info-row"><span class="info-label">Estado civil</span><span class="info-value">{{ $employee->marital_status }}</span></div>
                            @endif
                            @if($employee->religion)
                            <div class="info-row"><span class="info-label">Religión</span><span class="info-value">{{ $employee->religion }}</span></div>
                            @endif
                            @if($employee->ethnicity)
                            <div class="info-row"><span class="info-label">Etnia</span><span class="info-value">{{ $employee->ethnicity }}</span></div>
                            @endif
                            @if($employee->no_of_children)
                            <div class="info-row"><span class="info-label">No. de hijos</span><span class="info-value">{{ $employee->no_of_children }}</span></div>
                            @endif
                            @if($user->address)
                            <div class="info-row"><span class="info-label">Dirección</span><span class="info-value">{{ $user->address }}</span></div>
                            @endif
                            @if($user->phone)
                            <div class="info-row"><span class="info-label">Teléfono</span><span class="info-value">{{ $user->phoneNumber }}</span></div>
                            @endif
                            @if($user->email)
                            <div class="info-row"><span class="info-label">Correo</span><span class="info-value">{{ $user->email }}</span></div>
                            @endif
                            @if($employee->gender)
                            <div class="info-row"><span class="info-label">Género</span><span class="info-value">{{ $employee->gender }}</span></div>
                            @endif
                            @if($employee->phone_secondary)
                            <div class="info-row"><span class="info-label">Teléfono adicional</span><span class="info-value">{{ $employee->phone_secondary }}</span></div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">
                                Documentos Guatemala
                                <a href="javascript:void(0)" data-url="{{ route('employee.personal-info', $employee->id) }}"
                                    data-ajax-modal="true" data-title="Documentos" data-size="lg">
                                    <i class="fa-solid fa-pencil fa-xs text-muted"></i>
                                </a>
                            </div>
                            @if($employee->dpi_number)
                            <div class="info-row"><span class="info-label">DPI</span><span class="info-value">{{ $employee->dpi_number }}</span></div>
                            @endif
                            @if($employee->dpi_issued_place)
                            <div class="info-row"><span class="info-label">Lugar emisión DPI</span><span class="info-value">{{ $employee->dpi_issued_place }}</span></div>
                            @endif
                            @if($employee->nit_number)
                            <div class="info-row"><span class="info-label">NIT</span><span class="info-value">{{ $employee->nit_number }}</span></div>
                            @endif
                            @if($employee->igss_number)
                            <div class="info-row"><span class="info-label">No. IGSS</span><span class="info-value">{{ $employee->igss_number }}</span></div>
                            @endif
                            @if($employee->irtra_number)
                            <div class="info-row"><span class="info-label">No. IRTRA</span><span class="info-value">{{ $employee->irtra_number }}</span></div>
                            @endif
                            @if($employee->driver_license)
                            <div class="info-row"><span class="info-label">Licencia conducir</span><span class="info-value">{{ $employee->driver_license }}</span></div>
                            @endif
                            @if($employee->disability !== null)
                            <div class="info-row"><span class="info-label">Discapacidad</span><span class="info-value">{{ $employee->disability ? 'Sí' : 'No' }}</span></div>
                            @endif
                            @if($employee->disability && $employee->disability_description)
                            <div class="info-row"><span class="info-label">Tipo de discapacidad</span><span class="info-value">{{ $employee->disability_description }}</span></div>
                            @endif
                        </div>
                    </div>

                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">
                                Contacto de Emergencia
                                <a href="javascript:void(0)" data-url="{{ route('employee.emergency-contacts', $employee->id) }}"
                                    data-ajax-modal="true" data-title="Contactos de Emergencia" data-size="lg">
                                    <i class="fa-solid fa-pencil fa-xs text-muted"></i>
                                </a>
                            </div>
                            @php
                                $primary   = $employee->emergency_contacts['primary'] ?? null;
                                $secondary = $employee->emergency_contacts['secondary'] ?? null;
                            @endphp
                            @if($primary)
                                <div class="small fw-bold text-muted mb-2">Primario</div>
                                <div class="info-row"><span class="info-label">Nombre</span><span class="info-value">{{ $primary['name'] ?? '—' }}</span></div>
                                <div class="info-row"><span class="info-label">Parentesco</span><span class="info-value">{{ $primary['relationship'] ?? '—' }}</span></div>
                                <div class="info-row"><span class="info-label">Teléfono</span><span class="info-value">{{ $primary['phone'] ?? '—' }}</span></div>
                            @endif
                            @if($secondary)
                                <div class="small fw-bold text-muted mt-3 mb-2">Secundario</div>
                                <div class="info-row"><span class="info-label">Nombre</span><span class="info-value">{{ $secondary['name'] ?? '—' }}</span></div>
                                <div class="info-row"><span class="info-label">Parentesco</span><span class="info-value">{{ $secondary['relationship'] ?? '—' }}</span></div>
                                <div class="info-row"><span class="info-label">Teléfono</span><span class="info-value">{{ $secondary['phone'] ?? '—' }}</span></div>
                            @endif
                            @if(!$primary && !$secondary)
                                <p class="text-muted small">Sin contactos registrados.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB LABORAL --}}
        <div id="tab-laboral" class="tab-pane fade">
            <div class="row">
                <div class="col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">Datos Laborales</div>
                            @if($employee->date_joined)
                            <div class="info-row"><span class="info-label">Fecha de ingreso</span><span class="info-value">{{ format_date($employee->date_joined) }}</span></div>
                            @endif
                            @if($employee->contract_type)
                            <div class="info-row"><span class="info-label">Tipo de contrato</span><span class="info-value">{{ $employee->contract_type }}</span></div>
                            @endif
                            @if($employee->work_schedule)
                            <div class="info-row"><span class="info-label">Horario</span><span class="info-value">{{ $employee->work_schedule }}</span></div>
                            @endif
                            @if($employee->work_hours_per_week)
                            <div class="info-row"><span class="info-label">Horas por semana</span><span class="info-value">{{ $employee->work_hours_per_week }}</span></div>
                            @endif
                            @if($employee->supervisor)
                            <div class="info-row"><span class="info-label">Supervisor</span><span class="info-value">{{ $employee->supervisor->fullname }}</span></div>
                            @endif
                            @if($employee->termination_date)
                            <div class="info-row"><span class="info-label">Fecha de baja</span><span class="info-value">{{ format_date($employee->termination_date) }}</span></div>
                            @endif
                            @if($employee->termination_reason)
                            <div class="info-row"><span class="info-label">Motivo de baja</span><span class="info-value">{{ $employee->termination_reason }}</span></div>
                            @endif
                        </div>
                        <div class="card info-card mt-3">
                            <div class="card-body">
                                <div class="card-title">Datos Bancarios</div>
                                @if($employee->payment_method)
                                <div class="info-row"><span class="info-label">Forma de pago</span><span class="info-value">{{ $employee->payment_method }}</span></div>
                                @endif
                                @if($employee->bank_name)
                                <div class="info-row"><span class="info-label">Banco</span><span class="info-value">{{ $employee->bank_name }}</span></div>
                                @endif
                                @if($employee->bank_account_number)
                                <div class="info-row"><span class="info-label">No. de cuenta</span><span class="info-value">{{ $employee->bank_account_number }}</span></div>
                                @endif
                                @if($employee->bank_account_type)
                                <div class="info-row"><span class="info-label">Tipo de cuenta</span><span class="info-value">{{ $employee->bank_account_type }}</span></div>
                                @endif
                                @if($employee->personal_email)
                                <div class="info-row"><span class="info-label">Correo personal</span><span class="info-value">{{ $employee->personal_email }}</span></div>
                                @endif
                                @if($employee->immediate_supervisor_name)
                                <div class="info-row"><span class="info-label">Jefe inmediato</span><span class="info-value">{{ $employee->immediate_supervisor_name }}</span></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">Datos Oracle PRISMA</div>
                            <div class="info-row"><span class="info-label">Código Oracle</span><span class="info-value">{{ $employee->oracle_emp_code ?? '—' }}</span></div>
                            <div class="info-row"><span class="info-label">Código SmartHR</span><span class="info-value">{{ $employee->emp_code ?? 'Sin asignar' }}</span></div>
                            <div class="info-row"><span class="info-label">Estado Oracle</span>
                                <span class="info-value">
                                    @if($employee->oracle_active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row"><span class="info-label">Estado expediente</span>
                                <span class="info-value">
                                    <span class="badge" style="background-color:{{ $bgColor }};color:{{ $s['text'] }}">{{ $s['label'] }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($employee->worked_abroad)
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">Experiencia en el Extranjero</div>
                            @if($employee->foreign_country)
                            <div class="info-row"><span class="info-label">País</span><span class="info-value">{{ $employee->foreign_country }}</span></div>
                            @endif
                            @if($employee->foreign_company)
                            <div class="info-row"><span class="info-label">Empresa</span><span class="info-value">{{ $employee->foreign_company }}</span></div>
                            @endif
                            @if($employee->foreign_job_title)
                            <div class="info-row"><span class="info-label">Puesto</span><span class="info-value">{{ $employee->foreign_job_title }}</span></div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- TAB EDUCACION --}}
        <div id="tab-educacion" class="tab-pane fade">
            <div class="row">
                <div class="col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">
                                Educación
                                <a href="javascript:void(0)" data-url="{{ route('employee.education', $employee->id) }}"
                                    data-ajax-modal="true" data-title="Información Educativa" data-size="lg">
                                    <i class="fa-solid fa-pencil fa-xs text-muted"></i>
                                </a>
                            </div>
                            @if($employee->academic_level)
                            <div class="info-row"><span class="info-label">Nivel académico</span><span class="info-value">{{ $employee->academic_level }}</span></div>
                            @endif
                            @if($employee->degree_title)
                            <div class="info-row"><span class="info-label">Título</span><span class="info-value">{{ $employee->degree_title }}</span></div>
                            @endif
                            @if(!empty($employee->languages))
                            <div class="info-row"><span class="info-label">Idiomas</span><span class="info-value">{{ implode(', ', $employee->languages) }}</span></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card info-card">
                        <div class="card-body">
                            <div class="card-title">
                                Experiencia Laboral
                                <a href="javascript:void(0)" data-url="{{ route('employee.experience', $employee->id) }}"
                                    data-ajax-modal="true" data-title="Experiencia Laboral" data-size="lg">
                                    <i class="fa-solid fa-pencil fa-xs text-muted"></i>
                                </a>
                            </div>
                            @forelse($employee->workExperience as $exp)
                            <div class="info-row flex-column" style="align-items:flex-start;gap:2px">
                                <div class="fw-600" style="font-size:13px">{{ $exp->position }} — {{ $exp->company }}</div>
                                <div class="text-muted" style="font-size:12px">{{ format_date($exp->start_date) }} - {{ format_date($exp->end_date) }}</div>
                            </div>
                            @empty
                            <p class="text-muted small">Sin experiencia registrada.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB FAMILIA --}}
        <div id="tab-familia" class="tab-pane fade">
            <div class="card info-card">
                <div class="card-body">
                    <div class="card-title">
                        Información Familiar
                        <a href="javascript:void(0)" data-url="{{ route('family-information.create', ['user' => $user->id]) }}"
                            data-ajax-modal="true" data-title="Agregar Familiar" data-size="lg">
                            <i class="fa-solid fa-plus fa-xs text-muted"></i>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Parentesco</th>
                                    <th>Fecha Nacimiento</th>
                                    <th>Teléfono</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->family ?? [] as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->relationship }}</td>
                                    <td>{{ format_date($member->dob) }}</td>
                                    <td>{{ $member->phone }}</td>
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
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-muted text-center small">Sin familiares registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB SALARIO --}}
        @superadmin
        <div id="tab-salario" class="tab-pane fade">
            <div class="card info-card">
                <div class="card-body">
                    <div class="card-title">Información Salarial</div>
                    <form action="{{ route('employee.salary-setting', $employee->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="salary_detail_id" value="{{ $employee->salaryDetails->id ?? '' }}">
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Tipo de salario <span class="text-danger">*</span></label>
                                    <select class="form-control" name="basis">
                                        <option value="">Seleccionar tipo</option>
                                        <option value="monthly" {{ ($employee->salaryDetails->basis?->value ?? '') === 'monthly' ? 'selected': '' }}>Mensual</option>
                                        <option value="weekly" {{ ($employee->salaryDetails->basis?->value ?? '') === 'weekly' ? 'selected': '' }}>Semanal</option>
                                        <option value="hourly" {{ ($employee->salaryDetails->basis?->value ?? '') === 'hourly' ? 'selected': '' }}>Por hora</option>
                                        <option value="contract" {{ ($employee->salaryDetails->basis?->value ?? '') === 'contract' ? 'selected': '' }}>Por contrato</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Sueldo Ordinario</label>
                                    <div class="input-group">
                                        <span class="input-group-text">GTQ</span>
                                        <input type="text" class="form-control" name="base_salary"
                                            value="{{ $employee->salaryDetails->base_salary ?? '0.00' }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Método de pago</label>
                                    <select class="form-control" name="payment_method">
                                        <option value="">Seleccionar método</option>
                                        <option value="bank" {{ ($employee->salaryDetails->payment_method?->value ?? '') === 'bank' ? 'selected': '' }}>Transferencia bancaria</option>
                                        <option value="cheque" {{ ($employee->salaryDetails->payment_method?->value ?? '') === 'cheque' ? 'selected': '' }}>Cheque</option>
                                        <option value="cash" {{ ($employee->salaryDetails->payment_method?->value ?? '') === 'cash' ? 'selected': '' }}>Efectivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Bonificación Decreto 37-2001</label>
                                    <div class="input-group">
                                        <span class="input-group-text">GTQ</span>
                                        <input type="text" class="form-control" name="bonificacion_decreto"
                                            value="{{ $employee->salaryDetails->bonificacion_decreto ?? '250.00' }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Bonificación Variable</label>
                                    <div class="input-group">
                                        <span class="input-group-text">GTQ</span>
                                        <input type="text" class="form-control" name="bonificacion_variable"
                                            value="{{ $employee->salaryDetails->variable_bonus ?? '0.00' }}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Bonif. Variable sujeta a prestaciones</label>
                                    <select class="form-control" name="bonificacion_variable_prestaciones">
                                        <option value="">Seleccionar</option>
                                        <option value="1" {{ ($employee->salaryDetails->bonus_subject_to_benefits ?? '') == 1 ? 'selected': '' }}>Sí</option>
                                        <option value="0" {{ isset($employee->salaryDetails->bonus_subject_to_benefits) && $employee->salaryDetails->bonus_subject_to_benefits == 0 && $employee->salaryDetails->bonus_subject_to_benefits !== null ? 'selected': '' }}>No</option>
                                        <option value="2" {{ ($employee->salaryDetails->bonus_subject_to_benefits ?? '') == 2 ? 'selected': '' }}>No aplica</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-block mb-3">
                                    <label class="col-form-label">Categoría de Premios</label>
                                    <select class="form-control" name="categoria_premios">
                                        <option value="">Seleccionar</option>
                                        @foreach(['Supervisor','Jefe de tienda','Ventas','No aplica'] as $cat)
                                            <option value="{{ $cat }}" {{ ($employee->salaryDetails->award_category ?? '') === $cat ? 'selected': '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsuperadmin

    </div>

</div>
@endsection