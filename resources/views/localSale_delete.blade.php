@extends('theme.default')



@section('content')



<style type="text/css">

.col-md-6, .col-md-12, .col-md-3, .col-md-4, .col-md-2, .col-md-1{

    margin-top: 5px;

}

.errmsg{

	color:red;

}

</style>



@php

	// if (session()->has('saleActiveTab')) {

	// 	$tab = session()->get('saleActiveTab');

	// }else{

	// 	$tab = "customer";

	// }

	$tab = "member";

@endphp





<div class="span-fixed response-alert" id="response-alert"></div>





<div class="fcard pt-0 margin-fcard-1 clearfix">



	<div class="upper-controls pt-0 clearfix">

		<div class="fl">

			<h3>Delete Local Sale</h3>

			<hr class="m-0">

			{{--

			<div class="light-color f-12">Total: {{count($purchaseList)}}</div> --}}

		</div>

		<div class="fr">

			<div class="fr p-5" style="padding:5px;">

				Morning Collection: <b>{{$msc}}</b>

				<br>

				Evening Collection: <b>{{$esc}}</b>

			</div>

			<div class="fr p-5" style="padding:5px;">

				Today's Local Sale: <b>{{$tsale}}</b>

			</div>	

		</div>

	</div>





	<ul class="nav nav-tabs sale-tabs" hidden>

		<li class="@if($tab=="customer") active @endif"><a data-toggle="tab" href="#customersSale" onclick="document.getElementById('customerCode').focus();">Customers</a></li>

		<li class="@if($tab=="member") active @endif"><a data-toggle="tab" href="#memberSale" onclick="document.getElementById('memberCode').focus()">Members</a></li>

	</ul>

	

	<div class="tab-content" hidden>



		<div id="customersSale" class="tab-pane fade in @if($tab=="customer") active @endif" hidden>				

			<div class="">

				<form method="post" action="{{ url('/localSaleFormSubmit') }}?returnurl=localSaleForm" id="custLocalSaleForm">

					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">

					<input type="hidden" name="status" value="true">

					<input type="hidden" name="partyType" value="customer" id="partyType">

					<input type="hidden" name="sale_type" value="local_sale">

					<input type="hidden" name="returnurl" value="localSaleForm">

					<input type="hidden" name="activetab" value="customer">



					<div class="clearfix">

						<div class="col-md-6 hide">

							<label>Ledger name</label>

							<span id="ledgerErr" class="ledgerErr errmsg">  </span>

							<input list="ledgerList" name="ledgerName" id="ledger" class="ledger form-control" autocomplete="off">

						</div>



						<div class="col-md-2"> 

							<label>Date</label>

							<input type="text" class="form-control" id="cdate" placeholder="Enter Name" value="{{old("date")? old("date"):date("d-m-Y")}}" name="date" tabindex=11  autocomplete="off">

						</div>



						<div class="col-md-3">

							<label>Customer Code</label>

							<span id="customerCodeErr" class="customerCodeErr errmsg">  </span>

							<input id="customerCode" class="form-control" required autofocus name="customerCode" tabindex=12 data-name="code" autocomplete="off">

							<img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/> 

						</div>

						<div class="col-md-3">

							<label>Customer Name</label>

							<span id="customerNameErr" class="customerNameErr errmsg">  </span>

							<input id="customerName" name="partyName" class="form-control" required tabindex=13 data-name="name" autocomplete="off">

						</div>



						<div class="col-md-4">

							<div class="col-md-12 clearfix">

								<div class="fl">Current Balance:</div>

								<div class="custAccInfo bold ps-5 fl"></div>

							</div>

							<div class="col-sm-12 clearfix">
						
								 <select class="form-control" name="product" onChange="checkProduct(this, 'customer');getSaleAmount('customer')">
								 	<option>Select Category</option>
									@foreach ($categories as $cat)
								 	<option value="{{$cat->id}}" data-price="{{$cat->price}}">{{$cat->name}}</option>
								 	@endforeach
								 </select>

