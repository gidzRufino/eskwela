<div class="col-lg-12 no-padding">
    <h6 class="no-margin text-left">
        Legend :
        &nbsp; &nbsp; <button class="btn btn-success btn-xs" disabled><i class="fa fa-search"></i></button> - search schedule
        &nbsp; &nbsp; <button class="btn btn-warning btn-xs" disabled><i class="fa fa-minus"></i></button> - clear schedule
        &nbsp; &nbsp; <button class="btn btn-danger btn-xs" disabled><i class="fa fa-trash"></i></button> - remove subject
        &nbsp; &nbsp; <button class="btn btn-info btn-xs" disabled=""><i class="fa fa-search-plus"></i></button> - add subject
    </h6><br/>
    <table class="table table-striped table-bordered table-responsive" id="tableSched">
        <tr>
            <th>Subject</th>
            <th>Day / Time</th>
            <th>Instructor</th>
            <th>Units</th>
            <th class="text-center action" >Action</th>
        </tr>
        <tbody id="subjectLoadBody">
            <?php
            if($subjects):
                foreach ($subjects as $s):
                    ?>
                    <tr class="trSched"
                        id="tr_<?php echo $s->s_id ?>"
                        sec_id="" time_from="" time_to="" day="" tchr=""
                        sub_code="<?php echo $s->sub_code ?>"
                        >
                        <td><?php echo strtoupper($s->sub_code) ?></td>
                        <td class="hasValue" id="daytime_<?php echo $s->s_id ?>"></td>
                        <td class="hasValue" id="instructor_<?php echo $s->s_id ?>"></td>
                        <td class="hasValue" id="units_<?php echo $s->s_id ?>"></td>
                        <td class="text-center action">
                            <button onclick="loadSchedule('<?php echo $s->sub_code ?>')" title="search for schedule" class="btn btn-success btn-xs"><i class="fa fa-search"></i></button>
                            <button onclick="clearSchedule('<?php echo $s->s_id ?>')" title="clear schedule" class="btn btn-warning btn-xs"><i class="fa fa-minus"></i></button>
                            <button onclick="removeSubject('<?php echo $s->s_id ?>')" title="remove from the list" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>


                    <?php
                endforeach;
            endif;    
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td id="totalUnits"></td>
            </tr>
        </tfoot>

    </table>
</div>



