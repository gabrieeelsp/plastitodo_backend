<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchaseproduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relacion_compra_stock',
        'is_enable',
        'codigo',
        'rel_precio_codigo',
    ];

    public $timestamps = false;

    public function stockproduct ()
    {
        return $this->belongsTo(Stockproduct::class);
    }

    public function supplier ()
    {
        return $this->belongsTo(Supplier::class);
    }
}
