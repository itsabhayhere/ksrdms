@extends('theme.default') 
@section('content')
<!-- custome css   -->
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

<link href="{{ asset('css/addon/style.css') }}" rel="stylesheet" /> {{--
<link href="{{ asset('css/addon/tabs.css') }}" rel="stylesheet" /> --}}
<script type="text/javascript" href="{{ asset('js/addon/bootstrap.js') }}">

</script>
<style type="text/css">
    #regForm {
        width: 85%;
        margin-top: 0;
    }
</style>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">

    <a class="nav-back" href="memberList" title="Back to member list"><i class="fa fa-angle-left"></i>&nbsp;</a>

    <div class="fcard mt-30 clearfix">

        <div class="heading">
            <h2>Update Member <span class="light-color f-16">({{ $member_info[0]->memberPersonalName." - ".$member_info[0]->memberPersonalCode }})</span></h2>
        </div>

        <form method="post" id="regForm" action="{{url('/editMemberInfoSubmit')}}">

            <h3 class="light-color">Personal Information</h3>
            <input type="hidden" name="memberId" id="memberId" value="{{ $member_info[0]->id }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="col-sm-6">
                <input placeholder="Members Code..." readonly="readonly" id="memberPersonalCode" value="{{ $member_info[0]->memberPersonalCode }}"
                    class="memberPersonalCode form-control" name="memberPersonalCode">
            </div>
            <div class="col-sm-6">
                <input placeholder="Registration Date...." value="{{ $member_info[0]->memberPersonalregisterDate }}"
                    id="memberPersonalregisterDate" class="memberPersonalregisterDate form-control" name="memberPersonalregisterDate">
            </div>
            <div class="col-sm-6">
                <span class="membersNameErrorMessage errmsg" id="membersNameErrorMessage"> </span>
                <input placeholder="Name..." onfocusout="memberNameValidation();" value="{{ $member_info[0]->memberPersonalName }}"
                    name="memberPersonalName" id="memberPersonalName" class="form-control">
            </div>
            <div class="col-sm-4">
                <input placeholder="Father_Name...." value="{{ $member_info[0]->memberPersonalFatherName }}"
                    class="form-control" name="memberPersonalFatherName">
            </div>
            <div class="col-sm-2">
                <select name="memberPersonalGender" class="mt-10 selectpicker" title="Gender">
                    <option {{ $member_info[0]->memberPersonalGender == 'male' ? 'selected="selected"' : '' }} value="male">Male</option>
                    <option {{ $member_info[0]->memberPersonalGender == 'female' ? 'selected="selected"' : '' }} value="female">Female</option>
                </select>
            </div>

            <div class="col-sm-12">
                <span class="membersEmailErrorMessage" id="membersEmailErrorMessage"> </span>
                <input placeholder="email...." value="{{ $member_info[0]->memberPersonalEmail }}" id="memberPersonalEmail"
                    class="memberPersonalEmail form-control" name="memberPersonalEmail" onfocusout="checkMemberPersonalEmail()"
                    type="email">
            </div>

            <div class="col-sm-6">
                <span class="memberAadharNumberErrorMessage errmsg" id="memberAadharNumberErrorMessage"> </span>
                <input placeholder="Aadhaar No...." value="{{ $member_info[0]->memberPersonalAadarNumber }}"
                    id="memberPersonalAadarNumber" class="memberPersonalAadarNumber form-control" onfocusout="checkMemberPersonalAadarNumber()"
                    name="memberPersonalAadarNumber">
            </div>
            <div class="col-sm-6">
                <span class="membersNumberErrorMessage errmsg" id="membersNumberErrorMessage"> </span>
                <input placeholder="Mobile No....." max="9999999999" min="3999999999" value="{{ $member_info[0]->memberPersonalMobileNumber }}" id="memberPersonalMobileNumber"
                    type="number" name="memberPersonalMobileNumber" onfocusout="checkMemberPersonalNumberValidation();" class="form-control">
            </div>

            <div class="col-sm-12">
                <textarea placeholder="Address" name="memberPersonalAddress" class="form-control">{{ $member_info[0]->memberPersonalAddress }}</textarea>
            </div>

            <div class="col-sm-6">
                <select name="memberPersonalState" id="dairyPropritorState" class="memberPersonalState mt-10 selectpicker" onchange="dairyPropritorCityByStatus()"
                    data-live-search="true">
                    @foreach ($states as $allStates)
                        <option value="{{$allStates->id}}" {{ $allStates->id == $member_info[0]->memberPersonalState ? 'selected="selected"' : '' }} >{{$allStates->name}} </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-6">
                <select class="memberPersonalCity mt-10 selectpicker" id="dairyPropritorCity" name="memberPersonalCity" title="Select City"
                    data-live-search="true">
                    @foreach ($cities as $city)
                        <option value="{{$city->id}}" {{ $city->id == $member_info[0]->memberPersonalCity ? 'selected' : '' }} >{{$city->name}} </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-6">
                <input placeholder="Village...." class="form-control" value="{{ $member_info[0]->memberPersonalDistrictVillage }}"
                    name="memberPersonalDistrictVillage">
            </div>

            <div class="col-sm-6">
                <input placeholder="pin code...." class="form-control" value="{{ $member_info[0]->memberPersonalMobilePincode }}"
                    name="memberPersonalMobilePincode">
            </div>
            <div class="col-sm-6">
                        <select name="memberPersonalCategory"  class="mt-10 selectpicker" title="Category">
                            <option value="Gen"  {{ $member_info[0]->memberPersonalCategory == 'Gen' ? 'selected' : '' }}>Gen</option>
                            <option value="SC" {{ $member_info[0]->memberPersonalCategory == 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="BC" {{ $member_info[0]->memberPersonalCategory == 'BC' ? 'selected' : '' }}>BC</option>
                            <option value="Other" {{ $member_info[0]->memberPersonalCategory == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                     <div class="col-sm-6">
                     <label> Is Antodya Parwar? </label>
                     <input type="radio" name="is_andtodya" value="Yes" onclick="toggleFidVisibility(this);" {{ $member_info[0]->is_andtodya == 'Yes' ? 'checked' : '' }}>
                     <label for="Yes" >Yes</label>
                     <input type="radio"  name="is_andtodya" value="No" onclick="toggleFidVisibility(this);" {{ $member_info[0]->is_andtodya == 'No' ? 'checked' : '' }}  >
                     <label for="No"  >No</label>
                    </div>
                    <div class="col-sm-6" id="fid" style="{{ $member_info[0]->is_andtodya == 'Yes'  ? '' : 'display:none;' }}">
                        <input placeholder="ppp/Family Id...." value="{{ $member_info[0]->memberpp_familyid }}" name="memberpp_familyid" id="memberpp_familyid" class="form-control" {{ $member_info[0]->is_andtodya == 'Yes' ? 'required' : '' }}>
                    </div>
            <div class="col-md-12 mt-20">
                <h3 class="light-color ">Bank Account Details</h3>
            </div>

            <div class="col-sm-6">
                <input placeholder="Bank name" class="form-control" value="{{ $member_info[1]->memberPersonalBankName }}"
                    name="memberBankName">
            </div>
            <div class="col-sm-6">
                <input placeholder="Account holder Name" value="{{ $member_info[1]->memberPersonalAccountName }}" name="memberAccName" class="form-control">
            </div>
            <div class="col-sm-6">
                    <input placeholder="Account holder Father Name" value="{{ $member_info[1]->memberPersonalAccountFName }}" name="memberAccFName" class="form-control" >
                </div>
            <div class="col-sm-6">
                <input placeholder="Account Number" class="form-control" value="{{ $member_info[1]->memberPersonalAccountNumber }}"
                    name="memberBankNumber">
            </div>
            <div class="col-sm-6">
                <input placeholder="IFSC Code" class="form-control" value="{{ $member_info[1]->memberPersonalIfsc }}"
                    name="memberBankIfsc">
            </div>

            <div class="col-md-12 mt-20">
                <h3 class="light-color">Alert/Notification Setting</h3>
            </div>
            {{--
            <h2>Alert/Notification Setting</h2> --}}

            <div class="col-sm-12">

                <div class="col-sm-3">
                    <input type="checkbox" {{ $member_info[2]->alert_print_slip == 'true' ? 'checked' : '' }} value="true"
                    id="aleryPrintSlip" name="aleryPrintSlip" class="aleryPrintSlip">Print Slip
                </div>
                <div class="col-sm-3">
                    <input type="checkbox" {{ $member_info[2]->alert_sms == 'true' ? 'checked' : '' }} value="true" id="alerySms"
                    name="alerySms" class="alerySms">Sms
                </div>
                <div class="col-sm-3">
                    <input type="checkbox" {{ $member_info[2]->alert_email == 'true' ? 'checked' : '' }} value="true" id="aleryEmail"
                    name="aleryEmail" class="aleryEmail">Email
                </div>

            </div>

            
            <div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div class="pt-20"></div>
                <div class="pt-20"></div>

                <div class="memberSubmitButton">
                    <button type="Submit" id="memberInfoSubmit" class="memberInfoSubmit btn btn-primary btn-block" name="memberInfoSubmit"> Update Member </button>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection



<script type="text/javascript">
    /* member number validation */

     function toggleFidVisibility(radio) {

        var fidInput = document.getElementById('memberpp_familyid');
        var fidDiv = document.getElementById('fid');
        if (radio.value === 'Yes') {
            fidDiv.style.display = 'block';
             fidInput.required = true;
        } else {
            fidDiv.style.display = 'none';
             fidInput.required = false;
        }
    }
  function checkMemberPersonalNumberValidation(){
    var memberNumber = document.getElementById("memberPersonalMobileNumber").value;
    var memberId = document.getElementById("memberId").value;
   
        $.ajax({
           type:"GET",
           url:"{{url('/editMemberNumberValidation')}}?memberNumber="+memberNumber+"&memberId=" + memberId ,
           success:function(res){    
                    
            if(res == "true"){
              document.getElementById("membersNumberErrorMessage").innerHTML = "This number is already being used.";
              document.getElementById("memberPersonalMobileNumber").focus();
            }else if(res == "false"){
              document.getElementById("membersNumberErrorMessage").innerHTML = "";
            }
           }
        });
  }

    /* aadhar number validation */
  function checkMemberPersonalAadarNumber(){
    var aadhar_number  = document.getElementById("memberPersonalAadarNumber").value;
    var memberId = document.getElementById("memberId").value;
    var aadharNumber = aadhar_number.replace(/ /g,'') ;

    if(aadharNumber.length != 12){
      document.getElementById("memberAadharNumberErrorMessage").innerHTML = "This aadhar number is not valid.";
      document.getElementById("memberPersonalAadarNumber").focus();
    }else{
        $.ajax({
           type:"GET",
           url:"{{url('/editMemberAadharNumberValidation')}}?aadharNumber="+aadharNumber+"&memberId=" + memberId ,
           success:function(res){      
            if(res == "true"){
              document.getElementById("memberAadharNumberErrorMessage").innerHTML = "This aadhar number is already being used.";
              document.getElementById("memberPersonalAadarNumber").focus();
            }else if(res == "false"){
              document.getElementById("memberAadharNumberErrorMessage").innerHTML = "";
            }
           }
        });
     
    }
  }

    /* member name validation */
  function memberNameValidation() {
    
     var memberName  = document.getElementById("memberPersonalName").value;
   var memberId = document.getElementById("memberId").value;
       $.ajax({
               type:"GET",
               url:"{{url('/editMemberNameValidation')}}?memberName="+memberName+"&memberId=" + memberId ,
               success:function(res){               
                if(res == "true"){
                  document.getElementById("membersNameErrorMessage").innerHTML = "This Name is already being used.";
                  document.getElementById("memberPersonalName").focus();
                }else if(res == "false"){
                  document.getElementById("membersNameErrorMessage").innerHTML = "";
                }
               }
            });
     
  }

    /* get city by state id */
    function dairyPropritorCityByStatus(){
        console.log("sdaf");
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
                    $("#dairyPropritorCity").selectpicker("refresh" );
                    
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