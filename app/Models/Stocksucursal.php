<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocksucursal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'stock',
        'stock_pedido',
        'stock_minimo',
        'stock_maximo',
        'stockproduct_id',
        'sucursal_id',
    ];

    public function stockproduct()
    {
        return $this->belongsTo(Stockproduct::class);
    }
    
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function get_stock_orders()
    {

        $orderitems = Order::select('orderitems.cantidad as cantidad', 'saleproducts.relacion_venta_stock as relacion_venta_stock')
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->where('orders.sucursal_id', '=', $this->sucursal_id)
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->stockproduct_id)
            ->get();
        $cant = 0;
        foreach ( $orderitems as $orderitem ) {
            $cant = $cant + $orderitem['cantidad'] * $orderitem['relacion_venta_stock'];
        }
        return $cant;
    }
}
