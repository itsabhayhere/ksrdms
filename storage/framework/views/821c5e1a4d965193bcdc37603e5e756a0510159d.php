 
<?php $__env->startSection('content'); ?> 

<?php
	// if (session()->has('AdvanceActiveTab')) {
	// 	$tab = session()->get('AdvanceActiveTab');
	// }else{
	// 	$tab = "customer";
    // }
	$tab = "member";
    
?>


<div class="pageblur">

    <div class="span-fixed response-alert" id="response-alert"></div>
    
    <div class="fcard margin-fcard-1 pt-0 clearfix">
        <div class="upper-controls pt-0 clearfix">
            <div class="fl">
                <div class="heading">
                    <h3>Advance</h3>
                    <hr class="m-0">
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs sale-tabs">
            <li class="<?php if($tab=="customer"): ?> active <?php endif; ?>"><a data-toggle="tab" href="#customersAdvance" onclick="document.getElementById('customerCode').focus();">Customers</a></li>
            <li class="<?php if($tab=="member"): ?> active <?php endif; ?>"><a data-toggle="tab" href="#memberAdvance" onclick="document.getElementById('memberCode').focus()">Members</a></li>
            <li class="<?php if($tab=='supplier'): ?> active <?php endif; ?>"><a data-toggle="tab" href="#supplierAdvance" onclick="document.getElementById('supplierCode').focus()">Suppliers</a></li>
        </ul>
        
        <div class="tab-content pt-20">

            <div id="customersAdvance" class="tab-pane fade in <?php if($tab=="customer"): ?> active <?php endif; ?>">				
                <div class="col-sm-12">
                    <form method="post" action="<?php echo e(url('/advanceSubmit')); ?>" class="clearfix">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        <input type="hidden" id="dairyId" name="dairyId" value="<?php echo e(Session::get('loginUserInfo')->dairyId); ?>">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" name="partyType" value="customer">
        
                        <div class="col-md-12 clearfix">
                            <div class="col-sm-3">
                                <label>Date: </label>
                                <input type="text" class="form-control" id="date" value="<?php echo date("d-m-Y") ; ?>" name="date" autocomplete="off">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Customer Code</label>
                                <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessage"></span>
                                <input id="customerCode" class="form-control" name="partyCode" required data-name="code">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Customer Name</label>
                                <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>
                                <input class="form-control" id="customerName" name="partyName" required="true" data-name="name">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Current Balance</label>
                                <input class="form-control noinput" id="customerBalance" readonly tabindex="-1">
                            </div>

                            <div class="col-sm-3">
                                <label>Amount</label>
                                <input type="text" required="true" class="form-control" placeholder="Advance" id="camount" name="amount">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remarks" id="sremark" name="remark">
                            </div>

                            <div class="col-sm-3 pr-30">
                                <div class="pt-20"></div>
                                <button type="submit" name="submit" class="btn btn-primary">Add to Advance</button>
                            </div>
                        </div>
        
                        
                    </form>
                </div>

            </div>


            <div id="memberAdvance" class="tab-pane fade in <?php if($tab=="member"): ?> active <?php endif; ?>">

                <div class="col-sm-12">
                    <form method="post" action="<?php echo e(url('/advanceSubmit')); ?>" class="clearfix">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        <input type="hidden" id="mdairyId" name="dairyId" value="<?php echo e(Session::get('loginUserInfo')->dairyId); ?>">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" name="partyType" value="member">

                        <div class="col-md-12 clearfix">
                            <div class="col-sm-3">
                                <label>Date: </label>
                                <input type="text" class="form-control" id="mdate" value="<?php echo date("d-m-Y") ; ?>" name="date" autocomplete="off">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Member Code</label>
                                <span class="memberCodeErrorMessage errorMessage" id="memberCodeErrorMessage"></span>
                                <input id="memberCode" class="form-control" name="partyCode" required data-name="code">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Member Name</label>
                                <span class="memberNameErrorMessage errorMessage" id="memberNameErrorMessage"></span>
                                <input class="form-control" id="memberName" name="partyName" required="true" data-name="name">
                            </div>
                            
                            <div class="col-sm-3">
                                <label>Current Balance</label>
                                <input class="form-control noinput" id="memberBalance" readonly tabindex="-1">
                            </div>

                            <div class="col-sm-3">
                                <label>Amount</label>
                                <input type="text" required="true" class="form-control" placeholder="Advance" id="mamount" name="amount">
                            </div>
        
                            <div class="col-sm-3">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remarks" id="sremark" name="remark">
                            </div>
                            
                            <div class="col-sm-3 pr-30">
                                <div class="pt-20"></div>
                                <button type="submit" name="submit" class="btn btn-primary">Add to Advance</button>
                            </div>
                        </div>
        
                    </form>
                </div>

            </div>


            <div id="supplierAdvance" class="tab-pane fade in <?php if($tab=='supplier'): ?> active <?php endif; ?>">

                <div class="col-sm-12">
                    <form method="post" action="<?php echo e(url('/advanceSubmit')); ?>" class="clearfix">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        <input type="hidden" id="sdairyId" name="dairyId" value="<?php echo e(Session::get('loginUserInfo')->dairyId); ?>">
                        <input type="hidden" name="status" value="true">
                        <input type="hidden" name="partyType" value="supplier">

                        <div class="col-md-12 clearfix">
                            <div class="col-sm-3">
                                <label>Date: </label>
                                <input type="text" class="form-control" id="sdate" value="<?php echo date(" d-m-Y ") ; ?>" name="date" autocomplete="off">
                            </div>

                            <div class="col-sm-3">
                                <label>Supplier Code</label>
                                <span class="memberCodeErrorMessage errorMessage" id="supplierCodeErrorMessage"></span>
                                <input id="supplierCode" class="form-control" name="partyCode" required data-name="code">
                            </div>

                            <div class="col-sm-3">
                                <label>Supplier Name</label>
                                <span class="supplierNameErrorMessage errorMessage" id="supplierNameErrorMessage"></span>
                                <input class="form-control" id="supplierName" name="partyName" required="true" data-name="name">
                            </div>

                            <div class="col-sm-3">
                                <label>Current Balance</label>
                                <input class="form-control noinput" id="supplierBalance" readonly tabindex="-1">
                            </div>

                            <div class="col-sm-3">
                                <label>Amount</label>
                                <input type="text" required="true" class="form-control" placeholder="Advance" id="samount" name="amount">
                            </div>

                            <div class="col-sm-3">
                                <label>Remark</label>
                                <input type="text" class="form-control" placeholder="Remarks" id="sremark" name="remark">
                            </div>

                            <div class="col-sm-3 pr-30">
                                <div class="pt-20"></div>
                                <button type="submit" name="submit" class="btn btn-primary">Add to Credit</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>


        </div>


    </div>

    <div class="table-back ">
        <table id="advance-table" class="display tright" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Party Code</th>
                    <th>Party Name</th>
                    <th>Date <small>(DD-MM-YYYY)</small></th>
                    <th>Amount <small>&#8377; (INR)</small></th>
                    <th>Remark</th>
                </tr>
            </thead>

            <tbody class="table-transactions">

            </tbody>
        </table>

    </div>

