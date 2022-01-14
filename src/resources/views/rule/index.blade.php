@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('rule/index'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-2 col-lg-offset-1">
                                    @if( Auth::user()->can('create','App\Rule_MST'))
                                    <a href="{{ url('rule/create') }}">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm">新規登録</button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-3 col-lg-offset-8">
                                    @paginate(['item'=> $rules]) @endpaginate
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 col-lg-offset-1 table-parent fix-mobile-col">
                                    <table id="rule_table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="edit_button_with">編集</th>
                                                <th>画面機能ID</th>
                                                <th>画面機能ルール</th>
                                                <th>所属会社参照権限</th>
                                                <th>全会社参照権限</th>
                                                <th>所属会社名</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rules as $rule)
                                            <tr>
                                                <td class="hfsz">
                                                    @if( Auth::user()->getRuleAction(config('constant.RULE_EDIT')))
                                                    <a href="{{route('edit_rule', ['rule_id' => $rule->id])}}"><button
                                                            type="submit" style="float: left;"
                                                            class="btn btn-info btn-sm">編集</button></a>
                                                    @endif
                                                </td>
                                                <td class="hfsz">{{  $rule->id }}</td>
                                                <td class="hfsz">{{  $rule->rule }}</td>

                                                <td class="hfsz">@if($rule->admin_flag == 1) 所属会社参照権限 @endif</td>
                                                <td class="hfsz">@if($rule->superuser_user == 1) 全会社参照権限 @endif</td>
                                                <td class="hfsz">{{  $rule->company->abbreviate_name }}</td>
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
