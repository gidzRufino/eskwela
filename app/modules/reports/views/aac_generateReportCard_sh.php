<div class="well col-lg-12">
    <div id="success"></div>
    <div class="col-lg-2">
        <img class="img-circle img-responsive" style="width:100px; border:5px solid #fff" src="<?php if($student->avatar!=""):echo base_url().'uploads/'.$student->avatar;else:echo base_url().'uploads/noImage.png'; endif;  ?>" />
    </div>
    <div class="col-lg-6">
        <h3 style="color:black; margin:3px 0;">
            <span id="name">
            <?php echo $student->firstname." ". $student->lastname ?></span> </h3>
        <h4 style="color:black; margin:3px 0;">
        <span id="grade">
            <?php echo $student->level ?> - <?php echo $student->section ?> </span> </h4>
        <h5 style="color:black; margin:3px 0;">
        <span id="student_id"  style="color:#BB0000;"><?php echo $student->uid ?></span> 
        </h5>
        
    </div>
    <div class="col-lg-4 pull-right">
        <button style="margin-top:15px; font-size:285%;" onclick="$('#cardPreview').modal('show'), previewCard('<?php echo base64_encode($student->uid) ?>', <?php echo $sy ?>, <?php echo $term ?>)" class="btn btn-xl btn-success pull-right"><i class="fa fa-book fa-fw"></i> Generate Card  </button>
    </div>
</div>

<div class="col-lg-12">
   <div class="col-lg-6">
    <input type="hidden" id="term" value="<?php echo $term ?>" />
        <input type="hidden" id="sy" value="<?php echo $sy ?>" />
            <?php
            
//            $rem = Modules::run('main/getAdmissionRemarks', $student->uid, "");
//            if($rem->num_rows >0):
//                if($rem->row()->code_indicator_id == 2):
//                    ?>
                        <i title="Use this Option with Precaution" onclick="$('#finalGradeData').html($('#special_table').html())" class="fa fa-pencil pointer pull-right"></i>
                    <?php
//                endif;
//            endif;
            ?>
        <?php if($term==4):
            $lock = Modules::run('gradingsystem/checkIfCardLock', $student->uid, $sy);
            if($lock):
                $lock='fa-lock';
            else:
                $lock='fa-unlock';
            endif;
        endif;
        
        switch ($term){
            case 1:
                $sem = 1;
            break;    
            case 2:
                $sem = 1;
            break;
            case 3:
                $sem = 1;
            break;    
            case 4:
                $sem = 2;
            break;
       }
        
