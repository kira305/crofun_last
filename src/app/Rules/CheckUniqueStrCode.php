<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Customer_MST;
class CheckUniqueStrCode implements Rule
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
         
         $tsr_code = Customer_MST::where('tsr_code',$value)->first();
       
         if($tsr_code){
            
            if($this->id){

                if(($tsr_code->company->id == $this->company_id)&&($tsr_code->id != $this->id)){
                 
                  return false;

                }

            }else {

                if($tsr_code->company->id == $this->company_id){
              
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
        return trans('validation.tsr_code_existed');
    }
}
