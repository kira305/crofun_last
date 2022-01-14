@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('user/create'))
<script type="text/javascript">
    $( window ).on( "load", function() {
        @if($message = Session::get('success'))
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
                                <form id="create_user" method="post" action="{{ url('user/create') }}">
                                    @csrf
                                    <div class="row">
                                        {{-- r1 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>社員番号</label>
                                                    <input type="text" name="usr_code" value="{{ old('usr_code') }}" class="form-control">
                                                    <div class="text-danger">
                                                        {{ $errors->has('usr_code') ? $errors->first('usr_code') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>社員名</label>
                                                    <input type="text" name="usr_name" value="{{ old('usr_name') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('usr_name') ? $errors->first('usr_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r2 --}}
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
                                                <div class="col-lg-6 form-group">
                                                    <label>所属本部</label>
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
                                        {{-- r3 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属部署</label>
                                                    <select class="form-control" id="department_id" name="department_id">
                                                        <option> </option>
                                                        @foreach($departments as $department)
                                                            <option class="department_id"
                                                                {{ old('department_id') == $department->id ? 'selected' : '' }}
                                                                data-value="{{ $department->headquarter()->id }}"
                                                                value="{{$department->id}}">{{$department->department_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属グループ</label>
                                                    <select class="form-control" id="group_id" name="group_id">
                                                        <option> </option>
                                                        @foreach($groups as $group)
                                                            <option class="group_id"
                                                                {{ old('group_id') == $group->id ? 'selected' : '' }}
                                                                data-value="{{ $group->department()->id }}"
                                                                value="{{$group->id}}">{{$group->group_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                                                    <select class="form-control" id="position_id" name="position_id">
                                                        <option> </option>
                                                        @foreach($position_list as $position)
                                                            <option class="position_id"
                                                                {{ old('position_id') == $position->id ? 'selected' : '' }}
                                                                data-value="{{ $position->company_id }}"
                                                                value="{{$position->id}}">{{$position->position_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('position_id') ? $errors->first('position_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>メールアドレス</label>
                                                    <input type="email" name="mail_address" value="{{ old('mail_address') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('mail_address') ? $errors->first('mail_address') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r5 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>画面機能ルール</label>
                                                    <select class="form-control" id="rule_id" name="rule_id">
                                                        <option> </option>
                                                        @foreach($rule_list as $rule)
                                                        <option class="rule_id"
                                                            {{ old('rule_id') == $rule->id ? 'selected' : '' }}
                                                            data-value="{{ $rule->company_id }}" value="{{$rule->id}}">
                                                            {{$rule->rule}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('rule_id') ? $errors->first('rule_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">登録</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        @php($page = session()->has('userMst.page') ? '?page='.session('userMst.page') : '')
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('user/index'.$page) }}">戻る</a>
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
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
    integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
</script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
