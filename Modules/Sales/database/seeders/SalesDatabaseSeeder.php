<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class SalesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Usar SQL directo con valores correctamente formateados para PostgreSQL
    DB::unprepared("
        INSERT INTO taxes (name, percentage, active, created_at, updated_at) 
        VALUES 
            ('VAT', 14, true, NOW(), NOW()),
            ('GST', 30, true, NOW(), NOW())
    ");
    
    Model::unguard();
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $permissionsArray = [];
    $module_permissions = [
        'Estimate' => [
            'view-estimates','create-estimate','edit-estimate','show-estimate','delete-estimate',
        ],
        'Invoice' => [
            'view-invoices','create-invoice','edit-invoice','show-invoice','delete-invoice',
        ],
        'Expense' => [
            'view-expenses','create-expense','edit-expense','delete-expense',
        ],
        'Tax' => [
            'view-taxs','create-tax','edit-tax','delete-tax',
        ],
    ];
    foreach ($module_permissions as $module => $permissions) {
        foreach ($permissions as $permission) {
            $permissionsArray[] = [
                "name" => $permission,
                "module" => $module,
                "guard_name" => "web"
            ];
        }
    }
    Permission::insert($permissionsArray);
    Model::unguard(false);
}

}