<?php

namespace App\Http\Requests\v1\clients;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'data.id' => 'required|string',
            'data.type' => 'required|in:clients',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'sometimes|max:30|required|string|unique:users,name,'.$this->input('data.id'),   
            'data.attributes.coments_client' => 'sometimes|max:200',  
            'data.attributes.coments_direccion_client' => 'sometimes|max:100',          
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.name.required' => 'The Name field is required.',
            'data.attributes.name.max' => 'The Name must not be greater than 30 characters.',
            'data.attributes.name.unique' => 'The name has already been taken.',   
            
            'data.attributes.name.coments_client' => 'The Comment must not be greater than 200 characters.',
            'data.attributes.name.coments_direccion_client' => 'The Comment must not be greater than 100 characters.',
        ];
    }
}
