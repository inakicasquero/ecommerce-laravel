<?php

namespace Webkul\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Address implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match("/^[a-zA-Z0-9 ,()-]+$/", $value)) {
            $fail('core::validation.address')->translate();
        }
    }
}
