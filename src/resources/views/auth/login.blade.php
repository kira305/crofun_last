@extends('layouts.auth')
@section('content')
<style>
    .ab-c-t {
    position: absolute;
    top: 0px;
    left: 50%;
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
    }
</style>
<div class="login-box">

    <!-- /.login-logo -->
    <div class="login-box-body">
        <h2 style="font-family: Times New Roman, Times, serif;text-align: center">Cro-Fun</h2>
        <span class="text-danger">{!! \Session::get('message') !!}</span>
        <form action="{{ url('/login') }}" method="post" autocomplete="off" id="cross-form">
            {{ csrf_field() }}
            @if (isset($ok_message))
            <br><span class="text-info">{{ $ok_message }}</span>
            @endif

            <div class="form-group has-feedback">
                <label>社員番号</label>

                <input class="form-control" id="usr_id" name="usr_code" type="interger" autocomplete="nope"
                    value="{{ empty(old('usr_code')) ? Cookie::get('usr_code') :  old('usr_code') }}">
                @if ($errors->has('usr_code'))

                <br><span class="text-danger">{{ trans('auth.username') }}</span>

                @endif
                @csrf
            </div>
            <div class="form-group has-feedback">
                <label>パスワード</label>
                <input type="password" id="password" class="form-control" name="pw" autocomplete="new-password" value="">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('pw'))
                    <br><span class="text-danger">{{ trans('auth.password') }}</span>
                @endif
            </div>
            <div>
                @if (isset($message))
                    <span class="text-danger">{{ $message }}</span>
                @endif
            </div>
            <div class="form-group has-feedback" style="text-align: right">
                    <button type="submit" class="btn btn-primary">ログイン</button>
            </div>
        </form>
        <a href="{{ url('user/reset-password') }}">パスワードを忘れた場合</a><br>
    </div>
</div>
<script type="text/javascript">
    $( document ).ready(function() {
        document.getElementById("cross-form").reset();
    });
</script>
@endsection
