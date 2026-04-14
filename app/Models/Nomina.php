<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Nomina extends Model
{
    protected $table = 'nominas';

    protected $fillable = [
        'numero_planilla',
        'mes',
        'anio',
        'tipo',
        'fecha_pago',
        'fecha_inicio_periodo',
        'fecha_fin_periodo',
        'estado',
        'total_devengado',
        'total_deducciones',
        'total_liquido',
        'total_empleados',
        'observaciones',
        'created_by',
        'cerrada_by',
        'cerrada_at',
    ];

    protected $casts = [
        'fecha_pago'            => 'date',
        'fecha_inicio_periodo'  => 'date',
        'fecha_fin_periodo'     => 'date',
        'cerrada_at'            => 'datetime',
        'total_devengado'       => 'decimal:2',
        'total_deducciones'     => 'decimal:2',
        'total_liquido'         => 'decimal:2',
    ];

    public function detalle()
    {
        return $this->hasMany(NominaDetalle::class, 'nomina_id');
    }

    public function anticipo()
    {
        return $this->hasOne(NominaDetalle::class, 'nomina_id')
                    ->where('tipo', 'PRIMERA_QUINCENA');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cerradaPor()
    {
        return $this->belongsTo(User::class, 'cerrada_by');
    }

    // Nombre del mes en español
    public function getNombreMesAttribute(): string
    {
        $meses = [
            1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril',
            5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
            9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre'
        ];
        return $meses[$this->mes] ?? '';
    }

    // Título completo para mostrar
    public function getTituloAttribute(): string
    {
        $tipo = $this->tipo === 'PRIMERA_QUINCENA' ? 'Primera Quincena' : 'Segunda Quincena';
        return "Nómina {$tipo} {$this->nombre_mes} {$this->anio}";
    }

    // Siguiente número de planilla
    public static function siguienteNumero(): int
    {
        $ultimo = self::max('numero_planilla');
        return ($ultimo ?? 929) + 1;
    }

    // Calcular fecha de pago respetando fines de semana
    public static function calcularFechaPago(int $dia, int $mes, int $anio): Carbon
    {
        $fecha = Carbon::create($anio, $mes, $dia);
        // Si cae sábado (6) → viernes anterior
        if ($fecha->dayOfWeek === Carbon::SATURDAY) {
            $fecha->subDay();
        }
        // Si cae domingo (0) → viernes anterior
        elseif ($fecha->dayOfWeek === Carbon::SUNDAY) {
            $fecha->subDays(2);
        }
        return $fecha;
    }

    public function scopeBorradores($query)
    {
        return $query->where('estado', 'BORRADOR');
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado', 'CERRADA');
    }
}