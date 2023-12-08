<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolutioncomboitem extends Model
{
    use HasFactory;

    public function devolution()
    {
        return $this->belongsTo(Devolution::class);
    }

    public function devolutioncombosaleproducts()
    {
        return $this->hasMany(Devolutioncombosaleproduct::class);
    }
    
    public function salecomboitem()
    {
        return $this->belongsTo(Salecomboitem::class);
    }

    public function get_subtotal()
    {
        return round($this->salecomboitem->precio * $this->cantidad, 4, PHP_ROUND_HALF_UP);
    }

    public function getImpNeto()
    {
        return round($this->get_subtotal() / (1 + round($this->salecomboitem->ivaaliquot->valor / 100, 4, PHP_ROUND_HALF_UP)), 4, PHP_ROUND_HALF_UP);
    }

    public function getImpIva ( ) 
    {
        return round($this->get_subtotal() - $this->getImpNeto(), 4, PHP_ROUND_HALF_UP);
    }
    
}
