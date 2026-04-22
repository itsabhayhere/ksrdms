@extends('plant.layout')
@section('content')

<style>
    .table {
        margin-top: 0
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 p-0-5">

            <div class="dcard clearfix">
                <div class="head">
                    <div class="title">Sub Plant Dashboard</div>
                </div>

                <div class="body">
                    <div class="overlay-dcard" id="member-overlay"></div>

                    <div class="col-md-12">
                        <table class="table table-bordered mb-0 tr-pointer">
                            <tbody>
                                <tr onclick="openMemberDetail('total')">
                                    <th class="TotalMemberTitle" id=""> Total Registered Dairies </th>
                                    <td class="TotalMemberCount" id="TotalRegisteredDairies"></td>
                                </tr>
                                <tr onclick="openMemberDetail('active')">
                                    <th class="activeMemberTitle" id=""> Verified Dairies</th>
                                    <td class="activeMemberCount" id="totalVarifiedDairies"></td>
                                </tr>
                                <tr onclick="openMemberDetail('inactive')">
                                    <th class="inactiveMemberTitle" id=""> Total Member Registered </th>
                                    <td class="inactiveMemberCount" id="totalMemberRegisterd"></td>
                                </tr>
                                <tr onclick="openMemberDetail('credit')">
                                    <th class="craditMemberTitle" id=""> Total Active Members </th>
                                    <td class="craditMemberCount" id="totalActiveMember"></td>
                                </tr>
                                <tr onclick="openMemberDetail('debit')">
                                    <th class="debitMemberTitle" id=""> Total Inactive Members </th>
                                    <td class="debitMemberCount" id="totalInactiveMember"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <br>
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

        get_dashboardData();
    })

    function get_dashboardData() {
        $.ajax({
            type: "GET",
            url: "{{url('plant/getDashboardData')}}",
            success: function (res) {
                setDashboardData(res);
                console.log(res);
            },
            error: function (res) {
                console.log(res);
            }
        });
    }

    function setDashboardData(data) {
        $("#TotalRegisteredDairies").html(data.total_dairies);
        $("#totalVarifiedDairies").html(data.verified_dairyies);
        $("#totalMemberRegisterd").html(data.total_members);
        $("#totalActiveMember").html(data.total_active_members);
        $("#totalInactiveMember").html(data.total_inactive_members);

        if (data.cowmilk_yesterday == 0 && data.cowmilk_today == 0) {
            $("#cowmilkchart-overlay").show().html("No milk record available yesterday and today.");
        }

        if (data.buffelomilk_yesterday == 0 && data.buffelomilk_today == 0) {
            $("#buffaloMilkChart-overlay").show().html("No milk record available yesterday and today.");
        }

        $("#cowMilkTodayCount").html(data.cowmilk_yesterday);
        $("#cowMilkYesterdayCount").html(data.cowmilk_today);
        $("#buffaloMilkTodayCount").html(data.buffelomilk_yesterday);
        $("#buffaloMilkYesterdayCount").html(data.buffelomilk_today);

        google.charts.setOnLoadCallback(drawChart({"milk_yesterday": data.cowmilk_yesterday, "milk_today": data.cowmilk_today}, 'piechart', "Cow Milk Yesterday vs Today", 'Cow') );

        google.charts.setOnLoadCallback(drawChart({"milk_yesterday": data.buffelomilk_yesterday, "milk_today": data.buffelomilk_today}, 'piechart_', 'Buffalo Milk Yesterday vs Today', 'Buffalo'));

    }


    function drawChart(res, id, title, type) {
        var data = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Today '+type+' Milk ', res.milk_yesterday],
            ['Yesterday '+type+' Milk ', res.milk_today],
        ]);

        var options = {
            title: title,
            // pieHole: 0.4,
            is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById(id));

        chart.draw(data, options);
    }
</script>
@endsection