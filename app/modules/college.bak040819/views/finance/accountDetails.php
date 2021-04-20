<?php
$semester = Modules::run('main/getSemester');

if($sem==NULL):
    $sem = $semester;
endif;
$student = Modules::run('college/getSingleStudent', base64_decode($st_id), $this->session->userdata('school_year'), $sem);

switch ($student->year_level):
    case 1:
        $year_level = 'First Year';
    break;
    case 2:
        $year_level = 'Second Year';
    break;
    case 3:
        $year_level = 'Third Year';
    break;
    case 4:
        $year_level = 'Fourth Year';
    break;
    case 5:
        $year_level = 'Fifth Year';
    break;
endswitch;

$loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $student->admission_id, $sem);
$totalUnits = 0;
foreach ($loadedSubject as $sl):
    $totalUnits += ($sl->s_lect_unit + $sl->s_lab_unit);
endforeach;

$plan = Modules::run('college/finance/getPlanByCourse', $student->course_id, $student->year_level);
$charges = Modules::run('college/finance/financeChargesByPlan',$student->year_level, $student->school_year, $sem, $plan->fin_plan_id );

$tuition = Modules::run('college/finance/getChargesByCategory',1, $student->semester, $student->school_year, $plan->fin_plan_id );

$addCharge = Modules::run('college/finance/financeChargesByPlan',NULL, $student->school_year, $sem );

$promisoryRequest = Modules::run('college/finance/getPromisoryRequest', $student->uid, $sem);

?>

<div class="well col-lg-12" id="profBody">
    <div class="col-lg-2">
        <?php if($this->session->userdata('position_id')!=4): ?>
        <!--<a href="<?php echo base_url().'main/crop/'.$this->uri->segment(3) ?>">Crop Image</a>-->
        <?php endif; 
        if($student->u_id==""):
                $user_id = $student->us_id;
            else:
                $user_id = $student->u_id;
            endif;
        ?>
        <div>
            <img class="img-circle img-responsive" style="width:150px; border:5px solid #fff" src="<?php if($student->avatar!=""):echo base_url().'uploads/'.$student->avatar;else:echo base_url().'uploads/noImage.png'; endif;  ?>" />
        </div>
    </div>
    <div class="col-lg-6">
        <h2 style="margin:3px 0;">
            <span id="name" style="color:#BB0000;"><?php echo $student->firstname." ". $student->lastname ?></span>

        </h2>
        <h4 style="color:black; margin:3px 0;"><?php echo $student->course; ?> - <span id="a_section"><?php echo $year_level; ?></span>
        </h4>
        <h3 style="color:black; margin:3px 0;">
            <small>
                <a title="double click to edit" id="a_user_id"  style="color:#BB0000;">
                     <?php echo $student->uid ?>
                 </a>
            </small>

        </h3>
    </div>
    <input type="hidden" id="st_id" value="<?php echo $student->uid ?>" /> 
    <input type="hidden" id="course_id" />
    <input type="hidden" id="year_level" />
    <input type="hidden" id="finPlan_id" value="<?php echo $plan->fin_plan_id ?>" />
    <input type="hidden" id="admission_id" value="<?php echo $student->admission_id ?>" />
    <input type="hidden" id="currentSemester" value="<?php echo $sem?>" />
    <input type="hidden" id="currentYear" value="<?php echo $student->school_year?>" />
    <input type="hidden" id="totalUnits" value="<?php echo $totalUnits ?>" />
    <div class="btn-group-vertical pull-right">
        <button type="button" class="btn btn-default btn-xs" onclick="viewSL('<?php echo $student->admission_id ?>','<?php echo $sem?>')">View Study Load</button>
        <a href="<?php echo base_url('college/finance/printPermit/'.$this->uri->segment(4).'/'.$this->uri->segment(5)) ?>" target="_blank" class="btn btn-default btn-xs">Print Exam Permit</a>
    </div>
