<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_enable',
        'is_enable_web'
    ];

    public $timestamps = false;

    public function comboitems()
    {
        return $this->hasMany(Comboitem::class);
    }

    public function getComboitem_from_saleproduct($saleproduct_id)
    {
        foreach ( $this->comboitems as $comboitem ) {
            if ( $comboitem->hasSaleproduct($saleproduct_id) ) {
                return $comboitem;
            }
        }
        return null;
    }

    public function getIvaaliquot()
    {
        return $this->comboitems->first()->saleproducts->first()->stockproduct->ivaaliquot;
    }

    public function setPrecios()
    {
        $precio_min = 0;
        $precio_may = 0;
        foreach($this->comboitems as $comboitem){
            $precio_min = round($precio_min + $comboitem->getPrecioMin(), 6, PHP_ROUND_HALF_UP);
            $precio_may = round($precio_may + $comboitem->getPrecioMay(), 6, PHP_ROUND_HALF_UP);
        }
        $this->precio_min = round($precio_min * round(1 - round($this->desc_min / 100, 4, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), $this->precision_min, PHP_ROUND_HALF_UP);
        $this->precio_may = round($precio_may * round(1 - round($this->desc_may / 100, 4, PHP_ROUND_HALF_UP), 8, PHP_ROUND_HALF_UP), $this->precision_may, PHP_ROUND_HALF_UP);

    }
}
