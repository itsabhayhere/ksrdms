@extends('spradmin.layout') 
@section("content")

<style>
</style>


<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<style>
.card{
    padding: 15px 20px;
    /* border: 1px solid #f5f5f5; */
    border-radius: 37px;
    box-shadow: 0px 2px 8px rgba(0,0,0,0.12);
    font-size: 19px;
    background: white;
    margin: 10px 0;
    transition: 0.2s;
}
.card:hover{
    box-shadow: 0px 0px 3px rgba(0,0,0,0.15);
}
.set-icn{
    color: #017dff;
    font-size: 30px;
    margin-right: 10px;
}
.space-2{
    margin-bottom:50px;
}
h1{
    font-size: 48px!important;
}

.btn.disabled, .btn[disabled], fieldset[disabled] .btn{
    opacity:1;
}

/* .card a{
    color: #555;
} */
</style>

<div class="pageblur">

    <div class="fcard margin-fcard-1 p-0 clearfix">

        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h1 class="display-4">Android Settings</h1>
                {{-- <p class="lead">This is a modified jumbotron that occupies the entire horizontal space of its parent.</p> --}}
            </div>
        </div>

        <div class="clearfix">
            <form action="{{url('sa/updateAppSetting')}}" method="POST">
                <div class="col-md-8">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <label for="server_api_key">
                                Server API Key:
                            </label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="server_api_key" id="sever_api_key" class="form-control" value="{{$settings->server_api_key}}">
                        </div>
                    </div>
                    <div class="col-md-12 pt-5">
                        <div class="fr">
                            <input type="submit" class="btn btn-primary" value="Update">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <br>

    </div>

</div>

@endsection