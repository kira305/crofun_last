<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Service\MailService;
use App\Events\ChangePassEvent;
use App\User;
use App\Mail_MST;
use Crofun;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //        $this->middleware('guest');
    }

    public function reset_password(Request $request, MailService $mail_service)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'employee_id'   => 'required',
                'email'         => 'required',
            ], [
                'employee_id.required'        => trans('validation.user_code'),
                'email.required'               => trans('validation.mail_address'),
            ]);

            session()->flashInput($request->input());
            /*エラーチェック*/
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('employee_id')) {
                    $this->get_user($request->employee_id);
                }
                return view('auth.passwords.reset', ['errors' => $errors]);
            }

            $employee_id = $request->input('employee_id');
            $email       = $request->input('email');
            //$new_pw      = rand(100000,199999);
            $to_email    = $email;
            //nobusada
            //新規PWの設定
            $new_pw = Crofun::New_password_create();
            $param_upd = password_hash($new_pw, PASSWORD_DEFAULT);

            $user        = $this->get_user($employee_id, $new_pw);
            if ($user == false) {
                return view('auth.passwords.reset', ["message" => trans('message.usr_code_not_exist')]);
            } {
                if ($user->retire == true) {
                    return view('auth.passwords.reset', ["message" => trans('message.retire_employee_login')]);
                } else {
                    if (strcmp(trim($user->email_address), trim($email)) != 0) {
                        return view('auth.passwords.reset', ["message" => trans('message.code_and_mail')]);
                    }
                }
            }

            $user->pw = $param_upd;
            $user->login_first = false;
            $user->pw_error_ctr    =  0;

            //メール送信
            if ($user->update()) {
                event(new ChangePassEvent($user->usr_code));
                $mail_text   = $mail_service->mail_text();
                $data        = array(
                    'employee_id' => $employee_id,
                    'user_name'   => $user->usr_name,
                    "mail_text"  => $mail_text,
                    "password"   => $new_pw
                );

                $subject          = 'パスワード再発行';
                $result_send_mail = $mail_service->send_mail_reset_password($to_email, $data, $subject, $user);
                if (!$result_send_mail) {
                    return view('auth.passwords.reset', ["message" => 'メールが送信できません。']);
                } else {
                    return view('auth.login', ["ok_message" => "パスワード再発行が完了しました。"]);
                }
            } else {
                return view('auth.passwords.reset', ["message" => trans('message.password_can_not_update')]);
            }
        } else {
            return view('auth.passwords.reset');
        }
    }

    public function mail_text()
    {
        $mail_data   = Mail_MST::where('mail_id', 1)->first();
        if (!$mail_data) {
            return view('auth.passwords.reset', ["message" => trans('message.send_mail_fail')]);
        }
        return $mail_data->mail_text;
    }

    public  function get_user($employee_id)
    {
        $user        = User::where('usr_code', $employee_id)->first();
        if ($user != null) {
            return $user;
        } else {
            return false;
        }
    }
}
