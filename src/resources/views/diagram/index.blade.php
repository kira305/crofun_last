@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('tree/index'))
    <style>
        .box-height {
            height: 34px;
        }
        .pading-title {
            padding-top: 8px;
            text-align: right
        }
        .font-size-fix {
            font-size: 12px !important;
        }
    </style>
    <script type="text/javascript" src="{{ asset('js/diagram_document_ready.js') }}"></script>
    @include('layouts.confirm_js')
    <div class="row">
        <div class="col-md-12">
            <ul class="timeline">
                <li>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <div>
                                <div class="box-body">
                                    <form id="diagram" method="POST" action="{{ url('tree/index') }}">
                                    @csrf
                                    {{-- row 1 --}}
                                    <div class="col-lg-8 col-lg-offset-2 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">出力期間</span>
                                                </div>
                                                <div class="col-xs-3 search-item">
                                                    <input class="form-control" autocomplete="off" maxlength="10" id="start_date" type="" name="" value="">
                                                </div>
                                                <div class="search-title col-xs-1">
                                                    <span class="">~</span>
                                                </div>
                                                <div class="col-xs-3 search-item">
                                                    <input class="form-control " autocomplete="off" maxlength="10" id="end_date" type="" name="" value="">
                                                </div>
                                                <div class="col-xs-2 p-r-0 p-l-5">
                                                    <button class ="btn btn-primary w-full" type="button"  onclick="diagram()" >出力</button>
                                                </div>
                                                <span id="start_date_error" class="text-danger date-error">
                                                    {{ $errors->has('start_date') ? $errors->first('start_date') : '' }}
                                                </span>
                                                <span class="text-danger date-error end_date_error">
                                                    {{ $errors->has('end_date') ? $errors->first('end_date') : '' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="col-lg-8 col-lg-offset-2 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach ($companies as $company)
                                                            <option @if (session('company_id_d') != null)
                                                                {{ session('company_id_d') == $company->id ? 'selected' : '' }}
                                                            @else
                                                                {{ Auth::user()->company_id == $company->id ? 'selected' : '' }}
                                                        @endif
                                                        value="{{ $company->id }}">{{ $company->abbreviate_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">表示年月日</span>
                                                </div>
                                                <div class="col-xs-7 search-item">
                                                    <input class="form-control search_date" value="{{ old('search_date') }}" autocomplete="off" type='text' name="search_date" id='datepicker'>
                                                </div>
                                                <div class="col-xs-2 p-r-0 p-l-5">
                                                    <button class="btn btn-primary w-full" id="search_date" type="button">表示</button>
                                                </div>
                                                <div class="text-danger">
                                                    {{ $errors->has('search_date') ? $errors->first('search_date') : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <div class="box-body">
                                    <div class="row">
                                        <div class="col-xs-2 p-r-0 p-l-0 m-t-20">
                                            <button class ="btn btn-primary" type="button" id="clear">クリア</button>
                                        </div>
                                        @paginate(['item'=> $diagrams]) @endpaginate
                                    </div>
                                    <div class="row table-parent">
                                        <table class="table table-striped table-bordered table-hover font-size-fix">
                                            <thead class="thead-table" style="background-color: #20B2AA">
                                                <tr>
                                                    <th class="title">事業本部</th>
                                                    <th class="title">部署</th>
                                                    <th class="title">グループ</th>
                                                    <th class="title">販管費</th>
                                                    <th class="title">原価コード</th>
                                                    <th class="title">集計コード</th>
                                                    <th class="title">PJコード</th>
                                                </tr>
                                                <tr>
                                                    <th class="col-md-1">
                                                        <select class="form-control select-sort" name="headquarter_id"
                                                            id="headquarter_id_d">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['headquarter_id'] as $key => $headquarter)
                                                                <option class="headquarter_class"
                                                                    {{ session('headquarter_id_tr') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $headquarter }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th class="col-md-1">
                                                        <select class="search form-control select-sort" id="department_id_d"
                                                            name="department_id">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['department_id'] as $key => $department)
                                                                <option class="department_class"
                                                                    {{ session('department_id_tr') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $department }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th class="col-md-1">
                                                        <select class="search form-control select-sort" name="group_id" id="group_id_d">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['group_id'] as $key => $group)
                                                                <option class="group_class"
                                                                    {{ session('group_id_tr') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $group }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th class="col-md-1">
                                                        <select class="form-control select-sort" id="selling" name="hanka">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['hanka'] as $key => $hanka)
                                                                <option class=""
                                                                    {{ session('hanka') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $hanka }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th class="col-md-1">
                                                        <select class="form-control select-sort" id="cost" name="genka">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['genka'] as $key => $genka)
                                                                <option class=""
                                                                    {{ session('genka') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $genka }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th class="col-md-1">
                                                        <select class="search form-control select-sort" id="pj_gr_code"
                                                            name="pj_gr_code">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['pj_gr_code'] as $key => $projectGroup)
                                                                <option class="pj_gr_code"
                                                                    {{ session('pj_gr_code') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $projectGroup }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                    <th class="col-md-2">
                                                        <select class="search form-control select-sort" id="pj_code" name="pj_code">
                                                            <option value="" data-id=""> </option>
                                                            @foreach ($listTitleFilter['pj_code'] as $key => $project)
                                                                <option class="pj_code"
                                                                    {{ session('pj_code') == $key ? 'selected' : '' }}
                                                                    value="{{ $key }}">
                                                                    @if (!empty($key))
                                                                        {{ $key }}:{{ $project }}
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($diagrams as $diagram)
                                                    <tr>
                                                        <td>{{ $diagram->headquarters_code }}:{{ $diagram->headquarters }}
                                                        </td>
                                                        <td>
                                                            @if ($diagram->department_code != null && $diagram->department_code != null)
                                                                {{ $diagram->department_code }}:{{ $diagram->department_name }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($diagram->group_code != null && $diagram->group_name != null)
                                                                {{ $diagram->group_code }}:{{ $diagram->group_name }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($diagram->sales_management_code != null && $diagram->sales_management != null)
                                                                {{ $diagram->sales_management_code }}:{{ $diagram->sales_management }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($diagram->cost_code != null && $diagram->cost_name != null)
                                                                {{ $diagram->cost_code }}:{{ $diagram->cost_name }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($diagram->project_grp_code != null && $diagram->project_grp_name != null)
                                                                {{ $diagram->project_grp_code }}:{{ $diagram->project_grp_name }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($diagram->project_code != null && $diagram->project_name != null)
                                                                {{ $diagram->project_code }}:{{ $diagram->project_name }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
        $(document).ready(function() {

             // $( ".select-sort" ).each(function() {
            //     var selectList = jQuery(this).find("option");
            //     selectList.sort(function(a,b){
            //         a = a.value;
            //         b = b.value;
            //         return a-b;
            //     });
            //     $(this).eq(0).html(selectList);
            // });

            $("#search_date").click(function(event) {
                removeValue();
                $('#diagram').submit();
            });

            $("#clear").click(function(event) {
                removeValue();
                $('#diagram').submit();
            });
        });
        $(document).on('change', '#company_id', function() {
            $('#headquarter_id_d').prop('selectedIndex', 0);
            $('#department_id_d').prop('selectedIndex', 0);
            $('#group_id_d').prop('selectedIndex', 0);
            $('#selling').prop('selectedIndex', 0);
            $('#cost').prop('selectedIndex', 0);
            $('#pj_gr_code').prop('selectedIndex', 0);
            $('#pj_code').prop('selectedIndex', 0);

            removeValue();
            $('#diagram').submit();
        });


        $(document).on('change', '#headquarter_id_d', function() {
            var headquarter_code = $('#headquarter_id_d').find('option:selected').data('id');
            // removeSearchCondition('headquarter_id_d');
            $('#diagram').submit();
        });

        $(document).on('change', '#department_id_d', function() {
            // removeSearchCondition('department_id_d');
            $('#diagram').submit();
        });

        $(document).on('change', '#group_id_d', function() {
            // removeSearchCondition('group_id_d');
            $('#diagram').submit();
        });

        $(document).on('change', '#selling', function() {
            // removeSearchCondition('selling');
            $('#diagram').submit();
        });

        $(document).on('change', '#cost', function() {
            // removeremoveSearchConditionValue('cost');
            $('#diagram').submit();
        });

        $(document).on('change', '#pj_gr_code', function() {
            // removeSearchCondition('pj_gr_code');
            $('#diagram').submit();
        });


        $(document).on('change', '#pj_code', function() {
            // removeSearchCondition('pj_code');
            $('#diagram').submit();
        });

        function removeValue(other = null){

            if(other != 'headquarter_id_d') $('#headquarter_id_d').val('');
            if(other != 'department_id_d') $('#department_id_d').val('');
            if(other != 'group_id_d') $('#group_id_d').val('');
            if(other != 'selling') $('#selling').val('');
            if(other != 'cost') $('#cost').val('');
            if(other != 'pj_gr_code') $('#pj_gr_code').val('');
            if(other != 'pj_code') $('#pj_code').val('');
            return;
        }

        function validateForm(input){
            var reg = /(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])/;
            if (input.match(reg)) {
                return true;
            }else{
                return false;
            }
        }

        function diagram() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var company_id = $('#company_id').val();
            var startDateFormat =  validateForm(start_date);
            var endDateFormat =  validateForm(end_date);
            if (!startDateFormat || !endDateFormat) {
                $("#start_date_error").text('yyyy/mm/ddにて入力して下さい。');
                return;
            }else{
                $("#start_date_error").text('');
            }
            $('.date-error').text('');
            if (start_date == '' && end_date == '') {
                $.alert({
                    title: 'メッセージ',
                    content: '出力日を入力してください！',
                });
                return;
            }

            if (start_date == end_date) {
                document.location.href = "/tree/diagram2?start_date=" +
                    start_date + "&end_date=" + end_date +
                    "&company_id=" + company_id;
                return;
            }

            if (start_date == "" && end_date != "") {
                $.alert({
                    title: 'メッセージ',
                    content: '出力開始日を入力してください！',
                });
                return;
            }

            if (start_date != "" && end_date == "") {
                document.location.href = "/tree/diagram?start_date=" +
                    start_date + "&end_date=" + end_date +
                    "&company_id=" + company_id;
                return;
            }


            if (start_date > end_date) {
                $.alert({
                    title: 'メッセージ',
                    content: '出力日開始は出力日終了より大きいので出力できません！',
                });
                return;
            }

            document.location.href = "/tree/diagram?start_date=" +
                start_date + "&end_date=" + end_date +
                "&company_id=" + company_id;
        }

        $(document).ready(function() {
            var company_id = $('#company_id').val();
            $(".genka").each(function() {
                $(this).show();
                if ($(this).attr('data-id') !== company_id) {
                    $(this).remove();
                }
            });
            $(".hanka").each(function() {
                $(this).show();
                if ($(this).attr('data-id') !== company_id) {
                    $(this).remove();
                }
            });
        });

    </script>
@endsection
