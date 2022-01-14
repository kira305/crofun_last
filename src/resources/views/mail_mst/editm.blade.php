@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('mail_mst/editm'))
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
                                <form id="create_user" method="post" action="{{ url('mail_mst/editm') }}"
                                    enctype="multipart/form-data" name="MainForm">
                                    <input type="hidden" name="id" value="{{$mail_mst->id}}">
                                    <input type="hidden" name="mode1" value="update">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            {{-- r0 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>ID</label>
                                                    <div class="form-control">
                                                        {{$mail_mst->id}}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- r1 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>管理名称</label>
                                                    <input type="text" name="mail_ma_name" value="{{$mail_mst->mail_ma_name}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('mail_ma_name') ? $errors->first('mail_ma_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r2 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>メールタイトル</label>
                                                    <input type="text" name="mail_remark" value="{{$mail_mst->mail_remark}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('mail_remark') ? $errors->first('mail_remark') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r3 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>メール本文</label>
                                                    <textarea class="form-control" name="mail_text" rows="10">{{$mail_mst->mail_text}}</textarea>
                                                    <span class="text-danger">
                                                        {{ $errors->has('mail_text') ? $errors->first('mail_text') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="attention-box" style="padding: 10px;" >
                                                        <p class="attention-title" ><i class="glyphicon glyphicon-top glyphicon-exclamation-sign"></i>パラメータ情報</p>
                                                        <table class="table" style="margin-bottom: 0px">
                                                            <tr>
                                                                <td class="">##USER_ID##</td>
                                                                <td colspan="3">ログインユーザーID</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="">##USER_NAME##</td>
                                                                <td colspan="3">ログインユーザー名</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="">##USER_PASSWORD##</td>
                                                                <td colspan="3">ログインユーザーパスワード</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row search-form p-t-20">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">更新</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('mail_mst/indexm') }}">戻る</a>
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
@endsection
