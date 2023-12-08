<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comboitem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cantidad'
    ];

    public $timestamps = false;

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function is_configuration_ok () 
    {
        foreach ( $this->saleproducts as $saleproduct ) {
            if ( $saleproduct->pivot->is_enable ) {
                return true;
            }
        }
        return false;
    }

    public function saleproducts()
    {
        return $this->belongsToMany(Saleproduct::class)
            ->withPivot('is_enable');
    }

    public function hasSaleproduct($saleproduct_id)
    {
        foreach($this->saleproducts as $saleproduct){
            if($saleproduct->id == $saleproduct_id){
                return true;
            }
        }
        return false;
    }

    public function getPrecioMin()
    {
        $max_precio = 0;
        foreach($this->saleproducts as $saleproduct){
            if ( $saleproduct->pivot->is_enable ) {
                if($saleproduct->precio_min > $max_precio){
                    $max_precio = $saleproduct->precio_min;
                }
            }
        }
        return round($max_precio * $this->cantidad, 6, PHP_ROUND_HALF_UP);
    }

    public function getPrecioMay()
    {
        $max_precio = 0;
        foreach($this->saleproducts as $saleproduct){
            if ( $saleproduct->pivot->is_enable ) {
                if($saleproduct->precio_may > $max_precio){
                    $max_precio = $saleproduct->precio_may;
                }
            }
            
        }
        return round($max_precio * $this->cantidad, 6, PHP_ROUND_HALF_UP);
    }
}
