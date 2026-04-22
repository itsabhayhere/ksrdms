@extends('spradmin.layout') 
@section("content")

<script type="text/javascript">
    $(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn'),
            allPrevBtn = $('.prevBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary').removeClass("btn-default");
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){

        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input, select, textarea"),
            isValid = true;
            
        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid) {
            nextStepWizard.removeAttr('disabled').trigger('click');
        }
    });

    allPrevBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

        $(".form-group").removeClass("has-error");
        prevStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');
});


</script>

<!--<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
<!------ Include the above in your HEAD tag ---------->

<div class="fcard margin-fcard-1 pt-0 clearfix">
    <div class="adupper-controls">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                    <p>Start</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                    <p>Dairy Information</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                    <p>Subscription Plan</p>
                </div>
                {{--
                <div class="stepwizard-step">
                    <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                    <p>Member Setup</p>
                </div> --}}
                <div class="stepwizard-step">
                    <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled" onclick="reviewForm()">4</a>
                    <p>Finish</p>
                </div>
            </div>
        </div>
    </div>

    <form role="form" id="form-dairysetup" >
        <input type="hidden" name="createBySuperAdmin" value="1">

        <div class="row setup-content" id="step-1">
            <div class="col-xs-12">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-sm-12 ptb-100">
                                <h1 align="center">Welcome to the DMS Wizard................</h1>
                            </div>

                        </div>
                    </div>

                    <button class="btn btn-primary nextBtn btn-lg pull-right" data-no="1" type="button">Next</button>
                </div>
            </div>
        </div>
        <div class="row setup-content" id="step-2">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 col-lg-offset-2">
                <div class="col-sm-12">
                    <div class="fl">
                        <h3>Dairy Information: </h3>
                        <hr class="m-0">
                    </div>
                    <div class="clearfix mb-10"></div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Dairy Name</label>
                        <input maxlength="200" type="text" required="required" name="name" class="form-control" placeholder="Enter Society Name" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Dairy Code</label>
                        <input maxlength="200" type="text" required="required" name="code" class="form-control" placeholder="Enter Society Code" />
                    </div>
                </div>
                    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Mobile No.</label>
                        <input maxlength="200" type="text" id="mobile" required="required" name="mobile" class="form-control" placeholder="Enter Mobile No."/>
                    </div>
                </div>
                

                <div class="col-sm-12" style="text-align:left; margin-top:20px;">
                    <div class="fl">
                        <h3>Dairy Propritor Details: </h3>
                        <hr class="m-0">
                    </div>
                    <div class="clearfix mb-10"></div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Name</label>
                        <input maxlength="200" required="required" name="owname" class="form-control" placeholder="Name" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Mobile No. <small>(will be Login ID)</small></label>
                        <input maxlength="200" required="required" name="owmobile" id="owmobile" class="form-control" placeholder="Mobile" />
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input maxlength="200" type="email" required="required" name="owemail" class="form-control" placeholder="Email" />
                    </div>
                </div>

                {{-- <div class="col-sm-12 pt-10 pb-20">
                    <label>
                        <input type="checkbox" style="width:30px !important;" id="sameAddress" name="sameaddress"><span>Same address as above</span>
                    </label>
                </div>

                <div class="col-sm-6 pt-10 pb-20 hide">
                    <div>
                        <input type="checkbox" style="width:30px !important;" name="anotheraddress"><span>another address</span>
                    </div>
                </div> --}}

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
            </div>

            <div class="col-xs-12">
                <button class="btn btn-default prevBtn btn-lg pull-left" type="button">Prev</button>
                <button class="btn btn-primary nextBtn btn-lg pull-right" data-no="2" type="button">Next</button>
            </div>
        </div>

        <div class="row setup-content" id="step-3">
            <div class="col-xs-12">
                <div class="col-md-12">

                    <h1 class="text-center"> Subscription Plans</h3>
                        <input type="hidden" name="pricePlanId" value="" id="pricePlanId">
                        @foreach ($subsPlans as $d)
                            <div class="col-md-4">
                                <div class="s-plan" id="s-plan{{$d->id}}">
                                    <div class="s-p-edit">
                                        <a href="#" class="btn btn-link" role="button" onclick="selectPricePlan('{{$d->id}}')"> Select </a>
                                    </div>
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
                                    </div>
                            </div>
                        @endforeach

                    <div class="clearfix"></div>
                    <div class="col-xs-12" style="padding:20px 0 0">
                        <button class="btn btn-default prevBtn btn-lg pull-left" type="button">Prev</button>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-no="3" type="button">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row setup-content" id="step-5">
            <div class="col-xs-12">
                <div class="col-md-7">
                    <div style="border:1px solid #eee;margin: 15px 0;">
                        {{-- <h3>Check Details</h3> --}}
                        <div style="padding:20px 0">
                            <div class="uioiu90 clearfix">
                                <div class="fl">Society Name: </div>
                                <div class="fl rvw-val rvw-name"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Society Code: </div>
                                <div class="fl rvw-val rvw-code"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Mobile Number: </div>
                                <div class="fl rvw-val rvw-mobile"></div>
                            </div>
                        </div>
                        <hr>
                        <div style="padding:20px 0">
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor Name: </div>
                                <div class="fl rvw-val rvw-propname"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor Mobile: </div>
                                <div class="fl rvw-val rvw-propmobile"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor Email: </div>
                                <div class="fl rvw-val rvw-propemail"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor Address: </div>
                                <div class="fl rvw-val rvw-propaddress"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor State: </div>
                                <div class="fl rvw-val rvw-propstate"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor District: </div>
                                <div class="fl rvw-val rvw-propdistrict"></div>
                            </div>
                            <div class="uioiu90 clearfix">
                                <div class="fl">Propritor Pin: </div>
                                <div class="fl rvw-val rvw-proppin"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="col-md-12">
                        <div class="s-plan" id="s-plan-selected">
                            <div class="s-p-name">
                            </div>
                            <div class="s-p-price">
                                <span class="rupee-symb">&#8377;</span>
                                <span class="priceM" style="margin-left: 15px;">0</span>
                            </div>
                            per Month
                            <div class="s-p-price">
                                <span class="rupee-symb">&#8377;</span>
                                <span class="priceY" style="margin-left: 15px;">0</span>
                            </div>                
                            per Year
                            <div class="s-p-rate">
                            </div>
                            <div class="s-p-trial">
                            </div>    
                        </div>

                        <label for="isPaymentDone" class="pt-50" style="font-size: 19px;font-weight: 100;line-height: 1.1;">
                            <input type="radio" name="priceMonthlyOrYearly" id="isPaymentDone" value="monthly" style="left:0; float: left;" checked required>
                            Monthly Plan
                        </label>

                        <label for="yearlyPlan" class="pt-50" style="font-size: 19px;font-weight: 100;line-height: 1.1;">
                            <input type="radio" name="priceMonthlyOrYearly" class="checkbox" id="yearlyPlan" value="yearly" style="left:0; float: left;" required>
                            Yearly Plan
                        </label>

                    </div>
                </div>
                <div class="col-xs-12" style="padding:20px 0 0">
                    <button class="btn btn-default prevBtn btn-lg pull-left buttontopriceplan" type="button">Prev</button>
                    <button class="btn btn-success btn-lg pull-right" type="button" id="dairySubmitBtn">Finish!</button>
                </div>
            </div>
        </div>

    </div>
