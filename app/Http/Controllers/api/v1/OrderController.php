<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Ordercomboitem;
use App\Models\Ordercombosaleproduct;
use App\Models\Combo;
use App\Models\Stocksucursal;
use App\Models\Sale;
use App\Models\Saleitem;
use App\Models\Salecomboitem;
use App\Models\Salecombosaleproduct;

use App\Models\Ivacondition;
use App\Models\Comprobante;
use App\Models\Ivaaliquot;

use App\Models\User;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Http\Resources\v1\orders\OrderResource;
use App\Http\Resources\v1\orders\orderlist\OrderListResource;
use App\Http\Resources\v1\orders\orderchecksale\OrderCheckSaleResource;

use Carbon\Carbon;

use Afip;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = 20;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $atr = [];

        if ( $request->has('client_id')){
            array_push($atr, ['client_id', '=', $request->get('client_id')] );
        }

        if ( $request->has('state') ) {
            array_push($atr, ['state', '=', $request->get('state')] );
        }

        if ( $request->has('sucursal_id') ) {
            array_push($atr, ['sucursal_id', '=', $request->get('sucursal_id')] );
        }

        if ( $request->has('is_delivery') ) {
            
            array_push($atr, ['is_delivery', filter_var($request->get('is_delivery'), FILTER_VALIDATE_BOOL)] );
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
        //return $atr;

        // date_from----
        if ( $date_from ){

            $orders = Order::orderBy('id', 'DESC')
                ->where($atr)
                ->whereBetween('created_at', [$date_from, $date_to . ' 23:59:59'])
                ->paginate($limit);
            return OrderResource::collection($orders);
        }

        // sin date_ftom-------
        $orders = Order::orderBy('id', 'DESC')
            ->where($atr)
            ->paginate($limit);
        return OrderResource::collection($orders);

    }

    public function get_orders_distribucion(Request $request)
    {
        //return $request->all();

        $atr = [];

        array_push($atr, ['state', '=', 'EN DISTRIBUCION'] );

        if ( $request->has('sucursal_id') ) {
            array_push($atr, ['sucursal_id', '=', $request->get('sucursal_id')] );
        }

        if ( $request->has('deliveryshift_id') ) {
            array_push($atr, ['deliveryshift_id', '=', $request->get('deliveryshift_id')] );
        }

        $orders = Order::orderBy('id', 'DESC')
            ->where($atr)
            ->get();
        return OrderResource::collection($orders);
        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $event = $request->get('evento');
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
                $order->fecha_entrega_acordada = Carbon::createFromFormat('d-m-Y', $request->get('fecha_entrega_acordada'));
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
                    if ( $item['cantidad_total']) {
                        $orderItem->cantidad_total = $item['cantidad_total'];
                    }else {
                        $orderItem->cantidad_total = 0;
                    }
                    
                }

                $orderItem->save();


                if ( $order->sucursal ) {
                    $this->tomar_stock_pedido ( $orderItem->saleproduct, $order->sucursal, $orderItem->cantidad );
                }
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

                        if ( $order->sucursal ) {
                            $this->tomar_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                        }
                    }
                    
                }



                $ordercomboitem->save();
            }
            
            $order->state = "EDITANDO";
            if ( $event == 'FINALIZAR' ) {
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
    
    public function update(Request $request, Order $order)
    {
        $event = $request->get('evento');

	if (  $event == 'CANCELAR' && $order->state == 'FACTURADO' ) {
            $order->state = 'CANCELADO';
            $order->save();
            return new OrderResource(Order::find($order->id));
        }
        if (  $event == 'ENVIAR' && $order->state == 'FACTURADO' ) {
            $order->state = 'EN DISTRIBUCION';
            $order->save();
            return new OrderResource(Order::find($order->id));
        }
        if (  $event == 'CANCELAR ENVIO' && $order->state == 'EN DISTRIBUCION' ) {
            $order->state = 'FACTURADO';
            $order->save();
            return new OrderResource(Order::find($order->id));
        }
        if (  $event == 'SET ENTREGADO' && $order->state == 'EN DISTRIBUCION' ) {
            $order->state = 'ENTREGADO';
            $order->save();
            return new OrderResource(Order::find($order->id));
        }
        if (  $event == 'SET ENTREGADO' && $order->state == 'FACTURADO' ) {
            $order->state = 'ENTREGADO';
            $order->save();
            return new OrderResource(Order::find($order->id));
        }
        if (  $event == 'CANCELAR ENTREGADO' && $order->state == 'ENTREGADO' ) {
            if ( $order->is_delivery ) {
                $order->state = 'EN DISTRIBUCION';
            }else {
                $order->state = 'FACTURADO';
            }
            
            $order->save();
            return new OrderResource(Order::find($order->id));
        }

        $sucursal_anterior = null;
        $opcion_sucursal = '';
        
        try {
            DB::beginTransaction();

            if ( $request->has('sucursal_id')){
                if ( $order->sucursal ) {
                    if ( $order->sucursal->id == $request->get('sucursal_id') ) {
                        $opcion_sucursal = 'MANTIENE';
                        $sucursal_anterior = $order->sucursal;
                    }else {
                        $opcion_sucursal = 'CAMBIA';
                        $sucursal_anterior = $order->sucursal;
                    }
                }else {
                    $opcion_sucursal = 'AGREGA';
                }
                $order->sucursal()->associate($request->get('sucursal_id'));
            }else {
                if ( $order->sucursal ) {
                    $opcion_sucursal = 'ELIMINA';
                    $sucursal_anterior = $order->sucursal;
                }else {
                    $opcion_sucursal = 'NULO';
                }
                $order->sucursal()->associate(null);
            }

            if($request->has('fecha_entrega_acordada')){
                $order->fecha_entrega_acordada = Carbon::createFromFormat('d-m-Y', $request->get('fecha_entrega_acordada'));
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

            $order->save();

            if ( ( $event == 'GUARDAR' && $order->state == 'EDITANDO' ) || ( $event == 'FINALIZAR' && $order->state == 'EDITANDO' ) || ( $event == 'FINALIZAR PREPARACION' && $order->state == 'EN PREPARACION' ) ) {
                
                if ( $opcion_sucursal == 'MANTIENE' || $opcion_sucursal == 'CAMBIA' || $opcion_sucursal == 'ELIMINA' ) {
                    //return $opcion_sucursal;
                    //return $sucursal_anterior;
                    foreach ( $order->orderitems as $orderitem ) {
                        //return $orderitem;
                        $this->devolver_stock_pedido ( $orderitem->saleproduct, $sucursal_anterior, $orderitem->cantidad );
                    }
                    foreach ( $order->ordercomboitems as $ordercomboitem ) {
                        foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                            $this->devolver_stock_pedido ( $ordercombosaleproduct->saleproduct, $sucursal_anterior, $ordercombosaleproduct->cantidad );
                        }
                    }
                }

                $order->orderitems()->delete();
                $items = $request->get('items');
                foreach($items as $item){
                    $orderItem = new Orderitem;

                    $orderItem->order()->associate($order);
                    $orderItem->saleproduct()->associate($item['saleproduct_id']);
                    $orderItem->precio = $item['precio'];
                    $orderItem->cantidad = $item['cantidad'];

                    if($orderItem->saleproduct->stockproduct->is_stock_unitario_variable){
                        if ( $item['cantidad_total']) {
                            $orderItem->cantidad_total = $item['cantidad_total'];
                        }else {
                            $orderItem->cantidad_total = 0;
                        }
                    }
                    //return $order->sucursal;
                    if ( $order->sucursal ) {
                        //return 'noooo';
                        $this->tomar_stock_pedido ( $orderItem->saleproduct, $order->sucursal, $orderItem->cantidad );
                    }

                    if ( $event == 'FINALIZAR PREPARACION' && $order->state == 'EN PREPARACION' ) {
                        $orderItem->is_prepared = true;
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

                            if ( $order->sucursal ) {
                                //return 'noooo';
                                $this->tomar_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                            }
        
                            if ( $event == 'FINALIZAR PREPARACION' && $order->state == 'EN PREPARACION' ) {
                                $ordercombosaleproduct->is_prepared = true;
                            }

                            $ordercombosaleproduct->save();
                        }
                        
                    }



                    $ordercomboitem->save();
                }

                if ( $event == 'FINALIZAR' ) {
                    $order->state = 'FINALIZADO';
                }

            }

            if ( ( $event == 'GUARDAR' && $order->state == 'EN PREPARACION' ) || ( $event == 'FINALIZAR PREPARACION' && $order->state == 'EN PREPARACION' ) ) {
                $items = $request->get('items');
                foreach($items as $item){

                    foreach ( $order->orderitems as $orderitem ) {
                        if ( $orderitem->saleproduct_id == $item['saleproduct_id'] ) {

                            if ( $orderitem->is_prepared && !boolval($item['is_prepared']) ) {
                                //el stock vuelve a pedido
                                $this->tomar_stock_pedido ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
                                //devolver stock
                                $this->devolver_stock ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
                            }

                            if ( !$orderitem->is_prepared && boolval($item['is_prepared']) ) {
                                //saco el stock de pedido
                                $this->devolver_stock_pedido ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
                                //tomar stock
                                $this->tomar_stock ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
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
                                        if ( $ordercombosaleproduct->saleproduct_id == $saleproduct_order['saleproduct_id'] ) {
                                            if ( $ordercombosaleproduct->is_prepared && !boolval($saleproduct_order['is_prepared']) ) {
                                                //devolver stock
                                                $this->tomar_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                                                $this->devolver_stock ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                                            }
                
                                            if ( !$ordercombosaleproduct->is_prepared && boolval($saleproduct_order['is_prepared']) ) {
                                                //tomar stock
                                                $this->devolver_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                                                $this->tomar_stock ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
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

            if ( ( $event == 'EDITAR' && $order->state == 'FINALIZADO' ) || ( $event == 'EDITAR' && $order->state == 'CONFIRMADO' ) ) {
                $order->state = 'EDITANDO';
            }
            if ( ( $event == 'CONFIRMAR' && $order->state == 'FINALIZADO' ) ) {
                $order->state = 'CONFIRMADO';
            }

            if ( ( $event == 'INICIAR PREPARACION' && $order->state == 'CONFIRMADO' ) ) {
                $order->state = 'EN PREPARACION';
            }
            
            if ( ( $event == 'CANCELAR PREPARACION' && $order->state == 'EN PREPARACION' ) ) {
                
                foreach ( $order->orderitems as $orderitem ) {
                    if ( $orderitem->is_prepared ) {
                        //el stock vuelve a pedido
                        $this->tomar_stock_pedido ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
                        //devolver stock
                        $this->devolver_stock ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );

                        $orderitem->is_prepared = false;
                        $orderitem->save();
                    }
                    
                }
                foreach ( $order->ordercomboitems as $ordercomboitem ) {
                    foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                        if ( $ordercombosaleproduct->is_prepared ) {
                            //devolver stock
                            $this->tomar_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                            $this->devolver_stock ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );

                            $ordercombosaleproduct->is_prepared = false;
                            $ordercombosaleproduct->save();
                        }
                    }
                }
                $order->state = 'CONFIRMADO';
                
            }
            
            if ( ( $event == 'FINALIZAR PREPARACION' && $order->state == 'EN PREPARACION' ) || ( $event == 'GUARDAR' && $order->state == 'FACTURADO' ) || ( $event == 'GUARDAR' && $order->state == 'EN DISTRIBUCION' ) || ( $event == 'GUARDAR' && $order->state == 'PREPARADO' ) ) {
                if ( $request->has('cant_bultos') ) {                    
                    $order->cant_bultos = $request->get('cant_bultos');
                }
            }

            if ( ( $event == 'FINALIZAR PREPARACION' && $order->state == 'EN PREPARACION' ) ) {
                $order->state = 'PREPARADO';
            }

            if ( ( $event == 'EDITAR' && $order->state == 'PREPARADO' ) ) {
                //return $order->orderitems;
                foreach ( $order->orderitems as $orderitem ) {
                    
                    if ( $orderitem->is_prepared ) {
                        
                        //el stock vuelve a pedido
                        $this->tomar_stock_pedido ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
                        //devolver stock
                        $this->devolver_stock ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );

                        $orderitem->is_prepared = false;
                        $orderitem->save();
                    }
                    
                }
                foreach ( $order->ordercomboitems as $ordercomboitem ) {
                    foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                        if ( $ordercombosaleproduct->is_prepared ) {
                            //devolver stock
                            $this->tomar_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                            $this->devolver_stock ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );

                            $ordercombosaleproduct->is_prepared = false;
                            $ordercombosaleproduct->save();
                        }
                    }
                }
                $order->state = 'EDITANDO';
                
            }

            if ( ( $event == 'FACTURAR' && $order->state == 'PREPARADO' ) ) {
                //return 'seee';
                $order->save();

                $sale = Sale::create();
                
                $sale->user()->associate(auth()->user());
                $sale->sucursal()->associate($order->sucursal_id);

                $sale->total = $request->get('total');

                $sale->client()->associate($order->client_id);
                $client = $order->client;

                $saldo_cliente = $client->saldo;
                $saldo_cliente = round($saldo_cliente + $sale->total, 4, PHP_ROUND_HALF_UP);
                $sale->saldo = $saldo_cliente;

                foreach($order->orderitems as $item){
                    $saleItem = new Saleitem;
    
                    $saleItem->sale()->associate($sale);
                    $saleItem->saleproduct()->associate($item->saleproduct_id);
                    $saleItem->precio = $item->precio;
                    $saleItem->cantidad = $item->cantidad;
                    $saleItem->ivaaliquot_id = $saleItem->saleproduct->stockproduct->ivaaliquot->id;
                    if($saleItem->saleproduct->stockproduct->is_stock_unitario_variable){
                        $saleItem->cantidad_total = $item->cantidad_total;
                    }
    
                    $saleItem->save();
                }

                $client->saldo = $saldo_cliente;
                $client->save();

                $sale->saldo_sale = $sale->total;
    
                $sale->save();

                $order->sale()->associate($sale->id);

                $order->state = 'FACTURADO';

                if ( $order->ivacondition ) {
                    $comprobante = $this->make_fact($sale, $order->ivacondition->id);
                     
                }
                //return $sale;
            }

            //------------------           

            $order->save();
            
            usleep(500000);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new OrderResource(Order::find($order->id));
    }

    private function make_fact( Sale $sale, $ivacondition_id)
    {  


        $ivacondition = Ivacondition::findOrFail($ivacondition_id);

        //$afip = new Afip(array('CUIT' => 20291188568));
        $afip = new Afip(array('CUIT' => 30714071633, 'production' => true));

        $is_pago_efectivo = $sale->hasPaymentCash();

        if(( !$sale->client ) || ($sale->client && !$sale->client->tiene_informacion_fe())){ // Sin cliente registrado
            if($is_pago_efectivo){
                if($sale->total >= $ivacondition->modelofact->monto_max_no_id_efectivo) {
                    return response()->json(['message' => 'No es posible realizar la operación. No existe información fiscal correspondiente.'], 422);
                }
            }else{ // no es pago efectivo
                if($sale->total >= $ivacondition->modelofact->monto_max_no_id_no_efectivo) {
                    return response()->json(['message' => 'No es posible realizar la operación. No existe información fiscal correspondiente.'], 422);
                }
            }
        }
        // todo -> verificar si el cliente quiere incluir sus datos


        

        if( $sale->client && $sale->client->tiene_informacion_fe() ) {
            if ( $sale->client->tipo_persona == 'FISICA') {
                $nombre = $sale->client->name.' '.$sale->client->surname;
            }else {
                $nombre = $sale->client->nombre_fact;
            }
            $numero_doc = $sale->client->docnumber;
            $id_afip_doctype = $sale->client->doctype->id_afip;
            $name_doctype = $sale->client->doctype->name;
            $direccion = $sale->client->direccion_fact;

        }else {
            $nombre = "";
            $numero_doc = "0";
            $id_afip_doctype = 99;
            $name_doctype = "Sin identificar";
            $direccion = "";
        }

            
        
        $numero_comprobante = $afip->ElectronicBilling->getLastVoucher($sale->sucursal->punto_venta_fe, $ivacondition->modelofact->id_afip_factura);

        $numero_comprobante = $numero_comprobante + 1;

        if(!$sale->comprobante){
            $comprobante = new Comprobante;

            $comprobante->punto_venta = $sale->sucursal->punto_venta_fe;
            $comprobante->id_afip_tipo = $ivacondition->modelofact->id_afip_factura;
            $comprobante->comprobanteable_id = $sale->id;
            $comprobante->comprobanteable_type = 'App\Models\Sale';
            $comprobante->modelofact_id = $ivacondition->modelofact->id;
            $comprobante->docnumber = $numero_doc;
            $comprobante->doctype_id_afip = $id_afip_doctype;
            $comprobante->doctype_name =  $name_doctype;


            $comprobante->nombre_empresa = $sale->sucursal->empresa->name;
            $comprobante->razon_social_empresa = $sale->sucursal->empresa->razon_social;
            $comprobante->domicilio_comercial_empresa = $sale->sucursal->empresa->domicilio_comercial;
            $comprobante->ivacondition_name_empresa = $sale->sucursal->empresa->ivacondition->name;
            $comprobante->cuit_empresa = $sale->sucursal->empresa->cuit;
            
            $comprobante->ing_brutos_empresa = $sale->sucursal->empresa->ing_brutos;
            $comprobante->fecha_inicio_act_empresa = $sale->sucursal->empresa->fecha_inicio_act;

            $comprobante->condicion_venta = $sale->getCondicionVenta();

            
            $comprobante->nombre_fact_client = $nombre;
            $comprobante->direccion_fact_client = $direccion;
            $comprobante->ivacondition_name_client = $ivacondition->name;


        }else {
            $comprobante = $sale->comprobante;
        }

        $comprobante->numero = $numero_comprobante;
        $comprobante->save();


        //--- mando a autorizar ---------

        //revisar la fecha, actualmente va a enviar la fecha de la venta
        //pero puede ser la fecha actual 
        //ver cuantos dias max pueden pasar antes de enviar a autorizar
        
        $ImpNeto = 0;
        $ImpTotConc = 0;
        $ImpOpEx = 0;
        $ivaaliquots_send = array();
        foreach(Ivaaliquot::all() as $ivaaliquot){
            $baseImpIva = $sale->getBaseImpIva($ivaaliquot->id);            
            if ( $baseImpIva ){

                if ($ivaaliquot->id_afip != 1 && $ivaaliquot->id_afip != 2) {
                    array_push($ivaaliquots_send, array(
                        'Id' 		=> $ivaaliquot->id_afip, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                        'BaseImp' 	=> $baseImpIva, // Base imponible
                        'Importe' 	=> $sale->getImpIva($ivaaliquot->id) // Importe 
                    ) );
                }

                //guardo ImpTotConc para despues
                if ($ivaaliquot->id_afip == 1 ) { $ImpTotConc = $baseImpIva; }

                //guardo ImpOpEx para despues
                if ($ivaaliquot->id_afip == 2 ) { $ImpOpEx = $baseImpIva; }

                if (in_array($ivaaliquot->id_afip, [3, 4, 5, 6, 8, 9], false)){
                    $ImpNeto = $ImpNeto + $baseImpIva;
                }
            }
        }


        $ImpIVA = round($sale->total - ($ImpTotConc + $ImpOpEx + $ImpNeto), 2, PHP_ROUND_HALF_UP);

        

        $date = $sale->created_at->format('Ymd');

        

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> $sale->sucursal->punto_venta_fe,  // Punto de venta
            'CbteTipo' 	=> $ivacondition->modelofact->id_afip_factura,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> $id_afip_doctype, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> $numero_doc,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' => $numero_comprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => $numero_comprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval($date), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> floatval($sale->total), // Importe total del comprobante


            'ImpTotConc' 	=> $ImpTotConc,   // Importe neto no gravado
            'ImpNeto' 	=> $ImpNeto, // Importe neto gravado
            'ImpOpEx' 	=> $ImpOpEx,   // Importe exento de IVA
            'ImpIVA' 	=> $ImpIVA,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> $ivaaliquots_send, 
        );

        //return $data;
        
        $res = $afip->ElectronicBilling->CreateVoucher($data, false);
        try {
            
        } catch(\Exception $e) {
            return new ComprobanteSaleResource($comprobante);
        }

        $comprobante->cae = $res['CAE'];
        $comprobante->cae_fch_vto = $res['CAEFchVto'];

        $comprobante->save();

        return $comprobante;

        return new ComprobanteSaleResource($comprobante);
    }

    private function tomar_stock ( $saleproduct, $sucursal, $cantidad ) {
        $stockSucursal = Stocksucursal::where('stockproduct_id', $saleproduct->stockproduct_id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        if ( !$stockSucursal ) {
            $stockSucursal = Stocksucursal::create();
            $stockSucursal->stock = 0;
            $stockSucursal->stockproduct()->associate($saleproduct->stockproduct_id);
            $stockSucursal->sucursal()->associate($sucursal->id);
            $stockSucursal->save();

        }
        $stockSucursal->stock = $stockSucursal->stock - round($cantidad * $saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);

        $stockSucursal->save();
    }

    private function devolver_stock ( $saleproduct, $sucursal, $cantidad ) {
        $stockSucursal = Stocksucursal::where('stockproduct_id', $saleproduct->stockproduct_id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        if ( !$stockSucursal ) {
            $stockSucursal = Stocksucursal::create();
            $stockSucursal->stock = 0;
            $stockSucursal->stockproduct()->associate($saleproduct->stockproduct_id);
            $stockSucursal->sucursal()->associate($sucursal->id);
            $stockSucursal->save();

        }
        $stockSucursal->stock = $stockSucursal->stock + round($cantidad * $saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);

        $stockSucursal->save();
    }

    private function tomar_stock_pedido ( $saleproduct, $sucursal, $cantidad ) {
        $stockSucursal = Stocksucursal::where('stockproduct_id', $saleproduct->stockproduct_id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        if ( !$stockSucursal ) {
            $stockSucursal = Stocksucursal::create();
            $stockSucursal->stock = 0;
            $stockSucursal->stock_pedido = 0;
            $stockSucursal->stockproduct()->associate($saleproduct->stockproduct_id);
            $stockSucursal->sucursal()->associate($sucursal->id);
            $stockSucursal->save();

        }
        $stockSucursal->stock_pedido = $stockSucursal->stock_pedido + round($cantidad * $saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);

        $stockSucursal->save();
    }

    private function devolver_stock_pedido ( $saleproduct, $sucursal, $cantidad ) {
        $stockSucursal = Stocksucursal::where('stockproduct_id', $saleproduct->stockproduct_id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
            //return $stockSucursal;
        if ( !$stockSucursal ) {
            $stockSucursal = Stocksucursal::create();
            $stockSucursal->stock = 0;
            $stockSucursal->stock_pedido = 0;
            $stockSucursal->stockproduct()->associate($saleproduct->stockproduct_id);
            $stockSucursal->sucursal()->associate($sucursal->id);
            $stockSucursal->save();

        }
        $stockSucursal->stock_pedido = $stockSucursal->stock_pedido - round($cantidad * $saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);
        //return $stockSucursal;
        $stockSucursal->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if ( $order->state != 'EDITANDO' ) {
            return response()->json(['message' => 'El Pedido no se puede eliminar'], 422);
        }
        try {
            if ( $order->sucursal ) {
                foreach ( $order->orderitems as $orderitem ) {
                    //return $orderitem;
                    $this->devolver_stock_pedido ( $orderitem->saleproduct, $order->sucursal, $orderitem->cantidad );
                }
                foreach ( $order->ordercomboitems as $ordercomboitem ) {
                    foreach ( $ordercomboitem->ordercombosaleproducts as $ordercombosaleproduct ) {
                        $this->devolver_stock_pedido ( $ordercombosaleproduct->saleproduct, $order->sucursal, $ordercombosaleproduct->cantidad );
                    }
                }
            }
            
            $order->delete();
        }catch(\Exception $e){
            return $e;
        }
        
        return response()->json(['message' => 'Se ha eliminado el item']);
    }

    public function get_order_check_sale ( $order_id ) 
    {
        $order = Order::findOrFail($order_id);

        return new OrderCheckSaleResource($order);
    }

    public function update_precios ( Request $request, $order_id ) {
        $order = Order::findOrFail($order_id);
        //return $request->all();
        try {
            DB::beginTransaction();
            $items = $request->get('data')['items'];
            foreach ( $items as $item ) {
                if ( $item['tipo'] == 'saleproduct' && $item['actualizar_precio'] ) {
                    foreach ( $order->orderitems as $orderitem ) {
                        if ( $item['id'] == $orderitem->id ) {
                            $orderitem->precio = $item['precio_actualizado'];
                            
                            $orderitem->save();
                            
                        }
                    }
                }else {
                    foreach ( $order->ordercomboitems as $ordercomboitem ) {
                        if ( $item['id'] == $ordercomboitem->id ) {
                            $ordercomboitem->precio = $item['precio_actualizado'];
                            $ordercomboitem->save();
                        }
                    }
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new OrderResource($order);
    }
}
