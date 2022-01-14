<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Concerns;

use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use App\Events\GroupChangeEvent;
use App\NOC_Library\Noc_function001;
use App\mail_mst;
use Mail;
use Auth;
use Response;
use Excel;
use Helper;
use DB;
use Common;
use Crofun;

class mail_mstController extends Controller
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
        $mail_msts = mail_mst::orderBy('id', 'asc');
        $mail_msts = $mail_msts->paginate(5);

        return view(
            'mail_mst.indexm',
            [
                "mail_msts" => $mail_msts,
                "important_flg_info" => session('important_flg_info'),
            ]
        );
    }


    public function editm(Request $request)
    {
        if ($request->isMethod('post')) {
            $mail_mst = mail_mst::where("id", $request->id)->first();
            $mode_name = "更新";
            $ret_view = "editm";
            $old_date  = json_encode($mail_mst);

            $mail_mst->mail_ma_name   = $request->mail_ma_name;
            $mail_mst->mail_text      = $request->mail_text;
            $mail_mst->mail_remark    = $request->mail_remark;
            $mail_mst->mail_id         = 1;

            $validator = $this->validateData($request);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return view(
                    'mail_mst.' . $ret_view,
                    [
                        'mail_mst' => $mail_mst,
                        'status' => $mail_mst->status,
                        'errors' => $errors
                    ]
                );
            }
            if ($mail_mst->save()) {
                Crofun::log_create(Auth::user()->id, $mail_mst->id, config('constant.Mail'), config('constant.operation_UPDATE'), config('constant.MAIL_TEXT'), null, $mail_mst->mail_ma_name, null, json_encode($mail_mst), $old_date);
                return view('mail_mst.' . $ret_view, ["message" => $mode_name . "が完了しました。", 'mail_mst' => $mail_mst]);
            } else {
                return view('mail_mst.' . $ret_view, ["message" => trans('message.group_change_fail'), 'mail_mst' => $mail_mst]);
            }
        } else {
            $mail_mst = mail_mst::where("id", $request->id)->first();
            $ret_view = "editm";
        }
        return view('mail_mst.' . $ret_view, ['mail_mst' => $mail_mst, 'mode' => "update"]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateData($request);
            $mail_mst                = new mail_mst();
            $mail_mst->mail_ma_name  = $request->mail_ma_name;
            $mail_mst->mail_text     = $request->mail_text;
            $mail_mst->mail_remark   = $request->mail_remark;
            $mail_mst->mail_id         = 1;

            if ($validator->fails()) {
                $errors = $validator->errors();
                return view(
                    'mail_mst.createm',
                    [
                        'mail_mst' => $mail_mst,
                        'status' => $mail_mst->status,
                        'errors' => $errors
                    ]
                );
            }

            if ($mail_mst->save()) {
                Crofun::log_create(Auth::user()->id, $mail_mst->id, config('constant.Mail'), config('constant.operation_CRATE'), config('constant.MAIL_TEXT'), null, $mail_mst->mail_ma_name, null, json_encode($mail_mst), null);
                return view('mail_mst.createm', ["message" => "登録が完了しました。", 'mail_mst' => $mail_mst]);
            } else {
                return view('mail_mst.createm', ["message" => trans('message.group_change_fail'), 'mail_mst' => $mail_mst]);
            }
        }
        $mail_mst = array();
        $mail_mst  = new mail_mst();

        return view('mail_mst.createm', ['mail_mst' => $mail_mst, 'mode' => "create"]);
    }

    public function validateData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_ma_name'    => 'required',
            'mail_text'       => 'required',
            'mail_remark'     => 'required',
        ], [
            'mail_ma_name.required'    => trans('validation.cost_name'),
            'mail_text.required'       => trans('validation.cost_name'),
            'mail_remark.required'     => trans('validation.cost_name'),
        ]);
        return $validator;
    }
}