</form>




<div class="row setup-content" id="step-4">
    <div class="col-xs-12">
        <div class="col-md-12">
            <h3> Step 4 </h3>


            <div class="col-md-12">
                <h1>Daily Setup Wizard > Member Setup > Personal Information</h1>

                <div class="w3-bar w3-black">
                    <button class="w3-bar-item w3-button tablink w3-red" onclick="openCity(event,'London')">Personal Information</button>
                    <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'Paris')">Bank Account Details</button>
                    <button class="w3-bar-item w3-button tablink" onclick="openCity(event,'Tokyo')">Milk Collection Details</button>
                </div>

                <div id="London" class="w3-container w3-border city">
                    <h2>Personal Information</h2>


                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Member Code</label>
                            <input maxlength="200" required="required" name="memberCode" class="form-control" placeholder="Member Code" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Registration Date</label>
                            <input maxlength="200" name="registrationDate" required="required" class="form-control" placeholder="Registration Date" />
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-labe">Name</label>
                            <input maxlength="200" name="memberName" required="required" class="form-control" placeholder="name" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Father_name</label>
                            <input maxlength="200" name="memberFather" required="required" class="form-control" placeholder="F_name" />
                        </div>
                    </div>


                    <div class="col-sm-12" style="margin-top:10px;">
                        <div class="col-sm-2">
                            Sex
                        </div>

                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Gender</label>

                                <select class="selectpicker" name="memberGender">
                                            <option>Gender</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                    </select>
                            </div>
                        </div>

                        <div class="col-sm-6">

                        </div>

                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input maxlength="200" name="memberEmail" required="required" class="form-control" placeholder="email" />
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Aadhaar No.</label>
                            <input maxlength="200" name="memberAadhar" required="required" class="form-control" placeholder="Aadhaar no." />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Mobile No.</label>
                            <input maxlength="200" name="memberMobile" required="required" class="form-control" placeholder="Mobile No." />
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Address</label>
                            <textarea maxlength="200" name="memberAddress" required="required" class="form-control" placeholder="Address"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Village</label>
                            <input maxlength="200" name="memberVillage" required="required" class="form-control" placeholder="village" />
                        </div>
                    </div>


                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">District</label>

                            <select style="width:100%;  height:47px;" class="selectpicker" name="memberDistrict">
                                    <option>District</option>
                                    <option>Jaipur</option>
                                    <option>Ajmer</option>
                                    <option>Bharatpur</option>
                                    <option>Kota</option>
                                </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">State</label>
                            <select style="width:100%;  height:47px;" class="selectpicker" name="memberState">
                                    <option>State</option>
                                    <option>Rajasthan</option>
                                    <option>Himachal</option>
                                    <option>Uttrakhand</option>
                                </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Pin Code</label>
                            <input maxlength="200" name="memberPin" required="required" class="form-control" placeholder="pin" />
                        </div>
                    </div>

                </div>


                <div id="Paris" class="w3-container w3-border city" style="display:none">
                    <h2>Bank Account Details</h2>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Bank_name</label>
                            <input maxlength="200" name="bankName" required="required" class="form-control" placeholder="bank name" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Account No.</label>
                            <input maxlength="200" name="accNo" required="required" class="form-control" placeholder="account no." />
                        </div>
                    </div>




                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">IFSC</label>
                            <input maxlength="200" name="ifsc" required="required" class="form-control" placeholder="IFSC" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Branch Code</label>
                            <input maxlength="200" name="bankBranch" required="required" class="form-control" placeholder="branch_code" />
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Account Holder Name</label>
                            <input maxlength="200" name="accHolder" required="required" class="form-control" placeholder="account_holder" />
                        </div>
                    </div>
                    <div class="col-sm-6">

                    </div>




                </div>



                <div id="Tokyo" class="w3-container w3-border city" style="display:none">
                    <h2>Milk Collection Details</h2>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">Type of Milk Supplied</label>

                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select required="required" class="selectpicker" name="typeOfMilk">
                                    <option>Type of Milk</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                        </div>
                    </div>


                    <div class="col-sm-2" style="">
                        <div class="form-group">
                            <label class="control-label">Collection Manager</label>

                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <select required="required" class="selectpicker" name="collectionManager">
                                    <option>Manager</option>
                                    <option>Rajesh</option>
                                    <option>Mohan</option>
                                </select>
                        </div>
                    </div>

                    <div class="col-sm-1">
                        <div class="form-group">
                            <input maxlength="200" name="" required="required" class="form-control" type="checkbox" />
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <button type="button" name="Add New" value="Add New">Add New</button>
                        </div>
                    </div>

                </div>

            </div>


            <button class="btn btn-default prevBtn btn-lg pull-left" type="button">Prev</button>
            <button class="btn btn-primary nextBtn btn-lg pull-right" type="button" id="dairy-create-btn">Create</button>

        </div>
    </div>
