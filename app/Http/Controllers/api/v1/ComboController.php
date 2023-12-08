<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Comboitem;
use App\Models\Saleproduct;
use Illuminate\Http\Request;

use App\Http\Resources\v1\combos\ComboResource;
use App\Http\Resources\v1\combos\ComboitemsaleproductResource;

use App\Http\Requests\v1\combos\CreateComboRequest;

use Illuminate\Support\Facades\DB;

class ComboController extends Controller
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

        if ( $request->has('is_enable') ) {
            
            array_push($atr, ['is_enable', filter_var($request->get('is_enable'), FILTER_VALIDATE_BOOL)] );
        }

        $limit = 5;
        if($request->has('limit')){
            $limit = $request->get('limit');
        }

        $items = Combo::orderBy('name', 'ASC')
            ->where($atr)
            ->paginate($limit);
        
        return ComboResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateComboRequest $request)
    {
        $data = $request->get('data');

        $combo = Combo::create($request->input('data.attributes'));

        return new ComboResource($combo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Combo  $combo
     * @return \Illuminate\Http\Response
     */
    public function show(Combo $combo)
    {
        return new ComboResource($combo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Combo  $combo
     * @return \Illuminate\Http\Response
     */

    public function update_values(Request $request, $combo_id)
    {   
        $combo = Combo::findOrFail($combo_id);

        $data = $request->get('data');

        $combo->desc_min = $data['attributes']['desc_min'];
        $combo->desc_may = $data['attributes']['desc_may'];
        
        $combo->setPrecios();

        $combo->save();

        $combo_saved = Combo::find($combo->id);

        return new ComboResource($combo_saved);
    }

    public function update_configuration(Request $request, $combo_id)
    {   
        $combo = Combo::findOrFail($combo_id);

        if ( $combo->is_editable == false ) {
            return response()->json(['message' => 'El combo seleccionado no es editable'], 422);
        }

        $data = $request->get('data');

        $combo->desc_min = $data['attributes']['desc_min'];
        $combo->desc_may = $data['attributes']['desc_may'];

        $combo->precision_min = $data['attributes']['precision_min'];
        $combo->precision_may = $data['attributes']['precision_may'];

        $ci_enviados = $data['relationships']['comboitems'];
        $ci_ids_enviados = [];

        try {
            DB::beginTransaction();

            foreach ( $combo->comboitems as $comboitem ) {
                $eliminar = true;
                foreach ( $ci_enviados as $key => $ci_enviado ) {
                    if ( $ci_enviado['id'] == $comboitem->id ) {
                        $comboitem->name = $ci_enviado['name'];
                        $comboitem->cantidad = $ci_enviado['cantidad'];

                        // actualizar saleproducts begin ---------------------------
                        //no se pueden eliminar saleproducts
                        $ci_sp_enviados = $ci_enviado['relationships']['saleproducts'];
                        $ci_sp_ids_enviados = [];
                        foreach ( $comboitem->saleproducts as $saleproduct ) {
                            foreach ( $ci_sp_enviados as $key_sp => $ci_sp_enviado ) {
                                if ( $ci_sp_enviado['id'] == $saleproduct->id ) {
                                    $saleproduct->pivot->is_enable = $ci_sp_enviado['is_enable'];
                                    unset($ci_sp_enviados[$key_sp]);
                                    $saleproduct->pivot->save();
                                }
                            }
                        }

                        foreach ( $ci_sp_enviados as $ci_sp_enviado ) {
                            $comboitem->saleproducts()->attach($ci_sp_enviado['id'], ['is_enable' => $ci_sp_enviado['is_enable']]);
                        }

                        // actualizar saleproducts end ---------------------------

                        unset($ci_enviados[$key]);
                        $eliminar = false;
                        $comboitem->save();
                    }
                }
                if ( $eliminar ) {
                    $comboitem->delete();
                }
            }

            //creo los nuevos comboitems enviados
            foreach ( $ci_enviados as $ci_enviado ) {
                $comboitem_nuevo = Comboitem::create([
                    'name' => $ci_enviado['name'], 
                    'cantidad' => $ci_enviado['cantidad'],
                ]);

                $comboitem_nuevo->combo()->associate($combo->id);

                foreach ( $ci_enviado['relationships']['saleproducts'] as $ci_sp_enviado ) {
                    $comboitem_nuevo->saleproducts()->attach($ci_sp_enviado['id'], ['is_enable' => $ci_sp_enviado['is_enable']]);
                }
                
                $comboitem_nuevo->save();
            }
            $combo->setPrecios();
            $combo->is_enable = false;
            $combo->save();

            DB::commit();
            return new ComboResource(Combo::find($combo_id));
        }catch ( \Exception $e) {
            DB::rollback();
            return $e;
        }


        return $data;

        $combo->desc_min = $data['attributes']['desc_min'];
        $combo->desc_may = $data['attributes']['desc_may'];
        
        $combo->setPrecios();

        $combo->save();

        $combo_saved = Combo::find($combo->id);

        return new ComboResource($combo_saved);
    }


    public function update(Request $request, Combo $combo)
    {
        $data = $request->get('data');
        $is_enable_enviado = filter_var($data['attributes']['is_enable'], FILTER_VALIDATE_BOOL);  

        if ( $is_enable_enviado && !$this->is_configuration_ok($combo) ) {
            return response()->json(['message', 'La configuración no es correcta para el combo seleccionado.'], 422);
        }

        $combo->update($request->input('data.attributes'));

        $combo->save();

        return new ComboResource($combo);
    }

    private function is_configuration_ok ( Combo $combo ) 
    {
        foreach ( $combo->comboitems as $comboitem ) {
            if ( !$comboitem->is_configuration_ok() ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Combo  $combo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Combo $combo)
    {
        //
    }

    public function remove_image(Request $request, $combo_id)
    {

        $combo = Combo::findOrFail($combo_id);
        
        $combo->image = null;

        $combo->save();

        return new ComboResource($combo);
    }

    public function upload_image(Request $request, $combo_id)
    {
        usleep(1000000);
        $combo = Combo::findOrFail($combo_id);
        
        $request->validate([
            'image' => 'required|image',
        ]);        
        
        $url_image = $this->upload($request->file('image'));
        $combo->image = $url_image;

        $combo->save();

        return new ComboResource($combo);
    }

    private function upload($image)
    {
        $path_info = pathinfo($image->getClientOriginalName());
        $post_path = 'images/combos';

        $rename = uniqid() . '.' . $path_info['extension'];
        $image->move(public_path() . "/$post_path", $rename);
        return "$post_path/$rename";
    }

    public function get_saleproduct ( $saleproduct_id ) {
        $saleproduct = Saleproduct::findorFail($saleproduct_id);

        return new ComboitemsaleproductResource($saleproduct);
    }
}
