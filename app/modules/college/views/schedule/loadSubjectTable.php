<div class="col-lg-12">
    <table class="table table-stripped">
        <tr>
            <th>Subject</th>
            <th>Section Code</th>
            <th>Descriptive Title</th>
            <th>Schedule</th>
            <th>Semester</th>
            <th>Action</th>
        </tr>
        <?php
        foreach ($subjects as $s):
            if($s->sec_sem==$semester):
                $students = Modules::run('college/subjectmanagement/getStudentsPerSection', $s->sec_id);
            ?>
            <tr>
                <td><?php echo $s->sub_code; ?></td>
                <td><?php echo $s->section  ?></td>
                <td><?php echo $s->s_desc_title; ?></td>
                <td class="text-center">
                    <?php $scheds = Modules::run('college/schedule/getSchedulePerSection', $s->sec_id, $semester, $school_year); 
                        $sched = json_decode($scheds);
                        echo ($sched->count > 0 ? $sched->time.' [ '.$sched->day.' ]':'TBA');
                    ?>
                </td>
                <td class="text-center"><?php echo ($semester==1?'1st':($semester==2?'2nd':'Summer')) ?></td>
                <td class="text-center">
                    <button class="btn btn-xs btn-danger" onclick="document.location='<?php echo base_url('college/schedule/getSchedulePerSubject/').$s->s_id.'/' ?>'+$('#inputSem').val()+'/'+$('#inputSY').val()"><i class="fa fa-plus"></i></button>
                </td>

            </tr>
            <?php    
            endif;
        endforeach;
            
        ?>
    </table>
</div>