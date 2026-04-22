@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">User Login</div>
				<div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ url('/my-login-submit') }}">
                        <input type="hidden" value="user" id="loginType" name="loginType">
                    	<span type="hidden" name="loginMessageError" class="loginMessageError errmsg" id="loginMessageError"> </span>
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Mobile Number</label>
                            
                            <div class="col-md-6">
                            	<span class="MobileNumberErrorMessage errmsg" id="MobileNumberErrorMessage" > </span>
                                <input id="MobileNumber" max="10"  type="number" class="form-control" name="MobileNumber" value="{{ old('MobileNumber') }}" required autofocus>
							</div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>
                            
							<div class="col-md-6">
								<span id="passwordErrorMessage" class="passwordErrorMessage errmsg"> </span>
                                <input id="password" type="password" class="form-control" name="password" required>
							</div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                               <!--  <button type="submit" class="btn btn-primary">
                                    Login
                                </button> -->
                                <span onclick="userLogin();" class="btn btn-primary"> Login </span>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	function userLogin() {
		var MobileNumber  = document.getElementById("MobileNumber").value;
        var password  = document.getElementById("password").value;
		var loginType  = document.getElementById("loginType").value;
	

		if(MobileNumber != "" && password != ""){
		$.ajax({
            type:"POST",
            url:'my-login-submit' ,
            data: {
                MobileNumber: MobileNumber,
                password: password,
                loginType: loginType,
            },
           	success:function(res){
            if(res.success == "true"){
	           	window.location.href = "DairyAdminDashbord";
	        }else{
	        	document.getElementById("loginMessageError").innerHTML = res.message ;
	        }
           }
        });
		}
	}
</script>
@endsection
