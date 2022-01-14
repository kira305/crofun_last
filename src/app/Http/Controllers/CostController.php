<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Cost_MST;
use App\Rules\CompareUpdateTime;
use Auth;
use Crofun;
use Common;

class CostController extends Controller
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
        session(['cost' => array('page' => $request->page)]);
        if ($request->isMethod('post')) {
            $company_id     = $request->company_id;
            $headquarter_id = $request->headquarter_id;
            $department_id  = $request->department_id;
            $group_id       = $request->group_id;
            $cost_code      = $request->cost_code;
            $cost_name      = $request->cost_name;
            $type           = $request->type;
            $status         = $request->status;

            session(['company_id_c'     => $company_id]);
            session(['headquarter_id_c' => $headquarter_id]);
            session(['department_id_c'  => $department_id]);
            session(['group_id_c'       => $group_id]);
            session(['cost_code_c'      => $cost_code]);
            session(['cost_name_c'      => $cost_name]);
            session(['type_c'           => $type]);
            session(['status_c'         => $status]);

            $costs          = $this->search($company_id, $headquarter_id, $department_id, $group_id, $type, $cost_code, $cost_name, $status);

            return view('cost.index', [
                "costs"           => $costs,
                "company_id"     => session('company_id_c'),
                "headquarter_id" => session('headquarter_id_c'),
                "department_id"  => session('department_id_c'),
                "group_id"       => session('group_id_c'),
                "cost_code"      => session('cost_code_c'),
                "cost_name"      => session('cost_name_c'),
                "type"           => session('type_c'),
                "status"         => session('status_c')
            ]);
        }

        if (
            $request->session()->exists('company_id_c')     ||
            $request->session()->exists('headquarter_id_c') ||
            $request->session()->exists('department_id_c')  ||
            $request->session()->exists('group_id_c')       ||
            $request->session()->exists('type_c')           ||
            $request->session()->exists('cost_code_c')      ||
            $request->session()->exists('cost_name_c')      ||
            $request->session()->exists('status_c')
        ) {
            $condition = $this->searchCostBySession($request);

            $costs = $this->search(
                $condition[0],
                $condition[1],
                $condition[2],
                $condition[3],
                $condition[4],
                $condition[5],
                $condition[6],
                $condition[7]
            );

            return view('cost.index', [
                "costs"           => $costs,
                "company_id"     => session('company_id_c'),
                "headquarter_id" => session('headquarter_id_c'),
                "department_id"  => session('department_id_c'),
                "group_id"       => session('group_id_c'),
                "cost_code"      => session('cost_code_c'),
                "cost_name"      => session('cost_name_c'),
                "type"           => session('type_c'),
                "status"         => session('status_c')
            ]);
        }

        $usr_id           = Auth::user()->id;

        $company_id_R       = Common::checkUserCompany($usr_id);


        $costs = Cost_MST::leftjoin('department_mst', 'department_mst.id', '=', 'cost_mst.department_id')
            ->leftjoin('headquarters_mst', 'headquarters_mst.id', '=', 'cost_mst.headquarter_id')
            ->leftjoin('company_mst', 'company_mst.id', '=', 'cost_mst.company_id')
            ->leftjoin('group_mst', 'group_mst.id', '=', 'cost_mst.group_id')
            ->whereIn('cost_mst.company_id', $company_id_R)
            ->orderBy('cost_mst.company_id', 'asc')
            ->orderBy('cost_mst.status', 'desc')
            ->orderBy('headquarters_code', 'asc')
            ->orderBy('department_code', 'asc')
            ->orderBy('group_code', 'asc')
            ->select('cost_mst.*')
            ->paginate(25);

        return view('cost.index', ['costs' => $costs]);
    }

    public function searchCostBySession($request)
    {

        $condition = array();
        if ($request->session()->exists('company_id_c')) {

            $company_id = session('company_id_c');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }

        if ($request->session()->exists('headquarter_id_c')) {

            $headquarter_id = session('headquarter_id_c');
            array_push($condition, $headquarter_id);
        } else {

            $headquarter_id = "";
            array_push($condition, $headquarter_id);
        }

        if ($request->session()->exists('department_id_c')) {

            $department_id  = session('department_id_c');
            array_push($condition, $department_id);
        } else {

            $department_id  = "";
            array_push($condition, $department_id);
        }

        if ($request->session()->exists('group_name_c')) {

            $group_name = session('group_name_c');
            array_push($condition, $group_name);
        } else {

            $group_name = "";
            array_push($condition, $group_name);
        }

        if ($request->session()->exists('type_c')) {

            $type = session('type_c');
            array_push($condition, $type);
        } else {

            $type = "";
            array_push($condition, $type);
        }

        if ($request->session()->exists('cost_code_c')) {

            $cost_code = session('cost_code_c');
            array_push($condition, $cost_code);
        } else {

            $cost_code = "";
            array_push($condition, $cost_code);
        }

        if ($request->session()->exists('cost_name_c')) {

            $cost_name = session('cost_name_c');
            array_push($condition, $cost_name);
        } else {

            $cost_name = "";
            array_push($condition, $cost_name);
        }

        if ($request->session()->exists('status_c')) {

            $status = session('status_c');
            array_push($condition, $status);
        } else {

            $status = "";
            array_push($condition, $status);
        }

        return  $condition;
    }

    public function search($company_id, $headquarter_id, $department_id, $group_id, $type, $cost_code, $cost_name, $status)
    {

        $costs = Cost_MST::leftjoin('department_mst', 'department_mst.id', '=', 'cost_mst.department_id')
            ->leftjoin('headquarters_mst', 'headquarters_mst.id', '=', 'cost_mst.headquarter_id')
            ->leftjoin('company_mst', 'company_mst.id', '=', 'cost_mst.company_id')
            ->leftjoin('group_mst', 'group_mst.id', '=', 'cost_mst.group_id')
            ->orderBy('cost_mst.company_id', 'asc')
            ->orderBy('cost_mst.status', 'desc')
            ->orderBy('headquarters_code', 'asc')
            ->orderBy('department_code', 'asc')
            ->orderBy('group_code', 'asc')
            ->select('cost_mst.*');;

        if ($company_id != "") {

            $costs = $costs->where('cost_mst.company_id', $company_id);
        }

        if ($headquarter_id != "") {

            $costs = $costs->where('cost_mst.headquarter_id', $headquarter_id);
        }

        if ($department_id != "") {

            $costs = $costs->where('cost_mst.department_id', $department_id);
        }

        if ($group_id != "") {

            $costs = $costs->where('cost_mst.group_id', $group_id);
        }

        if ($type != "") {

            $costs = $costs->where('cost_mst.type', $type);
        }


        if ($cost_code != "") {

            $costs = $costs->where('cost_mst.cost_code', $cost_code);
        }

        if ($cost_name != "") {

            $costs = $costs->where('cost_mst.cost_name', 'like', "%$cost_name%");
        }

        if ($status == 'on') {

            $costs->where('cost_mst.status', false);
        }

        $costs          = $costs->paginate(25);

        return $costs;
    }

    public  function create(Request $request)
    {

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [

                'cost_code'     => 'required|max:4|regex:/^[a-zA-Z0-9あ-ん]*$/u',
                'cost_name'     => 'required|max:20',
                'company_id'    => 'required',
                'headquarter_id' => 'required',
                // 'department_id' => 'required',
                // 'group_id'      => 'required',
                'type'          => 'required',

            ], [

                'cost_code.required'        => trans('validation.cost_code'),
                'cost_code.max'             => trans('validation.max_string_4'),
                'cost_name.required'        => trans('validation.cost_name'),
                'cost_name.max'             => trans('validation.max_string_50'),
                'company_id.required'       => trans('validation.company_chose'),
                'headquarter_id.required'   => trans('validation.headquarter_chose'),
                // 'department_id.required'    => trans('validation.department_chose'),
                // 'group_id.required'         => trans('validation.group_chose'),
                'type.required'             => trans('validation.cost_type'),
                'cost_code.regex'           => trans('validation.list_code_format'),

            ]);


            session()->flashInput($request->input());
            $errors = $validator->errors();

            if ($validator->fails()) {

                if (!$errors->has('cost_code')) {

                    if ($this->checkCodeCreate($request)) {

                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }

                return view('cost.create', ['errors' => $errors]);
            }

            if ($this->checkCodeCreate($request)) {

                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('cost.create', ['errors' => $errors]);
            }

            $cost  = new Cost_MST();

            $cost->company_id     = $request->company_id;
            $cost->headquarter_id = $request->headquarter_id;

            if ($request->department_id != null && $request->department_id != '') {

                $cost->department_id  = $request->department_id;
            } else {

                $cost->department_id  = null;
            }
            // $cost->department_id  = $request->department_id;
            if ($request->group_id != null && $request->group_id != '') {

                $cost->group_id  = $request->group_id;
            } else {

                $cost->group_id  = null;
            }

            // $cost->group_id       = $request->group_id;
            $cost->cost_code      = $request->cost_code;
            $cost->cost_name      = $request->cost_name;
            $cost->type           = $request->type;

            if ($request->status == 'on') {

                $cost->status = false;
            } else {

                $cost->status = true;
            }

            if ($cost->save()) {

                Crofun::log_create(Auth::user()->id, $cost->id, config('constant.COST'), config('constant.operation_CRATE'), config('constant.COST_ADD'), $cost->company_id, $cost->cost_name, $cost->cost_code, json_encode($cost), null);

                return view('cost.create', ['message' => trans('message.save_success')]);
            } else {

                return view('cost.create', ['message' => trans('message.save_fail')]);
            }
        }

        return view('cost.create');
    }

    public  function edit(Request $request)
    {

        if ($request->isMethod('post')) {
            $cost = Cost_MST::where('id', $request->id)->first();
            $update_time = $cost->updated_at;
            $validator = Validator::make($request->all(), [
                'update_time'                => [new CompareUpdateTime($update_time)],
                'cost_code'     => 'required|max:4|regex:/^[a-zA-Z0-9]*$/u',
                'cost_name'     => 'required|max:20',
                'company_id'    => 'required',
                'headquarter_id' => 'required',
                'type'          => 'required',
            ], [
                'cost_code.required'        => trans('validation.cost_code'),
                'cost_code.max'             => trans('validation.max_string_4'),
                'cost_name.required'        => trans('validation.cost_name'),
                'cost_name.max'             => trans('validation.max_string_50'),
                'company_id.required'       => trans('validation.company_chose'),
                'headquarter_id.required'   => trans('validation.headquarter_chose'),
                'type.required'             => trans('validation.cost_type'),
                'cost_code.regex'           => trans('validation.list_code_format'),
            ]);

            $old_date             = json_encode($cost);
            $cost->company_id     = $request->company_id;
            $cost->headquarter_id = $request->headquarter_id;
            if ($request->department_id != null && $request->department_id != '') {
                $cost->department_id  = $request->department_id;
            } else {
                $cost->department_id  = null;
            }
            if ($request->group_id != null && $request->group_id != '') {
                $cost->group_id  = $request->group_id;
            } else {
                $cost->group_id  = null;
            }
            $cost->cost_code      = $request->cost_code;
            $cost->cost_name      = $request->cost_name;
            $cost->type           = $request->type;

            if ($request->status == 'on') {
                $cost->status = false;
            } else {
                $cost->status = true;
            }

            session()->flashInput($request->input());
            $errors = $validator->errors();
            $cost->updated_at = $errors->has('update_time') ? $request->update_time : $update_time;
            if ($validator->fails()) {
                if (!$errors->has('cost_code')) {
                    if ($this->checkCodeUpdate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }
                return view('cost.edit', ['cost' => $cost, 'errors' => $errors]);
            }

            if ($this->checkCodeUpdate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('cost.edit', ['cost' => $cost, 'errors' => $errors]);
            }

            if ($cost->update()) {
                Crofun::log_create(Auth::user()->id, $cost->id, config('constant.COST'), config('constant.operation_UPDATE'), config('constant.COST_EDIT'), $cost->company_id, $cost->cost_name, $cost->cost_code, json_encode($cost), $old_date);
                return view('cost.edit', ['cost' => $cost, 'message' => trans('message.update_success')]);
            } else {
                return view('cost.edit', ['cost' => $cost, 'message' => trans('message.save_fail')]);
            }
        }

        $cost = Cost_MST::where('id', $request->id)->first();
        return view('cost.edit', ['cost' => $cost]);
    }

    public function checkCodeCreate(Request $request)
    {

        $cost = Cost_MST::where('cost_code', $request->cost_code)->where('company_id', $request->company_id)->first();

        if ($cost) {

            return true;
        }

        return false;
    }

    public function checkCodeUpdate(Request $request)
    {

        $cost = Cost_MST::where('cost_code', $request->cost_code)->where('company_id', $request->company_id)->first();

        if ($cost) {

            if ($cost->id != $request->id) {

                return true;
            }
        }

        return false;
    }
}
