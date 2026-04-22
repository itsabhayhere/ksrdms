@extends('theme.default') 
@section('content') @if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif @isset($message)
<div class="alert alert-success">
    Suppplier Created
</div>
@endisset

<div class="fcard margin-fcard-1 clearfix">

    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>Suppliers List</h3>
        </div>
        <div class="fr">
            <a href="supplierForm" class="btn btn-primary">Add Supplier List </a>
        </div>
    </div>

    <div class="col-sm-12">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Party Name</th>
                    <th>Product Type</th>
                    <th>Milk Type</th>
                    <th>Unit</th>
                    <th>Product Price Per Unit</th>
                    <th>Date</th>
                    <th>Sale Type</th>
                    <th>amount</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($sales as $saleListData)
                <tr>
                    <td>{{ $saleListData->partyName}}</td>
                    <td>{{ $saleListData->productType}}</td>
                    <td>{{ $saleListData->milkType}}</td>
                    <td>{{ $saleListData->unit}}</td>
                    <td>{{ $saleListData->saleDate}}</td>
                    <td>{{ $saleListData->productPricePerUnit}}</td>
                    <td>{{ $saleListData->saleType}}</td>
                    <td>{{ $saleListData->amount }}</td>
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


function deleteSupplier(supplierId){
//    alert(supplierId);
    
    if(supplierId){
         $.ajax({
                type:"POST",
                url:'deleteSupplier' ,
                data: {
                    supplierId: supplierId,
                },
               success:function(res){  
                alert(res);        
                location.reload();
               }
            });
      }
  }

</script>
@endsection