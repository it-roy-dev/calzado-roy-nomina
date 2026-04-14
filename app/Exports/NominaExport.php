<?php

namespace App\Exports;

use App\Models\Nomina;
use App\Models\NominaDetalle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NominaExport implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    protected Nomina $nomina;
    protected $detalle;

    public function __construct(Nomina $nomina)
    {
        $this->nomina  = $nomina;
        $this->detalle = NominaDetalle::where('nomina_id', $nomina->id)
            ->with(['empleado.user', 'empleado.store', 'empleado.department', 'empleado.designation'])
            ->join('employee_details', 'nomina_detalle.employee_detail_id', '=', 'employee_details.id')
            ->leftJoin('stores', 'employee_details.store_id', '=', 'stores.id')
            ->orderByRaw("COALESCE(stores.oracle_store_no::text, '9999')")
            ->orderBy('nomina_detalle.id')
            ->select('nomina_detalle.*')
            ->get();
    }

    public function array(): array
    {
        $meses = [
            1=>'ENERO', 2=>'FEBRERO', 3=>'MARZO', 4=>'ABRIL',
            5=>'MAYO', 6=>'JUNIO', 7=>'JULIO', 8=>'AGOSTO',
            9=>'SEPTIEMBRE', 10=>'OCTUBRE', 11=>'NOVIEMBRE', 12=>'DICIEMBRE'
        ];
        $tipo = $this->nomina->tipo === 'PRIMERA_QUINCENA' ? 'PRIMERA QUINCENA' : 'SEGUNDA QUINCENA';

        $rows = [];

        // Filas de encabezado
        $rows[] = ['INTERNACIONAL DE CALZADO, S.A'];
        $rows[] = ["NOMINA {$tipo} {$meses[$this->nomina->mes]} {$this->nomina->anio}"];
        $rows[] = ["SEGÚN PLANILLA # {$this->nomina->numero_planilla}"];
        $rows[] = []; // fila vacía

        // Headers de columnas
        $headers = [
            'No.', 'SUP', 'TIENDA', 'PUESTO', 'CODIGO',
            'NOMBRES Y APELLIDOS', 'DIAS', 'OBSERVACION',
            'SAL.BASE', 'SALARIO', 'SALARIO EXTRA ORDINARIO',
            'BONIFICA', 'BONO VARI',
            'TOTAL BONIFICACIÓN DECTO. 78-89 Y REF. 37-2001',
            'TOT DEVEN', 'IGSS',
        ];

        if ($this->nomina->tipo === 'SEGUNDA_QUINCENA') {
            $headers[] = 'ANTICIPO DEL 40%';
        }

        $headers = array_merge($headers, [
            'PRESTAMOS', 'AYUVI', 'ISR', 'DESCTO JUDICIAL',
            'FALTANTE INV.', 'UNIFORME', 'CXC',
            'TOTAL OTROS', 'TOTAL DEDUCCIONES', 'LIQUIDO A RECIBIR',
            '', 'COD', 'REF', 'CT. BAC', 'NOMBRE', 'MONTO A PAGAR'
        ]);

        $rows[] = $headers;

        // Filas de empleados
        $i = 1;
        foreach ($this->detalle as $row) {
            $emp  = $row->empleado;
            $user = $emp->user ?? null;
            $tienda = $emp->store->oracle_store_no ?? ($emp->department->name ?? '');
            $sup    = $emp->store->supervisor ?? '';
            $puesto = $emp->designation->name ?? '';

            $fila = [
                $i++,
                $sup,
                $tienda,
                $puesto,
                $emp->oracle_emp_code ?? '',
                $user->fullname ?? '',
                $row->dias_trabajados,
                str_contains($row->observacion ?? '', 'PENDIENTE') ? 'PENDIENTE - Sin expediente' : ($row->observacion ?? ''),
                $row->salario_base > 0 ? $row->salario_base : 0,
                $row->salario_devengado,
                $row->salario_extra_ordinario,
                $row->bonificacion_decreto,
                $row->bono_variable,
                $row->total_bonificaciones,
                $row->total_devengado,
                $row->igss,
            ];

            if ($this->nomina->tipo === 'SEGUNDA_QUINCENA') {
                $fila[] = $row->anticipo_40;
            }

            $fila = array_merge($fila, [
                $row->prestamos,
                $row->ayuvi,
                $row->isr,
                $row->descuento_judicial,
                $row->faltante_inventario,
                $row->uniforme,
                $row->cxc,
                $row->total_otros_descuentos,
                $row->total_deducciones,
                $row->liquido_a_recibir,
                '',
                $emp->oracle_emp_code ?? '',
                $row->referencia_banco ?? '',
                $row->cuenta_banco ?? '',
                $user->fullname ?? '',
                $row->liquido_a_recibir,
            ]);

            $rows[] = $fila;
        }

        // Fila de totales
        $totalFila = array_fill(0, count($headers), '');
        $totalFila[0] = $this->detalle->count();
        $totalFila[5] = 'TOTAL';
        $totalFila[14] = $this->nomina->total_devengado;
        $totalFila[count($headers) - 7] = $this->nomina->total_deducciones;
        $totalFila[count($headers) - 6] = $this->nomina->total_liquido;
        $rows[] = $totalFila;

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,  'B' => 8,  'C' => 8,  'D' => 8,  'E' => 8,
            'F' => 32, 'G' => 6,  'H' => 20, 'I' => 10, 'J' => 10,
            'K' => 10, 'L' => 10, 'M' => 10, 'N' => 14, 'O' => 10,
            'P' => 10, 'Q' => 12, 'R' => 10, 'S' => 8,  'T' => 10,
            'U' => 10, 'V' => 10, 'W' => 8,  'X' => 8,  'Y' => 12,
            'Z' => 14, 'AA'=> 14, 'AB'=> 6,  'AC'=> 8,  'AD'=> 12,
            'AE'=> 12, 'AF'=> 28, 'AG'=> 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->detalle) + 6;

        // Título
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e3c72'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            ],
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'f1f5f9'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Planilla #' . $this->nomina->numero_planilla;
    }
}