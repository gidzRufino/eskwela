<div class="row">
    <div class="col-lg-12 clearfix">
        <h3 style="margin:5px;" class="page-header text-center">
            <small class="pull-left" style="color:#BB0000; margin-top:5px;" id="name_header"></small>    
            Student Information
            <span class="pull-right">
                <i id="profMin"  title="Minimize" data-toggle="tooltip" data-placement="left" class="fa fa-minus pull-right pointer tip-top" onclick="maxMin('prof', 'min')"></i>
                <i id="profMax" title="Maximize" data-toggle="tooltip" data-placement="left" class="fa fa-plus pull-right pointer hide tip-top" onclick="maxMin('prof', 'max')"></i>
            </span>
        </h3>
    </div>
    <!-- /.col-lg-12 -->
</div>

<div class="row">
    <div class="well col-lg-12" id="profBody">
        <div class="col-lg-2">
            <?php if ($this->session->userdata('position_id') != 4): ?>
                                                                                                                                                                    <!--<a href="<?php echo base_url() . 'main/crop/' . $this->uri->segment(3) ?>">Crop Image</a>-->
                <?php
            endif;
            $user_id = $students->u_id;
            /*
              if($students->u_id==""):
              $user_id = $students->us_id;
              else:
              $user_id = $students->u_id;
              endif; */
            ?>
            <div id="imgCrop" data-id="photo">
                <img class="img-circle img-responsive" style="width:150px; border:5px solid #fff" src="<?php
                if ($students->avatar != ""):echo base_url() . 'uploads/' . $students->avatar;
                else:echo base_url() . 'uploads/noImage.png';
                endif;
                ?>" />
            </div>
        </div>
        <input type="hidden" id="stdUserID" value="<?php echo $user_id ?>" />
        <div class="col-lg-6">
            <h2 style="margin:3px 0;">
                <span id="name" style="color:#BB0000;"><?php echo strtoupper($students->firstname . " " . $students->lastname) ?></span>
                <small>
                    <?php if ($this->session->position_id != 39): ?>
                        <i style="font-size:15px;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"
                           rel="clickover" 
                           data-content=" 
                           <?php
                           $data['pos'] = 's';
                           $data['st_user_id'] = $user_id;
                           $data['user_id'] = $user_id;
                           $data['firstname'] = $students->firstname;
                           $data['middlename'] = $students->middlename;
                           $data['lastname'] = $students->lastname;
                           $data['name_id'] = 'name';
                           $this->load->view('basicInfo', $data)
                           ?>
                           " 
                           ></i>
                       <?php endif; ?>
                </small>
            </h2>
            <?php $strand = Modules::run('subjectmanagement/getStrandCode', $students->strnd_id); ?>
            <h3 style="color:black; margin:3px 0;"><?php echo $students->level; ?> - <span id="a_section"><?php echo $students->section; ?></span><span id="a_strand"><?php echo ($strand != '' ? ' - ' . $strand->short_code : '') ?></span>
                <?php if ($this->session->userdata('is_admin')): ?>
                    <small>
                        <?php if ($this->session->position_id != 39): ?>
                            <i style="font-size:15px;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?> <?php echo $editable ?>"
                               rel="clickover" 
                               data-content=" 
                               <?php
                               $level['specs'] = Modules::run('registrar/getSpecialization');
                               $level['ro_year'] = $ro_year;
                               $level['gradeLevel'] = Modules::run('registrar/getGradeLevel');
                               $level['st_id'] = $students->uid;
                               $this->load->view('editProfileLevel', $level)
                               ?>
                               "   
                               ></i>
                        </small>

                        <?php
                    endif;
                endif;
                ?>

            </h3>

            <input type="hidden" id="admission_user_id" value="<?php echo $students->u_id ?>" />
            <h3 style="color:black; margin:3px 0;">
                <small>
                    <a title="double click to edit" id="a_user_id"  style="color:#BB0000;">
                        <?php echo ($students->lrn == "" ? $students->uid : $students->lrn) ?>
                    </a>
                    <input style="display: none; width:300px" type="text" id="input_user_id" value="<?php echo $students->uid ?>" readonly="" 
                           onkeypress="if (event.keyCode == 13) {
                                       editId_number('<?php echo $students->uid ?>', 'user_id'), $('#a_user_id').show(), $('#input_user_id').hide()}"/>
                           <?php if ($this->session->position_id != 39): ?>
                        <i id="editUserIDBtn" onclick="$('#a_user_id').hide(), $('#input_user_id').show(), $('#input_lrn').show(), $('#input_user_id').focus(), $('#saveUserIDBtn').show(), $('#closeUserIDBtn').show(), $('#saveLrnBtn').show(), $('#closeLrnBtn').show(), $(this).hide()" style="font-size:15px;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                    <?php endif; ?>
                    <input style="display: none; width:300px" type="text" id="input_lrn" value="<?php echo $students->lrn ?>" placeholder="LRN" 
                           /> 

                    <i id="saveLrnBtn" onclick="updateProfile('<?php echo base64_encode('st_id') ?>', '<?php echo base64_encode('esk_profile_students') ?>', '<?php echo $students->uid ?>', 'lrn', $('#input_lrn').val(), 'lrn'), $('#a_user_id').show(), $('#input_user_id').hide(), $('#input_lrn').hide(), $('#editUserIDBtn').show(), $('#saveLrnBtn').hide(), $(this).hide(), $('#saveUserIDBtn').hide(), $('#closeUserIDBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                    <i id="closeLrnBtn" onclick="$('#a_user_id').show(), $('#input_user_id').hide(), $('#input_lrn').hide(), $('#editUserIDBtn').show(), $('#saveLrnBtn').hide(), $(this).hide(), $('#saveUserIDBtn').hide(), $('#closeUserIDBtn').hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                </small>

            </h3>
            <h3 style="color:#BB0000; margin:3px 0;">
                <?php
                $remarks = Modules::run('main/getAdmissionRemarks', $students->uid, NULL, $students->sy);
                if ($remarks->num_rows() > 0) {
                    echo $remarks->row()->Indicator . ' [ ' . $remarks->row()->remark_date . ' ] ';
                }
                ?>
            </h3>
            <?php
            if ($this->session->userdata('position_id') == 1 || $this->session->userdata('position_id') == 49):
                ?>
                <select>
                    <option value="1" <?php echo ($students->is_esc == 1 ? 'selected' : ''); ?> onclick="updateIfRegular(this.value, <?php echo $students->u_id ?>)">ESC Grantee</option>
                    <option value="0" <?php echo ($students->is_esc == 0 ? 'selected' : ''); ?> onclick="updateIfRegular(this.value, <?php echo $students->u_id ?>)">Non ESC</option>
                </select>
                <?php
            endif;
            ?>
            <h3 style="color:black; margin:3px 0;">
            </h3>
        </div>

        <div class="col-lg-4">
            <button class="btn-warning btn-xs pull-right" st-id="<?php echo base64_encode($students->st_id); ?>" onclick="readyPrint(this)">Print Admission Form</button>
            <script>
                function readyPrint(btn)
                {
                    var st_id = $(btn).attr('st-id'),
                            url = "<?php echo site_url('registrar/printAdmission'); ?>/" + st_id;
                    window.open(url);
                }
            </script>
        </div>
        <?php
        if ($this->session->userdata('position_id') == 1 || $this->session->userdata('position_id') == 49):
            if (!$userPass):
                ?>
                <div class="col-lg-4">
                    <br>
                    <button class="btn-primary btn-xs pull-right" onclick="genPass('<?php echo base64_encode($students->st_id); ?>')">Generate Password</button>                        
                </div>
            <?php else: ?>
                <div class="col-lg-2" style="float: right; color: gray; padding-left: 55px">
                    <br>
                    <label>Student's Password:</label>
                <span style="color: gray" id="asterisk"><?php echo '********' ?></span>
                    <span style="color: gray" hidden="" id="secretPass"><?php echo $uPass->secret_key ?></span>
                    &nbsp;<i class="fa fa-eye fa-sm pointer" id="viewPass" onclick="$(this).hide(), $('#asterisk').hide(), $('#secretPass').show(), $('#hidePass').show()"></i>
                    &nbsp;<i class="fa fa-close fa-sm pointer" style="color: red; display: none" id="hidePass" onclick="$(this).hide(), $('#asterisk').show(), $('#secretPass').hide(), $('#viewPass').show()"></i>
                </div>
            <?php
            endif;
        endif;
        ?>
    </div>
