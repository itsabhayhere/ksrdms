@extends('theme.default')

@section('content')


<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<style>
.form-group{
    clear:both;
}
.console-area{
    height: 250px;
    border: 1px solid #ddd;
    background: #f7f7f7;
    padding: 5px;
    color: #a94442;
}</style>
<div class="pageblur">
    <div class="fcard margin-fcard-1 pt-0 clearfix">
    
        <div class="upper-controls pt-0 clearfix">
            <div class="heading">
                <div class="fl">
                    <h3>Port Settings</h3>
                    <hr class="m-0">        
                </div>
            </div>
        </div>
       
        <div class="pt-10"></div>

        <div class="col-md-10 col-md-offset-1">
        
            <br>
            <a class="btn btn-sm btn-primary pull-left" href="{{url('dairy-settings')}}"> <i class="fa fa-arrow-circle-left"></i> Return </a>
            <a href="{{url('download/getweight.zip')}}" class="pull-right" download="">Download GetWeight Application for your Computer</a>
            <br>
            <br>
            <br>


            <form id="regForm" method="post" action="{{ url('/portSubmit') }}">


                <div class="row">
                    <div class="col-md-6">
                        <div class="clearfix">
                            <div class="fl">
                                <h3>Milk Tester</h4>
                            </div>
                            <div class="fr pt-20">
                                <input type="checkbox" name="milkUtilityActive" value="1" data-toggle="toggle" @if($m->isActive) checked @endif data-on="Enabled" data-off="Disabled">
                            </div>
                        </div>
                        <hr>

                        <div class="form-group mb-20 clearfix">
                            <div class="col-sm-4">
                                <label>Communication Port</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="milkComPort" id="milkComPort" required class="selectpicker" title="Please select COM">
                                    <option value="COM1" @if($m->communicationPort == "COM1") selected @endif>COM 1</option>
                                    <option value="COM2" @if($m->communicationPort == "COM2") selected @endif>COM 2</option>
                                    <option value="COM3" @if($m->communicationPort == "COM3") selected @endif>COM 3</option>
                                    <option value="COM4" @if($m->communicationPort == "COM4") selected @endif>COM 4</option>
                                    <option value="COM5" @if($m->communicationPort == "COM5") selected @endif>COM 5</option>
                                    <option value="COM6" @if($m->communicationPort == "COM6") selected @endif>COM 6</option>
                                    <option value="COM7" @if($m->communicationPort == "COM7") selected @endif>COM 7</option>
                                    <option value="COM8" @if($m->communicationPort == "COM8") selected @endif>COM 8</option>
                                    <option value="COM9" @if($m->communicationPort == "COM9") selected @endif>COM 9</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-20 clearfix">
                            <div class="col-sm-4">
                                <label>Max Speed</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="milkMaxSpeed" id="milkMaxSpeed" class="selectpicker">
                                    <option value="2400" @if($m->maxSpeed == "2400") selected @endif>2400</option>
                                    <option value="9600" @if($m->maxSpeed == "9600") selected @endif>9600</option>
                                </select>
                            </div>
                        </div>


                        <fieldset>
                            <legend align="center">Connection Preference</legend>

                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">
                                    <label>Data Bits</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="mDataBits" id="mDataBits" class="selectpicker">
                                        <option value="8" @if($m->connectionPerferenceDataBits == "8") selected @endif>8</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">            
                                    <label>Parity</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="mParity" id="mParity" class="selectpicker" >
                                        <option value="1" @if($m->connectionPerferenceParity == "1") selected @endif>1</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">
                                    <label>Stop Bits</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="mStopBits" id="mStopBits" class="selectpicker" >
                                        <option value="1" @if($m->connectionPerferenceStopBits == "1") selected @endif>1</option>
                                    </select>
                                </div>
                            </div>

                        </fieldset>
        
                    </div>

                    
                    <div class="col-md-6" style="border-left:1px solid #6d6d6d">
                        <div class="clearfix">
                            <div class="fl">
                                <h3>Electronic Weighter</h3>
                            </div>
                            <div class="fr pt-20">
                                <input type="checkbox" name="weightUtilityActive" value="1" data-toggle="toggle" @if($w->isActive) checked @endif  data-on="Enabled" data-off="Disabled">
                            </div>
                        </div>
                        <hr>

                        <div class="form-group mb-20 clearfix">
                            <div class="col-sm-4">
                                <label>Communication Port</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="weightComPort" id="weightComPort" required class="selectpicker" title="Please select COM">
                                    <option value="COM1" @if($w->communicationPort == "COM1") selected @endif>COM 1</option>
                                    <option value="COM2" @if($w->communicationPort == "COM2") selected @endif>COM 2</option>
                                    <option value="COM3" @if($w->communicationPort == "COM3") selected @endif>COM 3</option>
                                    <option value="COM4" @if($w->communicationPort == "COM4") selected @endif>COM 4</option>
                                    <option value="COM5" @if($w->communicationPort == "COM5") selected @endif>COM 5</option>
                                    <option value="COM6" @if($w->communicationPort == "COM6") selected @endif>COM 6</option>
                                    <option value="COM7" @if($w->communicationPort == "COM7") selected @endif>COM 7</option>
                                    <option value="COM8" @if($w->communicationPort == "COM8") selected @endif>COM 8</option>
                                    <option value="COM9" @if($w->communicationPort == "COM9") selected @endif>COM 9</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group mb-20 clearfix">
                            <div class="col-sm-4">
                                <label>Max Speed</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="weightMaxSpeed" id="weightMaxSpeed" class="selectpicker">
                                    <option value="2400" @if($w->maxSpeed == "2400") selected @endif>2400</option>
                                    <option value="9600" @if($w->maxSpeed == "9600") selected @endif>9600</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-20 clearfix">
                            <div class="col-sm-4">
                                <label>Weight Mode</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="weightMode" id="weightMode" class="selectpicker">
                                    <option value="Ltr" @if($w->weightMode == "Ltr") selected @endif>Ltr</option>
                                    <option value="Kg" @if($w->weightMode == "Kg") selected @endif>Kg</option>
                                </select>
                            </div>
                        </div>
                        
                        <fieldset>
                            <legend align="center">Connection Preference</legend>

                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">
                                    <label>Data Bits</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="wDataBits" id="wDataBits" class="selectpicker" >
                                        <option value="8" @if($w->connectionPerferenceDataBits == "8") selected @endif>8</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">            
                                    <label>Parity</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="wParity" id="wParity" class="selectpicker" >
                                        <option value="1"  @if($w->connectionPerferenceParity == "1") selected @endif>1</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">
                                    <label>Stop Bits</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="wStopBits" id="wStopBits" class="selectpicker" >
                                        <option value="1" @if($w->connectionPerferenceStopBits == "1") selected @endif>1</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!--<div class="form-group mb-20 mt-10 clearfix ">-->

                            <!--    <label for="decimal_digit">Decimal Degit 1</label>-->
                            <!--    <input type="radio"  name="decimal_digit" value="1">-->
                            <!--    <label for="decimal_digit">Decimal Degit 2</label>-->
                            <!--    <input type="radio"  name="decimal_digit" value="2">-->
                                
                            <!--</div>-->

                        </fieldset>
            
                        <fieldset>
                            <legend align="center">Value Preference</legend>
                            <div class="form-group mb-20 clearfix">
                                <div class="col-sm-4">
                                    <label>Decimal Digits</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="wDecimal_digit" id="wDecimal_digit" class="selectpicker" >
                                        <option value="1" @if($w->decimal_digit == 1) selected @endif>1 Digit</option>
                                        <option value="2" @if($w->decimal_digit == 2) selected @endif>2 Digits</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        
                    </div>
                </div>
                

                <div class="form-group text-center pt-50 clearfix">
                    <a class="btn btn-info" id="testPortBtn">Test</a>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
                
            </form>
        </div>

    </div>
