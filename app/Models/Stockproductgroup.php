<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockproductgroup extends Model
{
    use HasFactory;

    public $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function stockproducts()
    {
        return $this->hasMany(Stockproduct::class);
    }
}
