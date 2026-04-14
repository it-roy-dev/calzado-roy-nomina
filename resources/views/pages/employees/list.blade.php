@extends('layouts.app')

@push('vendor-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@push('page-styles')
<style>
* { font-family: 'Inter', sans-serif; }

/* ══ HERO ══ */
.emp-list-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%);
    border-radius: 18px;
    padding: 24px 32px;
    margin-bottom: 24px;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35);
    position: relative;
    overflow: hidden;
}
.emp-list-hero::before {
    content:'';position:absolute;top:-60px;right:-60px;
    width:280px;height:280px;border-radius:50%;
    background:rgba(255,255,255,0.04);
}
.emp-list-hero h4 { color:#fff;font-weight:800;font-size:20px;margin:0; }
.emp-list-hero p  { color:rgba(255,255,255,0.65);font-size:13px;margin:0; }

/* ══ STAT CARDS ══ */
.emp-stat-card {
    background:#fff;
    border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    padding:18px 20px;
    display:flex;align-items:center;gap:14px;
    cursor:pointer;
    transition:all 0.2s;
    text-decoration:none;
}
.emp-stat-card:hover { box-shadow:0 4px 16px rgba(30,60,114,0.12);transform:translateY(-1px); }
.emp-stat-card.active { border-color:#1e3c72;box-shadow:0 4px 16px rgba(30,60,114,0.2); }
.emp-stat-icon {
    width:46px;height:46px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:18px;color:#fff;flex-shrink:0;
}
.emp-stat-val { font-size:24px;font-weight:800;color:#0f2456;line-height:1; }
.emp-stat-label { font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px; }

/* ══ TABLE CARD ══ */
.emp-table-card {
    background:#fff;
    border-radius:14px;
    border:1px solid #eef2f7;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
    overflow:hidden;
    margin-bottom:20px;
}
.emp-table-toolbar {
    padding:16px 20px;
    background:#f8faff;
    border-bottom:1px solid #f1f5f9;
    display:flex;align-items:center;justify-content:space-between;
    flex-wrap:wrap;gap:12px;
}
.emp-search-wrap {
    position:relative;flex:1;max-width:360px;
}
.emp-search-wrap i {
    position:absolute;left:12px;top:50%;
    transform:translateY(-50%);
    color:#94a3b8;font-size:13px;pointer-events:none;
}
.emp-search-input {
    width:100%;border:1px solid #e2e8f0;border-radius:10px;
    padding:9px 16px 9px 36px;font-size:13px;font-weight:500;
    color:#1e293b;background:#fff;transition:all 0.2s;outline:none;
}
.emp-search-input:focus { border-color:#1e3c72;box-shadow:0 0 0 3px rgba(30,60,114,0.1); }
.emp-search-input::placeholder { color:#cbd5e1; }

.emp-filter-btns { display:flex;gap:6px;flex-wrap:wrap; }
.emp-filter-btn {
    font-size:12px;font-weight:600;padding:6px 14px;
    border-radius:8px;border:1px solid #e2e8f0;
    background:#fff;color:#64748b;cursor:pointer;
    transition:all 0.18s;white-space:nowrap;
}
.emp-filter-btn:hover { border-color:#1e3c72;color:#1e3c72; }
.emp-filter-btn.active { background:#1e3c72;color:#fff;border-color:#1e3c72; }
.emp-filter-btn.warning.active { background:#f59e0b;border-color:#f59e0b;color:#000; }
.emp-filter-btn.success.active { background:#10b981;border-color:#10b981;color:#fff; }
.emp-filter-btn.danger.active  { background:#ef4444;border-color:#ef4444;color:#fff; }

/* ══ DATATABLES OVERRIDE ══ */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length { display:none !important; }
.dataTables_wrapper .dataTables_info {
    padding:12px 20px;font-size:12px;color:#94a3b8;font-weight:500;
    background:#f8fafc;border-top:1px solid #f1f5f9;
}
.dataTables_wrapper .dataTables_paginate {
    padding:12px 20px;background:#f8fafc;
    border-top:1px solid #f1f5f9;
    display:flex;justify-content:flex-end;gap:4px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius:8px !important;font-size:12px !important;
    font-weight:600 !important;padding:5px 11px !important;
    margin:0 2px !important;border:1px solid #e2e8f0 !important;
    color:#64748b !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background:#1e3c72 !important;border-color:#1e3c72 !important;color:#fff !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background:linear-gradient(135deg,#1e3c72,#2a5298) !important;
    border-color:#1e3c72 !important;color:#fff !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity:0.35 !important; }

/* Tabla */
#employee-table thead th {
    font-size:11px !important;font-weight:700 !important;
    text-transform:uppercase;letter-spacing:0.5px;
    color:#94a3b8 !important;background:#f8fafc !important;
    padding:11px 14px !important;border-bottom:1px solid #eef2f7 !important;
    white-space:nowrap;
}
#employee-table tbody td {
    padding:10px 14px !important;
    border-bottom:1px solid #f8fafc !important;
    font-size:13px;vertical-align:middle;
}
#employee-table tbody tr:hover td { background:#f8fbff !important; }
#employee-table tbody tr:last-child td { border-bottom:none !important; }

/* Length select */
.emp-length-wrap {
    display:flex;align-items:center;gap:8px;
    font-size:13px;font-weight:600;color:#64748b;
}
.emp-length-select {
    border:1px solid #e2e8f0;border-radius:8px;
    padding:6px 28px 6px 10px;font-size:13px;
    color:#1e293b;background:#fff;cursor:pointer;
    outline:none;appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 8px center;
}

/* Sync btn */
.btn-sync {
    background:rgba(255,255,255,0.12);color:#fff;
    border:1px solid rgba(255,255,255,0.25);border-radius:10px;
    font-size:13px;font-weight:600;padding:9px 18px;
    transition:all 0.2s;display:inline-flex;align-items:center;gap:6px;
}
.btn-sync:hover { background:rgba(255,255,255,0.2);color:#fff; }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb class="col">
        <x-slot name="title">Empleados</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Empleados</li>
        </ul>
    </x-breadcrumb>

    {{-- HERO --}}
    <div class="emp-list-hero" style="position:relative;z-index:1">
        <div class="d-flex align-items-center justify-content-between" style="position:relative;z-index:2">
            <div>
                <h4><i class="fa-solid fa-users me-2"></i>Gestión de Empleados</h4>
                <p>Internacional de Calzado S.A. — Guatemala</p>
            </div>
            <button type="button" id="btn-sync" class="btn-sync" onclick="syncEmployees()">
                <i class="fa-solid fa-rotate"></i> Sincronizar desde PRISMA
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="emp-stat-card active" id="filter-todos" onclick="filterByStatus('')">
                <div class="emp-stat-icon" style="background:linear-gradient(135deg,#1e3c72,#2a5298)">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="emp-stat-val">{{ $counts['total'] }}</div>
                    <div class="emp-stat-label">Total</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="emp-stat-card" id="filter-pendiente" onclick="filterByStatus('Pendiente')">
                <div class="emp-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="emp-stat-val" style="color:#f59e0b">{{ $counts['pendiente'] }}</div>
                    <div class="emp-stat-label">Pendientes</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="emp-stat-card" id="filter-completo" onclick="filterByStatus('Completo')">
                <div class="emp-stat-icon" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="emp-stat-val" style="color:#10b981">{{ $counts['completo'] }}</div>
                    <div class="emp-stat-label">Completos</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="emp-stat-card" id="filter-baja" onclick="filterByStatus('Dar de baja')">
                <div class="emp-stat-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                    <i class="fa-solid fa-user-minus"></i>
                </div>
                <div>
                    <div class="emp-stat-val" style="color:#ef4444">{{ $counts['dar_de_baja'] }}</div>
                    <div class="emp-stat-label">Dar de baja</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="emp-table-card">
        <div class="emp-table-toolbar">
            <div class="emp-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="emp-search-input" id="empSearch"
                    placeholder="Buscar por nombre, código PRISMA, SmartHR o tienda...">
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="emp-length-wrap">
                    <span>Mostrar</span>
                    <select class="emp-length-select" id="empLength">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>registros</span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            {!! $dataTable->table(['class' => 'table w-100', 'id' => 'employee-table']) !!}
        </div>
    </div>

</div>
@endsection

@push('page-scripts')
@vite(["resources/js/datatables.js"])
{!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var empTable;

window.addEventListener('load', function() {
    // Esperar a que DataTable esté listo
    var checkTable = setInterval(function() {
        if ($.fn.DataTable.isDataTable('#employee-table')) {
            empTable = $('#employee-table').DataTable();
            clearInterval(checkTable);

            // Buscador personalizado
            document.getElementById('empSearch').addEventListener('keyup', function() {
                empTable.search(this.value).draw();
            });

            // Selector de registros
            document.getElementById('empLength').addEventListener('change', function() {
                empTable.page.len(this.value).draw();
            });
        }
    }, 300);
});

// Filtro por estado (columna status)
function filterByStatus(status) {
    document.querySelectorAll('.emp-stat-card').forEach(c => c.classList.remove('active'));

    if (empTable) {
        empTable.column('status:name').search(status).draw();
    }

    if (!status) {
        document.getElementById('filter-todos').classList.add('active');
    } else if (status === 'Pendiente') {
        document.getElementById('filter-pendiente').classList.add('active');
    } else if (status === 'Completo') {
        document.getElementById('filter-completo').classList.add('active');
    } else if (status === 'Dar de baja') {
        document.getElementById('filter-baja').classList.add('active');
    }
}

// Sincronizar desde PRISMA
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
            const baja = parseInt(get('Empleados marcados DAR_DE_BAJA'));
            Swal.fire({
                icon: baja > 0 ? 'warning' : 'success',
                title: 'Sincronización completada',
                html: (() => {
                            let bajaHtml = '';
                            if (data.baja_employees && data.baja_employees.length > 0) {
                                bajaHtml = `
                                <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;margin-top:10px">
                                    <div style="font-weight:700;color:#dc2626;margin-bottom:6px">
                                        <i class="fa-solid fa-user-minus me-1"></i>
                                        ${data.baja_employees.length} empleado(s) marcados DAR DE BAJA:
                                    </div>
                                    <ul style="margin:0;padding-left:18px;font-size:13px;color:#991b1b">
                                        ${data.baja_employees.map(n => `<li>${n}</li>`).join('')}
                                    </ul>
                                </div>`;
                            }
                            return `
                            <div style="text-align:left;font-size:14px;line-height:2.2">
                                <div><i class="fa-solid fa-store me-2 text-primary"></i><b>Tiendas sincronizadas:</b> 83</div>
                                <hr style="margin:8px 0">
                                <div><i class="fa-solid fa-user-plus me-2 text-success"></i><b>Empleados nuevos:</b> ${get('Empleados nuevos creados')}</div>
                                <div><i class="fa-solid fa-rotate me-2 text-info"></i><b>Actualizados:</b> ${get('Empleados existentes actualizados')}</div>
                                ${baja > 0 ? `<div style="color:#ef4444"><i class="fa-solid fa-user-minus me-2"></i><b>Marcados DAR DE BAJA:</b> ${baja} — Requieren liquidación</div>` : ''}
                                <hr style="margin:8px 0">
                                <div><i class="fa-solid fa-circle-check me-2 text-success"></i><b>Con datos completos:</b> ${get('Con datos completos')}</div>
                                <div><i class="fa-solid fa-clock me-2 text-warning"></i><b>Pendientes RH:</b> ${get('Sin datos \\(pendiente RH\\)')}</div>
                                ${bajaHtml}
                            </div>`;
                        })(),
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#1e3c72',
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error en sincronización', text: data.message });
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