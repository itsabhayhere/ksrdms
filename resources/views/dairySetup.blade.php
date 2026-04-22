 @extends('theme.default')

@section('content')
<!-- custome css   -->
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

  <link href="{{ asset('css/addon/style.css') }}" rel="stylesheet" />
  <link href="{{ asset('css/addon/tabs.css') }}" rel="stylesheet" />
  <script type="text/javascript" href="{{ asset('js/addon/bootstrap.js') }}" >  </script>
  <!-- <link href="{!! asset('public/css/addon/11.css') !!}" rel="stylesheet" type="text/css"> -->
  <!-- <script src="{!! asset('js/addon/jquery-1.3.2.min.js') !!}"></script>   -->
  <!-- <script src="{!! asset('js/addon/jquery-ui-1.7.custom.min.js') !!}"></script>   -->
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

button#DairyInfoSubmit {
    margin: 11px 0 28px 234px;
}
  bs-datepicker-container { 
  	z-index: 3000;
  }
  .errmsg{
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

<div class="container">
	<div class="row">
        <form method="post" id="regForm" action="{{url('/addDairyAdminSubmit')}}">
					
                    <h1>Daily Setup Wizard </h1>

                    <h1>Few Details About Your Dairy:</h1>
                    <input type="hidden" name="createBySuperAdmin" value="1">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="status" value="status">
                    <div class="col-sm-6">
                        <input placeholder="Dairy name" required="required" name="society_name">
                    </div>
                    <div class="col-sm-6">
                        <span class="societyCodeErrormessage errmsg" id="societyCodeErrormessage"> </span>
                        <input placeholder="Society Code" required="required" type="text" class="society_code" id="society_code" onfocusout="checkSociety_code()" name="society_code">
                    </div>

                    <div class="col-sm-12">
                        <textarea placeholder="Address" id="dairyInfoAddressId" required="required" name="dairyInfoAddressId" style="width:100%; height:150px;"> </textarea>
                    </div>

                    <div class="col-sm-6">
                        <select name="state" id="dairyInfoState" onchange="dairyInfogetCityByStatus()" style="width:100%; margin-top:10px; height:47px;">
                            <option>--States-- </option>
                            @foreach ($states as $allStates)
                            <option value="{{$allStates->id}}">{{$allStates->name}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <select name="city" id="dairyInfoCity" style="width:100%; margin-top:10px; height:47px;">
                            <option>-- citys --</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <input id="dairyVIllageDistrict" placeholder="Village and District...." oninput="this.className = ''" name="district">
                    </div>

                    <div class="col-sm-6">
                        <input id="dairyPincode" placeholder="pin code...." oninput="this.className = ''" name="pincode">
                    </div>

                    <div class="col-sm-12" style="text-align:left; margin-top:20px;">
                        <h1>Dairy Propritor Details : </h1>
                    </div>

                    <div class="col-sm-6">
                        <input placeholder="Name..." required="required" name="dairyPropritorName">
                    </div>
                    <div class="col-sm-6">
                    	<span class="numberErrorMessage errmsg" id="numberErrorMessage"> </span>
                        <input type="number" placeholder="Mobile No...." max="10"  onfocusout="CheckdairyPropritorNumber()" required="required" id="PropritorMobile" class="PropritorMobile" name="PropritorMobile">
                    </div>

                    <div class="col-sm-12">
                        
                        <input placeholder="Email...." required="required" type="email"  id="dairyPropritorEmail" class="dairyPropritorEmail" name="dairyPropritorEmail">

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
                        <select name="dairyPropritorState" id="dairyPropritorState" onchange="dairyPropritorCityByStatus()" style="width:100%; margin-top:10px; height:47px;">
                            <option>--States-- </option>
                            @foreach ($states as $allStates)
                            <option id="state_{{$allStates->id}}" value="{{$allStates->id}}">{{$allStates->name}} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <select name="dairyPropritorCity" id="dairyPropritorCity" style="width:100%; margin-top:10px; height:47px;">
                            <option>-- citys --</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <input placeholder="Village and District...." id="dairyPropritorDistrict" name="dairyPropritorDistrict">
                    </div>

                    <div class="col-sm-6">
                        <input placeholder="pin code...." id="dairyPropritorPincode" name="dairyPropritorPincode">
                    </div>
                     
                   <div class="col-sm-6"> 
                      <input type="number" name="openingBalance" id="openingBalance" class="openingBalance">
                   </div> 
                   <div class="col-sm-6">
                      <select id="openingBalanceType" name="openingBalanceType" class="openingBalanceType" style="width:100%; margin-top:10px; height:47px;">
                        <option value="credit">Credit </option>
                        <option value="debit"> Debit </option>
                      </select>
                   </div> 
                     
                    <div class="col-sm-6"> 
                     	<select id="rateCardType" name="rateCardType" class="rateCardType" style="width:100%; margin-top:10px; height:47px;">
                     		<option value="fat">Rate on Fat</option>
                     		<option value="fat/snf"> Rate on Fat/Snf </option>
                     	</select>
                    </div>
                    <div class="col-sm-6 dairySubmitButton" >
                        <button type="Submit" id="DairyInfoSubmit" class="DairyInfoSubmit" name="DairyInfoSubmit"> Submit </button>
                    </div>

		</form>
    </div>
</div>

@endsection

<script src="{{ asset('js/addon/dairyInfo.js') }}">  </script>

<script type="text/javascript">

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

   /*
      sate same address as dairy infomation 
    */
    function getSameAddress(){

        if($("#sameAddressChecked").prop('checked') == true){
         var dairyInfoAddress = $("#dairyInfoAddressId").val();
         var dairyInfoState = $("#dairyInfoState").val();
         var dairyInfoCity = $("#dairyInfoCity").val();

         var dairyVIllageDistrict = $("#dairyVIllageDistrict").val();
         var dairyPincode = $("#dairyPincode").val();
		 
		    $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/city')}}?state_id="+dairyInfoState,
               success:function(res){    

                if(res){
                    $("#dairyPropritorCity").empty();
                    $.each(res,function(key,value){
                    	if(key == dairyInfoCity){
                    		$("#dairyPropritorCity").append('<option selected="true" value="'+key+'">'+value['name']+'</option>');
                    	}else{
                    		$("#dairyPropritorCity").append('<option value="'+key+'">'+value['name']+'</option>');	
                    	}
                    });
               	}else{
                   $("#dairyPropritorCity").empty();
                }
               }
            });
       
      
		$("#dairyPropritorAddress").val(dairyInfoAddress);
		$("#dairyPropritorDistrict").val(dairyVIllageDistrict);
		$("#dairyPropritorPincode").val(dairyPincode);
        document.getElementById("state_"+dairyInfoState).selected = "true";
        }else{
            $("#dairyPropritorAddress").val("");
          	$("#dairyPropritorAddress").val("");
			$("#dairyPropritorDistrict").val("");
			$("#dairyPropritorPincode").val("");
	        document.getElementById("state_"+dairyInfoState).selected = "false";
	       
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

    function CheckdairyPropritorNumber(){
     
      var PropritorMobile  = document.getElementById("PropritorMobile").value;
        $.ajax({
               type:"GET",
               url:"{{url('/add-dairy-admin/numberValidate')}}?PropritorMobile="+PropritorMobile ,
               success:function(res){               
                if(res == "true"){
                  document.getElementById("numberErrorMessage").innerHTML = "This number is already being used.";
                  document.getElementById("PropritorMobile").focus();
                }else if(res == "false"){
                  document.getElementById("numberErrorMessage").innerHTML = "";
                }
               }
            });
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

</script>
