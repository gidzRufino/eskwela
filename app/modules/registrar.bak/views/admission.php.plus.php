<?php
   $sy = $settings->school_year;
    
   $is_admin = $this->session->userdata('is_admin');
?>
<div class="row">
    <div class="col-lg-12">
        <h2 style="margin-top: 20px;" class="page-header">Admission <small id="schoolID">( <?php echo $sy ?> ) <span style="cursor: pointer;" onclick="$('#schoolID').fadeOut(500), $('#inputLRN').fadeIn(500),$('#inputLRN').focus(),$('#inputLRN').val('')" >[Set Student ID Manually]</span></small>
        <button id="saveAdmission" class="btn btn-sm btn-success pull-right "><i class="fa fa-save fa-fw fa-2x"></i></button>
        <button onclick="document.location='<?php echo base_url('main/dashboard') ?>'" style="margin-right:5px;" class="btn btn-sm btn-warning pull-right"><i class="fa fa-close fa-fw fa-2x"></i></button> 
        </h2>
    </div>
</div>

    <div class="formHeader row">
        <?php
            $attributes = array('class' => '','role'=>'form', 'id'=>'admissionForm');
            echo form_open(base_url().'registrar/saveAdmission', $attributes);
        ?>
        <div>
           <input class="form-control" style="display:none; margin-bottom: 10px;" onkeyup="checkID(this.value)" name="inputLRN" type="text" id="inputLRN" placeholder="Enter Student ID (LRN)">
            <small class="help-inline hide" style="color:red;" id="infoReply"></small>
            <input name="inputIdNum"  class="pull-left" style="width:200px;" value="" type="hidden" id="inputIdNum" placeholder="<?php echo $sy ?>"> 
            
            <div class="panel panel-green">
                <div class="panel-heading">
                    Personal Information
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    
                    <div class="col-lg-12">
                        <div class="form-group col-lg-3">
                            <label>First Name</label>
                              <?php
                                 $inputFirstName = array(
                                                'name'        => 'inputFirstName',
                                                'id'          => 'inputFirstName',
                                                'class'          => 'form-control',
                                                'placeholder'  => 'First Name',
                                                'onkeypress' => "notEmpty(document.getElementById('inputGrade'), 'Please Select Grade Level')",
                                                'style' => 'margin-bottom:0;'


                                              );

                                  echo form_input($inputFirstName);

                                ?>    
                        </div>
                      <div class="form-group col-lg-3">
                        <label>Middle Name</label>

                          <?php
                             $inputMiddleName = array(
                                            'name'        => 'inputMiddleName',
                                            'id'          => 'inputMiddleName',
                                            'class'       => 'form-control',
                                            'placeholder' => 'Middle Name',
                                            'onkeypress' => "notEmpty(document.getElementById('inputFirstName'), 'Please input First Name')",
                                            'style' => 'margin-bottom:0;'
                                          );

                              echo form_input($inputMiddleName);

                            ?> 
                      </div>

                      <div class="form-group  col-lg-3">
                        <label>Last Name</label>
                          <?php
                             $inputLastName = array(
                                            'name'        => 'inputLastName',
                                            'id'          => 'inputLastName',
                                            'class'          => 'form-control',
                                            'placeholder'  => 'Last Name',
                                            'onkeypress' => "notEmpty(document.getElementById('inputMiddleName'), 'Please input Middle Name')",
                                            'style' => ' margin-right:10px; margin-bottom:0;'
                                          );

                              echo form_input($inputLastName);

                            ?> 
                      </div>

                      <div class="form-group col-lg-3">
                          <label>Grade  Level</label><br />
                             <select style="height:35px; width: 200px;" name="inputGrade" onclick="selectSection(this.value), setId(this.value)" id="inputGrade" required>
                                  <option>Select Grade Level</option> 
                                    <?php 
                                           foreach ($grade as $level)
                                             {   
                                       ?>                        
                                            <option value="<?php echo $level->grade_id; ?>"><?php echo $level->level; ?></option>
                                    <?php }?>
                                </select>
                      </div> 
                    </div>
                    
                    <div class="col-lg-12">
                       <div class="form-group col-lg-3">
                            <label>Section</label><br />
                            <div id="AddedSection">
                              <select style="height:35px; width: 225px;"  name="inputSection" id="inputSection" required>
                                  <option>Select Section</option>  
                              </select>
                            </div>
                        </div>

                        <div class="form-group col-lg-3">
                            <label>Date of Birth</label>
                            <input style="margin-right: 10px;" class="form-control" name="inputBdate" type="text" data-date-format="mm-dd-yyyy" id="inputBdate" placeholder="Date of Birth" required>

                        </div>

                        <div class="form-group col-lg-3">
                            <label>Place of Birth</label>
                            <input  style="margin-right: 10px;" class="form-control"  name="inputPlaceOfBirth" type="text" id="inputPlaceOfBirth" placeholder="Place of Birth" required>
                        </div>


                        <div class="form-group col-lg-3">
                            <label class="control-label" for="inputNationaly">Nationality</label>
                            <input class="form-control"  name="inputNationality" type="text" id="inputNationality" placeholder="Nationality">

                        </div> 
                    </div>    
                    
                    <div class="col-lg-12">
                        <div class="form-group col-lg-3">
                            <label class="control-label" for="inputReligion">Religion</label>
                            <div class="controls">
                                <select name="inputReligion" id="inputreligion" style="width:225px;" required>
                                <option>Select Religion</option>
                                <?php 
                                    foreach ($religion as $r)
                                      {   
                                    ?>                        
                                  <option value="<?php echo $r->rel_id; ?>"><?php echo $r->religion; ?></option>

                                  <?php } 

                                  ?>
                                </select><a class="help-inline" 
                                  rel="clickover" 
                                  data-content=" 
                                       <div style='width:100%;'>
                                       <h6>Add Religion</h6>
                                       <input type='text' id='addreligion' />
                                       <div style='margin:5px 0;'>
                                       <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                                       <a href='#' id='religion' data-dismiss='clickover' table='religion' column='religion' pk='rel_id' retrieve='getReligion' onclick='saveNewValue(this.id)' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                                       </div>
                                        "   
                                  class="btn" data-toggle="modal" href="#">[ Add Religion ]</a>
                            </div>
                        </div>     

                        <div class="form-group col-lg-3">
                            <label class="control-label" for="inputGender">Gender</label>
                            <div class="controls">
                                <select name="inputGender" id="inputGender" style="width:225px;" required>
                                    <option>Select Your Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                                
                        </div>

                        <div class="form-group col-lg-3">
                            <label class="control-label" for="addMotherTongue">Mother Tongue</label>
                                <select name="addMotherTongue" id="inputmother_tongue" style="width:225px;" class="" required>
                                <option>Select Mother Tongue</option>
                                <?php 
                                    foreach ($motherTongue as $mt)
                                      {   
                                    ?>                        
                                  <option value="<?php echo $mt->mt_id; ?>"><?php echo $mt->mother_tongue; ?></option>

                                  <?php }?>
                                </select><a class="help-inline pull-left" 
                                  rel="clickover" 
                                  data-content=" 
                                       <div style='width:100%;'>
                                       <h6>Add Mother Tongue</h6>
                                       <input type='text' id='addmother_tongue' />
                                       <div style='margin:5px 0;'>
                                       <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                                       <a href='#' id='mother_tongue' data-dismiss='clickover' onclick='saveNewValue(this.id)' table='mother_tongue' column='mother_tongue' pk='mt_id' retrieve='getMotherTongue' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                                       </div>
                                        "   
                                  class="btn" data-toggle="modal" href="#">[ Add Mother Tongue ]</a>
                        </div>
                        
                        <div class="form-group col-lg-3">
                            <label class="control-label" for="addMotherTongue">Ethnic Group</label>
                            <div class="controls">
                                <select name="addEthnicGroup" id="inputethnic_group" style="width:225px;" class="controls-row" required>
                                <option>Select Ethnic Group</option>
                                <?php 
                                    foreach ($ethnicGroup as $eg)
                                      {   
                                    ?>                        
                                  <option value="<?php echo $eg->eg_id; ?>"><?php echo $eg->ethnic_group; ?></option>

                                  <?php }?>
                                </select><a class="help-inline pull-left" 
                                  rel="clickover" 
                                  data-content=" 
                                       <div style='width:100%;'>
                                       <h6>Add Ethnic Group</h6>
                                       <input type='text' id='addethnic_group' />
                                       <div style='margin:5px 0;'>
                                       <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                                       <a href='#' id='ethnic_group' data-dismiss='clickover' onclick='saveNewValue(this.id)' table='ethnic_group' column='ethnic_group' pk='eg_id' retrieve='getEthnicGroup' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                                       </div>
                                        "   
                                  class="btn" data-toggle="modal" href="#">[ Add Ethnic Group ]</a>
                            </div>
                        </div> 
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="form-group col-lg-3">
                            <label class="control-label" for="inputBirthDate">Date Enrolled</label>
                            <input class="form-control" name="inputEdate" type="text" value="<?php echo date('m-d-Y') ?>" data-date-format="mm-dd-yyyy" id="inputEdate" placeholder="Date Enrolled" required>

                        </div> 
                        <div class="form-group col-lg-3">
                            <label class="control-label" for="inputBirthDate">School Year</label>
                            <select onclick="getStudentByYear(this.value)" tabindex="-1" id="inputSY" name="inputSY" style="width:200px; font-size: 15px;">
                               <option>School Year</option>
                               <?php 
                                     foreach ($ro_year as $ro)
                                      {   
                                         $roYears = $ro->ro_years+1;
                                         if($this->uri->segment(3)==$ro->ro_years):
                                             $selected = 'Selected';
                                         else:
                                             $selected = '';
                                         endif;
                                     ?>                        
                                   <option <?php echo $selected; ?> value="<?php echo $ro->ro_years; ?>"><?php echo $ro->ro_years.' - '.$roYears; ?></option>
                                   <?php }?>
                           </select>
                        </div> 

                          <div class="form-group col-lg-3">
                            <label class="control-label" >School Last Attended:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputSLA" type="text" id="inputSLA" placeholder="School Last Attended">
                          </div>
                          <div class="form-group col-lg-3">
                            <label class="control-label" >Address of School Last Attended:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputAddressSLA" type="text" id="inputAddressSLA" placeholder="Address">
                          </div>
                    </div>
                 
                    
                  
                </div>
                <!-- /.panel-body -->
            </div>
            <div class="panel panel-primary ">
                <div class="panel-heading">
                    Contact Information
                </div>
                <div class="panel-body ">
                    <div class="col-lg-12">
                        <div class="form-group col-lg-3">
                            <label class="control-label" for="inputAddress">Street</label>
                              <input style="margin-bottom:0;" class="form-control" name="inputStreet" type="text" id="inputStreet" placeholder="Street" required>
                          </div>

                          <div class="form-group col-lg-3">
                            <label class="control-label" for="inputAddress">Barangay:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputBarangay" type="text" id="inputBarangay" placeholder="Barangay" required>
                          </div>

                          <div class="form-group col-lg-3">
                            <label class="control-label" for="inputCity">City / Municipality:</label>
                            <select onclick="getProvince(this.value)" placeholder="Select A Municipality / City" class="populate select2-offscreen " style="width:100%;" multiple="" id="inputMunCity" name="inputMunCity">
                                <?php foreach($cities as $city): ?>
                                    <option value="<?php echo $city->cid ?>"><?php echo $city->mun_city.' [ '.$city->province.' ]' ?></option>
                                <?php endforeach; ?>
                            </select>  
                            <!--<input style="margin-bottom:0;" class="form-control"  name="inputMunCity" class="select2-search" type="text" id="inputMunCity" placeholder="City / Municipality" required>-->
                          </div>

                          <div class="form-group col-lg-3">
                            <label class="control-label" for="inputState">Province:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputProvince" type="text" id="inputProvince" placeholder="State / Province" required>
                              <input style="margin-bottom:0;" class="form-control"  name="inputPID" type="hidden" id="inputPID" placeholder="State / Province" required>
                          </div>

                          <div class="form-group col-lg-3">
                            <label class="control-label" for="inputPostal">Postal Code:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputPostal" type="text" id="inputPostal" placeholder="Postal Code" required>
                          </div>

                          <div class="form-group col-lg-3">
                            <label class="control-label" for="inputContact">Contact Number:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputPhone" type="text" id="inputPhone" placeholder="Phone">
                          </div>

                          <div class="form-group col-lg-3">
                            <label class="control-label" for="inputEmail">Email:</label>
                              <input style="margin-bottom:0;" class="form-control"  name="inputEmail" type="text" id="inputEmail" placeholder="Email">
                          </div>
                    </div>
                </div>
                
            </div>
            <div class="panel panel-yellow ">
                <div class="panel-heading">
                    Parent / Guardian Information:
                    <div class="btn-group" style="margin-left:10px;" data-toggle="buttons-radio">
                        <button onclick="selectPG(0)" type="button" class="btn btn-sm btn-default active">Parent</button>
                        <button onclick="selectPG(1)"type="button" class="btn btn-sm btn-default">Guardian</button>
                        <input name="pgSelect" type="hidden" id="pgSelect" /> 
                    </div>
                </div>
                
                <div class="panel-body ">
                    <div id="Parents">
                        <div class="col-lg-12">
                            <div class="form-group col-lg-3">
                                <label class="control-label" for="inputFather">Father's First Name</label>
                                <input name="inputFExist" type="hidden" id="inputFExist" placeholder="Father's First and Middle Name" required>
                                <input style="margin-bottom:0;" class="form-control"   name="inputFName" type="text" id="inputFName" placeholder="Father's First Name" required>
                            </div>

                            <div class="form-group col-lg-3">
                              <label class="control-label" for="inputF_occ">Father's Middle Name:</label>
                                <input style="margin-bottom:0;" class="form-control"   name="inputFMName" type="text" id="inputFMName" placeholder="Father's Middle Name" required>
                            </div>

                            <div class="form-group col-lg-3">
                              <label class="control-label" for="inputF_occ">Father's Last Name:</label>
                                <input style="margin-bottom:0;" class="form-control"   name="inputFLName" type="text" id="inputFLName" placeholder="Father's Last Name" required>
                            </div>

                            <div class="form-group col-lg-3">
                              <label class="control-label" for="inputF_occ">Profession / Occupation:</label>
                                <input style="margin-bottom:0;" class="form-control"   name="inputF_occ" type="text" id="inputF_occ" placeholder="Father's Occupation" required>
                            </div>

                            <div class="form-group col-lg-3">
                              <label class="control-label" for="inputF_num">Contact Number:</label>
                                <input style="margin-bottom:0;" class="form-control"   name="inputF_num" type="text" id="inputF_num" placeholder="Father's Contact Number" required>
                            </div>

                            <div class="form-group col-lg-3">
                              <label class="control-label" for="inputPEmail">Email:</label>
                                <input style="margin-bottom:0;" class="form-control"   name="inputPEmail" type="text" id="inputPEmail" placeholder="Father's Email" required>
                            </div>

                             <div class="form-group  col-lg-3">
                              <label class="control-label" for="inputFather">Educational Attainment:</label>
                                  <select name="inputFeduc" id="inputFeduc" style="width:100%; height:50px;" required>
                                      <option>Select Educational Attainment</option> 
                                      <?php foreach ($educ_attain as $EA) { ?>
                                         <option value="<?php echo $EA->ea_id ?>"><?php echo $EA->attainment ?></option> 
                                      <?php }?>

                                   </select>
                            </div>
                            <div class="form-group col-lg-3">
                                    <label class="control-label" for="inputAddress">Father's Office Name</label>
                                      <input style="margin-bottom:0;" class="form-control" name="f_officeName" type="text" id="f_officeName" placeholder="Office Name" required>
                            </div>
                            <div class="col-lg-12 no-padding">
                                <h5 style="margin:0;">Father's Office Address:</h5>
                                <div class="form-group col-lg-3">
                                    <label class="control-label" for="inputAddress">Street</label>
                                      <input style="margin-bottom:0;" class="form-control" name="f_officeStreet" type="text" id="f_officeStreet" placeholder="Street" required>
                                  </div>

                                  <div class="form-group col-lg-3">
                                    <label class="control-label" for="inputAddress">Barangay:</label>
                                      <input style="margin-bottom:0;" class="form-control"  name="f_officeBarangay" type="text" id="f_officeBarangay" placeholder="Barangay" required>
                                  </div>

                                  <div class="form-group col-lg-3">
                                    <label class="control-label" for="inputCity">City / Municipality:</label>
                                    <select onclick="getFofficeProvince(this.value)" placeholder="Select A Municipality / City" class="populate select2-offscreen " style="width:100%;" multiple="" id="f_officeMunCity" name="f_officeMunCity">
                                        <?php foreach($cities as $city): ?>
                                            <option value="<?php echo $city->cid ?>"><?php echo $city->mun_city.' [ '.$city->province.' ]' ?></option>
                                        <?php endforeach; ?>
                                    </select>   
                                  </div>
                                    <div class="form-group col-lg-3">
                                      <label class="control-label" for="inputState">Province:</label>
                                        <input style="margin-bottom:0;" class="form-control"  name="f_officeProvince" type="text" id="f_officeProvince" placeholder="State / Province" required>
                                        <input style="margin-bottom:0;" class="form-control"  name="f_officePID" type="hidden" id="f_officePID" placeholder="State / Province" required>
                                    </div>
                            </div>
                        </div>
                       
                        <div class="col-lg-12" style="border-top: 1px solid gray; padding-top:10px;">
                            <div class="form-group col-lg-3">
                                <label class="control-label" for="inputMother">Mother's First Name:</label>
                                  <input style="width:220px; margin-right:10px; margin-bottom:0;" class="form-control" name="inputMother" type="text" id="inputMother" placeholder="Mother's First Name" required>
                              </div>

                              <div class="form-group col-lg-3">
                                <label class="control-label" for="inputMother">Mother's Middle Name:</label>
                                  <input style="margin-bottom:0;" class="form-control" name="inputMMName" type="text" id="inputMMName" placeholder="Mother's Middle Name" required>
                              </div>

                              <div class="form-group col-lg-3">
                                <label class="control-label" for="inputMother">Mother's Last Name:</label>
                                  <input style="margin-bottom:0;" class="form-control" name="inputMLName" type="text" id="inputMLName" placeholder="Mother's Last Name" required>
                              </div>

                              <div class="form-group col-lg-3">
                                <label class="control-label" for="inputM_num">Profession / Occupation:</label>
                                  <input style="margin-bottom:0;" class="form-control" name="inputM_occ" type="text" id="inputM_occ" placeholder="Mother's Occupation" required>
                              </div>
                              <div class="form-group col-lg-3">
                                <label class="control-label" for="inputM_occ">Contact Number:</label>
                                  <input style="margin-bottom:0;" class="form-control" name="inputM_num" type="text" id="inputM_num" placeholder="Mother's Contact Number" required>
                              </div>

                               <div class="form-group col-lg-3">
                                <label class="control-label" for="inputFather">Educational Attainment:</label>
                                    <select name="inputMeduc" id="inputMeduc" style="width:100%;" required>
                                        <option>Select Educational Attainment</option> 
                                        <?php foreach ($educ_attain as $EA) { ?>
                                        <option value="<?php echo $EA->ea_id ?>"><?php echo $EA->attainment ?></option> 
                                        <?php }?>

                                      </select>
                               </div>
                                 <div class="form-group col-lg-3">
                                    <label class="control-label" for="inputAddress">Mother's Office Name</label>
                                      <input style="margin-bottom:0;" class="form-control" name="m_officeName" type="text" id="m_officeName" placeholder="Office Name" required>
                                </div>
                                <div class="col-lg-12 no-padding">
                                    <h5 style="margin:0;">Mother's Office Address:</h5>
                                    <div class="form-group col-lg-3">
                                        <label class="control-label" for="inputAddress">Street</label>
                                          <input style="margin-bottom:0;" class="form-control" name="m_officeStreet" type="text" id="m_officeStreet" placeholder="Street" required>
                                      </div>

                                      <div class="form-group col-lg-3">
                                        <label class="control-label" for="inputAddress">Barangay:</label>
                                          <input style="margin-bottom:0;" class="form-control"  name=" " type="text" id="m_officeBarangay" placeholder="Barangay" required>
                                      </div>

                                      <div class="form-group col-lg-3">
                                        <label class="control-label" for="inputCity">City / Municipality:</label>
                                        <select onclick="getMofficeProvince(this.value)" placeholder="Select A Municipality / City" class="populate select2-offscreen " style="width:100%;" multiple="" id="m_officeMunCity" name="m_officeMunCity">
                                            <?php foreach($cities as $city): ?>
                                                <option value="<?php echo $city->cid ?>"><?php echo $city->mun_city.' [ '.$city->province.' ]' ?></option>
                                            <?php endforeach; ?>
                                        </select>   
                                      </div>
                                        <div class="form-group col-lg-3">
                                          <label class="control-label" for="inputState">Province:</label>
                                            <input style="margin-bottom:0;" class="form-control"  name="m_officeProvince" type="text" id="m_officeProvince" placeholder="State / Province" required>
                                            <input style="margin-bottom:0;" class="form-control"  name="m_officePID" type="hidden" id="m_officeProvince" placeholder="State / Province" required>
                                        </div>
                                </div>
                            </div>    
                           
                        </div>
                        <div style="display: none;" id="Guardian">
                            <div class="col-lg-12">
                                <div class="form-group col-lg-3">
                                 <label class="control-label" for="inputFather">Guardian's First Name:</label>
                                   <input name="inputGExist" type="hidden" id="inputGExist" placeholder="Father's Name" required>
                                   <input style="margin-bottom:0;" class="form-control"   name="inputGFName" type="text" id="inputGFName" placeholder="Guardian's Full Name" required>
                               </div>
                            
                               <div class="form-group col-lg-3" >
                                 <label class="control-label" for="inputG_occ">Guardian's Middle Name:</label>
                                   <input style="margin-bottom:0;" class="form-control" name="inputGMName" type="text" id="inputGMName" placeholder="Guardian's Middle Name" required>
                               </div>
                            
                               <div class="form-group col-lg-3" >
                                 <label class="control-label" for="inputG_occ">Guardian's Last Name:</label>
                                   <input style="margin-bottom:0;" class="form-control" name="inputGLName" type="text" id="inputGLName" placeholder="Guardian's Last Name" required>
                               </div>
                                
                               <div class="form-group col-lg-3" >
                                 <label class="control-label" for="inputG_occ">Relationship:</label>
                                   <input style="margin-bottom:0;" class="form-control" name="inputRelationship" type="text" id="inputRelationship" placeholder="Relation to Student" required>
                               </div>
                                <div class="form-group col-lg-3">
                                    <label class="control-label" for="inputGuardGender">Gender</label>
                                    <div class="controls">
                                        <select name="inputGuardGender" id="inputGuardGender" style="width:225px;" required>
                                            <option>Select Your Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>

                                </div>
                               <div class="form-group col-lg-3">
                                 <label class="control-label" for="inputF_num">Contact Number:</label>
                                   <input style="margin-bottom:0;" class="form-control"  name="inputG_num" type="text" id="inputG_num" placeholder="Contact Number" required>
                               </div>
                               <div class="form-group col-lg-3">
                                 <label class="control-label" for="inputPEmail">Email:</label>
                                   <input style="margin-bottom:0;" class="form-control"  name="inputGEmail" type="text" id="inputGEmail" placeholder="Email" required>
                               </div> 
                                <div class="form-group col-lg-3">
                                     <label class="control-label" for="inputFather">Educational Attainment:</label>
                                         <select name="inputGeduc" id="inputGeduc" style="width:100%;" required>
                                             <option>Select Educational Attainment</option> 
                                             <?php foreach ($educ_attain as $EA) { ?>
                                             <option value="<?php echo $EA->ea_id ?>"><?php echo $EA->attainment ?></option> 
                                             <?php }?>

                                           </select>
                                   </div>
                            </div>
                        </div>

                   </div>
            </div>  
            <div class="panel panel-red ">
                <div class="panel-heading">
                    Medical Information:
                </div>
                <div class="panel-body ">
                    <div class="col-lg-12">
                        <div class="form-group col-lg-3">
                          <label class="control-label" for="inputSSS">Blood Type:</label>
                            <input  style="margin-bottom:0;" class="form-control"  name="inputBType" type="text" id="inputBType" placeholder="Blood Type" required>
                        </div>
                        
                        <div class="form-group col-lg-3">
                          <label class="control-label" for="inputSSS">Height(in meters):</label>
                            <input  style="margin-bottom:0;" class="form-control"  name="height" type="text" id="height" placeholder="height" required>
                        </div>
                        
                        <div class="form-group col-lg-3">
                          <label class="control-label" for="inputSSS">Weight(in Kilograms):</label>
                            <input  style="margin-bottom:0;" class="form-control"  name="weight" type="text" id="weight" placeholder="weight" required>
                        </div>
                        
                        <div class="form-group col-lg-3" style="margin-right:25px; width:200px;">
                           <label class="control-label" for="Family Physician"/>Family Physician:</label> 
                           <div class="controls" id="AddedSection">
                            <select name="inputFPhy" id="inputFPhy" class=""  required>
                                <option>Search for existing Physician</option>
                                  <?php 
                                      foreach ($physician as $p)
                                        {   
                                  ?>                        
                                <option value="<?php echo $p->physician_id; ?>"><?php echo $p->physician; ?></option>
                                <?php }?>


                              </select><a class="help-inline" role="button" class="btn" data-toggle="modal" href="#addPhysician">add physician</a>
                          </div>   
                        </div>
                        <div class="form-group  col-lg-3">
                          <label class="control-label" for="inputSSS">Allergies(food, medicine, insects, plants):</label>
                            <input style="margin-bottom:0;" class="form-control"  name="inputAllergies" type="text" id="inputAllergies" placeholder="" required>
                        </div> 
                        <div class="form-group col-lg-3">
                          <label class="control-label" for="inputSSS">Other Important Medical Information:</label>
                            <input style="margin-bottom:0;" class="form-control"  name="inputOtherMedInfo" type="text" id="inputOtherInfo" placeholder="" required>
                        </div> 
                        
                    </div>
                </div>
            </div>
            
        </div>
        
        <?php
            echo form_close();
        ?>
    </div>
