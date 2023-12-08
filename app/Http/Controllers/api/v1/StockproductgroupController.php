<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stockproductgroup;
use Illuminate\Http\Request;

use App\Http\Resources\v1\stockproductgroups\StockproductgroupResource;

use App\Http\Requests\v1\stockproductgroups\CreateStockproductgroupRequest;

class StockproductgroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText);
        $atr = [];
        foreach($val as $q) {
            array_push($atr, ['name', 'LIKE', '%'.strtolower($q).'%']);
        };

        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $items = Stockproductgroup::orderBy('name', 'ASC')
            ->where($atr)
            ->paginate($limit);
        
        return StockproductgroupResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStockproductgroupRequest $request)
    {
        $data = $request->get('data');

        $stockproductgroup = Stockproductgroup::create($request->input('data.attributes'));

        $stockproductgroup->save();

        return new StockproductgroupResource($stockproductgroup);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stockproductgroup  $stockproductgroup
     * @return \Illuminate\Http\Response
     */
    public function show(Stockproductgroup $stockproductgroup)
    {
        return new StockproductgroupResource($stockproductgroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stockproductgroup  $stockproductgroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stockproductgroup $stockproductgroup)
    {
        $stockproductgroup->update($request->input('data.attributes'));
        $stockproductgroup->save();
        return new StockproductgroupResource($stockproductgroup);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stockproductgroup  $stockproductgroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stockproductgroup $stockproductgroup)
    {
        //
    }
}
