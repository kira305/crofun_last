<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckPassword implements Rule
{
    public $rule;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($rule)
    {
        $this->rule = $rule;
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
        $checkArray = str_split($value);
        foreach($checkArray as $item){
            if(strpos($this->rule, $item) === false){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.password_format');
    }
}
