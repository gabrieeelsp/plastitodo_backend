<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stockmovement;
use App\Models\Stockmovementitem;
use App\Models\Stocksucursal;
use Illuminate\Http\Request;

use App\Http\Resources\v1\stockmovements\StockmovementListResource;
use App\Http\Resources\v1\stockmovements\StockmovementResource;

use Illuminate\Support\Facades\DB;

class StockmovementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $atr = [];

        if ( $request->has('sucursal_id') ) {
            array_push($atr, ['sucursal_id', '=', $request->get('sucursal_id')] );
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
                $stockmovements = Stockmovement::orderBy('created_at', 'DESC')
                    ->where($atr)->get();
                    
                    return StockmovementListResource::collection($stockmovements);

            }
            
        }
        
        
        $stockmovements = Stockmovement::orderBy('created_at', 'DESC')
            ->Where($atr)
            ->paginate($limit);       
        
        return StockmovementListResource::collection($stockmovements);
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
    public function new(Request $request)
    {
        $stockmovement = Stockmovement::create();

        $stockmovement->sucursal()->associate($request->get('sucursal_id'));
        $stockmovement->tipo = $request->get('tipo');
        $stockmovement->user()->associate(auth()->user()->id);
        $stockmovement->save();

        return new StockmovementListResource(Stockmovement::find($stockmovement->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stockmovement  $stockmovement
     * @return \Illuminate\Http\Response
     */
    public function show(Stockmovement $stockmovement)
    {
        return new StockmovementResource($stockmovement);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stockmovement  $stockmovement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stockmovement $stockmovement)
    {

        
        if ( $stockmovement->estado == 'CONFIRMADO' ) {
            return response()->json(['message' => 'El movimiento ya encuentra confirmado'], 422);
        }
        $is_confirmar = $request->get('data')['meta']['is_confirmar'];

        $data = $request->get('data');

        $stockmovement->comments = $data['attributes']['comments'];

        $smi_enviados = $data['relationships']['stockmovementitems'];
        $ids_enviados = [];
        try {
            DB::beginTransaction();

            foreach ( $stockmovement->stockmovementitems as $stockmovementitem ) {
                $eliminar = true;
                foreach ( $smi_enviados as $key => $smi_enviado ) {
                    if ( $smi_enviado['id'] == $stockmovementitem->id ) {
                        $stockmovementitem->cantidad = $smi_enviado['cantidad'];
                        unset($smi_enviados[$key]);
                        $eliminar = false;
                        $stockmovementitem->save();
                    }
                }
                if ( $eliminar ) {
                    $stockmovementitem->delete();
                }
            }

            foreach ( $smi_enviados as $smi_enviado ) {
                $stockmovementitem_nuevo = Stockmovementitem::create();
                $stockmovementitem_nuevo->stockmovement()->associate($stockmovement->id);
                $stockmovementitem_nuevo->stockproduct()->associate($smi_enviado['stockproduct_id']);
                $stockmovementitem_nuevo->cantidad = $smi_enviado['cantidad'];
                $stockmovementitem_nuevo->save();
            }


            if ( $is_confirmar ) {
                $stockmovement->estado = 'CONFIRMADO';
                $stockmovement->user()->associate(auth()->user()->id);

                foreach ( Stockmovementitem::where('stockmovement_id', $stockmovement->id)->get() as $smi_update_stock ) {
                    $stockproduct = $smi_update_stock->stockproduct;
                    $existe_stockSucursal = false;
                    foreach ( $stockproduct->stocksucursals as $stocksucursal ) {
                        if ( $stocksucursal->sucursal_id == $stockmovement->sucursal_id ) {
                            $existe_stockSucursal = true;
                            if ( $stockmovement->tipo == 'EGRESO' ) {
                                $stocksucursal->stock = $stocksucursal->stock - $smi_update_stock->cantidad;
                            }else {
                                $stocksucursal->stock = $stocksucursal->stock + $smi_update_stock->cantidad;
                            }
                            $stocksucursal->save();

                        }
                    }
                    if ( !$existe_stockSucursal ) {
                        $stocksucursal_nuevo = Stocksucursal::create();
                        $stocksucursal_nuevo->stockproduct()->associate($stockproduct->id);
                        $stocksucursal_nuevo->sucursal()->associate($stockmovement->sucursal_id);
                        if ( $stockmovement->tipo == 'EGRESO' ) {
                            $stocksucursal_nuevo->stock = 0 - $smi_update_stock->cantidad;
                        }else {
                            $stocksucursal_nuevo->stock = $smi_update_stock->cantidad;
                        }
                        $stocksucursal_nuevo->save();

                    }
                    
                }
                
            }


            $stockmovement->save();
            
            DB::commit();
            return new StockmovementResource(Stockmovement::find($stockmovement->id));
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stockmovement  $stockmovement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stockmovement $stockmovement)
    {
        if ( $stockmovement->estado == 'CONFIRMADO' ) {
            return response()->json(['message' => 'El movimiento ya encuentra confirmado'], 422);
        }
        try {
            $stockmovement->delete();
        }catch(\Exception $e){
            return $e;
        }
        
        return response()->json(['message' => 'Se ha eliminado el item']);
    }
}
