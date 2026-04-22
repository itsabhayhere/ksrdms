<table id="MyTable" class="MyTable-dailyTransactionClass display table-bordered tright" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Member Code</th>
                <th>Milk Type</th>
                <th>Quantity</th>
                <th>Fat</th>
                <th>Snf</th>
                <th>Rate</th>
                <th>Total Amount</th>
                <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>
        </thead>

        <tbody class="">
            @php $i = 0; @endphp @foreach ($dailyTransactions as $d) @php $i++; @endphp
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->memberCode }}</td>
                <td>{{ $d->milkType }}</td>
                <td>{{ $d->milkQuality }}</td>
                <td>{{ number_format($d->fat, 1, ".", "") }}</td>
                <td> @if($d->snf==NULL) - @else {{ $d->snf }} @endif</td>
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
    </table>
