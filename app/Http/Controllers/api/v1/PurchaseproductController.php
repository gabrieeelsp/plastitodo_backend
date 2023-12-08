<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Purchaseproduct;
use Illuminate\Http\Request;

use App\Http\Resources\v1\purchaseproducts\PurchaseproductResource;

use App\Http\Requests\v1\purchaseproducts\CreatePurchaseproductRequest;


class PurchaseproductController extends Controller
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
    public function store(CreatePurchaseproductRequest $request)
    {

        $data = $request->get('data');
        $stockproduct_id = $data['relationships']["stockproduct"]["data"]["id"];
        $supplier_id = $data['relationships']["supplier"]["data"]["id"];

        $purchaseproduct = Purchaseproduct::create($request->input('data.attributes'));

        $purchaseproduct->stockproduct()->associate($stockproduct_id);
        $purchaseproduct->supplier()->associate($supplier_id);
        $purchaseproduct->save();

        return new PurchaseproductResource($purchaseproduct);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchaseproduct  $purchaseproduct
     * @return \Illuminate\Http\Response
     */
    public function show(Purchaseproduct $purchaseproduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchaseproduct  $purchaseproduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchaseproduct $purchaseproduct)
    {
        $purchaseproduct->update($request->input('data.attributes')); 

        $purchaseproduct->save();
        $purchaseproduct_saved = Purchaseproduct::find($purchaseproduct->id);

        return new PurchaseproductResource($purchaseproduct_saved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchaseproduct  $purchaseproduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchaseproduct $purchaseproduct)
    {
        //
    }
}
