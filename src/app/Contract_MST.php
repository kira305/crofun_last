<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Service\ContractService;
use Carbon\Carbon;

class Contract_MST extends Model
{
    public $timestamps = true;
    protected $table = 'contract';

    //check_updates_deadline(更新の確認期限)　と　contract_end_date(契約終了日)　差を取得
    public function getCheckUpdatesDeadline()
    {
        if(empty($this->attributes['check_updates_deadline'])) return null;
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['check_updates_deadline']);
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['contract_end_date']);
        $diffInMonths = $to->diffInMonths($from);
        return $diffInMonths;
    }

    public function getCheckUpdatesDeadlinePre3Month()
    {
        if(empty($this->attributes['check_updates_deadline'])) return null;
        return Carbon::parse($this->attributes['check_updates_deadline'])->addMonths(3)->format('Y/m/d');
    }

    public function getNextUpdatesDate()
    {
        if(empty($this->attributes['contract_span']) || (empty($this->attributes['contract_end_date']))) return '';
        $nextDay = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['contract_end_date'])->addMonths($this->attributes['contract_span']);
        return $nextDay;
    }

    public function checkDeadline()
    {
        $today = date('Y-m-d').' 00:00:00';

        if((empty($this->attributes['contract_end_date']))) return false;
        if($today > $this->attributes['contract_end_date']) return true;

        return false;
    }

    public function getProgressStatusAttribute()
    {
        $contractProcess =  Contract_progress::where('id', $this->attributes['progress_status'])->first();
        return !empty($contractProcess) ? $contractProcess->status : trans('message.process_status_non');
    }

    public function getOriginValueProgressStatus()
    {
        return $this->attributes['progress_status'];
    }

    public function getProjectIdAttribute()
    {
        return ContractService::pgArrayParse($this->attributes['project_id']);
    }

    public function getReferenceableDepartmentAttribute()
    {
        return ContractService::pgArrayParse($this->attributes['referenceable_department']);
    }

    public function getStampReceiptDateAttribute()
    {
        return !empty($this->attributes['stamp_receipt_date']) ? Carbon::parse($this->attributes['stamp_receipt_date'])->format('Y/m/d') : null;
    }

    public function getStampedReturnDateAttribute()
    {
        return !empty($this->attributes['stamped_return_date']) ? Carbon::parse($this->attributes['stamped_return_date'])->format('Y/m/d') : null;
    }

    public function getCollectionDateAttribute()
    {
        return !empty($this->attributes['collection_date']) ? Carbon::parse($this->attributes['collection_date'])->format('Y/m/d') : null;
    }

    public function getContractConclusionDateAttribute()
    {
        return !empty($this->attributes['contract_conclusion_date']) ? Carbon::parse($this->attributes['contract_conclusion_date'])->format('Y/m/d') : null;
    }

    public function getContractStartDateAttribute()
    {
        return !empty($this->attributes['contract_start_date']) ? Carbon::parse($this->attributes['contract_start_date'])->format('Y/m/d') : null;
    }

    public function getContractEndDateAttribute()
    {
        return !empty($this->attributes['contract_end_date']) ? Carbon::parse($this->attributes['contract_end_date'])->format('Y/m/d') : null;
    }

    public function getContractUpDateAttribute()
    {
        return !empty($this->attributes['contract_up_date']) ? Carbon::parse($this->attributes['contract_up_date'])->format('Y/m/d') : null;
    }

    public function company()
    {
        return $this->hasOne('App\Company_MST', 'id', 'company_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Customer_details_MST', 'id', 'client_id');
    }

    public function headquarter()
    {
        return $this->hasOne('App\Headquarters_MST', 'id', 'headquarter_id');
    }

    public function department()
    {
        return $this->hasOne('App\Department_MST', 'id', 'department_id');
    }

    public function group()
    {
        return $this->hasOne('App\Group_MST', 'id', 'group_id');
    }

    public function contractProgress()
    {
        return $this->hasOne('App\Contract_progress', 'id', 'progress_status');
    }

    public function getContractTypeName()
    {
        return $this->hasOne('App\contract_type', 'id', 'contract_type')->first()->type_name;
    }

    // public function getCustomerName()
    // {
    //     return $this->hasMany('App\Customer_name_MST', 'client_id', 'id')->first()->client_name_s;
    // }

    public function customer_name()
    {
        return $this->hasMany('App\Customer_name_MST', 'client_id', 'client_id');
    }

    public function getCustomerName()
    {
        return $this->customer_name()->first()->client_name_s;
    }
}
