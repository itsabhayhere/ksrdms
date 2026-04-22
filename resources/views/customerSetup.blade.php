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

        <a class="nav-back" href="customerList" title="Back to Customer list">
            <i class="fa fa-angle-left"></i>&nbsp; 
            {{-- <span class="sub">Back to Member List</span> --}}
        </a>
        
		<div class="fcard mt-30 clearfix">
            <div class="heading">
                <h3>New Customer Setup</h4>
            </div>
			<form method="post" action="{{ url('/customerSubmit') }}" class="clearfix">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
                <input type="hidden" name="status" value="true">
                <div class="col-sm-6"> 
                    <label>Customer Code</label>
                    <span class="customerCodeErrorMessage errorMessage" id="customerCodeErrorMessage"> </span>
					<div class="input-group">
						<span class="input-group-addon">{{ Session::get('loginUserInfo')->dairyId }}C</span>
						<input type="hidden" name="customerCodePrefix" id="customerCodePrefix" value="{{ Session::get('loginUserInfo')->dairyId }}C">
                        <input type="text" value="{{ old('customerCode') }}" class="form-control" id="customerCode" onfocusout="CheckCustomerCode()" placeholder="Enter Code" name="customerCode" aria-describedby="basic-addon1">
					</div>

                </div>
                <div class="col-sm-6"> 
                    <label>Customer Name</label>
                    <input type="text" value="{{ old('customerName') }}" class="form-control" id="customerName" placeholder="Enter Name" name="customerName">
                </div>

                <div class="col-sm-12"> 
                        <div class="col-sm-1"> 
                            <label> Gender </label>
                        </div>
						<div class="col-sm-2">
                            <label class="rdolb lh-25"><input type="radio" checked="checked" name="gender" value="male"> Male </label>
                        </div>
                        <div class="col-sm-2">
                            <label class="rdolb lh-25"><input type="radio" name="gender" value="female"> Female </label>
                        </div>
                </div>

                <div class="col-sm-6">
                    <label>Email</label>
                    
                    <input type="email" value="{{ old('customerEmail') }}" name="customerEmail" class="form-control"  id="customerEmail" placeholder="Enter Email">
                </div>
                <div class="col-sm-6"> 
                    <label>Mobile Number</label>
            <span class="customerNumberErrorMessage errorMessage" id="customerNumberErrorMessage"> </span>
                    <input type="number" value="{{ old('customerMobileNumber') }}"  class="form-control" id="customerMobileNumber" onfocusout="CheckcustomerEmail()" placeholder="Enter Mobile Number" name="customerMobileNumber">
                </div>

                <div class="col-sm-6"> </div>
                <div class="col-sm-12"> 
                    <label> Address </label>
                    <textarea class="form-control"  id="customerAddress" name="customerAddress">{{ old('customerAddress') }}</textarea>
                </div>
                <div class="col-sm-6"> 
                    <label>State</label>
                    <select onchange="FunMemberPersonalState()" name="customerState" id="customerState" class="form-control">
                        <option>--States-- </option>
                        @foreach ($states as $allStates)
                            <option value=" {{$allStates->id}} ">{{$allStates->name}}  </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6"> 
                    <label>City</label>
                    <select name="customerCity" class="form-control" id="customerCity">
                    
                    </select>
                </div>
                
                <div class="col-sm-6"> 
                    <label>Village/District</label>
                    <input type="text" class="form-control" value="{{ old('customerVillageDistrict') }}" id="customerVillageDistrict" placeholder="Enter Village/District" name="customerVillageDistrict">
                </div>
                <div class="col-sm-6"> 
                    <label>Pin code </label>
                    <input type="number"  class="form-control" id="customerPincode" value="{{ old('customerPincode') }}" placeholder="Enter PinCode" name="customerPincode">
                </div>
                
                <div class="col-sm-6">
                    <label>Opening balance Details</label>
                    <input type="number" placeholder="Customer Opening Balance" value="{{ old('customerOpeningBalance') }}" class="customerOpeningBalance form-control" id="customerOpeningBalance" name="customerOpeningBalance">
                </div>
                <div class="col-sm-6">
                    <label>Opening balance Type</label>
                    <select name="openingBalanceType" class="openingBalanceType form-control" value="{{ old('openingBalanceType') }}" name="openingBalanceType" id="openingBalanceType">
                    <option value="credit">Credit</option>
                    <option value="debit">Debit</option>
                    </select>
                </div>
        
                <div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
                    <div class="pt-10"></div>
                    <button type="submit" class="btn btn-primary btn-block customerSubmit">Add Customer</button>
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
                        $("#customerCity").append('<option value="'+key+'">'+value['name']+'</option>');
                    });
               
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
 function CheckCustomerCode(){
    var customer_code  = document.getElementById("customerCodePrefix").value + document.getElementById("customerCode").value;

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

  /* Supplier email validation  */
  function CheckcustomerEmail(){
 
	var mobileNumber  = document.getElementById("customerMobileNumber").value;

        $.ajax({
            type:"GET",
            url:"{{url('/checkCustomerEmail')}}?mobileNumber="+mobileNumber ,
            success:function(res){          
            if(res == "true"){
                document.getElementById("customerNumberErrorMessage").innerHTML = "This Number is already being used.";
                document.getElementById("customerMobileNumber").focus();
            }else if(res == "false"){
                document.getElementById("customerNumberErrorMessage").innerHTML = "";
            }
            }
        });   
    }

</script>
@endsection