<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:255',
                'unique:products,code',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'stock_on_hand' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}