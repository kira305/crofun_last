<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Customer_MST;
class CheckClientName implements Rule
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
         $client = Customer_MST::where('client_name',$value)->first();
       
         if($client){
            
            if($this->id){

                if(($client->company->id == $this->company_id)&&($client->id != $this->id)){
                 
                  return false;

                }

            }else {

                if($client->company->id == $this->company_id){
              
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
         return trans('validation.client_name_check');
    }
}
