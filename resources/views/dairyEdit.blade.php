  @extends('theme.default')

@section('content')
 <!-- custome css   -->
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

<link href="{{ asset('css/addon/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/addon/tabs.css') }}" rel="stylesheet" />
<script type="text/javascript" href="{{ asset('js/addon/bootstrap.js') }}" >  </script>
<style type="text/css">
* {
  box-sizing: border-box;
}

body {
  background-color: #f1f1f1;
}

#regForm {
  background-color: #ffffff;
  margin: 100px auto;
  font-family: Raleway;
  padding: 40px;
  width: 70%;
  min-width: 300px;
}

h1
{
  color:#0337ac;
}

input {
  padding: 10px;
  width: 100%;
  font-size: 17px;
  font-family: Raleway;
  border: 1px solid #aaaaaa;
  margin-top:10px;
}

/* Mark input boxes that gets an error on validation: */
input.invalid {
  background-color: #ffdddd;
}

/* Hide all steps by default: */
.tab {
  display: none;
}

button {
  background-color: #0337ac;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
  border-radius:4px;
}

button:hover {
  opacity: 0.8;
}

#prevBtn {
  background-color: #bbbbbb;
}

/* Make circles that indicate the steps of the form: */
.step {
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbbbbb;
  border: none;  
  border-radius: 50%;
  display: inline-block;
  opacity: 0.5;
}

.step.active {
  opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
  background-color: #0337ac;
}

textarea
{
  margin-top:10px;
}


select[multiple], select[size]

{
  height:250px;
  width:300px;
}

</style>


   <form method="post" id="regForm" action="{{url('/editDairyInfoSubmit')}}">
  
  <!-- One "tabStyle" for each step in the form: -->
  <div id="step_1">
  
  
  <div class="tabStyle "  >
  
  <h1>Daily Setup Wizard > Dairy Information</h1>
  
  <h1>Few Details About Your Dairy:</h1>
    <input type="hidden" name="dairyId" id="editDairyId" value="{{ $dairy_info->id}}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <div class="col-sm-6" >
     <input placeholder="Dairy name" oninput="this.className = ''" value="{{ $dairy_info->society_name }}" name="society_name">
    </div>
     <div class="col-sm-6" >
       <input placeholder="Society Code" oninput="this.className = ''" readonly="readonly" type="text" value="{{ $dairy_info->society_code }}" class="society_code" id="society_code"  name="society_code">
    </div>
    
    <div class="col-sm-12" >
       <textarea placeholder="Address" id="dairyInfoAddressId" name="dairyInfoAddressId"   style="width:100%; height:150px;">
       {{ $dairy_info->dairyAddress }} </textarea>
    </div>
    
    <div class="col-sm-6" >
         <select name="state" id="dairyInfoState"  onchange="dairyInfogetCityByStatus()" style="width:100%; margin-top:10px; height:47px;">
                <option>--States-- </option>
                @foreach ($states as $allStates)
                    <option value=" {{$allStates->id}} ">{{$allStates->name}}  </option>
                @endforeach
            </select>
    </div>
    
    
    <div class="col-sm-6" >
       <select name="city" id="dairyInfoCity" style="width:100%; margin-top:10px; height:47px;">
                <option>-- citys --</option>
        </select>
    </div>
    
    <div class="col-sm-6" >
       <input placeholder="Village and District...." value="{{ $dairy_info->district }}" oninput="this.className = ''" name="district">
    </div>
    
    <div class="col-sm-6" >
      <input placeholder="pin code...." value="{{ $dairy_info->pincode }}" oninput="this.className = ''" name="pincode">
    </div>
    
    <div class="col-sm-12" style="text-align:left; margin-top:20px;"><h1>Dairy  Propritor Details : </h1></div>
    
    <div class="col-sm-6" >
   <input placeholder="Name..." oninput="this.className = ''" value="{{ $dairy_propritor_info->dairyPropritorName }}" name="dairyPropritorName">
    </div>
     <div class="col-sm-6" >
      <input placeholder="Mobile No...." oninput="this.className = ''" type="number" max="10"  value="{{ $dairy_propritor_info->PropritorMobile }}" name="PropritorMobile">
    </div>
      <div class="col-sm-12" >
        <span class="emailerrormessage" id="emailerrormessage"> </span>
        <input placeholder="Email...." oninput="this.className = ''" value="{{ $dairy_propritor_info->dairyPropritorEmail }}" type="email" onfocusout="CheckdairyPropritorEmail()" id="dairyPropritorEmail" class="dairyPropritorEmail" name="dairyPropritorEmail">
    </div>
    
    
    
    <div class="col-sm-6">
    <div>
       <input type="checkbox" id="sameAddressChecked" value="sameAddressChecked" onclick='getSameAddress()' name="dairyPropritorSameAddress" style="width:30px !important;"><span>same as current address</span>
    
    </div>
     
    </div>
    
    <div class="col-sm-12">
    <div>
       <input type="text" value="{{ $dairy_propritor_info->dairyPropritorAddress }}" placeholder="dairy Propritor Address" id="dairyPropritorAddress" name="dairyPropritorAddress" style="width:100%; height:150px;">
    </div>
    </div>

    
    <div class="col-sm-6" >
        <select name="dairyPropritorState" id="dairyPropritorState"  onchange="dairyPropritorCityByStatus()" style="width:100%; margin-top:10px; height:47px;">
                <option>--States-- </option>
                @foreach ($states as $allStates)
                    <option value="{{$allStates->id}}" {{ $allStates->id == $dairy_propritor_info->dairyPropritorState ? 'selected="selected"' : '' }} >{{$allStates->name}}  </option>
                @endforeach
        </select>
    </div>
    
    
    <div class="col-sm-6" >
         <select name="dairyPropritorCity" id="dairyPropritorCity" style="width:100%; margin-top:10px; height:47px;">
              <option>-- citys --</option>
           </select>
    </div>
    
    <div class="col-sm-6" >
       <input placeholder="Village and District...." value="{{ $dairy_propritor_info->dairyPropritorDistrict }}" oninput="this.className = ''" name="dairyPropritorDistrict">
    </div>
    
    <div class="col-sm-6" >
      <input placeholder="pin code...." value="{{ $dairy_propritor_info->dairyPropritorPincode }}" oninput="this.className = ''" name="dairyPropritorPincode">
    </div>
    
  
  </div>
  
  </div>

  
  
 <div style="overflow:auto; float:right; margin-top:20px; clear:both;">
  
  <div style="float:right;">
      <!-- <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button> -->
      <button type="button" id="nextBtn" onclick="nextPrev(1)">Submit</button>
    </div>
   
  </div>
  <!-- Circles which indicates the steps of the form: -->
  <div style="text-align:center;margin-top:40px;">
    <span class="step" style="margin-top:10px;"></span>
    <span class="step"></span>
    <span class="step"></span>
  

  </div>
 
