@extends('layouts.app') 
@section('content') {{--

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> --}}
{{-- <link rel="stylesheet" href="{!! asset('css/bootstrap-select.css') !!}"> --}}
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> --}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script> --}}
{{-- <script src="{!! asset('js/bootstrap-select.js') !!}"></script> --}}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>

<style>
    .m-18-12 {
        margin: 18px 12px!important;
    }

    .carousel-indicators li {
        width: 30px;
        height: 5px;
        margin: 3px;
        box-shadow: 0px 1px 6px rgba(0, 0, 0, 0.5);
    }

    .carousel-indicators .active {
        width: 30px;
        height: 8px;
        margin: 1px;
    }

    .carousel-caption {
        padding-bottom: 0;
    }

    .carousel-control-next:hover,
    .carousel-control-prev:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    @media screen and (min-width: 768px) {
        .carousel-indicators {
            bottom: 0px;
        }
        .media-margin-200 {
            margin-top: 200px
        }
    }

    select.form-control:not([size]):not([multiple]) {
        height: calc(2.25rem + 8px);
    }

    .has-error{
        border: none;
    }
</style>

<div class="">

    <div class="fcard margin-fcard-1 pt-0 ps-5 clearfix">

        <form name="form-dairysetup" action="" method="post" id="form-dairysetup">

            <div class="col-md-5 fr media-margin-200">
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <a href="{{asset('images/slides/dashboard.png')}}" data-toggle="lightbox">
                                <img class="d-block w-100" src="{{asset('images/slides/dashboard.png')}}" alt="First slide">
                            </a>
                            <div class="carousel-caption d-none d-md-block">
                                <h4>Dashboard</h4>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <a href="{{asset('images/slides/ratecard.png')}}" data-toggle="lightbox">
                                <img class="d-block w-100" src="{{asset('images/slides/ratecard.png')}}" alt="Second slide">
                            </a>
                            <div class="carousel-caption d-none d-md-block">
                                <h4>Ratecard Management</h4>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <a href="{{asset('images/slides/dailyTransaction.png')}}" data-toggle="lightbox">
                                <img class="d-block w-100" src="{{asset('images/slides/dailyTransaction.png')}}" alt="Third slide">
                            </a>

                            <div class="carousel-caption d-none d-md-block">
                                <h4>Daily Milk Collection</h4>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

                <br><br>
            </div>

            <div class="col-sm-7">
                <h3>Enter Your Details</h3>
                <hr>
                <input type="hidden" name="tid" id="tid" readonly />
                <input type="hidden" name="merchant_id" value="196388" />

                <input type="hidden" name="currency" value="INR" />
                <input type="hidden" name="redirect_url" value="{{url('ccavResponseHandler')}}" />
                <input type="hidden" name="cancel_url" value="{{url('ccavResponseHandler')}}" />
                <input type="hidden" name="language" value="EN" />

                <input type="hidden" name="createBySuperAdmin" value="0" />
                <input type="hidden" name="pricePlanId" value="" id="pricePlanId">

                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label" for="name">Dairy Name:</label>
                        <input type="text" class="form-control" name="name" id="dairyName" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="dairyCode">Dairy Code:</label>
                        <input type="text" class="form-control" name="code" id="dairyCode" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="dairyContact">Dairy Contact:</label>
                        <input type="number" class="form-control" name="mobile" id="dairyContact" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="owName">Propritor Name:</label>
                        <input type="text" class="form-control" name="owname" id="owName" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="owMobile">Propritor Mobile No.:</label>
                        <input type="text" class="form-control" name="owmobile" id="owMobile" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="owEmail">Propritor Email:</label>
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
                        <select name="owstate" class=" form-control" id="pstate" title="Select State" onchange="getCity(this, 'pdistrict')" required
                            data-live-search="true">
                            @foreach ($states as $allStates)
                                <option value=" {{$allStates->id}} ">{{$allStates->name}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" class="control-label">District</label>
                        <select name="owdistrict" class="form-control" id="pdistrict" title="Select District"  data-live-search="true">
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
                            <div class="col-md-4 p-0-5 ">
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
                                        @if($d->noOfMem
                                        < 5000) {{ " Member Limit ".$d->noOfMem}} @else Member: Unlimited @endif
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
                                <br> Select Price
                                <div>
                                    <br>
                                    <label class="control-label" for="monthlyPrice">
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
                                    <label class="control-label" for="yearlyPrice">
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
                                {{-- <input type="hidden" name="submit" value="trial" id="submitfield"> --}}
                                <a href="javascript:void(0)" class="btn btn-default sahdfoaysodf" id="sahdfoaysodf" role="button" onclick="asdkfhsdaf('trial')"> Get Dashboard Now</a>                                {{-- &nbsp; &nbsp; &nbsp;
                                <a href="javascript:void(0)" class="btn btn-primary sahdfoaysodf123" id="sahdfoaysodf123" role="button" onclick="asdkfhsdaf('buy')"> Buy Now</a>                                --}}
                            </div>


                        </div>

                    </div>

                </div>
            </div>
    </div>


    </form>
</div>

</div>
<br>










{{--
<div class="container mb-5 mt-5">
    <div class="pricing card-deck flex-column flex-md-row mb-3">

        @foreach($pp as $d)

        <div class="card card-pricing shadow text-center px-3 mb-4">
            <span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">
                {{strtoupper($d->name)}}
            </span>
            <div class="bg-transparent card-header pt-4 border-0">
                <h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="15">
                    &#8377;<span class="price">{{$d->monthlyPrice}}</span>
                    <span class="h6 text-muted ml-2">/ per month</span>
                </h1>
            </div>
            <div class="card-body pt-0">
                <ul class="list-unstyled mb-4">
                    <li>
                        @if($d->noOfMem
                        < 5000) {{ " Member Limit ".$d->noOfMem}} @else Member: Unlimited @endif
                    </li>
                    <li>
                        {{" SMS Limit ".$d->noOfSms}}
                    </li>
                    <li>
                        {{$d->trial_time}} Days Free Trial
                    </li>
                </ul>
                <button type="button" class="btn btn-outline-primary mb-3" onclick="openPlanFormModel({{$d->id}})">Order now</button>
            </div>
        </div>

        @endforeach
    </div>
</div> --}}





<script>
    // $("#form-dairysetup").submit(function (e){
//     e.preventDefault(); 
//     if($('#dailyForm')[0].checkValidity()){;}else{return false;}
   
// })

function asdkfhsdaf(action){
    
    // if(action=='buy'){
    //     $("#submitfield").val('buy');
    // }else{
    //     $("#submitfield").val('trial');
    // }

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
        var stateID = $(e).val().trim();    
        
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


    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox();
    });
</script>
@endsection