?>
        <input type="hidden" id="cardLockController" value="0" />
        <h5 class="clearfix"><span class="pull-left">Final Grade </span> <?php if($term==4): ?><i onclick="lockFinalCard('<?php echo $student->uid?>',<?php echo $sy ?>)" id="final_lock" style="font-size:200%;" class="fa <?php echo $lock ?> fa-fw pull-right pointer text-danger"></i><?php endif; ?></h5>

            
        <hr />
        <div id="finalGradeData">    
        <table class="table table-striped">
            <tr>
                <td>
                    Subjects
                </td>
                <td>
                    Final Grade
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #b6d9ef;" colspan="2">Core Subjects</td>
            </tr>
            <?php $subject = Modules::run('academic/getSpecificSubjectPerlevel', $student->grade_id); 
                $gs_settings = Modules::run('gradingsystem/getSet', $sy);
                $coreSubs = Modules::run('subjectmanagement/getSHSubjects', $student->grade_id, $sem, $this->uri->segment(6), 1);  
                $i=0;
                foreach($coreSubs as $sub):
                    $singleSub = Modules::run('academic/getSpecificSubjects', $sub->sh_sub_id);
                    $finalGrade = Modules::run('gradingsystem/getFinalGrade', $student->uid, $singleSub->subject_id, $term, $sy);   
                    ?>
                    <tr>
                        <td style="padding-left: 5px;">&nbsp;&nbsp;&nbsp;<?php echo $singleSub->subject; ?></td>
                        <td style="text-align: center;">
                            <?php 
                                if($finalGrade->num_rows()>0):
                                    echo '<strong>'.$finalGrade->row()->final_rating.'</strong>';
                                else:
                                    $i++;
                                     if($term <= $this->session->userdata('term')):
                                            $assessment = Modules::run('gradingsystem/getPartialAssessment', $student->uid, $student->section_id, $singleSub->sub_id, $sy);
                                            switch ($term) {
                                                case 1:
                                                    $grading = 'first';
                                                    break;
                                                case 2:
                                                    $grading = 'second';
                                                    break;
                                                case 3:
                                                    $grading = 'third';
                                                    break;
                                                case 4:
                                                    $grading = 'fourth';
                                                    break;
                                            }
                                            if($assessment->is_validated > $term):
                                                Modules::run('gradingsystem/updateFinalGrade', $student->uid, $singleSub->sub_id, $assessment->$grading, $term, $sy ); 
                                                // $assessment->$term;
                                            else:
                                                echo 'no Final Grade Yet';

                                            endif;

                                    else:
                                        echo 'no Final Grade Yet';
                                    endif;
                                endif;
                            ?>

                        </td>
                    <?php
                endforeach; 
                ?>
                    <tr>
                        <td style="font-weight: bold; background: #b6d9ef;" colspan="2">Applied and Specialized Subjects</td>
                    </tr>   
                <?php        
                $appliedSubs = Modules::run('subjectmanagement/getSHSubjects', $student->grade_id, $sem, $this->uri->segment(6)); 
                $i=0;
                foreach($appliedSubs as $sub):
                    $singleSub = Modules::run('academic/getSpecificSubjects', $sub->sh_sub_id);
                    $finalGrade = Modules::run('gradingsystem/getFinalGrade', $student->uid, $singleSub->subject_id, $term, $sy);   
                    ?>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<?php echo $singleSub->subject; ?></td>
                        <td style="text-align: center;">
                            <?php 
                                if($finalGrade->num_rows()>0):
                                    echo '<strong>'.$finalGrade->row()->final_rating.'</strong>';
                                else:
                                    $i++;
                                     if($term <= $this->session->userdata('term')):
                                            $assessment = Modules::run('gradingsystem/getPartialAssessment', $student->uid, $student->section_id, $singleSub->sub_id, $sy);
                                            switch ($term) {
                                                case 1:
                                                    $grading = 'first';
                                                    break;
                                                case 2:
                                                    $grading = 'second';
                                                    break;
                                                case 3:
                                                    $grading = 'third';
                                                    break;
                                                case 4:
                                                    $grading = 'fourth';
                                                    break;
                                            }
                                            if($assessment->is_validated > $term):
                                                Modules::run('gradingsystem/updateFinalGrade', $student->uid, $singleSub->sub_id, $assessment->$grading, $term, $sy ); 
                                                $grade = json_decode(Modules::run('gradingsystem/getGradeForCard', $student->uid, $singleSub->sh_sub_id, $sy));
                                                // $assessment->$term;
                                                echo $grade->$grading;
                                            else:
                                                echo 'no Final Grade Yet';

                                            endif;

                                    else:
                                        echo 'no Final Grade Yet';
                                    endif;
                                endif;
                            ?>

                        </td>
                    <?php
                endforeach;
                ?>
        </table>
   </div>
       <input type="hidden" id="no_subject" value="<?php echo $i ?>" /> 
       <?php
            
       // if($gs_settings->gs_used==1):
            $remarks = Modules::run('gradingsystem/getCardRemarks', $student->uid,$term, $sy);
       ?>
        <br /><br /><br />
         <h5 class="pull-left">Remarks for the Card</h5>
         <button onclick="saveRemarks('<?php echo $student->uid?>',<?php echo $term ?>,<?php echo $sy ?>)" class="pull-right btn btn-small btn-success"> Save Remarks</button>
         <br />
         <hr />   
         <textarea id="cardRemarks" style="width:100%;" rows="5">
             <?php 
                 if($remarks->num_rows()>0):
                     echo $remarks->row()->remarks;
                 else:
                     echo '';
                 endif;
             ?>
         </textarea>
         <?php 
             if($term==4):
                 $data['grade_id'] = $student->grade_id;
                 $data['st_id'] = $student->uid;
                 $data['grade_level'] = Modules::run('registrar/getGradeLevel');
                 $this->load->view('incomplete_subjects', $data);
             endif;

        ?>
     </div>
            <div class="col-lg-6">
                <h5>Learner's Observed Values</h5>
                <hr />
                <?php if($gs_settings->customized_beh_settings):
                    
                    $settings = Modules::run('main/getSet');
                    $bhs['st_id'] = $student->uid;        
                    $bhs['sy'] = $sy;
                    $bhs['term'] = $term;
                    $this->load->view(strtolower($settings->short_name).'_customizedBehavior', $bhs);
                    
                else:
                ?>
                <table class="table table-striped">
                    <tr>
                        <td>Details</td>
                        <td>Ratings</td>
                    </tr>
                    <?php 
                        foreach($behavior as $beh)
                        {
                            $behaviorRating = Modules::run('gradingsystem/getBHRating', $student->uid,$term, $sy, $beh->bh_id); 
                            
                            ?>
                    <tr>
                        <td><?php echo $beh->bh_name ?></td>
                        <td>
                        <?php 
                        if($gs_settings->gs_used==1):
                            if($gs_settings->customized_card):
                                ?>
                                <select onclick="submitRating('<?php echo $student->uid?>', this.value, <?php echo $term ?>,<?php echo $sy ?>, <?php echo $beh->bh_id ?>)" tabindex="-1" style="width:200px" class="span2">
                                    <?php 
                                    if($behaviorRating->num_rows()>0):
                                            switch ($behaviorRating->row()->rate){
                                              case 1:
                                                  $one = 'selected';
                                                  $two = '';
                                                  $three = '';
                                                  $fourth = '';
                                              break;    
                                              case 2:
                                                  $one = '';
                                                  $two = 'selected';
                                                  $three = '';
                                                  $fourth = '';
                                              break;    
                                              case 3:
                                                  $one = '';
                                                  $two = '';
                                                  $three = 'selected';
                                                  $fourth = '';
                                              break;    
                                              case 4:
                                                  $one = '';
                                                  $two = '';
                                                  $three = '';
                                                  $fourth = 'selected';
                                              break;    
                                            }
                                        else:
                                                    $one = '';
                                                  $two = '';
                                                  $three = '';
                                                  $fourth = '';
                                    endif; ?> 
                                    <option>Select Rating</option>
                                    <option <?php echo $fourth ?> value="4">Very Good</option>
                                    <option <?php echo $three ?> value="3">Good</option>
                                    <option <?php echo $two ?> value="2">Fair</option>
                                    <option <?php echo $one ?> value="1">Poor</option>
                              </select>
                            <?php  
                            else:
                                ?>
                                 <select onclick="submitRating('<?php echo $student->uid?>', this.value, <?php echo $term ?>,<?php echo $sy ?>, <?php echo $beh->bh_id ?>)" tabindex="-1" style="width:200px" class="span2">
                                        <?php 
                                        if($behaviorRating->num_rows()>0):
                                                switch ($behaviorRating->row()->rate){
                                                  case 1:
                                                      $one = 'selected';
                                                      $two = '';
                                                      $three = '';
                                                  break;    
                                                  case 2:
                                                      $one = '';
                                                      $two = 'selected';
                                                      $three = '';
                                                  break;    
                                                  case 3:
                                                      $one = '';
                                                      $two = '';
                                                      $three = 'selected';
                                                  break;    
                                                }
                                            else:
                                                       $one = '';
                                                      $two = '';
                                                      $three = '';
                                        endif; ?> 
                                        <option>Select Rating</option>
                                        <option <?php echo $three ?> value="3">Lubhang Kasiya-siya</option>
                                        <option <?php echo $two ?> value="2">Kasiya-siya</option>
                                        <option <?php echo $one ?> value="1">Dapat Pang linangin</option>
                                  </select>

                                <?php
                            endif;
                        else:
                        ?>
                            <select onclick="submitRating('<?php echo $student->uid?>', this.value, <?php echo $term ?>,<?php echo $sy ?>, <?php echo $beh->bh_id ?>)" tabindex="-1" style="width:200px" class="span2">
                                <?php 
                                if($behaviorRating->num_rows()>0):
                                        switch ($behaviorRating->row()->rate){
                                          case 1:
                                              $one = 'selected';
                                              $two = '';
                                              $three = '';
                                              $fourth = '';
                                          break;    
                                          case 2:
                                              $one = '';
                                              $two = 'selected';
                                              $three = '';
                                              $fourth = '';
                                          break;    
                                          case 3:
                                              $one = '';
                                              $two = '';
                                              $three = 'selected';
                                              $fourth = '';
                                          break;    
                                          case 4:
                                              $one = '';
                                              $two = '';
                                              $three = '';
                                              $fourth = 'selected';
                                          break;    
                                        }
                                    else:
                                                $one = '';
                                              $two = '';
                                              $three = '';
                                              $fourth = '';
                                endif; ?> 
                                <option>Select Rating</option>
                                <option <?php echo $fourth ?> value="4">Always Observed</option>
                                <option <?php echo $three ?> value="3">Sometimes Observed</option>
                                <option <?php echo $two ?> value="2">Rarely Observed</option>
                                <option <?php echo $one ?> value="1">Not Observed</option>
                          </select>
                        <?php    
                            endif;
                        ?>
                        </td>
                    </tr>

                    <?php
                        }
                    ?>

                </table>
            <?php endif; ?>
            </div>
 
