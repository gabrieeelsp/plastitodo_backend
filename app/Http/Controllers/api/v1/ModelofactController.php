<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Modelofact;
use Illuminate\Http\Request;

use App\Http\Resources\v1\modelofacts\ModelofactResource;

class ModelofactController extends Controller
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

        //filtering is_enable
        if ( $request->has('filter_is_enable')) {
            $filter_is_enable = $request->get('filter_is_enable');
            if ( $filter_is_enable == 2 ) { array_push($atr, ['is_enable', true]); }
            if ( $filter_is_enable == 3 ) { array_push($atr, ['is_enable', false]); }
        }       

        
        $modelosfacts = Modelofact::orderBy('name', 'ASC')
            ->where($atr)->get();

        return ModelofactResource::collection($modelosfacts);
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
     * @param  \App\Models\Modelofact  $modelofact
     * @return \Illuminate\Http\Response
     */
    public function show(Modelofact $modelofact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modelofact  $modelofact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Modelofact $modelofact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modelofact  $modelofact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Modelofact $modelofact)
    {
        //
    }
}
