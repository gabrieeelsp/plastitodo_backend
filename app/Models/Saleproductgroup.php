<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saleproductgroup extends Model
{
    use HasFactory;

    public $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function saleproducts()
    {
        return $this->hasMany(Saleproduct::class);
    }
}
