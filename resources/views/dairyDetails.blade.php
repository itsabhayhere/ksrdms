@extends('theme.default')

@section('content')


<style>
    .res-message{
        padding: 10px;
        text-align: left;
        margin: 5px 0;
        font-size: 15px;
        border: 1px solid #aaa;
        border-left: 3px solid;
    }
    .res-message.error{
        border: 1px solid #790202;
        border-left: 5px solid #8a0000;
        background: #b72121;
        color: white;
    }
    .res-message.success{
        border: 1px solid #266d26;
        border-left: 5px solid #266d26;
        background: #4bab4b;
        color: white;
    }
</style>


<div class="pageblur">
    <div class="fcard margin-fcard-1 pt-25 clearfix">
        <div class="clearfix">
            <div class="heading">
                <div class="fl">
                    <h2>Update Dairy Details</h2>
                    <hr class="m-0">        
                </div>
            </div>
        </div>

        <div class="pt-30"></div>

        <div class="row">
            <div class="col-md-6 col-md-offset-2">
                <form name="dairyDetailsForm" action="{{url('updateDairyDetails')}}" method="post">

                    <br>
                    <a class="btn btn-sm btn-primary" href="{{url('dairy-settings')}}"> <i class="fa fa-arrow-circle-left"></i> Return </a>
                
                    <h3>Dairy details</h3>
                    <div class="clearfix">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name">Dairy Name:</label>
                            <input type="text" class="form-control" name="name" value="{{$dairy->dairyName}}" id="dairyName" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="dairyCode">Dairy Code:</label>
                                <input type="text" class="form-control" name="code" value="{{$dairy->society_code}}" id="dairyCode" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="dairyContact">Dairy Contact:</label>
                                <input type="number" class="form-control" name="mobile" value="{{$dairy->mobile}}" id="dairyContact" required>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="dairyContact">Dairy Address:</label>
                                <textarea maxlength="200" required="required" name="address" class="form-control" id="dairyContact" placeholder="Address">{{$dairy->dairyAddress}}</textarea>
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">State</label>
                                <select name="state" class="selectpicker" id="state"  title="Select State" onchange="getCity(this, 'ddistrict')" required data-live-search="true">
                                    @foreach ($states as $stt)
                                        @php if($stt->id == $dairy->state) $s = 'selected="selected"'; else $s=""; @endphp
                                        <option value="{{$stt->id}}" {{$s}}>{{$stt->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label">District</label>
                                <select name="district" class="selectpicker" id="ddistrict"  title="Select District" required data-live-search="true">
                                    @foreach ($dcity as $c)
                                        @php if($c->id == $dairy->city) $s = 'selected="selected"'; else $s=""; @endphp
                                        <option value="{{$c->id}}" {{$s}}>{{$c->name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <h3>Propritor details</h3>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="owName">Propritor Name:</label>
                            <input type="text" class="form-control" name="owname" value="{{$propritor->dairyPropritorName}}" id="owName" required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="owMobile">Propritor Mobile No.:</label>
                            <input type="text" class="form-control" name="owmobile" value="{{$propritor->PropritorMobile}}" id="owMobile" readonly required>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="owEmail">Propritor Email:</label>
                            <input type="text" class="form-control" name="owemail" value="{{$propritor->dairyPropritorEmail}}" id="owEmail" readonly required>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label">Address</label>
                            <textarea maxlength="200" required="required" name="owaddress" class="form-control" id="paddress" placeholder="Address">{{$propritor->dairyPropritorAddress}}</textarea>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">State</label>
                            <select name="owstate" class="selectpicker" id="pstate"  title="Select State" onchange="getCity(this, 'pdistrict')" required data-live-search="true">
                                @foreach ($states as $stt)
                                    @php if($stt->id == $propritor->dairyPropritorState) $s = 'selected="selected"'; else $s=""; @endphp
                                    <option value="{{$stt->id}}" {{$s}}>{{$stt->name}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">District</label>
                            <select name="owdistrict" class="selectpicker" id="pdistrict"  title="Select District" required data-live-search="true">
                                @foreach ($city as $c)
                                    @php if($c->id == $propritor->dairyPropritorCity) $s = 'selected="selected"'; else $s=""; @endphp
                                    <option value="{{$c->id}}" {{$s}}>{{$c->name}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label">Pin Code</label>
                            <input maxlength="200" required="required" name="owpin" value="{{$propritor->dairyPropritorPincode}}" class="form-control" placeholder="Pin code" id="ppinCode" />
                        </div>
                    </div>

                    <div class="clearfix mb-20"></div>

                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary fr">Update Details</button>
                    </div>
                </form>
            </div>
        </div>

        
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
                    $(".res-message").html(res.msg).show().addClass("error").removeClass("success").slideDown();
                }else{
                    $(".res-message").html(res.msg).show().addClass("success").removeClass("error").slideDown();
                    showNewPassField();
                }
            },
            error:function(res){
                $.alert("Something went wrong.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    })

    function showNewPassField(){
        $("#currPass-form").slideUp();
        $("#newPass-form").slideDown();
        console.log("asfidhasoif");
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
                    $(".res-message").html(res.msg).show().addClass("error").removeClass("success").slideDown();
                }else{
                    $(".res-message").html(res.msg).show().addClass("success").removeClass("error").slideDown();
                    window.location = "{{url('dairy-settings')}}";
                }
            },
            error:function(res){
                $.alert("Something went wrong.");
            }
        }).done(function(res){
            console.log(res);
            loader("hide");
        });
    })


    
    function getCity(e, cityid){
        var stateID = $(e).val();    
        
        if(stateID){
            $.ajax({
                type:"GET",
                url:"{{url('/add-dairy-admin/city')}}?state_id="+stateID,
                success:function(res){               
                if(res){
                    $("#"+cityid).empty();
                    $.each(res,function(key,value){
                        $("#"+cityid).append('<option value="'+key+'">'+value['name']+'</option>');
                    });
                    $("#"+cityid).selectpicker("refresh");
                }else{
                    $("#"+cityid).empty();
                }
                }
            });
        }else{
            $("#cityid"+cityid).empty();
        }
    }
</script>
@endsection