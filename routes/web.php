<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\OracleHelper;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\AllowancesController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\DeductionsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\ChatAppController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\TicketsController;
use App\Http\Controllers\Admin\HolidaysController;
use App\Http\Controllers\Admin\PayrollsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\EmployeesController;
use App\Http\Controllers\Admin\FamilyInfoController;
use App\Http\Controllers\Admin\AttendancesController;
use App\Http\Controllers\Admin\DepartmentsController;
use App\Http\Controllers\Admin\DesignationsController;
use App\Http\Controllers\Admin\EmployeeDetailsController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\UniformeController;
use App\Http\Controllers\BoletaController;
use Illuminate\Support\Facades\Artisan;

include __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::any('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    Route::get('profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [UserProfileController::class, 'update']);
    Route::post('employees/sync', [EmployeesController::class, 'sync'])->name('employees.sync');
    Route::get('employees/{employee}/expediente', [EmployeesController::class, 'expediente'])->name('employees.expediente');
    Route::post('employees/{employee}/expediente', [EmployeesController::class, 'saveExpediente'])->name('employees.expediente.save');
    Route::post('employees/{employee}/horario-admin', [EmployeesController::class, 'saveHorarioAdmin'])->name('employees.horario.admin');
    Route::post('/backups/create', function() {
        $pgdump = 'C:\\Program Files\\PostgreSQL\\16\\bin\\pg_dump.exe';
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '5432');
        $user = env('DB_USERNAME', 'postgres');
        $pass = env('DB_PASSWORD', 'roy');
        $db   = env('DB_DATABASE', 'nomina');
        
        $date    = date('Y-m-d-H-i-s');
        $sqlFile = storage_path("app\\backup-temp\\{$date}.sql");
        $zipFile = storage_path("app\\Laravel\\{$date}.zip");
        
        // Crear directorios
        @mkdir(storage_path('app\\backup-temp'), 0755, true);
        @mkdir(storage_path('app\\Laravel'), 0755, true);
        
        // Hacer dump
        putenv("PGPASSWORD={$pass}");
        $cmd = "\"$pgdump\" -h {$host} -p {$port} -U {$user} -d {$db} -f \"{$sqlFile}\" 2>&1";
        shell_exec($cmd);
        
        if (!file_exists($sqlFile)) {
            return back()->with('error', 'Error al crear el dump SQL.');
        }
        
        // Crear ZIP
        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE);
        $zip->addFile($sqlFile, basename($sqlFile));
        $zip->close();
        
        // Limpiar SQL temporal
        unlink($sqlFile);
        
        return back()->with('success', 'Respaldo creado correctamente.');
    })->name('backups.create')->middleware('auth');

    Route::post('/backups/create-files', function() {
        Artisan::call('backup:run');
        return back()->with('success', 'Respaldo completo creado correctamente.');
    })->name('backups.create-files')->middleware('auth');

    Route::post('/backups/create-files', function() {
        Artisan::call('backup:run');
        return back()->with('success', 'Respaldo completo creado correctamente.');
    })->name('backups.create-files')->middleware('auth');

    Route::group(['prefix' => 'apps'], function(){
        Route::get('chat/{contact?}', [ChatAppController::class, 'index'])->name('app.chat');
        Route::delete('delete-chat/{receiver}', [ChatAppController::class, 'destroy'])->name('chat.delete-conversation');
    });

    Route::resource('users', UsersController::class);
    Route::resource('employees', EmployeesController::class);
    Route::resource('clients', ClientsController::class);
    Route::get('client-list', [ClientsController::class, 'list'])->name('clients.list');
    Route::get('employee/personal-info/{employeeDetail}', [EmployeeDetailsController::class, 'personalInfo'])->name('employee.personal-info');
    Route::post('employee/personal-info/{employeeDetail}', [EmployeeDetailsController::class, 'updatePersonalInfo']);
    Route::get('employee/emergency-contacts/{employeeDetail}', [EmployeeDetailsController::class, 'emergencyContacts'])->name('employee.emergency-contacts');
    Route::post('employee/emergency-contacts/{employeeDetail}', [EmployeeDetailsController::class, 'updateEmergencyContacts']);
    Route::get('employee/experience/{employeeDetail}', [EmployeeDetailsController::class, 'workExperience'])->name('employee.experience');
    Route::post('employee/experience/{employeeDetail}', [EmployeeDetailsController::class, 'updateWorkExperience']);
    Route::delete('delete-experience/{experience}', [EmployeeDetailsController::class, 'deleteWorkExperience'])->name('employee.experience.delete');
    Route::get('employee/education/{employeeDetail}', [EmployeeDetailsController::class, 'education'])->name('employee.education');
    Route::post('employee/education/{employeeDetail}', [EmployeeDetailsController::class, 'updateEducation']);
    Route::delete('del-employee-education', [EmployeeDetailsController::class, 'deleteEducation'])->name('employee.education.delete');
    Route::post('employee-salary-setting/{employeeDetail}', [EmployeeDetailsController::class, 'salarySetting'])->name('employee.salary-setting');
    Route::group(['prefix' => 'payroll'], function(){
        Route::get('items',[PayrollsController::class, 'items'])->name('payroll.items'); 
        Route::resource('allowances', AllowancesController::class)->except(['show']);
        Route::resource('deductions', DeductionsController::class)->except(['show']);
        Route::resource('payslips', PayrollsController::class);
    });

    Route::get('employees-list', [EmployeesController::class, 'list'])->name('employees.list');
    Route::resource('departments', DepartmentsController::class)->except(['show']);
    Route::resource('designations', DesignationsController::class)->except(['show']);
    Route::resource('holidays', HolidaysController::class);
    Route::get('holidays-calendar', [HolidaysController::class, 'calendar'])->name('holidays.calendar');
    Route::resource('family-information', FamilyInfoController::class);
    Route::resource('assets', AssetsController::class);
    Route::get('backups', fn() => view('pages.backups',[ 'pageTitle' => __('Backups')]))->name('backups.index');
    Route::get('attendance', [AttendancesController::class, 'index'])->name('attendances.index');
    Route::get('attendance-details/{attendance}', [AttendancesController::class, 'attendanceDetails'])->name('attendance.details');
    Route::resource('tickets', TicketsController::class);
    Route::get('assigned-tickets', [TicketsController::class, 'assignedTickets'])->name('assigned-tickets');
    Route::post('assign-ticket', [TicketsController::class, 'assignUser'])->name('ticket.assign-user');

    Route::get('app-logs', fn() => redirect()->to('log-viewer'))->name('app.logs');

    //settings
    Route::prefix('settings')->group(function () {
        Route::get('company', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('company', [SettingsController::class, 'updateCompany'])->name('settings.company.update');

        Route::get('locale', [SettingsController::class, 'locale'])->name('settings.locale');
        Route::post('locale', [SettingsController::class, 'updateLocale'])->name('settings.locale.update');
        Route::get('theme', [SettingsController::class, 'theme'])->name('settings.theme');
        Route::post('theme', [SettingsController::class, 'updateTheme'])->name('settings.theme.update');
        Route::get('invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
        Route::post('invoice', [SettingsController::class, 'updateInvoice'])->name('settings.invoice.update');
        Route::get('salary', [SettingsController::class, 'salary'])->name('settings.salary');
        Route::post('salary', [SettingsController::class, 'updateSalarySettings'])->name('settings.salary.update');
        Route::get('mail', [SettingsController::class, 'email'])->name('settings.mail');
        Route::post('mail', [SettingsController::class, 'updateEmail'])->name('settings.mail.update');
    });

    // Nómina
    Route::prefix('nomina')->name('nomina.')->middleware(['auth'])->group(function () {
        Route::get('/',                         [NominaController::class, 'index'])->name('index');
        Route::post('/generar',                 [NominaController::class, 'generar'])->name('generar');
        Route::get('/{nomina}',                 [NominaController::class, 'show'])->name('show');
        Route::post('/detalle/{detalle}',        [NominaController::class, 'updateDetalle'])->name('detalle.update');
        Route::post('/{nomina}/cerrar',         [NominaController::class, 'cerrar'])->name('cerrar');
        Route::delete('/{nomina}',              [NominaController::class, 'destroy'])->name('destroy');
        Route::get('/{nomina}/export-excel',    [NominaController::class, 'exportExcel'])->name('export.excel');
        Route::get('/{nomina}/export-pdf',      [NominaController::class, 'exportPdf'])->name('export.pdf');
    });

    // Módulo de Boletas
    Route::prefix('boletas')->name('boletas.')->group(function () {
        Route::get('/',           [BoletaController::class, 'index'])->name('index');
        Route::get('/tienda',     [BoletaController::class, 'misTienda'])->name('tienda');
        Route::get('/mis-recibos',[BoletaController::class, 'misRecibos'])->name('mis-recibos');
        Route::get('/{boleta}',   [BoletaController::class, 'ver'])->name('ver');
        Route::post('/firma/registrar', [BoletaController::class, 'registrarFirma'])->name('firma.registrar');
        Route::post('/{boleta}/firmar', [BoletaController::class, 'firmar'])->name('firmar');
        Route::get('/firma/verificar', [BoletaController::class, 'verificarFirma'])->name('firma.verificar');
    });
});

Route::prefix('uniformes')->name('uniformes.')->middleware(['auth'])->group(function () {
    Route::get('/',                     [UniformeController::class, 'index'])->name('index');
    Route::get('/crear',                [UniformeController::class, 'create'])->name('create');
    Route::post('/',                    [UniformeController::class, 'store'])->name('store');
    Route::post('/{uniforme}/anular',   [UniformeController::class, 'anular'])->name('anular');
});
// RUTA TEMPORAL PARA PROBAR ORACLE
Route::get('/test-oracle', function () {
    try {
        // Probar consulta a empleados
        $employees = OracleHelper::query("
            SELECT * FROM RPS.EMPLOYEE WHERE ROWNUM <= 2
        ");
        
        // Probar consulta a tiendas
        $stores = OracleHelper::query("
            SELECT * FROM RPS.STORE WHERE ROWNUM <= 2
        ");
        
        return response()->json([
            'status' => 'success',
            'message' => 'Conexión Oracle exitosa con oci_connect',
            'employees_count' => count($employees),
            'stores_count' => count($stores),
            'sample_employee' => $employees[0] ?? null,
            'sample_store' => $stores[0] ?? null,
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error en conexión Oracle',
            'error' => $e->getMessage(),
        ], 500);
    }
});