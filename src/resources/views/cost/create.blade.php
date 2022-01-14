@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('cost/create'))
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
                                <form id="create_cost" method="post" action="{{ url('cost/create') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            {{-- r1 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        <option value=""> </option>
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
                                            {{-- r2 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属部署</label>
                                                    <select class="form-control" id="department_id" name="department_id">
                                                        <option value=""> </option>
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
                                                        <option value=""> </option>
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
                                            {{-- r3 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>販管費/原価</label>
                                                    <select class="form-control" name="type">
                                                        <option {{ old('type') == 2 ? 'selected' : '' }} value="2">販管費</option>
                                                        <option {{ old('type') == 1 ? 'selected' : '' }} value="1">原価</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('type') ? $errors->first('type') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>コード</label>
                                                    <input type="text" name="cost_code" value="{{ old('cost_code') }}" class="form-control">
                                                    <div class="text-danger">
                                                        {{ $errors->has('cost_code') ? $errors->first('cost_code') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- r4 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>名称</label>
                                                    <input type="text" name="cost_name" value="{{ old('cost_name') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('cost_name') ? $errors->first('cost_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">登録</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        @php($page = session()->has('cost.page') ? '?page='.session('cost.page') : '')
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('cost/index'.$page) }}">戻る</a>
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
