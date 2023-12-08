<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;

use App\Models\Caja;
use App\Models\Sale;

use App\Http\Requests\v1\refunds\CreateRefundRequest;

use App\Http\Resources\v1\sales\RefundResource;

use App\Http\Resources\v1\refunds\RefundListResource;

use Illuminate\Support\Facades\DB;

class RefundController extends Controller
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

            $refunds = Refund::orderBy('id', 'DESC')
                ->where($atr)
                ->whereBetween('created_at', [$date_from, $date_to . ' 23:59:59'])
                ->paginate($limit);
            return RefundListResource::collection($refunds);
        }

        // sin date_ftom-------
        $refunds = Refund::orderBy('id', 'DESC')
            ->where($atr)
            ->where($atr)
            ->paginate($limit);
        return RefundListResource::collection($refunds);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRefundRequest $request)
    {
        $data = $request->get('data');

        $caja = Caja::find($data['relationships']['caja']['data']['id']);

        if ( !$caja->is_open ){
            return response()->json(['message' => 'Caja Cerrada'], 422);
        }

        $sale = Sale::find($data['relationships']['sale']['data']['id']);

        try{
            DB::beginTransaction();

            $saleRefund = new Refund;

            $saleRefund->paymentmethod()->associate($data['relationships']['paymentmethod']['data']['id']);
            $paymentmethod = Paymentmethod::findOrFail($data['relationships']['paymentmethod']['data']['id']);
            if ( $paymentmethod->requires_confirmation ) {
                $saleRefund->is_confirmed = false;
            }else {
                $saleRefund->is_confirmed = true;
            }

            $saleRefund->sale()->associate($sale);
            
            $saleRefund->caja()->associate($caja);

            $saleRefund->valor = $data['attributes']['valor'];

            if ( $sale->client ){
                $saldo_cliente = $sale->client->saldo;
                $saldo_cliente = $saldo_cliente + $saleRefund->valor;

		$saleRefund->saldo = $saldo_cliente;
		$sale->client->saldo = $sale->client->saldo + $saleRefund->valor;
                $sale->client->save();
            }

            $sale->saldo_sale = $sale->saldo_sale + $data['attributes']['valor'];
            $sale->save();

            $saleRefund->save();

            usleep(1000000);
            
            DB::commit();
            // all good

        }  catch (\Exception $e) {
            DB::rollback();
            return $e;
            // something went wrong
        } 
        return new RefundResource($saleRefund);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function show(Refund $refund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Refund $refund)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function destroy(Refund $refund)
    {
        //
    }

    public function confirm ( $refund_id )
    {
        $refund = Refund::findOrFail($refund_id);
        $refund->is_confirmed = true;
        $refund->save();

        return new RefundResource($refund); 
    }

    public function no_confirm ( $refund_id )
    {
        $refund = Refund::findOrFail($refund_id);
        $refund->is_confirmed = false;
        $refund->save();

        return new RefundResource($refund); 
    }
}
