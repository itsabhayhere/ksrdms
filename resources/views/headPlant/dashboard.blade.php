@extends('headPlant.layout') 
@section('content')

<style>
        .table{
            margin-top:0
        }
    </style>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 p-0-5">
    
                <div class="dcard clearfix">
                    <div class="head">
                        <div class="title">&nbsp;</div>
                    </div>
    
                    <div class="body">
                        <div class="overlay-dcard" id="member-overlay"></div>

                        <table class="table table-bordered mb-0 ">
                            <tbody>
                                <tr>
                                    <td>Total Verified Dairies</td>
                                    <td>{{$total['total_verified']}}</td>
                                </tr>
                                <tr>
                                    <td>Total Members</td>
                                    <td>{{$total['total_members']}}</td>
                                </tr>
                                <tr>
                                    <td>Total Active Members</td>
                                    <td>{{$total['total_active_members']}}</td>
                                </tr>
                                <tr>
                                    <td>Total Inactive Members</td>
                                    <td>{{$total['total_members'] - $total['total_active_members']}}</td>
                                </tr>
                            </tbody>
                        </table>

                        <br>

                        @foreach($plants as $pl)
                            <div class="col-md-4 border-1">
                                    <h4>{{$pl->plantName}}</h4>
                                <table class="table table-bordered mb-0 tr-pointer">
                                    <tbody>
                                        <tr onclick="openMemberDetail('total')">
                                            <th class="TotalMemberTitle" id=""> Total Registered Dairies </th>
                                        <td class="TotalMemberCount" id="TotalRegisteredDairies" >{{$pl->registered_dairies}}</td>
                                        </tr>
                                        <tr onclick="openMemberDetail('active')">
                                            <th class="activeMemberTitle" id=""> Verified Dairies</th>
                                            <td class="activeMemberCount" id="totalVarifiedDairies">{{$pl->activated_dairies}}</td>
                                        </tr>
                                        <tr onclick="openMemberDetail('inactive')">
                                            <th class="inactiveMemberTitle" id=""> Cow Milk Collected Today </th>
                                            <td class="cow_milk_collected_today" id="cow_milk_collected_today">{{$pl->cowMilkTodayQty}}</td>
                                        </tr>
                                        <tr onclick="openMemberDetail('credit')">
                                            <th class="craditMemberTitle" id=""> Buffalo Milk Collected Today</th>
                                            <td class="buffalo_milk_collected_today" id="buffalo_milk_collected_today">{{$pl->buffaloMilkTodayQty}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <br>
            </div>
    
            <div class="col-md-6 p-0-5">
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
    
            <div class="col-md-6 p-0-5">
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
    </div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        $(document).ready(function () {
    
            google.charts.load('current', {
                'packages': ['corechart']
            });

            google.charts.setOnLoadCallback(drawChart);
            
            google.charts.setOnLoadCallback(drawChart_buf);

        })

        
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Today Cow Milk ', {{$total['total_cow_yesterday']}}],
            ['Yesterday Cow Milk ',  {{$total['total_cow_today']}}],
        ]);

        var options = {
            title: "Cow Milk Yesterday vs Today",
            // pieHole: 0.4,
            is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }


    function drawChart_buf() {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Today Buffalo Milk ', {{$total['total_buf_yesterday']}}],
            ['Yesterday Buffalo Milk ', {{$total['total_buf_today']}}],
        ]);

        var options = {
            title: 'Buffalo Milk Yesterday vs Today',
            // pieHole: 0.4,
            is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_'));

        chart.draw(data, options);
    }

            function get_dashboardData(){
                // $.ajax({
                //     type:"GET",
                //     url:"{{url('plant/getDashboardData')}}",
                //     success:function(res){
                //         setDashboardData(res);
                //         console.log(res);
                //     },
                //     error: function(res){
                //         console.log(res);
                //     }
                // });
            }
            
        function setDashboardData(data){
            // $("#TotalRegisteredDairies").html(data.total_dairies);
            // $("#totalVarifiedDairies").html(data.verified_dairyies);
            // $("#totalMemberRegisterd").html(data.total_members);
            // $("#totalActiveMember").html(data.total_active_members);
            // $("#totalInactiveMember").html(data.total_inactive_members);
        }
    
        </script>
@endsection