</div>


<div class="wmodel clearfix" id="portTestModel" style="width: 75%;">
	<div class="close">X</div>
	<div class="wmodel-body">
        <div class="container-fluid">
            <h3>Port Testing</h3>
            <a class="btn btn-primary" id="milkPortBtn">Click to test milk tester port</a>
            &nbsp;
            &nbsp;
            &nbsp;
            &nbsp;
            <a class="btn btn-primary" id="weightPortBtn">click to test Weight machine port</a>

            <div class="pt-20"></div>
            <h5>Output:</h5>
            <div class="console-area"></div>

        </div>

	</div>
</div>




<script type="text/javascript">

    $("#testPortBtn").on("click", function(){
        $("#portTestModel").fadeIn();
        $("#portTestModel .wmodel-body").html(res.data);
    });

    $(".wmodel .close").on('click', function(){
		$(this).closest(".wmodel").fadeOut();
	})


    $("#milkPortBtn").on("click", function(){

        $(".console-area").html("");

        var data = {
            portName: $("#milkComPort").val(),
            baudRate: $("#milkMaxSpeed").val(),
            parity:   $("#mParity").val(),
            dataBits: $("#mDataBits").val(),
            stopBits: $("#mStopBits").val(),
            saparator: '\r',
            startChars: ['$'],
            endChars: ['\n', '\r'],
        };

        $.ajax({
            method: "POST",
            cache: false,
            url: "http://localhost:9000/com/getweight",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(data),
            async: true,
            processData: false,
            success: function (resp) {
                console.log(resp);
                $(".console-area").html(resp);
            },
            error: function (err) {
                console.log(err);
                $(".console-area").html("ERROR: getWeight Utility may not be started on your computer.");
                alert(err.responseText);
            }
        })
    });


    $("#weightPortBtn").on("click", function(){

        $(".console-area").html("");

        var data = {
            portName: $("#weightComPort").val(),
            baudRate: $("#weightMaxSpeed").val(),
            parity:   $("#wParity").val(),
            dataBits: $("#wDataBits").val(),
            stopBits: $("#wStopBits").val(),
            saparator: '$',
            startChars: ['$'],
            endChars: ['\n', '\r'],
        };

        $.ajax({
            method: "POST",
            cache: false,
            url: "http://localhost:9000/com/getweight",
            contentType: "application/json; charset=utf-8",
            data: JSON.stringify(data),
            async: true,
            processData: false,
            success: function (resp) {
                console.log(resp);
                $(".console-area").html(resp);
            },
            error: function (err) {
                console.log(err);
                $(".console-area").html("ERROR: getWeight Utility may not be started on your computer.");
                alert(err.responseText);
            }
        })
    });


</script>
@endsection