<div>
    @push('page-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        .backup-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #eef2f7;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .backup-card-header {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(to right, #f8faff, #fff);
        }
        .backup-card-header h6 {
            font-size: 14px;
            font-weight: 700;
            color: #0f2456;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .backup-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .backup-table thead th {
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
        }
        .backup-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f8fafc;
            color: #1e293b;
            vertical-align: middle;
        }
        .backup-table tbody tr:last-child td { border-bottom: none; }
        .backup-table tbody tr:hover td { background: #f8fbff; }
        .btn-backup-primary {
            background: #1e3a5f;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }
        .btn-backup-primary:hover { background: #2d5a8e; }
        .btn-backup-secondary {
            background: transparent;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }
        .btn-backup-secondary:hover { border-color: #1e3a5f; color: #1e3a5f; }
        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.15s;
            color: #64748b;
        }
        .btn-action:hover { background: #f1f5f9; color: #1e3a5f; }
        .btn-action.danger:hover { background: #fee2e2; color: #ef4444; }
        .status-healthy {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .status-unhealthy {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .disk-btn {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: all 0.15s;
        }
        .disk-btn.active { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }
        .creating-spinner {
            display: none;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
        }
        .creating-spinner.show { display: flex; }
    </style>
    @endpush

    {{-- Header con botones --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div class="creating-spinner" id="creating-spinner">
            <i class="fa-solid fa-spinner fa-spin" style="color:#1e3a5f"></i>
            <span>Creando respaldo en segundo plano...</span>
        </div>
        <div style="display:flex;gap:10px;margin-left:auto">
            <form action="{{ route('backups.create') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-backup-primary">
                    <i class="fa-solid fa-plus"></i> Crear Respaldo
                </button>
            </form>
            <div class="dropdown">
                <button class="btn-backup-secondary dropdown-toggle" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="{{ route('backups.create') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fa-solid fa-database me-2"></i> Solo base de datos
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('backups.create-files') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fa-solid fa-folder me-2"></i> Solo archivos
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Estado del disco --}}
    <div class="backup-card">
        <div class="backup-card-header">
            <h6><i class="fa-solid fa-hard-drive" style="color:#1e3a5f"></i> Estado del Sistema de Respaldos</h6>
            <button class="btn-backup-secondary" wire:click="updateBackupStatuses"
                    wire:loading.attr="disabled">
                <i class="fa-solid fa-rotate" wire:loading.class="fa-spin" wire:target="updateBackupStatuses"></i>
                Actualizar
            </button>
        </div>
        <table class="backup-table">
            <thead>
                <tr>
                    <th>Disco</th>
                    <th>Estado</th>
                    <th>Cantidad</th>
                    <th>Último Respaldo</th>
                    <th>Espacio Usado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backupStatuses as $backupStatus)
                <tr wire:key="{{ $backupStatus['disk'] }}">
                    <td>
                        <span style="background:#f1f5f9;padding:4px 10px;border-radius:6px;font-weight:600;font-size:12px">
                            <i class="fa-solid fa-server me-1" style="color:#1e3a5f"></i>
                            {{ $backupStatus['disk'] }}
                        </span>
                    </td>
                    <td>
                        @if($backupStatus['healthy'])
                            <span class="status-healthy">
                                <i class="fa-solid fa-circle-check"></i> Saludable
                            </span>
                        @else
                            <span class="status-unhealthy">
                                <i class="fa-solid fa-circle-xmark"></i> Con problemas
                            </span>
                        @endif
                    </td>
                    <td><strong>{{ $backupStatus['amount'] }}</strong></td>
                    <td>{{ $backupStatus['newest'] }}</td>
                    <td>{{ $backupStatus['usedStorage'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#94a3b8;padding:24px">
                        <i class="fa-solid fa-database" style="font-size:24px;margin-bottom:8px;display:block;opacity:0.3"></i>
                        Sin información de respaldos
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Lista de respaldos --}}
    <div class="backup-card">
        <div class="backup-card-header">
            <h6><i class="fa-solid fa-file-zipper" style="color:#1e3a5f"></i> Archivos de Respaldo</h6>
            <div style="display:flex;align-items:center;gap:10px">
                @if(count($disks))
                    <div style="display:flex;gap:6px">
                        @foreach($disks as $disk)
                            <button class="disk-btn {{ $activeDisk === $disk ? 'active' : '' }}"
                                    wire:key="{{ $disk }}"
                                    wire:click="getFiles('{{ $disk }}')">
                                <i class="fa-solid fa-circle" style="font-size:8px;margin-right:4px;color:{{ $activeDisk === $disk ? '#10b981' : '#94a3b8' }}"></i>
                                {{ $disk }}
                            </button>
                        @endforeach
                    </div>
                @endif
                <button class="btn-backup-secondary" wire:click="getFiles"
                        wire:loading.attr="disabled" {{ $activeDisk ? '' : 'disabled' }}>
                    <i class="fa-solid fa-rotate" wire:loading.class="fa-spin" wire:target="getFiles"></i>
                    Actualizar
                </button>
            </div>
        </div>
        <table class="backup-table">
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Fecha de Creación</th>
                    <th>Tamaño</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                <tr wire:key="{{ $file['path'] }}">
                    <td>
                        <i class="fa-solid fa-file-zipper me-2" style="color:#1e3a5f"></i>
                        {{ $file['path'] }}
                    </td>
                    <td>{{ $file['date'] }}</td>
                    <td>
                        <span style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600">
                            {{ $file['size'] }}
                        </span>
                    </td>
                    <td style="text-align:right">
                        <button class="btn-action" title="Descargar"
                                wire:click="downloadFile('{{ $file['path'] }}')">
                            <i class="fa-solid fa-download"></i>
                        </button>
                        <button class="btn-action danger" title="Eliminar"
                                wire:click="showDeleteModal({{ $loop->index }})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:#94a3b8;padding:32px">
                        <i class="fa-solid fa-file-zipper" style="font-size:32px;margin-bottom:8px;display:block;opacity:0.2"></i>
                        No hay respaldos disponibles
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal eliminar --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
            <div class="modal-content" style="border-radius:14px;overflow:hidden">
                <div class="modal-header" style="background:linear-gradient(135deg,#0f2456,#2a5298);color:#fff;border:none">
                    <h5 class="modal-title" style="font-size:15px;font-weight:700">
                        <i class="fa-solid fa-trash me-2"></i> Eliminar Respaldo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px">
                    @if($deletingFile)
                    <p style="font-size:13px;color:#64748b">
                        ¿Está seguro que desea eliminar el respaldo creado el
                        <strong>{{ $deletingFile['date'] }}</strong>?
                        Esta acción no se puede deshacer.
                    </p>
                    @endif
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9">
                    <button type="button" class="btn btn-outline-secondary btn-sm cancel-button"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger btn-sm delete-button"
                            wire:click="deleteFile">
                        <i class="fa-solid fa-trash me-1"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
    document.addEventListener('livewire:initialized', function () {
        @this.updateBackupStatuses();

        @this.on('backupStatusesUpdated', function () {
            @this.getFiles();
        });

        @this.on('showErrorToast', function (data) {
            Toastify({
                text: data.message,
                duration: 10000,
                gravity: 'bottom',
                position: 'right',
                style: { background: '#ef4444' },
            }).showToast();
        });

        const backupFun = function (option = '') {
            const spinner = document.getElementById('creating-spinner');
            if (spinner) spinner.classList.add('show');

            Toastify({
                text: 'Creando respaldo en segundo plano...',
                duration: 5000,
                gravity: 'bottom',
                position: 'right',
                style: { background: '#1e3a5f' },
            }).showToast();

            @this.createBackup(option);

            setTimeout(() => {
                if (spinner) spinner.classList.remove('show');
                @this.updateBackupStatuses();
            }, 8000);
        };

        document.getElementById('create-backup')?.addEventListener('click', () => backupFun());
        document.getElementById('create-backup-only-db')?.addEventListener('click', (e) => {
            e.preventDefault(); backupFun('only-db');
        });
        document.getElementById('create-backup-only-files')?.addEventListener('click', (e) => {
            e.preventDefault(); backupFun('only-files');
        });

        @this.on('showDeleteModal', function () {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).show();
        });
        @this.on('hideDeleteModal', function () {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteModal')).hide();
        });

        document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function () {
            @this.deletingFile = null;
        });
    });
    </script>
    @endpush
</div>