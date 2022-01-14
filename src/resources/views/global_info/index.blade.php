@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('global_info/index'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form action="{{ url('global_info/index') }}" id="form" method="POST">
                                    {{-- row 1 --}}
                                    <div class="col-lg-10 col-lg-offset-1 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 col-lg-offset-3 search-form">
                                                <div class="search-title col-xs-3">
                                                    <span class="">状態</span>
                                                </div>
                                                <div class="col-xs-9 search-item">
                                                    <select class="form-control" id="important_flg" name="important_flg">
                                                        <option value="">▼ 選択してください</option>
                                                        @foreach($sel_data as $id => $data)
                                                        <option @if(isset($important_flg_info)) @if($important_flg_info==$id) selected @endif @endif
                                                            value="{{$id}}">{{$data}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    @csrf
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="submit" id="search"
                                                class="search-button btn btn-primary btn-sm">検索</button>
                                        </div>
                                        <div class="col-xs-3">
                                            <button type="button" id="clear"
                                                class="clear-button btn btn-default btn-sm">クリア</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-lg-2 m-b-10">
                                        @if( Auth::user()->getRuleAction(230))
                                        <a href="{{ url('global_info/create') }}">
                                            <button type="submit"
                                                class="btn btn-primary btn-sm">新規登録</button>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="col-lg-3 col-lg-offset-9">
                                        @if(sizeof($global_infos) > 0)
                                        @paginate(['item'=>$global_infos]) @endpaginate
                                        @endif
                                    </div>
                                </div>
                                <div class="row table-parent">
                                    <table id="example" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="hfsz text-center">編集</th>
                                                <th width="25%" class="hfsz text-center">掲載期間</th>
                                                <th width="8%" class="hfsz text-center">重要度</th>
                                                <th width="25%" class="hfsz">タイトル</th>
                                                <th width="37%" class="hfsz">内容</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($global_infos as $global_info)
                                            <tr>
                                                <td class="hfsz text-center">
                                                    <a href="{{route('edit_global_info', ['id' => $global_info->id])}}"><button
                                                            type="submit" style="float: left;"
                                                            class="btn btn-info btn-sm">編集</button></a>
                                                </td>
                                                <td class="hfsz text-center">
                                                    @if (!empty($global_info->start_date))
                                                    {{  date('Y/m/d [H:i]', strtotime($global_info->start_date)) }}
                                                    @endif
                                                    ～
                                                    @if (!empty($global_info->end_date))
                                                    {{ date('Y/m/d [H:i]', strtotime($global_info->end_date)) }}</td>
                                                @endif
                                                <td class="hfsz text-center">
                                                    {{  $dis_data[$global_info->important_flg] }}</td>
                                                <td class="hfsz">{{  $global_info->global_info_title }}</td>
                                                <td class="hfsz">{{  $global_info->global_info_content }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    </li>
    </ul>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#department_id").prop("disabled", true);
        if ($("#headquarter_id").val() != "") {
            $("#department_id").prop("disabled", false);
            var headquarter_id = $("#headquarter_id").val();
            $(".department_id").each(function () {
                $(this).show();
                if ($(this).attr('data-value') !== headquarter_id) {
                    $(this).remove();
                }
            });
        }

        $("#clear").click(function () {
            $('#important_flg').prop('selectedIndex', 0);
            $("#check").prop("checked", false);
            $("#form").submit();
        });
    });
</script>
@endsection
