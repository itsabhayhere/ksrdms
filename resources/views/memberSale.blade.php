@extends('theme.default')



@section('content')



<style type="text/css">

/* .col-md-6, .col-md-12, .col-md-3{

    margin-top: 12px;

} */

.errmsg{

	color:red;

}
.addproductbtn{
	z-index: 20;
}

#sale-table_length{
	display:none;
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



<div class="fcard margin-fcard-1 pt-0 clearfix">



		<div class="upper-controls pt-0 clearfix">

			<div class="fl">

				<h3>Product Sale</h3>

				<hr class="m-0">

				{{--

				<div class="light-color f-12">Total: {{count($purchaseList)}}</div> --}}

			</div>

			<div class="fr">

				{{-- <a class="btn btn-primary" href="DailyTransactionForm">Add Purchase</a> --}}

			</div>

		</div>





		

		<ul class="nav nav-tabs sale-tabs">

			<li class="@if($tab=="customer") active @endif"><a data-toggle="tab" href="#customersSale">Customers</a></li>

			<li class="@if($tab=="member") active @endif"><a data-toggle="tab" href="#memberSale">Members</a></li>

		</ul>



		<div class="tab-content">



			<div id="customersSale" class="tab-pane fade in @if($tab=="customer") active @endif">

				<div class="clearfix">

					<form method="post" action="{{ url('/productSaleFormSubmit') }}?returnurl=memberSaleForm">

						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">

						<input type="hidden" name="status" value="true">

						<input type="hidden" name="sale_type" value="product_sale">

						<input type="hidden" name="partyType" value="customer">

						{{-- <input type="hidden" name="ledgerName" id="cledger" /> --}}

						<input type="hidden" name="activetab" value="customer">

						<input type="hidden" name="returnurl" value="memberSaleForm">

						

						<div class="pt-10 clearfix">

							

							<div class="col-md-2"> 

								<label>Date</label>

								<input type="text" class="form-control" id="cdate" readonly placeholder="Enter Name" value="<?php echo date("d-m-Y", time()); ?>" name="date" tabindex="11" autocomplete="off">

							</div>



							<div class="col-md-4">

								<label>Customer Code</label>

								<span id="customerCodeErr" class="customerCodeErr errmsg"> </span>

								<input id="customerCode" class="form-control customerCode" name="customerCode" required autofocus tabindex=12 data-name="code" autocomplete="off">

								<img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/> 

							</div>

							<div class="col-md-4">

								<label>Customer Name</label>

								<span id="customerNameErr" class="customerNameErr errmsg"> </span>

								<input id="customerName" name="partyName" class="form-control customerCode" required tabindex=13 data-name="name"  autocomplete="off">

							</div>



							<div class="col-md-2 pt-20">

									Current Balance:

								<div class="custAccInfo monospace bold"></div>

							</div>

						</div>

						{{-- <div class="col-md-12"> 

						</div> --}}

						<div class="pt-10 clearfix cproductlist">

							<div class="col-md-3 ps-5"> 

								<label>Product</label>

								<select id="product_0" class="product selectpicker" name="product[]" title="Select Product" onchange="checkProduct(this.value, 'customer',0);getSaleAmount('customer',0)" data-live-search="true" tabindex="14">

									{{-- <option >--Select--</option> --}}

									@foreach($currnetData[3] as $prod)

								@php if($prod->productUnit <= 0){ $dis = "disabled class=out-stock"; $t = "(Out of stock)"; } else {$dis = ""; $t = ""; } @endphp

										<option value="{{$prod->productCode}}" data-subtext="<span style='right:10px;position:absolute'>{{$prod->productCode}}</span>"

											{{ $dis }}>{{$prod->productName . " " . $t}}</option>

									@endforeach

								</select>

							</div>



							<div class="col-md-2 ps-5">

								<label>Quantity</label>

								<input type="text" class="form-control" id="cQuantity_0" placeholder="Enter Quantity" name="quantity[]" tabindex="15" onkeyup="getSaleAmount('customer',0)"  autocomplete="off">

							</div>
						 	 <div class="col-md-2 ps-5">

									<label> Amount </label>

									<input type="number" readonly="readonly" class="form-control rupee"  id="cAmount_0" name="amount[]" min="0"  autocomplete="off">

								</div>

								<div class="col-md-2 ps-5">

									<label>Discount (&#8377;)</label>

									<input type="number" class="form-control"  id="cDiscount_0" name="discount[]" value="0" tabindex="27" onchange="getFinalAmount('customer',0)" min="0" autocomplete="off"> 

								</div>
							

							<div class="col-md-3 dnone">

								<label>&nbsp;</label>

								<input type="hidden" id="cunit_0" name="unit[]" value="Unit" class="noinput" style="width: 100%;line-height: 38px;padding: 0;" readonly  autocomplete="off">

							</div>

							

								<div class="col-md-2 ps-5"> 


								<label>&nbsp; </label>

								<input type="hidden" name="PricePerUnit[]" id="cRate_0" >

								<div id="cPricePerUnit_0" style="width: 100%;line-height: 38px;"></div>

							</div>
								<div class="col-md-1"> 
									<br/>
									<button type="button" class="btn btn-warning addproductbtn" tabindex="29" onclick="caddProduct()"><i class="fa fa-plus addproduct"> </i></button>
								</div>
						</div>

					

						<div class="pt-10 clearfix"> 

							<div class="col-md-4 ps-5">

								<label>Remark</label>

								<input type="text" class="form-control" id="cRemark" value="" name="remark" tabindex="101" row="2" placeholder="(Optional)" maxlength="300"  autocomplete="off">

							</div>



<!-- 							<div class="col-md-2 ps-5"> 

								<label> Amount </label>

								<input type="number" readonly="readonly" class="form-control rupee"  id="cAmount" name="amount" min="0"  autocomplete="off">

							</div>

				

							<div class="col-md-2 ps-5">

								<label>Discount (&#8377;)</label>

								<input type="number" class="form-control"  id="cDiscount" name="discount" value="0" tabindex="17" onchange="getFinalAmount('customer')" min="0"  autocomplete="off">

							</div>

 -->

							<div class="col-md-2 ps-5">

								<label>Final Amount</label>

								<input type="number" class="form-control" id="cfinalAmount" value="0" name="finalAmount" readonly min="0"  autocomplete="off">

							</div>

							

							<div class="col-md-2 ps-5">

								<label> Paid Amount </label>

								<input type="number" class="form-control rupee" value="0" id="cpaidAmount" name="paidAmount" tabindex="102" required min="0"  autocomplete="off">

							</div>



							<div class="col-md-1 pt-20 ps-5">

								<button type="submit" class="btn btn-primary btn-block customerSubmit" tabindex="105">Submit</button>

							</div>	

						</div>



					</form>

				</div>



			</div>



			<div id="memberSale" class="tab-pane fade in @if($tab=="member") active @endif">

					<div class="clearfix">

						<form method="post" action="{{ url('/productSaleFormSubmit') }}?returnurl=memberSaleForm">

							<input type="hidden" name="_token" value="{{ csrf_token() }}">

							<input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">

							<input type="hidden" name="status" value="true">

							<input type="hidden" name="partyType" value="member">

							{{-- <input type="hidden" name="ledgerName" id="mledger" /> --}}

							<input type="hidden" name="sale_type" value="product_sale">

							<input type="hidden" name="activetab" value="member">

							<input type="hidden" name="returnurl" value="memberSaleForm">					

							

							<div class="pt-10 clearfix">								

								<div class="col-md-2"> 

									<label>Date</label>

									<input type="text" class="form-control" readonly id="mdate" placeholder="Enter Name" value="<?php echo date("d-m-Y", time()); ?>" name="date"  tabindex="21" autocomplete="off">

								</div>



								<div class="col-md-4">

									<label>Member Code</label>

									<span id="memberCodeErr" class="memberCodeErr errmsg">  </span>

									<input id="memberCode" class="form-control" required autofocus name="memberCode" required tabindex="22" data-name="code"  autocomplete="off">

									<img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/>

								</div>

								<div class="col-md-4">

									<label>Member Name</label>

									<span id="memberNameErr" class="memberNameErr errmsg"> </span>

									<input id="memberName" name="partyName" class="form-control" required tabindex="23" data-name="name"  autocomplete="off">

								</div>

	

								<div class="col-md-2 pt-20">

									Current Balance:

									<div class="membAccInfo monospace bold"></div>

								</div>

							</div>

							

							<div class="pt-10 clearfix productlist">

								<div class="col-md-3 ps-5">

									<label>Product</label>

									<!--<select id="product" class="product selectpicker" multiple name="product[]" title="Select Product" onchange="checkProduct(this.value, 'member');getSaleAmount('member')" data-live-search="true" tabindex="24">-->
									<select id="product_0" class="product selectpicker" name="product[]" title="Select Product" onchange="checkProduct(this.value, 'member',0);getSaleAmount('member',0)" data-live-search="true" tabindex="24">

										{{-- <option >--Select--</option> --}}

										@foreach($currnetData[3] as $prod)

										@php if($prod->productUnit <= 0){ $dis = "disabled class=out-stock"; $t = "(Out of stock)"; } else {$dis = ""; $t = ""; } @endphp

											<option value="{{$prod->productCode}}" data-subtext="<span style='right:10px;position:absolute'>{{$prod->productCode}}</span>"

												{{ $dis }}>{{$prod->productName . " " . $t}}</option>

										@endforeach

									</select> 

								</div>



								<div class="col-md-2 ps-5"> 

									<label>Quantity</label>

									<input type="text" class="form-control" id="mQuantity_0" placeholder="Enter Quantity" name="quantity[]" tabindex="25" onkeyup="getSaleAmount('member',0)"  autocomplete="off">

								</div>
								<div class="col-md-2 ps-5">

									<label> Amount </label>

									<input type="number" readonly="readonly" class="form-control rupee"  id="mAmount_0" name="amount[]" min="0"  autocomplete="off">

								</div>

								<div class="col-md-2 ps-5">

									<label>Discount (&#8377;)</label>

									<input type="number" class="form-control"  id="mDiscount_0" name="discount[]" value="0" tabindex="27" onchange="getFinalAmount('member',0)" min="0" autocomplete="off"> 

								</div>

							

								<div class="col-md-3 dnone"> 

									<label>&nbsp;</label>

									<input type="text" id="munit_0" name="unit[]" value="Unit" class="noinput" style="width: 100%;line-height: 38px; padding:0" readonly  autocomplete="off">

								</div>

								<div class="col-md-2 ps-5"> 

									<label>&nbsp; </label>

									<input type="hidden" name="PricePerUnit[]" id="mRate_0">

									<div id="mPricePerUnit_0" style="width: 100%;line-height: 38px;"></div>

								</div>
								<div class="col-md-1"> 
									<br/>
									<button type="button" tabindex="28" class="btn btn-warning addproductbtn" onclick="addProduct()"><i class="fa fa-plus addproduct"> </i></button>
								</div>

							</div>



							<div class="pt-10 clearfix">

								<div class="col-md-5">

									<label>Remark</label>

									<input type="text" class="form-control" id="mRemark" value="" name="remark" tabindex="101" row="2" placeholder="(Optional)" maxlength="300"  autocomplete="off">

								</div>



								<!-- <div class="col-md-2 ps-5">

									<label> Amount </label>

									<input type="number" readonly="readonly" class="form-control rupee"  id="mAmount" name="amount" min="0"  autocomplete="off">

								</div>



								<div class="col-md-2 ps-5">

									<label>Discount (&#8377;)</label>

									<input type="number" class="form-control"  id="mDiscount" name="discount" value="0" tabindex="27" onchange="getFinalAmount('member')" min="0" autocomplete="off"> 

								</div> -->

					

								<div class="col-md-2 ps-5">

									<label>Final Amount</label>

									<input type="number" class="form-control" id="mfinalAmount" value="0" name="finalAmount" readonly min="0" autocomplete="off">

								</div>

	

								<div class="col-md-2 ps-5">

									<label> Paid Amount </label>

									<input type="number" class="form-control rupee" value="0" id="mpaidAmount" name="paidAmount" tabindex="102" required min="0" autocomplete="off">

								</div>

								

								<div class="col-md-1 pt-20 ps-5">

									<button type="submit" class="btn btn-primary btn-block customerSubmit" tabindex="105">Submit</button>

								</div>

		

							</div>

							

						</form>

					</div>



				</div>



		</div>



	</div>



	<div class="table-back">
	<label >Show
<select name="sale-tablelength" id="filterDropdown" aria-controls="sale-table" class="">
    <option value="500">500</option>
    <option value="1000">1000</option>
    <option value="1500">1500</option>
    <option value="2000">2000</option>
</select>
entries
</label>



		<table id="sale-table" class="display table-bordered tright" style="width:100%">

			<thead>

				<tr>

					<th></th>

					<th>Party Code</th>

					<th>Party Name</th>

					<th>Date</th>

					<th>Product</th>

					<th>Price Per Unit</th>

					<th>Quantity</th>

					<th>Amount</th>
					
					<th>Purchase Amount</th>

					<th>Discount</th>

					<th>Amount Paid by customer</th>

					<th>Final Amount</th>

					<th>Cash/Credit</th>

					<th></th>

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

								@foreach ($currnetData[2] as $mem)

									{

										value: "{{ $mem->memberPersonalCode }}",

										label: "{{ $mem->memberPersonalCode }}",

										desc: "{{ $mem->memberPersonalName }}",

									},

								@endforeach

							];

							

							var memberNames = [

								@foreach ($currnetData[2] as $mem)

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

										$( "#customerName" ).val( ui.item.desc);

										return false;

									},

									select: function( event, ui ) {

										// setCustomerCode();

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

										$( "#customerCode" ).val( ui.item.desc);

										return false;

									},

									select: function( event, ui ) {

										// setCustomerCode();

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

									$("#memberName").val(ui.item.desc);

									return false;

								},

								select: function( event, ui ) {

									// setMemberCode();

									return false;

								}

								}).autocomplete( "instance" )._renderItem = function( ul, item ) {

									return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

								};

								

							$( "#memberName" ).autocomplete({

								minLength: 0,

								source: memberNames,

								focus: function( event, ui ) {

									$("#memberName").val( ui.item.label );

									$("#memberCode").val(ui.item.desc);

									return false;

								},

								select: function( event, ui ) {

									$("#memberCode").trigger('change');

									// setMemberCode();

									console.log("283764");

									return false;

								}

								}).autocomplete( "instance" )._renderItem = function( ul, item ) {

									return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );

							};

					});





	// function setCustomerCode(){

	// 	cc = $("#customerCode").val();

 	// 	getledgerid(cc, "customer");

	// }



	// function setMemberCode(){

	// 	cc = $("#memberCode").val();

 	// 	getledgerid(cc, "member");

	// }

	

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

	

	$("#memberCode").on("change, focusout", function(){

		console.log("asd");

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "member", "");

    })




   //change by Deepak
	$(".customerCode").on("change, focusout", function(){

        v = $(this).val();

        if(v==(null||'')){

            return false;

        }

        getUserDetail(v, this, $(this).data("name"), "customer", "");

    });

    // Product add and remove







	// function getledgerid(userCode, type){

	// 	$("#loading-input-img").show();



	// 	if(userCode){

	// 		$.ajax({

	// 			type:"POST",

	// 			url:'getLedgerIdByName' ,

	// 			data: {

	// 				userCode: userCode,

	// 				type: type,

	// 				dairyId: dairyId,

	// 			},

	// 			success:function(res){

	// 				$("#loading-input-img").hide();

	// 				console.log(res);

	// 				if(res.error){

	// 					$.alert("there is an error");

	// 				}else{

	// 					document.getElementById("customerNameErr").innerHTML = "";

	// 					$("#cledger").val(res.ledgerId);

	// 				}

	// 			},

	// 			error:function(res){

	// 				$("#loading-input-img").hide();

	// 				console.log(res);

	// 			}

	// 		});



	// 	}

	// } 



	function getUserDetail(q, elm, qtype, user, no){

		$("#loading-input-img").show();



		if(q){

			loader("show");



			$.ajax({

				type:"POST",

				url:"{{url('getUserDetail')}}",

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





	function getSaleAmount(type,index = ""){
 
		if(type="customer"){
          console.log(index);
			if(index !=null){
				a = $("#cQuantity_"+index).val() * $("#cRate_"+index).val();
				$("#cAmount_"+index).val(a);

            let qty = parseFloat($("#cQuantity_" + index).val()) || 0;
            if (qty > 20) {
                if (!confirm("Quantity is greater than 20. Do you want to continue?")) {
                    $("#cQuantity_" + index).val(0);
					$("#cAmount_" + index).val(0);
					$("#cfinalAmount").val(0);
                    qty = 0;
                }
            }


			}else{
				a = $("#cQuantity").val() * $("#cRate").val();
				$("#cAmount").val(a);

			let qty = parseFloat($("#cQuantity").val()) || 0;
            if (qty > 20) {
                if (!confirm("Quantity is greater than 20. Do you want to continue?")) {
                    $("#cQuantity").val(0);
                    qty = 0;
                }
            }

			}


		}

		if(type="member"){
			if(index !=null){
				a = $("#mQuantity_"+index).val() * $("#mRate_"+index).val();
				$("#mAmount_"+index).val(a);

			let qty = parseFloat($("#mQuantity_" + index).val()) || 0;
            if (qty > 20) {
                if (!confirm("Quantity is greater than 20. Do you want to continue?")) {
                    $("#mQuantity_" + index).val(0);
					$("#mAmount_" + index).val(0);
					$("#mfinalAmount").val(0);
                    qty = 0;
                }
            }

			}else{
				a = $("#mQuantity").val() * $("#mRate").val();
				$("#mAmount").val(a);

			let qty = parseFloat($("#mQuantity").val()) || 0;
            if (qty > 20) {
                if (!confirm("Quantity is greater than 20. Do you want to continue?")) {
                    $("#mQuantity").val(0);
                    qty = 0;
                }
            }

			}


		}

		getFinalAmount(type);

	}




	function getFinalAmount(type){

		if(type="customer"){
			dis = $("#cDiscount").val();

			if( dis == "" || dis < 0){

				$("#cDiscount").val("0");

			} 

		    var currentIndex = $(".cproductlist").length;
			console.log(currentIndex);
			var totalAmount = 0;
			for($i=0;$i<currentIndex;$i++){
				totalAmount+= parseFloat($("#cAmount_"+$i).val()) - parseFloat($("#cDiscount_"+$i).val());
			}
			a = parseFloat($("#cAmount").val()) - parseFloat($("#cDiscount").val());

			$("#cfinalAmount").val(totalAmount);

			$("#cpaidAmount").val(totalAmount);

			/*if(isCash) $("#cpaidAmount").val(totalAmount);
			$("#cpaidAmount").attr("max", totalAmount);*/
        
	if (isCash) {
    $("#cpaidAmount").val(totalAmount);
    $("#cpaidAmount").prop("readonly", true);
} else {
    $("#cpaidAmount").val('');
    $("#cpaidAmount").prop("readonly", false);
}

		}

		if(type="member"){			

			dis = $("#mDiscount").val();

			if( dis == "" || dis < 0){

				$("#mDiscount").val("0");

			}

			var currentIndex = $(".productlist").length;
			console.log(currentIndex);
			var totalAmount = 0;
			for($i=0;$i<currentIndex;$i++){
				totalAmount+= parseFloat($("#mAmount_"+$i).val()) - parseFloat($("#mDiscount_"+$i).val());
			}


//			a =  parseFloat($("#mAmount").val()) - parseFloat($("#mDiscount").val());

			$("#mfinalAmount").val(totalAmount);

			$("#mpaidAmount").attr("max", totalAmount);

		}

	}




    $(document).ready(function () {
      $("#cQuantity_0").on("input", function () {
        let customer = $('#customerName').val().toLowerCase();

        if (customer === "cash") {
         
        } else {
          $("#cpaidAmount").val('');
          $("#cpaidAmount").prop("readonly", false);
        }
      });
    });




	// function getCustomerName(ledgerId){

		

	// 	var dairyId = document.getElementById("dairyId").value;

	// 	var mainValue = document.getElementById("ledgerValue"+ledgerId);



	// 	 	if(mainValue){

	// 	        var mainValue= mainValue.innerHTML;

	// 	        document.getElementById("productType").value = mainValue;

	// 	    }

		

	// 		if(ledgerId){

	// 	         $.ajax({

	// 	                type:"POST",

	// 	                url:'getUserNameByledger',

	// 	                data: {

	// 	                    ledgerId: ledgerId,

	// 	                    dairyId: dairyId,

	// 	                    mainValue: mainValue,

	// 	                },

	// 	               success:function(res){  

	// 	               		if(res == "false"){

	// 		               		document.getElementById("ledgerErr").innerHTML = "This ledger if is not valid.";

    //               				document.getElementById("ledger").focus();

    //               				document.getElementById("customerName").value = "";

	// 			            }else{

	// 			            	document.getElementById("customerName").value = res;

	// 			            	document.getElementById("ledgerErr").innerHTML = "";

	// 			            }	

	// 	               }

	// 	            });

	// 	    }



	// }



	// function getSaleAmount(){

	// 	var quantity =  $("#quantity").val();

	// 	var PricePerUnit =  $("#PricePerUnit").val();

	// 	$("#amount").val(quantity*PricePerUnit);

	// }



    $(function () {

        $('#cdate, #mdate').datetimepicker({

          format: 'DD-MM-YYYY'

   	 	});

	});



    function checkProduct(product, type,currentIndex=""){

		var dairyId = document.getElementById("dairyId").value;
		console.log('---1-1-1--');


		console.log(product);

		$.ajax({

				type:"POST",

				url:'{{url('getProductUnit')}}' ,

				data: {

					dairyId: dairyId,

					productCode: product,

				},

				success:function(res){

					console.log(res);

					if(type=="customer"){
						if(currentIndex !=null){
						$("#cPricePerUnit_"+currentIndex).html("<b>&#8377; "+res.rate+"</b> per unit &nbsp;(stock: <b>"+res.stock+"</b>)");

						$("#cRate_"+currentIndex).val(parseFloat(res.rate).toFixed(2));
						}else{
						$("#cPricePerUnit").html("<b>&#8377; "+res.rate+"</b> per unit &nbsp;(stock: <b>"+res.stock+"</b>)");

						$("#cRate").val(parseFloat(res.rate).toFixed(2));

						}

					}

					if(type=="member"){
						console.log('1-1-1');
						if(currentIndex !=null){
						$("#mPricePerUnit_"+currentIndex).html("<b>&#8377; "+res.rate+"</b> per unit &nbsp;(stock: <b>"+res.stock+"</b>)");
						$("#mRate_"+currentIndex).val(parseFloat(res.rate).toFixed(2));
						}else{
						$("#mPricePerUnit").html("<b>&#8377; "+res.rate+"</b> per unit &nbsp;(stock: <b>"+res.stock+"</b>)");
						$("#mRate").val(parseFloat(res.rate).toFixed(2));
						}


					}

					$("#quantity").focus();

				},

				error:function(res){

					console.log(res);

				}

			});

    }



	 /* supplier code validation */

	function CheckCustomerCode(){

	  var customer_code  = document.getElementById("customerCode").value;



		if(customer_code){

			$.ajax({

				type:"GET",

				url:"{{url('/checkCustomerCode')}}?customer_code="+customer_code ,

				success:function(res){          

					if(res == "true"){

						document.getElementById("customerCodeErrorMessage").innerHTML = "This code is already being used.";

						document.getElementById("customerCode").focus();

					}else if(res == "false"){

						document.getElementById("customerCodeErrorMessage").innerHTML = "";

					}

				}

			});

		}

	}



	$("#quantity").on("keyup", function(){

		var q = parseFloat($("#quantity").val()).toFixed(2);

		var rate = parseFloat($("#PricePerUnit").val()).toFixed(2);

		var am = parseFloat(q*rate).toFixed(2);

		$("#amount").val(am);

		console.log(q,am,rate);

	})


	function addProduct(){
		// add 
	var currentIndex = $(".productlist").length;
	console.log(currentIndex);
	var inputIndex = 30 + parseInt(currentIndex);
	var html="";
	 html+='<div class="pt-10 clearfix productlist"><div class="col-md-3 ps-5">';

		html+='<label>Product</label><select id="product_'+currentIndex+'" class="product selectpicker" name="product[]" title="Select Product" onchange="checkProduct(this.value,\'member\','+currentIndex+');getSaleAmount(\'member\','+currentIndex+')" data-live-search="true" tabindex="'+inputIndex+'">';
		html+='@foreach($currnetData[3] as $prod)@php if($prod->productUnit <= 0){ $dis = "disabled class=out-stock"; $t = "(Out of stock)"; } else {$dis = ""; $t = ""; } @endphp<option value="{{$prod->productCode}}" {{ $dis }}>{{$prod->productName . " " . $t}}</option>@endforeach</select></div>';
		html+='<div class="col-md-2 ps-5"><label>Quantity</label><input type="text" class="form-control" id="mQuantity_'+currentIndex+'" placeholder="Enter Quantity" name="quantity[]" tabindex="'+inputIndex+'" onkeyup="getSaleAmount(\'member\','+currentIndex+')"  autocomplete="off"></div>';
		html+='<div class="col-md-2 ps-5"><label> Amount </label><input type="number" readonly="readonly" class="form-control rupee"  id="mAmount_'+currentIndex+'" name="amount[]" min="0"  autocomplete="off"></div>';
		html+='<div class="col-md-2 ps-5"><label>Discount (&#8377;)</label><input type="number" class="form-control"  id="mDiscount_'+currentIndex+'" name="discount[]" value="0" tabindex="'+inputIndex+'" onchange="getFinalAmount(\'member\','+currentIndex+')" min="0" autocomplete="off"></div>';
		html+='<div class="col-md-3 dnone"><label>&nbsp;</label><input type="text" id="munit_'+currentIndex+'" name="unit[]" value="Unit" class="noinput" style="width: 100%;line-height: 38px; padding:0" readonly  autocomplete="off"></div>';
		html+='<div class="col-md-2 ps-5"><label>&nbsp; </label><input type="hidden" name="PricePerUnit[]" id="mRate_'+currentIndex+'"><div id="mPricePerUnit_'+currentIndex+'" style="width: 100%;line-height: 38px;"></div></div>';
		html+='<div class="col-md-1"> <br/><button type="button" tabindex="'+inputIndex+'" class="btn btn-red addproductbtn" onclick="removeProduct('+currentIndex+')"><i class="fa fa-minus addproduct"> </i></button></div>';


		$(".productlist:last").after(html);
		$(".selectpicker").selectpicker('refresh');


	}

	function removeProduct(index){
		$(".productlist:last").remove();
	}


	function caddProduct(){
		// add 
	var currentIndex = $(".cproductlist").length;
	console.log(currentIndex);

	var inputIndex = 30 + parseInt(currentIndex);
	var html="";
	 html+='<div class="pt-10 clearfix cproductlist"><div class="col-md-3 ps-5">';

		html+='<label>Product</label><select id="product_'+currentIndex+'" class="product selectpicker" name="product[]" title="Select Product" onchange="checkProduct(this.value,\'customer\','+currentIndex+');getSaleAmount(\'customer\','+currentIndex+')" data-live-search="true" tabindex="'+inputIndex+'">';
		html+='@foreach($currnetData[3] as $prod)@php if($prod->productUnit <= 0){ $dis = "disabled class=out-stock"; $t = "(Out of stock)"; } else {$dis = ""; $t = ""; } @endphp<option value="{{$prod->productCode}}" {{ $dis }}>{{$prod->productName . " " . $t}}</option>@endforeach</select></div>';
		html+='<div class="col-md-2 ps-5"><label>Quantity</label><input type="text" class="form-control" id="cQuantity_'+currentIndex+'" placeholder="Enter Quantity" name="quantity[]" tabindex="'+inputIndex+'" onkeyup="getSaleAmount(\'customer\','+currentIndex+')"  autocomplete="off"></div>';
		html+='<div class="col-md-2 ps-5"><label> Amount </label><input type="number" readonly="readonly" class="form-control rupee"  id="cAmount_'+currentIndex+'" name="amount[]" min="0"  autocomplete="off"></div>';
		html+='<div class="col-md-2 ps-5"><label>Discount (&#8377;)</label><input type="number" class="form-control"  id="cDiscount_'+currentIndex+'" name="discount[]" value="0" tabindex="'+inputIndex+'" onchange="getFinalAmount(\'customer\','+currentIndex+')" min="0" autocomplete="off"></div>';
		html+='<div class="col-md-3 dnone"><label>&nbsp;</label><input type="text" id="cunit_'+currentIndex+'" name="unit[]" value="Unit" class="noinput" style="width: 100%;line-height: 38px; padding:0" readonly  autocomplete="off"></div>';
		html+='<div class="col-md-2 ps-5"><label>&nbsp; </label><input type="hidden" name="PricePerUnit[]" id="cRate_'+currentIndex+'"><div id="cPricePerUnit_'+currentIndex+'" style="width: 100%;line-height: 38px;"></div></div>';
		html+='<div class="col-md-1"> <br/><button type="button" tabindex="'+inputIndex+'" class="btn btn-red addproductbtn" onclick="cremoveProduct('+currentIndex+')"><i class="fa fa-minus addproduct"> </i></button></div>';


		$(".cproductlist:last").after(html);
		$(".selectpicker").selectpicker('refresh');


	}

	function cremoveProduct(index){
		$(".cproductlist:last").remove();
	}



	function editSale(id){

		loader("show");		

			$.ajax({

				type:"POST",

				url:'{{url('getSaleDetails')}}',

				data: {

					id: id

				},

				success:function(res){

					if(res.error){

						$(".flash-alert .flash-msg").html(res.msg);

						$(".flash-alert").removeClass("hide").removeClass("alert-success").show().addClass("alert-danger");

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





	$(document).ready(function() {

	/*	$('#sale-table').DataTable({

			"ajax": 'getProductSaleAjax',

			"columnDefs": [{
					"targets": [ 0 ],
					"visible": false,
					"searchable": false,
				},
				{ "width": "52px", "targets": 4 }

			],

			"order": []
		});*/
	

    // Initialize DataTable with function
   /* var table = $('#sale-table').DataTable({
        "ajax": {
            "url": 'getProductSaleAjax',
            "data": function (d) {
                d.filter_value = $('#filterDropdown').val();
            }
        },
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
                "searchable": false
            },
            { "width": "52px", "targets": 4 }
        ],
        "order": []
    });*/

	var table = $('#sale-table').DataTable({
    "ajax": {
        "url": 'getProductSaleAjax',
        "data": function (d) {
            d.filter_value = $('#filterDropdown').val();
        }
    },
    "columnDefs": [
        {
            "targets": [0],
            "visible": false,
            "searchable": false
        },
        { "width": "52px", "targets": 4 }
    ],
    "order": [],
    "pageLength": 100,
    "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ]
});


    // On Change Reload Table with Updated Params
    $('#filterDropdown').on('change', function() {
        table.ajax.reload();
    });






		@if($noproduct)

			$.confirm({

					title: "No Product found",

					content: 'There are no product for sale, Please add atleast 1 product to start sale.',

					type: 'orange',

					typeAnimated: true,

					buttons: {

						addMember: {

							text: "Add Product",

							btnClass: 'btn-orange',

							action: function(){

								window.location = "{{url('ProductForm')}}";

							}

						}

					}

				});

		@endif

	})



	$("#custLocalSaleForm, #memberLocalSaleForm").submit(function (e) {

		e.preventDefault();

		console.log("erwsdfhsdkf");



		if (!this.checkValidity()) {

			console.log("erw");

			return false;

		}

		$(this).find(".SubmitBtn").attr("disabled", true);
		return false;

	});

	

</script>

@endsection