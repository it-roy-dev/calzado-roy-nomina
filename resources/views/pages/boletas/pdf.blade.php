<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; padding: 16px; }
        .recibo-wrapper {
            border: 1.5px solid #000;
            padding: 18px 20px;
            max-width: 380px;
            margin: 0 auto;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .header img { width: 100px; margin-bottom: 6px; display: block; margin-left: auto; margin-right: auto; }
        .empresa { font-size: 11px; font-weight: bold; text-transform: uppercase; text-align: center; }
        .titulo { font-size: 12px; font-weight: bold; text-transform: uppercase; text-align: center; margin: 3px 0; }
        .divider { border: none; border-top: 1px dashed #000; margin: 7px 0; }
        .fecha { font-size: 10px; margin-bottom: 2px; }
        .ubicacion { font-weight: bold; font-size: 11px; text-align: center; margin: 3px 0; }
        .yo-line { margin: 3px 0; font-size: 10px; line-height: 1.5; }
        .yo-line strong { text-transform: uppercase; }
        .cantidad { margin: 3px 0; font-size: 10px; line-height: 1.5; }
        .section-title { font-weight: bold; text-align: center; font-size: 10px; margin: 6px 0 3px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 2px 2px; font-size: 10px; line-height: 1.4; }
        table td.right { text-align: right; white-space: nowrap; }
        table td.label { padding-right: 6px; }
        .total-row td { font-weight: bold; border-top: 1px dashed #000; padding-top: 3px; }
        .liquido-row td { font-size: 12px; font-weight: bold; border-top: 1px solid #000; padding-top: 4px; }
        .firma-section { margin-top: 24px; text-align: center; }
        .firma-img { max-height: 50px; margin-bottom: 3px; }
        .firma-line { border-top: 1px solid #000; width: 200px; margin: 4px auto 3px; }
        .firma-label { font-size: 10px; font-weight: bold; text-align: center; line-height: 1.8; }
        .correlativo { text-align: right; font-size: 10px; font-weight: bold; margin-top: 10px; text-transform: uppercase; }
        .pendiente-msg { text-align: center; color: #cc0000; font-size: 11px; margin: 10px 0; font-weight: bold; }
        .corte { border: none; border-top: 1px dashed #000; margin-top: 14px; }
    </style>
</head>
<body>
<div class="recibo-wrapper">

@php
    $emp      = $empleado;
    $user     = $emp->user;
    $ubicacion = $emp->store->name ?? ($emp->department->name ?? 'ADMIN');
    $esPrimera = $boleta->tipo === 'PRIMERA_QUINCENA';
    $mesNombre = $meses[$nomina->mes];
    $isPendiente = str_contains($detalle->observacion ?? '', 'PENDIENTE');

    // Monto en letras
    $liquido = floatval($detalle->liquido_a_recibir);
    $entero  = intval($liquido);
    $centavos = round(($liquido - $entero) * 100);

    $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
    $enLetras  = strtoupper($formatter->format($entero)) . ' CON ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';
@endphp

{{-- HEADER --}}
<div class="header">
    <img src="{{ $logoSrc }}" alt="Roy">
    <div class="empresa">INTERNACIONAL DE CALZADO, S.A.</div>
    <div class="titulo">
        {{ $esPrimera ? 'CONSTANCIA DE SALARIOS' : 'CONSTANCIA PAGO DE SALARIOS' }}
    </div>
</div>

<div class="divider"></div>

{{-- FECHA Y TIENDA --}}
<div class="info-row">
    <span>GUATEMALA, {{ $nomina->fecha_pago->format('d/m/Y') }}</span>
</div>
<div style="text-align:center;font-weight:bold;font-size:11px;margin:3px 0">
    {{ $ubicacion }}
</div>

<div class="divider"></div>

@if($isPendiente)
<div class="pendiente-msg">⚠ EXPEDIENTE INCOMPLETO — SIN DATOS DE NÓMINA</div>
@else

{{-- YO LÍNEA --}}
<div class="yo-line">
    YO: <strong>{{ $user->fullname ?? '—' }}</strong>
</div>
<div class="yo-line">
    RECIBI DE: <strong>INTERNACIONAL DE CALZADO, S.A.</strong>
</div>

{{-- MONTO --}}
<div class="cantidad">
    LA CANTIDAD DE: <span class="monto">(Q{{ number_format($liquido, 2) }})</span>
</div>
<div class="cantidad">
    {{ $enLetras }}
</div>

{{-- CUENTA --}}
@if($detalle->cuenta_banco)
<div class="cantidad">
    ACREDITADO A CUENTA No. <strong>{{ $detalle->cuenta_banco }}</strong>
</div>
@endif

{{-- CONCEPTO --}}
<div class="cantidad" style="margin-top:4px">
    CONCEPTO DE PAGO:<br>
    NOMINA No. {{ $nomina->numero_planilla }}<br>
    @if($esPrimera)
        ANTICIPO SOBRE SUELDOS Y SALARIOS<br>
    @else
        DEL {{ $nomina->fecha_inicio_periodo->format('d/m/Y') }} AL {{ $nomina->fecha_fin_periodo->format('d/m/Y') }}<br>
    @endif
    MES DE {{ $mesNombre }} {{ $nomina->anio }}
</div>

<div class="divider"></div>

{{-- INGRESOS --}}
<div class="section-title">*INGRESOS*</div>

@if($esPrimera)
<table>
    <tr>
        <td class="label">ANTICIPO SOBRE SUELDOS Y SALARIOS</td>
        <td class="right">{{ number_format($detalle->anticipo_40, 2) }}</td>
    </tr>
    <tr class="total-row">
        <td class="label">TOTAL DEVENGADO</td>
        <td class="right">{{ number_format($detalle->anticipo_40, 2) }}</td>
    </tr>
</table>
@else
<table>
    <tr>
        <td class="label">DIAS TRABAJADOS</td>
        <td class="right">{{ $detalle->dias_trabajados }}</td>
    </tr>
    <tr>
        <td class="label">SUELDO ORDINARIO</td>
        <td class="right">{{ number_format($detalle->salario_devengado, 2) }}</td>
    </tr>
    <tr>
        <td class="label">SUELDO EXTRA ORDINARIO</td>
        <td class="right">{{ number_format($detalle->salario_extra_ordinario, 2) }}</td>
    </tr>
    <tr>
        <td class="label">BONIFICACION DECRETO (78-89 REF 37-2001)</td>
        <td class="right">{{ number_format($detalle->bonificacion_decreto, 2) }}</td>
    </tr>
    @if($detalle->bono_variable > 0)
    <tr>
        <td class="label">BONO VARIABLE</td>
        <td class="right">{{ number_format($detalle->bono_variable, 2) }}</td>
    </tr>
    @endif
    <tr class="total-row">
        <td class="label">TOTAL DEVENGADO</td>
        <td class="right">{{ number_format($detalle->total_devengado, 2) }}</td>
    </tr>
</table>

<div class="divider"></div>

{{-- DEDUCCIONES --}}
<div class="section-title">*DEDUCCIONES*</div>
<table>
    <tr>
        <td class="label">- IGSS 4.83%</td>
        <td class="right">{{ number_format($detalle->igss, 2) }}</td>
    </tr>
    <tr>
        <td class="label">- ISR EMPLEADOS</td>
        <td class="right">{{ number_format($detalle->isr, 2) }}</td>
    </tr>
    @if($detalle->descuento_judicial > 0)
    <tr>
        <td class="label">- EMBARGO JUDICIAL</td>
        <td class="right">{{ number_format($detalle->descuento_judicial, 2) }}</td>
    </tr>
    @endif
    @if($detalle->anticipo_40 > 0)
    <tr>
        <td class="label">- ANTICIPO SOBRE SUELDOS Y SALARIOS</td>
        <td class="right">{{ number_format($detalle->anticipo_40, 2) }}</td>
    </tr>
    @endif
    @if($detalle->prestamos > 0)
    <tr>
        <td class="label">- PRESTAMOS</td>
        <td class="right">{{ number_format($detalle->prestamos, 2) }}</td>
    </tr>
    @endif
    @if($detalle->ayuvi > 0)
    <tr>
        <td class="label">- AYUVI</td>
        <td class="right">{{ number_format($detalle->ayuvi, 2) }}</td>
    </tr>
    @endif
    @if($detalle->faltante_inventario > 0)
    <tr>
        <td class="label">- FALTANTE INVENTARIO</td>
        <td class="right">{{ number_format($detalle->faltante_inventario, 2) }}</td>
    </tr>
    @endif
    @if($detalle->uniforme > 0)
    <tr>
        <td class="label">- UNIFORME</td>
        <td class="right">{{ number_format($detalle->uniforme, 2) }}</td>
    </tr>
    @endif
    @if($detalle->cxc > 0)
    <tr>
        <td class="label">- CXC</td>
        <td class="right">{{ number_format($detalle->cxc, 2) }}</td>
    </tr>
    @endif
    <tr class="total-row">
        <td class="label">TOTAL DEDUCCIONES</td>
        <td class="right">Q {{ number_format($detalle->total_deducciones, 2) }}</td>
    </tr>
</table>
@endif

{{-- LIQUIDO --}}
<div class="divider"></div>
<table>
    <tr>
        <td style="font-size:12px;font-weight:bold">LIQUIDO A RECIBIR</td>
        <td class="right" style="font-size:12px;font-weight:bold">Q{{ number_format($liquido, 2) }}</td>
    </tr>
</table>

{{-- FIRMA --}}
<div class="firma-section">
    @if($firma && $boleta->estado === 'FIRMADA')
        <img src="{{ $firma->firma_svg }}" class="firma-img" alt="Firma">
    @else
        <div style="height:50px"></div>
    @endif
    <div class="firma-line"></div>
    <div>RECIBI CONFORME</div>
    <div>CODIGO: {{ $emp->oracle_emp_code ?? '—' }}</div>
</div>

@endif

{{-- CORRELATIVO --}}
<div class="correlativo">
    CORRELATIVO {{ $boleta->numero_correlativo ?? $boleta->id }}
</div>

<hr class="corte">
</div>
</body>