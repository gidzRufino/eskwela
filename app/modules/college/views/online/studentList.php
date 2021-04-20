<table class="table table-striped">
    <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
    </tr>  
    <tbody>
        <?php 
            $cnt=1;
            if($semester!=0):
                foreach($students->result() as $s):
                $admissionRem = Modules::run('reports/getStudentStat', $s->st_id);
                //if(Modules::run('college/enrollment/hasLoadedSubject', $s->admission_id, $s->school_year)):
                if (!$admissionRem):
                 ?>
                <tr onclick="document.location = '<?php echo base_url('college/enrollment/monitor').'/'.$s->semester.'/'.$s->school_year.'/'.base64_encode($s->st_id) ?>'">
                    <td><?php echo $cnt++ ?></td>
                    <td><?php echo strtoupper($s->lastname.', '.$s->firstname) ?></td>
                    <td><?php echo strtoupper($s->short_code)  ?></td>
                    <!--<td><?php echo $s->admission_id  ?></td>-->
                </tr>
            <?php 
                   // endif;
                   endif;
                endforeach; 
            endif;
            ?>  
        <?php 
            foreach($basicStudents->result() as $s):
            $admissionRem = Modules::run('reports/getStudentStat', $s->st_id);
            if (!$admissionRem):
            ?>
            <tr onclick="document.location = '<?php echo base_url('college/enrollment/monitor').'/'.$s->semester.'/'.$s->school_year.'/'.base64_encode($s->st_id) ?>/1'">
                <td><?php echo $cnt++ ?></td>
                <td><?php echo strtoupper($s->lastname.', '.$s->firstname) ?></td>
                <td><?php echo strtoupper($s->level)  ?></td>
                <!--<td><?php echo $s->admission_id  ?></td>-->
            </tr>
        <?php 
        endif;
        endforeach; ?>  
    </tbody>
</table>

