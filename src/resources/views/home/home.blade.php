@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('home'))
<div class="container-fluid">
    @php
        $canUpdate = Auth::user()->can('update','App\Customer_MST');
        $canView =  Auth::user()->can('view','App\Customer_MST');
    @endphp
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">お知らせ</div>
                <div class="table-parent">
                    <table class="table table-bordered table-hover m-b-0">
                        <tbody>
                            @foreach($global_info as $set_data)
                                @switch($set_data->important_flg)
                                    @case("1")
                                        <tr class="danger">
                                        @break
                                    @case("2")
                                        <tr class="warning">
                                        @break
                                    @case("3")
                                        <tr class="active">
                                        @break
                                    @default
                                        <tr class="active">
                                        @break
                                @endswitch
                                @if (!empty($set_data->save_ol_name))
                                    <td class="hfsz text-center" width="5%">
                                        <a href="{{route('global_info.download', ['id' => $set_data->id,'ol_name' =>"__" ,'sv_name' => "__"])}}"
                                            title="{{$set_data->save_ol_name}}">
                                            <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>
                                        </a>
                                    </td>
                                @else
                                    <td width="5%"></td>
                                @endif
                                    <td class="hfsz" width="83%">{{$set_data->global_info_title }}{{"  :  "}}{!!$set_data->global_info_content_change!!}</td>
                                    <td class="hfsz" width="10%">{{date('Y/m/d', strtotime($set_data->updated_at))}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-danger">
                <div class="panel-heading">契約終了アラート</div>
                <div class="table-parent">
                    <table class="table table-bordered table-hover m-b-0">
                        <thead>
                            <tr>
                                <th  class="active ">申請番号</th>
                                <th  class="active ">顧客名</th>
                                <th  class="active ">期限</th>
                                <th  class="active ">申請本部</th>
                                <th  class="active ">申請部</th>
                                <th  class="active ">申請グループ</th>
                                <th  class="active ">参照</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contractAlert as $data)
                                @php($contractAction = Crofun::getPermissionContract($data, Auth::user(), true))
                                @if ($contractAction)
                                    <tr class="">
                                        <td width="">{{$data->application_num}}</td>
                                        <td class="" width="">{{$data->getCustomerName()}}</td>
                                        <td class="" width="">{{$data->contract_end_date}}</td>
                                        <td class="" width="">{{$data->headquarter->headquarters}}</td>
                                        <td class="" width="">{{$data->department->department_name}}</td>
                                        <td class="" width="">{{$data->group->group_name}}</td>
                                        <td class="" width="">
                                            <a class="btn btn-primary" href="{{route('contract.edit', ['id' => $data->id])}}">参照</a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-success">
                <div class="panel-heading">仮登録一覧</div>
                <div class="table-parent">
                    <table class="table table-bordered table-hover m-b-0">
                        <thead>
                            <tr>
                                <th  class="active ">登録日</th>
                                <th  class="active ">顧客名</th>
                                <th  class="active ">申請部署</th>
                                <th  class="active ">申請Gr</th>
                                <th  class="active ">参照</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($customer as $customer_date)
                                <tr>
                                    <td class="">{{  date('Y/m/d',strtotime($customer_date->created_at)) }}</td>
                                    <td class="">
                                        @if($customer_date->client_name_ab == null)
                                            {{  $customer_date->client_name }}
                                        @else
                                            {{  $customer_date->client_name_ab }}
                                        @endif
                                    </td>
                                    <td class="">{{  $customer_date->com_grp()->department_name }}</td>
                                    <td class="">{{  $customer_date->com_grp()->group_name }}</td>
                                    <td class="">
                                        @if ($canUpdate)
                                            <a href="{{ route('customer_edit', ['id' => $customer_date->id]) }}" class="btn btn-primary" style="float: left">参照</a>
                                        @elseif($canView)
                                            <a href="{{ route('customer_view', ['id' => $customer_date->id]) }}" class="btn btn-primary" style="float: left">参照</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-warning">
                <div class="panel-heading">与信限度額要確認</div>
                <div class="table-parent" id="receivable">
                    <table class="table table-bordered table-hover m-b-0">
                        <thead>
                            <tr>
                                <th class="active">顧客名</th>
                                <th class="active">対象月</th>
                                <th class="active ">与信限度額</th>
                                <th class="active ">取引想定額</th>
                                <th class="active ">売掛金残</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($over_receivable as $key => $value)
                            <tr>
                                <td class="">
                                    @if($value->client_name_ab == null)
                                        {{  $value->client_name }}
                                    @else
                                        {{  $value->client_name_ab }}
                                    @endif
                                </td>
                                <td class="">
                                    @if($receivable_date[$key]->target_data != null)
                                        {{  date('Y/m',strtotime($receivable_date[$key]->target_data)) }}
                                    @endif
                                </td>
                                <td class="">{{ number_format($value->credit_expect/1000) }}</td>
                                <td class="" @if($transaction_date[$key]> $value->credit_expect)  style = "background-color: #FFB6C1;" @endif
                                    >{{ number_format($transaction_date[$key]/1000) }}
                                </td>
                                <td class=""
                                    @if($receivable_date[$key]->receivable != "")
                                        @if($receivable_date[$key]->receivable > $value->credit_expect)
                                            style = "background-color: #FFB6C1;"
                                        @endif
                                    @endif>
                                    @if($receivable_date[$key]->receivable != "")
                                        {{  number_format($receivable_date[$key]->receivable/1000)}}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
