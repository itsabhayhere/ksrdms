@extends('theme.default') 
@section('content')

<style>
    .inf {
        padding: 5px;
    }
</style>
<div class="row" id="profilePrint">
    <div class="col-sm-12">
        <div class="well profile clearfix">
            <div class="col-sm-12">
                <div class="col-xs-12 col-sm-8">
                    <h2>{{$mem->memberPersonalName}}</h2>
                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Member Code: </strong> </div>
                        <div class="col-sm-9">{{$mem->memberPersonalCode}} </div>
                    </div>
                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Address: </strong></div>
                        <div class="col-sm-9">
                            @if($mem->memberPersonalAddress!=(null||"")) {{$mem->memberPersonalAddress}}, @endif @if($mem->memberPersonalDistrictVillage!=(null||""))
                            {{$mem->memberPersonalDistrictVillage}}, @endif @if($mem->memberPersonalCity!=(null||"")) {{$mem->memberPersonalCity}},
                            @endif @if($mem->memberPersonalState!=(null||"")) {{$mem->memberPersonalState}}, @endif @if($mem->memberPersonalMobilePincode!=(null||""))
                            &nbsp; &nbsp; - {{$mem->memberPersonalMobilePincode}} @endif
                        </div>
                    </div>

                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Registration Date: </strong></div>
                        <div class="col-sm-9">
                            @if($mem->memberPersonalregisterDate!=(null||"")) {{date("d M, Y", strtotime($mem->memberPersonalregisterDate))}} @endif
                        </div>
                    </div>

                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Aadhar: </strong></div>
                        <div class="col-sm-9">
                            @if($mem->memberPersonalAadarNumber != (null||"")) {{$mem->memberPersonalAadarNumber}} @else Not available @endif
                        </div>
                    </div>

                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Email: </strong></div>
                        <div class="col-sm-9">
                            @if($mem->memberPersonalEmail != (null||"")) {{$mem->memberPersonalEmail}} @else Not available @endif
                        </div>
                    </div>

                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Mobile: </strong></div>
                        <div class="col-sm-9">
                            @if($mem->memberPersonalMobileNumber != (null||"")) {{$mem->memberPersonalMobileNumber}} @else Not available @endif
                        </div>
                    </div>

                    <hr>

                    <h4>Bank Info</h4>
                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Bank Name: </strong></div>
                        <div class="col-sm-9">
                            @if($bank->memberPersonalBankName != (null||"")) {{$bank->memberPersonalBankName}} @else Not available @endif
                        </div>
                    </div>

                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>IFSC Code: </strong></div>
                        <div class="col-sm-9">
                            @if($bank->memberPersonalIfsc != (null||"")) {{$bank->memberPersonalIfsc}} @else Not available @endif
                        </div>
                    </div>

                    <div class="inf clearfix">
                        <div class="col-sm-3"><strong>Account Number: </strong></div>
                        <div class="col-sm-9">
                            @if($bank->memberPersonalAccountName != (null||"")) {{$bank->memberPersonalAccountName}} @else Not available @endif
                        </div>
                    </div>
                </div>

            </div>


            @if($dailyTrns!=(null||false))
            <div class="col-xs-12 divider text-center pt-30">
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong> {{$dailyTrns->noOfShift}} </strong></h2>
                    <p>Total Shift</p>
                </div>
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong> {{$dailyTrns->qty}} <small>ltr</small></strong></h2>
                    <p>Total Milk Supplied</p>
                </div>
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong> {{number_format($dailyTrns->fat, 1)}} </strong></h2>
                    <p>Avg Fat</p>
                </div>
                <div class="col-xs-12 col-sm-3 emphasis">
                    <h2><strong> {{number_format($dailyTrns->snf, 1)}} </strong></h2>
                    <p>Avg SNF</p>
                </div>
            </div>
            @endif


        </div>
    </div>
</div>

<div class="printbtnarea" style="position: absolute;top: 35px;right: 35px;">
    <a href="#" class="btn btn-default" id="printbtn"> <i class="fa fa-print"></i></a>
</div>

<script>
    function divPrint() {
        $("#profilePrint").addClass("printable");
        window.print();
    }

    $("#printbtn").on("click", function(){
        divPrint();
    })

</script>
@endsection