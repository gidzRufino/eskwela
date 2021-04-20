


<?php
$children = explode(',', $child_links);
switch (count($children)):
    case 1:
        $width = '25%';
        $col = 'col-lg-12';
        break;
    case 2:
        $width = '50%';
        $col = 'col-lg-6';
        break;
    case 3:
        $width = '75%';
        $col = 'col-lg-4';
        break;
    default :
        $width = '100%';
        $col = 'col-lg-3';
        break;
endswitch;
?>
<div class="col-lg-12">
        <?php
        foreach ($children as $child):
            $isEnrolled = Modules::run('registrar/isEnrolled', $child, $this->session->school_year);
            if (!$isEnrolled):
                $school_year = $this->session->userdata('school_year') - 1;
            else:
                $school_year = $this->session->userdata('school_year');
            endif;

            $childDepartment = Modules::run('registrar/getStudentDepartment', $child, $school_year);

            if ($childDepartment == 'basic'):
                $student = Modules::run('registrar/getSingleStudent', $child, $school_year);
                $adviser = Modules::run('academic/getAdvisory', NULL, $school_year, $student->section_id);
                ?>
        <div class="<?php echo $col; ?> float-left">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user ">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username"><?php echo strtoupper($student->firstname . ' ' . $student->lastname) ?></h3>
                    <h5 class="widget-user-desc"><?php echo $student->level ?> - <?php echo $student->section ?></h5>
                </div>
                <div class="widget-user-image">
                    <img class="img-circle" src="../dist/img/user1-128x128.jpg" alt="User Avatar">
                </div>
                <div class="card-body">
                    <?php
                    
                        $plan = Modules::run('finance/getPlanByCourse', $student->grade_id, 0,$student->st_type, $student->school_year);

                        $charges = Modules::run('finance/financeChargesByPlan',0, $student->school_year, 0, $plan->fin_plan_id, $student->semester );
                        $addCharge = Modules::run('college/finance/financeChargesByPlan',NULL, $student->school_year, $student->semester  );
         
                        $financeAccount = Modules::run('finance/getFinanceAccount', $student->st_id);
                    ?>
                    <div class='panel panel-warning'>
                        <div class='panel-heading clearfix'>
                            <h5 class="pull-left">Finance Details</h5>
                        </div>
                        <div class='panel-body'>
                            <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:10%;">#</th>
                                            <th style="width:50%;">Particulars</th>
                                            <th style="width:40%; text-align: right;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="finChargesBody">
                                    <?php
                                    $i=1;
                                    $total=0;
                                    $amount=0;
                                    foreach ($charges as $c):
                                        $next = $c->school_year + 1;
                                        if($student->grade_id==12 || $student->grade_id==13):
                                            if($student->st_type !=2):
                                            ?>
                                            <tr id="tr_<?php echo $c->charge_id ?>">
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo $c->item_description ?></td>
                                                <td id="td_<?php echo $c->charge_id ?>" class="text-right"><?php echo number_format($c->amount, 2, '.',',') ?></td>
                                            </tr>

                                            <?php
                                                $total += $c->amount;
                                            else:
                                                if($c->item_description!='Tuition Fee' && $c->item_description!='Misc Fee'):    
                                                 ?>

                                                    <tr id="tr_<?php echo $c->charge_id ?>">
                                                        <td><?php echo $i++;?></td>
                                                        <td><?php echo $c->item_description ?></td>
                                                        <td id="td_<?php echo $c->charge_id ?>" class="text-right"><?php echo number_format($c->amount, 2, '.',',') ?></td>
                                                    </tr>
                                                <?php
                                                $total += $c->amount;
                                                endif;
                                            endif;
                                        else:
                                            ?>
                                            <tr id="tr_<?php echo $c->charge_id ?>">
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo $c->item_description ?></td>
                                                <td id="td_<?php echo $c->charge_id ?>" class="text-right"><?php echo number_format($c->amount, 2, '.',',') ?></td>
                                            </tr>

                                            <?php

                                                $total += $c->amount;
                                        endif;    
                                   endforeach;
                                        $totalExtra = 0;
                                        $extraCharges = Modules::run('finance/getExtraFinanceCharges',$user_id, $student->semester, $student->school_year);
                                        if($extraCharges->num_rows()>0):
                                            foreach ($extraCharges->result() as $ec):
                                            ?>
                                                <tr data-toggle="context" data-target="#extraMenu" onmouseover="$('#delete_trans_id').val('<?php echo $ec->extra_id ?>')" style="background: #0ff !important;" id="trExtra_<?php echo $ec->extra_id ?>"
                                                    delete_remarks="Extra Charges for <?php echo $ec->item_description?> voided: [Amount :<?php echo number_format($ec->extra_amount, 2, '.',',') ?>]">
                                                    <td style="background: #0ff !important;"><?php echo $i++;?></td>
                                                    <td style="background: #0ff !important;"><?php echo $ec->item_description?></td>
                                                    <td style="background: #0ff !important;" id="td_<?php echo $ec->extra_id ?>" class="text-right"><?php echo number_format($ec->extra_amount, 2, '.',',') ?></td>
                                                </tr>
                                            <?php
                                            $totalExtra += $ec->extra_amount;
                                            endforeach;
                                            $total = $total + $totalExtra;
                                        endif;

                                        if($total!=0):
                                    ?>
                                    <tr style="background:yellow;">
                                        <th>TOTAL</th>
                                        <th></th>
                                        <th class="text-right"><?php echo number_format($total, 2, '.',',') ?></th>
                                        <th></th>
                                    </tr>
                                    <?php endif; ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
