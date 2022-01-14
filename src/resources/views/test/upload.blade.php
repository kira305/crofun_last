
@extends('layouts.app')

@section('content')
<style type="text/css">

</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">契約書アップロード</div>

						<form class="form-horizontal"  method="post" action="{{url('/upload')}}" enctype="multipart/form-data">
							 {{ csrf_field() }}
						  <div class="form-group">
						    <label class="control-label col-sm-2" for="email">備考:</label>
						    <div class="col-sm-10">
						      <input  class="form-control" name="note" placeholder="入力してください">
						    </div>
						  </div>
						  <div class="form-group">
						    <label class="control-label col-sm-2" for="pwd">ファイル:</label>
						    <div class="col-sm-10">
						       <input type="file" class="form-control" name="bookcover"/>
						    </div>
						  </div>

						  <div class="form-group">
						    <div class="col-sm-offset-2 col-sm-10">
						      <button type="submit" class="btn btn-default">Submit</button>
						    </div>
						  </div>
						</form>


            </div>
        </div>
    </div>
</div>
@endsection
