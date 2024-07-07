<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Saleproduct;
use App\Models\Combo;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\DB;

use App\Http\Resources\v1\saleproducts\SaleproductResource;
use App\Http\Resources\v1\saleproducts\SaleproductVentaResource;

use App\Http\Requests\v1\saleproducts\CreateSaleproductRequest;

use Carbon\Carbon;

class SaleproductController extends Controller
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
        $atr_date = [];
        foreach($val as $q) {
            array_push($atr, ['name', 'LIKE', '%'.strtolower($q).'%']);
        }
        
<<<<<<< HEAD
        
	if ( $request->has('is_enable')) {
	    if (filter_var($request->get('is_enable'), FILTER_VALIDATE_BOOL)) {
		array_push($atr, ['is_enable', true]);
	    } else {
		array_push($atr, ['is_enable', false]);
	    }
	}
=======
        if ( $request->has('is_enable')) {
            if (filter_var($request->get('is_enable'), FILTER_VALIDATE_BOOL)) {
                array_push($atr, ['is_enable', true]);
            } else {
                array_push($atr, ['is_enable', false]);
            }
        }

>>>>>>> ee958a6682413aa0298ce1e116b5e421d135ed46

        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if( $request->has('is_promo') ){
            $hoy = Carbon::today();
            if ( filter_var($request->get('is_promo'), FILTER_VALIDATE_BOOL) ) {
                $items = Saleproduct::orderBy('name', 'ASC')
                    ->whereDate('fecha_desc_desde', '<=', $hoy)
                    ->whereDate('fecha_desc_hasta', '>=', $hoy)
                    ->where($atr)
                    ->paginate($limit);
            }else {
                $items = Saleproduct::orderBy('name', 'ASC')
                    ->whereDate('fecha_desc_desde', '>', $hoy)
                    ->orWhereDate('fecha_desc_hasta', '<', $hoy)
                    ->orWhere('fecha_desc_desde', '=', null)
                    ->where($atr)
                    ->paginate($limit);
            }
            
            return SaleproductResource::collection($items);
        }

        $items = Saleproduct::orderBy('name', 'ASC')
            ->where($atr)
            ->paginate($limit);
        
        return SaleproductResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSaleproductRequest $request)
    {

        $data = $request->get('data');
        $stockproduct_id = $data['relationships']["stockproduct"]["data"]["id"];

        $saleproduct = Saleproduct::create($request->input('data.attributes'));

        $saleproduct->stockproduct()->associate($stockproduct_id);
        $saleproduct->save();

        $saleproduct->set_precios($saleproduct->stockproduct->costo);
        $saleproduct->save();

        return new SaleproductResource($saleproduct);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Saleproduct  $saleproduct
     * @return \Illuminate\Http\Response
     */
    public function show(Saleproduct $saleproduct)
    {
        return new SaleproductResource($saleproduct);
    }

    public function get_saleproduct_siblings ( $saleproduct_id )
    {
	    //return $saleproduct_id;
        $saleproduct = Saleproduct::findOrFail($saleproduct_id);
	return new SaleproductVentaResource($saleproduct);
    } 

    public function search_barcode(Request $request)
    {
        $saleproduct = Saleproduct::where('barcode', $request->get('barcode'))->get()->first();
        return new SaleproductVentaResource($saleproduct);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Saleproduct  $saleproduct
     * @return \Illuminate\Http\Response
     */

    public function remove_image(Request $request, $saleproduct_id)
    {

        $saleproduct = Saleproduct::findOrFail($saleproduct_id);

        $request->validate([
            'order' => 'required|int'
        ]);
        
        if ( $request->get('order') == 1) {
            $saleproduct->image1 = null;
        }
        if ( $request->get('order') == 2) {
            $saleproduct->image2 = null;
        }
        if ( $request->get('order') == 3) {
            $saleproduct->image3 = null;
        }

        $saleproduct->save();

        return new SaleproductResource($saleproduct);
    }

    public function updload_image(Request $request, $saleproduct_id)
    {
        usleep(1000000);
        $saleproduct = Saleproduct::findOrFail($saleproduct_id);

        $request->validate([

            'image' => 'required|image',
            'order' => 'required|int'
        ]);
        

        $url_image = $this->upload($request->file('image'));
        if ( $request->get('order') == 1) {
            $saleproduct->image1 = $url_image;
        }
        if ( $request->get('order') == 2) {
            $saleproduct->image2 = $url_image;
        }
        if ( $request->get('order') == 3) {
            $saleproduct->image3 = $url_image;
        }

        $saleproduct->save();

        return new SaleproductResource($saleproduct);
    }

    private function upload($image)
    {
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/saleproducts';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path", $rename);
        return "$post_path/$rename";
    }

    public function update_values(Request $request, $saleproduct_id)
    {   
        $saleproduct = Saleproduct::findOrFail($saleproduct_id);

        $combos_to_update = [];

        if ( $request->has('data.update_group')) {
            if ( $request->get('data')['update_group'] == true ) {
                if ( $saleproduct->saleproductgroup ) {
                    $saleproducts = Saleproduct::where('saleproductgroup_id', $saleproduct->saleproductgroup_id)->get();

                    $data = $request->get('data');
                    
                    try {
                        DB::beginTransaction();

                        foreach ( $saleproducts as $itemGroup ) {
                            $itemGroup->porc_min = $data['attributes']['porc_min'];
                            $itemGroup->porc_may = $data['attributes']['porc_may'];
                            $itemGroup->precision_min = $data['attributes']['precision_min'];
                            $itemGroup->precision_may = $data['attributes']['precision_may'];

                            $itemGroup->set_precios($itemGroup->stockproduct->costo);
                            $itemGroup->save();

                            foreach ( $saleproduct->comboitems as $comboitem ) {
                                if ( !in_array($comboitem->combo_id, $combos_to_update ) ) {
                                    array_push($combos_to_update, $comboitem->combo_id);
                                }
                            }
                                                        
                        }

                        $combos = Combo::whereIn('id', $combos_to_update)->get();
                        foreach ( $combos as $combo ) {
                            $combo->setPrecios();
                            $combo->save();
                        }

                        DB::commit();
                    }catch(\Exception $e){
                        DB::rollback();
                        return $e;
                    }
                    return SaleproductResource::collection($saleproducts);
                }
                

                
            }

        }
        $data = $request->get('data');

        try {
            DB::beginTransaction();

            $saleproduct->porc_min = $data['attributes']['porc_min'];
            $saleproduct->porc_may = $data['attributes']['porc_may'];
            $saleproduct->precision_min = $data['attributes']['precision_min'];
            $saleproduct->precision_may = $data['attributes']['precision_may'];
            
            $saleproduct->set_precios($saleproduct->stockproduct->costo);

            $saleproduct->save();

            foreach ( $saleproduct->comboitems as $comboitem ) {
                if ( !in_array($comboitem->combo_id, $combos_to_update ) ) {
                    array_push($combos_to_update, $comboitem->combo_id);
                }
            }

            $combos = Combo::whereIn('id', $combos_to_update)->get();
            foreach ( $combos as $combo ) {
                $combo->setPrecios();
                $combo->save();
            }

            $saleproduct_saved = Saleproduct::find($saleproduct->id);

            DB::commit();
            return new SaleproductResource($saleproduct_saved);
            
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function update_desc_values(Request $request, $saleproduct_id)
    {   
        //return $request->all();
        $saleproduct = Saleproduct::findOrFail($saleproduct_id);

        if ( $request->has('data.update_group')) {
            if ( $request->get('data')['update_group'] == true ) {
                if ( $saleproduct->saleproductgroup ) {
                    $saleproducts = Saleproduct::where('saleproductgroup_id', $saleproduct->saleproductgroup_id)->get();

                    $data = $request->get('data');
                    
                    try {
                        DB::beginTransaction();

                        foreach ( $saleproducts as $itemGroup ) {
                            $itemGroup->desc_min = $data['attributes']['desc_min'];
                            $itemGroup->desc_may = $data['attributes']['desc_may'];

                            $itemGroup->fecha_desc_hasta = new Carbon(substr($data['attributes']['fecha_desc_desde'], 0, 10));
                            $itemGroup->fecha_desc_hasta = new Carbon(substr($data['attributes']['fecha_desc_hasta'], 0, 10) .'23:59');

                            $itemGroup->set_precios($itemGroup->stockproduct->costo);
                            $itemGroup->save();
                                                        
                        }

                        DB::commit();
                    }catch(\Exception $e){
                        DB::rollback();
                        return $e;
                    }
                    return SaleproductResource::collection($saleproducts);
                }
                

                
            }

        }
        $data = $request->get('data');

        try {
            DB::beginTransaction();

            $saleproduct->desc_min = $data['attributes']['desc_min'];
            $saleproduct->desc_may = $data['attributes']['desc_may'];

            $saleproduct->fecha_desc_desde = new Carbon(substr($data['attributes']['fecha_desc_desde'], 0, 10));
            $saleproduct->fecha_desc_hasta = new Carbon(substr($data['attributes']['fecha_desc_hasta'], 0, 10) .' 23:59:00');
            
            $saleproduct->set_precios($saleproduct->stockproduct->costo);
               
            $saleproduct->save();

            DB::commit();
            
            
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
        $saleproduct_saved = Saleproduct::find($saleproduct->id);
        return new SaleproductResource($saleproduct_saved);
    }

    public function update(Request $request, Saleproduct $saleproduct)
    {   

        try {
            DB::beginTransaction();

            $saleproduct->update($request->input('data.attributes')); 

            if ( $request->has('data.relationships.saleproductgroup')) { 
                if ( $request->get('data')['relationships']['saleproductgroup']['id'] != 0 ) { 
                    $saleproduct->saleproductgroup_id = $request->get('data')['relationships']['saleproductgroup']['id'];
                }else {
                    $saleproduct->saleproductgroup_id = null;
                }   
            }

            $saleproduct->save();
            $saleproduct_saved = Saleproduct::find($saleproduct->id);
            $saleproduct_saved->set_precios($saleproduct->stockproduct->costo);
            $saleproduct_saved->save();

            $combos_to_update = [];
            foreach ( $saleproduct->comboitems as $comboitem ) {
                if ( !in_array($comboitem->combo_id, $combos_to_update ) ) {
                    array_push($combos_to_update, $comboitem->combo_id);
                }
            }

            $combos = Combo::whereIn('id', $combos_to_update)->get();
            foreach ( $combos as $combo ) {
                $combo->setPrecios();
                $combo->save();
            }

            if ( $request->has('data.relationships.tags')) {
                $saleproduct->tags()->detach();
                foreach ( $request->get('data')['relationships']['tags'] as $tag ) {
                    $saleproduct->tags()->attach($tag['id']);
                }
            }

            if ( $request->has('data.relationships.catalogos')) {
                $saleproduct->catalogos()->detach();
                foreach ( $request->get('data')['relationships']['catalogos'] as $catalogo ) {
                    $saleproduct->catalogos()->attach($catalogo['id']);
                }
            }
            

            $saleproduct_saved->save();

            DB::commit();
            return new SaleproductResource($saleproduct_saved);
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Saleproduct  $saleproduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(Saleproduct $saleproduct)
    {
        //
    }

    public function get_saleproducts_select(Request $request)
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

        $saleproducts = DB::table('saleproducts')
                            ->where($atr)
                            ->select(
                                'saleproducts.name',
                                'saleproducts.id',
                                'saleproducts.image1',
                            )
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $saleproducts;
    }

    public function get_sale_products_venta(Request $request)
    {

        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText );
        $atr_saleproduct = [];
        array_push($atr_saleproduct, ['saleproducts.is_enable', 1]);
        foreach ($val as $q) {
            array_push($atr_saleproduct, ['saleproducts.name', 'LIKE', '%'.strtolower($q).'%'] );
        };

        $atr_combo = [];
        array_push($atr_combo, ['combos.is_enable', 1]);
        foreach ($val as $q) {
            array_push($atr_combo, ['combos.name', 'LIKE', '%'.strtolower($q).'%'] );
        };

        $limit = 10;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $saleproducts = DB::table('saleproducts')
                            ->where($atr_saleproduct)
                            ->join('stockproducts', 'saleproducts.stockproduct_id', '=', 'stockproducts.id')
                            ->select(
                                'saleproducts.name',
                                'saleproducts.id',
                                'saleproducts.precio_min',
                                'saleproducts.precio_may',

                                'stockproducts.is_stock_unitario_variable as is_stock_unitario_variable',
                                'stockproducts.stock_aproximado_unidad as stock_aproximado_unidad',
                                    
                                'saleproducts.relacion_venta_stock as relacion_venta_stock',

                                'saleproducts.desc_min as desc_min',
                                'saleproducts.desc_may as desc_may',
                                'saleproducts.fecha_desc_desde as fecha_desc_desde',
                                'saleproducts.fecha_desc_hasta as fecha_desc_hasta',
                                
                                'saleproducts.image1',
                                'saleproducts.image2',
                                'saleproducts.image3',
                            )
                            ->addSelect(DB::raw("'saleproduct' as tipo"));

        $combos = DB::table('combos')
                            ->where($atr_combo)
                            ->select(
                                'combos.name',
                                'combos.id',
                                'combos.precio_min',
                                'combos.precio_may',
                                'combos.precio_min as is_stock_unitario_variable',
                                'combos.precio_min as stock_aproximado_unidad',
                                'combos.precio_min as relacion_venta_stock',
                                'combos.precio_min as desc_min',
                                'combos.precio_may as desc_may',
                                'combos.precio_min as fecha_desc_desde',
                                'combos.precio_min as fecha_desc_hasta',

                                'combos.image as image1',
                                'combos.image as image2',
                                'combos.image as image3',
                            )
                            ->addSelect(DB::raw("'combo' as tipo"))
                            ->unionall($saleproducts)
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $combos;

        
        $saleproducts = Saleproduct::orderBy('name', 'ASC')
            ->where($atr)->get();
            //->paginate($limit);

        //VENTA -----
        return SaleproductVentaResource::collection($saleproducts);
    }
}
