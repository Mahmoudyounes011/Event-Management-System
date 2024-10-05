<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return
        [
            'products_ids' => 'required_without_all:names',
            'products_ids.*' => 'integer',
            'names' => 'required_without_all:products_ids|required_with_all:descriptions',
            'names.*' => 'string',
            'descriptions' => 'required_without_all:products_ids|required_with_all:names',
            'descriptions.*' => 'string',
            'prices' => 'required',
            'prices.*' => 'numeric'
        ];
    }
}
