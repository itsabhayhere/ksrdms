{{--
    plantSaleListModel.blade.php
    Loaded via AJAX into #table-plant-sales on plantSaleForm.
    Uses real `sales` table column names:
    partyName, productQuantity, fat, snf, productPricePerUnit, amount, paidAmount, shift
--}}
<table id="plantSaleTable" class="display table table-bordered table-hover"
       style="width:100%;font-size:13px;">
    <thead class="bg-light">
        <tr>
            <th style="display:none;">Sort</th>
            <th>#</th>
            <th>Plant Name</th>
            <th>Shift</th>
            <th>Milk Type</th>
            <th>Qty (Ltr)</th>
            <th>Fat</th>
            <th>SNF</th>
            <th>Rate (₹)</th>
            <th>Amount (₹)</th>
            <th>Paid (₹)</th>
            <th style="min-width:90px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $i => $s)
        <tr>
            <td style="display:none;">{{ $s->created_at }}</td>
            <td>{{ $i + 1 }}</td>
            <td><b>{{ $s->partyName }}</b></td>
            <td>
                @if($s->shift == 'morning')
                    <span class="label label-warning">Morning</span>
                @else
                    <span class="label label-info">Evening</span>
                @endif
            </td>
            <td>{{ ucfirst($s->milkType ?? '-') }}</td>
            <td>{{ number_format($s->productQuantity, 1) }}</td>
            <td>{{ number_format($s->fat ?? 0, 2) }}</td>
            <td>{{ $s->snf ? number_format($s->snf, 2) : '—' }}</td>
            <td>{{ number_format($s->productPricePerUnit ?? 0, 2) }}</td>
            <td><b>₹{{ number_format($s->amount, 2) }}</b></td>
            <td>₹{{ number_format($s->paidAmount ?? 0, 2) }}</td>
            <td>
                <a href="#" class="btn btn-xs btn-primary"
                   onclick="editSale(event, {{ $s->id }}, '{{ addslashes($s->partyName) }}')">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="#" class="btn btn-xs btn-danger"
                   onclick="deleteSale(event, {{ $s->id }})">
                    <i class="fa fa-trash"></i>
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="12" class="text-center text-muted" style="padding:20px;">
                No plant sales found for this date &amp; shift.
            </td>
        </tr>
        @endforelse
    </tbody>
    @if(count($sales) > 0)
    <tfoot>
        <tr style="background:#f9f9f9;font-weight:bold;">
            <td style="display:none;"></td>
            <td colspan="4" class="text-right">Totals:</td>
            <td>{{ number_format($sales->sum('productQuantity'), 1) }} L</td>
            <td colspan="3"></td>
            <td>₹{{ number_format($sales->sum('amount'), 2) }}</td>
            <td>₹{{ number_format($sales->sum('paidAmount'), 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>