<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Receivable_MST;
use App\CreditObject;

class Customer_MST extends Model
{
    public    $timestamps = true;
    protected $table      = 'customer_mst';
    protected $appends    = ['type', 'status_name', 'expiration_date'];
    protected $expiration_date = "";
    public $receivable_1 = array();
    public $receivable_2 = array();

    public function getExpirationDateAttribute()
    {
        return $this->expiration_date;
    }

    public function checkReceivableExist()
    {
        //日付取得
        $date = Receivable_MST::where('company_id', $this->company_id)
            ->orderBy('target_data', 'desc')
            ->first();
        if ($date) {
            return true;
        } else {
            return false;
        }
    }
    //12ヶ月前の売掛金残データを取得
    public function getReceivableAttribute()
    {
        $receivable = array();
        $searchTime = array();

        for ($i = 0; $i <= 11; $i++) {
            array_push($searchTime,$this->searchTime($i + 1));
        }

        $receivableList = Receivable_MST::where('client_id', $this->client_id)->whereIn('target_data', $searchTime)->get();

        for ($i = 0; $i <= 11; $i++) {
            $check = false;
            foreach ($receivableList as $item){
                if(Carbon::parse($item->target_data)->format('Y-m-d') == $searchTime[$i]){
                    $receivable[$i] = $this->modifyData($item);
                    $check = true;
                    break;
                }
            }
            if(!$check){
                $receivable[$i] = $this->modifyData(null);
            }
        }

        return $receivable;
    }
    // 12~6月前の売上データを取得
    public function getReceivable2Attribute()
    {
        return $this->getReceivableAttribute();
    }

    public function getReceivable1Attribute()
    {
        return $this->getReceivableAttribute();
    }
    // 一番最新売上データを取得
    public function newnestReceivable()
    {
        // 日付取得
        $date = Receivable_MST::where('company_id', $this->company_id)
            ->orderBy('target_data', 'desc')
            ->first();
        if ($date) {
            $item = Receivable_MST::where('client_id', $this->client_id)
                ->whereBetween('target_data', [$this->searchTime(1), $this->searchTime(0)])
                ->first();
            $item = $this->modifyData($item);
            return $item;
        } else {
            return null;
        }
    }

    /*今日の日付から、月を取得して、前の月から表示*/
    public  function searchTime($count_month)
    {
        $getDateTime = Carbon::now()->subMonths($count_month);
        $time = $getDateTime->year . '-' . $getDateTime->month . '-01';
        $time = Carbon::parse($time)->format('Y-m-d');

        return $time;
    }
    // 日付の形を変更
    public function modifyTime($data)
    {
        $time = Carbon::parse($time)->format('Y-m');
        return $time;
    }
    // オプジェトの要素をセット
    public function modifyData($item)
    {
        $credit = new CreditObject();
        if ($item == null) {
            $credit->target_data = '';
            $credit->receivable  = '';
        } else {
            $credit->target_data = substr($item->target_data, 0, 7);
            $credit->receivable  = $item->receivable;
        }
        return $credit;
    }

    public function company()
    {
        return $this->hasOne('App\Company_MST', 'id', 'company_id');
    }


    public function customer_name()
    {
        return $this->hasMany('App\Customer_name_MST', 'client_id', 'id');
    }

    public function getCustomerName()
    {
        return $this->customer_name()->first()->client_name_s;
    }

    public function credit_check()
    {
        return $this->hasMany('App\Credit_check', 'client_id', 'id')->orderBy('get_time', 'desc')->orderBy('created_at', 'desc')->first();
    }

    public function credit_check_by_get_time()
    {
        return $this->hasMany('App\Credit_check', 'client_id', 'id')->orderBy('get_time', 'desc')->first();
    }

    public function getTypeAttribute()
    {
        if ($this->sale == 1) {
            return '売上先';
        }elseif ($this->sale == 2) {
            return '仕入先';
        }elseif ($this->sale == 3) {
            return '売上先/仕入先';
        }
    }

    public function getStatusNameAttribute()
    {
        if ($this->status == 1) {
            return '取引終了';
        }elseif ($this->status == 2) {
            return '本登録中止';
        }elseif ($this->status == 3) {
            return '取引中';
        }elseif ($this->status == 4) {
            return '仮登録中';
        }
    }

    public function group()
    {
        return $this->hasOne('App\Group_MST', 'id', 'request_group');
    }

    public function com_grp()
    {
        return $this->leftjoin('group_mst', 'group_mst.id', '=', 'customer_mst.request_group')
            ->leftjoin('department_mst', 'department_mst.id', '=', 'group_mst.department_id')
            ->leftjoin('headquarters_mst', 'headquarters_mst.id', '=', 'department_mst.headquarters_id')
            ->leftjoin('company_mst',  'company_mst.id', '=', 'headquarters_mst.company_id')
            ->where('customer_mst.id', $this->id)
            ->select('group_mst.*', 'headquarters', 'department_name')
            ->first();
    }

    public function project()
    {
        return $this->hasMany('App\Project_MST', 'client_id', 'id');
    }

    public function projectForContract()
    {
        return $this->hasMany('App\Project_MST', 'client_id', 'id')->orderBy('status', 'desc');
    }
}
