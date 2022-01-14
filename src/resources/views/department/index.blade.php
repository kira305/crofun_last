@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('department/index'))
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
                                <form id="form" action="{{ url('department/index') }}" method="POST">
                                    {{-- row 1 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        <option selected value=""> </option>
                                                        @foreach($companies as $company)
                                                        <option @if(isset($company_id)) @if ($company_id==$company->id)
                                                            selected @endif
                                                            @endif
                                                            value="{{$company->id}}">{{$company->abbreviate_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">事業本部名</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" name="headquarter_id" id="headquarter_id">
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
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">部署名</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input type="text" class="form-control" id="department_name"
                                                name="department_name" @if(isset($department_name))
                                                value="{{ $department_name }}" @endif>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">非表示</span>
                                                </div>
                                                <div class="col-xs-9 search-item form-group">
                                                    <input type="checkbox" id="status" name="status" @if(isset($status)) @if($status=='on' ) checked @endif @endif autocomplete="off" />
                                                    <div class="btn-group">
                                                        <label for="status" class="btn btn-default border-none">
                                                            <span class="glyphicon glyphicon-ok"></span>
                                                            <span> </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    @csrf
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="submit" id="search"
                                                class="search-button btn btn-primary btn-sm">検索</button>
                                        </div>
                                        <div class="col-xs-3">
                                            <button type="button" id="clear"
                                                class="clear-button btn btn-default btn-sm">クリア</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    @if( Auth::user()->can('create','App\Department_MST'))
                                    <a href="{{ url('department/create') }}">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm">新規登録
                                        </button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-3 col-lg-offset-9">
                                    @paginate(['item'=> $departments]) @endpaginate
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="department_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">編集</th>
                                            <th>表示コード</th>
                                            <th>部署コード</th>
                                            <th>部署名</th>
                                            <th>事業本部名</th>
                                            <th>所属会社名</th>
                                            <th>非表示</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($departments as $department)
                                        <tr>
                                            <td>
                                                @if( Auth::user()->can('update','App\Department_MST'))
                                                    <a href="{{route('editdepartment', ['id' => $department->id,'page'=>request()->page])}}">
                                                        <button  type="submit" style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $department->department_code }}</td>
                                            <td>{{ $department->department_list_code }}</td>
                                            <td>{{ $department->department_name }}</td>
                                            <td>{{ $department->headquarter()->headquarters }}</td>
                                            <td>{{ $department->company()->abbreviate_name }}</td>
                                            <td>@if($department->status == false) 非表示 @endif </td>
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
    $( document ).ready(function() {
            $("#clear").click(function(){
                $('#company_id').prop('selectedIndex',0);
                $('#headquarter_id').prop('selectedIndex',0);
                $('#department_name').val('');
                $( "#status" ).prop( "checked", false );
                $( "#form" ).submit();
            });
    });
</script>
@endsection
