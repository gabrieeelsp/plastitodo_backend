<?php

namespace App\Http\Requests\v1\stockproducts;

use Illuminate\Foundation\Http\FormRequest;

class CreateStockproductRequest extends FormRequest
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
            'data.type' => 'required|in:stockproducts',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'required|string|max:100',
            'data.attributes.costo' => 'required|numeric|min:0',
            'data.attributes.is_stock_unitario_variable' => 'required|boolean',
            'data.attributes.stock_aproximado_unidad' => 'sometimes|numeric|min:0',
            'data.relationships.ivaaliquot.data.id' => 'required|exists:ivaaliquots,id'
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.name.required' => 'The name field is required.',
            'data.attributes.name.max' => 'The name field should be less than 100.',
            'data.attributes.costo.required' => 'The valor field is required.',
            'data.attributes.costo.min' => 'The valor field must be at least 0',
            'data.attributes.stock_aproximado_unidad.required' => 'The stock_aproximado_unidad field is required.',
            'data.attributes.stock_aproximado_unidad.min' => 'The stock_aproximado_unidad field must be at least 0',
            
            'data.relationships.ivaaliquot.data.id.exists' => 'The ivaaliquot id is invalid.',          
        ];
    }
}