</div>
<div class="col-lg-12">
    <div class="btn-group pull-right" role="group" aria-label="">

        <button type="button" class="btn btn-default" onclick="$('#promisoryNoteRequest').modal('show')">Promisory Note Request</button>
        <button type="button" class="btn btn-default <?php echo ($promisoryRequest->num_rows()>0?'':'disabled'); ?> <?php echo ($promisoryRequest->num_rows()>0?($promisoryRequest->row()->fr_approved?'btn-success':'btn-danger'):'btn-default') ?>" onclick="$('#viewPromisoryNoteRequest').modal('show')" >
            View Request <span class="badge"><?php echo ($promisoryRequest->row()->fr_approved?'':($promisoryRequest->num_rows()>0?$promisoryRequest->num_rows():'')); ?></span>
        </button>
     </div>
        
    <div id="viewPromisoryNoteRequest" class="modal fade" style="width:25%; margin:30px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="panel panel-yellow">
            <div class="panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Promisory Note Request  
            </div>
             <div class="panel-body">
                <div class="form-group">
                    <label>Details</label>
                    <textarea disabled="disabled" class="textarea form-control">
                        <?php echo $promisoryRequest->row()->fr_remarks; ?>
                    </textarea>
                </div>
                <?php
                    if($promisoryRequest->num_rows()>0 && $promisoryRequest->row()->fr_approved):
                ?>
                    <div class="form-group">
                        <label>Amount Allowed : <strong><?php echo $promisoryRequest->row()->fr_allowable_amount ?></strong> </label>
                        
                    </div>
                <?php 
                    else:
                ?>
                 
                    <div class="form-group">
                        <label>Amount Allowed </label>
                        <input type="text" id="request_amount" class="form-control" onclick="$(this).val('')" placeholder="Amount" />
                        <input type="hidden" id="request_id" value="<?php echo $promisoryRequest->row()->fr_id ?>" />
                    </div>
                <?php
                    endif;
                ?>
             </div>
             <?php 
                if($this->session->userdata('dept_id')==1 || $this->session->userdata('dept_id')==2):
             ?>
            <div class="panel-footer clearfix">
                <button data-dismiss='modal' class='btn btn-xs btn-danger pull-right'  onclick='promisoryAction(2)'>Reject</button>
                <a href='#'data-dismiss='modal' onclick='promisoryAction(1)' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-right'>Approve</a>&nbsp;&nbsp;
            </div>
             
             <?php
                endif;
                ?>
         </div>
    </div>

