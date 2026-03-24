@extends('layouts.app')

@push('page-style')
<style>
    span.badge.bg-warning.position-absolute {
        color: #000 !important;
        font-size: 13px !important;
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-weight: 700 !important;
    }
    span.badge.bg-dark.position-absolute,
    span.badge.bg-primary.position-absolute,
    span.badge.bg-secondary.position-absolute,
    span.badge.bg-danger.position-absolute,
    span.badge.bg-success.position-absolute {
        font-size: 13px !important;
        padding: 6px 14px !important;
        border-radius: 20px !important;
        font-weight: 700 !important;
    }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    {{-- Header --}}
    <x-breadcrumb class="col">
        <x-slot name="title">{{ __('Empleados') }}</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            </li>
            <li class="breadcrumb-item active">{{ __('Empleados') }}</li>
        </ul>
        <x-slot name="right">
            <div class="col-auto float-end ms-auto d-flex gap-2 align-items-center">
                <button type="button" id="btn-sync" class="btn btn-outline-primary" onclick="syncEmployees()">
                    <i class="fa-solid fa-rotate"></i> Sincronizar desde PRISMA
                </button>
                <a href="javascript:void(0)" data-url="{{ route('employees.create') }}"
                    class="btn add-btn" data-ajax-modal="true" data-size="lg" data-title="Agregar Empleado">
                    <i class="fa-solid fa-plus"></i> {{ __('Agregar Empleado') }}
                </a>
                <div class="view-icons">
                    <a href="{{ route('employees.index') }}" class="grid-view btn btn-link active"><i class="fa fa-th"></i></a>
                    <a href="{{ route('employees.list') }}" class="list-view btn btn-link"><i class="fa-solid fa-bars"></i></a>
                </div>
            </div>
        </x-slot>
    </x-breadcrumb>

    {{-- Contadores --}}
    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100 counter-card" data-filter="all" style="cursor:pointer">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $counts['total'] }}</div>
                    <div class="small text-muted">Total empleados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100 counter-card" data-filter="PENDIENTE" style="cursor:pointer">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $counts['pendiente'] }}</div>
                    <div class="small text-muted">Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100 counter-card" data-filter="COMPLETO" style="cursor:pointer">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-success">{{ $counts['completo'] }}</div>
                    <div class="small text-muted">Completos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100 counter-card" data-filter="DAR_DE_BAJA" style="cursor:pointer">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $counts['dar_de_baja'] }}</div>
                    <div class="small text-muted">Dar de baja</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="row mb-3 align-items-center">
        <div class="col-auto">
            <input type="text" id="search-grid" class="form-control" placeholder="Buscar empleado...">
        </div>
        <div class="col-auto">
            <select id="filter-tipo" class="form-select">
                <option value="all">Todos los tipos</option>
                <option value="T-">Solo tiendas</option>
                <option value="A-">Solo admin</option>
                <option value="sin">Sin codigo</option>
            </select>
        </div>
    </div>

    {{-- Grid de empleados --}}
    <div class="row staff-grid-row" id="employees-grid">
        @foreach ($employees as $employee)
            @php
                $detail      = $employee->employeeDetail;
                $status      = $detail->status ?? 'PENDIENTE';
                $empCode     = $detail->emp_code ?? null;
                $designation = $detail->designation->name ?? '-';
                $statusMap   = [
                    'PENDIENTE'   => ['label' => 'Pendiente',   'color' => 'warning'],
                    'COMPLETO'    => ['label' => 'Completo',    'color' => 'success'],
                    'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'color' => 'danger'],
                    'INACTIVO'    => ['label' => 'Inactivo',    'color' => 'secondary'],
                ];
                $s = $statusMap[$status] ?? $statusMap['PENDIENTE'];
                $codeColor = $empCode ? (str_starts_with($empCode, 'A-') ? 'primary' : 'dark') : 'secondary';
                if (!empty($detail->department_id) && $detail->department) {
                    $ubicacion = '<i class="fa-solid fa-building fa-xs me-1"></i>' . $detail->department->name;
                } elseif (!empty($detail->store_id) && $detail->store) {
                    $ubicacion = '<i class="fa-solid fa-store fa-xs me-1"></i>' . $detail->store->name;
                } else {
                    $ubicacion = '-';
                }
                $tipoCode = $empCode ? (str_starts_with($empCode, 'A-') ? 'A-' : 'T-') : 'sin';
            @endphp
            <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3 employee-card"
                data-status="{{ $status }}"
                data-tipo="{{ $tipoCode }}"
                data-name="{{ strtolower($employee->fullname) }}">
                <div class="profile-widget position-relative">

                    {{-- Badge status --}}
                    <span class="badge bg-{{ $s['color'] }} position-absolute" style="top:10px;left:10px;z-index:1;">{{ $s['label'] }}</span>

                    {{-- Badge codigo --}}
                    <span class="badge bg-{{ $codeColor }} position-absolute" style="top:10px;right:40px;z-index:1;">{{ $empCode ?? 'Sin codigo' }}</span>

                    {{-- Menu --}}
                    <div class="dropdown profile-action">
                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('employees.expediente', \Crypt::encrypt($employee->id)) }}">
                                <i class="fa-solid fa-file-pen m-r-5"></i>
                                {{ $status === 'COMPLETO' ? 'Editar Expediente' : 'Completar Expediente' }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)"
                                data-url="{{ route('employees.edit', ['employee' => \Crypt::encrypt($employee->id)]) }}"
                                data-ajax-modal="true" data-title="Editar Empleado" data-size="lg">
                                <i class="fa-solid fa-pencil m-r-5"></i> Editar
                            </a>
                            <a class="dropdown-item deleteBtn"
                                data-route="{{ route('employees.destroy', $employee->id) }}"
                                data-title="Eliminar Empleado"
                                data-question="Estas segura de eliminar este empleado?"
                                href="javascript:void(0)">
                                <i class="fa-regular fa-trash-can m-r-5"></i> Eliminar
                            </a>
                        </div>
                    </div>

                    {{-- Avatar --}}
                    <div class="profile-img mt-3">
                        <a href="{{ route('employees.show', ['employee' => \Crypt::encrypt($employee->id)]) }}" class="avatar">
                            <img src="{{ !empty($employee->avatar) ? uploadedAsset($employee->avatar,'users') : asset('images/user.jpg') }}" alt="Avatar">
                        </a>
                    </div>

                    {{-- Nombre y puesto --}}
                    <h4 class="user-name m-t-10 mb-0 text-ellipsis">
                        <a href="{{ route('employees.show', ['employee' => \Crypt::encrypt($employee->id)]) }}">
                            {{ $employee->fullname }}
                        </a>
                    </h4>
                    <div class="small text-muted mb-1">{{ $designation }}</div>
                    <div class="small text-muted pb-2">{!! $ubicacion !!}</div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.counter-card').forEach(card => {
    card.addEventListener('click', function () {
        document.querySelectorAll('.counter-card').forEach(c => c.classList.remove('border-primary'));
        this.classList.add('border-primary');
        filterGrid();
    });
});

