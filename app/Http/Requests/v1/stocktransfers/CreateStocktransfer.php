<?php

namespace App\Http\Requests\v1\stocktransfers;

use Illuminate\Foundation\Http\FormRequest;

class CreateStocktransfer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => 'required|array',
            'data.relationships.sucursal_origen_id' => 'required|exists:sucursals,id',
            'data.relationships.sucursal_destino_id' => 'required|exists:sucursals,id'
        ];
    }

    public function messages()
    {
        return [
           
            'data.relationships.sucursal_origen_id' => 'The sucursal_origen_id id is invalid.',   
            'data.relationships.sucursal_destino_id' => 'The sucursal_destino_id id is invalid.',         
        ];
    }
    
}
