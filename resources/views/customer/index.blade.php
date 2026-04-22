@extends('customer.layout') 
@section('content')

<style>
    body {
        background: #f3f3f3;
    }
</style>

<div class="clearfix">
    <div class="col-sm-12 text-center">

        <div class="col-sm-6">
            <div class="dcard clearfix">
                <div class="head">
                    <div class="title"> Balance</div>
                </div>

                <div class="body">
                    <div style="font-size: 45px;padding: 38px 0;color: #66768a;font-family: monospace;font-weight: 100;">
                        {{$curBal->openingBalance}}
                        <small>
                                @if($curBal->openingBalanceType == "credit") Cr. @endif
                                @if($curBal->openingBalanceType == "debit") Dr. @endif        
                        </small>
                    </div>                    
                </div>
            </div>
        </div>
            
    </div>

    
    <div class="col-sm-12" style="padding: 31px 20px;">
        <div class="dcard">
            <div id="chart_div" style="border: 1px solid #eee;"></div>
        </div>
    </div>

    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>

    </script>
@endsection