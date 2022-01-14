@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('user/concurrent/create',$user->id))
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
                                <div class="text-danger">
                                    {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                </div>
                                <form id="create_user" method="post" action="{{ url('user/concurrent/create') }}">
                                    @csrf
                                    <div class="row">
                                        {{-- r1 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>社員番号</label>
                                                    <input type="text" value="{{$user->usr_code}}" id="user_code"  class="form-control">
                                                    <input type="hidden" value="{{$user->id}}" name="usr_id">
                                                    <input type="hidden" name="usr_code" value="{{$user->usr_code}}">
                                                    <div class="text-danger">
                                                        {{ $errors->has('usr_code') ? $errors->first('usr_code') : '' }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>社員名</label>
                                                    <input type="text" id="user_name" value="{{$user->usr_name}}" class="form-control">
                                                    <input type="hidden" name="usr_name" value="{{$user->usr_name}}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('usr_name') ? $errors->first('usr_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r2 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    <select class="form-control" name="company_id_c" id="company_id">
                                                        <option value=""></option>
                                                            @foreach($companies as $company)
                                                                <option {{ old('company_id_c') == $company->id ? 'selected' : '' }}
                                                                    value="{{$company->id}}">{{$company->abbreviate_name}}
                                                                </option>
                                                            @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('company_id_c') ? $errors->first('company_id_c') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>事業本部名</label>
                                                    <select class="form-control" id="headquarter_id" name="headquarter_id_c">
                                                        <option value=""> </option>
                                                        @foreach($headquarters as $headquarter)
                                                            <option class="headquarter_id"
                                                                {{ old('headquarter_id_c') == $headquarter->id ? 'selected' : '' }}
                                                                data-value="{{ $headquarter->company()->id }}"
                                                                value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id_c') ? $errors->first('headquarter_id_c') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r3 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>事業部名</label>
                                                    <select class="form-control" id="department_id" name="department_id_c">
                                                        <option value=""> </option>
                                                        @foreach($departments as $department)
                                                            <option class="department_id"
                                                                {{ old('department_id_c') == $department->id ? 'selected' : '' }}
                                                                data-value="{{ $department->headquarter()->id }}"
                                                                value="{{$department->id}}">{{$department->department_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id_c') ? $errors->first('department_id_c') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>所属グループ</label>
                                                    <select class="form-control" name="group_id_c" id="group_id">
                                                        <option value=""></option>
                                                        @foreach($groups as $group)
                                                            <option class="group_id"
                                                                {{ old('group_id_c') == $group->id ? 'selected' : '' }}
                                                                data-value="{{ $group->department()->id }}"
                                                                value="{{$group->id}}">{{$group->group_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id_c') ? $errors->first('group_id_c') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r4 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>役職</label>
                                                    <select class="form-control" id="position_id" name="position_id_c">
                                                        <option value=""></option>
                                                        @foreach($position_list as $position)
                                                            <option class="position_id"
                                                                {{ old('position_id_c') == $position->id ? 'selected' : '' }}
                                                                data-value="{{ $position->company_id }}"
                                                                value="{{$position->id}}">{{$position->position_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('position_id_c') ? $errors->first('position_id_c') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">登録</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{route('edituserinfor', ['id' => $user->id])}}">戻る</a>
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
<script type="text/javascript">
    $(document).ready(function() {
            $("#user_code").prop("disabled", true);
            $("#user_name").prop("disabled", true);
            $("#headquarter_id").prop("disabled", true);
            $("#department_id").prop("disabled", true);
            $("#group_id").prop("disabled", true);

            if($("#company_id").val() !="") {
                $("#headquarter_id").prop("disabled", false);
                var company_id=$("#company_id").val();
                $(".headquarter_id").each(function() {
                        $(this).show();
                        if($(this).attr('data-value') !==company_id) {
                            $(this).hide();
                        }
                    }
                );
            }

            if($("#headquarter_id").val() !="") {
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

            if($("#departmen_id").val() !="") {
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
        }
    );

    $(document).on('change', '#company_id', function () {
            $('#headquarter_id').prop('selectedIndex', 0);
            $('#department_id').prop('selectedIndex', 0);
            $('#group_id').prop('selectedIndex', 0);
            $("#headquarter_id").prop("disabled", false);
            var company_id=$("#company_id").val();

            $(".headquarter_id").each(function() {
                    $(this).show();
                    if($(this).attr('data-value') !==company_id) {
                        $(this).hide();
                    }
                }
            );
        }
    );

    $(document).on('change', '#headquarter_id', function () {
            $('#department_id').prop('selectedIndex', 0);
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
</script>
@endsection
