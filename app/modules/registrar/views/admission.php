<?php
$sy = $settings->school_year;

$is_admin = $this->session->userdata('is_admin');
?>
<style>
    .reqindicators {
        color: red;
        margin-left: 5px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-12 col-xs-12">
            <?php
            switch ($dept):
                case 2:
                    $header = 'Grade School Admission Form';
                    $st = 2;
                    $en = 7;
                    break;
                case 3:
                    $header = 'Junior High School Admission Form';
                    $st = 8;
                    $en = 11;
                    break;
                case 4:
                    $header = 'Senior High School Admission Form';
                    $st = 12;
                    $en = 13;
                    break;
            endswitch;
            ?>
            <h3 style="margin-top: 20px;" class="page-header"><?php echo $header ?><br />
                <small class="muted">Please fill up the form completely</small>
                <button id="saveAdmission" class="btn btn-sm btn-success pull-right "><i class="fa fa-save fa-fw fa-2x"></i></button>
                <button onclick="document.location = '<?php echo base_url('main/dashboard') ?>'" style="margin-right:5px;" class="btn btn-sm btn-warning pull-right"><i class="fa fa-close fa-fw fa-2x"></i></button> 
            </h3>
        </div>
    </div>
</div>

<div class="formHeader row">
    <?php
    $attributes = array('class' => '', 'role' => 'form', 'id' => 'admissionForm', 'onsubmit' => 'event.preventDefault();');
    echo form_open(base_url() . 'registrar/saveAdmission', $attributes);
    ?>
    <div>
        <input class="form-control" style="display:none; margin-bottom: 10px;" onkeyup="checkID(this.value)" name="inputLRN" type="text" id="inputLRN" placeholder="Enter Student ID (LRN)">
        <small class="help-inline hide" style="color:red;" id="infoReply"></small>
        <input name="inputIdNum"  class="pull-left" style="width:200px;" value="" type="hidden" id="inputIdNum" placeholder="<?php echo $sy ?>"> 

        <div class="col-lg-12">
            <div class="form-group col-lg-3 col-xs-12">
                <label>First Name<span class="error">*</span></label>
                <?php
                $inputFirstName = array(
                    'name' => 'inputCFirstName',
                    'id' => 'inputCFirstName',
                    'class' => 'form-control',
                    'placeholder' => 'First Name',
                    'style' => 'margin-bottom:0;'
                );

                echo form_input($inputFirstName);
                ?>
                <div class="text-danger" id="inputCFirstNameEmpty"></div>
            </div>
            <div class="form-group col-lg-3 col-xs-12">
                <label>Middle Name</label>
                <?php
                $inputMiddleName = array(
                    'name' => 'inputCMiddleName',
                    'id' => 'inputCMiddleName',
                    'class' => 'form-control',
                    'placeholder' => 'Middle Name',
                    'style' => 'margin-bottom:0;'
                );

                echo form_input($inputMiddleName);
                ?>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label>Last Name<span class="error">*</span> </label>
                <?php
                $inputLastName = array(
                    'name' => 'inputCLastName',
                    'id' => 'inputCLastName',
                    'class' => 'form-control',
                    'placeholder' => 'Last Name',
                    'style' => ' margin-right:10px; margin-bottom:0;'
                );

                echo form_input($inputLastName);
                ?>
                <div class="text-danger" id="inputCLastNameEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <?php $gLevel = Modules::run('registrar/getGradeLevel'); ?>
                <label>Select Grade Level<span class="error">*</span></label><br />
                <select style="height:35px; width: 100%;" name="getLevel"  id="getLevel" required>
                    <option value="none">Select Grade Level</option>
                    <?php
                    foreach ($gLevel as $g):
                        switch ($dept):
                            case 2:
                                if ($g->dept_id == 1 || $g->dept_id == 2):
                                    ?>
                                    <option value="<?php echo $g->grade_id ?>"><?php echo $g->level ?></option>
                                    <?php
                                endif;
                                break;
                            case 3:
                                if ($g->dept_id == 3):
                                    ?>
                                    <option value="<?php echo $g->grade_id ?>"><?php echo $g->level ?></option>
                                    <?php
                                endif;
                                break;
                            case 4:
                                if ($g->dept_id == 4):
                                    ?>
                                    <option value="<?php echo $g->grade_id ?>"><?php echo $g->level ?></option>
                                    <?php
                                endif;
                                break;
                        endswitch;
                    endforeach;
                    ?>
                </select>
                <div class="text-danger" id="inputCourseEmpty"></div>
<!--                <label>Select Grade Level<span class="error">*</span></label><br />
                <select style="height:35px; width: 100%;" name="getLevel"  id="getLevel" required>
                    <option value="none">Select Grade Level</option>-->
                <?php
//                    if ($st == 2):
                ?>
                <!--                        <option value="14">Nursery</option>
                                        <option value="15">Kinder 1</option>
                                        <option value="1">Kinder 2</option>-->
                <?php
//                    endif;
//                    for ($i = $st; $i <= $en; $i++) :
                ?>
                                        <!--<option value="<?php // echo $i;  ?>"><?php // echo 'Grade ' . ($i - 1);  ?></option>-->
                <?php // endfor; ?>
                <!--                </select>
                                <div class="text-danger" id="inputCourseEmpty"></div>-->
            </div>
        </div>

        <div class="col-lg-12">
            <?php
            if ($dept == 4):
                $strand = Modules::run('registrar/getSeniorHighStrand');
                ?>
                <div class="form-group col-lg-3 col-xs-12">
                    <label>Select Strand<span class="error">*</span></label><br />
                    <select style="height:35px; width: 100%;" name="strand"  id="strand" required>
                        <?php foreach ($strand as $s): ?>
                            <option value="<?php echo $s->st_id ?>"><?php echo $s->strand ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php
            else:
                ?>        
                <input type="hidden" name="strand" id="strand" value="0" />
            <?php endif; ?>
            <div class="form-group col-lg-3 col-xs-12">
                <label>Date of Birth<span class="error">*</span></label>
                <input style="margin-right: 10px;" class="form-control" name="inputBdate" type="date"  data-date-format="yyyy-mm-dd" id="inputBdate" placeholder="Date of Birth" required>
                <div class="text-danger" id="inputBdateEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label>Place of Birth</label>
                <input style="margin-right: 10px;" class="form-control" name="inputPlaceOfBirth" type="text" id="inputPlaceOfBirth" placeholder="Place of Birth" required>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputNationaly">Nationality</label>
                <input class="form-control" name="inputNationality" type="text" id="inputNationality" placeholder="Nationality">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputCReligion">Religion</label>
                <div class="controls">
                    <select name="inputCReligion" id="inputCreligion" style="height:35px; width: 90%;" required>
                        <option>Select Religion</option>
                        <?php
                        foreach ($religion as $r) {
                            if ($r->religion != ""):
                                ?>
                                <option value="<?php echo $r->rel_id; ?>"><?php echo $r->religion; ?></option>

                                <?php
                            endif;
                        }
                        ?>
                    </select>
                    <i class="fa fa-plus fa-sm text-info" style="cursor:pointer" id="addRel" role="button" data-toggle="popover" title="Add Religion" data-content="
                       <form id='addReligionForm' class='form-group row'>
                       <div class='col-lg-12'>
                       <input type='text' class='form-control' placeholder='Type Religion' name='religionName' />
                       </div>
                       <div class='col-lg-12' style='margin-top: 10px;'>
                       <div class='pull-right'>
                       <button type='button' class='btn btn-success btn-xs' onclick='saveReligion(this)'>Save</button>
                       <button type='button' class='btn btn-danger btn-xs' onclick='closeMe(this)'>Close</button>
                       </div>
                       </div>
                       </form>"></i>
                </div>
            </div>
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputGender">Gender<span class="error">*</span></label>
                <div class="controls">
                    <select name="inputCGender" id="inputCGender" style="height:35px; width: 100%;" required>
                        <option value="none">Select Your Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="text-danger" id="inputCGenderEmpty"></div>

            </div>
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputBirthDate">Date Enrolled<span class="error">*</span></label>
                <input class="form-control" name="inputCEdate" type="text" value="<?php echo date('Y-m-d') ?>" data-date-format="yyyy-mm-dd" id="inputEdate" placeholder="Date Enrolled" required>

            </div>
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputBirthDate">School Year<span class="error">*</span></label><br />
                <select tabindex="-1" id="inputCSY" name="inputCSY" style="height:35px; width: 100%;">
                    <option value="none">School Year</option>
                    <?php
                    //for ($ro = 2018; $ro <= date('Y'); $ro++) { //disabled due to enrollment restriction
                    for ($ro = 2019; $ro <= date('Y'); $ro++) {
                        $roYears = $ro + 1;
                        if ($settings->school_year == $ro):
                            $selected = 'Selected';
                        else:
                            $selected = '';
                        endif;
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $ro; ?>"><?php echo $ro . ' - ' . $roYears; ?></option>
                    <?php } ?>
                </select>
                <div class="text-danger" id="inputCSYEmpty"></div>
            </div>
        </div>
        <div class="col-lg-12">
            <?php if ($dept == 4): ?>
                <div class="form-group col-lg-3 col-xs-12">
                    <label>Semester<span class="error">*</span></label><br />
                    <div id="AddedSection">
                        <select name="inputSemester" id="inputSemester" style="height:35px; width: 100%;" required>
                            <?php
                            $sem = Modules::run('main/getSemester');
                            switch ($sem):
                                case 1:
                                    $first = 'Selected';
                                    $second = '';
                                    $third = '';
                                    break;
                                case 2:
                                    $first = '';
                                    $second = 'selected';
                                    $third = '';
                                    break;
                                case 3:
                                    $first = '';
                                    $second = '';
                                    $third = 'Selected';
                                    break;
                            endswitch;
                            ?>
                            <option Selected value="1">First</option>
                            <option <?php //echo $second        ?> value="2">Second</option>
                            <option <?php //echo $third         ?> value="3">Summer</option>
                        </select>
                    </div>
                </div>
                <?php
            else:

            endif;
            ?>
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label">School Last Attended:</label>
                <input style="margin-bottom:0;" class="form-control" name="inputCSLA" type="text" id="inputSLA" placeholder="School Last Attended">
            </div>
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label">Address of School Last Attended:</label>
                <input style="margin-bottom:0;" class="form-control" name="inputCAddressSLA" type="text" id="inputAddressCSLA" placeholder="Address">
            </div>
        </div>
        <div class="col-lg-12 col-xs-12">
            <h4 style="margin-top: 20px;" class="page-header">Contact Information</h4>
        </div>
        <div class="col-lg-12">
            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputAddress">Street</label>
                <input style="margin-bottom:0;" class="form-control" name="inputStreet" type="text" id="inputStreet" placeholder="Street" required>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputAddress">Barangay:<span class="error">*</span></label>
                <input style="margin-bottom:0;" class="form-control"  name="inputBarangay" type="text" id="inputBarangay" placeholder="Barangay" required>
                <div id="inputBarangayEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputCity">City / Municipality:<span class="error">*</span></label>
                <select onclick="getProvince(this.value)" placeholder="Select A Municipality / City" class="populate select2-offscreen " style="width:100%;" multiple="" id="inputMunCity" name="inputMunCity">
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city->cid ?>"><?php echo $city->mun_city . ' [ ' . $city->province . ' ]' ?></option>
                    <?php endforeach; ?>
                </select>
                <!--<input style="margin-bottom:0;" class="form-control"  name="inputMunCity" class="select2-search" type="text" id="inputMunCity" placeholder="City / Municipality" required>-->
                <div class="text-danger" id="inputMunCityEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputState">Province:<span class="error">*</span></label>
                <input style="margin-bottom:0;" class="form-control"  name="inputProvince" type="text" id="inputProvince" placeholder="State / Province" required>
                <input style="margin-bottom:0;" class="form-control"  name="inputPID" type="hidden" id="inputPID" placeholder="State / Province" required>
                <div class="text-danger" id="inputProvinceEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputPostal">Postal Code:<span class="error">*</span></label>
                <input style="margin-bottom:0;" class="form-control"  name="inputPostal" type="text" id="inputPostal" placeholder="Postal Code" required>
                <div class="text-danger" id="inputPostalEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputContact">Contact Number:</label>
                <input style="margin-bottom:0;" class="form-control"  name="inputPhone" type="text" id="inputPhone" placeholder="Phone">
                <div class="text-danger" id="inputPhoneEmpty"></div>
            </div>

            <div class="form-group col-lg-3 col-xs-12">
                <label class="control-label" for="inputEmail">Email:</label>
                <input style="margin-bottom:0;" class="form-control"  name="inputEmail" type="text" id="inputEmail" placeholder="Email">
            </div>
        </div>

        <div class="col-lg-12 col-xs-12">
            <h4 style="margin-top: 20px;" class="page-header">Family Information :
            </h4>
        </div>

        <div id="Parents">
            <div class="col-lg-12 col-xs-12">
                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputFather">Father's First Name</label>
                    <input style="margin-bottom:0;" class="form-control" name="inputFName" type="text" id="inputFName" placeholder="Father's First Name" required>
                </div>

                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputF_occ">Father's Middle Name:</label>
                    <input style="margin-bottom:0;" class="form-control" name="inputFMName" type="text" id="inputFMName" placeholder="Father's Middle Name" required>
                </div>

                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputF_occ">Father's Last Name:</label>
                    <input style="margin-bottom:0;" class="form-control"   name="inputFLName" type="text" id="inputFLName" placeholder="Father's Last Name" required>
                </div>

                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputF_occ">Profession / Occupation:</label>
                    <input style="margin-bottom:0;" class="form-control"   name="inputF_occ" type="text" id="inputF_occ" placeholder="Father's Occupation" required>
                </div>

                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputF_num">Contact Number:</label>
                    <input style="margin-bottom:0;" class="form-control"   name="inputF_num" type="text" id="inputF_num" placeholder="Father's Contact Number" required>
                </div>

                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputPEmail">Email:</label>
                    <input style="margin-bottom:0;" class="form-control"   name="inputPEmail" type="text" id="inputPEmail" placeholder="Father's Email" >
                </div>

                <div class="form-group  col-lg-3 col-md-12">
                    <label class="control-label" for="inputFather">Educational Attainment:</label>
                    <select name="inputFeduc" id="inputFeduc" style="height:35px; width: 100%;" required>
                        <option>Select Educational Attainment</option>
                        <?php foreach ($educ_attain as $EA) { ?>
                            <option value="<?php echo $EA->ea_id ?>"><?php echo $EA->attainment ?></option>
                        <?php } ?>

                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-12">
                    <label class="control-label" for="inputAddress">Father's Office Name</label>
                    <input style="margin-bottom:0;" class="form-control" name="f_officeName" type="text" id="f_officeName" placeholder="Office Name" >
                </div>
                <div class="col-lg-12 no-padding">
                    <h5 style="margin:0;">Father's Office Address:</h5>
                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputAddress">Street</label>
                        <input style="margin-bottom:0;" class="form-control" name="f_officeStreet" type="text" id="f_officeStreet" placeholder="Street" >
                    </div>

                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputAddress">Barangay:</label>
                        <input style="margin-bottom:0;" class="form-control"  name="f_officeBarangay" type="text" id="f_officeBarangay" placeholder="Barangay" >
                    </div>

                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputCity">City / Municipality:</label>
                        <select onclick="getFofficeProvince(this.value)" placeholder="Select A Municipality / City" class="populate select2-offscreen " style="width:100%;" multiple="" id="f_officeMunCity" name="f_officeMunCity">
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city->cid ?>"><?php echo $city->mun_city . ' [ ' . $city->province . ' ]' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputState">Province:</label>
                        <input style="margin-bottom:0;" class="form-control"  name="f_officeProvince" type="text" id="f_officeProvince" placeholder="State / Province" >
                        <input style="margin-bottom:0;" class="form-control"  name="f_officePID" type="hidden" id="f_officePID" placeholder="State / Province" >
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-xs-12" style="border-top: 1px solid gray; padding-top:10px;">
                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputMother">Mother's First Name:</label>
                    <input style="width:220px; margin-right:10px; margin-bottom:0;" class="form-control" name="inputMother" type="text" id="inputMother" placeholder="Mother's First Name" />
                </div>

                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputMother">Mother's Maiden Middle Name:</label>
                    <input style="margin-bottom:0;" class="form-control" name="inputMMName" type="text" id="inputMMName" placeholder="Mother's Middle Name" >
                </div>

                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputMother">Mother's Maiden Last Name:</label>
                    <input style="margin-bottom:0;" class="form-control" name="inputMLName" type="text" id="inputMLName" placeholder="Mother's Last Name" >
                </div>

                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputM_num">Profession / Occupation:</label>
                    <input style="margin-bottom:0;" class="form-control" name="inputM_occ" type="text" id="inputM_occ" placeholder="Mother's Occupation" />
                </div>
                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputM_occ">Contact Number:</label>
                    <input style="margin-bottom:0;" class="form-control" name="inputM_num" type="text" id="inputM_num" placeholder="Mother's Contact Number" />
                </div>

                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputFather">Educational Attainment:</label>
                    <select name="inputMeduc" id="inputMeduc" style="height:35px; width: 100%;" required>
                        <option>Select Educational Attainment</option>
                        <?php foreach ($educ_attain as $EA) { ?>
                            <option value="<?php echo $EA->ea_id ?>"><?php echo $EA->attainment ?></option>
                        <?php } ?>

                    </select>
                </div>
                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputAddress">Mother's Office Name</label>
                    <input style="margin-bottom:0;" class="form-control" name="m_officeName" type="text" id="m_officeName" placeholder="Office Name" />
                </div>
                <div class="col-lg-12 no-padding col-xs-12">
                    <h5 style="margin:0;">Mother's Office Address:</h5>
                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputAddress">Street</label>
                        <input style="margin-bottom:0;" class="form-control" name="m_officeStreet" type="text" id="m_officeStreet" placeholder="Street" />
                    </div>

                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputAddress">Barangay:</label>
                        <input style="margin-bottom:0;" class="form-control"  name=" " type="text" id="m_officeBarangay" placeholder="Barangay" />
                    </div>

                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputCity">City / Municipality:</label>
                        <select onclick="getMofficeProvince(this.value)" placeholder="Select A Municipality / City" class="populate select2-offscreen " style="width:100%;" multiple="" id="m_officeMunCity" name="m_officeMunCity" />
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo $city->cid ?>"><?php echo $city->mun_city . ' [ ' . $city->province . ' ]' ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-3 col-xs-12">
                        <label class="control-label" for="inputState">Province:</label>
                        <input style="margin-bottom:0;" class="form-control"  name="m_officeProvince" type="text" id="m_officeProvince" placeholder="State / Province" />
                        <input style="margin-bottom:0;" class="form-control"  name="m_officePID" type="hidden" id="m_officeProvince" placeholder="State / Province" />
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-xs-12" style="border-top: 1px solid gray; padding-top:10px;">
                <h5>(IN CASE OF EMERGENCY):</h5>
                <div class="form-group col-lg-3 col-xs-12">

                    <label class="control-label" for="inputEmail">Contact Name:</label>
                    <div class="controls">
                        <?php
                        $inputInCaseName = array(
                            'name' => 'inputInCaseName',
                            'id' => 'inputInCaseName',
                            'placeholder' => 'Contact Name',
                            'class' => 'form-control',
                            'style' => 'width:230px;'
                        );

                        echo form_input($inputInCaseName);
                        ?> 

                    </div><div class="text-danger" id="inputInCaseNameEmpty"></div>
                </div> 
                <div class="form-group col-lg-3 col-xs-12">
                    <label class="control-label" for="inputEmail">Contact Number:</label>
                    <div class="controls">
                        <?php
                        $inputInCaseContact = array(
                            'name' => 'inputInCaseContact',
                            'id' => 'inputInCaseContact',
                            'placeholder' => 'Contact Number',
                            'class' => 'form-control',
                            'style' => 'width:230px;'
                        );

                        echo form_input($inputInCaseContact);
                        ?>    

                    </div><div class="text-danger" id="inputInCaseContactEmpty"></div>
                </div>
            </div>
        </div>

    </div>

    <?php
    echo form_close();
    ?>
</div>
<style type="text/css">
    .error {
        color: red;
        margin-left: 5px;
    }
</style>
<script type="text/javascript">


    function saveReligion(btn) {
        var form = $(btn).parent().parent().parent().serialize() + "&csrf_test_name=" + $.cookie('csrf_cookie_name'),
                url = "<?php echo site_url("/college/enrollment/saveReligion"); ?>";
        $.ajax({
            url: url,
            type: "POST",
            dataType: "JSON",
            data: form,
            beforeSend: function () {
                $('#loadingModal').modal('show');
            },
            success: function (data) {
                alert(data.message);
                $('#loadingModal').modal('hide');
                console.info(data.details);
                switch (data.type)
                {
                    case 1:
                        $("#addRel").popover('hide');
                        $("#inputCreligion").html(data.details);
                        break;
                    case 1000:
                        $("#addRel").popover('hide');
                        $("#inputCreligion").html(data.details);
                        break;
                }
            }
        })
    }
    function selectDepartment(value)
    {
        if (value == '1')
        {
            $('#k-12').show();
            $('#collegeAd').hide();
        } else {
            $('#collegeAd').show();
            $('#k-12').hide();
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

    function getFofficeProvince(value)
    {
        var url = "<?php echo base_url() . 'main/getProvince/' ?>" + value;
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            data: 'csrf_test_name=' + $.cookie('csrf_cookie_name'),
            success: function (data)
            {
                $('#f_officeProvince').val(data.name)
                $('#f_officePID').val(data.id)
            }
        })
    }

    function getMofficeProvince(value)
    {
        var url = "<?php echo base_url() . 'main/getProvince/' ?>" + value;
        $.ajax({
            type: "GET",
            url: url,
            dataType: 'json',
            data: 'csrf_test_name=' + $.cookie('csrf_cookie_name'),
            success: function (data)
            {
                $('#m_officeProvince').val(data.name)
                $('#m_officePID').val(data.id)
            }
        })
    }

    function checkID(value)
    {
        var url = "<?php echo base_url() . 'registrar/checkID' ?>"; // the script where you handle the form input.

        $.ajax({
            type: "POST",
            url: url,
            data: 'id=' + value + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            dataType: 'json',
            success: function (data)
            {
                if (data.status)
                {
                    $('#infoReply').html(data.msg)
                    $('#infoReply').fadeIn()
                    $('#inputFirstName').attr('disabled', 'disabled');
                    $('#inputMiddleName').attr('disabled', 'disabled');
                    $('#inputLastName').attr('disabled', 'disabled');
                    // $('#inputLRN').val('');
                } else {
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
        if (pgSelect == 0) {
            document.getElementById('pgSelect').value = '0'
            document.getElementById('Guardian').style.display = 'none'
            document.getElementById("Parents").style.display = ""
        } else if (pgSelect == 1) {
            document.getElementById('pgSelect').value = '1'
            document.getElementById("Guardian").style.display = ""
            document.getElementById("Parents").style.display = "none"
        }
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
            }
        });

        return false;
    }

    function saveNewValue(table) {
        var db_table = $('#' + table).attr('table')
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
                $('#input' + db_column).html(data)
            }
        });

        return false;

    }

    function setId(levelCode) {

        var option = $('#inputOption').val();
        if (option == 2) {
            var url = "<?php echo base_url() . 'registrar/getLatestCollegeNum/' ?>" + levelCode; // the script where you handle the form input.   
        } else {
            url = "<?php echo base_url() . 'registrar/getLatestIdNum/' ?>" + levelCode; // the script where you handle the form input. 
        }


        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: "level_id=" + levelCode + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                var id = parseInt(data.id) + parseInt(1)
                var prefix = '000';
                if (id < 10)
                {
                    prefix = '000';
                } else
                {
                    if (id < 100) {
                        prefix = '00';
                    } else {
                        prefix = '0';
                    }

                }
                var option = $('#inputOption').val();
                if (option == 2) {
                    $('#schoolID').html('( ' +<?php echo $sy . '15' ?> + prefix + id + ' )');
                    $('#inputIdNum').val(<?php echo $sy . '15' ?> + prefix + id);
                } else {
                    $('#schoolID').html('( ' +<?php echo $sy ?> + data.deptCode + levelCode + prefix + id + ' )');
                    $('#inputIdNum').val(<?php echo $sy ?> + data.deptCode + levelCode + prefix + id);
                }
                //console.log(data.deptCode)

            }
        });

        return false;

    }

    function searchGrade() {
        var value = document.getElementById("inputCourse").value;

        getInfo(value)
    }

    function setSection() {


        var data = new Array();

        data[0] = document.getElementById('inputNewSection').value;
        data[1] = document.getElementById('inputGrade').value;


        saveAdmission(data);
    }


    $(document).ready(function () {
        $("#inputMunCity").select2({maximumSelectionSize: 1});
        $("#m_officeMunCity").select2({maximumSelectionSize: 1});
        $("#f_officeMunCity").select2({maximumSelectionSize: 1});
        $("#inputCreligion").select2()
        $("#addRel").popover({
            html: true,
            container: 'body',
            placement: 'bottom'
        });

        $("#saveAdmission").click(function () {
            var base = "<?php echo site_url(); ?>";
            var url = base + 'college/enrollment/saveBasicEdAdmission/1'; // the script where you handle the form input.
            var proceed = 1;
            var warning = " is required";
            if ($("#inputCFirstName").val() === '') {
                proceed = 0;
                $("#inputCFirstNameEmpty").html("<small class='error'>Firstname" + warning + "</small>");
            }
            if ($("#inputCLastName").val() === '') {
                proceed = 0;
                $("#inputCLastNameEmpty").html("<small class='error'>Lastname" + warning + "</small>");

            }
            if ($("#getCourse").val() == "none") {
                proceed = 0;
                $("#inputCourseEmpty").html("<small class='error'>Course" + warning + "</small>");
            }
            if ($("#inputYear").val() == "none") {
                proceed = 0;
                $("#inputYearEmpty").html("<small class='error'>School year" + warning + "</small>");
            }
            if ($("#inputBdate").val() == "") {
                proceed = 0;
                $("#inputBdateEmpty").html("<small class='error'>Birthdate" + warning + "</small>");
            }
            if ($("#inputCGender").val() == 'none') {
                proceed = 0;
                $("#inputCGenderEmpty").html("<small class='error'>Gender" + warning + "</small>");
            }
            if ($("#inputCSY").val() == 'none') {
                proceed = 0;
                $("#inputCSYEmpty").html("<small class='error'>School Year" + warning + "</small>");
            }

            if ($("#inputBarangay").val() == '') {
                proceed = 0;
                $("#inputBarangayEmpty").html("<small class='error'>Barangay" + warning + "</small>");
            }
            if ($("#inputMunCity").val() == null) {
                proceed = 0;
                $("#inputMunCityEmpty").html("<small class='error'>Municipality" + warning + "</small>");
            }
            if ($("#inputProvince").val() == '') {
                proceed = 0;
                $("#inputProvinceEmpty").html("<small class='error'>Province" + warning + "</small>");
            }
            if ($("#inputPostal").val() == '') {
                proceed = 0;
                $("#inputPostalEmpty").html("<small class='error'>Postal" + warning + "</small>");
            }
            if ($("#inputInCaseName").val() == '') {
                proceed = 0;
                $("#inputInCaseNameEmpty").html("<small class='error'>Phone" + warning + "</small>");
            }
            if ($("#inputInCaseContact").val() == '') {
                proceed = 0;
                $("#inputInCaseContactEmpty").html("<small class='error'>Phone" + warning + "</small>");
            }
            if (proceed == 1) {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $('#admissionForm').serialize() + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
                    beforeSend: function () {
                        $('#loadingModal').modal('show');
                    },
                    success: function (data)
                    {
                        $('#saveAdmission').attr('disabled');
                        alert(data);
                        var confirmation = confirm("Do you want to enroll another student?");
                        if (confirmation == true) {
                            location.reload();
                        } else {
                            document.location = base + 'main/dashboard';
                        }
                    }
                });
            } else {
                setTimeout(clearEmptyMessage, 3000);
            }

        });
    });
    function clearEmptyMessage() {
        $(".errmess").remove();
    }

    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })

    $('#first_form').submit(function (e) {
        e.preventDefault();
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var email = $('#email').val();
        var password = $('#password').val();

        $(".error").remove();

        if (first_name.length < 1) {
            $('#first_name').after('<span class="error">This field is required</span>');
        }
        if (last_name.length < 1) {
            $('#last_name').after('<span class="error">This field is required</span>');
        }
        if (email.length < 1) {
            $('#email').after('<span class="error">This field is required</span>');
        } else {
            var regEx = /^[A-Z0-9][A-Z0-9._%+-]{0,63}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/;
            var validEmail = regEx.test(email);
            if (!validEmail) {
                $('#email').after('<span class="error">Enter a valid email</span>');
            }
        }
        if (password.length < 8) {
            $('#password').after('<span class="error">Password must be at least 8 characters long</span>');
        }
    });


</script>
