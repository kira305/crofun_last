@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('credit/create'))
<script type="text/javascript">
    $( window ).on( "load", function() {
        @if(isset($message))
        sweetAlert('{{$message}}','success');
        @endif

        @if(!$errors->isEmpty())
            sweetAlert('{{trans('message.save_fail')}}', 'error');
        @endif
    });
</script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form id="create_credit" method="post" action="{{ url('credit/create?company_id='.request()->company_id.'&client_id='.request()->client_id) }}">
                                    @csrf
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客コード</label>
                                                    <input type="text" id="client_code" value="{{ $client_code }}" name="client_code" class="form-control ">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客名</label>
                                                    <input type="text" id="client_name" name="client_name" value="{{ $client_name}}" class="form-control">
                                                    <input type="hidden" name="id" value="" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 2 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>希望与信限度額<sup class="color-red">1</sup></label>
                                                    <input type="text" name="credit_expect" id="credit_expect" value="{{ old('credit_expect')}}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('credit_expect') ? $errors->first('credit_expect') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>取引想定合計額<sup class="color-red">1</sup></label>
                                                    <input type="text" name="transaction" id="transaction" value="{{ number_format($transaction / 1000 )}}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 3 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>与信取得元<sup class="color-red">※</sup></label>
                                                    <select id="credit_division" class="form-control" name="credit_division">
                                                        <option selected value=""></option>
                                                        <option @if (old('credit_division')=='1' ) selected @endif value="1">リスクモンスター</option>
                                                        <option @if (old('credit_division')=='2' ) selected @endif value="2">東京商工リサーチ</option>
                                                        <option @if (old('credit_division')=='3' ) selected @endif value="3">帝国データバンク</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('credit_division') ? $errors->first('credit_division') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 4 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>格付け情報<sup class="color-red">※</sup></label>
                                                    <input type="text" name="rank" id="rank" value="{{ $rank_conversion }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('rank') ? $errors->first('rank') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>取得日<sup class="color-red">※</sup></label>
                                                    <input id="datepicker" autocomplete="off" value="{{ old('get_time')}}" type="text" name="get_time" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('get_time') ? $errors->first('get_time') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 5 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>与信期限</label>
                                                    <input id="expiration_date" value="{{(!empty($renew_time)) ? date('Y/m/d',strtotime($renew_time)) : '' }}" type="text" name="expiration_date" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('expiration_date') ? $errors->first('expiration_date') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>RM与信限度額<sup class="color-red">※,1</sup></label>
                                                    <input id="credit_limit" value="{{ old('credit_limit')}}" type="text" name="credit_limit" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('credit_limit') ? $errors->first('credit_limit') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 6 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>備考</label>
                                                    <textarea class="form-control" rows="3" name="note">{{ isset($note) ? $note : old('note') }}</textarea>
                                                    <p style="margin-top: 12px"><b class="color-red">1) </b><b class="color-header">1000円が省略された金額で表示されています。</b></p>
                                                    <p style="margin-top: 12px"><b class="color-red">※) </b><b class="color-header">RM情報を取り込むと、データ未入力であれば、RM情報からデータを設定します。</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2 col-lg-offset-2">
                                            <span class="btn btn-primary btn-file" class="input_lable">
                                                RM情報取込
                                                <input id="input_file" type="file" name="file_data">
                                            </span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="company_id" value="{{$company_id}}">
                                    <input type="hidden" id="client_id" name="client_id" value="{{$client_id}}">
                                    <input type="hidden" id="pre_url_status" name="pre_url_status" value="{{$pre_url_status}}">
                                    {{-- row update --}}
                                    <div class="row p-20">
                                        <div class="col-lg-12 ">
                                            <div class="col-xs-3 col-xs-offset-3">
                                                <button type="submit" class="btn btn-primary search-button">登録</button>
                                            </div>
                                            <div class="col-xs-3" >
                                                @if($pre_url_status == 1)
                                                <a href="{{route('customer_edit', ['id' => request()->client_id])}}">
                                                    <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                                </a>
                                                @elseif($pre_url_status == 2)
                                                <a href="{{route('customer_view', ['id' => request()->client_id])}}">
                                                    <button type="button" style="float: left" class="btn btn-danger search-button"> 戻る</button>
                                                </a>
                                                @else
                                                <a href="{{route('Credit_index')}}">
                                                    <button type="button" style="float: left" class="btn btn-danger search-button"> 戻る</button>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<script type="text/javascript">
    $('#datepicker').datepicker( {
            autoclose: true,
            todayHighlight: true,
            dateFormat: 'yy/mm/dd'
        }
    );

    //画面が開いた際
    $(document).ready(function() {
            $("#client_name").prop("readonly", true);
            $("#client_code").prop("readonly", true);
            $("#transaction").prop("readonly", true);
            $("#expiration_date").prop("readonly", true);

            $("#input_file").change(function() {
                    var csv=$('#input_file')[0].files[0];
                    var form=new FormData();
                    var client_id=$('#client_id').val();
                    form.append('csv', csv);
                    $.ajax( {
                            url: '/credit/upload?client_id='+client_id,
                            data: form,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success:function(response) {
                                if(response.status_code==400) {
                                    alert(response.message);
                                    return;
                                }
                                console.log(response);
                                set_data(response);
                            },
                            error: function (exception) {
                                alert(exception.responseText);
                                if(exception.status==500) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{trans('message.save_fail')}}',
                                        text: 'ファイルのルールはただしくありません。',
                                    })
                                }
                            }
                        }
                    );
                    event.preventDefault();
                }
            );
        }
    );

    function set_data(response) {
        var get_time=changeDate(response.get_time);
        $('#datepicker').val(get_time);
        $('#rank').val(response.rank);
        $('#credit_limit').val(response.credit_limit);
        $('#expiration_date').val(response.expiration_date);
        $('#credit_division').val(response.credit_division);
        $('#note').val(response.note);
        $("#rank").prop("readonly", true);
        $("#datepicker").prop("readonly", true);
        $("#expiration_date").prop("readonly", true);
        $('#credit_limit').prop("readonly", true);
        $("#datepicker").css('pointer-events', 'none');
    }

    function changeDate(data) {
        var date=new Date(data);
        var res=date.getFullYear()+'/'+('0'+ (date.getMonth()+1)).slice(-2)+'/'+('0'+ date.getDate()).slice(-2);
        return res;
    }

    function previous() {
        var base='{!! route("Credit_index") !!}';
        var url=base+'?client_id='+'{{ request()->client_id }}';
        window.location.href=url;
    }
</script>
@endsection
