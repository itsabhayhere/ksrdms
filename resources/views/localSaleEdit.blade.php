<div id="customersSaleEditor" class="tab-pane fade in @if($tab=="customer") active @endif">				
    <div class="">
        <form method="post" action="{{ url('/localSaleFormSubmit') }}?returnurl=localSaleForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
            <input type="hidden" name="status" value="true">
            <input type="hidden" name="partyType" value="customer" id="partyType">
            <input type="hidden" name="sale_type" value="local_sale">
            <input type="hidden" name="returnurl" value="localSaleForm">
            <input type="hidden" name="activetab" value="customer">

            <div class="">
                <div class="col-sm-6 hide">
                    <label>Ledger name</label>
                    <span id="ledgerErr" class="ledgerErr errmsg">  </span>
                    <input list="ledgerList" name="ledgerName" id="ledger" class="ledger form-control">
                </div>

                <div class="col-sm-2"> 
                    <label>Date</label>
                    <input type="text" class="form-control" id="cdate" placeholder="Enter Name" value="<?php echo date("d-m-Y", time()); ?>" name="date" tabindex=11  autocomplete="off">
                </div>

                <div class="col-sm-3">
                    <label>Customer Code</label>
                    <span id="customerCodeErr" class="customerCodeErr errmsg">  </span>
                    <input id="customerCode" class="form-control" required autofocus name="customerCode" required tabindex=12 data-name="code">
                    <img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/> 
                </div>
                <div class="col-sm-3">
                    <label>Customer Name</label>
                    <span id="customerNameErr" class="customerNameErr errmsg">  </span>
                    <input id="customerName" name="partyName" class="form-control" required required tabindex=13 data-name="name">
                </div>

                <div class="col-sm-4">
                    <div class="col-sm-12">
                        <div class="fl">Current Balance:</div>
                        <div class="custAccInfo bold ps-5 fl"></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'customer');getSaleAmount('customer')" value="cowMilk" tabindex="14">Cow Milk</label>
                    </div>
                    <div class="col-sm-6">
                        <label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'customer');getSaleAmount('customer')" value="buffaloMilk" tabindex="14">Buffalo Milk</label>
                    </div>
                </div>
                
            </div>
            {{-- <div class="col-sm-12">
            </div> --}}
            <div class="">
                <div class="col-sm-2"> 
                    <label>Quantity</label>
                    <input type="number" class="form-control" onkeyup="getSaleAmount('customer')" id="cQuantity" placeholder="Enter Quantity" name="quantity" tabindex="15" step="0.1">
                </div>
            
                <div class="col-sm-1"> 
                    <label>&nbsp;</label>
                    <input type="text" id="unit_" name="unit" value="Ltr" class="noinput" style="width: 100%;line-height: 32px; color: #d00606; padding-left:0; font-weight:bold" readonly>
                </div>

                <div class="col-sm-2">
                    <label>&nbsp; </label>
                    <input type="hidden" class="form-control" id="cPricePerUnit" name="PricePerUnit">
                    <input type="text" readonly="readonly" class="noinput"  id="cRate" name="rate" style="width: 100%;line-height: 32px;color: #d00606; padding-left:0;font-weight:bold">
                </div>

                <div class="col-sm-2"> 
                    <label> Amount </label>
                    <input type="number" readonly="readonly" class="form-control rupee"  id="cAmount" name="amount" min="0">
                </div>
                    
                <div class="col-sm-2">
                    <label>Discount (&#8377;)</label>
                    <input type="number" class="form-control"  id="cDiscount" name="discount" value="0" tabindex="16" onchange="getFinalAmount('customer')" min="0">
                </div>

                <div class="col-sm-2">
                    <label>Final Amount</label>
                    <input type="number" class="form-control" id="cfinalAmount" value="0" name="finalAmount" readonly min="0">
                </div>

                <div class="col-sm-2">
                    <label> Paid Amount </label>
                    <input type="number" class="form-control rupee" value="0" id="cpaidAmount" name="paidAmount" tabindex="17" required min="0">
                </div>

                <div class="col-sm-2 text-center">	
                    <div class="pt-25"></div>
                    <button type="submit" class="btn btn-primary customerSubmit" tabindex="20">Submit</button>
                </div>			
            </div>
        </form>
    </div>

</div>


    <div id="memberSale" class="tab-pane fade in @if($tab=="member") active @endif">

        <div class="">
            <form method="post" action="{{ url('/localSaleFormSubmit') }}?returnurl=localSaleForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="dairyId" name="dairyId" value="{{ Session::get('loginUserInfo')->dairyId }}">
                <input type="hidden" name="status" value="true">
                <input type="hidden" name="partyType" value="member" id="partyType">
                <input type="hidden" name="sale_type" value="local_sale">
                <input type="hidden" name="activetab" value="member">
                <input type="hidden" name="returnurl" value="localSaleForm">

                <div class="">
                    <div class="col-sm-6 hide">
                            <label>Ledger name</label>
                            <span id="ledgerErr" class="ledgerErr errmsg">  </span>
                            <input list="ledgerList" name="ledgerName" id="ledger" class="ledger form-control" onfocusout="getUserNameByLedger(this.value);">
                    </div>
    
                    <div class="col-sm-2"> 
                        <label>Date</label>
                        <input type="text" class="form-control" id="mdate" placeholder="Enter Name" value="<?php echo date("d-m-Y", time()); ?>" name="date" autocomplete="off" tabindex="21">
                    </div>
    
                    <div class="col-sm-3">
                        <label>Member Code</label>
                        <span id="memberCodeErr" class="memberCodeErr errmsg">  </span>
                        <input id="memberCode" class="form-control" required autofocus name="memberCode" required tabindex="22" data-name="code">
                        <img style="float:right;" id='loading-input-img' width="80px" src="http://rpg.drivethrustuff.com/shared_images/ajax-loader.gif"/>
                    </div>
                    <div class="col-sm-3">
                        <label>Member Name</label>
                        <span id="memberNameErr" class="memberNameErr errmsg">  </span>
                        <input id="memberName" name="partyName" class="form-control" required tabindex="23" data-name="name">
                    </div>
    
                    <div class="col-sm-4">
                        <div class="col-sm-12">
                            <div class="fl">Current Balance:</div>
                            <div class="membAccInfo bold ps-5 fl"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'member');getSaleAmount('member')" value="cowMilk" tabindex="24">Cow Milk</label>
                        </div>
                        <div class="col-sm-6">
                            <label class="rdolb lh-25"><input type="radio" name="product" onclick="checkProduct(this, 'member');getSaleAmount('member')" value="buffaloMilk" tabindex="24">Buffalo Milk</label>
                        </div>
                    </div>
                    
                </div>
                {{-- <div class="col-sm-12">
                </div> --}}
                <div class="">
                    <div class="col-sm-2"> 
                        <label>Quantity</label>
                        <input type="number" class="form-control" onkeyup="getSaleAmount('member')" id="mQuantity" placeholder="Enter Quantity" name="quantity" tabindex="25" step="0.1">
                    </div>
                
                    <div class="col-sm-1"> 
                        <label>&nbsp;</label>
                        <input type="text" id="unit_" name="unit" value="Ltr" class="noinput" style="width: 100%;line-height: 32px; color: #d00606; padding-left:0; font-weight:bold" readonly>
                    </div>
    
                    <div class="col-sm-2"> 
                        <label>&nbsp; </label>
                        <input type="hidden" class="form-control" id="mPricePerUnit" name="PricePerUnit">
                        <input type="text" readonly="readonly" class="noinput"  id="mRate" name="rate" style="width: 100%;line-height: 32px;color: #d00606; padding-left:0;font-weight:bold">
                    </div>

                    <div class="col-sm-2"> 
                        <label> Amount </label>
                        <input type="number" readonly="readonly" class="form-control rupee" id="mAmount" name="amount" min="0">
                    </div>

                    <div class="col-sm-2">
                        <label>Discount (&#8377;)</label>
                        <input type="number" class="form-control"  id="mDiscount" name="discount" value="0" tabindex="26" onchange="getFinalAmount('member')" min="0">
                    </div>

                    <div class="col-sm-2">
                        <label>Final Amount</label>
                        <input type="number" class="form-control" id="mfinalAmount" value="0" name="finalAmount" readonly min="0">
                    </div>

                    <div class="col-sm-2">
                        <label> Paid Amount </label>
                        <input type="number" class="form-control rupee" value="0" id="mpaidAmount" name="paidAmount" tabindex="27" required min="0">
                    </div>
    
                    <div class="col-sm-2 text-center">	
                        <div class="pt-25"></div>
                        <button type="submit" class="btn btn-primary customerSubmit" tabindex=30>Submit</button>
                    </div>			
                </div>
            </form>
        </div>
    </div>