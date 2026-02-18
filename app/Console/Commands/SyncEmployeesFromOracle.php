<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\EmployeeDetail;
use App\Models\User;
use App\Helpers\OracleHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncEmployeesFromOracle extends Command
{
    protected $signature = 'employees:sync';
    protected $description = 'Sincronizar empleados y tiendas desde Oracle PRISMA a PostgreSQL';

    public function handle()
    {
        $this->info('Iniciando sincronizacion desde Oracle PRISMA...');
        
        try {
            // PASO 1: Sincronizar tiendas primero
            $this->syncStores();
            
            // PASO 2: Sincronizar empleados
            $this->syncEmployees();
            
            $this->info('Sincronizacion completada exitosamente');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error en sincronizacion: ' . $e->getMessage());
            Log::error('Employee sync failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
    
    /**
     * Sincronizar tiendas desde Oracle
     */
    private function syncStores()
    {
        $this->info('Sincronizando tiendas...');
        
        $sql = "SELECT 
                    SID,
                    STORE_NO,
                    STORE_CODE,
                    STORE_NAME,
                    ACTIVE,
                    ACTIVATION_DATE,
                    ADDRESS1,
                    ADDRESS2,
                    ADDRESS3,
                    ADDRESS4,
                    ADDRESS5,
                    PHONE1
                FROM RPS.STORE
                WHERE ACTIVE = 1
                ORDER BY STORE_NO";
        
        $oracleStores = OracleHelper::query($sql);
        
        $synced = 0;
        
        foreach ($oracleStores as $oracleStore) {
                    // Detectar tipo de tienda
                    $type = 'tienda';
                    
                    if ($oracleStore['STORE_CODE'] == '000') {
                        $type = 'admin';
                    }
                    
                    // Detectar pais y generar codigo con prefijo
                    $storeNo = $oracleStore['STORE_NO'];
                    $storeCode = $oracleStore['STORE_CODE'];
                    $storeName = strtoupper($oracleStore['STORE_NAME'] ?? '');

                    // Detectar Honduras por nombre
                    if (strpos($storeName, 'HONDURAS') !== false) {
                        $code = 'H-' . $storeNo . '-' . $storeCode;
                    } else {
                        // Guatemala: todas las demas
                        $code = 'G-' . $storeNo . '-' . $storeCode;
                    }
                    // Concatenar direccion
                    $address = trim(
                        ($oracleStore['ADDRESS1'] ?? '') . ' ' .
                        ($oracleStore['ADDRESS2'] ?? '') . ' ' .
                        ($oracleStore['ADDRESS3'] ?? '') . ' ' .
                        ($oracleStore['ADDRESS4'] ?? '') . ' ' .
                        ($oracleStore['ADDRESS5'] ?? '')
                    );
                    
                    // Crear o actualizar tienda
                    Store::updateOrCreate(
                        ['oracle_store_sid' => $oracleStore['SID']],
                        [
                            'oracle_store_no' => $oracleStore['STORE_NO'],
                            'oracle_store_code' => $oracleStore['STORE_CODE'],
                            'code' => $code,
                            'name' => $oracleStore['STORE_NAME'],
                            'type' => $type,
                            'address' => $address,
                            'phone' => $oracleStore['PHONE1'],
                            'activation_date' => $oracleStore['ACTIVATION_DATE'],
                            'is_active' => $oracleStore['ACTIVE'] == '1',
                            'last_synced_at' => now(),
                        ]
                    );
                    
                    $synced++;
                }
        
        $this->info("   $synced tiendas sincronizadas");
    }
    
    /**
     * Sincronizar empleados desde Oracle
     */
    private function syncEmployees()
    {
        $this->info('Sincronizando empleados...');
        
            $sql = "SELECT 
                    SID,
                    EMPL_ID,
                    FULL_NAME,
                    USER_NAME,
                    ACTIVE,
                    USER_ACTIVE,
                    HIRE_DATE,
                    BASE_STORE_SID
                FROM RPS.EMPLOYEE
                WHERE ACTIVE = 1
                  AND FULL_NAME IS NOT NULL
                ORDER BY FULL_NAME";
        
        $oracleEmployees = OracleHelper::query($sql);
        
        $synced = 0;
        $skipped = 0;
        
        // Obtener tienda por defecto (Servidor Tiendas o primera tienda)
        $defaultStore = Store::where('oracle_store_code', '000')->first();
        if (!$defaultStore) {
            $defaultStore = Store::first();
        }
        
        foreach ($oracleEmployees as $oracleEmployee) {
            
            $store = null;
            
            // Buscar tienda asignada
            if (!empty($oracleEmployee['BASE_STORE_SID'])) {
                $store = Store::where('oracle_store_sid', $oracleEmployee['BASE_STORE_SID'])->first();
            }
            
            // Si no tiene tienda, asignar tienda por defecto (Administracion)
            if (!$store) {
                $store = $defaultStore;
            }
            
            if (!$store) {
                $this->warn("   Empleado {$oracleEmployee['EMPL_ID']} sin tienda - OMITIDO");
                $skipped++;
                continue;
            }
            
            // Separar nombre completo
            $fullName = $oracleEmployee['FULL_NAME'] ?? $oracleEmployee['USER_NAME'];
            $nameParts = explode(' ', trim($fullName), 2);
            $firstname = $nameParts[0] ?? 'Sin nombre';
            $lastname = $nameParts[1] ?? '';
            
            // Generar codigo de empleado temporal
            $empCode = 'TEMP-' . str_pad($oracleEmployee['EMPL_ID'], 6, '0', STR_PAD_LEFT);
            
            // Crear o actualizar usuario
            $user = User::updateOrCreate(
                ['email' => strtolower($oracleEmployee['USER_NAME']) . '@calzadoroy.com'],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'username' => $oracleEmployee['USER_NAME'],
                    'type' => 'Employee',
                    'password' => bcrypt('123456'),
                    'is_active' => $oracleEmployee['USER_ACTIVE'] == '1',
                ]
            );
            
            // Crear o actualizar empleado
            EmployeeDetail::updateOrCreate(
                ['oracle_employee_id' => $oracleEmployee['SID']],
                [
                    'emp_code' => $empCode,
                    'oracle_emp_code' => $oracleEmployee['EMPL_ID'],
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                    'date_joined' => $oracleEmployee['HIRE_DATE'],
                    'status' => 'PENDIENTE',
                ]
            );
            
            $synced++;
        }
        
        $this->info("   $synced empleados sincronizados");
        if ($skipped > 0) {
            $this->warn("   $skipped empleados omitidos");
        }
    }
}