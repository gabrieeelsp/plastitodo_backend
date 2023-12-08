<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use Illuminate\Http\Request;

use App\Http\Resources\v1\familias\FamiliaResource;

use App\Http\Requests\v1\familias\CreateFamiliaRequest;

use Illuminate\Support\Facades\DB;

class FamiliaController extends Controller
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

        $limit = 50;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $items = Familia::orderBy('name', 'ASC')
            ->where($atr)
            ->paginate($limit);
        
        return FamiliaResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFamiliaRequest $request)
    {
        $familia = Familia::create($request->input('data.attributes'));

        $familia->save();

        return new FamiliaResource($familia);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Familia  $familia
     * @return \Illuminate\Http\Response
     */
    public function show(Familia $familia)
    {
        return new FamiliaResource($familia);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Familia  $familia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Familia $familia)
    {
        $familia->update($request->input('data.attributes'));

        $familia->save();

        return new FamiliaResource($familia);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Familia  $familia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Familia $familia)
    {
        //
    }

    public function remove_image(Request $request, $familia_id)
    {
        $familia = Familia::findOrFail($familia_id);
        
        $familia->image = null;

        $familia->save();

        return new FamiliaResource($familia);
    }

    public function updload_image(Request $request, $familia_id)
    {
        usleep(1000000);
        $familia = Familia::findOrFail($familia_id);

        $request->validate([
            'image' => 'required|image',
        ]);
        

        $url_image = $this->upload($request->file('image'));
        $familia->image = $url_image;

        $familia->save();

        return new FamiliaResource($familia);
    }

    private function upload($image)
    {
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/familias';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path", $rename);
        return "$post_path/$rename";
    }

    public function get_familias_select(Request $request)
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

        $familias = DB::table('familias')
                            ->where($atr)
                            ->select(
                                'familias.name',
                                'familias.id',
                                'familias.image',
                            )
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $familias;
    }
}
