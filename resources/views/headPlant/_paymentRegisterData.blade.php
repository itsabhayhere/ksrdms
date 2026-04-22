
            <thead>
                    <tr>
                        <th>Society Code</th>
                        <th>Member Name</th>
                        <th>Total Samples Poured</th>
                        <th>Qty</th>
                        <th>Avg Fat</th>
                        <th>Avg SNF</th>
                        <th>Total Amount</th>
                      
                    </tr>
                </thead>
        <tbody>
        @php 
            $total  = 0;
            $qty    = 0;
            $avFat  = 0;
            $avSnf  = 0;
            $shift  = 0;
            $i      = 0; 
        @endphp
        @foreach($dailyTrns as $d)
            @php
                $i++;
                $total  += $d->amount;
                $qty    += (float)$d->qty;
                $shift  += $d->noOfShift;
                $avSnf  += $d->snf;
                $avFat  += $d->fat;
            @endphp
            <tr>
                <td>{{$d->memberCode}}</td>
                <td>{{$d->memberName}}</td>
                <td>{{$d->noOfShift}}</td>
                <td>{{number_format($d->qty, 1, ".", "")}}</td>
                <td>{{number_format($d->fat, 1, ".", "")}}</td>
                @if($d->snf == 0)
                    <td>-</td>
                @else
                    <td>{{number_format($d->snf, 0, ".", "")}}</td>
                @endif
                <td>{{number_format($d->amount, 2, ".", "")}}</td>
                
            </tr>
        @endforeach

        </tbody>
    <tfoot>
                <tr style="font-weight:bolder;background: #e4e4e4;">
                    <td>Total <small class="text-grey">(Comlete Report Payment)</small></td>
                    <td></td>
                    <td>{{$shift}}</td>
                    <td>{{number_format($qty, 1, ".", "")}}</td>
                    <td>{{number_format($avFat/$i, 1)}}</td>
                    @if($avSnf == 0)
                        <td>-</td>
                    @else
                        <td>{{number_format($avSnf/$i, 0)}}</td>
                    @endif
                    <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$total}}</td>
                   
                </tr>
            </tfoot>
            