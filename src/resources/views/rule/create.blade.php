@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('rule/create'))
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
                        <div class="box-body">
                            <form action="{{ url('rule/create') }}" id="create_rule" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        {{-- r1 --}}
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
                                        {{-- r2 --}}
                                        <div class="row search-form">
                                            <div class="col-lg-6 form-group">
                                                <label>画面機能ルール</label>
                                                <input type="text" name="rule_name" value="{{ old('rule_name') }}" class="form-control">
                                                <div class="text-danger">
                                                    {{ $errors->has('rule_name') ? $errors->first('rule_name') : '' }}
                                                </div>
                                                <div class="text-danger">
                                                    {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r3 --}}
                                        <div class="row search-form">
                                            <div class="col-lg-4 form-group">
                                                <label class="checkbox-title">管理者フラグ (<span class="color-header"> CRO-FUN内の所属会社情報更新権限付与 </span>)</label>
                                                <div class="icheck-primary d-inline ">
                                                    <input type="checkbox" {{ old('admin_flag') == 'on' ? 'checked' : '' }} name="admin_flag" id="admin_flag" class="input_checkbox">
                                                    <label for="admin_flag"></label>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r4 --}}
                                        <div class="row search-form">
                                            <div class="col-lg-4 form-group">
                                                <label class="checkbox-title">全会社参照フラグ (<span class="color-header"> CRO-FUN内の全会社情報更新権限付与 </span>)</label>
                                                <div class="icheck-primary d-inline ">
                                                    <input type="checkbox" {{ old('superuser_user') == 'on' ? 'checked' : '' }} name="superuser_user" id="superuser_user" class="input_checkbox">
                                                    <label for="superuser_user"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row search-form p-t-20">
                                            <div class="col-lg-6 form-group btn-upload-style">
                                                <div class="col-xs-3 col-xs-offset-3">
                                                    <button type="submit" class="btn btn-primary search-button">登録</button>
                                                </div>
                                                <div class="col-xs-3">
                                                    @php($page = session()->has('rule.page') ? '?page='.session('rule.page') : '')
                                                    <a style="float: left" class="btn btn-danger search-button" href="{{ url('rule/index'.$page) }}">戻る</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- end --}}
                                <div class="row search-form p-t-20">
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        @foreach ($menus as $menu)
                                            <div class="form-group @if((($menu->dis_sort != 1) && ($menu->dis_sort != 0)) || $menu->dis_sort === Null) checkbox-rule-child @endif">
                                                <div class="icheck-primary d-inline ">
                                                    <input type="checkbox" class="input_checkbox check_rule"
                                                        value="{{ $menu->id }}"
                                                        name="{{ $menu->id }}" id="{{ $menu->id }}"
                                                        @if($menu->id != 0)
                                                            {{ old($menu->id) == $menu->id ? 'checked' : '' }}
                                                        @endif
                                                        @if(old($menu->id) === '0')
                                                            checked
                                                        @endif>
                                                    <label for="{{ $menu->id }}" class="checkbox-rule-style"></label>
                                                </div>
                                                <label for="{{ $menu->id }}" class="checkbox-rule">{{ $menu->link_name }}</label>
                                            </div>
                                        @endforeach
                                        <input type="hidden" name="check_data" value="" id="check_data">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).on('submit', '#create_rule', function() {
            var check_data=[];
            $(".check_rule").each(function() {
                    if($(this).is(":checked")) {
                        check_data.push($(this).val());
                    }
                }
            );
            console.log(check_data);
            $("#check_data").val(check_data);
        }
    );
</script>
@endsection
