@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('mail_mst/indexm'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-lg-2 col-lg-offset-1">
                                        @if( Auth::user()->getRuleAction(11))
                                        <a href="{{ url('mail_mst/createm') }}">
                                            <button type="submit" style="float: left;"
                                                class="btn btn-primary">新規登録</button>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="col-lg-3 col-lg-offset-8">
                                        @paginate(['item'=>$mail_msts]) @endpaginate
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-10 col-lg-offset-1 table-parent fix-mobile-col">
                                        <table id="example" class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="hfsz text-center">編集</th>
                                                    <th width="5%" class="hfsz text-center">ID</th>
                                                    <th width="30%" class="hfsz text-left">管理名称</th>
                                                    <th width="60%" class="hfsz text-left">メールタイトル</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($mail_msts as $mail_mst)
                                                <tr>
                                                    <td class="hfsz text-center">
                                                        <a href="{{route('edit_mail_mst', ['id' => $mail_mst->id])}}">
                                                            <button type="submit" style="float: left;" class="btn btn-info btn-xs">編集</button>
                                                        </a>
                                                    </td>
                                                    <td class="hfsz text-center">{{  $mail_mst->id }}</td>
                                                    <td class="hfsz">{{  $mail_mst->mail_ma_name }}</td>
                                                    <td class="hfsz">{{  $mail_mst->mail_remark }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
