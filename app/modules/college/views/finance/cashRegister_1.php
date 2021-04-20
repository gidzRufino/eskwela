<table class="table table-striped">
    <tr>
        <th></th>
        <th style="width: 20%;">Item Description</th>
        <th class="text-center">From</th>
        <th class="text-center">To</th>
        <th class="text-right">Charge</th>
        <th class="text-right">Payments</th>
        <th class="text-right">Balance</th>
        <th style="width: 20%;" class="text-right">Amount</th>
    </tr>
    <tbody id="itemBody">
        <?php 
        $chargeAmount = 0;
        $charges = Modules::run('college/finance/financeChargesByPlan',$year_level, $school_year, $sem, $plan->fin_plan_id );
        foreach ($charges as $c): 
            
            $discount = Modules::run('finance/billing/getTransactionByCategory', $student->uid, 0,$student->school_year,$c->category_id,2);
            $totalDiscount += $discount->amount;
            
            $totalCharges += $c->amount;
            $chargeAmount = $c->amount;
            $inxcharges = Modules::run('finance/finance_pisd/inExtraCharges', $user_id, $c->item_id);
            foreach ($inxcharges as $inxc):
                $inxcharge += $inxc->extra_amount;
            endforeach;
            if(!$inxcharges):
            else:
                $chargeAmount = $chargeAmount+$inxcharge;
            endif;
            
            $payments = Modules::run('finance/finance_pisd/getTransactionByItemId', $student->uid, NULL,$school_year, $c->item_id);
            $tfdiscount = Modules::run('finance/billing/getTransactionByCategory', $student->uid, 0, $student->school_year, $c->category_id, 2);
            $tfdiscounts = $tfdiscount->amount;
            foreach ($payments->result() as $p):
                $totalPayments += $p->t_amount;
            endforeach;
            
            $totalBalance = $chargeAmount - ($totalPayments+$tfdiscounts);
            if($c->category_id==1):
                $due = $c->amount/10;
                if($tfdiscounts>=$chargeAmount):
                    $totalBalance = 0;
                endif;
            else:
                $due = $totalBalance;
            endif;
            if($totalBalance > 0):
                ?>
                 <tr id="trp_<?php echo $c->item_id ?>" tr_val="" totalvalue="">
                     <td id="control_<?php echo $c->item_id ?>"  class="text-center"><input td_id ="<?php echo $c->item_id ?>" class="form-check-input" type="checkbox" value="" id="defaultCheck1"/></td>
                     <td><?php echo strtoupper($c->item_description) ?></td>
                     <td class="text-center">
                         <select style="width:90%;"  name="sfrom<?php echo $c->item_id ?>" id="sfrom<?php echo $c->item_id ?>" hidden required>
                            <option>From</option> 
                        
                        <?php 
                            $months = array("Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec");
                            foreach ($months as $month) {
                        ?>                        
                        
                            <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                        
                        <?php 
                            }
                        ?>
                        
                        </select>
                     </td>  
                     <td class="text-center">
                         <select style="width:90%;"  name="sto<?php echo $c->item_id ?>" id="sto<?php echo $c->item_id ?>" hidden required>
                            <option>To</option> 
                        
                        <?php 
                            $months = array("Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec");
                            foreach ($months as $month) {
                        ?>                        
                        
                            <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                        
                        <?php 
                            }
                        ?>
                        
                        </select>
                     </td>      
                     <td class="text-right"><?php echo number_format($chargeAmount,2,".",",") ?></td>
                     <td class="text-right"><?php echo number_format($totalPayments,2,".",",") ?></td>
                     <td class="text-right"><?php echo number_format($totalBalance,2,".",",") ?></td>
                     <td id="due_<?php echo $c->item_id ?>" class="text-right editable_<?php echo $c->item_id ?>"  tdValue="0"></td>
                 </tr>
                 <?php
            endif;     
                $totalPayments = 0; 
                $totalBalance = 0;
        endforeach;
        $extraC = Modules::run('finance/finance_pisd/getExtraFinanceCharges',$user_id, 0, $student->school_year);
        if($extraC->num_rows()>0):
            foreach ($extraC->result() as $exc):
                $incharges = Modules::run('finance/finance_pisd/inCharges', $exc->item_id, $plan->fin_plan_id);
                if(!$incharges):
                    $mxPayments = Modules::run('finance/finance_pisd/getTransactionByItemId', $student->uid, NULL,$student->school_year, $exc->item_id);
                    foreach ($mxPayments->result() as $mxp):
                        $totalMxpayments += $mxp->t_amount;
                    endforeach;
                    $totalMxBalance = $exc->amount - $totalMxpayments;
                    //echo $totalMxpayments;
                    if($totalMxBalance > 0):
                    ?>
                    <tr id="trp_<?php echo $exc->item_id ?>" tr_val="" totalvalue="">
                        <td id="control_<?php echo $exc->item_id ?>"  class="text-center"><input td_id ="<?php echo $exc->item_id ?>" class="form-check-input" type="checkbox" value="" id="defaultCheck2"/></td>
                        <td><?php echo strtoupper($exc->item_description) ?></td>
                        <td class="text-center">
                         <select style="width:90%;"  name="sfrom<?php echo $c->item_id ?>" id="sfrom<?php echo $c->item_id ?>" hidden required>
                            <option>From</option> 
                        
                        <?php 
                            $months = array("Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec");
                            foreach ($months as $month) {
                        ?>                        
                        
                            <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                        
                        <?php 
                            }
                        ?>
                        
                        </select>
                     </td>  
                     <td class="text-center">
                         <select style="width:90%;"  name="sto<?php echo $c->item_id ?>" id="sto<?php echo $c->item_id ?>" hidden required>
                            <option>To</option> 
                        
                        <?php 
                            $months = array("Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec");
                            foreach ($months as $month) {
                        ?>                        
                        
                            <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                        
                        <?php 
                            }
                        ?>
                        
                        </select>
                     </td>      
                        <td class="text-right"><?php echo number_format($exc->amount,2,".",",") ?></td>
                        <td class="text-right"><?php echo number_format($totalMxpayments,2,".",",") ?></td>
                        <td class="text-right"><?php echo number_format($totalMxBalance,2,".",",") ?></td>
                        <td id="due_<?php echo $exc->item_id ?>" class="text-right editable_<?php echo $exc->item_id ?>"  tdValue="0"></td>
                    </tr>
                    <?php 
                    endif;
                    $totalMxpayments = 0;
                endif;    
                
            endforeach;  
        endif;
                                
        ?>
    </tbody>
