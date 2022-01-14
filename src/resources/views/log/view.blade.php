@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('log/view'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <div class="row">
                                    {{-- r1 --}}
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        <div class="row search-form">
                                            <div class="col-lg-6 form-group">
                                                <label>所属会社</label>
                                                <div class="form-control">
                                                    @if($log->company){{$log->company->abbreviate_name}}@endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 form-group">
                                                <label>処理区分</label>
                                                <div class="form-control">
                                                    {{$log->process}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- r2 --}}
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        <div class="row search-form">
                                            <div class="col-lg-6 form-group">
                                                <label>操作画面</label>
                                                <div class="form-control">
                                                    @if($log->menu){{$log->menu->link_name}}@endif
                                                </div>
                                            </div>
                                            <div class="col-lg-6 form-group">
                                                <label>対応テーブル</label>
                                                <div class="form-control">
                                                    @if($log->table){{$log->table->table_name}}@endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- r3 --}}
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        <div class="row search-form">
                                            <div class="col-lg-6 form-group">
                                                <label>変更されたデータのコード</label>
                                                <div class="form-control">
                                                    {{$log->code}}
                                                </div>
                                            </div>
                                            <div class="col-lg-6 form-group">
                                                <label>変更されたデータの名称</label>
                                                <div class="form-control">
                                                    {{$log->name}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- r4 --}}
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        <div class="row search-form">
                                            <div class="col-lg-6 form-group">
                                                <label>操作ユーザー</label>
                                                <div class="form-control">
                                                    {{$log->user->usr_name}}
                                                </div>
                                            </div>
                                            <div class="col-lg-6 form-group">
                                                <label>操作日</label>
                                                <div class="form-control">
                                                    {{$log->created_at}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                        @if(isset($new_date))
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>変更項目</th>
                                                    <th>変更前データ</th>
                                                    <th>変更後データ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($base as $key => $value)
                                                <tr
                                                    @if($log->process == config('constant.operation_UPDATE'))
                                                        @if(isset($old_date[$key]))
                                                            @if(isset($new_date[$key]))
                                                                @if (is_array($new_date[$key]) && is_array($old_date[$key]))
                                                                    @if($new_date[$key] != $old_date[$key])
                                                                        class = "difference"
                                                                    @endif
                                                                @else
                                                                    @if(strcmp($new_date[$key],$old_date[$key]) !=0)
                                                                        class = "difference"
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if(isset($new_date[$key])&& $new_date[$key]!=null)
                                                                class = "difference"
                                                            @endif
                                                        @endif
                                                    @endif
                                                    >
                                                    <td>{{  $item[$key] }} </td>
                                                    <td>
                                                        @if(is_array($old_date))
                                                            @if(isset($old_date[$key]))
                                                                @if (is_array($old_date[$key]))
                                                                    {{ Crofun::toPgArray($old_date[$key]) }}
                                                                @else
                                                                    {{  $old_date[$key] }}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($new_date[$key]))
                                                            @if (is_array($new_date[$key]))
                                                                {{ Crofun::toPgArray($new_date[$key]) }}
                                                            @else
                                                                {{  $new_date[$key] }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif
                                        <div class="row search-form">
                                            <div class="col-lg-12 form-group btn-upload-style">
                                                <div class="col-xs-3 col-xs-offset-6">
                                                    <a style="float: left" class="btn btn-danger search-button" href="{{ url('loxg/index?page='.request()->page) }}">戻る</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection
