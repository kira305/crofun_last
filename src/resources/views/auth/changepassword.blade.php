@extends('layouts.auth')

@section('content')

<div class="login-box" style="width: 400px">

  <!-- /.login-logo -->
  <div class="login-box-body">
     <div class="login-logo">
     </div>
     <h3 style="margin-left: 130px;">Cro-Fun</h3>
   <!--  <p class="login-box-msg">Sign in to start your session</p> -->
    <form action="/auth/remove-password" method="post">
		{{ csrf_field() }}
       @if (isset($message))
                  <br><span class="text-info">{{ $message }}</span>
       @endif
      <div class="form-group has-feedback">
        <label>旧パスワード</label>
        <input  id="password1" class="form-control" type="password" name="pw1">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
       @if ($errors->has('pw1'))
           <br><span class="text-danger">{{ trans($errors->first("pw1")) }}</span>
       @endif
      </div>

      <div class="form-group has-feedback">
        <label>新パスワード</label>
        <input  id="password2" class="form-control" type="password" name="pw2">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
       @if ($errors->has('pw2'))
           <br><span class="text-danger">{{ trans($errors->first("pw2")) }}</span>
        @endif
      </div>

      <div class="form-group has-feedback">
        <label>新パスワード(確認)</label>
        <input  id="password3" type="password" class="form-control"  name="pw3">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
       @if ($errors->has('pw3'))
           <br><span class="text-danger">{{ trans($errors->first("pw3")) }}</span>
        @endif
      </div>

      <div class="row">
        <div class="col-xs-3">

        </div>

        <div class="col-xs-3">
          <button type="submit" class="btn btn-primary btn-block btn-flat">更新</button>
        </div>
        <div class="col-xs-3">
          <a href="{{ url('/') }}"><button type="button" class="btn btn-default btn-block btn-flat">戻る</button></a>
        </div>

        <div class="col-xs-3">

        </div>

      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
@endsection
