	<ul class="pagination" style="float: right;">
		@php($cur   = $item->currentPage())
	    @php($total = $item->lastPage())
        <li class="page-item"><a class="page-link" href="{{ $item->url(1) }}"><<</a></li>
		@if($cur-2 > 0) <li class="page-item"><a class="page-link" href="{{ $item->url($cur-2) }}">{{ $cur-2 }}</a></li>@endif
		@if($cur-1 > 0) <li class="page-item"><a class="page-link" href="{{ $item->url($cur-1) }}">{{ $cur-1 }}</a></li>@endif
		<li class="page-item active"><span class="page-link">{{ $cur }}</span></li>
		@if($cur+1 <= $total) <li class="page-item"><a class="page-link" href="{{ $item->url($cur+1) }}">{{ $cur+1 }}</a></li>@endif
		@if($cur+2 <= $total) <li class="page-item"><a class="page-link" href="{{ $item->url($cur+2) }}">{{ $cur+2 }}</a></li>@endif
		<li class="page-item"><a class="page-link" href="{{ $item->url($total) }}">>></a></li>
	</ul>
