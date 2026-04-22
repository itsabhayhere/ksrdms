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

    <a class="nav-back" href="colMans" title="Back to Collection Managers list">
        <i class="fa fa-angle-left"></i>&nbsp; 
    </a>

    <div class="fcard clearfix">
        <form method="post" id="regForm" class="pt-0" action="{{url('/createColMan')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
            <input type="hidden" name="status" value="true">

            <div class="heading">
                <h2>New Collection Manager</h4>
            </div>

            <div id="London" class="w3-container w3-border clearfix">

                    <div class="col-sm-7">
                        <span class="userNameerror errmsg" id="userNameerrorMessage"></span>
                        <input placeholder="User Name" value="{{ old('userName') }}" id="userName" class="userName form-control"
                            onchange="CheckUserName()" name="userName" required autocomplete="off">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" placeholder="Date" id="registerDate" readonly class="datepicker form-control"
                            name="registerDate" value="{{date(" d-m-Y ",time())}}">
                    </div>

                    <div class="col-sm-2 pt-10">
                        <select name="gender" class="selectpicker" title="Gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div class="col-sm-5 pt-10">
                        <input placeholder="Email" id="email" value="{{ old('email') }}" class="email form-control"
                            name="email" type="email" autocomplete="off">
                    </div>

                    <div class="col-sm-5 pt-10">
                        <span class="membersNumberErrorMessage errmsg" id="membersNumberErrorMessage"> </span>
                        <input placeholder="Mobile No... (enter 10 digits)" value="{{ old('mobileNumber') }}" type="number" id="mobileNumber"
                            min="3999999999" max="9999999999" autocomplete="off" class="mobileNumber form-control"
                            onchange="mobileNumberValidation();" name="mobileNumber" required min="3999999999"
                            max="9999999999">
                    </div>

                    <div class="col-sm-12">
                        <textarea placeholder="Address" value="{{ old('address') }}" name="address" class="form-control"
                            autocomplete="off"></textarea>
                    </div>

            </div>

            <div class="w3-container w3-border clearfix">
                <div class="col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                    <div class="pt-20"></div>
                    <div class="pt-20"></div>

                    <div class="submitButton">
                        <button type="Submit" id="submit" class="submit btn btn-primary btn-block" name="submit"> Add</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection


<script type="text/javascript">

/* member number validation */

    function mobileNumberValidation(){

        var mobileNumber = $("#mobileNumber").val();
        if(mobileNumber.length != 10){
            $("#response-alert").html("Please enter 10 digit Mobile number, this is not valid.").show();
            $("#mobileNumber").addClass("has-error").focus();
            return;
        }
        $.ajax({
            type:"GET",
            url:"{{url('/colManMobileNumberValidation')}}?mobileNumber="+mobileNumber,
            success:function(res){               
                if(res.error){
                    $("#response-alert").html(res.msg).show();
                    $("#mobileNumber").addClass("has-error").focus();
                }else{
                    $("#response-alert").html("").hide();
                    $("#mobileNumber").removeClass("has-error");
                }
            }
        });        
    }


    /* member code validation */
    function CheckUserName(){
        var userName  = $("#userName").val();
        if(userName){
            $.ajax({
                type:"GET",
                url:"{{url('/colMansUserName')}}?userName="+userName,
                success:function(res){
                    if(res.error){                        
                        $("#response-alert").html(res.msg).show();
                        $("#userName").addClass("has-error").focus();
                        $("#submit").attr("disabled", true);
                    }else{
                        $("#response-alert").html("").hide();
                        $("#userName").removeClass("has-error");
                        $("#submit").attr("disabled", false);
                    }
                }
            });
        }
    }



</script>