<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditSectionRequest extends FormRequest
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
        return [
            'description' => 'string',
            'price' => 'numeric',
            'capacity' => 'integer',
            'edit_levels.*' => 'integer',
            'new_levels_prices.*' => 'numeric',
            'new_levels_names.*' => 'string',
            'levels_prices.*.*' => 'numeric|required_with:levels_names.*.*',
            'levels_names.*.*' => 'string|required_with:levels_prices.*.*',
            'delete_levels.*' => 'integer',
            'delete_categories.*' => 'integer',

        ];
    }
}
