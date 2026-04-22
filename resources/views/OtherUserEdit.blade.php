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
<!-- <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> -->
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


.errorMessage {
	color: red;
}

.otherUserSubmit{
	background-color: #0337ac;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 17px;
    font-family: Raleway;
    cursor: pointer;
    border-radius: 4px;
}
.selectedRole{
  margin-bottom: 18px;  
}
.city.margin-css{
    height: 552px;
}
.getMenuButton{
    background-color: #0337ac;
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    font-size: 17px;
    font-family: Raleway;
    cursor: pointer;
    border-radius: 4px;
}
input.getMenuButton {
    margin: 0 0px 10px 0px;
}
.mainMenu{
    color: black;
    font-size: 17px;
}

input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 
}

</style>

<link href="{!! asset('public/css/addon/w3.css') !!}" rel="stylesheet">
<div class="container">
    <div class="col-sm-12">
        <form method="post" action="{{ url('/otherUserEditSubmit') }}">
            <div class="tab">
                <div class="w3-bar w3-black">
                    <a class="w3-bar-item w3-button tablink w3-red" onclick="openCity(event,'Ll')">Personal Details</a>
                    <a class="w3-bar-item w3-button tablink" onclick="openCity(event,'Pp')">Role Details</a>
                </div>
                <div id="Ll" class="w3-container w3-border city margin-css">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                      <input type="hidden" name="dairyId" id="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
                      <input type="hidden" name="otherUserId" id="otherUserId" value="{{ $otherUserData[0]->id }}">
                    <div class="col-sm-6">
                        <label>Name</label>
                        <input type="text" class="form-control" oninput="this.className = ''" value="{{ $otherUserData[0]->userName }}" id="otherUserName" placeholder="Enter Name" name="otherUserName">
                    </div>
                    <div class="col-sm-6">
                        <label>Father Name</label>
                        <input type="text" class="form-control" oninput="this.className = ''" value="{{ $otherUserData[0]->fatherName }}" id="otherUserFatherName" placeholder="Enter Father Name" name="otherUserFatherName">
                    </div>
                    <div class="col-sm-6">
                        <label>Aadhar Number</label>
                        <span class="otherUserAadharNumberErrorMessage errorMessage" id="otherUserAadharNumberErrorMessage"> </span>
                        <input type="text" value="{{ $otherUserData[0]->aadharNumber }}" class="form-control" oninput="this.className = ''" id="otherUserAadharNumber" onfocusout="checkAadharNumber()" placeholder="############" name="otherUserAadharNumber">
                    </div>
                    <div class="col-sm-6">
                        <label>Email</label>
                        
                        <input type="email" name="otherUserEmail" value="{{ $otherUserData[0]->userEmail }}" oninput="this.className = ''" class="form-control" id="otherUserEmail" placeholder="Enter Email">
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <label> Gender </label>
                        </div>
                        <div class="col-sm-3">
                            <input type="radio" {{ $otherUserData[0]->gender == 'male' ? 'checked' : '' }} name="gender" value="male"> Male
                            <input type="radio" {{ $otherUserData[0]->gender == 'female' ? 'checked' : '' }} name="gender" value="female"> Female
                        </div>
                        <div class="col-sm-8"> </div>
                    </div>

                    <div class="col-sm-6">
                        <label>Mobile Number</label>
                        <span class="otherUserNumberErrorMessage errorMessage" id="otherUserNumberErrorMessage"> </span>
                        <input type="number" value="{{ $otherUserData[0]->mobileNumber }}"  class="form-control" oninput="this.className = ''" onfocusout="checkOtherUserEmail()" id="otherUserMobileNumber" placeholder="Enter Mobile Number" name="otherUserMobileNumber">
                    </div>
                    <div class="col-sm-6">

                    </div>
                    <div class="col-sm-6"> </div>
                    <div class="col-sm-12">
                        <label> Address </label>
                        <textarea class="form-control" oninput="this.className = ''" id="otherUserAddress" name="otherUserAddress">{{ $otherUserData[0]->address }} </textarea>
                    </div>
                    <div class="col-sm-6">
                        <label>State</label>
                        <select onchange="FunMemberPersonalState()" name="otherUserState" id="otherUserState" class="form-control">
                            <option>--States-- </option>
                            @foreach ($states as $allStates)
                                <option value=" {{$allStates->id}} " {{ $allStates->id == $otherUserData[0]->state ? 'selected="selected"' : '' }} >{{$allStates->name}}  </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label>City</label>
                        <select name="otherUserCity" class="form-control" id="otherUserCity">
                          <option value="--City--"> {{ $otherUserData[0]->city }} </option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label>Village/District</label>
                        <input type="text" class="form-control" value="{{ $otherUserData[0]->villageDistrict }}" oninput="this.className = ''" id="otherUserVillageDistrict" placeholder="Enter Village/District" name="otherUserVillageDistrict">
                    </div>
                    <div class="col-sm-6">
                        <label>Pin code </label>
                        <input type="text" class="form-control" value="{{ $otherUserData[0]->pincode }}" oninput="this.className = ''" id="otherUserPincode" placeholder="Enter PinCode" name="otherUserPincode">
                    </div>

                    <div class="col-sm-6"></div>
                   

                </div>
                <div id="Pp" class="w3-container w3-border city margin-css" style="display:none">
                		<div class="col-sm-6">
                			<label>Select Role</label>
		                	<select name="selectedRole" class="form-control selectedRole">
  		                  @foreach ($otherUserData[1] as $roleInfo)
                          <option value="{{ $roleInfo->id }}"  {{ $roleInfo->id == $otherUserData[0]->roleId ? 'selected="selected"' : '' }}   >{{ $roleInfo->role }}</option>
                        @endforeach
		                	</select>
	                	</div>
                      <div class="col-sm-12">
                    <label> Access To Menu </label>
	              	<table>
						        <tr>
                          <td>
                                <select  id="parent" multiple size="10"  style="width:150">
                                  <!-- <div class="menuSelection" id="menuSelection"> </div> -->
                                </select>
                            </td>
                            <td align="center" valign="middle">
                                <input type="button" class="getMenuButton" onClick="move(this.form.child,this.form.parent,'sender')" value="<<">
                                <input type="button" class="getMenuButton" onClick="move(this.form.parent,this.form.child,'recever')" value=">>">

                            </td>
                            <td>
                            	<!-- <input type="hidden" name="selectPages" id="selectPages" class="selectPages"> -->
                                <select id="child" multiple onchange="selectvalue();"  size="10" name="selectedMenu[]" >

                                </select>
                            </td>
                        </tr>
                    </table>
                  </div>
                    <p>
                        <center>

                        </center>
                    </p>

                </div>
            </div>
             
            <div style="overflow:auto; float:right; margin-top:20px; clear:both;">

                <!-- <div style="float:right;">
                    <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                    <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
                </div> -->
                <div class="col-sm-6">
                        <button type="submit"  class="btn btn-default otherUserSubmit">Submit</button>
                    </div>

            </div>
          
    </div>
