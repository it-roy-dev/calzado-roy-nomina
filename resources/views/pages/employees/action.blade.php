<x-table-action>
    <a class="dropdown-item" href="{{ $expUrl ?? '#' }}">
        <i class="fa-solid fa-file-pen m-r-5"></i>
        {{ $expLabel ?? 'Completar Expediente' }}
    </a>
    <a class="dropdown-item" href="javascript:void(0)"
        data-url="{{ route('employees.edit', ['employee' => \Crypt::encrypt($id)]) }}"
        data-ajax-modal="true" data-title="Editar Empleado" data-size="lg">
        <i class="fa-solid fa-pencil m-r-5"></i> Editar
    </a>
    <a class="dropdown-item deleteBtn"
        data-route="{{ route('employees.destroy', $id) }}"
        data-title="Eliminar Empleado"
        data-question="¿Estás segura de eliminar este empleado?"
        href="javascript:void(0)">
        <i class="fa-regular fa-trash-can m-r-5"></i> Eliminar
    </a>
</x-table-action>