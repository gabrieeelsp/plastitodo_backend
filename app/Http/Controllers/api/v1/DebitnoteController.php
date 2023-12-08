<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Debitnote;
use App\Models\Sale;
use App\Models\Debitnoteitem;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\v1\sales\DebitnoteSaleResource;

class DebitnoteController extends Controller
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
        $data = $request->get('data');
        try {
            DB::beginTransaction();
            $debitnote = Debitnote::create();

            $debitnote->user()->associate(Auth::user());
            $debitnote->sucursal()->associate($data['relationships']['sucursal']['data']['id']);

            $sale = Sale::findOrFail($data['relationships']['sale']['data']['id']);
            $debitnote->sale()->associate($sale);

            $debitnote->total = $data['attributes']['total'];

            if($sale->client){
                $saldo_cliente = $sale->client->saldo;
                $saldo_cliente = round($saldo_cliente - $debitnote->total, 6, PHP_ROUND_HALF_UP);
                $debitnote->saldo = $saldo_cliente;

                $sale->client->saldo = $saldo_cliente;
                $sale->client->save();
            }

            $items = $data['relationships']['debinoteitems'];
            foreach($items as $item){
                //velrifico que tenga ese saldo para descontar referido al ivaaliquot_id
                if($item['valor'] > $sale->get_subtotal_real_segun_iva($item['ivaaliquot_id'])) {
                    return response()->json(['message' => 'No se puede generar una nota de credito por el valor seleccionado'], 422);
                }
                
                $debitnoteItem = Debitnoteitem::create();
                $debitnoteItem->debitnote()->associate($debitnote);
                $debitnoteItem->ivaaliquot()->associate($item['ivaaliquot_id']);
                $debitnoteItem->valor = $item['valor'];

                $debitnoteItem->save();

            }

            $sale->saldo_sale = $sale->saldo_sale + $data['attributes']['total'];
            $sale->save();

            $debitnote->save();

            usleep(1000000);

            DB::commit();

            return new DebitnoteSaleResource($debitnote);
        } catch(\Exception $e) {


            DB::rollback();
            return $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Debitnote  $debitnote
     * @return \Illuminate\Http\Response
     */
    public function show(Debitnote $debitnote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Debitnote  $debitnote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Debitnote $debitnote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Debitnote  $debitnote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Debitnote $debitnote)
    {
        //
    }
}
