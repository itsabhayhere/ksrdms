<table class="shift-summary-table table table-bordered tright table-striped">
    <thead>
        <tr>
            <th colspan="6" style="text-align:center;">{{ucfirst($shift1)}} Shift</th>
        </tr>
        <tr>
            <th>Member Code</th>
            <th>Member Name</th>
            <th>Quantity</th>
            <th>FAT</th>
            <th>SNF</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
        $total=0; $i=0; $totalQty = 0; $totalFatPlus = 0; $totalSnfPlus = 0; $totalAmount = 0;
        foreach($data1 as $d):
            $i++;
            $total += (float)$d->amount;
            $totalQty += (float)$d->milkQuality;
            $totalFatPlus += (float)$d->milkQuality * (float)$d->fat;
            $totalSnfPlus += (float)$d->milkQuality * (float)$d->snf;
            $totalAmount += (float)$d->amount;
        @endphp
            <tr>
                <td>{{$d->memberCode}}</td>
                <td>{{$d->memberName}}</td>
                <td>{{$d->milkQuality}}</td>
                <td>{{number_format($d->fat, 1)}}</td>
                <td>@if($d->snf == 0) - @else {{number_format($d->snf, 0)}} @endif</td>
                <td>{{number_format($d->amount, 2)}}</td>
            </tr>
        @php endforeach; @endphp
        <tfoot>
        @if($i>0)
            <tr style="font-weight:bolder;background: #e4e4e4;">
                <td>Total</td>
                <td></td>
                <td>{{$totalQty}}</td>
                <td>{{number_format($totalFatPlus/$totalQty, 1, ".", "")}}</td>
                <td>{{number_format($totalSnfPlus/$totalQty, 0, ".", "")}}</td>
                <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($totalAmount, 2, ".", "")}}</td>
            </tr>
        </tfoot>
        @endif

@if(isset($data2))
            <tr>
                <th colspan="6" style="text-align:center;">{{ucfirst($shift2)}} Shift</th>
            </tr>
            <tr>
                <th>Member Code</th>
                <th>Member Name</th>
                <th>Quantity</th>
                <th>FAT</th>
                <th>SNF</th>
                <th>Amount</th>
            </tr>
            @php
            $total=0; $i=0; $totalQty = 0; $totalFatPlus = 0; $totalSnfPlus = 0; $totalAmount = 0;
            foreach($data2 as $d):
                $i++;
                $total += (float)$d->amount;
                $totalQty += (float)$d->milkQuality;
                $totalFatPlus += (float)$d->milkQuality * (float)$d->fat;
                $totalSnfPlus += (float)$d->milkQuality * (float)$d->snf;
                $totalAmount += (float)$d->amount;
            @endphp
                <tr>
                    <td>{{$d->memberCode}}</td>
                    <td>{{$d->memberName}}</td>
                    <td>{{$d->milkQuality}}</td>
                    <td>{{number_format($d->fat, 1)}}</td>
                    <td>@if($d->snf == 0) - @else {{number_format($d->snf, 0)}} @endif</td>
                    <td>{{number_format($d->amount, 2)}}</td>
                </tr>
            @php endforeach; @endphp

        </tbody>
        <tfoot>
            @if($i>0)
                <tr style="font-weight:bolder;background: #e4e4e4;">
                    <td>Total</td>
                    <td></td>
                    <td>{{$totalQty}}</td>
                    <td>{{number_format($totalFatPlus/$totalQty, 1, ".", "")}}</td>
                    <td>{{number_format($totalSnfPlus/$totalQty, 0, ".", "")}}</td>
                    <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{number_format($totalAmount, 2, ".", "")}}</td>
                </tr>
            @endif
        </tfoot>
    @endif
</table>