</div>
<div style="margin-top: 10px;" class="col-lg-12 no-padding">
        <div class="col-lg-5">
            <div class='panel panel-warning'>
                <div class='panel-heading clearfix'>
                    <h5 class="pull-left">Finance Details</h5>
                </div>
                <div class='panel-body'>
                    <table class="table table-hover table-striped">
                            <tr>
                                <th class="text-center" colspan="2"></th>
                                <th>
                                    <div class="btn-group pull-right" role="group" aria-label="">
                                        <button title="Set Finance Charges" class="btn btn-xs btn-info" onclick="setExtraFinanceCharges($('#selectCourse').val(), '<?php echo $student->year_level ?>')"><i class="fa fa-plus fa-fw"></i></button>
                                        
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th style="width:10%;">#</th>
                                <th style="width:50%;">Particulars</th>
                                <th style="width:40%; text-align: right;">Amount</th>
                            </tr>
                            <tbody id="finChargesBody">
                            <?php
                            $i=1;
                            $total=0;
                            $amount=0;
                            $tuitionFee = (($tuition->row()->amount*$totalUnits));
                            ?>
                                
                                <tr id="tr_<?php echo $c->charge_id ?>">
                                    <td><?php echo $i++;?></td>
                                    <td><?php echo 'TUITION '.$totalUnits.' UNITS @ '.(number_format($tuition->row()->amount,2,'.',','))  ?></td>
                                    <td id="td_<?php echo $c->charge_id ?>" class="text-right"><?php echo number_format($tuitionFee, 2, '.',',') ?></td>
                                </tr>
                            <?php    
                                foreach ($charges as $c):
                                    $next = $c->school_year + 1;
                                        $amount = ($c->item_id<=1 || $c->item_id<=2?0:$c->amount);
                                        if($c->item_id<=1 || $c->item_id<=2):
                                        else:
                                            ?>
                                            <tr id="tr_<?php echo $c->charge_id ?>">
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo $c->item_description.($c->item_id<=1 || $c->item_id<=2?' ( '.$totalUnits.' units )':'')  ?></td>
                                                <td id="td_<?php echo $c->charge_id ?>" class="text-right"><?php echo number_format($amount, 2, '.',',') ?></td>
                                            </tr>
                                           <?php
                                            
                                        endif;   
                                       $totalCharges += $amount;
                                endforeach;
                                $totalExtra = 0;
                                $extraCharges = Modules::run('college/finance/getExtraFinanceCharges',$student->uid, $sem, $student->school_year);
                               
                                
                                    ?>
                                        
                                    <tr style="background:yellow;">
                                        <th>TOTAL Charges</th>
                                        <th></th>
                                        <th class="text-right"><?php echo number_format($totalCharges, 2, '.',',') ?></th>
                                        <th></th>
                                    </tr>
                                    <?php
                                     if($extraCharges->num_rows()>0):
                                    foreach ($extraCharges->result() as $ec):
                                        ?>
                                            <tr style="background: #0ff" id="trExtra_<?php echo $ec->extra_id ?>" 
                                                delete_remarks="[ Extra Charges : <?php echo $ec->item_description?>, Amount:<?php echo number_format($ec->extra_amount, 2, '.',',')?>]"
                                                data-toggle="context" 
                                                data-target="#deleteExtraCharges" 
                                                onmouseover="$('#delete_extra_id').val('<?php echo $ec->extra_id ?>')">
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo $ec->item_description?></td>
                                                <td id="td_<?php echo $ec->extra_id ?>" class="text-right"><?php echo number_format($ec->extra_amount, 2, '.',',') ?></td>
                                            </tr>
                                        <?php
                                        $totalExtra += $ec->extra_amount;
                                        endforeach;
                                    endif;
                                    $total = $totalCharges + $tuitionFee + $totalExtra;
                                    ?>
                                    <tr style="background:yellow;">
                                        <th>TOTAL Extra Charges</th>
                                        <th></th>
                                        <th class="text-right"><?php echo number_format($totalExtra, 2, '.',',') ?></th>
                                        <th></th>
                                    </tr>
                                    <tr style="background:yellow;">
                                        <th>TOTAL Tuition</th>
                                        <th></th>
                                        <th class="text-right"><?php echo number_format($tuitionFee, 2, '.',',') ?></th>
                                        <th></th>
                                    </tr>
                                    <tr style="background:yellow;">
                                        <th>OVER ALL TOTAL</th>
                                        <th></th>
                                        <th class="text-right"><?php echo number_format($total, 2, '.',',') ?></th>
                                        <th></th>
                                    </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
        <div class="col-lg-7">
            <div class='panel panel-success'>
                <div class='panel-heading clearfix'>
                    <h5 class="pull-left">Payment/Discount History
                    </h5>
                    <a href="<?php echo base_url('college/finance/printSOA/'.$this->uri->segment(4).'/'.$this->uri->segment(5)) ?>" target="_blank" class="pull-right btn btn-danger" id="btnPrint"><i class="fa fa-print fa-2x"></i></a>
                    <button style="margin-right: 5px;" onclick="$('#cashRegister').modal('show')" class="pull-right btn btn-warning" id="btnPOS"><i class="fa fa-money fa-2x"></i></button>
                    
                </div>
                <div class='panel-body'>
                    <div style="margin:0" class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                          <div class="panel-heading clearfix pointer">
                                <div class="col-lg-2">Date</div>
                                <div class="col-lg-1">OR #</div>
                                <div class="col-lg-3">Particulars</div>
                                <div class="col-lg-2">Payment/Discounts</div>
                                <div class="col-lg-2"><span class="pull-right">Balance</span></div>
                                <div class="col-lg-2"><span class="pull-right">Remarks</span></div> 
                          </div>
                        </div>
                        <div class="panel panel-default">
                          <div class="panel-heading clearfix pointer">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-1"></div>
                                <div class="col-lg-3">TOTAL CHARGE</div>
                                <div class="col-lg-2"></div>
                                <div class="col-lg-2 no-padding">
                                    <span class="pull-right" style="font-weight:bold; font-size:18px"><?php echo number_format($total,2,'.',',') ?></span>
                                </div>
                                <div class="col-lg-2"></div>
                          </div>
                        </div>
                    
                    <div class="col-lg-12 no-padding"></div>
                    <?php
                            $transaction = Modules::run('college/finance/getTransactionByRefNumber', $student->uid, $sem, $student->school_year);
                            $paymentTotal = 0;
                            $i = 1;
                            if($transaction->num_rows()>0):
                                $balance = 0;
                                foreach ($transaction->result() as $tr):
                                    $i++;
                                    $total = $total - $tr->subTotal ;
                                    echo Modules::run('college/finance/getPaymentHistory', $tr, $total, $student);    
                                endforeach;
                            else:
                                
                            endif;    
                    ?>
                    <div style="margin:0" class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-danger">
                            <div class="panel-heading clearfix pointer">
                                <div class="col-lg-3"><b>RUNNING BALANCE</b></div>
                                <div class="col-lg-3"></div>
                                <div class="col-lg-2 no-padding"><span class="pull-right"></span></div>
                                <div class="col-lg-2 no-padding"><span class="pull-right" style="font-weight:bold; font-size:18px"><?php echo number_format($total, 2, '.',',')?></span></div>
                                <div class="col-lg-2"></div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

            

