<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Service\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\system;
use App\password_cycle;
use App\Rules\CheckPassword;
use Auth;
use Response;
use DB;

class removepassword extends Controller
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
        $validator = Validator::make($request->all(), [
            'pw1'   => 'required',
            'pw2'   => 'required',
            'pw3'   => 'required',
            'pw2'   => 'required',
            'pw3'   => 'required',
        ], [
            'pw1.required'  => trans('auth.password'),
            'pw2.required'  => trans('auth.password'),
            'pw3.required'  => trans('auth.password'),
            'pw2.min'       => trans('validation.password_min_lenght'),
            'pw2.regex'     => trans('validation.password_format'),
            'pw3.min'       => trans('validation.password_min_lenght'),
            'pw3.regex'     => trans('validation.password_format'),
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();
            return view('auth.changepassword', ['errors' => $errors]);
        } else {

            $get_user = user::where('usr_code', session('tmp_usr_code'))->first();
            if (!password_verify($request->pw1, $get_user->pw)) {
                //if (!Auth::attempt(['usr_code' => session('tmp_usr_code'), 'pw' => $request->pw1])) {
                $validator->errors()->add('pw1', 'validation.password_not_correctly');
                $t_error = true;
                $errors = $validator->errors();
                return view('auth.changepassword', ['errors' => $errors]);
            }
        }

        $minPw = system::where('f_setting_name','pass_min')->first()->f_setting_data;
        $maxPw = system::where('f_setting_name','pass_max')->first()->f_setting_data;
        $rule = system::where('f_setting_name','pass_char')->first()->f_setting_data;

        $validator = Validator::make($request->all(), [
            'pw1'   => 'required',
            'pw2'   => 'required',
            'pw3'   => 'required',
            // 'pw2'   => 'required|min:'.$minPw.'|max:'.$maxPw.'|regex:/^(?=.*[A-Z])(?=.*?\d)(?=.*?[a-z])^[a-zA-Z0-9!#$%&()*+,.:;=?@\[\]^_{}-]+$/',
            // 'pw3'   => 'required|min:'.$minPw.'|max:'.$maxPw.'|regex:/^(?=.*[A-Z])(?=.*?\d)(?=.*?[a-z])^[a-zA-Z0-9!#$%&()*+,.:;=?@\[\]^_{}-]+$/',
            'pw2'    => ['required',
                        'min:'.$minPw,
                        'max:'.$maxPw,
                        new CheckPassword($rule),
                        ],
            'pw3'    => ['required',
                        'min:'.$minPw,
                        'max:'.$maxPw,
                        new CheckPassword($rule),
                        ],
        ], [
            'pw1.required'  => trans('auth.password'),
            'pw2.required'  => trans('auth.password'),
            'pw3.required'  => trans('auth.password'),
            'pw2.min'       => $minPw.trans('validation.password_min_lenght'),
            'pw2.max'       => $maxPw.trans('validation.password_max_lenght'),
            // 'pw2.regex'     => trans('validation.password_format'),
            'pw3.min'       => $minPw.trans('validation.password_min_lenght'),
            'pw3.max'       => $maxPw.trans('validation.password_max_lenght'),
            // 'pw3.regex'     => trans('validation.password_format'),
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return view('auth.changepassword', ['errors' => $errors]);
        } else {
            $t_error = false;
            //PW一致確認(現在)
            $get_user = user::where('usr_code', session('tmp_usr_code'))->first();
            if (!password_verify($request->pw1, $get_user->pw)) {
                //if (!Auth::attempt(['usr_code' => session('tmp_usr_code'), 'pw' => $request->pw1])) {
                $validator->errors()->add('pw1', 'validation.password_not_correctly');
                $t_error = true;
            }
            //PW一致確認(入力分)
            if ($request->pw2 != $request->pw3) {
                $validator->errors()->add('pw2', 'validation.password_new_3');
                $t_error = true;
            } else {
                //PWサイクル確認
                //$system_datas = password_cycle::where('usr_code', session('tmp_usr_code'))->where('pw', $request->pw2)->first();
                $system_datas = password_cycle::where('usr_code', session('tmp_usr_code'))->get();
                if (!empty($system_datas)) {
                    foreach ($system_datas as $system_data) {
                        if (password_verify($request->pw2, $system_data->pw)) {
                            $validator->errors()->add('pw2', 'validation.password_new_4');
                            $t_error = true;
                        }
                    }
                }
            }

            if ($t_error == true) {
                $errors = $validator->errors();
                return view('auth.changepassword', ['errors' => $errors]);
            } else {
                //パスワード更新
                $user = User::where('usr_code', session('tmp_usr_code'))->first();
                $param_upd = password_hash($request->pw2, PASSWORD_DEFAULT);
                $user->pw = $param_upd;
                $user->password_chenge_date = date("Y/m/d H:i:s");
                $user->pw_error_ctr = 0;
                $user->login_first = true;

                if ($user->update()) {
                } else {
                    return view('auth.changepassword', ["error" => "[Error]パスワード設定時にエラーが発生しました。"]);
                }
                //パスワード履歴更新
                //既存分の加算
                $password_cycles = password_cycle::where('usr_code', session('tmp_usr_code'))->get();
                foreach ($password_cycles as $password_cycle) {
                    $password_cycle->cycle = $password_cycle->cycle + 1;
                    if (!$password_cycle->save()) {
                        return view('auth.changepassword', ["error" => "[Error]パスワード設定時にエラーが発生しました。"]);
                    }
                }
                //変更分を追加
                $password_cycle = new password_cycle();

                $password_cycle->usr_code   = session('tmp_usr_code');
                $password_cycle->cycle      = 1;
                $password_cycle->pw         = $param_upd;
                if (!$password_cycle->save()) {
                    return view('auth.changepassword', ["error" => "[Error]パスワード設定時にエラーが発生しました。"]);
                }
                //範囲外を削除
                $password_life_cycle = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_life_cycle')->first();
                $password_cycles = password_cycle::where('usr_code', session('tmp_usr_code'))->where('cycle', '>', $password_life_cycle->f_setting_data)->delete();

                //保存完了
                return view('auth.login', ["ok_message" => "パスワードの更新が完了しました。"]);
            }
        }
    }
}
