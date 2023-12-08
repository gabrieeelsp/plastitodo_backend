<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Http\Resources\v1\catalogos\CatalogoListResource;
use App\Http\Resources\v1\catalogos\CatalogoResource;

use App\Http\Requests\v1\catalogos\CreateCatalogoRequest;

class CatalogoController extends Controller
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

        $atr_name = [];

        foreach ($val as $q) {
            array_push($atr_name, ['name', 'LIKE', '%'.strtolower($q).'%'] );            
        };

        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }
        
        $users = null;
        //Paginate?
        if ( $request->has('paginate')) {
            $paginate = $request->get('paginate');
            
            if ( $paginate == 0 ) { 
                $catalogos = Catalogo::orderBy('name', 'ASC')
                    ->where($atr_name)->get();
                    
                    return CatalogoListResource::collection($catalogos);

            }            
        }        
        
        $catalogos = Catalogo::orderBy('name', 'ASC')
            ->orWhere($atr_name)
            ->paginate($limit);       
        
        return CatalogoListResource::collection($catalogos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCatalogoRequest $request)
    {
        $data = $request->get('data');

        $catalogo = Catalogo::create($request->input('data.attributes'));

        $catalogo->save();

        return new CatalogoResource($catalogo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Catalogo  $catalogo
     * @return \Illuminate\Http\Response
     */
    public function show(Catalogo $catalogo)
    {
        return new CatalogoResource($catalogo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Catalogo  $catalogo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Catalogo $catalogo)
    {
        $catalogo->update($request->input('data.attributes'));

        $catalogo->save();

        return new CatalogoResource($catalogo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Catalogo  $catalogo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Catalogo $catalogo)
    {
        //
    }

    public function get_catalogos_select(Request $request)
    {

        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText );
        $atr = [];
        foreach ($val as $q) {
            array_push($atr, ['name', 'LIKE', '%'.strtolower($q).'%'] );
        };

        $limit = 10;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $catalogos = DB::table('catalogos')
                            ->where($atr)
                            ->select(
                                'catalogos.name',
                                'catalogos.id',
                                'catalogos.color',
                            )
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $catalogos;
    }
}
