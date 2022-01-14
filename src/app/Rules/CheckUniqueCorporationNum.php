<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Customer_MST;
class CheckUniqueCorporationNum implements Rule
{ 
    public $company_id;
    public $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($company_id,$id)
    {
         $this->company_id = $company_id;
         $this->id         = $id;
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
        
         $corporation_num = Customer_MST::where('corporation_num',$value)->first();
        
         if($corporation_num){
            
            if($this->id){
 
                if(($corporation_num->company->id == $this->company_id)&&($corporation_num->id != $this->id)){
                 

                  return false;

                }

            }else {
               
                if($corporation_num->company->id == $this->company_id){
              
                   return false;

                }

            }
            
             return true;

         }else {

            return true;
         }
         
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.corporation_num_existed');
    }
}
