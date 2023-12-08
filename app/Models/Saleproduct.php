<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saleproduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relacion_venta_stock',
        'is_enable',
        'porc_min',
        'porc_may',
        'barcode',
        'is_enable_web',
        'comments',
        'precision_min',
        'precision_may'
    ];

    public $timestamps = false;

    public function stockproduct()
    {
        return $this->belongsTo(Stockproduct::class);
    }

    public function getCosto()
    {   
        return round($this->stockproduct->costo * $this->relacion_venta_stock, 8, PHP_ROUND_HALF_UP);
    }

    public function getPrecioMin()
    {   
        return round($this->getCosto() * round(1 + round($this->porc_min / 100, 4, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), 4, PHP_ROUND_HALF_UP);
    }

    public function getPrecioMay()
    {   
        return round($this->getCosto() * round(1 + round($this->porc_may / 100, 8, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), 4, PHP_ROUND_HALF_UP);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function catalogos()
    {
        return $this->belongsToMany(Catalogo::class);
    }

    public function saleproductgroup() 
    {
        return $this->belongsTo(Saleproductgroup::class);
    }
    public function comboitems()
    {
        return $this->belongsToMany(Comboitem::class)
            ->withPivot('is_enable');
    }

    public function set_precios ( $costo_base )
    {
        if ( $this->stockproduct->is_stock_unitario_variable ) {
            $costo = round($costo_base * $this->relacion_venta_stock * $this->stockproduct->stock_aproximado_unidad, 8, PHP_ROUND_HALF_UP);
        }else {
            $costo = round($costo_base * $this->relacion_venta_stock, 8, PHP_ROUND_HALF_UP);
        }
        $this->precio_min = round($costo * round(1 + round($this->porc_min / 100, 4, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), $this->precision_min, PHP_ROUND_HALF_UP);

        $this->precio_may = round($costo * round(1 + round($this->porc_may / 100, 8, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), $this->precision_may, PHP_ROUND_HALF_UP);

        $this->precio_min_desc = round($this->precio_min * round(1 - round($this->porc_min_desc / 100, 4, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), $this->presicion_min, PHP_ROUND_HALF_UP);

        $this->precio_may_desc = round($this->precio_may * round(1 - round($this->porc_may_desc / 100, 4, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), $this->presicion_may, PHP_ROUND_HALF_UP);
    }
}
