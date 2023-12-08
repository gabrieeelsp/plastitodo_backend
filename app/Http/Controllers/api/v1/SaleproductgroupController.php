<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Saleproductgroup;
use Illuminate\Http\Request;

use App\Http\Resources\v1\saleproductgroups\SaleproductgroupResource;

use App\Http\Requests\v1\saleproductgroups\CreateSaleproductgroupRequest;

class SaleproductgroupController extends Controller
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

        $items = Saleproductgroup::orderBy('name', 'ASC')
            ->where($atr)
            ->paginate($limit);
        
        return SaleproductgroupResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSaleproductgroupRequest $request)
    {
        $data = $request->get('data');

        $saleproductgroup = Saleproductgroup::create($request->input('data.attributes'));

        $saleproductgroup->save();

        return new SaleproductgroupResource($saleproductgroup);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Saleproductgroup  $saleproductgroup
     * @return \Illuminate\Http\Response
     */
    public function show(Saleproductgroup $saleproductgroup)
    {
        return new SaleproductgroupResource($saleproductgroup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Saleproductgroup  $saleproductgroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Saleproductgroup $saleproductgroup)
    {
        $saleproductgroup->update($request->input('data.attributes'));
        $saleproductgroup->save();
        return new SaleproductgroupResource($saleproductgroup);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Saleproductgroup  $saleproductgroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Saleproductgroup $saleproductgroup)
    {
        //
    }
}
