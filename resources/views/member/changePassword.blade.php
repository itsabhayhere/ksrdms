@extends('member.layout')

@section('content')

<div class="pageblur">
    <div class="clearfix">
        <div class="clearfix">
            <div class="heading">
                <div class="fl">
                    {{-- <h3>Change Password</h3> --}}
                        {{-- <hr class="m-0">         --}}
                </div>
            </div>
        </div>
    

        <div class="form-gap"></div>
        <div class="">
            <div class="row">
                <br>
                <br>
                <br>
                <div class="col-md-5 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="text-center">
                                <h3><i class="fa fa-lock fa-4x" style="font-size:110px; color: #4c4c4c;"></i></h3>
                                <h2 class="text-center">Change Password</h2>
                                <p>Enter your Current password here.</p>
                                <div class="panel-body">
                                    <div class="chngpass-res-message dnone"></div>                    

                                    <form id="currPass-form" role="form" autocomplete="off" class="form" method="post" onsubmit="event.preventDefault();">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-key color-blue"></i></span>
                                                <input id="curpass" name="curpass" placeholder="Current Password" class="form-control" type="password" autofocus>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input name="recover-submit" class="btn btn-primary btn-block" value="Proceed" type="submit">
                                        </div>
                                        <div class="form-group">
                                            <br>
                                            <a class="btn btn-sm btn-default btn-block" href="{{url('member/dashboard')}}">Cancel</a>
                                        </div>    
                                    </form>

                                    <form id="newPass-form" role="form" autocomplete="off" class="form dnone" method="post" onsubmit="event.preventDefault();">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-key color-blue"></i></span>
                                                <input id="newPass" name="curpass" placeholder="New Password" class="form-control" type="password">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-key color-blue"></i></span>
                                                <input id="conNewPass" name="curpass" placeholder="Confirm New Password" class="form-control"  type="password">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <input name="recover-submit" class="btn btn-primary btn-block" value="Change Password" type="submit">
                                        </div>
                                        
                                    </form>
                        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>
        <br>
        <br>

    </div>
</div>



<script>

    $("#currPass-form").on("submit", function(e){
        e.preventDefault();

        pass = $("#curpass").val();

        loader("show");
        $.ajax({
            type:"POST",
            url:'{{url('checkPassword')}}',
            data: {
                pass: pass
            },
            success:function(res){
                if(res.error){
                    $(".chngpass-res-message").html(res.msg).fadeIn().addClass("error").removeClass("success").slideDown();
                }else{
                    $(".chngpass-res-message").html(res.msg).fadeIn().addClass("success").removeClass("error").slideDown();
                    showNewPassField();
                }
            },
            error:function(res){
                $.alert("Something went wrong.");
                loader("hide");
            }
        }).done(function(res){
            loader("hide");
        });
    })

    function showNewPassField(){
        $("#currPass-form").slideUp();
        $("#newPass-form").slideDown();
        $("#newPass").focus();
    }

    
    $("#newPass-form").on("submit", function(e){
        e.preventDefault();

        pass = $("#curpass").val();
        npass = $("#newPass").val();
        cnpass = $("#conNewPass").val();

        loader("show");
        $.ajax({
            type:"POST",
            url:'{{url('setNewPassword')}}',
            data: {
                pass: pass,
                npass: npass,
                cnpass: cnpass
            },
            success:function(res){
                if(res.error){
                    $(".chngpass-res-message").html(res.msg).fadeIn().addClass("error").removeClass("success").slideDown();
                }else{
                    $(".chngpass-res-message").html(res.msg).fadeIn().addClass("success").removeClass("error").slideDown();
                    window.location = "{{url('member/dashboard')}}";
                }
            },
            error:function(res){
                $.alert("Something went wrong.");
                loader("hide");
            }
        }).done(function(res){
            loader("hide");
        });
    })

</script>
@endsection