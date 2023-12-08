<?php

namespace App\Http\Requests\v1\refunds;

use Illuminate\Foundation\Http\FormRequest;

class CreateRefundRequest extends FormRequest
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
            'data.type' => 'required|in:refunds',
            'data.attributes' => 'required|array',
            'data.attributes.valor' => 'sometimes|numeric|min:0',
            'data.relationships.paymentmethod.data.id' => 'required|exists:paymentmethods,id',
            'data.relationships.sale.data.id' => 'required|exists:sales,id',
            'data.relationships.caja.data.id' => 'required|exists:cajas,id', 
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.valor.required' => 'The valor field is required.',
            'data.attributes.valor.min' => 'The valor field must be at least 0',
            
            'data.relationships.paymentmethod.data.id.exists' => 'The paymentmethod id is invalid.',
            'data.relationships.sale.data.id.exists' => 'The sale id is invalid.',             
            'data.relationships.caja.data.id.exists' => 'The caja id is invalid.',             
        ];
    }
}
