<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddOrderRequest extends FormRequest
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
            'ids' => 'required',
            'ids.*' => 'required|integer',
            'quantity' => 'required',
            'quantity.*' => 'required|numeric',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'delivery' => 'in:0,1',
            'longitude' => [Rule::requiredIf(function()
            {
                return isset($this->delivery) && $this->delivery==1 && !isset($this->venue_id);
            })],
            'latitude' => [Rule::requiredIf(function()
            {
                return isset($this->delivery) && $this->delivery==1 && !isset($this->venue_id);
            })],
            'venue_id' => [Rule::requiredIf(function()
            {
                return isset($this->delivery) && $this->delivery==1 && !isset($this->longitude);
            })]
        ];
    }
}
