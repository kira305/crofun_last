@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('credit/index'))
@section('styles')
<link href="{{ asset('css/credit_search.css') }}" rel="stylesheet">
@stop
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form id="form" action="{{ url('credit/index') }}" method="POST">
                                    {{-- row 1 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control " id="company_id" name="company_id">
                                                        @foreach($companies as $company)
                                                        <option
                                                            {{ session('company_id_crdit') == $company->id ? 'selected' : '' }}
                                                            value="{{$company->id}}">{{$company->abbreviate_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">顧客コード</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input type="text" id="client_code" value="{{$errors->isEmpty() ?  session('client_code_crdit') : $requestOld['client_code']}}" class="form-control" autocomplete="off" name="client_code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">法人番号</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input type="text" id="corporation_num"
                                                    value="{{$errors->isEmpty() ? session('corporation_num_crdit') : $requestOld['corporation_num']}}" class="form-control"
                                                    autocomplete="off" name="corporation_num">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">顧客名カナ</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input type="text" id="client_name"
                                                    value="{{$errors->isEmpty() ? session('client_name_crdit') : $requestOld['client_name']}}" class="form-control"
                                                    autocomplete="off" name="client_name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">有効期間</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input id="start_time" value="{{$errors->isEmpty() ? session('start_time_crdit') : $requestOld['start_time']}}"
                                                autocomplete="off" name="start_time" type="text" class="form-control">
                                                </div>
                                                <div class="search-title col-xs-1">
                                                    <span class="">~</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" id="end_time" value="{{$errors->isEmpty() ? session('end_time_crdit') : $requestOld['end_time']}}"
                                                class="form-control" autocomplete="off" name="end_time">
                                                </div>
                                                <span class="text-danger dis-block">{{ $errors->has('start_time') ? $errors->first('start_time') : ''}}</span>
                                                <span class="text-danger dis-block" >{{ $errors->has('end_time') ? $errors->first('end_time') : ''}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    @csrf
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="submit" class="search-button btn btn-primary btn-sm">検索</button>
                                        </div>
                                        <div class="col-xs-3">
                                            <button type="button" id="clear" class="clear-button btn btn-default btn-sm">クリア</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-1">
                                    @if(Crofun::credit_index_return_button() == 1)
                                    <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if(Crofun::credit_index_return_button() == 2 && request()->client_id != null)
                                    <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-offset-9">
                                    @paginate(['item'=> $customers]) @endpaginate
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="credit_search_table" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>与信情報</th>
                                            <th>顧客</th>
                                            <th>プロジェクト</th>
                                            <th>顧客コード</th>
                                            <th>法人番号</th>
                                            <th>顧客名</th>
                                            <th>希望与信限度額</th>
                                            <th>最新売掛金残</th>
                                            <th>限度額-売掛金残</th>
                                            <th>与信期限</th>
                                            <th>取得日</th>
                                            <th>格付け情報</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $canViewCreditAdd = Auth::user()->can('credit-add');
                                            $canViewCusEdit = Auth::user()->can('customer-edit');
                                            $canViewCusView = Auth::user()->can('customer-view');
                                            $canViewClientProject = Auth::user()->can('project-index');
                                        @endphp
                                        @foreach ($customers as $customer)
                                        <tr>
                                            {{-- 与信情報 --}}
                                            <td>
                                                @if($canViewCreditAdd)
                                                <button onclick="credit({{$customer->company_id}},{{$customer->id}})"
                                                    class="btn btn-primary btn-sm">更新</button>
                                                @endif
                                            </td>
                                            {{-- 顧客 --}}
                                            <td>
                                                @if( $canViewCusEdit)
                                                <button onclick="client({{$customer->id}})"
                                                    class="btn btn-primary btn-sm">詳細</button>
                                                @elseif($canViewCusView)
                                                <button onclick="client_view({{$customer->id}})"
                                                    class="btn btn-primary btn-sm">詳細</button>
                                                @endif
                                            </td>
                                            {{-- プロジェクト --}}
                                            <td>
                                                @if( $canViewClientProject)
                                                <button onclick="project({{$customer->id}})"
                                                    class="btn btn-primary btn-sm">詳細</button>
                                                @endif
                                            </td>
                                            {{-- 顧客コード --}}
                                            <td>
                                                @if($customer->client_code_main == null)
                                                {{ $customer->client_code }}
                                                @else
                                                {{ $customer->client_code_main }}
                                                @endif
                                            </td>
                                            {{-- 法人番号	 --}}
                                            <td>{{$customer->corporation_num}}</td>
                                            {{-- 顧客名 --}}
                                            <td>{{$customer->client_name}}</td>
                                            {{-- 希望与信限度額	 --}}
                                            <td style="text-align: right">
                                                @if($customer->credit_expect)
                                                {{number_format($customer->credit_expect / 1000)}}
                                                @endif
                                            </td>
                                            @php
                                                $newnestReceivable = $customer->newnestReceivable();
                                            @endphp
                                            {{--  最新売掛金残	--}}
                                            <td style="text-align: right">
                                                @if($newnestReceivable != null)
                                                    {{number_format((int)$newnestReceivable->receivable / 1000)}}
                                                @endif
                                            </td>
                                            {{-- 限度額-売掛金残--}}
                                            @if(($customer->credit_expect != null) && ($newnestReceivable != null))
                                                @if(((int)$customer->credit_expect -(int)$customer->newnestReceivable()->receivable) < 0 )
                                                    <td style="background-color: #FFB6C1; text-align: right">
                                                @else
                                                    <td style="text-align: right">
                                                @endif
                                                        {{ number_format( ((int)$customer->credit_expect - (int)$customer->newnestReceivable()->receivable ) / 1000 ) }}
                                                    </td>
                                            @else
                                                <td >
                                                    {{-- empty --}}
                                                </td>
                                            @endif
                                            {{-- 与信期限 --}}
                                            <td>
                                                @if($customer->ex_date != null)
                                                    {{date('Y/m/d',strtotime($customer->ex_date)) }}
                                                @endif
                                            </td>
                                            {{-- 取得日	 --}}
                                            <td>
                                                @if($customer->get_time)
                                                    {{date('Y/m/d',strtotime($customer->get_time)) }}
                                                @endif
                                            </td>
                                            {{-- 格付け情報--}}
                                            <td>
                                                @if($customer->rank)
                                                    {{$customer->rank}}
                                                @endif
                                            </td>
                                        </tr>
                                        <td colspan="12">
                                            <table class="table table-config table-hover table-striped" rules="all">
                                                @if($customer->checkReceivableExist() != false)
                                                <tr>
                                                    @php
                                                        $ReceivableAttribute = $customer->getReceivableAttribute();
                                                    @endphp
                                                    @foreach ($ReceivableAttribute as $titleItem)
                                                        <td class="infor1 text-center bg-info">
                                                            @if($titleItem->target_data)
                                                            {{date('Y/m',strtotime($titleItem->target_data)) }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach ($ReceivableAttribute as $valItem)
                                                        <td class="infor2 text-right" style="text-align: right">
                                                            @if($valItem->receivable != null && $valItem->receivable != 0)
                                                            {{ number_format($valItem->receivable/1000)}}
                                                            @elseif($valItem->receivable == 0)
                                                            {{ $valItem->receivable }}
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @endif
                                            </table>
                                        </td>
                                        @endforeach
                                    </tbody>
                                </table>
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
        $("#start_time").datepicker({
            dateFormat: 'yy/mm/dd'
        });
        $("#end_time").datepicker({
            dateFormat: 'yy/mm/dd'
        });
    });
    $(document).on('click', '#clear', function() {
        $('#company_id').prop('selectedIndex', 0);
        $('#corporation_num').val('');
        $('#client_name').val('');
        $('#client_code').val('');
        $('#start_time').val('');
        $('#end_time').val('');
        $("#form").submit();
    });

    function client(id) {
        var base = '{!! route("customer_edit") !!}';
        var url = base + '?id=' + id;
        window.location.href = url;
    }

    function client_view(id) {
        var base = '{!! route("customer_view") !!}';
        var url = base + '?id=' + id;
        window.location.href = url;
    }

    function credit(company_id, client_id) {
        var base = '{!! route("create_credit") !!}';
        var url = base + '?company_id=' + company_id + '&client_id=' + client_id;
        window.location.href = url;
    }

    function project(client_id) {
        var base = '{!! route("project_index") !!}';
        var url = base + '?client_id=' + client_id;
        window.location.href = url;
    }
</script>
@endsection