<div id="addFinanceOption" class="modal fade" style="width:20%; margin:10px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Add Extra Charges
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>Finance Item</label> <br />
                <select style="width:90%;"  name="inputFinItems" id="inputFinItems" required>
                  <option>Select Item</option> 
                  <option value="0">Apply Discount</option> 
                    <?php 
                           foreach ($fin_items as $i)
                             {   
                       ?>                        
                            <option value="<?php echo $i->item_id; ?>"><?php echo $i->item_description; ?></option>
                    <?php }?>
                </select>
                <button onclick="$('#addItemModal').modal('show')" class="btn btn-xs btn-info pull-right"><i class="fa fa-plus fa-fw"></i></button>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="text" id="fin_amount" class="form-control" onclick="$(this).val('')" placeholder="Amount" />
            </div>
            <div class="form-group">
                <label>School Year</label> <br />
                <select style="width:100%;"  name="inputCSY" id="inputCSY" required>
                  <option value="0">Select School Year</option> 
                    <?php 
                           foreach ($ro_years as $ro)
                             {   
                               $next = ($ro->ro_years+1);
                       ?>                        
                            <option value="<?php echo $ro->ro_years; ?>"><?php echo $ro->ro_years.' - '.$next; ?></option>
                    <?php }?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Semester</label>
                <select tabindex="-1" id="inputSem" name="inputSem"  class="col-lg-12">
                   <option>Select Semester</option>
                   <option value="1">First Semester</option>
                   <option value="2">Second Semester</option>
                   <option value="3">Summer</option>
                   
               </select>
             </div>
        </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='addExtraFinanceCharges()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-left'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-left'>Cancel</button>&nbsp;&nbsp;
        </div>
        
    </div>
        
</div>
    
<div id="promisoryNoteRequest" class="modal fade" style="width:25%; margin:30px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="panel panel-yellow">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Request for Promisory Note   
        </div>
         <div class="panel-body">
            <div class="form-group">
                <label>Remarks</label>
                <textarea id="promisoryRemarks" class="textarea form-control">
                    
                </textarea>
            </div>
         </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='requestPromisory()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-left'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-left'>Cancel</button>&nbsp;&nbsp;
        </div>
     </div>
</div>
    

<div id="addItemModal" class="modal fade" style="width:15%; margin:30px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="panel panel-yellow">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Add Finance Item    
        </div>
         <div class="panel-body">
            <div class="form-group">
                <label>Item</label>
                <input type="text" id="fin_item" class="form-control" placeholder="Item" />
            </div>
         </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='addItems()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-left'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-left'>Cancel</button>&nbsp;&nbsp;
        </div>
     </div>
</div>