</form>


@endsection

<script type="text/javascript">

var currenttabStyle = 0; // Current tabStyle is set to be the first tabStyle (0)
showtabStyle(currenttabStyle); // Display the crurrent tabStyle

function showtabStyle(n) {
  // This function will display the specified tabStyle of the form...
  var x = document.getElementsByClassName("tabStyle");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    $("#step_1").show();
    $("#step_2").hide();
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
   document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    $("#step_1").show();
    $("#step_2").hide();
    document.getElementById("nextBtn").innerHTML = "Next";

  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tabStyle to display
  var x = document.getElementsByClassName("tabStyle");
  // Exit the function if any field in the current tabStyle is invalid:
  if (n == 1 && !validateForm()){

    return false;
  }else{
    $("#step_2").show();
    $("#step_1").hide();
    // Hide the current tabStyle:
  }
  x[currenttabStyle].style.display = "none";
  // Increase or decrease the current tabStyle by 1:
  currenttabStyle = currenttabStyle + n;
  // if you have reached the end of the form...
  if (currenttabStyle >= x.length) {
    // ... the form gets submitted:
 
  document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tabStyle:
  showtabStyle(currenttabStyle);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tabStyle");
  y = x[currenttabStyle].getElementsByTagName("input");
  // A loop that checks every input field in the current tabStyle:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
   // add an "invalid" class to the field: 
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currenttabStyle].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}

    
  /* chack email is valid or not */
  function CheckdairyPropritorEmail(){
     
      var dairyPropritorEmail  = document.getElementById("dairyPropritorEmail").value;
      var dairyId  = document.getElementById("editDairyId").value;
     
      var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        var filterReturn = filter.test(dairyPropritorEmail);
        if(filterReturn == true){
            $.ajax({
               type:"GET",
               url:"{{url('/editDairyEmailValidation')}}?dairyPropritorEmail="+dairyPropritorEmail+"&dairyId="+dairyId ,
               success:function(res){               
                if(res == "true"){
                  document.getElementById("emailerrormessage").innerHTML = "This email is already being used.";
                  document.getElementById("dairyPropritorEmail").focus();
                }else if(res == "false"){
                  document.getElementById("emailerrormessage").innerHTML = "";
                }
               }
            });
        }else{
            document.getElementById("emailerrormessage").innerHTML = "This email is not valid.";
            document.getElementById("dairyPropritorEmail").focus();
        }
      
    }
    /* get city by state id */
    function dairyPropritorCityByStatus(){
         var stateID = $("#dairyPropritorState").val();    
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){               
                if(res){
                    $("#dairyPropritorCity").empty();
                    $.each(res,function(key,value){
                        $("#dairyPropritorCity").append('<option value="'+key+'">'+value['name']+'</option>');
                    });
               
                }else{
                   $("#dairyPropritorCity").empty();
                }
               }
            });
        }else{
            $("#dairyPropritorCity").empty();
        }
    }
 
</script>
