@extends('theme.default')



@section('content')



<div class="fcard margin-fcard-1 clearfix">

    <div class="upper-controls clearfix">

        <div class="fl">

            <h3>Member List of {{ucfirst($type)}} Members</h3>

            <div class="light-color f-12">Total: {{count($members)}}</div>

        </div>

        {{-- <div class="fr">

            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>

            <a class="btn btn-primary" href="memberSetupForm">Add New Member</a>

        </div> --}}

    </div>

    <div class="table-back">

        <table id="MyTable" class="table table-bordered" cellspacing="0" width="100%">

        <thead>

            <tr>

                @if($type==("inactive"||"active"))

                    <th>Last Milk Transaction</th>

                @endif

                <th>Member Code</th>

                <th>Member Name</th>

                <th>Email</th>

                <th>Mobile Number</th>

                <th>Aadhar Number</th>

                <th>Address</th>

                <th>Village/District</th>

                <th>Balance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>

                <th></th>

            </tr>

        </thead>



        <tbody> 



            @foreach ($members as $m)

                @php

                    if($type==("inactive"||"active")){

                        $last = DB::table('daily_transactions')->selectRaw("MAX(date) as lastdate")->where(["memberCode" => $m->memberPersonalCode, "dairyId" => $m->dairyId])->get()->first();

                        if($last->lastdate == null){

                            $lastTrans = "Never";

                        }else{

                            $lastTrans  = $last->lastdate;

                        }

                    }

                @endphp



                <tr>

                    @if($type==("inactive"||"active"))

                        <td>{{ $lastTrans}}</td>

                    @endif

                    <td>{{ $m->memberPersonalCode}}</td>

                    <td>{{ $m->memberPersonalName}}</td>

                    <td>{{ $m->memberPersonalEmail}}</td>

                    <td>{{ $m->memberPersonalMobileNumber}}</td>

                    <td>{{ $m->memberPersonalAadarNumber}}</td>

                    <td>{{ $m->memberPersonalAddress}}, {{ $m->memberPersonalState}}, {{ $m->memberPersonalCity }}</td>

                    <td>{{ $m->memberPersonalDistrictVillage }}</td>

                    <td>

                        <span class="bold {{$m->openingBalanceType}}">

                            {{number_format((float)str_replace("-","",$m->openingBalance),2)}}

                        </span>

                    </td>

                    <td>

                        <a href="editMemberInfo?member_id={{ $m->id}}" class="link"> <i class="fa fa-edit"></i></a>

                        <a href="#" onclick="deleteSupplier({{ $m->id}});" class="danger-text"> <i class="fa fa-trash-o"></i> </a>

                    </td>

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





function deleteSupplier(memberId){

   

    if(memberId){

         $.ajax({

                type:"POST",

                url:'deleteMember' ,

                data: {

                    memberId: memberId,

                },

               success:function(res){  

                alert("Member Successfully Delated");        

                location.reload();

               }

            });

      }

  }



</script>

@endsection