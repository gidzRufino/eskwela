<?php
 switch ($sem):
     case 1:
         $semester = 'First';
     break;    
     case 2:
         $semester = 'Second';
     break;    
     case 3:
         $semester = 'Summer';
     break;    
     default :
         $semester = 'First';
     break;    
 endswitch;
 
 ?>
<div class="col-lg-12">
    <h4 class="text-center">COLLEGE GRADE SHEET</h4>
</div>
<div class="col-lg-12">
    <div class="col-lg-6">
        <dl class="dl-horizontal">
            <dt>
                Instructor : 
            </dt>
            <dd>
                <?php 
                
                $faculty = Modules::run('hr/getEmployeeName', $faculty_id);
                echo $faculty->firstname.' '.$faculty->lastname;
                        
                        ?>
            </dd>
        </dl>
        <dl class="dl-horizontal">
            <dt>
                Schedule : 
            </dt>
            <dd>
                <?php $scheds = Modules::run('college/schedule/getSchedulePerSection', $section_id); 
                    $sched = json_decode($scheds);
                    echo ($sched->count > 0 ? ' [ '.$sched->day.' ] '.$sched->time:'TBA');
                ?>
            </dd>
        </dl>
    </div>
    <div class="col-lg-6 pull-right">
        <dl class="dl-horizontal">
            <dt>
                School Year : 
            </dt>
            <dd>
                <?php 
                $next = $students->row()->school_year +1;
                echo $students->row()->school_year.' - '.$next; 
                        ?>
            </dd>
        </dl>
        <dl class="dl-horizontal">
            <dt>
                Semester : 
            </dt>
            <dd>
                <?php 
                    echo $semester;
                        ?>
            </dd>
        </dl>
    </div>
</div>

<div class="col-lg-12">
    <hr />
</div>

<div class="col-lg-12">
    <table class='table table-hover table-bordered'>
        <tr>
            <th style='vertical-align: middle;' rowspan="2">#</th>
            <th style='vertical-align: middle;' rowspan="2">LAST NAME</th>
            <th style='vertical-align: middle;' rowspan="2">FIRST NAME</th>
            <th style='vertical-align: middle;' rowspan="2">COURSE</th>
            <?php foreach($term->result() as $t): ?>
                <th class="text-center" colspan="3"><?php echo $t->gst_term; ?></th>
            <?php endforeach; ?>
        </tr>
        <tr>
            <?php foreach($term->result() as $t): 
                    foreach($assessCategory->result() as $ac): 
                ?>
                    <th class="text-center"><?php echo $ac->gsc_category ?></th>
                
            <?php 
                    endforeach;?>
                 
                    <th class="text-center">Grade</th>   
            <?php        
                endforeach; ?>
                    <th>Final Grade</th>
        </tr>
        <?php
            $i = 1;   
            foreach ($students->result() as $s):
        ?>
                <tr>
                    <th><?php echo $i++?></th>
                    <th><?php echo $s->lastname; ?></th>
                    <th><?php echo $s->firstname; ?></th>
                    <th><?php echo $s->short_code .' - '.$s->year_level; ?></th>
                    
                    <?php foreach($term->result() as $t): 
                            foreach($assessCategory->result() as $ac): 
                                $grade = Modules::run('college/gradingsystem/getRecordedGrade', $s->st_id, $ac->gsc_id, $t->gst_id, $sem, $school_year, $subject_id);
                                //print_r($grade->row());
                        ?>
                            <td id="<?php echo $i.'_'.$ac->gsc_id.'_'.$t->gst_id ?>" 
                                st_id="<?php echo $s->st_id ?>" 
                                tdn="<?php echo $ac->gsc_id.'_'.$t->gst_id?>"
                                term="<?php echo $t->gst_id ?>"
                                ondblclick="editTable(this.id)" 
                                class="text-center"
                                style="width:5%;"
                                ><?php echo $grade->row()->grade ?></td>

                    <?php 
                            $partialGrade += $grade->row()->grade * $grade->row()->subject_weight;
                            endforeach;?>

                            <td class="text-center"
                                data-toggle="context" 
                                data-target="#<?php echo ($grade->row()->is_final==0?'validateGrade':'inValidateGrade'); ?>"
                                onmouseover="$('#st_id').val('<?php echo $s->st_id ?>'), $('#term_id').val('<?php echo $t->gst_id ?>'), $('#year_level').val('<?php echo $s->year_level ?>'), $('#course_id').val('<?php echo $s->course_id ?>')"
                                ><strong><?php echo ($partialGrade!=0?number_format($partialGrade,2,'.',','):'') ?></strong></td>   
                    <?php
                        $finalGrade += $partialGrade * $grade->row()->term_weight;
                        unset($partialGrade);
                        endforeach; ?>
                            <td class="text-center text-strong"
                            ><?php echo ($finalGrade!=0?number_format($finalGrade,2,'.',',').' | ':'') ?><strong id="<?php echo $s->st_id ?>_fg"><?php echo Modules::run('college/gradingsystem/getTransmutation', number_format($finalGrade,2,'.',',')) ?></strong></td>
                </tr>
        <?php 
        
            unset($finalGrade);
            endforeach;
        ?>
    </table>
</div>

