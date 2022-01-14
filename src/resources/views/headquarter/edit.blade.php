@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('headquarter/edit'))
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
                                    <form id="headquarter_form" method="post" action="{{ url('headquarter/edit') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ $headquarter->updated_at }}" name="update_time">
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
                                                        <select class="form-control" disabled="disabled">
                                                            @foreach ($companies as $company)
                                                                <option
                                                                    @if ($headquarter->company_id == $company->id) selected @endif
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
                                                        <label>表示コード</label>
                                                        <input type="text" name="headquarters_code" value="{{ $headquarter->headquarters_code }}" class="form-control">
                                                        <input type="hidden" name="company_id" value="{{ $headquarter->company_id }}">
                                                        <span class="text-danger">
                                                            {{ $errors->has('headquarters_code') ? $errors->first('headquarters_code') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>事業本部コード</label>
                                                        <input type="text" name="headquarter_list_code" value="{{ $headquarter->headquarter_list_code }}" class="form-control">
                                                        <span class="text-danger">
                                                            {{ $errors->has('headquarter_list_code') ? $errors->first('headquarter_list_code') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>事業本部名</label>
                                                        <input type="text" name="headquarters" value="{{ $headquarter->headquarters }}" class="form-control">
                                                        <input type="hidden" id="id" name="id" value="{{ $headquarter->id }}">
                                                        <span class="text-danger">
                                                            {{ $errors->has('headquarters') ? $errors->first('headquarters') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>変更理由</label>
                                                        <textarea rows="3" class="form-control" name="note">{{ $headquarter->note }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                                <div class="row search-form">
                                                    <div class="col-lg-4 form-group">
                                                        <label class="checkbox-title">非表示</label>
                                                        <div class="icheck-primary d-inline ">
                                                            <input type="checkbox" @if($headquarter->status == false) checked @endif id="status" name="status" class="input_checkbox">
                                                            <label for="status"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-10 col-md-12 col-lg-offset-1" id="change_headquarter" hidden>
                                                <div class="row search-form">
                                                    <div class="col-lg-6 form-group">
                                                        <label>移行先事業本部</label>
                                                        <select class="form-control" id="change_id" name="headquarter_id">
                                                            <option value=""> </option>
                                                            @foreach ($headquarter_list as $headquarters)
                                                                <option
                                                                    @if ($headquarter->id == $headquarters->id) hidden @endif
                                                                    value="{{ $headquarters->id }}">{{ $headquarters->headquarters }}
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
                                                        <div class="col-xs-3 col-xs-offset-3">
                                                            <button type="button" id="change" class="btn btn-primary search-button">更新</button>
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <a style="float: left" class="btn btn-danger search-button" href="{{ url('headquarter/index?page=' . request()->page) }}">戻る</a>
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
                if ($('#status').is(':checked') == true && $("#change_id").val() == "") {
                    var form = new FormData();
                    form.append('headquarter_id', $("#id").val());
                    $.ajax({
                        url: '/headquarter/check',
                        data: form,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(response) {
                            if (response.status == 1) {
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
                                        $("#headquarter_form").submit();
                                    }else if (result.dismiss === Swal.DismissReason.cancel) {
                                        $('#change_headquarter').hide();
                                    }
                                })
                                $('#status').prop('selectedIndex', 0);
                            } else {
                                $("#headquarter_form").submit();
                            }
                        },
                        error: function(exception) {
                            alert(exception.responseText);
                        }
                    });
                } else {
                    $("#headquarter_form").submit();
                }
            });
        });
        $(document).on('change', '#status', function() {
            ckb = $("#status").is(':checked');
            if (ckb == true) {
                $('#change_headquarter').show();
            } else {
                $('#change_headquarter').hide();
            }
        });
    </script>
    <script src="{{ asset('select/icontains.js') }}"></script>
    <script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
