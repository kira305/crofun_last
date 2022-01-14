<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Project_MST;
use App\Cost_MST;
use App\Headquarters_MST;
use App\Service\DiagramService;
use App\Events\HeadquarterChangeEvent;
use App\Events\HeadquarterChangeWithoutParent;
use App\Rules\CompareUpdateTime;
use Illuminate\Support\Facades\Validator;
use Auth;
use Response;
use DB;
use Common;
use Crofun;

class HeadquarterController extends Controller
{
    protected $diagram_service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DiagramService $diagram_service)
    {
        //$this->middleware('auth');
        $this->diagram_service   = $diagram_service;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->isMethod('post')) {

            $company_id        = $request->company_id;
            $status            = $request->status;
            $headquarter_name  = $request->headquarter_name;

            session(['company_id_h'      => $company_id]);
            session(['headquarter_name_h' => $headquarter_name]);
            session(['status_h'          => $status]);

            $headquarters = $this->searchHeadquarter($company_id, $headquarter_name, $status);

            return view('headquarter.index', [
                "headquarters"     => $headquarters,
                "company_id"       => $request->company_id,
                "headquarter_name" => $headquarter_name,
                'status'           => $status
            ]);
        }

        if (
            $request->session()->exists('company_id_h')        ||
            $request->session()->exists('headquarter_name_h')  ||
            $request->session()->exists('status_h')
        ) {
            $search_condition  = $this->searchHeadquarterSession($request);

            $headquarters       = $this->searchHeadquarter($search_condition[0], $search_condition[1], $search_condition[2]);

            return view('headquarter.index', [
                "headquarters"     => $headquarters,
                "company_id"       => session('company_id_h'),
                "headquarter_name" => session('headquarter_name_h'),
                'status'           => session('status_h')
            ]);
        } else {

            $usr_id           = Auth::user()->id;

            $company_id       = Common::checkUserCompany($usr_id);

            $headquarters     = Headquarters_MST::whereIn('company_id', $company_id)->orderBy('company_id', 'asc')->orderBy('status', 'desc')->orderBy('headquarters_code', 'asc')->paginate(25);

            return view('headquarter.index', ["headquarters" => $headquarters]);
        }
    }

    public function searchHeadquarter($company_id, $headquarter_name, $status)
    {

        $headquarters      = Headquarters_MST::orderBy('company_id', 'asc')->orderBy('status', 'desc')->orderBy('headquarters_code', 'asc');

        if ($company_id != "") {

            $headquarters->where('company_id', $company_id);
        }

        if ($status == 'on') {

            $headquarters->where('status', false);
        }

        if ($headquarter_name != "") {

            $query = $headquarters->where('headquarters', 'like', "%$headquarter_name%");
        }

        $headquarters     = $headquarters->paginate(25);

        return $headquarters;
    }


    public function searchHeadquarterSession($request)
    {

        $condition = array();
        if ($request->session()->exists('company_id_h')) {

            $company_id = session('company_id_h');
            array_push($condition, $company_id);
        } else {

            $company_id = "";
            array_push($condition, $company_id);
        }

        if ($request->session()->exists('headquarter_name_h')) {

            $headquarter_name  = session('headquarter_name_h');
            array_push($condition, $headquarter_name);
        } else {

            $headquarter_name  = "";
            array_push($condition, $headquarter_name);
        }


        if ($request->session()->exists('status_h')) {

            $status = session('status_h');
            array_push($condition, $status);
        } else {

            $status = "";
            array_push($condition, $status);
        }


        return  $condition;
    }

    public function edit(Request $request)
    {
        $headquarter = Headquarters_MST::where("id", $request->id)->first();
        if ($request->isMethod('post')) {
            $update_time = $headquarter->updated_at;
            $validator = Validator::make($request->all(), [
                'update_time'                => [new CompareUpdateTime($update_time)],
                'headquarters_code'                => 'required|max:4|regex:/^[0-9]*$/u',
                'headquarters'                     => 'required|max:25',
                'company_id'                       => 'required',
                'headquarter_list_code'            => 'required|max:4|regex:/^[a-zA-Z0-9]*$/u'

            ], [
                'company_id.required'              => trans('validation.company_code'),
                'headquarters_code.required'       => trans('validation.list_code'),
                'headquarters_code.max'            => trans('validation.max_int_4'),
                'headquarters_code.regex'          => trans('validation.max_int_4'),

                'headquarters.required'            => trans('validation.headquarter_name'),
                'headquarters.max'                 => trans('validation.max_string_25'),
                'headquarter_list_code.required'   => trans('validation.headquarter_code'),
                'headquarter_list_code.max'        => trans('validation.max_string_4'),
                'headquarter_list_code.regex'      => trans('validation.list_code_format'),

            ]);
            $old_date                           = json_encode($headquarter);

            $headquarter->headquarters_code     = $request->headquarters_code;
            $headquarter->headquarters          = $request->headquarters;
            $headquarter->company_id            = $request->company_id;
            $headquarter->headquarter_list_code = $request->headquarter_list_code;
            $headquarter->note                  = $request->note;

            if ($request->status == 'on') {
                $headquarter->status = false;
            } else {
                $headquarter->status = true;
            }
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                $headquarter->updated_at = $errors->has('update_time') ? $request->update_time : $update_time;
                if (!$errors->has('company_id') && !$errors->has('headquarters_code')) {
                    if ($this->checkCodeUpdate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }
                $errors = $validator->errors();
                return view('headquarter.edit', ['errors' => $errors, 'headquarter' => $headquarter]);
            }

            if ($this->checkCodeUpdate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('headquarter.edit', ["errors" => $errors, 'headquarter' => $headquarter]);
            }

            if ($request->status == 'on') {
                if ($request->headquarter_id != "") {
                    event(new HeadquarterChangeEvent($request->id, $request->headquarter_id));
                } else {
                    event(new HeadquarterChangeWithoutParent($request->id));
                }
            }

            if ($headquarter->update()) {
                Project_MST::where('headquarter_id', $headquarter->id)->where('status', true)->update(
                    [
                        'updated_at'     => $headquarter->updated_at
                    ]
                );
                Cost_MST::where('headquarter_id', $headquarter->id)->where('status', true)->update(
                    [
                        'updated_at'     => $headquarter->updated_at
                    ]
                );

                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.HEADQUATER'), config('constant.operation_UPDATE'), config('constant.HEADQUATER_ADD'), $headquarter->company_id, $headquarter->headquarters, $headquarter->headquarter_list_code, json_encode($headquarter), $old_date);
                return view('headquarter.edit', ["message" => trans('message.update_success'), 'headquarter' => $headquarter]);
            } else {
                return view('headquarter.edit', ["message" => trans('message.update_fail'), 'headquarter' => $headquarter]);
            }
        }

        return view('headquarter.edit', ['headquarter' => $headquarter]);
    }

    public function create(Request $request)
    {

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [

                'headquarters_code'                => 'required|max:4|regex:/^[0-9]*$/u',
                'headquarters'                     => 'required|max:25',
                'company_id'                       => 'required',
                'headquarter_list_code'            => 'required|max:4|regex:/^[a-zA-Z0-9]*$/u'
                // 'headquarter_list_code'            => 'required|max:4|regex:/^[a-zA-Z0-9あ-ん]*$/u'

            ], [
                'company_id.required'              => trans('validation.company_code'),
                'headquarters_code.required'       => trans('validation.list_code'),
                'headquarters_code.max'            => trans('validation.max_int_4'),
                'headquarters_code.regex'          => trans('validation.max_int_4'),

                'headquarters.required'            => trans('validation.headquarter_name'),
                'headquarters.max'                 => trans('validation.max_string_25'),
                'headquarter_list_code.required'   => trans('validation.headquarter_code'),
                'headquarter_list_code.max'        => trans('validation.max_string_4'),
                'headquarter_list_code.regex'      => trans('validation.list_code_format'),


            ]);

            session()->flashInput($request->input());
            if ($validator->fails()) {

                $errors = $validator->errors();
                if (!$errors->has('company_id') && !$errors->has('headquarters_code')) {

                    if ($this->checkCodeCreate($request)) {

                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }

                $errors = $validator->errors();

                return view('headquarter.create', ['errors' => $errors]);
            }

            if ($this->checkCodeCreate($request)) {

                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('headquarter.create', ["errors" => $errors]);
            }


            $headquarter                        = new Headquarters_MST();
            $headquarter->id                    = $this->getMaxId()[0]->max + 1;
            $headquarter->headquarters_code     = $request->headquarters_code;
            $headquarter->headquarters          = $request->headquarters;
            $headquarter->company_id            = $request->company_id;
            $headquarter->headquarter_list_code = $request->headquarter_list_code;
            $status                             = $request->status;

            if ($status == 'on') {

                $headquarter->status = false;
            } else {

                $headquarter->status = true;
            }

            if ($headquarter->save()) {

                Crofun::log_create(Auth::user()->id, $headquarter->id, config('constant.HEADQUATER'), config('constant.operation_CRATE'), config('constant.HEADQUATER_ADD'), $headquarter->company_id, $headquarter->headquarters, $headquarter->headquarter_list_code, json_encode($headquarter), null);

                return view('headquarter.create', ["message" => trans('message.save_success')]);
            } else {

                return view('headquarter.create', ["message" => trans('message.save_fail')]);
            }
        }

        return view('headquarter.create');
    }

    public function checkCodeCreate(Request $request)
    {

        $check = Headquarters_MST::where('headquarters_code', $request->headquarters_code)->where('company_id', $request->company_id)->first();

        if ($check) {

            return true;
        } else {

            return false;
        }
    }

    public function checkCodeUpdate(Request $request)
    {

        $checks = Headquarters_MST::where('headquarters_code', $request->headquarters_code)->where('company_id', $request->company_id)->get();

        $headquarter   = Headquarters_MST::where("id", $request->id)->first();

        foreach ($checks as $check) {

            if (($check->company_id == $headquarter->company_id) && ($check->id !=  $request->id)) {

                return true;
            }
        }

        return false;
    }

    public function checkIDisExist($id)
    {

        $check = Headquarters_MST::where('id', $id)->first();

        return $check;
    }

    public function getMaxId()
    {

        $id  = DB::select('select MAX(id) from headquarters_mst');

        return $id;
    }

    public function getListHeadquarterAjax(Request $request)
    {

        if ($request->isMethod('post')) {

            $company_id = $request->company_id;

            try {

                $headquarters = Headquarters_MST::where('company_id', $company_id)->where('status', true)->orderBy('headquarters_code', 'asc')->get();
                return response()->json(['headquarters' => $headquarters]);
            } catch (Exception $e) {

                throw new Exception($e);
            }
        }
    }
}
