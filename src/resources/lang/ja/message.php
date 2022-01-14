<?php

return [

    'process_status_non'        => '確定不可',

    'save_success'              => '保存しました。',
    'save_fail'                 => 'エラーが発生しました。',

    'update_success'            => '変更しました。',
    'update_fail'               => '更新失敗しました。',

    'delete_success'            => '削除しました。',
    'delete_fail'               => '削除できません。',

    'too_many_login_fail'       => '指定回数ID・PWを間違ったため、パスワードを再発行して下さい。',
    'login_fail'                => '社員番号またはパスワードが正しくありません。',

    'customer_code'             => '顧客コードが重複しています。',

    'create_success'            => '登録が完了しました。',
    'edit_success'              => '更新が完了しました。',

    // user

    'usr_code_not_exist'        => '社員番号が存在していません。',
    'password_not_map'          => '新しいパスワードが一致していません。',

    'user_create_success'       => 'ユーザー情報を登録しました。',
    'user_create_fail'          => 'ユーザー情報の登録に失敗しました。',
    'user_update_success'       => 'ユーザー情報を変更しました。',
    'user_update_fail'          => 'ユーザー情報の変更に失敗しました。',
    'user_concurrent_success'   => 'ユーザー兼務情報を登録しました。',
    'user_concurrent_fail'      => 'ユーザー兼務情報の登録に失敗しました。',
    'concurrent_update_success' => 'ユーザー兼務情報を更新しました。',
    'concurrent_update_fail'    => 'ユーザー兼務情報の更新に失敗しました。',

    // password

    'usr_code_not_exist'        => '社員番号が存在していません。',
    'password_changed'          => 'パスワードが変更されました。',
    'retire_employee_login'     => '退職者がログインに禁止です。',
    'password_can_not_update'   => 'パスワードがアップデトできません。',


    // company


    //headquarter


    //department



    // group
    'group_change_success'      => 'グループ情報を変更しました。',
    'group_change_fail'         => 'グループ情報の変更に失敗しました。',
    'group_create_success'      => 'グループ情報を登録しました。',
    'group_create_fail'         => 'グループ情報の登録に失敗しました。',


    // mail
    'send_mail_success'         => 'メール送信しました。',
    'send_mail_fail'            => 'メール送信できません。',
    'create_user_success_mail'  => 'CROSS-FUNのアカウント発行。',

    // rule
    'create_rule'               => '画面機能のルールを登録しました。',
    'edit_rule'                 => '画面機能のルールを変更しました。',

    // position

    'create_position_success'   => '役職情報を登録しました。',
    'edit_position_success'     => '役職情報を変更しました。',

   // login
    'code_and_mail'             => 'メールアドレスと社員番号が一致しておりません。',

    'expired'                   => 'タイムアウトになりました。再度ログインしてください。',

    'import_file_pca'           => 'ファイルの構造がただしくありません。',
    'import_file__type_pca'     => 'csvおよびtxtファイルのみを選択して下さい。',
    'get_time_import'           => '取得年月を入力して下さい。',
    'file_type'                 => 'ファイル種類を選択して下さい。',
    'import_file_risumon'       => 'ファイルの構造がただしくありません。',

    // contract
    'contract_upload_success'   => '契約がアップロードされました。',
    'contract_upload_fail'      => '契約のアップに失敗しました。',

    'client_code_not_exist'     => '顧客コードが存在していません。',
    'project_code_not_exist'    => 'プロジェクトコードが存在していません。',

    'project_name_unique'       => 'プロジェクト名が重複しています。',

    //credit
    'credit_client_name'        => '・取込んだRMの顧客名と不一致：',
    'credit_corporation_num'    => '・取込んだRMの法人番号と不一致：',
    'credit_tsr_code'           => '・取込んだRMのTSRコードと不一致：',
    'credit_double'             => '連続登録は、不可です。',
    'customer_close'            => 'この顧客が取引中プロジェクトがあります。',
    'project_close'             => 'このプロジェクトの顧客が取引終了になりました。',

    'close_message'             => '紐づいているデータは、編集不可になりますが、移行先を選択しなくていいですか。',

];
