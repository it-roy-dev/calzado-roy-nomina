<div class="modal-body">
    <form method="POST" action="{{route('roles.store')}}">
        @csrf
        <div class="form-group">
            <label>Nombre del Rol <span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="name">
        </div>
        <div class="submit-section mb-2">
            <button class="btn btn-primary submit-btn">Guardar</button>
        </div>
    </form>
</div>