<?php

namespace App\DataTables;

use App\Models\User;
use App\Enums\UserType;
use Spatie\Menu\Laravel\Html;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class EmployeeDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('fullname', function ($row) {
                $img  = !empty($row->avatar) ? asset('storage/users/' . $row->avatar) : asset('images/user.jpg');
                $link = route('employees.show', ['employee' => Crypt::encrypt($row->id)]);
                return Html::userAvatar($row->fullname, $img, $link);
            })
            ->addColumn('emp_code', function ($row) {
                $code = $row->employeeDetail->emp_code ?? null;
                if ($code) {
                    $color = str_starts_with($code, 'A-') ? 'primary' : 'dark';
                    return '<span class="badge bg-' . $color . '">' . $code . '</span>';
                }
                return '<span class="badge bg-secondary">Sin código</span>';
            })
            ->addColumn('ubicacion', function ($row) {
                $detail = $row->employeeDetail;
                if (!$detail) return '—';
                if (!empty($detail->department_id) && $detail->department) {
                    return '<i class="fa-solid fa-building fa-xs"></i> ' . $detail->department->name;
                }
                if (!empty($detail->store_id) && $detail->store) {
                    return '<i class="fa-solid fa-store fa-xs"></i> ' . $detail->store->name;
                }
                return '—';
            })
            ->addColumn('designation', function ($row) {
                return $row->employeeDetail->designation->name ?? '—';
            })
            ->addColumn('status', function ($row) {
                $status = $row->employeeDetail->status ?? 'PENDIENTE';
                $map = [
                    'PENDIENTE'   => ['label' => 'Pendiente',   'color' => 'warning'],
                    'COMPLETO'    => ['label' => 'Completo',    'color' => 'success'],
                    'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'color' => 'danger'],
                    'INACTIVO'    => ['label' => 'Inactivo',    'color' => 'secondary'],
                ];
                $s = $map[$status] ?? $map['PENDIENTE'];
                return '<span class="badge bg-' . $s['color'] . '">' . $s['label'] . '</span>';
            })
            ->editColumn('phone', function ($row) {
                return $row->phoneNumber;
            })
            ->editColumn('created_at', function ($row) {
                return !empty($row->created_at) ? format_date($row->created_at) : '—';
            })
                ->addColumn('action', function ($row) {
                    $id     = $row->id;
                    $status = $row->employeeDetail->status ?? 'PENDIENTE';
                    $expLabel = $status === 'COMPLETO' ? 'Editar Expediente' : 'Completar Expediente';
                    $expUrl = route('employees.expediente', \Illuminate\Support\Facades\Crypt::encrypt($row->id));
                    return view('pages.employees.action', compact('id', 'expUrl', 'expLabel'));
                })
            ->rawColumns(['fullname', 'action', 'emp_code', 'ubicacion', 'status']);
    }

    public function query(): QueryBuilder
    {
        return User::where('type', '=', UserType::EMPLOYEE)
            ->with(['employeeDetail.designation', 'employeeDetail.store', 'employeeDetail.department'])
            ->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('employee-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('emp_code')->title('Código')->searchable(),
            Column::make('fullname')->title('Nombre')->searchable(),
            Column::make('ubicacion')->title('Tienda / Depto')->searchable(false),
            Column::make('designation')->title('Puesto')->searchable(false),
            Column::make('username')->title('Usuario')->searchable(),
            Column::make('status')->title('Estado')->searchable(false),
            Column::make('created_at')->title('Creado')->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-end'),
        ];
    }

    protected function filename(): string
    {
        return 'Empleados_' . date('YmdHis');
    }
}