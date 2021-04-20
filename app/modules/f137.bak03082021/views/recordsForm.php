
<!--End of Education History-->
<?php
// this will show or hide the buttons
if ($student->grade_level_id <= 7):
    $gs = '';
    $jhs = 'hide';
    $shs = 'hide';
elseif ($student->grade_level_id >= 7 && $student->grade_level_id < 12):
    $gs = '';
    $jhs = '';
    $shs = 'hide';
else:
    $gs = '';
    $jhs = '';
    $shs = '';
endif;
?> 
<!--Academic Records-->

<?php $year = $sy - ($student->grade_level_id - 2); ?>
<button class="btn btn-sm btn-primary pull-right" onclick="printOpt('<?php echo $student->grade_level_id ?>')">Print Form137</button>
<br/><br/><br/>
<div class="card">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="active <?php echo $gs ?>" id="gs" onclick="activePane(this.id)"><a href="#gsDisplay">Elementary Department</a></li>
        <li class=" <?php echo $jhs ?>" id="jhs" onclick="activePane(this.id)"><a href="#jhsDisplay">Junior High School</a></li>
        <li class=" <?php echo $shs ?>" id="shs" onclick="activePane(this.id)"><a href="#shsDisplay">Senior High School</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="gsDisplay">
            <br>
            <div class="col-md-6">
                <div class="row">
                    <?php
                    for ($x = 2; $x <= $student->grade_level_id; $x++):
                        if ($x >= 2 && $x <= 4):
                            $acadRec = Modules::run('f137/showAcadRecords', base64_encode($student->st_id), $x, $year++, $student->spr_id);
                            ?>
                            <div class="col-md-12">
                                <?php echo $acadRec ?>
                            </div>
                            <?php
                        endif;
                    endfor;
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <?php
                    for ($x = 5; $x <= $student->grade_level_id; $x++):
                        if ($x >= 5 && $x <= 7):
                            $acadRec = Modules::run('f137/showAcadRecords', base64_encode($student->st_id), $x, $year++, $student->spr_id);
                            ?>
                            <div class="col-md-12">
                                <?php echo $acadRec ?>
                            </div>
                            <?php
                        endif;
                    endfor;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="jhsDisplay">
            <br>
            <div class="col-md-6">
                <div class="row">
                    <?php
                    for ($x = 8; $x <= $student->grade_level_id; $x++):
                        if ($x >= 8 && $x <= 9):
                            $acadRec = Modules::run('f137/showAcadRecords', base64_encode($student->st_id), $x, $year++, $student->spr_id);
                            ?>
                            <div class="col-md-12">
                                <?php echo $acadRec ?>
                            </div>
                            <?php
                        endif;
                    endfor;
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <?php
                    for ($x = 10; $x <= $student->grade_level_id; $x++):
                        if ($x >= 10 && $x <= 11):
                            $acadRec = Modules::run('f137/showAcadRecords', base64_encode($student->st_id), $x, $year++, $student->spr_id);
                            ?>
                            <div class="col-md-12">
                                <?php echo $acadRec ?>
                            </div>
                            <?php
                        endif;
                    endfor;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="shsDisplay">
            <br>
            <?php
            for ($x = 2; $x <= $student->grade_level_id; $x++):
                if ($x >= 12 && $x <= 13):
                    $acadRec = Modules::run('f137/showAcadRecords', base64_encode($student->st_id), $x, $year++);
                    ?>
                    <div class="col-md-6">
                        <?php echo $acadRec ?>
                    </div>
                    <?php
                endif;
            endfor;
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    function activePane(id) {
        switch (id) {
            case 'gs':
                $('#gs').addClass('active');
                $('#jhs').removeClass('active');
                $('#shs').removeClass('active');
                $('#gsDisplay').addClass('active');
                $('#jhsDisplay').removeClass('active');
                $('#shsDisplay').removeClass('active');
                break;
            case 'jhs':
                $('#gs').removeClass('active');
                $('#jhs').addClass('active');
                $('#shs').removeClass('active');
                $('#gsDisplay').addClass('active');
                $('#gsDisplay').removeClass('active');
                $('#jhsDisplay').addClass('active');
                $('#shsDisplay').removeClass('active');
                break;
            case 'shs':
                $('#gs').removeClass('active');
                $('#jhs').removeClass('active');
                $('#shs').addClass('active');
                $('#gsDisplay').addClass('active');
                $('#gsDisplay').removeClass('active');
                $('#jhsDisplay').removeClass('active');
                $('#shsDisplay').addClass('active');
                break;
        }
    }
</script>