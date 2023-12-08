<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Saleitem;
use App\Models\Salecomboitem;
use App\Models\Salecombosaleproduct;
use App\Models\Caja;
use App\Models\Combo;
use App\Models\Comboitem;
use App\Models\Payment;
use App\Models\Stocksucursal;
use App\Models\User;
use App\Models\Modelofact;
use App\Models\Ivacondition;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use App\Http\Resources\v1\sales\salelist\SaleListResource;
use App\Http\Resources\v1\sales\SaleResource;

class SaleController extends Controller
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

        $user_id = null;
        if ( $request->has('user_id')){
            array_push($atr, ['user_id', '=', $request->get('user_id')] );
        }

        if ( $request->has('sucursal_id') ) {
            array_push($atr, ['sucursal_id', '=', $request->get('sucursal_id')] );
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
        //return $date_from;

        // date_from----
        if ( $date_from ){

            $sales = Sale::orderBy('id', 'DESC')
                ->where($atr)
                ->whereBetween('created_at', [$date_from, $date_to . ' 23:59:59'])
                ->paginate($limit);
            return SaleListResource::collection($sales);
        }

        // sin date_ftom-------
        $sales = Sale::orderBy('id', 'DESC')
            ->where($atr)
            ->where($atr)
            ->paginate($limit);
        return SaleListResource::collection($sales);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request->all();
        //usleep(1500000);
        $client = null;
            
        if($request->has('client_id')){
            $client = User::find($request->get('client_id'));
        }

        //---- verificar cuenta corriente del cliente sino devolver respuesta --------
        /* $total_payments = 0;
        if($request->has('payments')){
            $payments = $request->get('payments');
            foreach($payments as $payment){
                $total_payments = $total_payments + $payment['valor'];
            }
        }
        $saldo_sale = round($request->get('total') - round($total_payments, 2,PHP_ROUND_HALF_UP), 2, PHP_ROUND_HALF_DOWN);
        if ( !$client && $saldo_sale > 0 ) {
            return response()->json(['message' => 'La suma de los pagos no puede ser inferior al total de la venta'], 422);
        }

        if ( $client ) {
            if ( $saldo_sale > 0 && round($client->saldo + $saldo_sale, 2, PHP_ROUND_HALF_UP) > $client->credito_disponible) {
                return response()->json(['message' => 'EL cliente no posee crédito disponible que permita registrar la venta saldo_cliente: ' . $client->saldo. ' saldo_venta: '.$saldo_sale.' saldo disp: '. $client->credito_disponible   ], 422);
            }

        } */
        //---- verificar cuenta corriente del cliente sino devolver respuesta --------

        
        //---- verificar se se va a poder factuar sino devolver respuesta --------
        $ivacondition = null;
        if ( $request->has('ivacondition_id') ) {
            $ivacondition = Ivacondition::find($request->get('ivacondition_id'));
            
            if ( $ivacondition == null ) { return response()->json(['message' => 'código de condicion ante iva incorrecto.'], 422); }

            $is_pago_efectivo = false;
            if($request->has('payments')){
                $payments = $request->get('payments');
                foreach($payments as $payment){
                    if ( $payment['paymentmethod_id'] == 1 ) {
                        $is_pago_efectivo = true;
                    }
                }
            }
            
            
            
            if(( !$client ) || ($client != null && !$client->tiene_informacion_fe())){ // Sin cliente registrado
                
                if($is_pago_efectivo){
                    if($request->get('total') >= $ivacondition->modelofact->monto_max_no_id_efectivo) {
                        return response()->json(['message' => 'No es posible realizar la operación. No existe información fiscal correspondiente.1'], 422);
                    }
                }else{ // no es pago efectivo
                    if($request->get('total') >= $ivacondition->modelofact->monto_max_no_id_no_efectivo) {
                        return response()->json(['message' => 'No es posible realizar la operación. No existe información fiscal correspondiente.2'], 422);
                    }
                }
            }

        }

        //---- verificar se se va a poder factuar sino devolver respuesta --------

        

        $caja = null;
        if($request->has('caja_id')){
            $caja = Caja::find($request->get('caja_id'));
            if ( $caja == null ) { return response()->json(['message' => 'Código de caja incorrecto'], 422); }
        }

        if($request->has('payments') && !$caja){
            return response()->json(['message' => 'Caja inexistente'], 422);
        }

        if($request->has('payments') && !$caja->is_open){
            return response()->json(['message' => 'Caja cerrada'], 422);
        }

        try {
            DB::beginTransaction();
            
            $sale = Sale::create();

            $sale->user()->associate(Auth::user());
            $sale->sucursal()->associate($request->get('sucursal_id'));

            $sale->total = $request->get('total');
            $saldo_sale = $request->get('total');

            $saldo_cliente = 0;
            //$client = null;
            if($request->has('client_id')){
                //$client = User::find($request->get('client_id'));
                $sale->client()->associate($client);

                $saldo_cliente = $client->saldo;
                $saldo_cliente = round($saldo_cliente + $sale->total, 4, PHP_ROUND_HALF_UP);
                $sale->saldo = $saldo_cliente;
            }

            $items = $request->get('items');
            foreach($items as $item){
                $saleItem = new Saleitem;

                $saleItem->sale()->associate($sale);
                $saleItem->saleproduct()->associate($item['saleproduct_id']);
                $saleItem->precio = $item['precio'];
                $saleItem->cantidad = $item['cantidad'];
                $saleItem->ivaaliquot_id = $saleItem->saleproduct->stockproduct->ivaaliquot->id;
                if($saleItem->saleproduct->stockproduct->is_stock_unitario_variable){
                    $saleItem->cantidad_total = $item['cantidad_total'];
                }

                $saleItem->save();
                $stockSucursal = Stocksucursal::where('stockproduct_id', $saleItem->saleproduct->stockproduct_id)
                    ->where('sucursal_id', $request->get('sucursal_id'))
                    ->first();
                if ( !$stockSucursal ) {
                    $stockSucursal = Stocksucursal::create();
                    $stockSucursal->stock = 0;
                    $stockSucursal->stockproduct()->associate($saleItem->saleproduct->stockproduct_id);
                    $stockSucursal->sucursal()->associate($request->get('sucursal_id'));
                    $stockSucursal->save();

                }
                $stockSucursal->stock = $stockSucursal->stock - round($item['cantidad'] * $saleItem->saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);

                $stockSucursal->save();
            }

            $comboitems = $request->get('comboitems');
            foreach($comboitems as $comboitem){
                $combo = Combo::find($comboitem['combo_id']);

                $salecomboitem = new Salecomboitem;
                $salecomboitem->sale_id = $sale->id;
                $salecomboitem->precio = $comboitem['precio'];
                $salecomboitem->combo_id = $comboitem['combo_id'];
                $salecomboitem->cantidad = $comboitem['cantidad'];

                $salecomboitem->ivaaliquot()->associate($combo->getIvaaliquot());
                $salecomboitem->save();
                
                
                foreach($comboitem['comboitems'] as $combo_item_sale) {
                    $comboitem_def = Comboitem::find($combo_item_sale['comboitem_id']);

                    $cant_total = 0;
                    foreach($combo_item_sale['saleproducts'] as $saleproduct_sale){
                        if(!$comboitem_def->hasSaleproduct($saleproduct_sale['saleproduct_id'])){
                            return response()->json(['message' => 'El producto seleccionado no pertenece a la promoción.'], 422);
                        }

                        $salecombosaleproduct = new Salecombosaleproduct;
                        $salecombosaleproduct->cantidad = $saleproduct_sale['cantidad'];
                        $salecombosaleproduct->saleproduct()->associate($saleproduct_sale['saleproduct_id']);
                        $salecombosaleproduct->salecomboitem()->associate($salecomboitem->id);

                        $salecombosaleproduct->save();

                        $stockSucursal = Stocksucursal::where('stockproduct_id', $salecombosaleproduct->saleproduct->stockproduct_id)
                            ->where('sucursal_id', $request->get('sucursal_id'))
                            ->first();
                        if ( !$stockSucursal ) {
                            $stockSucursal = Stocksucursal::create();
                            $stockSucursal->stock = 0;
                            $stockSucursal->stockproduct()->associate($salecombosaleproduct->saleproduct->stockproduct_id);
                            $stockSucursal->sucursal()->associate($request->get('sucursal_id'));
                            $stockSucursal->save();

                        }
                        $stockSucursal->stock = $stockSucursal->stock - round($saleproduct_sale['cantidad'] * $salecombosaleproduct->saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);

                        $stockSucursal->save();

                        $cant_total = $cant_total + $saleproduct_sale['cantidad'];
                    }

                    if($cant_total != $comboitem_def->cantidad * round($comboitem['cantidad'], 4, PHP_ROUND_HALF_UP)){
                        return response()->json(['message' => 'La cantidad seleccionada de algún producto no es correcta.'], 422);
                    }

                    
                }



                $salecomboitem->save();
            }

            
            if($request->has('payments')){
                $payments = $request->get('payments');
                foreach($payments as $payment){
                    $salePayment = new Payment;

                    $salePayment->sale()->associate($sale);
                    $salePayment->paymentMethod()->associate($payment['paymentmethod_id']);

                    $paymentmethod = Paymentmethod::findOrFail($payment['paymentmethod_id']);
                    if ( $paymentmethod->requires_confirmation ) {
                        $salePayment->is_confirmed = false;
                    }else {
                        $salePayment->is_confirmed = true;
                    }


                    $salePayment->valor = $payment['valor'];
                    $salePayment->caja()->associate($caja);

                    if($request->has('client_id')){
                        $saldo_cliente =  round($saldo_cliente - $salePayment->valor, 6, PHP_ROUND_HALF_UP);
                        $salePayment->saldo = $saldo_cliente;
                    }

                    $salePayment->save();

                    $salePayment->created_at = Carbon::parse($salePayment->created_at)->addSeconds();

                    $salePayment->save();

                    $saldo_sale = $saldo_sale - $salePayment->valor;
                }
            }

            if($request->has('client_id')){
                $client->saldo = $saldo_cliente;
                $client->save();
            }

            $sale->saldo_sale = $saldo_sale;
            $sale->save();
            usleep(500000);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new SaleResource($sale);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        return new SaleResource($sale);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
    }

    public function make_devolution( $sale_id ) {
        $sale = Sale::findOrFail($sale_id);

        $devitems = [];
        foreach($sale->saleItems as $saleitem) {
            $devitem = [];
            $devitem['saleitem_id'] = $saleitem->id;
            $devitem['name'] = $saleitem->saleproduct->name;
            $devitem['precio'] = $saleitem->precio;
            $devitem['is_stock_unitario_variable'] = $saleitem->saleproduct->stockproduct->is_stock_unitario_variable;
            $devitem['relacion_venta_stock'] = $saleitem->saleproduct->relacion_venta_stock;
            $devitem['stock_aproximado_unidad'] = $saleitem->saleproduct->stockproduct->stock_aproximado_unidad;
            $devitem['cant_disponible_devolucion'] = $saleitem->get_cant_disponible_devolucion();
            $devitem['cant_total_disponible_devolucion'] = $saleitem->get_cant_total_disponible_devolucion();
                  
            array_push($devitems, $devitem);
        }

        $devcomboitems = [];
        foreach($sale->salecomboitems as $salecomboitem) {
            $devcomboitem = [];
            $devcomboitem['salecomboitem_id'] = $salecomboitem->id;
            $devcomboitem['name'] = $salecomboitem->combo->name;
            $devcomboitem['precio'] = $salecomboitem->precio;
            $devcomboitem['cant_disponible_devolucion'] = $salecomboitem->get_cant_disponible_devolucion();

            $devcombosaleproducts = [];
            foreach($salecomboitem->salecombosaleproducts as $salecombosaleproduct) {
                $devcombosaleproduct = [];
                $devcombosaleproduct['salecombosaleproduct_id'] = $salecombosaleproduct->id;
                $devcombosaleproduct['name'] = $salecombosaleproduct->saleproduct->name;
                $devcombosaleproduct['cant_disponible_devolucion'] = $salecombosaleproduct->get_cant_disponible_devolucion();

                array_push($devcombosaleproducts, $devcombosaleproduct);
            }

            $devcomboitem['devcombosaleproducts'] = $devcombosaleproducts;

            array_push($devcomboitems, $devcomboitem);
        }

        $devolution = [];
        $devolution['devitems'] = $devitems;
        $devolution['devcomboitems'] = $devcomboitems;


        return json_encode($devolution);
    }
}
