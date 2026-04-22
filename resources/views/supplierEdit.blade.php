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
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
		
		<a class="nav-back" href="supplierList" title="Back to Supplier list">
			<i class="fa fa-angle-left"></i>&nbsp; 
			{{-- <span class="sub">Back to Member List</span> --}}
		</a>

		<div class="fcard mt-30 clearfix">
			<div class="heading">
				<h3>Update Supplier Details ( <b>{{ $supplierEdit->supplierCode }}</b> )</h4>
			</div>
			<form method="post" action="{{ url('/supplierEditSubmit') }}" class="clearfix">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dairyId" id="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
				<input type="hidden" name="supplierId" id="supplierId" value="{{ $supplierEdit->id }}">
				<input type="hidden" name="status" value="true">
				<div class="col-sm-6"> 
					<label>Supplier Code</label>
					<span class="supplierCodeErrorMessage errorMessage" id="supplierCodeErrorMessage"> </span>
					<input type="text" class="form-control" id="supplierCode" readonly="readonly" value="{{ $supplierEdit->supplierCode }}"  placeholder="Enter Name" name="supplierCode">
				</div>
				<div class="col-sm-6"> 
					<label>Supplier Firm Name</label>
					<input type="text" class="form-control" id="supplierFatherName" value="{{ $supplierEdit->supplierFirmName }}" placeholder="Enter Father Name" name="supplierFirmName">
				</div>
				<div class="col-sm-6"> 
					<label>Person Name</label>
					<input type="text" class="form-control" id="supplierPersonName" value="{{ $supplierEdit->supplierPersonName }}" placeholder="Enter Name" name="supplierPersonName">
				</div>
				<div class="col-sm-6">
					<label>Email</label>
					<input type="email" name="supplierEmail" value="{{ $supplierEdit->supplierEmail }}" class="form-control"  id="supplierEmail" placeholder="Enter Email">
				</div>
				<div class="col-sm-12"> 
						<div class="col-sm-1"> 
							<label> Gender </label>
						</div>
						<div class="col-sm-3"> 
							<input type="radio" {{ $supplierEdit->gender == 'male' ? 'checked' : '' }}  name="gender" value="male"> Male 
							<input type="radio" {{ $supplierEdit->gender == 'female' ? 'checked' : '' }} name="gender" value="female"> Female 
						</div>
							<div class="col-sm-8"> 
							
						</div>
				</div>
			
				<div class="col-sm-6"> 
					<label>Mobile Number</label>
					<span class="supplierMobileNumberErrorMessage errorMessage" id="supplierMobileNumberErrorMessage"> </span>
					<input type="number" class="form-control" onfocusout="CheckSupplierMobileNumber()" value="{{ $supplierEdit->supplierMobileNumber }}" id="supplierMobileNumber" placeholder="Enter Mobile Number" name="supplierMobileNumber">
				</div>
				<div class="col-sm-6"> 
					<label>Gstin</label>
					<input type="text" name="supplierGstin" value="{{ $supplierEdit->supplierGstin }}" class="form-control" id="supplierGstin" placeholder="Enter Gstin">
				</div>
				<div class="col-sm-6"> </div>
				<div class="col-sm-12"> 
					<label> Address </label>
					<textarea class="form-control"  id="supplierAddress" name="supplierAddress" value="">{{ $supplierEdit->supplierAddress }}</textarea>
				</div>
				<div class="col-sm-6"> 
					<label>State</label>
					<select onchange="FunMemberPersonalState()" name="supplierState" id="supplierState" class="form-control">
						<option>--States-- </option>
						@foreach ($states as $allStates)
							<option value=" {{$allStates->id}} " {{ $allStates->id == $supplierEdit->supplierState ? 'selected="selected"' : '' }} >{{$allStates->name}}  </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm-6"> 
					<label>City</label>
					<select name="supplierCity" class="form-control" id="supplierCity">
					<option value="--City--">  {{ $supplierEdit->supplierCity }} </option>
					</select>
				</div>
				
				<div class="col-sm-6"> 
					<label>Village/District</label>
					<input type="text" value="{{ $supplierEdit->supplierVillageDistrict }}" class="form-control" id="supplierVillageDistrict" placeholder="Enter Village/District" name="supplierVillageDistrict">
				</div>
				<div class="col-sm-6"> 
					<label>Pin code </label>
					<input type="text" class="form-control" value="{{ $supplierEdit->supplierPincode }}" id="supplierPincode" placeholder="Enter PinCode" name="supplierPincode">
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
                    });
               
                }else{
                   $("#supplierCity").empty();
                }
               }
            });
        }else{
            $("#supplierCity").empty();
        }
    }

 /* supplier code validation */
 function CheckSupplierCode(){
    var supplior_code  = document.getElementById("supplierCode").value;

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
               }
            });
      }
  }

  /* Supplier email validation  */
  function CheckSupplierMobileNumber(){
 
	var supplierMobileNumber  = document.getElementById("supplierMobileNumber").value;
	var supplierId  = document.getElementById("supplierId").value;

         $.ajax({
               type:"GET",
               url:"{{url('/supplierEditEmailValidation')}}?supplierMobileNumber="+supplierMobileNumber+"&supplierId="+supplierId ,
               success:function(res){  
                       
                if(res == "true"){
                
                  document.getElementById("supplierMobileNumberErrorMessage").innerHTML = "This mobile number is already being used.";
                  document.getElementById("supplierMobileNumber").focus();
                }else if(res == "false"){
                  console.log(res);
                  document.getElementById("supplierMobileNumberErrorMessage").innerHTML = "";
                }
               }
            });
    
  }

</script>
@endsection