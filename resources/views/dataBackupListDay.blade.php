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

@isset($message)
      <div class="alert alert-success">
        Suppplier Created
      </div>
@endisset
<div class="container">
    <a href="supplierForm?dairyId={{{ Session::get('loginUserInfo')->dairyId }}}">Add Supplier List </a>
    <div class="col-sm-12">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>StartTime</th>
                <th>EndTime </th>
                <th>Monday </th>
                <th>Tuesday</th>
                <th>Wednedsay</th>
                <th>Thursday </th>
                <th>Friday</th>
                <th>Sterday</th>
                <th>Sunday</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($dataBackupValue as $dataBackup)
                <tr>
                     <td>{{ $dataBackup[0]->startTime}}</td>
                     <td>{{ $dataBackup[0]->endTime}}</td>
                     <td>{{ $dataBackup[0]->monday}}</td>
                     <td>{{ $dataBackup[0]->tuesday}}</td>
                     <td>{{ $dataBackup[0]->wednedsay}}</td>
                     <td>{{ $dataBackup[0]->thursday}}</td>
                     <td>{{ $dataBackup[0]->friday}}</td>
                     <td>{{ $dataBackup[0]->sterday}}</td>
                     <td>{{ $dataBackup[0]->sunday }}</td>
                     <td> <a href="supplierEdit?supplierId={{ $dataBackup[0]->id}}"> Edit </a></td>
                     <td> <a onclick="deleteSupplier({{ $dataBackup[0]->id}});"> Delete </a></td>
                      
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