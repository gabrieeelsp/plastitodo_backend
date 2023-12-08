<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salecomboitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function salecombosaleproducts()
    {   
        return $this->hasMany(Salecombosaleproduct::class);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function ivaaliquot()
    {
        return $this->belongsTo(Ivaaliquot::class);
    }

    public function devolutioncomboitems()
    {
        return $this->hasMany(Devolutioncomboitem::class);
    }

    public function get_cant_disponible_devolucion() { 
        $cant = $this->cantidad;
        foreach ( $this->devolutioncomboitems as $devolutioncomboitem ) {
            $cant = $cant - $devolutioncomboitem->cantidad;
        }
        return $cant;
    }

    public function get_subtotal() //descontando devoluciones
    {
        return round($this->precio * $this->cantidad, 4, PHP_ROUND_HALF_UP);
    }

    public function getImpNeto()
    {  
        return round($this->get_subtotal() / (1 + round($this->ivaaliquot->valor / 100, 4, PHP_ROUND_HALF_UP)), 4, PHP_ROUND_HALF_UP);
    }

    public function getImpIva ( ) 
    {
        return round($this->get_subtotal() - $this->getImpNeto(), 4, PHP_ROUND_HALF_UP);
    }
    
}
