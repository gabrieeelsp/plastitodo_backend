<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolutioncombosaleproduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function salecombosaleproduct()
    {
        return $this->belongsTo(Salecombosaleproduct::class);
    }
}
