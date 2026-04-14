@extends('layouts.app')

@push('vendor-styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }

.nom-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%);
    border-radius: 18px; padding: 0;
    margin-bottom: 22px;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35);
    overflow: hidden;
}
.nom-hero-inner { padding: 24px 32px; position: relative; z-index: 2; }
.nom-hero-stats {
    background: rgba(0,0,0,0.18);
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 14px 32px;
    display: flex; gap: 0;
}
.hero-stat { flex:1;text-align:center;padding:0 16px;border-right:1px solid rgba(255,255,255,0.1); }
.hero-stat:last-child { border-right:none; }
.hero-stat .hs-val { font-size:18px;font-weight:800;color:#fff;display:block; }
.hero-stat .hs-label { font-size:10px;font-weight:600;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.8px; }

.table-nom { width:100%;border-collapse:collapse;font-size:12px; }
.table-nom thead th {
    font-size:10px;font-weight:700;text-transform:uppercase;
    letter-spacing:0.5px;color:#94a3b8;
    padding:10px 12px;background:#f8fafc;
    border-bottom:1px solid #eef2f7;
    white-space:nowrap;
}
.table-nom tbody td {
    padding:9px 12px;border-bottom:1px solid #f8fafc;
    color:#1e293b;vertical-align:middle;
}
.table-nom tbody tr:hover td { background:#f8fbff; }
.table-nom tbody tr:last-child td { border-bottom:none; }

.editable-cell {
    background:#fffbeb;border:1px solid #fcd34d;
    border-radius:6px;padding:3px 8px;
    font-size:12px;font-weight:600;
    width:80px;text-align:right;
    cursor:pointer;transition:all 0.2s;
}
.editable-cell:focus {
    outline:none;background:#fff;
    border-color:#1e3c72;box-shadow:0 0 0 2px rgba(30,60,114,0.15);
}
.cell-saved { background:#d1fae5 !important;border-color:#10b981 !important; }

.nom-section {
    background:#fff;border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    margin-bottom:20px;overflow:hidden;
}
.nom-section-header {
    padding:14px 20px;border-bottom:1px solid #f1f5f9;
    display:flex;align-items:center;justify-content:space-between;
    background:linear-gradient(to right,#f8faff,#fff);
}
.nom-section-title {
    font-size:13px;font-weight:700;color:#0f2456;
    margin:0;display:flex;align-items:center;gap:10px;
}
.sec-icon {
    width:32px;height:32px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;color:#fff;flex-shrink:0;
}

/* ══ Search bar mejorado ══ */
.nom-table-toolbar {
    padding: 16px 20px;
    background: #f8faff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.nom-search-wrap {
    position: relative;
    flex: 1;
    max-width: 380px;
}
.nom-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
    pointer-events: none;
}
.nom-search-input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 9px 16px 9px 36px;
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
    background: #fff;
    transition: all 0.2s;
    outline: none;
}
.nom-search-input:focus {
    border-color: #1e3c72;
    box-shadow: 0 0 0 3px rgba(30,60,114,0.1);
}
.nom-search-input::placeholder { color: #cbd5e1; }
.nom-length-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}
.nom-length-select {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 7px 28px 7px 12px;
    font-size: 13px;
    color: #1e293b;
    background: #fff;
    cursor: pointer;
    outline: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 8px center;
}
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length { display: none !important; }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">{{ $nomina->titulo }}</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('nomina.index') }}">Nómina</a></li>
            <li class="breadcrumb-item active">{{ $nomina->titulo }}</li>
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
    <div class="nom-hero">
        <div class="nom-hero-inner">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:6px">
                        Planilla #{{ $nomina->numero_planilla }}
                    </div>
                    <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:6px">
                        {{ $nomina->titulo }}
                    </div>
                    <div style="font-size:13px;color:rgba(255,255,255,0.65)">
                        <i class="fa-solid fa-calendar me-1"></i>
                        Período: {{ $nomina->fecha_inicio_periodo->format('d/m/Y') }}
                        al {{ $nomina->fecha_fin_periodo->format('d/m/Y') }}
                        &nbsp;·&nbsp;
                        Pago: {{ $nomina->fecha_pago->format('d/m/Y') }}
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @if($nomina->estado === 'BORRADOR')
                    <form action="{{ route('nomina.cerrar', $nomina->id) }}" method="POST" id="form-cerrar-nomina">
                        @csrf
                    </form>
                    <button type="button" class="btn btn-warning fw-bold" style="border-radius:10px;font-size:13px;color:#000"
                        onclick="confirmarCierreNomina()">
                        <i class="fa-solid fa-lock me-1"></i> Cerrar Nómina
                    </button>
                    @else
                    <span class="badge bg-success" style="font-size:13px;padding:8px 16px;border-radius:10px">
                        <i class="fa-solid fa-lock me-1"></i> Cerrada
                    </span>
                    @endif
                    <a href="{{ route('nomina.export.excel', $nomina->id) }}"
                        class="btn" style="background:#10b981;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600">
                        <i class="fa-solid fa-file-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('nomina.export.pdf', $nomina->id) }}"
                        class="btn" id="btn-pdf"
                        style="background:#ef4444;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600"
                        onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin me-1\'></i> Generando...';this.style.opacity='0.7';this.style.pointerEvents='none';setTimeout(()=>{this.innerHTML='<i class=\'fa-solid fa-file-pdf me-1\'></i> PDF';this.style.opacity='1';this.style.pointerEvents='auto'},30000)">
                        <i class="fa-solid fa-file-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('nomina.index') }}"
                        class="btn" style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:10px;font-size:13px">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="nom-hero-stats">
            <div class="hero-stat">
                <span class="hs-val">{{ $nomina->total_empleados }}</span>
                <span class="hs-label">Empleados</span>
            </div>
            <div class="hero-stat">
                <span class="hs-val" style="font-size:15px">GTQ {{ number_format($nomina->total_devengado, 2) }}</span>
                <span class="hs-label">Total devengado</span>
            </div>
            <div class="hero-stat">
                <span class="hs-val" style="font-size:15px">GTQ {{ number_format($nomina->total_deducciones, 2) }}</span>
                <span class="hs-label">Total deducciones</span>
            </div>
            <div class="hero-stat">
                <span class="hs-val" style="font-size:15px;color:#10b981">GTQ {{ number_format($nomina->total_liquido, 2) }}</span>
                <span class="hs-label">Líquido a pagar</span>
            </div>
        </div>
    </div>

    {{-- Tabla de nómina --}}
    <div class="nom-section">
        <div class="nom-section-header">
            <h6 class="nom-section-title">
                <span class="sec-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                    <i class="fa-solid fa-table fa-xs"></i>
                </span>
                Detalle de Empleados
                @if($nomina->estado === 'BORRADOR')
                <span style="font-size:11px;background:#fef3c7;color:#92400e;padding:3px 8px;border-radius:6px;font-weight:600">
                    <i class="fa-solid fa-pencil fa-xs me-1"></i> Los campos en amarillo son editables
                </span>
                @endif
            </h6>
        </div>
        <div style="overflow-x:auto">
            {{-- Toolbar personalizada --}}
        <div class="nom-table-toolbar">
            <div class="nom-length-wrap">
                <span>Mostrar</span>
                <select class="nom-length-select" id="customLength">
                    <option value="20" selected>20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>registros</span>
            </div>
            <div class="nom-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="nom-search-input" id="customSearch"
                    placeholder="Buscar por nombre, código PRISMA o SmartHR...">
            </div>
        </div>
            <table class="table-nom" id="tabla-nomina" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cód. PRISMA</th>
                        <th>Cód. SmartHR</th>
                        <th>Nombre</th>
                        <th>SUP</th>
                        <th>Tienda</th>
                        <th>Puesto</th>
                        <th>Días</th>
                        <th>Obs.</th>
                        <th>Sal. Base</th>
                        <th>Salario</th>
                        <th>H. Extra</th>
                        <th>Bonif.</th>
                        <th>Bono Var.</th>
                        <th>Tot. Bonif.</th>
                        <th>Tot. Deven.</th>
                        <th>IGSS</th>
                        <th>ISR</th>
                        @if($nomina->tipo === 'SEGUNDA_QUINCENA')
                        <th>Anticipo 40%</th>
                        @endif
                        <th>Préstamos</th>
                        <th>AYUVI</th>
                        <th>Desc. Judicial</th>
                        <th>Faltante Inv.</th>
                        <th>Uniforme</th>
                        <th>CxC</th>
                        <th>Tot. Otros</th>
                        <th>Tot. Deduc.</th>
                        <th style="color:#10b981">Líquido</th>
                        <th>COD</th>
                        <th>REF</th>
                        <th>CT. BAC</th>
                        <th>Nombre Banco</th>
                        <th style="color:#10b981">Monto a Pagar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detalle as $i => $row)
                    @php
                        $emp = $row->empleado;
                        $user = $emp->user ?? null;
                        $ubicacion = $emp->store->name ?? ($emp->department->name ?? '—');
                        $editable = $nomina->estado === 'BORRADOR';
                    @endphp
                    <tr data-id="{{ $row->id }}"
                    style="{{ str_contains($row->observacion ?? '', 'PENDIENTE') ? 'background:#fee2e2;border-left:4px solid #ef4444;' : '' }}">
                    <td style="color:#94a3b8;font-weight:600">{{ $i + 1 }}</td>
                    <td style="font-size:11px;color:#64748b;font-weight:600">{{ $emp->oracle_emp_code ?? '—' }}</td>
                    <td>
                        <span style="background:{{ str_starts_with($emp->emp_code ?? '', 'A-') ? '#dbeafe' : '#f1f5f9' }};
                            color:{{ str_starts_with($emp->emp_code ?? '', 'A-') ? '#1e40af' : '#334155' }};
                            padding:2px 8px;border-radius:6px;font-size:11px;font-weight:700">
                            {{ $emp->emp_code ?? '—' }}
                        </span>
                    </td>
                    <td style="font-weight:600;white-space:nowrap">{{ $user->fullname ?? '—' }}</td>
                    <td style="font-size:11px;color:#64748b;text-align:center">
                        {{ $emp->store->supervisor ?? '—' }}
                    </td>
                    <td style="font-size:11px;color:#64748b;text-align:center">
                        {{ $emp->store->oracle_store_no ?? ($emp->department->name ?? '—') }}
                    </td>
                    <td style="font-size:11px;color:#64748b;white-space:nowrap">
                        {{ $emp->designation->name ?? '—' }}
                    </td>
                    <td>
                        @if($editable)
                        <input type="number" class="editable-cell" style="width:55px"
                            data-field="dias_trabajados" data-id="{{ $row->id }}"
                            value="{{ $row->dias_trabajados }}" min="0" max="31">
                        @else
                        {{ $row->dias_trabajados }}
                        @endif
                    </td>
                    <td>
                        @if(str_contains($row->observacion ?? '', 'PENDIENTE'))
                            <span style="color:#ef4444;font-size:11px;font-weight:700;white-space:nowrap">
                                <i class="fa-solid fa-triangle-exclamation fa-xs me-1"></i>Sin expediente
                            </span>
                        @else
                            @if($editable)
                            <input type="text" class="editable-cell" style="width:120px;text-align:left"
                                data-field="observacion" data-id="{{ $row->id }}"
                                value="{{ $row->observacion ?? '' }}" placeholder="—">
                            @else
                            {{ $row->observacion ?? '—' }}
                            @endif
                        @endif
                    </td>
                    <td>{{ $row->salario_base > 0 ? number_format($row->salario_base, 2) : '—' }}</td>
                    <td>{{ $row->total_devengado > 0 ? number_format($row->total_devengado, 2) : '—' }}</td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="salario_extra_ordinario" data-id="{{ $row->id }}"
                            value="{{ $row->salario_extra_ordinario }}" min="0" step="0.01">
                        @else
                        {{ $row->salario_extra_ordinario > 0 ? number_format($row->salario_extra_ordinario, 2) : '—' }}
                        @endif
                    </td>
                    <td>{{ $row->bonificacion_decreto > 0 ? number_format($row->bonificacion_decreto, 2) : '—' }}</td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="bono_variable" data-id="{{ $row->id }}"
                            value="{{ $row->bono_variable }}" min="0" step="0.01">
                        @else
                        {{ $row->bono_variable > 0 ? number_format($row->bono_variable, 2) : '—' }}
                        @endif
                    </td>
                    <td data-show="total_bonificaciones">{{ $row->total_bonificaciones > 0 ? number_format($row->total_bonificaciones, 2) : '—' }}</td>
                    <td data-show="total_devengado" style="font-weight:700">{{ $row->total_devengado > 0 ? number_format($row->total_devengado, 2) : '—' }}</td>
                    <td data-show="igss">{{ $row->igss > 0 ? number_format($row->igss, 2) : '—' }}</td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell {{ $row->isr_editado ? 'cell-saved' : '' }}"
                            data-field="isr" data-id="{{ $row->id }}"
                            value="{{ $row->isr }}" min="0" step="0.01">
                        @else
                        {{ $row->isr > 0 ? number_format($row->isr, 2) : '—' }}
                        @endif
                    </td>
                    @if($nomina->tipo === 'SEGUNDA_QUINCENA')
                    <td>{{ $row->anticipo_40 > 0 ? number_format($row->anticipo_40, 2) : '—' }}</td>
                    @endif
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="prestamos" data-id="{{ $row->id }}"
                            value="{{ $row->prestamos }}" min="0" step="0.01">
                        @else
                        {{ $row->prestamos > 0 ? number_format($row->prestamos, 2) : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="ayuvi" data-id="{{ $row->id }}"
                            value="{{ $row->ayuvi }}" min="0" step="0.01">
                        @else
                        {{ $row->ayuvi > 0 ? number_format($row->ayuvi, 2) : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="descuento_judicial" data-id="{{ $row->id }}"
                            value="{{ $row->descuento_judicial }}" min="0" step="0.01">
                        @else
                        {{ $row->descuento_judicial > 0 ? number_format($row->descuento_judicial, 2) : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="faltante_inventario" data-id="{{ $row->id }}"
                            value="{{ $row->faltante_inventario }}" min="0" step="0.01">
                        @else
                        {{ $row->faltante_inventario > 0 ? number_format($row->faltante_inventario, 2) : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="uniforme" data-id="{{ $row->id }}"
                            value="{{ $row->uniforme }}" min="0" step="0.01">
                        @else
                        {{ $row->uniforme > 0 ? number_format($row->uniforme, 2) : '—' }}
                        @endif
                    </td>
                    <td>
                        @if($editable && !str_contains($row->observacion ?? '', 'PENDIENTE'))
                        <input type="number" class="editable-cell"
                            data-field="cxc" data-id="{{ $row->id }}"
                            value="{{ $row->cxc }}" min="0" step="0.01">
                        @else
                        {{ $row->cxc > 0 ? number_format($row->cxc, 2) : '—' }}
                        @endif
                    </td>
                    <td data-show="total_otros_descuentos">{{ $row->total_otros_descuentos > 0 ? number_format($row->total_otros_descuentos, 2) : '—' }}</td>
                    <td data-show="total_deducciones" style="font-weight:700;color:#ef4444">{{ $row->total_deducciones > 0 ? number_format($row->total_deducciones, 2) : '—' }}</td>
                    <td data-show="liquido_a_recibir" style="font-weight:800;color:#10b981">{{ $row->liquido_a_recibir > 0 ? number_format($row->liquido_a_recibir, 2) : '—' }}</td>
                    {{-- Info bancaria --}}
                    <td style="font-size:11px">{{ $emp->oracle_emp_code ?? '—' }}</td>
                    <td style="font-size:11px">{{ $row->referencia_banco ?? '—' }}</td>
                    <td style="font-size:11px">{{ $row->cuenta_banco ?? '—' }}</td>
                    <td style="font-size:11px;white-space:nowrap">{{ $emp->bank_name ?? '—' }}</td>
                    <td style="font-weight:800;color:#10b981;white-space:nowrap">
                        {{ $row->liquido_a_recibir > 0 ? 'GTQ '.number_format($row->liquido_a_recibir, 2) : '—' }}
                    </td>
                </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc;font-weight:800;font-size:12px">
                        <td colspan="{{ $nomina->tipo === 'SEGUNDA_QUINCENA' ? 12 : 11 }}" style="padding:12px;color:#0f2456">TOTALES</td>
                        <td id="footer-devengado" style="padding:12px">GTQ {{ number_format($nomina->total_devengado, 2) }}</td>
                        <td colspan="{{ $nomina->tipo === 'SEGUNDA_QUINCENA' ? 8 : 7 }}"></td>
                        <td id="footer-deducciones" style="padding:12px;color:#ef4444">GTQ {{ number_format($nomina->total_deducciones, 2) }}</td>
                        <td id="footer-liquido" style="padding:12px;color:#10b981">GTQ {{ number_format($nomina->total_liquido, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>

$(document).ready(function() {
    initDataTable();
});

function initDataTable() {
    var table = $('#tabla-nomina').DataTable({
        pageLength: 20,
        lengthMenu: [20, 30, 50, 100],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[5, 'asc'], [3, 'asc']],
        drawCallback: function() {
            attachEditableEvents();
        }
    });

    document.getElementById('customSearch').addEventListener('keyup', function() {
        table.search(this.value).draw();
    });

    document.getElementById('customLength').addEventListener('change', function() {
        table.page.len(this.value).draw();
    });

    attachEditableEvents();
}

function attachEditableEvents() {
    document.querySelectorAll('.editable-cell').forEach(function(input) {
        if (input.dataset.bound) return;
        input.dataset.bound = '1';

        input.addEventListener('change', function() {
            const id    = this.dataset.id;
            const field = this.dataset.field;
            const value = this.value;
            const row   = this.closest('tr');

            const body = {};
            body[field] = value;
            //body['_method'] = 'PUT';

            fetch('/nomina/detalle/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(body)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.classList.add('cell-saved');
                    setTimeout(() => this.classList.remove('cell-saved'), 2000);

                    const d = data.row;

                    updateCell(row, 'igss',                   d.igss);
                    updateCell(row, 'total_bonificaciones',   d.total_bonificaciones);
                    updateCell(row, 'total_devengado',        d.total_devengado);
                    updateCell(row, 'total_otros_descuentos', d.total_otros_descuentos);
                    updateCell(row, 'total_deducciones',      d.total_deducciones);
                    updateCell(row, 'liquido_a_recibir',      d.liquido_a_recibir);
                    updateCell(row, 'salario_devengado',      d.salario_devengado);

                    const montoCell = row.querySelector('td:last-child');
                    if (montoCell) montoCell.textContent = 'GTQ ' + parseFloat(d.liquido_a_recibir).toFixed(2);

                    // Actualizar hero stats
                    if (data.nomina) {
                        const n = data.nomina;
                        const hsVals = document.querySelectorAll('.hs-val');
                        console.log('hs-val count:', hsVals.length, 'nomina:', n);
                        hsVals.forEach(function(el, i) {
                            console.log('hs-val[' + i + ']:', el.textContent);
                        });
                        if (hsVals[1]) hsVals[1].textContent = 'GTQ ' + parseFloat(n.total_devengado).toLocaleString('es-GT', {minimumFractionDigits:2});
                        if (hsVals[2]) hsVals[2].textContent = 'GTQ ' + parseFloat(n.total_deducciones).toLocaleString('es-GT', {minimumFractionDigits:2});
                        if (hsVals[3]) hsVals[3].textContent = 'GTQ ' + parseFloat(n.total_liquido).toLocaleString('es-GT', {minimumFractionDigits:2});

                        // Actualizar footer de tabla
                        const fmt = (v) => 'GTQ ' + parseFloat(v).toLocaleString('es-GT', {minimumFractionDigits:2});
                        const fd = document.getElementById('footer-devengado');
                        const fded = document.getElementById('footer-deducciones');
                        const fliq = document.getElementById('footer-liquido');
                        if (fd) fd.textContent = fmt(n.total_devengado);
                        if (fded) fded.textContent = fmt(n.total_deducciones);
                        if (fliq) fliq.textContent = fmt(n.total_liquido);
                    }
                }
            })
            .catch(err => console.error(err));
        });
    });
}

function updateCell(row, field, value) {
    const input = row.querySelector('[data-field="' + field + '"]');
    if (input) {
        input.value = parseFloat(value).toFixed(2);
        return;
    }
    // Si no es input editable buscar td por data-field
    const td = row.querySelector('[data-show="' + field + '"]');
    if (td) td.textContent = parseFloat(value) > 0 ? parseFloat(value).toFixed(2) : '—';
}

function confirmarCierreNomina() {
    Swal.fire({
        title: 'Cerrar Nómina',
        html: `
            <div style="text-align:left;font-size:13px;color:#1e293b">
                <div style="background:#f8fafc;border-radius:10px;padding:16px;margin-bottom:16px;border:1px solid #e2e8f0">
                    <div style="font-size:12px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Resumen de la nómina</div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="color:#64748b">Planilla No.</span>
                        <strong>{{ $nomina->numero_planilla }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="color:#64748b">Período</span>
                        <strong>{{ $nomina->fecha_inicio_periodo->format('d/m/Y') }} — {{ $nomina->fecha_fin_periodo->format('d/m/Y') }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="color:#64748b">Fecha de pago</span>
                        <strong>{{ $nomina->fecha_pago->format('d/m/Y') }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="color:#64748b">Total empleados</span>
                        <strong>{{ $nomina->total_empleados }}</strong>
                    </div>
                    <div style="height:1px;background:#e2e8f0;margin:10px 0"></div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="color:#64748b">Total devengado</span>
                        <strong style="color:#10b981">GTQ {{ number_format($nomina->total_devengado, 2) }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="color:#64748b">Total deducciones</span>
                        <strong style="color:#ef4444">GTQ {{ number_format($nomina->total_deducciones, 2) }}</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="color:#64748b;font-weight:700">Total líquido</span>
                        <strong style="color:#1e3a5f;font-size:15px">GTQ {{ number_format($nomina->total_liquido, 2) }}</strong>
                    </div>
                </div>
                <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:12px;display:flex;gap:10px;align-items:flex-start">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;margin-top:2px;flex-shrink:0"></i>
                    <div>
                        <div style="font-weight:700;color:#92400e;margin-bottom:3px">Esta acción es irreversible</div>
                        <div style="color:#a16207;font-size:12px">Una vez cerrada, la nómina no podrá editarse y se generarán los recibos de pago para todos los empleados.</div>
                    </div>
                </div>
            </div>
        `,
        icon: null,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-lock me-1"></i> Sí, cerrar nómina',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#1e3a5f',
        cancelButtonColor: '#94a3b8',
        width: 480,
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-cerrar-nomina').submit();
        }
    });
}

</script>
@endpush