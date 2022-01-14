<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




//authenticate
Route::get('/', 'Auth\LoginController@login')->name('login');
Route::get('/login', 'Auth\LoginController@login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('user/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('user/reset-password', 'Auth\ResetPasswordController@reset_password');
Route::post('user/reset-password', 'Auth\ResetPasswordController@reset_password');
//password
Route::get('/auth/changepassword', 'HomeController@showChangePasswordForm');
Route::post('/auth/changepassword', 'HomeController@showChangePasswordForm')->name('changepassword');
Route::get('/auth/remove-password', 'removepassword@index');
Route::post('/auth/remove-password', 'removepassword@index')->name('removepassword');
Route::get('/import_data_user', 'TestController@import_user');
Route::get('/import_data_cost', 'TestController@import_cost');
Route::get('/import_data_customer', 'TestController@import_customer');
Route::get('/import_data_customer_name', 'TestController@import_customer_name');
Route::get('/import_project', 'TestController@import_project');
Route::get('/import_group', 'TestController@import_group');
Route::get('/import_department', 'TestController@import_department');
Route::get('/import_headquarter', 'TestController@import_headquarter');
Route::get('/change_code_customer', 'TestController@change_code_customer');
Route::get('/change_project', 'TestController@change_project');
Route::get('/graph_auth', 'MsGraphAuthController@signin');
Route::get('/callback', 'MsGraphAuthController@callback');
// Route::get('user/logout', array('before' => 'auth', function()
// {
//     Route::get('user/logout', 'Auth\LoginController@logout')->name('logout');

// }));

//ユーザ
Route::middleware(['auth'])->group(function () {

    Route::get('/home','HomeController@index')->name('home');
    Route::get('/home/get-receivable-view','HomeController@getReceivableView')->name('receivableView');

    Route::get('user/index', 'UserController@index')->middleware('can:user-index');
    Route::post('user/index', 'UserController@index');
    Route::get('user/create', 'UserController@create')->middleware('can:create,App\User');
    Route::post('user/create', 'UserController@create')->middleware('can:create,App\User');
    Route::get('user/edit/{id}', 'UserController@edit')->name('edituserinfor')->middleware('can:update,App\User');
    Route::post('user/edit', 'UserController@edit')->middleware('can:update,App\User');
    Route::post('user/ajax', 'UserController@getListConcurrently');

	// パスワード
    Route::get('password/change', 'UserController@changePass')->middleware('can:change-password');
    Route::post('password/change', 'UserController@changePass');

	// 兼務
    Route::get('user/concurrent/create', 'UserController@concurrent_create')->name('concurrentcreate');
    Route::post('user/concurrent/create', 'UserController@concurrent_create')->name('concurrentcreate');
    Route::get('user/concurrent/edit', 'UserController@concurrent_edit')->name('concurrentedit');
    Route::post('user/concurrent/edit', 'UserController@concurrent_edit');
    Route::get('user/concurrent/delete', 'UserController@concurrent_delete')->name('concurrentdelete');
    Route::get('user/concurrent/reset', 'UserController@concurrent_reset');

	// 会社
    Route::get('company/index', 'CompanyController@index')->middleware('can:company-index');
    Route::get('company/create', 'CompanyController@create')->middleware('can:create,App\Company_MST');
    Route::post('company/create', 'CompanyController@create')->middleware('can:create,App\Company_MST');
    Route::get('company/edit/{id}', 'CompanyController@edit')->name('editcompany')->middleware('can:update,App\Company_MST');
    Route::post('company/edit', 'CompanyController@edit')->middleware('can:update,App\Company_MST');

	// 事業部
    Route::get('headquarter/index', 'HeadquarterController@index')->middleware('can:headquarter-index');
    Route::post('headquarter/index', 'HeadquarterController@index');
    Route::get('headquarter/edit/{id}', 'HeadquarterController@edit')->name('editheadquarter')
    ->middleware('can:update,App\Headquarters_MST');
    Route::post('headquarter/edit', 'HeadquarterController@edit')->middleware('can:update,App\Headquarters_MST');
    Route::get('headquarter/create', 'HeadquarterController@create')->middleware('can:create,App\Headquarters_MST');
    Route::post('headquarter/create', 'HeadquarterController@create')->middleware('can:create,App\Headquarters_MST');
    Route::post('headquarter/ajax', 'HeadquarterController@getListHeadquarterAjax');

	// 部署
    Route::get('department/index', 'DepartmentController@index')->middleware('can:department-index');
    Route::post('department/index', 'DepartmentController@index');
    Route::get('department/create', 'DepartmentController@create')->middleware('can:create,App\Department_MST');
    Route::post('department/create', 'DepartmentController@create')->middleware('can:create,App\Department_MST');
    Route::get('department/edit', 'DepartmentController@edit')->name('editdepartment')
    ->middleware('can:update,App\Department_MST');
    Route::post('department/edit', 'DepartmentController@edit')->middleware('can:update,App\Department_MST');
    Route::post('department/ajax', 'DepartmentController@getListDepartment');

	// グルプ
    Route::get('group/index', 'GroupController@index')->middleware('can:group-index');
    Route::post('group/index', 'GroupController@index');
    Route::get('group/create', 'GroupController@create')->middleware('can:create,App\Group_MST');
    Route::post('group/create', 'GroupController@create')->middleware('can:create,App\Group_MST');
    Route::get('group/edit', 'GroupController@edit')->name('editgroup')->middleware('can:update,App\Group_MST');
    Route::post('group/edit', 'GroupController@edit')->middleware('can:update,App\Group_MST');
    Route::post('group/ajax', 'GroupController@getListGroupAjax');

	// 顧客情報
    Route::get('/customer/infor', 'CustomerController@index')->middleware('can:customer-index');
    Route::post('/customer/infor', 'CustomerController@index');
    Route::get('/customer/create/{id?}', 'CustomerController@create')->name('customer_create')
    ->middleware('can:create,App\Customer_MST');
    Route::post('/customer/create', 'CustomerController@create')->middleware('can:create,App\Customer_MST');
    Route::post('/customer/upload', 'CustomerController@upload')->middleware('can:create,App\Customer_MST');
    Route::get('/customer/edit', 'CustomerController@edit')->name('customer_edit')
    ->middleware('can:update,App\Customer_MST');
    Route::get('/customer/view', 'CustomerController@view')->name('customer_view')
    ->middleware('can:view,App\Customer_MST');
    Route::post('/customer/edit', 'CustomerController@edit')->middleware('can:update,App\Customer_MST');
    Route::post('/customer/search', 'CustomerController@search');
    Route::get('/customer/csv1', 'CustomerController@getCsv1');
    Route::get('/customer/csv2', 'CustomerController@getCsv2');

    Route::post('/customer/getcode', 'CustomerController@getCustomerCode');
    Route::post('/customer/changecode', 'CustomerController@changeCode');
    Route::post('/customer/checkproject', 'CustomerController@checkProjectNotEnd');

    //ツリー図
    Route::get('/tree/index', 'TreeDiagramController@index')->middleware('can:tree-index');
    Route::post('/tree/index', 'TreeDiagramController@index');
    Route::post('/tree/get_genka', 'TreeDiagramController@getListGenka');
    Route::post('/tree/get_hanka', 'TreeDiagramController@getListHanka');
    Route::post('/tree/get_project', 'TreeDiagramController@getListProject');


    //csv作成
    Route::get('/tree/diagram', 'TreeDiagramController@diagram');
    Route::get('/tree/diagram2', 'TreeDiagramController@diagram2');


	// 権限
    Route::get('/rule/index', 'RuleController@index');
    Route::get('/rule/create', 'RuleController@create')->middleware('can:create,App\Rule_MST');
    Route::post('/rule/create', 'RuleController@create')->middleware('can:create,App\Rule_MST');
    Route::get('/rule/edit', 'RuleController@edit')->name('edit_rule')->middleware('can:update,App\Rule_MST');
    Route::post('/rule/edit', 'RuleController@edit')->middleware('can:update,App\Rule_MST');
    Route::post('/rule/ajax', 'RuleController@getListRuleAjax');


    // 役職
    Route::get('/position/index', 'PositionController@index')->middleware('can:position-index');
    Route::get('/position/edit', 'PositionController@edit')->name('edit_position')
    ->middleware('can:update,App\Position_MST');
    Route::post('/position/edit', 'PositionController@edit')->middleware('can:update,App\Position_MST');
    Route::get('/position/create', 'PositionController@create')->middleware('can:create,App\Position_MST');
    Route::post('/position/create', 'PositionController@create')->middleware('can:create,App\Position_MST');
    Route::post('/position/ajax', 'PositionController@getListPositionAjax');

    // クリア
    Route::post('/clear/group', 'GroupController@clear');
    Route::post('/clear/department', 'DepartmentController@clear');
    Route::post('/clear/headquarter', 'HeadquarterController@clear');

    //cost
    Route::get('/cost/index', 'CostController@index')->middleware('can:cost-index');
    Route::post('/cost/index', 'CostController@index');
    Route::get('/cost/create', 'CostController@create')->middleware('can:create,App\Cost_MST');
    Route::post('/cost/create', 'CostController@create')->middleware('can:create,App\Cost_MST');;
    Route::get('/cost/edit', 'CostController@edit')->name('edit_cost')->middleware('can:update,App\Cost_MST');
    Route::post('/cost/edit', 'CostController@edit')->middleware('can:update,App\Cost_MST');

    // 売掛金残検索
    Route::get('/receivable/index', 'ReceivableController@index')->name('Receivable_index')->middleware('can:view,App\Receivable_MST');
    Route::post('/receivable/index', 'ReceivableController@index')->middleware('can:view,App\Receivable_MST');
    Route::post('/pca/upload', 'PcaUploadController@upload');
    Route::get('/pca/upload', 'PcaUploadController@index');
    Route::get('/err/dowload', 'PcaUploadController@getCsv');

    // 売上検索
    Route::get('/process/index', 'ProcessController@index')->name('Process_index')->middleware('can:view1,App\Process_MST');
    Route::post('/process/index', 'ProcessController@index')->middleware('can:view1,App\Process_MST');

    // 契約書情報検索
    Route::get('/contract/index', 'ContractController@index')->name('contract.index')->middleware('can:view,App\Contract_MST');
    Route::post('/contract/index', 'ContractController@index')->name('contract.index');
    Route::match(['get', 'post'], 'contract/create','ContractController@create')->name('contract.create')->middleware('can:create,App\Contract_MST');
    Route::match(['get', 'post'], 'contract/edit','ContractController@edit')->name('contract.edit')->middleware('can:update,App\Contract_MST');
    Route::match(['get', 'post'], 'contract/view','ContractController@view')->name('contract.view')->middleware('can:display,App\Contract_MST');
    Route::match(['get', 'post'], 'contract/createfile','ContractController@createfile')->name('contract.createfile');

    Route::get('/contract/viewfile', 'ContractController@viewfile')->name('contract.viewfile');
    Route::get('/contract/display', 'ContractController@display')->name('contract_display');
    Route::get('/contract/getcsv', 'ContractController@getcsv')->name('contract.getcsv');;

    //契約書種類
    Route::match(['get', 'post'], 'contracttype/index','ContractTypeController@index')->name('contract_type.index');
    Route::match(['get', 'post'], 'contracttype/create','ContractTypeController@create')->name('contract_type.create');
    Route::match(['get', 'post'], 'contracttype/edit','ContractTypeController@edit')->name('contract_type.edit');

    //system
    Route::get('/system/confirmation', 'SystemController@confirmation');
    Route::get('/system/index', 'SystemController@index')->middleware('can:system-infor');
    Route::post('/system/confirmation', 'SystemController@confirmation');
    Route::post('/system/index', 'SystemController@index');

    //project

    Route::get('/project/index', 'ProjectController@index')->name('project_index');
    Route::post('/project/index', 'ProjectController@index');
    Route::get('/project/create', 'ProjectController@create')->name('create_project')
    ->middleware('can:create,App\Project_MST');
    Route::post('/project/create', 'ProjectController@create')->middleware('can:create,App\Project_MST')
    ->middleware('checkZenkaku');
    Route::get('/project/edit', 'ProjectController@edit')->name('edit_project')->middleware('can:project-edit');
    Route::get('/project/view', 'ProjectController@view')->name('view_project')->middleware('can:project-view');
    Route::get('/project/csv1', 'ProjectController@getCsv1');
    Route::get('/project/csv2', 'ProjectController@getCsv2');
    Route::post('/project/edit', 'ProjectController@edit');
    Route::post('/project/getcode', 'ProjectController@getMaxCode');
    Route::post('/project/checkcustomer', 'CustomerController@checkCustomerIsEnd');

    // import log search

    Route::get('/importlog/index', 'ImportSearchController@index')->middleware('can:pca-log-index');
    Route::get('/importlog/csv', 'ImportSearchController@getCsv');
    Route::get('/importlog/delete', 'ImportSearchController@delete')->middleware('jwt.verify');
    Route::post('/importlog/index', 'ImportSearchController@index');


    // contract
    Route::post('/contract/upload', 'ContractUploadController@upload');

    // 与信履歴検索
    Route::get('/credit/log', 'CreditController@log')->name('Credit_log')->middleware('can:log,App\Credit_MST');
    Route::post('/credit/log', 'CreditController@log')->middleware('can:log,App\Credit_MST');

    Route::get('/credit/create', 'CreditController@create')->name('create_credit')->middleware('can:credit-add');
    Route::post('/credit/create', 'CreditController@create');
    Route::post('/credit/upload', 'CreditController@upload');
    Route::get('/credit/edit', 'CreditController@edit')->name('edit_credit')->middleware('can:credit-edit');

    // credit search

    Route::get('/credit/index', 'CreditSearchController@index')->name('Credit_index');
    Route::post('/credit/index', 'CreditSearchController@index');


    // 操作LOG
    Route::get('/loxg/index', 'LogController@index')->name('LOG_INDEX')->middleware('can:index,App\Log_MST');
    Route::post('/loxg/index', 'LogController@index')->middleware('can:index,App\Log_MST');
    Route::get('/log/view', 'LogController@view')->name('LOG_VIEW')->middleware('can:view,App\Log_MST');
    Route::post('/log/view', 'LogController@view')->middleware('can:view,App\Log_MST');

    //検索顧客名
    Route::get('/custom_name/index', 'CustomnameController@index')->middleware('can:customer-name');
    Route::post('/custom_name/index', 'CustomnameController@index');
    Route::get('/custom_name/delete', 'CustomnameController@delete');



    //お知らせ
    Route::get ('global_info/index' , 'global_infoController@index')->middleware('can:system-new');
    Route::post('global_info/index' , 'global_infoController@index');
    Route::get ('global_info/edit'  , 'global_infoController@edit')->name('edit_global_info');
    Route::post('global_info/edit'  , 'global_infoController@edit');
    Route::get ('global_info/create', 'global_infoController@create');
    Route::post('global_info/create', 'global_infoController@create');
    Route::get ('global_info/delete', 'global_infoController@delete')->name('global_info.delete');
    Route::post('global_info/delete', 'global_infoController@delete');
    Route::get ('global_info/download/{id?}/{ol_name?}/{sv_name?}', 'global_infoController@download')->name('global_info.download');
    Route::post('global_info/download/{id?}/{ol_name?}/{sv_name?}', 'global_infoController@download')->name('global_info.download');

    //メール管理
    Route::get ('mail_mst/indexm' , 'mail_mstController@index')->middleware('can:mail-text');
    Route::post('mail_mst/indexm' , 'mail_mstController@index');
    Route::get ('mail_mst/editm'  , 'mail_mstController@editm')->name('edit_mail_mst');
    Route::post('mail_mst/editm'  , 'mail_mstController@editm');
    Route::get ('mail_mst/createm'  , 'mail_mstController@create')->name('create_mail_mst');
    Route::post('mail_mst/createm'  , 'mail_mstController@create');

    //api
    Route::post('headquarter/check'  , 'ApiController@headquarterCheck');
    Route::post('department/check'  , 'ApiController@departmentCheck');
    Route::post('group/check'  , 'ApiController@groupCheck');

});


