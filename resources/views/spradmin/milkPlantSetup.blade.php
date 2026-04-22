@extends('spradmin.layout')

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
		<div class="fcard mt-30 clearfix">
			<div class="heading">
				<h3>New Milk Plant Setup</h4>
			</div>
			<form method="post" action="{{ url('sa/newMilkPlantAdd') }}" class="clearfix">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
				<input type="hidden" name="status" value="true">

				<div class="col-md-12">
					<label for="isMainPlant">
						<input type="checkbox" value="1" class="" id="isMainPlant"
						@if(old('isMainPlant') == "1") checked @endif name="isMainPlant" onchange="isMainPlant1()">
						Main Plant
					</label>
				</div>

				<div id="selectMainPlantArea" class="clearfix @if(old('isMainPlant') == "1") dnone @endif ">
					<div class="col-sm-6"> 
						<label>Select Milk Plant</label>
						<select name="mainPlant" id="mainPlant" class="selectpicker" data-live-search="true" @if(old('isMainPlant') != "1") required @endif>
							@foreach ($mainPlants as $m)
								<option value="{{$m->id}}" @if(old('milkPlant') == $m->id) selected @endif>{{$m->plantName}}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-sm-6"> 
					<label>Plant Name</label>
					<input type="text" value="{{ old('plantName') }}" class="form-control" id="plantName" placeholder="Enter Plant Name" name="plantName">
				</div>
				<div class="col-sm-6"> 
					<label>Contact Number</label>
					<span class="numberErrorMessage errorMessage" id="numberErrorMessage"> </span>
					<input type="number" value="{{ old('contactNumber') }}"   class="form-control" onfocusout="CheckContactNumber()"  id="contactNumber" placeholder="Enter Contact Number" name="contactNumber">
				</div>
				<div class="col-sm-12"> 
					<label> Address </label>
					<textarea class="form-control"  placeholder="Enter Address" id="address" name="address">{{ old('address') }}</textarea>
				</div>

				<div class="col-sm-6"> 
					<label>State</label>
					<select onchange="FunMemberPersonalState()" name="state" id="state" class="selectpicker" data-live-search="true" >
						@foreach ($states as $allStates)
							<option value="{{$allStates->id}}" @if(old('state') == $allStates->id) selected @endif>{{$allStates->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-sm-6"> 
					<label>City</label>
					<select name="city" class="selectpicker" id="city" data-live-search="true">
						
					</select>
				</div>
				
				<div class="col-sm-6"> 
					<label>Pin code </label>
					<input type="text" class="form-control" value="{{ old('pinCode') }}" id="district" placeholder="Enter PinCode" name="pinCode">
				</div>

				<div class="clearfix"></div>
				<br>

				<h3> Plant Head Details </h3>
				<div class="col-sm-6"> 
					<label> Plant Head Name </label>
					<input type="text" class="form-control" value="{{ old('plantHeadName') }}" id="plantHeadName" placeholder="Enter Head Name " name="plantHeadName">
				</div>
				
				<div class="col-sm-6"> </div>

				<div class="col-sm-12"> 
						<div class="col-sm-1"> 
							<label> Gender </label>
						</div>
						<div class="col-sm-3"> 
							<label><input type="radio" name="sex" value="male" @if(old('sex') == "male") checked @endif> Male </label>

							<label><input type="radio" name="sex" value="female" @if(old('sex') == "female") checked @endif> Female </label>
						</div>
						<div class="col-sm-8"> 
							
						</div>
				</div>
				<div class="col-sm-6">
					<label>Email</label>
					<input type="email" name="email" value="{{ old('email') }}" class="form-control" id="email" placeholder="Enter Email">
				</div>
			
				<div class="col-sm-6"> 
					<label>Mobile Number</label>
					<span class="mobileNumberErrorMessage errorMessage" id="mobileNumberErrorMessage"> </span>
					<input type="number" value="{{ old('mobileNumber') }}" class="form-control" id="supplierMobileNumber" onfocusout="CheckEmail()"  placeholder="Enter Mobile Number" name="mobileNumber">
				</div>

				<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
					<div class="pt-10"></div>
					<button type="submit" class="btn btn-primary btn-block milkPlantSubmit">Add Milk Plant</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">

	function isMainPlant1(){
		if($("#isMainPlant").prop("checked")){
			$("#selectMainPlantArea").slideUp();
			$("#mainPlant").prop('required', false);
		}else{
			$("#selectMainPlantArea").slideDown();
			$("#mainPlant").prop('required', true);
		}
	}

	function FunMemberPersonalState(){
       var stateID = $("#state").val();    
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){               
                if(res){
                    $("#city").empty();
                    $.each(res,function(key,value){
                        $("#city").append('<option value="'+key+'">'+value['name']+'</option>');
                    });
					$("#city").selectpicker('refresh');
                }else{
                   $("#city").empty();
                }
               }
            });
        }else{
            $("#city").empty();
        }
    }

	FunMemberPersonalState();

  /* milk plant email validation  */
  function CheckEmail(){
 
	var mobileNumber  = document.getElementById("supplierMobileNumber").value;
	
              
	if(mobileNumber){
         $.ajax({
                type: 'post',
                url: 'checkMilkPlantEmail',
                data: {
                    mobileNumber: mobileNumber,
                },
               success:function(res){    
                if(res == "true"){
				  document.getElementById("mobileNumberErrorMessage").innerHTML = "This mobile number is already being used.";
                  document.getElementById("supplierMobileNumber").focus();
                }else if(res == "false"){
                  document.getElementById("mobileNumberErrorMessage").innerHTML = "";
                }
               }
            });
      }
  }

/* check contact number */
function CheckContactNumber(){
	var contactNumber  = document.getElementById("contactNumber").value;

	if(contactNumber){
         $.ajax({
                type: 'post',
                url: 'checkMilkPlantContactNumberValidation',
                data: {
                    contactNumber: contactNumber,
                },
               success:function(res){    
                if(res == "true"){
				  document.getElementById("numberErrorMessage").innerHTML = "This mobile number is already being used.";
                  document.getElementById("contactNumber").focus();
                }else if(res == "false"){
                  document.getElementById("numberErrorMessage").innerHTML = "";
                }
               }
            });
      }
}
</script>
@endsection