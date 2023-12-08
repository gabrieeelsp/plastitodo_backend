<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function paymentmethod()
    {
        return $this->belongsTo(Paymentmethod::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
