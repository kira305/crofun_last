<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Project_MST;
class CheckProjectCode implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $company_id;
    public $id;

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
           $project = Project_MST::where('project_code',$value)->first();

            if($project){
            
            if($this->id){
 
                if(($project->company_id == $this->company_id)&&($project->id != $this->id)){
                 

                  return false;

                }

            }else {
               
                if($project->company_id == $this->company_id){
              
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
        return trans('validation.project_code_existed');;
    }
}
