<?php 
    $acadSubjects = Modules::run('college/gradingsystem/getRecordedGrade', $details->st_id, NULL, NULL, NULL,NULL,NULL,1);
?>
<div class="col-lg-12 page-header">
    <table class="table table-hover">
        <tr>
            <th  style="width:10%;" >
                Subject Code
            </th>
            <th style="width:30%;" >Subject Description</th>
            <?php foreach($term->result() as $t): 
                    ?>
                <th style="width:10%;"  class="text-center"><?php echo $t->gst_term; ?></th>
            <?php endforeach; ?>
                <th>Final Grade</th>
                <th style="width:10%;" class="text-center" >Semester</th>
                <th style="width:10%;" class="text-center" >School Year</th>
        </tr>
        <?php
            foreach ($acadSubjects->result() as $as):
                $subject = Modules::run('college/subjectmanagement/getSubjectPerId', $as->sub_id);
                switch ($as->semester):
                    case 1:
                        $semester = 'First';
                    break;
                    case 2:
                        $semester = 'Second';
                    break;
                    case 3:
                        $semester = 'Summer';
                    break;
                endswitch;
        ?>        
        <tr>        
            <td style="width:10%;" ><?php echo $subject->sub_code; ?></td>
            <td style="width:30%;" ><?php echo $subject->s_desc_title; ?></td>
         <?php  
         foreach($term->result() as $t): 
            foreach($category->result() as $ac): 
                $grade = Modules::run('college/gradingsystem/getRecordedGrade', $details->st_id, $ac->gsc_id, $t->gst_id, NULL, NULL, $as->sub_id, 1);
                $partialGrade += $grade->row()->grade * $grade->row()->subject_weight;
            endforeach;
         ?>
            <td style="width:10%;" class="text-center"><strong><?php echo ($partialGrade!=0? Modules::run('college/gradingsystem/getTransmutation', number_format($partialGrade,2,'.',',')):'') ?></strong></td>

            <?php 
            $finalGrade += $partialGrade * $grade->row()->term_weight;
            if($t->gst_id==4):
                $grade_value = $grade->row()->grade;
            endif;
            unset($partialGrade);
         endforeach;
        ?>
            <td class="text-center"><?php echo ($grade_value!=0?number_format($finalGrade,2,'.',',').' | '.Modules::run('college/gradingsystem/getTransmutation', number_format($finalGrade,2,'.',',')):'') ?><strong></td>
            <td style="width:10%;"  class="text-center"><?php echo $semester ?></td>    
            <td style="width:10%;"  class="text-center"><?php echo $as->school_year ?></td>    
        </tr>
        <?php
        endforeach; ?>
    </table>
</div>