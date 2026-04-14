@extends('layouts.app')

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }
.form-card {
    background:#fff;border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    overflow:hidden;margin-bottom:20px;
}
.form-card-header {
    padding:15px 20px;border-bottom:1px solid #f1f5f9;
    background:linear-gradient(to right,#f8faff,#fff);
}
.form-card-title {
    font-size:13px;font-weight:700;color:#0f2456;margin:0;
    display:flex;align-items:center;gap:10px;
}
.form-card-body { padding:24px; }
.form-label { font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px; }
.form-control, .form-select {
    border:1px solid #e2e8f0;border-radius:10px;
    font-size:13px;padding:10px 14px;
    transition:all 0.2s;
}
.form-control:focus, .form-select:focus {
    border-color:#1e3c72;
    box-shadow:0 0 0 3px rgba(30,60,114,0.1);
}
.cuota-preview {
    background:#f0f9ff;border:1px solid #bae6fd;
    border-radius:10px;padding:16px 20px;
    font-size:13px;color:#0369a1;font-weight:600;
    display:none;
}
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Registrar Uniforme</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('uniformes.index') }}">Uniformes</a></li>
            <li class="breadcrumb-item active">Registrar Entrega</li>
        </ul>
    </x-breadcrumb>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-card">
                <div class="form-card-header">
                    <h6 class="form-card-title">
                        <span style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#1e3c72,#2a5298);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fa-solid fa-shirt fa-xs" style="color:#fff"></i>
                        </span>
                        Registrar Entrega de Uniforme
                    </h6>
                </div>
                <div class="form-card-body">
                    @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        @foreach($errors->all() as $e)
                        <div><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                    @endif

                    <form action="{{ route('uniformes.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Empleado <span class="text-danger">*</span></label>
                                <select name="employee_detail_id" class="form-select" required
                                    onchange="updateEmpleado(this)">
                                    <option value="">Seleccionar empleado...</option>
                                    @foreach($empleados as $emp)
                                    @php
                                        $detail = $emp->employeeDetail;
                                        $ubi = $detail->store->name ?? ($detail->department->name ?? '');
                                        $code = $detail->emp_code ?? $detail->oracle_emp_code ?? '';
                                    @endphp
                                    <option value="{{ $detail->id ?? '' }}"
                                        {{ old('employee_detail_id') == ($detail->id ?? '') ? 'selected' : '' }}>
                                        {{ $emp->fullname }}
                                        {{ $code ? "[$code]" : '' }}
                                        {{ $ubi ? "— $ubi" : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Entrega <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_entrega" class="form-control" required
                                    value="{{ old('fecha_entrega', date('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Monto Total (GTQ) <span class="text-danger">*</span></label>
                                <input type="number" name="monto_total" class="form-control"
                                    step="0.01" min="1" required
                                    value="{{ old('monto_total') }}"
                                    placeholder="0.00"
                                    oninput="calcularCuota()">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Número de Cuotas <span class="text-danger">*</span></label>
                                <select name="num_cuotas" class="form-select" required
                                    onchange="calcularCuota()">
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('num_cuotas', 1) == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'cuota' : 'cuotas' }}
                                    </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Descripción</label>
                                <input type="text" name="descripcion" class="form-control"
                                    value="{{ old('descripcion') }}"
                                    placeholder="Ej: Uniforme completo, camisa, pantalón...">
                            </div>

                            <div class="col-12">
                                <div class="cuota-preview" id="cuota-preview">
                                    <i class="fa-solid fa-calculator me-2"></i>
                                    Cuota mensual: <span id="cuota-val">GTQ 0.00</span>
                                    — Se descontará automáticamente en cada nómina
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                                <a href="{{ route('uniformes.index') }}"
                                    class="btn btn-outline-secondary" style="border-radius:10px;font-size:13px">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary px-4"
                                    style="border-radius:10px;font-size:13px;font-weight:600;background:#1e3c72;border:none">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('page-scripts')
<script>
function calcularCuota() {
    const monto   = parseFloat(document.querySelector('[name="monto_total"]').value) || 0;
    const cuotas  = parseInt(document.querySelector('[name="num_cuotas"]').value) || 1;
    const preview = document.getElementById('cuota-preview');
    const val     = document.getElementById('cuota-val');

    if (monto > 0) {
        const cuota = (monto / cuotas).toFixed(2);
        val.textContent = 'GTQ ' + parseFloat(cuota).toLocaleString('es-GT', {minimumFractionDigits: 2});
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush