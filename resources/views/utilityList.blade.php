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
<div class="fcard margin-fcard-1 clearfix">
    
    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>Utilities</h3>
        </div>
        <div class="fr">
            <a class="btn btn-primary" href="utilitySetupForm">Add New Utility</a>
        </div>
    </div>

    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Machin Type </th>
                <th>Communication Port </th>
                <th>Max Speed </th>
                <th>Echo</th>
                <th>Connection Perference Data Bits</th>
                <th>Connection Perference Parity </th>
                <th>Connection Perference Stop Bits</th>
                <th>Flow Control</th>
                <th>Weight Mode</th>
                <th>WeightMode Auto Tare</th>
                <th>WeightMode No Training</th>
                <th>WeightMode Weight In Doublke Decimal </th>
                <th>WeightMode Write In </th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($utility as $utilityData)
                <tr>
                       <td>{{ $utilityData->machinType}}</td>
                       <td>{{ $utilityData->communicationPort}}</td>
                       <td>{{ $utilityData->maxSpeed}}</td>
                       <td>{{ $utilityData->echo}}</td>
                       <td>{{ $utilityData->connectionPerferenceDataBits}}</td>
                       <td>{{ $utilityData->connectionPerferenceParity}}</td>
                       <td>{{ $utilityData->connectionPerferenceStopBits}}</td>
                       <td>{{ $utilityData->flowControl}}</td>
                       <td>{{ $utilityData->weightMode }}</td>
                       <td>{{ $utilityData->weightMode_auto_tare }}</td>
                       <td>{{ $utilityData->weightMode_no_training }}</td>
                       <td>{{ $utilityData->weightMode_weight_in_doublke_decimal }}</td>
                       <td>{{ $utilityData->weightMode_write_in }}</td>
                       <td> <a href="portEdit?utilityId={{ $utilityData->id}}"> Edit </a></td>
                       <td> <a href="" onclick="DeleteUtility({{ $utilityData->id}});"> Delete </a></td>
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


function DeleteUtility(utilityId){
//    alert(utilityId);
    
    if(utilityId){
         $.ajax({
                type:"POST",
                url:'utilityDelete' ,
                data: {
                    utilityId: utilityId,
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