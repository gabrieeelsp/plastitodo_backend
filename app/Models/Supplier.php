<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'direccion',
        'telefono',
        'telefono_movil',
        'email',
        'comments',
    ];

    public $timestamps = false;

    public function purchaseproducts ()
    {
        return $this->hasMany(Purchaseproduct::class)->orderBy('name');
    }
}
