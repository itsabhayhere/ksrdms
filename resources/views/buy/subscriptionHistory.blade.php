
@extends('theme.default')

@section('content')

    <div class="fcard margin-fcard-1  pt-0 ps-10 clearfix">
        <div class="clearfix">

            <div class="col-md-6">

                <div class="pt-20">                   
                    <a class="btn btn-sm btn-primary" href="{{url('dairy-settings')}}"> <i class="fa fa-arrow-circle-left"></i> Return </a>
                </div>

                <div class="buy-s-plan">
                    Current Plan
                    <div class="s-p-name" id="buy-s-name">{{$d->name}}</div>
                    <span>{{ucfirst($d->planType)}}</span>
                    <br>
                </div>
                <div class="clearfix f-16">
                    <div class="fl ps-5">From: {{ date("d M Y", strtotime($d->dateOfSubscribe))}}</div>
                    <div class="fl ps-5">To: {{ date("d M Y", strtotime($d->expiryDate))}}</div>
                </div>
            </div>
            <div class="col-md-6 pt-50">
                <div class="text-center">
                    <a class="btn btn-primary" href="{{url('renewPage')}}">Extend Validity</a>
                </div>
            </div>

            <div class="clearfix mb-20"></div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date of payment</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $item)
                    <tr>
                        <td>{{date("d M Y", strtotime($item->trans_date))}}</td>
                        <td>{{ucfirst($item->name)}}</td>
                        <td>&#8377; {{$item->amount}}</td>
                        <td>{{$item->status_message}}</td>
                        <td><a href="{{asset($item->invoiceFile)}}" download><i class="fa fa-download"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <script>
    
    function asdkfhsdaf(){
    
    if($('#form-dairysetup')[0].checkValidity()){;}
    else{
            closePlanFormModel();
            curInputs = $("#form-dairysetup").find("input, select, textarea"),
            $(".form-group").removeClass("has-error");
            for(var i=0; i<curInputs.length; i++){
                if (!curInputs[i].validity.valid){
                    isValid = false;
                    $(curInputs[i]).closest(".form-group").addClass("has-error");
                }
            }
            return false;
        }

    loader("show");

    data = $("#form-dairysetup").serializeArray();
    console.log(data);

    $.ajax({
        type:"POST",
        url:"{{url('registerNewDairy')}}",
        data: data,
        success:function(res){
            if(!res.error){
                $(".flash-alert").removeClass("hide").addClass("alert-success"); $(".flash-alert .flash-msg").html(res.msg);
                setTimeout(function(){ window.location.assign("{{url('/')}}"); }, 4000);
                
            }else{
                alert(res.msg);
                loader("hide");
            }
        },
        error:function(res){
            loader("hide");
        }
    }).done(function(res){
        // loader("hide");
        console.log(res);
    });
}




    function openPlanFormModel(event){
        event.preventDefault();
        $("#buyModel").fadeIn();
    }

    function closePlanFormModel(){
        $("#buyModel").fadeOut();
    }

    function selectThisPlan(elm, pid){

        $(".selected-plan").removeClass("selected-plan");
        $(elm).addClass("selected-plan");

        $("#pricePlanId").val(pid);
        $(".plans").slideUp();

            $.ajax({
                type:"POST",
                url:"{{url('getPricePlanDetails')}}",
                data: {planId: pid},
                success:function(res){  
                    if(res.error){
                        $.alert(res.msg);
                        closePlanFormModel();
                    }else{
                        setPlanData(res.plan);
                    }
                    console.log(res);
                },
                error: function(d){
                    console.log(d);
                }
            });

        $("#planSelectorMY").slideDown().show();

        console.log("asd");

    }

    function pricePlanBack(){
        $(".plans").slideDown();
        $("#planSelectorMY").slideUp();

    }

    function setPlanData(plan){
        $("#pricePlanId").val(plan.id);
        $("#buy-s-name").html(plan.name);
        $("#buy-sm-price").html(plan.monthlyPrice);
        $("#buy-sy-price").html(plan.yearlyPrice);
    }

    
    function getCity(e, cityid){
        var stateID = $(e).val();    
        
        if(stateID){
            $.ajax({
                type:"GET",
                url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
                success:function(res){               
                if(res){
                    $("#"+cityid).empty();
                    $.each(res,function(key,value){
                        $("#"+cityid).append('<option value="'+key+'">'+value['name']+'</option>');
                    });
                    // $("#"+cityid).selectpicker("refresh");
                }else{
                    $("#"+cityid).empty();
                }
                }
            });
        }else{
            $("#cityid"+cityid).empty();
        }
    }
</script>

@endsection