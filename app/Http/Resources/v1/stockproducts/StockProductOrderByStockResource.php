<?php

namespace App\Http\Resources\v1\stockproducts;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Stocksucursal;

use Illuminate\Support\Facades\DB;

class StockProductOrderByStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => 'stockproducts',
            'attributes' => [
                'name' => $this->name,
                'costo' => $this->costo,
                'is_stock_unitario_variable' => $this->is_stock_unitario_variable,
                'stock_aproximado_unidad' => $this->stock_aproximado_unidad,
                'stock' => $this->stock,
                'stock_minimo' => $this->stock_minimo,
                'stock_relativo' => $this->stock_relativo,
                'stock_pedidos' => $this->stock_pedido,

                'image'     => $this->image ? asset($this->image) : null,
            ],
            'relationships' => [
                'stocksucursals' => StockproductStockSucursalResource::collection(Stocksucursal::where('stockproduct_id', '=', $this->id)->get()),
            ]
        ];
    }

    public function get_stock_orders_by_sucursal( $sucursal_id, $stockproduct_id )
    {
        $cant = DB::table('orders')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->where('orders.sucursal_id', '=', $sucursal_id)
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->where('orderitems.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $stockproduct_id)
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
            ->where('saleproducts.stockproduct_id', '=', $stockproduct_id)
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

    public function get_stock_orders($stockproduct_id)
    {
        $cant = DB::table('orders')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->where('orderitems.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $stockproduct_id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get();

        $cant_combo = DB::table('orders')
            ->selectRaw('(SUM(ordercombosaleproducts.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    

            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])

            ->join('ordercomboitems', 'ordercomboitems.order_id', '=', 'orders.id')
            ->join('ordercombosaleproducts', 'ordercombosaleproducts.ordercomboitem_id', '=', 'ordercomboitems.id')
            ->where('ordercombosaleproducts.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'ordercombosaleproducts.saleproduct_id')
            ->where('saleproducts.stockproduct_id', '=', $stockproduct_id)
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
}
