<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordercomboitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function ordercombosaleproducts()
    {
        return $this->hasMany(Ordercombosaleproduct::class);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }
}
