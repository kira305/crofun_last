@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('customer/view'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body p-20">
                        @php
                            $contractEditId = '';
                            $contractViewId = '';
                            if(isset(request()->contract_edit_id))
                                $contractEditId = "&contract_edit_id=".request()->contract_edit_id;
                            elseif(isset(request()->contract_view_id)){
                                $contractViewId = "&contract_view_id=".request()->contract_view_id;
                            }
                        @endphp
                        {{-- row 1 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>会社コード</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->company->abbreviate_name }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>新規登録申請本部</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->com_grp()->headquarters }}
                                        </div>
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
                                        <div class="form-control" readonly>
                                            {{ $customer->com_grp()->department_name }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>新規登録申請グループ</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->com_grp()->group_name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 3 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>顧客名</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->client_name }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>顧客名カナ</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->client_name_kana }}
                                        </div>
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
                                        <div class="form-control" readonly>
                                            {{ $customer->client_name_ab }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>代表者氏名</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->representative_name }}
                                        </div>
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
                                        <div class="form-control" readonly>
                                            {{ $customer->client_address }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>ステータス</label>
                                        <div class="form-control" readonly>
                                            @if ($customer->status == 3)
                                                取引中
                                            @elseif ($customer->status == 4)
                                                仮登録中
                                            @elseif ($customer->status == 2)
                                                本登録中止
                                            @elseif ($customer->status == 1)
                                                取引終了
                                            @endif
                                        </div>
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
                                        <div class="form-control" readonly>
                                            {{ $customer->corporation_num }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>顧客コード</label>
                                        <div class="form-control" readonly>
                                            @if ($customer->client_code_main != null)
                                                {{ $customer->client_code_main }}
                                            @else
                                                {{ $customer->client_code }}
                                            @endif
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
                                        <div class="col-lg-4 form-group">
                                            <label class="checkbox-title">反社チェック済み</label>
                                            <div class="icheck-primary d-inline ">
                                                <input type="checkbox" disabled @if ($customer->antisocial == true)
                                                checked @endif
                                                id="antisocial" name="antisocial">
                                                <label for="antisocial"></label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="checkbox-title">信用調査有無</label>
                                            <div class="icheck-primary d-inline ">
                                                <input type="checkbox" disabled @if ($customer->credit == true)
                                                checked @endif
                                                id="credit" name="credit" class="input_checkbox">
                                                <label for="credit"></label>
                                            </div>
                                            <input type="hidden" name="id" id="hidden_id" value="{{ $customer->id }}">
                                            <input type="hidden" id="company_id" value="{{ $customer->company_id }}" name="company_id">
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label class="checkbox-title">FGLグループ会社</label>
                                            <div class="icheck-primary d-inline ">
                                                <input type="checkbox" disabled @if ($customer->fgl_flag == true) checked @endif id="fgl_flag" name="fgl_flag" class="input_checkbox">
                                                <label for="fgl_flag"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>取引区分</label>
                                        <div class="form-control" readonly>
                                            @if ($customer->sale == 1)
                                                売上先
                                            @elseif ($customer->sale == 2)
                                                仕入先
                                            @elseif ($customer->sale == 3)
                                                売上先+仕入先
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 8 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>回収サイト</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->collection_site }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>振込人名称</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->transferee_name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 9 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label class="checkbox-title">振込人名称相違</label>
                                        <div class="icheck-primary d-inline ">
                                            <input type="checkbox" disabled @if ($customer->transferee == true)
                                            checked @endif id="transferee" name="transferee">
                                            <label for="transferee"></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>決算月日</label>
                                        <div class="form-control" readonly>
                                            {{ $customer->closing_time }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 10 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>格付け情報</label>
                                        <div class="form-control" readonly>
                                            @if ($customer->credit_check_by_get_time())
                                                {{ $customer->credit_check_by_get_time()->rank }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>RM与信限度額<sup class="color-red">1</sup></label>
                                        <div class="form-control" readonly>
                                            @if ($customer->credit_check())
                                                {{ number_format($customer->credit_check()->credit_limit / 1000) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 11 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>希望限度額<sup class="color-red">1</sup></label>
                                        <div class="form-control" readonly>
                                            @if ($customer->credit_check())
                                                {{ number_format($customer->credit_check()->credit_expect / 1000) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>与信情報取得日</label>
                                        <div class="form-control" readonly>
                                            @if ($customer->credit_check_by_get_time())
                                                {{ Crofun::changeFormatDateOfCredit($customer->credit_check_by_get_time()->get_time) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 12 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>与信限度期間</label>
                                        <div class="form-control" readonly>
                                            @if ($customer->credit_check())
                                                {{ Crofun::changeFormatDateOfCredit($customer->credit_check()->expiration_date) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- row 13 --}}
                        <div class="row">
                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                <div class="row search-form">
                                    <div class="col-lg-6 form-group">
                                        <label>データ登録日</label>
                                        <div class="form-control" readonly>
                                            {{ Crofun::changeFormatDateOfCredit($customer->created_at) }}
                                        </div>
                                    </div>
                                    <div class="col-lg-6 form-group">
                                        <label>取引想定合計金額<sup class="color-red">1</sup></label>
                                        <div class="form-control" readonly>
                                            {{ number_format($transaction / 1000) }}
                                        </div>
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
                                        <textarea class="form-control" rows="3" name="note" readonly>{{$customer->note}}</textarea>
                                        <p style="margin-top: 12px"><b class="color-red">1) </b><b
                                            class="color-header">1000円が省略された金額で表示されています。</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                </div>
                            </div>
                        </div>
                        {{-- row update --}}
                        <div class="row p-b-20">
                            <div class="col-xs-3 display-middle">
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
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    function project_create_url() {
        var base='{!! route("create_project") !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?company_id='+company_id+'&customer_id='+customer_id+'&pre='+2;

        window.location.href=url;
    }

    function create_credit() {
        var base='{!! route("create_credit") !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?company_id='+company_id+'&client_id='+customer_id;

        window.location.href=url;
    }

    function project_index_url() {
        var base='{!! route('project_index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }


    function credit_index_url() {
        var base='{!! route('Credit_index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function contract_index_url() {
        var base='{!! route('contract.index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function receivable_index_url() {
        var base='{!! route('Receivable_index') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    function credit_log_url() {
        var base='{!! route('Credit_log') !!}';
        var company_id=$("#company_id").val();
        var customer_id=$("#hidden_id").val();
        var url=base+'?&client_id='+customer_id;

        window.location.href=url;
    }

    $("#csv").click(function(event) {
            var customer_id=$("#hidden_id").val();
            document.location.href="/customer/csv2?client_id="+customer_id;
        }
    );

</script>
@endsection
