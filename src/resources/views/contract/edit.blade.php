@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('contract/edit'))
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
<div class="row" >
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline">
                        <div>
                            <div class="box-body p-20">
                                @php
                                    $isReadonly = $contract->checkDeadline() == true || $contract->contract_canceled == true || $contract->update_finished == true || $contract->status == false ? true : false;
                                @endphp
                                <form id="edit_contract" method="post" action="{{ url('contract/edit'.'?id='.$contract->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="company_id" value="{{ $contract->company_id }}">
                                    <input type="hidden" id="post_act" name="post_act" value="{{ old('post_act') }}">
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
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>契約書ID</label>
                                                    <input disabled type="text" value="{{ $contract->id }}" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>進捗状況</label>
                                                    <input disabled type="text" value="{{ $contract->progress_status }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客コード</label>
                                                    @if (empty($customer->client_code_main))
                                                        <input disabled type="text" value="{{ $customer->client_code }}" class="form-control">
                                                    @else
                                                        <input disabled type="text" value="{{ $customer->client_code_main }}" class="form-control">
                                                    @endif
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text"></span>顧客名</label>
                                                    <div class="id-change">
                                                        <input disabled type="text" value="{{ $customer->getCustomerName()  }}" class="form-control">
                                                        @if (Auth::user()->can('customer-edit'))
                                                            <a class="btn btn-primary"
                                                                href="{{ route('customer_edit', ['id' => $customer->id, 'contract_edit_id' => $contract->id]) }}">顧客情報参照
                                                            </a>
                                                        @else
                                                            @if (Auth::user()->can('customer-view'))
                                                                <a class="btn btn-primary"
                                                                    href="{{ route('customer_view', ['id' => $customer->id, 'contract_edit_id' => $contract->id]) }}">顧客情報参照
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>プロジェクト</label>
                                                    <div class="table-responsive my-scrollbar">
                                                        <table id="contract_table" class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>選択</th>
                                                                    <th>コード</th>
                                                                    <th>プロジェクト名</th>
                                                                    <th>ステータス</th>
                                                                    <th>担当グループ</th>
                                                                    <th>参照</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($customer->projectForContract()->get() as $key => $project)
                                                                    <tr>
                                                                        @if ($isReadonly == true)
                                                                            @if (isset($contract->project_id[$project->id]))
                                                                                <td>
                                                                                    <input type="checkbox" onclick="return false;" disabled class="checkbox-center" name="project_id[{{$project->id}}]" value="{{ $project->id }}"
                                                                                    checked>
                                                                                </td>
                                                                                <td>{{ $project->project_code }}</td>
                                                                                <td>{{ $project->project_name }}</td>
                                                                                <td>{{ $project->status ? '取引中' : '取引終了'}}</td>
                                                                                <td>{{ $project->group->group_name }}</td>
                                                                                <td>
                                                                                    @if( Auth::user()->can('update',$project) && Auth::user()->can('checkProjectParent',$project) == 0)
                                                                                        <a class="btn btn-info btn-sm" href="{{route('edit_project', ['id' => $project->id,'contract_edit_id' => $contract->id])}}">
                                                                                            参照
                                                                                        </a>
                                                                                    @else
                                                                                        @if( Auth::user()->can('view',$project))
                                                                                            <a class="btn btn-info btn-sm" href="{{route('view_project', ['id' => $project->id,'contract_edit_id' => $contract->id])}}">
                                                                                                参照
                                                                                            </a>
                                                                                        @endif
                                                                                    @endif
                                                                                </td>
                                                                            @endif
                                                                        @else
                                                                            <td>
                                                                                <input type="checkbox" class="checkbox-center" name="project_id[{{$project->id}}]" value="{{ $project->id }}"
                                                                                @if(!empty(old('post_act')))
                                                                                    @if (!empty(old('project_id.'.$project->id) && old('project_id.'.$project->id) == $project->id))
                                                                                        checked
                                                                                    @endif
                                                                                @else
                                                                                    @if (isset($contract->project_id[$project->id]) && $contract->project_id[$project->id] == $project->id)
                                                                                        checked
                                                                                    @endif
                                                                                @endif>
                                                                            </td>
                                                                            <td>{{ $project->project_code }}</td>
                                                                            <td>{{ $project->project_name }}</td>
                                                                            <td>{{ $project->status ? '取引中' : '取引終了'}}</td>
                                                                            <td>{{ $project->group->group_name }}</td>
                                                                            <td>
                                                                                @if( Auth::user()->can('update',$project) && Auth::user()->can('checkProjectParent',$project) == 0)
                                                                                    <a class="btn btn-info btn-sm" href="{{route('edit_project', ['id' => $project->id,'page' => request()->page])}}">
                                                                                        参照
                                                                                    </a>
                                                                                @else
                                                                                    @if( Auth::user()->can('view',$project))
                                                                                        <a class="btn btn-info btn-sm" href="{{route('view_project', ['id' => $project->id,'show' =>1,'page' => request()->page])}}">
                                                                                            参照
                                                                                        </a>
                                                                                    @endif
                                                                                @endif
                                                                            </td>
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>参照可能部署</label>
                                                    <div class="table-responsive my-scrollbar" >
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                <thead>
                                                                    <tr>
                                                                        <th>選択</th>
                                                                        <th>本部</th>
                                                                        <th>部署</th>
                                                                    </tr>
                                                                </thead>
                                                                @foreach ($departmentsRef as $key => $department)
                                                                    <tr>
                                                                        @if ($isReadonly == true)
                                                                            @if(isset($contract->referenceable_department[$department->id]))
                                                                                <td>
                                                                                    <input type="checkbox" onclick="return false;" disabled class="checkbox-center" name="referenceable_department[{{ $department->id }}]" value="{{ $department->id }}"
                                                                                    @if(!empty(old('post_act')))
                                                                                        @if (!empty(old('referenceable_department.'.$department->id) && old('referenceable_department.'.$department->id) == $department->id))
                                                                                            checked
                                                                                        @endif
                                                                                    @else
                                                                                        @if (isset($contract->referenceable_department[$department->id]) && $contract->referenceable_department[$department->id] == $department->id)
                                                                                            checked
                                                                                        @endif
                                                                                    @endif>
                                                                                </td>
                                                                                <td>{{ $department->headquarter()->headquarters }}</td>
                                                                                <td>{{ $department->department_name }}</td>
                                                                            @endif
                                                                        @else
                                                                            <td>
                                                                                <input type="checkbox" class="checkbox-center" name="referenceable_department[{{ $department->id }}]" value="{{ $department->id }}"
                                                                                @if(!empty(old('post_act')))
                                                                                    @if (!empty(old('referenceable_department.'.$department->id) && old('referenceable_department.'.$department->id) == $department->id))
                                                                                        checked
                                                                                    @endif
                                                                                @else
                                                                                    @if (isset($contract->referenceable_department[$department->id]) && $contract->referenceable_department[$department->id] == $department->id)
                                                                                        checked
                                                                                    @endif
                                                                                @endif>
                                                                            </td>
                                                                            <td>{{ $department->headquarter()->headquarters }}</td>
                                                                            <td>{{ $department->department_name }}</td>
                                                                        @endif
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>種類</label>
                                                    <select class="form-control" id="contract_type" name="contract_type" @if($isReadonly) disabled @endif >
                                                        @php($contract_type_act = !empty(old('contract_type')) ? old('contract_type') :  $contract->contract_type)
                                                        @foreach ($contractTypes as $contractType)
                                                            <option value="{{ $contractType->id }}" @if ($contract_type_act == $contractType->id) selected @endif >
                                                                {{ $contractType->type_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_type') ? $errors->first('contract_type') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>法務チェック</label>
                                                    <select class="form-control" id="contract_completed" name="contract_completed" @if($isReadonly) disabled @endif>
                                                        @php($contract_completed_act = !empty(old('contract_completed')) ? old('contract_completed') :  $contract->contract_completed)
                                                        <option value="2" @if ($contract_completed_act == 2) selected @endif>法務チェック完了</option>
                                                        <option value="1" @if ($contract_completed_act == 1) selected @endif>法務チェック不要</option>
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>申請本部</label>
                                                    @if($isReadonly)
                                                        <input Readonly type="text" value="{{ $contract->headquarter->headquarters }}" class="form-control">
                                                    @else
                                                        <select class="form-control" id="headquarter_id"
                                                            name="headquarter_id">
                                                            @php($headquarter_id_act = !empty(old('headquarter_id')) ? old('headquarter_id') :  $contract->headquarter_id)
                                                            <option></option>
                                                            @foreach ($headquarters as $headquarter)
                                                                <option
                                                                    class="headquarter_id"
                                                                    @if ($headquarter_id_act == $headquarter->id) selected @endif
                                                                    data-value="{{ $headquarter->company_id }}"
                                                                    value="{{ $headquarter->id }}">
                                                                        {{ $headquarter->headquarters }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>申請部</label>
                                                    @if($isReadonly)
                                                        <input Readonly type="text" value="{{ $contract->department->department_name }}" class="form-control">
                                                    @else
                                                        <select class="form-control" id="department_id"
                                                            name="department_id">
                                                            <option></option>
                                                            @php($department_id_act = !empty(old('department_id')) ? old('department_id') :  $contract->department_id)
                                                            @foreach ($departments as $department)
                                                                <option
                                                                    class="department_id"
                                                                    @if ($department_id_act == $department->id) selected @endif
                                                                    data-value="{{ $department->headquarter()->id }}"
                                                                    value="{{ $department->id }}">
                                                                        {{ $department->department_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>新規登録申請グループ</label>
                                                    @if($isReadonly)
                                                        <input Readonly type="text" value="{{ $contract->group->group_name }}" class="form-control">
                                                    @else
                                                        <select class="form-control" id="group_id" name="group_id" >
                                                            <option></option>
                                                            @php($group_id_act = !empty(old('group_id')) ? old('group_id') :  $contract->group_id)
                                                            @foreach ($groups as $group)
                                                                <option
                                                                    class="group_id"
                                                                    @if ($group_id_act == $group->id) selected @endif
                                                                    data-value="{{ $group->department()->id }}"
                                                                    value="{{ $group->id }}">
                                                                        {{ $group->group_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>申請者</label>
                                                    <input type="text" name="application_user_name" maxlength="20" @if($isReadonly) readonly @endif value="{{ !empty(old('post_act')) ? old('application_user_name') : $contract->application_user_name }}" class="form-control">
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>X-Point 申請番号</label>
                                                    <input type="text" name="application_num" maxlength="20" @if($isReadonly) readonly @endif value="{{ !empty(old('post_act')) ? old('application_num') : $contract->application_num }}" class="form-control">
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>押印受付日</label>
                                                    <input id="stamp_receipt_date" name="stamp_receipt_date" @if($isReadonly) disabled @endif value="{{ !empty(old('post_act')) ? old('stamp_receipt_date') : $contract->stamp_receipt_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('stamp_receipt_date') ? $errors->first('stamp_receipt_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>押印返却日</label>
                                                    <input id="stamped_return_date" name="stamped_return_date"  @if($isReadonly) disabled @endif value="{{ !empty(old('post_act')) ? old('stamped_return_date') : $contract->stamped_return_date }}"
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
                                                    <label>回収日</label>
                                                    <input id="collection_date" name="collection_date" @if($isReadonly) disabled @endif value="{{ !empty(old('post_act')) ? old('collection_date') : $contract->collection_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('collection_date') ? $errors->first('collection_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>契約締結日</label>
                                                    <input id="contract_conclusion_date" name="contract_conclusion_date" @if($isReadonly) disabled @endif value="{{ !empty(old('post_act')) ? old('contract_conclusion_date') : $contract->contract_conclusion_date }}"
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
                                                    <label>契約開始日</label>
                                                    <input id="contract_start_date" name="contract_start_date" @if($isReadonly) disabled @endif value="{{ !empty(old('post_act')) ? old('contract_start_date') : $contract->contract_start_date }}"
                                                        class="form-control datepicker-contract" maxlength="10">
                                                    <span class="text-danger">
                                                        {{ $errors->has('contract_start_date') ? $errors->first('contract_start_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>契約終了日</label>
                                                    <input id="contract_end_date" name="contract_end_date" @if($isReadonly) disabled @endif value="{{ !empty(old('post_act')) ? old('contract_end_date') : $contract->contract_end_date }}"
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
                                                    <label>更新の確認期限</label>
                                                    <div class="input-group">
                                                        <input type="text" id="check_updates_deadline" @if($isReadonly) readonly @endif name="check_updates_deadline" value="{{ !empty(old('post_act')) ? old('check_updates_deadline') : $contract->getCheckUpdatesDeadline() }}" class="form-control uintTextBox" maxlength="3">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">ヶ月</span>
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
                                                @php($auto_update_act = !empty(old('auto_update')) ? old('auto_update') :  $contract->auto_update)
                                                <div class="col-lg-6 form-group">
                                                    <label>自動更新</label>
                                                    <select class="form-control" id="auto_update" name="auto_update" @if($isReadonly) disabled @endif>
                                                        <option value="true" @if ($auto_update_act == 'true') selected @endif>あり</option>
                                                        <option value="false" @if ($auto_update_act == 'false') selected @endif>なし</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>契約スパン</label>
                                                    <div class="input-group">
                                                        <input type="text" @if($isReadonly) disabled @endif id="contract_span" name="contract_span" class="form-control uintTextBox" maxlength="3"
                                                        @if ($auto_update_act == 'false')
                                                            disabled value=""
                                                        @else
                                                            value="{{ (!empty(old('post_act'))) ? old('contract_span') : $contract->contract_span }}"
                                                        @endif>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">ヶ月</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('contract_span') ? $errors->first('contract_span') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 13--}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>更新ログ</label>
                                                    <textarea readonly class="form-control " rows="3" name="update_log">{{ !empty(old('post_act')) ? old('update_log') : $contract->update_log }}</textarea>
                                                </div>
                                                <div class="col-lg-6 form-group " >
                                                    <label>備考</label>
                                                    <textarea @if($isReadonly) readonly @endif class="form-control" rows="3" name="note">{{ !empty(old('post_act')) ? old('note') : $contract->note }}</textarea>
                                                    <div class="text-danger">
                                                        {{ $errors->has('note') ? $errors->first('note') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 14 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">契約中止</label>
                                                        <div class="icheck-primary d-inline ">
                                                            @php($contract_canceled_act = !empty(old('contract_canceled')) ? old('contract_canceled') :  $contract->contract_canceled)
                                                            <input type="checkbox" @if($isReadonly) onclick="return false;" @endif @if ($contract_canceled_act == true || $contract_canceled_act == 'on') checked @endif
                                                            id="contract_canceled" name="contract_canceled" class="input_checkbox">
                                                            <label for="contract_canceled"></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">更新完了</label>
                                                        <div class="icheck-primary d-inline ">
                                                            @php($update_finished_act = !empty(old('update_finished')) ? old('update_finished') :  $contract->update_finished)
                                                            <input type="checkbox" @if($isReadonly) onclick="return false;" @endif @if ($update_finished_act == true || $update_finished_act == 'on') checked @endif
                                                            id="update_finished" name="update_finished" class="input_checkbox">
                                                            <label for="update_finished"></label>
                                                        </div>
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('update_finished') ? $errors->first('update_finished') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 15 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-lg-12 col-lg-offset-10 m-b-10" >
                                            <button type="button" @if(!$isReadonly) id="btn-add-contract" @endif class="btn btn-primary">追加</button>
                                        </div>
                                        <div class="table-parent col-lg-10 col-lg-12 col-lg-offset-1 ">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>ファイルタイプ</th>
                                                        <th>UP</th>
                                                        <th>ファイル名</th>
                                                        <th>参考</th>
                                                        <th>UP日</th>
                                                        <th>削除</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php($checkMain = isset($contractFiles[0]) ? $contractFiles[0]->file_type : null)
                                                    @php($isMain = !empty($checkMain) && $checkMain == 1 ? true : false)
                                                    <tr>
                                                        <td>
                                                            メイン
                                                        </td>
                                                        <td>
                                                            <div class="col-lg-6">
                                                                <div>
                                                                    @if ($isMain)
                                                                        <a target="_blank" class="btn btn-info" rel="noopener noreferrer" href="{{ route('contract_display', ['contract_file_id' => $contractFiles[0]->id, 'contract_id' => $contract->id]) }}">
                                                                            参照
                                                                        </a>
                                                                    @else
                                                                        <div class="input-group">
                                                                            <span class="input-group-btn">
                                                                                <span class="btn btn-success btn-file border-select-file">
                                                                                    選択<input type="file" @if($isReadonly) onclick="return false;" @endif class="contract_file_select" accept="application/pdf" data-target="contract_file_select_1" name="contract_file[1]">
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
                                                                    確認
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control" @if($isReadonly) readonly @endif rows="1" name="file_note[1]">{{$isMain ? $contractFiles[0]->note : ''}}</textarea>
                                                        </td>
                                                        <td><label style="margin-bottom: 0px" data-target="contract_file_select_1">{{$isMain ? $contractFiles[0]->updated_at : ''}}</label></td>
                                                        @if ($isMain)
                                                            <td><button type="button" @if($isReadonly) onclick="return false;" @endif class="btn btn-danger @if(!$isReadonly) delete-submit @endif width-68" contract-file-id="{{$isMain ? $contractFiles[0]->id : '' }}">削除</button></td>
                                                        @else
                                                            <td><button type="button" @if($isReadonly) onclick="return false;" @endif class="btn btn-warning btn-file clear-file" data-target="contract_file_select_1">クリア</button></td>
                                                        @endif
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
                                                                補足
                                                            </td>
                                                            <td>
                                                                <div class="col-lg-6">
                                                                    <div>
                                                                        <a target="_blank" class="btn btn-info" rel="noopener noreferrer" href="{{ route('contract_display', ['contract_file_id' => $contractFile->id, 'contract_id' => $contract->id]) }}">
                                                                            参照
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="text" id="" value="{{ $contractFile->file_original_name }}" class="form-control contract_file_show" data-target="contract_file_select_{{$key}}" readonly>
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control" @if($isReadonly) readonly @endif rows="1" name="file_note[{{$key}}]">{{ $contractFile->note }}</textarea>
                                                            </td>
                                                            <td><label style="margin-bottom: 0px" data-target="contract_file_select_{{$key}}">{{ $contractFile->updated_at }}</label></td>
                                                            <td><button type="button" class="btn btn-danger delete-submit width-68" @if($isReadonly) onclick="return false;" @endif contract-file-id="{{ $contractFile->id }}">削除</button></td>
                                                        </tr>
                                                    @endforeach
                                                    @php($lastKey = array_key_last($contractFiles->toArray()))
                                                    @if ($isMain)
                                                        @php($startTempKey = $lastKey + 2)
                                                    @else
                                                        @php($startTempKey = $lastKey + 3)
                                                    @endif
                                                    @for ($i = $startTempKey; $i < 11; $i++)
                                                    <tr class="contract-dis-none">
                                                        <td>
                                                            補足
                                                        </td>
                                                        <td>
                                                            <div class="col-lg-6">
                                                                <div class="dis-flex">
                                                                    <div class="input-group m-r-10">
                                                                        <span class="input-group-btn">
                                                                            <span class="btn btn-success btn-file border-select-file">
                                                                                選択 <input type="file" @if($isReadonly) onclick="return false;" @endif class="contract_file_select" accept="application/pdf" data-target="contract_file_select_{{$i}}" name="contract_file[{{$i}}]">
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
                                                                    確認
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control note-area" @if($isReadonly) readonly @endif rows="1" data-target="contract_file_select_{{$i}}" name="file_note[{{$i}}]"></textarea>
                                                        </td>
                                                        <td><label style="margin-bottom: 0px" data-target="contract_file_select_{{$i}}"></label></td>
                                                        <td><button type="button" class="btn btn-warning btn-file clear-file" @if($isReadonly) onclick="return false;" @endif data-target="contract_file_select_{{$i}}">クリア</button></td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- row btn --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group">
                                                    <div class="list-btn">
                                                        {{-- btn 1 --}}
                                                        <a><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-xl">前の契約参照</button></a>
                                                        {{-- btn 2 --}}
                                                        @if(!$isReadonly)
                                                            @if( Auth::user()->can('create','App\Contract_MST'))
                                                                <a class="btn btn-primary" href="{{route('contract.create', ['client_id' => $contract->client_id, 'refid' => $contract->id])}}">再締結</a>
                                                            @endif
                                                        @endif
                                                        {{-- btn 3 --}}
                                                        <a class="btn btn-success" href="{{route('contract.getcsv', ['id' => $contract->id])}}">CSV出力</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    <div class="row p-b-20">
                                        <div class="col-lg-12 ">
                                            <div class="col-xs-3 col-xs-offset-3">
                                                <button type="button" @if(!$isReadonly) id="form_submit" @endif class="btn btn-primary search-button" >更新</button>
                                            </div>
                                            <div class="col-xs-3">
                                                @php($page = session()->has('contractMst.page') ? '?page='.session('contractMst.page') : '')
                                                <a href="{{ url('contract/index'.$page) }}">
                                                    <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- /.modal -->
                                <div class="modal fade" id="modal-xl">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content ">
                                            <div class="modal-header">
                                                <h4 class="modal-title">前の契約参照一覧</h4>
                                            </div>
                                            <div class="modal-body">
                                                <table id="headquarter_table" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>参照</th>
                                                            <th class="text-center">顧客コード</th>
                                                            <th class="text-center">顧客名</th>
                                                            <th class="text-center">申請番号</th>
                                                            <th class="text-center">申請本部</th>
                                                            <th class="text-center">申請部</th>
                                                            <th class="text-center">申請グループ</th>
                                                            <th class="text-center">種類</th>
                                                            <th class="text-center">締結日</th>
                                                            <th class="text-center">契約開始日</th>
                                                            <th class="text-center">契約終了日</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php($contractAction = Crofun::getPermissionContract($contract, Auth::user()))
                                                        @foreach ($contractList4Ref as $contract4Ref)
                                                        <tr>
                                                            <td>
                                                                @if ($contractAction != null)
                                                                    @if ($contractAction->can_edit)
                                                                        <a href="{{route('contract.edit', ['id' => $contract4Ref->id])}}" class="btn btn-info btn-sm">編集</a>
                                                                    @elseif($contractAction->can_view || $contractAction->only_pj_refer_departments_edit)
                                                                        <a href="{{route('contract.view', ['id' => $contract4Ref->id])}}" class="btn btn-info btn-sm">参照</a>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>{{ $contract4Ref->customer->client_code }}</td>
                                                            <td>{{ $contract4Ref->getCustomerName() }}</td>
                                                            <td>{{ $contract4Ref->application_num }}</td>
                                                            <td>{{ $contract4Ref->headquarter->headquarters }}</td>
                                                            <td>{{ $contract4Ref->department->department_name }}</td>
                                                            <td>{{ $contract4Ref->group->group_name }}</td>
                                                            <td>{{ $contract4Ref->getContractTypeName() }}</td>
                                                            <td>{{ $contract4Ref->contract_conclusion_date }}</td>
                                                            <td>{{ $contract4Ref->contract_start_date }}</td>
                                                            <td>{{ $contract4Ref->contract_end_date }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">キャンセル</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
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
        $("#auto_update").change(function() {
            if ($(this).val() == "false") {
                $("#contract_span").prop('disabled', true);
                $('#contract_span').val('');
            } else {
                $("#contract_span").removeAttr("disabled");
            }
        });
        $(".delete-submit").click(function(event) {
            var item = $(this);
            $.confirm({
                title: '削除すると元に戻せませんが、本当に削除してよろしいですか？',
                content: '',
                type: 'red',
                typeAnimated: true,
                buttons: {
                    delete: {
                        text: 'YES',
                        btnClass: 'btn-blue',
                        with: '100px',
                        action: function() {
                            var fileId = item.attr('contract-file-id');
                            var input = $("<input>")
                                .attr("type", "hidden")
                                .attr("name", "file_delete").val(fileId);
                            $('#edit_contract').append(input);
                            $("#edit_contract").submit();
                        }
                    },
                    cancel: {
                        text: 'NO',
                        btnClass: 'btn-red',
                        action: function() {}
                    }
                }
            });
            // Swal.fire({
            //     title: '本気ですか',
            //     text: "削除すると元に戻せませんが、本当に削除してよろしいですか",
            //     icon: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     cancelButtonText: 'キャンセル',
            //     confirmButtonText: '削除'
            //     }).then((result) => {
            //     if (result.isConfirmed) {
            //         var fileId = item.attr('contract-file-id');
            //         var input = $("<input>")
            //             .attr("type", "hidden")
            //             .attr("name", "file_delete").val(fileId);
            //         $('#edit_contract').append(input);
            //         $("#edit_contract").submit();
            //     }
            // })
        });
    });

</script>
<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