</div>
<script type="text/javascript">
	function FunMemberPersonalState(){
       var stateID = $("#otherUserState").val();    
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){               
                if(res){
                    $("#otherUserCity").empty();
                    $.each(res,function(key,value){
                        $("#otherUserCity").append('<option value="'+key+'">'+value['name']+'</option>');
                    });
               
                }else{
                   $("#otherUserCity").empty();
                }
               }
            });
        }else{
            $("#otherUserCity").empty();
        }
    }

 /* Aadhar Number validation */
  function checkAadharNumber(){
    var aadharNumber  = document.getElementById("otherUserAadharNumber").value;
    // var str = "PB 10 CV 2662";
	aadharNumberCount = aadharNumber.replace(/ +/g, "");
    if(aadharNumberCount.length != 12){
    	document.getElementById("otherUserAadharNumberErrorMessage").innerHTML = "Aadhar number must in valid.";
        document.getElementById("otherUserAadharNumber").focus();
    }else{
    	 document.getElementById("otherUserAadharNumberErrorMessage").innerHTML = "";
    }

  }

  /* Supplier email validation  */
  function checkOtherUserEmail(){



 
	var otherUserNumber  = document.getElementById("otherUserMobileNumber").value;
  var otherUserId  = document.getElementById("otherUserId").value;
	
    if(otherUserNumber){
         $.ajax({
               type:"GET",
               url:"{{url('/UserEditEmailValidation')}}?otherUserNumber="+otherUserNumber+"&otherUserId="+otherUserId  ,
               success:function(res){   

                if(res == "true"){
				  document.getElementById("otherUserNumberErrorMessage").innerHTML = "This Mobile number is already being used.";
                  document.getElementById("otherUserMobileNumber").focus();
                }else if(res == "false"){
                  document.getElementById("otherUserNumberErrorMessage").innerHTML = "";
                }
               }
            });
      }
  }


