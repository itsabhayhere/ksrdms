@extends('theme.default')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="fcard margin-fcard-1 clearfix">
        <div class="upper-controls clearfix">
            <div class="fl">
                <h3>Purchase List</h3>
                {{-- <div class="light-color f-12">Total: {{count($purchaseList)}}</div> --}}
            </div>
            <div class="fr">
                <a class="btn btn-primary" href="DailyTransactionForm">Add Purchase</a>
            </div>
        </div>
    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ledger</th>
                <th>Party Name</th>
                <th>Items Purchased</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Price Per Unit</th>
                <th>Purchase Type</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($purchaseList as $purchaseListData)
               
                <tr>
                   <td>{{ $purchaseListData->ledgerId}}</td>
                   <td>{{ $purchaseListData->partyName}}</td>
                   <td>{{ $purchaseListData->itemsPurchased}}</td>
                   <td>{{ $purchaseListData->unit}}</td>
                   <td>{{ $purchaseListData->quantity}}</td>
                   <td>{{ $purchaseListData->PricePerUnit}}</td>
                   <td>{{ $purchaseListData->purchaseType}}</td>
                   <td>{{ $purchaseListData->amount}}</td>
                   <td>{{ $purchaseListData->date }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#MyTable').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                //to select and search from grid
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );
} );
</script>
@endsection