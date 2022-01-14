@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('group/index'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form action="{{ url('group/index') }}" id="form" method="POST">
                                    {{-- row 1 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        <option value=""> </option>
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
                                                    <span class="">事業本部</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="headquarter_id"
                                                        name="headquarter_id">
                                                        <option value=""> </option>
                                                        @foreach($headquarters as $headquarter)
                                                        <option class="headquarter_id"
                                                            data-value="{{ $headquarter->company_id }}"
                                                            @if(isset($headquarter_id))
                                                                @if($headquarter_id==$headquarter->id) selected @endif
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
                                                    <select class="form-control" style="float: left;" id="department_id"
                                                        name="department_id">
                                                        <option value=""></option>
                                                        @foreach($departments as $department)
                                                        <option class="department_id"
                                                            id="{{ $department->headquarter()->id }}"
                                                            data-value="{{ $department->headquarter()->id }}"
                                                            @if(isset($department_id))
                                                                @if($department_id==$department->id) selected @endif
                                                            @endif
                                                            value="{{$department->id}}">{{$department->department_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">グループ名</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input type="text" name="group_name" id="group_name"
                                                        @if(isset($group_name)) value="{{ $group_name }}" @endif
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">非表示</span>
                                                </div>
                                                <div class="col-xs-9 search-item form-group">
                                                    <input type="checkbox" id="status" name="status" @if(isset($status))
                                                        @if($status=='on' ) checked @endif @endif autocomplete="off" />
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
                        <div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        @if( Auth::user()->can('create','App\Group_MST'))
                                        <a href="{{ url('group/create') }}">
                                            <button type="submit" class="btn btn-primary btn-sm">新規登録</button>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="col-lg-3 col-lg-offset-9">
                                        @paginate(['item'=> $groups]) @endpaginate
                                    </div>
                                </div>
                                <div class="row table-parent">
                                    <table id="group_table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">編集</th>
                                                <th>表示コード</th>
                                                <th>グループコード</th>
                                                <th>グループ名</th>
                                                <th>部署名</th>
                                                <th>事業本部名</th>
                                                <th>所属会社名</th>
                                                <th>非表示</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groups as $group)
                                                <tr>
                                                    <td>
                                                        @if( Auth::user()->can('update','App\Group_MST'))
                                                            <a href="{{route('editgroup', ['id' => $group->id,'page'=>request()->page])}}">
                                                                <button type="submit" style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td>{{  $group->group_code }}</td>
                                                    <td>{{  $group->group_list_code }}</td>
                                                    <td>{{  $group->group_name }}</td>
                                                    <td>{{  $group->department()->department_name }}</td>
                                                    <td>{{  $group->headquarter()->headquarters }}</td>
                                                    <td>{{  $group->headquarter()->abbreviate_name }}</td>
                                                    <td>@if($group->status == false) 非表示 @endif </td>
                                                </tr>
                                            @endforeach
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
    $(document).ready(function() {
        $( "#department_id" ).prop( "disabled", true );
        if($( "#headquarter_id" ).val() != ""){
        $( "#department_id" ).prop( "disabled", false );
                    var headquarter_id = $("#headquarter_id").val();
                    $( ".department_id" ).each(function() {
                        $(this).show();
                        if($(this).attr('data-value') !== headquarter_id){
                            $(this).remove();
                        }
                    });
        }
        $("#clear").click(function(){
            $('#company_id').prop('selectedIndex',0);
            $('#headquarter_id').prop('selectedIndex',0);
            $('#department_id').prop('selectedIndex',0);
            $('#group_name').val('');
            $( "#status" ).prop( "checked", false );
            $( "#form" ).submit();
        });
    });
</script>
@endsection
