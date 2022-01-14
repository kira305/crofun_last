<table class="table table-bordered table-hover m-b-0">
    <thead>
        <tr>
            <th class="active">顧客名</th>
            <th class="active">対象月</th>
            <th class="active ">与信限度額</th>
            <th class="active ">取引想定額</th>
            <th class="active ">売掛金残</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($over_receivable as $key => $value)
        <tr>
            <td class="">
                @if($value->client_name_ab == null)
                    {{  $value->client_name }}
                @else
                    {{  $value->client_name_ab }}
                @endif
            </td>
            <td class="">
                @if($receivable_date[$key]->target_data != null)
                    {{  date('Y年m月',strtotime($receivable_date[$key]->target_data)) }}
                @endif
            </td>
            <td class="">{{ number_format($value->credit_expect/1000) }}</td>
            <td class="" @if($transaction_date[$key]> $value->credit_expect)  style = "background-color: #FFB6C1;" @endif
                >{{ number_format($transaction_date[$key]/1000) }}
            </td>
            <td class=""
                @if($receivable_date[$key]->receivable != "")
                    @if($receivable_date[$key]->receivable > $value->credit_expect)
                        style = "background-color: #FFB6C1;"
                    @endif
                @endif>
                @if($receivable_date[$key]->receivable != "")
                    {{  number_format($receivable_date[$key]->receivable/1000)}}
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
