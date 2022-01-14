@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('position/index'))
<div class="row">
    <div class="col-md-12">
        <ul class="timeline">
            <li>
                <div class="timeline-item">
                    <div class="timeline-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-2 col-lg-offset-1">
                                    @if( Auth::user()->can('create','App\Position_MST'))
                                    <a href="{{ url('position/create') }}">
                                        <button type="submit"
                                            class="btn btn-primary btn-sm">新規登録</button>
                                    </a>
                                    @endif
                                </div>
                                <div class="col-lg-3 col-lg-offset-8">
                                    @paginate(['item'=> $positions]) @endpaginate
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-10 col-lg-offset-1 table-parent fix-mobile-col">
                                    <table id="position_table" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="edit_button_with">編集</th>
                                                <th>役職名</th>
                                                <th>詳細情報参照参照</th>
                                                <th>所属会社名</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($positions as $position)
                                            <tr>
                                                <td>
                                                    @if( Auth::user()->can('update','App\Position_MST'))
                                                        <a href="{{route('edit_position', ['id' => $position->id,'page' => request()->page])}}">
                                                            <button type="submit" style="float: left;" class="btn btn-info btn-sm">編集</button>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{  $position->position_name }}</td>
                                                <td>{{  $position->getLookAttribute() }}</td>
                                                <td>{{  $position->company->abbreviate_name }}</td>
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