<!-- 								<label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'customer');getSaleAmount('customer')" value="cowMilk" tabindex="14" required>Cow Milk</label>
 -->
							</div>

<!-- 							<div class="col-sm-6 clearfix">

								<label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'customer');getSaleAmount('customer')" value="buffaloMilk" tabindex="14" required>Buffalo Milk</label>

							</div>

 -->						</div>

						

					</div>

					{{-- <div class="col-sm-12">

					</div> --}}

					<div class="clearfix">

						<div class="col-md-2"> 

							<label>Quantity</label>

							<input type="number" class="form-control" onkeyup="getSaleAmount('customer')" id="cQuantity" placeholder="Enter Quantity" name="quantity" tabindex="15" step="0.1" required autocomplete="off">

						</div>

					

						<div class="col-md-1"> 

							<label>&nbsp;</label>

							<input type="text" id="unit_" name="unit" value="Ltr" class="noinput" style="width: 100%;line-height: 30px; color: #d00606; padding-left:0; font-weight:bold" readonly autocomplete="off">

						</div>	



						<div class="col-md-2">

							<label>&nbsp; </label>

							<input type="hidden" class="form-control" id="cPricePerUnit" name="PricePerUnit" required>

							<input type="text" readonly="readonly" class="noinput"  id="cRate" name="rate" style="width: 100%;line-height: 30px;color: #d00606; padding-left:0;font-weight:bold" autocomplete="off">

						</div>



						<div class="col-md-2"> 

							<label> Amount </label>

							<input type="number" readonly="readonly" class="form-control rupee"  id="cAmount" name="amount" min="0" required autocomplete="off">

						</div>

							

						<div class="col-md-2">

							<label>Discount (&#8377;)</label>

							<input type="number" class="form-control"  id="cDiscount" name="discount" value="0" tabindex="16" onchange="getFinalAmount('customer')" min="0" required autocomplete="off">

						</div>



						<div class="col-md-2">

							<label>Final Amount</label>

							<input type="number" class="form-control" id="cfinalAmount" value="0" name="finalAmount" readonly min="0" autocomplete="off">

						</div>

						

						<div class="col-md-5 mt-5">

							<label>Remark</label>

							<input type="text" class="form-control" id="cRemark" value="" name="remark" tabindex="17" row="2" placeholder="(Optional)" maxlength="300" autocomplete="off">

						</div>

						

						<div class="col-md-2">

							<label> Paid Amount </label>

							<input type="number" class="form-control rupee" value="0" id="cpaidAmount" name="paidAmount" tabindex="18" required min="0" autocomplete="off">

						</div>



						<div class="col-md-2 text-center">	

							<div class="pt-25"></div>

							<button type="submit" class="btn btn-primary SubmitBtn" tabindex="20">Submit</button>

						</div>			

					</div>

				</form>

			</div>



		</div>





		<div id="memberSale" class="tab-pane fade in @if($tab=="member") active @endif">



			<div class="">

				<form method="post" action="{{ url('/localSaleFormSubmit') }}?returnurl=localSaleForm" id="memberLocalSaleForm">

					<input type="hidden" name="_token" value="{{ csrf_token() }}">

					<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">

					<input type="hidden" name="status" value="true">

					<input type="hidden" name="partyType" value="member" id="partyType">

					<input type="hidden" name="sale_type" value="local_sale">

					<input type="hidden" name="activetab" value="member">

					<input type="hidden" name="returnurl" value="localSaleForm">



					<div class="clearfix">

						<div class="col-md-6 hide">

								<label>Ledger name</label>

								<span id="ledgerErr" class="ledgerErr errmsg">  </span>

								<input list="ledgerList" name="ledgerName" id="ledger" class="ledger form-control" onfocusout="getUserNameByLedger(this.value);" autocomplete="off">

						</div>

		

						<div class="col-md-2"> 

							<label>Date</label>

							<input type="text" class="form-control" id="mdate" placeholder="Enter date" value="{{old("date")? old("date"):date("d-m-Y")}}" name="date" autocomplete="off" tabindex="21">

						</div>

		

						<div class="col-md-3">

							<label>Member Code</label>

							<span id="memberCodeErr" class="memberCodeErr errmsg">  </span>

							<input id="memberCode" class="form-control" required autofocus name="memberCode" tabindex="22" data-name="code" autocomplete="off">

							<img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/>

						</div>

						<div class="col-md-3">

							<label>Member Name</label>

							<span id="memberNameErr" class="memberNameErr errmsg">  </span>

							<input id="memberName" name="partyName" class="form-control" required tabindex="23" data-name="name" autocomplete="off">

						</div>

		

						<div class="col-md-4">

							<div class="col-md-12 clearfix">

								<div class="fl">Current Balance:</div>

								<div class="membAccInfo bold ps-5 fl"></div>

							</div>
							<div class="col-sm-12 clearfix">
						
								 <select class="form-control" name="product" onChange="checkProduct(this, 'member');getSaleAmount('member')">
								 	<option>Select Category</option>
									@foreach ($categories as $cat)
								 	<option value="{{$cat->id}}" data-price="{{$cat->price}}">{{$cat->name}}</option>
								 	@endforeach
								 </select>


<!-- 								<label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'customer');getSaleAmount('customer')" value="cowMilk" tabindex="14" required>Cow Milk</label>
 -->

							</div>

