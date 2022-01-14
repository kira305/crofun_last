@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('contracttype/edit'))
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
                                <form id="create_user" method="post" action="{{ url('contracttype/edit'.'?id='.$contractType->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" value="{{ $updateTime }}" name="update_time">
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
                                    <input type="hidden" name="id" value="{{ $contractType->id }}">
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label></span>所属会社</label>
                                                    <input type="text" disabled value="{{ $contractType->company->abbreviate_name }}" class="form-control">
                                                    <input type="hidden" name="company_id" value="{{ $contractType->company_id }}" class="form-control">
                                                </div>
                                            </div>
                                            {{-- r1 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>名称</label>
                                                    <input type="text" name="type_name" maxlength="20" value="{{ request()->session()->exists('contract_type_create') ? session('contract_type_create.type_name') : $contractType->type_name }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('type_name') ? $errors->first('type_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r2 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>表示コード</label>
                                                    <input type="text" maxlength="5" name="display_code" value="{{ request()->session()->exists('contract_type_create') ? session('contract_type_create.display_code') : $contractType->display_code}}" class="uintTextBox form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('display_code') ? $errors->first('display_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r3 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>説明</label>
                                                    <textarea class="form-control" rows="3" name="description">{{  request()->session()->exists('contract_type_create') ? session('contract_type_create.description') : $contractType->description }}</textarea>
                                                    <span class="text-danger">
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r4 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-4 form-group">
                                                    <label class="checkbox-title">非表示</label>
                                                    <div class="icheck-primary d-inline ">
                                                        <input type="checkbox" name="hidden" id="hidden"　@if($contractType->hidden == 1) checked @endif　class="input_checkbox">
                                                        <label for="hidden"></label>
                                                    </div>
                                                    <div class="text-danger m-t-5">
                                                        {{ $errors->has('hidden') ? $errors->first('hidden') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row search-form p-t-20">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button" name="mode1">更新</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('contracttype/index') }}">戻る</a>
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
@endsection
