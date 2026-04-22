@extends('theme.default')
@section('content')

<style>
    .rc-card { border: 1px solid #dde; border-radius: 6px; padding: 15px 18px;
               margin-bottom: 14px; background: #fff; }
    .rc-card-header { display: flex; align-items: center; justify-content: space-between; }
    .rc-badge { display: inline-block; padding: 3px 10px; border-radius: 12px;
                font-size: 11px; font-weight: bold; margin-right: 5px; }
    .badge-fat    { background: #d1ecf1; color: #0c5460; }
    .badge-fatsnf { background: #d4edda; color: #155724; }
    .badge-cow    { background: #fff3cd; color: #856404; }
    .badge-buff   { background: #f8d7da; color: #721c24; }
    .badge-both   { background: #e2e3e5; color: #383d41; }
    .applied-tick { color: #28a745; font-size: 13px; }
    .not-applied  { color: #aaa;     font-size: 12px; }
    .apply-row    { margin-top: 10px; padding-top: 8px; border-top: 1px solid #f0f0f0; }
</style>

<div class="pageblur">
<div class="clearfix">
<div class="fcard margin-fcard-1 clearfix">

    {{-- Flash messages --}}
    @if(Session::has('msg'))
        <div class="alert {{ Session::get('alert-class','alert-info') }} alert-dismissible">
            {!! Session::get('msg') !!}
        </div>
    @endif

    <div class="heading clearfix">
        <div class="fl"><h3>Plant Rate Cards</h3><hr style="margin-top:0;"></div>
        <div class="fr pt-5">
            <a href="{{ url('plantRateCardNew') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> New Plant Rate Card
            </a>
        </div>
    </div>

    {{-- Applied card status bar --}}
    <div class="col-sm-12 pb-10">
        <div style="background:#f6f7f9;border:1px solid #dedede;border-radius:5px;padding:10px 15px;">
            <b>Currently Applied:</b> &nbsp;&nbsp;
            <span>
                🐄 Cow:
                @if($dairyInfo->plantRateCardIdForCow)
                    <span class="applied-tick"><i class="fa fa-check-circle"></i>
                        Card #{{ $dairyInfo->plantRateCardIdForCow }}
                        ({{ strtoupper($dairyInfo->plantRateCardTypeForCow ?? '') }})
                    </span>
                @else
                    <span class="not-applied">Not applied</span>
                @endif
            </span>
            &nbsp;&nbsp;&nbsp;
            <span>
                🐃 Buffalo:
                @if($dairyInfo->plantRateCardIdForBuffalo)
                    <span class="applied-tick"><i class="fa fa-check-circle"></i>
                        Card #{{ $dairyInfo->plantRateCardIdForBuffalo }}
                        ({{ strtoupper($dairyInfo->plantRateCardTypeForBuffalo ?? '') }})
                    </span>
                @else
                    <span class="not-applied">Not applied</span>
                @endif
            </span>
        </div>
    </div>

    {{-- Rate card list --}}
    <div class="col-sm-12">
        @forelse($rateCardShort as $rc)
        <div class="rc-card">
            <div class="rc-card-header">
                <div>
                    <b>Card #{{ $rc->id }}</b>
                    &nbsp;
                    <span class="rc-badge {{ $rc->rateCardType === 'fat' ? 'badge-fat' : 'badge-fatsnf' }}">
                        {{ strtoupper($rc->rateCardType) }}
                    </span>
                    @if($rc->rateCardFor)
                        <span class="rc-badge
                            @if($rc->rateCardFor === 'cow') badge-cow
                            @elseif($rc->rateCardFor === 'buffalo') badge-buff
                            @else badge-both @endif">
                            {{ ucfirst($rc->rateCardFor) }}
                        </span>
                    @endif

                    {{-- Applied ticks --}}
                    @if($dairyInfo->plantRateCardIdForCow == $rc->id)
                        <span class="applied-tick"><i class="fa fa-check-circle"></i> Applied for Cow</span>
                    @endif
                    @if($dairyInfo->plantRateCardIdForBuffalo == $rc->id)
                        <span class="applied-tick"><i class="fa fa-check-circle"></i> Applied for Buffalo</span>
                    @endif
                </div>
                <div>
                    <a href="javascript:void(0)" class="btn btn-xs btn-info"
                       onclick="viewRateCard({{ $rc->id }})">
                        <i class="fa fa-eye"></i> View
                    </a>
                    &nbsp;
                    <a href="javascript:void(0)" class="btn btn-xs btn-danger"
                       onclick="deleteRateCard({{ $rc->id }})">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </div>
            </div>

            <div style="margin-top:6px;font-size:12px;color:#555;">
                Fat Range: <b>{{ $rc->minFat }} – {{ $rc->maxFat }}</b>
                @if($rc->rateCardType === 'fat/snf')
                    &nbsp;|&nbsp; SNF Range: <b>{{ $rc->minSnf }} – {{ $rc->maxSnf }}</b>
                @endif
                @if($rc->description)
                    &nbsp;|&nbsp; {{ $rc->description }}
                @endif
                &nbsp;|&nbsp; Created: {{ date('d-m-Y', strtotime($rc->created_at)) }}
            </div>

            {{-- Apply buttons --}}
            <div class="apply-row">
                <small class="text-muted">Apply as:</small> &nbsp;
                <a href="{{ url('plantRateCardApply') }}?shortCardId={{ $rc->id }}&rateCardType={{ $rc->rateCardType }}&type=cow"
                   class="btn btn-xs btn-warning"
                   onclick="return confirm('Apply Card #{{ $rc->id }} for COW plant sales?')">
                    🐄 Apply for Cow
                </a>
                &nbsp;
                <a href="{{ url('plantRateCardApply') }}?shortCardId={{ $rc->id }}&rateCardType={{ $rc->rateCardType }}&type=buffalo"
                   class="btn btn-xs btn-warning"
                   onclick="return confirm('Apply Card #{{ $rc->id }} for BUFFALO plant sales?')">
                    🐃 Apply for Buffalo
                </a>
                &nbsp;
                <a href="{{ url('plantRateCardApply') }}?shortCardId={{ $rc->id }}&rateCardType={{ $rc->rateCardType }}&type=both"
                   class="btn btn-xs btn-default"
                   onclick="return confirm('Apply Card #{{ $rc->id }} for BOTH cow and buffalo plant sales?')">
                    Apply for Both
                </a>
            </div>
        </div>
        @empty
        <div class="text-center text-muted pt-20 pb-20">
            <i class="fa fa-table fa-2x"></i><br><br>
            No plant rate cards yet.
            <a href="{{ url('plantRateCardNew') }}">Create one now.</a>
        </div>
        @endforelse
    </div>

</div>
</div>
</div>

{{-- View modal --}}
<div class="wmodel clearfix" id="rcViewModal" style="width:85%;display:none;">
    <div class="close" onclick="$('#rcViewModal').fadeOut();">X</div>
    <div class="wmodel-body" id="rcViewBody" style="padding:15px;overflow-x:auto;">
        <div class="text-center text-muted pt-20">Loading...</div>
    </div>
</div>

<script>
function viewRateCard(shortId) {
    $('#rcViewBody').html('<div class="text-center pt-20"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#rcViewModal').fadeIn();

    $.ajax({
        type: 'POST',
        url : '{{ url("plantRateCardGetList") }}',
        data: { _token: '{{ csrf_token() }}', shortid: shortId },
        success: function(res) { $('#rcViewBody').html(res); },
        error:   function()    { $('#rcViewBody').html('<p class="text-danger">Error loading rate card.</p>'); }
    });
}

function deleteRateCard(shortId) {
    $.confirm({
        title: 'Delete Plant Rate Card', type: 'red',
        content: 'This will permanently delete the rate card and all its cell values.',
        buttons: {
            confirm: { text: 'Delete', btnClass: 'btn-red', action: function() {
                $.ajax({
                    type: 'POST',
                    url : '{{ url("plantRateCardDelete") }}',
                    data: { _token: '{{ csrf_token() }}', shortid: shortId },
                    success: function(res) {
                        if (!res.error) { location.reload(); }
                        else { $.alert('Error: ' + res.msg); }
                    }
                });
            }},
            cancel: { text: 'Cancel' }
        }
    });
}
</script>
@endsection