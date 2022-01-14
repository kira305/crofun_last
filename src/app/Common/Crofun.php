<?php

namespace App\Common;

use DB;
use Auth;
use App\Log;
use App\Customer_MST;
use App\Company_MST;
use App\Contract_MST;
use App\Contract_rule;
use App\Headquarters_MST;
use App\Department_MST;
use App\Group_MST;
use App\Project_MST;
use App\Diagram;
use App\Cost_MST;
use App\Jobs\SendTeamsMessageJob;
use App\mail_mst;
use App\Service\ContractService;
use App\Service\MailService;
use Carbon\Carbon;
use App\system;
use App\TokenStore\TokenCache;
use DateTime;
use Illuminate\Support\Facades\Input;
use App\User;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Mail;
use Microsoft\Graph\Graph;

class Crofun
{
    public function toPgArray($set)
    {
        if (empty($set)) return null;
        settype($set, 'array'); // can be called with a scalar or array
        $result = array();
        foreach ($set as $t) {
            if (is_array($t)) {
                $result[] = $this->toPgArray($t);
            } else {
                $t = str_replace('"', '\\"', $t); // escape double quote
                if (!is_numeric($t)) // quote only non-numeric values
                    $t = '"' . $t . '"';
                $result[] = $t;
            }
        }
        return '{' . implode(",", $result) . '}'; // format
    }

    public function getMaxTimeInOr()
    {
        $created_at  = DB::select("select max(created_at) as created_at from organization_history where flag = true ");
        return $created_at;
    }

    public function getTimeForDiagram()
    {
        list($usec, $sec) = explode(' ', microtime());
        $dt = new DateTime(date('Y-m-d\TH:i:s', $sec) . substr($usec, 1));
        $dt  = date_sub($dt, date_interval_create_from_date_string('1 days'));
        return $dt->format('Y-m-d H:i:s.u');
    }

    public function changeFormatDateOfCredit($time)
    {
        if (empty($time)) return null;
        $time     = Carbon::parse($time)->format('Y/m/d');
        return $time;
    }

    public function changeFormatDateyymmdd($time)
    {
        $time     = Carbon::parse($time);
        $year     = $time->year;
        $month    = $time->month;
        $day      = $time->day;
        return sprintf("%04d", $year) . sprintf("%02d", $month) . sprintf("%02d", $day);
    }

    public function Url_explain()
    {
        $url = str_replace(request()->getSchemeAndHttpHost() . '/', '', (string)url()->previous());
        return $url;
    }

    public function project_index_return_button()
    {
        if ($this->Url_explain() === "credit/index") {
            return 4;
        }

        if ($this->Url_explain() != "home" && $this->Url_explain() != "project/index" && (strpos($this->Url_explain(), 'project/view') !== 0) && (strpos($this->Url_explain(), 'project/edit') !== 0)) {
            if (strpos($this->Url_explain(), 'credit/index') === false) {
                if (strpos($this->Url_explain(), 'customer/edit') !== false) {
                    return 1;
                }

                if (strpos($this->Url_explain(), 'customer/view') !== false) {
                    return 3;
                }
                return 0;
            } else {
                return 2;
            }
        }
        return 0;
    }

    public function credit_index_return_button()
    {
        if ($this->Url_explain() != "home" && $this->Url_explain() != "credit/index" && strpos($this->Url_explain(), 'customer/edit') !== false && request()->client_id != null) {
            return 1;
        }
        if (strpos($this->Url_explain(), 'project/index') !== false && request()->client_id != null) {
            return 1;
        }

        if (strpos($this->Url_explain(), 'customer/view') !== false) {
            return 2;
        }
        return 0;
    }

    public function contract_index_return_button()
    {
        if (strpos($this->Url_explain(), 'project/edit') !== false) {
            return 3;
        }

        if (strpos($this->Url_explain(), 'project/view') !== false) {
            return 4;
        }

        if ($this->Url_explain() != "home" && $this->Url_explain() != "contract/index" && request()->client_id != null && strpos($this->Url_explain(), 'customer/edit') !== false) {
            return 1;
        }

        if (strpos($this->Url_explain(), 'customer/view') !== false) {
            return 2;
        }

        return 0;
    }

    public function receivable_index_return_button()
    {
        if ($this->Url_explain() != "home" && $this->Url_explain() != "receivable/index" && request()->client_id != null && strpos($this->Url_explain(), 'customer/edit') !== false) {
            return 1;
        }

        if (strpos($this->Url_explain(), 'customer/view') !== false) {
            return 2;
        }

        return 0;
    }

