@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('headquarter/create'))
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
                                    <form id="create_user" method="post" action="{{ url('headquarter/create') }}" enctype="multipart/form-data">
                                        @csrf
                                        {{-- row 1 --}}
                                        <div class="row">
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>所属会社</label>
                                                        <select class="form-control" name="company_id">
                                                            @foreach ($companies as $company)
                                                                <option
                                                                    {{ old('company_id') == $company->id ? 'selected' : '' }}
                                                                    value="{{ $company->id }}">{{ $company->abbreviate_name }}
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
                                                        <label>表示コード</label>
                                                        <input type="text" name="headquarters_code" value="{{ old('headquarters_code') }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('headquarters_code') ? $errors->first('headquarters_code') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>事業本部コード</label>
                                                        <input type="text" name="headquarter_list_code" value="{{ old('headquarter_list_code') }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('headquarter_list_code') ? $errors->first('headquarter_list_code') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>事業本部名</label>
                                                        <input type="text" name="headquarters" value="{{ old('headquarters') }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('headquarters') ? $errors->first('headquarters') : '' }}
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
                                                            <a style="float: left" class="btn btn-danger search-button" href="{{ url('headquarter/index') }}">戻る</a>
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
