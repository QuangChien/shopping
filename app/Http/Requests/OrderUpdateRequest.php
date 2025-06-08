<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'billing_address' => 'sometimes|required|array',
            'shipping_address' => 'sometimes|required|array',
            'status' => 'sometimes|required|string',
            'payment_status' => 'sometimes|required|string',
            'shipping_status' => 'sometimes|required|string',
            'payment_method_id' => 'sometimes|required|exists:payment_methods,id',
            'shipping_method_id' => 'sometimes|required|exists:shipping_methods,id',
            'discount_amount' => 'sometimes|nullable|numeric|min:0',
            'shipping_amount' => 'sometimes|nullable|numeric|min:0',
            'tax_amount' => 'sometimes|nullable|numeric|min:0',
        ];
    }
} 