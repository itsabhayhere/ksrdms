@php $__ver = "?v=1.9"; 
@endphp

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="{{asset('favicon.png')}}" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ledger Report</title>

    <!--<link href="{{asset('css/bootstrap-3.2.0.min.css')}}" rel="stylesheet" id="bootstrap-css">-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <!--<link href="{{asset('css\4.7.0-font-awesome.min.css')}}" rel="stylesheet" type="text/css">-->
    <!--<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <!--<script src="{!! asset('js/bootstrap-select.js') !!}"></script>-->

    <link href="{!! asset('css/style1.css'). $__ver !!}" rel="stylesheet" type="text/css">
    <link href="{!! asset('css/style2.css'). $__ver !!}" rel="stylesheet" type="text/css">


    <style>
        @page {
            margin: 10px;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            padding: 5px 1px;
        }

        td,
        th,
        table,
        tr,
        body,
        div {
            background: white!important;
        }

        td{
            padding: 5px 10px!important;
        }

        h4{
            margin-bottom: 0px;
            color: black;
        }
    </style>

</head>

<body>
    <div class="wrapper">

        <div class="text-center">
            <h4>
                {{$headings['dairyName']}}
            </h4>
            <h4>
                Society code: {{$headings['society_code']}}
            </h4>
            <h4>
                {{$headings['report']}}
            </h4>
        </div>

        @include('report.ledgerReport')
    </div>
</body>

</html>