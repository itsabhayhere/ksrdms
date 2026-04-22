@extends('theme.default') 
@section('content')

<style type="text/css">
    .col-sm-6,
    .col-sm-12,
    .col-sm-4 {
        margin-top: 10px;
    }
</style>

<div class="span-fixed response-alert" id="response-alert"></div>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">

    <a class="nav-back" href="memberList" title="Back to member list">
        <i class="fa fa-angle-left"></i>&nbsp; 
        {{-- <span class="sub">Back to Member List</span> --}}
    </a>

    <div class="fcard clearfix">
        <form method="post" id="regForm" class="pt-0" action="{{url('/memberSetupFormSubmit')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
            <input type="hidden" name="status" value="true">

            <div class="heading">
                <h2>Member Setup</h4>
            </div>

            <div id="London" class="w3-container w3-border clearfix">
                <h3 class="light-color">Personal Information</h2>

                    <div class="col-sm-6">
                        <span class="membersCodeErrorMessage errmsg" id="membersCodeErrorMessage"></span>
                        <input placeholder="Members Code..." value="{{ old('memberPersonalCode') }}" id="memberPersonalCode" class="memberPersonalCode form-control"
                            onchange="CheckMemberPersonalCode()" name="memberPersonalCode" required autocomplete="off">
                    </div>
                    <div class="col-sm-6">
                        <input type="text" placeholder="Registration Date..." id="memberPersonalregisterDate" readonly class="memberPersonalregisterDate datepicker form-control"
                            name="memberPersonalregisterDate" value="{{date(" d-m-Y ",time())}}">
                    </div>

                    <div class="col-sm-6">
                        <span class="membersNameErrorMessage errmsg" id="membersNameErrorMessage"> </span>
                        <input placeholder="Name..." id="memberPersonalName" value="{{ old('memberPersonalName') }}" class="memberPersonalName form-control"
                            onchange="CheckMemberPersonalName()" name="memberPersonalName" required autocomplete="off">
                    </div>
                    <div class="col-sm-4">
                        <input class="form-control" placeholder="Father Name...." value="{{ old('memberPersonalFatherName') }}" name="memberPersonalFatherName"
                            title="father name" autocomplete="off">
                    </div>

                    <div class="col-sm-2">
                        <select name="memberPersonalGender" class="mt-10 selectpicker" title="Gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="col-sm-12">
                        <input placeholder="email...." id="memberPersonalEmail" value="{{ old('memberPersonalEmail') }}" class="memberPersonalEmail form-control"
                            name="memberPersonalEmail" type="email" autocomplete="off">
                    </div>

                    <div class="col-sm-6">
                        <span class="memberAadharNumberErrorMessage errmsg" id="memberAadharNumberErrorMessage"> </span>
                        <input placeholder="Aadhaar No...." id="memberPersonalAadarNumber" value="{{ old('memberPersonalAadarNumber') }}" class="memberPersonalAadarNumber form-control"
                            onchange="checkMemberPersonalAadarNumber()" name="memberPersonalAadarNumber"  autocomplete="off">
                    </div>
                    <div class="col-sm-6">
                        <span class="membersNumberErrorMessage errmsg" id="membersNumberErrorMessage"> </span>
                        <input placeholder="Mobile No... (enter 10 digits)" value="{{ old('memberPersonalMobileNumber') }}" type="number" id="memberPersonalMobileNumber"
                            min="3999999999" max="9999999999" autocomplete="off" class="memberPersonalMobileNumber form-control"
                            onchange="checkMemberPersonalNumberValidation();" name="memberPersonalMobileNumber" required min="3999999999"
                            max="9999999999">
                    </div>

                    <div class="col-sm-12">
                        <textarea placeholder="Address" value="{{ old('memberPersonalAddress') }}" name="memberPersonalAddress" class="form-control"
                            autocomplete="off"></textarea>
                    </div>

                    <div class="col-sm-6">
                        <select name="memberPersonalState" id="memberPersonalState" class="memberPersonalState mt-10 selectpicker" onchange="FunMemberPersonalState()"
                            data-live-search="true" title="Select State">
                        @foreach ($states as $allStates)
                            <option value=" {{$allStates->id}} ">{{$allStates->name}} </option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-sm-6">
                        <select class="memberPersonalCity mt-10 selectpicker" id="memberPersonalCity" name="memberPersonalCity" data-live-search="true"
                            title="Select city">
                            <option></option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <input placeholder="Village...." value="{{ old('memberPersonalDistrictVillage') }}" name="memberPersonalDistrictVillage"
                            class="form-control">
                    </div>

                    <div class="col-sm-6">
                        <input placeholder="pin code...." value="{{ old('memberPersonalMobilePincode') }}" name="memberPersonalMobilePincode" class="form-control">
                    </div>
                    <div class="col-sm-6">
                        <select name="memberPersonalCategory"  class="mt-10 selectpicker" title="Category">
                            <option value="Gen">Gen</option>
                            <option value="SC">SC</option>
                            <option value="BC">BC</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                     <div class="col-sm-6">
                     <label> Is Antodya Parwar? </label>
                     <input type="radio" name="is_andtodya" value="Yes" onclick="toggleFidVisibility(this);">
                     <label for="Yes" >Yes</label>
                     <input type="radio"  name="is_andtodya" checked value="No" onclick="toggleFidVisibility(this);">
                     <label for="No"  >No</label>
                    </div>
                    <div class="col-sm-6" id="fid" style="display:none;">
                        <input placeholder="ppp/Family Id...." value="{{ old('memberpp_familyid') }}" name="memberpp_familyid" id="memberpp_familyid" class="form-control">
                    </div>

            </div>
            <div id="Paris" class="w3-container w3-border clearfix">
                <div class="pt-20"></div>
                <h3 class="light-color">Bank Account Details</h3>
                <div class="col-sm-6">
                    <input placeholder="Bank name or branch" value="{{ old('memberBankName') }}" name="memberBankName" class="form-control">
                </div>
                <div class="col-sm-6">
                    <input placeholder="Account holder Name" value="{{ old('memberAccName') }}" name="memberAccName" class="form-control" >
                </div>
                <div class="col-sm-6">
                    <input placeholder="Account holder Father Name" value="{{ old('memberAccFName') }}" name="memberAccFName" class="form-control" >
                </div>
                <div class="col-sm-6">
                    <input placeholder="Account No." value="{{ old('memberBankNumber') }}" name="memberBankNumber" class="form-control" 
                        autocomplete="off">
                </div>
                <div class="col-sm-6">
                    <input placeholder="IFSC Code" value="{{ old('memberBankIfsc') }}" name="memberBankIfsc" class="form-control">
                </div>
                {{--
                <div class="col-sm-6">
                    <input placeholder="Branch Code" value="{{ old('memberBankBranchCode') }}" name="memberBankBranchCode" class="form-control">
                </div> --}}
            </div>

            <div class="w3-container w3-border clearfix">
                <div class="pt-20"></div>
                <h3 class="light-color">Opening balance Details</h3>
                <div class="col-sm-6">
                    <input placeholder="Opening Balance" value="{{ old('memberPersonalOpeningBalance') }}" class="memberPersonalOpeningBalance form-control"
                        id="memberPersonalOpeningBalance" name="memberPersonalOpeningBalance" required>
                </div>
                <div class="col-sm-6">
                    <select name="openingBalanceType" class="openingBalanceType selectpicker" name="openingBalanceType" id="openingBalanceType"
                        title="Opening Balance Type" required>
                        <option value="credit">Credit</option>
                        <option value="debit">Debit</option>
                    </select>
                </div>

                <div class="col-sm-6"></div>
            </div>

            {{-- <div id="Tokyo" class="w3-container w3-border clearfix">
                <div class="pt-20"></div>

                <h3 class="light-color">Milk Collection Details</h3>

                <div class="col-sm-8 col-sm-offset-2">
                    <div class="col-sm-6">
                        <label class="fr lh-60">Type of Milk Supplied</label>
                    </div>
                    
                    <div class="col-sm-6">
                        <select name="milkType" class="mt-10 selectpicker" title="Select Cow Or Buffalo" required>
                            <option value="cow">Cow</option>
                            <option value="buffalo">Buffalo</option>
                        </select>
                    </div>
                </div>
            </div> --}}

            <div class="w3-container w3-border clearfix">
                <div class="pt-20"></div>
                <h3 class="light-color">Alert/Notification Setting</h3>

                <div class="col-sm-12">

                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="true" id="aleryPrintSlip" name="aleryPrintSlip" class="aleryPrintSlip">Print Slip
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="true" id="alerySms" name="alerySms" class="alerySms">SMS
                            </label>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="true" id="aleryEmail" name="aleryEmail" class="aleryEmail">Email
                            </label>
                        </div>

                    </div>

                </div>
                <div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                    <div class="pt-20"></div>
                    <div class="pt-20"></div>

                    <div class="memberSubmitButton">
                        <button type="Submit" id="memberInfoSubmit" class="memberInfoSubmit btn btn-primary btn-block" name="memberInfoSubmit"> Add Member </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
 {{--
<script src="{{ asset('js/addon/dairyInfo.js') }}">
    --}}

