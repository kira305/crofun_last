<?php

namespace App\Http\Controllers;

use Crofun;
use App\Concurrently;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\File_upload;
use App\User;
use App\customer_name_MST;
use App\Cost_MST;
use App\Credit_check;
use App\Customer_MST;
use App\Project_MST;
use App\Group_MST;
use App\Department_MST;
use App\Headquarters_MST;
use App\Service\MailService;
use App\Post;
use App\Process_MST;
use App\Receivable_MST;
use Mail;
use Auth;
use Response;
use Excel;
use DB;
use App\Table_MST;
use App\User_MST;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Jobs\SaveTeamsChatId;
use App\Jobs\SaveUserTeamsId;
use App\Jobs\SendTeamsMessageJob;
use App\Mail_MST;
use App\TokenStore\TokenCache;
use Illuminate\Support\Facades\Cache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class TestController extends Controller
{
    protected $mail_service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MailService $mail_service)
    {
        $this->mail_service = $mail_service;
    }

    public function import_user()
    {

        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/user_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $user    = new User();
                        $user->usr_code            = $content[1];
                        $user->usr_name            = $content[2];
                        $user->rule                = $content[3];
                        $user->pw                  = password_hash("Noc-net!", PASSWORD_DEFAULT);
                        $user->email_address       = $content[5];
                        $user->company_id          = $content[6];
                        $user->headquarter_id      = $content[7];
                        $user->department_id       = $content[8];
                        $user->group_id            = $content[9];
                        $user->retire              = $content[10];
                        $user->created_at          = Carbon::now();
                        $user->updated_at          = Carbon::now();
                        $user->position_id         = $content[13];
                        $user->pw_error_ctr        = $content[14];
                        $user->login_first         = true;
                        $user->password_chenge_date = Carbon::now();
                        if ($user->save()) {

                            $to_email    = $user->email_address;
                            $mail_text   = $this->mail_service->mail_text();
                            $data        = array(
                                'user_name' => $user->usr_name,
                                "mail_text" => $mail_text,
                                "password" => "Noc-net!",
                                "employee_id" => $user->usr_code
                            );
                            $subject     = trans('message.create_user_success_mail');
                            $result_send_mail = $this->mail_service->send_mail_create_user($to_email, $data, $subject);
                        }
                    }

                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function import_cost()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/cost_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $cost    = new Cost_MST();
                        $cost->cost_name = $content[0];
                        $cost->cost_code =  $content[1];
                        $cost->company_id =  (int)$content[4];
                        $cost->headquarter_id  =  (int)$content[5];
                        $cost->department_id =  (int)$content[6];
                        $cost->group_id        =  (int)$content[7];
                        $cost->type            =  (int)$content[8];
                        if ($content[9] == '-1') {
                            $cost->status          =  true;
                        } else {
                            $cost->status          =  false;
                        }


                        $cost->save();
                    }
                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }

    public function change_project(Request $request)
    {
        $this->createTestUser();
        $this->testPublicApi();
        if($request->action == 1){
            $this->getUserTeamsId();
        }elseif($request->action == 2){
            $this->getChatId();
        }else{
            $this->sendChatMessage();
        }
        // dd(session('response'));
        // Crofun::getChatId();
        // $this->sendChatMessage();
        //$this->tableUpdateCustomer();

        // $this->fixmailuser();
        // $projects = Project_MST::all();

        // foreach ($projects as $project) {
        //     $project->updated_at = '2020-08-12 13:58:18';
        //     $project->update();
        // }
    }

    public function testPublicApi(){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://worldtimeapi.org/api/timezone/Asia/Tokyo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        dd($result);
    }

    public function getUserTeamsId()
    {
        // $mailService = new MailService();

        $users = User::get();
        // $guzzle = new \GuzzleHttp\Client();
        // $url = 'https://login.microsoftonline.com/2dc8cdf2-eb35-41a0-939d-51afa1c7cd76/oauth2/token?api-version=1.0';
        // $token = json_decode($guzzle->post($url, [
        //     'form_params' => [
        //         'client_id' => '6f9303d3-0b5b-43a0-80f6-0a7ee5a06c1b',
        //         'client_secret' => 'TeK6k9E34QS4Z.5JC.0_YFPf.~WIxj2RYh',
        //         'resource' => 'https://graph.microsoft.com/',
        //         'grant_type' => 'client_credentials',
        //     ],
        // ])->getBody()->getContents());
        // $accessToken = $token->access_token;
        // $graphApp = new Graph();
        // $graphApp->setAccessToken($accessToken);

        // $teamsUser = $graphApp->createRequest("GET", "/users/" . "rpasoumu@noc-net.co.jp")
        //         ->setReturnType(Model\User::class)
        //         ->execute();
        // dd($teamsUser);
        $mailService = new MailService();
        $action = 'TeamsのユーザーID取得';
        $guzzle = new \GuzzleHttp\Client();
        $url = config('azure.tokenAppEndpoint');
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => config('azure.appId'),
                'client_secret' => config('azure.appSecret'),
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials',
            ],
        ])->getBody()->getContents());
        $accessToken = $token->access_token;
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        foreach ($users as $key => $user) {
            $mailService->saveUserTeamsId($user, $graph, $action);
            // $job2 = (new SaveTeamsChatId($user, $graph))->delay(Carbon::now()->addSecond(3));
            // dispatch($job2);
        }
        dd('teamid created');
    }

    public function sendChatMessage()
    {
        $users = User::where('id',81)->get();
        $graph = $this->getGraph();
        $mail_service = new Mail_MST();
        $mail = $mail_service->where('id',2)->first();
        $content = $mail->mail_text;
        foreach ($users as $key => $user) {
            $this->mail_service->sendChatMessage($user, $graph, $mail, $content);
            break;
            // if ($key == 20) break;
            // $job = (new SendTeamsMessageJob($mail_service, $user, $graph, $key))->delay(Carbon::now()->addSecond(3));
            // dispatch($job);
        }
        dd('send chat test ok');
    }

    public function getChatId()
    {
        $users = User::get();
        $action = 'TeamsのチャットID作成';
        $tokenCache = new TokenCache();
        $graph = $tokenCache->getGraph();
        foreach ($users as $key => $user) {
            $job1 = (new SaveTeamsChatId($user, $graph, $action))->delay(Carbon::now()->addSecond(3));
            dispatch($job1);
        }

        dd('teams chat id created');
    }

    private function getGraph(): Graph
    {
        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessTokenFromRefreshToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        return $graph;
    }

    public function changeDefault()
    {
        $creditCheck = Credit_check::get();
        $acc = Receivable_MST::get();
        $process = Process_MST::get();
        $project = Project_MST::get();

        // foreach($creditCheck as $key => $item){
        //     $item->rank = '??';
        //     $item->credit_expect = 10;
        //     $item->credit_limit = 10;
        //     $item->update();
        // }

        // foreach($acc as $key => $item){
        //     $item->credit = 10;
        //     $item->debit = 10;
        //     $item->receivable = 10;
        //     $item->save();
        // }

        // foreach($process as $key => $item){
        //     $item->credit = 10;
        //     $item->debit = 10;
        //     $item->receivable = 10;
        //     $item->save();
        // }

        foreach ($project as $key => $item) {
            $item->transaction_money = 1;
            $item->transaction_shot = 0;
            $item->save();
        }
        dd(2);
    }

    public function fixmailuser()
    {
        $users = User_MST::get();
        foreach ($users as $key => $user) {
            $user->email_address = "y-kasahara@noc-net.co.jp";
            $user->save();
        }
        dd(1);
    }

    public function createCloneUser()
    {
        try {
            $users = User_MST::where('group_id', '<>', 18)->where('id', '>', 379)->get();
            foreach ($users as $key => $user) {
                // $newUser->usr_name = $user->usr_name;
                // $newUser->usr_code = '3'.substr($user->usr_code, 1);
                // $newUser->company_id = $user->company_id;
                // $newUser->headquarter_id = $user->headquarter_id;
                // $newUser->department_id = $user->department_id;
                // $newUser->group_id = $user->group_id;
                // $newUser->position_id = 12;
                // $newUser->rule = 3;
                $user->pw = '$2y$10$xVbkldRKC88BhMdpqPcSte2lxK38JdhnQUXELqjHJQ/d49E7sjpAe';
                // $newUser->login_first = $user->login_first;
                // $newUser->pw_error_ctr = $user->pw_error_ctr;
                // $newUser->email_address = $user->email_address;
                // $newUser->password_chenge_date = $user->password_chenge_date;
                // $newUser->retire = $user->retire;
                $user->save();
            }
            dd(1);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function createTestUser()
    {
        try {
            $csv_url = public_path() . '/uploads/usercreate_test _concurent.csv';
            // mb_language("Japanese");
            $row = 0;

            if (($handle = fopen($csv_url, "r")) !== FALSE) {

                while (($data = fgetcsv($handle)) !== FALSE) { // if data is not end row
                    if ($row >= 1) {
                        $user                     = new User_MST();
                        $user->usr_name = $data[0];
                        $user->usr_code = $data[1];
                        $user->company_id = $data[2];
                        $user->headquarter_id = $data[3];
                        $user->department_id = $data[4];
                        $user->group_id = $data[5];
                        $user->position_id = $data[6];
                        $user->rule = $data[7];
                        $user->pw = '$2y$10$cpPKbILDnskjWx6s1rkwg.EecYXrhXDpOHgCLmdmr.OZBqdHaQWaW';
                        $user->login_first = true;
                        $user->email_address = 'trin.phuong@noc-net.co.jp';
                        $user->retire = false;

                        $concurrent               = new Concurrently();
                        // dd($data);
                        $user->usr_name = $data[0];
                        $user->usr_code = $data[1];
                        $user->company_id = 1;
                        $user->headquarter_id = 5;
                        $user->department_id = 15;
                        $user->group_id = 32;
                        $user->position_id = 25;
                        $user->rule = $data[2];
                        $user->pw = '$2y$10$cpPKbILDnskjWx6s1rkwg.EecYXrhXDpOHgCLmdmr.OZBqdHaQWaW';
                        $user->login_first = true;
                        $user->email_address = 'trin.phuong@noc-net.co.jp';
                        $user->retire = false;
                        $user->save();
                        $newUser = User_MST::where('usr_code', $data[1])->first();

                        $concurrent->usr_id           = $newUser->id;
                        $concurrent->usr_code         = $data[1];
                        $concurrent->usr_name         = $data[0];
                        $concurrent->company_id       = $data[3];
                        $concurrent->headquarter_id   = $data[4];
                        $concurrent->department_id    = $data[5];
                        $concurrent->group_id         = $data[6];
                        $concurrent->position_id      = $data[7];
                        $concurrent->status           = true;
                        $concurrent->save();
                    }
                    $row++;
                }
                fclose($handle);
            }
            dd(1);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    private function tableUpdateCustomer()
    {
        // $table = Table_MST::find(100);
        // dd((array)json_decode($table->field_name));
        $array = [
        "id" => "顧客ID",
        "company_id" => "会社ID",
        "client_code" => "仮顧客コード",
        "client_name" => "顧客名",
        "client_name_kana" => "顧客名ｶﾅ",
        "client_name_ab" => "略称",
        "closing_time" => "決算月日",
        "sale" => "取り引区分",
        "antisocial" => "反社チェック名",
        "collection_site" => "回収サイト",
        "transferee" => "振込人名称相違",
        "transferee_name" => "振込人名称",
        "credit" => "信用調査有無",
        "akikura_code" => "商蔵コード",
        "status" => "ステータス",
        "note" => "備考",
        "updated_at" => "更新日",
        "created_at" => "作成日",
        "corporation_num" => "法人コード",
        "tsr_code" => "TSRコード",
        "client_address" => "住所",
        "request_group" => "申請グループID",
        "expiration_date" => "決算月日",
        "status_name" => "ステータス",
        "type" => "取引区分",
        "client_code_main" => "顧客コード",
        "fgl_flag" => "FGLグループ会社",
        "representative_name" => "代表者氏名",
        ];
        $data = '[
            "id" => "顧客ID",
            "company_id" => "会社ID",
            "client_code" => "仮顧客コード",
            "client_name" => "顧客名",
            "client_name_kana" => "顧客名ｶﾅ",
            "client_name_ab" => "略称",
            "closing_time" => "決算月日",
            "sale" => "取り引区分",
            "antisocial" => "反社チェック名",
            "collection_site" => "回収サイト",
            "transferee" => "振込人名称相違",
            "transferee_name" => "振込人名称",
            "credit" => "信用調査有無",
            "akikura_code" => "商蔵コード",
            "status" => "ステータス",
            "note" => "備考",
            "updated_at" => "更新日",
            "created_at" => "作成日",
            "corporation_num" => "法人コード",
            "tsr_code" => "TSRコード",
            "client_address" => "住所",
            "request_group" => "申請グループID",
            "expiration_date" => "決算月日",
            "status_name" => "ステータス",
            "type" => "取引区分",
            "client_code_main" => "顧客コード",
            "fgl_flag" => "FGLグループ会社",
            "representative_name" => "代表者氏名",
            ]';
        $data = str_replace(array("\n", "\r", "  "), '', $data);
        $table = Table_MST::find(100);
        $table->field_name = json_encode($array);
        $table->item = $data;
        $table->update();
        dd($table);
    }

    private function tableUpdateContractFile()
    {
        $array = [
            'id' => 'ID',
            'contract_id' => '契約ID',
            'file_original_name' => '元ファイル名',
            'file_encryption_name' => 'ファイル暗号化名',
            'note' => '備考',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'file_type' => 'ファイルタイプ',
            'del_flg' => '削除フラグ'
        ];
        $data = "[
            'id' => 'ID',
            'contract_id' => '契約ID',
            'file_original_name' => '元ファイル名',
            'file_encryption_name' => 'ファイル暗号化名',
            'note' => '備考',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'file_type' => 'ファイルタイプ',
            'del_flg' => '削除フラグ'
        ]";
        $data = str_replace(array("\n", "\r", "  "), '', $data);
        $table = Table_MST::find(202);
        $table->field_name = json_encode($array);
        $table->item = $data;
        $table->update();
        dd($table);
    }


    private function tableUpdateContractType()
    {
        $array = [
            'id' => 'ID',
            'description' => '説明',
            'display_code' => '表示コード',
            'hidden' => '非表示',
            'type_name' => '名称',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'company_id' => '所属会社'
        ];
        $data = "[
            'id' => 'ID',
            'description' => '説明',
            'display_code' => '表示コード',
            'hidden' => '非表示',
            'type_name' => '名称',
            'created_at' => '作成日',
            'updated_at' => '更新日',
            'company_id' => '所属会社'
        ]";
        $data = str_replace(array("\n", "\r", "  "), '', $data);
        $table = Table_MST::find(201);
        $table->field_name = json_encode($array);
        $table->item = $data;
        $table->update();
        dd($table);
    }

    private function tableUpdate()
    {
        $array = [
            'id' => 'ID',
            'company_id' => '会社ID',
            'updated_at' => '更新日',
            'created_at' => '作成日',
            'client_id' => '顧客ID',
            'group_id' => 'グループID',
            'headquarter_id' => '申請本部ID',
            'department_id' => '申請部',
            'project_id' => 'プロジェクトID',
            'contract_type' => '契約種類ID',
            'application_num' => 'X-Point 申請番号',
            'stamp_receipt_date' => '押印受付日',
            'stamped_return_date' => '押印返却日',
            'collection_date' => '回収日',
            'contract_conclusion_date' => '契約締結日',
            'contract_start_date' => '契約開始日',
            'contract_end_date' => '契約終了日',
            'referenceable_department' => '参照可能部署',
            'auto_update' => '自動更新',
            'contract_span' => '契約スパン',
            'update_log' => '更新ログ',
            'note' => '備考',
            'contract_id' => '契約ID',
            'progress_status' => '進捗状況',
            'contract_canceled' => '契約中止',
            'update_finished' => '更新完了',
            'check_updates_deadline' => '更新の確認期限',
            'contract_completed' => '契約書のチェック完了',
            'pre_contract_id' => '前の契約ID',
            'status' => '非表示',
            'application_user_name' => '申請者'
        ];
        $data = "['id' => 'ID',
                'company_id' => '会社ID',
                'updated_at' => '更新日',
                'created_at' => '作成日',
                'client_id' => '顧客ID',
                'group_id' => 'グループID',
                'headquarter_id' => '申請本部ID',
                'department_id' => '申請部',
                'project_id' => 'プロジェクトID',
                'contract_type' => '契約種類ID',
                'application_num' => 'X-Point 申請番号',
                'stamp_receipt_date' => '押印受付日',
                'stamped_return_date' => '押印返却日',
                'collection_date' => '回収日',
                'contract_conclusion_date' => '契約締結日',
                'contract_start_date' => '契約開始日',
                'contract_end_date' => '契約終了日',
                'referenceable_department' => '参照可能部署',
                'auto_update' => '自動更新',
                'contract_span' => '契約スパン',
                'update_log' => '更新ログ',
                'note' => '備考',
                'contract_id' => '契約ID',
                'progress_status' => '進捗状況',
                'contract_canceled' => '契約中止',
                'update_finished' => '更新完了',
                'check_updates_deadline' => '更新の確認期限',
                'contract_completed' => '契約書のチェック完了',
                'pre_contract_id' => '前の契約ID,
                'status' => '非表示'',
                'application_user_name' => '申請者']";
        $data = str_replace(array("\n", "\r", "  "), '', $data);
        $table = Table_MST::find(104);
        $table->field_name = json_encode($array);
        $table->item = $data;
        $table->update();
        dd($table);
    }
    public function import_customer()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/customer_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);

                        $customer    = new Customer_MST();
                        $customer->id = (int)$content[0];
                        $customer->company_id = (int)$content[1];
                        $customer->client_code =  $content[2];
                        $customer->client_name =  $content[3];
                        $customer->client_name_kana  =  $content[4];
                        $customer->client_name_ab =  $content[5];
                        $customer->closing_time        =  $content[6];
                        $customer->sale        =  $content[7];
                        $customer->antisocial            =  $content[8];
                        $customer->collection_site    = $content[9];
                        $customer->transferee        = FALSE;
                        $customer->transferee_name    = $content[11];
                        $customer->credit    =  $content[12];
                        $customer->akikura_code    = (int)$content[13];
                        $customer->status    = (int)$content[14];
                        $customer->note    = $content[15];


                        $customer->corporation_num    = $content[18];
                        $customer->tsr_code   = (int)$content[19];
                        $customer->client_address  = $content[20];
                        $customer->request_group   = (int)$content[21];
                        $customer->client_code_main   = $this->clean($content[22]);

                        $customer->save();
                    }
                    $row++;
                }
            }

            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }

    public function change_code_customer()
    {
        $customers = Customer_MST::all();
        foreach ($customers as $customer) {
            $customer->client_code_main = $this->clean($customer->client_code_main);
            $customer->update();
        }
    }
    function clean($string)
    {

        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    }

    public function import_customer_name()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/customer_name.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $customer_name    = new Customer_name_MST();
                        $customer_name->id = (int)$content[0];
                        $customer_name->client_id = (int)$content[1];
                        $customer_name->client_name_s =  $content[2];
                        $customer_name->client_name_hankaku_s =  $content[3];
                        $customer_name->del_flag  =  (int)$content[4];
                        $customer_name->save();
                    }
                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }

    public function import_project()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/project_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $project    = new Project_MST();
                        $project->client_id = (int)$content[1];
                        $project->project_code = $content[2];
                        $project->project_name = $content[3];
                        $project->company_id = (int)$content[4];
                        $project->headquarter_id = (int)$content[5];
                        $project->department_id = (int)$content[6];
                        $project->group_id = (int)$content[7];
                        $project->get_code = $content[8];
                        $project->get_code_name = $content[9];
                        $project->once_shot = false;
                        $project->status = true;
                        $project->note = $content[12];
                        $project->transaction_money = (int)$content[13];
                        $project->transaction_shot = (int)$content[14];
                        $project->save();
                    }
                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }


    public function import_group()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/group_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $group    = new Group_MST();
                        $group->id = $content[0];
                        $group->group_name = $content[1];
                        $group->department_id =  $content[2];
                        $group->group_list_code =  $content[3];
                        $group->status  = true;
                        $group->note  =  $content[5];
                        $group->group_code  = $content[8];
                        $group->cost_code  =  $content[9];
                        $group->cost_name  =  $content[10];
                        $group->save();
                    }
                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }

    // public function import_group()
    // {
    //      DB::beginTransaction();
    //      try {

    //          $file_url = public_path() . '/import/group_mst.txt';
    //          $row = 0;
    //          if (($handle = fopen($file_url, "r")) !== FALSE) {

    //             while (($data = fgets($handle)) !== FALSE) {

    //                 if($row >= 1){

    //                    $content = explode(',', $data);
    //                    $group    = new Group_MST();
    //                    $group->group_name = $content[1];
    //                    $group->department_id =  $content[2];
    //                    $group->group_list_code =  $content[3];
    //                    $group->status  = true;
    //                    $group->note  =  $content[5];
    //                    $group->group_code  = $content[8];
    //                    $group->cost_code  =  $content[9];
    //                    $group->cost_name  =  $content[10];
    //                    $group->save();
    //                 }
    //                 $row++;

    //             }
    //          }
    //           DB::commit();
    //      }catch(Exception $e) {

    //             //エラーある場合は蓄積されたデータを消し
    //             DB::rollBack();

    //             throw new Exception($e);

    //     }

    // }


    public function import_department()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/department_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $department    = new Department_MST();
                        $department->department_name = $content[1];
                        $department->headquarters_id =  $content[2];
                        $department->department_list_code =  $content[3];
                        $department->status  = true;
                        $department->note  =  $content[5];
                        $department->department_code  =  $content[8];

                        $department->save();
                    }
                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }

    public function import_headquarter()
    {
        DB::beginTransaction();
        try {

            $file_url = public_path() . '/import/headquarters_mst.txt';
            $row = 0;
            if (($handle = fopen($file_url, "r")) !== FALSE) {

                while (($data = fgets($handle)) !== FALSE) {

                    if ($row >= 1) {

                        $content = explode(',', $data);
                        $headquarter    = new Headquarters_MST();
                        $headquarter->headquarters = $content[1];
                        $headquarter->company_id =  $content[2];
                        $headquarter->headquarter_list_code =  $content[3];
                        $headquarter->status  = true;
                        $headquarter->note  =  $content[5];
                        $headquarter->headquarters_code  =  $content[8];

                        $headquarter->save();
                    }
                    $row++;
                }
            }
            DB::commit();
        } catch (Exception $e) {

            //エラーある場合は蓄積されたデータを消し
            DB::rollBack();

            throw new Exception($e);
        }
    }
    public function mail()
    {
        $to_name = 'Noc server';
        $to_email = 'nguyen.hung@noc-net.co.jp';
        $data = array('name' => "Sam Jose", "body" => "顧客情報管理システムの件につきまして");
        $subject = '顧客管理システム';
        Mail::send('mails.mail', $data, function ($message) use ($to_email, $subject) {
            $message->to($to_email)->subject($subject);
        });
    }


    public function getupload(Request $request)
    {


        return view('test.upload');
    }

    public function viewupload(Request $request)
    {


        return view('test.view_upload');
    }

    public function upload(Request $request)
    {


        if ($request->isMethod('post')) {

            $cover = $request->file('bookcover');
            //$extension = $cover->getClientOriginalExtension();
            Storage::disk('public')->put($cover->getClientOriginalName(),  File::get($cover));
            $file            = new File_upload();
            $file->note      = $request->note;
            $file->file_name = $cover->getClientOriginalName();
            $file->save();
        }

        return view('test.view_upload', ["file" => $file]);
    }
    public function dowload()
    {


        $headers = array(
            'Content-Type: application/pdf',
        );
        $file = public_path() . "\uploads\N2.pdf";


        return Response::download($file, 'filename.pdf', $headers);
    }

    public function get_file()
    {

        $users = User::all();

        foreach ($users as $user) {

            $posts = $user->posts;

            foreach ($posts as $post) {

                var_dump($post->title);
            }
        }
        return view('test_data', ["users" => $users]);
    }
    public function create_excel()
    {

        $data = 1;
        Excel::create('Filename', function ($excel) use ($data) {
            //タイトルの変更
            $excel->setTitle('Winroda徒然草');

            $excel->setDescription('このファイルはWinroadが作成しました');

            $excel->sheet('mySheet', function ($sheet) use ($data) {
                $sheet->row(1, array(
                    '名前', '住所', '電話番号', '性別', '事務所', '銀行口座'
                ));

                $sheet->row(2, array(
                    '人事', '経理', '総務', '本部', '営業', '管理'
                ));

                $sheet->row(3, array(
                    '税金', '納税', '免除', '弁償', '解約', '解雇'
                ));
                // $sheet->row(1,function($row){
                //     //1行目のセルの背景色に青を指定
                //     $row->setBackground('#0000FF');
                //     //1行目のセルの文字色に白を指定
                //     $row->setFontColor('#FFFFFF');

                // });
                $sheet->setStyle([
                    'borders' => [
                        'allborders' => [
                            'color' => [
                                'rgb' => '#800000'
                            ]
                        ]
                    ]
                ]);

                $sheet->setHeight(1, 500);
                // $sheet->setAutoFilter();

                $sheet->cell('A7:E7', function ($cell) {

                    // $cell->setBackground('#00CED1');
                    // $cell->setBorder('solid');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A9', function ($cell) {

                    // $cell->setBackground('#00CED1');
                    // $cell->setBorder('solid');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A5:E5');
                $sheet->cell('A2', function ($cell) {

                    $cell->setBackground('#7FFF00');
                    $cell->setBorder('solid', 'none', 'none', 'solid');
                });

                //2行目の後に行を追加します。
                $sheet->appendRow(2, ['test1', 'test2']);
                //最終行の後に行を追加します。
                $sheet->appendRow(['test3', 'test4']);

                $sheet->setSize('A1', 25, 18);

                $sheet->setBorder('A7', 'thin');

                // $sheet->cell('B1', function($cell) {
                //     $cell->setValue('some value');
                // });
            });
        })->download();
    }

    public function show(Request $request)
    {

        $user = auth()->user();
        $post_id = $request->input('id');
        $post = Post::where('id', $id);

        if ($user->can('view', $post)) {

            echo 1;
        } else {

            echo 0;
        }
    }
    public function getuserinfor()
    {

        echo "get user infor";
    }

    public function getadmininfor()
    {

        echo "get admin infor";
    }

    public function getsystemadminonfor()
    {

        echo "get system admin infor";
    }
}
