@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('project/create'))
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
                            <div class="box-body">
                                <p class="text-danger text-center">{{ $errors->has('message') ? $errors->first('message') : ''}}</p>
                                <form id="create_project" method="post"  action="{{ url('project/create?pre=' . request()->pre) }}">
                                    @csrf
                                    {{-- row 1 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label>会社コード</label>
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        <option value="{{ $company_id }}">
                                                            {{ Crofun::getCompanyById($company_id)->abbreviate_name }}
                                                        </option>
                                                    </select>
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
                                                        <div class="form-control">{{ Crofun::getClientById($customer_id)->client_code_main }}</div>
                                                        <span class="text-danger">
                                                            {{ $errors->has('client_code') ? $errors->first('client_code') : '' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>顧客名</label>
                                                    <div class="form-control">
                                                        @if (Crofun::getClientById($customer_id)->client_name_ab)
                                                            {{ Crofun::getClientById($customer_id)->client_name_ab }}
                                                        @else
                                                            {{ Crofun::getClientById($customer_id)->client_name }}
                                                        @endif
                                                    </div>
                                                    <span class="text-danger">
                                                        {{ $errors->has('client_name') ? $errors->first('client_name') : '' }}
                                                    </span>
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
                                                    <input type="text" disabled name="credit_expect" id="credit_expect" class="form-control"
                                                    @if (isset($credit_expect)) value= "{{ number_format($credit_expect / 1000) }}"@endif >
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>取引想定合計額(顧客単位)<sup class="color-red">1</sup></label>
                                                    <input disabled type="text" class="form-control" id="transaction" name="transaction"
                                                    @if (isset($transaction)) value="{{ number_format($transaction / 1000) }}"  @endif >
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
                                                    <input type="text" readonly name="project_code" id="project_code" value="{{ old('project_code') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('project_code') ? $errors->first('project_code') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>プロジェクト名</label>
                                                    <input type="text" id="project_name" name="project_name" value="{{ old('project_name') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('project_name') ? $errors->first('project_name') : '' }}
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>事業本部</label>
                                                    <select class="form-control input-sm" id="headquarter_id"  name="headquarter_id">
                                                        <option> </option>
                                                        @foreach ($headquarters as $headquarter)
                                                            <option class="headquarter_id" id="headquarter_id"
                                                                {{ old('headquarter_id') == $headquarter->id ? 'selected' : '' }}
                                                                data-value="{{ $headquarter->company_id }}"
                                                                value="{{ $headquarter->id }}">{{ $headquarter->headquarters }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('headquarter_id') ? $errors->first('headquarter_id') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>部署</label>
                                                    <select class="form-control input-sm" id="department_id"
                                                    name="department_id">
                                                    <option> </option>
                                                        @foreach ($departments as $department)
                                                            <option class="department_id"
                                                                {{ old('department_id') == $department->id ? 'selected' : '' }}
                                                                data-value="{{ $department->headquarter()->id }}"
                                                                value="{{ $department->id }}">{{ $department->department_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('department_id') ? $errors->first('department_id') : '' }}
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
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>担当Grp</label>
                                                    <select class="form-control input-sm" id="group_id" name="group_id">
                                                        <option> </option>
                                                        @foreach ($groups as $group)
                                                            <option class="group_id"
                                                                {{ old('group_id') == $group->id ? 'selected' : '' }}
                                                                data-value="{{ $group->department()->id }}"
                                                                value="{{ $group->id }}">{{ $group->group_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">
                                                        {{ $errors->has('group_id') ? $errors->first('group_id') : '' }}
                                                    </span>
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
                                                    <input type="text" name="get_code" id="get_code" value="{{ old('get_code') }}" class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('get_code') ? $errors->first('get_code') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>集計コード名</label>
                                                    <input type="text" id="get_code_name" name="get_code_name" value="{{ old('get_code_name') }}"  class="form-control">
                                                    <span class="text-danger">
                                                        {{ $errors->has('get_code_name') ? $errors->first('get_code_name') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- row 8 --}}
                                    <div class="row">
                                        <div class="col-lg-10 col-md-12 col-lg-offset-1">
                                            <div class="row search-form">
                                                <div class="col-lg-6 form-group">
                                                    <label><span class="important-text">&nbsp;必須&nbsp;</span>取引想定額</label>
                                                    <input type="text" id="transaction_money" name="transaction_money" class="form-control" value="{{ old('transaction_money') }}">
                                                    <input type="hidden" name="customer_id" value="{{ $customer_id }}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('transaction_money') ? $errors->first('transaction_money') : '' }}
                                                    </span>
                                                </div>
                                                <div class="col-lg-6 form-group">
                                                    <label>スポット取引想定<sup class="color-red">1</sup></label>
                                                    <input type="text" id="transaction_shot"  name="transaction_shot" class="form-control" value="{{ old('transaction_shot') }}">
                                                    <span class="text-danger">
                                                        {{ $errors->has('transaction_shot') ? $errors->first('transaction_shot') : '' }}
                                                    </span>
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
                                                    <textarea class="form-control" rows="3" name="note"> {{ old('note') }}</textarea>
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
                                                        <input type="checkbox" @if(old('once_shot') == 'on') checked @endif id="once_shot" name="once_shot" >
                                                        <label for="once_shot"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <p style="margin-top: 12px;padding-left: 15px;"><b class="color-red">1) </b><b class="color-header">1000円が省略された金額で表示されています。</b></p>
                                        </div>
                                    </div>
                                    {{-- row update --}}
                                    <div class="row p-b-20">
                                        <div class="col-lg-12 ">
                                            <div class="col-xs-3 col-xs-offset-3 col-sm-offset-3">
                                                @if (!isset($success))
                                                <button type="button" id="form_submit"class="btn btn-primary search-button">登録</button>
                                                @endif
                                            </div>
                                            <div class="col-xs-3" >
                                                @if (request()->pre == 1)
                                                    <a href="{{ route('customer_edit', ['id' => $customer_id]) }}" class="btn btn-danger search-button" style="float: left">戻る</a>
                                                @else
                                                    <a href="{{ route('customer_view', ['id' => $customer_id]) }}" class="btn btn-danger search-button" style="float: left">戻る</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#form_submit").click(function() {
                var company_id = $('#company_id').val();
                $.ajax({
                    type: 'POST',
                    url: '/project/getcode',
                    data: {
                        "company_id": company_id
                    },
                    success: function(data) {
                        if (data.num == 500) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{trans('message.save_fail')}}',
                                text: 'プロジェクトコードがなくなりました。システム管理者に連絡してください。',
                            })
                            return;
                        }
                        $('#project_code').val(data.num);
                        $('#create_project').submit();
                    },
                    error: function(exception) {
                        alert(exception.responseText);
                    }
                });
            });
        });

    </script>
    <script src="{{ asset('select/icontains.js') }}"></script>
    <script src="{{ asset('select/comboTreePlugin.js') }}"></script>
@endsection
