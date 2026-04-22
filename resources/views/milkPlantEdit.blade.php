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

</style>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
		<div class="fcard mt-30 clearfix">
			<div class="heading">
				<h3>Update Milk Plant Details ( <b>{{ $milkPlant->plantName }}</b> )</h4>
			</div>
			<form method="post" action="{{ url('/milkPlantEditSubmit') }}" class="clearfix">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
				<input type="hidden" name="status" value="true">
				<input type="hidden" name="milkPlantId" id="milkPlantId" value="{{ $milkPlant->id }}">
				
				<div class="col-sm-6"> 
					<label>Plant Name</label>
					<input type="text" class="form-control" id="plantName" value="{{ $milkPlant->plantName }}" placeholder="Enter Plant Name" name="plantName">
				</div>
				<div class="col-sm-6"> 
					<label>Contact Number</label>
					<span class="contactNumberErrorMessage errorMessage" id="contactNumberErrorMessage"> </span>
					<input type="text" class="form-control" max="10"  id="contactNumber" onfocusout="CheckContactNumber()" value="{{ $milkPlant->contactNumber }}" placeholder="Enter Contact Number" name="contactNumber">
				</div>
				<div class="col-sm-12"> 
					<label> Address </label>
					<textarea class="form-control" placeholder="Enter Address" id="address" name="address">{{ $milkPlant->address }}</textarea>
				</div>

				<div class="col-sm-6"> 
					<label>State</label>
					<select onchange="FunMemberPersonalState()" name="state" id="state" class="form-control">
						<option>--States-- </option>
						@foreach ($states as $allStates)
							<option value=" {{$allStates->id}} "  {{ $allStates->id == $milkPlant->state ? 'selected="selected"' : '' }} >{{$allStates->name}}  </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm-6"> 
					<label>City</label>
					<select name="city" class="form-control" id="city">
					<option value="--City--">{{ $milkPlant->city }}</option>
					</select>
				</div>
				
				<div class="col-sm-6"> 
					<label>Village/District</label>
					<input type="text" class="form-control" id="district" value="{{ $milkPlant->district }}" placeholder="Enter Village/District" name="district">
				</div>
				<div class="col-sm-6"> 
					<label>Pin code </label>
					<input type="text" class="form-control" id="pinCode" value="{{ $milkPlant->pincode }}" placeholder="Enter PinCode" name="pinCode">
				</div>


				<div class="col-md-12">
					<div class="pt-40"></div>
				</div>

				<h3> Plant Head Details </h3>

				<div class="col-sm-6"> 
					<label> Plant Head Name </label>
					<input type="text" class="form-control" id="plantHeadName" value="{{ $milkPlant->plantHeadName }}" placeholder="Enter Head Name " name="plantHeadName">
				</div>
				
				<div class="col-sm-6"> </div>

				<div class="col-sm-12"> 
						<div class="col-sm-1"> 
							<label> Gender </label>
						</div>
						<div class="col-sm-3"> 
							<input type="radio" {{ $milkPlant->sex == 'male' ? 'checked' : '' }} name="sex" value="male"> Male 
							<input type="radio" {{ $milkPlant->sex == 'female' ? 'checked' : '' }} name="sex" value="female"> Female 
						</div>
							<div class="col-sm-8"> 
							
						</div>
				</div>
				<div class="col-sm-6">
					<label>Email</label>
					
					<input type="email" name="email" class="form-control" value="{{ $milkPlant->email }}"  id="email" placeholder="Enter Email">
				</div>
			
				<div class="col-sm-6"> 
					<label>Mobile Number</label>
					<span class="emailErrorMessage errorMessage" id="emailErrorMessage"> </span>
					<input type="text" class="form-control" value="{{ $milkPlant->mobile }}" max="10"  onfocusout="CheckEmail()" id="supplierMobileNumber" placeholder="Enter Mobile Number" name="mobileNumber">
				</div>

				<div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">	
					<div class="pt-10"></div>
					<button type="submit" class="btn btn-primary btn-block milkPlantSubmit">Submit</button>
				</div>

			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
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
               
                }else{
                   $("#city").empty();
                }
               }
            });
        }else{
            $("#city").empty();
        }
    }

  /* milk plant email validation  */
  function CheckEmail(){
 
	var mobileNumber  = document.getElementById("supplierMobileNumber").value;
	var milkPlantId  = document.getElementById("milkPlantId").value;
              
	if(mobileNumber){
         $.ajax({
                type: 'post',
                url: 'milkPlantEditEmail',
                data: {
                    mobileNumber: mobileNumber,
                    milkPlantId: milkPlantId,
                },
                success:function(res){   
                if(res == "true"){
				  document.getElementById("emailErrorMessage").innerHTML = "This number is already being used.";
                  document.getElementById("supplierMobileNumber").focus();
                }else if(res == "false"){
                  document.getElementById("emailErrorMessage").innerHTML = "";
                }
               }
            });
      }
  }

  /* contact number validation */
  function CheckContactNumber(){

  	var contactNumber  = document.getElementById("contactNumber").value;
	var milkPlantId  = document.getElementById("milkPlantId").value;
    
    if(contactNumber){
         $.ajax({
                type: 'post',
                url: 'milkPlantEditContactNumber',
                data: {
                    contactNumber: contactNumber,
                    milkPlantId: milkPlantId,
                },
                success:function(res){   

                if(res == "true"){
				  document.getElementById("contactNumberErrorMessage").innerHTML = "This content number is already being used.";
                  document.getElementById("contactNumber").focus();
                }else if(res == "false"){
                  document.getElementById("contactNumberErrorMessage").innerHTML = "";
                }
               }
            });
      }
  }

</script>
@endsection