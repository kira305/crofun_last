@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('user/edit',$user))
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
                                <form id="create_user" method="post" action="{{ url('user/edit') }}">
                                    @csrf
                                    @php
                                        $checkCompany = Auth::user()->checkCompany($user->company_id);
                                        $checkIsDisable = Auth::user()->checkIsDisable($user->id);
                                    @endphp
                                    <input type="hidden" value="{{ $user->updated_at }}" name="update_time">
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
                                    <div class="row">
                                        {{-- r1 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>社員番号</label>
                                                    <input type="text" name="usr_code" class="form-control"
                                                    value="{{$user->usr_code}}" @if(!$checkCompany) disabled @endif
                                                    @if ($checkIsDisable == 1) disabled @endif>
                                                    <input type="hidden" type="text" name="id" class="form-control" value="{{$user->id}}" id="user_id">

                                                    <div class="text-danger">
                                                        {{ $errors->has('usr_code') ? $errors->first('usr_code') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>社員名</label>
                                                    <input type="text" name="usr_name" class="form-control"
                                                    value="{{$user->usr_name}}" @if(!$checkCompany) disabled @endif
                                                    @if ($checkIsDisable == 1) disabled @endif >
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r2 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input type="text" readonly class="form-control" value="{{$user->company->company_name}}">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" name="company_id" id="company_id">
                                                            @foreach($companies as $company)
                                                                <option @if ($user->company_id == $company->id) selected @endif
                                                                    value="{{$company->id}}">{{$company->abbreviate_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" name="company_id" id="company_id" disabled>
                                                            <option value="{{$user->id}}">
                                                                {{$user->company->abbreviate_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id') ? $errors->first('company_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属本部</label>
                                                    @if ($checkIsDisable == 1)
                                                    <input type="text" readonly class="form-control"
                                                        value="{{$user->headquarter->headquarters}}">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                            <option value=""> </option>
                                                            @foreach($headquarters as $headquarter)
                                                                <option class="headquarter_id"
                                                                    data-value="{{ $headquarter->company_id }}" @if ($user->headquarter_id == $headquarter->id) selected @endif
                                                                    value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="headquarter_id_N"
                                                            name="headquarter_id" disabled>
                                                            <option value="{{$user->id}}">
                                                                {{$user->headquarter->headquarters}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r3 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属部署</label>
                                                    @if ($checkIsDisable == 1)
                                                    <input type="text" readonly class="form-control"
                                                        value="{{$user->department->department_name}}">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="department_id" name="department_id">
                                                            <option> </option>
                                                            @foreach($departments as $department)
                                                                <option class="department_id"
                                                                    data-value="{{ $department->headquarter()->id }}" @if ($user->department_id == $department->id) selected @endif
                                                                    value="{{$department->id}}">{{$department->department_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="department_id_N" name="department_id" disabled>
                                                            <option value="{{$user->id}}">
                                                                {{$user->department->department_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属グループ</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input type="text" readonly class="form-control" value="{{$user->group->group_name}}">
                                                    @elseif ($checkCompany == true)
                                                        <select class="form-control" id="group_id" name="group_id">
                                                            <option> </option>
                                                            @foreach($groups as $group)
                                                                <option class="group_id" data-value="{{ $group->department()->id }}"
                                                                    @if ($user->group_id == $group->id) selected @endif
                                                                    value="{{$group->id}}">{{$group->group_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="group_id_N" name="group_id"
                                                            disabled>
                                                            <option value="{{$user->id}}">{{$user->group->group_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r4 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>役職</label>
                                                    @if ($checkIsDisable == 1)
                                                    <input type="text" readonly class="form-control"
                                                        value="{{$user->position->position_name}}">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="position_id" name="position_id">
                                                            <option value=""> </option>
                                                            @foreach($position_list as $position)
                                                                <option class="position_id" data-value="{{ $position->company_id }}"
                                                                    @if ($user->position_id == $position->id) selected @endif
                                                                    value="{{$position->id}}">{{$position->position_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="position_id_N" name="position_id" disabled>
                                                            <option value="{{$user->id}}">
                                                                {{$user->position->position_name}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('position_id') ? $errors->first('position_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>メールアドレス</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input type="text" readonly class="form-control" value="{{$user->email_address}}">
                                                    @else
                                                        <input type="text" name="mail_address" class="form-control" value="{{$user->email_address}}" @if(!$checkCompany) disabled @endif>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('mail_address') ? $errors->first('mail_address') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r5 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>画面機能ルール</label>
                                                    @if ($checkIsDisable == 1)
                                                        <input type="text" readonly class="form-control" value="{{$user->getrole->rule}}">
                                                    @elseif ($checkCompany)
                                                        <select class="form-control" id="rule_id" name="rule_id">
                                                            <option value=""> </option>
                                                            @foreach($rule_list as $rule)
                                                                <option class="rule_id" data-value="{{ $rule->company_id }}" @if($user->rule == $rule->id) selected @endif
                                                                    value="{{$rule->id}}">{{$rule->rule}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select class="form-control" id="rule_id_N" name="rule_id" disabled>
                                                            <option value="{{$user->id}}">
                                                                {{$user->getrole->rule}}
                                                            </option>
                                                        </select>
                                                    @endif
                                                    <span class="text-danger">
                                                        {{ $errors->has('rule_id') ? $errors->first('rule_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r6 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-4 form-group">
                                                    <label class="checkbox-title">退職</label>
                                                    <div class="icheck-primary d-inline ">
                                                        <input type="checkbox" name="retire" id="retire" class="input_checkbox"
                                                        @if ($user->retire == true) checked @endif
                                                        @if (!$checkCompany) disabled @endif
                                                        @if ($checkIsDisable == 1) readonly @endif>
                                                        <label for="retire"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    @if ($checkIsDisable != 1)
                                                        <div class="col-xs-3 col-xs-offset-3">
                                                            <button type="submit" @if(!$checkCompany) disabled @endif class="btn btn-primary search-button">更新</button>
                                                        </div>
                                                    @endif
                                                    <div class="col-xs-3">
                                                        @php($page = session()->has('userMst.page') ? '?page='.session('userMst.page') : '')
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('user/index'.$page) }}">戻る</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="table-parent col-lg-10 col-lg-12 col-lg-offset-1 ">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>編集</th>
                                                    <th>会社</th>
                                                    <th>兼務本部</th>
                                                    <th>兼務部署</th>
                                                    <th>兼務グループ</th>
                                                    <th>役職</th>
                                                    <th>ステータス</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($concurrents as $concurrent)
                                                <tr>
                                                    <td>
                                                        @if($concurrent->status == true)
                                                        <a href="{{route('concurrentedit', ['id' => $concurrent->id])}}">
                                                            <button type="submit" @if(Auth::user()->checkIsDisable($user->id) == 1)
                                                                disabled
                                                                @endif
                                                                style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                        </a>
                                                        @endif
                                                    </td>
                                                    <td>{{  $concurrent->company->abbreviate_name }}</td>
                                                    <td>{{  $concurrent->headquarter->headquarters }}</td>
                                                    <td>{{  $concurrent->department->department_name }}</td>
                                                    <td>{{  $concurrent->group->group_name }}</td>
                                                    <td>{{  $concurrent->position->position_name }}</td>
                                                    <td>
                                                        @if($concurrent->status == true)
                                                        {{ "兼務" }}
                                                        @else
                                                        {{ "兼務解除" }}
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($concurrent->status == true)
                                                        <button @if (Auth::user()->checkIsDisable($user->id) == 1)
                                                            disabled
                                                            @endif
                                                            data-value = "{{$concurrent->id}}" class="btn btn-danger delete
                                                            btn-sm">削除
                                                        </button>
                                                        @else
                                                        <button @if (Auth::user()->checkIsDisable($user->id) == 1)
                                                            disabled
                                                            @endif
                                                            onclick="location.href='{{ url('user/concurrent/reset?id='.$concurrent->id)}}';"
                                                            class="btn btn-danger btn-sm">解除取消
                                                        </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-10 col-lg-12 col-lg-offset-1 ">
                                        <a href="{{route('concurrentcreate', ['usr_id' => $user->id])}}">
                                            <button @if ($checkIsDisable == 1) disabled @endif type="button" class="btn btn-primary">兼務情報新規登録</button>
                                        </a>
                                    </div>
                                </div>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script type="text/javascript">
    $(document).on('change', '#headquarter_id', function () {
            $('#department_id').prop('selectedIndex', 0);
            $('#group_id').prop('selectedIndex', 0);
            $("#department_id").prop("disabled", false);
            var headquarter_id=$("#headquarter_id").val();
            $(".department_id").each(function() {
                    $(this).show();
                    if($(this).attr('data-value') !==headquarter_id) {
                        $(this).hide();
                    }
                }
            );
        }
    );

    $(document).on('change', '#department_id', function () {
            $('#group_id').prop('selectedIndex', 0);
            $("#group_id").prop("disabled", false);
            var department_id=$("#department_id").val();
            $(".group_id").each(function() {
                    $(this).show();
                    if($(this).attr('data-value') !==department_id) {
                        $(this).hide();
                    }
                }
            );
        }

    );

    $(document).ready(function() {
            $(".delete").click(function() {
                    var base='{!! route("concurrentdelete") !!}';
                    var id=$(this).data('value');
                    var user_id=$("#user_id").val();
                    var url=base+"?id="+id+"&usr_id="+user_id;
                    Swal.fire({
                        title: 'このデータを削除しますか。',
                        text: "",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'キャンセル',
                        confirmButtonText: '削除'
                        }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href=url;
                        }
                    })
                }
            );
            $("#department_id").prop("disabled", true);
            $("#group_id").prop("disabled", true);
        }
    );
</script>
@endsection
