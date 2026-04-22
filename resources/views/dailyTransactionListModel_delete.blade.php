@extends('theme.default')

@section('content')

<style>
.btn-success {
    padding: 6px 12px;
    background-color: #28a745;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    border: none;
}
.btn-success:hover {
    background-color: #218838;
}
</style>


<div class="fcard margin-fcard-1 clearfix">
        <div class="upper-controls clearfix">
            <div class="fl">
                <h3>Delete Milk Collection Data
</h3>
            </div>
            <div class="fr">
                {{-- <a class="btn btn-primary" href="DailyTransactionForm">Add New Transaction</a> --}}
            </div>
        </div>
        
    <div class="table-back">
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
                  {{ $d->date }}
                    {{-- <a href="DailyTransactionResendNoti?transactionId={{ $d->id}}&memberCode={{ $d->memberCode}}&dairyId={{ $d->dairyId}}" title="Notify on message or email or android" role="button" > <i class="fa fa-bell"></i> </a> --}}
                </td>
            </tr>
            @endforeach        
        </tbody>
    </table>
 </div>
</div>
<script>
$(document).ready(function() {
    $('#MyTable').DataTable({
        paging: true,           // pagination
        pageLength: 10,         // number of records per page
        lengthChange: true,     // user can select page length
        searching: true,        // enable search
        ordering: true,         // enable sorting
        info: true,             // show info (e.g., "Showing 1 to 10 of 50 entries")
        autoWidth: false,

        dom: 'Bfrtip',          // layout: Buttons + filter + table + pagination
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Deleted_Transactions_Report',
                text: '⬇️ Download Excel',
                className: 'btn btn-success'
            }
        ],

        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo($(column.footer()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
            });
        }
    });
});
</script>

@endsection