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
            <h3>Your Dairies</h3>
        </div>
        <div class="fr">
            <a href="#" class="info-text" data-toggle="tooltip" data-placement="left" title="Use Shift button and mouse wheel to scroll the table in horizontally."><i class="glyphicon glyphicon-info-sign"></i></a>
            <a class="btn btn-primary" href="{{url('sa/dairysetupwizard')}}"> <i class="fa fa-plus-square"></i> Setup New Dairy </a>
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

                <a class="btn btn-default @if($filter=="all") active @endif" href="{{url('sa/dairyList')}}?filter=all">All</a>
                <a class="btn btn-default @if($filter=="recent") active @endif" href="{{url('sa/dairyList')}}?filter=recent">Recently installed</a>
                <a class="btn btn-default @if($filter=="duePayment") active @endif" href="{{url('sa/dairyList')}}?filter=duePayment">Due Payment</a>
                <a class="btn btn-default @if($filter=="trial") active @endif" href="{{url('sa/dairyList')}}?filter=trial">On Trial</a>
                <a class="btn btn-default @if($filter=="expired") active @endif" href="{{url('sa/dairyList')}}?filter=expired">Licence Expired</a>
                <a class="btn btn-default @if($filter=="deactivated") active @endif" href="{{url('sa/dairyList')}}?filter=deactivated">Deactivated</a>
        </div>

        <table id="dairiesTable" class="display table-bordered table-stripped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Society Code</th>
                    <th>Society Name</th>
                    <th>Contact</th>
                    <th>Propritor Name</th>
                    <th>Registerd Time</th>
                    <th>Total Members</th>
                    <th>SMS Account</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($dairies as $d)
                    @php 
                        $amem = DB::table("member_personal_info")->where(["dairyId" => $d->id, "status" => "true"])->count("id");
                        $class="";
                        if(strtotime($d->dateOfSubscribe) < strtotime("-1 month")){
                            $class.=" recent";
                        }
                        // $subscibetime = time() - strtotime($d->dateOfSubscribe);
                        if(!$d->isPaymentDone && strtotime($d->trialEndDate) < time()){
                            $class.=" expired";
                        }
                        if($d->isActivated){
                            $class.=" activated";
                        }else{
                            $class.=" deactivated";
                        }
                    @endphp

                <tr class="{{$class}}">
                    <td> <span class="{{str_replace(" ", "", strtolower($d->planName))}}">{{ $d->planName}}</span></td>
                    <td>{{ $d->society_code}}</td>
                    <td>{{ $d->dairyName}}</td>
                    <td>{{ $d->owmobile . ", " . $d->owemail}}</td>
                    <td>{{ $d->owname}}</td>
                    <td>{{ date("d-m-Y", strtotime($d->dateOfSubscribe))}}</td>
                    <td>{{ $amem}}</td>
                    <td>{{ $d->remainingSms}}</td>

                    <td class="p-0">
                        &nbsp;
                        @if($d->isActivated)
                            <a href="javascript:void(0);" onclick="askToDeactivate('{{$d->id}}')" class="btn btn-default btn-sm"
                                data-toggle="tooltip" data-placement="top" title="Deactivate dairy"> 
                                <i class="fa fa-power-off"></i>
                            </a>
                        @else
                            <a href="javascript:void(0);" onclick="askToActivate('{{$d->id}}')" class="btn btn-default btn-sm"
                                data-toggle="tooltip" data-placement="top" title="Activate dairy"> 
                                <i class="fa fa-toggle-right"></i>
                            </a>
                        @endif
                        &nbsp;&nbsp;
                        <a href="#" onclick="deleteSupplier({{ $d->id}});" class="btn btn-default btn-sm danger-text"> <i class="fa fa-trash-o"></i> </a>
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


    function askToDeactivate(id){
        $.confirm({
            icon: 'fa fa-power-off',
			title: 'Deactivate Dairy',
			content: 'Are you sure to deactivate this dairy?',
			type: 'red',
			typeAnimated: true,
			buttons: {
				deactivate: {
					text: 'Deactivate',
					btnClass: 'btn-red',
					action: function(){
                        dairyDeactivateActivate(id, "deactivate");
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
    function askToActivate(id){
        $.confirm({
            icon: 'fa fa-power-off',
			title: 'Activate Dairy',
			content: 'Are you sure to activate this dairy?',
			type: 'red',
			typeAnimated: true,
			buttons: {
				deactivate: {
					text: 'Activate',
					btnClass: 'btn-red',
					action: function(){
                        dairyDeactivateActivate(id, "activate");
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

    function dairyDeactivateActivate(id, action){
        loader("show");
        $.ajax({
            type:"POST",
            url:'{{url("sa/deactivateDairy")}}',
            data: {
                dairyId: id,
                action: action
            },
            success:function(res){
                if(res.error){
                    $(".flash-alert .flash-msg").html(res.msg);
                    $(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-danger");
                    setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);
                }else{
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