<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Purchaseorder;
use App\Models\Purchaseorderitem;

use App\Models\Stockmovement;
use App\Models\Stockmovementitem;

use App\Models\Stocksucursal;

use Illuminate\Http\Request;

use App\Http\Resources\v1\purchaseorders\PurchaseorderListResource;

use App\Http\Resources\v1\purchaseorders\PurchaseorderResource;

use App\Http\Resources\v1\stockmovements\StockmovementResource;

use Illuminate\Support\Facades\DB;

class PurchaseorderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $atr = [];

        if ( $request->has('supplier_id') ) {
            array_push($atr, ['supplier_id', '=', $request->get('supplier_id')] );
        }

        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }
        
        $users = null;
        //Paginate?
        if ( $request->has('paginate')) {
            $paginate = $request->get('paginate');
            
            if ( $paginate == 0 ) { 
                $purchaseorders = Purchaseorder::orderBy('created_at', 'DESC')
                    ->where($atr)->get();
                    
                    return PurchaseorderListResource::collection($purchaseorders);

            }
            
        }
        
        
        $purchaseorders = Purchaseorder::orderBy('created_at', 'DESC')
            ->Where($atr)
            ->paginate($limit);       
        
        return PurchaseorderListResource::collection($purchaseorders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchaseorder  $purchaseorder
     * @return \Illuminate\Http\Response
     */
    public function show(Purchaseorder $purchaseorder)
    {
        
        if ( $purchaseorder->estado != 'RECIBIDO' ) {
            $purchaseproducts = clone $purchaseorder->supplier->purchaseproducts;
            
            $ids_delete = [];
            foreach ( $purchaseorder->purchaseorderitems as $poi ) {
                foreach ( $purchaseproducts as $key => $pp ) {
                    
                    if ( $poi->purchaseproduct_id == $pp->id ) {
                        
                        if ( !$pp->is_enable ) {
                            $poi->delete();
                            //array_push($ids_delete, $poi->id);
                        }
                        unset($purchaseproducts[$key]);
                        
                    }
                }
            }
            
            foreach ( $purchaseproducts as $pp ) {
                if ( $pp->is_enable ) {
                    $orderitem = Purchaseorderitem::create();
                    $orderitem->purchaseproduct()->associate($pp->id);
                    $orderitem->purchaseorder()->associate($purchaseorder->id);
                    $orderitem->save();
                }
            }

            return new PurchaseorderResource(Purchaseorder::find($purchaseorder->id));
        }

        return new PurchaseorderResource($purchaseorder);
            

        

        

        

        //return new PurchaseorderResource($purchaseorder);

        //$purchaseproducts = clone $purchaseorder->supplier->purchaseproducts;

        

        //agrego productos nuevos incluidos en el proveedor
        /* foreach ( $purchaseproducts as $purchaseproduct ) {
            $orderitem = Purchaseorderitem::create();
            $orderitem->purchaseproduct()->associate($purchaseproduct->id);
            $orderitem->purchaseorder()->associate($purchaseorder->id);
            $orderitem->save();
        } */

        

        //return new PurchaseorderResource(Purchaseorder::find($purchaseorder->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchaseorder  $purchaseorder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchaseorder $purchaseorder)
    {

        $is_confirmar = $request->get('data')['meta']['is_confirmar'];
        
        try {
            DB::beginTransaction();

            $data = $request->get('data');
            //return $data['relationships']['purchaseorderitems'];
            foreach ( $data['relationships']['purchaseorderitems'] as $poi ) {
                $purchaseorderitem = Purchaseorderitem::findOrFail($poi['id']);
                $purchaseorderitem->cantidad = $poi['cantidad'];
                $purchaseorderitem->save();
            }


            if ( $request->has('data.relationships.sucursal')) { 
                if ( $request->get('data')['relationships']['sucursal'] != null ) {
                    $purchaseorder->sucursal()->associate($request->get('data')['relationships']['sucursal']['id']);
                }
                
            }

            $purchaseorder->save();


            if ( $is_confirmar ) {

                foreach ( $purchaseorder->purchaseorderitems as $purchaseorderitem_delete ) {
                    if ( $purchaseorderitem_delete->cantidad == 0 ) {
                        $purchaseorderitem_delete->delete();
                    }
                }

                $purchaseorder->estado = 'RECIBIDO';

                $stockmovement = Stockmovement::create();
                $stockmovement->user()->associate(auth()->user()->id);
                $stockmovement->sucursal()->associate($purchaseorder->sucursal->id);
                $stockmovement->tipo = 'INGRESO';
                $stockmovement->estado = 'CONFIRMADO';
                $stockmovement->save();

                foreach ( $purchaseorder->purchaseorderitems as $poi ) {
                    if ( $poi->cantidad > 0 ) {
                        $stockmovementitem = Stockmovementitem::create();
                        $stockmovementitem->stockmovement()->associate($stockmovement->id);
                        $stockproduct = $poi->purchaseproduct->stockproduct;
                        $stockmovementitem->stockproduct()->associate($stockproduct->id);
                        $stockmovementitem->cantidad = round($poi->cantidad * $poi->purchaseproduct->relacion_compra_stock, 6, PHP_ROUND_HALF_UP);
                        $stockmovementitem->save();

                        
                        //---actualiza stock
                        $existe_stockSucursal = false;
                        foreach ( $stockproduct->stocksucursals as $stocksucursal ) {
                            if ( $stocksucursal->sucursal_id == $stockmovement->sucursal_id ) {
                                $existe_stockSucursal = true;
                                $stocksucursal->stock = $stocksucursal->stock + $stockmovementitem->cantidad;
                                $stocksucursal->save();

                            }
                        }
                        if ( !$existe_stockSucursal ) {
                            $stocksucursal_nuevo = Stocksucursal::create();
                            $stocksucursal_nuevo->stockproduct()->associate($stockproduct->id);
                            $stocksucursal_nuevo->sucursal()->associate($stockmovement->sucursal_id);
                            $stocksucursal_nuevo->stock = $stockmovementitem->cantidad;
                            $stocksucursal_nuevo->save();

                        }


                        //actiualizo stock
                    }
                    


                }
               

            }
            $purchaseorder->save();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new PurchaseorderResource(Purchaseorder::find($purchaseorder->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchaseorder  $purchaseorder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchaseorder $purchaseorder)
    {
        try {
            $purchaseorder->delete();
        }catch(\Exception $e){
            return $e;
        }
        
        return response()->json(['message' => 'Se ha eliminado el item']);

        
    }

    
}
