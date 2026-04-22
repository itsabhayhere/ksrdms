
<form action="{{ url('export/member/report') }}" method="get" id="form1">


<button type="submit" class="btn btn-success" value="Export">Export Combine Excel</button>
</form>

<form action="{{ url('export/member/report/pdf') }}" method="get" id="form2">


    <input type="hidden" name="partyCode" id="pdf_partyCode">
    <input type="hidden" name="partyName" id="pdf_partyName">
    <input type="hidden" name="memberPassbookStartDate" id="pdf_startDate">
    <input type="hidden" name="memberPassbookEndDate" id="pdf_endDate">
        <input type="hidden" name="status" id="pdf_status">



    <button type="submit" class="btn btn-success" style="margin-right: 3px;" onclick="copyToPdfForm()">PDF</button>
</form>



<table class="table passbookReport-table tright table-bordered table-stripped display" style="width:100%" width="100%" cellpadding="0" cellspacing="0" border="1">
    <thead> 
        <tr>
            <th rowspan="2">Date</th>
            <th colspan="5">Morning Shift</th>
            <th colspan="5">Evening Shift</th>
            <th colspan="2">Total</th>
        </tr>
        <tr>
            <th>Qty</th>
            <th>Fat</th>
            <th>Snf</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Qty</th>
            <th>Fat</th>
            <th>Snf</th>
            <th>Rate</th>
            <th>Amount</th>

            <th>Qty</th>
            {{-- <th>AvgFat</th>
            <th>AvgSnf</th> --}}
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
            @php 
                $totalAmountM = 0;
                $totalQtyM = 0;
                $countM = 0;
                $fatPlusM = 0;
                $snfPlusM = 0;

                $totalAmountE = 0;
                $totalQtyE = 0;
                $fatPlusE = 0;
                $snfPlusE = 0;
                $countE = 0;
                $fatkg=0;
                $snfkg=0;
            @endphp

        @foreach($shift as $date)
            @php
           
                $x = 0;
                $sft = "";
               
                 
                if(isset($date['morning']) && isset($date['evening'])){
                    if( count($date['morning']) > count($date['evening'])){
                        $sft = 'morning';
                    }else{
                        $sft = 'evening';
                    }
                }else{
                    if(isset($date['morning'])) $sft = 'morning';
                    if(isset($date['evening'])) $sft = 'evening';
                    if($sft == "") continue;
                }

                $mxLoop = count($date[$sft]);
                $qtyloop = 0; $FatLoop = 0; $SnfLoop = 0; $amtloop = 0;
            @endphp
            @foreach($date[$sft] as $d)
                <tr>
                    <td style="width: 80px;">{{date("d-m-Y", strtotime($d->date))}}</td>
                    @if(isset($date['morning'][$x]))
                        @php                    
                            $qtyloop += (float)$date['morning'][$x]->milkQuality;
                            $FatLoop += (float)$date['morning'][$x]->milkQuality * (float)$date['morning'][$x]->fat;
                            $SnfLoop += (float)$date['morning'][$x]->milkQuality * (float)$date['morning'][$x]->snf;
                            $amtloop += (float)$date['morning'][$x]->amount;
                            $fatkg += (float)($date['morning'][$x]->fatkg ?? 0);
                             $snfkg += (float)($date['morning'][$x]->snfkg ?? 0);

                            $countM++;
                            $totalAmountM += (float)$date['morning'][$x]->amount;
                            $totalQtyM += (float)$date['morning'][$x]->milkQuality;
                            $fatPlusM += (float)$date['morning'][$x]->milkQuality * (float)$date['morning'][$x]->fat;
                            $snfPlusM += (float)$date['morning'][$x]->milkQuality * (float)$date['morning'][$x]->snf;
                        @endphp
                        <td>{{$date['morning'][$x]->milkQuality}}</td>
                        <td>{{number_format($date['morning'][$x]->fat, 1)}}</td>
                        <td>{{number_format($date['morning'][$x]->snf, 0)}}</td>
                        <td>{{number_format($date['morning'][$x]->rate, 2)}}</td>
                        <td>{{number_format($date['morning'][$x]->amount, 2)}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                    @if(isset($date['evening'][$x]))
                        @php
                            $qtyloop += (float)$date['evening'][$x]->milkQuality;
                            $FatLoop += (float)$date['evening'][$x]->milkQuality * (float)$date['evening'][$x]->fat;
                            $SnfLoop += (float)$date['evening'][$x]->milkQuality * (float)$date['evening'][$x]->snf;
                            $amtloop += (float)$date['evening'][$x]->amount;
                             $fatkg += (float)($date['evening'][$x]->fatkg ?? 0);
                             $snfkg += (float)($date['evening'][$x]->snfkg ?? 0);

                            $countE++;
                            $totalAmountE += (float)$date['evening'][$x]->amount;
                            $totalQtyE += (float)$date['evening'][$x]->milkQuality;
                            $fatPlusE += (float)$date['evening'][$x]->milkQuality * (float)$date['evening'][$x]->fat;
                            $snfPlusE += (float)$date['evening'][$x]->milkQuality * (float)$date['evening'][$x]->snf;
                        @endphp
                        <td>{{$date['evening'][$x]->milkQuality}}</td>
                        <td>{{number_format($date['evening'][$x]->fat, 1)}}</td>
                        <td>{{number_format($date['evening'][$x]->snf, 0)}}</td>
                        <td>{{number_format($date['evening'][$x]->rate, 2)}}</td>
                        <td>{{number_format($date['evening'][$x]->amount, 2)}}</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    @endif
                    @php $x++; @endphp

                    @if($x == $mxLoop)
                        <td>{{ $qtyloop }}</td>
                        {{-- <td> @if($qtyloop !=0 ) {{ number_format($FatLoop/$qtyloop, 1, ".", "") }} @else 0.0 @endif </td>
                        <td> @if($qtyloop !=0 ) {{ number_format($SnfLoop/$qtyloop, 0, ".", "") }} @else 0 @endif </td> --}}
                        <td>{{ number_format($amtloop, 2, ".", "") }}</td>
                    @else
                        <td>-</td>
                        {{-- <td>-</td>
                        <td>-</td> --}}
                        <td>-</td>
                    @endif
                </tr>
            @endforeach

        @endforeach

    </tbody>

    <tfoot>
        @php 
            if($countM!=0){
                if($totalQtyM != 0){
                    $avgFatPlusM = number_format($fatPlusM/$totalQtyM, 1, ".", "");
                    $avgSnfPlusM = number_format($snfPlusM/$totalQtyM, 0, ".", "");
                }else{
                    $avgFatPlusM = "0.0";
                    $avgSnfPlusM = "0";
                }
            }else{
                $avgFatPlusM = 0;
                $avgSnfPlusM = 0;
            }
            if($countE!=0){
                if($totalQtyE != 0){
                    $avgFatPlusE = number_format($fatPlusE/$totalQtyE, 1, ".", "");
                    $avgSnfPlusE = number_format($snfPlusE/$totalQtyE, 0, ".", "");
                }else{
                    $avgFatPlusE = "0.0";
                    $avgSnfPlusE = "0";
                }
            }else{
                $avgFatPlusE = 0;
                $avgSnfPlusE = 0;
            }

            $totalQty = $totalQtyM + $totalQtyE;
            if($totalQty != 0){
                $avgSnf = number_format((($avgSnfPlusM * $totalQtyM) + ($avgSnfPlusE * $totalQtyE))/$totalQty, 0, ".", "");
                $avgFat = number_format((($avgFatPlusM * $totalQtyM) + ($avgFatPlusE * $totalQtyE))/$totalQty, 1, ".", "");
            }else{
                $avgSnf = "0.0";
                $avgFat = "0";
            }
        @endphp

        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td>Total</td>
            <td>{{$totalQtyM}}</td>
            <td>{{$avgFatPlusM}}</td>
            <td>{{$avgSnfPlusM}}</td>
            <td></td>
            <td style="font-family:'DejaVu Sans', sans-serif;font-size:14px">{{number_format($totalAmountM, 2)}}</td>
            <td>{{$totalQtyE}}</td>
            <td>{{$avgFatPlusE}}</td>
            <td>{{$avgSnfPlusE}}</td>
            <td></td>
            <td style="font-family:'DejaVu Sans', sans-serif;font-size:14px"> {{number_format($totalAmountE, 2)}}</td>

            <td>{{$totalQty}}</td>
            {{-- <td>{{$avgFat}}</td>
            <td>{{$avgSnf}}</td> --}}
            <td style="font-family:'DejaVu Sans', sans-serif;font-size:14px"> {{number_format($totalAmountM + $totalAmountE, 2)}}</td>
        </tr>
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td>Total </td>
            <td>Fatkg</td>
            <td>{{$fatkg}}</td>
            <td>Snfkg</td>
            <td>{{$snfkg}}</td>
            <td ></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-family:'DejaVu Sans', sans-serif;font-size:14px"></td>

            <td></td>
           
            <td style="font-family:'DejaVu Sans', sans-serif;font-size:14px"> </td>
        </tr>
    </tfoot>
</table>

<script>
function copyToPdfForm() {
    document.getElementById('pdf_partyCode').value = document.getElementById('memberCodeP').value;
    document.getElementById('pdf_partyName').value = document.getElementById('memberNameStP').value;
    document.getElementById('pdf_startDate').value = document.getElementById('memberPassbookStartDate').value;
    document.getElementById('pdf_endDate').value = document.getElementById('memberPassbookEndDate').value;
    document.getElementById('pdf_status').value = document.getElementById('status').value;

    
}




</script>
