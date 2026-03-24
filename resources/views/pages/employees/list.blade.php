@extends('layouts.app')

@push('page-style')
<style>
    #employee-table .badge {
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

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
                    <a href="{{ route('employees.index') }}" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
                    <a href="{{ route('employees.list') }}" class="list-view btn btn-link active"><i class="fa-solid fa-bars"></i></a>
                </div>
            </div>
        </x-slot>
    </x-breadcrumb>

    {{-- Contadores --}}
    <div class="row mb-4 g-3">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $counts['total'] }}</div>
                    <div class="small text-muted">Total empleados</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $counts['pendiente'] }}</div>
                    <div class="small text-muted">Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-success">{{ $counts['completo'] }}</div>
                    <div class="small text-muted">Completos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="fs-2 fw-bold text-danger">{{ $counts['dar_de_baja'] }}</div>
                    <div class="small text-muted">Dar de baja</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped custom-table w-100']) !!}
            </div>
        </div>
    </div>

</div>
@endsection

@push('page-scripts')
@vite(["resources/js/datatables.js"])
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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