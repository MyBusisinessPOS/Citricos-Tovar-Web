<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'name' => 'required',
            'adresse' => 'required',
            'phone' => 'required',
            'email' => "required|unique:clients,email,{$this->client},id",
            'country' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'adresse' => 'required',
        ];
    }
}
