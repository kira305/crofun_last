<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Customer_MST;

class CompareUpdateTime implements Rule
{
    public $update_time;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($update_time)
    {
        $this->update_time = $update_time;

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->update_time != null) {
            if ($value == $this->update_time) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.update_conflict');
    }
}
