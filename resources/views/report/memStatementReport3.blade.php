<table class="table memStatement3-table tright table-bordered table-stripped">
    <thead>
        <tr>
            <th>Member Code</th>
            <th>Date</th>
            <th>Activity</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Cash</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @php
            $balType = $ubal->openingBalanceType == "credit" ? "Cr." : "Dr.";
        @endphp

        {{-- Opening Balance --}}
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{ strtotime($opb->created_at) }}">{{ date("d-m-Y", strtotime($opb->created_at)) }}</td>
            <td>Opening Balance</td>
            <td>{{ $opb->amountType == "credit" ? $opb->finalAmount : '-' }}</td>
            <td>{{ $opb->amountType == "debit" ? $opb->finalAmount : '-' }}</td>
            <td>-</td>
            <td></td>
        </tr>

        {{-- Milk Collection --}}
        @foreach($balSheet['milkCollection'] as $d)
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{ strtotime($d->created_at) }}">{{ date('d-m-Y', strtotime($d->date)) }}</td>
            <td>Milk Collection</td>
            <td>{{ $d->amount }}</td>
            <td>-</td>
            <td>-</td>
            <td>{{ $d->currentBalance ?? '' }}</td>
        </tr>
        @endforeach

        {{-- Local Sale --}}
        @foreach($balSheet['localsaleFinal'] as $d)
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{ strtotime($d->created_at) }}">{{ date('d-m-Y', strtotime($d->saleDate)) }}</td>
            <td>Local Sale</td>
            <td>-</td>
            <td>{{ number_format($d->finalAmount - $d->paidAmount, 2, ".", "") }}</td>
            <td>{{ number_format($d->paidAmount, 2, ".", "") }}</td>
            <td>{{ $d->currentBalance ?? '' }}</td>
        </tr>
        @endforeach

        {{-- Product Sale --}}
        @foreach ($balSheet['productsale'] as $item)
            @php
                $pro = $item->productName ?? $item->productCode;
            @endphp
            <tr>
                <td>{{ $mem->memberPersonalCode }}</td>
                <td data-sort="{{ strtotime($item->created_at) }}">{{ date("d-m-Y", strtotime($item->saleDate)) }}</td>
                <td><b>{{ $item->productQuantity }} x </b>{{ $pro }} (Rate: {{ $item->productPricePerUnit }}, Discount: {{ $item->discount }})</td>
                <td>-</td>
                <td>{{ number_format(($item->finalAmount - $item->paidAmount), 2, ".", "") }}</td>
                <td>{{ number_format($item->paidAmount, 2, ".", "") }}</td>
                <td>{{ $item->currentBalance ?? '' }}</td>
            </tr>
        @endforeach

        {{-- Advance --}}
        @foreach ($balSheet['advance'] as $item)
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{ strtotime($item->date) }}">{{ date("d-m-Y", strtotime($item->date)) }}</td>
            <td>Advance: {{ $item->remark }}</td>
            <td>-</td>
            <td>{{ number_format($item->amount, 2, ".", "") }}</td>
            <td></td>
            <td>{{ $item->currentBalance ?? '' }}</td>
        </tr>
        @endforeach

        {{-- Credit --}}
        @foreach ($balSheet['credit'] as $item)
        <tr>
            <td>{{ $mem->memberPersonalCode }}</td>
            <td data-sort="{{ strtotime($item->date) }}">{{ date("d-m-Y", strtotime($item->date)) }}</td>
            <td>Credit: {{ $item->remark }}</td>
            <td>{{ number_format($item->amount, 2, ".", "") }}</td>
            <td>-</td>
            <td>-</td>
            <td>{{ $item->currentBalance ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr style="font-weight:bolder;background: #e4e4e4;">
            <td colspan="3"></td>
            <td>Current Balance</td>
            <td style="font-family:'DejaVu Sans', sans-serif;">&#8377; {{ number_format($ubal->openingBalance, 2 ,".", "") . " " . $balType }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
