<?php

namespace App\Http\Controllers;

use Crofun;
use App\Contract_MST;
use App\global_info;
use App\Customer_MST;
use App\Project_MST;
use Auth;
use App\Service\CustomerService;
use Carbon\Carbon;
use Psy\Util\Json;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $customer_service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CustomerService $customer_service)
    {
        $this->customer_service   = $customer_service;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provis_array = array();
        $global_info = global_info::select('global_info.*');
        $global_info->where('start_date', '<=', date('Y/m/d H:i:s'))->where('end_date', '>=', date('Y/m/d H:i:s'));
        $global_info->where('delete_flg', 0);
        $global_info = $global_info->orderBy('important_flg', 'asc')->orderBy('id', 'desc')->get();
        foreach ($global_info as $key => $value) {
            $global_info[$key]->global_info_content_change = $this->replaceUrl($value->global_info_content);
        }

        //本務の会社情報のみ表示
        $company_id  = Auth::user()->company_id;
        $customer    = Customer_MST::where('company_id', $company_id)->where('status', 4)->orderBy('id', 'desc')->get();

        //最新の与信情報
        $customers = $this->customer_service->crediteslatest(array($company_id))->get();
        $isReceivableExist = $this->customer_service->checkReceivableExistByCompanyID($company_id);

        $listClientId = array_column($customers->toArray(), 'client_id');

        //与信限度額取得
        $newnestReceivableObj = $this->customer_service->newnestReceivables($listClientId);

        $projectList = Project_MST::whereIn('client_id', $listClientId)->where('status', 'true');
        //取引想定額取得
        $transaction_money_array = $projectList->groupBy('client_id')->selectRaw('sum(transaction_money) as sum_transaction_money, client_id')->pluck('sum_transaction_money', 'client_id');
        //単発
        $transaction_shot_array = $projectList->where('project_mst.once_shot', 'true')->groupBy('client_id')->selectRaw('sum(transaction_shot) as sum_transaction_shot, client_id')->pluck('sum_transaction_shot', 'client_id');

        $over_receivable  = array();
        $transaction_date = array();
        $receivable_date  =  array();
        foreach ($customers as $customer_date) {
            // //取引想定額
            $transaction_money = isset($transaction_money_array[$customer_date->client_id]) ? $transaction_money_array[$customer_date->client_id] : 0;
            // //単発
            $transaction_shot = isset($transaction_shot_array[$customer_date->client_id]) ? $transaction_shot_array[$customer_date->client_id] : 0;
            $transaction =  $transaction_money + $transaction_shot;
            $creditExpect = $customer_date->credit_expect;

            if (($creditExpect != null)) {
                $newestReceivable = $this->customer_service->getNewestReceivable($isReceivableExist, $newnestReceivableObj, $customer_date->client_id);
                if ($newestReceivable != null) {
                    $receivable_date[$customer_date->id] = $newestReceivable;
                    //取引想定額が与信限度額より超えるか。 or 与信限度額が最新の売掛金残より超えるか。
                    if ($transaction > $creditExpect || $newestReceivable->receivable > $creditExpect) {
                        $transaction_date[$customer_date->id] = $transaction;
                        $over_receivable[$customer_date->id] = $customer_date;
                    }
                }
            }
        }
        //見積もり期限アラート機能（HOME）
        $today = date('Y-m-d').' 00:00:00';
        $todayPlus3months = Carbon::parse($today)->addMonths(3)->format('Y-m-d H:i:s');

        $contractAlert = Contract_MST::where('check_updates_deadline', '<>', null)
                                    ->where('check_updates_deadline','<=', $todayPlus3months )
                                    ->where('contract_end_date', '>=', $today)
                                    ->where('update_finished', false)
                                    ->where('contract_canceled', false)
                                    ->orderBy('contract_end_date', 'desc')
                                    ->get();

        return view('home.home', compact('global_info', 'provis_array', 'global_info', 'customer', 'over_receivable', 'transaction_date', 'receivable_date', 'contractAlert'));
    }

    public function showChangePasswordForm()
    {
        return view('auth.changepassword');
    }

    public static function  replaceUrl($chn_data)
    {
        $chn_data = htmlspecialchars($chn_data, ENT_QUOTES);
        $chn_data = nl2br($chn_data);
        //文字列にURLが混じっている場合のみ下のスクリプト発動
        if (preg_match("/(http|https):\/\/[-\w\.]+(:\d+)?(\/[^\s]*)?/", $chn_data)) {
            preg_match_all("/(http|https):\/\/[-\w\.]+(:\d+)?(\/[^\s]*)?/", $chn_data, $pattarn);
            foreach ($pattarn[0] as $key => $val) {
                $replace[] = '<a href="' . $val . '" target="_blank">' . $val . '</a>';
            }
            $chn_data = str_replace($pattarn[0], $replace, $chn_data);
        }
        return $chn_data;
    }
}
