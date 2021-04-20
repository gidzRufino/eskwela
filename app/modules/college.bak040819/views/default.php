<h1 class="page-header" style="text-align: center">COLLEGE INFORMATION MANAGEMENT SYSTEM</h1>
<div class="col-lg-12">
    <div class="well" style="margin-bottom: 0px">
        <div class="row">
            <?php 
                foreach ($menus as $m):
                    $menu = Modules::run('college/getMenu', $m);
            ?>
                    <div class="col-lg-3" style="margin-top: 20px;">
                        <button onclick="document.location='<?php echo base_url($menu->cmenu_link) ?>'" type="button" class="btn <?php echo $menu->cmenu_btn_type ?>" style="text-align:center; width:100%; height:130px;">
                                <div class="col-md-12">
                                        <i class="fa <?php echo $menu->cmenu_icon ?> fa-4x"></i><br/>
                                        <h4><?php echo $menu->cmenu_name ?></h4>
                                </div>
                        </button>
                    </div>
            <?php        
                endforeach;
                $assignedSubject = Modules::run('college/subjectmanagement/getAssignedSubjectRaw', $this->session->userdata('employee_id'));
                
                if(!empty($assignedSubject)):
            ?>
            
                <div class="col-lg-3" style="margin-top: 20px;">
                    <button onclick="document.location='<?php echo base_url('college/gradingsystem') ?>'" type="button" class="btn btn-primary" style="text-align:center; width:100%; height:130px;">
                            <div class="col-md-12">
                                    <i class="fa fa-calculator fa-4x"></i><br/>
                                    <h4><?php echo 'Grading System' ?></h4>
                            </div>
                    </button>
                </div>
            <?php
                endif;
            ?>    
            
            <div class="col-lg-12">
                <h4 class="page-header" style="text-align: center">School Calendar</h4>
            <?php
                $dbTime = Modules::run('main/refreshIdGeneration');
                foreach ($dbTime->result() as $dbT):
                    if(strtotime($dbT->timestamp)):
                        
                    endif;
                endforeach;
                echo Modules::run('calendar/getCalWidget', date('Y'), date('m'));
            ?>
            </div>
        </div>
    </div> <!-- <div class="well"> -->
</div>

