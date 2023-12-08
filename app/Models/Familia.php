<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'comments',
    ];

    public $timestamps = false;
}
