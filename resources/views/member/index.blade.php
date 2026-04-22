@extends('member.layout') 
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
                    <div class="title"> Milk Collection Today Vs. Yesterday </div>
                </div>

                <div class="body">
                    <div class="overlay-dcard" id="milkcollection-overlay"></div>
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th class="cowMilkTodayTitle" id="cowMilkTodayTitle"> Cow Milk Collected Today </th>
                                <td class="cowMilkTodayCount" id="cowMilkTodayCount"> </td>
                            </tr>
                            <tr>
                                <th class="cowMilkYesterdayTitle" id="cowMilkYesterdayTitle"> Cow Milk Collected Yesterday </th>
                                <td class="cowMilkYesterdayCount" id="cowMilkYesterdayCount"> </td>
                            </tr>
                            <tr>
                                <th class="buffaloMilkTodayTitle" id="buffaloMilkTodayTitle"> Buffalo Milk Collected Today </th>
                                <td class="buffaloMilkTodayCount" id="buffaloMilkTodayCount"> </td>
                            </tr>
                            <tr>
                                <th class="buffaloMilkYesterdayTitle" id="buffaloMilkYesterdayTitle"> Buffalo Milk Collected Yesterday </th>
                                <td class="buffaloMilkYesterdayCount" id="buffaloMilkYesterdayCount"> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

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

    <div class="col-sm-12 pt-30">

        <div class="col-md-6">

            <div class="dcard clearfix">
                <div class="head">
                    <div class="title"> Cow Milk <small>(Today vs Yesterday)</small> </div>
                </div>
                <div class="body">
                    <div class="overlay-dcard" id="cowmilkchart-overlay"></div>

                    <div class="cowMilkChart">
                        <div id="piechart"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-6">

            <div class="dcard clearfix">
                <div class="head">
                    <div class="title"> Buffalo Milk <small>(Today vs Yesterday)</small></div>
                </div>
                <div class="body">
                    <div class="overlay-dcard" id="buffaloMilkChart-overlay"></div>
                    <div class="buffaloMilkChart">
                        <div id="piechart_"></div>
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


        $( document ).ready(function() {
        /* Milk Collection Today Vs. Yesterday Start */
            $.ajax({
                type:"POST",
                url:'{{url("member/getMilkCollactionAjax")}}' ,
                data: { },
                success:function(res){
                    if(res[0] == 0 && res[1] == 0){
                        $("#cowmilkchart-overlay").show().html("No milk record available yesterday and today.");
                    }

                    if(res[2] == 0 && res[3] == 0){
                        $("#buffaloMilkChart-overlay").show().html("No milk record available yesterday and today.");
                    }

                    $("#cowMilkTodayCount").html(res[0]);
                    $("#cowMilkYesterdayCount").html(res[1]);
                    $("#buffaloMilkTodayCount").html(res[2]);
                    $("#buffaloMilkYesterdayCount").html(res[3]);

                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Task','Hours per Day'],
                            ['Today Cow Milk ',      res[0]],
                            ['Yesterday Cow Milk ',  res[1]],
                        ]);

                        var options = {
                            title: "Today's Cow Milk",
                            // pieHole: 0.4,
                            // is3D: true,
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                        chart.draw(data, options);
                    }

                    google.charts.setOnLoadCallback(drawChart_);

                    function drawChart_() {

                        var data = google.visualization.arrayToDataTable([
                          ['Task', 'Hours per Day'],
                          ['Today Buffalo Milk ',        res[2]],
                          ['Yesterday Buffalo Milk',    res[3]],
                        ]);

                        var options = {
                            title: "Today's Buffalo Milk",
                            // pieHole: 0.5,
                            // is3D: true,
                        };

                        var chart_ = new google.visualization.PieChart(document.getElementById('piechart_'));

                        chart_.draw(data, options);
                    }
               
               }
            });
        /* Milk Collection Today Vs. Yesterday End */


        /* Member Credit Report  End */

         $.ajax({
            type:"POST",
            url:'{{url('member/monthlyMilkCollaction')}}',
            data: { },
            success:function(res){
                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"] ;
                // console.log(months);
                var count = res.length ;

                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawVisualization);

                function drawVisualization() {
                        // Some raw data (not necessarily accurate)
                        var data = google.visualization.arrayToDataTable([
                                ['Month', 'Cow', 'Buffalo'],
                                [months[0],  res[0][0],      res[0][1]],
                                [months[1],  res[1][0],      res[1][1]],
                                [months[2],  res[2][0],      res[2][1]],
                                [months[3],  res[3][0],      res[3][1]],
                                [months[4],  res[4][0],      res[4][1]],
                                [months[5],  res[5][0],      res[5][1]],
                                [months[6],  res[6][0],      res[6][1]],
                                [months[7],  res[7][0],      res[7][1]],
                                [months[8],  res[8][0],      res[8][1]],
                                [months[9],  res[9][0],      res[9][1]],
                                [months[10], res[10][0],     res[10][1]],
                                [months[11], res[11][0],     res[11][1]],
                        ]);
                    // console.log(data);

                    var options = {
                        title : 'Monthly Milk Production',
                        vAxis: {title: 'Ltr'},
                        hAxis: {title: 'Month'},
                        seriesType: 'bars',
                        series: {5: {type: 'line'}}
                    };

                    var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
                    chart.draw(data, options);
                }           
            }
        });

    });


    </script>
@endsection