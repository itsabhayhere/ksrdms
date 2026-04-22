@extends('spradmin.layout') 
@section("content")

<style>
.small-black-585{
    font-size: 14px;
    font-weight: 700;
    letter-spacing: initial;
}
.s-p-price{
    margin-bottom: 20px;
}
.m-18-12{
    margin: 18px 12px !important;
}
</style>

  <title>CCAvenue Payment</title>
<script>
	window.onload = function() {
		var d = new Date().getTime();
		document.getElementById("tid").value = d;
	};
</script>


<div class="plans clearfix">
    @foreach($pp as $d)
        <div class="col-md-3 p-0-5">
            <div class="s-plan">

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

                <div class="s-p-buy pt-20">
                    <a href="#" class="btn btn-primary buy-btn" role="button" onclick="openPlanFormModel({{$d->id}})"> Get Trial Now </a>
                </div>

            </div>
        </div>
    @endforeach;
</div>
    



<div class="wmodel clearfix" id="buyModel" style="width:100%;height:100%;max-height:100%">
    <div class="close" onclick="closePlanFormModel()">X</div>
    <div class="wmodel-body">
        <div class="container">

            <form name="form-dairysetup" action="{{url('sa/saveDairyAndPay2')}}" method="post">
            <div class="col-sm-7">
                <h3>Enter Your Details</h3>
                <hr>
                    <input type="hidden" name="tid" id="tid" readonly />
                    <input type="hidden" name="merchant_id" value="196388"/>
                    <input type="hidden" name="order_id" value="{{time()}}"/>
                    
                    <input type="hidden" name="currency" value="INR"/>
                    <input type="hidden" name="redirect_url" value="{{url('sa/ccavResponseHandler')}}"/>
                    <input type="hidden" name="cancel_url" value="{{url('sa/ccavResponseHandler')}}"/>
                    <input type="hidden" name="language" value="EN"/>
            
                    <input type="hidden" name="createBySuperAdmin" value="0"/>
                    <input type="hidden" name="pricePlanId" value="" id="pricePlanId">
                    <input type="hidden" name="amount" id="amount" value="0"/>

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
                            <input type="number" class="form-control" name="mobile" id="dairyContact" required>
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
                            <select name="owstate" class="selectpicker" id="pstate"  title="Select State" onchange="getCity(this, 'pdistrict')" required data-live-search="true">
                                @foreach ($states as $allStates)
                                    <option value=" {{$allStates->id}} ">{{$allStates->name}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">District</label>
                            <select name="owdistrict" class="selectpicker" id="pdistrict"  title="Select District" required data-live-search="true">
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
                        <button type="submit" id="buy-btn" value="buy" name="submit" class="btn btn-default fr">Buy Now</button>
                    </div>
                    <div class="col-sm-12">
                        <button type="submit" id="trial-btn" value="try" name="submit" class="btn btn-default fr">Try for free</button>
                    </div>

                    {{-- <div class="col-sm-12">
                        <button type="submit" id="getdashboardbtn" class="btn btn-default fr">Get Your Dairy Dashboard</button>
                    </div> --}}
            </div>

            <div class="col-md-5">
                <div class="pt-100"></div>
                <div class="buy-s-plan">
                    Selected Plan
                    <div class="s-p-name" id="buy-s-name"></div>
                    <br>
                    <br>
                    Select Price
                    <div>
                        <br>
                        <label for="monthlyPrice" id="monthlyPriceLbl">
                            <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="monthlyPrice" value="monthly" required>
                            <div class="s-p-price fl" >
                                <span class="rupee-symb">&#8377;</span>
                                <span class="" style="margin-left: 15px;" id="buy-sm-price">
                                </span>
                                <span class="small-black-585">per Month </span>
                            </div>
                        </label>
                    </div>
                    <div>
                        <label for="yearlyPrice" id="yearlyPriceLbl">
                            <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="yearlyPrice" value="yearly" required>
                            <div class="s-p-price fl">
                                <span class="rupee-symb">&#8377;</span>
                                <span class="" style="margin-left: 15px;" id="buy-sy-price">
                                </span>
                                <span class="small-black-585">per Year </span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

        </form>
            
        </div>
    </div>
</div>


<script>

    var plan = null;

    function openPlanFormModel(id){
        $("#buyModel").fadeIn();

            $.ajax({
                type:"POST",
                url:"{{url('getPricePlanDetails')}}",
                data: {planId: id},
                success:function(res){  
                    if(res.error){
                        $.alert(res.msg);
                        closePlanFormModel();
                    }else{
                        plan = res.plan;
                        setPlanData(res.plan);
                    }
                    console.log(res);
                },
                error: function(d){
                    console.log(d);
                }
            });
    }

    function closePlanFormModel(){
        $("#buyModel").fadeOut();
    }

    function setPlanData(plan){
        $("#pricePlanId").val(plan.id);
        $("#buy-s-name").html(plan.name);
        $("#buy-sm-price").html(plan.monthlyPrice);
        $("#buy-sy-price").html(plan.yearlyPrice);
    }

    $("#monthlyPriceLbl").on('click', function(){
        if(plan!=null){
            $("#amount").val(plan.monthlyPrice)
        }
    })
    $("#yearlyPriceLbl").on('click', function(){
        if(plan!=null){
            $("#amount").val(plan.yearlyPrice)
        }
    })

    
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
                    $("#"+cityid).selectpicker("refresh");
                }else{
                    $("#"+cityid).empty();
                }
                }
            });
        }else{
            $("#cityid"+cityid).empty();
        }
    }


    $("#getdashboardbtn").on("click", function(event){
        event.preventDefault();

        // loader("show");

        data = $("#form-dairysetup").serializeArray();
        console.log(data);

        $.ajax({
            type:"POST",
            url:"{{url('registerNewDairy')}}",
            data: data,
            success:function(res){
                console.log(res);
                if(!res.error){
                    // $(".flash-alert").removeClass("hide").addClass("alert-success"); $(".flash-alert .flash-msg").html(res.msg);
                    // window.location.assign("{{url('sa/dairyList')}}");
                }else{
                    // $.alert(res.msg);
                }
            },
            error:function(res){
            }
        }).done(function(res){
            // loader("hide");
            console.log(res);
        });
    });

</script>

@endsection