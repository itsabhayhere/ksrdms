@extends('theme.default') 
@section('content')

<div class="clearfix">
    <div class="col-md-12 p-0-5">

        <div class="col-md-4 p-0-5">

            <div class="dcard clearfix">
                <div class="head">
                    <div class="title">Dairy Summary</div>
                </div>

                <div class="body">
                        <div class="overlay-dcard" id="member-overlay"></div>

                    <table class="table table-bordered mb-0 tr-pointer">
                        <tbody>
                            <tr onclick="openMemberDetail('total')">
                                <th class="TotalMemberTitle" id="TotalMemberTitle"> Total Member </th>
                                <td class="TotalMemberCount" id="TotalMemberCount" ></td>
                            </tr>
                            <tr onclick="openMemberDetail('active')">
                                <th class="activeMemberTitle" id="activeMemberTitle"> Active Member </th>
                                <td class="activeMemberCount" id="activeMemberCount"></td>
                            </tr>
                            <tr onclick="openMemberDetail('inactive')">
                                <th class="inactiveMemberTitle" id="inactiveMemberTitle"> Inactive Member </th>
                                <td class="inactiveMemberCount" id="inactiveMemberCount"></td>
                            </tr>
                            <tr onclick="openMemberDetail('credit')">
                                <th class="craditMemberTitle" id="craditMemberTitle"> Cradit Member </th>
                                <td class="craditMemberCount" id="craditMemberCount"></td>
                            </tr>
                            <tr onclick="openMemberDetail('debit')">
                                <th class="debitMemberTitle" id="debitMemberTitle"> Debit Member </th>
                                <td class="debitMemberCount" id="debitMemberCount"></td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>

        <div class="col-md-4 p-0-5">

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
                            <tr>
                                <th>&nbsp;</th>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="col-md-4 p-0-5">
                <div class="dcard clearfix">
                    <div class="head">
                        <div class="title"> Today's Sale </div>
                    </div>
    
                    <div class="body">
    
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th class="localSaleTitle" id="localSaleTitle"> Local Sale </th>
                                    <td class="localSaleCount" id="localSaleCount"></td>
                                </tr>
                                <tr>
                                    <th class="plantSaleTitle" id="plantSaleTitle"> Plant Sale </th>
                                    <td class="plantSaleCount" id="plantSaleCount"></td>
                                </tr>
                                <tr>
                                    <th class="proSaleTitle" id="proSaleTitle"> Product Sale </th>
                                    <td class="proSaleCount" id="proSaleCount"></td>
                                </tr>
                                <tr class="tr-no-border">
                                    <th>&nbsp;</th>
                                    <td></td>
                                </tr>
                                <tr class="tr-no-border">
                                    <th>&nbsp;</th>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
    
                    </div>
                </div>
            </div>
            
    </div>

    <div class="col-md-12 pt-10 p-0-5">

        <div class="col-md-4 p-0-5">

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

        <div class="col-md-4 p-0-5">

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


        <div class="col-md-4 p-0-5">
            <div class="dcard clearfix">
                <div class="head">
                    <div class="title"> Credit/Debit Report  </div>
                </div>
                <div class="body">
                    <table class="table table-bordered mb-0 tr-pointer cr-dr-report">
                        <tbody>
                            <tr onclick="openMemberDetail('credit')">
                                <th class="creditMemberTitle" id="creditMemberTitle"> Member Amount on Credit Side </th>
                                <td class="creditMemberCount green-text" id="creditMemberCount"></td>
                            </tr>

                            <tr onclick="openCustomerDetail('credit')">
                                <th class="creditCustTitle" id="creditCustTitle"> Customer Amount on Credit Side </th>
                                <td class="creditCustCount green-text" id="creditCustCount"></td>
                            </tr>
                            <tr onclick="openSuppDetail('credit')">
                                <th class="creditSuppTitle" id="creditSuppTitle"> Supplier Amount on Credit Side </th>
                                <td class="creditSuppCount green-text" id="creditSuppCount"></td>
                            </tr>
                            <tr onclick="openMemberDetail('debit')">
                                <th class="debitMemberTitle" id="debitMemberTitle">Member Amount on Debit Side </th>
                                <td class="debitMember red-text" id="debitMember"></td>
                            </tr>
                            <tr onclick="openCustomerDetail('debit')">
                                <th class="debitCustTitle" id="debitCustTitle">Customer Amount on Debit Side </th>
                                <td class="debitCust red-text" id="debitCust"></td>
                            </tr>
                            <tr onclick="openSuppDetail('debit')">
                                <th class="debitSuppTitle" id="debitSuppTitle">Supplier Amount on Debit Side </th>
                                <td class="debitSupp red-text" id="debitSupp"></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

    
    <div class="col-md-12 " style="padding: 10px;">
        <div class="dcard">
            <div id="chart_div" style="border: 1px solid #eee;"></div>
        </div>
    </div>

    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>

        function openMemberDetail(type){
            switch(type){
                case "total":{
                    window.location = "{{url('memberDetailDash?type=total')}}";
                    break;
                }
                case "active":{
                    window.location = "{{url('memberDetailDash?type=active')}}";
                    break;
                }
                case "inactive":{
                    window.location = "{{url('memberDetailDash?type=inactive')}}";
                    break;                    
                }
                case "credit":{
                    window.location = "{{url('memberDetailDash?type=credit')}}";
                    break;
                }
                case "debit":{
                    window.location = "{{url('memberDetailDash?type=debit')}}";
                    break;                    
                }
                default:
            }
        }

        function openCustomerDetail(type){
            switch(type){
                case "credit":{
                    window.location = "{{url('customerDetailDash?type=credit')}}";
                    break;
                }
                case "debit":{
                    window.location = "{{url('customerDetailDash?type=debit')}}";
                    break;                    
                }
                default:
            }
        }

        function openSuppDetail(type){
            switch(type){
                case "credit":{
                    window.location = "{{url('suppDetailDash?type=credit')}}";
                    break;
                }
                case "debit":{
                    window.location = "{{url('suppDetailDash?type=debit')}}";
                    break;                    
                }
                default:
            }
        }


        $( document ).ready(function() {
        /* Milk Collection Today Vs. Yesterday Start */
            $.ajax({
                type:"POST",
                url:'getMilkCollactionData' ,
                data: { },
                success:function(res){
                    console.log(res);
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
                            is3D: true,
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

                        chart.draw(data, options);
                    }

                    google.charts.setOnLoadCallback(drawChart_);

                    function drawChart_() {

                        var data = google.visualization.arrayToDataTable([
                          ['Task',  'Hours per Day'],
                          ['Today Buffalo Milk ',       res[2]],
                          ['Yesterday Buffalo Milk',    res[3]],
                        ]);

                        var options = {
                            title: "Today's Buffalo Milk",
                            // pieHole: 0.5,
                            is3D: true,
                        };

                        var chart_ = new google.visualization.PieChart(document.getElementById('piechart_'));

                        chart_.draw(data, options);
                    }
               
               }
            });
        /* Milk Collection Today Vs. Yesterday End */

        /*  Today's Sale Start */
            $.ajax({
                type:"POST",
                url:'todaySale',
                data: { },
                success:function(res){
                    $("#localSaleCount").html("&#8377; "+res.localSaleDataCount);
                    $("#plantSaleCount").html("&#8377; "+res.plantSaleDataCount);
                    $("#proSaleCount").html("&#8377; "+res.proSaleDataCount);
                    $("#creditMemberCount").html("&#8377; "+res.creditMember+" <small>Cr.</small> (total "+res.creditMemberCount+")");
                    $("#debitMember").html("&#8377; "+res.debitMember+" <small>Dr.</small> (total "+res.debitMemberCount+")");
                    $("#creditCustCount").html("&#8377; "+res.creditCust+" <small>Cr.</small> (total "+res.creditCustCount+")");
                    $("#debitCust").html("&#8377; "+res.debitCust+" <small>Dr.</small> (total "+res.debitCustCount+")");
                    $("#creditCustCount").html("&#8377; "+res.creditCust+" <small>Cr.</small> (total "+res.creditCustCount+")");
                    $("#debitCust").html("&#8377; "+res.debitCust+" <small>Dr.</small> (total "+res.debitCustCount+")");
                    $("#creditSuppCount").html("&#8377; "+res.creditSupp+" <small>Cr.</small> (total "+res.creditSuppCount+")");
                    $("#debitSupp").html("&#8377; "+res.debitSupp+" <small>Dr.</small> (total "+res.debitSuppCount+")");
               }
            });

        /*  Today's Sale  End */

        /* Member Credit Report  End */

         $.ajax({
                type:"POST",
                url:'monthlyMilkCollaction' ,
                data: { },
                success:function(res){
                    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"] ;
                    console.log(res[3][0]);
                    console.log(res[3][1]);
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
                          title : 'Monthly Milk Production by Dairy',
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



        checkDairy();

    });


    function checkDairy(){
        $.ajax({
            type:"POST",
            url:"{{url('checkDairy')}}",
            success:function(res){
                setMembersData(res.members);
                console.log(res);
            },
            error: function(res){
                console.log(res);
            }
        });
    }


    // function checkDairyStatus(res){
    //     setMembersData(res.members);
    //     // setSuppliersData(res.suppliers);
    // }

    function setMembersData(members){
        if(members.countMembers == 0){
            $("#member-overlay").show().html("No members found. please <a href='{{url('memberSetupForm')}}' > add new members </a> now.");
            console.log("sdf");
        }
        $("#TotalMemberCount").html(members.countMembers);
        $("#activeMemberCount").html(members.activeMembers);
        $("#inactiveMemberCount").html(members.countMembers - members.activeMembers);
        $("#craditMemberCount").html(members.creditMembers);
        $("#debitMemberCount").html(members.countMembers - members.creditMembers);
    }
    </script>
@endsection