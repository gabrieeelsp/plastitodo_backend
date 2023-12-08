<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saleitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleproduct()
    {
        return $this->belongsTo(Saleproduct::class);
    }

    public function ivaaliquot()
    {
        return $this->belongsTo(Ivaaliquot::class);
    }

    public function devolutionitems()
    {
        return $this->hasMany(Devolutionitem::class);
    }

    public function get_cant_disponible_devolucion()
    {
        $cant = $this->cantidad;
        foreach($this->devolutionitems as $devitem){
            $cant = round($cant - $devitem->cantidad, 4, PHP_ROUND_HALF_UP);
        }
        return $cant;
    }

    public function get_cant_total_disponible_devolucion () {
        $cant = $this->cantidad_total;
        foreach ( $this->devolutionitems as $devitem ) {
            $cant = $cant - $devitem->cantidad_total;
        }
        return $cant;
    }

    public function get_subtotal() //descontando devoluciones
    {
        if($this->saleproduct->stockproduct->is_stock_unitario_variable){
            return round((($this->precio / $this->saleproduct->stockproduct->stock_aproximado_unidad ) / $this->saleproduct->relacion_venta_stock ) * $this->cantidad_total, 4, PHP_ROUND_HALF_UP);
        }else {
            return round($this->precio * $this->cantidad, 4, PHP_ROUND_HALF_UP);
        }
    }

    public function get_subtotal_real()
    {
        $subtotal_real = $this->get_subtotal();
        foreach($this->devolutionitems as $devitem){
            $subtotal_real = round($subtotal_real - $devitem->get_subtotal(), 4, PHP_ROUND_HALF_UP);
        }
        return $subtotal_real;
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
