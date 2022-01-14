<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Position_MST;
use Auth;
use DB;
use Crofun;
use Common;

class PositionController extends Controller
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
        session(['position' => array('page' => $request->page)]);
        $usr_id           = Auth::user()->id;
        $company_id_R     = Common::checkUserCompany($usr_id);
        $positions        = Position_MST::orderBy('company_id', 'ASC')->orderBy('id', 'ASC')->whereIn('company_id', $company_id_R)->paginate(25);

        return view('position.index', ["positions" =>  $positions]);
    }

    public function validateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position_name'                     => 'required|max:25',
            'look'                              => 'required',

        ], [
            'position_name.required'       => trans('validation.position_name'),
            'position_name.max'            => trans('validation.max_string_25'),
            'look.required'                => trans('validation.look_rule'),

        ]);
        return $validator;
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateData($request);
            $position                   = Position_MST::where("id", $request->id)->first();
            $old_date                   = json_encode($position);
            $position_name              = $request->position_name;
            $position->position_name    = $position_name;
            $look                       = $request->look;

            if ($look == 1) {
                $company_look = true;
            } else {
                $company_look = false;
            }

            if ($look == 2) {
                $headquarter_look = true;
            } else {
                $headquarter_look = false;
            }

            if ($look == 3) {
                $department_look = true;
            } else {
                $department_look = false;
            }

            if ($look == 4) {
                $group_look = true;
            } else {
                $group_look = false;
            }

            $position->company_look     = $company_look;
            $position->headquarter_look = $headquarter_look;
            $position->department_look  = $department_look;
            $position->group_look       = $group_look;

            if ($request->mail_flag == 'on') {
                $position->mail_flag = true;
            } else {
                $position->mail_flag = false;
            }

            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('id')) {
                    if ($this->checkCodeUpdate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }

                $errors = $validator->errors();
                return view('position.edit', ['position' => $position, 'errors' => $errors]);
            }

            if ($this->checkCodeUpdate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('position.edit', ['position' => $position, "errors" => $errors]);
            }

            if ($position->update()) {
                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $position->id, config('constant.POSION'), config('constant.operation_UPDATE'), config('constant.POSITION_EDIT'), $position->company_id, $position->position_name, null, json_encode($position), $old_date);
                return view('position.edit', ['position' => $position, "message" => trans('message.edit_position_success')]);
            } else {
                return view('position.edit', ['position' => $position, "message" => trans('message.save_fail')]);
            }
        }

        $position = Position_MST::where("id", $request->id)->first();
        return view('position.edit', ['position' => $position]);
    }
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateData($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('id')) {
                    if ($this->checkCodeCreate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }

                $errors = $validator->errors();
                return view('position.create', ['errors' => $errors]);
            }

            if ($this->checkCodeCreate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('position.create', ["errors" => $errors]);
            }

            $company_id          = $request->company_id;
            $position_name       = $request->position_name;
            $look                = $request->look;

            if ($look == 1) {
                $company_look = true;
            } else {
                $company_look = false;
            }

            if ($look == 2) {
                $headquarter_look = true;
            } else {
                $headquarter_look = false;
            }

            if ($look == 3) {
                $department_look = true;
            } else {
                $department_look = false;
            }

            if ($look == 4) {
                $group_look = true;
            } else {
                $group_look = false;
            }

            if ($request->mail_flag == 'on') {
                $mail_flag = true;
            } else {
                $mail_flag = false;
            }
            $position                   = new Position_MST();
            $position->id               = $this->getMaxId()[0]->max + 1;
            $position->company_id       = $company_id;
            $position->position_name    = $position_name;
            $position->company_look     = $company_look;
            $position->headquarter_look = $headquarter_look;
            $position->department_look  = $department_look;
            $position->group_look       = $group_look;
            $position->mail_flag        = $mail_flag;

            if ($position->save()) {
                Crofun::log_create(Auth::user()->id, $position->id, config('constant.POSION'), config('constant.operation_CRATE'), config('constant.POSITION_ADD'), $position->company_id, $position->position_name, null, json_encode($position), null);
                return view('position.create', ["message" => trans('message.create_position_success')]);
            } else {
                return view('position.create', ["message" => trans('message.save_fail')]);
            }
        }
        return view('position.create');
    }

    public function checkCodeCreate(Request $request)
    {
        $position = Position_MST::where('id', $request->id)->first();
        if ($position) {
            return true;
        } else {
            return false;
        }
    }

    public function checkCodeUpdate(Request $request)
    {
        $position = Position_MST::where('id', $request->id)->first();
        if ($position) {
            if ($position->id != $request->id) {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getMaxId()
    {
        $id  = DB::select('select MAX(id) from position_mst');
        return $id;
    }
    //選択されている会社のposionのみ入力可
    public function getListPositionAjax(Request $request)
    {
        if ($request->isMethod('post')) {
            $company_id = $request->company_id;
            try {
                $positions = Position_MST::where('company_id', $company_id)->get();
                return response()->json(['positions' => $positions]);
            } catch (Exception $e) {
                throw new Exception($e);
            }
        }
    }
}