<div id="addDiscount" class="modal fade" style="width:20%; margin:10px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-warning">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Apply Discounts
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>Finance Item</label> <br />
                <select style="width:90%;"  name="inputDiscountedItems" id="inputDiscountedItems" required>
                  <option>Select Item</option> 
                  <option value="0">General</option> 
                    <?php 
                           foreach ($fin_items as $i)
                             {   
                       ?>                        
                            <option value="<?php echo $i->item_id; ?>"><?php echo $i->item_description; ?></option>
                    <?php }?>
                </select>
                <button onclick="$('#addItemModal').modal('show')" class="btn btn-xs btn-info pull-right"><i class="fa fa-plus fa-fw"></i></button>
            </div>
            <div class="form-group">
                <label class="control-label">Discount Type</label>
                <select tabindex="-1" id="inputDiscountedType" name="inputDiscountedType"  class="col-lg-12">
                   <option value="0">Percent</option>
                   <option value="1">Amount</option>
                   
               </select>
             </div>
            <div class="form-group">
                <label>Value</label>
                <input type="text" id="discount_amount" class="form-control" onclick="$(this).val('')" placeholder="Amount" />
            </div>
            <div class="form-group">
                <label>School Year</label> <br />
                <select style="width:100%;"  name="inputDiscountedCSY" id="inputDiscountedCSY" required>
                  <option value="0">Select School Year</option> 
                    <?php 
                           foreach ($ro_years as $ro)
                             {   
                               $next = ($ro->ro_years+1);
                       ?>                        
                            <option value="<?php echo $ro->ro_years; ?>"><?php echo $ro->ro_years.' - '.$next; ?></option>
                    <?php }?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Semester</label>
                <select tabindex="-1" id="inputDiscountedSem" name="inputSem"  class="col-lg-12">
                   <option>Select Semester</option>
                   <option value="1">First Semester</option>
                   <option value="2">Second Semester</option>
                   <option value="3">Summer</option>
                   
               </select>
             </div>
            <div class="form-group">
                <label>Remarks</label>
                <input type="text" id="inputDiscountedRemarks" class="form-control" onclick="$(this).val('')" placeholder="Remarks" />
            </div>
        </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='applyDiscount()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-left'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-left'>Cancel</button>&nbsp;&nbsp;
        </div>
        
    </div>
        
</div>

<div id="cashRegister" class="modal fade" style="width:70%; margin:30px auto; " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="panel panel-red">
        <div class="panel-heading clearfix">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>CASH Register
            
            <div class="form-group pull-right">
                <select tabindex="-1" id="inputReceipt" name="inputReceipt"  class="col-lg-12">
                   <option value="0">Official Receipt</option>
                   <option value="1">Acknowledgment Receipt</option>
                   <option value="2">Temporary Receipt</option>
                   
               </select>
             </div>
            <input type="hidden" id="charge_id" />
        </div>
         <div class="panel-body">
             <div class="col-lg-8">
                 <div class="bg-info clearfix">
                     <div class="form-group col-lg-4">
                        <label>OR #</label>
                        <input type="text" id="refNumber" class="form-control" onclick="this.placeholder=$(this).val(), $(this).val('')" placeholder="OR Number" />
                    </div>
                     <div class="form-group col-lg-4">
                        <label class="control-label" for="input">Transaction Date</label>
                        <input class="form-control" name="transactionDate" type="text" value="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd" id="transactionDate" placeholder="" required>

                    </div> 
                    <div class="form-group col-lg-4">
                        <label>Remarks</label>
                        <input type="text" id="transRemark" class="form-control"  placeholder="Remarks" />
                    </div>
                 </div>
                <div class="well col-lg-12 no-padding">
                    <table class="table table-striped">
                        <tr>
                            <th>Item Description</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        <tbody id="itemBody">
                            
                        </tbody>
                    </table>
                </div>    
             </div>
             
            <div class="col-lg-4 text-center no-padding">
                <div class="panel panel-default">
                    <div class="panel-body bg-primary text-center">
                        <button type="button" onclick="$('#addCashItemModal').modal('show')" class="btn btn-info" id="add_items" style="width: 100%; height: 40px;"><i class="fa fa-plus fa-lg fa-fw"></i><b>Add Items</b></button>
                        <h4>T O T A L</h4>
                        <input class="text-center" style="font-size: 25px; font-weight: bold; color: red; width: 100%;" name="pttAmount" id="pttAmount" disabled>
                        <h4>Amount Tendered</h4>
                        <input class="text-center" style="font-size: 25px; font-weight: bold; color: green; width: 100%;" name="ptAmountTendered" id="ptAmountTendered" onblur="cash_change()" required>
                        <h4>C H A N G E</h4>
                        <input class="text-center" style="font-size: 25px; font-weight: bold; color: blue; width: 100%;" name="ptChange" id="ptChange" disabled>
                        <div class="row">
                            <div class="col-xs-12 col-md-12" style="margin-top: 20px;">
                                <button type="button" class="btn btn-success" id="paynow" onclick="saveTransaction()" style="width: 100%; height: 60px;"><i class="fa fa-thumbs-up fa-lg fa-fw"></i><b>PAY NOW!!!</b></button>
                                <button data-dismiss="modal" type="button" class="btn btn-danger" id="cancel_trans" style="width: 100%; height: 40px; margin-top: 10px;"><b><i class="fa fa-times fa-lg fa-fw"></i>C A N C E L</b></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
         </div>
     </div>
