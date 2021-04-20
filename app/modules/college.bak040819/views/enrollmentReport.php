<div class="row">
    <div class="col-lg-12">
        <h3 style="margin-top: 20px;" class="page-header">Enrollment Report
            <!--<small id="num_students"> [ <?php echo $allStudents->num_rows.' / '.$num_of_students; ?> ] </small>-->
            <input type="text" id="rfid" style="position: absolute; left:-1000px;" onchange="scanStudents(this.value)" onload="self.focus();" />
            <input type="hidden" id="hiddenSection" value="<?php echo $this->uri->segment(3) ?>" />
            
                <?php if($this->session->userdata('is_admin') || $this->session->userdata('is_superAdmin')){ ?>
                <!--
                <div class="btn-group pull-right ">
                    <button class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Print</button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li onclick="$('#printIdModal').modal('show')"><a href="#">For ID</a></li>
                        <li onclick="getSubjectOfferedPerSem(), $('#studentsPerSubject').modal('show')"><a href="#">List of Students Per Subject</a></li>
                      </ul>
                </div>-->
                <div class="btn-group pull-right">
                     <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url('college') ?>'">Dashboard</button>
                        <a href="#importCsv" data-toggle="modal"  id="uploadAssessment" class="btn btn-success" >
                            <i class="fa fa-upload"></i>
                        </a>
 
                </div>
                <div class="form-group pull-right">
                        <select onclick="getEnrolledStudentsBySemester(this.value)" tabindex="-1" id="inputSem" style="width:200px; font-size: 15px;" class=" ">
                            
                            <?php
                                switch ($sem):
                                    case 1:
                                        $first = 'Selected';
                                        $second='';
                                        $third='';
                                    break;
                                    case 2:
                                        $first = '';
                                        $second='selected';
                                        $third='';
                                    break;
                                    case 3:
                                        $first = '';
                                        $second='';
                                        $third='Selected';
                                    break;
                                endswitch;
                            ?>
                            <option <?php echo $first ?> value="1">First</option>
                            <option <?php echo $second ?> value="2">Second</option>
                            <option <?php echo $third ?> value="3">Summer</option>
                        </select>
                 </div>
                <div class="form-group pull-right">
                        <select onclick="getStudentByYear(this.value)" tabindex="-1" id="inputSY" style="width:200px; font-size: 15px;">
                            <option>School Year</option>
                            <?php 
                                  foreach ($ro_year as $ro)
                                   {   
                                      $roYears = $ro->ro_years+1;
                                      if($this->session->userdata('school_year')==$ro->ro_years):
                                          $selected = 'Selected';
                                      else:
                                          $selected = '';
                                      endif;
                                  ?>                        
                                <option <?php echo $selected; ?> value="<?php echo $ro->ro_years; ?>"><?php echo $ro->ro_years.' - '.$roYears; ?></option>
                                <?php }?>
                        </select>
                 </div>
            <?php } ?>
        </h3>
    </div>