<!-- 							<div class="col-sm-6 clearfix">

								<label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'member');getSaleAmount('member')" value="cowMilk" tabindex="24" required>Cow Milk</label>

							</div>

							<div class="col-sm-6 clearfix">

								<label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'member');getSaleAmount('member')" value="buffaloMilk" tabindex="24" required>Buffalo Milk</label>

							</div>

						</div>
 -->
						

					</div>

					{{-- <div class="col-md-12">

					</div> --}}

				</form>

			</div>

		</div>

	</div>
</div>



<div class="table-back">

	<table id="sale-table" class="display table-bordered tright" style="width:100%">

		<thead>

			<tr>

				<th></th>

				<th>Party Code</th>

				<th>Party Name</th>

				<th>Milk Type</th>

				<th>Date</th>

				<th>Price Per Unit</th>

				<th>Quantity</th>

				<th>Amount</th>

				<th>Discount</th>

				<th>Final Amount</th>

				<th>Amount Paid by customer</th>

				<th>Cash/Credit</th>

		

			</tr>

		</thead>

		<tbody>

		</tbody>

	</table>

</div>









<div class="wmodel clearfix" id="saleEditModel" style="width: 75%;">

	<div class="close">X</div>

	<div class="wmodel-body">

		

	</div>

</div>





