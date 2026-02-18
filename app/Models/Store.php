<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'oracle_store_sid',
        'oracle_store_no',
        'oracle_store_code',
        'code',
        'name',
        'type',
        'address',
        'phone',
        'activation_date',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'activation_date' => 'date',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Relación con empleados
     */
    public function employees()
    {
        return $this->hasMany(EmployeeDetail::class);
    }
}