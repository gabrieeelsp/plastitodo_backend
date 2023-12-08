<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Stockproduct;
use App\Models\Sucursal;
use App\Models\Purchaseorder;
use App\Models\Purchaseorderitem;
use Illuminate\Http\Request;

use App\Http\Resources\v1\suppliers\SupplierResource;
use App\Http\Resources\v1\purchaseorders\PurchaseorderResource;

use App\Http\Requests\v1\suppliers\CreateSupplierRequest;

use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
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
                $suppliers = Supplier::orderBy('name', 'ASC')
                    ->where($atr_name)->get();
                    
                    return SupplierResource::collection($suppliers);

            }            
        }        
        
        $suppliers = Supplier::orderBy('name', 'ASC')
            ->orWhere($atr_name)
            ->paginate($limit);       
        
        return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSupplierRequest $request)
    {
        $data = $request->get('data');

        $supplier = Supplier::create($request->input('data.attributes'));

        $supplier->save();

        return new SupplierResource($supplier);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        //return $request->input('data.attributes');
        $supplier->update($request->input('data.attributes'));

        $supplier->save();

        return new SupplierResource($supplier);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }

    public function get_suppliers_select(Request $request)
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

        $suppliers = DB::table('suppliers')
                            ->where($atr)
                            ->select(
                                'suppliers.name',
                                'suppliers.id',
                            )
                            ->orderBy('name', 'ASC')
                            ->paginate($limit);
        return $suppliers;
    }

    public function make_order( Request $request ) 
    {

        $supplier = Supplier::findOrFail($request->get('supplier_id'));

        $purchaseorder = Purchaseorder::create();
        $purchaseorder->supplier()->associate($supplier->id);
        $purchaseorder->user()->associate(auth()->user()->id);
        $purchaseorder->estado = "EDITANDO";
        //$purchaseorder->sucursal()->associate($request->get('sucursal_id'));
        $purchaseorder->save();
        $orderitems = [];
        foreach( $supplier->purchaseproducts as $purchaseproduct ) {
            if ( $purchaseproduct->is_enable ) {
                $orderitem = Purchaseorderitem::create();
                $orderitem->purchaseproduct()->associate($purchaseproduct->id);
                $orderitem->purchaseorder()->associate($purchaseorder->id);
                $orderitem->save();    
            }
            
        }

        return new PurchaseorderResource($purchaseorder);
    }
}
