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
            ->addColumn('oracle_emp_code', function ($row) {
                $code = $row->oracle_emp_code ?? null;
                return $code
                    ? '<span style="font-size:12px;color:#64748b;font-weight:600">' . $code . '</span>'
                    : '<span style="color:#cbd5e1">—</span>';
            })
            ->addColumn('emp_code', function ($row) {
                $code = $row->smarthr_code ?? null;
                if ($code) {
                    $color = str_starts_with($code, 'A-') ? '#3b82f6' : '#1e293b';
                    return '<span class="badge" style="background:' . $color . ';color:#fff">' . $code . '</span>';
                }
                return '<span class="badge bg-secondary">Sin código</span>';
            })
            ->addColumn('fullname', function ($row) {
                $img  = !empty($row->avatar) ? asset('storage/users/' . $row->avatar) : asset('images/user.jpg');
                $link = route('employees.show', ['employee' => Crypt::encrypt($row->id)]);
                return Html::userAvatar($row->fullname, $img, $link);
            })
            ->addColumn('ubicacion', function ($row) {
                if (!empty($row->store_name)) {
                    return '<i class="fa-solid fa-store fa-xs me-1"></i>' . $row->store_name;
                }
                if (!empty($row->dept_name)) {
                    return '<i class="fa-solid fa-building fa-xs me-1"></i>' . $row->dept_name;
                }
                return '—';
            })
            ->addColumn('designation', function ($row) {
                return $row->desig_name ?? '—';
            })
            ->editColumn('created_at', function ($row) {
                return !empty($row->created_at) ? format_date($row->created_at) : '—';
            })
            ->addColumn('status', function ($row) {
                $status = $row->emp_status ?? 'PENDIENTE';
                $map = [
                    'PENDIENTE'   => ['label' => 'Pendiente',   'color' => 'warning'],
                    'COMPLETO'    => ['label' => 'Completo',    'color' => 'success'],
                    'DAR_DE_BAJA' => ['label' => 'Dar de baja', 'color' => 'danger'],
                    'INACTIVO'    => ['label' => 'Inactivo',    'color' => 'secondary'],
                ];
                $s = $map[$status] ?? $map['PENDIENTE'];
                return '<span class="badge bg-' . $s['color'] . '">' . $s['label'] . '</span>';
            })
            ->addColumn('action', function ($row) {
                $id        = $row->id;
                $status    = $row->emp_status ?? 'PENDIENTE';
                $expLabel  = $status === 'COMPLETO' ? 'Editar Expediente' : 'Completar Expediente';
                $expUrl    = route('employees.expediente', Crypt::encrypt($row->id));
                $darDeBaja = $status === 'DAR_DE_BAJA';
                return view('pages.employees.action', compact('id', 'expUrl', 'expLabel', 'darDeBaja'));
            })
            ->filterColumn('oracle_emp_code', function($query, $keyword) {
                $query->where('employee_details.oracle_emp_code', 'ILIKE', "%{$keyword}%");
            })
            ->filterColumn('emp_code', function($query, $keyword) {
                $query->where('employee_details.emp_code', 'ILIKE', "%{$keyword}%");
            })
            ->filterColumn('fullname', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('users.firstname', 'ILIKE', "%{$keyword}%")
                      ->orWhere('users.lastname', 'ILIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('ubicacion', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('stores.name', 'ILIKE', "%{$keyword}%")
                      ->orWhere('departments.name', 'ILIKE', "%{$keyword}%");
                });
            })
            ->filterColumn('designation', function($query, $keyword) {
                $query->where('designations.name', 'ILIKE', "%{$keyword}%");
            })
            ->filterColumn('status', function($query, $keyword) {
                $map = [
                    'Pendiente'   => 'PENDIENTE',
                    'Completo'    => 'COMPLETO',
                    'Dar de baja' => 'DAR_DE_BAJA',
                    'Inactivo'    => 'INACTIVO',
                ];
                $dbValue = $map[$keyword] ?? $keyword;
                $query->where('employee_details.status', $dbValue);
            })
            ->rawColumns(['fullname', 'action', 'emp_code', 'oracle_emp_code', 'ubicacion', 'status'])
            ->setRowId('id');
    }

    public function query(): QueryBuilder
    {
        return User::where('users.type', '=', UserType::EMPLOYEE)
            ->where(function($q) {
                $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'Tienda'))
                ->orWhereHas('employeeDetail', fn($r) => $r->whereNotNull('oracle_emp_code'));
            })
            ->leftJoin('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('stores', 'employee_details.store_id', '=', 'stores.id')
            ->leftJoin('departments', 'employee_details.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->select(
                'users.*',
                'employee_details.oracle_emp_code',
                'employee_details.emp_code as smarthr_code',
                'employee_details.store_id',
                'employee_details.department_id',
                'employee_details.status as emp_status',
                'stores.name as store_name',
                'departments.name as dept_name',
                'designations.name as desig_name'
            )
            ->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('employee-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(2)
            ->searching(true)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('oracle_emp_code')->title('Cód. PRISMA')->searchable(true),
            Column::make('emp_code')->title('Cód. SmartHR')->searchable(true),
            Column::make('fullname')->title('Nombre')->searchable(true),
            Column::make('ubicacion')->title('Tienda / Depto')->searchable(true),
            Column::make('designation')->title('Puesto')->searchable(true),
            Column::make('username')->title('Usuario')->searchable(true),
            Column::make('status')->name('status')->title('Estado')->searchable(true),
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