function move(frombox, tobox, action) {


	var arrFrombox = new Array();
	var arrTobox = new Array();
	var arrLookup = new Array();
	var i;

for (i = 0; i < tobox.options.length; i++) {
	arrLookup[tobox.options[i].text] = tobox.options[i].value;
	arrTobox[i] = tobox.options[i].text;
	}
var fromLength = 0;
var toLength = arrTobox.length;

for(i = 0; i < frombox.options.length; i++) {
	arrLookup[frombox.options[i].text] = frombox.options[i].value;
	if (frombox.options[i].selected && frombox.options[i].value != "") {
		arrTobox[toLength] = frombox.options[i].text;
		toLength++;
	}
	else {
	arrFrombox[fromLength] = frombox.options[i].text;
	fromLength++;
   }
}
arrFrombox.sort();
arrTobox.sort();
frombox.length = 0;
tobox.length = 0;

var c;

for(c = 0; c < arrFrombox.length; c++) {
	var no = new Option();
	no.value = arrLookup[arrFrombox[c]];
	no.text = arrFrombox[c];
	frombox[c] = no;
}

for(c = 0; c < arrTobox.length; c++) {
	var no = new Option();
	no.value = arrLookup[arrTobox[c]];
	no.text = arrTobox[c];
	tobox[c] = no;
   }
   
   selectvalue();

  if(action == "sender"){
    var menuSelect = "";
      var loopData = "";
        $.ajax({
            type:"POST",
            url:'sidebarMenu' ,
            async: false,
            data: {
                userId: userId,
            },
           success:function(res){
           
            var MainMenuTitle = "" ;
            for(i=0;i<res.length;i++){
                if(res[i].subMenu == 1){
                    var mainSubMenuData = "" ;
                    MainMenuTitleSelect = res[i].title ;
                    MainMenuIdSelect = res[i].id ;

                    $.ajax({
                    type:"POST",
                    url:'sidebarSubMenu' ,
                    async: false,
                    data: {
                        parentMenuId: res[i].id,
                    },
                    success:function(result){
                        loopData = '<option class="mainMenu" value= "'+ MainMenuIdSelect +'">'+ MainMenuTitleSelect +'</option>' ;
                         
                        for(j=0;j<result.length;j++){
                            loopData = loopData + '<option value= "'+ MainMenuIdSelect +"_" + result[j].id +'">'+ result[j].title +'</option>' ;
                      
                        }
                        menuSelect = menuSelect + loopData ;
                    }
                    });
                }else{
                     menuSelect = menuSelect +  '<option class="mainMenu" value= "'+ res[j].id +'">'+ res[j].title +'</option>' ;
                }
            }
            document.getElementById("parent").innerHTML = "";
            $('#parent').append(menuSelect);
           }
        });
  }


}

function selectvalue(){
	$('#child option').prop('selected', true);
}


function openCity(evt, cityName) {
   var menuSelect = "";
  if(cityName == "Pp"){
      var loopData = "";
        $.ajax({
            type:"POST",
            url:'sidebarMenu' ,
            async: false,
            data: {
                userId: userId,
            },
           success:function(res){
           
            var MainMenuTitle = "" ;
            for(i=0;i<res.length;i++){
                if(res[i].subMenu == 1){
                    var mainSubMenuData = "" ;
                    MainMenuTitleSelect = res[i].title ;
                    MainMenuIdSelect = res[i].id ;

                    $.ajax({
                    type:"POST",
                    url:'sidebarSubMenu' ,
                    async: false,
                    data: {
                        parentMenuId: res[i].id,
                    },
                    success:function(result){
                        loopData = '<option class="mainMenu" value= "'+ MainMenuIdSelect +'">'+ MainMenuTitleSelect +'</option>' ;
                         
                        for(j=0;j<result.length;j++){
                            loopData = loopData + '<option value= "'+ MainMenuIdSelect +"_" + result[j].id +'">'+ result[j].title +'</option>' ;
                      
                        }
                        
                        menuSelect = menuSelect + loopData ;
                    }
                    });

                }else{
                     menuSelect = menuSelect +  '<option class="mainMenu" value= "'+ res[j].id +'">'+ res[j].title +'</option>' ;
                }
            }

            $('#parent').append(menuSelect);
           
           }
        });
          
          
  }
  var i, x, tablinks;
  x = document.getElementsByClassName("city");
  for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " w3-red";
}
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the crurrent tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
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
    document.getElementsByClassName("step")[currentTab].className += " finish";
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

</script>
@endsection