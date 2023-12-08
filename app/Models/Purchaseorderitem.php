<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchaseorderitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function purchaseorder ()
    {
        return $this->belongsTo(Purchaseorder::class);
    }

    public function purchaseproduct ()
    {
        return $this->belongsTo(Purchaseproduct::class);
    }
}
