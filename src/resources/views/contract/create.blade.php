@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('contract/create'))
@include('layouts.confirm_js')
<script src="{{ asset('js/contract.js') }}"></script>
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
<style>
.table-responsive {
    max-height:175px;
}
</style>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline">
                        <div>
                            <div class="box-body p-20">
                                @php
                                    $auto_update_act = null;
                                    if(isset($contract)){
                                        $auto_update_act = $contract->auto_update;
                                    }elseif(!empty(session('contract_create.auto_update'))){
                                        $auto_update_act = session('contract_create.auto_update');
                                    }
                                    $refid = isset(request()->refid) ? '&refid='.request()->refid : '';
                                    $contractId = session('contract_create.contract_id');
                                    $canUpdate = Auth::user()->can('update','App\Customer_MST');
                                    $canView =  Auth::user()->can('view','App\Customer_MST')
                                @endphp
                                <form id="create_contract" method="post" action="{{ url('contract/create'.'?client_id='.$customer->id.$refid) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="company_id" name="company_id" value="{{ $customer->company_id }}">
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>?????????ID</label>
                                                    <input disabled type="text" name="id" value="{{ $contractId }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('id') ? $errors->first('id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>????????????</label>
                                                    <input disabled type="text" name="progress_status" value="{{ session('contract_create.progress_status') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('progress_status') ? $errors->first('progress_status') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>???????????????</label>
                                                    @if (empty($customer->client_code_main))
                                                        <input disabled type="text" value="{{ $customer->client_code }}" class="form-control">
                                                    @else
                                                        <input disabled type="text" name="client_code" value="{{ $customer->client_code_main }}" class="form-control">
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_code_main') ? $errors->first('client_code_main') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text"></span>?????????</label>
                                                    <input disabled type="text" name="client_name" value="{{ $customer->getCustomerName()  }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name') ? $errors->first('client_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>??????????????????</label>
                                                    <div class="table-responsive my-scrollbar">
                                                        <table id="contract_table" class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>??????</th>
                                                                    <th>?????????</th>
                                                                    <th>?????????????????????</th>
                                                                    <th>???????????????</th>
                                                                    <th>??????????????????</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($customer->project()->get() as $project)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="checkbox" class="checkbox-center" name="project_id[{{$project->id}}]" value="{{ $project->id }}"
                                                                            @if (isset($contract->project_id[$project->id]) && $contract->project_id[$project->id] == $project->id)
                                                                                checked
                                                                            @endif
                                                                            @if (!empty(session('contract_create.project_id.'.$project->id) && session('contract_create.project_id.'.$project->id) == $project->id))
                                                                                checked
                                                                            @endif>
                                                                        </td>
                                                                        <td>{{ $project->project_code }}</td>
                                                                        <td>{{ $project->project_name }}</td>
                                                                        <td>{{ $project->status ? '?????????' : '????????????'}}</td>
                                                                        <td>{{ $project->group->group_name }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>??????????????????</label>
                                                    <div class="table-responsive my-scrollbar">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>??????</th>
                                                                    <th>??????</th>
                                                                    <th>??????</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($departments as $department)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="checkbox" class="checkbox-center" name="referenceable_department[{{ $department->id }}]" value="{{ $department->id }}"
                                                                            @if (isset($contract->referenceable_department[$department->id]) && $contract->referenceable_department[$department->id] == $department->id)
                                                                                checked
                                                                            @endif
                                                                            @if (!empty(session('contract_create.referenceable_department.'.$department->id) && session('contract_create.referenceable_department.'.$department->id) == $department->id))
                                                                                checked
                                                                            @endif>
                                                                        </td>
                                                                        <td>{{ $department->headquarter()->headquarters }}</td>
                                                                        <td>{{ $department->department_name }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>??????</label>
                                                    <select class="form-control" id="contract_type" name="contract_type">
                                                        @php($contract_type = isset($contract) ? $contract->contract_type : session('contract_create.contract_type'))
                                                        @foreach ($contractTypes as $contractType)
                                                            <option @if ($contract_type == $contractType->id) selected @endif
                                                                value="{{ $contractType->id }}">{{ $contractType->type_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_type') ? $errors->first('contract_type') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>??????????????????</label>
                                                    <select class="form-control" id="contract_completed" name="contract_completed">
                                                        @php($contract_completed = isset($contract) ? $contract->contract_completed : session('contract_create.contract_completed'))
                                                        <option></option>
                                                        <option value="2" @if ($contract_completed == 2) selected @endif>????????????????????????</option>
                                                        <option value="1" @if ($contract_completed == 1) selected @endif>????????????????????????</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_completed') ? $errors->first('contract_completed') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 5--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>????????????</label>
                                                    <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                        <option></option>
                                                        @php($headquarter_id = isset($contract) ? $contract->headquarter_id : session('contract_create.headquarter_id'))
                                                        @foreach ($headquarters as $headquarter)
                                                            <option class="headquarter_id" @if ($headquarter_id == $headquarter->id) selected @endif
                                                                data-value="{{ $headquarter->company_id }}"
                                                                value="{{ $headquarter->id }}">{{ $headquarter->headquarters }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>?????????</label>
                                                    <select class="form-control" id="department_id" name="department_id">
                                                        <option></option>
                                                        @php($department_id = isset($contract) ? $contract->department_id : session('contract_create.department_id'))
                                                        @foreach ($departments as $department)
                                                            <option class="department_id" @if ($department_id == $department->id) selected @endif
                                                                data-value="{{ $department->headquarter()->id }}"
                                                                value="{{ $department->id }}">{{ $department->department_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 6--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>??????????????????????????????</label>
                                                    <select class="form-control" id="group_id" name="group_id">
                                                        <option></option>
                                                        @php($group_id = isset($contract) ? $contract->group_id : session('contract_create.group_id'))
                                                        @foreach ($groups as $group)
                                                            <option class="group_id" @if ($group_id == $group->id) selected @endif
                                                                data-value="{{ $group->department()->id }}"
                                                                value="{{ $group->id }}">{{ $group->group_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>?????????</label>
                                                    @php($application_user_name = isset($contract) ? $contract->application_user_name : session('contract_create.application_user_name'))
                                                    <input type="text" name="application_user_name" maxlength="20" value="{{ $application_user_name }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('application_user_name') ? $errors->first('application_user_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 6.1--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>X-Point ????????????</label>
                                                    @php($application_num = isset($contract) ? $contract->application_num : session('contract_create.application_num'))
                                                    <input type="text" maxlength="20" name="application_num" value="{{ $application_num }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('application_num') ? $errors->first('application_num') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 7--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;??????&nbsp;</span>???????????????</label>
                                                    @php($stamp_receipt_date = isset($contract) ? $contract->stamp_receipt_date : session('contract_create.stamp_receipt_date'))
                                                    <input id="stamp_receipt_date" name="stamp_receipt_date" value="{{ $stamp_receipt_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamp_receipt_date') ? $errors->first('stamp_receipt_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>???????????????</label>
                                                    @php($stamped_return_date = isset($contract) ? $contract->stamped_return_date : session('contract_create.stamped_return_date'))
                                                    <input id="stamped_return_date" name="stamped_return_date" value="{{ $stamped_return_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamped_return_date') ? $errors->first('stamped_return_date') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 8--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>?????????</label>
                                                    @php($collection_date = isset($contract) ? $contract->collection_date : session('contract_create.collection_date'))
                                                    <input id="collection_date" name="collection_date" value="{{ $collection_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('collection_date') ? $errors->first('collection_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>???????????????</label>
                                                    @php($contract_conclusion_date = isset($contract) ? $contract->contract_conclusion_date : session('contract_create.contract_conclusion_date'))
                                                    <input id="contract_conclusion_date" name="contract_conclusion_date" value="{{ $contract_conclusion_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_conclusion_date') ? $errors->first('contract_conclusion_date') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 9--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>???????????????</label>
                                                    <input id="contract_start_date" name="contract_start_date" value="{{ session('contract_create.contract_start_date') }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_start_date') ? $errors->first('contract_start_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>???????????????</label>
                                                    <input id="contract_end_date" name="contract_end_date" value="{{ session('contract_create.contract_end_date') }}"
                                                    class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_end_date') ? $errors->first('contract_end_date') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 10--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>?????????????????????</label>
                                                    <div class="input-group">
                                                        @php($check_updates_deadline = isset($contract) ? $contract->getCheckUpdatesDeadline() : session('contract_create.check_updates_deadline'))
                                                        <input type="text" id="check_updates_deadline" name="check_updates_deadline" value="{{ $check_updates_deadline }}" class="form-control uintTextBox" maxlength="3">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">??????</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 11--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>????????????</label>
                                                    @php($auto_update = isset($contract) ? $contract->auto_update : session('contract_create.auto_update'))
                                                    <select class="form-control" id="auto_update" name="auto_update">
                                                        <option value="false" @if ($auto_update == "false") selected @endif>??????</option>
                                                        <option value="true" @if ($auto_update == "true") selected @endif>??????</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>???????????????</label>
                                                    <div class="input-group">
                                                        <input type="text" id="contract_span" {{$auto_update_act == "false" ? 'disabled' : ''}} name="contract_span"
                                                        @php($contract_span = isset($contract) ? $contract->contract_span : session('contract_create.contract_span'))
                                                        value="{{ $contract_span }}" class="form-control uintTextBox" maxlength="3">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">??????</span>
                                                        </div>
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_span') ? $errors->first('contract_span') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 12--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>??????</label>
                                                    @php($note = isset($contract) ? $contract->note : session('contract_create.note'))
                                                    <textarea class="form-control" rows="3" name="note">{{ $note }}</textarea>
                                                    <span class="text-danger">
                                                        {{ $errors->has('note') ? $errors->first('note') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 13 --}}
                                    <div class="row">
                                        @if (empty($contractId) || !isset($contractFiles) || !isset($newContract))
                                        <div class="col-lg-10 col-lg-12 col-lg-offset-10 m-b-10" >
                                            <button type="button" id="btn-add-contract" class="btn btn-primary">??????</button>
                                        </div>
                                        @endif
                                        <div class="table-parent col-lg-10 col-lg-12 col-lg-offset-1 ">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>?????????????????????</th>
                                                        <th>UP</th>
                                                        <th>???????????????</th>
                                                        <th>??????</th>
                                                        <th>UP???</th>
                                                        <th>??????</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (!empty($contractId) && isset($contractFiles) && isset($newContract))
                                                        {{-- created --}}
                                                        @php($checkMain = isset($contractFiles[0]) ? $contractFiles[0]->file_type : null)
                                                        @php($isMain = !empty($checkMain) && $checkMain == 1 ? true : false)
                                                        <tr>
                                                            <td>
                                                                ?????????
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-6">
                                                                    <div>
                                                                        @if ($isMain)
                                                                            <a target="_blank" class="btn btn-info" rel="noopener noreferrer" href="{{ route('contract_display', ['contract_file_id' => $contractFiles[0]->id, 'contract_id' => $newContract->id]) }}">
                                                                                ??????
                                                                            </a>
                                                                        @else
                                                                            <div class="input-group">
                                                                                <span class="input-group-btn">
                                                                                    <span class="btn btn-success btn-file border-select-file">
                                                                                        ??????<input type="file" class="contract_file_select" accept="application/pdf" data-target="contract_file_select_1" name="contract_file[1]">
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="id-change">
                                                                    <input type="text" id="" value="{{$isMain ?  $contractFiles[0]->file_original_name : ''}}" class="form-control contract_file_show" data-target="contract_file_select_1" readonly>
                                                                    <a target="_blank" class="btn btn-info contract-view-dis-none local-view" data-target="contract_file_select_1" href="#">
                                                                        ??????
                                                                    </a>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control" rows="1" name="file_note[1]">{{$isMain ? $contractFiles[0]->note : ''}}</textarea>
                                                            </td>
                                                            <td><label style="margin-bottom: 0px" data-target="contract_file_select_1">{{$isMain ? $contractFiles[0]->updated_at : ''}}</label></td>
                                                            <td></td>
                                                        </tr>
                                                        @foreach ($contractFiles as $key=>$contractFile)
                                                            @if (!empty($contractFile->file_type) && $contractFile->file_type == 1)
                                                                @continue
                                                            @endif
                                                            @if ($isMain)
                                                                @php($key = $key +1)
                                                            @else
                                                                @php($key = $key +2)
                                                            @endif
                                                            <tr>
                                                                <td>
                                                                    ??????
                                                                </td>
                                                                <td>
                                                                    <div class="col-lg-6">
                                                                        <div>
                                                                            <a target="_blank" class="btn btn-info" rel="noopener noreferrer" href="{{ route('contract_display', ['contract_file_id' => $contractFile->id, 'contract_id' => $newContract->id]) }}">
                                                                                ??????
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="" value="{{ $contractFile->file_original_name }}" class="form-control contract_file_show" data-target="contract_file_select_{{$key}}" readonly>
                                                                </td>
                                                                <td>
                                                                    <textarea class="form-control"  rows="1" name="file_note[{{$key}}]">{{ $contractFile->note }}</textarea>
                                                                </td>
                                                                <td><label style="margin-bottom: 0px" data-target="contract_file_select_{{$key}}">{{ $contractFile->updated_at }}</label></td>
                                                                <td></td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- end --}}
                                                    @else
                                                        {{-- creating --}}
                                                        <tr>
                                                            <td>
                                                                ?????????
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-6">
                                                                    <div class="dis-flex">
                                                                        <div class="input-group m-r-10">
                                                                            <span class="input-group-btn">
                                                                                <span class="btn btn-success btn-file border-select-file">
                                                                                    ??????<input type="file" class="contract_file_select" value="{{ session('contract_create.contract_file.1')}}" accept="application/pdf" data-target="contract_file_select_1" name="contract_file[1]">
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="id-change">
                                                                    <input type="text" id="" value="" class="form-control contract_file_show" data-target="contract_file_select_1" readonly>
                                                                    <a target="_blank" class="btn btn-info contract-view-dis-none local-view" data-target="contract_file_select_1" href="#">
                                                                        ??????
                                                                    </a>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control" rows="1" name="file_note[1]"></textarea>
                                                            </td>
                                                            <td><label style="margin-bottom: 0px" data-target="contract_file_select_1"></label></td>
                                                            <td><button type="button" class="btn btn-warning btn-file clear-file" data-target="contract_file_select_1">?????????</button></td>
                                                        </tr>
                                                        @for ($i = 2; $i < 11; $i++)
                                                        <tr class="contract-dis-none">
                                                            <td>
                                                                {{ $i==1 ? '?????????' : '??????' }}
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-6">
                                                                    <div class="dis-flex">
                                                                        <div class="input-group m-r-10">
                                                                            <span class="input-group-btn">
                                                                                <span class="btn btn-success btn-file border-select-file">
                                                                                    ?????? <input type="file" class="contract_file_select" accept="application/pdf" data-target="contract_file_select_{{$i}}" name="contract_file[{{$i}}]">
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="id-change">
                                                                    <input type="text" id="" class="form-control contract_file_show" data-target="contract_file_select_{{$i}}" readonly>
                                                                    <a target="_blank" class="btn btn-info contract-view-dis-none local-view" data-target="contract_file_select_{{$i}}" href="#">
                                                                        ??????
                                                                    </a>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control note-area" rows="1" data-target="contract_file_select_{{$i}}" name="file_note[{{$i}}]"></textarea>
                                                            </td>
                                                            <td><label style="margin-bottom: 0px" data-target="contract_file_select_{{$i}}"></label></td>
                                                            <td><button type="button" class="btn btn-warning btn-file clear-file" data-target="contract_file_select_{{$i}}">?????????</button></td>
                                                        </tr>
                                                        @endfor
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    <div class="row p-b-20">
                                        <div class="col-lg-12 ">
                                            <div class="col-xs-3 col-xs-offset-3">
                                                @if (empty($contractId))
                                                    <button type="button" id="create_submit" class="btn btn-primary search-button">??????</button>
                                                @endif
                                            </div>
                                            <div class="col-xs-3">
                                                @if (isset(request()->refid))
                                                    <a href="{{route('contract.edit', ['id' => request()->refid])}}" style="float: left" class="btn btn-danger search-button">??????</a>
                                                @else
                                                    @if($canUpdate)
                                                        <a href="{{route('customer_edit', ['id' => request()->client_id])}}" style="float: left" class="btn btn-danger search-button">??????</a>
                                                    @elseif($canView)
                                                        <a href="{{route('customer_view', ['id' => request()->client_id])}}" style="float: left" class="btn btn-danger search-button">??????</a>
                                                    @endif
                                                @endif
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
    $('.datepicker-contract').datepicker( {
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'yy/mm/dd'
        }
    );
    $(document).ready(function() {
        if($("#auto_update").val() == "false"){
            $("#contract_span").prop('disabled', true);
                $('#contract_span').val('');
        }
        $("#auto_update").change(function() {
            if ($(this).val() == "false") {
                $("#contract_span").prop('disabled', true);
                $('#contract_span').val('');
            } else {
                $("#contract_span").removeAttr("disabled");
            }
        });
        $("#create_submit").click(function(event) {
            $("#create_contract").submit();
        });
    });
</script>
@endsection
