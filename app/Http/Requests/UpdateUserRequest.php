<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'email' => "required|unique:users,email,{$this->user},id",
            'password' => 'nullable|string|min:6',
            'firstname' => 'required|min:3',
            'lastname' => 'required|min:3',
            'username' => 'required|min:3',
            'phone' => 'required',
            'role_id' => 'required',
        ];
    }
}
