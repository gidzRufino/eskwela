 <script type="text/javascript">
      $(function(){
            $("#sorter").tablesorter({debug: true});
           // $('#num_students').html('[ '+<?php //echo $num_of_students ?>+' ]');
	});
      
      function search(value)
      {
          var sy = $('#inputSY').val();
          var option = $('#searchOption').val()
          $('#verify_icon').removeClass('fa-search')
          $('#verify_icon').addClass('fa-spinner fa-spin');
          
          var url = '<?php echo base_url().'search/getStudents/' ?>'+option+'/'+value+'/'+sy;
            $.ajax({
               type: "GET",
               url: url,
               data: "id="+value, // serializes the form's elements.
               success: function(data)
               {
                   if(data!=""){
                       $('#studentTable').html(data)   
                       $('#verify_icon').removeClass('fa-spinner fa-spin')
                       $('#verify_icon').addClass('fa-search');
                   }else{
                         
                   }
                   
                     
               }
             });

        return false;
      }
      
      
    function getSearchOption(value)
      {
          switch(value)
          {
              case 'profile_students_admission.grade_level_id':
                  $('#grade').show()
                  $('#searchBox').hide();
                  $('#section').hide()
              break;
              case 'profile_students_admission.section_id':
                  $('#section').show();
                  $('#grade').hide();
                  $('#searchBox').hide();
              break;
              default:
                  $('#grade').hide()
                  $('#section').hide()
                  $('#searchBox').show();
              break;
          }
      }
      
</script>

<?php 
    switch ($this->uri->segment(2)){
        case 'getAllStudentsBySection':
            $gradeSection = $section_id;
            $option = 'section';
         break;
        case 'getAllStudentsByGradeLevel':
            $gradeSection = $grade_id;
            $option = 'level';
        break;
    
        case "" :
            
        break;
        
        default :
            $gradeSection = "";
            $option ="default";
        break;    
    }
?>
<div class="col-lg-12">
    <div id="links" class="pull-left">
        <?php echo $links; ?>
    </div>
    <div class="pull-right">
        <div class="pull-left">
                <h5 style="margin:0;">Search By:
                <select id="searchOption" onclick="getSearchOption(this.value)" style="width:150px; margin-right:5px; height:40px;">
                    <option>Select Option</option>
                    <option value="st_id">Student ID</option>
                    <option value="profile_students_admission.grade_level_id">Grade Level</option>
                    <option value="profile_students_admission.section_id">Section</option>
                    <option selected="selected" value="lastname">Last Name</option>
                    <option value="firstname">First Name</option>
                    <option value="barangay">Barangay</option>
                    <option value="mun_city">City</option>
                </select>
                 </h5>
            </div>
        <div class="pull-left">
            <div class="form-group pull-right" id="section" style="display: none;">
                        <select onclick="search(this.value)" tabindex="-1" id="inputSection" style="width:200px; font-size: 15px;" class="populate select2-offscreen span2">
                            <option>Search By Section</option>
                            <?php 
                                      foreach ($section->result() as $sec)
                                   {   
                                  ?>                        
                                <option value="<?php echo $sec->section_id; ?>"><?php echo $sec->level.' [ '.$sec->section.' ]'; ?></option>
                                <?php }?>
                        </select>
                 </div>
                <div class="form-group pull-right" id="grade" style=" display: none;">
                        <select onclick="search(this.value)" tabindex="-1" id="inputGrade" style="width:200px; font-size: 15px;" class="populate select2-offscreen span2">
                            <option>Search Grade level here</option>
                            <?php 
                                  foreach ($grade as $level)
                                   {   
                                  ?>                        
                                <option value="<?php echo $level->grade_id; ?>"><?php echo $level->level; ?></option>
                                <?php }?>
                        </select>
                 </div>
             <div class="form-group input-group " id="searchBox" >
                    <input type="hidden" id="gradeSection" value="<?php echo $gradeSection?>" />
                    <input style="width:250px;" onkeyup="search(this.value)" class="form-control" id="verify" placeholder="Search" type="text">
                    <span class="input-group-btn">
                        <button class="btn btn-default">
                            <i id="verify_icon" class="fa fa-search"></i>
                        </button>   
                        <button href="#chartDetails" data-toggle="modal" class="btn btn-default">
                            <i id="chart_details" class="fa fa-bar-chart"></i>
                        </button>   
                    </span> 
            </div>
        </div>
        
    </div>