<script type="text/javascript">

	var isCash = false;

	var isFormSubmitted = false;



	var dairyId = document.getElementById("dairyId").value;

						$( function() {
							var customers = [
								@foreach ($currnetData[0] as $cust)

									{

										value: "{{ $cust->customerCode }}",

										label: "{{ $cust->customerCode }}",

										desc: "{{ $cust->customerName }}",

									},

								@endforeach

							];

							var customersName = [
								@foreach ($currnetData[0] as $cust)
									{

										value: "{{ $cust->customerName }}",

										label: "{{ $cust->customerName }}",

										desc: "{{ $cust->customerCode }}",

									},
								@endforeach
							];



							var members = [
								@foreach ($currnetData[1] as $mem)

									{

										value: "{{ $mem->memberPersonalCode }}",

										label: "{{ $mem->memberPersonalCode }}",

										desc: "{{ $mem->memberPersonalName }}",

									},

								@endforeach

							];

							

							var memberNames = [

								@foreach ($currnetData[1] as $mem)

									{

										value: "{{ $mem->memberPersonalName }}",

										label: "{{ $mem->memberPersonalName }}",

										desc: "{{ $mem->memberPersonalCode }}",

									},

								@endforeach

							];

						

							$( "#customerCode" ).autocomplete({

									minLength: 0,

									source: customers,

									focus: function( event, ui ) {

										$( "#customerCode" ).val( ui.item.label );

										return false;

									},

									select: function( event, ui ) {

										$( "#customerCode" ).val( ui.item.label);

										$( "#customerName" ).val( ui.item.desc);

										return false;

									}

								}).autocomplete( "instance" )._renderItem = function( ul, item ) {

									return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

								};



							$( "#customerName" ).autocomplete({

									minLength: 0,

									source: customersName,

									focus: function( event, ui ) {

										$( "#customerName" ).val( ui.item.label );

										return false;

									},

									select: function( event, ui ) {

										$( "#customerName" ).val( ui.item.label );

										$( "#customerCode" ).val( ui.item.desc);

										return false;

									}

								}).autocomplete( "instance" )._renderItem = function( ul, item ) {

									return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

								};

							$( "#memberCode" ).autocomplete({

								minLength: 0,

								source: members,

								focus: function( event, ui ) {

									$( "#memberCode" ).val( ui.item.label );

									return false;

								},

								select: function( event, ui ) {

									$( "#memberCode" ).val( ui.item.label );

									$("#memberName").val(ui.item.desc);

									return false;

								}

								}).autocomplete( "instance" )._renderItem = function( ul, item ) {

									return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

								};

								

							$( "#memberName" ).autocomplete({

								minLength: 0,

								source: memberNames,

								focus: function( event, ui ) {

									$( "#memberName" ).val( ui.item.label );

									return false;

								},

								select: function( event, ui ) {

									$( "#memberName" ).val( ui.item.label );

									$("#memberCode").val(ui.item.desc);

									return false;

								}

								}).autocomplete( "instance" )._renderItem = function( ul, item ) {

									return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

								};



					});





	$(document).ready(function() {

		$('#sale-table').DataTable({

			"ajax": 'getLocalSaleAjaxDelete',

			"columnDefs": [{

					"targets": [ 0 ],

					"visible": false,

					"searchable": false,
				},

				{ "width": "52px", "targets": 4}

				],

			"order": []

		});





		$(document).on('keypress', 'input', function(event) {

			if (event.which == 13) {

				event.preventDefault();

				old = $(this).attr('tabindex');

				$(this).closest('form').find(":input:visible").each(function(){

					if($(this).attr('tabindex') > old){

						$(this).focus();

						return false;

					}

				})

				

				return false;

			}

		});



	})



	$("#memberCode, #memberName").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "member", "");

    })



	$("#customerCode, #customerName").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "customer", "");

    })





	function getUserDetail(q, elm, qtype, user, no){

		$("#loading-input-img").show();



		if(q){

			loader("show");



			$.ajax({

				type:"POST",

				url:'getUserDetail' ,

				data: {

					q: q,

					qtype: qtype,

					dairyId: dairyId,

					user: user

				},

				success:function(res){

					$("#loading-input-img").hide();

					if(res.error){

						$("#response-alert").html(res.msg).show();

						$(elm).addClass("has-error");

					}else{

						$("#response-alert").hide();

						setUserData(res.data, elm, user, no);

					}

					loader("hide");

					console.log(res);



				},

				error:function(res){

					$("#loading-input-img").hide();

					console.log(res);

				}

			});



		}

	} 



	function setUserData(data, elm, user, no){

		var cl = "";

		if(data.balType == "credit"){

			cl = "Cr"; 

		}else if(data.balType == "debit"){

			cl = "Dr";

		}

		if(user=="member"){

			$("#memberCode").val(data.code);

			$("#memberName").val(data.name);

			$(".membAccInfo").html("&#8377; "+data.bal+" "+cl+".").removeClass("cr, dr").addClass(cl);

		}

		

		if(user=="customer"){

			$("#customerCode").val(data.code);

			$("#customerName").val(data.name);

			$(".custAccInfo").html("&#8377; "+data.bal+" "+cl+".").removeClass("cr, dr").addClass(cl);

		}

		

		if(data.isCash){isCash = true;}else{isCash = false;}



		$("#customerCode, #customerName, #memberCode, #memberName").removeClass("has-error");



	}



	function getUserNameByLedger(ledgerId){

		

		var dairyId = document.getElementById("dairyId").value;

		var mainValue = document.getElementById("ledgerValue"+ledgerId);



		 	if(mainValue){

		        var mainValue= mainValue.innerHTML;

		        document.getElementById("productType").value = mainValue;

		    }

		

			if(ledgerId){

		         $.ajax({

		                type:"POST",

		                url:'getUserNameByLedger',

		                data: {

		                    ledgerId: ledgerId,

		                    dairyId: dairyId,

		                    mainValue: mainValue,

		                },

		               success:function(res){

		               		if(res == "false"){

			               		document.getElementById("ledgerErr").innerHTML = "This ledger if is not valid.";

                  				document.getElementById("ledger").focus();

                  				document.getElementById("customerName").value = "";

				            }else{

				            	document.getElementById("customerName").value = res;

				            	document.getElementById("ledgerErr").innerHTML = "";

							}

		               }

		            });

		    }



	}



	function getSaleAmount(type){

		if(type="customer"){

			a = parseFloat($("#cQuantity").val()) * parseFloat($("#cPricePerUnit").val());

			$("#cAmount, #cfinalAmount,#cpaidAmount").val( Math.round (a*100) / 100);
			  
             if( $('#customerName').val() != "cash"){

                 $('#cpaidAmount').val('');

			 }
		}

		if(type="member"){

			a = $("#mQuantity").val() * $("#mPricePerUnit").val();

			$("#mAmount, #mfinalAmount").val( Math.round (a*100) / 100);

		}

	}



	function getFinalAmount(type){

		if(type="customer"){

			dis = $("#cDiscount").val();

			if( dis == "" || dis < 0){

				$("#cDiscount").val("0");

			}

			a = parseFloat($("#cAmount").val()) - parseFloat($("#cDiscount").val());

			a = Math.round (a*100) / 100;

			$("#cfinalAmount").val( Math.round (a*100) / 100);

			if(isCash) $("#cpaidAmount").val( Math.round (a*100) / 100);

			$("#cpaidAmount").attr("max",  Math.round (a*100) / 100);

		}

		if(type="member"){

			dis = $("#mDiscount").val();

			if( dis == "" || dis < 0){

				$("#mDiscount").val("0");

			}

			a =  parseFloat($("#mAmount").val()) - parseFloat($("#mDiscount").val());

			a = Math.round (a*100) / 100;

			$("#mfinalAmount").val( Math.round (a*100) / 100);

			$("#mpaidAmount").attr("max",  Math.round (a*100) / 100);

		}

	}





    $(function () {

        $('#cdate, #mdate').datetimepicker({

          format: 'DD-MM-YYYY'

   	 	});

	});



	function checkPaymentMode(paymentMode){

        if(paymentMode == "other"){

        	 $("#otherPaymentMode").show();          

        }else{

            $("#otherPaymentMode").hide();

        }

    }





	var nocust = false;

	var nomemb = false;





	@if($dairyInfo->buffaloMilkPrice == (null||"") || $dairyInfo->cowMilkPrice == (null||""))

		@php $dairyInfo->buffaloMilkPrice = 0; $dairyInfo->cowMilkPrice = 0; @endphp

		$.confirm({

			title: 'Set Milk Price first',

			content: 'Please set milk price first to start sale.',

			type: 'orange',

			typeAnimated: true,

			buttons: {

				setPrice: {

					text: 'Set Milk Price',

					btnClass: 'btn-orange',

					action: function(){

						window.location = "{{url('productList')}}";

					}

				}

			}

		});

	@elseif(count($currnetData[0]) == 0 && count($currnetData[1]) == 0)

		nocust = true;

		nomemb = true;

	@elseif(count($currnetData[0]) == 0)

		nocust = true;

	@elseif(count($currnetData[1]) == 0)

		nomemb = true;

	@endif





    function checkProduct(product, type){

    	price = $(product).find(':selected').data('price');
		// buf = {{$dairyInfo->buffaloMilkPrice}};

		// cow = {{$dairyInfo->cowMilkPrice}}



		if(type=="customer"){

			rate = "cRate";

			pp = "cPricePerUnit";

		}

		if(type=="plant"){

			rate = "pRate";

			pp = "pPricePerUnit";

		}

		if(type=="member"){

			rate = "mRate";

			pp = "mPricePerUnit";

		}

		if(price !=null){

		// if($(product).val()=="buffaloMilk"){

			$("#"+pp).val(parseFloat(price).toFixed(2));

			$("#"+rate).val("@ "+price+" per Ltr");
		}else{
			$("#"+pp).val("");

			$("#"+rate).val("");

		}

		// }else{

		// 	$("#"+pp).val(parseFloat(cow).toFixed(2));

		// 	$("#"+rate).val("@ "+cow+" per Ltr");

		// }



		$("#quantity").focus();

	

		// console.log(product);

		// $.ajax({

		// 		type:"POST",

		// 		url:'getProductUnit' ,

		// 		data: {

		// 			dairyId: dairyId,

		// 			productCode: product,

		// 		},

		// 		success:function(res){

		// 			console.log(res);

		// 			$("#unit_").val(res.unit);

		// 			$("#rate").val(res.rate+" per "+ res.unit);

		// 			$("#PricePerUnit").val(parseFloat(res.rate).toFixed(2)); 

		// 			$("#quantity").focus();

		// 		},

		// 		error:function(res){

		// 			console.log(res);

		// 		}

		// 	});

    }



    // function checkUnit(checkUnit){

	

 	// 	if(checkUnit == "specify"){

    //     	 $("#otherUnit").show();

    //     }else{

    //         $("#otherUnit").hide();

    //     }

    // }



	$("#quantity").on("keyup", function(){

		var q = parseFloat($("#quantity").val()).toFixed(2);

		var rate = parseFloat($("#PricePerUnit").val()).toFixed(2);

		var am = parseFloat(q*rate).toFixed(2);

		$("#amount").val(am);

		console.log(q,am,rate);

		console.log($("#PricePerUnit").val());

	})





	function editSale(id){

		loader("show");

			$.ajax({

				type:"POST",

				url:'{{url("getSaleDetails")}}',

				data: {

					id: id

				},

				success:function(res){

					if(res.error){

						$(".flash-alert .flash-msg").html(res.msg);

						$(".flash-alert").removeClass("hide").removeClass("alert-danger").show().addClass("alert-danger");

						setTimeout( function(){$(".flash-alert").fadeOut("fast");}, 7000);

					}else{

						$("#saleEditModel").fadeIn();

						$("#saleEditModel .wmodel-body").html(res.data);

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



	$(".wmodel .close").on('click', function(){

		$(this).closest(".wmodel").fadeOut();

	})



		if(nocust == true || nomemb == true){

			if(!nomemb){

				url= "{{url('CustomerForm')}}";

				backdis = true;

				Title = "Add Customer"; text = "Add Customer";

			}else{

				url = "{{url('memberSetupForm')}}";

				backdis = false;

				Title = "Add Customer and Member first"; text = "Add Member";

			}

			$.confirm({

				title: Title,

				content: 'There are No Customer for milk sale, Please add atleast 1 customer to start milk sale.',

				type: 'orange',

				typeAnimated: true,

				backgroundDismiss: backdis,

				buttons: {

					addMember: {

						text: text,

						btnClass: 'btn-orange',

						action: function(){

							window.location = url;

						}

					}

				}

			});

		}



		@if($noCustomer)

			$.alert("No Customer found. <a href='{{url('CustomerForm')}}'>Add Customer</a> <br/><br/> You only have a 'cash' customer to sale your product.");

		@endif



		



        $("#custLocalSaleForm, #memberLocalSaleForm").submit(function (e) {

			e.preventDefault();

			if(isFormSubmitted){

				return false;

			}



			console.log("erwsdfhsdkf");



			if (!this.checkValidity()) {

				console.log("erw");

				return false;

			}

			$(this).find(".SubmitBtn").attr("disabled", true);



			isFormSubmitted = true;

			$(this)[0].submit();

		});

</script>

@endsection