    public function process_index_return_button()
    {
        if (strpos($this->Url_explain(), 'project/edit') !== false) {
            return 3;
        }

        if (strpos($this->Url_explain(), 'project/view') !== false) {
            return 4;
        }


        if ($this->Url_explain() != "home" && $this->Url_explain() != "process/index" && request()->client_id != null && strpos($this->Url_explain(), 'customer/edit') !== false) {
            return 1;
        }

        if (strpos($this->Url_explain(), 'customer/view') !== false) {
            return 2;
        }

        return 0;
    }

    public function credit_log_return_button()
    {
        if ($this->Url_explain() != "home" && $this->Url_explain() != "credit/log" && request()->client_id != null && strpos($this->Url_explain(), 'customer/edit') !== false) {
            return 1;
        }

        if (strpos($this->Url_explain(), 'customer/view') !== false) {
            return 2;
        }

        if (strpos($this->Url_explain(), 'credit/edit') !== false) {
            return 3;
        }
        return 0;
    }

    public function customer_edit_return_button()
    {
        if (strpos($this->Url_explain(), 'credit/index') !== false) {
            return 1;
        }
        return 0;
    }

    public function credit_create_return_button()
    {
        if (strpos($this->Url_explain(), 'customer/edit') !== false) {
            return 1;
        }
        if (strpos($this->Url_explain(), 'customer/view') !== false) {
            return 2;
        }

        return 0;
    }

    public function checkProjectIsEnd($customer_id)
    {
        $project = Project_MST::where('status', true)->where('client_id', $customer_id)->first();
        if ($project) {
            return 1;
        }

        return 2;
    }

    public static function stripXSS()
    {
        $sanitized = static::cleanArray(Input::get());
        Input::merge($sanitized);
    }