</div>
<div id="special_table" class="hide">
    <?php
        
        $data['term'] = $term;
        $data['sy'] = $sy;
        $data['student'] = $student;
        $this->load->view('reportCard/manualEntry', $data);
    ?>
</div>
<?php 
if($this->uri->segment(6)!=NULL):
    $data['strand'] = $this->uri->segment(6);
else:
    $data['strand'] = '';
endif;
$this->load->view('reportCardPreview', $data); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#searchAssessDate").select2();
       
    });
    
    function deleteINC(id)
    {
        var url = "<?php echo base_url().'reports/deleteINC/'?>"+id; // the script where you handle the form input.
        $.ajax({
               type: "GET",
               url: url,
               data: "id="+id+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   $('#tr_'+id).hide();
               }
        })
    }
    
    function saveINC(st_id){
          var url = "<?php echo base_url().'reports/saveINC/'?>"; // the script where you handle the form input.
          var sub = $('#inc_subject').val();
          var grade = $('#inputGrade').val();
          var option = $('#inc_option').val();
        $.ajax({
               type: "POST",
               url: url,
               dataType:'json',
               data: "level_id="+grade+'&subject_id='+sub+'&option='+option+'&st_id='+st_id+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   if(option==0){
                      var msg='Previous Years Completed';
                   }else{
                       msg='Current School Year';
                   }
                    
                   var result = '<tr><td>'+data.subject+'</td><td>'+data.level+'</td><td>'+msg+'</td></tr>';
                   $('#inc_table').append(result);
               }
             });

        return false;
      }
    
    function submitRating(st_id, rating, grading, school_year, bh_id)
    {
        
        var url = "<?php echo base_url().'gradingsystem/saveBH/'?>"+st_id+'/'+rating+'/'+grading+'/'+school_year+'/'+bh_id ;
           $.ajax({
            type: "GET",
            url: url,
            data: 'qcode='+grading, // serializes the form's elements.
            success: function(data)
            {
                      
                      
            }
          });
    }
    
    function saveRemarks(st_id,grading, school_year)
    {
        var remarks = $('#cardRemarks').val();
        
        var url = "<?php echo base_url().'gradingsystem/saveRemarks/'?>"+st_id+'/'+remarks+'/'+grading+'/'+school_year ;
           $.ajax({
            type: "GET",
            url: url,
            data: 'qcode='+grading, // serializes the form's elements.
            success: function(data)
            {   
               alert('Remarks Save')
            }
          });
    }
    function previewCard(st_id, sy, term)
    {
        var url = "<?php echo base_url().'reports/cardReview/'?>"+st_id+'/'+sy+'/'+term+'/<?php echo $this->uri->segment(6); ?>';
        $.ajax({
               type: "GET",
               url: url,
               data: 'csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                  $('#cardPreviewData').html(data);
               }
             });

        return false;
    }
    
    function printCard(st_id, sy, term, strand)
    {
        var url = "<?php echo base_url().'reports/printReportCard/'?>"+st_id+'/'+sy+'/'+term+'/'+strand;
        window.open(url, '_blank');
    }
    
    function lockFinalCard(st_id, sy)
    {
        var lockController = $('#cardLockController').val()
        
       var answer = confirm("Do you really want to Lock the Final Rating? Doing so will prevent you from future Changes.");
        if(answer==true){
            var url = "<?php echo base_url().'gradingsystem/lockFinalCard/'?>"+st_id+'/'+sy ;
               $.ajax({
                type: "GET",
                dataType: 'json',
                url: url,
                data: 'qcode='+sy, // serializes the form's elements.
                success: function(data)
                {   
                    if(data.status){
                        if(lockController==0){
                            $('#final_lock').removeClass('fa-unlock');
                            $('#final_lock').addClass('fa-lock')
                            $('#cardLockController').val(1)
                        }else{
                            $('#final_lock').removeClass('fa-lock');
                            $('#final_lock').addClass('fa-unlock')
                            $('#cardLockController').val(0)
                        }
                    }else{
                        alert('Unable to Finalize Card')
                    }
                }
              });
         }
         
        
    }
</script>