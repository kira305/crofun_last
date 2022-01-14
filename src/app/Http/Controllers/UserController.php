<?php

namespace App\Http\Controllers;

use App\Service\UserService;
use App\Service\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\ChangePassEvent;
use App\User;
use App\Concurrently;
use App\password_cycle;
use App\Rules\CheckPassword;
use App\Rules\CompareUpdateTime;
use App\system;
use Auth;
use DB;
use Exception;
use Crofun;

class UserController extends Controller
{
    protected $user_service;
    protected $mail_service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $user_service, MailService $mail_service)
    {
        //$this->middleware('auth');
        $this->user_service = $user_service;
        $this->mail_service = $mail_service;
    }

    /**
     * ユーザー一覧ページ
     * @param http $request
     * @return ユーザーリスト
     */
    public function index(Request $request)
    {
        session(['userMst' => array('page' => $request->page)]);
        if ($request->isMethod('post')) {
            if (Auth::user()->rule()->superuser_user == 1) {
                $users = $this->user_service->searchBySuperRule(
                    $request->company_id,
                    $request->headquarter_id,
                    $request->department_id,
                    $request->group_id,
                    $request->usr_code,
                    $request->usr_name,
                    $request->position_id,
                    $request->rule_id
                );
            } else {
                $users = $this->user_service->searchUser(
                    $request->company_id,
                    $request->headquarter_id,
                    $request->department_id,
                    $request->group_id,
                    $request->usr_code,
                    $request->usr_name,
                    $request->position_id,
                    $request->rule_id
                );
            }

            session(['company_id'     => $request->company_id]);
            session(['headquarter_id' => $request->headquarter_id]);
            session(['department_id'  => $request->department_id]);
            session(['group_id'       => $request->group_id]);
            session(['usr_code'       => $request->usr_code]);
            session(['usr_name'       => $request->usr_name]);
            session(['position_id'    => $request->position_id]);
            session(['rule_id'        => $request->rule_id]);

            return view(
                'user.index',
                [
                    "users"          => $users, // 検索結果ユーザーリスト
                    "company_id"     => $request->company_id, // 選択された本部
                    "headquarter_id" => $request->headquarter_id, // 選択された本部
                    "department_id"  => $request->department_id, //選択された部署
                    "group_id"       => $request->group_id, //選択さたグループ
                    "usr_code"       => $request->usr_code, //入力されたユーザーコード
                    "usr_name"       => $request->usr_name,
                    "position_id"    => $request->position_id,
                    "rule_id"        => $request->rule_id,
                ]
            );
        }

        if ($this->checkSessionExisted() == 1) {
            $search_condition = $this->user_service->getUserBySession($request); //sessionに存在された検索条件をまとめる
            if (Auth::user()->rule()->superuser_user == 1) {
                $users            = $this->user_service->searchBySuperRule(
                    $search_condition[7],
                    $search_condition[0],
                    $search_condition[1],
                    $search_condition[2],
                    $search_condition[3],
                    $search_condition[4],
                    $search_condition[5],
                    $search_condition[6]
                );
            } else {
                $users            = $this->user_service->searchUser(
                    $search_condition[7],
                    $search_condition[0],
                    $search_condition[1],
                    $search_condition[2],
                    $search_condition[3],
                    $search_condition[4],
                    $search_condition[5],
                    $search_condition[6]
                );
            }

            return view(
                'user.index',
                [
                    "users"          => $users,
                    "company_id"     => session('company_id'),
                    "headquarter_id" => session('headquarter_id'),
                    "department_id"  => session('department_id'),
                    "group_id"       => session('group_id'),
                    "usr_code"       => session('usr_code'),
                    "usr_name"       => session('usr_name'),
                    "position_id"    => session('position_id'),
                    "rule_id"        => session('rule_id'),
                ]
            );
        } else {
            if (Auth::user()->rule()->superuser_user == 1) {
                $users = $this->user_service->getAllUsers();
            } else {
                $users = $this->user_service->getAllUserOfCompany();
            }

            return view('user.index', ["users" => $users]);
        }
    }

    public function checkSessionExisted()
    {
        if (session('company_id') != null && session('company_id') != "") {
            return 1;
        }

        if (session('headquater_id') != null && session('headquarter_id') != "") {
            return 1;
        }

        if (session('department_id') != null && session('department_id') != "") {
            return 1;
        }

        if (session('group_id') != null && session('group_id') != "") {
            return 1;
        }

        if (session('usr_code') != null && session('usr_code') != "") {
            return 1;
        }

        if (session('usr_name') != null && session('usr_name') != "") {
            return 1;
        }

        if (session('position_id') != null && session('position_id') != "") {
            return 1;
        }

        if (session('rule_id') != null && session('rule_id') != "") {
            return 1;
        }

        return 0;
    }
    public function validationDataInput(Request $request, $update_time = null)
    {
        $validator = Validator::make($request->all(), [
            'update_time'                => [new CompareUpdateTime($update_time)],
            'usr_code'      => 'required|max:7|min:7|regex:/^[0-9]*$/u',
            'usr_name'      => 'required|max:50',
            'company_id'    => 'required',
            'headquarter_id' => 'required',
            'department_id' => 'required',
            'group_id'      => 'required',
            'position_id'   => 'required',
            'mail_address'  => 'required|email|max:200',
            'rule_id'       => 'required'
        ], [
            'usr_code.required'         => trans('validation.user_code'),
            'usr_code.max'              => trans('validation.user_code_max'),
            'usr_code.min'              => trans('validation.user_code_max'),
            'usr_code.regex'            => trans('validation.code_int'),
            'usr_name.required'         => trans('validation.user_name'),
            'usr_name.max'              => trans('validation.max_string_50'),
            'company_id.required'       => trans('validation.company_chose'),
            'headquarter_id.required'   => trans('validation.headquarter_chose'),
            'department_id.required'    => trans('validation.department_chose'),
            'group_id.required'         => trans('validation.group_chose'),
            'position_id.required'      => trans('validation.user_position'),
            'mail_address.required'     => trans('validation.mail_address'),
            'mail_address.max'          => trans('validation.max_string_200'),
            'mail_address.email'        => trans('validation.email'),
            'rule_id.required'          => trans('validation.user_role'),
        ]);
        return $validator;
    }
    /**
     * ユーザー情報登録関数
     * @param http $request
     * @return ユーザー情報登録結果
     */
    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validationDataInput($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('company_id') && !$errors->has('usr_code')) {
                    if ($this->checkCodeIsExistWhenCreate($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }
                $errors = $validator->errors();
                return view('user.create', ["errors" => $errors]);
            }

            if ($this->checkCodeIsExistWhenCreate($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('user.create', ["errors" => $errors]);
            }
            // フォームからのデータ
            $usr_code       = $request->usr_code;
            $usr_name       = $request->usr_name;
            $company_id     = $request->company_id;
            $headquarter_id = $request->headquarter_id;
            $department_id  = $request->department_id;
            $group_id       = $request->group_id;
            $position_id    = $request->position_id;
            $email_address  = $request->mail_address;
            $rule_id        = $request->rule_id;
            $retire         = $request->retire;
            //$password       = rand(100000,199999);
            $password = Crofun::New_password_create();
            $param_upd = password_hash($password, PASSWORD_DEFAULT);

            // ユーザーオブジェクトを作る
            $user                  = new User();
            $user->usr_code        = $usr_code;
            $user->usr_name        = $usr_name;
            $user->company_id      = $company_id;
            $user->headquarter_id  = $headquarter_id;
            $user->department_id   = $department_id;
            $user->group_id        = $group_id;
            $user->email_address   = $email_address;
            $user->position_id     = $position_id;
            $user->rule            = $rule_id;
            $user->pw              = $param_upd;
            if ($retire == 'on') { // 退職ボタンをチェックした場合
                $user->retire = true;
            } else {
                $user->retire = false;
            }
            try {
                DB::beginTransaction();
                if ($user->save()) { //ユーザー情報保存
                    $to_email    = $email_address;
                    $mail_text   = $this->mail_service->mail_text();
                    $data        = array('user_name' => $usr_name, "mail_text" => $mail_text, "password" => $password, "employee_id" => $usr_code);
                    $subject     = trans('message.create_user_success_mail');
                    //save teams user id　teamsのユーザーID
                    // $this->mail_service->setUserTeamsId($user);
                    //save team chat id　　teamsのユーザーのチャットID　上のユーザーIDを取得したのちチャットIDを取得したいため、Jobにて実行
                    // $this->mail_service->setTeamsChatId($user);

                    $result_send_mail = $this->mail_service->send_mail_create_user($to_email, $data, $subject, $user);
                    Crofun::log_create(Auth::user()->id, $user->id, config('constant.USER'), config('constant.operation_CRATE'), config('constant.USER_ADD'), $user->company_id, $user->usr_name, $user->usr_code, json_encode($user), null);
                    if (!$result_send_mail) { // メール送信失敗場合
                        return view('user.create', ["message" => trans('message.send_mail_fail')]);
                    }
                }

                DB::commit();
                return back()->with('success', trans('message.user_create_success'));
            } catch (Exception $e) {
                DB::rollBack();  // メール送信失敗場合登録した情報を保存しない
                throw new Exception($e); // エラーが発生したら別途で解決
            }
        }
        return view('user.create');
    }
    /**新規の重複チェック**/
    public function checkCodeIsExistWhenCreate(Request $request)
    {
        $user = User::where('usr_code', $request->usr_code)->first();
        if ($user) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ユーザー情報を修正関数
     * @param http $request
     * @return ユーザー情報変更結果
     */
    public function edit(Request $request)
    {
        $concurrents = $this->getConcurrently($request->id);
        if ($request->isMethod('post')) {
            $user        = User::where('id', $request->id)->first();
            $update_time = $user->updated_at;
            $validator   = $this->validationDataInput($request, $update_time);
            $old_date    = json_encode($user);
            $user        = $this->setUserProperty($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                $user->updated_at = $errors->has('update_time') ? $request->update_time : $update_time;
                if (!$errors->has('company_id') && !$errors->has('usr_code')) {
                    if ($this->checkCodeIsExistWhenEdit($request)) {
                        $validator->errors()->add('unique', trans('validation.code_unique'));
                    }
                }
                $errors = $validator->errors();
                return view('user.edit', ["errors" => $errors, 'user' => $user, 'concurrents' => $concurrents]);
            }

            if ($this->checkCodeIsExistWhenEdit($request)) {
                $validator->errors()->add('unique', trans('validation.code_unique'));
                $errors = $validator->errors();
                return view('user.edit', ["errors" => $errors, 'user' => $user, 'concurrents' => $concurrents]);
            }

            try {
                if ($user->update()) {
                    Concurrently::where('usr_id', $user->id)->update(['usr_code' => $user->usr_code]);
                    Concurrently::where('usr_id', $user->id)->update(['usr_name' => $user->usr_name]);
                    //UPADTEのログ
                    Crofun::log_create(Auth::user()->id, $user->id, config('constant.USER'), config('constant.operation_UPDATE'), config('constant.USER_EDIT'), $user->company_id, $user->usr_name, $user->usr_code, json_encode($user), $old_date);
                    return view('user.edit', [
                        'user' => $user,
                        'concurrents' => $concurrents,
                        'message' => trans('message.user_update_success')
                    ]);
                }
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e); //　エラー発生場合別途で処理
            }
        }
        $user = $this->user_service->getUserInfor($request->id);
        return view('user.edit', ["user" => $user, 'concurrents' => $concurrents]);
    }

    public  function setUserProperty(Request $request)
    {
        $usr_code              = $request->usr_code;
        $usr_name              = $request->usr_name;
        $company_id            = $request->company_id;
        $headquarter_id        = $request->headquarter_id;
        $department_id         = $request->department_id;
        $group_id              = $request->group_id;
        $position              = $request->position_id;
        $email_address         = $request->mail_address;
        $rule_id                  = $request->rule_id;
        $retire                = $request->retire;
        // $password              = rand(100000,199999);
        // 修正ユーザーを取得
        $user                     = User::where('id', $request->id)->first();
        $user->id                 = $request->id;
        $user->usr_code           = $usr_code;
        $user->usr_name           = $usr_name;
        $user->company_id         = $company_id;
        $user->headquarter_id     = $headquarter_id;
        $user->department_id      = $department_id;
        $user->group_id           = $group_id;
        $user->email_address      = $email_address;
        $user->position_id        = $position;
        $user->rule               = $rule_id;
        if ($retire == 'on') {  // 退職ボタンをチェックする場合
            $user->retire = true;
        } else {
            $user->retire = false;
        }
        return $user;
    }
    /**重複チェック**/
    public function checkCodeIsExistWhenEdit(Request $request)
    {
        $users = User::where('usr_code', $request->usr_code)->get();
        foreach ($users as $user) {
            if ($user->id != $request->id) {
                return true;
            }
        }
        return false;
    }

    public function getConcurrently($usr_id)
    {
        $concurrents = $this->user_service->getConcurrently($usr_id);
        return $concurrents;
    }
    /**
     * 兼務情報を追加する関数
     * @param http $request
     * @return 兼務情報の追加結果
     */
    public function concurrent_create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'usr_code'        => 'required',
                'usr_name'        => 'required|max:50',
                'company_id_c'    => 'required',
                'headquarter_id_c' => 'required',
                'department_id_c' => 'required',
                'group_id_c'      => 'required',
                'position_id_c'   => 'required',
            ], [
                'usr_code.required'         => trans('validation.user_code'),
                'usr_name.required'         => trans('validation.user_name'),
                'usr_name.max'              => trans('validation.max_string_25'),
                'company_id_c.required'       => trans('validation.company_chose'),
                'headquarter_id_c.required'   => trans('validation.headquarter_chose'),
                'department_id_c.required'    => trans('validation.department_chose'),
                'group_id_c.required'         => trans('validation.group_chose'),
                'position_id_c.required'      => trans('validation.position_chose'),
            ]);
            $user = $this->user_service->getUserInfor($request->usr_id);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                return view('user.concurrent_create', ["errors" => $errors, "user" => $user]);
            }
            /*重複チェック*/
            if ($this->check_crateconcurrent($request)) {
                $validator->errors()->add('unique', trans('validation.group_doulble'));
                $errors = $validator->errors();
                return view('user.concurrent_create', ["errors" => $errors, "user" => $user]);
            }
            $usr_id         = $request->usr_id;
            $usr_code       = $request->usr_code;
            $usr_name       = $request->usr_name;
            $company_id     = $request->company_id_c;
            $headquarter_id = $request->headquarter_id_c;
            $department_id  = $request->department_id_c;
            $group_id       = $request->group_id_c;
            $position_id    = $request->position_id_c;

            $concurrently = new Concurrently();
            $concurrently->usr_id           = $usr_id;
            $concurrently->usr_code         = $usr_code;
            $concurrently->usr_name         = $usr_name;
            $concurrently->company_id       = $company_id;
            $concurrently->headquarter_id   = $headquarter_id;
            $concurrently->department_id    = $department_id;
            $concurrently->group_id         = $group_id;
            $concurrently->position_id      = $position_id;
            $concurrently->status           = true;


            if ($concurrently->save()) { //追加成功場合
                //UPADTEのログ
                Crofun::log_create(Auth::user()->id, $concurrently->id, config('constant.CONCURRENTLY'), config('constant.operation_CRATE'), config('constant.CONCURRENTLY_ADD'), $concurrently->company_id, $concurrently->usr_name, $concurrently->usr_code, json_encode($concurrently), null);
                return view('user.concurrent_create', ["user" => $user, 'message' => trans('message.user_concurrent_success')]);
            } else { // 追加失敗場合
                return view('user.concurrent_create', ["user" => $user, 'message' => trans('message.user_concurrent_fail')]);
            }
        }

        $request->session()->forget('company_id');
        $user        = $this->user_service->getUserInfor($request->usr_id);
        return view('user.concurrent_create', ["user" => $user]);
    }

    /**
     * ユーザーの兼務情報を修正
     * @param $request
     * @return 兼務情報修正結果
     */
    public function concurrent_edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $concurrently = Concurrently::where('id', $request->concurrent_id)->first();
            $update_time = $concurrently->updated_at;
            $validator = Validator::make($request->all(), [
                'update_time'                => [new CompareUpdateTime($update_time)],
                'usr_code'      => 'required',
                'usr_name'      => 'required|max:50',
                'company_id'    => 'required',
                'headquarter_id' => 'required',
                'department_id' => 'required',
                'group_id'      => 'required',
                'position_id'   => 'required',
            ], [
                'usr_code.required'         => trans('validation.user_code'),
                'usr_name.required'         => trans('validation.user_name'),
                'usr_name.max'              => trans('validation.max_string_25'),
                'company_id.required'       => trans('validation.company_chose'),
                'headquarter_id.required'   => trans('validation.headquarter_chose'),
                'department_id.required'    => trans('validation.department_chose'),
                'group_id.required'         => trans('validation.group_chose'),
                'position_id.required'      => trans('validation.position_chose'),
            ]);
            $concurrent         = $this->setConcurrentProperty($request);
            session()->flashInput($request->input());
            if ($validator->fails()) {
                $errors = $validator->errors();
                $concurrent->updated_at = $errors->has('update_time') ? $request->update_time : $update_time;
                return view('user.concurrent_edit', ["errors" => $errors, "concurrent" => $concurrent]);
            }
            /*重複チェック*/
            if ($this->check_updaconcurrent($request, $concurrently)) {
                $validator->errors()->add('unique', trans('validation.group_doulble'));
                $errors = $validator->errors();
                return view('user.concurrent_edit', ["errors" => $errors, "concurrent" => $concurrent]);
            }
            try {
                $usr_code       = $request->usr_code;
                $usr_name       = $request->usr_name;
                $company_id     = $request->company_id;
                $headquarter_id = $request->headquarter_id;
                $department_id  = $request->department_id;
                $group_id       = $request->group_id;
                $position_id    = $request->position_id;
                //本人情報を取得
                $old_date    = json_encode($concurrently);
                $concurrently->usr_code         = $usr_code;
                $concurrently->usr_name         = $usr_name;
                $concurrently->company_id       = $company_id;
                $concurrently->headquarter_id   = $headquarter_id;
                $concurrently->department_id    = $department_id;
                $concurrently->group_id         = $group_id;
                $concurrently->position_id      = $position_id;

                if ($concurrently->update()) {
                    //UPADTEのログ
                    Crofun::log_create(Auth::user()->id, $concurrently->id, config('constant.CONCURRENTLY'), config('constant.operation_UPDATE'), config('constant.CONCURRENTLY_EDIT'), $concurrently->company_id, $concurrently->usr_name, $concurrently->usr_code, json_encode($concurrently), $old_date);
                    return view('user.concurrent_edit', ["concurrent" => $concurrently, 'message' => trans('message.concurrent_update_success')]);
                } else {
                    return view('user.concurrent_edit', ["concurrent" => $concurrently, 'message' => trans('message.concurrent_update_fail')]);
                }
            } catch (Exception $e) { // エラーが発生場合
                throw new Exception($e);
            }
        }
        $concurrent_id      = $request->id;
        $concurrent         = Concurrently::where('id', $concurrent_id)->first();
        return view('user.concurrent_edit', ["concurrent" => $concurrent]);
    }

    public function setConcurrentProperty(Request $request)
    {
        $usr_code       = $request->usr_code;
        $usr_name       = $request->usr_name;
        $company_id     = $request->company_id;
        $headquarter_id = $request->headquarter_id;
        $department_id  = $request->department_id;
        $group_id       = $request->group_id;
        $position       = $request->position_id;

        $concurrently = Concurrently::where('id', $request->concurrent_id)->first(); //本人情報を取得
        $concurrently->usr_code         = $usr_code;
        $concurrently->usr_name         = $usr_name;
        $concurrently->company_id       = $company_id;
        $concurrently->headquarter_id   = $headquarter_id;
        $concurrently->department_id    = $department_id;
        $concurrently->group_id         = $group_id;
        $concurrently->position_id      = $position;

        return $concurrently;
    }

    /**
     * 兼務情報削除関数
     * @param http $request
     * @return 兼務情報を削除結果
     */
    public function concurrent_delete(Request $request)
    {
        $usr_id      = $request->usr_id;
        $id          = $request->id;
        $del_data = Concurrently::where('id', $id)->first();
        $delete      = Concurrently::where('id', $id)->update(['status' => false]);
        $new_data = Concurrently::where('id', $id)->first();
        $user        = $this->user_service->getUserInfor($usr_id);
        $concurrents = $this->user_service->getConcurrently($usr_id);
        Crofun::log_create(Auth::user()->id, $id, config('constant.CONCURRENTLY'), config('constant.operation_DELETE'), config('constant.USER_EDIT'), $del_data->company_id, $user->usr_name, $user->usr_code, json_encode($new_data), json_encode($del_data));
        if ($delete) {
            return view('user.edit', [
                "user" => $user,
                'concurrents' => $concurrents,
                'message' => trans('message.delete_success')
            ]);
        } else {
            return view('user.edit', [
                "user"        => $user,
                'concurrents' => $concurrents,
                'message'     =>  trans('message.delete_fail')
            ]);
        }
    }
    //兼務解除取消　
    public function concurrent_reset(Request $request)
    {
        $id       = $request->id;
        $concurrent = Concurrently::where('id', $id)->first();
        $old_concurrent = Concurrently::where('id', $id)->first();
        $concurrent->status = true;
        $concurrent->update();
        $user        = $this->user_service->getUserInfor($concurrent->usr_id);
        $new_concurrent = Concurrently::where('id', $id)->first();

        Crofun::log_create(Auth::user()->id, $id, config('constant.CONCURRENTLY'), config('constant.operation_UPDATE'), config('constant.USER_EDIT'), $old_concurrent->company_id, $user->usr_name, $user->usr_code, json_encode($new_concurrent), json_encode($old_concurrent));
        return redirect('/user/edit/' . $concurrent->usr_id);
    }
    /**
     * ユーザーのパスワードを変更
     * @param http $request
     * @return パスワード変更の結果
     */
    public function changePass(Request $request)
    {
        if ($request->isMethod('post')) {
            $minPw = system::where('f_setting_name','pass_min')->first()->f_setting_data;
            $maxPw = system::where('f_setting_name','pass_max')->first()->f_setting_data;
            $rule = system::where('f_setting_name','pass_char')->first()->f_setting_data;
            $validator = Validator::make($request->all(), [
                'usr_code'      => 'required|min:7|max:7',
                'now_pass'      => 'required',
                'new_pass_1'    => ['required',
                                    'min:'.$minPw,
                                    'max:'.$maxPw,
                                    new CheckPassword($rule),
                                    ],
                'new_pass_2'    => ['required',
                                    'min:'.$minPw,
                                    'max:'.$maxPw,
                                    new CheckPassword($rule),
                                    ],
                // 'new_pass_1'    => 'required|min:'.$minPw.'|max:'.$maxPw.'|regex:/^(?=.*[A-Z])(?=.*?\d)(?=.*?[a-z])^[a-zA-Z0-9!#$%&()*+,.:;=?@\[\]^_{}-]+$/',
                // 'new_pass_2'    => 'required|min:'.$minPw.'|max:'.$maxPw.'|regex:/^(?=.*[A-Z])(?=.*?\d)(?=.*?[a-zA-Z])^[a-zA-Z0-9!#$%&()*+,.:;=?@\[\]^_{}-]+$/',

            ], [
                'usr_code.required'         => trans('validation.user_code'),
                'now_pass.required'         => trans('validation.password_now'),
                'new_pass_1.required'       => trans('validation.password_new_1'),
                'new_pass_1.min'            => $minPw.trans('validation.password_min_lenght'),
                'new_pass_1.max'            => $maxPw.trans('validation.password_max_lenght'),
                // 'new_pass_1.regex'          => trans('validation.password_format'),
                'new_pass_2.required'       => trans('validation.password_new_2'),
                'new_pass_2.min'            => $minPw.trans('validation.password_min_lenght'),
                'new_pass_2.max'            => $maxPw.trans('validation.password_max_lenght'),
                // 'new_pass_2.regex'          => trans('validation.password_format'),
            ]);

            session()->flashInput($request->input());
            //エラーチェック
            if ($validator->fails()) {
                $errors = $validator->errors();
                if (!$errors->has('now_pass')) {
                    if (!Auth::attempt(['usr_code' => $request->usr_code, 'pw' => $request->now_pass])) {
                        $validator->errors()->add('correct', trans('validation.password_not_correctly'));
                    }
                }
                $errors = $validator->errors();
                return view('auth.passwords.change_password', ['errors' => $errors]);
            }

            $usr_code   = $request->usr_code;
            $new_pass_1 = $request->new_pass_1;
            $new_pass_2 = $request->new_pass_2;

            //同じPWか
            if (strcmp($new_pass_1, $new_pass_2) != 0) { //一回入力されたpwと再入力pwに比較
                $validator->errors()->add('new_pass_retype', trans('message.password_not_map'));
                $errors = $validator->errors();
                return view('auth.passwords.change_password', ['errors' => $errors]);
            } else {
                //PWサイクル確認
                $t_error = false;
                $system_datas = password_cycle::where('usr_code', $usr_code)->get();
                if (!empty($system_datas)) {
                    foreach ($system_datas as $system_data) {
                        if (password_verify($request->new_pass_1, $system_data->pw)) {
                            $validator->errors()->add('new_pass_1', trans('validation.password_new_4'));
                            $t_error = true;
                        }
                    }
                }

                if ($t_error == true) {
                    $errors = $validator->errors();
                    return view('auth.passwords.change_password', ['errors' => $errors]);
                }
            }

            $user = User::where('usr_code', $usr_code)->first();
            $user->pw = password_hash($new_pass_1, PASSWORD_DEFAULT);
            if ($user->update()) { //パスワードアップデート
                //パスワード履歴更新
                event(new ChangePassEvent($usr_code));
                //既存分の加算
                $password_cycles = password_cycle::where('usr_code', $usr_code)->get();
                foreach ($password_cycles as $password_cycle) {
                    $password_cycle->cycle = $password_cycle->cycle + 1;
                    if (!$password_cycle->save()) {
                        return view('auth.passwords.change_password', ["error" => "[Error]パスワード設定時にエラーが発生しました。"]);
                    }
                }
                //変更分を追加
                $password_cycle = new password_cycle();
                $password_cycle->usr_code   = $usr_code;
                $password_cycle->cycle      = 1;
                $password_cycle->pw         = $user->pw;

                if (!$password_cycle->save()) {
                    return view('auth.passwords.change_password', ["error" => "[Error]パスワード設定時にエラーが発生しました。"]);
                }
                //範囲外を削除
                $password_life_cycle = system::where('f_setting_group', 'login')->where('f_setting_name', 'password_life_cycle')->first();
                $password_cycles = password_cycle::where('usr_code', $usr_code)->where('cycle', '>', $password_life_cycle->f_setting_data)->delete();

                return view('auth.passwords.change_password', ['message' =>  trans('message.password_changed')]);
            }
        }
        return view('auth.passwords.change_password');
    }

    public function getListConcurrently(Request $request)
    {
        if ($request->isMethod('post')) {
            $user_id = $request->user_id;
            try {
                $list_concurrent = $this->user_service->getConcurrently($user_id);
            } catch (Exception $e) {
                throw new Exception($e);
            }
            return response()->json(['lists' =>  $list_concurrent]);
        }
    }
    /*重複新規チェック*/
    public function check_crateconcurrent($request)
    {
        $check = Concurrently::where('group_id', $request->group_id_c)->where('usr_id', $request->usr_id)->first();
        if ($check) {
            return true;
        }

        $checks = User::where('group_id', $request->group_id_c)->where('id', $request->usr_id)->get();
        foreach ($checks as $check) {
            if ($check->id !=  $request->concurrent_id) {
                return true;
            }
        }
        return false;
    }


    /*重複更新チェック*/
    public function check_updaconcurrent($request, $concurrently)
    {
        $checks = Concurrently::where('usr_id', $concurrently->usr_id)->where('group_id', $request->group_id)->get();
        foreach ($checks as $check) {
            if ($check->id !=  $request->concurrent_id) {
                return true;
            }
        }

        $checks = User::where('id', $concurrently->usr_id)->where('group_id', $request->group_id)->get();
        foreach ($checks as $check) {
            if ($check->id !=  $request->concurrent_id) {
                return true;
            }
        }
        return false;
    }
}
