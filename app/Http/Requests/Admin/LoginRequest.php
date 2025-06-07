<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    /**
     * Determines if the user is allowed to make this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for requests
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    /**
     * Custom attributes for validation
     */
    public function attributes(): array
    {
        return [
            'email' => __('admin.auth.email'),
            'password' => __('admin.auth.password'),
        ];
    }
}
