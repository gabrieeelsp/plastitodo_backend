<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditnoteitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function creditnote()
    {
        return $this->belongsTo(Creditnote::class);
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
