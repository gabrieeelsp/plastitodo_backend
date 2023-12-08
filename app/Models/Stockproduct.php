<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order;

use Illuminate\Support\Facades\DB;

class Stockproduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'costo',
        'is_stock_unitario_variable',
        'stock_aproximado_unidad',
        'ivaaliquot_id',
    ];

    public $timestamps = false;

    public function ivaaliquot()
    {
        return $this->belongsTo(Ivaaliquot::class);
    }

    public function stocksucursals ()
    {
        return $this->hasMany(Stocksucursal::class);
    }

    public function stockSucursales()
    {
        return $this->hasMany(Stocksucursal::class);
    }

    public function getStockSucursal($sucursal_id) {
        
        foreach ( $this->stockSucursales as $stockSucursal) {
            if ( $sucursal_id == $stockSucursal->sucursal_id ) {
                return $stockSucursal->stock;
            }
            
        }
        return 0;
    }

    public function getStockTotal() {
        $stock = 0;
        foreach ( $this->stockSucursales as $stockSucursal) {
            $stock = $stock + $stockSucursal->stock;
        }
        return round($stock, 4, PHP_ROUND_HALF_UP);
    }

    public function saleproducts() 
    {
        return $this->hasMany(Saleproduct::class)->orderBy('name');
    }
    
    public function stockproductgroup() 
    {
        return $this->belongsTo(Stockproductgroup::class);
    }

    public function purchaseproducts ()
    {
        return $this->hasMany(Purchaseproduct::class);
    }

    public function familia() 
    {
        return $this->belongsTo(Familia::class);
    }

    public function get_stock_orders()
    {
        $cant = DB::table('orders')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->where('orderitems.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get();

        $cant_combo = DB::table('orders')
            ->selectRaw('(SUM(ordercombosaleproducts.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    

            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])

            ->join('ordercomboitems', 'ordercomboitems.order_id', '=', 'orders.id')
            ->join('ordercombosaleproducts', 'ordercombosaleproducts.ordercomboitem_id', '=', 'ordercomboitems.id')
            ->where('ordercombosaleproducts.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'ordercombosaleproducts.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get();
        

/*         $cant = DB::table('orderitems')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')
            ->join('orders', 'orderitems.order_id', '=', 'orders.id')
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get(); */
        $cant_total = 0;
        if ( $cant->count() ) {
            $cant_total = $cant->first()->cantidad;
        }
        if ( $cant_combo->count() ) {
            $cant_total = $cant_total + $cant_combo->first()->cantidad;
        }
        return $cant_total;
        
    }

    public function get_stock_orders_by_sucursal( $sucursal_id )
    {
        $cant = DB::table('orders')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->where('orders.sucursal_id', '=', $sucursal_id)
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->where('orderitems.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get();

        $cant_combo = DB::table('orders')
            ->selectRaw('(SUM(ordercombosaleproducts.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    

            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->where('orders.sucursal_id', '=', $sucursal_id)
            ->join('ordercomboitems', 'ordercomboitems.order_id', '=', 'orders.id')
            ->join('ordercombosaleproducts', 'ordercombosaleproducts.ordercomboitem_id', '=', 'ordercomboitems.id')
            ->where('ordercombosaleproducts.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'ordercombosaleproducts.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get();
        

/*         $cant = DB::table('orderitems')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')
            ->join('orders', 'orderitems.order_id', '=', 'orders.id')
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get(); */
        $cant_total = 0;
        if ( $cant->count() ) {
            $cant_total = $cant->first()->cantidad;
        }
        if ( $cant_combo->count() ) {
            $cant_total = $cant_total + $cant_combo->first()->cantidad;
        }
        return $cant_total;
        
    }
    /* public function get_stock_orders()
    {

        $orderitems = Order::select('orderitems.cantidad as cantidad', 'saleproducts.relacion_venta_stock as relacion_venta_stock')
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $this->id)->get();
        $cant = 0;
        foreach ( $orderitems as $orderitem ) {
            $cant = $cant + $orderitem['cantidad'] * $orderitem['relacion_venta_stock'];
        }
        return $cant;
    } */
}
