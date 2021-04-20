<?php echo Modules::run('academic/viewTeacherInfo', $id); ?>
<div class='col-lg-12 no-padding'>
    <div class="panel panel-info">
        <div class="panel-heading clearfix">
            <h5 class="text-center no-margin col-lg-7">Subjects Assigned</h5>
            <div class="col-lg-5 pull-right">
                <a href="#" onclick="$('#addSubjectModal').modal('show')"class="btn btn-sm btn-primary pull-right" style="margin-right: 5px;">Add Subject</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-stripped table-hover">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Section Code</th>
                        <th>Descriptive Title</th>
                        <th>Course</th>
                        <th>Schedule</th>
                        <th class="text-center">Units</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <?php
                $totalUnits = 0;
                foreach ($subjects as $s):
                    $totalUnits += $s->s_lect_unit;
                ?>
                <tr>
                    <td><?php echo $s->sub_code; ?></td>
                    <td><?php echo $s->section; ?></td>
                    <td><?php echo $s->s_desc_title; ?></td>
                    <td><?php echo $s->short_code; ?></td>
                    <td>
                        <?php $scheds = Modules::run('college/schedule/getSchedulePerSection', $s->sec_id); 
                            $sched = json_decode($scheds);
                            echo ($sched->count > 0 ? $sched->time.' [ '.$sched->day.' ]':'');
                        ?>
                    </td>
                    <td class="text-center"><?php echo $s->s_lect_unit; ?></td>
                    <td class="text-center">
                    </td>
                </tr>
                <?php    
                endforeach;
                if($totalUnits > 0):
                ?>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center"><?php echo $totalUnits; ?></th>
                        <th></th>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>