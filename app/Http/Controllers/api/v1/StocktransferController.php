<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stocktransfer;
use App\Models\Stocktransferitem;
use App\Models\Stocksucursal;
use Illuminate\Http\Request;

use App\Http\Resources\v1\stocktransfers\StocktransferListResource;
use App\Http\Resources\v1\stocktransfers\StocktransferResource;

use App\Http\Requests\v1\stocktransfers\CreateStocktransfer;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StocktransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $atr = [];

        if ( $request->has('sucursal_origen_id') ) {
            array_push($atr, ['sucursal_origen_id', '=', $request->get('sucursal_origen_id')] );
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
                $stocktransfer = Stocktransfer::orderBy('created_at', 'DESC')
                    ->where($atr)->get();
                    
                    return StocktransferListResource::collection($stocktransfer);

            }
            
        }
        
        
        $stocktransfer = Stocktransfer::orderBy('created_at', 'DESC')
            ->Where($atr)
            ->paginate($limit);       
        
        return StocktransferListResource::collection($stocktransfer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStocktransfer $request)
    {
        $stocktransfer = Stocktransfer::create($request->input('data.relationships'));

        $stocktransfer->user_origen()->associate(auth()->user());

        $stocktransfer->save();

        return new StocktransferResource(Stocktransfer::find($stocktransfer->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stocktransfer  $stocktransfer
     * @return \Illuminate\Http\Response
     */
    public function show(Stocktransfer $stocktransfer)
    {
        return new StocktransferResource($stocktransfer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stocktransfer  $stocktransfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stocktransfer $stocktransfer)
    {
        //return $request->all();
        
        $event = $request->get('evento');

        if (  $event == 'ENVIAR' && $stocktransfer->estado == 'PREPARADO' ) {
            $stocktransfer->estado = 'EN DISTRIBUCION';
            $stocktransfer->save();
            return new StocktransferResource(Stocktransfer::find($stocktransfer->id));
        }
        if (  $event == 'CANCELAR ENVIO' && $stocktransfer->estado == 'EN DISTRIBUCION' ) {
            $stocktransfer->estado = 'PREPARADO';
            $stocktransfer->save();
            return new StocktransferResource(Stocktransfer::find($stocktransfer->id));
        }
        if ( ( $event == 'INICIAR INGRESO' && $stocktransfer->estado == 'EN DISTRIBUCION' ) ) {
            $stocktransfer->estado = 'INGRESANDO';
            $stocktransfer->user_destino()->associate(auth()->user());
            $stocktransfer->recibido_at = Carbon::now();
        }
        if ( ( $event == 'EDITAR INGRESO' && $stocktransfer->estado == 'RECIBIDO' ) ) {
            $stocktransfer->estado = 'INGRESANDO';
        }

        try {
            DB::beginTransaction();


            if ( ( $event == 'GUARDAR' && $stocktransfer->estado == 'EDITANDO' ) || ( $event == 'FINALIZAR' && $stocktransfer->estado == 'EDITANDO' ) ) {
                $stocktransfer->stocktransferitems()->delete();
                $items = $request->get('items');
                foreach($items as $item){
                    $stocktransferitem = new Stocktransferitem;

                    $stocktransferitem->stocktransfer()->associate($stocktransfer);
                    $stocktransferitem->stockproduct()->associate($item['stockproduct_id']);
                    $stocktransferitem->cantidad = $item['cantidad'];

                    $stocktransferitem->save();
                }
                

                if ( $event == 'FINALIZAR' ) {
                    $stocktransfer->estado = 'FINALIZADO';
                }
            }

            
            if ( ( $event == 'GUARDAR' && $stocktransfer->estado == 'EN PREPARACION' ) || ( $event == 'FINALIZAR PREPARACION' && $stocktransfer->estado == 'EN PREPARACION' ) ) {
                $items = $request->get('items');
                
                foreach($items as $item){

                    foreach ( $stocktransfer->stocktransferitems as $stocktransferitem ) {
                        if ( $stocktransferitem->stockproduct_id == $item['stockproduct_id'] ) {

                            if ( $stocktransferitem->is_prepared && !boolval($item['is_prepared']) ) {
                                //devolver stock
                                $this->devolver_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_origen, $stocktransferitem->cantidad );
                            }
                            
                            if ( !$stocktransferitem->is_prepared && boolval($item['is_prepared']) ) {
                                //tomar stock
                                $this->tomar_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_origen, $stocktransferitem->cantidad );
                            }

                            $stocktransferitem->is_prepared = boolval($item['is_prepared']);
                            $stocktransferitem->save();
                        }
                    }
                }

                
            }

            if ( ( $event == 'GUARDAR' && $stocktransfer->estado == 'INGRESANDO' ) || ( $event == 'FINALIZAR INGRESO' && $stocktransfer->estado == 'INGRESANDO' ) ) {
                $items = $request->get('items');
                
                foreach($items as $item){

                    foreach ( $stocktransfer->stocktransferitems as $stocktransferitem ) {
                        if ( $stocktransferitem->stockproduct_id == $item['stockproduct_id'] ) {

                            if ( $stocktransferitem->is_recibido && !boolval($item['is_recibido']) ) {
                                //devolver stock
                                $this->tomar_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_destino, $stocktransferitem->cantidad );
                            }
                            
                            if ( !$stocktransferitem->is_recibido && boolval($item['is_recibido']) ) {
                                //tomar stock
                                $this->devolver_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_destino, $stocktransferitem->cantidad );
                            }

                            $stocktransferitem->is_recibido = boolval($item['is_recibido']);
                            $stocktransferitem->save();
                        }
                    }
                }
                
            }

            if ( ( $event == 'FINALIZAR INGRESO' && $stocktransfer->estado == 'INGRESANDO' ) ) {
                $stocktransfer->estado = 'RECIBIDO';
                $stocktransfer->is_recibido = true;
            }

            if ( ( $event == 'CANCELAR PREPARACION' && $stocktransfer->estado == 'EN PREPARACION' ) ) {

                foreach ( $stocktransfer->stocktransferitems as $stocktransferitem ) {
                    if ( $stocktransferitem->is_prepared ) {
                        //devolver stock
                        $this->devolver_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_origen, $stocktransferitem->cantidad );

                        $stocktransferitem->is_prepared = false;
                        $stocktransferitem->save();
                    }
                    
                }
                
                $stocktransfer->estado = 'FINALIZADO';
                
            }

            if ( ( $event == 'CANCELAR INGRESO' && $stocktransfer->estado == 'INGRESANDO' ) ) {

                foreach ( $stocktransfer->stocktransferitems as $stocktransferitem ) {
                    if ( $stocktransferitem->is_recibido ) {
                        //devolver stock
                        $this->tomar_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_destino, $stocktransferitem->cantidad );

                        $stocktransferitem->is_recibido = false;
                        $stocktransferitem->save();
                    }
                    
                }

                
                $stocktransfer->estado = 'EN DISTRIBUCION';
                $stocktransfer->user_destino()->associate(null);
                $stocktransfer->recibido_at = null;
                
            }

            if ( ( $event == 'EDITAR' && $stocktransfer->estado == 'PREPARADO' ) ) {
                
                foreach ( $stocktransfer->stocktransferitems as $stocktransferitem ) {
                    if ( $stocktransferitem->is_prepared ) {
                        //devolver stock
                        $this->devolver_stock ( $stocktransferitem->stockproduct, $stocktransfer->sucursal_origen, $stocktransferitem->cantidad );

                        $stocktransferitem->is_prepared = false;
                        $stocktransferitem->save();
                    }                    
                }

                $stocktransfer->estado = 'EDITANDO';
                
            }

            if ( ( $event == 'FINALIZAR PREPARACION' && $stocktransfer->estado == 'EN PREPARACION' ) ) {
                $stocktransfer->estado = 'PREPARADO';
            }

            if ( ( $event == 'EDITAR' && $stocktransfer->estado == 'FINALIZADO' ) ) {
                $stocktransfer->estado = 'EDITANDO';
            }

            if ( ( $event == 'INICIAR PREPARACION' && $stocktransfer->estado == 'FINALIZADO' ) ) {
                $stocktransfer->estado = 'EN PREPARACION';
            }

            $stocktransfer->save();

            usleep(1000000);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return new StocktransferResource(Stocktransfer::find($stocktransfer->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stocktransfer  $stocktransfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stocktransfer $stocktransfer)
    {
        if ( $stocktransfer->estado == 'RECIBIDO' ) {
            return response()->json(['message' => 'El movimiento ya encuentra Recibido'], 422);
        }
        try {
            $stocktransfer->delete();
        }catch(\Exception $e){
            return $e;
        }
        
        return response()->json(['message' => 'Se ha eliminado el item']);
    }


    private function tomar_stock ( $stockproduct, $sucursal, $cantidad ) {
        $stockSucursal = Stocksucursal::where('stockproduct_id', $stockproduct->id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        if ( !$stockSucursal ) {
            $stockSucursal = Stocksucursal::create();
            $stockSucursal->stock = 0;
            $stockSucursal->stockproduct()->associate($stockproduct->id);
            $stockSucursal->sucursal()->associate($sucursal->id);
            $stockSucursal->save();

        }
        $stockSucursal->stock = $stockSucursal->stock - $cantidad;

        $stockSucursal->save();
    }

    private function devolver_stock ( $stockproduct, $sucursal, $cantidad ) {
        $stockSucursal = Stocksucursal::where('stockproduct_id', $stockproduct->id)
            ->where('sucursal_id', $sucursal->id)
            ->first();
        if ( !$stockSucursal ) {
            $stockSucursal = Stocksucursal::create();
            $stockSucursal->stock = 0;
            $stockSucursal->stockproduct()->associate($stockproduct->id);
            $stockSucursal->sucursal()->associate($sucursal->id);
            $stockSucursal->save();

        }
        $stockSucursal->stock = $stockSucursal->stock + $cantidad;

        $stockSucursal->save();
    }
}
