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
* {
  box-sizing: border-box;
}


#regForm {
  /* background-color: #ffffff; */
  padding: 30px 10px;
}

h1
{
    color:#0337ac;
}

input {
  padding: 10px;
  width: 100%;
  font-size: 17px;
  /* font-family: Raleway; */
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
#tab1
{
    float:left;
    width:3%;
}
#tab2
{
    width:3%;
}
#tab3
{
    width:3%;
}
input[type="radio"]
{
    width:20px;
}
input[type="checkbox"]
{
  width:20px;
}
section {
  display: none;
  padding: 20px 0 0;
  border-top: 1px solid #ddd;
}
#tab1:checked ~ #content1,
#tab2:checked ~ #content2,
#tab3:checked ~ #content3,
#tab4:checked ~ #content4 {
  display: block;
}
@media screen and (max-width: 650px) {
  label {
    font-size: 0;
  }
  label:before {
    margin: 0;
    font-size: 18px;
  }
}
@media screen and (max-width: 400px) {
  label {
    padding: 15px;
  }
}




</style>
<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
    <div class="fcard mt-30 clearfix">
        <div class="heading">
            <h3>Port Setup</h4>
        </div>

        <form id="regForm" method="post" action="{{ url('/portEditSubmit') }}" class="clearfix">

            <input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
            <input type="hidden" name="status" value="true">
            <input type="hidden" name="utilityId" value="{{ $utility->id }}">
            
            <input type="hidden" id="machinTypeHidden" class="machinTypeHidden" name="machinTypeHidden" value="{{ $utility->machinType == 'Electronic Weighter' ? 'Electronic Weighter' : '' }}">
            <div id="mm" class="w3-container w3-border city">

                <div class="col-sm-12">

                    <div class="col-sm-2">
                        <label>Machine</label>
                    </div>
                    <div class="col-sm-4">
                        <select name="machinType" id="machinType" onchange="getMilkType(this)" class="machinType form-control" required>
                            <option {{ $utility->machinType == 'Milk Tester' ? 'selected="selected"' : '' }} value="Milk Tester">Milk Tester</option>
                            <option {{ $utility->machinType == 'Electronic Weighter' ? 'selected="selected"' : '' }} value="Electronic Weighter">Electronic Weighter</option>
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <label>Communication Port</label>
                    </div>
                    <div class="col-sm-4">
                        <select name="communicationPort" class="form-control" required>
                            <option></option>
                            <option {{ $utility->communicationPort == '1' ? 'selected="selected"' : '' }}  value="1">Communication 1</option>
                            <option {{ $utility->communicationPort == '2' ? 'selected="selected"' : '' }}  value="2">Communication 2</option>
                            <option {{ $utility->communicationPort == '3' ? 'selected="selected"' : '' }}  value="3">Communication 3</option>
                            <option {{ $utility->communicationPort == '4' ? 'selected="selected"' : '' }}  value="4">Communication 4</option>
                            <option {{ $utility->communicationPort == '5' ? 'selected="selected"' : '' }}  value="5">Communication 5</option>
                            <option {{ $utility->communicationPort == '6' ? 'selected="selected"' : '' }}  value="6">Communication 6</option>
                        </select>
                    </div>

                </div>

                <div class="col-sm-12">

                    <div class="col-sm-2">
                        <label>Max Speed</label>
                    </div>
                    <div class="col-sm-4">
                        <select name="maxSpeed" class="form-control">
                            <option value="2400">2400</option>
                        </select>
                    </div>


                </div>

                <div class="col-sm-12">

                    <div class="pt-40"></div>
                        
                    <div class="col-sm-3">

                        <fieldset>
                            <legend align="center">Echo</legend>

                            <div class="col-sm-6">
                                <label>
                                    <input {{ $utility->echo == 'off' ? 'checked' : '' }} type="radio" name="echo" value="off" checked="checked"> 
                                    Off
                                </label>
                            </div>

                            <div class="col-sm-6">
                                <label>
                                    <input {{ $utility->echo == 'on' ? 'checked' : '' }} type="radio" name="echo" value="on" >
                                    On
                                </label>
                            </div>

                            <div class="col-sm-8">

                            </div>

                        </fieldset>

                    </div>

                    <div class="col-sm-6">

                        <fieldset>
                            <legend align="center">Connection Preference</legend>

                            <div class="col-sm-12 mb-10">
                                <div class="col-sm-6">
                                    <label>Data Bits</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="connectionPerferenceDataBits" class="selectback form-control">
                                        <option value="8" >8</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 mb-10">
                                <div class="col-sm-6">
                                    <label>Parity</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="connectionPerferenceParity" class="selectback form-control">
                                        <option value="1" >1</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 mb-10">

                                <div class="col-sm-6">
                                    <label>Stop Bits</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="connectionPerferenceStopBits" class="selectback form-control">
                                            <option value="1">1</option>
                                    </select>
                                </div>
                            </div>

                        </fieldset>

                    </div>

                    <div class="col-sm-3">

                        <fieldset>
                            <legend align="center">Flow Control</legend>

                            <div class="col-sm-12 mb-10">
                                <label>
                                    <input {{ $utility->flowControl == 'None' ? 'checked' : '' }} name="flowControl" type="radio" value="None" checked="checked">
                                    None
                                </label>
                            </div>

                            <div class="col-sm-12 mb-10">
                                <label>
                                    <input {{ $utility->flowControl == 'Xon / Xoff' ? 'checked' : '' }} name="flowControl" type="radio" value="Xon / Xoff">
                                    Xon / Xoff
                                </label>
                            </div>

                            <div class="col-sm-12 mb-10">
                                <label>
                                    <input {{ $utility->flowControl == 'RTS' ? 'checked' : '' }} name="flowControl" type="radio" value="RTS">
                                    RTS
                                </label>
                            </div>

                            <div class="col-sm-12 mb-10">
                                <label>
                                    <input {{ $utility->flowControl == 'Xon / RTS' ? 'checked' : '' }} name="flowControl" type="radio" value="Xon / RTS">
                                    Xon / RTS
                                </label>
                            </div>
                        </fieldset>
                    </div>
            
                </div>


                <div class="col-md-12">
                    
                        <div id="weightModelView" class="weightModelView" style="display: none;" class="col-sm-5">

                                <fieldset>
                                    <legend align="center">Weight Model</legend>
        
                                    <div class="col-sm-12">
        
                                        <div class="col-sm-1">
                                            <input name="weightMode" {{ $utility->weightMode == 'Kg' ? 'checked' : '' }} type="radio" value="Kg" checked="checked">
                                        </div>
                                        <div class="col-sm-9">
                                            <label>Kg </label>
                                        </div>
        
                                    </div>
        
                                    <div class="col-sm-12">
                                        <div class="col-sm-1">
                                            <input name="weightMode" {{ $utility->weightMode == 'Litre' ? 'checked' : '' }} type="radio" value="Litre">
                                        </div>
                                        <div class="col-sm-9">
                                            <label>Litre </label>
                                        </div>
        
                                    </div>
        
                                    <div class="col-sm-12">
                                        <div class="col-sm-1">
                                            <input name="weightMode_auto_tare" {{ $utility->weightMode_auto_tare == 'Auto Tare' ? 'checked' : '' }} type="checkbox" value="Auto Tare">
                                        </div>
                                        <div class="col-sm-9">
                                            <label>Auto Tare </label>
                                        </div>
        
                                    </div>
        
                                    <div class="col-sm-12">
                                        <div class="col-sm-1">
                                            <input name="weightMode_no_training" {{ $utility->weightMode_no_training == 'No Training' ? 'checked' : '' }} type="checkbox" name="" value="No Training">
                                        </div>
                                        <div class="col-sm-9">
                                            <label>No Training</label>
                                        </div>
        
                                    </div>
        
                                    <div class="col-sm-12">
                                        <div class="col-sm-1">
                                            <input name="weightMode_weight_in_doublke_decimal" {{ $utility->weightMode_weight_in_doublke_decimal == 'Weight in Doublke Decimal' ? 'checked' : '' }} type="checkbox" value="Weight in Doublke Decimal ">
                                        </div>
                                        <div class="col-sm-9">
                                            <label>Weight in Doublke Decimal </label>
                                        </div>
        
                                    </div>
        
                                    <div class="col-sm-12">
                                        <div class="col-sm-1">
                                            <input name="weightMode_write_in" type="checkbox" {{ $utility->weightMode_write_in == 'Write in 3 Digit (999.9)' ? 'checked' : '' }} value="Write in 3 Digit (999.9) ">
                                        </div>
                                        <div class="col-sm-9">
                                            <label>Write in 3 Digit (999.9) </label>
                                        </div>
        
                                    </div>
        
                                </fieldset>
        
                            </div>
                </div>


                <div class="col-sm-12">

                    <div class="pt-40"></div>
                    <div class="col-sm-4 col-md-offset-4">
                        <button type="submit" class="sbutton btn btn-primary">Apply</button>
                        <button class="sbutton btn btn-primary">Test</button>
                        <button class="sbutton btn btn-primary">Reset</button>
                    </div>

                </div>

            </div>
        </form>
    </div>
   
    </div>
