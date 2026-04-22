<table class="table paymentRegister-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Mem. Code</th>
            <th>Member Name</th>
            <th>No. of Shift</th>
            <th>Qty</th>
            <th> Fatkg</th>
            <th> SnfKg</th>
            <th>Avg Fat</th>
            <th>Avg SNF</th>
            <th>Total Amount</th>
            <th>Signature</th>
            @if(!empty($full))
            <th>Product Purchase</th>
            <th>Advance</th>
            <th>Credit</th>
            <th>Local Sale</th>
            <th>Current Balance</th>
            @endif

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

             $totalProductSale = 0;
    $totalAdvance     = 0;
    $totalCredit      = 0;
    $totalLocalSale   = 0;
    $avFatkg =0;
    $fatKg_total=0;
    $snfKg_total=0;

        @endphp
        @foreach($dailyTrns as $d)
            @php
                $i++;
                $total  += $d->amount;
                $qty    += (float)$d->qty;
                $shift  += $d->noOfShift;
                $avSnf  += (float)(($d->snfkg/$d->qty)*10000);
                $avFat = (float)(($d->fatkg/$d->qty)*100);
                $fatKg_total+=$d->fatkg;
                $snfKg_total+=$d->snfkg;




                $avFatkg += $d->fat*$d->qty;

                        $totalProductSale += $d->product_sale;
        $totalAdvance     += $d->advance;
        $totalCredit      += $d->credit;
        $totalLocalSale   += $d->local_sale;
            @endphp
            <tr>
                <td>{{$d->memberCode}}</td>
                <td>{{$d->memberName}}</td>
                <td>{{$d->noOfShift}}</td>
                <td>{{number_format($d->qty, 1, ".", "")}}</td>
                   <td>{{$d->fatkg}}</td>
            <td>{{$d->snfkg}}</td>
                <td>{{number_format($avFat, 2, ".", "")}}</td>
                @if($d->snf == 0)
                    <td>-</td>
                @else
                    <td>{{number_format($d->snf, 0, ".", "")}}</td>
                @endif

                <td>{{number_format($d->amount, 2, ".", "")}}</td>
                <td></td>
            @if(!empty($full))

                <td>{{$d->product_sale}}</td>
                 <td>{{$d->advance}}</td>
                <td>{{$d->credit}}</td>
                <td>{{$d->local_sale}}</td>
                <td>{{$d->currentBalance}}</td> 
                @endif
              
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td>Member Name</td>

            <td>{{$shift}}</td>
            <td>{{number_format($qty, 1, ".", "")}}</td>

            <td>{{$fatKg_total}}</td>
            <td>{{$snfKg_total}}</td>


            <td>{{number_format(($avFatkg/$qty), 2, ".", "")}}</td>
          
            @if($avSnf == 0)
                <td>-</td>
            @else
                <td>{{number_format($avSnf/$i, 0)}}</td>
            @endif


            <td> {{$total}}</td>
            <td></td>

        @if(!empty($full))
        <td>{{number_format($totalProductSale, 2)}}</td>
        <td>{{number_format($totalAdvance, 2)}}</td>
        <td>{{number_format($totalCredit, 2)}}</td>
        <td>{{number_format($totalLocalSale, 2)}}</td>
        <td></td>
        @endif
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
