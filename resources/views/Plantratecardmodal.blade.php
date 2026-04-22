{{--
    plantRateCardModal.blade.php
    Loaded via AJAX into .cmodel-body — exact modelRateCardList design
    Variables: $rateCard  (plant_fat_snf_ratecard rows)
               $shortCard (plant_ratecardshort row)
               $rangeList (plant_rangelist rows)
               $cardFor   ('cow' | 'buff' | 'both' | '')
--}}

<style>
    .dual-column {
        position: relative;
        min-width: 60px;
        background: #eee;
    }
    .dual-column .bl  { position: absolute; bottom: 3px; left:  4px; font-size: 10px; }
    .dual-column .tr_ { position: absolute; top:    3px; right: 4px; font-size: 10px; }
    .updated_cell { background: #fffde7 !important; }
    .updated_cell input { color: #e65100 !important; font-weight: bold; }
    #prcModalWrap tr th,
    #prcModalWrap tr td { text-align: right; }
    #prcModalWrap thead th { background: #eee; }
</style>

<div id="prcModalWrap">

    {{-- ── Section 1: Rate Card Details (exact modelRateCardList top table) ──── --}}
    <h4 style="color:#0337ac;text-align:center;margin-bottom:12px;">Rate Card Details</h4>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Rate Card Type</th>
                <th>Min Fat</th>
                <th>Max Fat</th>
                <th>Min SNF</th>
                <th>Max SNF</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ strtoupper($shortCard->rateCardType) }}</td>
                <td>{{ $shortCard->minFat }}</td>
                <td>{{ $shortCard->maxFat }}</td>
                <td>@if(strtolower($shortCard->rateCardType) == 'fat') - @else {{ $shortCard->minSnf }} @endif</td>
                <td>@if(strtolower($shortCard->rateCardType) == 'fat') - @else {{ $shortCard->maxSnf }} @endif</td>
            </tr>
        </tbody>
    </table>

    {{-- ── Section 2: Range List table (exact modelRateCardList middle table) ── --}}
    @if(count($rangeList) > 0)
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>S.N</th>
                <th>Min FAT</th>
                <th>Max FAT</th>
                <th>Rate Increase FAT Increase</th>
                @if(strtolower($shortCard->rateCardType) === 'fat/snf')
                    <th>Rate Increase SNF Increase</th>
                @endif
                <th>Rate For Min FAT &amp; SNF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rangeList as $rl)
            <tr>
                <td>{{ $rl->id }}</td>
                <td>{{ $rl->mnFat }}</td>
                <td>{{ $rl->mxFat }}</td>
                <td>{{ $rl->rIncFat }}</td>
                @if(strtolower($shortCard->rateCardType) === 'fat/snf')
                    <td>{{ $rl->rIncSnf ?? '-' }}</td>
                @endif
                <td>{{ $rl->rAvgFatSnf }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ── Section 3: Apply buttons + Edit (exact modelRateCardList action bar) ── --}}
    <div class="clearfix" style="margin-bottom:10px;">
        <div class="fl">
            <h4 style="color:#0337ac;margin:6px 0;">Rate Card</h4>
        </div>
        <div class="fr" style="display:flex;gap:6px;flex-wrap:wrap;">
            <a href="{{ url('plantRateCardApply') }}?shortCardId={{ $shortCard->id }}&rateCardType={{ $shortCard->rateCardType }}&type=cow"
               class="btn btn-sm btn-success"
               onclick="return confirm('Apply Card #{{ $shortCard->id }} for COW?')">
                <i class="fa fa-check"></i> Apply RateCard For Cow
            </a>
            <a href="{{ url('plantRateCardApply') }}?shortCardId={{ $shortCard->id }}&rateCardType={{ $shortCard->rateCardType }}&type=buffalo"
               class="btn btn-sm btn-success"
               onclick="return confirm('Apply Card #{{ $shortCard->id }} for BUFFALO?')">
                <i class="fa fa-check"></i> Apply RateCard For Buffalo
            </a>
            <a href="{{ url('plantRateCardApply') }}?shortCardId={{ $shortCard->id }}&rateCardType={{ $shortCard->rateCardType }}&type=both"
               class="btn btn-sm btn-success"
               onclick="return confirm('Apply Card #{{ $shortCard->id }} for BOTH?')">
                <i class="fa fa-check"></i> Apply RateCard For Both
            </a>
            <button class="btn btn-sm btn-primary" onclick="togglePrcEdit({{ $shortCard->id }})">
                <i class="fa fa-edit"></i> Edit
            </button>
            {{-- Save button (hidden until Edit clicked) --}}
            <button class="btn btn-sm btn-warning" id="prcSaveBtn_{{ $shortCard->id }}"
                    style="display:none;" onclick="savePlantRcEdits({{ $shortCard->id }})">
                <i class="fa fa-save"></i> Save
            </button>
            {{-- PDF --}}
            <form action="{{ url('plantRateCardPdf') }}" method="post" style="display:inline;"
                  id="prcPdfForm_{{ $shortCard->id }}">
                {{ csrf_field() }}
                <input type="hidden" name="rateCardTable" id="prcPdfTable_{{ $shortCard->id }}">
                <button type="button" class="btn btn-sm btn-default"
                        onclick="downloadPrcPdf({{ $shortCard->id }})">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
            </form>
        </div>
    </div>

    {{-- ── Section 4: Rate chart table (exact modelRateCardList bottom table) ── --}}
    @php
        $fatList  = $rateCard->pluck('fatRange')->unique()->sort()->values();
        $snfList  = $rateCard->pluck('snfRange')->unique()->sort()->values();
        $isFatSnf = (strtolower($shortCard->rateCardType) === 'fat/snf');
    @endphp

    <div style="overflow-x:auto;" id="prcTableWrap_{{ $shortCard->id }}">
        <table class="table table-bordered table-striped" style="font-size:12px;white-space:nowrap;">
            <thead>
                <tr>
                    @if($isFatSnf)
                        <th class="dual-column" style="min-width:60px;padding:8px 6px;">
                            <span class="bl">FAT</span>
                            <span class="tr_">SNF</span>
                        </th>
                        @foreach($snfList as $s)
                            <th>{{ $s }}</th>
                        @endforeach
                    @else
                        <th>FAT</th>
                        <th>SNF</th>
                        <th>Rate</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($fatList as $f)
                <tr>
                    <th style="background:#f5f5f5;text-align:right;">{{ number_format($f, 1) }}</th>
                    @if($isFatSnf)
                        @foreach($snfList as $s)
                            @php $cell = $rateCard->where('fatRange', $f)->where('snfRange', $s)->first(); @endphp
                            <td @if($cell && $cell->updated_at) class="updated_cell" @endif>
                                @if($cell)
                                    <input class="prc-cell-input" type="number" step="0.01"
                                           style="width:58px;border:none;background:transparent;text-align:right;font-size:12px;"
                                           data-rcid="{{ $cell->id }}"
                                           data-f="{{ $f }}" data-s="{{ $s }}"
                                           data-fixrate="{{ $cell->amount }}"
                                           value="{{ $cell->amount }}"
                                           readonly
                                           onchange="prcCellChange(this)">
                                @else
                                    <span style="color:#ccc;">—</span>
                                @endif
                            </td>
                        @endforeach
                    @else
                        @php $cell = $rateCard->where('fatRange', $f)->first(); @endphp
                        <td>{{ $cell ? $cell->snfRange ?? '-' : '-' }}</td>
                        <td @if($cell && $cell->updated_at) class="updated_cell" @endif>
                            @if($cell)
                                <input class="prc-cell-input" type="number" step="0.01"
                                       style="width:68px;border:none;background:transparent;text-align:right;font-size:12px;"
                                       data-rcid="{{ $cell->id }}"
                                       data-f="{{ $f }}" data-s=""
                                       data-fixrate="{{ $cell->amount }}"
                                       value="{{ $cell->amount }}"
                                       readonly
                                       onchange="prcCellChange(this)">
                            @else
                                <span style="color:#ccc;">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>{{-- /prcModalWrap --}}

