@foreach($milkReq as $m)
<div class="s-req s-req-left">
    <div class="s-req-usr">{{$m->colMan}}</div>
    <div class="s-req-date">{{date("d-m-Y", strtotime($m->date))}} &nbsp; &nbsp; Shift: {{$m->shift}}</div>
    <div class="s-req-content">{{$m->comment}}</div>
    <div class="s-req-time">request time: {{date("d-m-Y g:i a", strtotime($m->created_at))}}</div>

    @if($m->isSeen == "true")
        <div class="s-req-seen">seen at {{date("d-m-Y g:i a", strtotime($m->seen_at))}}</div>
    @endif

</div>


@if($m->resText != (null||""))
    <div class="s-req s-req-right">
        <div class="s-req-usr">{{$m->memberCode}}</div>
        <div class="s-req-content">{{$m->resText}}</div>
        <div class="fr s-req-time">at {{date("d-m-Y g:i a", strtotime($m->response_at))}}</div>
        <div class="clearfix"></div>
    </div>
@endif

@endforeach
