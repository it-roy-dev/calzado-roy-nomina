<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirmaDigital extends Model
{
    protected $table = 'firma_digital';

    protected $fillable = [
        'user_id',
        'firma_svg',
        'nombre_firmante',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}