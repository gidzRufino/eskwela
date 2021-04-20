<table style="width:100%;" class="table table-bordered table-striped table-hover payrollTable text-center">

    <tr style="font-weight:bold; border-bottom: 2px solid black;">
        <td style="width:10px;">Avatar</td>
        <td style="width:150px;">Name of Employee</td>
        <td style="width:50px;">Position</td>
        <td>Basic Pay</td>
        <?php 
        $charges = Modules::run('hr/payroll/getPayrollCharges', $pc_code);
        foreach($charges as $deductions): 
            
            ?>
            <td ><?php echo $deductions->pi_item_name ?></td>
            
        <?php 
        endforeach; ?>
        <td>Other Deductions</td>
        <td>Total Deductions</td>
        <td>NetPay</td>
        <td></td>
    </tr>
   <?php
    $salaryTotal = 0;
    $sssTotal = 0;
    $phTotal = 0;
    $pagibigTotal = 0;
    $tinTotal = 0;
    $contTotal = 0;
    $netTotal = 0;
    $total = 0;
    $totalOd = 0;
    $totalNet = 0;
    $overAllDeductibleTardy = 0;
    
    
    foreach($getPayrollReport as $pr):
        if($pr->salary!=0):   
            switch ($paymentSchedule->monthly):
                case 0:
                    $over = 2;
                    break;
                case 1:
                    $over = 1;
                    break;
                case 2:
                    $over = 4;
                    break;
            endswitch;
            
            $firstDay = Modules::run('main/getFirstLastDay', date("F", mktime(0, 0, 0, date('m', strtotime($startDate)), 10)), date('Y', strtotime($startDate)), 'first');
            $lastDay = Modules::run('main/getFirstLastDay', date("F", mktime(0, 0, 0, date('m', strtotime($endDate)), 10)), date('Y', strtotime($startDate)), 'last');
//            $firstDayName = date('D',  strtotime('first Day of '.date("F", mktime(0, 0, 0, segment_4, 10)).' '.date('Y', strtotime($startDate))));
            $holiday = Modules::run('calendar/holidayExist', date('m', strtotime($startDate)), date('Y', strtotime($startDate)));
            $schoolDays = Modules::run('main/getNumberOfSchoolDays', $firstDay, $lastDay, date('m', strtotime($startDate)), date('Y', strtotime($startDate)));
            
            //calculation of the Basic Pay and the benefits
            $workdays = $hrdb->count_workdays($startDate, $endDate);
            $salary = number_format(($pr->salary), 2, '.', ',');
            
            $expectedHours = $workdays * 8;
            $days = Modules::run('hr/hrdbprocess/getPayrollTimes', $pr->user_id, $startDate, $endDate);
            $days = json_decode($days);
            
            $totalDailySalary = ($pr->salary/30)*$days->present;
            //use this if exact deduction
            //$totalDeductibleTardy = round($pr->salary/$schoolDays/8/60*$undertime, 2);
            
            $totalDeductibleTardy = round($pr->salary/30/8/60*$days->undertime, 2);
            $totalDeductibleTardy = ($pr->pay_type?0:$totalDeductibleTardy);
            $netpay = ($pr->salary/$over);
            
      ?>
    <tr>
        <td style="text-align: center"><img class="img-circle"  style="width:30px;" src="<?php echo base_url().'uploads/'.$pr->avatar  ?>" /></td>
        <td  onclick="$('#addCharges').modal('show'), $('#em_id').val('<?php echo $pr->employee_id ?>'), $('#item_pc_code').val('<?php echo $pc_code ?>')" ><?php echo $pr->firstname.' '.$pr->lastname ?></td>
        <td><?php echo $pr->position ?></td>
        <td><?php echo $salary ?></td>
        <?php 
        $totalStat= 0;
        $addOn = 0;
        $totalNetPayroll = 0;
        $items = 1;
        $deduct = array();
        foreach($charges as $deductions): 
            $items++;
        $charge = Modules::run('hr/payroll/getPayrollChargesByItem', $deductions->pc_item_id, $pc_code, $pr->employee_id);
        //print_r($charge);
            $amount = ($charge!=NULL?$charge->pc_amount:0);
                ?>
                <td class="tdValue_<?php echo $items ?>" ><?php echo $amount ?></td>

            <?php
            if($charge!=NULL):
                if($charge->pi_item_type==1):
                    $addOn += $amount;
                else:
                    $totalStat += $amount;
                endif;
            endif;
        endforeach;
        ?>
        <td><?php echo $totalDeductibleTardy; ?></td>
        <td><?php echo number_format(($totalDeductibleTardy+$totalStat), 2, '.', ',') ?></td>
        <td><?php echo number_format((($netpay+$addOn)-($totalDeductibleTardy+$totalStat)), 2, '.', ',') ?></td>
        <td>
            <?php $ptrans = Modules::run('hr/payroll/checkTransaction', $pr->employee_id, $pc_code); ?>
            <div class="btn-group">
                <?php if($ptrans==NULL):?>
                    <button onclick="approvePayroll('<?php echo $pr->employee_id?>','<?php echo (($netpay+$addOn)-($totalDeductibleTardy+$totalStat)) ?>','<?php echo $pc_code ?>')" class="btn btn-xs btn-warning">Approve</button>
                    <button class="btn btn-xs btn-danger disabled">Release</button>
                <?php else: ?>
                    <button class="btn btn-xs btn-success">Approved</button>
                    <button onclick="releasePayroll('<?php echo $pr->employee_id?>','<?php echo (($netpay+$addOn)-($totalDeductibleTardy+$totalStat)) ?>','<?php echo $pc_code ?>')"  class="btn btn-xs btn-danger">Release</button>
                <?php endif; ?>
            </div>
        </td> 
    </tr>
    <?php
        $totalNetPay += $netpay;
        $addOnTotal += $addOn;
        $statTotal += $totalStat;
        $totalOD += ($totalDeductibleTardy+$totalStat);
        $totalTardy += $totalDeductibleTardy;
        unset($addOn);
        unset($totalStat);
        unset($expectedPerHourRate);
        $items =1;
      endif;
      endforeach;  
    ?>
    <tr style='border-top-style:double; font-weight: bold;'>
        <td colspan="3">Total</td>
        <td><?php echo number_format($totalNetPay, 2, '.',','); ?></td>
        <?php
        $charges = Modules::run('hr/payroll/getPayrollCharges', $pc_code);
        $totalNetPayroll = (($totalNetPay+$addOnTotal)-($totalOD));
        foreach($charges as $deductions): 
            $items++;
            $chrg = Modules::run('hr/payroll/getPayrollChargesByItem', $deductions->pc_item_id, $pc_code);
            ?>
            <td class="total_<?php echo $items ?>"></td>
            
        <?php 
        
        endforeach; ?>
        <td><?php echo number_format($totalTardy,2,'.',','); ?></td>
        <td><?php echo number_format($totalOD,2,'.',','); ?></td>
        <td><?php echo number_format($totalNetPayroll,2,'.',','); ?></td>
        <td></td>
    </tr>
