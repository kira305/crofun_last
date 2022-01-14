@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('system/index'))
<script type="text/javascript">
    $( window ).on( "load", function() {
        @if(isset($message) && $message['color'] == 'green')
        sweetAlert('{{$message['msg']}}','success');
        @endif

        @if(isset($message) && $message['color'] == 'red')
            sweetAlert('{{$message['msg']}}', 'error');
        @endif
    });
</script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <form id="systemf" action="{{action('SystemController@confirmation')}}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="disabled_ctl" value="{{$disabled_ctl}}">
                                <div class="top">
                                    @if($disabled_ctl != "conf")
                                        @if( Auth::user()->getRuleAction(19))
                                            <a href="javascript:form.submit()">
                                                <button type="submit" name="act_bnt" value="row_add" style="float: none;" class="btn btn-success">項目追加</button>
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                <span class="clearfix"></span>
                                <div class="row table-parent">
                                    <table id="sys_tbl" class="table table-sm table-hover table-condensed ">
                                        <thead>
                                            <tr>
                                                <th style="padding-bottom: 0px;width: 6%">編集</th>
                                                <th style="padding-bottom: 0px;">管理グループ </th>
                                                <th style="padding-bottom: 0px;"> 管理キー </th>
                                                <th style="padding-bottom: 0px;">設定内容 </th>
                                                <th style="padding-bottom: 0px;">説明 </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($system_datas as $set_item)
                                                <tr>
                                                    <td align="center">
                                                        <input type="checkbox" class="checkbox-center"
                                                            name="m_s1[{{$set_item['f_system_info_key']}}][edit_flg]"
                                                            value="1" @if ($set_item['edit_flg']==1) checked @endif
                                                            @if($disabled_ctl=="conf" ) disabled="" @endif>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="10"
                                                            name="m_s1[{{$set_item['f_system_info_key']}}][f_setting_group]"
                                                            value="{{$set_item['f_setting_group']}}"
                                                            class="form-control  input-sm" @if($disabled_ctl=="conf" )
                                                            disabled="" @endif>
                                                    </td>
                                                    @if (!empty($set_item['dis_Error']))
                                                    <td class="{{$set_item['dis_Error']}}">
                                                        @else
                                                    <td>
                                                        @endif
                                                        <input type="text" size="10"
                                                            name="m_s1[{{$set_item['f_system_info_key']}}][f_setting_name]"
                                                            value="{{$set_item['f_setting_name']}}"
                                                            class="form-control  input-sm" @if($disabled_ctl=="conf" )
                                                            disabled="" @endif>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="50"
                                                            name="m_s1[{{$set_item['f_system_info_key']}}][f_setting_data]"
                                                            value="{{$set_item['f_setting_data']}}"
                                                            class="form-control  input-sm" @if($disabled_ctl=="conf" )
                                                            disabled="" @endif>
                                                    </td>
                                                    <td>
                                                        <input type="text" size="60"
                                                            name="m_s1[{{$set_item['f_system_info_key']}}][f_setting_nm]"
                                                            value="{{$set_item['f_setting_nm']}}"
                                                            class="form-control  input-sm" @if($disabled_ctl=="conf" )
                                                            disabled="" @endif>
                                                    </td>
                                                    <input type="hidden"
                                                        name="m_s1[{{$set_item['f_system_info_key']}}][new_ent]"
                                                        value="{{$set_item['new_ent']}}">
                                                    <input type="hidden"
                                                        name="m_s1[{{$set_item['f_system_info_key']}}][f_system_info_key]"
                                                        value="{{$set_item['f_system_info_key']}}">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div style="text-align:center;">
                                        @if ($disabled_ctl == "")
                                            <a href="javascript:form.submit()">
                                                <button type="submit" name="act_bnt" value="row_conf" class="btn btn-primary btn-lg">確認</button>
                                            </a>
                                        @elseif ($disabled_ctl == "conf")
                                            <a href="{{ url('system/update') }}">
                                                <button type="submit" name="act_bnt" value="row_upd" class="btn btn-primary btn-lg">更新</button>
                                            </a>
                                            <a>
                                                <button type="submit" name="act_bnt" value="row_back" class="btn btn-danger btn-lg">戻る</button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <input type="hidden" id="flag" value="0">
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '#clear', function () {
        $('#company_id').prop('selectedIndex',0);
        $('#headquarter_id').prop('selectedIndex',0);
        $('#department_id').prop('selectedIndex',0);
        $('#group_id').prop('selectedIndex',0);
        $('#type').prop('selectedIndex',0);
        $('#cost_code').val('');
        $('#cost_name').val('');
        $( "#check" ).prop( "checked", false );
        $( "#form" ).submit();
    });

    $(document).ready(function() {
        $( "#headquarter_id" ).prop( "disabled", true );
        $( "#department_id" ).prop( "disabled", true );
        $( "#group_id" ).prop( "disabled", true );
        $(".user_row").click(function(){
            if($('#flag').val() === '0'){
                $('.concurrent').show();
                $('#flag').val('1');
            } else {
                $('.concurrent').hide();
                $('#flag').val('0');
            }
        });
    });
</script>
@endsection