<script>
var _prcEditMode_{{ $shortCard->id }} = false;

/* Toggle Edit mode — readonly on/off (same as modelRateCardList) */
function togglePrcEdit(shortId) {
    _prcEditMode_{{ $shortCard->id }} = !_prcEditMode_{{ $shortCard->id }};
    var inputs = $('#prcTableWrap_' + shortId + ' .prc-cell-input');
    if (_prcEditMode_{{ $shortCard->id }}) {
        inputs.removeAttr('readonly');
        inputs.css('background', '#fffde7');
        $('#prcSaveBtn_' + shortId).show();
    } else {
        inputs.attr('readonly', true);
        inputs.css('background', 'transparent');
        $('#prcSaveBtn_' + shortId).hide();
    }
}

/* Cell change tracker (same pattern as modelRateCardList) */
function prcCellChange(el) {
    var fixRate = parseFloat($(el).data('fixrate'));
    var newRate = parseFloat($(el).val());
    if (newRate !== fixRate) {
        $(el).closest('td').addClass('updated_cell');
    } else {
        $(el).closest('td').removeClass('updated_cell');
    }
    pageUnloadFlag = true;
}

/* Save edits (same AJAX as updateRateCardNew) */
function savePlantRcEdits(shortId) {
    var data = [];
    $('#prcTableWrap_' + shortId + ' .prc-cell-input').each(function() {
        data.push({
            rcid : $(this).data('rcid'),
            f    : $(this).data('f'),
            s    : $(this).data('s'),
            rate : $(this).val()
        });
    });

    loader('show');
    $.ajax({
        type: 'POST',
        url : 'plantRateCardUpdate',
        data: {
            _token          : $('meta[name="csrf-token"]').attr('content'),
            rateCardShortId : shortId,
            data            : data
        },
        success: function(res) {
            loader('hide');
            pageUnloadFlag = false;
            if (res.error) {
                $.alert('Error: ' + res.msg);
            } else {
                $.alert(res.msg + ' (' + res.count + ' cells updated)');
                /* turn off edit mode */
                togglePrcEdit(shortId);
            }
        },
        error: function() {
            loader('hide');
            $.alert('Server error. Please try again.');
        }
    });
}

/* PDF (same as modelRateCardList PDF) */
function downloadPrcPdf(shortId) {
    $('#prcPdfTable_' + shortId).val($('#prcTableWrap_' + shortId).html());
    $('#prcPdfForm_' + shortId).submit();
}
</script>