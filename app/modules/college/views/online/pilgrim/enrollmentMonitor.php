<div class="col-lg-12 clearboth" style="background: #ccc;">
    <div class="col-lg-8 col-xs-12" style="margin:10px auto; float: none !important" tabindex="-1" aria-hidden="true">
        <div class="modal-header clearfix" style="background:#fff;border-radius:15px 15px 0 0; ">
            <?php if($this->eskwela->getSet()->level_catered == 4): ?>
                <div class="col-lg-1 col-xs-2 no-padding pointer" onclick="document.location='<?php echo base_url('college') ?>'">
            <?php else: ?>   
                <div class="col-lg-1 col-xs-2 no-padding pointer" onclick="document.location='<?php echo base_url() ?>'">
            <?php endif; ?>
                <img src="<?php echo base_url() . 'images/forms/' . $settings->set_logo ?>"  style="width:50px; background: white; margin:0 auto;"/>
                
            </div>
            <div class="col-lg-5 col-xs-10">
                <h1 class="text-left no-margin"style="font-size:20px; color:black;"><?php echo $settings->set_school_name ?></h1>
                <h6 class="text-left"style="font-size:10px; color:black;"><?php echo $settings->set_school_address ?></h6>
            </div>

            <h4 class="text-right" style="color:black;">Welcome <?php echo $this->session->name . '!'; ?></h4>
            <h5 class="text-right" style="color:black;">S.Y. <?php echo $school_year.' - '.($school_year+1) ?><?php echo ($semester==1?' - First Semester':($semester==2?' - Second Semester':($semester==3?' - Summer':''))) ?></h5>
        </div>
        <div style="background: #fff; border-radius:0 0 15px 15px ; padding: 5px 10px 10px; overflow-y: scroll; min-height: 100vh;">  
            
            <div class="col-lg-12 col-xs-12" style="margin-bottom:10px;">
                <span>Student's Legend :</span>
                &nbsp; &nbsp; <button onclick="getEnrollmentDetails('0','<?php echo $semester ?>','<?php echo $school_year ?>','Students for Evaluation')" class="btn btn-info btn-xs"><i class="fa fa-user fa-fw"></i> For Evaluation - <?php echo Modules::run('college/enrollment/getNumStudentsPerStatus',$semester, $school_year,'0') ?></button>
            &nbsp; &nbsp; <button onclick="getEnrollmentDetails('3','<?php echo $semester ?>','<?php echo $school_year ?>','Students for Payment')" class="btn btn-warning btn-xs"><i class="fa fa-money fa-fw"></i> For Payment - <?php echo Modules::run('college/enrollment/getNumStudentsPerStatus',$semester, $school_year, 3) ?></button>
            &nbsp; &nbsp; <button onclick="getEnrollmentDetails('4','<?php echo $semester ?>','<?php echo $school_year ?>','Students for Payment Evaluation')" class="btn btn-danger btn-xs"><i class="fa fa-cc fa-fw"></i> For Payment Evaluation - <?php echo Modules::run('college/enrollment/getNumStudentsPerStatus', $semester, $school_year,4) ?></button>
            &nbsp; &nbsp; <button onclick="getEnrollmentDetails('5','<?php echo $semester ?>','<?php echo $school_year ?>','Students for Payment Confirmation')" class="btn btn-success btn-xs"><i class="fa fa-cc fa-fw"></i> For Payment Confirmation - <?php echo Modules::run('college/enrollment/getNumStudentsPerStatus',$semester, $school_year, 5) ?></button>
            &nbsp; &nbsp; <button onclick="getEnrollmentDetails('1','<?php echo $semester ?>','<?php echo $school_year ?>','Officially Enrolled Students')" class="btn btn-primary btn-sm"><i class="fa fa-user fa-fw"></i> Officially Enrolled - <?php echo Modules::run('college/enrollment/getNumStudentsPerStatus',$semester, $school_year, 1) ?></button>
            </div><br />
            <div class="clearfix row">
                <div class="col-lg-8 col-xs-12">
                    <div class="col-lg-12">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                Enrollment Details
                            </div>
                            <div class="panel-body" id="studentDetails" style='min-height: 50vh;'>
                                <?php if($st_id != NULL): 
                                        echo $student;
                                    endif; 
                                ?>
                            </div>

                        </div>
                    </div>
                    
                </div>
                <div class="col-lg-4 col-xs-12 ">    
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Total Students Enrolled Online <strong><span class="pull-right"><?php echo Modules::run('college/enrollment/getNumStudentsPerStatus',$semester, $school_year) ?></span></strong>
                        </div>
                        <div class="panel-body no-padding" style='min-height: 80vh; overflow-y: scroll;'>
                            <div class="list-group">
                                <?php 
                                    foreach($students as $s): 
                                        switch($s->status):
                                            case 0:
                                                $status = 'FOR EVALUATION';
                                            break;
                                            case 1:
                                            break;    
                                            case 3:
                                                $status = 'FOR PAYMENT';
                                            break;    
                                            case 4:
                                                $status = 'FOR PAYMENT EVALUATION';
                                            break;    
                                            case 5:
                                                $status = 'FOR PAYMENT CONFIRMATION';
                                            break;    
                                        endswitch;
                                        
                                ?>
                                <a href="<?php echo base_url('college/enrollment/monitor').'/'.$s->semester.'/'.$s->school_year.'/'.base64_encode($s->st_id).'/'.(isset($s->course)?'':1) ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                                  <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?php echo strtoupper($s->firstname.' '.$s->lastname) ?></h5>
                                  </div>
                                    <p class="mb-1"><?php echo ucwords(strtolower((isset($s->course)?$s->course:$s->level))) ?></p>
                                    <small class="text-danger">Enrollment Status: <b><?php echo $status ?></b></small>
                                </a>
                                <?php endforeach; ?>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>  <!--end of modal-body --> 
        </div>
    </div>   
</div>

<div id="enrollDetails" class="modal fade col-lg-4 col-xs-12" style="margin:30px auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-info">
        <div class="panel-heading clearfix">
            <h4 id="entitle" class="no-margin col-lg-6">hey!</h4>
            <button class="pull-right btn btn-xs btn-danger" data-dismiss="modal">x</button>
        </div>
        <div style="height:60vh; overflow-y: scroll; cursor: pointer" class="panel-body" id="enrollBody">
            
        </div>
    </div>
</div>    
   
<input type="hidden" value="<?php echo base_url() ?>" id="base" />
<script type="text/javascript">

    $(document).ready(function () {
        //$('#enrollDetails').modal('show');
    });
    
    function getEnrollmentDetails(status, semester, school_year, title)
    {
        var base = $('#base').val();
        var url = base+'college/enrollment/listOfStudentsEnrolled/'+semester+'/'+school_year+'/'+status;
        
        $.ajax({
            type: 'GET',
            url: url,
            data:'',
            success: function (data)
            {
                $('#entitle').html(title);
                $('#enrollDetails').modal('show');
                $('#enrollBody').html(data);
            }
        });

    }

</script>