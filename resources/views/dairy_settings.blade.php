@extends('theme.default') 
@section("content")

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<style>
    .card {
        padding: 15px 20px;
        /* border: 1px solid #f5f5f5; */
        border-radius: 37px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.12);
        font-size: 19px;
        background: white;
        margin: 10px 0;
        transition: 0.2s;
    }

    .card:hover {
        box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.15);
    }

    .set-icn {
        color: #017dff;
        font-size: 30px;
        margin-right: 10px;
    }

    .space-2 {
        margin-bottom: 50px;
    }

    h1 {
        font-size: 48px!important;
    }

    .btn.disabled,
    .btn[disabled],
    fieldset[disabled] .btn {
        opacity: 1;
    }

    .subscriptionName {
        font-weight: bold;
        color: #e46100;
        font-size: 14px;
        text-transform: uppercase;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.15);
    }

    .planType {}

    .smsBalance {
        font-weight: bold;
        font-size: 14px;
        color: #095600;
        letter-spacing: 2px;
        text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.25);
    }

    .red-sms {
        color: #de0000;
    }

    .info-card {
        border: 1px solid #e6e6e6;
        padding: 10px 15px;
        border-radius: 25px;
        box-shadow: 0px 3px 15px rgba(0, 0, 0, 0.1);
    }

    /* .card a{
    color: #555;
} */
</style>

<div class="pageblur">

    <div class="fcard margin-fcard-1 p-0 clearfix">

        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h1 class="display-4">Settings</h1>
                {{--
                <p class="lead">This is a modified jumbotron that occupies the entire horizontal space of its parent.</p> --}}
            </div>
        </div>

        <div class="space-2 clearfix">

            <div class="col-md-4">
                <a href="{{url('change_password')}}">
                    <div class="card">
                        <div class="card-body">
                            <i class="set-icn fa fa-key" aria-hidden="true"></i> Change Password
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{url('dairyDetails')}}">
                    <div class="card">
                        <div class="card-body">
                            <i class="set-icn fa fa-file-text" aria-hidden="true"></i> Update Dairy Details
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{url('utilitySetupForm')}}">
                    <div class="card">
                        <div class="card-body">
                            <i class="set-icn fa fa-random" aria-hidden="true"></i> Port Setup
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{url('subscriptionHistory')}}">
                    <div class="card">
                        <div class="card-body">
                            <i class="set-icn fa fa-paper-plane-o" aria-hidden="true"></i> Subscription Plan
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="info-card">
                    <h4>Dairy Details</h4>
                    <table class="table">
                        <tr>
                            <td>Society Name: </td>
                            <td>{{$dairy->dairyName}}</td>
                        </tr>
                        <tr>
                            <td>Society Code: </td>
                            <td>{{$dairy->society_code}}</td>
                        </tr>
                        <tr>
                            <td>Dairy Contact: </td>
                            <td>{{$dairy->mobile}}</td>
                        </tr>
                        <tr>
                            <td>Dairy Address: </td>
                            <td>{{$dairy->dairyAddress.", city: ". $dairy->cityName.", State: ".$dairy->stateName}}</td>
                        </tr>
                    </table>

                    <h4>Propritor Details</h4>
                    <table class="table">
                        <tr>
                            <td>Propritor Name: </td>
                            <td>{{$prp->dairyPropritorName}}</td>
                        </tr>
                        <tr>
                            <td>Propritor Mobile: </td>
                            <td>{{$prp->PropritorMobile}}</td>
                        </tr>
                        <tr>
                            <td>Propritor Email: </td>
                            <td>{{$prp->dairyPropritorEmail}}</td>
                        </tr>
                        <tr>
                            <td>Propritor Address: </td>
                            <td>{{$prp->dairyPropritorAddress.", city: ". $prp->cityName.", State: ".$prp->stateName}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-card">
                    <div class="fl">
                        <H4>Subscription</H4>
                    </div>

                    <table class="table">
                        <tr>
                            <td>Subscription: </td>
                            <td class="subscriptionName">{{$subsc->name}}</td>
                        </tr>
                        <tr>
                            <td>Type: </td>
                            <td class="planType">{{ucfirst($subsc->planType)}}</td>
                        </tr>
                        <tr>
                            <td>Last Date: </td>
                            <td class="expiryDate">{{ date("d M Y", strtotime($subsc->expiryDate))}}</td>
                        </tr>
                        <tr>
                            <td>SMS Balance: </td>
                            <td class="smsBalance @if($dairy->remainingSms < 50) red-sms @endif">{{ucfirst($dairy->remainingSms)}}</td>
                        </tr>
                    </table>
                </div>

                <div class="clearfix mb-20"></div>

                <div class="info-card">
                    <div>
                        <a href="{{url("/download/getweight.zip ")}}" download>Download GetWeight Application for your Computer</a>
                        <br>
                    </div>
                    <div class="fl">
                        <H4>Milk Tester Utility</H4>
                    </div>
                    <div class="fr">
                        <input type="checkbox" data-toggle="toggle" @if($mu->isActive) checked @endif data-on="Enabled" data-off="Disabled"
                        disabled>
                    </div>

                    <table class="table">
                        <tr>
                            <td>Communication Port: </td>
                            <td>{{$mu->communicationPort}}</td>
                        </tr>
                        <tr>
                            <td>Max Speed: </td>
                            <td>{{$mu->maxSpeed}}</td>
                        </tr>
                    </table>

                    <div class="fl">
                        <h4>Weight Utility</h4>
                    </div>
                    <div class="fr">
                        <input type="checkbox" data-toggle="toggle" @if($wu->isActive) checked @endif data-on="Enabled" data-off="Disabled"
                        disabled>
                    </div>
                    <table class="table">
                        <tr>
                            <td>Communication Port: </td>
                            <td>{{$wu->communicationPort}}</td>
                        </tr>
                        <tr>
                            <td>Max Speed: </td>
                            <td>{{$wu->maxSpeed}}</td>
                        </tr>
                    </table>
                </div>
                <div class="clearfix mb-20"></div>
            </div>
        </div>


    </div>


</div>


@endsection