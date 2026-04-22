<table id="MyTable" class="MyTable-dailyTransactionClass display table-bordered tright" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Member Code</th>
            <th>Milk Type</th>
            <th>Quantity</th>
            <th>Fat</th>
            <th>Snf</th>
            <th>Fat Kg</th>
            <th>Snf Kg</th>
            <th>Rate</th>
            <th>Total Amount</th>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>
    </thead>

    <tbody class="">
       <?php 
            $i = 0;
            $totalQty = 0;
            $totalFat = 0;
            $totalSnf = 0;
            $totalFatKg = 0;
            $totalSnfKg = 0;
            $totalRate = 0;
            $totalAmount = 0;
            $fatTotal=0;
            $totalQty=0;
        ?>
        <?php $i = 0; ?> <?php $__currentLoopData = $dailyTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php $i++; ?>

           <?php 
                $i++;
                $totalQty += $d->milkQuality;
                $totalFat += $d->fat;
                $totalSnf += $d->snf ?? 0;
                $totalFatKg += $d->fatkg;
                $totalSnfKg += $d->snfkg;
                $totalRate += $d->rate;
                $totalAmount += $d->amount;

            ?>

        <tr>
            <td><?php echo e($i); ?></td>
            <td><?php echo e($d->memberCode); ?></td>
            <td><?php echo e($d->milkType); ?></td>
            <td><?php echo e($d->milkQuality); ?></td>
            <td><?php echo e(number_format($d->fat, 1, ".", "")); ?></td>
            <td><?php if($d->snf==NULL): ?> - <?php else: ?> <?php echo e($d->snf); ?> <?php endif; ?></td>
            <td><?php echo e($d->fatkg); ?></td>
            <td><?php echo e($d->snfkg); ?></td>
            <td><?php echo e(number_format($d->rate, 2, ".", "")); ?></td>
            <td><?php echo e(number_format($d->amount, 2, ".", "")); ?></td>
            <td>
                <a href="DailyTransactionEdit?transactionId=<?php echo e($d->id); ?>" title="Edit" role="button" onclick="editTransaction(event, <?php echo e($d->id); ?>, '<?php echo e($d->memberCode); ?>')"> <i class="fa fa-edit"></i> </a>
                &nbsp;
                <a href="DailyTransactionPsf?listId=<?php echo e($d->id); ?>" title="Get PDF File"> <i class="fa fa-file-pdf-o"></i> </a>
                &nbsp;
                
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>

    
    <?php
    $fatTotal = ($totalQty > 0) ? floor(($totalFatKg / $totalQty * 100) * 10) / 10 : 0;
    $snfTotal = ($totalQty > 0) ? round(($totalSnfKg / $totalQty * 10000)) : 0;
    $rateTotal = ($totalQty > 0) ? number_format($totalAmount / $totalQty, 2, ".", "") : 0;
?>

<tfoot style="font-weight: bold; background: #f4f4f4;">
    <tr>
        <td colspan="3" class="text-right">Total:</td>
        <td><?php echo e(number_format($totalQty, 1, ".", "")); ?></td>
        <td><?php echo e(number_format($fatTotal, 2, ".", "")); ?></td>   
        <td><?php echo e(number_format($snfTotal, 2, ".", "")); ?></td>   
        <td><?php echo e(number_format($totalFatKg, 2, ".", "")); ?></td>
        <td><?php echo e(number_format($totalSnfKg, 2, ".", "")); ?></td>
        <td><?php echo e($rateTotal); ?></td>                            
        <td><?php echo e(number_format($totalAmount, 2, ".", "")); ?></td>
        <td></td>
    </tr>
</tfoot>

    

</table>