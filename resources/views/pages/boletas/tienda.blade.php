@extends('layouts.app')

@push('page-styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; }
.bol-hero {
    background: linear-gradient(135deg, #0f2456 0%, #1e3c72 45%, #2a5298 100%);
    border-radius: 18px; padding: 0; margin-bottom: 22px;
    box-shadow: 0 12px 40px rgba(15,36,86,0.35); overflow: hidden;
}
.bol-hero-inner { padding: 24px 32px; }
.bol-card {
    background: #fff; border-radius: 14px; border: 1px solid #eef2f7;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 16px;
    overflow: hidden; transition: box-shadow 0.2s;
}
.bol-card:hover { box-shadow: 0 4px 16px rgba(30,60,114,0.1); }
.bol-card-header {
    padding: 14px 20px; display: flex; align-items: center;
    justify-content: space-between; border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(to right, #f8faff, #fff);
}
.badge-firmada { background:#d1fae5; color:#065f46; padding:5px 12px; border-radius:8px; font-size:12px; font-weight:700; }
.badge-pendiente { background:#fef3c7; color:#92400e; padding:5px 12px; border-radius:8px; font-size:12px; font-weight:700; }
.firma-pad { border: 2px dashed #e2e8f0; border-radius: 10px; cursor: crosshair; background: #f8fafc; }
.firma-pad:hover { border-color: #1e3c72; }
</style>
@endpush

@section('page-content')
<div class="content container-fluid">

    <x-breadcrumb>
        <x-slot name="title">Recibos de Pago</x-slot>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Recibos — {{ $storeUser->store->name ?? 'Mi Tienda' }}</li>
        </ul>
    </x-breadcrumb>

    {{-- Hero --}}
    <div class="bol-hero">
        <div class="bol-hero-inner">
            <div style="font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.5);margin-bottom:6px">
                Recibos de Pago
            </div>
            <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:6px">
                {{ $storeUser->store->name ?? 'Mi Tienda' }}
            </div>
            <div style="font-size:13px;color:rgba(255,255,255,0.65)">
                {{ $meses[$mes] }} {{ $anio }} —
                {{ $boletas->where('estado','FIRMADA')->count() }} de {{ $boletas->count() }} firmados
            </div>
        </div>
    </div>

    {{-- Filtro mes/año --}}
    <div style="background:#fff;border-radius:14px;border:1px solid #eef2f7;box-shadow:0 2px 8px rgba(0,0,0,0.05);padding:16px 20px;margin-bottom:20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label style="font-size:12px;font-weight:600;color:#64748b">Mes</label>
                <select name="mes" class="form-select form-select-sm">
                    @foreach($meses as $num => $nombre)
                        <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label style="font-size:12px;font-weight:600;color:#64748b">Año</label>
                <select name="anio" class="form-select form-select-sm">
                    @foreach([2024,2025,2026,2027] as $y)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    {{-- Lista de boletas --}}
    @forelse($boletas as $boleta)
    @php
        $emp  = $boleta->empleado;
        $user = $emp->user ?? null;
    @endphp
    <div class="bol-card">
        <div class="bol-card-header">
            <div class="d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#1e3c72,#2a5298);display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px;flex-shrink:0">
                    <i class="fa-solid fa-user fa-xs"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:14px;color:#0f2456">{{ $user->fullname ?? '—' }}</div>
                    <div style="font-size:12px;color:#64748b">
                        {{ $emp->designation->name ?? '—' }} —
                        {{ $boleta->tipo === 'PRIMERA_QUINCENA' ? '1ra Quincena' : '2da Quincena' }}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($boleta->estado === 'FIRMADA')
                    <span class="badge-firmada"><i class="fa-solid fa-check me-1"></i>Firmado</span>
                @else
                    <span class="badge-pendiente"><i class="fa-solid fa-clock me-1"></i>Pendiente firma</span>
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary"
                    style="font-size:12px;border-radius:7px"
                    onclick="verBoleta({{ $boleta->id }})">
                    <i class="fa-solid fa-eye me-1"></i> Ver
                </button>
                @if($boleta->estado === 'PENDIENTE')
                <button type="button" class="btn btn-sm btn-success"
                    style="font-size:12px;border-radius:7px"
                    onclick="abrirFirma({{ $boleta->id }}, '{{ $user->fullname ?? '' }}')">
                    <i class="fa-solid fa-signature me-1"></i> Firmar
                </button>
                @endif
            </div>
        </div>
        @if($boleta->estado === 'FIRMADA')
        <div style="padding:10px 20px;font-size:12px;color:#065f46;background:#f0fdf4">
            <i class="fa-solid fa-circle-check me-1"></i>
            Firmado el {{ $boleta->firmada_at?->format('d/m/Y H:i') }}
        </div>
        @endif
    </div>
    @empty
    <div style="text-align:center;padding:60px;color:#94a3b8;background:#fff;border-radius:14px;border:1px solid #eef2f7">
        <i class="fa-solid fa-file-invoice" style="font-size:40px;opacity:0.2;display:block;margin-bottom:12px"></i>
        <p style="font-size:14px;font-weight:500">No hay recibos disponibles para este período</p>
    </div>
    @endforelse

</div>

{{-- Modal ver boleta --}}
<div class="modal fade" id="modalVerBoleta" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#0f2456,#2a5298);color:#fff;border:none">
                <h5 class="modal-title" style="font-weight:700;font-size:15px">
                    <i class="fa-solid fa-file-invoice me-2"></i> Recibo de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0;height:75vh">
                <iframe id="iframeBoleta" src="" style="width:100%;height:100%;border:none"></iframe>
            </div>
        </div>
    </div>
</div>

{{-- Modal firma --}}
<div class="modal fade" id="modalFirma" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px">
        <div class="modal-content" style="border-radius:14px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#0f2456,#2a5298);color:#fff;border:none">
                <h5 class="modal-title" style="font-weight:700;font-size:15px">
                    <i class="fa-solid fa-signature me-2"></i> Firmar Recibo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px">
                <p style="font-size:13px;color:#64748b;margin-bottom:16px" id="nombreFirmante"></p>

                <div id="seccionFirmaExistente" style="display:none">
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;margin-bottom:16px">
                        <p style="font-size:13px;color:#065f46;margin:0;font-weight:600">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Firma registrada — se usará para firmar este documento
                        </p>
                    </div>
                    <div style="text-align:center;margin-bottom:16px">
                        <img id="firmaPreview" src="" style="max-height:80px;border:1px solid #e2e8f0;border-radius:8px;padding:8px">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success flex-fill" onclick="confirmarFirma()">
                            <i class="fa-solid fa-check me-1"></i> Firmar Documento
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="mostrarPad()">
                            <i class="fa-solid fa-rotate me-1"></i> Cambiar firma
                        </button>
                    </div>
                </div>

                <div id="seccionNuevaFirma">
                    <p style="font-size:12px;color:#64748b;margin-bottom:8px">Dibuja tu firma en el recuadro:</p>
                    <canvas id="firmaPad" class="firma-pad" width="440" height="150"
                        style="width:100%;height:150px"></canvas>
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFirma()">
                            <i class="fa-solid fa-eraser me-1"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-primary flex-fill" onclick="guardarYFirmar()">
                            <i class="fa-solid fa-signature me-1"></i> Guardar firma y firmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let boletaActualId = null;
let firmando = false;
let canvas, ctx, dibujando = false;
let ultimaX, ultimaY;

function verBoleta(id) {
    document.getElementById('iframeBoleta').src = '/boletas/' + id + '?t=' + Date.now();
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalVerBoleta'));
    modal.show();
}

function abrirFirma(id, nombre) {
    boletaActualId = id;
    document.getElementById('nombreFirmante').textContent = 'Firmando recibo de: ' + nombre;

    fetch('/boletas/firma/verificar', {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.tiene_firma) {
            document.getElementById('firmaPreview').src = data.firma_svg;
            document.getElementById('seccionFirmaExistente').style.display = 'block';
            document.getElementById('seccionNuevaFirma').style.display = 'none';
        } else {
            mostrarPad();
        }
    });

    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalFirma'));
    modal.show();
    setTimeout(iniciarPad, 300);
}

function mostrarPad() {
    document.getElementById('seccionFirmaExistente').style.display = 'none';
    document.getElementById('seccionNuevaFirma').style.display = 'block';
    setTimeout(iniciarPad, 100);
}

function iniciarPad() {
    canvas = document.getElementById('firmaPad');
    ctx    = canvas.getContext('2d');
    canvas.width  = canvas.offsetWidth;
    canvas.height = 150;
    ctx.strokeStyle = '#0f2456';
    ctx.lineWidth   = 2.5;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';

    canvas.onmousedown  = (e) => { dibujando = true; [ultimaX, ultimaY] = getPos(e); };
    canvas.onmousemove  = (e) => { if (!dibujando) return; dibujar(e); };
    canvas.onmouseup    = () => dibujando = false;
    canvas.onmouseleave = () => dibujando = false;

    canvas.ontouchstart = (e) => { e.preventDefault(); dibujando = true; [ultimaX, ultimaY] = getPos(e.touches[0]); };
    canvas.ontouchmove  = (e) => { e.preventDefault(); if (!dibujando) return; dibujar(e.touches[0]); };
    canvas.ontouchend   = () => dibujando = false;
}

function getPos(e) {
    const r = canvas.getBoundingClientRect();
    return [e.clientX - r.left, e.clientY - r.top];
}

function dibujar(e) {
    const [x, y] = getPos(e);
    ctx.beginPath();
    ctx.moveTo(ultimaX, ultimaY);
    ctx.lineTo(x, y);
    ctx.stroke();
    [ultimaX, ultimaY] = [x, y];
}

function limpiarFirma() {
    if (canvas) ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function guardarYFirmar() {
    if (!canvas) return;
    const firmaData = canvas.toDataURL('image/png');

    fetch('/boletas/firma/registrar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ firma_svg: firmaData })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) confirmarFirma();
    });
}

function confirmarFirma() {
    if (firmando) return;
    firmando = true;

    fetch('/boletas/' + boletaActualId + '/firmar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        firmando = false;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalFirma')).hide();
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Documento firmado!',
                text: data.message,
                confirmButtonColor: '#1e3c72',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    });
}
</script>
@endpush