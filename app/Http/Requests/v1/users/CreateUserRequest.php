<?php

namespace App\Http\Requests\v1\users;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'data.type' => 'required|in:users',
            'data.attributes' => 'required|array',
            'data.attributes.name' => 'required|string|max:100',
            'data.attributes.surname' => 'required|string|max:100',

            'data.attributes.email' => 'required|string|email|max:255',
            'data.attributes.password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'data.attributes.name.required' => 'The name field is required.',
            'data.attributes.name.max' => 'The name field should be less than 100.', 
            'data.attributes.surname.required' => 'The surname field is required.',
            'data.attributes.surname.max' => 'The surname field should be less than 100.', 
            'data.attributes.email.required' => 'The name field is required.',
            'data.attributes.email.email' => 'The email field is wrong.', 

        ];
    }
}
