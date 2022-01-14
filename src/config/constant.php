<?php

return [
    //テーブル名称
    'COMPANY'      => 1,
    'HEADQUATER'   => 2,
    'DEPARTMENT'   => 3,
    'GROUP'        => 4,
    'USER'         => 5,
    'CONCURRENTLY' => 6,
    'COST'         => 7,
    'POSION'       => 8,
    'RULE_RULE'    => 9,
    'Mail'         => 10,
    'global_info'  => 11,
    'RULE'         => 12,
    'system'       => 13,
    'CLIENT'       => 100,
    'CLIENTNAME'   => 101,
    'PROJECT'      => 102,
    'CONTRACT'     => 104,
    'PCA'          => 105,
    'PCAUP'        => 'PCAデータLOG',
    'PCALOG'       => 'PCAデータUP',
    'PROCESS'      => 106,
    'CREDIT'       => 200,
    'RSCEIVABLE'   => 103,
    'PASSWORDUPD'  => 'パスワード更新',
    'TREE'         => 'ツリー図',
    'LOG'          => '操作ログ',
    'HOME'         => 'ホーム',
    'INDEX'        => '検索',
    'EDIT'         => '詳細',
    'CREATE'       => '登録',
    'CREDITLOG'    => '与信情報一覧',
    'NOTICE'       => 'お知らせ',
    'CONTRACT_TYPE'=> 201,
    'CONTRACT_FILE'=> 202,



//画面ID
    //マスタの管理
    'COMPANY_INDEX'     => 201,
    'COMPANY_ADD'       => 202,
    'COMPANY_EDIT'      => 203,
    'HEADQUATER_INDEX'  => 204,
    'HEADQUATER_ADD'    => 205,
    'HEADQUATER_EDIT'   => 206,
    'DEPARTMENT_INDEX'  => 207,
    'DEPARTMENT_ADD'    => 208,
    'DEPARTMENT_EDIT'   => 209,
    'GROUP_INDEX'       => 210,
    'GROUP_ADD'         => 211,
    'GROUP_EDIT'        => 212,
    'USER_INDEX'        => 213,
    'USER_ADD'          => 214,
    'USER_EDIT'         => 215,
    'CONCURRENTLY_ADD'  => 216,
    'CONCURRENTLY_EDIT' => 217,
    'COST_INDEX'        => 218,
    'COST_ADD'          => 219,
    'COST_EDIT'         => 220,
    'POSITION_INDEX'    => 221,
    'POSITION_ADD'      => 222,
    'POSITION_EDIT'     => 223,
    'RULE_INDEX'        => 224,
    'RULE_ADD'          => 225,
    'RULE_EDIT'         => 226,
    'MAIL_TEXT'         => 227,
    'SYSTEM_INFOR'      => 229,
    'SYSTEM_NEW'        => 230,
    'CONTRACT_TYPE_CREATE' => 233,
    'CONTRACT_TYPE_EDIT' => 234,
    //顧客情報の管理
    'CLIENT_INDEX'      => 1,
    'CLIENT_ADD'        => 2,
    'CLIENT_EDIT'       => 3,
    'CLIENT_VIEW'       => 4,
    'PROJECT_INDEX'     => 5,
    'PROJECT_ADD'       => 6,
    'PROJECT_EDIT'      => 7,
    'PROJECT_VIEW'      => 8,
    'CONTRACT_VIEWFILE' => 9,
    'PCA_UPLOAD'        => 10,
    'PCA_LOG'           => 11,
    'CONTRACT_INDEX'    => 13,
    // 'CONTRACT_DISPLAY'  => 15,
    'CLIENT_NAME'       => 16,
    'CONTRACT_CREATE'   => 18,
    'CONTRACT_EDIT'     => 19,
    'CONTRACT_VIEW'     => 20,

    //与信情報の管理
    'CREDIT_INDEX'      => 101,
    'CREDIT_ADD'        => 102,
    'CREDIT_EDIT'       => 103,
    'RSCEIVABLE_INDEX'  => 104,
    'CREDIT_LOG'        => 105,
    'CREDIT_LOG_VIEW'   => 106,
    'PROCESS_INDEX'     => 107,

    //ツリー図の表示機能
    'TREE_INDEX'        => 301,
    'IMPORT_LOG_INDEX'  => 302,
    'IMPORT_LOG_CSV'    => 303,
    'IMPORT_LOG_DELETE' => 304,


    //システムマスタ
    'PASSWORD'          => 901,
    'LOG_INDEX'         => 228,
    'LOG_VIEW'          => 231,
//END　画面ID

    //操作区分
    'operation_UPDATE'   => '更新',
    'operation_CRATE'    => '作成',
    'operation_LOGIN'    => 'login',
    'operation_LOGOUT'   => 'logout',
    'operation_DELETE'   => '削除',
    'operation_REFERENCE' => '参照',
    'operation_FILEUP'   => 'ファイルのUP',
    'operation_Bulk_up'  => '一括更新',
    'operation_OFF'      => '一括非表示',


    //テーブル名
    'COMPANY_MST'      => 'company',

    'CREDIT_EXPECT'    => 1000,
    'CREDIT_LIMIT'     => 1000,
    'RANK_MIN'         => 0,
    'RANK_MAX'         => 100,

    'WITHOUT_RULE' => [216,217]
];
