<div id="importCsv" style="width:350px; margin: 10px auto 0;" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green">
        <div class="panel-heading">
            <h4>Upload Academic Records</h4>
        </div>
             <?php
        $attributes = array('class' => '', 'id'=>'importCSV', 'style'=>'margin-top:20px;');
        echo form_open_multipart(base_url().'f137/importAssessment', $attributes);
        ?>
        <div class="panel-body">
            <input type="hidden" id="st_sprid" name="st_sprid" />
            <input type="hidden" id="student_id" name="student_id" />
            <input type="hidden" id="selectSY" name="selectSY" />
            <input type="hidden" id="sLevel" name="sLevel" />
            <input type="hidden" id="lastSYen" name="lastSYen" value="<?php echo segment_5 ?>" />
            <input type="hidden" id="importTerm" name="importTerm" />
            <input style="height:30px" type="file" name="userfile" ><br />
            <input type="submit" name="submit" value="UPLOAD" class="btn btn-success">
        </div>
        <?php
            echo form_close();
        ?>
    </div>
    
</div>
