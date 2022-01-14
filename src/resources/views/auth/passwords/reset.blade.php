@extends('layouts.auth')

@section('content')

<div class="login-box" style="width: 400px">

  <!-- /.login-logo -->
  <div class="login-box-body">
     <div class="login-logo">

     </div>

     <h3 style="margin-left: 130px;">Cro-Fun</h3>

   <!--  <p class="login-box-msg">Sign in to start your session</p> -->
    <form action="/user/reset-password" method="post">

       <div class="form-group has-feedback">
        <label>社員番号</label>
        <input  class="form-control" value="{{ old('employee_id')}}" name="employee_id" >
         @csrf
         @if ($errors->has('employee_id'))

          <span class="text-danger">{{ $errors->first('employee_id') }}</span>

        @endif
      </div>
      <div class="form-group has-feedback">
        <label>メールアドレス</label>
        <input type="email" class="form-control" value="{{ old('email')}}" name="email">
       @if ($errors->has('email'))

          <span class="text-danger">{{ $errors->first('email') }}</span>

        @endif
      </div>
        @if (isset($message))

          <span class="text-danger">{{ $message }}</span>

        @endif
      <div class="row">
        <div class="col-xs-3">

        </div>

        <div class="col-xs-3">
          <button type="submit" class="btn btn-primary btn-block btn-flat">再発行</button>
        </div>
        <div class="col-xs-3">
          <a href="{{ url('/') }}" class="btn btn-default btn-block btn-flat">戻る</button></a>
        </div>

        <div class="col-xs-3">

        </div>

      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
@endsection
