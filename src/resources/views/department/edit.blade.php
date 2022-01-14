@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('department/edit'))
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
                                    <form id="department_form" method="post" action="{{ url('department/edit') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ $department->updated_at }}" name="update_time">
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
                                                        <label>所属会社</label>
                                                        <div class = "form-control">
                                                            {{$department->company()->abbreviate_name}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>事業本部名</label>
                                                        @if($department->headquarter()->status == false)
                                                            <input type="text"  name="cost_code" readonly value="{{ $department->headquarter()->headquarters }}" class="form-control">
                                                        @else
                                                        <select class="form-control" id="headquarter_id" name="headquarter_id">
                                                            @foreach($headquarters as $headquarter)
                                                                <option class="headquarter_id_chose"
                                                                data-id = "{{ $headquarter->company_id }}"
                                                                @if ($department->headquarters_id == $headquarter->id) selected @endif
                                                                value="{{$headquarter->id}}">{{$headquarter->headquarters}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @endif
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
                                                        <input type="text" @if($department->headquarter()->status == false) readonly @endif
                                                        name="department_code" value="{{$department->department_code}}" class="form-control">
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
                                                        <input type="text"  @if($department->headquarter()->status == false) readonly @endif
                                                        name="department_list_code" value="{{$department->department_list_code}}" class="form-control">
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
                                                        <input type="text" @if($department->headquarter()->status == false) readonly @endif
                                                        name="department_name"  value="{{$department->department_name}}" class="form-control">
                                                        <input type="hidden" id="id" name="department_id" value="{{$department->id}}">
                                                        <span class="text-danger">
                                                            {{ $errors->has('department_name') ? $errors->first('department_name') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>変更理由</label>
                                                        <textarea rows="3" name="note" class="form-control">{{ $department->note }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">非表示</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if ($department->status == false) checked @endif
                                                            @if($department->headquarter()->status == false) disabled @endif name="status" id="status" class="input_checkbox">
                                                            <label for="status"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1" id="change_department" hidden>
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>移行先事業部</label>
                                                        <select class="form-control" id="change_id" name="new_department_id">
                                                            <option value=""> </option>
                                                            @foreach($departments as $department_list)
                                                                <option class="department_id_chose"  @if ($department->id == $department_list->id) hidden @endif
                                                                data-id = "{{$department_list->headquarter()->company_id}}"
                                                                value="{{$department_list->id}}">
                                                                    {{$department_list->department_name}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span class="text-danger">
                                                            {{ $errors->has('company_id') ? trans('validation.company_chose') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group btn-upload-style">
                                                        @if($department->headquarter()->status != false)
                                                            <div class="col-xs-3 col-xs-offset-3">
                                                                <button type="button" class="btn btn-primary search-button" id="change">更新</button>
                                                            </div>
                                                        @endif
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
<script type="text/javascript">
$(document).ready(function() {
    $("#change").click(function() {
            if($('#status').is(':checked')==true && $("#change_id").val()=="") {
                var form=new FormData();
                form.append('department_id', $("#id").val());
                $.ajax( {
                        url: '/department/check',
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
                                        $("#department_form").submit();
                                    }else if (result.dismiss === Swal.DismissReason.cancel) {
                                        $('#change_department').hide();
                                    }
                                })
                                $('#status').prop('selectedIndex', 0);
                            }
                            else {
                                $("#department_form").submit();
                            }
                        },
                        error: function (exception) {
                            alert(exception.responseText);
                        }
                    }
                );
            }
            else {
                $("#department_form").submit();
            }
        }
    );
    }
);

$(document).ready(function() {
        var headquarter=$("#department_id").find(':selected').attr('data-value');
        var company_id=$("#headquarter_id").find(':selected').attr('data-id');
        $(".headquarter_id_chose").each(function() {
                if($(this).attr('data-id') !=company_id) {
                    $(this).remove();
                }
            }
        );
        $(".department_id_chose").each(function() {
                if($(this).attr('data-id') !=company_id) {
                    $(this).remove();
                }
            }
        );
    }
);

$(document).on('change', '#status', function () {
        ckb=$("#status").is(':checked');
        if(ckb==true) {
            $('#change_department').show();
        }
        else {
            $('#change_department').hide();
        }
    }
);
</script><script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
