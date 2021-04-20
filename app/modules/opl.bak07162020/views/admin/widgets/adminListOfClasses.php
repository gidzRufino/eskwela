<div class="card">
    <div class="card-header text-center">
        <span>List of Section</span>
        <!--<span><i class="fa fa-search float-right fa-xs mt-2" style="cursor: pointer;" onclick="readySearch(this)"></i></span>-->
    </div>
    <div class="card-body" id="subjectListMain">
        <div class="list-group">
            <?php
                $sections = Modules::run('registrar/getAllSection');
                
                foreach ($sections->result() as $section):
                    $totalStudents = Modules::run('registrar/getNumberOfStudentPerSection',$section->section_id, $this->session->school_year, 1);
                ?>
                <a class="list-group-item list-group-item-action search-list" href="<?php echo base_url();?>opl/sectionDashboardForAdmin/<?php echo $section->grade_id;?>/<?php echo $section->section_id;?>/<?php echo $this->session->school_year ?>">
                        <span style="color:#BB0000;">
                            <?php echo $section->level;?>&nbsp;[&nbsp;<?php echo $section->section; ?>&nbsp;]
                        </span>
                        <span class="badge badge-danger right float-right"><?php echo $totalStudents ?></span>  
                    </a>  
            <?php
                endforeach;
            ?>
            
        </div>
    </div>
</div>