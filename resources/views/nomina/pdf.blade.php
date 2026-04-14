<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    * { font-family: Arial, sans-serif; font-size: 7px; margin: 0; padding: 0; }
    body { padding: 10px; }
    .header { text-align: center; margin-bottom: 8px; }
    .header h2 { font-size: 11px; font-weight: bold; margin-bottom: 2px; }
    .header h3 { font-size: 10px; font-weight: bold; margin-bottom: 2px; }
    .header h4 { font-size: 9px; margin-bottom: 6px; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #1e3c72; color: #fff;
        padding: 3px 2px; text-align: center;
        font-size: 6.5px; font-weight: bold;
        border: 0.5px solid #1e3c72;
    }
    tbody td {
        padding: 2px 2px; border: 0.5px solid #e2e8f0;
        text-align: center; font-size: 6.5px;
    }
    tbody td.left { text-align: left; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr.pendiente { background: #fee2e2; }
    tfoot td {
        background: #1e3c72; color: #fff;
        padding: 3px 2px; font-weight: bold;
        font-size: 7px; border: 0.5px solid #1e3c72;
    }
    .liquido { color: #10b981; font-weight: bold; }
    .logo { width: 60px; }
</style>
</head>
<body>
@php
    $logoPath = public_path('images/logo3.png');
    $logoBase64 = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,' . $logoBase64;
@endphp
<div class="header">
    <table style="width:100%;margin-bottom:8px;border:none">
        <tr>
            <td style="width:130px;vertical-align:middle;border:none">
                <img src="{{ $logoSrc }}" style="width:120px;height:auto" alt="Roy">
            </td>
            <td style="vertical-align:middle;text-align:center;border:none">
                <div style="font-size:12px;font-weight:bold">INTERNACIONAL DE CALZADO, S.A</div>
                <div style="font-size:11px;font-weight:bold">
                    NOMINA {{ $nomina->tipo === 'PRIMERA_QUINCENA' ? 'PRIMERA QUINCENA' : 'SEGUNDA QUINCENA' }}
                    {{ strtoupper($nomina->nombre_mes) }} {{ $nomina->anio }}
                </div>
                <div style="font-size:10px">SEGÚN PLANILLA # {{ $nomina->numero_planilla }}</div>
            </td>
            <td style="width:130px;border:none"></td>
        </tr>
    </table>
</div>

<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>CÓD. PRISMA</th>
            <th>CÓD. SmartHR</th>
            <th>NOMBRES Y APELLIDOS</th>
            <th>TIENDA</th>
            <th>PUESTO</th>
            <th>DÍAS</th>
            <th>OBS.</th>
            <th>SAL.BASE</th>
            <th>SALARIO</th>
            <th>H.EXTRA</th>
            <th>BONIF.</th>
            <th>BONO VAR.</th>
            <th>TOT.BONIF.</th>
            <th>TOT.DEVEN.</th>
            <th>IGSS</th>
            <th>ISR</th>
            @if($nomina->tipo === 'SEGUNDA_QUINCENA')
            <th>ANTICIPO 40%</th>
            @endif
            <th>PRÉSTAMOS</th>
            <th>AYUVI</th>
            <th>DESC.JUD.</th>
            <th>FALT.INV.</th>
            <th>UNIFORME</th>
            <th>CXC</th>
            <th>TOT.OTROS</th>
            <th>TOT.DEDUC.</th>
            <th>LÍQUIDO</th>
            <th>CT.BAC</th>
            <th>MONTO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detalle as $i => $row)
        @php
            $emp  = $row->empleado;
            $user = $emp->user ?? null;
            $pendiente = str_contains($row->observacion ?? '', 'PENDIENTE');
        @endphp
        <tr class="{{ $pendiente ? 'pendiente' : '' }}">
            <td>{{ $i + 1 }}</td>
            <td>{{ $emp->oracle_emp_code ?? '—' }}</td>
            <td>{{ $emp->emp_code ?? '—' }}</td>
            <td class="left">{{ $user->fullname ?? '—' }}</td>
            <td>{{ $emp->store->oracle_store_no ?? ($emp->department->name ?? '—') }}</td>
            <td class="left">{{ $emp->designation->name ?? '—' }}</td>
            <td>{{ $row->dias_trabajados }}</td>
            <td class="left">{{ $pendiente ? 'Sin expediente' : ($row->observacion ?? '') }}</td>
            <td>{{ $row->salario_base > 0 ? number_format($row->salario_base, 2) : '—' }}</td>
            <td>{{ number_format($row->salario_devengado, 2) }}</td>
            <td>{{ $row->salario_extra_ordinario > 0 ? number_format($row->salario_extra_ordinario, 2) : '—' }}</td>
            <td>{{ number_format($row->bonificacion_decreto, 2) }}</td>
            <td>{{ $row->bono_variable > 0 ? number_format($row->bono_variable, 2) : '—' }}</td>
            <td>{{ number_format($row->total_bonificaciones, 2) }}</td>
            <td><strong>{{ number_format($row->total_devengado, 2) }}</strong></td>
            <td>{{ number_format($row->igss, 2) }}</td>
            <td>{{ $row->isr > 0 ? number_format($row->isr, 2) : '—' }}</td>
            @if($nomina->tipo === 'SEGUNDA_QUINCENA')
            <td>{{ $row->anticipo_40 > 0 ? number_format($row->anticipo_40, 2) : '—' }}</td>
            @endif
            <td>{{ $row->prestamos > 0 ? number_format($row->prestamos, 2) : '—' }}</td>
            <td>{{ $row->ayuvi > 0 ? number_format($row->ayuvi, 2) : '—' }}</td>
            <td>{{ $row->descuento_judicial > 0 ? number_format($row->descuento_judicial, 2) : '—' }}</td>
            <td>{{ $row->faltante_inventario > 0 ? number_format($row->faltante_inventario, 2) : '—' }}</td>
            <td>{{ $row->uniforme > 0 ? number_format($row->uniforme, 2) : '—' }}</td>
            <td>{{ $row->cxc > 0 ? number_format($row->cxc, 2) : '—' }}</td>
            <td>{{ $row->total_otros_descuentos > 0 ? number_format($row->total_otros_descuentos, 2) : '—' }}</td>
            <td><strong style="color:#ef4444">{{ number_format($row->total_deducciones, 2) }}</strong></td>
            <td class="liquido">{{ number_format($row->liquido_a_recibir, 2) }}</td>
            <td>{{ $row->cuenta_banco ?? '—' }}</td>
            <td class="liquido"><strong>{{ number_format($row->liquido_a_recibir, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $nomina->tipo === 'SEGUNDA_QUINCENA' ? 14 : 13 }}">
                TOTAL — {{ $nomina->total_empleados }} EMPLEADOS
            </td>
            <td>GTQ {{ number_format($nomina->total_devengado, 2) }}</td>
            <td colspan="{{ $nomina->tipo === 'SEGUNDA_QUINCENA' ? 9 : 8 }}"></td>
            <td>GTQ {{ number_format($nomina->total_deducciones, 2) }}</td>
            <td>GTQ {{ number_format($nomina->total_liquido, 2) }}</td>
            <td></td>
            <td>GTQ {{ number_format($nomina->total_liquido, 2) }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>