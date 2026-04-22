@extends('theme.default')

@section('content')


<style type="text/css">

.col-sm-12 {
    margin-top: 12px;
}
.col-sm-6 {
    margin-top: 12px;
}
.errorMessage {
	color: red;
}
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 
}
</style>
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
	
		<a class="nav-back" href="supplierList" title="Back to Supplier list">
			<i class="fa fa-angle-left"></i>&nbsp; 
			{{-- <span class="sub">Back to Member List</span> --}}
		</a>
		
		<div class="fcard mt-30">
			<div class="heading">
				<h3>New Supplier Setup </h4>
			</div>
			<form method="post" action="{{ url('/supplierSubmit') }}" class="clearfix">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
				<input type="hidden" name="status" value="true">
				<div class="col-sm-6"> 
					<label>Supplier Code</label>
					<span class="supplierCodeErrorMessage errorMessage" id="supplierCodeErrorMessage"> </span>
					<div class="input-group">
						<span class="input-group-addon">{{ Session::get('loginUserInfo')->dairyId }}S</span>
						<input type="hidden" name="supplierCodePrefix" id="supplierCodePrefix" value="{{ Session::get('loginUserInfo')->dairyId }}S">
						<input type="text" value="{{ old('supplierCode') }}" class="form-control" id="supplierCode" onfocusout="CheckSupplierCode()" placeholder="Enter new code for supplier" name="supplierCode" aria-describedby="basic-addon1">
					</div>
				</div>
				<div class="col-sm-6">
					<label>Supplier Firm Name</label>
					<input type="text" value="{{ old('supplierFirmName') }}" class="form-control" id="supplierFatherName" placeholder="Enter Firm Name" name="supplierFirmName">
				</div>
				<div class="col-sm-6"> 
					<label>Person Name</label>
					<input type="text" value="{{ old('supplierPersonName') }}" class="form-control" value="{{ old('supplierPersonName') }}" id="supplierPersonName" placeholder="Enter Name" name="supplierPersonName">
				</div>
				<div class="col-sm-6">
					<label>Email</label>
					
					<input type="email" value="{{ old('supplierEmail') }}" name="supplierEmail" class="form-control"  id="supplierEmail" placeholder="Enter Email">
				</div>
				<div class="col-sm-12"> 
						<div class=""> 
							<label> Gender </label>
						</div>
						<div class="col-sm-2">
							<label class="rdolb lh-25"><input type="radio" checked="checked" name="gender" value="male"> Male </label>
						</div>
						<div class="col-md-2">
							<label class="rdolb lh-25"><input type="radio" name="gender" value="female"> Female </label>
						</div>
						<div class="col-sm-8"> 
						</div>
				</div>
			
				<div class="col-sm-6"> 
					<label>Mobile Number <small>(enter 10 digits)</small></label>
					<span class="supplierMobileNumberErrorMessage errorMessage" id="supplierMobileNumberErrorMessage"> </span>
					<input type="number"  class="form-control" value="{{ old('supplierMobileNumber') }}" id="supplierMobileNumber" onfocusout="CheckSupplierMobileNumber()" placeholder="Enter Mobile Number" name="supplierMobileNumber" required min="3999999999" max="9999999999">
				</div>
				<div class="col-sm-6"> 
					<label>Gstin</label>
					<input type="text" name="supplierGstin" value="{{ old('supplierGstin') }}" class="form-control" id="supplierGstin" placeholder="Enter Gstin">
				</div>
				<div class="col-sm-6"> </div>
				<div class="col-sm-12">
					<label> Address </label>
					<textarea class="form-control" id="supplierAddress" name="supplierAddress">{{ old('supplierAddress') }}</textarea>
				</div>
				<div class="col-sm-6"> 
					<label>State</label>
					<select onchange="FunMemberPersonalState()" name="supplierState" id="supplierState" data-live-search="true" class="selectpicker" title="Select State" required>
						{{-- <option>--States-- </option> --}}
						@foreach ($states as $allStates)
							<option value=" {{$allStates->id}} ">{{$allStates->name}}  </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm-6"> 
					<label>City</label>
					<select name="supplierCity" class="selectpicker" id="supplierCity" title="Select city">
					</select>
				</div>
				
				<div class="col-sm-6"> 
					<label>Village/District</label>
					<input type="text" class="form-control" value="{{ old('supplierVillageDistrict') }}" id="supplierVillageDistrict" placeholder="Enter Village/District" name="supplierVillageDistrict">
				</div>
				<div class="col-sm-6">
					<label>Pin code </label>
					<input type="text" class="form-control" value="{{ old('supplierPincode') }}" id="supplierPincode" placeholder="Enter PinCode" name="supplierPincode">
				</div>
				
				<div class="col-sm-6">
					<label>Opening balance</label>
					<input type="number" value="{{ old('openingBalance') }}" placeholder="Customer Opening Balance" class="customerOpeningBalance form-control" id="customerOpeningBalance" name="openingBalance" required>
				</div>
				<div class="col-sm-6">
					<label>Opening balance Type</label>
					<select name="openingBalanceType" class="openingBalanceType selectpicker" name="openingBalanceType" id="openingBalanceType" required>
						<option value="credit">Credit</option>
						<option value="debit">Debit</option>
					</select>
				</div>

				<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
					<div class="pt-10"></div>
					<button type="submit" class="btn btn-primary btn-block supplierSubmit">Submit</button>
				</div>
				
			</div>
	
		</form>
	</div>
</div>
<script type="text/javascript">

	function FunMemberPersonalState(){
		console.log(0);
       var stateID = $("#supplierState").val();
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){
                if(res){
                    $("#supplierCity").empty();
                    $.each(res,function(key,value){
                        $("#supplierCity").append('<option value="'+key+'">'+value['name']+'</option>');
						$("#supplierCity").selectpicker("refresh");
					});

               
                }else{
                   $("#supplierCity").empty();
				}
				console.log(res);				
			   },
			   error:function(data){
				   console.log(data);
			   }
            });
        }else{
            $("#supplierCity").empty();
        }
    }

 /* supplier code validation */
 function CheckSupplierCode(){
    var supplior_code  = document.getElementById("supplierCodePrefix").value + document.getElementById("supplierCode").value;

	if(supplior_code){
		$.ajax({
				type:"GET",
				url:"{{url('/checkSupplierCode')}}?supplior_code="+supplior_code ,
				success:function(res){          
				if(res == "true"){
					document.getElementById("supplierCodeErrorMessage").innerHTML = "This code is already being used.";
					document.getElementById("supplierCode").focus();
				}else if(res == "false"){
					document.getElementById("supplierCodeErrorMessage").innerHTML = "";
				}
				console.log(res);
				},
				error:function(res){
					console.log(res);
				}
			});
		}
	}

  /* Supplier email validation  */
  function CheckSupplierMobileNumber(){
 
	var supplierMobileNumber  = document.getElementById("supplierMobileNumber").value;

    if(supplierMobileNumber){
         $.ajax({
               type:"GET",
               url:"{{url('/checkSupplierEmail')}}?supplierMobileNumber="+supplierMobileNumber ,
               success:function(res){          
                if(res == "true"){
				  document.getElementById("supplierMobileNumberErrorMessage").innerHTML = "This mobile number is already being used.";
                  document.getElementById("supplierMobileNumber").focus();
                }else if(res == "false"){
                  document.getElementById("supplierMobileNumberErrorMessage").innerHTML = "";
                }
					console.log(res);
               },
			   error:function(res){
				   console.log(res);
			   }

            });
      }
  }

</script>
@endsection