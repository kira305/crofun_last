<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Events\GroupChangeEvent;
use App\Events\GroupChangeWithoutParent;
use App\User;
use App\Cost_MST;
use App\Group_MST;
use App\Project_MST;
use App\Department_MST;
use App\Rules\CompareUpdateTime;
use Auth;
use DB;
use Common;
use Crofun;

class GroupController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        session(['group' => array('page' => $request->page)]);
        if ($request->isMethod('post')) {
            $company_id            = $request->company_id;
            $headquarter_id        = $request->headquarter_id;
            $department_id         = $request->department_id;
            $status                = $request->status;
            $group_name            = $request->group_name;

            session(['company_id_g'     => $company_id]);
            session(['headquarter_id_g' => $headquarter_id]);
            session(['department_id_g'  => $department_id]);
            session(['group_name_g'     => $group_name]);
            session(['status_g'         => $status]);

            $groups                = $this->searchGroup($company_id, $headquarter_id, $department_id, $group_name, $status);
            return view('group.index', [
                "groups"         => $groups,
                "company_id"     => $company_id,
                "headquarter_id" => $headquarter_id,
                "department_id"  => $department_id,
                "group_name"     => $group_name,
                "status"         => $status
            ]);
        }
        if (
            $request->session()->exists('company_id_g') ||
            $request->session()->exists('headquarter_id_g') ||
            $request->session()->exists('department_id_g')  ||
            $request->session()->exists('group_name_g')     ||
            $request->session()->exists('status_g')
        ) {
            $search_condition  = $this->searchGroupSession($request);
            $groups            = $this->searchGroup($search_condition[0], $search_condition[1], $search_condition[2], $search_condition[3], $search_condition[4]);
            return view('group.index', [
                "groups"         => $groups,
                "company_id"     => session('company_id_g'),
                "headquarter_id" => session('headquarter_id_g'),
                "department_id"  => session('department_id_g'),
                "group_name"     => session('group_name_g'),
                "status"         => session('status_g')
            ]);
        } else {
            $usr_id           = Auth::user()->id;
            $company_id_R     = Common::checkUserCompany($usr_id);
            $groups = Group_MST::join('department_mst', 'department_mst.id', '=', 'group_mst.department_id')
                ->join('headquarters_mst', 'headquarters_mst.id', '=', 'department_mst.headquarters_id')
                ->join('company_mst', 'company_mst.id', '=', 'headquarters_mst.company_id')
                ->whereIn('company_id', $company_id_R)
                ->orderBy('company_id', 'asc')
                ->orderBy('status', 'desc')
                ->orderBy('group_code', 'asc')
                ->select('group_mst.*')
                ->paginate(25);
            return view('group.index', ["groups" => $groups]);
        }
    }

    public function searchGroup($company_id, $headquarter_id, $department_id, $group_name, $status)
    {
        $usr_id           = Auth::user()->id;
        $company_id_R       = Common::checkUserCompany($usr_id);
        $groups = Group_MST::join('department_mst', 'department_mst.id', '=', 'group_mst.department_id')
            ->join('headquarters_mst', 'headquarters_mst.id', '=', 'department_mst.headquarters_id')
            ->join('company_mst', 'company_mst.id', '=', 'headquarters_mst.company_id')
            ->whereIn('company_id', $company_id_R)
            ->orderBy('company_id', 'asc')
            ->orderBy('status', 'desc')
            ->orderBy('group_code', 'asc')
            ->select('group_mst.*');
        if ($status == 'on') {
            $groups->where('group_mst.status', false);
        }
        if ($company_id != "") {
            $groups->where('headquarters_mst.company_id', $company_id);
        }

        if ($headquarter_id != "") {
            $groups->where('department_mst.headquarters_id', $headquarter_id);
        }

        if ($department_id != "") {
            $groups->where('group_mst.department_id', $department_id);
        }

        if ($group_name != "") {
            $groups->where('group_name', 'like', '%' . $group_name . '%');
        }
        $groups = $groups->orderBy('status', 'desc')->orderBy('group_code', 'asc')->paginate(25);
        return $groups;
    }

    public function searchGroupSession($request)
    {
        $condition = array();
        if ($request->session()->exists('company_id_g')) {
            $company_id = session('company_id_g');
            array_push($condition, $company_id);
        } else {
            $headquarter_id = "";
            array_push($condition, $headquarter_id);
        }
        if ($request->session()->exists('headquarter_id_g')) {
            $headquarter_id = session('headquarter_id_g');
            array_push($condition, $headquarter_id);
        } else {
            $headquarter_id = "";
            array_push($condition, $headquarter_id);
        }

        if ($request->session()->exists('department_id_g')) {
            $department_id  = session('department_id_g');
            array_push($condition, $department_id);
        } else {
            $department_id  = "";
            array_push($condition, $department_id);
        }

        if ($request->session()->exists('group_name_g')) {
            $group_name = session('group_name_g');
            array_push($condition, $group_name);
        } else {
            $group_name = "";
            array_push($condition, $group_name);
        }

        if ($request->session()->exists('status_g')) {
            $status = session('status_g');
            array_push($condition, $status);
        } else {
            $status = "";
            array_push($condition, $status);
        }
        return  $condition;
    }


    public function edit(Request $request)
    {
        $group = Group_MST::where("id", $request->id)->first();
        if ($request->isMethod('post')) {
            $update_time = $group->updated_at;
            $validator = $this->validateData($request, $update_time);
            $old_date                           = json_encode($group);
            $group->group_code                  = $request->group_code;
            $group->group_name                  = $request->group_name;
            $group->department_id               = $request->department_id;
            $headquarter_id                     = $request->headquarter_id;
            $group->group_list_code             = $request->group_list_code;
            $group->cost_code                   = $request->cost_code;
            $group->cost_name                   = $request->cost_name;
            $group->note                        = $request->note;

            if ($validator->fails()) {
                if ($request->status == 'on') {
                    $group->status = false;
                } else {
                    $group->status = true;
                }

                $errors = $validator->errors();
                $group->updated_at = $errors->has('update_time') ? $request->update_time : $update_time;
                /**グループコード　部署コードを編集した**/
                if (!$errors->has('department_id') && !$errors->has('group_code')) {
                    /**重複のチェック**/
                    if ($this->checkCodeRule($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }

                $errors = $validator->errors();
                return view('group.edit', [
                    'group' => $group,
                    'status' => $group->status,
                    'errors'         => $errors,
                    'headquarter_id' => $headquarter_id,
                ]);
            }

            if ($this->checkCodeRule($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('group.edit', ['group' => $group, 'status' => $group->status, 'errors' => $errors]);
            }

            if ($request->status == 'on') {
                $group->status = false;
                if ($request->group_id != "") {
                    event(new GroupChangeEvent($request->id, $request->group_id));
                } else {
                    event(new GroupChangeWithoutParent($request->id));
                }
            } else {
                $group->status = true;
            }


            if ($this->checkCodeRule($request)) {
                return view('group.edit', ['group' => $group, 'status' => $group->status, 'unique' => trans('validation.code_unique')]);
            }

            if ($group->update()) {
                Project_MST::where('group_id', $group->id)->where('status', true)->update(
                    [
                        'updated_at'     => $group->updated_at,
                        'department_id'  => $group->department_id,
                        'headquarter_id' => $headquarter_id
                    ]
                );
                Cost_MST::where('group_id', $group->id)->where('status', true)->update(
                    [
                        'updated_at'     => $group->updated_at,
                        'department_id'  => $group->department_id,
                        'headquarter_id' => $headquarter_id
                    ]
                );
                User::where('group_id', $group->id)->where('retire', false)->update(
                    [
                        'updated_at'     => $group->updated_at,
                        'department_id'  => $group->department_id,
                        'headquarter_id' => $headquarter_id
                    ]
                );
                //ログ追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.GROUP'), config('constant.operation_UPDATE'), config('constant.GROUP_EDIT'), $group->headquarter()->company_id, $group->group_name, $group->group_list_code, json_encode($group), $old_date);
                return view('group.edit', ["message" => trans('message.group_change_success'), 'group' => $group, 'status' => $group->status]);
            } else {
                return view('group.edit', ["message" => trans('message.group_change_fail'), 'group' => $group, 'status' => $group->status]);
            }
        }

        return view('group.edit', ['group' => $group, 'status' => $group->status]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateData($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('department_id') && !$errors->has('group_code')) {
                    if ($this->checkCodeWhenCreate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }
                $errors = $validator->errors();
                return view('group.create', ["errors" => $errors]);
            }

            if ($this->checkCodeWhenCreate($request)) {
                return view('group.create', ['unique' => trans('validation.code_unique')]);
            }
            $group                              = new Group_MST();
            $group->id                          = $this->getMaxId()[0]->max + 1;
            $group->group_code                  = $request->group_code;
            $group->group_name                  = $request->group_name;
            $group->department_id               = $request->department_id;
            $group->group_list_code             = $request->group_list_code;
            $group->cost_code                   = $request->cost_code;
            $group->cost_name                   = $request->cost_name;
            if ($request->status == 'on') {
                $group->status = false;
            } else {
                $group->status = true;
            }
            if ($group->save()) {
                //ログ追加
                Crofun::log_create(Auth::user()->id, $group->id, config('constant.GROUP'), config('constant.operation_CRATE'), config('constant.GROUP_ADD'), $group->headquarter()->company_id, $group->group_name, $group->group_list_code, json_encode($group), null);
                return view('group.create', ["message" => trans('message.group_create_success')]);
            } else {
                return view('group.create', ["message" => trans('message.group_create_fail')]);
            }
        }
        return view('group.create');
    }
    /**重複チェックの関数**/
    public function checkCodeRule(Request $request)
    {
        $check_code_group = Group_MST::where("group_code", $request->group_code)->get();
        $group            = Group_MST::where("id", $request->id)->first();
        foreach ($check_code_group as $check) {
            if (($check->headquarter()->company_id == $group->headquarter()->company_id) && ($check->id !=  $request->id)) {
                return true;
            }
        }
        return false;
    }


    public function checkCodeWhenCreate(Request $request)
    {
        $department       = Department_MST::where('id', $request->department_id)->first();
        $check_code_group = Group_MST::where("group_code", $request->group_code)->get();
        foreach ($check_code_group as $check) {
            if (($check->headquarter()->company_id == $department->headquarter()->company_id) && ($check->id !=  $request->id)) {
                return true;
            }
        }
        return false;
    }
    public function getMaxId()
    {
        $id  = DB::select('select MAX(id) from group_mst');
        return $id;
    }

    public function validateData(Request $request, $update_time = null)
    {
        /*エラーのチェック_「group_list_code.required」は、名称変更になり影響CLASSを変更した際、影響範囲が多きため、エラーメッセージを変更*/
        $validator = Validator::make($request->all(), [
            'update_time'                => [new CompareUpdateTime($update_time)],
            'headquarter_id'            => 'required',
            'department_id'             => 'required',
            'group_name'                => 'required|max:25',
            'group_code'                => 'required|max:4|regex:/^[0-9]*$/u', //数字だけ
            'group_list_code'           => 'required|max:4|regex:/^[a-zA-Z0-9]*$/u', //大文字　小文字　数字
            'cost_code'                 => 'nullable|max:4|regex:/^[a-zA-Z0-9]*$/u',
            'cost_name'                 => 'nullable|max:25',
        ], [

            'headquarter_id.required'   => trans('validation.headquarter_chose'),
            'department_id.required'    => trans('validation.department_chose'),
            'group_name.required'       => trans('validation.group_name'),
            'group_name.max'            => trans('validation.max_string_25'),
            'group_code.max'            => trans('validation.max_int_4'),
            'group_code.regex'          => trans('validation.code_format'),
            'group_code.required'       => trans('validation.list_code'),
            'group_code.regex'          => trans('validation.max_int_4'),
            'cost_code.max'             => trans('validation.max_string_4'),
            'cost_name.max'             => trans('validation.max_string_25'),
            'group_list_code.required'  => trans('validation.group_code'),
            'group_list_code.max'       => trans('validation.max_string_4'),
            'group_list_code.regex'     => trans('validation.list_code_format'),
            'cost_code.regex'           => trans('validation.list_code_format'),
        ]);

        return $validator;
    }
    public function getListGroupAjax(Request $request)
    {
        if ($request->isMethod('post')) {
            $department_id = $request->department_id;
            try {
                $groups = Group_MST::where('department_id', $department_id)->where('status', true)->orderBy('group_code', 'asc')->get();
                return response()->json(['groups' => $groups]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }
}
