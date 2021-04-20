<div class="page-header" style="color:black; margin:20px 0;width: 100%;">
    <?php
    switch ($sem):
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
    <span style="text-align: left; font-weight: bold">Semester: <?php echo $semester ?></span>
        <input style="display: none;float: right;" name="enDate" type="text" data-date-format="yyyy-mm-dd" id="enDate" value="<?php echo $students->date_admitted; ?>" placeholder="Date Enrolled" 

                onkeypress="if (event.keyCode==13){editEnBdate(this.value,'<?php echo $student->u_id ?>')}"   

               required>   
        <i id="a_enDate" onblur="$('#a_enDate').show(),$('#enDate').hide()" onclick="$('#enDate').datepicker(), $('#a_enDate').hide(), $('#enDate').show(),$('#enDate').focus()" style="float: right;font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
        <span  id="a_enDate" style="float: right; "  >
            Date Enrolled: &nbsp;
            <?php echo $students->date_admitted; ?> &nbsp;
        </span> 
        
        

</div>
<div class="col-lg-6" style="border-right: 1px solid #ccc">
    <table class="table table-stripped table-hover">
        <tr>
            <th colspan="4" class="text-center"> Subjects Offered 
                <button class="btn btn-warning btn-xs pull-right" onclick="$('#addSubjectModal').modal('show')">ADD SUBJECT</button>
            </th>
        </tr>
    <tr>
        <th class="text-center">Code</th>
        <th class="text-center">Subject</th>
        <th class="text-center">Lecture Units</th>
        <th class="text-center">Lab Units</th>
        <th></th>
    </tr>
    
    <tbody id="subjectOffered">
    <?php
        $totalUnitsLec = 0;
        $totalUnitsLab = 0;
        if(!empty($subjects)):
            foreach ($subjects as $sub):
                $exist = Modules::run('college/subjectmanagement/ifSubjectExist', $student->u_id, $sub->s_id);
                if(!$exist):
                    ?>
                        <tr class="pointer" id="tr_<?php echo $sub->s_id ?>" onclick="$('#addSubjectModal').modal('show'), searchSubject('<?php echo $sub->sub_code ?>')">
                            <td class="text-center"><?php echo $sub->sub_code ?></td>
                            <td class="text-center"><?php echo $sub->s_desc_title ?></td>
                            <td id="<?php echo $sub->s_id ?>_lect" class="text-center"><?php echo ($sub->s_lect_unit==""?0:$sub->s_lab_unit+$sub->s_lect_unit); ?></td>
                            <td class="text-center"><?php echo ($sub->s_lab_unit==""?0:$sub->s_lab_unit) ?></td>
                        </tr>

                    <?php
                else:
                    ?>
                        <tr class="pointer" id="tr_<?php echo $sub->s_id ?>" >
                            <td class="text-center"><?php echo $sub->sub_code ?></td>
                            <td class="text-center"><?php echo $sub->s_desc_title ?></td>
                            <td id="<?php echo $sub->s_id ?>_lect" class="text-center"><?php echo ($sub->s_lect_unit==""?0:$sub->s_lab_unit+$sub->s_lect_unit); ?></td>
                            <td class="text-center"><?php echo ($sub->s_lab_unit==""?0:$sub->s_lab_unit) ?></td>
                        </tr>

                    <?php    
                endif;
            endforeach;
        endif;    
    ?>
    
    </tbody>    