    public static function cleanArray($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $key = strip_tags($key);
            if (is_array($value)) {
                $result[$key] = static::cleanArray($value);
            } else {
                $result[$key] = trim(strip_tags($value)); // Remove trim() if you want to.
            }
        }
        return $result;
    }

    public function getCompanyById($company_id)
    {
        $company = Company_MST::where('id', $company_id)->first();
        return $company;
    }

    public function getClientById($id)
    {
        $customer = Customer_MST::where('id', $id)->first();
        return $customer;
    }

    // 管理している会社の仮コード最大値+1の取得　ajax
    public function customer_number_create($company_id)
    {
        $code  = DB::select('select MAX(client_code) from customer_mst where  company_id = ' . $company_id);
        $code  = $code[0]->max;
        $int   = substr($code, 1);
        $int   = $int + 1;
        $code  = 'K' . sprintf("%06d", $int);

        return  $code;
    }
    //管理している会社の顧客コード最大値+1を取得　ajax
    public function customer_number_create_main($company_id)
    {
        $id  = DB::select('select MAX(client_code_main) from customer_mst where company_id = ' . $company_id);

        $int = (int)$id[0]->max + 1;
        return sprintf("%06d", $int);
    }


    public function get_max_code_project($company_id)
    {
        $projects = Project_MST::where('company_id', $company_id)->get();
        if ($projects->count() > 0) {
            $company  = Company_MST::where('id', $company_id)->first();
            $max_code = $company->project_max_code;
            return $this->setFirstCodeProject($max_code);
        }

        return $this->setFirstCodeProject(1);
    }

    public function setFirstCodeProject($item)
    {
        switch ($item) {

            case 1 <= $item && $item <= 999:
                return 'Z' . sprintf("%03d", $item);
                break;
            case 1000 <= $item && $item <= 1999:
                return 'Y' . sprintf("%03d", $item - 1000);
                break;
            case 2000 <= $item && $item <= 2999:
                return 'X' . sprintf("%03d", $item - 2000);
                break;
            case 3000 <= $item && $item <= 3999:
                return 'U' . sprintf("%03d", $item - 3000);
                break;
            case 4000 <= $item && $item <= 4999:
                return 'T' . sprintf("%03d", $item - 4000);
                break;
            case 5000 <= $item && $item <= 5999:
                return 'R' . sprintf("%03d", $item - 5000);
                break;
            case 6000 <= $item && $item <= 6999:
                return 'Q' . sprintf("%03d", $item - 6000);
                break;
            case 7000 <= $item && $item <= 7999:
                return 'P' . sprintf("%03d", $item - 7000);
                break;
            case 8000 <= $item && $item <= 8999:
                return 'O' . sprintf("%03d", $item - 8000);
                break;
            case 9000 <= $item && $item <= 9999:
                return 'O' . sprintf("%03d", $item - 9000);
                break;
            case  $item > 9999:
                return 500;
                break;
            default:
                return 'Z001';
        }
    }

    //ログイン時のログ
    public function authenticate_Log($process)
    {
        $log           = new Log();
        $log->user_id  = Auth::user()->id;
        $log->company_id  = Auth::user()->company_id;
        $log->process  = $process;

        if ($log->save()) {
            return true;
        }
        return false;
    }
    //新規の場合
    // record_id = 対象のテーブルID、table_id　＝constantでのテーブルID、process　＝　constantでの操作区分、form_id　＝　constantでの画面ID、name= 対象の名、code＝対象のコード、newdata＝登録データのJSON、olddata＝修正前のデータ（JSON）
    public function log_create($user_id, $record_id, $table_id, $process, $form_id, $company_id, $name, $code, $newdata, $olddata)
    {
        $log             = new Log();
        $log->user_id    = $user_id;
        $log->process    = $process;
        $log->table_id   = $table_id;
        $log->record_id  = $record_id;
        $log->form_id    = $form_id;
        $log->company_id = $company_id;
        $log->name       = $name;
        $log->code       = $code;
        $log->new_data   = $newdata;
        $log->old_data   = $olddata;
        $log->save();
    }
    //UPDATEの場合
    public function log_update($object, $table_id, $form_id, $company_id, $name, $code)
    {
        $a = $object->getAttributes();
        $b = $object->getOriginal();

        foreach ($a as $x => $y) {
            if (strcmp($y, $b[$x]) != 0) {
                $log             = new Log();
                $log->user_id    = Auth::user()->id;
                $log->item       = $x;
                $log->record_id  = $object->id;
                $log->table_id   = $table_id;
                $log->old_data   = $b[$x];
                $log->new_data   = $y;
                $log->item       = $x;
                $log->process    = "UPDATE";
                $log->form_id    = $form_id;
                $log->company_id = $company_id;
                $log->name       = $name;
                $log->code       = $code;
                $log->save();
            }
        }
    }
    //親の情報を変更
    public function log_change($user_id, $process, $table_id, $item, $old_data, $new_data)
    {
        $log             = new Log();
        $log->user_id    = Auth::user()->id;
        $log->process    = $process;
        $log->table_id   = $table_id;
        $log->item       = $item;
        $log->old_data   = $old_data;
        $log->new_data   = $new_data;
        $log->save();
    }

    public function getDate($add_second)
    {
        $time     = Carbon::now();
        $time     = $time->addSeconds($add_second)->format('Y-m-d H:i:s');
        return $time;
    }

    public function checkGroup()
    {
        DB::beginTransaction();
        $groups = Group_MST::whereDate('updated_at', Carbon::today())
            ->orWhereDate('created_at', Carbon::today())
            ->get();

        foreach ($groups as $group) {
            $diagram = new Diagram();
            $projects = Project_MST::where('group_id', $group->id)->get();
            if (!$projects->isEmpty()) {
                foreach ($projects as $project) {
                    $diagram = new Diagram();
                    $diagram->company_id        = $project->company_id;
                    $diagram->company_name      = $project->company->abbreviate_name;
                    $diagram->headquarters_code = $project->headquarter->headquarter_list_code;
                    $diagram->headquarters      = $project->headquarter->headquarters;
                    $diagram->department_code   = $project->department->department_list_code;
                    $diagram->department_name   = $project->department->department_name;
                    $diagram->group_code        = $group->group_list_code;
                    $diagram->group_name        = $group->group_name;
                    $diagram->project_code      = $project->project_code;
                    $diagram->project_name      = $project->project_name;
                    $diagram->cost_code         = $project->group->cost_code;
                    $diagram->cost_name         = $project->group->cost_name;
                    $diagram->project_grp_code  = $project->get_code;
                    $diagram->project_grp_name  = $project->get_name;
                    $diagram->pj_id             = $project->id;
                    $diagram->created_at        = $group->updated_at;
                    $diagram->flag              = $group->status;
                    $diagram->id = $this->getMaxId();
                    $diagram->save();
                }
            }

            $costs = Cost_MST::where('group_id', $group->id)->get();

            if (!$costs->isEmpty()) {
                foreach ($costs as $cost) {
                    $diagram = new Diagram();
                    $diagram->company_id                    = $cost->company_id;
                    $diagram->company_name                  = $cost->company->abbreviate_name;
                    $diagram->headquarters_code             = $cost->headquarter->headquarter_list_code;
                    $diagram->headquarters                  = $cost->headquarter->headquarters;
                    $diagram->department_code               = $cost->department->department_list_code;
                    $diagram->department_name               = $cost->department->department_name;
                    $diagram->group_code                    = $group->group_list_code;
                    $diagram->group_name                    = $group->group_name;

                    if ($cost->type == 1) {
                        $diagram->cost_code         = $cost->cost_code;
                        $diagram->cost_name         = $cost->cost_name;
                    } else {
                        $diagram->sales_management_code         = $cost->cost_code;
                        $diagram->sales_management              = $cost->cost_name;
                    }

                    $diagram->tree_id                       = $cost->id;
                    $diagram->created_at                    = $cost->updated_at;
                    $diagram->flag                          = $group->status;
                    $diagram->id = $this->getMaxId();
                    $diagram->save();
                }
            }
        }

        DB::commit();
    }

    public function checkDepartment()
    {
        DB::beginTransaction();
        $departments = Department_MST::whereDate('updated_at', Carbon::today())
            ->orWhereDate('created_at', Carbon::today())
            ->get();
        foreach ($departments as $department) {
            $projects = Project_MST::where('department_id', $department->id)->get();
            if (!$projects->isEmpty()) {
                foreach ($projects as $project) {
                    $diagram = new Diagram();
                    $diagram->company_id        = $project->company_id;
                    $diagram->company_name      = $project->company->abbreviate_name;
                    $diagram->headquarters_code = $project->headquarter->headquarter_list_code;
                    $diagram->headquarters      = $project->headquarter->headquarters;
                    $diagram->department_code   = $department->department_list_code;
                    $diagram->department_name   = $department->department_name;
                    $diagram->group_code        = $project->group->group_list_code;
                    $diagram->group_name        = $project->group->group_name;
                    $diagram->project_code      = $project->project_code;
                    $diagram->project_name      = $project->project_name;
                    $diagram->cost_code         = $project->group->cost_code;
                    $diagram->cost_name         = $project->group->cost_name;
                    $diagram->project_grp_code  = $project->get_code;
                    $diagram->project_grp_name  = $project->get_name;
                    $diagram->pj_id             = $project->id;
                    $diagram->created_at        = $department->updated_at;
                    $diagram->flag              = $department->status;
                    $diagram->id = $this->getMaxId();
                    $diagram->save();
                }
            }

            $costs = Cost_MST::where('department_id', $department->id)->get();

            if (!$costs->isEmpty()) {
                foreach ($costs as $cost) {
                    $diagram = new Diagram();
                    $diagram->company_id                    = $cost->company_id;
                    $diagram->company_name                  = $cost->company->abbreviate_name;
                    $diagram->headquarters_code             = $cost->headquarter->headquarter_list_code;
                    $diagram->headquarters                  = $cost->headquarter->headquarters;
                    $diagram->department_code               = $department->department_list_code;
                    $diagram->department_name               = $department->department_name;
                    $diagram->group_code                    = $cost->group->group_list_code;
                    $diagram->group_name                    = $cost->group->group_name;
                    if ($cost->type == 1) {
                        $diagram->cost_code         = $cost->cost_code;
                        $diagram->cost_name         = $cost->cost_name;
                    } else {
                        $diagram->sales_management_code         = $cost->cost_code;
                        $diagram->sales_management              = $cost->cost_name;
                    }
                    $diagram->tree_id                       = $cost->id;
                    $diagram->created_at                    = $department->updated_at;
                    $diagram->flag                          = $department->status;
                    $diagram->id = $this->getMaxId();
                    $diagram->save();
                }
            }
        }

        DB::commit();
    }

    public function checkHeadquarter()
    {
        DB::beginTransaction();
        $headquarters = Headquarters_MST::whereDate('updated_at', Carbon::today())
            ->orWhereDate('created_at', Carbon::today())
            ->get();
        foreach ($headquarters as $headquarter) {
            $projects = Project_MST::where('headquarter_id', $headquarter->id)->get();
            if (!$projects->isEmpty()) {
                foreach ($projects as $project) {
                    $diagram = new Diagram();
                    $diagram->company_id        = $project->company_id;
                    $diagram->company_name      = $project->company->abbreviate_name;
                    $diagram->headquarters_code = $headquarter->headquarter_list_code;
                    $diagram->headquarters      = $headquarter->headquarters;
                    $diagram->department_code   = $project->department->department_list_code;
                    $diagram->department_name   = $project->department->department_name;
                    $diagram->group_code        = $project->group->group_list_code;
                    $diagram->group_name        = $project->group->group_name;
                    $diagram->project_code      = $project->project_code;
                    $diagram->project_name      = $project->project_name;
                    $diagram->cost_code         = $project->group->cost_code;
                    $diagram->cost_name         = $project->group->cost_name;
                    $diagram->project_grp_code  = $project->get_code;
                    $diagram->project_grp_name  = $project->get_name;
                    $diagram->pj_id             = $project->id;
                    $diagram->created_at        = $headquarter->updated_at;
                    $diagram->flag              = $headquarter->status;
                    $diagram->id = $this->getMaxId();
                    $diagram->save();
                }
            }

            $costs = Cost_MST::where('headquarter_id', $headquarter->id)->get();
            if (!$costs->isEmpty()) {
                foreach ($costs as $cost) {
                    $diagram = new Diagram();
                    $diagram->company_id                    = $cost->company_id;
                    $diagram->company_name                  = $cost->company->abbreviate_name;
                    $diagram->headquarters_code             = $headquarter->headquarter_list_code;
                    $diagram->headquarters                  = $headquarter->headquarters;
                    $diagram->department_code               = $cost->department->department_list_code;
                    $diagram->department_name               = $cost->department->department_name;
                    $diagram->group_code                    = $cost->group->group_list_code;
                    $diagram->group_name                    = $cost->group->group_name;
                    if ($cost->type == 1) {
                        $diagram->cost_code         = $cost->cost_code;
                        $diagram->cost_name         = $cost->cost_name;
                    } else {
                        $diagram->sales_management_code         = $cost->cost_code;
                        $diagram->sales_management              = $cost->cost_name;
                    }
                    $diagram->tree_id                       = $cost->id;
                    $diagram->created_at                    = $headquarter->updated_at;
                    $diagram->flag                          = $headquarter->status;

                    $diagram->id = $this->getMaxId();
                    $diagram->save();
                }
            }
        }
        DB::commit();
    }

    public function checkProject()
    {
        DB::beginTransaction();
        $projects = Project_MST::whereDate('updated_at', Carbon::today()->subDay(1))
            ->orWhereDate('created_at', Carbon::today()->subDay(1))
            ->get();

        foreach ($projects as $project) {
            $diagram = new Diagram();
            if ($project) {
                $diagram->company_id        = $project->company_id;
                $diagram->company_name      = $project->company->abbreviate_name;
                $diagram->headquarters_code = $project->headquarter->headquarter_list_code;
                $diagram->headquarters      = $project->headquarter->headquarters;
                $diagram->department_code   = $project->department->department_list_code;
                $diagram->department_name   = $project->department->department_name;
                $diagram->group_code        = $project->group->group_list_code;
                $diagram->group_name        = $project->group->group_name;
                $diagram->project_code      = $project->project_code;
                $diagram->project_name      = $project->project_name;
                $diagram->cost_code         = $project->group->cost_code;
                $diagram->cost_name         = $project->group->cost_name;
                $diagram->project_grp_code  = $project->get_code;
                $diagram->project_grp_name  = $project->get_code_name;
                $diagram->pj_id             = $project->id;
                $diagram->created_at        = $this->getTimeForDiagram();
                $diagram->flag              = $project->status;
            }
            $diagram->id = $this->getMaxId();
            $diagram->save();
        }
        DB::commit();
    }

    public function checkCost()
    {
        DB::beginTransaction();
        $costs = Cost_MST::whereDate('updated_at', Carbon::today()->subDay(1))
            ->orWhereDate('created_at', Carbon::today()->subDay(1))
            ->get();

        foreach ($costs as $cost) {
            $diagram = new Diagram();
            if ($cost) {
                $diagram->company_id        = $cost->company_id;
                $diagram->company_name      = $cost->company->abbreviate_name;
                $diagram->headquarters_code = $cost->headquarter->headquarter_list_code;
                $diagram->headquarters      = $cost->headquarter->headquarters;
                if ($cost->department_id != null) {
                    $diagram->department_code   = $cost->department->department_list_code;
                    $diagram->department_name   = $cost->department->department_name;
                }
                if ($cost->group_id != null) {
                    $diagram->group_code        = $cost->group->group_list_code;
                    $diagram->group_name        = $cost->group->group_name;
                }
                if ($cost->type == 1) {
                    $diagram->cost_code         = $cost->cost_code;
                    $diagram->cost_name         = $cost->cost_name;
                } else {
                    $diagram->sales_management_code         = $cost->cost_code;
                    $diagram->sales_management              = $cost->cost_name;
                }
                $diagram->tree_id           = $cost->id;
                $diagram->created_at        = $this->getTimeForDiagram();
                $diagram->flag              = $cost->status;
            }
            $diagram->id = $this->getMaxId();
            $diagram->save();
        }
        DB::commit();
    }

    public function getMaxId()
    {
        $id  = DB::select('select MAX(id) from organization_history');
        return $id[0]->max + 1;
    }


    //テーブルコメント欄取得
    public function tablecomnet_get()
    {
        $comment = DB::select("select
        information_schema.columns.column_name,
        information_schema.columns.data_type,
        (select description from pg_description where
        pg_description.objoid=pg_stat_user_tables.relid and
        pg_description.objsubid=information_schema.columns.ordinal_position
        )
        from
        pg_stat_user_tables,
        information_schema.columns
        where
        pg_stat_user_tables.relname= ?
        and pg_stat_user_tables.relname=information_schema.columns.table_name", ['company']);
    }

    //テーブルコメント欄取得
    public function field_name_josn()
    {

        $json = json_encode(['id' => 'ユーザーID', 'usr_code' => '社員コード', 'usr_name' => 'ユーザー名', 'rule' => '画面ルールID', 'pw' => 'PW', 'email_address' => 'メールアドレス', 'company_id' => '会社ID', 'headquarter_id' => '事業本部ID', 'department_id' => '部署ID', 'group_id' => 'グループID', 'retire' => '退職', 'updated_at' => '更新日', 'created_at' => '作成日', 'position_id' => '役職ID', 'login_first' => 'ログイン', 'password_chenge_date' => 'pw変更']);

        dd($json);

        //ルールアクション
        foreach ($menus as $check) {
            $old_Rule_log[$check->id] = $check->link_name;
        }
        dd($old_Rule_log);
    }

    //-- Newをパスワード生成する
    //   受取パラメータ：なし
    //   返信パラメータ：パスワード
    //  nobusada

    public static function New_password_create()
    {
        //最小桁
        $password_min = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_min')->first();
        //最大桁
        $password_max = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_max')->first();
        //使用文字
        $password_char1 = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_char1')->first();
        $password_char2 = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_char2')->first();
        $password_char3 = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_char3')->first();
        $password_char4 = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_char4')->first();

        //vars
        $passw = array();
        $arr1 = str_split($password_char1->f_setting_data, 1);
        $arr2 = str_split($password_char2->f_setting_data, 1);
        $arr3 = str_split($password_char3->f_setting_data, 1);
        $arr4 = str_split($password_char4->f_setting_data, 1);
        $passw_strings = array(
            $arr1,
            $arr2,
            $arr3,
            $arr4,
        );
        $pw_length      = rand($password_min->f_setting_data, $password_max->f_setting_data);
        //logic
        while (count($passw) < $pw_length) {
            // 4種類必ず入れる
            if (count($passw) < 4) {
                $key = key($passw_strings);
                next($passw_strings);
            } else {
                // 後はランダムに取得
                $key = array_rand($passw_strings);
            }
            $passw[] = $passw_strings[$key][array_rand($passw_strings[$key])];
        }
        // 生成したパスワードの順番をランダムに並び替え
        shuffle($passw);

        return implode($passw);
    }

    public static function Time_array_Get()
    {
        $TIME_ARRAY = array(
            "09:00",
            "10:00",
            "11:00",
            "12:00",
            "13:00",
            "14:00",
            "15:00",
            "16:00",
            "17:00",
            "18:00",
            "19:00",
            "20:00",
            "21:00",
            "22:00",
            "23:00",
            "24:00",
            "00:00",
            "01:00",
            "02:00",
            "03:00",
            "04:00",
            "05:00",
            "06:00",
            "07:00",
            "08:00",
        );
        return $TIME_ARRAY;
    }

    public function getLinkForCustomerCreate()
    {
        return system::where('f_setting_name', 'corporate_info')->first()->f_setting_data;
    }

    public function getPermissionContract($contract, $user, $getAlert = false, $getAlertTeams = false)
    {

        $permissionArray = array();
        $concurrents = $user->concurrently();

        //画面ルール
        // 契約書編集画面の参照権限
        $ruleAction = null;
        if ($user->getRuleAction(19) == true) {
            $ruleAction = 19;
        } else {
            // 契約書参照画面の参照権限
            if ($user->getRuleAction(20)) {
                $ruleAction = 20;
            }
        }
        $userMainPermission = $this->checkUnitPermissionContract($contract, $user, $user, $ruleAction);
        $permissionArray = array_merge($permissionArray, $userMainPermission);
        if ($concurrents != null) {
            foreach ($concurrents as $concurrent) {
                $concurrentPermission = $this->checkUnitPermissionContract($contract, $user, $concurrent, $ruleAction);
                if ($concurrentPermission != null) {
                    $permissionArray = array_merge($permissionArray, $concurrentPermission);
                }
            }
        }
        list($maxPermission, $alert, $alertTeams) = $this->checkMaxPermission($permissionArray);
        if (!$getAlert && !$getAlertTeams) {
            return $maxPermission;
        } elseif ($getAlert) {
            return $alert;
        } elseif ($getAlertTeams) {
            return $alertTeams;
        }
    }

    private function checkMaxPermission($permissionArray)
    {
        $maxPermission = null;
        $max = 0;
        $alert = false;
        $alertTeams = false;
        foreach ($permissionArray as $permission) {
            $count = $this->countPermission($permission);
            if ($count > $max) {
                $max = $count;
                $maxPermission = $permission;
            }
            if ($permission->send_alert == true) $alert = true;
            if ($permission->send_alert_teams == true) $alertTeams = true;
        }

        return array($maxPermission, $alert, $alertTeams);
    }

    private function countPermission($permission)
    {
        if ($permission->can_edit == true) return 3;
        if ($permission->only_pj_refer_departments_edit == true) return 2;
        if ($permission->can_view == true) return 1;
        return 0;
    }

    private function checkUnitPermissionContract($contract, $mainUser, $user, $ruleAction = null)
    {

        $data = array();
        //画面ルール
        // 契約書編集画面の参照権限
        if ($ruleAction == 19) {
            $data['rule_contract_edit'] = true;
        } else {
            // 契約書参照画面の参照権限
            if ($ruleAction == 20) {
                $data['rule_contract_view'] = true;
            }
        }
        //契約書編集画面の参照権限と契約書参照画面の参照権限がない場合
        if(!isset($data['rule_contract_edit']) && !isset($data['rule_contract_view'])){
            $fixedData = array('id' => 55);
            $ruleArray = array();
            $ruleArray[] = $this->getContractRule($fixedData);
            return $ruleArray;
        }

        if ($user->position->mail_flag == true) {
            $data['mail_flag'] = true;
        } else {
            $data['mail_flag'] = false;
        }

        // 全事業部参照 or 全社参照フラグ
        if ($user->position->company_look == true || $mainUser->getrole->admin_flag == 1) {
            $data['look_all_can_ref'] = true;
        } elseif ($user->position->headquarter_look == true) {
            $data['look_headquarters_can_ref'] = true;
        } elseif ($user->position->department_look == true) {
            $data['look_department_can_ref'] = true;
        } elseif ($user->position->group_look == true) {
            $data['look_group_can_ref'] = true;
        }
        $ruleArray = array();
        $isApplyRule = false;
        // 申請グループ一致
        if ($user->group_id == $contract->group_id) {
            $data['match_apply_group'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_apply_group']);
            $isApplyRule = true;
        } elseif ($user->department_id == $contract->department_id) {
            $data['match_apply_department'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_apply_department']);
            $isApplyRule = true;
        } elseif ($user->headquarter_id == $contract->headquarter_id) {
            $data['match_apply_headquarters'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_apply_headquarters']);
            $isApplyRule = true;
        }

        $isPjIsMatch = false;
        // PJ担当
        if ($this->checkPjIsMatch($user, $contract, 'group_id') == true) {
            $data['match_pj_group'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_pj_group']);
            $isPjIsMatch = true;
        } elseif ($this->checkPjIsMatch($user, $contract, 'department_id') == true) {
            $data['match_pj_department'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_pj_department']);
            $isPjIsMatch = true;
        } elseif ($this->checkPjIsMatch($user, $contract, 'headquarter_id') == true) {
            $data['match_pj_headquarters'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_pj_headquarters']);
            $isPjIsMatch = true;
        }

        $isMatchRefer = false;
        // 参照可能本部のみ一致
        if ($this->checkIsDepartmentIsMatch($user, $contract, 'department_id', 'id') == true) {
            $data['match_refer_department'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_refer_department']);
            $isMatchRefer = true;
        } elseif ($this->checkIsDepartmentIsMatch($user, $contract, 'headquarter_id', 'headquarters_id') == true) {
            $data['match_refer_headquarters'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
            unset($data['match_refer_headquarters']);
            $isMatchRefer = true;
            // 参照可能部署一致
        }

        if (!$isPjIsMatch && !$isApplyRule && !$isMatchRefer) {
            $data['no_match_organization'] = true;
            $result = $this->getContractRule($data);
            if ($result != null) $ruleArray[] = $result;
        }
        return !empty($ruleArray) ? $ruleArray : null;
    }

    private function getContractRule($data)
    {
        $contractRule = Contract_rule::orderBy('id', 'asc');
        foreach ($data as $key => $item) {
            $contractRule = $contractRule->where($key, $item);
        }
        $contractRule = $contractRule->first();
        if (empty($contractRule)) {
            return null;
        } else {
            return $contractRule;
        }
    }

    private function checkPjIsMatch($user, $contract, $checkType)
    {
        if (empty($contract->project_id)) return false;
        $projectIdArray = $contract->project_id;
        $projects = Project_MST::whereIn('id', $projectIdArray)->get();
        if (empty($projects)) return false;

        foreach ($projects as $project) {
            if ($user->{$checkType} == $project->{$checkType}) return true;
        }
        return false;
    }

    private function checkIsDepartmentIsMatch($user, $contract, $checkType, $checkCompareType)
    {
        if (empty($contract->referenceable_department)) return false;
        $departmentIdArray = $contract->referenceable_department;

        $departments = Department_MST::whereIn('id', $departmentIdArray)->get();
        if (empty($departments)) return false;

        foreach ($departments as $department) {
            if ($user->{$checkType} == $department->{$checkCompareType}) return true;
        }
        return false;
    }

    public function updateContractInScheduled()
    {
        $contracts = Contract_MST::where('contract_canceled', false)->where('update_finished', false)->get();
        $today = date('Y/m/d');
        $contractService = new ContractService();

        foreach ($contracts as $contract) {
            $isUpdate = false;
            if ($contract->auto_update == true && !empty($contract->contract_span) && $today > $contract->contract_end_date && !$contract->contract_canceled && !$contract->update_finished) {
                $endDate =  $contract->contract_end_date;
                $contract->contract_end_date = Carbon::parse($contract->contract_end_date)->addMonths($contract->contract_span)->format('Y-m-d');
                $contract->update_log = $contract->update_log . "\n" . "前回契約 " . $contract->contract_start_date . " ~ " . $endDate . " 自動更新日 " . $today;
                $isUpdate = true;
            }

            $temp = $contract->getOriginValueProgressStatus();
            $contract->progress_status = $contractService->getContractProcess($contract);
            if ($temp != $contract->progress_status) $isUpdate = true;
            if ($isUpdate)
                $contract->update();
        }

        $this->sendAlertContractMail();
    }

    public function sendAlertContractMail()
    {
        $todayFulltime = date('Y-m-d') . ' 00:00:00';
        $contractAlert = Contract_MST::where('check_updates_deadline', '<>', null)
            ->where('contract_end_date', '<>', null)
            ->where('update_finished', false)
            ->where('contract_canceled', false)
            ->where('status', true)
            ->orderBy('contract_end_date', 'desc')
            ->when($todayFulltime != "", function ($query) use ($todayFulltime) {
                $todayPlus3months = Carbon::parse($todayFulltime)->addMonths(3)->format('Y-m-d H:i:s');
                $todayPlus2months = Carbon::parse($todayFulltime)->addMonths(2)->format('Y-m-d H:i:s');
                $todayPlus1months = Carbon::parse($todayFulltime)->addMonths(1)->format('Y-m-d H:i:s');
                return $query->where(function ($childQuery) use ($todayFulltime, $todayPlus3months, $todayPlus2months, $todayPlus1months) {
                    $childQuery->where('check_updates_deadline', $todayPlus3months)
                        ->orWhere('check_updates_deadline', $todayPlus2months)
                        ->orWhere('check_updates_deadline', $todayPlus1months)
                        ->orWhere('check_updates_deadline', $todayFulltime);
                });
            })
            ->get();

        foreach ($contractAlert as $contract) {
            $users = User::where('retire', false)->get();
            $mail = Mail_MST::find(4);
            $data = array('mail_text' => $mail->mail_text);
            $content = $mail->mail_text;
            $subject = $mail->mail_remark;
            $mainArray = array();

            $tokenCache = new TokenCache();
            $graph = $tokenCache->getGraph();
            foreach ($users as $user) {
                $permissionAlert = Self::getPermissionContract($contract, $user, false, true);
                if ($permissionAlert && !in_array($user->email_address, $mainArray)) {
                    array_push($mainArray, $user->email_address);
                    $to_email = $user->email_address;
                    Mail::send('mails.credit', $data, function ($message) use ($to_email, $subject) {
                        $message->to($to_email)->subject($subject);
                    });

                    // $job = (new SendTeamsMessageJob($user, $graph, $mail, $content))->delay(Carbon::now()->addSecond(3));
                    // dispatch($job);
                }
            }
        }
    }
}
