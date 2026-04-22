@extends('layouts.app') 
@section('content')


<div class="container">
    <div class="row">
        <div class="col-md-8 login-panel-custom">
            <div class="panel panel-default">
                <div class="panel-heading text-center" style="font-size:36px">Super Admin Login</div>
                <div class="panel-body" style="padding:30px 15px">
                    <form class="form-horizontal" method="POST" action="{{ url('/sa/login') }}">
                        <input type="hidden" value="dairy" id="loginType" name="loginType">
                        <span type="hidden" name="loginMessageError" class="loginMessageError errmsg" id="loginMessageError"> </span>                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Username</label>

                            <div class="col-md-6">
                                <span class="MobileNumberErrorMessage errmsg" id="MobileNumberErrorMessage"> </span>
                                <input id="MobileNumber" type="text" class="form-control" name="username" value="{{ old('username') }}" required
                                    autofocus>
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
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} style="margin-left:-15px"> 
                                        <span style="margin-left:10px">Remember Me</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="fl">
                                    <button type="button" onclick="userLogin();" class="btn btn-primary"> Login </button>
                                </div>
                                <div class="fr">
                                    <a class="btn btn-default" href="{{ route('password.request') }}">
                                        Forgot Your Password?
                                    </a>
                                </div>
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
        var username  = document.getElementById("MobileNumber").value;
        var password  = document.getElementById("password").value;

        if(MobileNumber != "" && password != ""){
            $.ajax({
                type:"POST",
                url:"{{url('sa/login')}}" ,
                data: {
                    username: username,
                    password: password,
                },
                success:function(res){
                    console.log(res);
                    if(res.success == "true"){
                        $(".flash-alert .flash-msg").html(res.message);
                        $(".flash-alert").show("slide").addClass("alert-success").removeClass("alert-danger");
                        window.location.href = "sa/dairyList";
                    }else{
                        $(".flash-alert .flash-msg").html(res.message);
                        $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                    }
                },
                error:function(res){
                    console.log(res);
                }
            });
        }
    }

</script>
@endsection