<?php
if ($task->task_author_id == $this->session->username):
    $submittedTask = Modules::run('opl/opl_variables/getSubmittedTask', $task->task_auto_id, $this->session->school_year);
    if (count($submittedTask->result()) > 0):
        $col = 'col-lg-6';
    else:
        $col = 'col-lg-12';
    endif;
else:
    $col = 'col-lg-12';
endif;
if ($this->session->isStudent): 
     $iSubmitted = Modules::run('opl/opl_variables/getSubmittedTask', $task->task_auto_id, $this->session->school_year, $this->session->details->st_id);
    if ($iSubmitted->row()):
        $col = 'col-lg-6';
    else:
        $col = 'col-lg-12';
    endif;
endif;

?>

<section id="taskDetails" class="<?php echo $col; ?> float-left">
    <div class="card card-widget " id="card_<?php echo $task->task_auto_id ?>">
        <div class="card-header">
            <div class="user-block">
                <img class="img-circle" width="50" src="<?php echo base_url() . 'uploads/' . $task->avatar; ?>" alt="User Image">
                <span class="username"><?php echo $task->task_title . ' - ' . $task->tt_type ?></span>
                <span class="description"><a href="#"><?php echo $task->firstname . ' ' . $task->lastname; ?></a> <small><?php echo date('F d, Y g:i a', strtotime($task->task_start_time)) ?></small></span>
            </div>
            <?php if ($task->task_author_id == $this->session->username):
                ?>
                <div class="box-tools float-right">
                    <div class="float-right">
                        <button type="button" class="btn btn-box-tool btn-xs" data-toggle="tooltip" title="" data-original-title="Mark as read">
                            <i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-box-tool btn-xs" data-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool btn-xs" data-widget="remove"><i class="fas fa-times"></i></button>
                    </div><br />
                    <span class="float-right text-muted danger" 
                          style="font-size: 12px;">
                        Submitted by: <?php echo count($submittedTask->result()) ?> student(s)
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($this->session->isStudent): 
                    if(!$iSubmitted->row()):
                        ?>
                            <div class="box-tools float-right">
                                <button id="btnAnswer" onclick="$(this).hide(), $('#answerWrapper').show(), $('#taskDetails').removeClass('col-lg-12'), $('#taskDetails').addClass('col-lg-6')" type="button" class="btn btn-primary btn-xs">
                                    Answer this Task
                                </button>

                            </div>
                        <?php

                    endif;
                endif; ?>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <?php echo $task->task_details ?>
            <!--<button type="button" class="btn btn-default btn-sm"><i class="fa fa-thumbs-up"></i> Got It!</button>-->
        </div>
        <div class="card-footer card-comments">
            
        </div>
        <div class="card-footer">
            <form action="#" method="post">
                <img class="img-fluid img-circle img-sm" width="50" src="<?php echo base_url() . 'uploads/' . $task->avatar; ?>" alt="">
                <!-- .img-push is used to add margin to elements next to floating images -->
                <div class="img-push">
                    <input type="text" class="form-control form-control-sm" placeholder="Press enter to post comment">
                </div>
            </form>
        </div>
    </div>
