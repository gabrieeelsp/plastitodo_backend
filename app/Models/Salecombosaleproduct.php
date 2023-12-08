<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salecombosaleproduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function saleproduct()
    {
        return $this->belongsTo(Saleproduct::class);
    }
    public function salecomboitem()
    {
        return $this->belongsTo(Salecomboitem::class);
    }

    public function devolutioncombosaleproducts()
    {
        return $this->hasMany(Devolutioncombosaleproduct::class);
    }

    public function get_cant_disponible_devolucion()
    {
        $cant = $this->cantidad;
        foreach($this->devolutioncombosaleproducts as $devolutioncombosaleproduct){
            $cant = $cant - $devolutioncombosaleproduct->cantidad;
        }
        return $cant;
    }
}
