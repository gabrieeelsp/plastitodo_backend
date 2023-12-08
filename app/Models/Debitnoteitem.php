<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debitnoteitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function debitnote()
    {
        return $this->belongsTo(Debitnote::class);
    }

    public function ivaaliquot()
    {
        return $this->belongsTo(Ivaaliquot::class);
    }

    public function getImpNeto()
    {
        return round($this->valor / (1 + round($this->ivaaliquot->valor / 100, 4, PHP_ROUND_HALF_UP)), 4, PHP_ROUND_HALF_UP);
    }

    public function getImpIva ( ) 
    {
        return round($this->valor - $this->getImpNeto(), 4, PHP_ROUND_HALF_UP);
    }
}
