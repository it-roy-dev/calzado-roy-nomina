<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'name',
        'location',
        'description',
    ];

    /**
     * Relación con área
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con empleados
     */
    public function employees()
    {
        return $this->hasMany(EmployeeDetail::class);
    }
}