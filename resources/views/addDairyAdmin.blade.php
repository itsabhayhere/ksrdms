 @extends('theme.default')

@section('content')
 <!-- custome css   -->
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

<link href="{{ asset('css/addon/style.css') }}" rel="stylesheet" />
<link href="{{ asset('css/addon/tabs.css') }}" rel="stylesheet" />
<script type="text/javascript" href="{{ asset('js/addon/bootstrap.js') }}" >  </script>

<link href="{!! asset('public/css/addon/11.css') !!}" rel="stylesheet" type="text/css">
<script src="{!! asset('js/addon/jquery-1.3.2.min.js') !!}"></script>  
<script src="{!! asset('js/addon/jquery-ui-1.7.custom.min.js') !!}"></script>  
<style type="text/css">
.container {
    width: auto;
}
#regForm {
  background-color: #ffffff;
  margin: 100px auto;
  font-family: Raleway;
  padding: 0px ;
  width: 70%;
  min-width: 300px;
}

div.tab.mainTitle {
    display: initial;
}
.pp{
    background-color: #0337ac;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
  border-radius:4px;
}

.pp1{
  background-color: red;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
  border-radius:4px;
}

.pp2{
  background-color: green;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 17px;
  font-family: Raleway;
  cursor: pointer;
  border-radius:4px;
}

.pp:hover{
   
  color: #fff !important;

}

.pp1:hover{
   
  color: #fff !important;

}

.pp2:hover{
   
  color: #fff !important;

}
.memberPersonalregisterDate {
  z-index: 9999 !important;
}
.showDairyTab{
text-decoration: underline;
}

bs-datepicker-container { z-index: 3000; }

</style>
 <a  type="button" id="modalButton" class="showDairyTab" data-toggle="modal" data-target="#myModal">Open Wizard</a>

  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">