</table>

 <div class="bg-success clearfix" style="padding-top:10px;">
     <div class="form-group col-lg-3">
        <label>TOTAL</label>
        <input class="text-center" value="0" style="font-size: 25px; font-weight: bold; color: red; width: 100%;" name="pttAmount" id="pttAmount" disabled>
    </div>
     <div class="form-group col-lg-3">
        <label class="control-label" for="input">AMOUNT TENDER</label>
        <input class="text-center" style="font-size: 25px; font-weight: bold; color: green; width: 100%;" name="ptAmountTendered" id="ptAmountTendered" onblur="cash_change()" required>

    </div> 
    <div class="form-group col-lg-3">
        <label>CHANGE</label>
        <input class="text-center" style="font-size: 25px; font-weight: bold; color: blue; width: 100%;" name="ptChange" id="ptChange" disabled>
    </div>
     <div class="form-group col-lg-3">
        <button type="button" class="btn btn-success" id="paynow" onclick="$('#confirmPayment').modal('show') " style="width: 100%;"><i class="fa fa-thumbs-up fa-lg fa-fw"></i><b>PAY NOW!!!</b></button>
        <button type="button" class="btn btn-danger" onclick="$('#pttAmount').val(0)" style="width: 100%; margin-top: 10px;"><b><i class="fa fa-times fa-lg fa-fw"></i>C L E A R</b></button>
         
     </div>
 </div>
<script type="text/javascript">
var total = 0;

$(function () { 
    
    $('.form-check-input').click(function()
    {
        if($(this).is(':checked'))
           { 
            var id = $(this).attr('td_id');
            if (id==1||id==10) {
                var sfromid = '#sfrom'+id; 
                var stoid = '#sto' + id;
                $(sfromid).removeAttr('hidden');
                $(stoid).removeAttr('hidden');
            }
            var OriginalContent = $('.editable_'+id).attr('tdValue'); 
            $('.editable_'+id).addClass("cellEditing"); 
            $('.editable_'+id).html("<input  type='text' style='height:30px; text-align:center' value='' />"); 
            $('.editable_'+id).children().first().focus(); 
            $('.editable_'+id).children().first().keypress(function (e) 
                { if (e.which == 13) 
                    { 
                        var newContent = $(this).val(); 
                        calculate(newContent, 'add')
                        $('.editable_'+id).attr('tdValue', newContent);
                        $('#trp_'+id).attr('tr_val', id+'_'+newContent)

                        $(this).parent().html(numberWithCommas(parseFloat(newContent).toFixed(2))); 
                        $(this).parent().removeClass("cellEditing");
                    }
                });
            
        }else{
            var id = $(this).attr('td_id');
            var OriginalContent = $('.editable_'+id).attr('tdValue');
            if (id==1||id==10) {
                var sfromid = '#sfrom'+id; 
                var stoid = '#sto' + id;
                $(sfromid).attr('hidden', 'hidden');
                $(stoid).attr('hidden', 'hidden');
            }
            calculate(OriginalContent, 'minus');
            $('.editable_'+id).attr('tdValue', 0)
            $('.editable_'+id).html('');
            $('#trp_'+id).attr('tr_val','');
            
        }
        
    });
    
    
    function calculate(value, option)
    {
        if(option=='add')
        {
            total = parseFloat(total) + parseFloat(value);
        }else{
            total = parseFloat(total) - parseFloat(value);
            
        }    
        $('#pttAmount').val(numberWithCommas(total.toFixed(2)));
    }
})


    function numberWithCommas(x) {
            if(x==null){
                x = 0;
            }
            
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
  
</script>  
    