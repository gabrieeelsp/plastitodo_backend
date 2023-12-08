<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function saleitems()
    {
        return $this->hasMany(Saleitem::class);
    }

    public function salecomboitems()
    {
        return $this->hasMany(Salecomboitem::class);
    }

    public function comprobante()
    {
        return $this->morphOne(Comprobante::class, 'comprobanteable');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function devolutions()
    {
        return $this->hasMany(Devolution::class);
    }

    public function creditnotes()
    {
        return $this->hasMany(Creditnote::class);
    }

    public function debitnotes()
    {
        return $this->hasMany(Debitnote::class);
    }

    public function hasPaymentCash()
    {
        foreach($this->payments as $payment){
            if($payment->paymentmethod_id === 1){
                return true;
            }
        }
        return false;
    }

    public function getCondicionVenta()
    {
        return "Contado";
    }

    public function get_subtotal_real_segun_iva($ivaaliquot_id)
    {
        $subtotal_real = 0;
        foreach($this->saleitems as $item){
            if($item->ivaaliquot_id == $ivaaliquot_id){
                $subtotal_real = round($subtotal_real + $item->get_subtotal_real(), 4, PHP_ROUND_HALF_UP);
            }
            
        }

        foreach($this->creditnotes as $creditnote){
            $subtotal_real = round($subtotal_real - $creditnote->get_subtotal_segun_iva($ivaaliquot_id));
        }

        return $subtotal_real;
    }

    public function getBaseImpIva($ivaaliquot_id)
    {
        $baseImpIva = 0;
        foreach ( $this->saleItems as $saleItem ) {
            if ( $saleItem->ivaaliquot->id == $ivaaliquot_id ){
                $baseImpIva = round($baseImpIva + $saleItem->getImpNeto(), 2, PHP_ROUND_HALF_UP);
            }            
        }    

        foreach ( $this->salecomboitems as $salecomboitem) {
            if ( $salecomboitem->ivaaliquot->id == $ivaaliquot_id ){
                $baseImpIva = round($baseImpIva + $salecomboitem->getImpNeto(), 2, PHP_ROUND_HALF_UP);
            } 
        }     
        return $baseImpIva;
    }

    public function getImpIva($ivaaliquot_id) 
    {
        $impIva = 0;
        foreach ( $this->saleItems as $saleItem ) {
            if ( $saleItem->ivaaliquot_id == $ivaaliquot_id ){
                $impIva = round($impIva + $saleItem->getImpIva(), 2, PHP_ROUND_HALF_UP);
            }            
        } 
        foreach ( $this->salecomboitems as $salecomboitem) {
            if ( $salecomboitem->ivaaliquot_id == $ivaaliquot_id) {
                $impIva = round($impIva + $salecomboitem->getImpIva(), 2, PHP_ROUND_HALF_UP);
            }
        }
        return $impIva;
    }
}
