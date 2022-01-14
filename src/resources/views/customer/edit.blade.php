@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('customer/edit'))
@include('layouts.confirm_js')
<script type="text/javascript">
    $( window ).on( "load", function() {
        @if(isset($message))
        sweetAlert('{{$message}}','success');
        @endif

        @if(!$errors->isEmpty())
            sweetAlert('{{trans('message.save_fail')}}', 'error');
        @endif
    });
</script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body p-20">
                                @php
                                    $contractEditId = '';
                                    $contractViewId = '';
                                    if(isset(request()->contract_edit_id))
                                        $contractEditId = "&contract_edit_id=".request()->contract_edit_id;
                                    elseif(isset(request()->contract_view_id)){
                                        $contractViewId = "&contract_view_id=".request()->contract_view_id;
                                    }
                                @endphp
                                <form id="edit_customer" method="post" action="{{ url('customer/edit?id='.$customer->id.$contractEditId.$contractViewId) }}">
                                    <input type="hidden" value="{{ $customer->updated_at }}" name="update_time">
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <span class="text-danger">
                                                        {{ $errors->has('update_time') ? $errors->first('update_time') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>会社コード</label>
                                                    <input class="form-control" readonly
                                                        value="{{ $customer->company->abbreviate_name }}">
                                                    <input type="hidden" id="company_id"
                                                        value="{{ $customer->company_id }}" name="company_id">
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>新規登録申請本部</label>
                                                    <input class="form-control" readonly
                                                        value="{{ $customer->com_grp()->headquarters }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>新規登録申請部署</label>
                                                    <input class="form-control" readonly
                                                        value="{{$customer->com_grp()->department_name}}">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>新規登録申請グループ</label>
                                                    <input class="form-control select_level_1" readonly
                                                        value="{{$customer->com_grp()->group_name}}">
                                                    <input type="hidden" name="group_id"
                                                        value="{{$customer->com_grp()->id}}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>顧客名</label>
                                                    <input type="text" id="client_name" name="client_name"
                                                        value="{{$customer->client_name}}" class="form-control">
                                                    <input type="hidden" name="id" value="" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name') ? $errors->first('client_name') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>
                                                        <span class="important-text">&nbsp;必須&nbsp;</span>顧客名カナ
                                                    </label>
                                                    <input id="name_kana" type="text" name="client_name_kana"
                                                        value="{{$customer->client_name_kana}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name_kana_conversion') ? $errors->first('client_name_kana_conversion') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>略称</label>
                                                    <input type="text" value="{{$customer->client_name_ab}}"
                                                        name="client_name_ab" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name_ab') ? $errors->first('client_name_ab') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>代表者氏名</label>
                                                    <input type="text" id="representative_name" value="{{ $customer->representative_name }}"
                                                        name="representative_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 5 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>住所</label>
                                                    <input type="text" value="{{$customer->client_address}}"
                                                        name="client_address" id="client_address" value=""
                                                        class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>ステータス</label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option id="status_1" @if ($customer->status == 1) selected
                                                            @endif value="1">
                                                            取引終了
                                                        </option>
                                                        <option id="status_2" @if ($customer->status == 2) selected
                                                            @endif
                                                            value="2">本登録中止
                                                        </option>
                                                        <option id="status_3" @if ($customer->status == 3) selected
                                                            @endif
                                                            value="3">取引中
                                                        </option>
                                                        <option id="status_4" @if ($customer->status == 4) selected
                                                            @endif
                                                            value="4">仮登録中
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 6 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>法人番号</label>
                                                    <input type="text" name="corporation_num" id="corporation_num"
                                                        value="{{$customer->corporation_num}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('corporation_num') ? $errors->first('corporation_num') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客コード</label>
                                                    <div class="id-change">
                                                        @if($customer->client_code_main == null)
                                                        <input value="{{$customer->client_code}}" name="client_code"
                                                            id="client_code" class="form-control">
                                                        @else
                                                        <input value="{{$customer->client_code_main}}"
                                                            name="client_code_main" id="client_code"
                                                            class="form-control">
                                                        @endif
                                                        <button type="button" id="get_number" style="display: none"
                                                            class="btn btn-primary">本登録
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 7 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>TSRコード</label>
                                                    <input type="text" id="tsr_code" name="tsr_code"
                                                        value="{{$customer->tsr_code}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('tsr_code') ? $errors->first('tsr_code') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>商蔵コード</label>
                                                    <input type="text" value="{{$customer->akikura_code}}"
                                                        name="akikura_code" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('akikura_code') ? $errors->first('akikura_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 8 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">反社チェック済み</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if ($customer->antisocial == true)
                                                            checked @endif
                                                            id="antisocial" name="antisocial">
                                                            <label for="antisocial"></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">信用調査有無</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if ($customer->credit == true)
                                                            checked @endif
                                                            id="credit" name="credit" class="input_checkbox">
                                                            <label for="credit"></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">FGLグループ会社</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if ($customer->fgl_flag == true) checked @endif id="fgl_flag" name="fgl_flag" class="input_checkbox">
                                                            <label for="fgl_flag"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span
                                                            class="important-text">&nbsp;必須&nbsp;</span>取引区分</label>
                                                    <select id="type" class="form-control" name="sale">
                                                        <option selected value=""></option>
                                                        <option @if ($customer->sale == 1) selected @endif
                                                            value="1">売上先</option>
                                                        <option @if ($customer->sale == 2) selected @endif
                                                            value="2">仕入先</option>
                                                        <option @if ($customer->sale == 3) selected @endif
                                                            value="3">売上先+仕入先
                                                        </option>
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('sale') ? $errors->first('sale') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 9 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>回収サイト</label>
                                                    <input style="float: left;" value="{{$customer->collection_site}}"
                                                        type="text" name="collection_site" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>決算月日</label>
                                                    <input type="text" name="closing_month" id="closing_month"
                                                        value="{{$customer->closing_time}}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 10 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label class="checkbox-title">振込人名称相違</label>
                                                    <div class="icheck-primary d-inline ">
                                                        <input type="checkbox" @if ($customer->transferee == true)
                                                        checked @endif
                                                        id="transferee" name="transferee">
                                                        <label for="transferee"></label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>振込人名称</label>
                                                    <input type="text" value="{{$customer->transferee_name}}"
                                                        name="transferee_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 11 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>格付け情報</label>
                                                    <input id="rank_h" name="rank"
                                                        @if($customer->credit_check_by_get_time())
                                                    value="{{$customer->credit_check_by_get_time()->rank}}"
                                                    @endif
                                                    class="form-control"
                                                    readonly >
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>RM与信限度額<sup class="color-red">1</sup></label>
                                                    <input id="credit_limit" readonly @if($customer->credit_check())
                                                    value="{{number_format($customer->credit_check()->credit_limit / 1000)}}"
                                                    @endif
                                                    class="form-control" readonly >
                                                    <input type="hidden" name="credit_limit"
                                                        @if($customer->credit_check())
                                                    value="{{$customer->credit_check()->credit_limit}}"
                                                    @endif
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 12 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>希望限度額<sup class="color-red">1</sup></label>
                                                    <input id="credit_expect" readonly @if($customer->credit_check())
                                                    value="{{number_format($customer->credit_check()->credit_expect / 1000)}}"
                                                    @endif
                                                    class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>与信情報取得日</label>
                                                    <input id="get_time_h" name="get_time"
                                                        @if($customer->credit_check_by_get_time())
                                                    value="{{ Crofun::changeFormatDateOfCredit($customer->credit_check_by_get_time()->get_time) }}"
                                                    @endif
                                                    class="form-control"
                                                    readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 13 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>与信限度期限</label>
                                                    <input id="expiration_date" name="expiration_date"
                                                        @if($customer->credit_check())
                                                    value="{{Crofun::changeFormatDateOfCredit($customer->credit_check()->expiration_date) }}"
                                                    @endif
                                                    class="form-control"
                                                    readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 14 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>データ登録日</label>
                                                    <input type="text" readonly
                                                        value="{{ Crofun::changeFormatDateOfCredit($customer->created_at) }}"
                                                        class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>取引想定合計金額<sup class="color-red">1</sup></label>
                                                    <input type="text" readonly
                                                        value="{{number_format($transaction/1000)}}" name="transaction"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 15 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>備考</label>
                                                    <textarea class="form-control" rows="3"
                                                        name="note">{{$customer->note}}</textarea>
                                                    <p style="margin-top: 12px"><b class="color-red">1) </b><b
                                                            class="color-header">1000円が省略された金額で表示されています。</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" id="client_id" value="{{$customer->id}}">
                                    <input id="client_code_main" type="hidden" name="client_code_main" value="">
                                    @csrf
                                    {{-- end --}}
                                </form>
                                {{-- row btn --}}
                                <div class="row">
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        <div class="row search-form">
                                            <div class="col-lg-12 form-group">
                                                <div class="list-btn">
                                                    {{-- btn 1 --}}
                                                    @if( Auth::user()->can('contract-index'))
                                                    <a><button onclick="contract_index_url()" type="button"
                                                            class="btn btn-warning">契約書情報</button></a>
                                                    @endif
                                                    {{-- btn 2 --}}
                                                    @if( Auth::user()->can('project-index'))
                                                    <a><button onclick="project_index_url()" type="button"
                                                            class="btn btn-danger">プロジェクト情報</button></a>
                                                    @endif
                                                    {{-- btn 3 --}}
                                                    @if( Auth::user()->can('credit-log'))
                                                    <a><button onclick="credit_log_url()" type="button"
                                                            class="btn btn-warning">与信情報取得履歴</button></a>
                                                    @endif
                                                    {{-- btn 4 --}}
                                                    @if( Auth::user()->can('credit-index'))
                                                    <a><button onclick="credit_index_url()" type="button"
                                                            class="btn btn-danger">与信一覧</button></a>
                                                    @endif
                                                    {{-- btn 5 --}}
                                                    @if( Auth::user()->can('receivable-index'))
                                                    <a><button onclick="receivable_index_url()" type="button"
                                                            class="btn btn-warning">売掛金残</button></a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-lg-12 form-group">
                                                <div class="list-btn">
                                                    {{-- btn 1 --}}
                                                    @if( Auth::user()->can('contract-create'))
                                                        <a href="{{route('contract.create', ['client_id' => $customer->id])}}" class="btn btn-primary">契約書登録</a>
                                                    @endif
                                                    {{-- btn 2 --}}
                                                    @if ($customer->status == 3)
                                                        @if( Auth::user()->can('project-add'))
                                                        <a>
                                                            <button onclick="project_create_url()" type="button"
                                                                class="btn btn-primary">プロジェクト登録</button>
                                                        </a>
                                                        @endif
                                                    @endif
                                                    {{-- btn 3 --}}
                                                    @if( Auth::user()->can('credit-add'))
                                                    <a><button onclick="create_credit()" type="button"
                                                            class="btn btn-primary">与信情報登録</button></a>
                                                    @endif
                                                    {{-- btn 4 --}}
                                                    <a><button id="csv" type="button"
                                                            class="btn btn-success">CSV出力</button></a>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <span hidden id="upload_success" class="text-success">{{ trans('message.contract_upload_success') }}</span>
                                                <span hidden id="upload_fail" class="text-danger">{{ trans('message.contract_upload_fail') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row update --}}
                                <div class="row p-b-20">
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="button" id="form_submit" class="btn search-button btn-primary">更新</button>
                                        </div>
                                        <div class="col-xs-3" >
                                            @php($page = session()->has('customer.page') ? '?page='.session('customer.page') : '')
                                            @if(!empty($contractEditId))
                                                <a href="{{ url('contract/edit?id='.request()->contract_edit_id) }}">
                                                    <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                                </a>
                                            @elseif(!empty($contractViewId))
                                                <a href="{{ url('contract/view?id='.request()->contract_view_id) }}">
                                                    <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                                </a>
                                            @else
                                                @if(Crofun::customer_edit_return_button() == 0)
                                                    <a href="{{ url('customer/infor'.$page) }}">
                                                        <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                                    </a>
                                                @else
                                                    <a href="{{route('Credit_index')}}">
                                                        <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {

            $("#client_code").prop("readonly", true);
            $("#get_number").click(function (event) {

                    var client_code=$("#client_code").val();
                    console.log(client_code);

                    $("#status_1").remove();
                    $("#status_2").remove();
                    $("#status_3").remove();
                    $("#status_4").remove();
                    $("#status").append("<option value='3' selected id='status_3'>取引中</option>");
                    $("#status").append("<option value='1'  id='status_1'>取引終了</option>");
                    var company_id=$("#company_id").val();
                    // $.ajax( {
                    //         type: 'POST',
                    //         url: '/customer/changecode',
                    //         data: {
                    //             "company_id": company_id
                    //         },

                    //         success: function (data) {
                    //             $("#client_code").val('更新ボタンを押下採番');
                    //             $("#client_code_main").val('更新ボタンを押下採番');
                    //         },

                    //         error: function (exception) {
                    //             alert(exception.responseText);
                    //         }
                    //     }
                    // );

                    $("#client_code").val('更新ボタンを押下採番');
                    $("#client_code_main").val('更新ボタンを押下採番');

                    if($("#status").val()=='2') {
                        $("#status_1").remove();
                        $("#status_2").remove();
                        $("#status_3").remove();
                        $("#status_4").remove();
                        $("#status").append("<option value='3' selected id='status_3'>取引中</option>");
                        $("#status").append("<option value='1'  id='status_1'>取引終了</option>");
                        return;
                    }
                }
            );

            $("#csv").click(function(event) {
                    client_id=$("#client_id").val();
                    document.location.href="/customer/csv2?client_id="+client_id;
                }
            );

            $("#form_submit").click(function(event) {
                    $("#edit_customer").submit();
                }
            );


            if($("#status").val()=='1') {
                $("#status_2").remove();
                $("#status_4").remove();
            }
            if($("#status").val()=='2') {
                $("#status_1").remove();
                $("#status_3").remove();
                $("#status_4").remove();
                $("#get_number").show();
            }

            if($("#status").val()=='3') {
                $("#status_2").remove();
                $("#status_4").remove();
            }

            if($("#status").val()=='4') {
                $("#status_1").remove();
                $("#status_3").remove();
                $("#get_number").show();
            }


            $("#input_file").change(function() {

                    var pdf=$('#input_file')[0].files[0];
                    var form=new FormData();
                    form.append('pdf', pdf);
                    form.append('type', 1);
                    form.append('client_id', $("#client_id").val());
                    form.append('company_id', $("#company_id").val());

                    $.ajax( {

                            url: '/contract/upload',
                            data: form,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success:function(response) {

                                if(response.status_code==200) {
                                    $("#upload_success").show();
                                    $("#upload_fail").hide();
                                }

                                if (response.status_code==500) {
                                    $("#upload_fail").show();
                                    $("#upload_success").hide();
                                }
                            }
                            ,
                            error: function (exception) {
                                alert(exception.responseText);
                            }
                        }
                    );
                }
            );

            $("#status").change(function() {

                    if($("#status").val() !=1) {
                        return
                    }
                    var form=new FormData();
                    form.append('customer_id', $("#client_id").val());
                    $.ajax( {

                            url: '/customer/checkproject',
                            data: form,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success:function(response) {
                                if(response.status==1) {
                                    $.alert( {
                                            title: 'メッセージ',
                                            content: response.message,
                                        }
                                    );
                                    $('#status').prop('selectedIndex', 0);
                                }
                            }

                            ,
                            error: function (exception) {
                                alert(exception.responseText);
                            }
                        }
                    );
                }
            );
        }
    );

    function project_create_url() {

        var base='{!! route("create_project") !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?company_id='+company_id+'&customer_id='+customer_id+'&pre='+1;

        window.location.href=url;
    }

    function create_credit() {

        var base='{!! route("create_credit") !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?company_id='+company_id+'&client_id='+customer_id;

        window.location.href=url;
    }

    function project_index_url() {

        var base='{!! route('project_index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?client_id='+customer_id;

        window.location.href=url;
    }

    function credit_log_url() {

        var base='{!! route('Credit_log') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function credit_index_url() {

        var base='{!! route('Credit_index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function contract_index_url() {

        var base='{!! route('contract.index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function contract_index_url() {

        var base='{!! route('contract.index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function receivable_index_url() {
        var base='{!! route('Receivable_index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#client_id").val();
        var url=base+'?&client_id='+customer_id;
        window.location.href=url;
    }

</script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>

@endsection
