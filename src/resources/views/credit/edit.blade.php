@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('credit/edit'))
<script type="text/javascript" src="{{ asset('js/digaram_datepicker.js') }}"></script>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form id="edit_credit" method="post" action="{{ url('credit/edit') }}">
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客コード</label>
                                                    <input type="text" id="client_code" name="client_code" class="form-control " disabled
                                                    @if($credit->customer->client_code_main != null)
                                                        value="{{ $credit->customer->client_code_main }}"
                                                    @else
                                                        value="{{ $credit->customer->client_code }}"
                                                    @endif>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客名</label>
                                                    <input type="text" id="client_name" name="client_name" value="{{  $credit->customer->client_name }}" class="form-control" disabled>
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
                                                    <input type="text" name="credit_expect" id="credit_expect" disabled value="{{ number_format( $credit->credit_expect  /1000) }}" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>取引想定合計額<sup class="color-red">1</sup></label>
                                                    <input type="text" name="transaction" id="transaction" disabled value="{{ number_format($transaction / 1000 )}}" class="form-control">
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
                                                    <select id="credit_division" class="form-control" name="credit_division" disabled>
                                                        <option selected value=""></option>
                                                        <option @if ($credit->credit_division =='1' ) selected @endif value="1">リスクモンスター</option>
                                                        <option @if ($credit->credit_division =='2' ) selected @endif value="2">東京商工リサーチ</option>
                                                        <option @if ($credit->credit_division =='3' ) selected @endif value="3">帝国データバンク</option>
                                                    </select>
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
                                                    <input type="text" disabled name="rank" id="rank" value="{{ $credit->rank }}" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>取得日<sup class="color-red">※</sup></label>
                                                    <input id="datepicker" disabled autocomplete="off" value="{{ date('Y/m/d',strtotime($credit->get_time)) }}" type="text" name="get_time" class="form-control">
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
                                                    <input id="expiration_date" readonly value="{{ (!empty($credit->expiration_date)) ? date('Y/m/d',strtotime($credit->expiration_date)) : ''}}" type="text" name="expiration_date" class="form-control">
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>RM与信限度額<sup class="color-red">※,1</sup></label>
                                                    <input id="credit_limit" readonly value="{{ number_format( $credit->credit_limit /1000 ) }}" type="text" name="credit_limit" class="form-control">
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
                                                    <textarea class="form-control" disabled rows="3" name="note">{{ $credit->note }}</textarea>
                                                    <p style="margin-top: 12px"><b class="color-red">1) </b><b class="color-header">1000円が省略された金額で表示されています。</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            @if(request()->client_id != null)
                                                <a href="{{route('Credit_log', ['client_id' => request()->client_id ,'page'=>request()->page])}}"
                                                    style="float: right;width: 100px;" class="btn btn-danger">戻る</a>
                                            @else
                                                <a href="{{route('Credit_log', ['page'=>request()->page])}}"
                                                    style="float: right;width: 100px;" class="btn btn-danger">戻る</a>
                                            @endif
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

<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
