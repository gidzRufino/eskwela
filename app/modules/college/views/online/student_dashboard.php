<?php
switch ($this->session->details->year_level):
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

switch(TRUE):     
    case $this->session->details->school_year != $this->session->school_year:
    case $this->session->details->school_year != $this->session->school_year && $this->session->details->status==0:
        $previous_school_year = $this->session->school_year;
        $currYearLevel = $this->session->details->year_level+1;
    break;
    case $this->session->details->school_year == $this->session->school_year:
        if($this->session->details->status==0):
            $previous_school_year = $this->session->details->school_year-1;
        else:
            $previous_school_year = $this->session->details->school_year;
        endif;
        $currYearLevel = $this->session->details->year_level;
    break;    
endswitch;
?>
<div id="studentDashboard" class="modal fade col-lg-6 col-xs-12" style="margin:10px auto;" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header clearfix" style="background:#fff;border-radius:15px 15px 0 0; ">
        <div class="col-lg-1 col-xs-2 no-padding">
            <img src="<?php echo base_url() . 'images/forms/' . $settings->set_logo ?>"  style="width:50px; background: white; margin:0 auto;"/>
        </div>
        <div class="col-lg-5 col-xs-10">
            <h1 class="text-left no-margin"style="font-size:20px; color:black;"><?php echo $settings->set_school_name ?></h1>
            <h6 class="text-left"style="font-size:10px; color:black;"><?php echo $settings->set_school_address ?></h6>
        </div>
        <?php
        //print_r($this->session->userdata());
        ?>

        <h4 class="text-right" style="color:black;">Welcome <?php echo $this->session->name . '!'; ?></h4>
        <h6 class="text-right" style="color:black;"><?php echo $this->session->details->course.'<br />'.$year_level; ?></h6>
    </div>
    <div style="background: #fff; border-radius:0 0 15px 15px ; padding: 5px 10px 10px; overflow-y: scroll">  
        <div class="modal-body clearfix">
            <div style="width: 100%" class="col-lg-12 no-padding">
                
                <div class="col-lg-12">
                        <?php if(!$this->session->isOld):
                            $this->load->view('upload_req');
                            endif; ?>
                    </div>
                <div class="form-group pull-left">
                    <h4 class="text-left no-margin col-lg-12 col-xs-12 no-padding">Subjects Offered <small>As reflected in the prospectus</small></h4>
                </div>
                <div class="form-group pull-right">
                    <button onclick="$('#searchSubject').modal('show')" title="search more subjects" style="margin-left:5px;" class="btn btn-xs btn-info pull-right"><i style="font-size: 20px" class="fa fa-search-plus"></i></button>
                    <select onclick="getSchedule(this.value)" tabindex="-1" id="inputSem" style="width:200px; font-size: 15px;" class=" ">
                        <option value="0">Please Select Semester</option>
                      <option value="1">First</option>
                        <!--  <option value="2">Second</option>
                        <option value="3">Summer</option>-->
                    </select>
                </div>
            </div>
            <div style="width: 100%; overflow-y: scroll;" class="pull-left col-lg-12" id="schedDetails">

            </div>
        </div> <!--end of modal-body --> 
        
        <div class="modal-footer clearfix" id="confirmGrp">
            
            <div class="col-lg-3 col-md-1 col-xs-1"></div>
            <div class="col-lg-6 col-md-12 col-xs-12">
                <button onclick="submitEnrollmentData()" style="margin: 0 auto; display: none;" class="btn btn-small btn-success btn-block">CONFIRM</button><br />
                <button style="margin: 0 auto" class="btn btn-small btn-danger btn-block">CANCEL</button>
            </div>
        </div>
        <div class="modal-footer clearfix" style="display: none;" id="confirmMsgWrapper">
            <div class="col-lg-3 col-md-1 col-xs-1"></div>
            <div class="col-lg-6 col-md-12 col-xs-12">
                <div class="alert alert-info">
                    <p id="confirmMsg" class="text-center"></p>
                    <button onclick="document.location='<?php  echo base_url('entrance') ?>'" class="btn btn-danger btn-xs">Close</button>
                </div>
            </div>
        </div>

    </div>
    
