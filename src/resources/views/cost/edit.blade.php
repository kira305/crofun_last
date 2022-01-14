@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('cost/edit'))
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
                                <form id="create_cost" method="post" action="{{ url('cost/edit') }}">
                                    @csrf
                                    @php
                                        $checkIsNull = $cost->checkIsNull();
                                    @endphp
                                    <input type="hidden" value="{{ $cost->updated_at }}" name="update_time">
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
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            {{-- r1 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    @if($checkIsNull == 0)
                                                        <select class="form-control" id="company_id" name="company_id">
                                                            @foreach($companies as $company)
                                                                <option {{ $cost->company_id == $company->id ? 'selected' : '' }}
                                                                    value="{{$company->id}}">{{$company->abbreviate_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" readonly value="{{ $cost->company->company_name }}" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属本部</label>
                                                    @if($checkIsNull == 0)
                                                        <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                            <option> </option>
                                                            @foreach($headquarters as $headquarter)
                                                                <option class="headquarter_id"
                                                                    {{ $cost->headquarter_id == $headquarter->id ? 'selected' : '' }}
                                                                    data-value="{{ $headquarter->company_id }}"
                                                                    value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" readonly name="headquarter_id" value="{{ $cost->headquarter->headquarters }}" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r2 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属部署</label>
                                                    @if($checkIsNull == 0)
                                                        <select class="form-control" id="department_id" name="department_id">
                                                            <option> </option>
                                                            @foreach($departments as $department)
                                                                <option class="department_id"
                                                                    {{ $cost->department_id == $department->id ? 'selected' : '' }}
                                                                    data-value="{{ $department->headquarter()->id }}"
                                                                    value="{{$department->id}}">{{$department->department_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($checkIsNull == 3)
                                                        <input type="text" readonly name="department_id" value="" class="form-control">
                                                    @else
                                                        <input type="text" readonly name="department_id" value="{{ $cost->department->department_name }}" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属グループ</label>
                                                    @if($checkIsNull == 0)
                                                        <select class="form-control" id="group_id" name="group_id">
                                                            <option> </option>
                                                            @foreach($groups as $group)
                                                                <option class="group_id"
                                                                    {{ $cost->group_id == $group->id ? 'selected' : '' }}
                                                                    data-value="{{ $group->department()->id }}"
                                                                    value="{{$group->id}}">{{$group->group_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($checkIsNull == 3)
                                                        <input type="text" readonly value="" class="form-control">
                                                    @else
                                                        <input type="text" readonly name="group_id" value="{{ $cost->group->group_name }}" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r3 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>販管費/原価</label>
                                                    @if($checkIsNull == 0)
                                                        <select class="form-control" name="type">
                                                            <option value="2" @if ($cost->type == "2") selected @endif >販管費</option>
                                                            <option value="1" @if ($cost->type == "1") selected @endif >原価</option>
                                                        </select>
                                                    @elseif($cost->type == "2")
                                                        <input type="text" readonly value="販管費" class="form-control">
                                                    @else
                                                        <input type="text" readonly value="原価" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('type') ? $errors->first('type') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>コード</label>
                                                    @if($checkIsNull == 0)
                                                        <input type="text" name="cost_code" value="{{ $cost->cost_code }}" class="form-control">
                                                        <input hidden name="id" value="{{ $cost->id }}">
                                                    @else
                                                        <input type="text" readonly name="cost_code" value="{{ $cost->cost_code }}" class="form-control">
                                                    @endif
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
                                                    @if($checkIsNull == 0)
                                                        <input type="text" name="cost_name" value="{{ $cost->cost_name }}" class="form-control">
                                                    @else
                                                        <input type="text" readonly name="cost_name" value="{{ $cost->cost_name }}" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('cost_name') ? $errors->first('cost_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r5 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-4 form-group">
                                                    <label class="checkbox-title">非表示</label>
                                                    <div class="icheck-primary d-inline ">
                                                        <input type="checkbox" name="status" id="status" @if($checkIsNull != 0) disabled @endif
                                                        @if ($cost->status == false) checked @endif class="input_checkbox">
                                                        <label for="status"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    @if($checkIsNull == 0)
                                                        <div class="col-xs-3 col-xs-offset-3">
                                                            <button type="submit" class="btn btn-primary search-button">登録</button>
                                                        </div>
                                                    @endif
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