</div>
<div class="row">
    <ul class="nav nav-tabs" role="tablist" id="profile_tab">
        <li class="active"><a href="#PersonalInfo">Personal Information</a></li>
        <li><a href="#attendanceInformation">Attendance Information</a></li>
        <li><a href="#academicInformation">Academic Information</a></li>
        <li><a href="#medicalInformation">Medical Information</a></li>
        <?php if ($this->session->position_id != 39): ?>
            <li><a href="#enrollmentRequirements">Requirements for Enrollment</a></li>
        <?php endif; ?>
        <li class="pull-right"><a onclick="imgSignUpload(this.id)" href="imgSignUpload(this.id)" id="sign">Upload Signature</a></li>

    </ul>
    <div class="tab-content col-lg-12">
        <div style="padding-top: 15px;" class="tab-pane active" id="PersonalInfo">
            <div class="col-lg-6 pull-right">
                <img class="img-responsive pull-right" style="width:150px; border:5px solid #fff" src="<?php
                if ($students->avatar != ""):
                    echo base_url() . 'uploads/sign/' . $user_id . '.png';
                else:
                    echo base_url() . 'uploads/noImage.png';
                endif;
                ?>" />
            </div>
            <dl class="dl-horizontal">
                <dt>
                    Address:
                </dt>
                <dd >
                    <span id="address_span"><?php echo strtoupper($students->street . ', ' . $students->barangay . ' ' . $students->mun_city . ', ' . $students->province . ', ' . $students->zip_code); ?></span>

                    <?php if ($this->session->position_id != 39): ?>
                        <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?> <?php echo $editable ?>"
                           rel="clickover"  id="addClick"
                           data-content="  <?php
                           $data['cities'] = Modules::run('main/getCities');
                           $data['address_id'] = $students->address_id;
                           $data['st_id'] = $students->st_id;
                           $data['street'] = $students->street;
                           $data['barangay'] = $students->barangay;
                           $data['city'] = $students->city_id;
                           $data['province'] = $students->province;
                           $data['pid'] = $students->province_id;
                           $data['zip_code'] = $students->zip_code;
                           $data['user_id'] = $user_id;
                           $this->load->view('addressInfo', $data)
                           ?>
                           "></i>
                       <?php endif; ?>
                </dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>
                    Contact No:
                </dt>
                <dd>
                    <span title="double click to edit" id="a_mobile" ondblclick="$('#a_mobile').hide(), $('#mobile').show(), $('#mobile').focus()" ><?php
                        if ($students->cd_mobile != ""):echo $students->cd_mobile;
                        else: echo "[empty]";
                        endif;
                        ?></span>
                    <input style="display: none; width:300px" type="text" id="mobile" value="<?php echo $students->cd_mobile; ?>" 

                           title="press enter to save your edit"
                           />
                           <?php if ($this->session->position_id != 39): ?>
                        <i id="editContactBtn" onclick="$('#a_mobile').hide(), $('#mobile').show(), $('#mobile').focus(), $(this).hide(), $('#saveContactBtn').show(), $('#closeContactBtn').show()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                    <?php endif; ?>
                    <i id="saveContactBtn" onclick="saveMobile('<?php echo $user_id ?>', $('#mobile').val(), 'cd_mobile'), $('#a_mobile').show(), $('#mobile').hide(), $('#editContactBtn').show(), $('#saveContactBtn').hide(), $('#closeContactBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                    <i id="closeContactBtn" onclick="$('#a_mobile').show(), $('#mobile').hide(), $('#editContactBtn').show(), $('#saveContactBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                </dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>
                    Gender: 
                </dt>
                <dd style="color:black; ">
                    <span id="st_sex">
                        <?php echo $students->sex; ?> 
                    </span>

                    <?php if ($this->session->position_id != 39): ?>
                        <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"
                           rel="clickover"  
                           <?php if ($this->session->userdata('is_admin')): ?>
                               data-content=" 
                               <div class='col-lg-12 form-group' style='width:230px;'>
                               <label class='control-label'>Gender</label>
                               <div class='controls' id='AddedSection'>
                               <select name='inputGender' id='inputGender' class='pull-left' required>
                               <option>Select Gender</option>  
                               <option value='Male'>Male</option>  
                               <option value='Female'>Female</option>  
                               </select>
                               </div>
                               </div>
                               <div class'col-lg-12'>
                               <button data-dismiss='clickover' class='btn btn-xs btn-danger'>Cancel</button>&nbsp;&nbsp;
                               <a href='#' data-dismiss='clickover' onclick='saveGender()' style='margin-right:10px;' class='btn btn-xs btn-success pull-left'>Save</a>
                               </div> 
                               "
                           <?php endif; ?> ></i>
                       <?php endif; ?>
                </dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>
                    Birthdate: 
                </dt>
                <dd>
                    <span id="a_bdate" >
                        <?php echo $students->temp_bdate; ?>
                    </span> 
                    <input style="display: none;" name="inputBdate" type="text" data-date-format="yyyy-mm-dd" id="bdate" value="<?php echo $students->temp_bdate; ?>" placeholder="Date of Birth" 
                           onblur="" onkeypress="if (event.keyCode == 13) {
                                       editBdate(this.value, '<?php echo $students->user_id ?>')
                                   }"   

                           required>
                           <?php if ($this->session->position_id != 39): ?>
                        <i id="editBdateBtn" onclick="$('#bdate').datepicker(), $('#a_bdate').hide(), $('#bdate').show(), $('#bdate').focus(), $('#closeBdateBtn').show(), $('#saveBdateBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                    <?php endif; ?>
                    <i id="saveBdateBtn" onclick="editBdate($('#bdate').val(), '<?php echo $user_id ?>'), $('#a_bdate').show(), $('#bdate').hide(), $('#editBdateBtn').show(), $('#saveBdateBtn').hide(), $('#closeBdateBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                    <i id="closeBdateBtn" onclick="$('#a_bdate').show(), $('#bdate').hide(), $('#editBdateBtn').show(), $('#saveBdateBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                </dd>
            </dl>

            <dl class="dl-horizontal">
                <dt>
                    Religion: 
                </dt>
                <dd>
                    <span id="a_religion" >
                        <?php echo $students->religion; ?>
                    </span>
                    <?php if ($this->session->position_id != 39): ?>
                        <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>" 
                           rel="clickover"  
                           data-content=" 
                           <div class='col-lg-12 form-group' style='width:230px;'>
                           <label class='control-label'>Religion</label>
                           <div class='controls' id='AddedSection'>
                           <select name='inputReligion' id='inputReligion' class='pull-left' required>
                           <option>Select Religion</option>  
                           <?php
                           foreach ($religion as $rel):
                               ?>
                               <option value='<?php echo $rel->rel_id ?>'><?php echo $rel->religion ?></option>
                               <?php
                           endforeach;
                           ?>
                           </select>
                           </div>
                           </div>
                           <div class'col-lg-12'>
                           <button data-dismiss='clickover' class='btn btn-xs btn-danger'>Cancel</button>&nbsp;&nbsp;
                           <a href='#' data-dismiss='clickover' onclick='saveReligion()' style='margin-right:10px;' class='btn btn-xs btn-success pull-left'>Save</a>
                           </div> 
                           " ></i>
                        <a class="help-inline" 
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
                           class="btn" data-toggle="modal" href="#">[ Add Religion ( if not exist ) ]</a> 
                       <?php endif; ?>                
                </dd>
            </dl>

            <dl class="dl-horizontal">
                <dt>
                    Mother Tongue: 
                </dt>
                <dd>
                    <span id="a_motherTongue" >
                        <?php echo $students->mother_tongue; ?>
                    </span> 
                    <?php if ($this->session->position_id != 39): ?>
                        <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"
                           rel="clickover"  
                           data-content=" 
                           <div class='col-lg-12 form-group' style='width:230px;'>
                           <label class='control-label'>Mother Tongue</label>
                           <div class='controls' id='AddedSection'>
                           <select name='inputMotherTongue' id='inputMotherTongue' class='pull-left' required>
                           <option>Select Mother Tongue</option>  
                           <?php
                           foreach ($motherTongue as $mt):
                               ?>
                               <option value='<?php echo $mt->mt_id ?>'><?php echo $mt->mother_tongue ?></option>
                               <?php
                           endforeach;
                           ?>
                           </select>
                           </div>
                           </div>
                           <div class'col-lg-12'>
                           <button data-dismiss='clickover' class='btn btn-xs btn-danger'>Cancel</button>&nbsp;&nbsp;
                           <a href='#' data-dismiss='clickover' onclick='saveMotherTongue()' style='margin-right:10px;' class='btn btn-xs btn-success pull-left'>Save</a>
                           </div> 
                           " ></i><a class="help-inline" 
                           rel="clickover" 
                           data-content=" 
                           <div style='width:100%;'>
                           <h6>Add Mother Tongue</h6>
                           <input type='text' id='addmother_tongue' />
                           <div style='margin:5px 0;'>
                           <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                           <a href='#' id='mother_tongue' data-dismiss='clickover' table='mother_tongue' column='mother_tongue' pk='mt_id' retrieve='getMotherTongue' onclick='saveNewValue(this.id)' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                           </div>
                           "   
                           class="btn" data-toggle="modal" href="#">[ Add Mother Tongue ( if not exist ) ]</a>
                       <?php endif; ?>
                </dd>
            </dl>

            <dl class="dl-horizontal">
                <dt>
                    Ethnic Group: 
                </dt>
                <dd>
                    <span id="a_ethnicGroup" >
                        <?php echo $students->ethnic_group; ?>
                    </span>
                    <?php if ($this->session->position_id != 39): ?>
                        <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"
                           rel="clickover"  
                           data-content=" 
                           <div class='col-lg-12 form-group' style='width:230px;'>
                           <label class='control-label'>Ethnic Group</label>
                           <div class='controls' id='AddedSection'>
                           <select name='inputEthnicGroup' id='inputEthnicGroup' class='pull-left' required>
                           <option>Select Ethnic Group</option>  
                           <?php
                           foreach ($ethnicGroup as $eg):
                               ?>
                               <option value='<?php echo $eg->eg_id ?>'><?php echo $eg->ethnic_group ?></option>
                               <?php
                           endforeach;
                           ?>
                           </select>
                           </div>
                           </div>
                           <div class'col-lg-12'>
                           <button data-dismiss='clickover' class='btn btn-xs btn-danger'>Cancel</button>&nbsp;&nbsp;
                           <a href='#' data-dismiss='clickover' onclick='saveEthnicGroup()' style='margin-right:10px;' class='btn btn-xs btn-success pull-left'>Save</a>
                           </div> 
                           " ></i> <a class="help-inline" 
                           rel="clickover" 
                           data-content=" 
                           <div style='width:100%;'>
                           <h6>Add Ethnic Group</h6>
                           <input type='text' id='addethnic_group' />
                           <div style='margin:5px 0;'>
                           <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                           <a href='#' id='ethnic_group' data-dismiss='clickover' table='ethnic_group' column='ethnic_group' pk='eg_id' retrieve='getEthnicGroup' onclick='saveNewValue(this.id)' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                           </div>
                           "   
                           class="btn" data-toggle="modal" href="#">[ Add Ethnic Group ( if not exist ) ]</a>
                       <?php endif; ?>
                </dd>
            </dl>

            <hr style="margin:3px 0;" />
            <h5 >Family Information</h5>
            <hr style="margin:3px 0 15px;" />
            <div class="col-lg-6" style="padding-left: 0;">
                <dl class="dl-horizontal">
                    <dt>
                        Father's Name :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_fname" ><?php
                            if ($students->f_lastname != ""):echo strtoupper($students->f_firstname . ' ' . $students->f_lastname);
                            else: echo "[empty]";
                            endif;
                            ?> </span>
                        <small>
                            <?php if ($this->session->position_id != 39): ?>
                                <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?> "
                                   rel="clickover" 
                                   data-content=" 
                                   <?php
                                   $data['pos'] = 'f';
                                   $data['school_year'] = $students->school_year;
                                   $data['parent_id'] = $students->p_id;
                                   $data['st_user_id'] = $user_id;
                                   $data['firstname'] = $students->f_firstname;
                                   $data['middlename'] = $students->f_middlename;
                                   $data['lastname'] = $students->f_lastname;
                                   $this->load->view('parentBasicInfo', $data)
                                   ?>
                                   " 
                                   ></i>
                               <?php endif; ?>
                        </small>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Education :
                    </dt>
                    <dd>
                        <span id="F_educAttainValue" ><?php echo strtoupper($f->attainment); ?></span>
                        <small>
                            <?php if ($this->session->position_id != 39): ?>
                                <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?> <?php echo $editable ?>"
                                   rel="clickover" 
                                   data-content=" 
                                   <select id='F_educAttain' name='inputFeduc' style='width:100%;'>
                                   <option>Select Educational Attainment</option> 
                                   <?php foreach ($educ_attain as $EA) { ?>
                                       <option value='<?php echo $EA->ea_id ?>'><?php echo $EA->attainment ?></option> 
                                   <?php } ?>

                                   </select>
                                   <div class'col-lg-12'>
                                   <button data-dismiss='clickover' class='btn btn-xs btn-danger'>Cancel</button>&nbsp;&nbsp;
                                   <a href='#' data-dismiss='clickover' id='F' u-id='<?php echo ($students->u_id); ?>' onclick='updateEducAttain(this.id, this)' style='margin-right:10px;' class='btn btn-xs btn-success pull-left'>Save</a>
                                   </div>
                                   " 
                                   ></i>
                               <?php endif; ?>
                        </small>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Occupation :
                    </dt>
                    <dd>
                        <?php $f_occ = Modules::run('registrar/getOccupation', $students->f_occ); ?>
                        <span id="a_f_occupation"><?php echo strtoupper($f_occ->occupation); ?></span>
                        <input style="display: none; width:300px" type="text" id="f_occupation" value="<?php echo $f_occ->occupation; ?>" 
                               onkeypress=""
                               title="press enter to save your edit"
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editFOccBtn" onclick="$('#a_f_occupation').hide(), $('#f_occupation').show(), $('#f_occupation').focus(), $('#closeFOccBtn').show(), $('#saveFOccBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveFOccBtn" onclick="updateOccupation($('#f_occupation').val(), '<?php echo $students->p_id; ?>', 'f'), $('#a_f_occupation').show(), $('#m_occupation').hide(), $('#editFOccBtn').show(), $('#saveFOccBtn').hide(), $('#closeFOccBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeFOccBtn" onclick=" $('#a_f_occupation').show(), $('#f_occupation').hide(), $('#editFOccBtn').show(), $('#saveFOccBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Office :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_f_office_name" ><?php
                            if ($students->f_office_name != ""):echo strtoupper($students->f_office_name);
                            else: echo "[empty]";
                            endif;
                            ?></span>
                        <input style="display: none; width:300px" type="text" id="f_office_name" value="<?php echo $students->f_office_name; ?>" 
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editF_office_nameBtn" onclick="$('#a_f_office_name').hide(), $('#f_office_name').show(), $('#f_office_name').focus(), $('#closeM_office_nameBtn').show(), $('#saveF_office_nameBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveF_office_nameBtn" onclick="updateProfile('<?php echo base64_encode('p_id') ?>', '<?php echo base64_encode('esk_profile_parent') ?>',<?php echo $students->p_id ?>, 'f_office_name', $('#f_office_name').val(), 'f_office_name'), $('#a_f_office_name').show(), $('#f_office_name').hide(), $('#editF_office_nameBtn').show(), $('#saveF_office_nameBtn').hide(), $('#closeF_office_nameBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeF_office_nameBtn" onclick=" $('#a_f_office_name').show(), $('#f_office_name').hide(), $('#editF_office_nameBtn').show(), $('#saveF_office_nameBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Contact :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_fmobile" ><?php
                            if ($students->f_mobile != ""):echo $students->f_mobile;
                            else: echo "[empty]";
                            endif;
                            ?></span>
                        <input style="display: none; width:300px" type="text" id="fmobile" value="<?php echo $students->f_mobile; ?>" 

                               title="press enter to save your edit"
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editFMobileBtn" onclick="$('#a_fmobile').hide(), $('#fmobile').show(), $('#fmobile').focus(), $('#closeFMobileBtn').show(), $('#saveFMobileBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveFMobileBtn" onclick="updateProfile('<?php echo base64_encode('p_id') ?>', '<?php echo base64_encode('esk_profile_parent') ?>',<?php echo $students->p_id ?>, 'f_mobile', $('#fmobile').val(), 'fmobile');
                                        $('#a_fmobile').show(), $('#fmobile').hide(), $('#editFMobileBtn').show(), $('#saveFMobileBtn').hide(), $('#closeFMobileBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeFMobileBtn" onclick=" $('#a_fmobile').show(), $('#fmobile').hide(), $('#editFMobileBtn').show(), $('#saveFMobileBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>

            </div>

            <div class="col-lg-6">
                <dl class="dl-horizontal">
                    <dt>
                        Mother's Name :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_mname" ondblclick="$('#a_mname').hide(), $('#mname').show(), $('#mname').focus()" ><?php
                            if ($students->m_lastname != ""):echo strtoupper($students->m_firstname . ' ' . $students->m_lastname);
                            else: echo "[empty]";
                            endif;
                            ?> </span>
                        <small>
                            <?php if ($this->session->position_id != 39): ?>
                                <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"
                                   rel="clickover" 
                                   data-content=" 
                                   <?php
                                   $data['pos'] = 'm';
                                   $data['school_year'] = $students->school_year;
                                   $data['parent_id'] = $students->p_id;
                                   $data['st_user_id'] = $user_id;
                                   $data['firstname'] = $students->m_firstname;
                                   $data['middlename'] = $students->m_middlename;
                                   $data['lastname'] = $students->m_lastname;
                                   $this->load->view('parentBasicInfo', $data)
                                   ?>
                                   " 
                                   ></i>
                               <?php endif; ?>
                        </small>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Education :
                    </dt>
                    <dd>
                        <span id="M_educAttainValue" ><?php echo strtoupper($m->attainment); ?></span>
                        <small>
                            <?php if ($this->session->position_id != 39): ?>
                                <i style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"
                                   rel="clickover" 
                                   data-content=" 
                                   <select id='M_educAttain' name='inputFeduc' style='width:100%;'>
                                   <option>Select Educational Attainment</option> 
                                   <?php foreach ($educ_attain as $EA) { ?>
                                       <option value='<?php echo $EA->ea_id ?>'><?php echo $EA->attainment ?></option> 
                                   <?php } ?>

                                   </select>
                                   <div class'col-lg-12'>
                                   <button data-dismiss='clickover' class='btn btn-xs btn-danger'>Cancel</button>&nbsp;&nbsp;
                                   <a href='#' data-dismiss='clickover' id='M' u-id='<?php echo ($students->u_id); ?>' onclick='updateEducAttain(this.id, this)' style='margin-right:10px;' class='btn btn-xs btn-success pull-left'>Save</a>
                                   </div>
                                   " 
                                   ></i>
                               <?php endif; ?>
                        </small>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Occupation :
                    </dt>
                    <dd>
                        <?php $m_occ = Modules::run('registrar/getOccupation', $students->m_occ); ?>
                        <span id="a_m_occupation"><?php echo strtoupper($m_occ->occupation); ?></span>
                        <input style="display: none; width:250px" type="text" id="m_occupation" value="<?php echo $m_occ->occupation; ?>" 
                               onkeypress="if (event.keyCode == 13) {
                                           updateOccupation(this.value, '<?php echo $user_id; ?>', 'm')
                                       }"
                               title="press enter to save your edit"
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editMOccBtn" onclick="$('#a_m_occupation').hide(), $('#m_occupation').show(), $('#m_occupation').focus(), $('#closeMOccBtn').show(), $('#saveMOccBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveMOccBtn" onclick="updateOccupation($('#m_occupation').val(), '<?php echo $students->p_id; ?>', 'm'), $('#a_m_occupation').show(), $('#m_occupation').hide(), $('#editMOccBtn').show(), $('#saveMOccBtn').hide(), $('#closeMOccBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeMOccBtn" onclick="$('#a_m_occupation').show(), $('#m_occupation').hide(), $('#a_m_occupation').show(), $('#m_occupation').hide(), $('#editMOccBtn').show(), $('#saveMOccBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Office :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_m_office_name" ><?php
                            if ($students->m_office_name != ""):echo strtoupper($students->m_office_name);
                            else: echo "[empty]";
                            endif;
                            ?></span>
                        <input style="display: none; width:300px" type="text" id="m_office_name" value="<?php echo $students->m_office_name; ?>" 
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editM_office_nameBtn" onclick="$('#a_m_office_name').hide(), $('#m_office_name').show(), $('#m_office_name').focus(), $('#closeM_office_nameBtn').show(), $('#saveM_office_nameBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveM_office_nameBtn" onclick="updateProfile('<?php echo base64_encode('p_id') ?>', '<?php echo base64_encode('esk_profile_parent') ?>',<?php echo $students->p_id ?>, 'm_office_name', $('#m_office_name').val(), 'm_office_name'), $('#a_m_office_name').show(), $('#m_office_name').hide(), $('#editM_office_nameBtn').show(), $('#saveM_office_nameBtn').hide(), $('#closeM_office_nameBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeM_office_nameBtn" onclick=" $('#a_m_office_name').show(), $('#m_office_name').hide(), $('#editM_office_nameBtn').show(), $('#saveM_office_nameBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Contact :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_mmobile" ><?php
                            if ($students->m_mobile != ""):echo $students->m_mobile;
                            else: echo "[empty]";
                            endif;
                            ?></span>
                        <input style="display: none; width:250px" type="text" id="mmobile" value="<?php echo $students->m_mobile; ?>" 

                               title="press enter to save your edit"
                               />

                        <?php if ($this->session->position_id != 39): ?>
                            <i id="editMMobileBtn" onclick="$('#a_mmobile').hide(), $('#mmobile').show(), $('#mmobile').focus(), $('#closeMMobileBtn').show(), $('#saveMMobileBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveMMobileBtn" onclick="updateProfile('<?php echo base64_encode('p_id') ?>', '<?php echo base64_encode('esk_profile_parent') ?>', '<?php echo ($students->p_id) ?>', 'm_mobile', $('#mmobile').val(), 'mmobile'), $('#a_mmobile').show(), $('#mmobile').hide(), $('#editMMobileBtn').show(), $('#saveMMobileBtn').hide(), $('#closeMMobileBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeMMobileBtn" onclick=" $('#a_mmobile').show(), $('#mmobile').hide(), $('#editMMobileBtn').show(), $('#saveMMobileBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
            <hr style="margin:3px 0;" />
            <h5 >In Case of Emergency:</h5>
            <hr style="margin:3px 0 15px;" /> 
            <div class="col-lg-6" style="padding-left: 0;">
                <dl class="dl-horizontal">
                    <dt>
                        Contact Name :
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_ice_name" ><?php
                            if ($students->ice_name != ""):echo strtoupper($students->ice_name);
                            else: echo "[empty]";
                            endif;
                            ?></span>
                        <input style="display: none; width:300px" type="text" id="ice_name" value="<?php echo $students->ice_name; ?>" 
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editIce_nameBtn" onclick="$('#a_ice_name').hide(), $('#ice_name').show(), $('#ice_name').focus(), $('#closeIce_nameBtn').show(), $('#saveIce_nameBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveIce_nameBtn" onclick="updateProfile('<?php echo base64_encode('u_id') ?>', '<?php echo base64_encode('esk_profile_parent') ?>',<?php echo $students->u_id ?>, 'ice_name', $('#ice_name').val(), 'ice_name'), $('#a_ice_name').show(), $('#ice_name').hide(), $('#editIce_nameBtn').show(), $('#saveIce_nameBtn').hide(), $('#closeIce_nameBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeIce_nameBtn" onclick=" $('#a_ice_name').show(), $('#ice_name').hide(), $('#editIce_nameBtn').show(), $('#saveIce_nameBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
            <div class="col-lg-6">
                <dl class="dl-horizontal">
                    <dt>
                        Contact Number : 
                    </dt>
                    <dd>
                        <span title="double click to edit" id="a_ice_contact" ><?php
                            if ($students->ice_contact != ""):echo $students->ice_contact;
                            else: echo "[empty]";
                            endif;
                            ?></span>
                        <input style="display: none; width:300px" type="text" id="ice_contact" value="<?php echo $students->ice_contact; ?>" 
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editIce_contactBtn" onclick="$('#a_ice_contact').hide(), $('#ice_contact').show(), $('#ice_contact').focus(), $('#closeIce_contactBtn').show(), $('#saveIce_contactBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>
                            <i id="saveIce_contactBtn" onclick="updateProfile('<?php echo base64_encode('u_id') ?>', '<?php echo base64_encode('esk_profile_parent') ?>',<?php echo $students->u_id ?>, 'ice_contact', $('#ice_contact').val(), 'ice_contact'), $('#a_ice_contact').show(), $('#ice_contact').hide(), $('#editIce_contactBtn').show(), $('#saveIce_contactBtn').hide(), $('#closeIce_contactBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeIce_contactBtn" onclick=" $('#a_ice_contact').show(), $('#ice_contact').hide(), $('#editIce_contactBtn').show(), $('#saveIce_contactBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>


        </div>  
        <div class="tab-pane" id="attendanceInformation">
            <div class="col-lg-6 pull-right">
                <?php
                echo Modules::run('attendance/current', $option, base64_decode($this->uri->segment(3)));
                ?> 
            </div>

        </div>
        <div style="padding-top: 15px;" class="tab-pane" id="academicInformation">
            <dl class="dl-horizontal">
                <dt>
                    Date Enrolled:
                </dt>
                <dd>
                    <span  id="a_enDate"  >
                        <?php echo $date_admitted; ?>
                    </span> 
                    <input style="display: none;" name="enDate" type="text" data-date-format="mm-dd-yyyy" id="enDate" value="<?php echo $date_admitted; ?>" placeholder="Date of Birth" 

                           onkeypress="if (event.keyCode == 13) {
                                       editEnBdate(this.value, '<?php echo $students->u_id ?>')
                                   }"   

                           required>
                           <?php if ($this->session->position_id != 39): ?>
                        <i id="editEndateBtn" onclick="$('#enDate').datepicker(), $('#a_enDate').hide(), $('#enDate').show(), $('#enDate').focus(), $('#closeEndateBtn').show(), $('#saveEndateBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                        <i id="saveEndateBtn" onclick="editEnBdate($('#enDate').val(), '<?php echo $students->u_id ?>'), $('#a_enDate').show(), $('#enDate').hide(), $('#editBdateBtn').show(), $('#saveBdateBtn').hide(), $('#closeEndateBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                        <i id="closeEndateBtn" onclick="$('#a_enDate').show(), $('#enDate').hide(), $('#editEndateBtn').show(), $('#saveEndateBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                    <?php endif; ?>
                </dd>
            </dl>
            <hr /> 

            <dl class="dl-horizontal">
                <?php $olSubject = Modules::run('registrar/getOvrLoadSub', $students->uid) ?>
                <?php if (count($olSubject) == 0): ?>
                    <dt>
                        Overload Subject:
                    </dt>
                    <dd>

                        <span  id="a_ovrSubj"  >
                            [ NONE ]&nbsp;&nbsp;
                        </span>
                        <span class="badge badge-pill badge-light nowrap clickover pointer" onclick="$('#ovrSubj').modal('show')" style="cursor: pointer; background-color: green;"><i class="fa fa-plus-circle" title="Add Subject"></i></span>
                        <!--<i id="editOvrSubjBtn" onclick="$('#a_ovrSubj').hide(), $('#closeOvrSubjBtn').show(), $('#saveOvrSubjBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>-->   
                        <i id="saveOvrSubjBtn" onclick="$('#a_ovrSubj').show() $('#editOvrSubjBtn').show(), $('#saveOvrSubjBtn').hide(), $('#closeOvrSubjBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                        <i id="closeOvrSubjBtn" onclick="$('#a_ovrSubj').show(), $('#editOvrSubjBtn').show(), $('#saveOvrSubjBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                    </dd>
                    <?php
                else:
                    $data['olSubject'] = $olSubject;
                    echo $this->load->view('listOfSubjOvrLoad', $data);
                endif;
                ?>
            </dl>
            <hr />
            <?php echo Modules::run('widgets/getWidget', 'gradingsystem_widget', 'acadInfo', $students); ?>
        </div>
        <div style="padding-top: 15px;" class="tab-pane" id="medicalInformation">
            <div class="col-lg-6">
                <dl class="dl-horizontal">
                    <dt>
                        Blood Type:
                    </dt>
                    <dd>
                        <span  id="a_blood_type"  >
                            <?php echo $students->blood_type ?>
                        </span> 
                        <input style="display: none; width:250px" type="text" id="blood_type" value="<?php echo $students->blood_type ?>" 
                               onkeypress="if (event.keyCode == 13) {
                                       }"
                               title="press enter to save your edit"
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editBtypeBtn" onclick="$('#a_blood_type').hide(), $('#blood_type').show(), $('#blood_type').focus(), $('#closeBtypeBtn').show(), $('#saveBtypeBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                            <i id="saveBtypeBtn" onclick="updateProfile('<?php echo base64_encode('user_id') ?>', '<?php echo base64_encode('esk_profile_medical') ?>',<?php echo $students->u_id ?>, 'blood_type', $('#blood_type').val(), 'blood_type'), $('#a_blood_type').show(), $('#blood_type').hide(), $('#editBtypeBtn').show(), $('#saveBtypeBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeBtypeBtn" onclick="$('#a_blood_type').show(), $('#blood_type').hide(), $('#editBtypeBtn').show(), $('#saveBtypeBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Height:
                    </dt>
                    <dd>
                        <span  id="a_height"  >
                            <?php echo $students->height ?>
                        </span> 
                        <input style="display: none; width:250px" type="text" id="height" value="<?php echo $students->height ?>" 
                               onkeypress="if (event.keyCode == 13) {
                                       }"
                               title="press enter to save your edit"
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editHeightBtn" onclick="$('#a_height').hide(), $('#height').show(), $('#blood_type').focus(), $('#closeHeightBtn').show(), $('#saveHeightBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                            <i id="saveHeightBtn" onclick="updateProfile('<?php echo base64_encode('user_id') ?>', '<?php echo base64_encode('esk_profile_medical') ?>',<?php echo $students->u_id ?>, 'height', $('#height').val(), 'height'), $('#a_height').show(), $('#height').hide(), $('#editHeightBtn').show(), $('#saveHeightBtn').hide(), $('#closeHeightBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeHeightBtn" onclick="$('#a_height').show(), $('#height').hide(), $('#editHeightBtn').show(), $('#saveHeightBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Weight:
                    </dt>
                    <dd>
                        <span  id="a_weight"  >
                            <?php echo $students->weight ?>
                        </span> 
                        <input style="display: none; width:250px" type="text" id="weight" value="<?php echo $students->weight ?>" 
                               onkeypress=""
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editWeightBtn" onclick="$('#a_weight').hide(), $('#weight').show(), $('#blood_type').focus(), $('#closeWeightBtn').show(), $('#saveWeightBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                            <i id="saveWeightBtn" onclick="updateProfile('<?php echo base64_encode('user_id') ?>', '<?php echo base64_encode('esk_profile_medical') ?>',<?php echo $students->u_id ?>, 'weight', $('#weight').val(), 'weight'), $('#a_weight').show(), $('#weight').hide(), $('#editWeightBtn').show(), $('#saveWeightBtn').hide(), $('#closeWeightBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeWeightBtn" onclick="$('#a_weight').show(), $('#weight').hide(), $('#editWeightBtn').show(), $('#saveWeightBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>
                <dl class="dl-horizontal">
                    <dt>
                        Other Medical Info:
                    </dt>
                    <dd>
                        <span  id="a_otherMedInfo"  >
                            <?php echo $students->other_important ?>
                        </span> 
                        <input style="display: none; width:250px" type="text" id="otherMedInfo" value="<?php echo $students->other_important ?>" 
                               onkeypress=""
                               />
                               <?php if ($this->session->position_id != 39): ?>
                            <i id="editOtherMedInfoBtn" onclick="$('#a_otherMedInfo').hide(), $('#otherMedInfo').show(), $('#otherMedInfo').focus(), $('#closeOtherMedInfoBtn').show(), $('#saveOtherMedInfoBtn').show(), $(this).hide()" style="font-size:15px; color:#777;" class="fa fa-pencil-square-o clickover pointer <?php echo $editable ?>"></i>   
                            <i id="saveOtherMedInfoBtn" onclick="updateProfile('<?php echo base64_encode('user_id') ?>', '<?php echo base64_encode('esk_profile_medical') ?>',<?php echo $students->u_id ?>, 'other_important', $('#otherMedInfo').val(), 'otherMedInfo'), $('#a_otherMedInfo').show(), $('#otherMedInfo').hide(), $('#editOtherMedInfoBtn').show(), $('#saveOtherMedInfoBtn').hide(), $('#closeOtherMedInfoBtn').hide()" style="font-size:15px; display: none; " class="fa fa-save clickover pointer"></i>
                            <i id="closeOtherMedInfoBtn" onclick="$('#a_otherMedInfo').show(), $('#otherMedInfo').hide(), $('#editOtherMedInfoBtn').show(), $('#saveOtherMedInfoBtn').hide(), $(this).hide()" style="font-size:15px; display: none; " class="fa fa-close clickover pointer text-danger"></i>
                        <?php endif; ?>
                    </dd>
                </dl>


            </div>
        </div>
        <div id="uploadSignature"  style="width:20%; margin: 50px auto;"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="panel panel-primary" style='width:100%;'>
                <div class="panel-heading clearfix">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h6>Upload Signature</h6>

                </div>
                <div class="panel-body">
                    <?php
                    $attributes = array('class' => '', 'id' => 'importCSV', 'style' => 'margin-top:20px;');
                    echo form_open_multipart(base_url() . 'main/do_upload', $attributes);
                    ?>
                    <input style="height:35px;" class="btn-mini" type="file" name="userfile" size="20" />
                    <br /><br />

                    <input type="hidden" name="picture_option" value="sign" />
                    <input type="submit" value="upload" class="btn-info"/>
                    <input type="hidden" name="id" value="<?php echo $user_id ?>" />
                    <input type="hidden" name="location" value="<?php echo $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>" />

                    </form>
                </div>
            </div>
        </div>
        <?php if ($this->session->position_id != 39): ?>
            <div style="padding-top: 15px;" class="tab-pane" id="enrollmentRequirements">
                <h3>Checklist</h3>
                <?php
                $data['stid'] = $students->uid;
                $data['level'] = $students->gl_id;
                echo $this->load->view('checkListPerDept', $data);
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>





</div>
<div class="span7">


</div>

</div>
<?php $this->load->view('ovrLoadSubj') ?>
<?php $this->load->view('imgCrop'); ?>

<script type="text/javascript">

    $(document).ready(function () {

        $(".clickover").clickover({
            placement: 'right',
            html: true
        });
        $("#Feduc_attain").select2();
        $(".tip-top").tooltip();
    });
    $('#profile_tab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });

    function updateIfRegular(val, uid) {
        $.ajax({
            type: 'GET',
            url: '<?php echo base_url() . 'registrar/updateIfRegular/' ?>' + val + '/' + uid,
            success: function () {
                location.reload();
            }
        });
    }

    function saveMobile(user_id, mobile_no, column)
    {
        var url = "<?php echo base_url() . 'hr/saveContacts/' ?>";
        $.ajax({
            type: "POST",
            url: url,
            data: 'user_id=' + user_id + '&mobile_no=' + mobile_no + '&column=' + column + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
            success: function (data)
            {
                $('#a_mobile').html(mobile_no)
                alert('Successfully Updated');
            }
        })
    }

    function maxMin(body, action)
    {
        if (action == "max") {
            $('#' + body + 'Min').removeClass('hide');
            $('#' + body + 'Max').addClass('hide')
            $('#' + body + 'Body').removeClass('hide fade');
            $('#name_header').html('')
            $('#attend_widget').attr('style', 'max-height: 250px; overflow-y: scroll;');
            //$('#attendance_container').attr('style', 'max-height: 250px; overflow-y: scroll;');
            $('#attend_widget_body').attr('style', 'max-height: 300px; overflow-y: scroll;');
        } else {
            $('#' + body + 'Min').addClass('hide')
            $('#' + body + 'Max').removeClass('hide');
            $('#' + body + 'Body').addClass('hide fade');
            $('#name_header').html($('#name').html())
            $('#attend_widget').attr('style', 'max-height:auto');
            //$('#attendance_container').attr('style', 'max-height:auto');
            $('#attend_widget_body').attr('style', 'max-height:auto');

        }
    }

    function getProvince(value)
    {
        var url = "<?php echo base_url() . 'main/getProvince/' ?>" + value;
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            data: 'csrf_test_name=' + $.cookie('csrf_cookie_name'),
            success: function (data)
            {
                $('#inputProvince').val(data.name)
                $('#inputPID').val(data.id)
            }
        })
    }

    function updateEducAttain(mf, btn)
    {
        var pk = '<?php echo base64_encode('u_id') ?>'
        var tbl = '<?php echo base64_encode('profile_parent') ?>'
        var pk_value = $(btn).attr('u-id');
        if (mf == 'F') {
            var column = 'f_educ';
        } else {
            var column = 'm_educ';
        }
        var value = $('#' + mf + '_educAttain').val()
        var id = mf + '_educAttainValue'

        updateProfile(pk, tbl, pk_value, column, value, id)

    }

    function updateOccupation(occ, user_id, mf)
    {
        var url = "<?php echo base_url() . 'registrar/editOccupation/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'value=' + occ + '&owner=' + user_id + '&mf=' + mf + '&sy=' + '<?php echo $students->school_year ?>' + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //alert(data)
                if (mf == 'f') {
                    $('#a_f_occupation').html(data)
                    $('#a_f_occupation').show()
                    $('#f_occupation').hide()
                } else {
                    $('#a_m_occupation').html(data)
                    $('#a_m_occupation').show()
                    $('#m_occupation').hide()
                }



            }
        });

        return false;
    }

    function editId_number(idNum, id)
    {
        var editedIdNum = $('#input_' + id).val();
        var url = "<?php echo base_url() . 'registrar/editIdNumber/' ?>"
        $.ajax({
            type: "POST",
            url: url,
            data: "origIdNumber=" + idNum + "&editedIdNumber=" + editedIdNum + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$('#Pos').show();
                $('#a_' + id).html(data)
                $('#a_' + id).show()
                $('#input_' + id).hide()
            }
        });

        return false;
    }


    function editAddressInfo()
    {
        var url = "<?php echo base_url() . 'registrar/editAddressInfo/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'street=' + $('#street').val() + '&user_id=' + $('#address_user_id').val() + '&barangay=' + $('#barangay').val() + '&city=' + $('#city').val() + '&province=' + $('#inputPID').val() + '&address_id=' + '<?php echo $user_id ?>' + '&zip_code=' + $('#zip_code').val() + '&sy=' + '<?php echo $students->school_year ?>' + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$('#address_span').html(data);
                location.reload();
            }
        });

        return false;
    }

    function editBasicInfo()
    {
        var name_id = $('#name_id').val();
        //alert($('#lastname').val() + ' ' + $('#firstname').val() + ' ' + $('#middlename').val() + ' ' + $('#rowid').val() + ' ' + $('#st_user_id').val() + ' ' + $('#pos').val())
        var url = "<?php echo base_url() . 'registrar/editBasicInfo/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'lastname=' + $('#lastname').val() + '&firstname=' + $('#firstname').val() + '&middlename=' + $('#middlename').val() + '&rowid=' + $('#rowid').val() + '&user_id=' + $('#st_user_id').val() + '&pos=' + $('#pos').val() + '&sy=' + '<?php echo $students->school_year ?>' + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                $('#' + name_id).html(data);
                // location.reload;
            }
        });

        return false;
    }

    function editBdate(cal_id, owner)
    {
        var url = "<?php echo base_url() . 'calendar/editBdate/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'bDate=' + cal_id + '&owner=' + owner + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                $('#a_bdate').show()
                $('#bdate').hide()
                $('#a_bdate').html(cal_id)

            }
        });

        return false;
    }

    function editEnBdate(cal_id, owner)
    {
        var url = "<?php echo base_url() . 'calendar/editEndate/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'enDate=' + cal_id + '&owner=' + owner + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                $('#a_enDate').show()
                $('#enDate').hide()
                $('#enDate').html(cal_id)

            }
        });

        return false;
    }

    function updateProfile(pk, table, pk_id, column, value, id)
    {
        var url = "<?php echo base_url() . 'users/editProfile/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'id=' + pk_id + '&column=' + column + '&value=' + value + '&tbl=' + table + '&pk=' + pk + '&sy=<?php echo $students->school_year ?>' + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$("form#quoteForm")[0].reset()
                $('#a_' + id).show()
                $('#' + id).hide()
                $('#a_' + id).html(data.msg)

            }
        });

        return false; // avoid to execute the actual submit of the form.
    }

    function saveProfileLevel()
    {
        var hash = '<?php echo $this->uri->segment(3) ?>';
        var user_id = $('#admission_user_id').val();
        var st_id = $('#st_id').val();
        var section_id = $('#inputSection').val();
        var grade_id = $('#inputGrade').val();
        var school_year = $('#inputEditSY').val();
        var strand_id = $('#inputStrand').val();

        switch (grade_id)
        {
            case '10':
            case '11':
                var specs = $('#inputSpecialization').val();
                break;
            default:
                specs = 0;
                break;
        }

        var url = "<?php echo base_url() . 'users/editProfileLevel/' ?>"; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'st_id=' + st_id + '&user_id=' + user_id + '&specs=' + specs + '&school_year=' + school_year + '&section_id=' + section_id + '&grade_id=' + grade_id + '&strand_id=' + strand_id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$("form#quoteForm")[0].reset()
                $('#a_section').html(data.section)
                $('#a_grade').html(data.level)
                alert(data.msg)
                document.location = '<?php echo base_url('registrar/viewDetails') ?>/' + hash + '/' + school_year

            }
        });

        return false; // avoid to execute the actual submit of the form.
    }

    function selectSection(level_id) {
        var url = "<?php echo base_url() . 'registrar/getSectionByGL/' ?>" + level_id; // the script where you handle the form input.

        $.ajax({
            type: "POST",
            url: url,
            data: "level_id=" + level_id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                $('#inputSection').html(data);
                switch (level_id)
                {
                    case '10':
                    case '11':
                        $('#tle_specs').show();
                        $('#sh_strand').hide();
                        break;
                    case '12':
                    case '13':
                        $('#tle_specs').hide();
                        $('#sh_strand').show();
                        break;
                    default:
                        $('#tle_specs').hide();
                        $('#sh_strand').hide();
                        break;
                }
            }
        });

        return false;
    }

    function saveGender()
    {
        var url = "<?php echo base_url() . 'users/editProfile/' ?>"; // the script where you handle the form input.
        var table = '<?php echo base64_encode('esk_profile') ?>'
        var pk = '<?php echo base64_encode('user_id') ?>'
        var st_id = '<?php echo $students->u_id ?>'
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'id=' + st_id + '&column=sex&value=' + $('#inputGender').val() + '&tbl=' + table + '&pk=' + pk + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$("form#quoteForm")[0].reset()
                $('#st_sex').html(data.msg)

            }
        });
        return false;
    }

    function saveReligion()
    {
        var url = "<?php echo base_url() . 'users/editProfile/' ?>"; // the script where you handle the form input.
        var table = '<?php echo base64_encode('esk_profile') ?>'
        var pk = '<?php echo base64_encode('user_id') ?>'
        var st_id = '<?php echo $students->u_id ?>'
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'id=' + st_id + '&column=rel_id&value=' + $('#inputReligion').val() + '&tbl=' + table + '&pk=' + pk + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$("form#quoteForm")[0].reset()
                $('#a_religion').html(data.msg)

            }
        });
        return false;
    }

    function saveMotherTongue() {
        var url = "<?php echo base_url() . 'users/editProfile/' ?>"; // the script where you handle the form input.
        var table = '<?php echo base64_encode('esk_profile_students') ?>'
        var pk = '<?php echo base64_encode('user_id') ?>'
        var st_id = '<?php echo $students->u_id ?>'
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'id=' + st_id + '&column=mother_tongue_id&value=' + $('#inputMotherTongue').val() + '&tbl=' + table + '&pk=' + pk + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$("form#quoteForm")[0].reset()
                $('#a_motherTongue').html(data.msg)

            }
        });
        return false;
    }

    function saveEthnicGroup() {
        var url = "<?php echo base_url() . 'users/editProfile/' ?>"; // the script where you handle the form input.
        var table = '<?php echo base64_encode('esk_profile') ?>'
        var pk = '<?php echo base64_encode('user_id') ?>'
        var st_id = '<?php echo $students->u_id ?>'
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'id=' + st_id + '&column=ethnic_group_id&value=' + $('#inputEthnicGroup').val() + '&tbl=' + table + '&pk=' + pk + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                //$("form#quoteForm")[0].reset()
                $('#a_ethnicGroup').html(data.msg)

            }
        });
        return false;
    }

    function saveNewValue(table) {
        var db_table = $('#' + table).attr('table');
        var db_column = $('#' + table).attr('column')
        var pk = $('#' + table).attr('pk')
        var retrieve = $('#' + table).attr('retrieve')
        var db_value = $('#add' + db_column).val()
        var url = "<?php echo base_url() . 'registrar/saveNewValue/' ?>"// the script where you handle the form input.

        $.ajax({
            type: "POST",
            url: url,
            data: "table=" + db_table + "&column=" + db_column + "&value=" + db_value + "&pk=" + pk + "&retrieve=" + retrieve + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                $('#input' + db_column).html(data);
            }
        });

        return false;
    }

    function imgSignUpload(id) {
        //alert(id + ' ' + $('#stdUserID').val());
        $('#stdUID').val($('#stdUserID').val());
        $('#picture_option').val(id);
        $('#imgUpload').modal('show');
    }


    $(document).ready(function () {
        $('#imgCrop').click(function () {
            $('#stdUID').val($('#stdUserID').val());
            $('#picture_option').val($(this).data('id'));
            $('#imgUpload').modal('show');
        });

        $('#saveOvrSubj').click(function () {
            var grade_level = $('#grade_level').val();
            var section = $('#selectSection').val();
            var subject = $('#selectSubject').val();
            var stid = '<?php echo $students->uid ?>';
            var ifRegular = '<?php echo $students->if_regular ?>';
            var term = $('#semSelect').val();
            var url = '<?php echo base_url() . 'registrar/saveOverload' ?>';
//            alert(grade_level + ' ' + section + ' ' + subject + ' ' + term);

            if (grade_level == 0 || section == 0 || subject == 0) {
                $("#errorMsg").append('<div class="alert alert-danger">' +
                        '<span class="glyphicon glyphicon-remove"> </span>' +
                        ' All Fields Should not be Empty!!!' +
                        '</div>');
                $('.alert-danger').delay(500).show(10, function () {
                    $(this).delay(3000).hide(10, function () {
                        $(this).remove();
                    });
                });
//            } else if (grade_level == 12 || grade_level == 13) {
//                if (term == 0) {
//                    $("#errorMsg").append('<div class="alert alert-danger">' +
//                            '<span class="glyphicon glyphicon-remove"> </span>' +
//                            ' Please Select Term' +
//                            '</div>');
//                    $('.alert-danger').delay(500).show(10, function () {
//                        $(this).delay(3000).hide(10, function () {
//                            $(this).remove();
//                        });
//                    });
//                }
            } else {
                $('#ovrSubj').modal('hide');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: 'st_id=' + stid + '&level=' + grade_level + '&section=' + section + '&subject=' + subject + '&ifRegular=' + ifRegular + '&term=' + term + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
                    success: function (data) {

                    }
                });
            }
        });
    });

    function delSelSubj(id) {
        $.confirm({
            title: 'Confirmation Alert!',
            content: 'Are you sure you want to delete this record?',
            buttons: {
                confirm: function () {
                    var url = '<?php echo base_url() . 'registrar/delSelSubj/' ?>' + id;
                    $.ajax({
                        type: 'GET',
                        data: 'id=' + id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
                        url: url,
                        success: function (data) {
                            $.alert('Record Deleted Successfuly!');
                        }
                    });
                },
                cancel: function () {
                    $.alert('Canceled!');
                }
            }
        });
    }

    function genPass(id) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: 'id=' + id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
            url: '<?php echo base_url() . 'registrar/generatePass' ?>',
            success: function (data) {
                if (data.status == 'true') {
                    alert(data.msg);
                    location.reload();
                } else {
                    alert(data.msg);
                    location.reload();
                }
            }
        });
    }
</script>