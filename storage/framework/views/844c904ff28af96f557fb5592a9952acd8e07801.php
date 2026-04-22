 
<?php $__env->startSection('content'); ?>

<style>
    .r-font-12 {
        font-weight: 100;
    }
    .mr-35{
        margin-right: 35px;
    }
</style>

<div class="clearfix " style="margin-top:-30px;">

    <div class="col-md-12 login-panel-custom ">
        <div class="panel panel-default">

            <div class="panel-body" style="padding:5px 15px">

                <div class="col-md-7 col-sm-12">
                    <br>

                    <div class="r-font-12" style="font-weight: 100;">
                        <div class="text-center" style="font-size:40px">Get your dashboard</div>
                        <br>
                        <div class="text-center" style="font-size:32px;">
                            <span style="color: rgb(102, 167, 17);font-weight: 500;">Register Now</span> in DMS
                        </div>
                        <div class="text-center" style="font-size:28px;">
                            & Get <span style="color: rgb(255, 101, 0);font-weight: 500;">30 days free trial</span>
                        </div>

                        <br>

                        <div class="text-center">
                            <div>
                                <a href="<?php echo e(url('buy')); ?>" class="btn btn-success btn-register btn-lg" style="background: #66a711;border-color: #5e9416;">Register For Free</a>
                            </div>
                            <div>
                                <a href="https://play.google.com/store/apps/details?id=com.dmsdairy">
                                    <img src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png" style="width: 150px;" alt="Ksr service DMS Dairy Management System">
                                </a>        
                            </div>
                        </div>

                        <div class="text-center" style="font-size:22px;letter-spacing:1px">
                            <br> For more information, don't hesitate to call us: <a href="tel:+919499194291" class="">
                               <span style="font-weight: bold;">+91&nbsp;94991&nbsp;94291</span>
                            </a>
                        </div>
                        
                        <div class="text-center" style="font-size:20px">Or</div>
                        <div class="text-center" style="font-size:22px;letter-spacing:1px">
                            Mail us <a href="mailto:support@ksrdms.com" style="font-weight: bold;">support@ksrdms.com</a>
                        </div>
                    </div>
                    <br><br><br>

                </div>

                <div class="col-md-5 col-sm-12">
                   
                    <br>
                    <br>
                  
                    <div class="panel-heading text-center" style="font-size:28px">Login to your dairy</div>

                    <form class="form-horizontal" method="POST" action="<?php echo e(url('/my-login-submit')); ?>" id="login-form" onsubmit="event.preventDefault();userLogin();">
                        <input type="hidden" value="dairy" id="loginType" name="loginType">
                        <span type="hidden" name="loginMessageError" class="loginMessageError errmsg" id="loginMessageError"> </span>                        <?php echo e(csrf_field()); ?>

                        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                            <label for="email" class="col-md-4 col-sm-12 control-label">Mobile Number</label>

                            <div class="col-md-8 col-sm-12">
                                <span class="MobileNumberErrorMessage errmsg" id="MobileNumberErrorMessage"> </span>
                                <!--<input id="MobileNumber" pattern="[0-9]{10}" type="text" class="form-control" name="username" placeholder="10 digit Mobile number"-->
                                <!--    value="<?php echo e(old('username')); ?>" required>-->
                                     <input id="MobileNumber" type="text" class="form-control" name="username" placeholder="10 digit Mobile number"
                                    value="<?php echo e(old('username')); ?>" required>
                            </div>
                        </div>

                        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="password" class="col-md-4 col-sm-12 control-label">Password</label>

                            <div class="col-md-8 col-sm-12">
                                <span id="passwordErrorMessage" class="passwordErrorMessage errmsg"> </span>
                                <input id="password" type="password" class="form-control" placeholder="Enter your password" name="password" required>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <div class="col-md-4 col-sm-12"></div>
                            <div class="col-md-8 col-sm-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?> style="margin-left:-15px"> 
                                        <span style="margin-left:10px">Remember Me</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 col-sm-12">
                                <div class="fl">

                                </div>

                                <div class="fr">
                                    <a class="fl btn btn-default mr-35" href="#" onclick="loginOtpArea()">
                                            Login With OTP
                                    </a>
        
                                    <button type="submit" class="fl btn btn-primary"> Login </button>
                                </div>

                                
                            </div>
                        </div>
                    </form>

                    <form action="" class="form-horizontal dnone" id="login-form-otp">
                        <span type="hidden" name="loginMessageError" class="loginMessageError errmsg" id="loginMessageError"> </span>                        <?php echo e(csrf_field()); ?>

                        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                            <label for="email" class="col-md-4 control-label">Mobile Number</label>

                            <div class="col-md-6">
                                <span class="MobileNumberErrorMessage errmsg" id="MobileNumberErrorMessage"> </span>
                                <input id="MobileNumberOtp" max="10" type="number" class="form-control" name="username" value="<?php echo e(old('username')); ?>" required
                                    autofocus>
                            </div>
                        </div>

                        <div class="dnone otp-field form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="otp" class="col-md-4 control-label">OTP</label>
                            <div class="col-md-6">
                                <span id="otpErrorMessage" class="otpErrorMessage errmsg"> </span>
                                <input id="otp" type="password" class="form-control" name="otp" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">

                                <div class="fl">
                                    <button type="button" onclick="userOtpLogin(event)" class="btn btn-primary dnone otp-login-btn"> Login </button>

                                    <div class="timerdiv dnone" style="float: right;padding: 10px;">didn't get otp, wait <span id="timer"></span></div>
                                    <button type="button" onclick="sendOtp1(event)" class="btn btn-primary sendotp-btn"> Send OTP </button>
                                </div>

                                <div class="fr">
                                    <a class="btn btn-default" href="#" onclick="loginPassArea()">
                                                Login With Password
                                        </a>
                                </div>

                                
                            </div>
                        </div>
                    </form>
                </div>
                <br>
                <br>

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
        loader("show");
        $.ajax({
            type:"POST",
            url:'my-login-submit' ,
            data: {
                username: MobileNumber,
                password: password,
                loginType: loginType,
            },
            success:function(res){
                if(res.success == "true"){
                    $(".flash-alert .flash-msg").html(res.message);
                    $(".flash-alert").show("slide").addClass("alert-success").removeClass("alert-danger");
                    window.location.href = res.url;
                }else{
                    $(".flash-alert .flash-msg").html(res.message);
                    $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                }
                loader("hide");
           },
           error:function(res){
                $(".flash-alert .flash-msg").html("Some error has occured, please try again.");
                $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                loader("hide");
           }
        }).done(function(res){
                loader("hide"); 
                //  console.log(res);
            });
        }
    }

    function loginPassArea(){
        $("#login-form-otp").slideUp();
        $("#login-form").slideDown();
        $("#MobileNumber").val($("#MobileNumberOtp").val());
    }

    function loginOtpArea(){
        $("#login-form").slideUp();
        $("#login-form-otp").slideDown();
        $("#MobileNumberOtp").val($("#MobileNumber").val());
    }


    function sendOtp1(e){
        e.stopPropagation();

        console.log("jreur");
        var MobileNumber = document.getElementById("MobileNumberOtp").value;

        if(MobileNumber != ""){
            loader("show");
            $.ajax({
                type:"POST",
                url:'<?php echo e(url('sendLoginOtp')); ?>',
                data: {
                    username: MobileNumber,
                },success:function(res){
                    if(!res.error){
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").show("slide").addClass("alert-success").removeClass("alert-danger");
                        $(".otp-field").removeClass("dnone").show();
                        $(".otp-login-btn").removeClass("dnone").show();
                        $(".sendotp-btn").addClass("dnone");
                        $(".timerdiv").removeClass('dnone').show();
                        otpAgainTimer(45);
                    }else{
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                    }
                    loader("hide");
                },error:function(res){
                    $(".flash-alert .flash-msg").html("Some error has occured, please try again.");
                    $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                    loader("hide");
                }
            }).done(function(res){
                loader("hide");
                // console.log(res);
            });
        }

    }

    function userOtpLogin(){
        var MobileNumber  = document.getElementById("MobileNumberOtp").value;
        var otp  = document.getElementById("otp").value;
        var loginType  = document.getElementById("loginType").value;

        console.log(otp);

        if(MobileNumber != "" && otp != ""){
            loader("show");
            console.log(otp);

            $.ajax({
                type:"POST",
                url:'<?php echo e(url('loginOtp')); ?>' ,
                data: {
                    username: MobileNumber,
                    otp: otp,
                    loginType: loginType,
                },success:function(res){
                    if(!res.error){
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").show("slide").addClass("alert-success").removeClass("alert-danger");
                        window.location.href = res.url;
                    }else{
                        $(".flash-alert .flash-msg").html(res.msg);
                        $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                    }
                },error: function(res){
                    $(".flash-alert .flash-msg").html("Some error has occured, please try again.");
                    $(".flash-alert").show("slide").addClass("alert-danger").removeClass("alert-success");
                    loader("hide");
                }
            }).done(function(res){
                loader("hide");
                // console.log(res);
            });
        }
    }

    let timerOn = true;

    function otpAgainTimer(remaining) {
        var m = Math.floor(remaining / 60);
        var s = remaining % 60;
        
        m = m < 10 ? '0' + m : m;
        s = s < 10 ? '0' + s : s;
        document.getElementById('timer').innerHTML = m + ':' + s;
        remaining -= 1;
        
        if(remaining >= 0 && timerOn) {
            setTimeout(function() {
                otpAgainTimer(remaining);
            }, 1000);
            return;
        }

        if(!timerOn) {
            // Do validate stuff here
            return;
        }
        
        // Do timeout stuff here
        // alert('Timeout for otp');
        $(".timerdiv").hide();
        $(".sendotp-btn").addClass("btn-link").removeClass("dnone btn-primary").text("Send otp again").show();

    }


        function loader(cmd){
            if(cmd=="show"){
                // $("#wrapper").addClass("blur-3");
                $(".loader").show();
            }else{
                $(".loader").hide();
                // $("#wrapper").removeClass("blur-3");
            }
        }

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>