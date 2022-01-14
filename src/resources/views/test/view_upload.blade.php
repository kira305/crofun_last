
@extends('layouts.app')

@section('content')
<style type="text/css">

</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">契約書アップロード</div>

					<div class="col-md-6 offset-md-3">
					    <div class="card">

					        <div class="card-body">
					            <h4 class="card-title">ファイル名: {{ $file->file_name}}</h4>
					            <p class="card-text">
					                ファイル <strong>{{ $file->file_name}}</strong> は　{{ Auth::user()->username }} によるアップロードされました。
					            </p>
					            <p>備考：{{ $file->note}}</p>
					            <a href="/dowload"><input type="button" value="Dowload"></a>
					        </div>
					    </div>
					</div>

            </div>
        </div>
    </div>
</div>
@endsection
