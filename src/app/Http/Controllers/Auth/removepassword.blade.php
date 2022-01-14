<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\NOC_Library\Noc_function001;
use App\User;
use App\system;
use App\Logger;
use Auth;
use Session;
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
        $this->lockoutTime  = 1;    //lockout for 1 minute (value is in minutes)
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
            $this->throttleKey($request), $attempts, $lockoutMinites
        );
    }

    protected function login(Request $request)
    {    
        if ($request->isMethod('post')) {
           $request->validate([
                'usr_code' => 'required',
                'pw'       => 'required',
            ], [
                'usr_code.required' => trans('auth.username'),
                'pw.required'       => trans('auth.password'),
            ]);
        	
        	
        	$lock_out = system::where('f_setting_group','login')->where('f_setting_name','lock_out')->first();
	        if (Auth::attempt(['usr_code' => $request->usr_code, 'pw' => $request->pw])) {
	            if(Auth::user()->retire == true){
					/*退職済み*/
					return view('auth.login', ["message"=>trans('message.login_fail'),"usr_code" => $request->usr_code,"pw" => $request->pw]);
	            }
					/*認証成功*/
					/*ロックアウト回数確認*/
       				if (Auth::user()->pw_error_ctr > $lock_out->f_setting_data){
						return view('auth.login', ["message"=>trans('message.login_fail'),"usr_code" => $request->usr_code,"pw" => $request->pw]);
       				}
                    $user = User::where('usr_code',$request->usr_code)->first();
					/*初回ログイン確認*/
		        	if ($user->login_first == true || $user->login_first == null){
						return view('auth.changepassword',["message"=>"[初回ログイン]パスワードの再設定を行ってください。"]);
		        	}

					/*パスワード更新サイクル確認password_cycle*/
        			if (!empty($user->password_chenge_date)){
			        	$password_cycle = system::where('f_setting_group','login')->where('f_setting_name','password_cycle')->first();
						$date1 = new DateTime($user->password_chenge_date);
						$date2 = new DateTime('now');
						$date2_diff = $date1->diff($date2);
        				if ($password_cycle < $date2_diff){
	        				//パスワード設定画面へ
							return view('auth.changepassword',["message"=>"パスワードの有効期限が切れました。再設定を行ってください。"]);
        				}
        			}else{
        				//パスワード設定画面へ
						return view('auth.changepassword',["message"=>"[初回ログイン]パスワードの再設定を行ってください。"]);
        			}

		        	/*ログイン成功の場合エラーカウントクリア*/
					$user->pw_error_ctr    =  0;
					if($user->update()){ 
					}

					/*TOPに遷移*/
					return redirect('/home');

	        }else {
	        		/*PWエラーカウントUp(但し、ロックアウト回数以上の場合はカウントしない)*/
                    $user = User::where('usr_code',$request->usr_code)->first();
	        		//ユーザーが存在した場合のみ
	        		if (!empty($user)){
	        			if (empty($user->pw_error_ctr)){
	    	                $user->pw_error_ctr    =  1;
	        			}else{
    	                	$user->pw_error_ctr    =  ++$user->pw_error_ctr;
	        			}
	        			//ロックアウト回数以上の場合はカウントしない
        				if ($user->pw_error_ctr <= ++$lock_out->f_setting_data){
        					//エラー回数更新
		                    if($user->update()){ 
	   	                	}
	                    }
	        		}

		        	/*PWエラー*/
					 $this->incrementLoginAttempts($request);
					 return view('auth.login', ["message"=>trans('message.login_fail'),"usr_code" => $request->usr_code,"pw" => $request->pw]);
	        }
        }
		return view('auth.login');   
        
    }

    public function logout(Request $request)
    {
            $this->guard()->logout();
            Session::flush();
            $request->session()->invalidate();

            return redirect('/');
    }

}