</div>

<div id="addCashItemModal" class="modal fade" style="width:15%; margin:50px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="panel panel-yellow">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Add Finance Item    
        </div>
         <div class="panel-body">
            <div class="form-group">
                <label>Finance Item</label> <br />
                <select style="width:90%;"  name="cashFinItems" id="cashFinItems" required onclick="calculateItem(this.value)">
                  <option value="0">Select Item</option> 
                    <?php 
                           foreach ($fin_items as $i)
                             {   
                       ?>                        
                            <option id="<?php echo $i->item_id; ?>_desc" value="<?php echo $i->item_id; ?>"><?php echo $i->item_description; ?></option>
                    <?php }?>
                </select>   
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="text" id="edit_fin_amount" class="form-control"  placeholder="Amount" />
            </div>
         </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='addToItems()' style='color: white' class='btn btn-xs btn-success pull-right'>Add</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-right' style="margin-right:10px; ">Cancel</button> &nbsp;&nbsp;
        </div>
     </div>
</div>


<div id="deleteFinTransaction"  style="width:35%; margin: 50px auto;"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-red" style='width:100%;'>
        <div class="panel-heading">
            <h3>
                <i class="fa fa-info-circle fa-fw"></i> Are you Sure you want to void this finance transaction, make sure you know what you are doing
                ? Please note also that you can't undo this action.
            </h3>
        </div>
        <div class="panel-body">
                <input type="hidden" id="delete_trans_id" />
                <input type="hidden" id="delete_item_id" />
                <input type="hidden" id="delete_trans_type" />
            <div style='margin:5px 0;'>
            <a href='#' id='<?php echo $g->grade_id ?>' data-dismiss='modal' onclick='deleteFinanceTransaction()' style='margin-right:10px; color: white;' class='btn btn-xs btn-danger pull-left'>Delete</a>
            <button data-dismiss='modal' class='btn btn-xs btn-warning pull-left'>Cancel</button>&nbsp;&nbsp;
            </div>

        </div>
    </div>
</div>

<div id="deleteExtraChargesModal"  style="width:35%; margin: 50px auto;"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-red" style='width:100%;'>
        <div class="panel-heading">
            <h3>
                <i class="fa fa-info-circle fa-fw"></i> Are you Sure you want to delete this extra charges, make sure you know what you are doing
                ? Please note also that you can't undo this action.
            </h3>
        </div>
        <div class="panel-body">
            <input type="hidden" id="delete_extra_id" />
            <div style='margin:5px 0;'>
            <a href='#'  data-dismiss='modal' onclick='deleteExtraCharges()' style='margin-right:10px; color: white;' class='btn btn-xs btn-danger pull-left'>Delete</a>
            <button data-dismiss='modal' class='btn btn-xs btn-warning pull-left'>Cancel</button>&nbsp;&nbsp;
            </div>

        </div>
    </div>
</div>

<div id="viewStudyLoad"  style="width:50%; margin: 50px auto;"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <div class="panel panel-default" style='width:100%;'>
         <button type="button" class="btn btn-xs pull-right btn-danger" style="margin-top:5px; margin-right:5px " data-dismiss="modal" aria-label="Close">
             <i class="fa fa-close"></i>
          </button>
        <div class="panel-heading">
                Study Load Details
        </div>
        <div id="SLBody" class="panel-body">
            
       </div>

    </div>
</div>
</div>

<div id="otherMenu">
    <ul class="dropdown-menu" role="menu">
        <li onclick="$('#deleteFinTransaction').modal('show')" class="pointer text-danger"><i class="fa fa-trash fa-fw"></i>VOID TRANSACTION</li>
    </ul>
</div>

<div id="deleteExtraCharges">
    <ul class="dropdown-menu" role="menu">
        <li onclick="$('#deleteExtraChargesModal').modal('show')" class="pointer text-danger"><i class="fa fa-trash fa-fw"></i>Delete Extra Charges</li>
    </ul>
</div>
                

