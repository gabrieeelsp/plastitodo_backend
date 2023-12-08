<?php

namespace App\Http\Requests\v1\users;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordUserRequest extends FormRequest
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
            'data.attributes.password' => 'required|string|min:8|confirmed',
        ];
    }

}