function filterGrid() {
    const search        = document.getElementById('search-grid').value.toLowerCase();
    const tipo          = document.getElementById('filter-tipo').value;
    const activeCounter = document.querySelector('.counter-card.border-primary');
    const statusFilter  = activeCounter ? activeCounter.dataset.filter : 'all';

    document.querySelectorAll('.employee-card').forEach(card => {
        const matchStatus = statusFilter === 'all' || card.dataset.status === statusFilter;
        const matchTipo   = tipo === 'all' || card.dataset.tipo === tipo;
        const matchSearch = card.dataset.name.includes(search);
        card.style.display = (matchStatus && matchTipo && matchSearch) ? '' : 'none';
    });
}

document.getElementById('search-grid').addEventListener('input', filterGrid);
document.getElementById('filter-tipo').addEventListener('change', filterGrid);

function syncEmployees() {
    const btn = document.getElementById('btn-sync');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-rotate fa-spin"></i> Sincronizando...';

    fetch('{{ route("employees.sync") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const output = data.output.replace(/<br \/>/g, '\n');
            const get = (label) => {
                const match = output.match(new RegExp(label + ':\\s*(\\d+)'));
                return match ? match[1] : '0';
            };
            Swal.fire({
                icon: 'success',
                title: 'Sincronizacion completada',
                html: `
                    <div style="text-align:left;font-size:14px;line-height:2">
                        <div><b>Tiendas sincronizadas:</b> 83</div>
                        <hr style="margin:8px 0">
                        <div><b>Empleados nuevos:</b> ${get('Empleados nuevos creados')}</div>
                        <div><b>Empleados actualizados:</b> ${get('Empleados existentes actualizados')}</div>
                        <div><b>Marcados DAR DE BAJA:</b> ${get('Empleados marcados DAR_DE_BAJA')}</div>
                        <hr style="margin:8px 0">
                        <div><b>Con datos completos:</b> ${get('Con datos completos')}</div>
                        <div><b>Pendientes RH:</b> ${get('Sin datos \\(pendiente RH\\)')}</div>
                    </div>
                `,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#0dcaf0',
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error en sincronizacion', text: data.message });
        }
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo conectar al servidor.' });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Sincronizar desde PRISMA';
    });
}
</script>
@endpush