</div>     
</div>
<input type="hidden" id="base" value="<?php echo base_url(); ?>" />
<input type="hidden" id="studentID" value="<?php echo base64_encode($this->session->details->st_id) ?>" />
<input type="hidden" id="course_id" value="<?php echo $this->session->course_id ?>" />
<input type="hidden" id="year_level" value="<?php echo $currYearLevel; ?>" />
<input type="hidden" id="previous_year_level" value="<?php echo $this->session->details->year_level; ?>" />
<input type="hidden" id="previous_school_year" value="<?php echo $previous_school_year ?>" />
<input type="hidden" id="currentSchoolYear" value="<?php echo $this->session->school_year ?>" />
<input type="hidden" id="previous_semester" value="<?php echo $this->session->details->semester ?>" />
<input type="hidden" id="current_semester" value="<?php echo $this->session->semester ?>" />
<input type="hidden" id="user_id" value="<?php echo $this->session->details->user_id ?>" />
<input type="hidden" id="isOld" value="<?php echo $this->session->isOld ?>" />
<input type="hidden" id="enStatus" value="<?php echo $this->session->details->status ?>" />


<div id="scheduleModal" class="modal fade col-lg-4 col-xs-12" style="margin:15px auto;" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header clearfix alert-info" style="border-radius:15px 15px 0 0; ">
        <h4 class="pull-left">Please Select Schedule</h4>
        <button type="button" data-dismiss="modal" class="pull-right btn btn-xs btn-danger"><i class="fa fa-close"></i></button>
    </div>

    <div style="background: #fff; border-radius:0 0 15px 15px ; overflow: scroll">  
        <div id="schedBody" class="modal-body clearfix">
        </div>    
    </div>    
</div>

<div id="searchSubject" class="modal fade col-lg-4 col-xs-12" style="margin:15px auto;" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header clearfix alert-warning" style="border-radius:15px 15px 0 0; ">
        <h4 class="pull-left">Search Subject</h4>
        <button type="button" data-dismiss="modal" class="pull-right btn btn-xs btn-danger"><i class="fa fa-close"></i></button>
        <input class="form-control" onkeypress="if (event.keyCode == 13) {
                    searchSubjectOffered(this.value)
                }"  name="studentNumber" type="text" id="studentNumber" placeholder="Search Subject Code and press enter" />
    </div>

    <div style="background: #fff; border-radius:0 0 15px 15px ; padding: 0px 10px 10px;  box-shadow:3px 3px 5px 6px #ccc; ">  
        <div id="searchBody" class="modal-body clearfix">
        </div>    
    </div>    
</div>

