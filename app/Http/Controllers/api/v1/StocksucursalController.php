<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stocksucursal;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class StocksucursalController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stocksucursal  $stocksucursal
     * @return \Illuminate\Http\Response
     */
    public function show(Stocksucursal $stocksucursal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stocksucursal  $stocksucursal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stocksucursal $stocksucursal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stocksucursal  $stocksucursal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stocksucursal $stocksucursal)
    {
        //
    }

    public function update_values ( Request $request )
    {
        try {
            DB::beginTransaction();

            $items = $request->get('data')['stocksucursals'];

            foreach ( $items as $item ) {
                $stocksucursal = Stocksucursal::find($item['id']);
                $stocksucursal->stock_minimo = $item['stock_minimo'];
                $stocksucursal->stock_maximo = $item['stock_maximo'];
                $stocksucursal->save();
            }

            DB::commit();
            return true;
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }
}
