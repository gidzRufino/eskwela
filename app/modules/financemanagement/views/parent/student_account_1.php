<?php
    
   $is_admin = $this->session->userdata('is_admin');
   $userid = $this->session->userdata('user_id');
   $syurl = $this->uri->segment(4);
   if ($syurl!='') {
     $sy_now = base64_decode($syurl);
   }else{
    $sy_now = $default_sy->value; // default year
   }
?>
<script type="text/javascript">
  $(document).ready(function() {
    $("#searchingStudents").select2();
    $("#search_sy").select2();
    $("#selectPlan").select2();
    var jtcharge = document.getElementById("htcharge").value;
    var jtcredit = document.getElementById("htcredit").value;
    var jtbalance = document.getElementById("htbalance").value;
    var jtbalance_due = document.getElementById("balance_due").value;
    var jact_id = document.getElementById("act_id").value;
    // document.getElementById('lact_id').innerHTML = jact_id;
    document.getElementById('ltcharge').innerHTML = jtcharge;
    document.getElementById('ltcredit').innerHTML = jtcredit;
    document.getElementById('ltbalance').innerHTML = jtbalance;
    document.getElementById('ltbalance_due').innerHTML = jtbalance_due;  
  });

</script>

<div class="clearfix" style="margin:0px;">
  <div class="panel panel-default" style="margin-top: 15px;">
    <div class="panel-heading text-center">
      <h3 class="panel-title"><b>Finance Accounts</b></h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <input type="hidden" name="sy_now" id="sy_now" value="<?php echo base64_encode($sy_now) ?>" required>
        <input type="hidden" name="sy_prev" id="sy_prev" value="<?php echo base64_encode($sy_now-1) ?>" required>
        <div class="col-md-12">
          <div class="panel panel-info" style="margin-bottom: 0px;">
         
      <?php if ($this->uri->segment(3)!=''){ ?>

        <div class="panel-body" style="height: 140px;">
          <div class="row">
            <div class="col-md-8" style=" margin-right: -23px;">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <div class="row">
                    <div class="col-md-2">

                      <?php 

                        $avatari = $searched_student->avatar; 
                        $student_full_name = ""; 
                        $student_full_name = $searched_student->firstname ." ".$searched_student->middlename ." ".$searched_student->lastname; 
                        $student_level_section = $searched_student->level ." / ".$searched_student->section;
                        $student_id = $searched_student->st_id;
                        $slevelID = $searched_student->grade_level_id;
                        $splan_desc = '';
                        $studentAccountID = '';
                        $student_planID = '';
                        if ($searched_student->firstname == " " || $searched_student->firstname == NULL){
                          $not_enrolled = true;
                        }else{
                          $not_enrolled = false;
                        }
                        foreach ($finance_plan as $finance_plan) {
                          $splan_desc = $finance_plan->plan_description;
                          $studentAccountID = $finance_plan->accounts_id;
                          $student_planID = $finance_plan->plan_id;
                        }                        
                        if ($splan_desc=='') {
                          $plan_print = 'Please Select Payment Plan';
                        }else{
                          $plan_print = $splan_desc;
                        }

                        $prev_plan = 0;
                        foreach ($check_plan as $cp) {
                          $check_sy = $cp->sy_id;
                          if($check_sy<$sy_now){
                            $prev_plan = 1;
                          }
                        }

                        if($not_enrolled == true){ ?>
                          
                          <img alt="image not available." src="<?php echo base_url()?>uploads/noImage.png" style="left: 5px; height:85px; border:solid white; z-index:5; position: relative; -webkit-box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.3); box-shadow: 0 1px 10px rgba(0, 0, 0, 0.3);"  class="img-circle"/>

                        <?php }else if($not_enrolled == false){ ?>
                          
                          <img alt="<?php echo $searched_student->lastname.", ".$searched_student->firstname;   ?> image not available." src="<?php echo base_url()?>uploads/<?php echo $avatari;?>" style="left: 5px; height:85px; border:solid white; z-index:5; position: relative; -webkit-box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.3); -moz-box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.3); box-shadow: 0 1px 10px rgba(0, 0, 0, 0.3);"  class="img-circle"/>

                        <?php } ?>

                    </div>
                    <div class="col-md-10">
                      <input type="hidden" name="st_hid" id="st_hid" value="<?php echo base64_encode($student_id) ?>" required>
                      
                      <?php 
                        if($not_enrolled == true){ ?>

                          <h3 style="color:red; margin: 10px 0 0 0px;">The account that you selected was not enrolled or roll-overed to the selected school year. Please try again.</h3>

                        <?php }else if($not_enrolled == false){ ?>

                          <h3 style="color:black; margin: 0 0 0 0px;"><b>Name: </b>&nbsp;<span style="color:#BB0000;"><b><?php echo $searched_student->lastname.", ".$searched_student->firstname;   ?></b></span></h3>
                          <h5 style="color:black; margin: 0 0 0 0px;"><b>Student ID:</b> &nbsp;<span style="color:gray;"><b><?php echo $searched_student->st_id;?></b></span> </h5>
                          <h5 style="color:black; margin: 0 0 0 0px;"><b>Grade Level:</b> &nbsp;<span style="color:gray;"><b><?php echo $searched_student->level;?></b>&nbsp;&nbsp;</span><span style="color:black; margin: 0 0 0 0px;"><b>Section:</b> &nbsp;</span><span style="color:gray;"><b><?php echo $searched_student->section;?></b></span></h5>
                      <?php
                        }
                      ?>

                    </div>                
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="panel panel-primary ">
                <div class="panel-heading">
                  <div class="row">
                    <?php if ($plan_print == 'Please Select Payment Plan') { ?>
                      
                    <div class="col-xs-12">
                    
                    <?php }else{ ?>
                    
                    <div class="col-xs-12" onclick="act_plan()">

                    <?php } ?>
                      <!-- <b>&nbsp;Payment Plan:</b><b>&nbsp;<span id="planName"><b><?php echo $splan_desc;?></b></span></b><br /> -->
                      <i class="fa fa-cube fa-fw"></i><b>&nbsp;Total Charge:</b><b>&nbsp;&nbsp;PhP &nbsp;<span id="ltcharge"></span></b><br />
                      <i class="fa fa-bar-chart fa-fw"></i><b>&nbsp;Total Credit:</b><b>&nbsp;&nbsp;PhP &nbsp;<span id="ltcredit"></span></b><br />
                      <i class="fa fa-pie-chart fa-fw"></i><b>&nbsp;Total Balance:</b><b>&nbsp;&nbsp;PhP &nbsp;<span id="ltbalance"></span></b><br />
                      <i class="fa fa-area-chart fa-fw"></i><b>&nbsp;Balance Due:</b><b>&nbsp;&nbsp;PhP &nbsp;<span id="ltbalance_due"></span></b>
                    </div>
                  </div>
                </div>
              </div>
            </div> <!-- col-lg-3 col-md-3 pull-right -->
          </div>  <!-- row -->         
        </div> <!-- panel-body -->
          </div>
        </div>
      </div> <!-- row -->
      <div class="row">
      <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
              <div class="btn-group btn-group-justified">
                <div class="btn-group">
                  <button type="button" class="btn btn-info" data-toggle="tab" data-target="#ahtab"><b>Account History</b></button>
                </div>
                <div class="btn-group">
                  <button type="button" class="btn btn-info" data-toggle="tab" data-target="#icdtab"><b>Itemized Account Details</b></button>
                </div>
              </div>
            </div>
            <div class="panel-body">
              <div class="tab-content">
                <div class="tab-pane active" id="ahtab">
                  <div class="col-md-12">
                    <table class="table table-condensed table-bordered">
                      <tr>
                        <th class="bg-primary" style="text-align:center;">Date</th>
                        <th class="bg-primary" style="text-align:center;">Control/Ref #</th>
                        <th class="bg-primary" style="text-align:center;">Description</th>
                        <th class="bg-primary" style="text-align:center;">Charges</th>
                        <th class="bg-primary" style="text-align:center;">Credit</th>
                        <th class="bg-primary" style="text-align:center;">Remarks</th>
                      </tr>

                      <?php

                        // $stud_id = $searched_student->user_id;
                        $stud_id = $searched_student->st_id;
                        $tcharge = 0;
                        $tcredit = 0;
                        $istCharge = 0;
                        $istCredit = 0;
                        $stPlanGen = 1;
                        // $stLevel_id = $searched_student->grade_level_id;
                        $stLevel_id = $searched_student->grade_id;
                        $stPlan_id = null;
                        foreach ($finance_plan as $fp) {
                          $stPlan_id = $finance_plan->plan_id;  
                        }                        
                        if ($stPlan_id!=null && $stPlan_id!=11){ // not null payment plan and full scholar code ($stPlan_id=11)
                        foreach ($initialLevel as $ist){ 
                          if($ist->level_id==$stLevel_id && $ist->plan_id==$stPlan_id && $sy_now==$ist->sy_id || $ist->level_id==$stLevel_id && $ist->plan_id==$stPlanGen && $sy_now==$ist->sy_id){ 
                           if ($ist->ch_cr==0) {
                              $istCharge = $ist->item_amount;
                              $istCredit = 0;
                              $tcharge = $tcharge + $istCharge;
                              $dis_charge = 'PhP '.number_format($istCharge,2);
                              $dis_credit = '-';
                            }elseif ($ist->ch_cr==1) {
                              $istCharge = 0;
                              $istCredit = $ist->item_amount;
                              $tcredit = $tcredit + $istCredit;
                              $dis_charge = '-';
                              $dis_credit = 'PhP '. number_format($istCredit,2);
                            }

                      ?>

                      <tr class="bg-info">
                        <td style="text-align:center;"><?php echo $ist->implement_date?></td> 
                        <td style="text-align:center;">Initial</td>        
                        <td style="text-align:center;"><?php echo $ist->item_description ?></td> 
                        <td style="text-align:center;"><?php echo $dis_charge ?></td>        
                        <td style="text-align:center;"><?php echo $dis_credit ?></td> 
                        <td style="text-align:center;"> - </td>                        
                      </tr>

                      <?php }} 

                        // foreach ($sTransaction as $st){ if($st->stud_id==$stud_id && $st->sy_id==$sy_now){
                          foreach ($sTransaction as $st){ if($st->stud_id==$stud_id){

                      ?>

                      <tr class="info">
                        <td style="text-align:center;"><?php echo $st->tdate ?></td>
                        <td style="text-align:center;"><a href="<?php echo base_url('financemanagement/pay_confirm/'.base64_encode($st->trans_id).'/'.base64_encode($sy_id).'/'.base64_encode($st->stud_id)) ?>"><?php echo $st->ref_number ?> | <?php echo $st->trans_id ?></td>
                        
                        
                        <td style="text-align:center;"><?php echo $st->item_description ?></td>

                        <?php 

                          if($st->charge_credit==1){ 
                          $scredit=$st->d_credit;
                          $tcredit=$tcredit+$scredit; 
                        
                        ?>

                        <td style="text-align:center;"> - </td>
                        <td style="text-align:center;">PhP &nbsp;<?php echo number_format($st->d_credit,2) ?></td>

                        <?php 

                          }elseif($st->charge_credit==0){ 
                          $scharge=$st->d_charge;
                          $tcharge=$tcharge+$scharge; 

                        ?>

                        <td style="text-align:center;">PhP &nbsp;<?php echo number_format($st->d_charge,2) ?></td>
                        <td style="text-align:center;"> - </td>

                        <?php }if($st->tremarks==""){ ?> 

                        <td style="text-align:center;"> - </td>
                    
                        <?php }else{ ?>
                    
                        <td style="text-align:center;"><?php echo $st->tremarks ?></td>
                    
                        <?php } ?>
                    
                      </tr>                   

                      <?php 

                        } } //  foreach ($sTransaction as $st){ if($st->stud_id==$stud_id){
                      } // if ($stPlan_id!=null && $stPlan_id!=11){ // not null payment plan and full scholar code
                        $tbalance = $tcharge - $tcredit; 

                      ?>

                      <tr>
                        <th colspan="3" style="text-align:right;"><span style="color:black; margin:3px 0;">T O T A L </span></th>
                        <th style="text-align:center;"><span style="color:#BB0000;">PhP &nbsp;<?php echo number_format($tcharge,2) ?></span></th>
                        <th style="text-align:center;"><span style="color:#BB0000;">PhP &nbsp;<?php echo number_format($tcredit,2) ?></span></th>
                        <th></th>
                      </tr>
                      <tr>
                        <th colspan="3" style="text-align:right;"><span style="color:black; margin:3px 0;">TOTAL BALANCE</span></th>
                        <th colspan="2" style="text-align:center;"><span style="color:#BB0000;">PhP &nbsp;<?php echo number_format($tbalance,2) ?></span></th>
                        <th></th>
                      </tr>
                    </table>
                    <input type="hidden" name="htcharge" id="htcharge" value="<?php echo number_format($tcharge,2) ?>" required>
                    <input type="hidden" name="htcredit" id="htcredit" value="<?php echo number_format($tcredit,2) ?>" required>
                    <input type="hidden" name="htbalance" id="htbalance" value="<?php echo number_format($tbalance,2) ?>" required>              
                    <input type="hidden" name="act_id" id="act_id" value="<?php echo base64_encode($stud_id); ?>" required>   
                  </div>                  
                </div>
                <div class="tab-pane" id="icdtab">
                  <div class="col-md-12">
                    <table class="table table-condensed table-bordered">
                      <tr>
                        <th style="text-align:center;">Item</th>
                        <th style="text-align:center;">Charge</th>
                        <th style="text-align:center;">Credit</th>
                        <th style="text-align:center;">Balance</th>
                        <th style="text-align:center;">Balance Due</th>
                        <th style="text-align:center;">Frequency</th>
                        <th style="text-align:center;">Due Date</th>
                      </tr>

                      <?php 

                        $tbalance_due = 0;
                        $ar_itemChoice = array();
                        $ar_balance = array();
                        $ar_index = 0;
                        $ar = 0;
                        $cID = 0;
                        $stPlanGen = 1;
                        // print_r($initialLevel);

                        if ($stPlan_id!=null && $stPlan_id!=11 ){ // not null payment plan and full scholar code

                        foreach ($initialLevel as $iL) {
                          if($iL->level_id==$stLevel_id && $iL->plan_id==$stPlan_id && $sy_now==$iL->sy_id || $iL->level_id==$stLevel_id && $iL->plan_id==$stPlanGen && $sy_now==$iL->sy_id){
                          $cID += 1; 

                      ?>

                      <tr class="info">

                      <?php 

                        $ar += 1;
                        $ar_index = $ar - 1;
                        $ar_itemChoice[$ar_index] = $iL->item_description;
                        $init_item_id = $iL->item_id;
                        $stCharge = 0;
                        $stCredit = 0;
                        $monthNow = date('n');
                        foreach ($sTransaction as $sBal) {
                          if($sBal->stud_id==$stud_id  && $sBal->sy_id==$sy_now){ 
                            if($sBal->item_id==$init_item_id){
                              if ($sBal->charge_credit==0) {
                                $ssCharge = $sBal->d_charge; // if there is an existing amount
                                $stCharge = $stCharge + $ssCharge;
                              }elseif($sBal->charge_credit==1) {
                                $ssCredit = $sBal->d_credit;
                                $stCredit = $stCredit + $ssCredit;
                        } } } }  
                        
                        $tAmount = $iL->item_amount; 
                        $stBalance = $tAmount - $stCredit; 
                        $tfreq = $iL->schedule_id;

                        if($tfreq==1){
                          $tmBalance = $tAmount/10;
                        }elseif($tfreq==2){
                          $tmBalance = $tAmount/4;
                        }elseif($tfreq==3){
                          $tmBalance = $tAmount/2;
                        }elseif($tfreq==4){
                          $tmBalance = $tAmount;
                        }elseif($tfreq==5){
                          $tmBalance = $tAmount/2;
                        }

                      ?>

                        <td style="text-align:center;" id="item<?php echo $cID ?>"><?php echo $iL->item_description ?></td>

                      <?php
                        switch ($monthNow) {
                          case '1':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'January';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'February';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '2':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'February';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'February';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '3':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'March';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'February';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '4':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'April';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'February';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '5':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'May';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'February';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '6':
                            if($tfreq==1){  
                              $balance_due = $tAmount -($tmBalance*9) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*3) - $stCredit;
                              $due_date = 'August';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'July';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '7':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*8) - $stCredit;
                              $due_date = 'July';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*3) - $stCredit;
                              $due_date = 'August';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'July';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '8':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*7) - $stCredit;
                              $due_date = 'August';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*3) - $stCredit;
                              $due_date = 'August';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'July';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '9':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*6) - $stCredit;
                              $due_date = 'September';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'October';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'July';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '10':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*5) - $stCredit;
                              $due_date = 'October';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'October';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'July';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '11':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*4) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'December';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          case '12':
                            if($tfreq==1){
                              $balance_due = $tAmount -($tmBalance*3) - $stCredit;
                              $due_date = 'December';
                            }elseif($tfreq==2){
                              $balance_due = $tAmount -($tmBalance*1) - $stCredit;
                              $due_date = 'December';
                            }elseif($tfreq==3){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'November';
                            }elseif($tfreq==4){
                              $balance_due = $tAmount -($tmBalance*0) - $stCredit;
                              $due_date = 'June';
                            }elseif($tfreq==5){
                              $balance_due = $tAmount -($tmBalance*2) - $stCredit;
                              $due_date = 'April';
                            }
                            break;
                          default:
                            $balance_due = $stBalance;
                            break;
                        }
                        $tbalance_due = $tbalance_due + $balance_due;
                        $ar_balance[$ar_index] = $balance_due;
                      
                      ?>

                      <td style="text-align:center;"><?php echo number_format($tAmount,2)?></td>
                      <td style="text-align:center;"><?php echo number_format($stCredit,2) ?></td>
                      <td style="text-align:center;"><?php echo number_format($stBalance,2) ?></td>
                      <td style="text-align:center;" id="bDue<?php echo $cID ?>"><?php echo number_format($balance_due,2) ?></td>
                      <td style="text-align:center;"><?php echo $iL->schedule_description ?></td>
                      <td style="text-align:center;"><?php echo $due_date." ". date('Y') ?></td>
                    </tr>

                  <?php }}} 
                   // print_r($show_extra) ;                 
                   foreach ($show_extra as $i_soa) {
                    if ($i_soa->total_charge!=0){
                      $ar += 1;                      $cID += 1;
                      $ar_index = $ar - 1;
                      $ar_itemChoice[$ar_index] = $i_soa->item_description;

                  ?>
                  
                  <tr class="info">
                    <td style="text-align:center;" id="item<?php echo $cID ?>"><?php echo $i_soa->item_description ?></td>
                    <td style="text-align:center;" ><?php echo number_format($i_soa->total_charge, 2) ?></td>
                    <td style="text-align:center;" ><?php echo number_format($i_soa->total_credit, 2) ?></td>

                  <?php 

                    $tot_credit = $i_soa->total_credit;
                    $tot_charge = $i_soa->total_charge;
                    $total_balance = $tot_charge-$tot_credit;

                  ?>

                    <td style="text-align:center;" ><?php echo number_format($total_balance, 2) ?></td>
                    <td style="text-align:center;" id="bDue<?php echo $cID ?>"><?php echo number_format($total_balance, 2) ?></td>
                    <td style="text-align:center;">On Post</td>
                    <td style="text-align:center;">A S A P</td>
                  </tr>


                  <?php 

                    $tbalance_due = $tbalance_due + $total_balance;

                  } } ?>


                    </table>
                    <input type="hidden" name="balance_due" id="balance_due" value="<?php echo number_format($tbalance_due,2) ?>" required>
                    <input type="hidden" name="pointID" id="pointID" value="<?php echo $cID ?>" required>
                  </div>
                </div>
              </div>
            </div>          
        </div>
      </div> 
    </div>
  </div>

  <?php } ?>  <!-- $this->uri->segment(3)!='' -->


<!-- Modals -->


<!-- Modals -->
  <div class="panel-footer">
      
  </div>
  </div>
</div>

<?php if ($this->uri->segment(3)!=''){ ?>

<script type="text/javascript">
  $(document).ready(function() {
    $("#searchingStudents").select2();
    $("#search_sy").select2();
    $("#selectPlan").select2();
    var jtcharge = document.getElementById("htcharge").value;
    var jtcredit = document.getElementById("htcredit").value;
    var jtbalance = document.getElementById("htbalance").value;
    var jtbalance_due = document.getElementById("balance_due").value;
    var jact_id = document.getElementById("act_id").value;
    // document.getElementById('lact_id').innerHTML = jact_id;
    document.getElementById('ltcharge').innerHTML = jtcharge;
    document.getElementById('ltcredit').innerHTML = jtcredit;
    document.getElementById('ltbalance').innerHTML = jtbalance;
    document.getElementById('ltbalance_due').innerHTML = jtbalance_due;  
  });

</script>

<?php }else{ ?>

<script type="text/javascript">
  $(document).ready(function() {
    $("#searchingStudents").select2();
    $("#search_sy").select2();
    $("#selectPlan").select2();
  });

</script>
<?php } ?>