</table>
</div>
<div class="col-lg-6">
    <table class="table table-stripped table-hover">
        <tr>
            <th colspan="4" class="text-center"> Subjects Taken </th>
        </tr>
        <tr>
            <th class="text-center">Subject Code</th>
            <th class="text-center">Description</th>
            <th class="text-center">Units</th>
            <th class="text-center">Schedule</th>
            <th></th>
        </tr>
        <tbody id="subjectTaken">

            <?php
                //print_r($student);
                $loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $student->admission_id, NULL, $student->school_year); 
                $count = count($loadedSubject);
                $aprv = 0;
                $isOnline = FALSE;
                foreach ($loadedSubject as $ls):
                    $aprv += $ls->is_final;
                    $isOnline = ($student->enrolled_online?TRUE:FALSE);
                    
                   if($this->session->userdata('position_id') == 1 || $this->session->position=='Registrar' || $this->session->position=='Dean' || $this->session->position=='Dean Secretary' || $this->session->position=='Registrar\'s Staff' || $this->session->position=='OICT Department Head'
                           || $this->session->position=='IT Coordinator' || $this->session->position=='Quality Assurance Director'):
            ?>
                        <tr class="pointer" id="trtaken_<?php echo $ls->s_id ?>" onclick="removeSubject('<?php echo $ls->s_id ?>','<?php echo $ls->pre_req ?>')">
                            <td class="text-center"><?php echo $ls->sub_code ?></td>
                            <td class="text-center"><?php echo $ls->s_desc_title ?></td>
                            <td id="<?php echo $ls->s_id ?>_lect" class="text-center"><?php echo ($ls->sub_code=="NSTP 11" || $ls->sub_code=="NSTP 12" || $ls->sub_code=="NSTP 1" || $ls->sub_code=="NSTP 2" ? 3 :($ls->s_lect_unit)) ?></td>
                            <td class="text-center">
                            <?php $scheds = Modules::run('college/schedule/getSchedulePerSection', $ls->cl_section, $student->semester, $student->school_year); 
                                $sched = json_decode($scheds);
                                echo ($sched->count > 0 ? $sched->time.' [ '.$sched->day.' ]':'TBA');
                            ?>
                            </td>
                        </tr>

            <?php   
                        $totalUnitsLec += ($ls->sub_code=="NSTP 11" || $ls->sub_code=="NSTP 12" || $ls->sub_code=="NSTP 1" || $ls->sub_code=="NSTP 2" ? 3 :($ls->s_lect_unit));
                        $totalUnitsLab += $ls->s_lab_unit;
                    else:
                        if($ls->is_final):    
            ?>
                        
                            <tr class="pointer" id="trtaken_<?php echo $ls->s_id ?>" onclick="removeSubject('<?php echo $ls->s_id ?>','<?php echo $ls->pre_req ?>')">
                                <td class="text-center"><?php echo $ls->sub_code ?></td>
                                <td class="text-center"><?php echo $ls->s_desc_title ?></td>
                                <td id="<?php echo $ls->s_id ?>_lect" class="text-center"><?php echo ($ls->s_lect_unit==0?0:($ls->sub_code=="NSTP 11" || $ls->sub_code=="NSTP 12" || $ls->sub_code=="NSTP 1" || $ls->sub_code=="NSTP 2" ? 3 :($ls->s_lect_unit))); ?></td>
                                <td class="text-center">
                                <?php $scheds = Modules::run('college/schedule/getSchedulePerSection', $ls->cl_section, $student->semester, $student->school_year); 
                                    $sched = json_decode($scheds);
                                    echo ($sched->count > 0 ? $sched->time.' [ '.$sched->day.' ]':'TBA');
                                ?>
                                </td>
                            </tr>

            <?php       $totalUnitsLec += ($ls->sub_code=="NSTP 11" || $ls->sub_code=="NSTP 12" || $ls->sub_code=="NSTP 1" || $ls->sub_code=="NSTP 2" ? 3 :($ls->s_lect_unit));
                        $totalUnitsLab += $ls->s_lab_unit;
                        endif;
                    endif;
                    
                    
                endforeach;
            ?>

            <tr id="tr_total_taken">
                <td colspan="2"></td>
                <td id="totalLect_taken" class="text-center" style="font-weight: bold;"><?php echo ($totalUnitsLec==0?0:$totalUnitsLec) ?></td>
                <!--<td class="text-center" style="font-weight: bold;"><?php echo ($totalUnitsLab==0?0:$totalUnitsLab) ?></td>-->
            </tr>    
        </tbody>
        <?php if($this->session->userdata('position_id') == 1 || $this->session->position=='Registrar'|| $this->session->position=='OICT Department Head' || $this->session->position=='IT Coordinator'
                 || $this->session->position=='Quality Assurance Director'): ?>
        <tfoot>
            <tr>
                <?php if($count!=$aprv): ?>
                    <td colspan="4">
                        <?php if(!$isOnline): ?>
                            <button id="btnApprove" class="btn btn-warning pull-right">Approve</button>
                        <?php else: ?>
                            <button id="onlineBtnApprove" class="btn btn-warning pull-right">Approve Online Application</button>
                        <?php endif; ?>
                        <?php 
                            if($this->session->userdata('position_id')==1 || $this->session->userdata('position_id')== 49 ) :
                                echo '<button style="margin-right:10px;" onclick="$(\'#printStudyLoad\').modal(\'show\')" class="btn btn-success pull-right">Print Study Load</button>';    
                            endif;
                        ?>
                    </td>
                <?php else: ?>
                <td colspan="4">
                    
                    <button onclick="$('#printStudyLoad').modal('show')" class="btn btn-warning pull-right">Print Study Load</button>
                    <?php if($count!=$aprv): ?>
                        <button style="margin-right:10px;" class="btn btn-success pull-right disabled">Study Load Already Approved</button>
                    <?php endif; ?>
                </td>
                
                <?php endif; ?>
            </tr>
        </tfoot>
        <?php endif ?>
        
        <?php if($this->session->position=='Dean' || $this->session->position=='Dean Secretary'): ?>
            <tfoot>
                <tr>
                    <?php if($count!=$aprv): ?>
                        <td colspan="4">
                            <?php if(!$isOnline): ?>
                                <button id="btnApprove" class="btn btn-warning pull-right">Approve</button>
                            <?php else: ?>
                                <button id="onlineBtnApprove" class="btn btn-warning pull-right">Approve Online Application</button>
                            <?php endif; ?>
                        </td>
                    <?php else: ?>
                    <td colspan="4">
                        <button style="margin-right:10px;" class="btn btn-success pull-right disabled">Study Load Already Approved</button>
                    </td>

                    <?php endif; ?>
                </tr>
            </tfoot>
        <?php endif; ?>
            
        <?php if($this->session->position=='Registrar\'s Staff'): ?>
        <tfoot>
            <tr>
                <td colspan="4">
                    <button onclick="$('#printStudyLoad').modal('show')" class="btn btn-warning pull-right">Print Study Load</button>
                </td>
                
            </tr>
        </tfoot>
        <?php endif ?>
