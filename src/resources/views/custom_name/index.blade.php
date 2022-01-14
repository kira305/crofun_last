@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('custom_name/index'))
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form action="{{ url('custom_name/index') }}" id="form" method="post">
                                    {{-- row 1 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        @foreach($companies as $company)
                                                        <option @if(session()->has('company_id_name'))
                                                            @if(session('company_id_name') == $company->id) selected @endif
                                                            @else
                                                            @if(Auth::user()->company_id == $company->id) selected @endif
                                                            @endif
                                                            value="{{$company->id}}">{{$company->abbreviate_name}}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">法人番号</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input type="text" value="{{session('corporation_num_name')}}"
                                                    name="corporation_num" id="corporation_num" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">顧客コード</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input type="text" value="{{session('client_code_name')}}"
                                                    name="client_code" id="client_code" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">顧客名カナ</span>
                                                </div>
                                                <div class="col-xs-7 col-md-8 search-item">
                                                    <input id="client_name_kana"
                                                    value="{{session('client_name_kana_name')}}" name="client_name_kana"
                                                    type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-5 col-md-4">
                                                    <span class="">検索対象顧客名カナ</span>
                                                </div>
                                                <div class="search-item col-xs-7 col-md-8">
                                                    <input id="target_client_name_kana"
                                                    value="{{session('target_client_name_kana_name')}}"
                                                    name="target_client_name_kana" type="text"
                                                    class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    @csrf
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="submit" id="search" class="search-button btn btn-primary btn-sm">検索</button>
                                        </div>
                                        <div class="col-xs-3">
                                            <button type="button" id="clear" class="clear-button btn btn-default btn-sm">クリア</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-offset-9">
                                    @paginate(['item'=> $custom_name]) @endpaginate
                                </div>
                            </div>
                            <div class="row table-parent">
                                <table id="customer_name_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>顧客コード</th>
                                            <th>顧客名</th>
                                            <th>顧客名ｶﾅ</th>
                                            <th>検索対象顧客名ｶﾅ</th>
                                            <th>検索対象外</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($custom_name as $customname)
                                        <tr>
                                            <td>
                                                @if($customname->customer->client_code_main == null)
                                                    {{  $customname->customer->client_code }}
                                                @else
                                                    {{  $customname->customer->client_code_main }}
                                                @endif
                                            </td>
                                            <td>{{  $customname->customer->client_name }}</td>
                                            <td>{{  $customname->customer->client_name_kana }}</td>
                                            <td>{{  $customname->client_name_hankaku_s }}</td>
                                            <td>
                                                @if($customname->del_flag == false)
                                                    {{ "検索対象" }}
                                                @else
                                                    {{ "検索対象外" }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($customname->client_name_hankaku_s != $customname->customer->client_name_kana)
                                                    @if($customname->del_flag == false)
                                                        <button data-value="{{$customname->id}}" class="btn btn-danger delete btn-sm">削除</button>
                                                    @endif
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
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).on('click', '#clear', function () {
        //1ページに移動
        $('#company_id').prop('selectedIndex',0);
        $('#client_code').val('');
        $('#corporation_num').val('');
        $('#client_name_kana').val('');
        $("#target_client_name_kana" ).val('');
        $( "#form" ).submit();
	});

    $(".delete").click(function() {
            var id=$(this).data('value');
            $.confirm( {
                    title: 'このデータを削除しますか',
                    content: '',
                    type: 'red',
                    typeAnimated: true,
                    buttons: {
                        delete: {
                            text: 'YES',
                            btnClass: 'btn-blue',
                            with :'100px',
                            action: function() {
                                document.location.href="/custom_name/delete?id="+id;
                            }
                        },
                        cancel: {
                            text: 'NO',
                            btnClass: 'btn-red',
                            action: function() {}
                        }
                    }
                }
            );
        }
    );
</script>
@endsection
