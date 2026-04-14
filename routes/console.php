<?php

use App\Jobs\AutoClockoutUnsignedAttendances;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::job(new AutoClockoutUnsignedAttendances())->cron("0 */8 * * *");
Schedule::command('horarios:sincronizar')->dailyAt('02:00');

// Respaldo automático diario de BD a la 1:00am
Schedule::command('backup:run --only-db')->dailyAt('01:00');

// Respaldo completo semanal los domingos a las 2:00am
Schedule::command('backup:run')->weeklyOn(0, '02:00');

// Limpiar respaldos viejos diario a la 1:30am
Schedule::command('backup:clean')->dailyAt('01:30');