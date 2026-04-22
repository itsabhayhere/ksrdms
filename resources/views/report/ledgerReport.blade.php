<table class="table ledgerReport-table tright table-bordered table-striped">
    <thead>
        <tr>
            <th>Party Code</th>
            <th>Party Name</th>
            <th>Current Balance</th>
            <th>Balance Type</th>
        </tr>
    </thead>
    <tbody>

        @php 
        foreach ($queryData as $data) {
            $dairyInfo = [];
                if($data->userType == "4"){

                    $member = DB::table('member_personal_info')
                                ->where('ledgerId', $data->id)
                                ->get()->first();
                    
                    $mamberBank = DB::table('user_current_balance')
                                        ->where('ledgerId', $data->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get()->first();

                    if(($member) == (null||false||"") || ($mamberBank) == (null||false||"")){
                        continue;
                    }

                    $info = ["ledgerId"=>$data->id,
                                    "Party_Name"=>$member->memberPersonalName,
                                    "Code"=>$member->memberPersonalCode,
                                    "Current_Balance"=>$mamberBank->openingBalance,
                                    "Current_Balance_Type"=>$mamberBank->openingBalanceType
                                ];

                }elseif ($data->userType == "2") {

                    $cust = DB::table('customer')
                                    ->where('ledgerId', $data->id)
                                    ->get()->first();
               
                    if(($cust) == (null||false||"")){
                        continue;
                    }

                    $ub = DB::table('user_current_balance')
                                        ->where('ledgerId', $data->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get()->first();

                    $info = ["ledgerId"=>$cust->ledgerId,
                                        "Party_Name"=>$cust->customerName,
                                        "Code"=>$cust->customerCode,
                                        "Current_Balance"=>$ub->openingBalance,
                                        "Current_Balance_Type"=>$ub->openingBalanceType
                                    ];

                }elseif ($data->userType == "6") {
                    $milkPlant = DB::table('milk_plants')
                                ->where('ledgerId', $data->id)
                                ->get()->first();

                    
                    $pb = DB::table('user_current_balance')
                                ->where('ledgerId', $data->id)
                                ->orderBy('created_at', 'desc')
                                ->get()->first();

                    $info = ["ledgerId"=>$milkPlant->ledgerId,
                                        "Party_Name"=>$milkPlant->plantName,
                                        "Code"=>$milkPlant->plantCode,
                                        "Current_Balance"=>$pb->openingBalance,
                                        "Current_Balance_Type"=>$pb->openingBalanceType
                                    ];

                }elseif ($data->userType == "3") {
                    $supp = DB::table('suppliers')
                            ->where('ledgerId', $data->id)
                            ->get()->first();

                    if(($supp) == (null||false||"")){
                        continue;
                    }
                    $sb = DB::table('user_current_balance')
                            ->where('ledgerId', $data->id)
                            ->orderBy('created_at', 'desc')
                            ->get()->first();

                    $info = [
                                    "ledgerId"=>$supp->ledgerId,
                                    "Party_Name"=>$supp->supplierFirmName,
                                    "Code"=>$supp->supplierCode,
                                    "Current_Balance"=>$sb->openingBalance,
                                    "Current_Balance_Type"=>$sb->openingBalanceType
                                ];
                }else{
                    continue;
                }

           
           
        @endphp
            <tr>
                <td>
                    {{$info['Code']}}
                </td>
                <td>
                    {{$info['Party_Name']}}
                </td>
                <td style="font-family:'DejaVu Sans', sans-serif;"  >
                    &#8377; {{number_format($info['Current_Balance'], 2)}}
                </td>
                <td>
                    {{ucfirst($info['Current_Balance_Type'])}}
                </td>
            </tr>

        @php 
            unset($dairyInfo);
        }
       @endphp
    </tbody>
</table>