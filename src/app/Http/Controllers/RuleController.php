<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Rule_MST;
use App\Rule_action;
use App\Menu;
use Auth;
use Exception;
use DB;
use Crofun;
use Common;

class RuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');

    }

    public function index(Request $request)
    {
        session(['rule' => array('page' => $request->page)]);
        $usr_id           = Auth::user()->id;
        $company_id_R     = Common::checkUserCompany($usr_id);

        $rules = Rule_MST::orderBy('company_id', 'ASC')->orderBy('id', 'ASC')->whereIn('company_id', $company_id_R)->paginate(15);
        return view('rule.index', ["rules" => $rules]);
    }

    public function create(Request $request)
    {
        /*表示する画面情報を取得*/
        $menus = Menu::orderBy('id', 'asc')->whereNotIn('id',config('constant.WITHOUT_RULE'))->get();
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'rule_name'                  => 'required|max:25',
            ], [
                'rule_name.required'         => trans('validation.rule'),
                'rule_name.max'              => trans('validation.max_string_code')
            ]);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                return view('rule.create', ["menus" => $menus, 'errors' => $errors]);
            }

            if ($this->checkRuleIsExisted($request)) {
                $validator->errors()->add('unique', trans('validation.rule_exist'));
                $errors = $validator->errors();
                return view('rule.create', ["menus" => $menus, 'errors' => $errors]);
            }

            try {
                DB::beginTransaction();
                $rule             = new Rule_MST();
                $rule->rule       = $request->rule_name;
                $rule->company_id = $request->company_id;
                if ($request->admin_flag == 'on') {
                    $rule->admin_flag = 1;
                }
                if ($request->superuser_user == 'on') {
                    $rule->superuser_user = 1;
                }
                $rule->save();
                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $rule->id, config('constant.RULE_RULE'), config('constant.operation_CRATE'), config('constant.RULE_ADD'), $rule->company_id, $rule->rule, null, json_encode($rule), null);

                $rule_log = array();
                if ($request->check_data != null) {
                    $check_data = explode(',', $request->check_data);
                    foreach ($check_data as $check) {
                        $rule_action             = new Rule_action();
                        $rule_action->rule_id    = $rule->id;
                        $rule_action->action_id  = $check;
                        $rule_log[$check] = true;
                        $rule_action->save();
                    }
                }

                DB::commit();
                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $rule->id, config('constant.RULE'), config('constant.operation_CRATE'), config('constant.RULE_EDIT'), $rule->company_id, $rule->rule, null, json_encode($rule_log), null);

                return view('rule.create', ["menus" => $menus, 'message' => trans('message.create_rule')]);
            } catch (Exception $e) {
                DB::rollBack();
                echo 'Message: ' . $e->getMessage();
            }
        }
        return view('rule.create', ["menus" => $menus]);
    }

    private function getListWithoutRule(){

    }
    public function edit(Request $request)
    {
        $menus = Menu::orderBy('id', 'asc')->whereNotIn('id',config('constant.WITHOUT_RULE'))->get();
        $rule  = Rule_MST::where('id', $request->rule_id)->first();
        $old_date  = json_encode($rule);
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'rule_name'                  => 'required|max:25',
            ], [
                'rule_name.required'         => trans('validation.rule'),
                'rule_name.max'              => trans('validation.max_string_code')
            ]);

            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                return view('rule.edit', ["rule" => $rule, "menus" => $menus, 'errors' => $errors]);
            }

            if ($this->checkRuleUpdate($request)) {
                $validator->errors()->add('unique', trans('validation.rule_exist'));
                $errors = $validator->errors();
                return view('rule.edit', ["rule" => $rule, "menus" => $menus, 'errors' => $errors]);
            }

            try {
                DB::beginTransaction();
                $check_data = explode(',', $request->check_data);
                $rule->rule = $request->rule_name;
                if ($request->admin_flag == 'on') {
                    $rule->admin_flag = 1;
                } else {
                    $rule->admin_flag = 0;
                }
                if ($request->superuser_user == 'on') {
                    $rule->superuser_user = 1;
                } else {
                    $rule->superuser_user = 0;
                }
                $rule->update();
                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $rule->id, config('constant.RULE_RULE'), config('constant.operation_UPDATE'), config('constant.RULE_EDIT'), $rule->company_id, $rule->rule, null, json_encode($rule), $old_date);
                $old_date = Rule_action::where('rule_id', $request->rule_id)->get();

                foreach ($old_date as $check) {
                    if ($check->action_id == 0) {
                        $old_Rule_log[$check->action_id] = false;
                    } else {
                        $old_Rule_log[$check->action_id] = true;
                    }
                }

                Rule_action::where('rule_id',$request->rule_id)->delete();
                if ($request->check_data != null) {
                    foreach ($check_data as $check) {
                        $rule_action             = new Rule_action();
                        $rule_action->rule_id    = $rule->id;
                        $rule_action->action_id  = $check;
                        $rule_action->save();
                        $Rule_log[$check] = true;
                    }
                }
                DB::commit();
                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $rule->id, config('constant.RULE'), config('constant.operation_UPDATE'), config('constant.RULE_EDIT'), $rule->company_id, $rule->rule, null, json_encode($Rule_log), json_encode($old_Rule_log));

                return view('rule.edit', ["menus" => $menus, 'rule' => $rule, 'message' => trans('message.edit_rule')]);
            } catch (Exception $e) {
                DB::rollBack();
            }
        }

        return view('rule.edit', ["menus" => $menus, 'rule' => $rule]);
    }

    public  function checkRuleIsExisted(Request $request)
    {
        $rule = Rule_MST::where('rule', $request->rule_name)->where('company_id', $request->company_id)->first();
        if ($rule) {
            return true;
        } else {
            return false;
        }
    }

    public function checkRuleUpdate(Request $request)
    {
        $rule = Rule_MST::where('rule', $request->rule_name)->where('company_id', $request->company_id)->first();
        if ($rule) {
            if ($rule->id != $request->rule_id) {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getMaxId()
    {
        $id  = DB::select('select MAX(id) from rule_lure_mst');
        return $id;
    }
    //選択されている会社のposionのみ入力可
    public function getListRuleAjax(Request $request)
    {
        if ($request->isMethod('post')) {
            $company_id = $request->company_id;
            try {
                $rules = Rule_MST::where('company_id', $company_id)->get();
                return response()->json(['rule' => $rules]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }
}
