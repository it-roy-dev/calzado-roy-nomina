<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Módulos que SÍ usamos — formato [modulo => [acciones]]
        $modulos = [
            // EMPLEADOS
            'employees'     => ['create', 'view', 'edit', 'delete'],
            'attendances'   => ['create', 'view', 'edit', 'delete'],
            'departments'   => ['create', 'view', 'edit', 'delete'],
            'designations'  => ['create', 'view', 'edit', 'delete'],
            'holidays'      => ['create', 'view', 'edit', 'delete'],
            'uniformes'     => ['create', 'view', 'edit', 'delete'],
            // NÓMINA
            'nomina'        => ['create', 'view', 'edit', 'delete'],
            'boletas'       => ['create', 'view', 'edit', 'delete'],
            'mis-boletas'   => ['view'],
            'reportes'      => ['view'],
            // FINANZAS
            'assets'        => ['create', 'view', 'edit', 'delete'],
            'contabilidad'  => ['create', 'view', 'edit', 'delete'],
            // SISTEMA
            'tickets'       => ['create', 'view', 'edit', 'delete'],
            'mis-tickets'   => ['create', 'view'],
            'usuarios'      => ['create', 'view', 'edit', 'delete'],
            'respaldos'     => ['view'],
            'ajustes'       => ['view', 'edit'],
            'roles'         => ['create', 'view', 'edit', 'delete'],
            // DRAWING APPS
            'notas'         => ['create', 'view', 'edit', 'delete'],
        ];

        // Eliminar permisos que NO están en nuestra lista
        $permisosValidos = [];
        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $permisosValidos[] = $accion . '-' . $modulo;
            }
        }

        // Eliminar permisos no válidos (pero no los del sistema base que necesitamos)
        Permission::whereNotIn('name', $permisosValidos)->delete();

        // Crear o actualizar permisos válidos
        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $nombre = $accion . '-' . $modulo;
                $perm = Permission::where('name', $nombre)->where('guard_name', 'web')->first();
                if (!$perm) {
                    Permission::create([
                        'name'       => $nombre,
                        'module'     => $modulo,
                        'guard_name' => 'web',
                    ]);
                } else {
                    $perm->update(['module' => $modulo]);
                }
            }
        }

        // Configurar roles con sus permisos
        $rolesPermisos = [
            'Administrador' => $permisosValidos,

            'Nómina' => [
                'view-employees', 'edit-employees',
                'create-nomina', 'view-nomina', 'edit-nomina', 'delete-nomina',
                'create-boletas', 'view-boletas', 'edit-boletas', 'delete-boletas',
                'view-mis-boletas',
                'view-reportes',
                'create-mis-tickets', 'view-mis-tickets',
                'create-notas', 'view-notas', 'edit-notas', 'delete-notas',
            ],

            'Recursos Humanos' => [
                'create-employees', 'view-employees', 'edit-employees', 'delete-employees',
                'create-departments', 'view-departments', 'edit-departments', 'delete-departments',
                'create-designations', 'view-designations', 'edit-designations', 'delete-designations',
                'view-mis-boletas',
                'create-mis-tickets', 'view-mis-tickets',
            ],

            'Contabilidad' => [
                'create-contabilidad', 'view-contabilidad', 'edit-contabilidad', 'delete-contabilidad',
                'view-mis-boletas',
                'create-mis-tickets', 'view-mis-tickets',
            ],

            'Informática' => [
                'create-tickets', 'view-tickets', 'edit-tickets', 'delete-tickets',
                'create-assets', 'view-assets', 'edit-assets', 'delete-assets',
                'view-mis-boletas',
                'create-mis-tickets', 'view-mis-tickets',
            ],

            'Empleado' => [
                'view-mis-boletas',
                'create-mis-tickets', 'view-mis-tickets',
            ],

            'Tienda' => [
                'view-boletas',
                'create-mis-tickets', 'view-mis-tickets',
            ],
        ];

        foreach ($rolesPermisos as $rolNombre => $permisos) {
            $rol = Role::firstOrCreate(['name' => $rolNombre, 'guard_name' => 'web']);
            $permisosExistentes = Permission::whereIn('name', $permisos)->pluck('name')->toArray();
            $rol->syncPermissions($permisosExistentes);
            $this->command->info("Rol '$rolNombre' configurado con " . count($permisosExistentes) . " permisos");
        }

        $this->command->info('Listo.');
    }
}