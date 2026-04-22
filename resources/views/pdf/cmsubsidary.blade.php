
@php $__ver = "?v=1.9"; @endphp

<!DOCTYPE html>
<html lang="en">
   <head>


      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <link rel="shortcut icon" type="image/png" href="{{asset('favicon.png')}}" />
      <title>CmSubsidary</title>
      
      <!--<link href="{{asset("css/bootstrap-3.2.0.min.css")}}" rel="stylesheet" id="bootstrap-css">-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

      <!--<link href="{{asset('css\4.7.0-font-awesome.min.css')}}" rel="stylesheet" type="text/css">-->
      <!--<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">-->
      
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
      

      <link href="{!! asset('css/style1.css'). $__ver !!}" rel="stylesheet" type="text/css">
      <link href="{!! asset('css/style2.css'). $__ver !!}" rel="stylesheet" type="text/css"> 

      <!--<link rel="stylesheet" href="{{ asset("css/jquery-ui-1.12.1.css")}}">-->
      <!--<script src="{{ asset('js/jquery-ui-1.12.1.js')}}"></script>-->

      <style>
         @page { margin:10px; }
      .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
       padding: 5px 1px;
      }
      td, th, table, tr, body, div{
         background:white!important;
      }
      </style>


   </head>
   <body>
      <div class="wrapper">
            {!! $content !!}
      </div>
   </body>
</html>