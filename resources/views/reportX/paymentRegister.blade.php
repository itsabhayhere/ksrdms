<table class="table paymentRegister-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Mem. Code</th>
            <th>Member Name</th>
            <th>No. of Shift</th>
            <th>Qty</th>
            <th>Avg Fat</th>
            <th>Avg SNF</th>
            <th>Total Amount</th>
            <th>Signature</th>
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
                <td></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td>Total</td>
            <td></td>
            <td>{{$shift}}</td>
            <td>{{number_format($qty, 1, ".", "")}}</td>
            @if($i == 0)
                <td>-</td>
            @else
                <td>{{number_format($avFat/$i, 1)}}</td>
            @endif
            @if($avSnf == 0)
                <td>-</td>
            @else
                <td>{{number_format($avSnf/$i, 0)}}</td>
            @endif
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{$total}}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

@if($avSnf == 0)
    <script>
        $("#paymentRegister-table").find("tr").each(function() {
            $(this).filter("th:eq(5)").remove();
            $(this).filter("td:eq(5)").remove();
        });
    </script>
@endif
