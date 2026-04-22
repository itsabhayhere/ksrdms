 @extends('theme.default')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
		
		<a class="nav-back" href="customerList" title="Back to Customer list">
			<i class="fa fa-angle-left"></i>&nbsp; 
			{{-- <span class="sub">Back to Member List</span> --}}
		</a>

		<div class="fcard mt-30 clearfix">
			<div class="heading">
				<h3>Update Customer Details (<b>{{ $CustomerEdit->customerCode }}</b>) </h4>
			</div>
			<form method="post" action="{{ url('/customerEditSubmit') }}">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="dairyId" id="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
			<input type="hidden" name="customerId" id="customerId" value="{{ $CustomerEdit->id }}">
			<input type="hidden" name="status" value="true">
			<div class="col-sm-6"> 
				<label>Customer Code</label>
				  <span class="customerCodeErrorMessage errorMessage" id="customerCodeErrorMessage"> </span>
				<input type="text" class="form-control" id="customerCode" readonly="readonly" value="{{ $CustomerEdit->customerCode }}"  placeholder="Enter Name" name="customerCode">
			</div>
			<div class="col-sm-6"> 
				<label>Customer Name</label>
				<input type="text" class="form-control" id="customerName" value="{{ $CustomerEdit->customerName }}" placeholder="Enter Father Name" name="customerName">
			</div>

			<div class="col-sm-12"> 
				<div class="col-sm-1"> 
					<label> Gender </label>
				</div>
				<div class="col-sm-3"> 
					<input type="radio" {{ $CustomerEdit->gender == 'male' ? 'checked' : '' }}  name="gender" value="male"> Male 
					<input type="radio" {{ $CustomerEdit->gender == 'female' ? 'checked' : '' }} name="gender" value="female"> Female 
				</div>
					<div class="col-sm-8"> 
					
				</div>
			</div>

			<div class="col-sm-6">
				<label>Email</label>
				 
				<input type="email" name="customerEmail" value="{{ $CustomerEdit->customerEmail }}" class="form-control"  id="customerEmail" placeholder="Enter Email">
			</div>

		
			<div class="col-sm-6"> 
				<label>Mobile Number</label>
				<span class="customerNumberErrorMessage errorMessage" id="customerNumberErrorMessage"> </span>
				<input type="number" class="form-control" value="{{ $CustomerEdit->customerMobileNumber }}" pattern="\d{3}[\-]\d{3}[\-]\d{4}" onfocusout="CheckcustomerEmail()" id="customerMobileNumber" placeholder="Enter Mobile Number" name="customerMobileNumber">
			</div>

			<div class="col-sm-6"> </div>
			<div class="col-sm-12"> 
				<label> Address </label>
				<textarea class="form-control"  id="customerAddress" name="customerAddress">{{ $CustomerEdit->customerAddress }}</textarea>
			</div>
			<div class="col-sm-6"> 
				<label>State</label>
				<select onchange="FunMemberPersonalState()" name="customerState" id="customerState" class="selectpicker">
	                @foreach ($states as $allStates)
	                    <option value=" {{$allStates->id}} " {{ $allStates->id == $CustomerEdit->customerState ? 'selected="selected"' : '' }} >{{$allStates->name}}  </option>
	                @endforeach
				</select>
			</div>
			<div class="col-sm-6"> 
				<label>City</label>
				<select name="customerCity" class="selectpicker" id="customerCity">
					<option value="{{ $CustomerEdit->city }}">{{ $CustomerEdit->customerCity }}</option>
				</select>
			</div>
			
			<div class="col-sm-6"> 
				<label>Village/District</label>
				<input type="text" value="{{ $CustomerEdit->customerVillageDistrict }}" class="form-control" id="customerVillageDistrict" placeholder="Enter Village/District" name="customerVillageDistrict">
			</div>
			<div class="col-sm-6"> 
				<label>Pin code </label>
				<input type="text" class="form-control" value="{{ $CustomerEdit->customerPincode }}" id="customerPincode" placeholder="Enter PinCode" name="customerPincode">
			</div>
			<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
				<div class="pt-10"></div>
				<button type="submit"  class="btn btn-primary btn-block supplierSubmit">Submit</button>
			</div>
			
		</div>
 
    </form>
</div>
</div>
<script type="text/javascript">
	function FunMemberPersonalState(){
       var stateID = $("#customerState").val();    
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){               
                if(res){
                    $("#customerCity").empty();
                    $.each(res,function(key,value){
                        $("#customerCity").append('<option value="'+value['id']+'">'+value['name']+'</option>');
                    });
					$("#customerCity").selectpicker("refresh");
                }else{
                   $("#customerCity").empty();
                }
               }
            });
        }else{
            $("#customerCity").empty();
        }
    }

 /* supplier code validation */
 function CheckcustomerCode(){
    var supplior_code  = document.getElementById("customerCode").value;

     if(supplior_code){
         $.ajax({
               type:"GET",
               url:"{{url('/checkcustomerCode')}}?supplior_code="+supplior_code ,
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

  /* Supplier email validation  */
  function CheckcustomerEmail(){
 
	var mobileNumber  = document.getElementById("customerMobileNumber").value;
	var CustomerId  = document.getElementById("customerId").value;
		$.ajax({
          type:"GET",
          url:"{{url('/customerEditEmailValidation')}}?mobileNumber="+mobileNumber+"&CustomerId="+CustomerId ,
          success:function(res){       
             console.log(res);   
           if(res == "true"){
             document.getElementById("customerNumberErrorMessage").innerHTML = "This number is already being used.";
             document.getElementById("customerMobileNumber").focus();
           }else if(res == "false"){
             document.getElementById("customerNumberErrorMessage").innerHTML = "";
           }
          }
        });
     
  }

</script>
@endsection