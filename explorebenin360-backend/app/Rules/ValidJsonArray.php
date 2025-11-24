<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidJsonArray implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a valid JSON string.');
            return;
        }
        
        $decoded = json_decode($value, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail('The :attribute must be valid JSON.');
            return;
        }
        
        if (!is_array($decoded)) {
            $fail('The :attribute must be a JSON array.');
        }
    }
}
