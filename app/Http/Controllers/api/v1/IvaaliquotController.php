<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Ivaaliquot;
use Illuminate\Http\Request;

use App\Http\Resources\v1\ivaaliquots\IvaaliquotResource;

class IvaaliquotController extends Controller
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
        
        $items = Ivaaliquot::orderBy('name', 'ASC')
            ->where($atr)->get();

        return IvaaliquotResource::collection($items);
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
     * @param  \App\Models\Ivaaliquot  $ivaaliquot
     * @return \Illuminate\Http\Response
     */
    public function show(Ivaaliquot $ivaaliquot)
    {
        return new IvaaliquotResource($ivaaliquot);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ivaaliquot  $ivaaliquot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ivaaliquot $ivaaliquot)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ivaaliquot  $ivaaliquot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ivaaliquot $ivaaliquot)
    {
        //
    }
}
