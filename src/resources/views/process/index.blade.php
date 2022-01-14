@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('process/index'))
<script type="text/javascript" src="{{ asset('js/MonthPicker.js') }}"></script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form action="{{ url('process/index') }}" id="form" method="post">
                                    {{-- row 1 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach($companies as $company)
                                                        <option
                                                            @if($errors->isEmpty())
                                                                @if(session()->has('company_id_pr'))
                                                                    @if(session('company_id_pr') == $company->id) selected @endif
                                                                @else
                                                                    @if(Auth::user()->company_id == $company->id) selected @endif
                                                                @endif
                                                            @else
                                                                @if($company_id == $company->id) selected @endif
                                                            @endif
                                                            value="{{$company->id}}">{{$company->abbreviate_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">事業本部</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                        <option value=""> </option>
                                                        @foreach($headquarters as $headquarter)
                                                        <option class="headquarter_id"
                                                            data-value="{{ $headquarter->company_id }}"
                                                            @if(isset($headquarter_id)) @if ($headquarter_id==$headquarter->
                                                            id) selected @endif
                                                            @endif
                                                            value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">部署</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="department_id" name="department_id">
                                                        <option selected value=""></option>
                                                        @foreach($departments as $department)
                                                        <option class="department_id"
                                                            data-value="{{ $department->headquarter()->id }}"
                                                            @if(isset($department_id)) @if ($department_id==$department->id)
                                                            selected @endif
                                                            @endif
                                                            value="{{$department->id}}">{{$department->department_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">グループ</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="group_id" name="group_id">
                                                        <option selected value=""></option>
                                                        @foreach($groups as $group)
                                                        <option class="group_id" data-value="{{ $group->department()->id }}"
                                                            @if(isset($group_id)) @if ($group_id==$group->id) selected
                                                            @endif
                                                            @endif
                                                            value="{{$group->id}}">{{$group->group_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">顧客コード</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('client_code_pr') : $client_code}}"
                                                    name="client_code" id="client_code" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">顧客名カナ</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input id="client_name_kana" value="{{$errors->isEmpty() ? session('client_name_kana_pr') : $client_name_kana}}"
                                                    name="client_name_kana" type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">プロジェクトコード</span>
                                                </div>
                                                <div class="search-item col-xs-7 col-md-8">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('project_code_pr') : $project_code}}"
                                                    name="project_code" id="project_code" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">プロジェクト名</span>
                                                </div>
                                                <div class="search-item col-xs-7 col-md-8">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('project_name_pr') : $project_name}}"
                                                    name="project_name" id="project_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 5 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">法人番号</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('corporation_num_pr') : $corporation_num}}"
                                                    name="corporation_num" id="corporation_num"
                                                    class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-3">
                                                    <span class="">売上年月</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('target_data_st_pr') : $target_data_st}}"
                                                    name="target_data_st" id="target_data_st" class="form-control" autocomplete="off">
                                                </div>
                                                <div class="search-title col-xs-1">
                                                    <span class="">~</span>
                                                </div>
                                                <div class="col-xs-4 search-item">
                                                    <input type="text" value="{{$errors->isEmpty() ? session('target_data_en_pr') : $target_data_en}}"
                                                    name="target_data_en" id="target_data_en" class="form-control" autocomplete="off">
                                                </div>
                                                <div class="text-danger">{{ $errors->has('target_data_st') ? $errors->first('target_data_st') : ''}}</div>
                                                <div class="text-danger">{{ $errors->has('target_data_en') ? $errors->first('target_data_en') : ''}}</div>
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
                                <div class="col-lg-2">
                                    @if(Crofun::process_index_return_button() == 1)
                                    <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if(Crofun::process_index_return_button() == 2)
                                    <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if(Crofun::process_index_return_button() == 3 && request()->project_id != null)
                                    <a href="{{route('edit_project', ['id' => request()->project_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if(Crofun::process_index_return_button() == 4)
                                    <a href="{{route('view_project', ['id' => request()->project_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-offset-9">
                                    @if(isset($processe))
                                        @paginate(['item'=> $processe]) @endpaginate
                                    @endif
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="process_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>プロジェクトコード</th>
                                            <th>プロジェクト名</th>
                                            <th>売上年月</th>
                                            <th>売上</th>
                                            <th>顧客コード</th>
                                            <th>顧客名</th>
                                            <th>グループ</th>
                                            <th>部署</th>
                                            <th>本部</th>
                                            <th>所属会社</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($processe))
                                        @foreach ($processe as $processes)
                                            <tr>
                                                <td>{{ $processes->project->project_code }}</td>
                                                <td>{{ $processes->project->project_name }}</td>
                                                <td>{{ date('Y/m',strtotime($processes->target_data)) }}</td>
                                                <td style="text-align: right">{{  number_format(($processes->debit - $processes->credit) / 1000) }}</td>
                                                <td>
                                                    @if($processes->project->customer->client_code_main == null)
                                                        {{  $processes->project->customer->client_code }}
                                                    @else
                                                        {{  $processes->project->customer->client_code_main }}
                                                    @endif
                                                </td>
                                                <td>{{ $processes->project->customer->client_name }}</td>
                                                <td>{{ $processes->project->group->group_name }}</td>
                                                <td>{{ $processes->project->department->department_name }}</td>
                                                <td>{{ $processes->project->headquarter->headquarters }}</td>
                                                <td>{{ $processes->company->abbreviate_name }}</td>
                                            </tr>
                                        @endforeach
                                        @endif
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
        $('#headquarter_id').prop('selectedIndex',0);
        $('#department_id').prop('selectedIndex',0);
        $('#group_id').prop('selectedIndex',0);
        $('#client_code').val('');
        $('#corporation_num').val('');
        $('#client_name_kana').val('');
        $('#project_code').val('');
        $('#project_name').val('');
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
