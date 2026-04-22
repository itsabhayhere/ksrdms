
@extends('layouts.app')

@section('content')

    <div class="container p-0">
        <div class="fcard margin-fcard-1 m-0 pt-0 ps-10 clearfix">
            <div class="clearfix">
                <h1>You subscription plan is expired.</h1>
                                            
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
                                <div class="mb-10"></div>
                                <div class="text-center">
                                    <a class="btn btn-primary" href="{{url('renewPage')}}">Renew Now</a>
                                </div>
            </div>

            {{-- <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
            
                <form name="form-dairysetup" action="" method="post" id="form-dairysetup">

                        <div class="col-sm-12">
                            
                            <hr>
                            <input type="hidden" name="tid" id="tid" readonly />
                            <input type="hidden" name="merchant_id" value="196388"/>
                            <input type="hidden" name="order_id" value="123654789"/>
                            
                            <input type="hidden" name="currency" value="INR"/>
                            <input type="hidden" name="redirect_url" value="{{url('sa/ccavResponseHandler')}}"/>
                            <input type="hidden" name="cancel_url" value="{{url('sa/ccavResponseHandler')}}"/>
                            <input type="hidden" name="language" value="EN"/>
            
                            <input type="hidden" name="createBySuperAdmin" value="0"/>
                            <input type="hidden" name="pricePlanId" value="" id="pricePlanId">
            
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name">Dairy Name:</label>
                                    <input type="text" class="form-control" name="name" id="dairyName" required>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dairyCode">Dairy Code:</label>
                                    <input type="text" class="form-control" name="code" id="dairyCode" required>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="dairyContact">Dairy Contact:</label>
                                    <input type="number" class="form-control" name="mobile" id="dairyContact" required >
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="owName">Propritor Name:</label>
                                    <input type="text" class="form-control" name="owname" id="owName" required>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="owMobile">Propritor Mobile No.:</label>
                                    <input type="text" class="form-control" name="owmobile" id="owMobile" required>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="owEmail">Propritor Email:</label>
                                    <input type="text" class="form-control" name="owemail" id="owEmail" required>
                                </div>
                            </div>
            
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Address</label>
                                    <textarea maxlength="200" required="required" name="owaddress" class="form-control" id="paddress" placeholder="Address"></textarea>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">State</label>
                                    <select name="owstate" class="form-control" id="pstate"  title="Select State" onchange="getCity(this, 'pdistrict')" required data-live-search="true">
                                        @foreach ($states as $allStates)
                                            <option value=" {{$allStates->id}} ">{{$allStates->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">District</label>
                                    <select name="owdistrict" class="form-control" id="pdistrict"  title="Select District" required data-live-search="true">
                                    </select>
                                </div>
                            </div>
            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Pin Code</label>
                                    <input maxlength="200" required="required" name="owpin" class="form-control" placeholder="Pin code" id="ppinCode" />
                                </div>
                            </div>
            
                            <div class="clearfix mb-20"></div>
            
                            <div class="col-sm-12">
                                <a type="button" id="getdashboardbtn" class="btn btn-default fr" onclick="openPlanFormModel(event)">Get Your Dairy Dashboard</a>
                            </div>
                        </div>
            
            
            
                        <div class="wmodel clearfix" id="buyModel" style="width:90%;height:90%;max-height:100%">
                                <div class="close" onclick="closePlanFormModel()">X</div>
                                <div class="wmodel-body">
                                    <div class="container">
                                        
                                        <h1 class="text-center">Select Plans</h1>
            
                                        <div class="plans clearfix">
                                                @foreach($pp as $d)
                                                    <div class="col-md-4 p-0-5 " >
                                                        <div class="s-plan" data-planId="{{$d->id}}" onclick="selectThisPlan(this, {{$d->id}})">
                                            
                                                            <div class="s-p-name">
                                                                {{strtoupper($d->name)}}
                                                            </div>
                                                            
                                                            <br>
                                                            <br>
                                                            <div class="s-p-price">
                                                                <span class="rupee-symb">&#8377;</span>
                                                                <span class="" style="margin-left: 15px;">{{$d->monthlyPrice}}</span>
                                                            </div>
                                                            per Month
                                            
                                                            <br>
                                                            <br>
                                                            <br>
                                                            <div class="s-p-price">
                                                                <span class="rupee-symb">&#8377;</span>
                                                                <span class="" style="margin-left: 15px;">{{$d->yearlyPrice}}</span>
                                                            </div>
                                                            per Year
                                                            
                                                            <br>
                                                            <br>
                                                            <div class="s-p-rate">
                                                                @if($d->noOfMem < 5000)
                                                                    {{" Member Limit ".$d->noOfMem}}
                                                                @else
                                                                    Member: Unlimited
                                                                @endif
                                                            </div>
                                                            <div class="s-p-rate">
                                                                {{" SMS Limit ".$d->noOfSms}}
                                                            </div>
                                                            <div class="s-p-trial">
                                                                {{$d->trial_time}} Days Free Trial
                                                            </div>
                                            
                                                            <div class="s-p-buy pt-20 ">
                                                                <a href="#" class="btn btn-primary buy-btn" role="button"> Get Trial Now </a>
                                                            </div>
                                            
                                                        </div>
                                                    </div>
                                                @endforeach;
                                            </div>
                                                    
                                                
                                            <div id="planSelectorMY" class="dnone">
            
                                                <a href="javascript:void(0);" onclick="pricePlanBack()" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
            
                                                <div class="buy-s-plan">
                                                    Selected Plan
                                                    <div class="s-p-name" id="buy-s-name"></div>
                                                    <br>
                                                    Select Price
                                                    <div>
                                                        <br>
                                                        <label for="monthlyPrice" >
                                                            <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="monthlyPrice" value="monthly" required>
                                                            <div class="s-p-price fl" style="line-height: 18px;">
                                                                <span class="rupee-symb">&#8377;</span>
                                                                <span class="" style="margin-left: 15px;" id="buy-sm-price">
                                                                </span>
                                                                <span class="small-black-585">per Month </span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    <br>
                                                    <div>
                                                        <label for="yearlyPrice" >
                                                            <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="yearlyPrice" value="yearly" required>
                                                            <div class="s-p-price fl" style="line-height: 18px;">
                                                                <span class="rupee-symb">&#8377;</span>
                                                                <span class="" style="margin-left: 15px;" id="buy-sy-price">
                                                                </span>
                                                                <span class="small-black-585">per Year </span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
            
                                                <div class="text-center">
                                                    <a href="javascript:void(0)" class="btn btn-primary sahdfoaysodf" id="sahdfoaysodf" role="button" onclick="asdkfhsdaf()"> Get Dashboard </a>
                                                </div>
            
                                            </div>
                        
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>

                    </form>
                
                </div> --}}

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