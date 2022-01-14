@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('contracttype/index'))
<style type="text/css">
</style>
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li style="display: none">
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div>
                            <div class="box-body">
                                <form action="{{ url('contracttype/index') }}" id="form" method="post">
                                    {{-- row 1 --}}
                                    <div class="col-lg-8 col-lg-offset-3">
                                        <div class="row search-row">
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-2">
                                                    <span class="">所属会社</span>
                                                </div>
                                                <div class="col-xs-10 search-item">
                                                    <select class="form-control clear-select" id="company_id" name="company_id">
                                                        @foreach ($companies as $company)
                                                            <option
                                                                @if (session()->has('contract_type.company_id'))
                                                                    @if (session('contract_type.company_id') == $company->id) selected @endif
                                                                @else
                                                                    @if (Auth::user()->company_id == $company->id) selected @endif
                                                                @endif
                                                                value="{{ $company->id }}">{{ $company->abbreviate_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-6 search-form">
                                                <div class="search-title col-xs-2">
                                                    <span class="">表示可能</span>
                                                </div>
                                                <div class="col-xs-10 search-item form-group">
                                                    <input type="checkbox" id="hidden" name="hidden" @if (request()->session()->exists('contract_type.hidden')) checked @endif autocomplete="off" />
                                                    <div class="btn-group">
                                                        <label for="hidden" class="btn btn-default border-none">
                                                            <span class="glyphicon glyphicon-ok"></span>
                                                            <span> </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end --}}
                                    @csrf
                                    <div class="col-lg-12 ">
                                        <div class="col-xs-3 col-xs-offset-3">
                                            <button type="submit" id="search"
                                                class="search-button btn btn-primary btn-sm">検索</button>
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
                                <div class="col-lg-2 col-lg-offset-2">
                                    @if( Auth::user()->getRuleAction(config('constant.CONTRACT_TYPE_CREATE')))
                                        <a href="{{ url('contracttype/create') }}"><button type="submit" class="btn btn-primary btn-sm">新規登録</button></a>
                                    @endif
                                </div>
                                <div class="col-lg-3 col-lg-offset-7">
                                    @paginate(['item'=> $contractTypes]) @endpaginate
                                </div>
                            </div>
                            <div class="col-lg-8 col-lg-offset-2 table-parent fix-mobile-col">
                                <table id="headquarter_table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%" class=" text-center">編集</th>
                                            <th width="" class=" text-center">所属会社</th>
                                            <th width="" class=" text-left">名称</th>
                                            <th width="" class=" text-left">表示コード</th>
                                            <th width="" class=" text-left">非表示</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($contractTypes as $contractType)
                                        <tr>
                                            <td>
                                                @if( Auth::user()->getRuleAction(config('constant.CONTRACT_TYPE_EDIT')))
                                                    <a href="{{route('contract_type.edit', ['id' => $contractType->id])}}">
                                                        <button type="submit" style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{  $contractType->company->abbreviate_name }}</td>
                                            <td>{{  $contractType->type_name }}</td>
                                            <td>{{  $contractType->display_code }}</td>
                                            <td>{{  $contractType->hidden == 1 ? '非表示' : ''}}</td>
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
    $(document).ready(function() {
            $("#clear").click(function() {
                    $('#company_id').prop('selectedIndex', 0);
                    $('#headquarter_name').val('');
                    $("#status").prop("checked", false);
                    $("#form").submit();
                }
            );
        }
    );
</script>
@endsection