</div>


<script>

function selectPricePlan(id){
    $("#pricePlanId").val(id);
    $(".s-plan.selected-plan").removeClass("selected-plan");
    $("#s-plan"+id).addClass("selected-plan");
}

var planreq = false;

function reviewForm(){
    if(planreq){return;}
    planreq = true;

    priceId = $("#pricePlanId").val();
    if(priceId == (""||null)){
        $.alert("Please select an subscription plan first.");
        $(".buttontopriceplan").trigger('click');
    }

    loader("show");

    $(".rvw-name").html($("#form-dairysetup [name=name]").val());
    $(".rvw-code").html($("#form-dairysetup [name=code]").val());
    $(".rvw-mobile").html($("#form-dairysetup [name=mobile]").val());
    $(".rvw-propname").html($("#form-dairysetup [name=owname]").val());
    $(".rvw-propmobile").html($("#form-dairysetup [name=owmobile]").val());
    $(".rvw-propemail").html($("#form-dairysetup [name=owemail]").val());
    $(".rvw-propaddress").html($("#form-dairysetup [name=owaddress]").val());
    $(".rvw-propstate").html($("#form-dairysetup [name=owstate]").find(":selected").text());
    $(".rvw-propdistrict").html($("#form-dairysetup [name=owdistrict]").find(":selected").text());
    $(".rvw-proppin").html($("#form-dairysetup [name=owpin]").val());

    $.ajax({
        type:"POST",
        url:"{{url('sa/getPricePlanDetails')}}",
        data: {planId: priceId},
        success:function(res){
            console.log(res);
            if(!res.error){
                $("#s-plan-selected .s-p-name").html(res.plan.name);
                $("#s-plan-selected .priceM").html(res.plan.monthlyPrice);
                $("#s-plan-selected .priceY").html(res.plan.yearlyPrice);
            }else{
                $.alert(res.msg);
                $(".buttontopriceplan").trigger('click');
            }
            planreq = false;
            loader("hide");
        },
        error:function(res){
            planreq = false;
            loader("hide");
            console.log(res);
        }
    });
}

    function openCity(evt, cityName) {
            var i, x, tablinks;
            x = document.getElementsByClassName("city");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < x.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " w3-red";
        }

