@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('group/edit'))
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
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
                                <form id="group_form" method="post" action="{{ url('group/edit') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" value="{{ $group->updated_at }}" name="update_time">
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
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    <div class="form-control" disabled>
                                                        {{$group->headquarter()->abbreviate_name}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- start --}}
                                        @if($group->department()->status == false || $group->headquarter()->status == false)
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>事業本部名</label>
                                                    <div class="form-control" disabled>
                                                        {{ $group->headquarter()->headquarters }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>部署名</label>
                                                    <div class="form-control" disabled>
                                                        {{ $group->department()->department_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        {{-- else --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>事業本部名</label>
                                                    <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                        <option　> </option>
                                                        @foreach($headquarters as $headquarter)
                                                        <option class="headquarter_id"
                                                            data-id="{{ $headquarter->company_id }}"
                                                            @if(!isset($headquarter_id))
                                                                @if ($group->headquarter()->headquarters_id == $headquarter->id) selected @endif
                                                            @else
                                                                @if ($headquarter_id == $headquarter->id) selected @endif
                                                            @endif
                                                            value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>部署名</label>
                                                    <select class="form-control" id="department_id" name="department_id">
                                                        <option value=""> </option>
                                                        @foreach($departments as $department)
                                                        <option class="department_id" @if ($group->department_id == $department->id) selected @endif
                                                            data-value="{{ $department->headquarters_id }}"
                                                            value="{{$department->id}}">{{$department->department_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        {{-- end if --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>表示コード</label>
                                                    <input type="text" @if(!$group->department()->status || !$group->headquarter()->status) readonly @endif
                                                    name="group_code" value="{{$group->group_code}}" class="form-control">
                                                    <div class="text-danger">
                                                        {{ $errors->has('group_code') ? $errors->first('group_code') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ $errors->has('unique') ? $errors->first('unique') : '' }}
                                                    </div>
                                                    <div class="text-danger">
                                                        {{ isset($unique) ? $unique : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>グループコード</label>
                                                    <input type="text" @if(!$group->department()->status || !$group->headquarter()->status) readonly @endif
                                                    name="group_list_code" value="{{$group->group_list_code}}" class="form-control">
                                                    <input type="hidden" id="group_id" name="id" value="{{$group->id}}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_list_code') ? $errors->first('group_list_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>グループ名</label>
                                                    <input type="text" name="group_name" @if(!$group->department()->status || !$group->headquarter()->status) readonly @endif
                                                    value="{{$group->group_name}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_name') ? $errors->first('group_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>原価コード</label>
                                                    <input type="text" @if(!$group->department()->status || !$group->headquarter()->status) readonly @endif
                                                    name="cost_code" value="{{ $group->cost_code }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('cost_code') ? $errors->first('cost_code') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>原価名</label>
                                                    <input type="text" @if(!$group->department()->status || !$group->headquarter()->status) readonly @endif
                                                    value="{{ $group->cost_name }}" name="cost_name" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('cost_name') ? $errors->first('cost_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>変更理由</label>
                                                    <textarea rows="3" class="form-control" name="note">{{ $group->note }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-4 form-group">
                                                    <label class="checkbox-title">非表示</label>
                                                    <div class="icheck-primary d-inline ">
                                                        <input type="checkbox" name="status" id="status" @if (!$group->status) checked @endif
                                                        @if($group->department()->status == false || $group->headquarter()->status == false) disabled @endif
                                                        class="input_checkbox">
                                                        <label for="status"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1" id="change_group" hidden>
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>移行先グループ</label>
                                                    <select class="form-control" name="group_id" id="change_id">
                                                        <option value=""> </option>
                                                        @foreach($group_list as $group_item)
                                                            <option class="group_id_chose" @if ($group->id == $group_item->id) hidden @endif
                                                                data-value = "{{$group_item->headquarter()->company_id}}"  value="{{$group_item->id}}">
                                                                    {{$group_item->group_name}}
                                                            </option>
                                                            "{{$group_item->headquarter()->company_id}}"
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    @if($group->department()->status != false && $group->headquarter()->status != false)
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                            <button type="button" id="change" class="btn btn-primary search-button">更新</button>
                                                        </div>
                                                    @endif
                                                    <div class="col-xs-3">
                                                        @php($page = session()->has('group.page') ? '?page='.session('group.page') : '')
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('group/index'.$page) }}">戻る</a>
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

<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#change").click(function() {
                if($('#status').is(':checked')==true && $("#change_id").val()=="") {
                    var form=new FormData();
                    form.append('group_id', $("#group_id").val());
                    $.ajax( {
                            url: '/group/check',
                            data: form,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success:function(response) {
                                if(response.status==1) {
                                    Swal.fire({
                                        title: response.message,
                                        text: '',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        cancelButtonText: 'キャンセル',
                                        confirmButtonText: '実行'
                                        }).then((result) => {
                                        if (result.isConfirmed) {
                                            $("#group_form").submit();
                                        }else if (result.dismiss === Swal.DismissReason.cancel) {
                                            $('#change_group').hide();
                                        }
                                    })
                                    $('#status').prop('selectedIndex', 0);
                                }
                                else {
                                    $("#group_form").submit();
                                }
                            },
                            error: function (exception) {
                                alert(exception.responseText);
                            }
                        }
                    );
                }
                else {
                    $("#group_form").submit();
                }
            }
        );
    });

    $(document).ready(function() {
            var headquarter=$("#department_id").find(':selected').attr('data-value');
            $("#fake_name").text(headquarter);
            var headquarter=$("#department_id").find(':selected').attr('data-value');
            var company_id=$("#headquarter_id").find(':selected').attr('data-id'); //ログインしている人の会社ID
            var headquarter_id=$("#headquarter_id").val();

            $(".headquarter_id").each(function() {
                    if($(this).attr('data-id') !=company_id) {
                        $(this).remove();
                    }
                }
            );

            $(".department_id").each(function() {
                    if($(this).attr('data-value') !=headquarter_id) {
                        $(this).remove();
                    }
                }
            );

            $(".group_id_chose").each(function() {
                    if($(this).attr('data-value') !=company_id) {
                        $(this).remove();
                    }
                }
            );
        }
    );

    $(document).on('change', '#status', function () {
            ckb=$("#status").is(':checked');
            if(ckb==true) {
                $('#change_group').show();
            }
            else {
                $('#change_group').hide();
            }
        }
    );
</script>
@endsection
