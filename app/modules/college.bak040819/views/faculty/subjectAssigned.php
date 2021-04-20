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
            <table class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Schedule</th>
                    <th></th>
                </tr>
                <tbody id="subjectsAssignedTable">
                    <?php
                    $i = 1;
                    //print_r($assignment);
                    foreach ($assignment as $as): ?>
                    <tr id="as_<?php echo $as->ass_id ?>">
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $as->subject ?></td>
                        <td><?php echo $as->level ?></td>
                        <td><?php echo $as->section ?></td>
                        <td>COMING SOON</td>
                        <td><button title="Delete Subject Assigned" onclick="removeSubject('<?php echo $as->ass_id ?>')" class="btn btn-xs btn-danger pull-right" style="margin-right: 5px;"><i class="fa fa-trash"></i></button></td>
                    </tr>
                    <?php endforeach; ?> 
                </tbody>
                
            </table>
        </div>
    </div>
</div>