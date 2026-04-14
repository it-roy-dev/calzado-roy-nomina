<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\StoreUser;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GenerarUsuariosTienda extends Command
{
    protected $signature   = 'tiendas:generar-usuarios';
    protected $description = 'Genera usuarios de acceso por tienda para el módulo de recibos';

    public function handle()
    {
        $tiendas = Store::where('is_active', true)
            ->whereNotIn('oracle_store_no', [0, 99, 100])
            ->orderBy('oracle_store_no')
            ->get();

        $rol = Role::firstOrCreate(['name' => 'Tienda', 'guard_name' => 'web']);

        $creados    = 0;
        $existentes = 0;

        foreach ($tiendas as $tienda) {
            $username = '1' . str_pad($tienda->oracle_store_no, 3, '0', STR_PAD_LEFT);

            // Verificar si ya existe
            $userExiste = User::where('username', $username)->first();

            if ($userExiste) {
                // Asegurar que tenga la relación con la tienda
                StoreUser::firstOrCreate([
                    'user_id'  => $userExiste->id,
                    'store_id' => $tienda->id,
                ]);
                $existentes++;
                continue;
            }

            // Crear usuario
            $user = User::create([
                'firstname' => 'Tienda',
                'lastname'  => $tienda->name,
                'email'     => 'tienda' . $tienda->oracle_store_no . '@calzadoroy.com',
                'username'  => $username,
                'password'  => Hash::make($username),
                'type'      => UserType::EMPLOYEE,
                'is_active' => true,
            ]);

            $user->assignRole($rol);

            StoreUser::create([
                'user_id'  => $user->id,
                'store_id' => $tienda->id,
            ]);

            $creados++;
            $this->line("✓ Tienda {$tienda->oracle_store_no} — usuario: {$username}");
        }

        $this->info("═══════════════════════════════");
        $this->info("Usuarios creados:   {$creados}");
        $this->info("Ya existían:        {$existentes}");
        $this->info("Total tiendas:      " . ($creados + $existentes));
        $this->info("═══════════════════════════════");

        return 0;
    }
}