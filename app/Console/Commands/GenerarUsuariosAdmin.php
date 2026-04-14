<?php

namespace App\Console\Commands;

use App\Models\EmployeeDetail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GenerarUsuariosAdmin extends Command
{
    protected $signature   = 'admin:generar-usuarios';
    protected $description = 'Genera usuarios de acceso individual para empleados admin en el módulo de recibos';

    public function handle()
    {
        // Solo empleados de departamento (admin), no de tienda
        $empleados = EmployeeDetail::with(['user'])
            ->whereNotNull('department_id')
            ->whereNotNull('oracle_emp_code')
            ->where('oracle_active', true)
            ->whereNotIn('status', ['DAR_DE_BAJA', 'INACTIVO'])
            ->get();

        $rol = \Spatie\Permission\Models\Role::firstOrCreate([
            'name'       => 'Tienda',
            'guard_name' => 'web'
        ]);

        $creados    = 0;
        $existentes = 0;

        foreach ($empleados as $empleado) {
            $username = $empleado->oracle_emp_code;

            if (empty($username)) continue;

            $userExiste = User::where('username', $username)->first();

            if ($userExiste) {
                // Asegurarse que tenga el rol
                if (!$userExiste->hasRole('Tienda')) {
                    $userExiste->assignRole($rol);
                }
                $existentes++;
                continue;
            }

            $user = User::create([
                'firstname' => $empleado->user->firstname ?: 'Admin',
                'lastname'  => $empleado->user->lastname  ?: $username,
                'email'     => $username . '_recibos@calzadoroy.com',
                'username'  => $username,
                'password'  => Hash::make($username),
                'type'      => \App\Enums\UserType::EMPLOYEE,
                'is_active' => true,
            ]);

            $user->assignRole($rol);

            $creados++;
            $fullname = $empleado->user->fullname ?: 'Sin nombre';
            $this->line("✓ Admin {$username} — {$fullname}");
        }

        $this->info("═══════════════════════════════");
        $this->info("Usuarios creados:   {$creados}");
        $this->info("Ya existían:        {$existentes}");
        $this->info("Total empleados:    " . ($creados + $existentes));
        $this->info("═══════════════════════════════");

        return 0;
    }
}