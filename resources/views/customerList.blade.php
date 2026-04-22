@extends('theme.default') 
@section('content') 
@isset($message)
<div class="alert alert-success">
    Suppplier Created
</div>
@endisset
<div class="fcard margin-fcard-1 clearfix">


    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>Customer List</h3>
            <div class="light-color f-12">Total: {{count($allCoustomer)}}</div>

        </div>
        <div class="fr">
            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            <a href="CustomerForm" class="btn btn-primary">Add Customer </a>
        </div>
    </div>

    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Address</th>
                    <th>Village/District</th>
                    <th>Balance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>


                @foreach ($allCoustomer as $allCoustomerData)
                @php 
                    $bal = DB::table("user_current_balance")->where("ledgerId", $allCoustomerData->ledgerId)->get()->first();
                @endphp
                <tr class="@if($allCoustomerData->customerCode == session()->get('loginUserInfo')->dairyId.'C1') cash-cust @endif">
                    <td>{{ $allCoustomerData->customerCode}}</td>
                    <td>{{ $allCoustomerData->customerName}}</td>
                    <td>{{ $allCoustomerData->gender}}</td>
                    <td>{{ $allCoustomerData->customerEmail}}</td>
                    <td>{{ $allCoustomerData->customerMobileNumber}}</td>
                    <td>
                        {{ $allCoustomerData->customerAddress}} , {{ $allCoustomerData->customerState}}, {{ $allCoustomerData->customerCity}}
                    </td>
                    <td>{{ $allCoustomerData->customerVillageDistrict }}</td>
                    <td> 
                        <span class="bold {{$bal->openingBalanceType}}"> 
                            {{ number_format((float)str_replace("-","",$bal->openingBalance),2) }}
                        </span>
                    </td>
                    <td> <a href="customerEdit?CustomerId={{ $allCoustomerData->id}}"> Edit </a></td>
                    <td> <a href="#" onclick="deleteSupplier({{ $allCoustomerData->id}});"> Delete </a></td>
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


function deleteSupplier(customerId){
    
    if(customerId){
         $.ajax({
                type:"POST",
                url:'deleteCustomer' ,
                data: {
                    customerId: customerId,
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