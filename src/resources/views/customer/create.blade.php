@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('customer/create'))
@include('layouts.confirm_js')
<script type="text/javascript">
    $( window ).on( "load", function() {
        @if($message = Session::get('message'))
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
                    <div class="timeline">
                        <div>
                            <div class="box-body p-20">
                                <form id="create_customer" method="post" action="{{ url('customer/create') }}">
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>会社コード</label>
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach ($companies as $company)
                                                            <option
                                                                {{ old('company_id') == $company->id ? 'selected' : '' }}
                                                                value="{{ $company->id }}">
                                                                {{ $company->abbreviate_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>新規登録申請本部</label>
                                                    <select class="form-control" id="headquarter_id"
                                                        name="headquarter_id">
                                                        <option></option>
                                                        @foreach ($headquarters as $headquarter)
                                                            <option class="headquarter_id" @if (old('headquarter_id') == $headquarter->id)
                                                                selected
                                                        @endif
                                                        data-value="{{ $headquarter->company_id }}"
                                                        value="{{ $headquarter->id }}">{{ $headquarter->headquarters }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>新規登録申請部署</label>
                                                    <select class="form-control" id="department_id"
                                                        name="department_id">
                                                        <option> </option>
                                                        @foreach ($departments as $department)
                                                            <option class="department_id" @if (old('department_id') == $department->id)
                                                                selected
                                                        @endif
                                                        data-value="{{ $department->headquarter()->id }}"
                                                        value="{{ $department->id }}">{{ $department->department_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>新規登録申請グループ</label>
                                                    <select class="form-control" id="group_id" name="group_id">
                                                        <option> </option>
                                                        @foreach ($groups as $group)
                                                            <option class="group_id" @if (old('group_id') == $group->id) selected
                                                        @endif
                                                        data-value="{{ $group->department()->id }}"
                                                        value="{{ $group->id }}">{{ $group->group_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>顧客名<sup class="color-red">※</sup></label>
                                                    <input type="text" id="client_name" name="client_name"
                                                        value="{{ old('client_name') }}" class="form-control">
                                                    <input type="hidden" name="id" value="">
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name') ? $errors->first('client_name') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>顧客名カナ</label>
                                                    <input id="name_kana" type="text"
                                                        value="{{ old('client_name_kana_conversion') }}"
                                                        name="client_name_kana" class="form-control">
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
                                                    <input type="text" value="{{ old('client_name_ab') }}"
                                                        name="client_name_ab" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name_ab') ? $errors->first('client_name_ab') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>代表者氏名</label>
                                                    <input type="text" id="representative_name" value="{{ old('representative_name') }}"
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
                                                    <label>住所<sup class="color-red">※</sup></label>
                                                    <input type="text" value="{{ old('client_address') }}"
                                                        name="client_address" id="client_address" value=""
                                                        class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>ステータス</label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="4">仮登録中
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
                                                    <label>法人番号<sup class="color-red">※</sup></label>
                                                    <input type="text" name="corporation_num" id="corporation_num"
                                                        value="{{ old('corporation_num') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('corporation_num') ? $errors->first('corporation_num') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客コード</label>
                                                    <input type="text" id="client_code" value="{{ old('client_code') }}"
                                                        name="client_code" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 7 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>TSRコード<sup class="color-red">※</sup></label>
                                                    <input type="text" id="tsr_code" name="tsr_code"
                                                        value="{{ old('tsr_code') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('tsr_code') ? $errors->first('tsr_code') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>商蔵コード</label>
                                                    <input type="text" value="{{ old('akikura_code') }}"
                                                        style="float: left;" name="akikura_code" class="form-control">
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
                                                            <input type="checkbox" @if (old('antisocial') == 'on') checked @endif id="antisocial" name="antisocial"
                                                            class="input_checkbox">
                                                            <label for="antisocial"></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">信用調査有無</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if (old('credit') == 'on') checked @endif id="credit" name="credit"
                                                            class="input_checkbox">
                                                            <label for="credit"></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">FGLグループ会社</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if (old('fgl_flag') == 'on') checked @endif id="fgl_flag" name="fgl_flag" class="input_checkbox">
                                                            <label for="fgl_flag"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>取引区分</label>
                                                    <select id="type" class="form-control" name="sale">
                                                        <option selected value=""></option>
                                                        <option @if (old('sale') == '1')
                                                            selected @endif
                                                            value="1">売上先</option>
                                                        <option @if (old('sale') == '2')
                                                            selected @endif
                                                            value="2">仕入先</option>
                                                        <option @if (old('sale') == '3')
                                                            selected @endif
                                                            value="3">仕入先+売上先</option>
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
                                                    <input value="{{ old('collection_site') }}" type="text"
                                                        name="collection_site" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>振込人名称</label>
                                                    <input type="text" value="{{ old('transferee_name') }}"
                                                        name="transferee_name" class="form-control">
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
                                                        <input type="checkbox" @if (old('transferee') == 'on') checked @endif id="transferee" name="transferee" class="input_checkbox">
                                                        <label for="transferee"></label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>決算月日</label>
                                                    <input type="text" name="closing_month" id="closing_month"
                                                        value="{{ old('closing_month') }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 11 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>格付け情報<sup class="color-red">※</sup></label>
                                                    <input id="rank_h" name="rank" value="{{ old('rank') }}"
                                                        class="form-control" readonly>
                                                    <input hidden id="check_credit" name="check_credit"
                                                        value="{{ old('check_credit') }}">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>RM与信限度額<sup class="color-red">※,1</sup></label>
                                                    <input id="credit_limit" name="credit_limit"
                                                        value="{{ old('credit_limit') }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 12 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>希望限度額<sup class="color-red">※,1</sup></label>
                                                    <input id="credit_expect" name="credit_expect"
                                                        value="{{ old('credit_expect') }}" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>与信情報取得日<sup class="color-red">※</sup></label>
                                                    <input id="get_time_h" name="get_time" value="{{ old('get_time') }}"
                                                        class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 13 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>与信限度期限<sup class="color-red">※</sup></label>
                                                    <input id="expiration_date" name="expiration_date"
                                                        value="{{ old('expiration_date') }}" class="form-control"
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
                                                    <label>備考</label>
                                                    <textarea class="form-control" rows="3" name="note">{{ old('note') }}</textarea>
                                                    <p style="margin-top: 12px"><b class="color-red">1) </b><b class="color-header">1000円が省略された金額で表示されています。</b></p>
                                                    <p style="margin-top: 12px"><b class="color-red">※) </b><b class="color-header">RM情報を取り込むと、データ未入力であれば、RM情報からデータを設定します。</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @csrf
                                    {{-- end --}}
                                </form>
                                <div class="row">
                                    <div class="col-lg-2 col-lg-offset-5">
                                        <form id="upload" action="{{ url('customer/upload') }}" method="post"
                                            enctype="multipart/form-data">
                                            <span class="btn btn-primary btn-file">
                                                RM情報取込
                                                <input id="input_file" type="file" name="file_data">
                                            </span>
                                        </form>
                                    </div>
                                </div>
                                {{-- row 13 --}}
                                <div class="row">
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        <div class="row search-form ">
                                            <div class="col-lg-8 col-lg-offset-2 form-group attention-box">
                                                <p class="attention-title" ><i class="glyphicon glyphicon-top glyphicon-exclamation-sign"></i>ご注意</p>
                                                <a target="_blank" rel="noopener noreferrer"
                                                    href="{{Crofun::getLinkForCustomerCreate()}}">
                                                    法人インフォ
                                                </a>
                                                は官庁法人DBへのリンクになります。
                                                <p>法人番号正式顧客名・住所などがわからなければ参照してください。</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- end --}}
                            </div>
                            <div class="row p-b-20">
                                <div class="col-lg-12 ">
                                    <div class="col-xs-3 col-xs-offset-3">
                                        <button type="button" id="form_submit" class="search-button btn btn-primary btn-sm">登録</button>
                                    </div>
                                    <div class="col-xs-3">
                                        @php($page = session()->has('customer.page') ? '?page='.session('customer.page') : '')
                                        <a href="{{ url('customer/infor'.$page) }}">
                                            <button type="button" id="clear" class="clear-button btn btn-danger btn-sm">戻る</button>
                                        </a>
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
    //画面が開いた際
    $(document).ready(function() {

        $("#client_code").prop("readonly", true);
        //クリックされたら
        $("#form_submit").click(function(event) {
            if (($("#credit_expect").val() == "") && ($("#input_file").val() != "")) {
                $.alert({
                    title: 'メッセージ',
                    content: '希望限度額を入力してください！',
                });
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/customer/getcode',
                data: {
                    "company_id": $('#company_id').val()
                },
                success: function(data) {
                    $('#client_code').val(data.num);
                    $("#create_customer").submit();
                },
                error: function(exception) {
                    alert(exception.responseText);
                }
            });
        });

        $("#input_file").change(function() {
            if ($("#credit_expect").val() == "") {
                $.alert({
                    title: 'メッセージ',
                    content: '希望限度額を入力してください！',
                });
                $('#input_file').val('');
            } else {
                $("#upload").submit();
            }
        });

        $('#upload').submit(function(event) { // when from submit send data to server

            var csv = $('#input_file')[0].files[0];
            var form = new FormData();
            form.append('csv', csv);
            form.append('credit_expect', $("#credit_expect").val());
            $.ajax({
                url: '/customer/upload',
                data: form,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(response) {
                    if (response.status == 302) {
                        $.alert({
                            title: 'メッセージ',
                            content: response.errors.csv,
                        });
                        return;
                    }

                    if (response.status == 300) {
                        $.alert({
                            title: 'メッセージ',
                            content: response.errors.credit_expect,
                        });
                        return;
                    }
                    console.log(response);
                    set_data(response);
                    $('#check_credit').val(1);
                },
                error: function(exception) {
                    alert(exception.responseText);
                    if (exception.status == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{trans('message.save_fail')}}',
                            text: 'ファイルのルールはただしくありません。',
                        })
                    }
                }
            });
            event.preventDefault();
        });
    });

    function set_data(response) {
        if ($('#client_name').val().length == 0) {
            $('#client_name').val(response.csv.client_name);
        }
        if ($('#tsr_code').val().length == 0) {
            $('#tsr_code').val(response.csv.tsr_code);
        }

        if ($('#client_address').val().length == 0) {
            $('#client_address').val(response.csv.client_address);
        }
        if ($('#corporation_num').val().length == 0) {
            $('#corporation_num').val(response.csv.corporation_num);
        }
        $('#get_time_h').val(moment(response.csv.get_time).format('YYYY/MM/DD'));
        $('#rank_h').val(response.csv.rank);
        $('#credit_limit').val(response.csv.credit_limit);
        $('#expiration_date').val(moment(response.expiration_date).format('YYYY/MM/DD'));
    }

    // 「,」区切りで出力
    function number_format(num) {
        return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g, '$1,');
    }

</script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
