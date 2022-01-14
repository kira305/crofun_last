@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('position/edit'))
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
                                <form id="create_user" method="post" action="{{ url('position/edit') }}"　enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            {{-- r1 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>所属会社</label>
                                                    <div class="form-control" disabled>
                                                        {{$position->company->abbreviate_name}}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- r2 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>役職</label>
                                                    <input type="text" name="position_name" value="{{ $position->position_name }}" class="form-control">
                                                    <input type="hidden" name="id" value="{{ $position->id }}"
                                                    class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('position_name') ? $errors->first('position_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r4 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>参照範囲</label>
                                                    <select class="form-control" name="look">
                                                        <option {{ $position->company_look == true ? 'selected' : '' }} value="1">全事業本部</option>
                                                        <option {{ $position->headquarter_look == true ? 'selected' : '' }} value="2">所属事業本部のみ</option>
                                                        <option {{ $position->department_look == true ? 'selected' : '' }} value="3">所属部署のみ</option>
                                                        <option {{ $position->group_look == true ? 'selected' : '' }} value="4">所属Grpのみ</option>
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('look') ? $errors->first('look') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- r5 --}}
                                            <div class="row search-form">
                                                <div class="col-lg-4 form-group">
                                                    <label class="checkbox-title">与信限度額越えメールフラグ</label>
                                                    <div class="icheck-primary d-inline ">
                                                        <input type="checkbox" name="mail_flag" id="mail_flag"　@if($position->mail_flag == true) checked @endif　class="input_checkbox">
                                                        <label for="mail_flag"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row search-form p-t-20">
                                                <div class="col-lg-6 form-group btn-upload-style">
                                                    <div class="col-xs-3 col-xs-offset-3">
                                                        <button type="submit" class="btn btn-primary search-button">更新</button>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        @php($page = session()->has('position.page') ? '?page='.session('position.page') : '')
                                                        <a style="float: left" class="btn btn-danger search-button" href="{{ url('position/index'.$page) }}">戻る</a>
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
@endsection