<!--                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">3,200</h5>
                                <span class="description-text">TOTAL CHARGES</span>
                            </div>
                             /.description-block 
                        </div>
                         /.col 
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <h5 class="description-header">13,000</h5>
                                <span class="description-text">TOTAL PAYMENT</span>
                            </div>
                             /.description-block 
                        </div>
                         /.col 
                        <div class="col-sm-4">
                            <div class="description-block">
                                <h5 class="description-header">35</h5>
                                <span class="description-text">TOTAL BALANCE</span>
                            </div>
                             /.description-block 
                        </div>
                         /.col 
                    </div>
                     /.row 
                </div>-->
            </div>
            <!-- /.widget-user -->
        </div>
        <?php
            else:
                $student = Modules::run('college/getSingleStudent', $child, $school_year);
                // print_r($student);

                switch ($student->year_level):
                    case 1:
                        $year_level = "First Year";
                        break;
                    case 2:
                        $year_level = "Second Year";
                        break;
                    case 3:
                        $year_level = "Third Year";
                        break;
                    case 4:
                        $year_level = "Fourth Year";
                        break;
                    case 5:
                        $year_level = "Fifth Year";
                        break;
                endswitch;
                ?>
                <div class="<?php echo $col ?> pointer" onclick="$('#collegeDetails').modal('show')">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <img class="img-circle img-responsive" style="width:100%; border:5px solid #fff" src="<?php echo ($student->avatar == "" ? base_url() . 'uploads/noImage.png' : base_url() . 'uploads/' . $student->avatar) ?>" />
                        </div>
                        <div class="panel-footer">
                            <h5 class="text-center"><?php echo strtoupper($student->firstname . ' ' . $student->lastname) ?></h5>
                            <h6 class="text-danger text-center"><?php echo $child ?></h6><hr style="margin:3px 0;" />
                            <h6 class="text-center"><?php echo $student->course ?></h6>
                            <h6 class="text-center"><?php echo $year_level ?></h6>
                        </div>
                    </div>
                </div>
            <?php
            endif;
        endforeach;
        ?>
</div>

<div id="financeDetails" style="width:90%; margin: 15px auto 0;" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-danger">
        <div class="panel-heading clearfix">
            <h4 class="no-margin col-lg-4" id="name"></h4>
            <button data-dismiss="modal" class="btn btn-xs btn-danger pull-right"><i class="fa fa-close"></i></button>
        </div>
        <div id="financeData" class="panel-body">


        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        // $('#inputSY').select2();

    });


    function viewDetails(id)
    {
        var url = "<?php echo base_url() . 'finance/loadAccountDetails/' ?>" + id

        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'id=' + id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                $('#financeData').html(data)
            }
        });

        return false;
    }

</script>