@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('credit/log'))
<script type="text/javascript" src="{{ asset('js/MonthPicker.js') }}"></script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                @php
                                    $customerCanUpdate = Auth::user()->can('update','App\Customer_MST');
                                    $customerCanView =  Auth::user()->can('view','App\Customer_MST')
                                @endphp
                                <form action="{{ url('credit/log') }}" id="form" method="post">
                                    {{-- row 1 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach($companies as $company)
                                                        <option
                                                            @if(session()->has('company_id_cr_log'))
                                                                @if(session('company_id_cr_log') == $company->id) selected @endif
                                                            @else
                                                                @if(Auth::user()->company_id == $company->id) selected @endif
                                                            @endif
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
                                                    <input type="text" value="{{$errors->isEmpty() ? session('client_code_cr_log') : $client_code}}"
                                                    name="client_code" id="client_code" class="form-control">
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
                                                    <input type="text" value="{{$errors->isEmpty() ? session('corporation_num_cr_log') : $corporation_num}}"
                                                    name="corporation_num" id="corporation_num" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">顧客名カナ</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input id="client_name_kana"
                                                    value="{{$errors->isEmpty() ? session('client_name_kana_cr_log') : $client_name_kana}}"
                                                    name="client_name_kana" type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">取得日</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('get_data_st_cr_log') : $get_data_st}}"
                                                    name="get_data_st" id="datepicker" class="form-control">
                                                </div>
                                                <div class="search-title col-xs-1">
                                                    <span class="">~</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('get_data_en_cr_log') : $get_data_en}}"
                                                    name="get_data_en" id="datepicker2" class="form-control">
                                                </div>
                                                <span class="text-danger dis-block">{{ $errors->has('get_data_st') ? $errors->first('get_data_st') : ''}}</span>
                                                <span class="text-danger dis-block">{{ $errors->has('get_data_en') ? $errors->first('get_data_en') : ''}}</span>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">有効期限</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('expiration_data_st_cr_log') : $expiration_data_st}}"
                                                    name="expiration_data_st" id="datepicker3" class="form-control">
                                                </div>
                                                <div class="search-title col-xs-1">
                                                    <span class="">~</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('expiration_data_en_cr_log') : $expiration_data_en}}"
                                                    name="expiration_data_en" id="datepicker4" class="form-control">
                                                </div>
                                                <span class="text-danger dis-block">{{ $errors->has('expiration_data_st') ? $errors->first('expiration_data_st') : ''}}</span>
                                                <span class="text-danger dis-block">{{ $errors->has('expiration_data_en') ? $errors->first('expiration_data_en') : ''}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    @csrf
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="submit" id="search" class="search-button btn btn-primary btn-sm">検索</button>
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
                                    @if(Crofun::credit_log_return_button() == 1)
                                        <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                            <button class="btn btn-warning btn-sm"> 戻る</button>
                                        </a>
                                    @endif
                                    @if(Crofun::credit_log_return_button() == 2)
                                        <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                            <button class="btn btn-warning btn-sm"> 戻る</button>
                                        </a>
                                    @endif
                                    @if(Crofun::credit_log_return_button() == 3)
                                        @if($customerCanUpdate && isset(request()->client_id))
                                            <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                                <button class="btn btn-warning btn-sm"> 戻る</button>
                                            </a>
                                        @elseif($customerCanView && isset(request()->client_id))
                                            <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                                <button class="btn btn-warning btn-sm"> 戻る</button>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                <div class="col-lg-offset-9">
                                    @paginate(['item'=> $credit]) @endpaginate
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="credit_log_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>顧客コード</th>
                                            <th>顧客名</th>
                                            <th>取得日</th>
                                            <th>格付け情報</th>
                                            <th>希望与信限度額</th>
                                            <th>RM与信限度額</th>
                                            <th>有効期限</th>
                                            <th>取得元</th>
                                            <th>参照</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $canCreditEdit = Auth::user()->can('credit-edit');
                                        @endphp
                                        @foreach ($credit as $credit)
                                        <tr>
                                            <td>
                                                @if($credit->customer->client_code_main == null)
                                                    {{ $credit->customer->client_code }}
                                                @else
                                                    {{ $credit->customer->client_code_main }}
                                                @endif
                                            </td>
                                            <td>{{  $credit->customer->client_name }}</td>
                                            <td>{{  date('Y/m/d',strtotime($credit->get_time)) }}</td>
                                            <td>{{  $credit->rank }}</td>
                                            <td style="text-align: right">
                                                @if($credit->credit_expect)
                                                    {{  number_format($credit->credit_expect /1000) }}
                                                @endif
                                            </td>
                                            <td style="text-align: right">
                                                @if($credit->credit_limit)
                                                    {{  number_format($credit->credit_limit /1000) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($credit->expiration_date != null)
                                                    {{  date('Y/m/d',strtotime($credit->expiration_date)) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($credit->credit_division == '1'){{ 'RM' }}
                                                @elseif($credit->credit_division == '2'){{ 'TSR' }}
                                                @elseif($credit->credit_division == '3'){{ 'TDB' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($canCreditEdit)
                                                    @if($customerCanUpdate && request()->client_id != null)
                                                        <a href="{{route('edit_credit', ['id' => $credit->id, 'client_id' =>  request()->client_id])}}">
                                                            <button style="float: left;" class="btn btn-info btn-sm">参照</button>
                                                        </a>
                                                    @elseif($customerCanView && request()->client_id != null)
                                                        <a href="{{route('edit_credit', ['id' => $credit->id, 'client_id' =>  request()->client_id])}}">
                                                            <button style="float: left;" class="btn btn-info btn-sm">参照</button>
                                                        </a>
                                                    @else
                                                        <a href="{{route('edit_credit', ['id' => $credit->id,'page'=>request()->page])}}">
                                                            <button style="float: left;" class="btn btn-info btn-sm">参照</button>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
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
    $(document).on('click', '#clear', function () {
		//1ページに移動
        $('#company_id').prop('selectedIndex',0);
        $('#client_code').val('');
        $('#corporation_num').val('');
        $('#client_name').val('');
        $('#client_name_kana').val('');
        $('#datepicker').val('');
        $('#datepicker2').val('');
        $('#datepicker3').val('');
        $('#datepicker4').val('');
        $( "#form" ).submit();
	});

	$('#datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
	});

	$('#datepicker2').datepicker({
        autoclose: true,
        todayHighlight: true,
	});
	$('#datepicker3').datepicker({
        autoclose: true,
        todayHighlight: true,
	});

	$('#datepicker4').datepicker({
        autoclose: true,
        todayHighlight: true,
	});
</script>
@endsection