<script type="text/javascript">
    
    var itemDescID = "";
    var Amount = 0;
    var itemId = "";
    window.setTimeout(function () {
        $('#inputFinItems').select2();
        $('#cashFinItems').select2();
        $('#inputDiscountedItems').select2();
        $('#transactionDate').datepicker({
            orientation: "left"
        });
        $('#inputFinItems').click(function(){
            if($(this).val()==0)
            {
                $('#addFinanceOption').modal('hide');
                $('#addDiscount').modal('show')
            }
            
        });
    }, 500);
    
    function printPermit(adm_id)
    {
        var url = "<?php echo base_url('college/finance/printPermit/') ?>"+adm_id 
        window.open(url, '_blank');
    }
    
    function viewSL(adm_id, sem)
     {

        var url = "<?php echo base_url().'college/subjectmanagement/getStudyLoad/'?>"+adm_id+'/'+sem; // the script where you handle the form input.

        $.ajax({
               type: "GET",
               url: url,
               //dataType:'json',
               data: 'csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   $('#viewStudyLoad').modal('show')
                   $('#SLBody').html(data)
               }
             });

        return false; 
     }
     
    function promisoryAction(action)
     {
         var prom_id = $('#request_id').val()
         var amount = $('#request_amount').val();
      
         
        var url = "<?php echo base_url().'college/finance/approvePromisory'?>"; // the script where you handle the form input.

        $.ajax({
               type: "POST",
               url: url,
               //dataType:'json',
               data: "amount="+amount+"&prom_id="+prom_id+"&action="+action+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   alert(data);
               }
             });

        return false; 
     }
    
    
    function requestPromisory()
     {
         var sem = $('#inputSem').val();
         var st_id = $('#st_id').val();
         var remarks = $('#promisoryRemarks').val();
         
        var url = "<?php echo base_url().'college/finance/requestPromisory'?>"; // the script where you handle the form input.

        $.ajax({
               type: "POST",
               url: url,
               //dataType:'json',
               data: "remarks="+remarks+"&st_id="+st_id+"&semester="+sem+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   alert(data);
               }
             });

        return false; 
     }
    
    function addToItems()
    {
        
        var itemAmount = $("#edit_fin_amount").val();
        Amount = parseFloat(Amount) + parseFloat(itemAmount);
        $('#itemBody').append('<tr tr_val="'+itemId+'_'+itemAmount+'" id="'+itemId+'"><td>'+itemDescID+'</td><td>'+itemAmount+'</td><td><button onclick="$(\'#'+itemId+'\').hide(), deductAmount('+itemAmount+')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button></td></tr>');
        $('#pttAmount').val(Amount)
        
        $("#edit_fin_amount").val('') ;
    }
    
    function deductAmount(itemAmount)
    {
        Amount = parseFloat(Amount) - parseFloat(itemAmount);
        $('#pttAmount').val(Amount)
    }
    
    function cash_change()
    {
        var item_amount = document.getElementById("pttAmount").value;
        if (item_amount != '') {
            var con_amount = string2number(item_amount);
        } else {
            var con_amount = 0;
        }
        var tend_amount = document.getElementById('ptAmountTendered').value;
        var con_tendered = string2number(tend_amount);

        calc_change = con_tendered - con_amount;
        tot_change = number2string(calc_change);
        document.getElementById('ptChange').value = tot_change;

    }
        
    function deleteFinanceTransaction()
    {
        var trans_id = $('#delete_trans_id').val();
        var trans_type = $('#delete_trans_type').val();
        var st_id = $('#st_id').val();
        var sem = 0;
        var sy = $('#currentYear').val();
        var item_id = $('#delete_item_id').val();
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . 'finance/deleteTransaction' ?>',
            //dataType: 'json',
            data: {
                st_id: st_id,
                sem: sem,
                school_year:sy,
                trans_id:trans_id,
                trans_type:trans_type,
                item_id: item_id,
                delete_remarks: $('#td_trans_'+trans_id).attr('delete_remarks'),
                csrf_test_name: $.cookie('csrf_cookie_name')
            },
            success: function (response) {
                alert(response);
                location.reload();
            }

        });
    }
        
    function deleteExtraCharges()
    {
        var trans_id = $('#delete_extra_id').val();
        var st_id = $('#st_id').val();
        var sy = $('#currentYear').val();
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . 'college/finance/deleteExtraCharges' ?>',
            //dataType: 'json',
            data: {
                st_id: st_id,
                school_year:sy,
                trans_id:trans_id,
                delete_remarks: $('#trExtra_'+trans_id).attr('delete_remarks'),
                csrf_test_name: $.cookie('csrf_cookie_name')
            },
            success: function (response) {
                alert(response);
                location.reload();
            }

        });
    }
    
    function saveTransaction()
    {
        var or_num = $('#refNumber').val();
        var st_id = $('#st_id').val();
        var sem = $('#currentSemester').val();
        var sy = $('#currentYear').val();
        var transDate = $('#transactionDate').val();
        
        var data = [];
        $('#itemBody tr').each(function(){
            data.push($(this).attr('tr_val'));
        });
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . 'college/finance/saveTransaction' ?>',
            //dataType: 'json',
            data: {
                items: JSON.stringify(data),
                or_num: or_num,
                st_id: st_id,
                sem: sem,
                school_year:sy,
                transDate:transDate,
                receipt: $('#inputReceipt').val(),
                t_remarks: $('#transRemark').val(),
                csrf_test_name: $.cookie('csrf_cookie_name')
            },
            success: function (response) {
                alert(response);
                $('#cashRegister').modal('hide')
            }

        });
    }
    
    function calculateItem(item_id)
    {
         itemId = item_id;
         var totalUnits = $('#totalUnits').val();
         var plan_id = $('#finPlan_id').val();
         var sem = $('#currentSemester').val();
         var sy = $('#currentYear').val();
         var st_id = $('#st_id').val();
        var url = '<?php echo base_url().'college/finance/calculateItem'?>';

        $.ajax({
               type: "POST",
               url: url,
               dataType:'json',
               data: "item_id="+item_id+'&plan_id='+plan_id+'&sem='+sem+'&school_year='+sy+'&st_id='+st_id+'&totalUnits='+totalUnits+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   $('#edit_fin_amount').attr('placeholder', data.totalPayment);
                   itemDescID = $('#'+item_id+'_desc').html();
               }
             });

        return false; 
    }
    
    
    function applyDiscount()
     {
         var sem = $('#inputDiscountedSem').val();
         var school_year = $('#inputDiscountedCSY').val()
         var discountType = $('#inputDiscountedType').val();
         var finItem = $('#inputDiscountedItems').val();
         var finAmount = $('#discount_amount').val();
         var st_id = $('#st_id').val();
         var remarks = $('#inputDiscountedRemarks').val();
         var finYear = $('#year_level').val();
         var admission_id = $('#admission_id').val();
         var plan_id = $('#finPlan_id').val();
         
        var url = "<?php echo base_url().'college/finance/applyDiscounts'?>"; // the script where you handle the form input.

        $.ajax({
               type: "POST",
               url: url,
               //dataType:'json',
               data: "finItem="+finItem+"&st_id="+st_id+"&plan_id="+plan_id+"&remarks="+remarks+"&admission_id="+admission_id+"&discount_type="+discountType+"&year_level="+finYear+"&semester="+sem+"&finAmount="+finAmount+"&school_year="+school_year+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   
                   alert('Discount Successfully Added');
                   location.reload();
                  
               }
             });

        return false; 
     }
    
    function addExtraFinanceCharges()
     {
         var sem = $('#inputSem').val();
         var school_year = $('#inputCSY').val()
         var finItem = $('#inputFinItems').val();
         var finAmount = $('#fin_amount').val();
         var st_id = $('#st_id').val();
         var admission_id = $('#admission_id').val();
         var finYear = $('#year_level').val();
         var plan_id = $('#finPlan_id').val();
         
        var url = "<?php echo base_url().'college/finance/addExtraFinanceCharges'?>"; // the script where you handle the form input.

        $.ajax({
               type: "POST",
               url: url,
               //dataType:'json',
               data: "finItem="+finItem+"&st_id="+st_id+"&plan_id="+plan_id+"&admission_id="+admission_id+"&year_level="+finYear+"&semester="+sem+"&finAmount="+finAmount+"&school_year="+school_year+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   window.setTimeout(function () {
                    $('#finChargesBody').html(data)
                },500);
                   alert('Successfully Added');
                  
               }
             });

        return false; 
     }
    
    function setExtraFinanceCharges(course_id, year_level)
    {
        $('#addFinanceOption').modal('show');
        $('#course_id').val(course_id);
        $('#year_level').val(year_level);
    }
    
        function number2string(sNumber)
    {
        //Seperates the components of the number
        var n = sNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }
    
    function string2number(svariable)
    {
        var cNumber = svariable.replace(/\,/g, '');
        cNumber = parseFloat(cNumber);
        if (isNaN(cNumber) || !cNumber) {
            cNumber = 0;
        }
        return cNumber;
    }
    </script>