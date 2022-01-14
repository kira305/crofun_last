@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('user/index'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form id="form" action="{{ url('user/index') }}" method="POST">
                                    {{-- row 1 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
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
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">事業本部</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
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
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">部署</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
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
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">グループ</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
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
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">社員番号</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
                                                    <input type="text" class="form-control" name="usr_code"
                                                id="usr_code" @if(isset($usr_code)) value="{{$usr_code}}" @endif>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">社員名</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
                                                    <input type="text" @if(isset($usr_name)) value="{{$usr_name}}"
                                                id="user_name" @endif class="form-control" name="usr_name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">役職</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
                                                    <select class="form-control" id="position_id" name="position_id">
                                                        <option value=""> </option>
                                                        @foreach($position_list as $position)
                                                        <option class="position_id" data-value="{{ $position->company_id }}"
                                                            @if(isset($position_id)) @if ($position_id==$position->id)
                                                            selected @endif
                                                            @endif
                                                            value="{{$position->id}}">{{$position->position_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-4 col-md-3">
                                                    <span class="">画面機能ルール</span>
                                                </div>
                                                <div class="col-xs-8 col-md-9 search-item">
                                                    <select class="form-control" id="rule_id" name="rule_id">
                                                        <option value=""></option>
                                                        @foreach($rule_list as $rule)
                                                        <option class="rule_id" data-value="{{ $rule->company_id }}"
                                                            @if(isset($rule_id)) @if ($rule_id==$rule->id) selected @endif @endif
                                                            value="{{$rule->id}}">{{$rule->rule}}</option>
                                                        @endforeach
                                                    </select>
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
            </li>
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    @if( Auth::user()->can('create','App\User'))
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ url('user/create') }}">新規登録
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-offset-9">
                                    @paginate(['item'=> $users]) @endpaginate
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="user_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="edit_button_with">編集</th>
                                            <th>社員番号</th>
                                            <th>社員名</th>
                                            <th>所属会社</th>
                                            <th>所属事業本部</th>
                                            <th>所属部署</th>
                                            <th>所属グループ</th>
                                            <th>役職</th>
                                            <th>画面機能ルール</th>
                                            <th>退職</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $canCreate = Auth::user()->can('update','App\User');
                                        @endphp
                                        @foreach ($users as $user)
                                            <tr id="{{ $user->id }}">
                                                <td>
                                                    @if($canCreate)
                                                        <a href="{{route('edituserinfor', ['id' => $user->id,'page' => request()->page])}}">
                                                            <button style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="user_row">{{ $user->usr_code }}</td>
                                                <td class="user_row">{{ $user->usr_name }}</td>
                                                <td class="user_row">{{ $user->company->abbreviate_name }}</td>
                                                <td class="user_row">{{ $user->headquarter->headquarters }}</td>
                                                <td class="user_row">{{ $user->department->department_name }}</td>
                                                <td class="user_row">{{ $user->group->group_name }}</td>
                                                <td class="user_row">{{ $user->position->position_name }}</td>
                                                <td class="user_row">{{ $user->getrole->rule }}</td>
                                                <td>@if($user->retire == true) 退職 @endif </td>
                                            </tr>
                                            @foreach ($user->concurrently() as $con)
                                            <tr class="concurrent">
                                                <td></td>
                                                <td class="user_row"></td>
                                                <td class="user_row">{{ $con->usr_name }}</td>
                                                <td class="user_row">{{ $con->company->abbreviate_name }}</td>
                                                <td class="user_row">{{ $con->headquarter->headquarters }}</td>
                                                <td class="user_row">{{ $con->department->department_name }}</td>
                                                <td class="user_row">{{ $con->group->group_name }}</td>
                                                <td class="user_row">{{ $con->position->position_name }}</td>
                                                <td class="user_row"></td>
                                                <td>@if($con->status == false) 兼務解除 @endif</td>
                                            </tr>
                                            @endforeach
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
    $( document ).ready(function() {
                $('#user_table').DataTable({
                'paging'      : false,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : false,
                'stateSave'   : true,
                'info'        : false,
                'autoWidth'   : false,
                'pageLength': 10,
                'dom': '<"top"p>',
            })

        });

    $(document).on('click', '#clear', function () {
            $('#company_id').prop('selectedIndex',0);
            $('#headquarter_id').prop('selectedIndex',0);
            $('#department_id').prop('selectedIndex',0);
            $('#group_id').prop('selectedIndex',0);
            $('#position_id').prop('selectedIndex',0);
            $('#rule_id').prop('selectedIndex',0);
            $('#usr_code').val('');
            $('#user_name').val('');
            $( "#form" ).submit();
    });

    $(document).ready(function() {
        $( "#headquarter_id" ).prop( "disabled", true );
        $( "#department_id" ).prop( "disabled", true );
        $( "#group_id" ).prop( "disabled", true );
        $(".user_row").click(function(){
            if($('#flag').val() === '0'){
                $('.concurrent').show();
                $('#flag').val('1');
            } else {
                $('.concurrent').hide();
                $('#flag').val('0');
            }
        });
    });
</script>
@endsection
