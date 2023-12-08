<?php

namespace App\Http\Requests\v1\catalogos;

use Illuminate\Foundation\Http\FormRequest;

class CreateCatalogoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'data' => 'required|array',
            'data.type' => 'required|in:catalogos',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'required|string|max:100',
            'data.attributes.color' => 'required|string|max:100',
            'data.attributes.coments' => 'sometimes|max:200', 


        ];
    }

    public function messages()
    {
        return [
            'data.attributes.name.required' => 'The name field is required.',
            'data.attributes.name.max' => 'The name field should be less than 100.', 
            'data.attributes.color.required' => 'The color field is required.',

            'data.attributes.name.coments' => 'The Comment must not be greater than 200 characters.',

        ];
    }
}
