@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('department/create'))
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
                                    <form id="create_user" method="post" action="{{ url('department/create') }}" enctype="multipart/form-data">
                                        @csrf
                                        {{-- row 1 --}}
                                        <div class="row">
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>所属会社</label>
                                                        <select class="form-control" id="company_id" name="company_id">
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
                                                        <label>事業本部名</label>
                                                        <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                            <option> </option>
                                                            @foreach ($headquarters as $headquarter)
                                                                <option class="headquarter_id"
                                                                    {{ old('headquarter_id') == $headquarter->id ? 'selected' : '' }}
                                                                    data-value="{{ $headquarter->company_id }}"
                                                                    value="{{ $headquarter->id }}">
                                                                    {{ $headquarter->headquarters }}
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
                                                        <label>表示コード</label>
                                                        <input type="text" name="department_code" value="{{ old('department_code') }}" class="form-control">
                                                        <div class="text-danger">
                                                            {{ $errors->has('department_code') ? $errors->first('department_code') : '' }}
                                                        </div>
                                                        <div class="text-danger">
                                                            {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>部署コード</label>
                                                        <input type="text" name="department_list_code" value="{{ old('department_list_code') }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('department_list_code') ? $errors->first('department_list_code') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>部署名</label>
                                                        <input type="text" name="department_name" value="{{ old('department_name') }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('department_name') ? $errors->first('department_name') : '' }}
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
                                                            @php($page = session()->has('department.page') ? '?page='.session('department.page') : '')
                                                            <a style="float: left" class="btn btn-danger search-button" href="{{ url('department/index'.$page) }}">戻る</a>
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
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
    </script>
    <script src="{{ asset('select/icontains.js') }}"></script>
    <script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
