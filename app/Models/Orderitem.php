<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function saleproduct()
    {
        return $this->belongsTo(Saleproduct::class);
    }

    
}