<script type="text/javascript">

    $( document ).ready(function() {
       
        var selectedText = document.getElementById('machinTypeHidden').value;
            if(selectedText == "Electronic Weighter"){
                $("#weightModelView").show();
            }else{
                $("#weightModelView").hide();
            }
    });

    function getMilkType(milkTypeValue){
            var selectedText = milkTypeValue.options[milkTypeValue.selectedIndex].innerHTML;
            var selectedValue = milkTypeValue.value;
            
            if(selectedValue == "Electronic Weighter"){
                $("#weightModelView").show();
            }else{
                $("#weightModelView").hide();
            }
    }

    function openCity(evt, cityName) {
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

  /* Supplier email validation  */
  function CheckcustomerEmail(){
 
    var customer_email  = document.getElementById("customerEmail").value;

    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        var filterReturn = filter.test(customer_email);
        if(filterReturn == true){   
         $.ajax({
               type:"GET",
               url:"{{url('/checkCustomerEmail')}}?customer_email="+customer_email ,
               success:function(res){          
                if(res == "true"){
                  document.getElementById("customerEmailErrorMessage").innerHTML = "This Email is already being used.";
                  document.getElementById("customerEmail").focus();
                }else if(res == "false"){
                  document.getElementById("customerEmailErrorMessage").innerHTML = "";
                }
               }
            });
      }else{
            document.getElementById("customerEmailErrorMessage").innerHTML = "This email is not valid.";
            document.getElementById("customerEmail").focus();
        }
  }

</script>
@endsection