</div>
<div id="studentTable" class="row table-responsive" style="margin:0 1%;">
    <table style="font-size:12px;" class="tablesorter table table-striped">
        <thead style="background:#E6EEEE;">
            <tr>
                <th>Image</th>
                <th>USER ID</th>
                <th>LAST NAME</th>
                <th>FIRST NAME</th>
                <th>MIDDLE NAME</th>
                <th>GRADE</th>
                <th>SECTION</th>
                <th>GENDER</th>
                <td>STATUS</td>
                <?php
                    if($this->session->userdata('is_admin')):
                ?>
                <td>Action</td>
                <?php
                    endif;
                ?>
                
                <td>School Year</td>
            </tr> 
        </thead>

        <?php
           $settings = Modules::run('main/getSet');
           foreach ($students as $s)
           {
        ?>
            <tr>
                <td style="width:60px; text-align: center;">
                    <?php if(file_exists('uploads/'.$s->avatar)): ?>
                        <img class="img-circle" style="width:50px;" src="<?php echo base_url().'uploads/'.$s->avatar  ?>" />
                    <?php else: ?>
                        <img class="img-circle" style="width:50px;" src="<?php echo base_url().'images/forms/'. strtolower($settings->set_logo)  ?>" />
                    <?php endif; ?>
                </td>
                <td><a href="<?php echo base_url('registrar/viewDetails/'.base64_encode($s->uid)) ?>/<?php echo $s->ro_years ?>"><?php echo $s->uid; ?></a></td>
                <td><?php echo strtoupper($s->lastname); ?></td>
                <td><?php echo strtoupper($s->firstname); ?></td>
                <td><?php echo strtoupper($s->middlename); ?></td>
                <td><?php echo $s->level; ?></td>
                <td><?php echo $s->section; ?></td>
                <td><?php echo $s->sex; ?></td>
                <td id="img_<?php echo $s->uid ?>_td" style="text-align:center"><?php
                    //echo $s->stats;
                    if($s->status){
                        ?>
                    <a href="#adminRemarks" data-toggle="modal">
                        <img onclick="getRemarks('<?php echo $s->st_id ?>','<?php echo $s->psid ?>')" style="cursor: pointer;width:20px" src="<?php echo base_url() ?>images/official.png" alt="official" />
                    </a>
                    <?php
                    }else{
                    ?>
                    <a href="#adminRemarks" data-toggle="modal">
                        <img onclick="getRemarks('<?php echo $s->st_id ?>','<?php echo $s->psid ?>')" style="cursor: pointer;width:20px"  src="<?php echo base_url() ?>images/unofficial.png" alt="official" />
                    </a>
                    <?php
                    }
                ?>
                </td>
                
                <?php
                    if($this->session->userdata('is_admin') || $this->session->userdata('position')=='Admin Officer'):
                ?>
                <td>
                    <?php if($s->rfid==""||$s->rfid=="NULL"):?>
                    <a href="#addId" data-toggle="modal" onclick="showAddRFIDForm('<?php echo $s->psid ?>','RFID')" >Add RFID</a> |
                    <?php else: ?>
                    <a href="#addId" data-toggle="modal" onclick="showAddRFIDForm('<?php echo $s->psid ?>','<?php echo $s->rfid ?>')" >Edit RFID</a> |
                    <?php endif; ?>
                    <a href="#deleteIDConfirmation" data-toggle="modal" onclick="showDeleteConfirmation('<?php echo $s->uid ?>','<?php echo $s->psid ?>')" style="color:#FF3030;" >DELETE</a>

                </td>
                <td>
                    <?php echo $s->ro_years.' - '.($s->ro_years+1) ?>
                </td>
                <?php
                    endif;
                ?>
        </tr> 
        <?php 
            } 
            
        ?>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="addId" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h3 id="myModalLabel">Scan Students Identification Card</h3>
        </div>
        <div class="modal-body">
          <div class="control-group">
            <label class="control-label" for="input">CARD NUMBER:</label>
            <div class="controls">
              <input type="text" id="inputCard" onclick="this.value=''" placeholder="RFID" required>
              <input type="hidden" id="stud_id" >
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn"  data-dismiss="modal" >Close</button>
          <button onclick="updateProfile('<?php echo base64_encode('user_id') ?>','<?php echo base64_encode('esk_profile')?>','rfid')" class="btn btn-primary">Save </button>
          <div id="resultSection" class="help-block" ></div>
        </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteIDConfirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
                <button type="button" class="close"  data-dismiss="modal"  aria-hidden="true">&times;</button>
              <h4 id="myModalLabel">[ Delete Roll Over ] : ID Verifications</h4>
            </div>
            <div class="modal-body">
              <div class="control-group">
                <label class="control-label" for="input">ENTER EMPLOYEE ID #:</label>
                <div class="controls">
                  <input type="text" id="user_id" onclick="this.value=''" placeholder="ID #:" required>
                  <input type="hidden" id="stud_id" >
                  <input type="hidden" id="sy" value="<?php echo $this->uri->segment(3) ?>" >
                </div>
              </div>
                <div class="row-fluid">
                    <h6>Action: <br />To Delete student id( <span id="sp_stud_id"></span> )</h6>
                    <input type="checkbox" id="deleteAll" onclick="deleteAll($('#sp_stud_id').html())"/>
                </div>
            </div>
            <div class="modal-footer">
              <button class="btn" onclick="$('#deleteAll').prop('checked', false)"  data-dismiss="modal" >Close</button>
              <button onclick="deleteROStudent()" class="btn btn-danger">CONFIRM DELETE </button>
              <div id="resultSection" class="help-block" ></div>
            </div>
        </div>
      </div>
    </div>


<?php echo Modules::run('main/showAdminRemarksForm') ?>
