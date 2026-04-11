<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;

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
        Log::info('Sale@get_subtotal_iva - inicio', [
            'sale_id' => $this->id,
            'ivaaliquot_id' => $ivaaliquot_id
        ]);

        $subtotal_real = 0;

        foreach($this->saleitems as $item){

            Log::info('Sale@get_subtotal_iva - evaluando saleitem', [
                'sale_id' => $this->id,
                'saleitem_id' => $item->id,
                'ivaaliquot_item' => $item->ivaaliquot_id
            ]);

            if($item->ivaaliquot_id == $ivaaliquot_id){

                $subtotal_item = $item->get_subtotal_real();

                Log::info('Sale@get_subtotal_iva - subtotal item agregado', [
                    'saleitem_id' => $item->id,
                    'subtotal_item' => $subtotal_item
                ]);

                $subtotal_real = round(
                    $subtotal_real + $subtotal_item,
                    4,
                    PHP_ROUND_HALF_UP
                );

                Log::info('Sale@get_subtotal_iva - subtotal acumulado', [
                    'subtotal_real' => $subtotal_real
                ]);
            }
        }

        Log::info('Sale@get_subtotal_iva - subtotal acumulado', [
            'subtotal_real_acumulado' => $subtotal_real
        ]);

        foreach($this->creditnotes as $creditnote){

            $subtotal_creditnote = $creditnote->get_subtotal_segun_iva($ivaaliquot_id);

            Log::info('Sale@get_subtotal_iva - descontando creditnote', [
                'creditnote_id' => $creditnote->id,
                'subtotal_creditnote' => $subtotal_creditnote
            ]);

            $subtotal_real = round(
                $subtotal_real - $subtotal_creditnote,
                4,
                PHP_ROUND_HALF_UP
            );

            Log::info('Sale@get_subtotal_iva - subtotal luego de creditnote', [
                'subtotal_real' => $subtotal_real
            ]);
        }

        Log::info('Sale@get_subtotal_iva - resultado final', [
            'sale_id' => $this->id,
            'ivaaliquot_id' => $ivaaliquot_id,
            'subtotal_real' => $subtotal_real
        ]);

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
