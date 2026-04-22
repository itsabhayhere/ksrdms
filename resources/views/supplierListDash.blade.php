@extends('theme.default') 
@section('content')

<div class="fcard margin-fcard-1 clearfix">
    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>{{ucfirst($type). " Suppliers"}}</h3>            
            <div class="light-color f-12">Total: {{count($supp)}}</div>
        </div>
        <div class="fr">
            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            {{-- <a href="supplierForm" class="btn btn-primary">Add Supplier </a> --}}
        </div>
    </div>

    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Supplier Code</th>
                    <th>Supplier Name</th>
                    <th>Person Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Mobile Number</th>
                    <th>Gstin</th>
                    <th>Address</th>
                    <th>Village/District</th>
                    <th>Balance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>

@php $i=0; @endphp 

        @foreach ($supp as $s)
            @php $i++; 
                $bal = DB::table("user_current_balance")->where("ledgerId", $s->ledgerId)->get()->first();
            @endphp

                <tr>
                    <td></td>
                    <td>{{ $s->supplierCode}}</td>
                    <td>{{ $s->supplierFirmName}}</td>
                    <td>{{ $s->supplierPersonName}}</td>
                    <td>{{ $s->supplierEmail}}</td>
                    <td>{{ $s->gender}}</td>
                    <td>{{ $s->supplierMobileNumber}}</td>
                    <td>{{ $s->supplierGstin}}</td>
                    <td>
                        {{ $s->supplierAddress}}, {{ $s->supplierState }},{{ $s->supplierCity }}
                    </td>
                    <td>{{ $s->supplierVillageDistrict }}</td>
                    <td> 
                        <span class="bold {{$bal->openingBalanceType}}"> 
                            {{ number_format((float)str_replace("-","",$bal->openingBalance),2) }}
                        </span>
                    </td>
                    <td> <a href="supplierEdit?supplierId={{ $s->id}}"> Edit </a></td>
                    <td> <a href="#" onclick="deleteSupplier({{ $s->id}});"> Delete </a></td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
    var t = $('#MyTable').DataTable( {
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
        },
        
        "order": [[ 1, 'asc' ]]
    } );

    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

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