</section>
<?php if($this->session->isStudent): 
        if($iSubmitted->row()):
    ?>
        <div class="card card-widget card-blue card-outline" >
            <div class="card-header">
                <h6 class="text-center">Your Answer</h6>
            </div>
            <div class="card-body">
                <?php 
                    if($iSubmitted->row()->ts_submission_type==1):
                ?>
                <span>Submitted File : </span><a href="<?php echo $iSubmitted->row()->ts_details ?>" target="_blank" ><?php echo $iSubmitted->row()->ts_file_name ?></a>
                <?php
                    else:
                        echo $iSubmitted->row()->ts_details;
                        
                    endif;
                    ?>
            </div>
            <div class="card-footer card-comments" id="<?php echo $this->session->details->st_id?>_comments">
                <?php 
                    echo Modules::run('opl/opl_variables/getComments', $iSubmitted->row()->ts_id, 4, $this->session->school_year); 
                    ?>
            </div>
            <div class="card-footer">
                <img class="img-fluid img-circle img-sm" width="50" src="<?php echo base_url() . 'uploads/' . $this->session->details->avatar; ?>" alt="">
                    <!-- .img-push is used to add margin to elements next to floating images -->
                    <div class="img-push">
                        <!--<input type="text" class="form-control form-control-sm" placeholder="Press enter to post comment">-->
                        <textarea id="<?php echo $this->session->details->st_id ?>_textarea" class="form-control form-control-sm clearfix" placeholder="Type here to Post a Comment"></textarea>
                        <button onclick="sendComment('4','<?php echo ($this->session->isStudent ? $this->session->details->st_id : $this->session->employee_id) ?>','<?php echo $iSubmitted->row()->ts_id ?>', '<?php echo $this->session->details->st_id ?>','1')" class="btn btn-xs btn-primary float-right col-xs-12">send</button>
                    </div>
                </div>
        </div>

    <?php
        endif;
    endif; 
    
 if ($task->task_author_id == $this->session->username): ?>
    <section class="col-lg-6 float-left card no-padding">
        <div class="card no-margin">
            <div class="card-header">
                <div class="user-block">
                    <span>Student Response</span>
                </div>
            </div>
            <div class="card-body no-padding">
                <div class=" col-lg-12 no-padding" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <?php
                    foreach ($submittedTask->result() as $sT):

                        $student = Modules::run('opl/opl_variables/getStudentBasicEdInfoByStId', $sT->ts_submitted_by, $this->session->school_year);
                        ?>
                        <div onclick="$('#scoreWrapper_<?php echo $student->user_id ?>').show()" class="panel card card-primary card-outline no-margin pointer accordion" >
                            <div class="card-header with-border">
                                <h6 class="box-title col-lg-9 float-left" data-toggle="collapse" data-target="#collapse<?php echo $student->user_id ?>" aria-expanded="false">
                                    <?php echo ucwords(strtolower($student->lastname . ' ' . $student->firstname)) ?><br />
                                    <small class="description" style="color: #78838e; font-size: 10px;">Date Submitted : <?php echo date('F d, Y', strtotime($sT->ts_date_submitted)) ?></small>
                                </h6>
                                <div class="form-group-sm col-lg-2 float-right clearfix">
                                    <button type="button" onclick="$('#scoreWrapper_<?php echo $student->st_id ?>').show(), $(this).hide()" id="recordToggle_<?php echo $student->st_id ?>" class="btn btn-xs btn-primary float-right">Score</button>

                                    <div class="input-group mb-3" id="scoreWrapper_<?php echo $student->st_id ?>"  style="display: none;">
                                        <input type="text" class="form-control"i d="score_<?php echo $student->st_id ?>" style="height: 25px;" placeholder="score" aria-label="score" aria-describedby="button-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-success btn-xs"onclick="$('#scoreWrapper_<?php echo $student->st_id ?>').hide(), $('#recordToggle_<?php echo $student->st_id ?>').show();" type="button" id=""><i class="fas fa-save"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="collapse<?php echo $student->user_id ?>" class="panel-collapse collapse in" style="">
                                <div class="card-body">
                                    
                                    <?php 
                                        if($sT->ts_submission_type==1):
                                    ?>
                                    <span>Submitted File : </span><a href="<?php echo $sT->ts_details ?>" target="_blank" ><?php echo $sT->ts_file_name ?></a>
                                    <?php
                                        else:
                                            echo $sT->ts_details;

                                        endif;
                                        ?>
                                </div>
                                <div class="card-footer card-comments" id="<?php echo $student->st_id ?>_comments">
                                
                                    <?php 
                                        echo Modules::run('opl/opl_variables/getComments', $sT->ts_id, 4, $this->session->school_year); 
                                        ?>
                                </div>
                                <div class="card-footer">
                                        <img class="img-fluid img-circle img-sm" width="50" src="<?php echo base_url() . 'uploads/' . $task->avatar; ?>" alt="">
                                        <!-- .img-push is used to add margin to elements next to floating images -->
                                        <div class="img-push">
                                            <!--<input type="text" class="form-control form-control-sm" placeholder="Press enter to post comment">-->
                                            <textarea id="<?php echo $student->st_id ?>_textarea" class="form-control form-control-sm clearfix" placeholder="Type here to Post a Comment"></textarea>
                                            <button onclick="sendComment('4','<?php echo ($this->session->isStudent?$this->session->details->st_id:$this->session->employee_id) ?>','<?php echo $sT->ts_id ?>', '<?php echo $student->st_id ?>','0')" class="btn btn-xs btn-primary float-right col-xs-12">send</button>
                                        </div>
                                </div>

                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>