<script type="text/javascript">
$(document).ready(function () {
        
        $('#inputSem').select2();
        $('#studentDashboard').modal('show');

        $('.modal').on("hidden.bs.modal", function (e) { //fire on closing modal box
            if ($('.modal:visible').length) { // check whether parent modal is opend after child modal close
                $('body').addClass('modal-open'); // if open mean length is 1 then add a bootstrap css class to body of the page
            }
        });
        //hasTimeConflict('08:30','10:30','07:30','11:30');    
    });

    $(function () {

        var totalUnits = 0;

        fetchSearchSubject = function (sub_code, tchr, sec_id, units, timeDay, sub_id, timeFrom, timeTo, day)
        {
            var exist = 0;
            $('#tableSched tr.trSched').each(function () {
                //alert($(this).attr('id'))
                if ($(this).attr('sub_code') === sub_code)
                {
                    exist++;
                    alert('Sorry this subject already exist');
                }
            });


            if (exist == 0) {
                $('#subjectLoadBody').append(
                        '<tr id="tr_' + sub_id + '" class="trSched" sub_code="' + sub_code + '" >' +
                        '<td>' + sub_code + '</td>' +
                        '<td class="hasValue" id="units_' + sub_id + '">' + units + '</td>' +
                        '<td class="hasValue" id="daytime_' + sub_id + '">' + timeDay + '</td>' +
                        '<td class="hasValue" id="instructor_' + sub_id + '">' + tchr + '</td>' +
                        '<td class="text-center"> \n\
                                    <button onclick="loadSchedule(\'' + sub_code + '\')" title="search for schedule" class="btn btn-success btn-xs"><i class="fa fa-search"></i></button>\n\
                                    <button onclick="clearSchedule(\'' + sub_id + '\')" title="clear schedule" class="btn btn-warning btn-xs"><i class="fa fa-minus"></i></button>\n\
                                    <button onclick="removeSubject(\'' + sub_id + '\')" title="remove from the list" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>\n\
                                </td>' +
                        +'</tr>'
                        );
                fetchSchedule(sub_code, tchr, sec_id, units, timeDay, sub_id, timeFrom, timeTo, day);
            }

            $('#searchSubject').modal('hide');

        };


        fetchSchedule = function (sub_code, tchr, sec_id, units, timeDay, sub_id, timeFrom, timeTo, day)
        {
            var condition = 0;
            $('#tableSched tr.trSched').each(function () {
                var dbDay = $(this).attr('day');
                var dbTimeFrom = $(this).attr('time_from');
                var dbTimeTo = $(this).attr('time_to');

                if ($(this).attr('id') !== 'tr_' + sub_id) {
                    //alert($(this).attr('id'))
                    if (dbDay === day) {
                        if (hasTimeConflict(timeFrom, timeTo, dbTimeFrom, dbTimeTo)) {
                            //alert('Sorry, You cannot add this schedule due to conflict');
                            $(this).addClass('alert alert-danger');
                            condition++;
                        }
                    }

                }
            });
            plotSched(sub_code, tchr, sec_id, units, timeDay, sub_id, timeFrom, timeTo, day, condition);


        };

        plotSched = function (sub_code, tchr, sec_id, units, timeDay, sub_id, timeFrom, timeTo, day, condition)
        {
            if (condition == 0) {
                $('#units_' + sub_id).html(units);
                $('#daytime_' + sub_id).html(timeDay);
                $('#instructor_' + sub_id).html(tchr);

                totalUnits += parseInt(units);
                $('#totalUnits').html(totalUnits)

                $('#tr_' + sub_id).attr('sec_id', sec_id);
                $('#tr_' + sub_id).attr('time_from', timeFrom);
                $('#tr_' + sub_id).attr('time_to', timeTo);
                $('#tr_' + sub_id).attr('day', day);
                $('#tr_' + sub_id).attr('sub_code', sub_code);
                $('#tr_' + sub_id).attr('sub_id', sub_id);

                if (totalUnits != 0) {
                    $('#confirmGrp').show();
                }
            }
        };


        removeSubject = function (sub_id)
        {
            totalUnits -= $('#units_' + sub_id).html();
            $('#totalUnits').html(totalUnits);
            $('#tr_' + sub_id).remove();
        };


        clearSchedule = function (sub_id)
        {

            totalUnits -= $('#units_' + sub_id).html();
            $('#totalUnits').html(totalUnits);

            $('#tr_' + sub_id + ' td.hasValue').each(function () {
                $(this).html('');
            });

            $('#tr_' + sub_id).attr('sec_id', '');
            $('#tr_' + sub_id).attr('time_from', '');
            $('#tr_' + sub_id).attr('time_to', '');
            $('#tr_' + sub_id).attr('day', '');
        };


        loadEnrollmentData = function (adm_id=0, st_id, user_id)
        {
            var enrollmentDetails = [];
            $('#tableSched tr.trSched').each(function () {
                var id = {
                    'cl_sub_id'         : $(this).attr('sub_id'),
                    'cl_section'        : $(this).attr('sec_id'),
                    'cl_user_id'        : $('#user_id').val(),
                    'cl_adm_id'         : adm_id
                };
                enrollmentDetails.push(id);
            });
            
            var enrollmentData = JSON.stringify(enrollmentDetails);
            var school_year = $('#previous_school_year').val();
            var semester    = $('#inputSem').val();
            var base = $('#base').val();
            var url = base+'core/saveLoadedSubjects';
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    enData          : enrollmentData,
                    semester        : semester,
                    school_year     : school_year,
                    st_id           : st_id,
                    user_id         : user_id,
                    csrf_test_name  : $.cookie('csrf_cookie_name'),

                }, // serializes the form's elements.
                //dataType: 'json',
                beforeSend: function () {
                    $('#confirmGrp').hide();
                    $('#confirmMsgWrapper').show();
                    $('#confirmMsg').html('Please Wait while system is submitting your request...');
                    
                },
                success: function (data)
                {
                    $('#confirmMsg').html('You have Successfully submitted your application for enrollment, We will notify you in the next 24 to 48 hours once your study load is approved. Thank you for using this online system.');
                    $('.action').remove();
                    
                    //alert(data);
                }
            });

            return false;
           
        }


        getSchedule = function (sem)
        {
            if (sem != 0) {
                var st_id = $('#studentID').val();
                var course_id = $('#course_id').val();
                var year_level = $('#year_level').val();
                var school_year = $('#previous_school_year').val();
                var base = $('#base').val();
                
                var url = base+'college/enrollment/getSubjectLoad/' + st_id + '/' + course_id + '/' + year_level + '/' + sem + '/' + school_year; // the script where you handle the form input.
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        csrf_test_name: $.cookie('csrf_cookie_name'),

                    }, // serializes the form's elements.
                    // dataType:'json',
                    beforeSend: function () {
                        $('#schedDetails').html('System is processing...Thank you for waiting patiently')
                    },
                    success: function (data)
                    {
                        $('#schedDetails').html(data);
                        if (totalUnits != 0) {
                            $('#confirmGrp').show();
                        }
                    }
                });

                return false;
            }

        }



        searchSubjectOffered = function (value)
        {
            var semester = $('#inputSem').val();
            var school_year = $('#currentSchoolYear').val();
            var base = $('#base').val();
            var url = base + 'college/enrollment/searchSubject/' + value + '/' + semester + '/' + school_year; // the script where you handle the form input.
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    csrf_test_name: $.cookie('csrf_cookie_name'),
                }, // serializes the form's elements.
                // dataType:'json',
                beforeSend: function () {
                    $('#searchBody').html('System is processing...Thank you for waiting patiently')
                },
                success: function (data)
                {
                    $('#searchBody').html(data);

                }
            });

            return false;

        };



    });
    
     function submitEnrollmentData ()
        {
            var base = $('#base').val();
            var is_old = $('#isOld').val();
            var currentSem = $('#inputSem').val();
            var course_id = $('#course_id').val();
            var year_level = $('#previous_year_level').val();
            var school_year = $('#previous_school_year').val();
            var st_id = $('#studentID').val();
            var user_id = $('#user_id').val();

            var url = base + 'college/enrollment/saveCollegeRO/'; // the script where you handle the form input.
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    semester: currentSem,
                    course_id: course_id,
                    year_level: year_level,
                    school_year: school_year,
                    st_id: st_id,
                    user_id: user_id,
                    is_old : is_old,
                    csrf_test_name: $.cookie('csrf_cookie_name'),

                }, // serializes the form's elements.
                dataType: 'json',
                beforeSend: function () {
                    $('#btnConfirm').hide();
                    $('#schedBody').html('System is processing...Thank you for waiting patiently');
                },
                success: function (data)
                {
                    if(data.status==true){
                        loadEnrollmentData(data.admission_id, st_id, user_id);
                        console.log(data);
                    }else{
                        alert(data.msg)
                    }
                }
            });

            return false;

        }

    function hasTimeConflict(timeFrom, timeTo, dbFrom, dbTo)
    {
        var cf = timestamp(timeFrom);
        var ct = timestamp(timeTo);
        var tf = timestamp(dbFrom);
        var tt = timestamp(dbTo);

        if (cf >= tf && cf < tt) {
            //alert('conflict 1');
            return true;
        } else if (ct < tt && ct > tt) {
            //alert('conflict 2');
            return true;

        } else if (cf == tf) {
            //alert('conflict 3');
            return true;
        } else {
            //alert('time is available');
            return false;
        }

    }

    function getFinDetails()
    {
        var base = $('#base').val();
        var url = base+ 'student/accounts'; // the script where you handle the form input.
        document.location = url;
    }

    function modalControl(open, close)
    {
        $('#' + open).modal('show');
        $('#' + close).modal('hide');
    }

    function loadSchedule(s_id)
    {
        var semester = $('#inputSem').val();
        var school_year = $('#currentSchoolYear').val();
        var base = $('#base').val();
        var url = base+ 'college/enrollment/loadSchedule/' + s_id + '/' + semester + '/'+school_year; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            data: {
                csrf_test_name: $.cookie('csrf_cookie_name'),

            }, // serializes the form's elements.
            // dataType:'json',
            beforeSend: function () {
                $('#schedBody').html('System is processing...Thank you for waiting patiently')
            },
            success: function (data)
            {
                $('#scheduleModal').modal('show');
                $('#schedBody').html(data);
            }
        });

        return false;

    }
</script>
