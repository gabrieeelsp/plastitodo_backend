<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Stockproduct;
use App\Models\Ivaaliquot;
use App\Models\Combo;
use App\Models\Sucursal;
use App\Models\Stocksucursal;
use Illuminate\Http\Request;

use App\Http\Resources\v1\stockproducts\StockproductResource;
use App\Http\Resources\v1\stockproducts\StockproductStockResource;
use App\Http\Resources\v1\stockproducts\StockProductOrderByStockResource;

use App\Http\Requests\v1\stockproducts\CreateStockproductRequest;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class StockproductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $orderBy = 'name';  
        $order = 'ASC'; 
        if ( $request->has('order_time_set_costo') && filter_var($request->get('order_time_set_costo'), FILTER_VALIDATE_BOOL)) {
            $orderBy = 'time_set_costo';
            $order = 'DESC';
        }

        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText);
        $atr = [];
        foreach($val as $q) {
            array_push($atr, ['name', 'LIKE', '%'.strtolower($q).'%']);
        };

        if ( $request->has('ivaaliquot_id') ) {
            array_push($atr, ['ivaaliquot_id', '=', $request->get('ivaaliquot_id')] );
        }

        $limit = 50;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        if ( $request->has('supplier_id') ) {
            $supplier_id = $request->get('supplier_id');
            $items = Stockproduct::orderBy($orderBy, $order)
                ->where($atr)
                ->whereHas('purchaseproducts',
                    function($query) use ($supplier_id) {
                        $query->where('supplier_id', $supplier_id);
                    })
                ->paginate($limit);
            return StockproductResource::collection($items);
        }


        
        $items = Stockproduct::orderBy($orderBy, $order)
            ->where($atr)
            ->paginate($limit);
        
        return StockproductResource::collection($items);
    }
    /* public function get_stock(Request $request)
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
    
        $supplier_id = 13;
        $items = Stockproduct::orderBy('name', 'ASC')
            ->where($atr)
            ->whereHas('purchaseproducts', 
                function($query) use ($supplier_id) {
                    $query->where('supplier_id', $supplier_id);  
                })
            ->paginate($limit);
        
        return StockproductStockResource::collection($items);
    } */
    public function get_stock(Request $request)
    {
        $orderBy = 'name';   
        if ( $request->has('order_stock') && filter_var($request->get('order_stock'), FILTER_VALIDATE_BOOL)) {
            $orderBy = 'stock_relativo';
        }
    
        $searchText = trim($request->get('q'));
        $val = explode(' ', $searchText);
        $atr = [];
        foreach($val as $q) {
            array_push($atr, ['stockproducts.name', 'LIKE', '%'.strtolower($q).'%']);
        };

        if ( $request->has('sucursal_id') ) {
            array_push($atr, ['sucursal_id', '=', $request->get('sucursal_id')] );
        }

        $limit = 50;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }


        


        $stockproducts = DB::table('stockproducts')
            ->select(
                'stockproducts.id as id',
                'stockproducts.name as name',   
                'stockproducts.costo as costo',
                'stockproducts.is_stock_unitario_variable as is_stock_unitario_variable',
                'stockproducts.stock_aproximado_unidad as stock_aproximado_unidad',
                'stockproducts.image as image',
                'stockproducts.familia_id as familia_id',
                )
            //->addSelect(DB::raw("0 as stock_pedido"))
            ->selectRaw('SUM(stocksucursals.stock) as stock')
            ->selectRaw('SUM(stocksucursals.stock_minimo) as stock_minimo')
            ->selectRaw('SUM(stocksucursals.stock_pedido) as stock_pedido')
            //->selectRaw('(SUM(stocksucursals.stock) / SUM(stocksucursals.stock_minimo + 0.0000001)) as stock_relativo')
            ->selectRaw('(SUM(stocksucursals.stock - stocksucursals.stock_pedido) / SUM(stocksucursals.stock_minimo + 0.0000001)) as stock_relativo')
            ->where($atr)
            ->join('stocksucursals', 'stockproducts.id', '=', 'stocksucursals.stockproduct_id')



            
            ->groupBy('stockproducts.id')
            ->orderBy($orderBy)
            
            ->paginate($limit);

                //----------------------
        

        /* $stockproducts_orderitems = DB::table('orders')
            ->selectRaw('(SUM(orderitems.cantidad * saleproducts.relacion_venta_stock)) as cantidad')    
            ->whereIn('orders.state', ['EDITANDO', 'FINALIZADO', 'CONFIRMADO', 'EN PREPARACION'])
            ->join('orderitems', 'orderitems.order_id', '=', 'orders.id')
            ->where('orderitems.is_prepared', false)
            ->join('saleproducts', 'saleproducts.id', '=', 'orderitems.saleproduct_id')
            ->join('stockproducts', 'stockproducts.id', '=', $this->id)
            //->where('saleproducts.stockproduct_id', '=', $this->id)
            ->groupBy('saleproducts.stockproduct_id')
            ->get(); */
        return StockProductOrderByStockResource::collection($stockproducts);

        $items = Stockproduct::orderBy('name', 'ASC')
            ->where($atr)
            ->paginate($limit);
        
        return StockproductStockResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStockproductRequest $request)
    {
        $data = $request->get('data');
        $ivaaliquot_id = $data['relationships']["ivaaliquot"]["data"]["id"];

        try {

            $stockproduct = Stockproduct::create($request->input('data.attributes'));

            $stockproduct->ivaaliquot()->associate($ivaaliquot_id);
            $stockproduct->save();

            $sucursals = Sucursal::all();
            foreach ( $sucursals as $sucursal ) {
                $stocksucursal = Stocksucursal::create([
                    'stock' => 0,
                    'stock_pedido' => 0,
                    'stock_minimo' => 0,
                    'stock_maximo' => 0,
                    'stockproduct_id' => $stockproduct->id,
                    'sucursal_id' => $sucursal->id,

                ]);
                $stocksucursal->save();
            }

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        



        return new StockproductResource($stockproduct);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stockproduct  $stockproduct
     * @return \Illuminate\Http\Response
     */
    public function show(Stockproduct $stockproduct)
    {
        return new StockproductResource($stockproduct);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stockproduct  $stockproduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stockproduct $stockproduct)
    {        
        $combos_to_update = [];

        try {
            DB::beginTransaction();

            if ( $stockproduct->costo != $request->get('data')['attributes']['costo'] ) {
                $stockproduct->time_set_costo = Carbon::now();
            }

            $stockproduct->update($request->input('data.attributes'));

            if ( $request->has('data.relationships.ivaaliquot')) {
                $stockproduct->ivaaliquot_id = $request->get('data')['relationships']['ivaaliquot']['id'];
            }

            if ( $request->has('data.relationships.stockproductgroup')) {
                if ( $request->get('data')['relationships']['stockproductgroup']['id'] != 0 ) {
                    $stockproduct->stockproductgroup_id = $request->get('data')['relationships']['stockproductgroup']['id'];
                }else {
                    $stockproduct->stockproductgroup_id = null;
                }
            }

	    if ( $request->has('data.relationships.familia')) {
                if ( $request->get('data')['relationships']['familia']['id'] != 0 ) {
                    $stockproduct->familia_id = $request->get('data')['relationships']['familia']['id'];
                }else {
                    $stockproduct->familia_id = null;
                }
            }


            foreach ( $stockproduct->saleproducts as $itemSaleproduct ) {
                $itemSaleproduct->set_precios($request->get('data')['attributes']['costo']);
                $itemSaleproduct->save();

                foreach ( $itemSaleproduct->comboitems as $comboitem ) {
                    if ( !in_array($comboitem->combo_id, $combos_to_update ) ) {
                        array_push($combos_to_update, $comboitem->combo_id);
                    }
                }
            }

            $stockproduct->save();

            $stockproduct_saved = Stockproduct::find($stockproduct->id);
            
            $combos = Combo::whereIn('id', $combos_to_update)->get();
            foreach ( $combos as $combo ) {
                $combo->setPrecios();
                $combo->save();
            }
            
            DB::commit();
            return new StockproductResource($stockproduct_saved);

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
    }

    public function update_values(Request $request, $stockproduct_id)
    {
        $stockproduct = Stockproduct::findOrFail($stockproduct_id);
        $combos_to_update = [];
        try {

            DB::beginTransaction();

            if ( $request->has('data.update_group')) {
                if ( $request->get('data')['update_group'] == true ) {
                    if ( $stockproduct->stockproductgroup ) {
                        $stockproducts_group = Stockproduct::where('stockproductgroup_id', $stockproduct->stockproductgroup_id)->get();

                        $data = $request->get('data');
                        foreach ( $stockproducts_group as $itemGroup ) {

                            $itemGroup->costo = $data['attributes']['costo'];
                            $itemGroup->time_set_costo = Carbon::now();

                            foreach ( $itemGroup->saleproducts as $itemSaleproduct ) {
                                $itemSaleproduct->set_precios($data['attributes']['costo']);
                                $itemSaleproduct->save();

                                foreach ( $itemSaleproduct->comboitems as $comboitem ) {
                                    if ( !in_array($comboitem->combo_id, $combos_to_update ) ) {
                                        array_push($combos_to_update, $comboitem->combo_id);
                                    }
                                }
                            }

                            $itemGroup->save();
                            
                        }
                        $combos = Combo::whereIn('id', $combos_to_update)->get();
                        foreach ( $combos as $combo ) {
                            $combo->setPrecios();
                            $combo->save();
                        }
                        DB::commit();
                        return StockproductResource::collection($stockproducts_group);

                    }
                }
            }

            $data = $request->get('data');
            $stockproduct->costo = $data['attributes']['costo'];
            $stockproduct->time_set_costo = Carbon::now();
            foreach ( $stockproduct->saleproducts as $itemSaleproduct ) {
                $itemSaleproduct->set_precios($data['attributes']['costo']);
                $itemSaleproduct->save();

                foreach ( $itemSaleproduct->comboitems as $comboitem ) {
                    if ( !in_array($comboitem->combo_id, $combos_to_update ) ) {
                        array_push($combos_to_update, $comboitem->combo_id);
                    }
                }
            }
            $stockproduct->save();

            $stockproduct_saved = Stockproduct::find($stockproduct->id);

            $combos = Combo::whereIn('id', $combos_to_update)->get();
            foreach ( $combos as $combo ) {
                $combo->setPrecios();
                $combo->save();
            }

            DB::commit();
            return new StockproductResource($stockproduct_saved);

        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stockproduct  $stockproduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stockproduct $stockproduct)
    {
        //
    }

    public function get_stockproducts_select(Request $request)
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

        $stockproducts = DB::table('stockproducts')
                            ->where($atr)
                            ->select(
                                'stockproducts.name',
                                'stockproducts.id',
                                'stockproducts.image',
                            )
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $stockproducts;
    }

    public function remove_image(Request $request, $stockproduct_id)
    {

        $stockproduct = Stockproduct::findOrFail($stockproduct_id);
        
        $stockproduct->image = null;

        $stockproduct->save();

        return new StockproductResource($stockproduct);
    }

    public function updload_image(Request $request, $stockproduct_id)
    {
        usleep(1000000);
        $stockproduct = Stockproduct::findOrFail($stockproduct_id);

        $request->validate([

            'image' => 'required|image',
        ]);
        

        $url_image = $this->upload($request->file('image'));
        $stockproduct->image = $url_image;

        $stockproduct->save();

        return new StockproductResource($stockproduct);
    }

    private function upload($image)
    {
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/stockproducts';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path", $rename);
        return "$post_path/$rename";
    }

}
