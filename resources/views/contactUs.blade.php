@extends($layout)

@section("content")
<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="container pageblur">

    <div class="fcard margin-fcard-1 pt-0 clearfix">

        <h2 class="text-center">
            Contact us
            <hr/>
            </h3>

            <br />

            <div class="row">
                <div class="col-md-8">
                    <h4>Enter Your details, we will contact you</h4>
                    <form action="{{url('contactMail')}}" method="post">
                        <input type="hidden" name="dairyName" value="{{$dairyName}}">
                        <input type="hidden" name="queryFrom" value="{{$queryFrom}}">

                        <input class="form-control" name="name" placeholder="Name..." value="{{$name}}"/><br />
                        <input class="form-control" name="phone" placeholder="Phone..." value="{{$phone}}"/><br />
                        <input class="form-control" name="email" placeholder="E-mail..." value="{{$email}}"/><br />
                        <textarea class="form-control" name="message" placeholder="How can we help you?" style="height:150px;"></textarea>
                        <br/>
                        <div class="g-recaptcha" data-sitekey="6LfwRIAaAAAAALzh3Z4sx3H7dHoEIt0OZw00y9mU"></div><br />
                        <input class="btn btn-primary" type="submit" value="Send" /><br /><br />
                    </form>
                </div>
                <div class="col-md-4 fs-18 lh-3 letter-spacing-2">
                    <i class="fa fa-phone" style="font-size: 6rem;"></i>
                    <br>
                    <a href="tel:+919499194291" class="bold">
                        +91&nbsp;94991&nbsp;94291
                    </a>
                    <br />
                    {{-- <div class="" style="font-size:30px">Or</div> --}}
                    <br>
                    <i class="fa fa-envelope" style="font-size: 6rem;"></i>
                    <br>
                    <a href="mailto:support@ksrdms.com" class="bold">support@ksrdms.com</a>
                    <br/>
                </div>
            </div>
    </div>

</div>
@endsection