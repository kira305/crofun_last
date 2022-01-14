<?php
Breadcrumbs::for('home', function ($trail) {
    $trail->push('ホーム', url('home'));
});

Breadcrumbs::for('customer/infor', function ($trail) {
    $trail->parent('home');
    $trail->push('顧客情報', url('customer/infor'));
});

Breadcrumbs::for('customer/create', function ($trail) {

    $trail->parent('customer/infor');
    $trail->push('新規登録', url('customer/create'));
});

Breadcrumbs::for('customer/edit', function ($trail) {

    $trail->parent('customer/infor');
    $trail->push('編集', url('customer/edit'));
});

Breadcrumbs::for('customer/view', function ($trail) {

    $trail->parent('customer/infor');
    $trail->push('参照', url('customer/view'));
});
// company

Breadcrumbs::for('company/index', function ($trail) {
    $trail->parent('home');
    $trail->push('会社マスタ', url('company/index'));
});


Breadcrumbs::for('company/create', function ($trail) {

    $trail->parent('company/index');
    $trail->push('新規登録', url('company/create'));
});

Breadcrumbs::for('company/edit', function ($trail) {

    $trail->parent('company/index');
    $trail->push('編集', url('company/edit'));
});

// headquarter

Breadcrumbs::for('headquarter/index', function ($trail) {
    $trail->parent('home');
    $trail->push('事業本部マスタ', url('headquarter/index'));
});


Breadcrumbs::for('headquarter/create', function ($trail) {

    $trail->parent('headquarter/index');
    $trail->push('新規登録', url('headquarter/create'));
});

Breadcrumbs::for('headquarter/edit', function ($trail) {

    $trail->parent('headquarter/index');
    $trail->push('編集', url('headquarter/edit'));
});

//department

Breadcrumbs::for('department/index', function ($trail) {
    $trail->parent('home');
    $trail->push('部署マスタ', url('department/index'));
});



Breadcrumbs::for('department/create', function ($trail) {

    $trail->parent('department/index');
    $trail->push('新規登録', url('department/create'));
});

Breadcrumbs::for('department/edit', function ($trail) {

    $trail->parent('department/index');
    $trail->push('編集', url('department/edit'));
});

// group

Breadcrumbs::for('group/index', function ($trail) {
    $trail->parent('home');
    $trail->push('グループマスタ', url('group/index'));
});


Breadcrumbs::for('group/create', function ($trail) {

    $trail->parent('group/index');
    $trail->push('新規登録', url('group/create'));
});

Breadcrumbs::for('group/edit', function ($trail) {

    $trail->parent('group/index');
    $trail->push('編集', url('group/edit'));
});

// user

Breadcrumbs::for('user/index', function ($trail) {
    $trail->parent('home');
    $trail->push('ユーザーマスタ', url('user/index'));
});



Breadcrumbs::for('user/create', function ($trail) {

    $trail->parent('user/index');
    $trail->push('新規登録', url('user/create'));
});

Breadcrumbs::for('user/edit', function ($trail,$user_id) {

    $trail->parent('user/index');
    $trail->push('編集', url('user/edit/'. $user_id));
});

// rule

Breadcrumbs::for('rule/index', function ($trail) {
    $trail->parent('home');
    $trail->push('画面機能のルール', url('rule/index'));
});



Breadcrumbs::for('rule/create', function ($trail) {

    $trail->parent('rule/index');
    $trail->push('新規登録', url('rule/create'));
});

Breadcrumbs::for('rule/edit', function ($trail) {

    $trail->parent('rule/index');
    $trail->push('編集', url('rule/edit'));
});

// position

Breadcrumbs::for('position/index', function ($trail) {
    $trail->parent('home');
    $trail->push('役職マスタ', url('position/index'));
});

Breadcrumbs::for('position/create', function ($trail) {

    $trail->parent('position/index');
    $trail->push('新規登録', url('position/create'));
});

Breadcrumbs::for('position/edit', function ($trail) {

    $trail->parent('position/index');
    $trail->push('編集', url('position/edit'));
});

// cost

Breadcrumbs::for('cost/index', function ($trail) {
    $trail->parent('home');
    $trail->push('ツリー図マスタ', url('cost/index'));
});

Breadcrumbs::for('cost/create', function ($trail) {

    $trail->parent('cost/index');
    $trail->push('新規登録', url('cost/create'));
});

Breadcrumbs::for('cost/edit', function ($trail) {

    $trail->parent('cost/index');
    $trail->push('編集', url('cost/edit'));

});

//  password
Breadcrumbs::for('password/change', function ($trail) {
    $trail->parent('home');
    $trail->push('パスワード更新', url('password/change'));
});

// concurrent


Breadcrumbs::for('user/concurrent/create', function ($trail,$user_id) {

    $trail->parent('user/edit',$user_id);
    $trail->push('兼務新規登録', url('user/concurrent/create'));
});

