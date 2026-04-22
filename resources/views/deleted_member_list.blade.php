@extends('theme.default') 
@section('content')

<div class="fcard margin-fcard-1 pt-0 clearfix">
    <div class="upper-controls clearfix">
        <div class="fl">
            <h3>Deleted Member List</h3>
            {{--<div class="light-color f-12">Total: {{count($members)}}</div> --}}
        </div>
        <div class="fr">
            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            {{-- <a class="btn btn-primary" href="memberSetupForm">Add New Member</a> --}}
        </div>
    </div>
    <div class="table-back">
        <table id="MyTable" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Member Code</th>
                    <th>Member Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Mobile Number</th>
                    <th>Aadhar Number</th>
                    <th>Address</th>
                    <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                </tr>
            </thead>

            <tbody>

                {{-- @php dd($members) @endphp --}}
                @foreach ($members as $d)
                <tr>
                    <td>{{ $d->memberPersonalCode}}</td>
                    <td>{{ $d->memberPersonalName}}</td>
                    <td>{{ $d->memberPersonalEmail}}</td>
                    <td>{{ $d->memberPersonalGender}}</td>
                    <td>{{ $d->memberPersonalMobileNumber}}</td>
                    <td>{{ $d->memberPersonalAadarNumber}}</td>
                    <td>{{ $d->memberPersonalAddress}}, {{ $d->memberPersonalState}}, {{ $d->memberPersonalCity }}</td>
                    <td>
                        <a href="editMemberInfo?member_id={{ $d->id}}&restore=true" class="link"> <i class="fa fa-edit"></i> Restore</a>
                        {{-- <a href="#" onclick="deleteMember(this, {{ $d->id}});" class="danger-text" data-code="{{$d->memberPersonalCode}}" data-name="{{$d->memberPersonalName}}"> <i class="fa fa-trash-o"></i> </a> --}}
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#MyTable').DataTable({
            initComplete: function (){
                this.api().columns().every( function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo( $(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                    //to select and search from grid
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        });
    
                    column.data().unique().sort().each( function (d, j) {
                        select.append( '<option value="'+d+'">'+d+'</option>')
                    });
                });
            }
        });
    });


    function deleteMember(e, memberId){
        memberName = $(e).data("name");
        memberCode = $(e).data("code");

        $.confirm({
            title: "Are you sure.",
            content: 'You are about to delete member \n Code: '+memberCode+'\n Name: '+memberName,
            type: 'orange',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: "Proceed",
                    btnClass: 'btn-danger',
                    action: function(confirm){
                        realDelete(memberId)
                    },
                },
                cancel:{
                }
            }

        });
    }

  
    function realDelete(memberId){
        console.log("sdf");
        if(memberId){
            $.ajax({
                type:"POST",
                url:'deleteMember' ,
                data: {
                    memberId: memberId,
                },
                success:function(res){  
                    $.alert("Member Successfully Delated");        
                    location.reload();
                }
            });
        }
    }

</script>
@endsection