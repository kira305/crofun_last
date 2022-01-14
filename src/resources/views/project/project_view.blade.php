@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('project/view'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            @php
                                $contractEditId = '';
                                $contractViewId = '';
                                if(isset(request()->contract_edit_id))
                                    $contractEditId = "&contract_edit_id=".request()->contract_edit_id;
                                elseif(isset(request()->contract_view_id)){
                                    $contractViewId = "&contract_view_id=".request()->contract_view_id;
                                }
                            @endphp
                            {{-- row 1 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>会社コード</label>
                                            <div readonly class="form-control">{{ $project->company->abbreviate_name }}</div>
                                            <input type="hidden" id="company_id" value="{{$project->company_id}}" name="company_id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 2 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>顧客コード</label>
                                            <div class="id-change">
                                                <div readonly class="form-control">{{ Crofun::getClientById($project->client_id)->client_code_main }}</div>
                                                <input type="hidden" id="client_id" value="{{ $project->client_id }}">
                                                @if (Auth::user()->can('customer-edit'))
                                                    <a class="btn btn-primary"
                                                        href="{{ route('customer_edit', ['id' => $project->client_id]) }}">顧客情報参照
                                                    </a>
                                                @else
                                                    @if (Auth::user()->can('customer-view'))
                                                        <a class="btn btn-primary"
                                                            href="{{ route('customer_view', ['id' => $project->client_id, 'see' => 1]) }}">顧客情報参照
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                            <input type="hidden" id="client_id" value="{{ $project->client_id }}">
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>顧客名</label>
                                            <div readonly class="form-control">
                                                {{ Crofun::getClientById($project->client_id)->client_name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 3 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>与信希望限度額 (顧客単位)<sup class="color-red">1</sup></label>
                                            <input type="text" readonly name="credit_expect" id="credit_expect"
                                            @if (isset($credit_expect)) value={{ number_format($credit_expect/1000)}} @endif class="form-control">
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>取引想定合計額(顧客単位)<sup class="color-red">1</sup></label>
                                            <input readonly type="text" id="transaction" name="transaction"
                                            @if (isset($transaction)) value="{{  number_format($transaction/1000)}}" @endif class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 4 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>プロジェクトコード</label>
                                            <input type="text" readonly value="{{ $project->project_code }}" class="form-control">
                                            <input type="hidden" id="project_id" name="id" value="{{ $project->id }}">
                                            <input type="hidden" name="project_code" id="project_code" value="{{ $project->project_code }}">
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>プロジェクト名</label>
                                            <input type="text" id="project_name" readonly name="project_name" value="{{ $project->project_name }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 5 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>事業本部</label>
                                            <input readonly type="text" value="{{$project->headquarter->headquarters}}" name="" class="form-control">
                                            <input type="hidden" id="headquarter_id" name="headquarter_id" value="{{$project->headquarter->id}}">
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>部署</label>
                                            <input readonly type="text" value="{{$project->department->department_name}}" name="" class="form-control">
                                            <input type="hidden" id="department_id" name="department_id" value="{{$project->department->id}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 6 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>担当Grp</label>
                                            <input readonly type="text" value="{{$project->group->group_name}}" name="" class="form-control">
                                            <input type="hidden" id="group_id" name="group_id" value="{{$project->group->id}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 7 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>集計コード</label>
                                            <input type="text" readonly name="get_code" id="get_code" value="{{ $project->get_code}}" class="form-control">
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>集計コード名</label>
                                            <input readonly type="text" id="get_code_name" name="get_code_name" value="{{ $project->get_code_name}}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 8 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>取引想定額</label>
                                            <input type="text" readonly name="transaction_money" class="form-control"
                                            @if($project->transaction_money != null) value="{{ number_format($project->transaction_money/1000) }}" @endif>
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>スポット取引想定<sup class="color-red">1</sup></label>
                                            <input type="text" readonly class="form-control"
                                            @if( $project->transaction_shot )  value="{{ number_format($project->transaction_shot/1000) }}"  @endif>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 9 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-6 form-group">
                                            <label>備考</label>
                                            <textarea class="form-control" readonly rows="3" name="note">{{ $project->note }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row 10 --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-sm-2 form-group">
                                            <label class="checkbox-title">単発</label>
                                            <div class="icheck-primary d-inline ">
                                                <input disabled type="checkbox" @if ($project->once_shot == true) checked @endif id="once_shot" name="once_shot" onchange="myfunc(this.value)">
                                                <label for="once_shot"></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 form-group">
                                            <label class="checkbox-title">取引終了</label>
                                            <div class="icheck-primary d-inline ">
                                                <input disabled type="checkbox" @if ($project->status == false) checked @endif id="status" name="status">
                                                <label for="status"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <p style="margin-top: 12px;padding-left: 15px;"><b class="color-red">1) </b><b class="color-header">1000円が省略された金額で表示されています。</b></p>
                                </div>
                            </div>
                            {{-- row btn --}}
                            <div class="row">
                                <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                    <div class="row search-form">
                                        <div class="col-lg-12 form-group">
                                            <div class="list-btn">
                                                {{-- btn 1 --}}
                                                @if (Auth::user()->can('process-index'))
                                                    <a><button onclick="process_index_url()" type="button" class="btn btn-warning">売上一覧</button></a>
                                                @endif
                                                {{-- btn 2 --}}
                                                @if (Auth::user()->can('contract-index'))
                                                    <a><button onclick="contract_index_url()" type="button" class="btn btn-warning">契約書一覧</button></a>
                                                @endif
                                                {{-- btn 3 --}}
                                                @if (Auth::user()->can('contract-up'))
                                                    <span class="btn btn-primary btn-file">
                                                        契約UP
                                                        <input type="file" id="input_file" name="file_data" accept="application/pdf">
                                                    </span>
                                                @endif
                                                {{-- btn 4 --}}
                                                <a><button id="csv2" type="button" class="btn btn-success">CSV出力</button></a>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 text-center">
                                            <span hidden id="upload_success" class="text-success">{{ trans('message.contract_upload_success') }}</span>
                                            <span hidden id="upload_fail" class="text-danger">{{ trans('message.contract_upload_fail') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- row update --}}
                            <div class="row p-b-20">
                                <div class="col-xs-3 display-middle">
                                    @php($page = session()->has('project.page') ? '?page='.session('project.page') : '')
                                    @if(!empty($contractEditId))
                                        <a href="{{ url('contract/edit?id='.request()->contract_edit_id) }}">
                                            <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                        </a>
                                    @elseif(!empty($contractViewId))
                                        <a href="{{ url('contract/view?id='.request()->contract_view_id) }}">
                                            <button type="button" style="float: left" class="btn btn-danger search-button">戻る</button>
                                        </a>
                                    @else
                                        <a href="{{ url('project/index'.$page) }}">
                                            <button type="button"  class="btn btn-danger search-button">戻る</button>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function() {
            $("#csv2").click(function(event) {
                    var project_id=$("#project_id").val();
                    document.location.href="/project/csv2?project_id="+project_id;
                }
            );
            $("#input_file").change(function() {
                    var pdf=$('#input_file')[0].files[0];
                    var form=new FormData();
                    form.append('pdf', pdf);
                    form.append('type', 2);
                    form.append('client_id', $("#hidden_id").val());
                    form.append('company_id', $("#company_id").val());
                    form.append('client_id', $("#client_id").val());
                    form.append('project_id', $("#project_id").val());
                    form.append('headquarter_id', $("#headquarter_id").val());
                    form.append('department_id', $("#department_id").val());
                    form.append('group_id', $("#group_id").val());
                    $.ajax( {
                            url: '/contract/upload',
                            data: form,
                            cache: false,
                            contentType: false,
                            processData: false,
                            type: 'POST',
                            success:function(response) {
                                if(response.status_code==200) {
                                    $("#upload_success").show();
                                    $("#upload_fail").hide();
                                }
                                if (response.status_code==500) {
                                    $("#upload_fail").show();
                                    $("#upload_success").hide();
                                }
                            },
                            error: function (exception) {
                                alert(exception.responseText);
                                if (response.status_code==500) {
                                    $("#upload_fail").show();
                                    $("#upload_success").hide();
                                }
                            }
                        }
                    );
                }
            );
            if(window.check==1) {
                $("#group_id").attr("id", "new_group_id");
                $("#new_group_id").prop("disabled", true);
                $("#department_id").attr("id", "new_department_id");
                $("#new_department_id").prop("disabled", true);
                $("#headquarter_id").attr("id", "new_headquarter_id");
                $("#new_headquarter_id").prop("disabled", true);
                $("input").prop("disabled", true);
                $("textarea").prop("disabled", true);
                $("#form_submit").hide();
            }
        }
    );

    function process_index_url() {
        var base='{!! route('Process_index') !!}';
        var project_id='{!! request()->id !!}';
        var url=base+'?&project_id='+project_id;
        window.location.href=url;
    }

    function contract_index_url() {
        var base='{!! route('contract.index') !!}';
        var project_id='{!! request()->id !!}';
        var url=base+'?&project_id='+project_id;
        window.location.href=url;
    }

    function myfunc(value) {
        var check1=document.getElementById("once_shot").checked;
        if (check1==false) {
            $('#transaction_shot').val(null);
        }
    }
</script>
<script src="{{ asset('select/icontains.js') }}"></script>
<script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
