@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('user/concurrent/edit',$concurrent->usr_id))
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
                                <div class="text-danger">
                                    {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                </div>
                                @php
                                    $checkCompany = Auth::user()->checkCompany($concurrent->company_id);
                                    $checkIsDisable = $concurrent->checkIsDisable($concurrent->id);
                                @endphp
                                <form id="create_user" method="post" action="{{ url('user/concurrent/edit') }}">
                                    @csrf
                                    <input type="hidden" value="{{ $concurrent->updated_at }}" name="update_time">
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <span class="text-danger">
                                                        {{ $errors->has('update_time') ? $errors->first('update_time') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{-- r1 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>社員番号</label>
                                                    <input type="text" id="user_code" value="{{$concurrent->usr_code}}" class="form-control">
                                                    <input type="hidden" name="usr_code" value="{{$concurrent->usr_code}}">
                                                    <input type="hidden" name="concurrent_id" value="{{$concurrent->id}}">
                                                    <div class="text-danger">
                                                        {{ $errors->has('usr_code') ? $errors->first('usr_code') : '' }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>社員名</label>
                                                    <input type="text" id="user_name" value="{{$concurrent->usr_name}}" class="form-control">
                                                    <input type="hidden" name="usr_name" value="{{$concurrent->usr_name}}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('usr_name') ? trans('validation.user_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r2 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input readonly name="company_id" value="{{$concurrent->company->company_name}}" class="form-control">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="company_id" name="company_id">
                                                            @foreach($companies as $company)
                                                                <option @if ($concurrent->company_id == $company->id) selected  @endif
                                                                    value="{{$company->id}}">{{$company->abbreviate_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" name="company_id_N" id="company_id" disabled>
                                                            <option value="{{$concurrent->id}}">
                                                                {{$concurrent->company->abbreviate_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>事業本部名</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input readonly name="headquarter_id" value="{{$concurrent->headquarter->headquarters}}" class="form-control">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" name="headquarter_id" id="headquarter_id">
                                                            @foreach($headquarters as $headquarter)
                                                                <option class="headquarter_id"
                                                                    data-value="{{ $headquarter->company_id }}" @if ($concurrent->headquarter_id == $headquarter->id) selected @endif
                                                                    value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input class="form-control" value="{{$concurrent->headquarter->headquarters}}" readonly>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r3 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>事業部名</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input readonly name="department_id" value="{{$concurrent->department->department_name}}" class="form-control">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="department_id" name="department_id">
                                                            <option value=""> </option>
                                                            @foreach($departments as $department)
                                                                <option class="department_id"
                                                                    data-value="{{ $department->headquarters_id }}"
                                                                    @if($concurrent->department_id == $department->id) selected @endif
                                                                    value="{{$department->id}}">{{$department->department_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="department_id_N"
                                                            name="department_id" disabled>
                                                            <option value="{{$concurrent->id}}">
                                                                {{$concurrent->department->department_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属グループ</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input readonly name="group_id" value="{{$concurrent->group->group_name}}" class="form-control">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="group_id" name="group_id">
                                                            <option value=""> </option>
                                                            @foreach($groups as $group)
                                                                <option class="group_id" data-value="{{ $group->department_id }}"
                                                                    @if ($concurrent->group_id == $group->id) selected @endif
                                                                    value="{{$group->id}}">{{$group->group_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="group_id_N" name="group_id" disabled>
                                                            <option value="{{$concurrent->id}}">
                                                                {{$concurrent->group->group_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r4 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>役職</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input readonly name="position_id"  value="{{$concurrent->position->position_name}}" class="form-control">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="position_id" name="position_id">
                                                            <option value=""> </option>
                                                            @foreach($position_list as $position)
                                                                <option class="position_id" data-value="{{ $position->company_id }}"
                                                                    @if ($concurrent->position_id == $position->id) selected @endif
                                                                    value="{{$position->id}}">{{$position->position_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="position_id_N" name="position_id" disabled>
                                                            <option value="{{$concurrent->id}}">
                                                                {{$concurrent->position->position_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('position_id') ? $errors->first('position_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    @if ($checkIsDisable != 1)
                                                        <div class="col-xs-3 col-xs-offset-3">
                                                            <button type="submit" class="btn btn-primary search-button" @if(!$checkCompany) disabled @endif>編集</button>
                                                        </div>
                                                    @endif
                                                    <div class="col-xs-3">
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{route('edituserinfor', ['id' => $concurrent->usr_id])}}">戻る</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                </form>
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
        $( "#user_code" ).prop( "disabled", true );
        $( "#user_name" ).prop( "disabled", true );
    });
</script>
@endsection
