<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Http\Resources\v1\tags\TagListResource;
use App\Http\Resources\v1\tags\TagResource;

use App\Http\Requests\v1\tags\CreateTagRequest;

class TagController extends Controller
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
                $tags = Tag::orderBy('name', 'ASC')
                    ->where($atr_name)->get();
                    
                    return TagListResource::collection($tags);

            }            
        }        
        
        $tags = Tag::orderBy('name', 'ASC')
            ->orWhere($atr_name)
            ->paginate($limit);       
        
        return TagListResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTagRequest $request)
    {
        $data = $request->get('data');

        $tag = Tag::create($request->input('data.attributes'));

        $tag->save();

        return new TagResource($tag);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $tag->update($request->input('data.attributes'));

        $tag->save();

        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //
    }

    public function get_tags_select(Request $request)
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

        $tags = DB::table('tags')
                            ->where($atr)
                            ->select(
                                'tags.name',
                                'tags.id',
                                'tags.color',
                            )
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $tags;
    }
}
