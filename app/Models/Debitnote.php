<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debitnote extends Model
{
    use HasFactory;

    public function debitnoteitems()
    {
        return $this->hasMany(Debitnoteitem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function comprobante()
    {
        return $this->morphOne(Comprobante::class, 'comprobanteable');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function get_subtotal_segun_iva($ivaaliquot_id)
    {
        foreach($this->debitnoteitems as $item){
            if($item->ivaaliquot_id == $ivaaliquot_id){
                return $item->valor;
            }
        }
    }

    public function getBaseImpIva($ivaaliquot_id)
    {
        $baseImpIva = 0;
        foreach ( $this->debitnoteitems as $item ) {
            if ( $item->ivaaliquot_id == $ivaaliquot_id ){
                $baseImpIva = round($baseImpIva + $item->getImpNeto(), 2, PHP_ROUND_HALF_UP);
            }
            
        }         
        return $baseImpIva;
    }

    public function getImpIva($ivaaliquot_id) 
    {
        $impIva = 0;
        foreach ( $this->debitnoteitems as $item ) {
            if ( $item->ivaaliquot_id == $ivaaliquot_id ){
                $impIva = round($impIva + $item->getImpIva(), 2, PHP_ROUND_HALF_UP);
            }  
        } 
        return $impIva;
    }
}
