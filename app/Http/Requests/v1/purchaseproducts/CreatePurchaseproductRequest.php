<?php

namespace App\Http\Requests\v1\purchaseproducts;

use Illuminate\Foundation\Http\FormRequest;

class CreatePurchaseproductRequest extends FormRequest
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
            'data.type' => 'required|in:purchaseproducts',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'required|string|max:100',
            'data.attributes.relacion_compra_stock' => 'required|numeric|min:0',
            'data.attributes.is_enable' => 'required|boolean',
            'data.relationships.stockproduct.data.id' => 'required|exists:stockproducts,id',
            'data.relationships.supplier.data.id' => 'required|exists:suppliers,id'
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.name.required' => 'The name field is required.',
            'data.attributes.name.max' => 'The name field should be less than 100.',
            'data.attributes.relacion_compra_stock.required' => 'The valor Relacion Compra field is required.',
            'data.attributes.relacion_compra_stock.min' => 'The valor field Relacion Compra must be at least 0',

            
            'data.relationships.stockproduct.data.id.exists' => 'The stockproduct id is invalid.',   
            'data.relationships.supplier.data.id.exists' => 'The supplier id is invalid.',         
        ];
    }
}
