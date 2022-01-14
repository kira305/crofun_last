@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('company/create'))
<script type="text/javascript" src="{{ asset('js/bs-custom-file-input.min.js') }}"></script>
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
                                    <form id="create_user" method="post" action="{{ url('company/create') }}" enctype="multipart/form-data">
                                        @csrf
                                        {{-- row 1 --}}
                                        <div class="row">
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>会社名</label>
                                                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="form-control" maxlength="25">
                                                        <span class="text-danger">
                                                            {{ $errors->has('company_name') ? $errors->first('company_name') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>省略名</label>
                                                        <input type="text" maxlength="10" name="abbreviate_name" value="{{ old('abbreviate_name') }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('abbreviate_name') ? $errors->first('abbreviate_name') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group btn-upload-style">
                                                        <label>ロゴファイル</label>
                                                        <div class="input-group ">
                                                            <div class="custom-file">
                                                                <input id="logo" value="{{ old('logo') }}" type="file" name="logo" class="custom-file-input">
                                                                <label class="custom-file-label" for="logo">{{ old('logo') }}</label>
                                                            </div>
                                                        </div>
                                                        <span class="text-danger">
                                                            {{ $errors->has('logo') ? $errors->first('logo') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <p style="padding-left: 15px;"><b class="color-red">※) </b><b class="color-header">推奨サイズは、1770*452ピクセルになります。</b></p>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group btn-upload-style">
                                                        <div class="col-xs-3 col-xs-offset-3">
                                                            <button type="submit" class="btn btn-primary search-button">登録</button>
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <a style="float: left" class="btn btn-danger search-button" href="{{ url('company/index') }}">戻る</a>
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
    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
