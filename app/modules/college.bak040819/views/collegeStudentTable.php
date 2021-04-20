<script type="text/javascript">
      $(function(){
            $("#sorter").tablesorter({debug: true});
           // $('#num_students').html('[ '+<?php //echo $num_of_students ?>+' ]');
	});
      
      function search(value)
      {
          var option = $('#searchOption').val()
          $('#verify_icon').removeClass('fa-search')
          $('#verify_icon').addClass('fa-spinner fa-spin');
          
          var url = '<?php echo base_url().'search/getCollegeStudents/' ?>'+option+'/'+value;
            $.ajax({
               type: "GET",
               url: url,
               data: "id="+value, // serializes the form's elements.
               success: function(data)
               {
                   if(data!=""){
                       $('#links').hide()
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
<div class="col-lg-12">
    <div id="links" class="pull-left">
        <?php echo $links; ?>
    </div>
    <div class="pull-right">
        <div class="pull-left">
                <h5 style="margin:0;">Search By:
                <select id="searchOption" onclick="getSearchOption(this.value)" style="width:150px; margin-right:5px; height:40px;">
                    <option>Select Option</option>
                    <option value="esk_profile_students.st_id">Student ID</option>
                    <option value="esk_c_courses.course">Course</option>
                    <option selected="selected" value="lastname">Last Name</option>
                    <option value="firstname">First Name</option>
                    <option value="barangay">Barangay</option>
                    <option value="mun_city">City</option>
                </select>
                 </h5>
            </div>
        <div class="pull-left">
             <div class="form-group input-group " id="searchBox" >
                    <input style="width:250px;" onkeyup="search(this.value)" class="form-control" id="verify" placeholder="Search" type="text">
                    <span class="input-group-btn">
                        <button class="btn btn-default">
                            <i id="verify_icon" class="fa fa-search"></i>
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
                <th></th>
                <th>USER ID</th>
                <th>LAST NAME</th>
                <th>FIRST NAME</th>
                <th>MIDDLE NAME</th>
                <th>GENDER</th>
                <th>COURSE</th>
                <th>YEAR</th>
                <th>School Year</th>
                <th>Semester</th>
                <td>STATUS</td>
                <?php
                    if($this->session->userdata('is_admin')):
                ?>
                <td>Action</td>
                <?php
                    endif;
                ?>
            </tr> 
        </thead>

        <?php
           foreach ($students as $s)
           {
           switch ($s->year_level):
                case 1:
                    $year_level = 'First Year';
                break;
                case 2:
                    $year_level = 'Second Year';
                break;
                case 3:
                    $year_level = 'Third Year';
                break;
                case 4:
                    $year_level = 'Fourth Year';
                break;
                case 5:
                    $year_level = 'Fifth Year';
                break;
            endswitch;
            
            switch ($s->semester):
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
            <tr>
                <td style="width:60px; text-align: center;"><img class="img-circle" style="width:50px;" src="<?php echo base_url().'uploads/'.($s->avatar==""?'noImage.png':$s->avatar)  ?>" /></td>
                <td><a href="<?php echo base_url('college/viewCollegeDetails/'.base64_encode($s->st_id)).'/'.$s->semester ?>"><?php echo $s->st_id; ?></a></td>
                <td><?php echo strtoupper($s->lastname); ?></td>
                <td><?php echo strtoupper($s->firstname); ?></td>
                <td><?php echo strtoupper($s->middlename); ?></td>
                <td><?php echo $s->sex; ?></td>
                <td><?php echo $s->short_code; ?></td>
                <td><?php echo $year_level; ?></td>
                <td>
                    <?php echo $s->school_year.' - '.($s->school_year+1) ?>
                </td>
                <td><?php echo $semester; ?></td>
                <td class="text-center">
                    <?php 
                        if($s->status){
                            ?>
                        <a href="#adminRemarks" data-toggle="modal">
                            <img onmouseover="$('#input_sem').val('<?php echo $s->semester ?>'), $('#us_id').val('<?php echo $s->u_id ?>')" style="cursor: pointer;width:20px" src="<?php echo base_url() ?>images/official.png" alt="official" />
                        </a>
                        <?php
                        }else{
                        ?>
                        <a href="#adminRemarks" data-toggle="modal">
                            <img style="cursor: pointer;width:20px"  src="<?php echo base_url() ?>images/unofficial.png" alt="official" />
                        </a>
                        <?php
                        }
                    ?>
                </td>
                <?php
                    if($this->session->userdata('is_admin')):
                ?>
                <td style="width:100px;">
                    <?php if($s->rfid==""||$s->rfid=="NULL"):?>
                    <a href="#addId" data-toggle="modal" onclick="showAddRFIDForm('<?php echo $s->u_id ?>','RFID')" >Add RFID</a> <br />
                    <?php else: ?>
                    <a href="#addId" data-toggle="modal" onclick="showAddRFIDForm('<?php echo $s->u_id ?>','<?php echo $s->rfid ?>')" >Edit RFID</a> <br />
                    <?php endif; ?>
                    <a href="#deleteIDConfirmation" data-toggle="modal" onclick="showDeleteConfirmation('<?php echo $s->uid ?>','<?php echo $s->psid ?>','<?php echo $s->admission_id ?>')" style="color:#FF3030;" >DELETE</a><br />
                    
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

<div class="modal fade" id="adminRemarks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" >&times;</button>
          <h3 id="myModalLabel">Update Student Status</h3>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label class="control-label" for="input">Remarks:</label>
            <div class="controls">
              <input name="dateRemarked" type="hidden" id="dateRemarked" placeholder="Date" value="<?php echo date('m-d-Y'); ?>">
              <select id="inputRemarks" style="width:300px">
                 <option>Select Student Status</option>         
                    <option value ="0" >Dropped</option>
                    <option value ="1" >Old</option>
                    <option value ="2" >New</option>
                    <option value ="3" >Returnee</option>
                    <option value ="4" >Transferee</option>
             </select>
            </div>
            <input type="hidden" id="st_id" name="st_id" value="" />
            <input type="hidden" id="us_id" name="user_id" value="" />
            <input type="hidden" id="input_sem" name="input_sem" value="" />
          </div>
                       
        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" onclick="submitRemarks(), $('#secretContainer').fadeOut(500)" class="btn btn-primary">Save </button>
          <div id="resultSection" class="help-block"></div>
        </div>
    </div>
  </div>
</div>


<script type="text/javascript">
    function submitRemarks()
    {
        var url = "<?php echo base_url().'college/saveAdmissionRemarks/'?>"; // the script where you handle the form input.
        var user_id = $('#us_id').val()
        var sem = $('#input_sem').val()
        var code = $('#inputRemarks').val()
            $.ajax({
                   type: "POST",
                   url: url,
                   data: "code="+code+"&st_id="+$('#st_id').val()+"&user_id="+user_id+"&semester="+sem+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
                   success: function(data)
                   {
                       $('#remarks_'+st_id+"_td").html(data);
                       if(code > 0){
                           $('#img_'+st_id+"_td img").attr("src",'<?php echo base_url();?>images/unofficial.png');
                       }else{
                           $('#img_'+st_id+"_td img").attr("src",'<?php echo base_url();?>images/official.png');
                       }
                       
                   }
                 });

            return false;
    }
</script>
