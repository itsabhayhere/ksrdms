@extends('spradmin.layout') 
@section("content")

<style>
a.btn.active{
    background-color: #329df9!important;
    color: white;
    font-weight: 1000;
}

thead th {
    text-align: left;
}

span.standard{
    background: #329df9;
    padding: 5px 10px;
    border-radius: 20px;
    color: white;
    font-size: 11px;
}
span.superdeluxe{
    background: #f19307;
    padding: 5px 10px;
    border-radius: 20px;
    color: white;
    font-size: 11px;
}

span.deluxe{
    background: #7a9a17;
    padding: 5px 10px;
    border-radius: 20px;
    color: white;
    font-size: 11px;
}

tr.recent{

}
tr.expired{

}

tr.activated{
    background: #d9f3e6!important;
}
tr.deactivated{
    background: #f5dfdf!important;
}

table.dataTable.display tbody tr.even>.sorting_1, table.dataTable.order-column.stripe tbody tr.even>.sorting_1 {
    background-color: rgba(250, 250, 250, 0.5);
}
table.dataTable.display tbody tr.odd>.sorting_1, table.dataTable.order-column.stripe tbody tr.odd>.sorting_1{
    background-color: rgba(241, 241, 241, 0.5);
}
</style>

<div class="fcard margin-fcard-1 pt-0 ps-5 clearfix">
    <div class="adupper-controls clearfix">
        <div class="fl">
            <h3>Milk Plants</h3>
        </div>
        <div class="fr">
            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            <a class="btn btn-primary" href="{{url('sa/addNewPlant')}}"> <i class="fa fa-plus-square"></i> Add New Plant </a>
        </div>
    </div>

    <div class="adtable-back">

        <div class="fl" style="margin:0 10px 10px 10px">
            <div class="light-color f-12">Total: {{count($dairies)}}</div>
        </div>

        <div class="fr" style="margin:0 10px 10px ">
                {{-- <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-filter"  aria-hidden="true"></i>
                        Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="btn-block dropdown-item" type="button">Due Payment</a>
                        <a class="btn-block dropdown-item" type="button">Newly Installed</a>
                        <a class="btn-block dropdown-item" type="button"></a>
                    </div>
                </div> --}}

                <a class="btn btn-default @if($filter=="all") active @endif" href="{{url('sa/milkPlants')}}?filter=all">All</a>
                <a class="btn btn-default @if($filter=="main") active @endif" href="{{url('sa/milkPlants')}}?filter=main">Main Plants</a>
                {{-- <label>Select Milk Plant</label> --}}
                <select name="mainPlant" id="mainPlantFilter" class="selectpicker @if($filter=="mainPlant") active @endif"
                     onchange="window.location = '{{url('sa/milkPlants')}}?filter=mainPlant&filtermainPlantId='+this.value;" 
                            data-live-search="true" title="filter by main plant">
                    @foreach ($mainPlants as $m)
                        <option value="{{$m->id}}" @if($filtermainPlantId == $m->id) selected @endif>{{$m->plantName}}</option>
                    @endforeach
                </select>
        </div>

        <table id="dairiesTable" class="display table-bordered table-stripped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Main Plant</th>
                    <th>Plant Code</th>
                    <th>Plant Name</th>
                    <th>Plant Contact/Address</th>
                    <th>Plant Head</th>
                    <th>Plant Head Contact</th>
                    <th>Dairies Registered</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($plants as $p)
                    @php 
                        $dairyReg = DB::table('plantdairymap')->where(["plantId" => $p->id])->count();
                        if($p->parentPlantId){
                            $parent = DB::table('milk_plants')->where(["id" => $p->parentPlantId])->get()->first();
                            $parentName = $parent->plantName;
                        }else{
                            $parentName = null;
                        }
                    @endphp
                    <tr class="trP{{$p->id}}">
                        <td>{{$parentName}}</td>
                        <td>{{$p->plantCode}}</td>
                        <td>{{$p->plantName}}</td>
                        <td>{{$p->contactNumber.", ".$p->address.", ".$p->city. ", ".$p->state}}</td>
                        <td>{{$p->headName}}</td>
                        <td>{{$p->mobile.", ".$p->email}}</td>
                        <td>{{$dairyReg}}</td>
                        <td class="p-0">
                            <a href="#" onclick="deleteMilkPlant({{$p->id}});" class="btn btn-default btn-sm danger-text"> <i class="fa fa-trash-o"></i> </a>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

</div>

<script>
    $("#dairiesTable").dataTable({        
        bPaginate : true,
        bFilter : false,
        info: false,
    });


    function deleteMilkPlant(id){
        $.confirm({
            icon: 'fa fa-trash-o',
			title: 'Delete Milk Plant',
			content: 'Are you sure to delete this milkPlant?',
			type: 'red',
			typeAnimated: true,
			buttons: {
				delete: {
					text: 'Delete',
					btnClass: 'btn-red',
					action: function(){
                        deletePlant(id, "delete");
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

    function deletePlant(id, action){
        loader("show");
        $.ajax({
            type:"POST",
            url:'{{url("sa/milkPlantDelete")}}',
            data: {
                plantId: id,
                action: action
            },
            success:function(res){
                if(res.error){
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 12000);
                }else{
                    $("tr.trP"+id).slideUp().remove();
                    $.alert(res.msg);
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