@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('contract/createfile',$user->id))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <p class="message">{{ isset($message) ? $message : '' }}</p>
                                <form id="create_user" method="post" action="{{ url('contract/createfile'.'?contract_id='.$contract->id) }}">
                                    @csrf
                                    <div class="row">
                                        {{-- r1 --}}
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>社員番号</label>
                                                    <input type="text" value="{{$user->usr_code}}" id="user_code"  class="form-control">
                                                    <input type="hidden" value="{{$user->id}}" name="usr_id">
                                                    <input type="hidden" name="usr_code" value="{{$user->usr_code}}">
                                                    <div class="text-danger">
                                                        {{ $errors->has('usr_code') ? $errors->first('usr_code') : '' }}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>社員名</label>
                                                    <input type="text" id="user_name" value="{{$user->usr_name}}" class="form-control">
                                                    <input type="hidden" name="usr_name" value="{{$user->usr_name}}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('usr_name') ? $errors->first('usr_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-12 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">登録</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{route('edituserinfor', ['id' => $user->id])}}">戻る</a>
                                                    </div>
                                                </div>
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
<script src="https://code.jquery.com/jquery-1.12.4.min.js"
    integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous">
</script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
            $("#user_code").prop("disabled", true);
            $("#user_name").prop("disabled", true);
            $("#headquarter_id").prop("disabled", true);
            $("#department_id").prop("disabled", true);
            $("#group_id").prop("disabled", true);

            if($("#company_id").val() !="") {
                $("#headquarter_id").prop("disabled", false);
                var company_id=$("#company_id").val();
                $(".headquarter_id").each(function() {
                        $(this).show();
                        if($(this).attr('data-value') !==company_id) {
                            $(this).hide();
                        }
                    }
                );
            }

            if($("#headquarter_id").val() !="") {
                $("#department_id").prop("disabled", false);
                var headquarter_id=$("#headquarter_id").val();
                $(".department_id").each(function() {
                        $(this).show();
                        if($(this).attr('data-value') !==headquarter_id) {
                            $(this).hide();
                        }
                    }
                );
            }

            if($("#departmen_id").val() !="") {
                $("#group_id").prop("disabled", false);
                var department_id=$("#department_id").val();
                $(".group_id").each(function() {
                        $(this).show();
                        if($(this).attr('data-value') !==department_id) {
                            $(this).hide();
                        }
                    }
                );
            }
        }
    );

    $(document).on('change', '#company_id', function () {
            $('#headquarter_id').prop('selectedIndex', 0);
            $('#department_id').prop('selectedIndex', 0);
            $('#group_id').prop('selectedIndex', 0);
            $("#headquarter_id").prop("disabled", false);
            var company_id=$("#company_id").val();

            $(".headquarter_id").each(function() {
                    $(this).show();
                    if($(this).attr('data-value') !==company_id) {
                        $(this).hide();
                    }
                }
            );
        }
    );

    $(document).on('change', '#headquarter_id', function () {
            $('#department_id').prop('selectedIndex', 0);
            $("#department_id").prop("disabled", false);
            var headquarter_id=$("#headquarter_id").val();
            $(".department_id").each(function() {
                    $(this).show();
                    if($(this).attr('data-value') !==headquarter_id) {
                        $(this).hide();
                    }
                }
            );
        }
    );

    $(document).on('change', '#department_id', function () {
            $('#group_id').prop('selectedIndex', 0);
            $("#group_id").prop("disabled", false);
            var department_id=$("#department_id").val();
            $(".group_id").each(function() {
                    $(this).show();
                    if($(this).attr('data-value') !==department_id) {
                        $(this).hide();
                    }
                }
            );
        }
    );
</script>
@endsection
