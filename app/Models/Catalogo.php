<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        'name',
        'color',
	'key',
        'comments',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function saleproducts()
    {
        return $this->belongsToMany(Saleproduct::class);
    }
}
