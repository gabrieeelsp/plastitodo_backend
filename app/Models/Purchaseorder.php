<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchaseorder extends Model
{
    use HasFactory;

    public function purchaseorderitems ()
    {
        return $this->hasMany(Purchaseorderitem::class);
    }

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier ()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function sucursal ()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
