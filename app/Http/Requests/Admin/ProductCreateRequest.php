<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:100|unique:products,sku',
            'tax_class_id' => 'nullable|exists:product_tax_classes,id',
            'status' => 'required|in:enabled,disabled',
            'visibility' => 'required|in:not_visible,catalog,search,catalog_search',
            'type_id' => 'required|in:simple,configurable,grouped,virtual,bundle,downloadable',
            
            // Category IDs as array
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            
            // Product attributes (dynamic)
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable',
        ];
    }
} 