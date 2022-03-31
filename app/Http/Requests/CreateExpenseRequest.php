<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'date' => 'required',
            // 'Ref' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'details' => 'required',
            'amount' => 'required',
        ];
    }
}
