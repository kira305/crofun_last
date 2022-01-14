@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('contracttype/create'))
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
                                <form id="create_user" method="post" action="{{ url('contracttype/create') }}" enctype="multipart/form-data" name="MainForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label></span>所属会社</label>
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach ($companies as $company)
                                                            <option
                                                                {{ session('contract_type_create.company_id') == $company->id ? 'selected' : '' }}
                                                                value="{{ $company->id }}">
                                                                {{ $company->abbreviate_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- r1 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>名称</label>
                                                    <input type="text" name="type_name" maxlength="20" value="{{ session('contract_type_create.type_name') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('type_name') ? $errors->first('type_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r2 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>表示コード</label>
                                                    <input type="text" maxlength="5" name="display_code" value="{{ session('contract_type_create.display_code') }}" class="form-control uintTextBox">
                                                    <span class="text-danger">
                                                        {{ $errors->has('display_code') ? $errors->first('display_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r3 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>説明</label>
                                                    <textarea class="form-control" rows="3" name="description">{{ session('contract_type_create.description') }}</textarea>
                                                    <span class="text-danger">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row search-form p-t-20">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button" name="mode1">登録</button>
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