</div>



<script>

	var dairyId = document.getElementById("dairyId").value;

                                    $( function() {
                                        var members = [
                                            <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $memberInfoData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {
                                                    value: "<?php echo e($memberInfoData->memberPersonalCode); ?>",
                                                    label: "<?php echo e($memberInfoData->memberPersonalCode); ?>",
                                                    desc: "<?php echo e($memberInfoData->memberPersonalName); ?>",
                                                },
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ];

                                        var membersName = [
                                            <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $memberInfoData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {
                                                    value: "<?php echo e($memberInfoData->memberPersonalName); ?>",
                                                    label: "<?php echo e($memberInfoData->memberPersonalName); ?>",
                                                    desc: "<?php echo e($memberInfoData->memberPersonalCode); ?>",
                                                },
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ];

                                        var customer = [
                                            <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {
                                                    value: "<?php echo e($cust->customerCode); ?>",
                                                    label: "<?php echo e($cust->customerCode); ?>",
                                                    desc: "<?php echo e($cust->customerName); ?>",
                                                },
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ];

                                        var customerName = [
                                            <?php $__currentLoopData = $customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {
                                                    value: "<?php echo e($cust->customerName); ?>",
                                                    label: "<?php echo e($cust->customerName); ?>",
                                                    desc: "<?php echo e($cust->customerCode); ?>",
                                                },
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ];
                                    
                                        var suppliers = [
                                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {
                                                    value: "<?php echo e($sup->supplierCode); ?>",
                                                    label: "<?php echo e($sup->supplierCode); ?>",
                                                    desc: "<?php echo e($sup->supplierFirmName); ?>",
                                                },
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ];

                                        var supplierName = [
                                            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {
                                                    value: "<?php echo e($sup->supplierFirmName); ?>",
                                                    label: "<?php echo e($sup->supplierFirmName); ?>",
                                                    desc: "<?php echo e($sup->supplierCode); ?>",
                                                },
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ];
                                    
                                        $( "#memberCode" ).autocomplete({
                                            minLength: 0,
                                            source: members,
                                            focus: function( event, ui ) {
                                                $( "#memberCode" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#memberCode" ).val( ui.item.value );
                                                $( "#memberName" ).val( ui.item.desc );
                                                // SetMemberCode();
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#memberName" ).autocomplete({
                                            minLength: 0,
                                            source: membersName,
                                            focus: function( event, ui ) {
                                                $( "#memberName" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#memberCode" ).val( ui.item.desc );
                                                $( "#memberName" ).val( ui.item.value );
                                                // SetMemberCode();
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#customerCode" ).autocomplete({
                                            minLength: 0,
                                            source: customer,
                                            focus: function( event, ui ) {
                                                $( "#customerCode" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#customerCode" ).val( ui.item.value);
                                                $( "#customerName" ).val( ui.item.desc);
                                                // SetCustomerCode();
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#customerName" ).autocomplete({
                                            minLength: 0,
                                            source: customerName,
                                            focus: function( event, ui ) {
                                                $( "#customerName" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#customerCode" ).val( ui.item.desc);
                                                $( "#customerName" ).val( ui.item.value);
                                                // SetCustomerCode();
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };


                                        $( "#supplierCode" ).autocomplete({
                                            minLength: 0,
                                            source: suppliers,
                                            focus: function( event, ui ) {
                                                $( "#supplierCode" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#supplierCode" ).val( ui.item.value );
                                                $( "#supplierName" ).val( ui.item.desc );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };

                                        $( "#supplierName" ).autocomplete({
                                            minLength: 0,
                                            source: supplierName,
                                            focus: function( event, ui ) {
                                                $( "#supplierName" ).val( ui.item.label );
                                                return false;
                                            },
                                            select: function( event, ui ) {
                                                $( "#supplierCode" ).val( ui.item.desc );
                                                $( "#supplierName" ).val( ui.item.value );
                                                return false;
                                            }
                                        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                                            return $( "<li>" ).append( "<div>" + item.label + "<br>" + item.desc + "</div>" ).appendTo( ul );
                                        };
                                    });


$("#memberCode, #memberName").on("change, focusout", function(){
    v = $(this).val();
    if(v==(null||'')){
        return false;
    }
    getUserDetail(v, this, $(this).data("name"), "member", "");
})

$("#customerCode, #customerName").on("change, focusout", function(){
    v = $(this).val();
    if(v==(null||'')){
        return false;
    }
    getUserDetail(v, this, $(this).data("name"), "customer", "");
})

$("#supplierCode, #supplierName").on("change, focusout", function(){
    v = $(this).val();
    if(v==(null||'')){
        return false;
    }
    getUserDetail(v, this, $(this).data("name"), "supplier", "");
})

	function getUserDetail(q, elm, qtype, user, no){

		if(q){
			loader("show");

			$.ajax({
				type:"POST",
				url:'getUserDetail' ,
				data: {
					q: q,
					qtype: qtype,
					dairyId: dairyId,
					user: user
				},
				success:function(res){
					if(res.error){
						$("#response-alert").html(res.msg).show();
						$(elm).addClass("has-error");
					}else{
						$("#response-alert").hide();
						setUserData(res.data, elm, user, no);
					}
					loader("hide");
                    console.log(res);
				},
				error:function(res){
					console.log(res);
				}
			});

		}
	}

    function setUserData(data, elm, user, no){
		if(user=="member"){
			$("#memberCode").val(data.code);
			$("#memberName").val(data.name);
            $("#memberBalance").val(data.bal+ " "+ data.balType);
		}
		
		if(user=="customer"){
			$("#customerCode").val(data.code);
			$("#customerName").val(data.name);
            if(!data.isCash){
                $("#customerBalance").val(data.bal+ " "+ data.balType);
            }else{
                $("#customerBalance").val("Cash User");
            }
		}
        if(user=="supplier"){
            $("#supplierCode").val(data.code);
            $("#supplierName").val(data.name);
            $("#supplierBalance").val(data.bal+ " "+ data.balType);
        }
		
		$("#customerCode, #customerName, #memberCode, #memberName, #supplierCode, #supplierName").removeClass("has-error");
	}

    $(document).ready(function() {
		$('#advance-table').DataTable({
            "ajax": 'getAdvanceData',
            "order": [],
		});

        $("#date, #mdate, #sdate").datetimepicker({
            format:"DD-MM-YYYY"
        })
	});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('theme.default', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>