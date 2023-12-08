<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Deliveryshift;
use Illuminate\Http\Request;

use App\Http\Resources\v1\deliveryshifts\DeliveryshiftResource;

class DeliveryshiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText );
        $atr = [];
        foreach ($val as $q) {
            array_push($atr, ['name', 'LIKE', '%'.strtolower($q).'%'] );
        };
        
        $items = Deliveryshift::orderBy('name', 'ASC')
            ->where($atr)->get();

        return DeliveryshiftResource::collection($items);
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
     * @param  \App\Models\Deliveryshift  $deliveryshift
     * @return \Illuminate\Http\Response
     */
    public function show(Deliveryshift $deliveryshift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deliveryshift  $deliveryshift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deliveryshift $deliveryshift)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deliveryshift  $deliveryshift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deliveryshift $deliveryshift)
    {
        //
    }
}
