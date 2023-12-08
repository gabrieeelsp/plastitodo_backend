<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'direccion', 
        'punto_venta_fe',
        'telefono',
        'telefono_movil',
        'horario',
    ];

    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