Breadcrumbs::for('user/concurrent/edit', function ($trail,$user_id) {

    $trail->parent('user/edit',$user_id);
    $trail->push('兼務編集', url('user/concurrent/edit'));

});

// system

Breadcrumbs::for('system/index', function ($trail) {
    $trail->parent('home');
    $trail->push('システム情報管理', url('system/index'));
});

// project

Breadcrumbs::for('project/index', function ($trail) {
    $trail->parent('home');
    $trail->push('プロジェクト情報', url('project/index'));
});

Breadcrumbs::for('project/create', function ($trail) {

    $trail->parent('project/index');
    $trail->push('新規登録', url('project/create'));
});

Breadcrumbs::for('project/edit', function ($trail) {

    $trail->parent('project/index');
    $trail->push('編集', url('project/edit'));
});

Breadcrumbs::for('project/view', function ($trail) {

    $trail->parent('project/index');
    $trail->push('参照', url('project/view'));
});
// import log

Breadcrumbs::for('importlog/index', function ($trail) {
    $trail->parent('home');
    $trail->push('PCAデータLOG', url('importlog/index'));
});

Breadcrumbs::for('pca/uplode', function ($trail) {
    $trail->parent('home');
    $trail->push('PCAデータUP', url('pca/uplode'));
});

Breadcrumbs::for('process/index', function ($trail) {
    $trail->parent('home');
    $trail->push('売上情報', url('process/index'));
});

Breadcrumbs::for('receivable/index', function ($trail) {
    $trail->parent('home');
    $trail->push('売掛金残', url('receivable/index'));
});

//contract
Breadcrumbs::for('contract/index', function ($trail) {
    $trail->parent('home');
    $trail->push('契約書情報', url('contract/index'));
});
Breadcrumbs::for('contract/create', function ($trail) {
    $trail->parent('contract/index');
    $trail->push('契約書新規登録', url('contract/create'));
});
Breadcrumbs::for('contract/edit', function ($trail) {
    $trail->parent('contract/index');
    $trail->push('契約書編集', url('contract/edit'));
});

Breadcrumbs::for('contract/createfile', function ($trail) {
    $trail->parent('contract/index');
    $trail->push('契約書ファイルUP', url('contract/createfile'));
});

//contractype
Breadcrumbs::for('contracttype/index', function ($trail) {
    $trail->parent('home');
    $trail->push('契約書種類情報', url('contracttype/index'));
});

Breadcrumbs::for('contracttype/create', function ($trail) {
    $trail->parent('contracttype/index');
    $trail->push('新規登録', url('contracttype/create'));
});

Breadcrumbs::for('contracttype/edit', function ($trail) {
    $trail->parent('contracttype/index');
    $trail->push('編集', url('contracttype/edit'));
});

//credit
Breadcrumbs::for('credit/create', function ($trail) {
    $trail->parent('credit/index');
    $trail->push('与信情報登録', url('contract/index'));
});


Breadcrumbs::for('credit/log', function ($trail) {
    $trail->parent('home');
    $trail->push('与信情報取得履歴', url('credit/log'));
});

Breadcrumbs::for('credit/edit', function ($trail) {
    $trail->parent('credit/log');
    $trail->push('与信取得参照', url('credit/log'));
});
Breadcrumbs::for('loxg/index', function ($trail) {
    $trail->parent('home');
    $trail->push('システム操作ログ', url('loxg/index'));
});

Breadcrumbs::for('log/view', function ($trail) {
    $trail->parent('loxg/index');
    $trail->push('操作詳細', url('log/view'));
});

Breadcrumbs::for('custom_name/index', function ($trail) {
    $trail->parent('home');
    $trail->push('検索対象顧客名', url('custom_name/index'));
});


Breadcrumbs::for('global_info/index', function ($trail) {
    $trail->parent('home');
    $trail->push('お知らせ情報', url('global_info/index'));
});

Breadcrumbs::for('global_info/edit', function ($trail) {
    $trail->parent('global_info/index');
    $trail->push('お知らせ情報編集', url('global_info/edit'));
});

Breadcrumbs::for('global_info/create', function ($trail) {
    $trail->parent('global_info/index');
    $trail->push('新規登録', url('global_info/create'));
});

Breadcrumbs::for('credit/index', function ($trail) {
    $trail->parent('home');
    $trail->push('与信一覧', url('credit/index'));
});

//ツリー図
Breadcrumbs::for('tree/index', function ($trail) {
    $trail->parent('home');
    $trail->push('ツリー図作成', url('tree/index'));
});

Breadcrumbs::for('mail_mst/indexm', function ($trail) {
    $trail->parent('home');
    $trail->push('メール文面管理', url('mail_mst/indexm'));
});

Breadcrumbs::for('mail_mst/editm', function ($trail) {
    $trail->parent('mail_mst/indexm');
    $trail->push('編集', url('mail_mst/edit'));
});

Breadcrumbs::for('mail_mst/createm', function ($trail) {
    $trail->parent('mail_mst/indexm');
    $trail->push('新規登録', url('mail_mst/create'));
});


?>
