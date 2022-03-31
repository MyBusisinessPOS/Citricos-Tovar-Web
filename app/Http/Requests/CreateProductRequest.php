<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'code' => 'required|unique:products,code,' . $this->product,
            'name' => 'required',
            'Type_barcode' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'cost' => 'required',
            'unit_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'This code already used. Generate Now',
            'code.required' => 'This field is required',
        ];
    }
}
