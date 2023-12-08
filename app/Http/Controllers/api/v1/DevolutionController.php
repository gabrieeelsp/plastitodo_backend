<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Devolution;
use App\Models\Sale;
use App\Models\Devolutionitem;
use App\Models\Stocksucursal;
use App\Models\Comboitem;
use App\Models\Salecombosaleproduct;
use Illuminate\Http\Request;

use App\Models\Salecomboitem;
use App\Models\Devolutioncomboitem;
use App\Models\Devolutioncombosaleproduct;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\v1\sales\DevolutionSaleResource;

class DevolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //$data = $request->get('data');
        try {
            DB::beginTransaction();
            $devolution = Devolution::create();

            $devolution->user()->associate(Auth::user());

            $devolution->sucursal()->associate($request->get('sucursal_id'));

            $sale = Sale::find($request['sale_id']); 

            $devolution->sale()->associate($sale);

            $devolution->total = $request->get('total');

            if($sale->client){
                $saldo_cliente = $sale->client->saldo;
                $saldo_cliente = round($saldo_cliente - $devolution->total, 6, PHP_ROUND_HALF_UP);
                $devolution->saldo = $saldo_cliente;

                $sale->client->saldo = $saldo_cliente;
                $sale->client->save();
            }

            $items = $request->get('items');
            foreach($items as $item){
                $devolutionItem = new Devolutionitem;
                $devolutionItem->devolution()->associate($devolution);
                $devolutionItem->saleitem()->associate($item['saleitem_id']);

                if($item['cantidad'] > $devolutionItem->saleitem->get_cant_disponible_devolucion()) {
                    return response()->json(['message' => 'La cantidad enviada no esta permitida para algún producto seleccionado.']);
                }
                if ($devolutionItem->saleitem->saleproduct->stockproduct->is_stock_unitario_variable) {
                    if($item['cantidad_total'] > $devolutionItem->saleitem->get_cant_total_disponible_devolucion()) {
                        return response()->json(['message' => 'La cantidad enviada no esta permitida para algún producto seleccionado.']);
                    }else {
                        $devolutionItem->cantidad_total = $item['cantidad_total'];
                    }

                    if( 
                        $devolutionItem->saleitem->get_cant_disponible_devolucion() - $item['cantidad'] == 0 && $devolutionItem->saleitem->get_cant_total_disponible_devolucion() - $item['cantidad_total'] != 0
                        ||
                        $devolutionItem->saleitem->get_cant_disponible_devolucion()  - $item['cantidad'] != 0 && $devolutionItem->saleitem->get_cant_total_disponible_devolucion() - $item['cantidad_total'] == 0
                    ) {
                        return response()->json(['message' => 'Existe un error en la cantidad devuelta y la cantidad total devuelta para algún producto que posee stock unitario variable.'], 422);
                    }
                }
                

                $devolutionItem->cantidad = $item['cantidad'];

                $devolutionItem->save();

                $stockSucursal = Stocksucursal::where('stockproduct_id', $devolutionItem->saleitem->saleproduct->stockproduct_id)
                    ->where('sucursal_id', $request->get('sucursal_id'))
                    ->first();
                //$stockSucursal->stock = round($stockSucursal->stock + $item['cantidad'], 4, PHP_ROUND_HALF_UP);
                $stockSucursal->stock = $stockSucursal->stock + round($item['cantidad'] * $devolutionItem->saleitem->saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);
                $stockSucursal->save();
            }


            $comboitems = $request->get('comboitems');
            foreach($comboitems as $salecomboitemDev){
                $salecomboitem = Salecomboitem::find($salecomboitemDev['salecomboitem_id']);
                $devolutioncomboitem = new Devolutioncomboitem;
                $devolutioncomboitem->salecomboitem_id = $salecomboitem->id;
                $devolutioncomboitem->devolution_id = $devolution->id;
                $devolutioncomboitem->cantidad = $salecomboitemDev['cantidad'];

                $devolutioncomboitem->save();

                $restricciones = array();
                foreach ( $salecomboitem->combo->comboitems as $comboitem ) {
                    array_push($restricciones, array($comboitem->id, $comboitem->cantidad * $salecomboitemDev['cantidad'], 0));
                }
                
                foreach ( $salecomboitemDev['salecombosaleproducts'] as $salecombosaleproductDev ) {
                    $salecombosaleproduct = Salecombosaleproduct::find($salecombosaleproductDev['salecombosaleproduct_id']);
                    $devolutioncombosaleproduct = new Devolutioncombosaleproduct;
                    $devolutioncombosaleproduct->devolutioncomboitem_id = $devolutioncomboitem->id;
                    $devolutioncombosaleproduct->cantidad = $salecombosaleproductDev['cantidad'];
                    $devolutioncombosaleproduct->salecombosaleproduct_id = $salecombosaleproductDev['salecombosaleproduct_id'];
                    $devolutioncombosaleproduct->save();
                    
                    $comboitem = $salecombosaleproduct->salecomboitem->combo->getComboitem_from_saleproduct($salecombosaleproduct->saleproduct_id);
                    $i = 0;
                    foreach ( $restricciones as $restriccion ) {
                        if ( $restriccion[0] == $comboitem->id ) {
                            $restricciones[$i][2] = $restricciones[$i][2] + $salecombosaleproductDev['cantidad'];
                        }
                        $i = $i +1;
                    }
                    

                    $stockSucursal = Stocksucursal::where('stockproduct_id', $salecombosaleproduct->saleproduct->stockproduct_id)
                        ->where('sucursal_id', $request->get('sucursal_id'))
                        ->first();
                    //$stockSucursal->stock = round($stockSucursal->stock + $item['cantidad'], 4, PHP_ROUND_HALF_UP);
                    $stockSucursal->stock = $stockSucursal->stock + round($salecombosaleproductDev['cantidad'] * $salecombosaleproduct->saleproduct->relacion_venta_stock, 6, PHP_ROUND_HALF_UP);
                    $stockSucursal->save();
                } 
                
                foreach ( $restricciones as $restriccion ) {
                    if ( $restriccion[1] != $restriccion[2] ) {
                        return response()->json(['message' => 'La configuración no es correcta para algún combo seleccionado.'], 422);
                    }
                }
                
            }

            $sale->saldo_sale = $sale->saldo_sale - $request->get('total');
            $sale->save();

            $devolution->save();

            usleep(1000000);

            DB::commit();
        }catch(\Exception $e) {
            DB::rollback();
            return $e;
        }

        return new DevolutionSaleResource($devolution);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Devolution  $devolution
     * @return \Illuminate\Http\Response
     */
    public function show(Devolution $devolution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devolution  $devolution
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Devolution $devolution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Devolution  $devolution
     * @return \Illuminate\Http\Response
     */
    public function destroy(Devolution $devolution)
    {
        //
    }
}
