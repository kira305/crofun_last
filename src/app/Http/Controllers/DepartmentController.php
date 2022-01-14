<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Project_MST;
use App\Cost_MST;
use App\Department_MST;
use App\Headquarters_MST;
use App\Events\DepartmentChangeEvent;
use App\Events\DepartmentChangeWithoutParent;
use App\Rules\CompareUpdateTime;
use Auth;
use DB;
use Common;
use Crofun;

class DepartmentController extends Controller
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
        session(['department' => array('page' => $request->page)]);
        if ($request->isMethod('post')) {
            $company_id            = $request->company_id;
            $headquarter_id        = $request->headquarter_id;
            $status                = $request->status;
            $department_name       = $request->department_name;
            session(['company_id_d'     => $company_id]);
            session(['headquarter_id_d' => $headquarter_id]);
            session(['department_name_d' => $department_name]);
            session(['status_d'         => $status]);
            $departments = $this->searchDepartment($company_id, $headquarter_id, $department_name, $status);

            return view('department.index', ["departments" => $departments, "headquarter_id" => $headquarter_id, "department_name"  => $department_name, 'status' => $status, "company_id" => $company_id]);
        }

        if (
            $request->session()->exists('company_id_d') ||
            $request->session()->exists('headquarter_id_d') ||
            $request->session()->exists('department_name_d')  ||
            $request->session()->exists('status_d')
        ) {
            $search_condition  = $this->searchDepartmentSession($request);
            $departments       = $this->searchDepartment($search_condition[0], $search_condition[1], $search_condition[2], $search_condition[3]);

            return view('department.index', [
                "departments"      => $departments,
                "company_id"       => session('company_id_d'),
                "headquarter_id"   => session('headquarter_id_d'),
                "department_name"  => session('department_name_d'),
                "status"           => session('status_d')
            ]);
        } else {
            $usr_id       = Auth::user()->id;
            $company_id   = Common::checkUserCompany($usr_id);
            $departments  = Department_MST::join('headquarters_mst', 'headquarters_mst.id', '=', 'department_mst.headquarters_id')->join('company_mst', 'company_mst.id', '=', 'headquarters_mst.company_id')->whereIn('headquarters_mst.company_id', $company_id)->select('department_mst.*')->orderBy('company_id', 'asc')->orderBy('status', 'desc')->orderBy('department_code', 'asc')->paginate(25);
            return view('department.index', ["departments" => $departments]);
        }
    }

    public function searchDepartment($company_id, $headquarter_id, $department_name, $status)
    {

        $usr_id       = Auth::user()->id;
        $usr_company_id   = Common::checkUserCompany($usr_id);
        $departments  = Department_MST::join('headquarters_mst', 'headquarters_mst.id', '=', 'department_mst.headquarters_id')->join('company_mst', 'company_mst.id', '=', 'headquarters_mst.company_id')->whereIn('headquarters_mst.company_id', $usr_company_id)->select('department_mst.*')->orderBy('company_id', 'asc')->orderBy('status', 'desc')->orderBy('department_code', 'asc');
        if ($company_id != "") {
            $departments->where('company_id', $company_id);
        }

        if ($headquarter_id != "") {
            $departments->where('headquarters_id', $headquarter_id);
        }

        if ($status == 'on') {
            $departments->where('department_mst.status', false);
        }

        if ($department_name != "") {
            $departments->where('department_name', 'like', "%$department_name%");
        }

        $departments          = $departments->paginate(25);
        return $departments;
    }

    public function searchDepartmentSession($request)
    {
        $condition = array();
        if ($request->session()->exists('company_id_d')) {
            $company_id = session('company_id_d');
            array_push($condition, $company_id);
        } else {
            $company_id = "";
            array_push($condition, $company_id);
        }
        if ($request->session()->exists('headquarter_id_d')) {
            $headquarter_id = session('headquarter_id_d');
            array_push($condition, $headquarter_id);
        } else {
            $headquarter_id = "";
            array_push($condition, $headquarter_id);
        }

        if ($request->session()->exists('department_name_d')) {
            $department_name  = session('department_name_d');
            array_push($condition, $department_name);
        } else {
            $department_name  = "";
            array_push($condition, $department_name);
        }

        if ($request->session()->exists('status_d')) {
            $status = session('status_d');
            array_push($condition, $status);
        } else {
            $status = "";
            array_push($condition, $status);
        }

        return  $condition;
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $department    = Department_MST::where("id", $request->department_id)->first();
            $update_time = $department->updated_at;
            $validator     = $this->validateData($request, $update_time);
            $old_date      = json_encode($department);
            $department->headquarters_id       = $request->headquarter_id;
            $department->department_name       = $request->department_name;
            $department->department_code       = $request->department_code;
            $department->department_list_code  = $request->department_list_code;
            $department->note                  = $request->note;

            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                $department->updated_at = $errors->has('update_time') ? $request->update_time : $update_time;
                if (!$errors->has('headquarter_id') && !$errors->has('department_code')) {
                    if ($this->checkCodeUpdate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }
                $errors = $validator->errors();
                return view('department.edit', ['department' => $department, 'errors' => $errors]);
            }

            if ($this->checkCodeUpdate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('department.edit', ['department' => $department, "errors" => $errors]);
            }

            if ($request->status == 'on') {
                $department->status = false;
                if ($request->new_department_id != "") {
                    event(new DepartmentChangeEvent($request->department_id, $request->new_department_id));
                } else {
                    event(new DepartmentChangeWithoutParent($request->department_id));
                }
            } else {

                $department->status = true;
            }
            if ($department->update()) {
                Project_MST::where('department_id', $department->id)->where('status', true)->update(
                    [
                        'updated_at'     => $department->updated_at,
                        'headquarter_id' => $department->headquarters_id
                    ]
                );
                Cost_MST::where('department_id', $department->id)->where('status', true)->update(
                    [
                        'updated_at'     => $department->updated_at,
                        'headquarter_id' => $department->headquarters_id
                    ]
                );
                User::where('department_id', $department->id)->where('retire', false)->update(
                    [
                        'updated_at'     => $department->updated_at,
                        'headquarter_id' => $department->headquarters_id
                    ]
                );

                Crofun::log_create(Auth::user()->id, $department->id, config('constant.DEPARTMENT'), config('constant.operation_UPDATE'), config('constant.DEPARTMENT_EDIT'), $department->headquarter()->company_id, $department->department_name, $department->department_list_code, json_encode($department), $old_date);
                return view('department.edit', ['department' => $department, "message" => trans('message.update_success')]);
            } else {
                return view('department.edit', ['department' => $department, "message" => trans('message.update_fail')]);
            }

            return view('department.edit', ['department' => $department, "message" => trans('message.update_success')]);
        }

        $department = Department_MST::where("id", $request->id)->first();
        return view('department.edit', ['department' => $department]);
    }



    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateData($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('headquarter_id') && !$errors->has('department_code')) {
                    if ($this->checkCodeCreate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }

                $errors = $validator->errors();
                return view('department.create', ['errors' => $errors]);
            }

            if ($this->checkCodeCreate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('department.create', ["errors" => $errors]);
            }

            $department                         = new Department_MST();
            $department->id                     = $this->getMaxId()[0]->max + 1;
            $department->department_code        = $request->department_code;
            $department->department_name        = $request->department_name;
            $department->headquarters_id        = $request->headquarter_id;
            $department->department_list_code   = $request->department_list_code;
            if ($request->status == 'on') {
                $department->status = false;
            } else {
                $department->status = true;
            }

            if ($department->save()) {
                Crofun::log_create(Auth::user()->id, $department->id, config('constant.DEPARTMENT'), config('constant.operation_CRATE'), config('constant.DEPARTMENT_ADD'), $department->headquarter()->company_id, $department->department_name, $department->department_list_code, json_encode($department), null);
                return view('department.create', ["message" => trans('message.save_success')]);
            } else {
                return view('department.create', ["message" => trans('message.save_fail')]);
            }
        }
        return view('department.create');
    }

    public function checkCodeUpdate(Request $request)
    {
        $checks       = Department_MST::where('department_code', $request->department_code)->get();
        $headquarter  = Headquarters_MST::where('id', $request->headquarter_id)->first();
        foreach ($checks as $check) {
            if (($check->headquarter()->company_id ==  $headquarter->company_id) &&  ($check->id != $request->department_id)) {
                return true;
            }
        }
        return false;
    }

    public function checkCodeCreate(Request $request)
    {
        $checks       = Department_MST::where('department_code', $request->department_code)->get();
        $headquarter  = Headquarters_MST::where('id', $request->headquarter_id)->first();
        foreach ($checks as $check) {
            if (($check->headquarter()->company_id ==  $headquarter->company_id)) {
                return true;
            }
        }
        return false;
    }

    public function checkIDisExist($id)
    {
        $check = Department_MST::where('id', $id)->first();
        return $check;
    }

    public function validateData(Request $request, $update_time = null)
    {
        $validator = Validator::make($request->all(), [
            'update_time'                => [new CompareUpdateTime($update_time)],
            'headquarter_id'             => 'required',
            'department_name'            => 'required|max:25',
            'department_code'            => 'required|max:4|regex:/^[0-9]*$/u',
            'department_list_code'       => 'required|max:4|regex:/^[a-zA-Z0-9]*$/u'

        ], [
            'headquarter_id.required'         => trans('validation.headquarter_chose'),
            'department_code.required'        => trans('validation.list_code'),
            'department_code.max'             => trans('validation.max_int_4'),
            'department_code.regex'           => trans('validation.max_int_4'),
            'department_name.required'        => trans('validation.department_name'),
            'department_name.max'             => trans('validation.max_string_25'),
            'department_list_code.required'   => trans('validation.department_code'),
            'department_list_code.max'        => trans('validation.max_string_4'),
            'department_list_code.regex'      => trans('validation.list_code_format'),

        ]);

        return $validator;
    }
    public function getMaxId()
    {
        $id  = DB::select('select MAX(id) from department_mst');
        return $id;
    }

    public function getListDepartment(Request $request)
    {
        if ($request->isMethod('post')) {
            $headquarter_id = $request->headquarter_id;
            try {
                $departments = Department_MST::where('headquarters_id', $headquarter_id)->where('status', true)->orderBy('department_mst.department_code', 'asc')->get();
                return response()->json(['departments' => $departments]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }
}
