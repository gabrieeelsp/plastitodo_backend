<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devolution extends Model
{
    use HasFactory;

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function devolutionitems()
    {
        return $this->hasMany(Devolutionitem::class);
    }

    public function devolutioncomboitems()
    {
        return $this->hasMany(Devolutioncomboitem::class);
    }

    public function comprobante()
    {
        return $this->morphOne(Comprobante::class, 'comprobanteable');
    }

    public function getBaseImpIva($ivaaliquot_id)
    {
        $baseImpIva = 0;
        foreach ( $this->devolutionitems as $devItem ) {
            if ( $devItem->saleitem->ivaaliquot->id == $ivaaliquot_id ){
                $baseImpIva = round($baseImpIva + $devItem->getImpNeto(), 2, PHP_ROUND_HALF_UP);
            }            
        }  
        
        foreach ( $this->devolutioncomboitems as $devcomboitem ) {
            if ( $devcomboitem->salecomboitem->ivaaliquot_id == $ivaaliquot_id ) {
                $baseImpIva = round($baseImpIva + $devcomboitem->getImpNeto(), 2, PHP_ROUND_HALF_UP);
            }
        }
        return $baseImpIva;
    }

    public function getImpIva($ivaaliquot_id) 
    {
        $impIva = 0;
        foreach ( $this->devolutionitems as $devItem ) {
            if ( $devItem->saleitem->ivaaliquot_id == $ivaaliquot_id ){
                $impIva = round($impIva + $devItem->getImpIva(), 2, PHP_ROUND_HALF_UP);
            }            
        } 

        foreach ( $this->devolutioncomboitems as $devcomboitem ) {
            if ( $devcomboitem->salecomboitem->ivaaliquot_id == $ivaaliquot_id ) {
                $impIva = round($impIva + $devcomboitem->getImpIva(), 2, PHP_ROUND_HALF_UP);
            }
        }
        return $impIva;
    }

}