</script>

<script type="text/javascript">
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
    /* aadhar number validation */
    function checkMemberPersonalAadarNumber(){
        var aadhar_number  = document.getElementById("memberPersonalAadarNumber").value;
        var aadharNumber = aadhar_number.replace(/ /g,'') ;

        if(aadharNumber.length != 12){
            $("#response-alert").html("Please enter 12 digit Aadhar number, this is not valid.").show();
            $("#memberPersonalAadarNumber").addClass("has-error").focus();
        }else{
            $.ajax({
                type:"GET",
                url:"{{url('/memberAadharNumberValidation')}}?aadharNumber="+aadharNumber ,
                success:function(res){      
                    if(res == "true"){
                        $("#response-alert").html("This Aadhar number is already being used.").show();
                        $("#memberPersonalAadarNumber").addClass("has-error").focus();
                    }else if(res == "false"){
                        $("#response-alert").html("").hide();
                        $("#memberPersonalAadarNumber").removeClass("has-error");
                    }
                }
            });
        }
    }

/* member number validation */

    function checkMemberPersonalNumberValidation(){

        var memberNumber = $("#memberPersonalMobileNumber").val();
        if(memberNumber.length != 10){
            $("#response-alert").html("Please enter 10 digit Mobile number, this is not valid.").show();
            $("#memberPersonalMobileNumber").addClass("has-error").focus();
            return;
        }
        $.ajax({
            type:"GET",
            url:"{{url('/memberNumberValidation')}}?memberNumber="+memberNumber ,
            success:function(res){               
                if(res == "true"){
                    $("#response-alert").html("This Mobile number is already registered in this dairy.").show();
                    $("#memberPersonalMobileNumber").addClass("has-error").focus();
                }else if(res == "false"){
                    $("#response-alert").html("").hide();
                    $("#memberPersonalMobileNumber").removeClass("has-error");
                }
            }
        });        
    }


    /* member code validation */
    function CheckMemberPersonalCode(){
        var personal_code  = document.getElementById("memberPersonalCode").value;
        if(personal_code){
            $.ajax({
                type:"GET",
                url:"{{url('/memberPersonalCode')}}?personal_code="+personal_code,
                success:function(res){
                    if(res == "true"){                        
                        $("#response-alert").html("This Member code is already belongs to a Member of your dairy.").show();
                        $("#memberPersonalCode").addClass("has-error").focus();
                    }else if(res == "false"){
                        $("#response-alert").html("").hide();
                        $("#memberPersonalCode").removeClass("has-error");
                    }
                }
            });
        }
    }

    /* member Name validation */
    function CheckMemberPersonalName(){
        var memberName  = document.getElementById("memberPersonalName").value;
        if(memberName){
            $.ajax({
                type:"GET",
                url:"{{url('/memberNameValidation')}}?memberName="+memberName,
                success:function(res){
                    if(res == "true"){                        
                        $("#response-alert").html("This Member name is already belongs to a Member of your dairy. please choose some different name").show();
                        $("#memberPersonalName").addClass("has-error").focus();
                    }else if(res == "false"){
                        $("#response-alert").html("").hide();
                        $("#memberPersonalName").removeClass("has-error");
                    }
                }
            });
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
                        $("#memberPersonalCity").selectpicker("refresh");
                    }else{
                    $("#memberPersonalCity").empty();
                    }
                }
            });
        }else{
            $("#memberPersonalCity").empty();
        }
    }

</script>