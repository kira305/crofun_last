@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('contract/index'))
    <style type="text/css">
    </style>
    <div class="row">
        <div class="col-md-12">
            <ul class="timeline">
                <li>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <div>
                                <div class="box-body">
                                    @php
                                        $projectId = !empty(request()->project_id) ? '?project_id='.request()->project_id : '';
                                        $clientId = !empty(request()->client_id) ? '?client_id='.request()->client_id : '';
                                        $fixed_company_id = session()->has('contract.company_id') ? session('contract.company_id') : Auth::user()->company_id;
                                    @endphp
                                    <form action="{{ url('contract/index'.$projectId.$clientId) }}" id="form" method="post">
                                        {{-- row 1 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">所属会社</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" id="company_id" name="company_id">
                                                            @foreach ($companies as $company)
                                                                <option @if ($fixed_company_id == $company->id) selected @endif value="{{ $company->id }}">
                                                                    {{ $company->abbreviate_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">申請本部</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" id="headquarter_id" name="headquarter_id">
                                                            <option value=""> </option>
                                                            @foreach ($headquarters as $headquarter)
                                                                <option class="headquarter_id"
                                                                    data-value="{{ $headquarter->company_id }}"
                                                                    @if (session('contract.headquarter_id') == $headquarter->id) selected @endif
                                                                    value="{{ $headquarter->id }}">{{ $headquarter->headquarters }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 2 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">申請部</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" id="department_id" name="department_id">
                                                            <option selected value=""></option>
                                                            @foreach ($departments as $department)
                                                                <option class="department_id"
                                                                    data-value="{{ $department->headquarter()->id }}"
                                                                    @if (session('contract.department_id') == $department->id) selected @endif
                                                                    value="{{ $department->id }}">{{ $department->department_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">申請グループ</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" id="group_id" name="group_id">
                                                            <option selected value=""></option>
                                                            @foreach ($groups as $group)
                                                                <option class="group_id"
                                                                    data-value="{{ $group->department()->id }}"
                                                                    @if (session('contract.group_id') == $group->id) selected @endif
                                                                    value="{{ $group->id }}">{{ $group->group_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 3 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">顧客コード</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <input type="text" value="{{ session('contract.client_code') }}"
                                                            name="client_code" id="client_code" class="form-control clear-text">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">顧客名カナ</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <input id="client_name_kana" name="client_name_kana" type="text" size="80%"
                                                            value="{{ session('contract.client_name_kana') }}"
                                                            class="form-control clear-text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 4 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">法人番号</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <input id="corporation_num"
                                                            value="{{ session('contract.corporation_num') }}"
                                                            name="corporation_num" type="text"
                                                            class="form-control clear-text">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">種類</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" name="contract_type" id="contract_type">
                                                            <option selected value=""></option>
                                                            @foreach ($contractTypes as $contractType)
                                                                <option @if (session('contract.contract_type') == $contractType->id) selected @endif data-company="{{ $contractType->company_id }}"
                                                                    value="{{ $contractType->id }}" @if($fixed_company_id != $contractType->company_id) hidden @endif  >
                                                                        {{ $contractType->type_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 5 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">プロジェクトコード</span>
                                                    </div>
                                                    <div class="search-item col-xs-7 col-md-8">
                                                        <input type="text" value="{{ session('contract.project_code') }}"
                                                            name="project_code" id="project_code" class="form-control clear-text">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">プロジェクト名</span>
                                                    </div>
                                                    <div class="search-item col-xs-7 col-md-8">
                                                        <input type="text" value="{{ session('contract.project_name') }}"
                                                            name="project_name" id="project_name" class="form-control clear-text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 6 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">参照可能部署</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" name="referenceable_department">
                                                            <option selected value=""></option>
                                                            @foreach ($departmentsRef as $refDepartment)
                                                                <option @if (session('contract.referenceable_department') == $refDepartment->id) selected @endif
                                                                    value="{{ $refDepartment->id }}">{{ $refDepartment->department_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">進捗状況</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" name="progress_status">
                                                            <option selected value=""></option>
                                                            @foreach ($progressStatusList as $progressStatus)
                                                                <option @if (session('contract.progress_status') == $progressStatus->status) selected @endif
                                                                    value="{{ $progressStatus->status }}">{{ $progressStatus->status }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 7 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">X-Point 申請番号</span>
                                                    </div>
                                                    <div class="search-item col-xs-7 col-md-8">
                                                        <input type="text" value="{{ session('contract.application_num') }}"
                                                            name="application_num" class="form-control clear-text">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-5 col-md-4">
                                                        <span class="">自動更新</span>
                                                    </div>
                                                    <div class="col-xs-7 col-md-8 search-item">
                                                        <select class="form-control clear-select" name="auto_update">
                                                            <option selected value=""></option>
                                                            <option value="true" @if (session('contract.auto_update') == 'true') selected @endif>あり</option>
                                                            <option value="false" @if (session('contract.auto_update') == 'false') selected @endif>なし</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 8 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-3">
                                                        <span class="">押印受付日</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.stamp_receipt_date_st') }}"
                                                            name="stamp_receipt_date_st" class="form-control datepicker clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="search-title col-xs-1">
                                                        <span class="">~</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.stamp_receipt_date_en') }}"
                                                            name="stamp_receipt_date_en" class="form-control clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamp_receipt_date_st') ? $errors->first('stamp_receipt_date_st') : '' }}
                                                    </span>
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamp_receipt_date_en') ? $errors->first('stamp_receipt_date_en') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-3">
                                                        <span class="">押印返却日</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.stamped_return_date_st') }}"
                                                            name="stamped_return_date_st" class="form-control datepicker clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="search-title col-xs-1">
                                                        <span class="">~</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.stamped_return_date_en') }}"
                                                            name="stamped_return_date_en" class="form-control clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamped_return_date_st') ? $errors->first('stamped_return_date_st') : '' }}
                                                    </span>
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamped_return_date_en') ? $errors->first('stamped_return_date_en') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 9 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-3">
                                                        <span class="">回収日</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.collection_date_st') }}"
                                                            name="collection_date_st" class="form-control datepicker clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="search-title col-xs-1">
                                                        <span class="">~</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.collection_date_en') }}"
                                                            name="collection_date_en" class="form-control clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('collection_date_st') ? $errors->first('collection_date_st') : '' }}
                                                    </span>
                                                    <span class="text-danger">
                                                        {{ $errors->has('collection_date_en') ? $errors->first('collection_date_en') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-3">
                                                        <span class="">契約締結日</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.contract_conclusion_date_st') }}"
                                                            name="contract_conclusion_date_st" class="form-control datepicker clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="search-title col-xs-1">
                                                        <span class="">~</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.contract_conclusion_date_en') }}"
                                                            name="contract_conclusion_date_en" class="form-control clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_conclusion_date_st') ? $errors->first('contract_conclusion_date_st') : '' }}
                                                    </span>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_conclusion_date_en') ? $errors->first('contract_conclusion_date_en') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 10 --}}
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-3">
                                                        <span class="">契約開始日</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.contract_start_date_st') }}"
                                                            name="contract_start_date_st" class="form-control datepicker clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="search-title col-xs-1">
                                                        <span class="">~</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.contract_start_date_en') }}"
                                                            name="contract_start_date_en" class="form-control clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_start_date_st') ? $errors->first('contract_start_date_st') : '' }}
                                                    </span>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_start_date_en') ? $errors->first('contract_start_date_en') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-md-12 col-lg-6 search-form">
                                                    <div class="search-title col-xs-3">
                                                        <span class="">契約終了日</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.contract_end_date_st') }}"
                                                            name="contract_end_date_st" class="form-control datepicker clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="search-title col-xs-1">
                                                        <span class="">~</span>
                                                    </div>
                                                    <div class="col-xs-4 search-item">
                                                        <input type="text" value="{{ session('contract.contract_end_date_en') }}"
                                                            name="contract_end_date_en" class="form-control clear-text datepicker"
                                                            autocomplete="off">
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_end_date_st') ? $errors->first('contract_end_date_st') : '' }}
                                                    </span>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_end_date_en') ? $errors->first('contract_end_date_en') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- end --}}
                                        @csrf
                                        <div class="col-lg-12 ">
                                            <div class="col-xs-3 col-xs-offset-3">
                                                <button type="submit" id="search"
                                                    class="search-button btn btn-primary btn-sm">検索</button>
                                            </div>
                                            <div class="col-xs-3">
                                                <button type="button" id="clear"
                                                    class="clear-button btn btn-default btn-sm">クリア</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="timeline-item">
                        <div class="timeline-body">
                            <div class="box-body">
                                <div class="col-lg-2 dis-flex ">
                                    <a class="btn btn-success" href="{{route('contract.getcsv', ['id' => 'multi'])}}">CSV出力</a>
                                </div>
                                <div class="row">
                                    <div class="col-lg-1">
                                        @if (Crofun::contract_index_return_button() == 1)
                                            <a href="{{ route('customer_edit', ['id' => request()->client_id]) }}">
                                                <button class="btn btn-warning btn-sm"> 戻る</button>
                                            </a>
                                        @endif
                                        @if (Crofun::contract_index_return_button() == 2)
                                            <a href="{{ route('customer_view', ['id' => request()->client_id]) }}">
                                                <button class="btn btn-warning btn-sm"> 戻る</button>
                                            </a>
                                        @endif
                                        @if (Crofun::contract_index_return_button() == 3)
                                            <a href="{{ route('edit_project', ['id' => request()->project_id]) }}">
                                                <button class="btn btn-warning btn-sm"> 戻る</button>
                                            </a>
                                        @endif
                                        @if (Crofun::contract_index_return_button() == 4)
                                            <a href="{{ route('view_project', ['id' => request()->project_id]) }}">
                                                <button class="btn btn-warning btn-sm"> 戻る</button>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-lg-offset-9">
                                        @paginate(['item'=>$contract]) @endpaginate
                                    </div>
                                </div>
                                <div class="row table-parent">
                                    <table id="contract_table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>顧客コード</th>
                                                <th>顧客名</th>
                                                <th>種類</th>
                                                <th>進捗状況</th>
                                                <th>X-point申請番号</th>
                                                <th>押印受付日</th>
                                                <th>押印返却日</th>
                                                <th>回収日</th>
                                                <th>契約締結日</th>
                                                <th>契約開始日</th>
                                                <th>契約終了日</th>
                                                <th>自動更新</th>
                                                <th>次回契約終了日</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contract as $contract)
                                                <tr>
                                                    <td>
                                                        @php($contractAction = Crofun::getPermissionContract($contract, Auth::user()))
                                                        @if ($contractAction != null)
                                                            @if ($contractAction->can_edit)
                                                                <a rel="noopener noreferrer" href="{{ route('contract.edit', ['id' => $contract->id]) }}">
                                                                    <button style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                                </a>
                                                            @elseif($contractAction->only_pj_refer_departments_edit)
                                                                <a rel="noopener noreferrer" href="{{ route('contract.view', ['id' => $contract->id]) }}">
                                                                    <button style="float: left;" class="btn btn-success btn-sm">参照</button>
                                                                </a>
                                                            @elseif($contractAction->can_view)
                                                                <a rel="noopener noreferrer" href="{{ route('contract.view', ['id' => $contract->id]) }}">
                                                                    <button style="float: left;" class="btn btn-primary btn-sm">参照</button>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (empty($contract->customer->client_code_main))
                                                            {{ $contract->customer->client_code }}
                                                        @else
                                                            {{ $contract->customer->client_code_main }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $contract->customer->client_name }}</td>
                                                    <td>{{ $contract->getContractTypeName() }}</td>
                                                    <td>{{ $contract->progress_status }}</td>
                                                    <td>{{ $contract->application_num }}</td>
                                                    <td>{{ !empty($contract->stamp_receipt_date) ? date('Y/m/d', strtotime($contract->stamp_receipt_date)) : '' }}</td>
                                                    <td>{{ !empty($contract->stamped_return_date) ? date('Y/m/d', strtotime($contract->stamped_return_date)) : '' }}</td>
                                                    <td>{{ !empty($contract->collection_date) ? date('Y/m/d', strtotime($contract->collection_date)) : '' }}</td>
                                                    <td>{{ !empty($contract->contract_conclusion_date) ?date('Y/m/d', strtotime($contract->contract_conclusion_date)) : '' }}</td>
                                                    <td>{{ !empty($contract->contract_start_date) ? date('Y/m/d', strtotime($contract->contract_start_date)) : '' }}</td>
                                                    <td>{{ !empty($contract->contract_end_date) ? date('Y/m/d', strtotime($contract->contract_end_date)) : '' }}</td>
                                                    <td>{{ $contract->auto_update == 'true' ? 'あり' : 'なし' }}</td>
                                                    <td>{{ !empty($contract->getNextUpdatesDate()) ? date('Y/m/d', strtotime($contract->getNextUpdatesDate())) : '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </li>
            </ul>
        </div>
    </div>
    <script type="text/javascript">
        $(document).on('click', '#clear', function() {
            //1ページに移動
            $('.clear-select').prop('selectedIndex', 0);
            $('.clear-text').val('');
            $('#form').submit();
        });

        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
        });

        $(document).ready(function() {
            $("#company_id").change(function() {
                var companyId = $(this).val();
                $('#contract_type > option').each(function() {
                    var optionId = $(this).attr('data-company');
                    if (typeof optionId !== 'undefined'){
                        if(optionId == companyId){
                            $(this).removeAttr("hidden");
                        }else{
                            $(this).prop("hidden", true);
                        }
                    }else {
                        $(this).prop('selected', true);
                    }
                });
            });
        });

    </script>
@endsection
