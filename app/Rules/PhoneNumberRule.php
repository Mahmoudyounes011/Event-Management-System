<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class PhoneNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $exists = DB::table('phone_numbers')->where('phone_number', $value)->exists();

        if ($exists)
        {
            $fail("The $attribute has already been taken.");
        }

        if (!preg_match('/^[0-9]{4,14}$/', $value))
        {
            $fail("The $attribute must be a valid phone number.");
        }
    }

    public function message()
    {
        return 'The :attribute must be a valid phone number.';
    }

}
