@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('company/index'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-2 col-lg-offset-1 ">
                                    @if( Auth::user()->can('create','App\Company_MST'))
                                    <a href="{{ url('company/create') }}">
                                        <button type="submit" class="btn btn-primary btn-sm">新規登録</button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-3 col-lg-offset-8">
                                    @paginate(['item'=> $companies]) @endpaginate
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 col-lg-offset-1 table-parent fix-mobile-col">
                                    <table id="company_table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">編集</th>
                                                <th>会社名</th>
                                                <th>省略名</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($companies as $company)
                                            <tr>
                                                <td>
                                                    @if( Auth::user()->can('update','App\Company_MST'))
                                                    <a
                                                        href="{{route('editcompany', ['id' => $company->own_company,'page'=>request()->page])}}">
                                                        <button style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                    </a>
                                                    @endif
                                                </td>
                                                <td>{{  $company->company_name }}</td>
                                                <td>{{  $company->abbreviate_name }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection
