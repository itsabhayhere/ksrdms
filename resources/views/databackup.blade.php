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
 <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

<script type="text/javascript" href="{{ asset('js/addon/bootstrap.js') }}" >  </script>
<link href="{!! asset('public/css/addon/w3.css') !!}" rel="stylesheet" type="text/css">
<link href="{!! asset('public/css/addon/11.css') !!}" rel="stylesheet" type="text/css">
<script src="{!! asset('js/addon/jquery-1.3.2.min.js') !!}"></script>  
<script src="{!! asset('js/addon/jquery-ui-1.7.custom.min.js') !!}"></script>  
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
<div class="container">

    <form method="post" action="{{ url('/dataBackupSubmit') }}" > 
      <input type="hidden" name="dairyId" value="{{{ Session::get('loginUserInfo')->dairyId }}}">
      <input type="hidden" name="status" value="true">


        <div id="kk" class="w3-container w3-border city">
            <h2>Data Setup</h2>

            <input id="tab1" type="radio" name="tabs" value="daily" checked>
            <label for="tab1">Daily</label>

            <input id="tab2" type="radio" name="tabs" value="weekly">
            <label for="tab2">Weekly</label>

            <input id="tab3" type="radio" name="tabs" value="monthly">
            <label for="tab3">Monthly</label>

            <section id="content1">

                <div class="col-sm-12">

                    <div style="margin-top:10px;">

                        <div class="col-sm-12">

                            <fieldset>
                                <legend align="left">Daily Setting</legend>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Starting at</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input id="dailyStartTime" name="startTime" type="text">
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Ending no later than</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input id="dailyEndTime" name="endTime" type="text" >
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>
                            </fieldset>

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-12">
                        <p class="formschedule">Select the days when you want the schedule to be performed : </p>
                    </div>

                    <div class="col-sm-12">

                        <div class="col-sm-1">
                            <input name="monday" value="yes"  type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Monday</label>
                        </div>

                        <div class="col-sm-1">
                            <input name="tuesday" value="yes" type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Tuesday</label>
                        </div>

                        <div class="col-sm-1">
                            <input name="wednedsay" value="yes" type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Wednesday</label>
                        </div>

                        <div class="col-sm-1">
                            <input name="thursday" value="yes" type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Thrursday</label>
                        </div>

                        <div class="col-sm-1">
                            <input name="friday" value="yes" type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Friday</label>
                        </div>

                        <div class="col-sm-1">
                            <input name="sterday" value="yes" type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Saturday</label>
                        </div>
                        <div class="col-sm-1yesyes " >
                                                <input name="sunday" value="yes" type="checkbox">
                        </div>
                        <div class="col-sm-2">
                            <label>Sunday</label>
                        </div>

                    </div>

                    <!-- <div class="col-sm-12">

                        <div class="col-sm-4">
                        </div>
                        <div class="col-sm-4">
                            <button class="sbutton">Apply</button>
                            <button class="sbutton">Reset</button>
                        </div>

                        <div class="col-sm-4">
                        </div>

                    </div> -->

            </section>

            <section id="content2">

                <div class="col-sm-12">

                    <div style="margin-top:10px;">

                        <div class="col-sm-12">

                            <fieldset>
                                <legend align="left">Weekly Setting</legend>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>On Every</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="week_day">
                                            <option>Sunday</option>
                                            <option>Monday</option>
                                            <option>Tuesday</option>
                                            <option>Wednesday</option>
                                            <option>Turusday</option>
                                            <option>Friday</option>
                                            <option>Saturday</option>

                                        </select>
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Starting at</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="monthStartTime" id="weeklyStartTime">
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Ending no later than</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="monthEndTime" id="weeklyEndTime">
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>
                            </fieldset>

                            </div>

                        </div>

                    </div>
                    <!-- 
                      <div class="col-sm-12 cc">

                          <div class="col-sm-4">
                          </div>
                          <div class="col-sm-4">
                              <button class="sbutton">Apply</button>
                              <button class="sbutton">Reset</button>
                          </div>

                          <div class="col-sm-4">
                          </div>

                      </div>
                     -->

            </section>

            <section id="content3">

                <div class="col-sm-12">

                    <div style="margin-top:10px;">

                        <div class="col-sm-12">

                            <fieldset>
                                <legend align="left">Monthly Setting</legend>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>On the Day of the Month</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <select name="monte_date">
                                            @for ($i = 1; $i <= 31 ; $i++)
                                              <option value="{{ $i }}">  {{ $i }} </option> 
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Starting at</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="yearStartTime" id="monthlyStartTime">
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Ending no later than</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="yearEndTime" id="monthlyEndTime">
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                </div>
                            </fieldset>

                            </div>

                        </div>

                    </div>
                    
            </section>
                    <div class="col-sm-12 cc">

                        <div class="col-sm-4">
                        </div>
                        <div class="col-sm-4">
                            <button class="sbutton">Apply</button>
                            <button class="sbutton">Reset</button>
                        </div>

                        <div class="col-sm-4">
                        </div>
                    </div>
            </div>
    </form>
    </div>
<script type="text/javascript">

    $(function () {
        $('#dailyStartTime').datetimepicker({
          format: 'LT'
        });
    });
    $(function () {
        $('#dailyEndTime').datetimepicker({
          format: 'LT'
        });
    });

    $(function () {
        $('#weeklyStartTime').datetimepicker({
          format: 'LT'
        });
    });
    $(function () {
        $('#weeklyEndTime').datetimepicker({
          format: 'LT'
        });
    });

    $(function () {
        $('#monthlyStartTime').datetimepicker({
          format: 'LT'
        });
    });
    $(function () {
        $('#monthlyEndTime').datetimepicker({
          format: 'LT'
        });
    });



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