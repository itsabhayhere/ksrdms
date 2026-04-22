<style>
    .section-fotter li {
        min-height: 2em;
    }

    .section-fotter ul {
        list-style: none;
        padding: 0px;
    }

    .footer-big {
        position: inherit;
        background: #c7daec;
        padding: 20px 10px;
        box-shadow: none;
        color: black!important;
        font-size: 1.2em;
    }

    .footer-hr {
        border-color: #5490ce;
    }

    .footer a {
        color: #5f5a5a;
    }

    .section-fotter {
        color: black;
    }

    .section-fotter h4 {
        color: black;
    }

    .w-80 {
        width: 80%;
    }

    .light-color {
        font-weight: normal color: #777;
    }

    .social-icon {
        font-size: 32px;
        padding: 20px;
    }

    .social-icon a {
        padding: 8px;
        transition: 0.3s;
    }

    .social-icon a:hover,
    .social-icon a:focus {
        text-decoration: none;
    }

    .social-icon a.google {
        color: #ea4335;
    }

    .social-icon a.facebook {
        color: #4267b2;
    }

    .social-icon a.linkedin {
        color: #0077b5;
    }

    .social-icon a.twitter {
        color: #1da1f2;
    }

    .social-icon a.google:hover {
        color: #fb695d;
    }

    .social-icon a.facebook:hover {
        color: #5f86d4;
    }

    .social-icon a.linkedin:hover {
        color: #1690d0;
    }

    .social-icon a.twitter:hover {
        color: #44b0f3;
    }

    .container {
        margin-left: auto!important;
    }
    .ksr-logo{
        width: 136px;
    }
    .app-play{
        width: 152px;
    }
</style>

<hr class="m-0 footer-hr ">
<footer class="footer footer-big">
    <div class="container">

        <div class="row">
            {{-- <span class="text-muted">Place sticky footer content here.</span> --}}
            <div class="col-md-3">
                <div class="ksr-logo">
                    <img src="{{asset('images/pdfLogo.png')}}" class="img-responsive" alt="">
                </div>
                <h3>KSR Services</h3>
                <p>VPO Banwala</p>
                <p>Tehsil Dabwali</p>
                <p>Haryana 125103</p>
                <br>
                <p class="bold">GSTIN: 06AMWPV3442M1Z6</p>
            </div>
            <div class="col-md-3 section-fotter ">
                <h4><b>Services</b></h4>
                <ul>
                    <li><a href="javascript:void(0);">Dairy Management System</a></li>
                    <li><a href="javascript:void(0);">Web Application</a></li>
                    <li><a href="https://play.google.com/store/apps/details?id=com.dmsdairy" target="_blank">Mobile Application</a></li>
                </ul>
                <div class="app-play text-center">
                    <a href="https://play.google.com/store/apps/details?id=com.dmsdairy">
                        <img src="https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png" alt="Ksr service DMS Dairy Management System" class="img-responsive">
                    </a>
                </div>
            </div>
            <div class="col-md-3 section-fotter">
                <h4 class="section-2 pt-5"><b>Company</b></h4>
                <ul>
                    <li><a href="{{url('aboutUs')}}">About Us</a></li>
                    <li><a href="{{url('contactUs')}}">Contact Us</a></li>
                    <li><a href="{{url('termsCond')}}">Terms and Conditions</a></li>
                    <li><a href="{{url('privacyPolicy')}}">Privacy Policy</a></li>
                    <li><a href="{{url('refund')}}">Refund & Cancellation</li>
                        <li><a href="{{url('disclaimer')}}">Disclaimer</li>

                    </ul>
            </div>
            <div class="col-md-3 section-fotter">
                <h4 class="section-2 pt-5"><b>Your Order is Secured</b></h4>
                <p class="light-color">Powered by</p>
                <div class="w-80">
                    <a href="https://www.ccavenue.com/" target="_blank">
                        <img src="{{asset('images/ccavenue.png')}}" alt="ccAvenue" class="img-responsive">
                    </a>
            </div>
            <small>100% SECURE PAYMENT GATEWAY</small>
        </div>

        <div class="clearfix"></div>
        <div class="col-md-12 social-icon">
            <a href="#" class="google">
                    <i class="fa fa-google-plus"></i>
                </a>
            <a href="#" class="facebook">
                    <i class="fa fa-facebook-f"></i>
                </a>
            <a href="#" class="linkedin">
                    <i class="fa fa-linkedin"></i>
                </a>
            <a href="#" class="twitter">
                    <i class="fa fa-twitter-square"></i>
                </a>
        </div>
    </div>
    </div>

    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© {{date("Y")}} powered by
        <a href="https://techpathway.com/" target="_blank"> techpathway.com</a>
    </div>
    <!-- Copyright -->
</footer>