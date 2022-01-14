@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('pca/uplode'))
    <script type="text/javascript" src="{{ asset('js/MonthPicker.js') }}"></script>
    <div class="row" id="databinding">
        <div class="col-md-12">
            <ul class="timeline">
                <li>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <div class="box-body">
                                @if ($message = Session::get('message'))
                                    <p class="" style="text-align: center;color: green">{{ $message }}</p>
                                @endif
                                <p hidden id="server_err" style="text-align: center;color: red">
                                    {{ trans('message.save_fail') }}</p>
                                <form id="upload" method="post" action="{{ url('pca/upload') }}"
                                    enctype="multipart/form-data">
                                    {{-- row 1 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">会社コード</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach ($companies as $company)
                                                            <option
                                                                {{ old('company_id') == $company->id ? 'selected' : '' }}
                                                                value="{{ $company->id }}">{{ $company->abbreviate_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if ($errors->has('company_id'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('company_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">取込データ</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="import_type" name="import_type">
                                                        <option {{ old('import_type') == 1 ? 'selected' : '' }} value="1">売上
                                                        </option>
                                                        <option {{ old('import_type') == 2 ? 'selected' : '' }} value="2">
                                                            売掛金残
                                                        </option>
                                                    </select>
                                                </div>
                                                @if ($errors->has('import_type'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('import_type') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">取得年月</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <input id="get_time" value="{{ old('get_time') }}" autocomplete="off"
                                                        name="get_time" type="text" size="70" class="form-control">
                                                </div>
                                                @if ($errors->has('get_time'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('get_time') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="col-xs-3">
                                                    <span class="btn btn-primary btn-file">
                                                        データ取込
                                                        <input type="file" id="input_file" name="file_data">
                                                    </span>
                                                </div>
                                                @if ($errors->has('file_data'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('file_data') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @csrf
                                    {{-- end --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="timeline-item">
                        @if ($result == 0)
                            <div id="upload_status" class="box-body">
                                {{-- row 1 --}}
                                <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                    <div class="row search-row">
                                        <div class="col-md-12 col-lg-8 search-form">
                                            <div class="search-title col-xs-3">
                                                <span class="">取込データ</span>
                                            </div>
                                            <div class="col-xs-9 search-item">
                                                <span id="import_type_1" class="form-control">{{ $type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row 2 --}}
                                <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                    <div class="row search-row">
                                        <div class="col-md-12 col-lg-8 search-form">
                                            <div class="search-title col-xs-3">
                                                <span class="">取込ステータス</span>
                                            </div>
                                            <div class="col-xs-9 search-item">
                                                <span id="import_status" class="form-control">{{ $status }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row 3 --}}
                                <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                    <div class="row search-row">
                                        <div class="col-md-12 col-lg-8 search-form">
                                            <div class="search-title col-xs-3">
                                                <span class="">データ件数</span>
                                            </div>
                                            <div class="col-xs-9 search-item">
                                                <span id="total_data" class="form-control">{{ $data_total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row 4 --}}
                                <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                    <div class="row search-row">
                                        <div class="col-md-12 col-lg-8 search-form">
                                            <div class="search-title col-xs-3">
                                                <span class="">ファイル名</span>
                                            </div>
                                            <div class="col-xs-9 search-item">
                                                <span id="file_name" class="form-control">{{ $file_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row 5 --}}
                                <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                    <div class="row search-row">
                                        <div class="col-md-12 col-lg-8 search-form">
                                            <div class="search-title col-xs-3">
                                                <span class="">取込日</span>
                                            </div>
                                            <div class="col-xs-9 search-item">
                                                <span class="form-control" id="get_time_1">
                                                    {{ date('Y年m月d日', strtotime($time)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- row 6 --}}
                                @if (isset($file_name_err))
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-8 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">エラーファイル</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <span class="form-control" id="err_file_name">{{ $file_name_err }}</span>
                                                    <input type="hidden" id="import_id" value="{{ $import_id }}">
                                                </div>
                                                <button id="csv" class="btn btn-primary">Dowload</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- end --}}
                            </div>
                        @endif
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <script type="text/javascript">
        $("#get_time").MonthPicker({
            Button: false,
            MonthFormat: 'yy/mm'
        });
        $(document).ready(function() {
            $("#input_file").change(function() {
                $("#upload").submit();
            });
            $("#csv").click(function(event) {
                var import_id = $("#import_id").val();
                document.location.href = "/err/dowload?import_id=" + import_id;
            });
        });
    </script>
    <script src="{{ asset('select/icontains.js') }}"></script>
    <script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
