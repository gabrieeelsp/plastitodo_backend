<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function orderitems()
    {
        return $this->hasMany(Orderitem::class);
    }

    public function ordercomboitems()
    {
        return $this->hasMany(Ordercomboitem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function deliveryshift()
    {
        return $this->belongsTo(Deliveryshift::class);
    }

    public function ivacondition()
    {
        return $this->belongsTo(Ivacondition::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
