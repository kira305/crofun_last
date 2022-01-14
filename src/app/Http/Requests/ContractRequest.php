<?php

namespace App\Http\Requests;

use App\Contract_MST;
use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
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
            if(isset(request()->file_delete)) return $rules;
            $rules = [
                'contract_type' => 'required',
                'headquarter_id' => 'required',
                'department_id' => 'required',
                'group_id' => 'required',
                'application_num' => 'required',
                'stamp_receipt_date' => 'required|date_format:Y/m/d',
                'contract_completed' => 'required',
                'stamped_return_date' => 'nullable|date_format:Y/m/d',
                'collection_date' => 'nullable|date_format:Y/m/d',
                'contract_conclusion_date' => 'nullable|date_format:Y/m/d',
                'contract_start_date' => 'nullable|date_format:Y/m/d',
                'contract_end_date' => 'nullable|date_format:Y/m/d',
            ];

            if(!empty(request()->collection_date)){
                $rules = array_merge($rules, ['stamped_return_date' => 'required|date_format:Y/m/d']);
            }else {
                $rules = array_merge($rules, ['stamped_return_date' => 'nullable|date_format:Y/m/d']);
            }

            if(request()->auto_update == "true"){
                $rules = array_merge($rules, ['contract_span' => 'required']);
            }

            if(request()->contract_completed == 1){
                $rules = array_merge($rules, ['note' => 'required']);
            }

            if(!empty(request()->contract_start_date) || !empty(request()->contract_end_date)){
                if(empty(request()->contract_start_date)){
                    $rules = array_merge($rules, ['contract_start_date' => 'required']);
                }
                if(empty(request()->contract_end_date)){
                    $rules = array_merge($rules, ['contract_end_date' => 'required']);
                }
            }
            // if(!empty(request()->contract_conclusion_date)){
            //     $rules = array_merge($rules, ['collection_date' => 'required']);
            // }
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
            'contract_type.required' => trans('validation.common_required'),
            'contract_completed.required' => trans('validation.common_required'),
            'headquarter_id.required' => trans('validation.common_required'),
            'department_id.required' => trans('validation.common_required'),
            'group_id.required' => trans('validation.common_required'),
            'application_num.required' => trans('validation.common_required'),
            // 'collection_date.required' => trans('validation.collection_date_required'),
            'stamp_receipt_date.required' => trans('validation.common_required'),
            'stamped_return_date.required' => trans('validation.collection_date_required'),
            'contract_start_date.required' => trans('validation.contract_action_date_required'),
            'contract_end_date.required' => trans('validation.contract_action_date_required'),
            // 'contract_conclusion_date.required' => trans('validation.contract_action_date_required'),
            'contract_span.required' => trans('validation.contract_span_required'),
            'note.required' => trans('validation.note_required'),
            'stamp_receipt_date.date_format'       => trans('validation.credit_start_time'),
            'stamped_return_date.date_format'       => trans('validation.credit_start_time'),
            'collection_date.date_format'       => trans('validation.credit_start_time'),
            'contract_conclusion_date.date_format'       => trans('validation.credit_start_time'),
            'contract_start_date.date_format'       => trans('validation.credit_start_time'),
            'contract_end_date.date_format'       => trans('validation.credit_start_time'),
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
        if (request()->method() == 'POST' && !isset(request()->file_delete)) {
            $validator->after(function ($validator) {
                if (!empty(request()->stamped_return_date)) {
                    if (!empty(request()->collection_date)){
                        if(request()->stamped_return_date > request()->collection_date)
                            $validator->errors()->add('collection_date', trans('validation.collection_date_fail'));
                    }
                    if (!$validator->errors()->has('stamp_receipt_date')){
                        if(request()->stamp_receipt_date > request()->stamped_return_date)
                            $validator->errors()->add('stamped_return_date', trans('validation.stamped_return_date_fail'));
                    }
                }

                if (!$validator->errors()->has('contract_start_date') && !$validator->errors()->has('contract_end_date')){
                    // if(request()->stamped_return_date > request()->contract_conclusion_date){
                    //     $validator->errors()->add('contract_conclusion_date', trans('validation.contract_conclusion_date_fail'));
                    // }

                    if(request()->contract_start_date > request()->contract_end_date){
                        $validator->errors()->add('contract_end_date', trans('validation.contract_end_date_fail'));
                    }

                    // if(request()->contract_conclusion_date > request()->contract_start_date){
                    //     $validator->errors()->add('contract_start_date', trans('validation.contract_start_date_fail'));
                    // }
                }
                if(isset(request()->contract_canceled) && isset(request()->update_finished)){
                    $validator->errors()->add('update_finished', trans('validation.contract_update_finished'));
                }

                if(!$validator->errors()->has('contract_end_date') && !empty(request()->check_updates_deadline) && (empty(request()->contract_end_date) || empty(request()->contract_start_date)) ){
                    $validator->errors()->add('contract_end_date', trans('validation.required_for_check_update'));
                }

                if(request()->route()->getName() != 'contract.create'){
                    $contract = Contract_MST::where('id', request()->id)->first();
                    $update_time = request()->update_time;
                    $value = $contract != null ? $contract->updated_at : null;
                    if ($update_time != null) {
                        if ($value != $update_time) {
                            request()->session()->flash('update_time_session', $update_time);
                            $validator->errors()->add('update_time', trans('validation.update_conflict'));
                            // $validator->errors()->isEmpty();
                        }
                    }
                }

            });
            if(request()->route()->getName() == 'contract.create'){
                $data = request()->all();
                unset($data['contract_file']);
                unset($data['_token']);
                request()->session()->flash('contract_create', $data);
            }
        }
    }
}
