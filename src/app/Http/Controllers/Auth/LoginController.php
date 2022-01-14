<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\User;
use App\system;
use App\Logger;
use Auth;
use Session;
use Crofun;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Auth guard
     *
     * @var
     */
    protected $auth;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        // $this->middleware('auth')->except('login');
        $this->auth = $auth;
        $this->lockoutTime  = 1;        //lockout for 1 minute (value is in minutes)
        $this->maxLoginAttempts = 3;    //lockout after 3 attempts

    }

    protected function getCredentials(Request $request)
    {
        return $request->only('user_code', 'pw');
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $attempts = 3;
        $lockoutMinites = 15;
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $attempts,
            $lockoutMinites
        );
    }

    protected function login(Request $request)
    {
        if ($request->isMethod('post')) {

            session()->flashInput($request->input());
            $request->validate([
                'usr_code' => 'required',
                'pw'       => 'required',
            ], [
                'usr_code.required' => trans('auth.username'),
                'pw.required'       => trans('auth.password'),
            ]);

            /*システムテーブルから、ロックアウトの回数を取得する*/
            $lock_out = system::where('f_setting_group', 'login')->where('f_setting_name', 'lock_out')->first();
            /*ユーザーテーブルから、ユーザー情報を取得する*/
            $get_user = user::where('usr_code', $request->usr_code)->first();

            /*認証を行う*/
            if (Auth::attempt(['usr_code' => $request->usr_code, 'pw' => $request->pw])) {

                if ($get_user->retire == true) {
                    /*退職済み*/
                    return view('auth.login', ["message" => trans('message.login_fail'), "usr_code" => $request->usr_code, "pw" => $request->pw]);
                }
                /*認証成功*/
                /*ロックアウト回数確認*/
                if ($get_user->pw_error_ctr > $lock_out->f_setting_data) {

                    return view('auth.login', ["message" => trans('message.too_many_login_fail'), "usr_code" => $request->usr_code, "pw" => $request->pw]);
                }
                $user = User::where('usr_code', $request->usr_code)->first();

                session(['tmp_usr_code'   => $request->usr_code]);

                /*初回ログイン確認*/
                if ($user->login_first == false || $user->login_first == null) {

                    return view('auth.changepassword', ["message" => "パスワードの再設定を行ってください。"]);
                }
                /*パスワード更新サイクル確認password_cycle*/
                if (!empty($user->password_chenge_date)) {
                    $password_cycle = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_cycle')->first();
                    $date1 = new \DateTime($user->password_chenge_date);
                    $date2 = new \DateTime();
                    $date2_diff = $date1->diff($date2);
                    $date_diff = $date2_diff->days;

                    if ($password_cycle->f_setting_data < $date2_diff->days) {
                        //パスワード設定画面へ
                        return view('auth.changepassword', ["message" => "パスワードの有効期限が切れました。再設定を行ってください。"]);
                    }
                } else {
                    //パスワード設定画面へ
                    return view('auth.changepassword', ["message" => "[初回ログイン]パスワードの再設定を行ってください。"]);
                }
                /*ログイン成功の場合エラーカウントクリア*/
                $user->pw_error_ctr    =  0;
                /*クッキー・セッションに取得情報を格納する。*/
                if ($user->update()) {
                    Cookie::queue(Cookie::forever('usr_code', $request->usr_code));
                    Cookie::queue(Cookie::forever('pw', $request->pw));

                    session(['usr_id'     => $get_user->id]);
                    session(['usr_code1'   => $request->usr_code]);
                    session(['pw1'         => $request->pw]);
                    /*ログインした際のログ*/
                    Crofun::log_create(Auth::user()->id, $get_user->id, null, config('constant.operation_LOGIN'), null, $get_user->company_id, null, null, null, null);
                    return redirect()->route('home');
                }
            } else {
                /*PWエラーカウントUp(但し、ロックアウト回数以上の場合はカウントしない)*/
                //$user = User::where('usr_code',$request->usr_code)->first();
                //ユーザーが存在した場合のみ
                if (!empty($get_user)) {
                    if (empty($get_user->pw_error_ctr)) {
                        $get_user->pw_error_ctr    =  1;
                    } else {
                        $get_user->pw_error_ctr    =  ++$get_user->pw_error_ctr;
                    }
                    //ロックアウト回数以上の場合はカウントしない
                    if ($get_user->pw_error_ctr <= ++$lock_out->f_setting_data) {
                        //エラー回数更新
                        if ($get_user->update()) {
                        }
                    }
                    //フンの追加
                    else {

                        return view('auth.login', ["message" => trans('message.too_many_login_fail'), "usr_code" => $request->usr_code, "pw" => $request->pw]);
                    }
                }
                /*PWエラー*/
                // $this->incrementLoginAttempts($request);
                return view('auth.login', ["message" => trans('message.login_fail'), "usr_code" => $request->usr_code, "pw" => $request->pw]);
            }
        }
        return view('auth.login');
    }

    public function setCookie()
    {
        $minutes = 60;
        $response = new Response('Set Cookie');
        $response->withCookie(cookie('name', 'MyValue', $minutes));
        return $response;
    }

    public function logout(Request $request)
    {
        if (Crofun::authenticate_Log('logout')) {

            Session::flush();
            Auth::logout();
            return redirect('/');
        }
    }
}