<div class="container">

    <div class="stepwizard">
    <div class="stepwizard-row setup-panel">
        <div class="stepwizard-step">
            <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
            <p>Start</p>
        </div>
        <div class="stepwizard-step">
            <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
            <p>Dairy Information</p>
        </div>
        <div class="stepwizard-step">
            <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
            <p>Shift Setup</p>
        </div>
        <div class="stepwizard-step">
            <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
            <p>Member Setup</p>
        </div>
      <!-- 
        <div class="stepwizard-step">
            <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
            <p>Submit</p>
        </div> 
      -->
    </div>
  </div>


    <div class="row">
   <form method="post" id="regForm" action="{{url('/addDairyAdminSubmit')}}">
    <div class="tab mainTitle" id="abc" >

        <h1>Daily Setup Wizard</h1>
        <div class="col-sm-12">
            <h1 align="center">Welcome to the Dairy Setup Wizard................</h1>
        </div>

    </div>
    <div class="row setup-content" id="step-1">
      <div class="tab ">

            <h1>Daily Setup Wizard > Dairy Information</h1>

            <h1>Few Details About Your Dairy:</h1>
            <input type="hidden" name="createBySuperAdmin" value="1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="status" value="status">
            <div class="col-sm-6">
                <input placeholder="Dairy name" oninput="this.className = ''" name="society_name">
            </div>
            <div class="col-sm-6">
               <span class="societyCodeErrormessage" id="societyCodeErrormessage"> </span>
                <input placeholder="Society Code" oninput="this.className = ''" type="text" class="society_code" id="society_code" onfocusout="checkSociety_code()" name="society_code">
            </div>

            <div class="col-sm-12">
                <textarea placeholder="Address" id="dairyInfoAddressId" name="dairyInfoAddressId"  style="width:100%; height:150px;"> </textarea>
            </div>

            <div class="col-sm-6">
                <select name="state" id="dairyInfoState"  onchange="dairyInfogetCityByStatus()" style="width:100%; margin-top:10px; height:47px;">
                    <option>--States-- </option>
                    @foreach ($states as $allStates)
                        <option value=" {{$allStates->id}} ">{{$allStates->name}}  </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-6">
                <select name="city" id="dairyInfoCity" style="width:100%; margin-top:10px; height:47px;">
                    <option>-- citys --</option>
                </select>
            </div>

           
             <div class="col-sm-6">
                <input placeholder="Village and District...." oninput="this.className = ''" name="district">
            </div>

            <div class="col-sm-6">
                <input placeholder="pin code...." oninput="this.className = ''" name="pincode">
            </div>

            <div class="col-sm-12" style="text-align:left; margin-top:20px;">
                <h1>Dairy Propritor Details : </h1>
            </div>

            <div class="col-sm-6">
                <input placeholder="Name..." oninput="this.className = ''" name="dairyPropritorName">
            </div>
            <div class="col-sm-6">
                <input placeholder="Mobile No...." oninput="this.className = ''" name="PropritorMobile">
            </div>

            <div class="col-sm-12">
                <span class="emailerrormessage" id="emailerrormessage"> </span>
                <input placeholder="Email...." oninput="this.className = ''" type="email" onfocusout="CheckdairyPropritorEmail()" id="dairyPropritorEmail" class="dairyPropritorEmail" name="dairyPropritorEmail">

            </div>



            <div class="col-sm-6">
                <div>
                    <input type="checkbox" id="sameAddressChecked" value="sameAddressChecked" onclick='getSameAddress()' name="dairyPropritorSameAddress" style="width:30px !important;"><span>same as current address</span>
                </div>

            </div>

            <div class="col-sm-12">
                <div>
                    <input type="text" placeholder="dairy Propritor Address" id="dairyPropritorAddress" name="dairyPropritorAddress" style="width:100%; height:150px;">
                </div>
            </div>

            <div class="col-sm-6">
                <select name="dairyPropritorState" id="dairyPropritorState"  onchange="dairyPropritorCityByStatus()" style="width:100%; margin-top:10px; height:47px;">
                    <option>--States-- </option>
                    @foreach ($states as $allStates)
                        <option value=" {{$allStates->id}} ">{{$allStates->name}}  </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-6">
                <select name="dairyPropritorCity" id="dairyPropritorCity" style="width:100%; margin-top:10px; height:47px;">
                  <option>-- citys --</option>
               </select>
            </div>

            
             <div class="col-sm-6">
                <input placeholder="Village and District...." oninput="this.className = ''" name="dairyPropritorDistrict">
            </div>

            <div class="col-sm-6">
                <input placeholder="pin code...." oninput="this.className = ''" name="dairyPropritorPincode">
            </div>

            <div class="col-sm-6">
              <label>Opening balance</label>
                <input type="number" placeholder="Customer Opening Balance"  max="10" class="customerOpeningBalance form-control" id="customerOpeningBalance" oninput="this.className = ''" name="openingBalance">
            </div>
            <div class="col-sm-6">
              <label>Opening balance Type</label>
                <select name="openingBalanceType" oninput="this.className = ''" class="openingBalanceType form-control" name="openingBalanceType" id="openingBalanceType">
                  <option value="credit">Credit</option>
                  <option value="debit">Debit</option>
                </select>
            </div>

        </div>
    </div>
    <div class="row setup-content" id="step-2">
      <div class="tab">

          <h1>Morning Shift</h1>
          <div class="col-sm-2" style="margin-top:20px;">
              From:
          </div>
          <div class="col-sm-4">
            <div style="position: relative">

              <input placeholder="12.00 AM" class="morningShiftStart" oninput="this.className = ''"  name="morningShiftStartTime">
            </div>
          </div>
          <div class="col-sm-2" style="margin-top:20px;">
              To:
          </div>
          <div class="col-sm-4">
            <div style="position: relative">
              <input placeholder="12.00 PM" style="z-index: 3001;" class="morningShiftEnd"  oninput="this.className = ''" name="morningShiftEndtime">
            </div>
          </div>

          <h1 style="margin-top:10px !important;">Evening Shift</h1>
          <div class="col-sm-2" style="margin-top:20px;">
              From:
          </div>
          <div class="col-sm-4">
            <div style="position: relative">
              <input placeholder="11.59 AM" style="z-index: 3002;" class="eveningShiftStart" oninput="this.className = ''" name="eveningShiftStartTime">
            </div>
          </div>
          <div class="col-sm-2" style="margin-top:20px;">
              To:
          </div>
          <div class="col-sm-4">
            <div style="position: relative">
              <input placeholder="11.59 PM" style="z-index: 3003;" class="eveningShiftEnd" oninput="this.className = ''" name="eveningShiftEndTime">
            </div>
          </div>
      </div>
    </div>
    <div class="row setup-content" id="step-3">
        <div class="tab">

        <h1>Daily Setup Wizard > Member Setup > Personal Information</h1>
        <div class="w3-bar w3-black">
            <a class=" w3-bar-item w3-button tablink w3-red pp" onclick="openCity(event,'London')">Personal Information</a>
            <a class="w3-bar-item w3-button tablink pp1" onclick="openCity(event,'Paris')">Bank Account Details</a>
            <a class=" w3-bar-item w3-button tablink pp2" onclick="openCity(event,'Tokyo')">Milk Collection Details</a>
        </div>

        <div id="London" class="w3-container w3-border city">
            <h2>Personal Information</h2>


            <div class="col-sm-6">
                <span class="membersCodeErrorMessage" id="membersCodeErrorMessage"> </span>
                <input placeholder="Members Code..." id="memberPersonalCode" class="memberPersonalCode" oninput="this.className = ''" onfocusout="CheckMemberPersonalCode()" name="memberPersonalCode">
            </div>
            <div class="col-sm-6">
                <input placeholder="Registration Date...." oninput="this.className = ''" id="memberPersonalregisterDate" class=memberPersonalregisterDate name="memberPersonalregisterDate">
            </div>

            <div class="col-sm-6">
                <input placeholder="Name..." oninput="this.className = ''" name="memberPersonalName">
            </div>
            <div class="col-sm-4">
                <input placeholder="Father_Name...." oninput="this.className = ''" name="memberPersonalFatherName">
            </div>

            <div class="col-sm-2" style="margin-top:20px;">
                <select name="memberPersonalGender" class="form-control">
                   <option>Gender</option>
                   <option value="male">Male</option>
                   <option value="female">Female</option>
               </select>
            </div>

            <div class="col-sm-12">
                <span class="membersEmailErrorMessage" id="membersEmailErrorMessage"> </span>
                <input placeholder="email...." oninput="this.className = ''" id="memberPersonalEmail" class="memberPersonalEmail" name="memberPersonalEmail" onfocusout="checkMemberPersonalEmail" type="email">
            </div>

            <div class="col-sm-6">
              <span class="memberAadharNumberErrorMessage" id="memberAadharNumberErrorMessage"> </span>
                <input placeholder="Aadhaar No...." oninput="this.className = ''" id="memberPersonalAadarNumber" class="memberPersonalAadarNumber" onfocusout="checkMemberPersonalAadarNumber()" name="memberPersonalAadarNumber">
            </div>
            <div class="col-sm-6">
                <input placeholder="Mobile No....." oninput="this.className = ''" type="number" max="10"  name="memberPersonalMobileNumber">
            </div>

            <div class="col-sm-12">
                <textarea placeholder="Address" name="memberPersonalAddress" style="width:100%; height:150px;"></textarea>
            </div>
            
            <div class="col-sm-6">
                <select name="memberPersonalState" id="memberPersonalState" class="memberPersonalState" onchange="FunMemberPersonalState()" style="width:100%; margin-top:10px; height:47px;">
                @foreach ($states as $allStates)
                    <option value=" {{$allStates->id}} ">{{$allStates->name}}  </option>
                @endforeach
                </select>
            </div>
             <div class="col-sm-6">
                <select class="memberPersonalCity" id="memberPersonalCity" name="memberPersonalCity" style="width:100%; margin-top:10px; height:47px;">
                  <option>--City--</option>
                  
                </select>
            </div>

            <div class="col-sm-6">
                <input placeholder="Village...." oninput="this.className = ''" name="memberPersonalDistrictVillage">
            </div>

            <div class="col-sm-6">
                <input placeholder="pin code...." oninput="this.className = ''" name="memberPersonalMobilePincode">
            </div>

        </div>
        <div id="Paris" class="w3-container w3-border city" style="display:none">
            <h2>Bank Account Details</h2>
            <div class="col-sm-6">
                <input placeholder="Bank name" oninput="this.className = ''" name="memberBankName">
            </div>
            <div class="col-sm-6">
                <input placeholder="Account Name" oninput="this.className = ''" name="memberBankNumber">
            </div>
            <div class="col-sm-6">
                <input placeholder="IFSC Code" oninput="this.className = ''" name="memberBankIfsc">
            </div>
            <div class="col-sm-6">
                <input placeholder="Branch Code" oninput="this.className = ''" name="memberBankBranchCode">
            </div>
            <h2>Opening balance Details</h2>
            <div class="col-sm-6">
                <input placeholder="member Personal Opening Balance" class="memberPersonalOpeningBalance" id="memberPersonalOpeningBalance" oninput="this.className = ''" name="memberPersonalOpeningBalance">
            </div>
            <div class="col-sm-6">
                <select name="openingBalanceType" class="openingBalanceType form-control" name="openingBalanceType" id="openingBalanceType">
                  <option>Credit</option>
                  <option>Debit</option>
                </select>
            </div>

            <div class="col-sm-6"></div>
        </div>

        <div id="Tokyo" class="w3-container w3-border city" style="display:none">
            <h2>Milk Collection Details</h2>

            <div class="col-sm-12">

            <div class="col-sm-3">
                <label>Type of Milk Supplied</label>
            </div>
            <div class="col-sm-1">
                <select name="milkType" >
                   <option>Type of Milk</option>
                   <option value="1">1</option>
                   <option value="1">2</option>
                </select>
            </div>

          </div>

          <h2>Alert/Notification Setting</h2>

            <div class="col-sm-12">

            <div class="col-sm-3">
                <input type="checkbox" value="true" id="aleryPrintSlip" name="aleryPrintSlip" class="aleryPrintSlip">Print Slip
            </div>
            <div class="col-sm-3">
                <input type="checkbox" value="true" id="alerySms" name="alerySms" class="alerySms">Sms
            </div>
            <div class="col-sm-3">
                <input type="checkbox" value="true" id="aleryEmail" name="aleryEmail" class="aleryEmail">Email
            </div>
            
            </div>
        </div>
        </div>
    </div>
    <div style="overflow:auto; float:right; margin-top:20px; clear:both; margin-bottom: 15px;">

        <div style="float:right;">
            <button type="button" class="btn btn-default prevBtn btn-lg pull-left" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
            <button type="button" class="onClickCount btn btn-primary nextBtn btn-lg pull-right" id="nextBtn" onclick="nextPrev(1)">Next</button>
            <!-- <button type="submit" class="submitButtone" id="nextBtn" onclick="nextPrev(1)">Next</button> -->
        </div>

    </div>
    <!-- Circles which indicates the steps of the form: -->
    <div style="text-align:center;margin-top:40px;">
        <span class="step" style="margin-top:10px;"></span>
        <span class="step"></span>
        <span class="step"></span>
        <span class="step"></span>
        <span class="step"></span>
    </div>
    </form>
    </div>
  </div>
