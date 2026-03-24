<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\EmployeeDetail;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Helpers\OracleHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncEmployeesFromOracle extends Command
{
    protected $signature = 'employees:sync';
    protected $description = 'Sincronizar empleados y tiendas desde Oracle PRISMA a PostgreSQL (solo Guatemala)';

    public function handle()
    {
        $this->info('Iniciando sincronizacion desde Oracle PRISMA (Guatemala)...');
        
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
     * Sincronizar tiendas desde Oracle (solo Guatemala)
     */
    private function syncStores()
    {
        $this->info('Sincronizando tiendas de Guatemala...');
        
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
                    PHONE1,
                    SBS_SID
                FROM RPS.STORE
                WHERE ACTIVE = 1
                AND SBS_SID = 680861302000159257
                ORDER BY STORE_NO";
        
        $oracleStores = OracleHelper::query($sql);
        
        $synced = 0;
        
        foreach ($oracleStores as $oracleStore) {
            // Detectar tipo de tienda
            $type = 'tienda';
            
            if ($oracleStore['STORE_CODE'] == '000') {
                $type = 'admin';
            }
            
            // Generar codigo con prefijo Guatemala
            $storeNo = $oracleStore['STORE_NO'];
            $storeCode = $oracleStore['STORE_CODE'];
            $code = 'G-' . $storeNo . '-' . $storeCode;
            
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
        
        $this->info("   $synced tiendas de Guatemala sincronizadas");
    }
    
    /**
     * Mapeo manual de empleados con departamento y puesto
     */
    private function getEmployeeMapping()
    {
        return [
            // TIENDAS GUATEMALA
            '3497' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4085' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4378' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4038' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3940' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4335' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4612' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '1658' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3879' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4504' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4236' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3783' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4129' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4364' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3911' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3680' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4542' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3248' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3901' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4465' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4395' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3893' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4426' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4518' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4433' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4608' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3928' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4477' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4498' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4031' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4594' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4536' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4313' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3854' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4218' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4591' => ['departamento' => null, 'puesto' => 'Cubre vacaciones'],
            '4574' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4432' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4592' => ['departamento' => null, 'puesto' => 'Cubre vacaciones'],
            '4467' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3843' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4291' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4016' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4463' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4507' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4619' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4042' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4217' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4407' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4460' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4141' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4278' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4605' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4303' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3281' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3841' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '3980' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4598' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4352' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4614' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4588' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4412' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4450' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4435' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4121' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4367' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4565' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '1818' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3445' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '3945' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4081' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4331' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3657' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3807' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4415' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4189' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4596' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3364' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3202' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4035' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4312' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4416' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4373' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4558' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4195' => ['departamento' => null, 'puesto' => 'Jefe interina'],
            '4585' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4420' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4055' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4430' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4545' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3593' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4109' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4527' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4576' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '1994' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4486' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4479' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3225' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4553' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4309' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4482' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4521' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4444' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3190' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4516' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4595' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4616' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4276' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '1729' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4601' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4428' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4570' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4368' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4474' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4615' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4393' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3530' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4204' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4489' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3920' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4071' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4600' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4461' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4058' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4566' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4580' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4151' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4328' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4338' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3995' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4561' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4602' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4150' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4348' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4491' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4488' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4617' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3894' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4500' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4572' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3970' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4374' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4531' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4573' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4339' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4443' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4537' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3822' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3963' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4317' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4326' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4343' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '1679' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3027' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4197' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4549' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3742' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4103' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4253' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4587' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3988' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4234' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4439' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4111' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4168' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4392' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3801' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3461' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4410' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4496' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4159' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4290' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4604' => ['departamento' => null, 'puesto' => 'Cubre vacaciones'],
            '4405' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4555' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4564' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4618' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4310' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4445' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4613' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4581' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4345' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4472' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4484' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3957' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4136' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4562' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4371' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4202' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4298' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4213' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4382' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4033' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4128' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4402' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '1698' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3827' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '3948' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4232' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4192' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3011' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4018' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4438' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4458' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4515' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4347' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4425' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4579' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3078' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '3855' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4409' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4037' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4245' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4599' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4519' => ['departamento' => null, 'puesto' => 'Cubre vacaciones'],
            '3864' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4167' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4611' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4492' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4254' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4077' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4255' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4025' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4582' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4487' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3250' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4569' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4597' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4590' => ['departamento' => null, 'puesto' => 'Cubre vacaciones'],
            '4583' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4306' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4466' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4423' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4514' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3784' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4237' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4238' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3958' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4118' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4400' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4523' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3913' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '1592' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4434' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4503' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4603' => ['departamento' => null, 'puesto' => 'Cubre vacaciones'],
            '4274' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4220' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4369' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3212' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4300' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4524' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4225' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4481' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4311' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4431' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4577' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3779' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4360' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4589' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4512' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4508' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4607' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4544' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4547' => ['departamento' => null, 'puesto' => 'Jefa de tienda interina'],
            '4610' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4575' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4115' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4554' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '4149' => ['departamento' => null, 'puesto' => 'Jefe de tienda'],
            '4559' => ['departamento' => null, 'puesto' => 'Sub jefe de tienda'],
            '4509' => ['departamento' => null, 'puesto' => 'Asesor de ventas'],
            '3667' => ['departamento' => null, 'puesto' => 'Supervisor de tiendas'],
            
            // ADMINISTRACIÓN GUATEMALA
            '5106' => ['departamento' => 'Gerencia General', 'puesto' => 'Gerente general'],
            '5312' => ['departamento' => 'Gerencia de Proyectos', 'puesto' => 'Gerente de proyectos'],
            '5226' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Supervisor de tiendas'],
            '5210' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Gerente de operaciones'],
            '5333' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Gerente de operaciones'],
            '5378' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Supervisor de tiendas'],
            '5419' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Operaciones'],
            '5287' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Supervisor de tiendas'],
            '5379' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Supervisor de tiendas'],
            '5366' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Supervisor de tiendas'],
            '5400' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Supervisor de tiendas'],
            '5395' => ['departamento' => 'Operaciones Tiendas', 'puesto' => 'Técnico en mantenimiento'],
            '5322' => ['departamento' => 'Contabilidad', 'puesto' => 'Contador general tiendas'],
            '5152' => ['departamento' => 'Contabilidad', 'puesto' => 'Auxiliar contabilidad'],
            '5349' => ['departamento' => 'Contabilidad', 'puesto' => 'Auxiliar contabilidad'],
            '5390' => ['departamento' => 'Contabilidad', 'puesto' => 'Auxiliar contabilidad'],
            '5377' => ['departamento' => 'Contabilidad', 'puesto' => 'Auxiliar contabilidad'],
            '5397' => ['departamento' => 'Contabilidad', 'puesto' => 'Auxiliar contabilidad'],
            '5247' => ['departamento' => 'Contabilidad', 'puesto' => 'Mensajero'],
            '5184' => ['departamento' => 'Digitación', 'puesto' => 'Encargado de digitación'],
            '5351' => ['departamento' => 'Digitación', 'puesto' => 'Digitación'],
            '5420' => ['departamento' => 'Digitación', 'puesto' => 'Digitación'],
            '5401' => ['departamento' => 'Digitación', 'puesto' => 'Digitación'],
            '6278' => ['departamento' => 'Digitación', 'puesto' => 'Digitación'],
            '5342' => ['departamento' => 'Digitación', 'puesto' => 'Auxiliar de inventarios'],
            '5340' => ['departamento' => 'Mercadeo y Diseño', 'puesto' => 'Gerente de mercadeo'],
            '5412' => ['departamento' => 'Mercadeo y Diseño', 'puesto' => 'Coordinador de visual merchandising'],
            '6154' => ['departamento' => 'Mercadeo y Diseño', 'puesto' => 'Auxiliar de mercadeo'],
            '5362' => ['departamento' => 'Mercadeo y Diseño', 'puesto' => 'Diseñador gráfico'],
            '5392' => ['departamento' => 'Mercadeo y Diseño', 'puesto' => 'Community manager'],
            '5327' => ['departamento' => 'Compras', 'puesto' => 'Gerente de producto'],
            '5318' => ['departamento' => 'Operaciones', 'puesto' => 'Coordinador de operaciones'],
            '5328' => ['departamento' => 'Operaciones', 'puesto' => 'Auditor financiero'],
            '5411' => ['departamento' => 'Operaciones', 'puesto' => 'Analista de datos'],
            '5423' => ['departamento' => 'Operaciones', 'puesto' => 'Auxiliar de inventarios'],
            '6212' => ['departamento' => 'Operaciones', 'puesto' => 'Auxiliar de bodega'],
            '5345' => ['departamento' => 'Operaciones', 'puesto' => 'Auxiliar de inventarios'],
            '5391' => ['departamento' => 'Operaciones', 'puesto' => 'Auxiliar de inventarios'],
            '5404' => ['departamento' => 'Operaciones', 'puesto' => 'Auxiliar de piloto'],
            '5403' => ['departamento' => 'Operaciones', 'puesto' => 'Piloto'],
            '5339' => ['departamento' => 'Operaciones', 'puesto' => 'Auxiliar de inventarios'],
            '5202' => ['departamento' => 'Informática', 'puesto' => 'Jefe informática'],
            '5407' => ['departamento' => 'Informática', 'puesto' => 'Desarrollador jr'],
            '5402' => ['departamento' => 'Informática', 'puesto' => 'Soporte técnico'],
            '5253' => ['departamento' => 'Informática', 'puesto' => 'Soporte técnico'],
            '5388' => ['departamento' => 'Capital Humano', 'puesto' => 'Gerente de capital humano'],
            '5256' => ['departamento' => 'Capital Humano', 'puesto' => 'Gerente de capacitación'],
            '5398' => ['departamento' => 'Capital Humano', 'puesto' => 'Especialista en reclutamiento'],
            '5416' => ['departamento' => 'Capital Humano', 'puesto' => 'Especialista en reclutamiento'],
            '5415' => ['departamento' => 'Capital Humano', 'puesto' => 'Auditor y capacitador'],
            '6231' => ['departamento' => 'Capital Humano', 'puesto' => 'Recepcionista'],
            '5168' => ['departamento' => 'Capital Humano', 'puesto' => 'Conserje'],
            '6277' => ['departamento' => 'Capital Humano', 'puesto' => 'Conserje'],
            '6250' => ['departamento' => 'Compras ROY', 'puesto' => 'Gerente compras'],
            '5408' => ['departamento' => 'Ventas en Línea', 'puesto' => 'Asesor de ventas online'],
            '5417' => ['departamento' => 'Ventas en Línea', 'puesto' => 'Coordinador ventas online'],
            '5381' => ['departamento' => 'Ventas en Línea', 'puesto' => 'Asesor de ventas online'],
            '5389' => ['departamento' => 'Ventas en Línea', 'puesto' => 'Asesor de ventas online'],
            '5421' => ['departamento' => 'Ventas en Línea', 'puesto' => 'Asesor de ventas'],
            '5422' => ['departamento' => 'Ventas en Línea', 'puesto' => 'Asesor de ventas'],
        ];
    }
    
    /**
     * Sincronizar empleados desde Oracle (solo Guatemala)
     */
    private function syncEmployees()
    {
        $this->info('Sincronizando empleados de Guatemala...');
        
        // CONSULTA CON FILTRO DE SUBSIDIARIA GUATEMALA
            $sql = "SELECT 
                        e.SID,
                        e.EMPL_ID,
                        e.FULL_NAME,
                        e.USER_NAME,
                        e.ACTIVE,
                        e.USER_ACTIVE,
                        e.HIRE_DATE,
                        e.BASE_STORE_SID,
                        e.SBS_SID,
                        (
                            SELECT MIN(est.STORE_SID)
                            FROM RPS.EMPLOYEE_STORE est
                            INNER JOIN RPS.STORE s3 ON s3.SID = est.STORE_SID
                            WHERE est.EMPL_SID = e.SID
                            AND s3.STORE_NO NOT IN (0, 99, 100)
                            AND s3.SBS_SID = 680861302000159257
                        ) as REAL_STORE_SID
                    FROM RPS.EMPLOYEE e
                    WHERE e.SBS_SID = 680861302000159257
                    AND e.ACTIVE = 1
                    AND e.FULL_NAME IS NOT NULL
                    AND e.USER_NAME IS NOT NULL
                    AND e.USER_NAME >= '3000'
                    AND e.USER_NAME <= '7999'
                    ORDER BY e.USER_NAME";
        
        $oracleEmployees = OracleHelper::query($sql);
        
        $synced = 0;
        $updated = 0;
        $created = 0;
        $skipped = 0;
        $markedInactive = 0;
        
        // Obtener tienda por defecto (SERVIDOR TIENDAS)
        $defaultStore = Store::where('oracle_store_code', '000')->first();
        if (!$defaultStore) {
            $defaultStore = Store::first();
        }
        
        // Obtener mapeo manual de empleados
        $employeeMapping = $this->getEmployeeMapping();
        
        $this->info('   Empleados con datos mapeados: ' . count($employeeMapping));
        
        $withData = 0;
        $withoutData = 0;
        
        foreach ($oracleEmployees as $oracleEmployee) {
            
            // VERIFICAR SI YA EXISTE
            $existing = EmployeeDetail::where('oracle_employee_id', $oracleEmployee['SID'])->first();
            
            // CASO: EMPLEADO YA EXISTE
            if ($existing) {
                // Comparar estado de ACTIVE
                if ($existing->oracle_active && $oracleEmployee['ACTIVE'] == '0') {
                    // CASO B: Se dio de baja en Oracle
                    $existing->update([
                        'oracle_active' => false,
                        'status' => 'DAR_DE_BAJA',
                    ]);
                    $markedInactive++;
                    $this->warn("   Empleado {$oracleEmployee['USER_NAME']} marcado como DAR_DE_BAJA");
                } else {
                    // CASO A o C: Actualizar solo datos básicos de Oracle
                    $fullName = $oracleEmployee['FULL_NAME'] ?? $oracleEmployee['USER_NAME'];
                    $nameParts = explode(' ', trim($fullName), 2);
                    $firstname = $nameParts[0] ?? 'Sin nombre';
                    $lastname = $nameParts[1] ?? '';
                    
                    $existing->user->update([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'is_active' => $oracleEmployee['USER_ACTIVE'] == '1',
                    ]);
                    
                    $updated++;
                }
                continue;
            }
            
            // CASO: EMPLEADO NUEVO
            $store = null;

            // Primero intentar con BASE_STORE_SID (si no es SERVIDOR TIENDAS)
            if (!empty($oracleEmployee['BASE_STORE_SID'])) {
                $baseStore = Store::where('oracle_store_sid', $oracleEmployee['BASE_STORE_SID'])->first();
                if ($baseStore && $baseStore->oracle_store_code !== '000') {
                    $store = $baseStore;
                }
            }

            // Si era SERVIDOR TIENDAS, buscar tienda real desde EMPLOYEE_STORE
            if (!$store && !empty($oracleEmployee['REAL_STORE_SID'])) {
                $store = Store::where('oracle_store_sid', $oracleEmployee['REAL_STORE_SID'])->first();
            }

            // Si aún no tiene tienda, usar tienda por defecto
            if (!$store) {
                $store = $defaultStore;
            }
            
            if (!$store) {
                $this->warn("   Empleado {$oracleEmployee['USER_NAME']} sin tienda - OMITIDO");
                $skipped++;
                continue;
            }
            
            // Separar nombre completo
            $fullName = $oracleEmployee['FULL_NAME'] ?? $oracleEmployee['USER_NAME'];
            $nameParts = explode(' ', trim($fullName), 2);
            $firstname = $nameParts[0] ?? 'Sin nombre';
            $lastname = $nameParts[1] ?? '';
            
            // DETERMINAR CÓDIGO, DEPARTAMENTO Y PUESTO
            $empCode      = null;
            $departmentId = null;
            $designationId = null;

            if (isset($employeeMapping[$oracleEmployee['USER_NAME']])) {
                $mappedData = $employeeMapping[$oracleEmployee['USER_NAME']];

                // Buscar puesto
                $designation   = Designation::firstOrCreate(['name' => $mappedData['puesto']]);
                $designationId = $designation->id;

                // Si tiene departamento → ADMIN
                if ($mappedData['departamento'] !== null) {
                    $department   = Department::where('name', $mappedData['departamento'])->first();
                    $departmentId = $department?->id;

                    $lastA   = EmployeeDetail::where('emp_code', 'LIKE', 'A-%')
                        ->whereNotNull('emp_code')
                        ->orderByRaw("LENGTH(emp_code) DESC, emp_code DESC")
                        ->value('emp_code');
                    $nextA   = $lastA ? ((int) substr($lastA, 2)) + 1 : 1;
                    $empCode = 'A-' . str_pad($nextA, 2, '0', STR_PAD_LEFT);

                // Si no tiene departamento → TIENDA
                } else {
                    $storeNo = $store->oracle_store_no ?? $store->id;
                    $prefix  = "T-{$storeNo}-";

                    $lastT   = EmployeeDetail::where('emp_code', 'LIKE', "{$prefix}%")
                        ->whereNotNull('emp_code')
                        ->orderByRaw("LENGTH(emp_code) DESC, emp_code DESC")
                        ->value('emp_code');
                    $nextT   = $lastT ? ((int) substr($lastT, strlen($prefix))) + 1 : 1;
                    $empCode = $prefix . str_pad($nextT, 2, '0', STR_PAD_LEFT);
                }

                $withData++;
            } else {
                $empCode     = null;
                $withoutData++;
            }
            
            // Crear usuario
            $user = User::create([
                'email' => strtolower($oracleEmployee['USER_NAME']) . '@calzadoroy.com',
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $oracleEmployee['USER_NAME'],
                'type' => 'Employee',
                'password' => bcrypt('123456'),
                'is_active' => $oracleEmployee['USER_ACTIVE'] == '1',
            ]);
            
            // Crear empleado
            EmployeeDetail::create([
                'emp_code' => $empCode,
                'oracle_employee_id' => $oracleEmployee['SID'],
                'oracle_emp_code' => $oracleEmployee['USER_NAME'],
                'oracle_active' => $oracleEmployee['ACTIVE'] == '1',
                'user_id' => $user->id,
                'store_id' => $store->id,
                'department_id' => $departmentId,
                'designation_id' => $designationId,
                'date_joined' => $oracleEmployee['HIRE_DATE'],
                'status' => 'PENDIENTE',
            ]);
            
            $created++;
            $synced++;
        }
        
        $this->info("   === RESUMEN ===");
        $this->info("   Empleados nuevos creados: $created");
        $this->info("   Empleados existentes actualizados: $updated");
        $this->info("   Empleados marcados DAR_DE_BAJA: $markedInactive");
        $this->info("   Con datos completos: $withData");
        $this->info("   Sin datos (pendiente RH): $withoutData");
        
        if ($skipped > 0) {
            $this->warn("   $skipped empleados omitidos");
        }
    }
}