<div class="col-lg-12 no-padding" style="margin-top:10px;">
    <div class="alert alert-success clearfix" style="margin-bottom: 0; padding: 3px;">
        <h4 class="text-center">Academic Records

        </h4>
        
    </div>
    <table class="table table-striped table-bordered">
        <tr>
            <th style="vertical-align: middle; text-align: center;" class="col-lg-1" rowspan="2">Subjects</th>
            <th class="col-lg-8 text-center" colspan="4">Grading Periods</th>
            <th style="vertical-align: middle; text-align: center;" class="col-lg-1" rowspan="2">Final Rating</th>
            <th style="vertical-align: middle; text-align: center;" class="col-lg-1" rowspan="2">Action</th>
        </tr>
        <tr>
            <th class="col-lg-2 text-center">1</th>
            <th class="col-lg-2 text-center">2</th>
            <th class="col-lg-2 text-center">3</th>
            <th class="col-lg-2 text-center">4</th>
            
            
        </tr>

        <?php 
       // print_r($acadRecords);
        $count = 0;
        foreach($acadRecords->result() as $ar): 
            if($ar->subject_id != 0):
            ?>
        <tr>
            <td class="col-lg-1 text-center"><?php echo $ar->subject ?></td>
            <td class="col-lg-2 text-center"><?php echo $ar->first ?></td>
            <td class="col-lg-2 text-center"><?php echo $ar->second ?></td>
            <td class="col-lg-2 text-center"><?php echo $ar->third ?></td>
            <td class="col-lg-2 text-center"><?php echo $ar->fourth ?></td>
            
            <th class="col-lg-1  text-center"><?php echo $ar->avg ?></th>
            <th class="col-lg-1  text-center"><i class="fa fa-trash pointer text-danger" onclick="deleteSingleRecord('<?php echo $ar->ar_id ?>')"></i> </th>
        </tr>
        <?php
            endif;
        endforeach; 
        ?>
        
    </table>
    <input type="hidden" id="getSPR" value="<?php echo $spr_id; ?>" />
</div>