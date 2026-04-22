<table id="MyTable" class="MyTable-dailyTransactionClass display table-bordered tright" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Member Code</th>
            <th>Milk Type</th>
            <th>Quantity</th>
            <th>Fat</th>
            <th>Snf</th>
            <th>Fat Kg</th>
            <th>Snf Kg</th>
            <th>Rate</th>
            <th>Total Amount</th>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>
    </thead>

    <tbody class="">
       @php 
            $i = 0;
            $totalQty = 0;
            $totalFat = 0;
            $totalSnf = 0;
            $totalFatKg = 0;
            $totalSnfKg = 0;
            $totalRate = 0;
            $totalAmount = 0;
            $fatTotal=0;
            $totalQty=0;
        @endphp
        @php $i = 0; @endphp @foreach ($dailyTransactions as $d) @php $i++; @endphp

           @php 
                $i++;
                $totalQty += $d->milkQuality;
                $totalFat += $d->fat;
                $totalSnf += $d->snf ?? 0;
                $totalFatKg += $d->fatkg;
                $totalSnfKg += $d->snfkg;
                $totalRate += $d->rate;
                $totalAmount += $d->amount;

            @endphp

        <tr>
            <td>{{ $i }}</td>
            <td>{{ $d->memberCode }}</td>
            <td>{{ $d->milkType }}</td>
            <td>{{ $d->milkQuality }}</td>
            <td>{{ number_format($d->fat, 1, ".", "") }}</td>
            <td>@if($d->snf==NULL) - @else {{ $d->snf }} @endif</td>
            <td>{{ $d->fatkg }}</td>
            <td>{{ $d->snfkg }}</td>
            <td>{{ number_format($d->rate, 2, ".", "") }}</td>
            <td>{{ number_format($d->amount, 2, ".", "") }}</td>
            <td>
                <a href="DailyTransactionEdit?transactionId={{ $d->id }}" title="Edit" role="button" onclick="editTransaction(event, {{$d->id}}, '{{$d->memberCode}}')"> <i class="fa fa-edit"></i> </a>
                &nbsp;
                <a href="DailyTransactionPsf?listId={{ $d->id }}" title="Get PDF File"> <i class="fa fa-file-pdf-o"></i> </a>
                &nbsp;
                {{-- <a href="DailyTransactionResendNoti?transactionId={{ $d->id}}&memberCode={{ $d->memberCode}}&dairyId={{ $d->dairyId}}" title="Notify on message or email or android" role="button" > <i class="fa fa-bell"></i> </a> --}}
            </td>
        </tr>
        @endforeach
    </tbody>

    
    @php
    $fatTotal = ($totalQty > 0) ? floor(($totalFatKg / $totalQty * 100) * 10) / 10 : 0;
    $snfTotal = ($totalQty > 0) ? round(($totalSnfKg / $totalQty * 10000)) : 0;
    $rateTotal = ($totalQty > 0) ? number_format($totalAmount / $totalQty, 2, ".", "") : 0;
@endphp

<tfoot style="font-weight: bold; background: #f4f4f4;">
    <tr>
        <td colspan="3" class="text-right">Total:</td>
        <td>{{ number_format($totalQty, 1, ".", "") }}</td>
        <td>{{ number_format($fatTotal, 2, ".", "") }}</td>   
        <td>{{ number_format($snfTotal, 2, ".", "") }}</td>   
        <td>{{ number_format($totalFatKg, 2, ".", "") }}</td>
        <td>{{ number_format($totalSnfKg, 2, ".", "") }}</td>
        <td>{{ $rateTotal }}</td>                            
        <td>{{ number_format($totalAmount, 2, ".", "") }}</td>
        <td></td>
    </tr>
</tfoot>

    

</table>