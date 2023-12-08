<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockmovementitem extends Model
{
    use HasFactory;

    public $timestamps = False;

    public function stockmovement ()
    {
        return $this->belongsTo(Stockmovement::class);
    }

    public function stockproduct ()
    {
        return $this->belongsTo(Stockproduct::class);
    }
}
