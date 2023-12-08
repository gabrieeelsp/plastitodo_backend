<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolutionitem extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function devolution()
    {
        return $this->belongsTo(Devolution::class);
    }

    public function saleitem()
    {
        return $this->belongsTo(Saleitem::class);
    }

    public function get_subtotal()
    {
        if($this->saleitem->saleproduct->stockproduct->is_stock_unitario_variable){
            return round($this->saleitem->precio * $this->cantidad_total, 4, PHP_ROUND_HALF_UP);
        }else {
            return round($this->saleitem->precio * $this->cantidad, 4, PHP_ROUND_HALF_UP);
        }
    }

    public function getImpNeto()
    {
        return round($this->get_subtotal() / (1 + round($this->saleitem->ivaaliquot->valor / 100, 4, PHP_ROUND_HALF_UP)), 4, PHP_ROUND_HALF_UP);
    }

    public function getImpIva ( ) 
    {
        return round($this->get_subtotal() - $this->getImpNeto(), 4, PHP_ROUND_HALF_UP);
    }
}
