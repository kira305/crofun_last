@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('global_info/create'))
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
                                <form id="create_user" method="post" action="{{ url('global_info/edit') }}" enctype="multipart/form-data" name="MainForm">
                                    <input type="hidden" name="mode1" value="add">
                                    <input type="hidden" name="mode2" value="">
                                    <input type="hidden" name="save_sv_name" value="{{$global_info->save_sv_name}}">
                                    <input type="hidden" name="save_ol_name" value="{{$global_info->save_ol_name}}">
                                    <input type="hidden" name="id" value="{{$global_info->id}}">
                                    @csrf
                                    <div class="row">
                                        {{-- row 1 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>タイトル</label>
                                                    <input type="text" name="global_info_title" value="{{$global_info->global_info_title}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('global_info_title') ? $errors->first('global_info_title') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r2 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>内容</label>
                                                    <input type="text" name="global_info_content" value="{{$global_info->global_info_content}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('global_info_title') ? $errors->first('global_info_title') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r3 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group ">
                                                    <label>重要度</label>
                                                    <div class="id-change">
                                                        <div class="m-r-15">
                                                            <div class="icheck-danger d-inline check-radio">
                                                                <input type="radio" name="important_flg" id="important_flg_1" @if ($global_info->important_flg == "1") checked @endif value="1">
                                                                <label for="important_flg_1">
                                                                    重要
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="m-r-15">
                                                            <div class="icheck-danger d-inline check-radio">
                                                                <input type="radio" id="important_flg_2" name="important_flg" @if ($global_info->important_flg == "2") checked @endif value="2">
                                                                <label for="important_flg_2">
                                                                    注意
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="m-r-15">
                                                            <div class="icheck-danger d-inline check-radio">
                                                                <input type="radio" name="important_flg" id="important_flg_3" @if ($global_info->important_flg == "3") checked @endif value="3">
                                                                <label for="important_flg_3">
                                                                    お知らせ
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('important_flg') ? $errors->first('important_flg') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r4 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>掲載開始</label>
                                                    <div class="id-change">
                                                        <input type="text" id="start_date" name="start_date" class="form-control m-r-15 date-picker-ontop" autocomplete="off"
                                                        @if(!empty($global_info->start_date))
                                                            value="{{date('Y/m/d', strtotime($global_info->start_date))}}"
                                                        @else
                                                            value=""
                                                        @endif>
                                                        <select class="form-control" id="start_time" name="start_time">
                                                            <option value="">▼ 選択してください</option>
                                                            @foreach($TIME_ARRAY as $id => $data)
                                                                <option
                                                                    @if(isset($global_info->start_time) && !empty($global_info->start_time))
                                                                        @if ($data == date('H:i', strtotime($global_info->start_time))) selected @endif
                                                                    @endif
                                                                    value="{{$data}}">{{$data}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('start_date') ? $errors->first('start_date') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r5 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>掲載終了</label>
                                                    <div class="id-change">
                                                        <input type="text" id="end_date" name="end_date" class="form-control m-r-15 date-picker-ontop" autocomplete="off"
                                                        @if(!empty($global_info->end_date))
                                                            value="{{date('Y/m/d', strtotime($global_info->end_date))}}"
                                                        @else
                                                            value=""
                                                        @endif >
                                                        <select class="form-control" id="end_time" name="end_time">
                                                            <option value="">▼ 選択してください</option>
                                                            @foreach($TIME_ARRAY as $id => $data)
                                                                <option
                                                                    @if(isset($global_info->end_time) && !empty($global_info->end_time))
                                                                        @if ($data == date('H:i', strtotime($global_info->end_time))) selected @endif
                                                                    @endif
                                                                    value="{{$data}}">{{$data}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('end_date') ? $errors->first('end_date') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- r6 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                @if (empty($global_info->save_ol_name))
                                                    <div class="col-lg-6 form-group btn-upload-style">
                                                        <label>添付ファイル</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" id="save_ol_name" name="save_ol_name" class="custom-file-input">
                                                                <label class="custom-file-label" for="save_ol_name"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-lg-6 form-group">
                                                        <label>添付ファイル</label>
                                                        <div class="id-change">
                                                            <div class="form-control over-hidden">
                                                                <a class="over-hidden" href="{{route('global_info.download', ['id' => 0,'ol_name' =>$global_info->save_ol_name ,'sv_name' => $global_info->save_sv_name])}}" >{{$global_info->save_ol_name}}</a>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary" name="mode2" value="file_delete">削除</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">登録</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('global_info/index') }}">戻る</a>
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
<script type="text/javascript">
    $(document).ready(function() {
            //ファイル追加
            $('input[type=file]').change(function() {
                    objForm=document.MainForm;
                    objForm.mode1.value="add";
                    objForm.mode2.value="file_add";
                    objForm.action='{{ url('global_info/edit') }}';
                    objForm.submit();
                }
            );

            var headquarter=$("#department_id").find(':selected').attr('data-value');
            $("#fake_name").text(headquarter);
            $("#department_id").change(function() {
                    var headquarter_name=$(this).children("option:selected").data("value");
                    $("#fake_name").text(headquarter_name);
                    $("#headquarter_name").val(headquarter_name);
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