</div> 
<div class="row" id="student-table" >
    <div class="col-lg-12">
        <h4 class="text-center"><strong>Enrollment Report</strong></h4>
        <h4 class="text-center">School Year <span id="sy"></span></h4>
    </div>
    <div class="col-lg-12">
        <div class="col-lg-6">
            <h5>4 YEARS PROGRAM</h5>
            <table id="fourYear" class="table table-stripped">
                <tr>
                    <th>YR LEVEL</th>
                    <?php 
                        $course = Modules::run('college/getCoursePerNumYear', 4);
                        foreach ($course as $c):
                    ?>
                    <th class="text-center"><?php echo $c->short_code ?></th>
                    <?php        
                        endforeach;
                    ?>
                    <th class="text-center">TOTAL</th>
                </tr>
                <tr>
                    <td>1ST YEAR</td><?php 
                        foreach ($course as $c):
                            $first = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id, 1);
                            $firstTotal += $first;
                    ?>
                    <th class="text-center thValue"><?php echo $first ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center thValue"><?php echo $firstTotal ?></th>
                </tr>
                <tr>
                    <td>2ND YEAR</td>
                    <?php 
                        foreach ($course as $c):
                            $second = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id, 2);
                            $secondTotal += $second;
                    ?>
                    <th class="text-center thValue"><?php echo $second ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center thValue"><?php echo $secondTotal ?></th>
                </tr>
                <tr>
                    <td>3RD YEAR</td>
                    <?php 
                        foreach ($course as $c):
                            $third = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id, 3);
                            $thirdTotal += $third;
                    ?>
                    <th class="text-center thValue"><?php echo $third ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center thValue"><?php echo $thirdTotal ?></th>
                </tr>
                <tr>
                    <td>4TH YEAR</td>
                    <?php 
                        foreach ($course as $c):
                            $fourth = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id, 4);
                            $fourthTotal += $fourth;
                    ?>
                    <th class="text-center thValue"><?php echo $fourth ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center thValue"><?php echo $fourthTotal ?></th>
                </tr>
                <tr class="totalColumn">
                    <td>TOTAL</td>
                    <?php 
                        foreach ($course as $c):
                            $courseTotal = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id);
                            $overAllTotal += $courseTotal;
                    ?>
                    <th class="text-center thTotal"><?php echo $courseTotal ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center"><?php echo $overAllTotal ?></th>
                </tr>
                
            </table>
        </div>
        
    </div>
    <div class="col-lg-12">
        <div class="col-lg-6">
            <h5>2 YEARS PROGRAM</h5>
            <table id="fourYear" class="table table-stripped">
                <tr>
                    <th>YR LEVEL</th>
                    <?php 
                        $course2 = Modules::run('college/getCoursePerNumYear', 2);
                        foreach ($course2 as $c):
                    ?>
                    <th class="text-center"><?php echo $c->short_code ?></th>
                    <?php        
                        endforeach;
                    ?>
                    <th class="text-center">TOTAL</th>
                </tr>
                <tr>
                    <td>1ST YEAR</td><?php 
                        foreach ($course2 as $c):
                            $first2 = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id, 1);
                            $firstTotal2 += $first2;
                    ?>
                    <th class="text-center thValue"><?php echo $first2 ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center thValue"><?php echo $firstTotal2 ?></th>
                </tr>
                <tr>
                    <td>2ND YEAR</td>
                    <?php 
                        foreach ($course2 as $c):
                            $second2= Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id, 2);
                            $secondTotal2 += $second2;
                    ?>
                    <th class="text-center thValue"><?php echo $second2 ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center thValue"><?php echo $secondTotal2 ?></th>
                </tr>
                
                <tr class="totalColumn">
                    <td>TOTAL</td>
                    <?php 
                        foreach ($course2 as $c):
                            $courseTotal2 = Modules::run('college/getTotalCollegeStudents',$school_year, $sem, 1, $c->course_id);
                            $overAllTotal2 += $courseTotal2;
                    ?>
                    <th class="text-center thTotal"><?php echo $courseTotal2 ?></th>
                    <?php        
                        endforeach;
                    ?> 
                    <th class="text-center"><?php echo $overAllTotal2 ?></th>
                </tr>
                
            </table>
        </div>
        
    </div>
</div>


<script type="text/javascript">
    
    $(document).ready(function() {
      
      switch($('#inputSem').val()){
          case '1':
              var sem = '1st Semester'; 
          break;
          case '2':
              var sem = '2nd Semester'; 
          break; 
          case '3':
              var sem = 'Summer'; 
          break; 
      }
      
      $("#inputSY").select2();
      
      $('#sy').html($('#inputSY').val()+' - '+(parseInt($('#inputSY').val())+parseInt(1))+' '+sem);
  });  

    function scanStudents(value)
    {
         var url = "<?php echo base_url().'college/scanStudent/'?>"+value; // the script where you handle the form input.
             $.ajax({
                   type: "POST",
                   url: url,
                   dataType:'json',
                   data: "value="+value+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
                   success: function(data)
                   {
                       $('#rfid').val('');
                       document.location = '<?php echo base_url('college/viewCollegeDetails/') ?>'+data.st_id
                       //console.log(data)
                   }
                 });

            return false;  
    }
</script>
