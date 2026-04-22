
        @php 
            $total = 0;
            $avFat = 0;
            $avSnf = 0;
            $avQty = 0;
            $i     = 0;
        @endphp
        
        @foreach ($dailyTransactions as $d)

        @php
            $i++; 
            $total += $d->amount;
            $avFat += $d->fat;
            $avSnf += $d->snf;
            $avQty += $d->milkQuality;            
        @endphp
        <tr>
            <td>{{ date("d-m-Y", strtotime($d->date)) }}</td>
            <td>{{ ucfirst($d->shift) }}</td>
            <td>{{ $d->milkType }}</td>
            <td>{{ $d->milkQuality }}</td>
            <td>{{ $d->fat }}</td>
            <td> @if($d->snf==NULL) - @else {{ $d->snf }} @endif</td>
            <td>{{ $d->rate }}</td>
            <td>{{ $d->amount }}</td>
        </tr>
        @endforeach
        @if($i>0)
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td>Total</td>
            <td></td>
            <td></td>
            <td>{{ $avQty }}</td>
            <td>{{ number_format($avFat/$i, 2) }} </td>
            <td>{{ number_format($avSnf/$i, 2) }} </td>
            <td></td>
            <td>{{ $total }}</td>
        </tr>
        @endif