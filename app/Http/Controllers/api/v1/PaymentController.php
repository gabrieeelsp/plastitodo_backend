<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;

use App\Models\Caja;
use App\Models\Sale;

use App\Http\Requests\v1\payments\CreatePaymentRequest;

use App\Http\Resources\v1\sales\PaymentResource;
use App\Http\Resources\v1\payments\PaymentListResource;

use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
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
        //return $request->all();

        $atr = [];


        if ( $request->has('is_confirmed') ) {
            
            array_push($atr, ['is_confirmed', filter_var($request->get('is_confirmed'), FILTER_VALIDATE_BOOL)] );
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

            $payments = Payment::orderBy('id', 'DESC')
                ->where($atr)
                ->whereBetween('created_at', [$date_from, $date_to . ' 23:59:59'])
                ->paginate($limit);
            return PaymentListResource::collection($payments);
        }

        // sin date_ftom-------
        $payments = Payment::orderBy('id', 'DESC')
            ->where($atr)
            ->where($atr)
            ->paginate($limit);
        return PaymentListResource::collection($payments);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePaymentRequest $request)
    {
        $data = $request->get('data');

        $caja = Caja::find($data['relationships']['caja']['data']['id']);

        if ( !$caja->is_open ){
            return response()->json(['message' => 'Caja Cerrada'], 422);
        }

        $sale = Sale::find($data['relationships']['sale']['data']['id']);

        try{
            DB::beginTransaction();

            $salePayment = new Payment;

            $salePayment->paymentmethod()->associate($data['relationships']['paymentmethod']['data']['id']);
            $paymentmethod = Paymentmethod::findOrFail($data['relationships']['paymentmethod']['data']['id']);
            if ( $paymentmethod->requires_confirmation ) {
                $salePayment->is_confirmed = false;
            }else {
                $salePayment->is_confirmed = true;
            }


            $salePayment->sale()->associate($sale);
            
            $salePayment->caja()->associate($caja);

            $salePayment->valor = $data['attributes']['valor'];

            if ( $sale->client ){
                $saldo_cliente = $sale->client->saldo;
                $saldo_cliente = $saldo_cliente - $salePayment->valor;

		$salePayment->saldo = $saldo_cliente;
		$sale->client->saldo = $sale->client->saldo - $salePayment->valor;
                $sale->client->save();
            }

            $sale->saldo_sale = $sale->saldo_sale - $data['attributes']['valor'];
            $sale->save();

            $salePayment->save();

            usleep(1000000);
            
            DB::commit();
            // all good

        }  catch (\Exception $e) {
            DB::rollback();
            return $e;
            // something went wrong
        } 
        return new PaymentResource($salePayment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
    public function confirm ( $payment_id )
    {
        $payment = Payment::findOrFail($payment_id);
        $payment->is_confirmed = true;
        $payment->save();

        return new PaymentResource($payment); 
    }

    public function no_confirm ( $payment_id )
    {
        $payment = Payment::findOrFail($payment_id);
        $payment->is_confirmed = false;
        $payment->save();

        return new PaymentResource($payment); 
    }
}
