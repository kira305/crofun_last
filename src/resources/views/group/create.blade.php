@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('group/create'))
<script type="text/javascript">
    $( window ).on( "load", function() {
        @if(isset($message))
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
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form id="create_user" method="post" action="{{ url('group/create') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach($companies as $company)
                                                            <option {{ old('company_id') == $company->id ? 'selected' : '' }}
                                                                value="{{$company->id}}">{{$company->abbreviate_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>事業本部名</label>
                                                    <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                        <option> </option>
                                                        @foreach($headquarters as $headquarter)
                                                            <option class="headquarter_id"
                                                                {{ old('headquarter_id') == $headquarter->id ? 'selected' : '' }}
                                                                data-value="{{ $headquarter->company_id }}"
                                                                value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>部署名</label>
                                                    <select class="form-control" id="department_id" name="department_id">
                                                        <option value=""></option>
                                                        @foreach($departments as $department)
                                                        <option class="department_id"
                                                            id="{{ $department->headquarter()->id }}"
                                                            {{ old('department_id') == $department->id ? 'selected' : '' }}
                                                            data-value="{{ $department->headquarter()->id }}"
                                                            @if(isset($department_id))
                                                                @if ($department_id==$department->id) selected @endif
                                                            @endif
                                                            value="{{$department->id}}">{{$department->department_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>表示コード</label>
                                                    <input type="text" name="group_code" value="{{ old('group_code') }}" class="form-control">
                                                    <div class="text-danger">
                                                        {{ $errors->has('group_code') ? $errors->first('group_code') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ isset($unique) ? $unique : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>グループコード</label>
                                                    <input type="text" name="group_list_code" value="{{ old('group_list_code') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_list_code') ? $errors->first('group_list_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>グループ名</label>
                                                    <input type="text" name="group_name" value="{{ old('group_name') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_name') ? $errors->first('group_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>原価コード</label>
                                                    <input type="text" id="cost_code" value="{{ old('cost_code') }}" name="cost_code" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('cost_code') ? $errors->first('cost_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>原価名</label>
                                                    <input type="text" name="cost_name" value="{{ old('cost_name') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('cost_name') ? $errors->first('cost_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">登録</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        @php($page = session()->has('group.page') ? '?page='.session('group.page') : '')
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('group/index'.$page) }}">戻る</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
            $("#department_id").change(function() {
                    var headquarter_name=$(this).children("option:selected").data("value");
                    $("#headquarter_name").val(headquarter_name);
                    $("#fake_name").text(headquarter_name);
                }
            );
        }
    );
    $(document).on('submit','#create_user',function(){
        var headquarter_name = $("#department_id").children("option:selected").data("value");
        $("#headquarter_name").val(headquarter_name);
    });
</script>
@endsection