<section class="col-lg-6 float-right" id="answerWrapper" style="display: none;">
<?php $this->load->view('../students/createResponse'); ?>

</section>
<input type="hidden" id="task_id" value="<?php echo $task->task_auto_id ?>" />
<input type="hidden" id="task_code" value="<?php echo $task->task_code ?>" />
<input type="hidden" id="subject_id" value="<?php echo $task->task_subject_id ?>" />
<input type="hidden" id="task_type" value="<?php echo $task->task_type ?>" />
<input type="hidden" id="school_year" value="<?php echo $this->session->school_year ?>" />
<input type="hidden" id="st_id" value="<?php echo ($this->session->isStudent?$this->session->details->st_id:'') ?>" />
<script type="text/javascript">
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover({
            html: true
        });

    });

    $(function () {
        // Summernote
        $('.textarea').summernote();

        $("#btnAnswer").click(function () {
            $('html,body').animate({
                scrollTop: $("#answerWrapper").offset().top},
                    'slow');
        });
        
        sendComment = function(com_type, st_id, post_id, id, isStudent) {
            
            var base = $('#base').val();
            var url = base + 'opl/sendComment';
            
            $.ajax({
                type    : 'POST',
                url     : url,
                data    : {
                    com_type        : com_type,
                    com_details     :$('#'+id+'_textarea').val(),
                    com_from        : st_id,
                    com_to          : post_id,
                    is_student      : isStudent,
                    csrf_test_name: $.cookie('csrf_cookie_name')
                },
                success: function (data)
                {
                    $('#'+id+'_comments').html(data);
                    $('#'+id+'_textarea').val('');
                }
            });
            
            return false;
            
        }

        createResponse = function () {
            var base = $('#base').val();
            var url = base + 'opl/student/createResponse';

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    task_id: $('#task_id').val(),
                    task_details: $('#answerDetails').val(),
                    task_submission_type: $('#submissionType').val(),
                    csrf_test_name: $.cookie('csrf_cookie_name')
                }, // serializes the form's elements.
                //dataType: 'json',
                beforeSend: function () {
                    $('#loadingModal').modal('show');
                },
                success: function (data)
                {
                    alert(data);
                    location.reload();
                }
            });
        };

    });

    //uploading script
    $('#fileSubmit').click(function (e) {
	var file = document.getElementById("userfile").files[0];
        //readUploadFile(fileUpload);
        var formdata = new FormData();
        formdata.append("userfile", file);
        formdata.append('csrf_test_name', $.cookie('csrf_cookie_name'));
        formdata.append('submission_type', $('#submissionType').val());
        formdata.append('task_type', $('#task_type').val());
        formdata.append('task_code', $('#task_code').val());
        formdata.append('subject_id', $('#subject_id').val());
        formdata.append('st_id', $('#st_id').val());
        formdata.append('school_year', $('#school_year').val());
        formdata.append('task_id', $('#task_id').val());
        
         var ajax = new XMLHttpRequest();
        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);
        ajax.open("POST", "<?php echo base_url() . 'opl/student/uploadResponse/' ?>");
        ajax.send(formdata);
   

    });
     
    function progressHandler(event) {

        $('#progressBarWrapper').show();
       
    }
    function completeHandler(event) {
       // _("status").innerHTML = event.target.responseText;
        $("#progressBarWrapper").hide();
        alert(event.target.responseText);
        location.reload();
    }
    
    function errorHandler(event) {
        alert(event.target.responseText);
    }
    function abortHandler(event) {
        alert(event.target.responseText);
    }

    function readUploadFile(data)
    {

        var currentData = document.getElementById('uploadValue');

        if (currentData.value == "") {
            // alert(currentData.value)   
            currentData.value = data
        } else {
            currentData.value = currentData.value + ',' + data
        }
    }

</script>    

<script src="<?php echo base_url('opl_assets/js/ajaxfileupload.js'); ?>"></script>

