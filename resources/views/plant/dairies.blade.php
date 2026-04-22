@extends('plant.layout')

@section('content')

    <div class="fcard margin-fcard-1 clearfix">
        
        <div class="upper-controls clearfix">
            <div class="fl">
                <h3>Dairies</h3>
                <div class="light-color f-12" id="totaldairy">Total: {{count($dairies)}}</div>
            </div>

            {{-- <div class="fr">
                <input type="text" id="plant_id" class="form-control">
            </div>
            --}}

        </div>
       
    
        
        <div class="table-back">
            <table id="MyTable" class="display table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Society Code</th>
                        <th>Society Name </th>
                        <th>Society Contact</th>
                        <th>Address</th>
                        <th>Dairy Propritor </th>
                        <th>Propritor Contact</th>
                        <th></th>
                    </tr>
                </thead>
        
                <tbody id="searchdairy">
        
                    @foreach ($dairies as $r)
                        <tr class="dairyReq{{$r->id}}">
                            <td>{{ $r->society_code}}</td>
                            <td>{{ $r->dairyName}}</td>
                            <td>{{ $r->mobile}}</td>
                            <td>{{ $r->dairyAddress}}, {{ $r->state}}, {{ $r->city}}</td>
                            <td>{{ $r->dairyPropritorName}}</td>
                            <td>{{ $r->PropritorMobile.", ".$r->dairyPropritorEmail}}</td>    
                            <td>
                                {{-- <a href="javascript:void(0);" onclick="completeRequest({{ $r->dairyId}}, 'Accept');" class="btn btn-default btn-sm"> Accept </a> --}}
                                <a href="javascript:void(0);" onclick="completeRequest({{ $r->dairyId}}, 'Decline');" class="btn btn-danger btn-sm"> Remove </a>
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
                            .search( val ? '^'+val+'$':'', true, false)
                            .draw();
                    });
 
                column.data().unique().sort().each(function(d, j){
                    select.append( '<option value="'+d+'">'+d+'</option>')
                });
            });
        },
        "sScrollX": "100%", "sScrollXInner": "100%",
    });
});


    function completeRequest(id, action){
        $.confirm({
            // icon: 'fa fa-trash-o',
			title: 'Remove Dairy?',
			content: 'Are you sure to remove this request?',
			type: (action=='Accept')?'green':'red',
			typeAnimated: true,
			buttons: {
				accept: {
					text: action,
					btnClass: (action=='Accept')?'btn-green':'btn-red',
					action: function(){
                        sendCompleteRequest(id, action);
					}
				},
                cancel:{
					text: 'Cancel',
					btnClass: 'btn-default',
					action: function(){
                        return true;
					}
                }
			}
		});
    }

    function sendCompleteRequest(id, action){
        loader("show");
        $.ajax({
            type:"POST",
            url:'{{url("plant/plantAddRequestComplete")}}',
            data: {
                dairyId: id,
                action: action
            },
            success:function(res){
                if(res.error){
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 12000);
                }else{
                    $("tr.dairyReq"+id).slideUp().remove();
                    
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-success");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 12000);
                }
            },
            error:function(res){
                $.alert("Something is going wrong. check your internet.");
            }
        }).done(function(res){
            loader("hide");
            console.log(res);
        });
    }

</script>
@endsection