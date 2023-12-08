<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Ordercomboitem;
use App\Models\Ordercombosaleproduct;
use App\Models\Combo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Http\Resources\v1\orders\OrderResource;
use App\Http\Resources\v1\orders\orderlist\OrderListResource;

use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $atr = [];

        $client_id = null;
        if ( $request->has('client_id')){
            array_push($atr, ['client_id', '=', $request->get('client_id')] );
        }

        $date_from = null;
        $date_to = null;
        if ( $request->has('date_from') ) {
            $date_from = $request->get('date_from');
            if ( $request->has('date_to' )) {
                $date_to = $request->get('date_to');
            }else {
                $date_to = $request->get('date_from');
            }
        }

        // date_from----
        if ( $date_from ){

            $orders = Order::orderBy('id', 'DESC')
                ->where($atr)
                ->whereBetween('created_at', [$date_from, $date_to . ' 23:59:59'])
                ->paginate($limit);
            return OrderListResource::collection($orders);
        }

        // sin date_ftom-------
        $orders = Order::orderBy('id', 'DESC')
            ->where($atr)
            ->where($atr)
            ->paginate($limit);
        return OrderListResource::collection($orders);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $is_finalizar = $request->get('is_finalizar');
        try {
            DB::beginTransaction();
            $order = Order::create();

            $order->user()->associate(auth()->user());

            if ( $request->has('sucursal_id')){
                $order->sucursal()->associate($request->get('sucursal_id'));
            }

            if($request->has('client_id')){
                $order->client()->associate($request->get('client_id'));
            }

            if($request->has('fecha_entrega_acordada')){
                $order->fecha_entrega_acordada = Carbon::createFromFormat('d-m-Y', $request->get('fecha_entrega_acordada'))->format('Y-m-d');
            }

            if($request->has('deliveryshift_id')){
                $order->deliveryshift()->associate($request->get('deliveryshift_id'));
            }

            if($request->has('ivacondition_id')){
                $order->ivacondition()->associate($request->get('ivacondition_id'));
            }

            if ( $request->has('is_delivery') && boolval($request->get('is_delivery'))) {
                $order->is_delivery = true;
            }else {
                $order->is_delivery = false;
            }

            $items = $request->get('items');
            foreach($items as $item){
                $orderItem = new Orderitem;

                $orderItem->order()->associate($order);
                $orderItem->saleproduct()->associate($item['saleproduct_id']);
                $orderItem->precio = $item['precio'];
                $orderItem->cantidad = $item['cantidad'];

                if($orderItem->saleproduct->stockproduct->is_stock_unitario_variable){
                    $orderItem->cantidad_total = $item['cantidad_total'];
                }

                $orderItem->save();
            }

            $comboitems = $request->get('comboitems');
            foreach($comboitems as $comboitem){
                $combo = Combo::find($comboitem['combo_id']);

                $ordercomboitem = new Ordercomboitem;
                $ordercomboitem->order_id = $order->id;
                $ordercomboitem->precio = $comboitem['precio'];
                $ordercomboitem->combo_id = $comboitem['combo_id'];
                $ordercomboitem->cantidad = $comboitem['cantidad'];

                $ordercomboitem->save();
                
                
                foreach($comboitem['comboitems'] as $combo_item_order) {

                    foreach($combo_item_order['saleproducts'] as $saleproduct_order){

                        $ordercombosaleproduct = new Ordercombosaleproduct;
                        $ordercombosaleproduct->cantidad = $saleproduct_order['cantidad'];
                        $ordercombosaleproduct->saleproduct()->associate($saleproduct_order['saleproduct_id']);
                        $ordercombosaleproduct->ordercomboitem()->associate($ordercomboitem->id);

                        $ordercombosaleproduct->save();
                    }
                    
                }



                $ordercomboitem->save();
            }

            $order->state = "EDITANDO";
            if ( $is_finalizar ) {
                $order->state = 'FINALIZADO';
            }
            $order->save();
            usleep(500000);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function set_event ( Request $request, $order_id ) {
        $order = Order::findOrFail($order_id);
        $event = $request->get('data')['event'];

        try {
            DB::beginTransaction();


            if ( $event == 'GUARDAR' && $order->state == 'EDITANDO' ) {
                // validar
                $order->state = 'EN PREPARACION';
            }

            if ( $state_nuevo == 'PREPARADO' ) {
                // validar
                // actualizar stock
                $order->state = 'PREPARADO';
            }

            if ( $state_nuevo == 'EDITANDO' ) {
                // validar
                // devolver stock
                $order->state = 'EDITANDO';
            }

            if ( $state_nuevo == 'FINALIZADO' ) {
                // validar
                $order->state = 'FINALIZADO';
            }

            if ( $state_nuevo == 'CONFIRMADO' ) {
                // validar

                if ( $order->state == 'EN PREPARACION' ) {
                    foreach ( $order->orderitems as $orderitem ) {
                        $orderitem->is_prepared = false;
                        $orderitem->save();
                    }
    
                    foreach ( $order->ordercomboitems as $ordercomboitem ) {
                        foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                            $ordercombosaleproduct->is_prepared = false;
                            $ordercombosaleproduct->save();
                        }
                    }
                }

                $order->state = 'CONFIRMADO';
            }

            $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new OrderResource($order);
    }
    public function set_state ( Request $request, $order_id ) {
        
        $order = Order::findOrFail($order_id);
        $state_nuevo = $request->get('data')['state'];

        try {
            DB::beginTransaction();


            if ( $state_nuevo == 'EN PREPARACION' ) {
                // validar
                $order->state = 'EN PREPARACION';
            }

            if ( $state_nuevo == 'PREPARADO' ) {
                // validar
                // actualizar stock

                foreach($order->orderitems as $orderitem){
                    if ( !$orderitem->is_prepared ) {
                        //tomar stock

                        $orderitem->is_prepared = true;
                        $orderitem->save();
                    }
                }

                foreach( $order->ordercomboitems as $ordercomboitem ){  
                    foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                        if ( !$ordercombosaleproduct->is_prepared ) {
                            //tomar stock
                        }
                        $ordercombosaleproduct->is_prepared = true;
                        $ordercombosaleproduct->save();
                    }
                   
                }                 

                $order->state = 'PREPARADO';
            }

            if ( $state_nuevo == 'EDITANDO' ) {
                // validar
                // devolver stock
                if ( $order->state == 'EN PREPARACION' ) {
                    foreach ( $order->orderitems as $orderitem ) {
                        $orderitem->is_prepared = false;
                        $orderitem->save();

                        //devolver stock
                    }
    
                    foreach ( $order->ordercomboitems as $ordercomboitem ) {
                        foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                            $ordercombosaleproduct->is_prepared = false;
                            $ordercombosaleproduct->save();

                            //devolver stock
                        }
                    }
                }
                $order->state = 'EDITANDO';
            }

            if ( $state_nuevo == 'FINALIZADO' ) {
                // validar
                $order->state = 'FINALIZADO';
            }

            if ( $state_nuevo == 'CONFIRMADO' ) {
                // validar

                

                $order->state = 'CONFIRMADO';
            }

            $order->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new OrderResource($order);
    }
    public function update(Request $request, Order $order)
    {
        $is_finalizar = $request->get('is_finalizar');
        try {
            DB::beginTransaction();
            //$order = Order::create();

            //$order->user()->associate(auth()->user());

            if ( $request->has('sucursal_id')){
                $order->sucursal()->associate($request->get('sucursal_id'));
            }else {
                $order->sucursal_id = null;
            }

            if($request->has('fecha_entrega_acordada')){
                //return 'seee';
                $order->fecha_entrega_acordada = Carbon::createFromFormat('d-m-Y', $request->get('fecha_entrega_acordada'));
                //$order->fecha_entrega_acordada = Carbon::now();
                //return Carbon::now();
            }else {
                $order->fecha_entrega_acordada = null;
            }

            if($request->has('deliveryshift_id')){
                $order->deliveryshift()->associate($request->get('deliveryshift_id'));
            }else {
                $order->deliveryshift_id = null;
            }

            if ( $request->has('is_delivery') && boolval($request->get('is_delivery'))) {
                $order->is_delivery = true;
            }else {
                $order->is_delivery = false;
            }

            if($request->has('ivacondition_id')){
                $order->ivacondition()->associate($request->get('ivacondition_id'));
            }else {
                $order->ivacondition_id = null;
            }

            if ( $order->state == 'EDITANDO' ) {
                $order->orderitems()->delete();

                $items = $request->get('items');
                foreach($items as $item){
                    $orderItem = new Orderitem;

                    $orderItem->order()->associate($order);
                    $orderItem->saleproduct()->associate($item['saleproduct_id']);
                    $orderItem->precio = $item['precio'];
                    $orderItem->cantidad = $item['cantidad'];

                    if($orderItem->saleproduct->stockproduct->is_stock_unitario_variable){
                        $orderItem->cantidad_total = $item['cantidad_total'];
                    }

                    $orderItem->save();
                }

                $order->ordercomboitems()->delete();

                $comboitems = $request->get('comboitems');
                foreach($comboitems as $comboitem){
                    $combo = Combo::find($comboitem['combo_id']);

                    $ordercomboitem = new Ordercomboitem;
                    $ordercomboitem->order_id = $order->id;
                    $ordercomboitem->precio = $comboitem['precio'];
                    $ordercomboitem->combo_id = $comboitem['combo_id'];
                    $ordercomboitem->cantidad = $comboitem['cantidad'];

                    $ordercomboitem->save();
                    
                    
                    foreach($comboitem['comboitems'] as $combo_item_order) {

                        foreach($combo_item_order['saleproducts'] as $saleproduct_order){

                            $ordercombosaleproduct = new Ordercombosaleproduct;
                            $ordercombosaleproduct->cantidad = $saleproduct_order['cantidad'];
                            $ordercombosaleproduct->saleproduct()->associate($saleproduct_order['saleproduct_id']);
                            $ordercombosaleproduct->ordercomboitem()->associate($ordercomboitem->id);

                            $ordercombosaleproduct->save();
                        }
                        
                    }



                    $ordercomboitem->save();
                }
            } elseif ( $order->state == 'EN PREPARACION' ) {
                $items = $request->get('items');
                foreach($items as $item){

                    foreach ( $order->orderitems as $orderitem ) {
                        if ( $orderitem->saleproduct_id == $item['saleproduct_id'] ) {

                            if ( $orderitem->is_prepared && !boolval($item['is_prepared']) ) {
                                //devolver stock
                            }

                            if ( !$orderitem->is_prepared && boolval($item['is_prepared']) ) {
                                //tomar stock
                            }

                            $orderitem->is_prepared = boolval($item['is_prepared']);
                            $orderitem->save();
                        }
                    }
                }
                $comboitems = $request->get('comboitems');
                foreach($comboitems as $comboitem){                    
                    
                    foreach($comboitem['comboitems'] as $combo_item_order) {
                        
                        foreach($combo_item_order['saleproducts'] as $saleproduct_order){
                            
                            foreach ( $order->ordercomboitems as $ordercomboitem ) {
                                
                                if ( $ordercomboitem->combo_id == $comboitem['combo_id'] ) {
                                    foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                                        //return $ordercombosaleproduct;
                                        if ( $ordercombosaleproduct->saleproduct_id == $saleproduct_order['saleproduct_id'] ) {
                                            if ( $ordercombosaleproduct->is_prepared && !boolval($saleproduct_order['is_prepared']) ) {
                                                //devolver stock
                                            }
                
                                            if ( !$ordercombosaleproduct->is_prepared && boolval($saleproduct_order['is_prepared']) ) {
                                                //tomar stock
                                            }
                                            $ordercombosaleproduct->is_prepared = $saleproduct_order['is_prepared'];
                                            $ordercombosaleproduct->save();
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                }
            }

            


            if ( $is_finalizar ) {
                $order->state = 'FINALIZADO';
            }

            $order->save();
            
            usleep(500000);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
