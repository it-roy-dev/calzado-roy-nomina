<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    protected $table = 'boletas';

    protected $fillable = [
        'nomina_id',
        'nomina_detalle_id',
        'employee_detail_id',
        'numero_correlativo',
        'tipo',
        'pdf_path',
        'pdf_firmado_path',
        'estado',
        'firmada_at',
        'firmada_by',
    ];

    protected $casts = [
        'firmada_at' => 'datetime',
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function detalle()
    {
        return $this->belongsTo(NominaDetalle::class, 'nomina_detalle_id');
    }

    public function empleado()
    {
        return $this->belongsTo(EmployeeDetail::class, 'employee_detail_id');
    }

    public function firmadaPor()
    {
        return $this->belongsTo(User::class, 'firmada_by');
    }

    public function esFirmada(): bool
    {
        return $this->estado === 'FIRMADA';
    }
}