@extends('theme.default') 
@section('content')

<form action="{{url("bookingconfirms")}}" method="post">
    <input type="submit">
</form>
@endsection