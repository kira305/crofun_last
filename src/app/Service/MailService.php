<?php

namespace App\Service;

use App\User;
use App\Customer_MST;
use App\Concurrently;
use App\mail_mst;
use Mail;
use App\Constant_value;
use Exception;
use App\TokenStore\TokenCache;
use App\Jobs\SaveTeamsChatId;
use App\Jobs\SendTeamsMessageJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class MailService
{

    public function mail_text()
    {
        return 'があなたにCROSS-FUNのアカウントを作成しました。';
    }
    //PW　restのお知らせ
    public function send_mail_reset_password($to_email, $data, $subject, $user)
    {
        $send_edit = $this->creartSentence(3, $data);
        $subject = $send_edit['subject'];
        $content['content'] = $send_edit['content'];
        try {
            Mail::send(['text' => 'mails.noc_mail'], $content, function ($message) use ($to_email, $subject) {
                $message->to($to_email)->subject($subject);
            });
            if(count(Mail::failures()) > 0){
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
        // $tokenCache = new TokenCache();
        // $graph = $tokenCache->getGraph();
        // $mailObj = $send_edit['mailObj'];
        // $content = $send_edit['content'];
        // return $this->sendChatMessage($user, $graph, $mailObj, $content);

    }

    public function send_mail_create_user($to_email, $data, $subject, $user)
    {
        $send_edit          = $this->creartSentence(2, $data);
        $subject            = $send_edit['subject'];
        $content['content'] = $send_edit['content'];
        try {
            Mail::send(['text' => 'mails.noc_mail'], $content, function ($message) use ($to_email, $subject) {
                $message->to($to_email)->subject($subject);
            });
            if(count(Mail::failures()) > 0){
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        //　ユーザーのお知らせ　チャット
        // $tokenCache = new TokenCache();
        //　新しいアクセストークンを取得
        // $graph = $tokenCache->getGraph();
        // $mailObj = $send_edit['mailObj'];
        // $content = $send_edit['content'];
        //メッセージ送信のキュー
        // $job = (new SendTeamsMessageJob($user, $graph, $mailObj, $content))->delay(Carbon::now()->addSecond(3));
        // dispatch($job);

        // return true;
    }

    public function send_mail_change_pass($to_email, $data, $subject)
    {
        $result = Mail::send('mails.change_pass', $data, function ($message) use ($to_email, $subject) {
            $message->to($to_email)->subject($subject);
        });

        return $result;
    }
    // メール送信するためuser id リストを取得
    public function getListUserId($client_id)
    {
        $customer_mst = Customer_MST::where('id', $client_id)->first();
        $group_id     = $customer_mst->project->pluck('group_id');

        $user_id_1    = User::leftJoin('position_mst', 'user_mst.position_id', '=', 'position_mst.id')
            ->where('retire', false)
            ->where('mail_flag', true)
            ->whereIn('group_id', $group_id)->pluck('user_mst.id')->toArray();
        // 兼務のユーザー
        $user_id_2    = Concurrently::leftJoin(
            'position_mst',
            'concurrently_mst.position_id',
            '=',
            'position_mst.id'
        )
            ->where('status', true)
            ->where('mail_flag', true)
            ->whereIn('group_id', $group_id)->pluck('usr_id')->toArray();
        $user_id      = array_merge($user_id_1, $user_id_2);

        return $user_id;
    }

    // メール情報を抽出してそれぞれのユーザーに送る　与信情報のチャット
    public function sendCreditMail($client_id, $list_user_id)
    {
        $mail        = Mail_MST::find(1);
        $data        = array('mail_text' => $mail->mail_text);
        $subject     =  $mail->mail_remark;
        $users       = User::whereIn('id', $list_user_id)->get();

        //　アクセストークンの取得
        // $tokenCache = new TokenCache();
        // $graph = $tokenCache->getGraph();
        $action = $subject;

        foreach ($users as $user) {
            //　チャットを送信するためのjob
            // $job = (new SendTeamsMessageJob($user, $graph, $action, $mail->mail_text))->delay(Carbon::now()->addSecond(3));
            // dispatch($job);
            $to_email = $user->email_address;
            $result = Mail::send('mails.credit', $data, function ($message) use ($to_email, $subject) {
                $message->to($to_email)->subject($subject);
            });
        }
        return $users->pluck('id');
    }

    // メール本文生成
    public function creartSentence($mail_id, $data)
    {
        $chg_data = array(
            "##USER_ID##"            =>    "employee_id",
            "##USER_NAME##"          =>    "user_name",
            "##USER_PASSWORD##"    =>    "password",
        );

        $mail = Mail_MST::find($mail_id);
        $ret_data['subject'] = $mail->mail_remark;
        $ret_data['content'] = $mail->mail_text;
        $ret_data['mailObj'] = $mail;
        foreach ($chg_data as $key => $cdata) {
            if (!empty($data[$cdata])) {
                $ret_data['content'] = str_replace($key, $data[$cdata], $ret_data['content']);
            } else {
                $ret_data['content'] = str_replace($key, "", $ret_data['content']);
            }
        }
        return $ret_data;
    }

    //API機能 start

    //[action] 1:与信限度額 2:ユーザー新規登録 3:ユーザーパスワード再発行 4:契約更新アラート
    // public function sendChatMessage($user, $graph,$mailObj, $userCreateData = null)
    // {
    //     $action = $mailObj->mail_ma_name;
    //     $chatId = $user->teams_chat_id;
    //  APIのURL
    //     $getEventsUrl = '/chats/' . $chatId . '/messages';
    //     $sendContent = $this->getHtmlTitleMessage($mailObj, $userCreateData);
            //　APIのボディ
    //     $newEvent = [
    //         "importance" => "high",
    //         'body' => [
    //             'contentType' => "html" ,
    //             'content' => nl2br($sendContent),
    //         ]
    //     ];
    //     // POST /me/events
    //     try {
    //         $response = $graph->createRequest('POST', $getEventsUrl)
    //             ->attachBody($newEvent)
    //             ->setReturnType(Model\Event::class)
    //             ->setTimeout("1000")
    //             ->execute();
    //         // log send api info　APIのログ作成
    //         $logMessage = '対応ユーザーのメール: ' . $user->email_address . "\n" . 'アクション: ' . $action . "\n" ;
    //         Log::channel('graphapilog')->info($logMessage);
    //     } catch (Exception $e) {
    //         //log
    //         $this->sendMailWhenApiFail($action,$user,$e);
    //         //mail error send
    //         return false;
    //     }
    //     return true;
    // }
    //  メールテキストをHTMLに変換
    // private function getHtmlTitleMessage($mailObj, $userCreateData){
    //     if($mailObj->id == 1){
    //         $content = $mailObj->mail_text;
    //         return "<h1 style='color: #8a6d3b;background-color: #fcf8e3;border-color: #faebcc;padding: 10px 15px;border-radius: 3px;font-weight: bold'>".
    //                 $mailObj->mail_ma_name."</h1>"."\r\n".$content;
    //     }elseif($mailObj->id == 2){
    //         $content = $userCreateData;
    //         return "<h1 style='color: #31708f;background-color: #d9edf7;border-color: #bce8f1;padding: 10px 15px;border-radius: 3px;font-weight: bold'>".
    //                 $mailObj->mail_ma_name."</h1>"."\r\n".$content;
    //     }elseif($mailObj->id == 3){
    //         $content = $userCreateData;
    //         return "<h1 style='color: #3c763d;background-color: #dff0d8;border-color: #d6e9c6;padding: 10px 15px;border-radius: 3px;font-weight: bold'>".
    //                 $mailObj->mail_ma_name."</h1>"."\r\n".$content;
    //     }elseif($mailObj->id == 4){
    //         $content =  $mailObj->mail_text;
    //         return "<h1 style='color: #a94442;background-color: #f2dede;border-color: #ebccd1;padding: 10px 15px;border-radius: 3px;font-weight: bold'>".
    //                 $mailObj->mail_ma_name."</h1>"."\r\n".$content;
    //     }
    //     return false;
    // }

    // public function sendMailWhenApiFail($action,$user,$e)
    // {
    //     $mail        = Mail_MST::find(5);
    //     //log
    //     $logMessage = '対応ユーザーのメール: ' . $user->email_address . "\n" . 'アクション: ' . $action . "\n" . $e->getMessage();
    //     Log::channel('graphapilogerror')->info($logMessage);
    //     $data        = array('mail_text' => $logMessage);
    //     $subject     =  $mail->mail_remark;;

    //     $groupId = Constant_value::where('name','send_error_group_id')->first()->value;
    //     $users = User::where('retire', false)->where('group_id', $groupId)->get();
    //     foreach($users as $user){
    //         $to_email = $user->email_address;
    //         Mail::send('mails.api_error', $data, function ($message) use ($to_email, $subject) {
    //             $message->to($to_email)->subject($subject);
    //         });
    //     }

    //     return $data;
    // }

    // public function setUserTeamsId($user)
    // {
    //     $action = 'TeamsのユーザーID取得';
    //     $guzzle = new \GuzzleHttp\Client();
    //     $url = config('azure.tokenAppEndpoint');
    //     //APIの実行
    //     $token = json_decode($guzzle->post($url, [
    //         'form_params' => [
    //             'client_id' => config('azure.appId'),
    //             'client_secret' => config('azure.appSecret'),
    //             'resource' => 'https://graph.microsoft.com/',
    //             'grant_type' => 'client_credentials',
    //         ],
    //     ])->getBody()->getContents());
    //     //アプリケーション権限のaccessToken取得
    //     $accessToken = $token->access_token;
    //     $graph = new Graph();
    //     // graphにアクセストークンの追加
    //     $graph->setAccessToken($accessToken);
    //     $this->saveUserTeamsId($user, $graph, $action);
        //　Jobのテストで、実際のコードには、いらない
        // $job1 = (new SaveUserTeamsId($user, $graph, $action))->delay(Carbon::now()->addSecond(3));
        // dispatch($job1);

    //     return true;
    // }
    //　チームIDの取得
    // public function saveUserTeamsId($user, $graphApp, $action)
    // {
    //     try{
                // API チームID
    //         $teamsUser = $graphApp->createRequest("GET", "/users/" . $user->email_address)
    //             ->setReturnType(Model\User::class)
    //             ->execute();
    //         // log send api info
    //         $logMessage = '対応ユーザーのメール: ' . $user->email_address . "\n" . 'アクション: ' . $action . "\n" ;
    //         Log::channel('graphapilog')->info($logMessage);
                //USRテーブルにgetIDを登録できるようにセット
    //         $user->teams_user_id = $teamsUser->getId();
    //         $user->save();
    //     } catch (Exception $e) {
    //         //log
    //         $this->sendMailWhenApiFail($action,$user,$e);
    //         //mail error send
    //     }
    //     return true;
    // }
    // public function setTeamsChatId($user)
    // {
    //     $action = 'TeamsのチャットID作成';
    //     $tokenCache = new TokenCache();
    //     $graph = $tokenCache->getGraph();
         //　キューでチャットIDを取得するためにチャットを作成する
    //     $job1 = (new SaveTeamsChatId($user, $graph, $action))->delay(Carbon::now()->addSecond(3));
    //     dispatch($job1);
    //     return true;
    // }

    // public function saveTeamsChatId($user, $graph, $action)
    // {
    //     $userTeamId = "'" . $user->teams_user_id . "'";
    //     $teamsChatId = $this->getChatId($userTeamId, $graph, $user, $action);
    //     if(!$teamsChatId) return false;
    //     $user->teams_chat_id = $teamsChatId;
    //     $user->save();
    //     return true;
    // }

        //　作成したチャットのチャットIDを取得
    // public function getChatId($userTeamId, $graph, $user, $action)
    // {
                //Constant_value　テーブルにteamsの情報がセットされてテーブル
    //     $crofunTeamId = Constant_value::where('name','crofun_teams_user_id')->first()->value;
            //チャットを作成
    //     $body = [
    //         "chatType" => "oneOnOne",
    //         "members" => [
    //             [
    //                 "@odata.type" => "#microsoft.graph.aadUserConversationMember",
    //                 "roles" => [
    //                     "owner"
    //                 ],
    //                 "user@odata.bind" => "https://graph.microsoft.com/v1.0/users(" . $crofunTeamId . ")"
    //             ],
    //             [
    //                 "@odata.type" => "#microsoft.graph.aadUserConversationMember",
    //                 "roles" => [
    //                     "owner"
    //                 ],
    //                 "user@odata.bind" => "https://graph.microsoft.com/v1.0/users(" . $userTeamId . ")"
    //             ]
    //         ]
    //     ];
    //     try{
                //　チャットの作成API　
    //         $response = $graph->createRequest('POST', '/chats')
    //             ->attachBody($body)
    //             ->setReturnType(Model\Event::class)
    //             ->execute();
    //         // log send api info
    //         $logMessage = '対応ユーザーのメール: ' . $user->email_address . "\n" . 'アクション: ' . $action . "\n" ;
    //         Log::channel('graphapilog')->info($logMessage);
    //     } catch (Exception $e) {
    //         //log
    //         $this->sendMailWhenApiFail($action,$user,$e);
    //         //mail error send
    //         return false;
    //     }
          //  チャットIDをリターン
    //     return $response->getId();
    // }

    //API機能 end
}
