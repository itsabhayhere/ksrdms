@extends('layouts.app')
@section("content")

  <title>CCAvenue Payment</title>

    <form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction">
        @php
            echo "<input type=hidden name=encRequest value=$encrypted_data>";
            echo "<input type=hidden name=access_code value=$access_code>";
            echo "<input type=hidden name=order_id value=$order_id>";
            @endphp
          
    </form>
        
    
<script language='javascript'>document.redirect.submit();</script>

@endsection