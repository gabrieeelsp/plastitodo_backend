<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocktransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_origen_id',
        'sucursal_destino_id',
    ];

    public function stocktransferitems ()
    {
        return $this->hasMany(Stocktransferitem::class);
    }

    public function sucursal_origen ()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_origen_id');
    }
    public function sucursal_destino ()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino_id');
    }

    public function user_origen ()
    {
        return $this->belongsTo(User::class, 'user_origen_id');
    }
    public function user_destino ()
    {
        return $this->belongsTo(User::class, 'user_destino_id');
    }
}
