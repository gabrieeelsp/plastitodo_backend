<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Creditnote;
use App\Models\Sale;
use App\Models\Creditnoteitem;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\v1\sales\CreditnoteSaleResource;


class CreditnoteController extends Controller
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
            $creditnote = Creditnote::create();

            $creditnote->user()->associate(Auth::user());
            $creditnote->sucursal()->associate($data['relationships']['sucursal']['data']['id']);

            $sale = Sale::findOrFail($data['relationships']['sale']['data']['id']);
            $creditnote->sale()->associate($sale);

            $creditnote->total = $data['attributes']['total'];

            if($sale->client){
                $saldo_cliente = $sale->client->saldo;
                $saldo_cliente = round($saldo_cliente - $creditnote->total, 6, PHP_ROUND_HALF_UP);
                $creditnote->saldo = $saldo_cliente;

                $sale->client->saldo = $saldo_cliente;
                $sale->client->save();
            }

            $items = $data['relationships']['creditnoteitems'];
            foreach($items as $item){
                //velrifico que tenga ese saldo para descontar referido al ivaaliquot_id
                if($item['valor'] > $sale->get_subtotal_real_segun_iva($item['ivaaliquot_id'])) {
                    return response()->json(['message' => 'No se puede generar una nota de credito por el valor seleccionado'], 422);
                }
                
                $creditnoteItem = Creditnoteitem::create();
                $creditnoteItem->creditnote()->associate($creditnote);
                $creditnoteItem->ivaaliquot()->associate($item['ivaaliquot_id']);
                $creditnoteItem->valor = $item['valor'];

                $creditnoteItem->save();

            }

            $sale->saldo_sale = $sale->saldo_sale - $data['attributes']['total'];
            $sale->save();

            $creditnote->save();

            usleep(1000000);

            DB::commit();

            return new CreditnoteSaleResource($creditnote);
        } catch(\Exception $e) {


            DB::rollback();
            return $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Creditnote  $creditnote
     * @return \Illuminate\Http\Response
     */
    public function show(Creditnote $creditnote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Creditnote  $creditnote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Creditnote $creditnote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Creditnote  $creditnote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Creditnote $creditnote)
    {
        //
    }
}
