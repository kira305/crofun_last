<?php

namespace App\Http\Requests;

use App\Contract_MST;
use App\Contract_type;
use Illuminate\Foundation\Http\FormRequest;

class ContractTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if (request()->method() == 'POST') {
            $rules = [
                'type_name' => 'required',
                'display_code' => 'required',
            ];
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type_name.required' => trans('validation.contract_type_name'),
            'display_code.required' => trans('validation.contract_type_display_code'),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if (request()->method() == 'POST') {
            $validator->after(function ($validator) {
                if(request()->route()->getName() == 'contract_type.create'){
                    if (!$validator->errors()->has('display_code')) {
                        if ($this->checkUnique('display_code')) {
                            $validator->errors()->add('display_code', trans('validation.code_unique'));
                        }
                    }
                    if (!$validator->errors()->has('type_name')) {
                        if ($this->checkUnique('type_name')) {
                            $validator->errors()->add('type_name', trans('validation.type_name_unique'));
                        }
                    }
                }else{
                    if (!$validator->errors()->has('display_code')) {
                        if ($this->checkUniqueForEdit('display_code', request()->id)) {
                            $validator->errors()->add('display_code', trans('validation.code_unique'));
                        }
                    }
                    if (!$validator->errors()->has('type_name')) {
                        if ($this->checkUniqueForEdit('type_name', request()->id)) {
                            $validator->errors()->add('type_name', trans('validation.type_name_unique'));
                        }
                    }

                    if (!$validator->errors()->has('hidden') && isset(request()->hidden)) {
                        if (!$this->isCanHidden()) {
                            $validator->errors()->add('hidden', trans('validation.cannot_hidden'));
                        }
                    }
                    // dd(1);
                    $update_time = request()->update_time;
                    $contractType = Contract_type::where("id", request()->id)->first();
                    $value = $contractType != null ? $contractType->updated_at : null;
                    if ($update_time != null) {
                        if ($value != $update_time) {
                            request()->session()->flash('update_time_session', $update_time);
                            $validator->errors()->add('update_time', trans('validation.update_conflict'));
                        }
                    }
                }
            });
            // if(request()->route()->getName() == 'contract_type.create'){
                request()->session()->flash('contract_type_create', request()->all());
            // }
        }
    }

    private function checkUnique($check_item)
    {
        $check_code_group = Contract_type::where($check_item, request()->{$check_item})->where('company_id', request()->company_id)->get();
        if (sizeof($check_code_group) != 0) {
            return true;
        }
        return false;
    }

    private function checkUniqueForEdit($check_item, $id)
    {
        $check_code_group = Contract_type::where($check_item, request()->{$check_item})->where('company_id', request()->company_id)->where('id', '<>',$id)->get();
        if (sizeof($check_code_group) != 0) {
            return true;
        }
        return false;
    }

    private function isCanHidden()
    {
        $contract = Contract_MST::where('contract_type', request()->id)->get();
        if (sizeof($contract) != 0) {
            return false;
        }
        return true;
    }
}
