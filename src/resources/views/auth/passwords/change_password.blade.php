@extends('layouts.app')

@section('content')
@section('breadcrumbs', Breadcrumbs::render('password/change'))
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
                                    <form id="create_user" method="post" action="{{ url('password/change') }}"
                                        enctype="multipart/form-data">
                                        {{-- row 1 --}}
                                        <div class="col-lg-8 col-lg-offset-2 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-9 col-md-offset-1 search-form">
                                                    <div class="search-title col-xs-4">
                                                        <span class="">社員番号</span>
                                                    </div>
                                                    <div class="col-xs-8 search-item">
                                                        <input type="text" disabled value="{{ Auth::user()->usr_code }}"
                                                            class="form-control">
                                                        <input type="hidden" name="usr_code"
                                                            value="{{ Auth::user()->usr_code }}">
                                                        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                                                    </div>
                                                    @if ($errors->has('usr_code'))
                                                        <div class="text-danger">{{ $errors->first('usr_code') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 2 --}}
                                        <div class="col-lg-8 col-lg-offset-2 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-9 col-md-offset-1 search-form">
                                                    <div class="search-title col-xs-4">
                                                        <span class="">現在のPW</span>
                                                    </div>
                                                    <div class="col-xs-8 search-item">
                                                        <input name="now_pass" type="password" value="{{ old('now_pass') }}"
                                                            class="form-control">
                                                    </div>
                                                    @if ($errors->has('now_pass'))
                                                        <div class="text-danger">{{ $errors->first('now_pass') }}</div>
                                                    @endif
                                                    @if ($errors->has('correct'))
                                                        <div class="text-danger">{{ $errors->first('correct') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 3 --}}
                                        <div class="col-lg-8 col-lg-offset-2 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-9 col-md-offset-1 search-form">
                                                    <div class="search-title col-xs-4">
                                                        <span class="">新しいPW</span>
                                                    </div>
                                                    <div class="col-xs-8 search-item">
                                                        <input name="new_pass_1" type="password"
                                                            value="{{ old('new_pass_1') }}" class="form-control">
                                                    </div>
                                                    @if ($errors->has('new_pass_1'))
                                                        <div class="text-danger">{{ $errors->first('new_pass_1') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- row 4 --}}
                                        <div class="col-lg-8 col-lg-offset-2 col-md-12">
                                            <div class="row search-row">
                                                <div class="col-md-9 col-md-offset-1 search-form">
                                                    <div class="search-title col-xs-4">
                                                        <span class="">新しいPW確認</span>
                                                    </div>
                                                    <div class="col-xs-8 search-item">
                                                        <input name="new_pass_2" type="password"
                                                            value="{{ old('new_pass_2') }}" class="form-control">
                                                    </div>
                                                    @if ($errors->has('new_pass_2'))
                                                        <div class="text-danger">{{ $errors->first('new_pass_2') }}</div>
                                                    @endif
                                                    @if ($errors->has('new_pass_retype'))
                                                        <div class="text-danger">{{ $errors->first('new_pass_retype') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        {{-- end --}}
                                        @csrf
                                        <div class="col-lg-12 ">
                                            <div class="col-xs-3 col-xs-offset-3">
                                                <button type="submit" class="search-button btn btn-primary btn-sm">登録</button>
                                            </div>
                                            <div class="col-xs-3">
                                                <a href="{{ url('/home') }}">
                                                    <button type="button" class="clear-button btn btn-danger btn-sm" >戻る</button>
                                                </a>
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