</div>
</div>
</div>
@endsection

<script src="{{ asset('js/addon/dairyInfo.js') }}">  </script>

<script type="text/javascript">

   window.onload = function() {
     jQuery('#modalButton').click();

 /* dairy setup form show in popup */
  $(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn'),
            allPrevBtn = $('.prevBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for(var i=0; i<curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    allPrevBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

        $(".form-group").removeClass("has-error");
        prevStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');
});



  };



  

  /* aadhar number validation */
  function checkMemberPersonalAadarNumber(){
    var aadhar_number  = document.getElementById("memberPersonalAadarNumber").value;
    if(aadhar_number.length != 14){
      document.getElementById("memberAadharNumberErrorMessage").innerHTML = "This aadhar number is not valid.";
      document.getElementById("memberPersonalAadarNumber").focus();
    }else{
       document.getElementById("memberAadharNumberErrorMessage").innerHTML = "";
    }
  }

  /* member email validation */

  function checkMemberPersonalEmail(){
    //memberPersonalEmail
    //membersEmailErrorMessage
     var member_email  = document.getElementById("memberPersonalEmail").value;
     
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        var filterReturn = filter.test(member_email);
        if(filterReturn == true){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/memberEmailValidation')}}?member_email="+member_email ,
               success:function(res){               
                if(res == "true"){
                  document.getElementById("membersEmailErrorMessage").innerHTML = "This email is already being used.";
                  document.getElementById("memberPersonalEmail").focus();
                }else if(res == "false"){
                  document.getElementById("membersEmailErrorMessage").innerHTML = "";
                }
               }
            });
        }else{
            document.getElementById("membersEmailErrorMessage").innerHTML = "This email is not valid.";
            document.getElementById("memberPersonalEmail").focus();
        }
  }

  /* member code validation */
  function CheckMemberPersonalCode(){
    var personal_code  = document.getElementById("memberPersonalCode").value;
     if(society_code){
         $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/memberPersonalCode')}}?personal_code="+personal_code ,
               success:function(res){          
                if(res == "true"){
                  document.getElementById("membersCodeErrorMessage").innerHTML = "This code is already being used.";
                  document.getElementById("memberPersonalCode").focus();
                }else if(res == "false"){
                  document.getElementById("membersCodeErrorMessage").innerHTML = "";
                }
               }
            });
      }
  }

   /*
      sate same address as dairy infomation 
    */
    function getSameAddress(){

        if($("#sameAddressChecked").prop('checked') == true){
         var dairyInfoAddress = $("#dairyInfoAddressId").val();
          $("#dairyPropritorAddress").val(dairyInfoAddress);
        }else{
          $("#dairyPropritorAddress").val("");
        }
    }

    /* check society code */
    function checkSociety_code(){
      
      var society_code  = document.getElementById("society_code").value;
     
      if(society_code){
         $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/SocietyValidate')}}?society_code="+society_code ,
               success:function(res){          
                if(res == "true"){
                  document.getElementById("societyCodeErrormessage").innerHTML = "This societ code is already being used.";
                  document.getElementById("society_code").focus();
                }else if(res == "false"){
                  document.getElementById("societyCodeErrormessage").innerHTML = "";
                }
               }
            });
      }
    }
    
    /* chack email is valid or not */

    function CheckdairyPropritorEmail(){
     
      var dairyPropritorEmail  = document.getElementById("dairyPropritorEmail").value;
     
      var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        var filterReturn = filter.test(dairyPropritorEmail);
        if(filterReturn == true){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/emailValidate')}}?dairyPropritorEmail="+dairyPropritorEmail ,
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
 
   

    /*
        get states and city for dairyInfo
    */ 
    
    function dairyInfogetCityByStatus(){
       var stateID = $("#dairyInfoState").val();    
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){               
                if(res){
                    $("#dairyInfoCity").empty();
                    $.each(res,function(key,value){
                        $("#dairyInfoCity").append('<option value="'+key+'">'+value['name']+'</option>');
                    });
               
                }else{
                   $("#dairyInfoCity").empty();
                }
               }
            });
        }else{
            $("#dairyInfoCity").empty();
        }
   
    }

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
//memberPersonalCity
    function FunMemberPersonalState(){
       var stateID = $("#memberPersonalState").val();    
        
        if(stateID){
            $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
               success:function(res){               
                if(res){
                    $("#memberPersonalCity").empty();
                    $.each(res,function(key,value){
                        $("#memberPersonalCity").append('<option value="'+key+'">'+value['name']+'</option>');
                    });
               
                }else{
                   $("#memberPersonalCity").empty();
                }
               }
            });
        }else{
            $("#memberPersonalCity").empty();
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
        document.getElementById("nextBtn").innerHTML = "Next";
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