$("#dairy-create-btn").click(function(){
    console.log("sidrf");
    console.log($("#form-dairysetup").serializeArray());
})

// $("#sameAddress").on('click', function(){
//     if($("#sameAddress").is(":checked")){
//         $("#paddress").html($("#daddress").val());
//         $("#pstate").val($("#dstate").val());
        
//         var $options = $("#ddistrict > option").clone();
//         $('#pdistrict').append($options);

//         $("#pdistrict").val($("#ddistrict").val());
//         $("#ppinCode").val($("#dpinCode").val());
//         $('.selectpicker').selectpicker('refresh');
//     }else{
//         $("#paddress").html("");
//         $("#pstate").val("");
//         $("#pdistrict").val("");
//         $("#ppinCode").val("");
//         $('.selectpicker').selectpicker('refresh');
//     }
// })

// $("#eveningShiftFrom, #eveningShiftTo, #morningShiftFrom, #morningShiftTo").datetimepicker({
//     format: 'LT',
//     stepping: 30
// });

$("#mobile").on("focusout", function(){
    $("#owmobile").val($(this).val());
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

    $("#dairySubmitBtn").on("click", function(event){
        event.preventDefault();

        loader("show");

        data = $("#form-dairysetup").serializeArray();
        console.log(data);

            $.ajax({
                type:"POST",
                url:"{{url('sa/addDairyAdminSubmit')}}",
                data: data,
                success:function(res){
                    console.log(res);
                    if(!res.error){
                        $(".flash-alert").removeClass("hide").addClass("alert-success"); $(".flash-alert .flash-msg").html(res.msg);
                        window.location.assign("{{url('sa/dairyList')}}");
                    }else{
                        $.alert(res.msg);
                    }
                    loader("hide");
                },
                error:function(res){
                    loader("hide");
                    console.log(res);
                }
            });
    })
</script>
@endsection