<?php

namespace App\Http\Requests\v1\sucursals;

use Illuminate\Foundation\Http\FormRequest;

class CreateSucursalRequest extends FormRequest
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
            'data.type' => 'required|in:sucursals',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'required|string|max:100',
            'data.relationships.empresa.data.id' => 'required|exists:empresas,id'


        ];
    }

    public function messages()
    {
        return [
            'data.attributes.name.required' => 'The name field is required.',
            'data.attributes.name.max' => 'The name field should be less than 100.', 

            'data.relationships.empresa.data.id.exists' => 'The Empresa id is invalid.',     

        ];
    }
}
