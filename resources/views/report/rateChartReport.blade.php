<div class="col-md-12 rateChart table-back">
    
        <h3 class="text-center">Rate Card For @if($cardFor=="cow")
                                                    Cow
                                                @elseif($cardFor=="buff")
                                                    Buffalo
                                                @elseif($cardFor=="both")
                                                    Cow And Buffalo
                                                @endif
        </h3>
    
        <hr class="m-0">
    
        <table class="table-cardShort table-bordered table-striped table-mini">
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
                    <td>{{strtoupper($shortCard->rateCardType)}}</td>
                    <td>{{$shortCard->minFat}}</td>
                    <td>{{$shortCard->maxFat}}</td>
                    <td>
                        @if(strtolower($shortCard->rateCardType) == 'fat' ) - @else {{$shortCard->minSnf}} @endif
                    </td>
                    <td>
                        @if(strtolower($shortCard->rateCardType) == 'fat' ) - @else {{$shortCard->maxSnf}} @endif
                    </td>
                </tr>
            </tbody>
        </table>
    
        <table class="table-cardRange table-bordered table-striped table-mini">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Min FAT</th>
                    <th>Max FAT</th>
                    @if(strtolower($shortCard->rateCardType) == 'fat/snf' )
                        <th>Min SNF</th>
                        <th>Max SNF</th>
                    @endif
                    <th>Rate Increase FAT Increase</th>
                    {{-- <th>Rate Decrease FAT Decrease</th> --}}
                    <th>Rate Increase SNF Increase</th>
                    {{-- <th>Rate Decrease SNF Decrease</th> --}}
                    <th>Rate For Min FAT & SNF</th>
                    {{-- <th>Average Fat</th> --}}
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($rangeList as $range)
                <tr>
                    <th>{{$i++}}</th>
                    <th>{{number_format(round($range->mnFat,1), 1, ".", "")}}</th>
                    <th>{{number_format(round($range->mxFat,1), 1, ".", "")}}</th>
                    @if(strtolower($shortCard->rateCardType) == 'fat/snf' )
                        <th>{{$range->mnSnf}}</th>
                        <th>{{$range->mxSnf}}</th>
                    @endif
                    <th>{{number_format(round($range->rIncFat,2), 2, ".", "")}}</th>
                    {{-- <th>{{$range->rDecFat}}</th> --}}
                    <th>
                        @if(strtolower($shortCard->rateCardType) == 'fat' ) - @else {{$range->rIncSnf}} @endif
                    </th>
                    {{-- <th>{{$range->rDecSnf}}</th> --}}
                    <th>{{number_format(round($range->rAvgFatSnf,2), 2, ".", "")}}</th>
                    {{-- <th>{{$range->avgFat}}</th> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    
        <h4 class="text-center"></h4>
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <h3>Rate Card</h3>
            </div>
        </div>
    
        <table class="rateChartTable tright table-bordered table-striped">
            <thead>
                <tr>
                    <th class='dual-column'>
                        <span class='bl'>FAT</span>
                        <span class='tr_'>SNF</span>
                    </th>
    
                    @php $column = 0; 
    @endphp @for($i = (float)$shortCard->minSnf; number_format($i, 1)
                    <=(float)$shortCard->maxSnf; $i+=10)
                        <th>{{number_format($i, 0)}}</th>
                        @php $column++;
    @endphp @endfor
                </tr>
                <tbody class="table-body">
                    {{-- @for($f = (float)$shortCard->minFat; $f
                    <=(float)$shortCard->maxFat; $f+=0.1)
                        <th>{{number_format($f, 1)}}</th>
                        @for($s = (float)$rateCard->; ) @endfor --}}
                         @php $ccount=0; 
    @endphp @foreach($rateCard as $rate) @if($ccount==0)
                        <tr>
                            <th>{{number_format($rate->fatRange, 1)}}</th>
                            @endif @php $ccount++;
    @endphp
                            <td class="tr @if($rate->updated_at!=null) updated_cell @endif">
                                {{number_format($rate->amount, 2)}}
                            </td>
                            @if($ccount==$column)
                        </tr>
                        @php $ccount=0; 
    @endphp @endif @endforeach
                </tbody>
        </table>
        <div class="pt-10 col-md-4 col-md-offset-4">
        </div>
    </div>
    
    