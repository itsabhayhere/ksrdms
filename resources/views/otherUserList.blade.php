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
                <th>User Name</th>
                <th>Father Name</th>
                <th>Aadhar Number</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Mobile Number</th>
                <th>Address</th>
                <th>Village/District</th>
                <th>Pin code</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($otherUsers as $otherUsersData)
                <tr>
                       <td>{{ $otherUsersData->userName}}</td>
                       <td>{{ $otherUsersData->fatherName}}</td>
                       <td>{{ $otherUsersData->aadharNumber}}</td>
                       <td>{{ $otherUsersData->userEmail}}</td>
                       <td>{{ $otherUsersData->gender}}</td>
                       <td>{{ $otherUsersData->mobileNumber}}</td>
                       <td>{{ $otherUsersData->address}}, {{ $otherUsersData->state }},{{ $otherUsersData->city }}</td>
                    
                       <td>{{ $otherUsersData->villageDistrict }}</td>
                       <td>{{ $otherUsersData->pincode }}</td>
                       <td> <a href="otherUserEdit?otherUserId={{ $otherUsersData->id}}"> Edit </a></td>
                       <td> <a onclick="deleteSupplier({{ $otherUsersData->id}});"> Delete </a></td>
                      
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


function deleteSupplier(otherUserId){
//    alert(supplierId);
    
    if(otherUserId){
         $.ajax({
                type:"POST",
                url:'deleteSupplier' ,
                data: {
                    otherUserId: otherUserId,
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