@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('project/index'))
<style type="text/css">
</style>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form id="form" action="{{ url('project/index') }}" method="POST">
                                    {{-- row 1 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="company_id" name="company_id_p">
                                                        @foreach($companies as $company)
                                                        <option @if(session()->has('company_id_p'))
                                                            @if(session('company_id_p') == $company->id) selected @endif
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
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">事業本部</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="headquarter_id"
                                                    name="headquarter_id_p">
                                                        <option selected value=""></option>
                                                        @foreach($headquarters as $headquarter)
                                                        <option class="headquarter_id"
                                                            data-value="{{ $headquarter->company_id }}"
                                                            {{ session('headquarter_id_p') == $headquarter->id ? 'selected' : '' }}
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
                                                    <select class="form-control" id="department_id" name="department_id_p">
                                                        <option selected value=""></option>
                                                        @foreach($departments as $department)
                                                        <option class="department_id"
                                                            data-value="{{ $department->headquarter()->id }}"
                                                            {{ session('department_id_p') == $department->id ? 'selected' : '' }}
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
                                                    <select class="form-control" id="group_id" name="group_id_p">
                                                        <option selected value=""></option>
                                                        @foreach($groups as $group)
                                                        <option class="group_id" data-value="{{ $group->department()->id }}"
                                                            {{ session('group_id_p') == $group->id ? 'selected' : '' }}
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
                                                    <input type="text" id="client_code" value="{{ session('client_code') }}"
                                                class="form-control" name="client_code">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">法人番号</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input type="text" id="personal_code" value="{{ session('personal_code') }}"
                                                class="form-control" name="personal_code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <p style="color: #367fa9; padding-left:15px">＊半角カナにて検索してください。旧顧客名はヒットしません。</p>
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">顧客名カナ</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input type="text" id="client_name" value="{{ session('client_name') }}"
                                                class="form-control" name="client_name">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">ステータス</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="status" name="status">
                                                        <option value=""></option>
                                                        <option {{ session('project_status') == 1 ? 'selected' : '' }}
                                                            value="1">取引中</option>
                                                        <option {{ session('project_status') == 2 ? 'selected' : '' }}
                                                            value="2">取引終了</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 5 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">プロジェクトコード</span>
                                                </div>
                                                <div class="search-item col-xs-7 col-md-8">
                                                    <input type="text" id="project_code" value="{{ session('project_code') }}"
                                                class="form-control" name="project_code">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form" >
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">プロジェクト名</span>
                                                </div>
                                                <div class="search-item col-xs-7 col-md-8">
                                                    <input type="text" id="project_name" value="{{ session('project_name') }}"
                                                class="form-control" name="project_name">
                                                </div>
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
                                    <button type="button" id="csv1" class="btn btn-success btn-sm">CSV出力</button>
                                    @php($project_index_return_button = Crofun::project_index_return_button())
                                    @if($project_index_return_button == 1)
                                    <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if($project_index_return_button == 2)
                                    <a href="{{route('Credit_index', ['client_id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if($project_index_return_button == 3)
                                    <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                    @if($project_index_return_button == 4)
                                    <a href="{{route('Credit_index')}}">
                                        <button class="btn btn-warning btn-sm"> 戻る</button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-offset-9">
                                    @paginate(['item'=>$projects]) @endpaginate
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="project_index_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="edit_button_with">編集</th>
                                            <th>プロジェクトコード</th>
                                            <th>プロジェクト名</th>
                                            <th>担当事業本部</th>
                                            <th>担当部署</th>
                                            <th>担当Grp</th>
                                            <th>顧客コード</th>
                                            <th>顧客名</th>
                                            <th>ステータス</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projects as $project)
                                        <tr>
                                            <td>
                                                @if( Auth::user()->can('update',$project) && Auth::user()->can('checkProjectParent',$project) == 0)
                                                    <a href="{{route('edit_project', ['id' => $project->id,'page' => request()->page])}}">
                                                        <button style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                    </a>
                                                @else
                                                    @if( Auth::user()->can('view',$project))
                                                        <a href="{{route('view_project', ['id' => $project->id,'show' =>1,'page' => request()->page])}}">
                                                            <button style="float: left;" class="btn btn-primary btn-sm">参照</button>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $project->project_code }}</td>
                                            <td>{{ $project->project_name }}</td>
                                            <td>{{ $project->headquarter->headquarters }}</td>
                                            <td>{{ $project->department->department_name }}</td>
                                            <td>{{ $project->group->group_name }}</td>
                                            <td>
                                                @if($project->customer->client_code_main == null)
                                                {{ $project->customer->client_code }}
                                                @else
                                                {{  $project->customer->client_code_main }}
                                                @endif
                                            </td>
                                            <td>{{ $project->customer->client_name_kana }}</td>
                                            <td>
                                                @if($project->status === true)
                                                    取引中
                                                @else
                                                    取引終了
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <input type="hidden" id="flag" value="0">
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '#clear', function () {
        $('#company_id').prop('selectedIndex',0);
        $('#headquarter_id').prop('selectedIndex',0);
        $('#department_id').prop('selectedIndex',0);
        $('#group_id').prop('selectedIndex',0);
        $('#status').prop('selectedIndex',0);
        $('#client_code').val('');
        $('#personal_code').val('');
        $('#client_name').val('');
        $('#project_code').val('');
        $('#project_name').val('');
        $( "#form" ).submit();
    });

    $(document).ready(function() {
        $( "#headquarter_id" ).prop( "disabled", true );
        $( "#department_id" ).prop( "disabled", true );
        $( "#group_id" ).prop( "disabled", true );
        $( "#csv1" ).click(function(event) {
                document.location.href = "/project/csv1";
        });
    });
</script>
@endsection
