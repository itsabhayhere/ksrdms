@extends('layouts.app')

@section('content')

<div class="container p-0">
   <div class="fcard margin-fcard-1 m-0 pt-0 ps-10 clearfix">
      <div class="clearfix text-center">
         <h1>Renew DMS</h1>
      </div>
      <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12">
         <form name="form-dairysetup" action="{{url('proceedToCheckOut')}}" method="post" id="form-dairysetup">
            <div class="col-sm-12">
               <hr>
               <input type="hidden" name="tid" id="tid" readonly />

               <input type="hidden" name="merchant_id" value="196388"/>

               <input type="hidden" name="currency" value="INR"/>

               <input type="hidden" name="redirect_url" value="{{url('ccavResponseHandler')}}"/>

               <input type="hidden" name="cancel_url" value="{{url('ccavResponseHandler')}}"/>

               <input type="hidden" name="language" value="EN"/>

               <input type="hidden" name="amount" id="amount" value="0">

               <input type="hidden" name="order_id" id="order_id" value="{{time().$dairy->id}}">

               <input type="hidden" name="pricePlanId" value="" id="pricePlanId">

               <div class="col-sm-6">

                  <div class="form-group">

                     <label for="name">Dairy Name:</label>

                     <span style="font-size:1.2em">{{$dairy->dairyName}}</span>

                  </div>

               </div>

               <div class="col-sm-6">

                  <div class="form-group">

                     <label for="dairyCode">Dairy Code:</label>

                     <span style="font-size:1.2em">{{$dairy->society_code}}</span>

                  </div>

               </div>

               <div class="mb-20"></div>

               <div class="plans clearfix ">

                  <h3 class="text-center">Select Plans</h3>
                  {{-- <p class="text-center text-dark mt-2">
    After completing the payment, please call the admin at <strong>+91-9991707888</strong> for activation.
</p> --}}
<p class="text-center text-dark mt-2 p-3" style="background-color: #ffa200; border: 1px solid #ffeeba; border-radius: 5px;font-size: 23px;">
    After completing the payment, please call the admin at <strong>+91-9991707888</strong> for activation.
</p>




                  @foreach($pp as $d)

                  <div class="col-md-6 p-0-5">

                     <div class="s-plan" data-planId="{{$d->id}}" onclick1="selectThisPlan(this, {{$d->id}})">

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
                  @endforeach

                  
                  <div class="col-md-6 p-0-5">
            <img src="{{ url('images/paymentq.jpg') }}" class="img-fluid" style="width: 352px;">
                  </div>

               </div>

               <div class="clearfix mb-20"></div>

               {{-- 

               <div class="col-sm-12">

                  <a type="button" id="getdashboardbtn" class="btn btn-default fr" onclick="openPlanFormModel(event)">Get Your Dairy Dashboard</a>

               </div>

               --}}

            </div>

            <div class="wmodel clearfix" id="buyModel" style="width: 500px;height: 500px;">

               <div class="close" onclick="closePlanFormModel()">X</div>

               <div class="wmodel-body">

                  <div class="">

                     <div id="planSelectorMY" class="">

                        {{-- <a href="javascript:void(0);" onclick="pricePlanBack()" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a> --}}

                        <div class="buy-s-plan">

                           Selected Plan

                           <div class="s-p-name" id="buy-s-name"></div>

                           <br>

                           Select Price

                           <div>

                              <br>

                              <label for="monthlyPrice" >

                                 <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="monthlyPrice" value="monthly" data-price="" onchange="setPrice(this)" required>

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

                                 <input type="radio" name="priceMonthlyOrYearly" class="fl m-18-12" id="yearlyPrice" value="yearly" data-price="" onchange="setPrice(this)" required>

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

                           <button type="submit" class="btn btn-primary">Get Dashboard</button>

                           {{-- <a href="javascript:void(0)" class="btn btn-primary sahdfoaysodf" id="sahdfoaysodf" role="button" onclick="asdkfhsdaf()"> Get Dashboard </a> --}}

                        </div>

                     </div>

                  </div>

               </div>

            </div>

         </form>

      </div>

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

            url:"{{url('ccavRequestHandler')}}",

            data: data,

            success:function(res){

               if(!res.error){

                  $(".flash-alert").removeClass("hide").addClass("alert-success"); $(".flash-alert .flash-msg").html(res.msg);

                     setTimeout(function(){ window.location.assign("{{url('/')}}"); }, 4000);

               }else{

                  alert(res.msg);

               }

            },

            error:function(res){

                  loader("hide");

            }

         }).done(function(res){

               loader("hide");

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





      loader("show");

      $.ajax({

         type:"POST",

         url:"{{url('getPricePlanDetails')}}",

         data: {planId: pid},

         success:function(res){  

               if(res.error){

                     $.alert(res.msg);

               }else{

                  openPlanFormModel(event);

                  setPlanData(res.plan);

               }

               console.log(res);

         },

         error: function(d){

            console.log(d);

         }

      }).done(function(res){

               loader("hide");

            console.log(res);

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

       $("#monthlyPrice").data("price", plan.monthlyPrice);

       $("#yearlyPrice").data("price", plan.yearlyPrice);

   }

   

   

    function setPrice(e){

        a = $(e).data("price");

        console.log(a);

        $("#amount").val(a);

    }

   

   

   

   

   function getCity(e, cityid){

       var stateID = $(e).val();

       if(stateID){

         loader('show');

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

           }).done(function(res){

               loader("hide");

               console.log(res);

            });

       }else{

           $("#cityid"+cityid).empty();

       }

   }

   

</script>

@endsection