</table>
    
</div>

<div id="addSubjectModal" class="modal fade" style="width:900px; margin:10px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green">
        <div class="panel-heading clearfix">
            <h4 class="no-margin">Add Subject
            <div class="form-group col-lg-4 pull-right" id='searchBox' style="margin:10px 0;">
                <div class="controls">
                  <input autocomplete="off"  class="form-control"  onkeypress="if(event.keyCode==13){searchSubject(this.value)}"  name="searchSubject" type="text" id="searchSubject" placeholder="Search Subject" required>
                  <input type="hidden" id="teacher_id" name="teacher_id" value="0" />
                  <input type="hidden" id="course_id" name="course_id" value="<?php echo $course_id ?>" />
                </div>
                <div style="min-height: 30px; background: #FFF; width:230px; position:absolute; z-index: 2000; display: none;" class="resultOverflow" id="teacherSearch">

                </div>
            </div>
            </h4>
        </div>
        <div class="panel-body clearfix" id="subjectBody">
            
        </div>
            
    </div>
</div>

<div id="printStudyLoad"  style="width:35%; margin: 50px auto;"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green" style='width:100%;'>
        <div class="panel-heading">
            <h3>
                <i class="fa fa-info-circle fa-fw"></i> Are you sure you want to finalize the enrollment of this student? Please note that this will automatically update the student's finance accounts.
                Do you want to continue?
            </h3>
        </div>
        <div class="panel-body">
                <input type="hidden" id="del_charge_id" />
            <div style='margin:5px 0;'>
            <a href='#' id='<?php echo $g->grade_id ?>' data-dismiss='modal' onclick="printStudyLoad(<?php echo $student->semester; ?>, <?php echo $student->school_year ?>,'<?php echo base64_encode($student->uid); ?>')" style='margin-right:10px; color: white;' class='btn btn-xs btn-success pull-left'>Yes</a>
            <button data-dismiss='modal' class='btn btn-xs btn-warning pull-left'>No</button>&nbsp;&nbsp;
            <div class="pull-right" >
                <input type="checkbox" id="reprint" /> REPRINTING
            </div>
            </div>

        </div>
    </div>
