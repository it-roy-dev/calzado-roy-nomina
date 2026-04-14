<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uniforme extends Model
{
    protected $table = 'uniformes';

    protected $fillable = [
        'employee_detail_id',
        'fecha_entrega',
        'monto_total',
        'num_cuotas',
        'cuotas_pagadas',
        'monto_cuota',
        'saldo_pendiente',
        'estado',
        'descripcion',
        'created_by',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
        'monto_total' => 'decimal:2',
        'monto_cuota' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function empleado()
    {
        return $this->belongsTo(EmployeeDetail::class, 'employee_detail_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Cuotas pendientes de pagar
    public function cuotasPendientes(): int
    {
        return $this->num_cuotas - $this->cuotas_pagadas;
    }

    // Scope solo activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'ACTIVO');
    }
}