</table>
<input type="hidden" id="charges" value="<?php echo $items; ?>" />

<script type="text/javascript">
        
    function releasePayroll(em_id, netPay, pc_code)
    {
        var url = "<?php echo base_url().'hr/payroll/releasePayroll/' ?>"; // the script where you handle the form input.
        $.ajax({
           type: "POST",
           url: url,
            data: {
            em_id: em_id,
            netPay: netPay,
            pc_code:pc_code,
            csrf_test_name: $.cookie('csrf_cookie_name')
        },
            
           beforeSend: function() {
                //$('#consolidatedPayroll').html('<b class="text-center">Please Wait while Payroll is generating...</b>')
            },
           success: function(data)
           {
               generatePayroll($('#payPeriod').val());
           }
         });

        return false; // avoid to execute the actual submit of the form
    }
        
    function approvePayroll(em_id, netPay, pc_code)
    {
        var url = "<?php echo base_url().'hr/payroll/approvePayroll/' ?>"; // the script where you handle the form input.
        $.ajax({
           type: "POST",
           url: url,
            data: {
            em_id: em_id,
            netPay: netPay,
            pc_code:pc_code,
            csrf_test_name: $.cookie('csrf_cookie_name')
        },
            
           beforeSend: function() {
                //$('#consolidatedPayroll').html('<b class="text-center">Please Wait while Payroll is generating...</b>')
            },
           success: function(data)
           {
               alert('Successfully Approved');
               generatePayroll($('#payPeriod').val())
           }
         });

        return false; // avoid to execute the actual submit of the form
    }
    
    function getTotal()
    {
        var charges = $('#charges').val();
        var total = 0;
        for(var i=1; i<=charges;i++)
        {
            $('.tdValue_'+i).each(function(){
                total += parseFloat($(this).text())
            })
            $('.total_'+i).text(numberWithCommas(Number(total.toFixed(1)).toFixed(2)))
            
            total=0;
        }
        
    }
    
    function numberWithCommas(x) {
        if(x==null){
            x = 0;
        }
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>