</div>
<input type="hidden" id="subload_sem" value="<?php echo $student->semester; ?>" />
<input type="hidden" id="subload_year" value="<?php echo $student->school_year; ?>" />
<script type="text/javascript">
    $(document).ready(function(){
        if($('#totalLect_taken').html()==0)
        {
            $('#tr_total_taken').hide()
        };
        
        $('#btnApprove').click(function(){
            var url = "<?php echo base_url().'college/subjectmanagement/approveLoad/'?>"+'<?php echo $student->admission_id ?>/<?php echo $students->user_id ?>'; // the script where you handle the form input.

            $.ajax({
               type: "GET",
               url: url,
               dataType:'json',
               data: 'csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               { 
                   alert(data.msg);
                   socket.emit('sendNotification', { sendto: data.username,  msg :data.remarks, title:'Approved'}, function(data){});
                  
               }
             });

            return false; 
        });
        
        $('#onlineBtnApprove').click(function(){
            
            var url = "<?php echo base_url().'college/subjectmanagement/approveLoadOnline/'?>"+'<?php echo $student->admission_id ?>/<?php echo $students->user_id ?>/<?php echo $student->school_year ?>'; // the script where you handle the form input.
            $.ajax({
               type: "GET",
               url: url,
               dataType:'json',
               data: 'csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   alert(data.msg);
                   //socket.emit('sendNotification', { sendto: data.username,  msg :data.remarks, title:'Approved'}, function(data){});
                   location.reload();
                   
               }
             });

            return false; 
        });
        
    });
    
    var sub_id = 0;
    var sec_id = 0;
    
    function printStudyLoad(sem, year, st_id)
    {
        if($('#reprint').is(':checked')){
           var url = "<?php echo base_url('college/subjectmanagement/printStudyLoad/')?>/"+st_id+'/'+sem+'/'+year+'/'+1;
        }else{
            var url = "<?php echo base_url('college/subjectmanagement/printStudyLoad/')?>/"+st_id+'/'+sem+'/'+year;
        }
         // the script where you handle the form input.    
        window.open(url, '_blank');
    }
    
    function searchSubject(value)
     {
        var course_id = $('#course_id').val();
        var sem = $('#subload_sem').val();
        var school_year = $('#subload_year').val();
        var url = "<?php echo base_url().'college/subjectmanagement/loadSubject/'?>"+value+'/'+course_id+'/'+sem+'/'+school_year; // the script where you handle the form input.

        $.ajax({
               type: "GET",
               url: url,
               //dataType:'json',
               data: 'csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   $('#subjectBody').html(data)
               }
             });

        return false; 
     }
    
    function addSubject(subject_id, pre_req, section_id)
    {
        var lectUnit = $('#'+subject_id+'_lect').html();
        var unitTaken = $('#totalLect_taken').html();
        
        var url = "<?php echo base_url().'college/subjectmanagement/prerequisteCheck/' ?>";
        
        $.ajax({
               type: "POST",
               url: url,
               dataType: 'json',
               data: "subject_id="+subject_id+'&pre_req='+pre_req+'&student_id='+'<?php echo $student->u_id ?>'+'&school_year='+'<?php echo $student->school_year ?>'+'&csrf_test_name='+$.cookie('csrf_cookie_name')                                                                                                                                                                                                                                                                                     , // serializes the form's elements.
               success: function(data)
               {
                    if(data.status)
                    {
                        
                        /*var unitsTaken = parseInt(unitTaken) + parseInt(lectUnit);
                        $('#totalLect_taken').html(unitsTaken)
                        $('#tr_'+subject_id).attr('onclick','');
                        $('#subjectTaken').prepend('<tr class=\'pointer\' onclick=\'removeSubject('+subject_id+', "'+pre_req+'" )\' id=\'trtaken_'+subject_id+'\'>'+$('#tr_'+subject_id).html()+'</tr>')
                       // $('#tr_'+subject_id).remove();
                        $('#tr_total_taken').show() */
                        saveLoadedSubject(subject_id, section_id, 0)
                    }else{
                        sub_id = subject_id;
                        sec_id = section_id;
                        //alert('Sorry, This subject cannot be taken due to subject prerequisite issue')
                        $('#overideStudyLoad').modal('show');
                    }
               }
        });
        
       
    }
    
    
</script>    