</div>

<script type="text/javascript">
    
       function getProvince(value)
      {
          var url = "<?php echo base_url().'main/getProvince/'?>"+value;
          $.ajax({
                       type: "GET",
                       url: url,
                       dataType:'json',
                       data: 'csrf_test_name='+$.cookie('csrf_cookie_name'),
                       success: function(data)
                       {
                           $('#inputProvince').val(data.name)
                           $('#inputPID').val(data.id)
                       }
          })
      }
    
       function getFofficeProvince(value)
      {
          var url = "<?php echo base_url().'main/getProvince/'?>"+value;
          $.ajax({
                       type: "GET",
                       url: url,
                       dataType:'json',
                       data: 'csrf_test_name='+$.cookie('csrf_cookie_name'),
                       success: function(data)
                       {
                           $('#f_officeProvince').val(data.name)
                           $('#f_officePID').val(data.id)
                       }
          })
      }
    
       function getMofficeProvince(value)
      {
          var url = "<?php echo base_url().'main/getProvince/'?>"+value;
          $.ajax({
                       type: "GET",
                       url: url,
                       dataType:'json',
                       data: 'csrf_test_name='+$.cookie('csrf_cookie_name'),
                       success: function(data)
                       {
                           $('#m_officeProvince').val(data.name)
                           $('#m_officePID').val(data.id)
                       }
          })
      }
    
      function checkID(value)
      {
          var url = "<?php echo base_url().'registrar/checkID' ?>"; // the script where you handle the form input.

                $.ajax({
                       type: "POST",
                       url: url,
                       data: 'id='+value+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
                       dataType:'json',
                       success: function(data)
                       {
                          if(data.status)
                              {
                                  $('#infoReply').html(data.msg)
                                  $('#infoReply').fadeIn()
                                  $('#inputFirstName').attr('disabled','disabled');
                                  $('#inputMiddleName').attr('disabled','disabled');
                                  $('#inputLastName').attr('disabled','disabled');
                                 // $('#inputLRN').val('');
                              }else{
                                  $('#infoReply').html(data.msg)
                                  $('#infoReply').fadeOut(5000)
                                  $('#inputFirstName').removeAttr('disabled');
                                  $('#inputMiddleName').removeAttr('disabled');
                                  $('#inputLastName').removeAttr('disabled');
                              }
                       }
                     });

                return false; // avoid to execute the actual submit of the form.
                
      }
      function selectPG(pgSelect)
      {
          if(pgSelect==0){
              document.getElementById('pgSelect').value='0'
              document.getElementById('Guardian').style.display='none'
              document.getElementById("Parents").style.display=""
          }else if(pgSelect==1){
              document.getElementById('pgSelect').value='1'
              document.getElementById("Guardian").style.display=""
              document.getElementById("Parents").style.display="none"
          }
      }
    
      function selectSection(level_id){
          var url = "<?php echo base_url().'registrar/getSectionByGL/'?>"+level_id; // the script where you handle the form input.

            $.ajax({
                   type: "POST",
                   url: url,
                   data: "level_id="+level_id+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
                   success: function(data)
                   {
                       $('#inputSection').html(data);
                   }
                 });

            return false;
      }
      
      function saveNewValue(table){
         var db_table = $('#'+table).attr('table')
         var db_column = $('#'+table).attr('column')
         var pk = $('#'+table).attr('pk')
         var retrieve = $('#'+table).attr('retrieve')
         var db_value = $('#add'+db_column).val()
         var url = "<?php echo base_url().'registrar/saveNewValue/'?>"// the script where you handle the form input.

            $.ajax({
                   type: "POST",
                   url: url,
                   data: "table="+db_table+"&column="+db_column+"&value="+db_value+"&pk="+pk+"&retrieve="+retrieve+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
                   success: function(data)
                   {
                       $('#input'+db_column).html(data)
                   }
                 });

            return false;
   
      }
      
      function setId(levelCode){
          
         var url = "<?php echo base_url().'registrar/getLatestIdNum/'?>"+levelCode; // the script where you handle the form input.

            $.ajax({
                   type: "POST",
                   url: url,
                   dataType: 'json',
                   data: "level_id="+levelCode+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
                   success: function(data)
                   {
                       //alert(data.id)
                       var id = parseInt(data.id) + parseInt(1)
                       var prefix = '000';
                       if(id<10)
                           {
                               prefix='000';
                           }else
                           {
                               if(id<100){
                                  prefix='00'; 
                               }else{
                                  prefix='0'; 
                               }
                               
                           }
                           
                       
                       $('#schoolID').html('( '+<?php echo $sy ?>+data.deptCode+levelCode+prefix+id+' )');
                       $('#inputIdNum').val(<?php echo $sy ?>+data.deptCode+levelCode+prefix+id);
                   }
                 });

            return false;
   
      }

      function searchGrade(){
          var value = document.getElementById("inputCourse").value;
          
            getInfo(value)
      }
      
      function setSection(){
          
            
            var data = new Array();
            
            data[0] = document.getElementById('inputNewSection').value;
            data[1] = document.getElementById('inputGrade').value;
            
            
            saveAdmission(data);
      }
      
      $(document).ready(function() {
          $("#inputreligion").select2(); 
          //$("#inputGrade").select2();  
          //$("#inputSection").select2(); 
          $("#inputMunCity").select2({maximumSelectionSize: 1 }); 
          $("#m_officeMunCity").select2({maximumSelectionSize: 1 }); 
          $("#f_officeMunCity").select2({maximumSelectionSize: 1 }); 
          $("#inputFPhy").select2(); 
          $("#inputFeduc").select2(); 
          $("#inputGeduc").select2(); 
          $("#inputMeduc").select2(); 
          $("#inputmother_tongue").select2(); 
          $("#inputethnic_group").select2();  
          $('#inputBdate').datepicker({orientation: 'auto'});
          $('#inputEdate').datepicker({orientation: 'auto'});
          
          $("#saveAdmission").click(function() {
               if($("#inputLastName").val() === "" || $("#inputFirstName").val() === "" || $("#inputMiddleName").val() === ""){
                //document.getElementById('loading').style.display='none';
                alert("Please Fill-up the form!");
            }else{
                $("#admissionForm").submit();
            }
          });
      
        });
        
        $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        })
        

</script>
