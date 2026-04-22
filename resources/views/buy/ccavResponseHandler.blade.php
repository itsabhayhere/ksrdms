@extends('layouts.app') 
@section("content")

  {{-- <title>CCAvenue Payment</title>
<center>
    @if($order_status==="Success")
        <br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.
        
    @elseif($order_status==="Aborted")
        <br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail
    
    @elseif($order_status==="Failure")
        <br>Thank you for shopping with us.However,the transaction has been declined.
    @else
        <br>Security Error. Illegal access detected
    @endif

    <br><br>

    <table cellspacing=4 cellpadding=4>
        @for($i = 0; $i < $dataSize; $i++) 
            @php $information=explode('=',$decryptValues[$i]); @endphp
            <tr>
                <td>{{$information[0]}}</td>
                <td>{{$information[1]}}</td>
            </tr>
        @endfor
    </table>
    <br>
    <br>

</center> --}}




<div class="jumbotron text-center">
    <h1 class="display-3">Thank You!</h1>
    <p class="lead">
        Your payment has been completed successfully.<br/>
        We have send your invoice on your email. <strong>Please check your email</strong>.
    </p>
    <hr>
    <p>
      Having trouble? <a href="{{url('contactUs')}}">Contact us</a>
    </p>
    <p class="lead">
      <a class="btn btn-primary btn-lg" href="{{url('/')}}" role="button">Continue to homepage</a>
    </p>
  </div>

@endsection