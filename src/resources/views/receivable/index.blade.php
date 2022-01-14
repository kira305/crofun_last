@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('receivable/index'))
<script type="text/javascript" src="{{ asset('js/MonthPicker.js') }}"></script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form action="{{ url('receivable/index') }}" id="form" method="post">
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
                                                        <option @if(session()->has('company_id_r'))
                                                            @if(session('company_id_r') == $company->id) selected @endif
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
                                                    <input type="text" value="{{$errors->isEmpty() ? session('client_code_r') : $requestOld['client_code']}}"
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
                                                    <input type="text" value="{{$errors->isEmpty() ? session('corporation_num_r') : $requestOld['corporation_num']}}"
                                                    name="corporation_num" id="corporation_num" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">顧客名カナ</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input id="client_name_kana" value="{{$errors->isEmpty() ? session('client_name_kana_r') : $requestOld['client_name_kana']}}"
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
                                                    <span class="">売掛年月</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('target_data_st_r') : $requestOld['target_data_st']}}"
                                                    name="target_data_st" id="target_data_st" class="form-control" autocomplete="off">
                                                </div>
                                                <div class="search-title col-xs-1">
                                                    <span class="">~</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('target_data_en_r') : $requestOld['target_data_en']}}"
                                                    name="target_data_en" id="target_data_en" class="form-control" autocomplete="off">
                                                </div>
                                                <span class="text-danger dis-block">{{ $errors->has('target_data_st') ? $errors->first('target_data_st') : ''}}</span>
                                                <span class="text-danger dis-block">{{ $errors->has('target_data_en') ? $errors->first('target_data_en') : ''}}</span>
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
                            <div class="col-lg-10 col-lg-offset-1">
                                <div class="col-lg-1">
                                    @if(Crofun::receivable_index_return_button() == 1)
                                        <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                            <button class="btn btn-warning btn-sm"> 戻る</button>
                                        </a>
                                    @endif
                                    @if(Crofun::receivable_index_return_button() == 2)
                                        <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                            <button class="btn btn-warning btn-sm"> 戻る</button>
                                        </a>
                                    @endif
                                </div>
                                <div class="col-lg-offset-7">
                                    @if(isset($receivable))
                                        @paginate(['item'=> $receivable]) @endpaginate
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 col-lg-offset-1 fix-mobile-col table-parent">
                                    <table id="receivable_table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>顧客コード</th>
                                                <th>顧客名</th>
                                                <th>売掛年月</th>
                                                <th>売掛金残</th>
                                                <th>所属会社</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($receivable))
                                                @foreach ($receivable as $receivable)
                                                <tr>
                                                    <td>
                                                        @if($receivable->customer->client_code_main == null)
                                                            {{ $receivable->customer->client_code }}
                                                        @else
                                                            {{ $receivable->customer->client_code_main }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $receivable->customer->client_name }}</td>
                                                    <td>{{ date('Y/m',strtotime($receivable->target_data)) }}</td>
                                                    <td style="text-align: right">{{ number_format($receivable->receivable /1000) }}</td>
                                                    <td>{{ $receivable->company->abbreviate_name }}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
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
    $(document).on('click', '#clear', function () {
		//1ページに移動
        $('#company_id').prop('selectedIndex',0);
        $('#client_code').val('');
        $('#corporation_num').val('');
        $('#client_name').val('');
        $('#client_name_kana').val('');
        $( "#target_data_st" ).val('');
        $( "#target_data_en" ).val('');
        $( "#form" ).submit();
	});

	$("#target_data_st").MonthPicker({
		Button: false ,
		MonthFormat: 'yy/mm'
	});
	$("#target_data_en").MonthPicker({
		Button: false ,
		MonthFormat: 'yy/mm'
	});
</script>
@endsection
