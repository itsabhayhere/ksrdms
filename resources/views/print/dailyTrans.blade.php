

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>KSRDMS.COM (KSR SERVICES)</title>
    	
    <link href="{{asset("css/bootstrap-3.2.0.min.css")}}" rel="stylesheet" id="bootstrap-css">
    <script src="{{asset("js/jquery-1.11.1.min.js")}}"></script>
    <script src="{{asset("js/bootstrap-3.2.0.min.js")}}"></script>
    <script src="  https://printjs-4de6.kxcdn.com/print.min.js"></script>
    <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">
    <link rel="stylesheet" href="{{asset('css/print.css')}}?ver1.9">

</head>
<body class="body-small-print">
    
    <section>
        <div class="clearfix">
            <h3 class="text-center">{{$dairy->society_code.": ". $dairy->dairyName}}</h3>
            <hr class="heading-hr">
            <div class="bill-head-desc">
                <div class="clearfix">
                    <div class="fl">Daily transaction reciept </div>
                    <div class="fr bold">{{date("d-m-Y", strtotime($trans->date))}}</div>
                </div>
                <br>
                <div class="clearfix">
                    <div class="half" >Member Code: </div><div class="half bold">{{$trans->memberCode}}</div>
                </div>
                <div class="clearfix">
                    <div class="half" >Member Name: </div><div class="half bold">{{$trans->memberName}}</div>
                </div>
                <div class="clearfix">
                    <div class="half" >Shift: </div><div class="half bold">{{$trans->shift}}</div>
                </div>
            </div>
        </div>

        <hr class="heading-hr">
        <div class="bill-desc clearfix">
            <div class="half" >Quantity: </div><div class="half bold tr">{{number_format($trans->milkQuality, 1)}}</div>
            <div class="half" >Fat: </div><div class="half bold tr">{{ number_format($trans->fat, 1)}}</div>
            <div class="half" >SNF: </div><div class="half bold tr">{{$trans->snf}}</div>
            <div class="half" >Rate: </div><div class="half bold tr"> &#8377; {{number_format($trans->rate, 2)}}</div>
            <div class="half" >Total Amount: </div><div class="half bold tr"> &#8377; {{number_format($trans->amount, 2)}}</div>
        </div>
        <hr class="heading-hr">
        <div class="bill-desc clearfix">
            @php if($ub->openingBalanceType == "credit") $dc = "Cr."; else $dc = "Dr."; @endphp
            <div class="half" >Current Balance: </div><div class="half bold tr"> &#8377; {{number_format($ub->openingBalance, 2) ." ".$dc}}</div>
        </div>
        
    </section>

    <section class="in-link light">

    </section>

    <script>
        $( document ).ready(function() {
            document.print();
        });
    </script>

</body>
</html>