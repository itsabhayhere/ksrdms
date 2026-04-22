@extends('theme.default')

@section('content')

<div class="fcard margin-fcard-1 clearfix">
        <div class="upper-controls clearfix">
            <div class="fl">
                <h3>Transaction List</h3>
            </div>
            <div class="fr">
                <a class="btn btn-primary" href="DailyTransactionForm">Add New Transaction</a>
            </div>
        </div>
        
    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Member Code</th>
                <th>Member Name</th>
                <th>Date</th>
                <th>Milk Type</th>
                <th>Milk Quality</th>
                <th>Rate Card Type</th>
                <th>Fat</th>
                <th>Snf</th>
                <th>Amount</th>
                <th>Pdf</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($dailyTransactions as $i)
                <tr>
                       <td>{{ $i->memberCode}}</td>
                       <td>{{ $i->memberName}}</td>
                       <td>{{ $i->date}}</td>
                       <td>{{ $i->milkType}}</td>
                       <td>{{ $i->milkQuality}}</td>
                       <td>{{ $i->shift}}</td>
                       <td>{{ $i->fat}}</td>
                       <td>{{ $i->snf}}</td>
                       <td>{{ $i->amount }}</td>
                       <td> <a href="DailyTransactionPsf?listId={{ $i->id}}"> Get Pdf </a></td>
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