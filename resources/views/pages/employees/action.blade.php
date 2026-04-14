<div class="d-flex gap-1 justify-content-end">
    <a href="{{ route('employees.show', \Crypt::encrypt($id)) }}"
        class="btn btn-sm" style="background:#f1f5f9;color:#1e3c72;border:1px solid #e2e8f0;border-radius:7px;font-size:11px;font-weight:600;padding:4px 10px"
        title="Ver Perfil">
        <i class="fa-solid fa-eye me-1"></i> Perfil
    </a>
    <a href="{{ $expUrl ?? '#' }}"
        class="btn btn-sm" style="background:#1e3c72;color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:600;padding:4px 10px"
        title="{{ $expLabel ?? 'Completar Expediente' }}">
        <i class="fa-solid fa-file-pen me-1"></i> {{ $expLabel ?? 'Expediente' }}
    </a>
    @if(isset($darDeBaja) && $darDeBaja)
    <a href="javascript:void(0)"
        class="btn btn-sm" style="background:#ef4444;color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:600;padding:4px 10px"
        title="Procesar Liquidación">
        <i class="fa-solid fa-file-invoice-dollar me-1"></i> Liquidación
    </a>
    @endif
</div>