<div class="modal-body">
    <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <x-form.label>{{ __('Primer Nombre') }}</x-form.label>
                            <x-form.input type="text" name="firstname" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <x-form.label>{{ __('Segundo Nombre') }}</x-form.label>
                            <x-form.input type="text" name="middlename" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <x-form.label>{{ __('Apellidos') }}</x-form.label>
                            <x-form.input type="text" name="lastname" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <x-form.label>{{ __('Usuario') }}</x-form.label>
                            <x-form.input type="text" name="username" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <x-form.label>{{ __('Correo Electrónico') }}</x-form.label>
                            <x-form.input type="email" name="email" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label>{{ __('Número de Teléfono') }}</label>
                            <x-form.phone type="text" name="phone" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <x-form.input-block>
                            <x-form.label>
                                {{ __('Contraseña') }}
                            </x-form.label>
                            <x-form.input type="password" name="password" />
                        </x-form.input-block>
                    </div>
                    <div class="col-sm-6">
                        <x-form.input-block>
                            <x-form.label>
                                {{ __('Confirmar Contraseña') }}
                            </x-form.label>
                            <x-form.input type="password" name="password_confirmation" />
                        </x-form.input-block>
                    </div>
                    <div class="col-sm-6">
                        <x-form.input-block>
                            <x-form.label>{{ __('Rol') }}</x-form.label>
                            <select name="role" id="role" class="form-control select">
                                @foreach ($roles as $role)
                                    <option>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </x-form.input-block>
                    </div>
                    <div class="col-sm-6">
                        <div class="input-block mb-3">
                            <x-form.label>
                                {{ __('Avatar') }}
                            </x-form.label>
                            <x-form.input type="file" name="avatar" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="input-block mb-3">
                    <x-form.label>{{ __('Dirección') }}</x-form.label>
                    <x-form.input type="text" name="address" />
                </div>
            </div>
            <div class="col-md-12">
                <x-form.label>{{ __('Activo') }}</x-form.label>
                <div class="input-block">
                    <div class="status-toggle">
                        <x-form.input type="checkbox" id="status" class="check" name="status" />
                        <label for="status" class="checktoggle">checkbox</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="submit-section mb-2">
            <x-form.button class="btn btn-primary submit-btn">{{ __('Guardar') }}</x-form.button>
        </div>
    </form>
</div>
