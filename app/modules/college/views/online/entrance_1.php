
<div id="verifyStudentNumber" class="modal fade col-lg-3 col-xs-12" style="margin:10px auto;" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header clearfix" style="background:#fff;border-radius:15px 15px 0 0; ">
        <div style="width:165px;margin:0 auto;">
            <img src="<?php echo base_url() . 'images/forms/' . $settings->set_logo ?>"  style="width:165px; background: white; margin:0 auto;"/>
        </div>
        <h1 class="text-center"style="font-size:30px; color:black;"><?php echo $settings->set_school_name ?></h1>
        <h6 class="text-center"style="font-size:15px; color:black;"><?php echo $settings->set_school_address ?></h6>
        <h4 class="text-center text-success" >- ONLINE ENROLLMENT SYSTEM -</h4>
    </div>
    <div style="background: #fff; border-radius:0 0 15px 15px ; padding: 0px 10px 10px;">  

        <?php if ($st_id == NULL): 
     //print_r($settings);
            ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="input" style="text-align: center">Please Enter Your Student ID Number</label>
                    <input class="form-control" onkeypress="if (event.keyCode == 13) {
                                requestEntry(this.value)
                            }"  name="studentNumber" type="text" id="studentNumber" placeholder="Type Here">
                </div>
                <div class="form-group" required>
                    <label>Please Select Your Department</label>
                    <select class="form-control" id="departmentSelect">
                        <?php switch ($settings->level_catered):
                            case 0:
                            case 4:
                                ?>
                                    <option value="none">Select a Department</option>
                                    <option value="0">Pre-school</option>
                                    <option value="1">Grade School</option>
                                    <option value="2">Junior High School</option>
                                    <option value="3">Senior High School</option>
                                    <!--<option onclick="alert('Enrollment in this department is being halted at the moment please try again later'); location.reload()" value="4">College</option>-->
                                    <option  value="4">College</option>
                                <?php
                            break;
                            case 1:
                               ?>
                                    <option value="none">Select a Department</option>
                                    <option value="0">Pre-school</option>
                                    <option value="1">Grade School</option>
                                    
                                    <?php
                            break;
                            case 2:
                                ?>
                                    <option value="none">Select a Department</option>
                                    <option value="0">Pre-school</option>
                                    <option value="1">Grade School</option>
                                    <option value="2">Junior High School</option>
                                    
                                <?php    
                            break;    
                            case 3:
                                ?>
                                    <option value="none">Select a Department</option>
                                    <option value="0">Pre-school</option>
                                    <option value="1">Grade School</option>
                                    <option value="2">Junior High School</option>
                                    <option value="3">Senior High School</option>
                                    
                                <?php    
                            break;    
                        endswitch; ?>
                    </select>
                </div>
                 <?php switch ($settings->level_catered):
                    case 0:
                    case 4:
                        ?>
                            <div class="form-group" required>
                                <label>Please Select Semester</label>
                                <select class="form-control" id="semesterSelect">
                                    <option value="none">Please Select Semester</option>
                                    <option selected value="1">First Semester</option>
            <!--                        <option value="2">Second Semester</option>
                                    <option selected value="3">Summer</option>-->
                                </select>
                            </div>
                        <?php
                    break; 
                    case 3:
                        ?>
                            <div class="form-group" required>
                                <label>Please Select Semester</label>
                                <select class="form-control" id="semesterSelect">
                                    <option value="none">Please Select Semester</option>
                                    <option selected value="1">First Semester</option>
            <!--                        <option value="2">Second Semester</option>
                                    <option selected value="3">Summer</option>-->
                                </select>
                            </div>
                        <?php    
                    break;   
                    default:
                    break;    
                endswitch; ?>
            </div>
            <div class="modal-footer">
                <div class="form-group success">
                    <div class="controls">
                        <button id="requestBtn" onclick="requestEntry()" class="btn btn-info btn-block" aria-hidden="true" >REQUEST ENTRY</button>
                        <!--<button class="btn btn-danger btn-block" data-dismiss="modal" aria-hidden="true">Cancel</button>-->
                        <div id="resultSection" class="help-block success" ></div>
                        <p class="text-center">New Student ? <a href="#" onclick="$('#selectNewOption').modal('show')">[ CLICK HERE ]</a></p>
                    </div>

                </div>
            </div>
        <?php else: ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="input" style="text-align: center">Please Enter the One Time Password</label>
                    <input class="form-control" onkeypress="if (event.keyCode == 13) {
                                verifyOTP(this.value)}"  name="otpVerify" type="password" id="otpVerify" placeholder="Type Here">
                </div>
                <p class="text-center">Not able to receive your One-Time Pin?<br /> <a class="text-danger" href="<?php echo base_url('entrance'); ?>">Click here to Request Again</a></p>
                <div class="alert" id="alertMsg">

                </div>
                <input type="hidden" id="department" value="<?php echo $department ?>" />
                <input type="hidden" id="infoS" value="<?php echo $semester ?>" />
            </div>

        <?php endif; ?>    
    </div>     
</div>

<div id="verifyMobileNumbaer" class="modal fade col-lg-2 col-xs-10" style="margin:30px auto;" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="col-lg-12 modal-header" style="background: #FFF; box-shadow: 3px 3px 5px 5px #ccc; border-radius: 5px; border: 1px solid #ccc;">
        <p class="text-center">Please confirm if this is the number you register in the system</p>
        <h5 class="text-center" id="enPhone"></h5>

        <button id="numConfirm" class="btn btn-block btn-success">Yes, It is</button>
        <button id="numReject" class="btn btn-block btn-danger">No, It's not</button>

    </div>
</div>

<div id="selectNewOption" class="modal fade col-lg-2 col-xs-10" style="margin:30px auto;"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="col-lg-12 modal-header" style="background: #FFF; box-shadow: 3px 3px 5px 5px #ccc; border-radius: 5px; border: 1px solid #ccc;">
        <p class="text-center">Please select an option to enroll</p>
        <?php switch ($settings->level_catered):
            case 0:
            case 4:
                ?>
                <button id="gs" onclick="document.location='<?php echo base_url('admission/basicEd/2') ?>'" class="btn btn-block btn-primary">PRE-SCHOOL & <br /> GRADE SCHOOL</button>
                <button id="jhs" onclick="document.location='<?php echo base_url('admission/basicEd/3') ?>'" class="btn btn-block btn-warning">JUNIOR HIGH SCHOOL</button>
                <button id="shs" onclick="document.location='<?php echo base_url('admission/basicEd/4') ?>'" class="btn btn-block btn-success">SENIOR HIGH SCHOOL</button>
                <button id="college" onclick="document.location='<?php echo base_url('admission/college') ?>'" class="btn btn-block btn-danger">COLLEGE LEVEL</button>
                <?php
            break;
            case 1:
               ?>
                <button id="gs" onclick="document.location='<?php echo base_url('admission/basicEd/2') ?>'" class="btn btn-block btn-primary">PRE-SCHOOL & <br /> GRADE SCHOOL</button>

                    <?php
            break;
            case 2:
                ?>
                <button id="gs" onclick="document.location='<?php echo base_url('admission/basicEd/2') ?>'" class="btn btn-block btn-primary">PRE-SCHOOL & <br /> GRADE SCHOOL</button>
                <button id="jhs" onclick="document.location='<?php echo base_url('admission/basicEd/3') ?>'" class="btn btn-block btn-warning">JUNIOR HIGH SCHOOL</button>

                <?php    
            break;    
            case 3:
                ?>
                <button id="gs" onclick="document.location='<?php echo base_url('admission/basicEd/2') ?>'" class="btn btn-block btn-primary">PRE-SCHOOL & <br /> GRADE SCHOOL</button>
                <button id="jhs" onclick="document.location='<?php echo base_url('admission/basicEd/3') ?>'" class="btn btn-block btn-warning">JUNIOR HIGH SCHOOL</button>
                <button id="shs" onclick="document.location='<?php echo base_url('admission/basicEd/4') ?>'" class="btn btn-block btn-success">SENIOR HIGH SCHOOL</button>

                <?php    
            break;    
        endswitch; ?>
    </div>
</div>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>" />
<input type="hidden" id="st_id" value="<?php echo $st_id ?>" />
<script type="text/javascript">
//    var _0x53ce=['Your\x20One-Time\x20Pin\x20is\x20','url','REQUEST\x20ENTRY','&semester=','Sending\x20OTP','#numConfirm','#departmentSelect','click','id=','Sorry\x20Your\x20Id\x20is\x20not\x20registered\x20','&csrf_test_name=','/entrance','Sorry\x20You\x20have\x20entered\x20a\x20wrong\x20One\x20Time\x20Password','location','#verifyStudentNumber','&department=','contact_num','cookie','college/enrollment/requestEntry/','json','Server\x20is\x20still\x20busy\x20processing...Please\x20try\x20again\x20later','csrf_cookie_name','POST','otp','none','encrypt_num','#requestBtn','ajax','val','System\x20is\x20reqeusting\x20entry...Thank\x20you\x20for\x20waiting\x20patiently','#otpVerify','#semesterSelect','modal','#studentNumber','#numReject','show','reload','otp=','Sorry\x20there\x20is\x20no\x20contact\x20information\x20provided\x20by\x20you,\x20Please\x20contact\x20the\x20school\x20registrar\x20for\x20more\x20info','You\x20will\x20receive\x20your\x20One\x20Time\x20Password\x20in\x20a\x20short\x20while.','status','college/enrollment/verifyOTP/','#base_url','Sorry,\x20You\x20need\x20to\x20input\x20your\x20ID\x20Number','html','#department','#st_id','&id=','core/send'];(function(_0x3cf200,_0x53ce8d){var _0x488e9b=function(_0x51acc8){while(--_0x51acc8){_0x3cf200['push'](_0x3cf200['shift']());}};_0x488e9b(++_0x53ce8d);}(_0x53ce,0x164));var _0x488e=function(_0x3cf200,_0x53ce8d){_0x3cf200=_0x3cf200-0x0;var _0x488e9b=_0x53ce[_0x3cf200];return _0x488e9b;};$(document)['ready'](function(){$(_0x488e('0x1'))[_0x488e('0x13')]('show');});var base=$(_0x488e('0x1d'))[_0x488e('0xf')]();var numSend=0x0;function verifyOTP(){var _0x314aa1=$(_0x488e('0x11'))[_0x488e('0xf')]();var _0x541791=$('#infoS')[_0x488e('0xf')]();var _0x1393ed=base+_0x488e('0x1c');$['ajax']({'type':_0x488e('0x9'),'url':_0x1393ed,'data':_0x488e('0x18')+_0x314aa1+_0x488e('0x22')+$(_0x488e('0x21'))['val']()+_0x488e('0x2')+$(_0x488e('0x20'))[_0x488e('0xf')]()+_0x488e('0x27')+_0x541791+_0x488e('0x2e')+$[_0x488e('0x4')]('csrf_cookie_name'),'dataType':_0x488e('0x6'),'beforeSend':function(){$('#requestBtn')[_0x488e('0x1f')](_0x488e('0x10'));},'success':function(_0x41dd8b){if(_0x41dd8b[_0x488e('0x1b')]){document[_0x488e('0x0')]=_0x41dd8b[_0x488e('0x25')];}else{alert(_0x488e('0x30'));document['location']=base+'entrance';}}});return![];}function sendText(_0x38508b,_0x2a40ba,_0x7c7134=''){var _0x32f550=base+_0x488e('0x23');$[_0x488e('0xe')]({'type':_0x488e('0x9'),'url':_0x32f550,'data':{'csrf_test_name':$[_0x488e('0x4')](_0x488e('0x8')),'num':_0x38508b,'msg':_0x2a40ba},'dataType':_0x488e('0x6'),'beforeSend':function(){$('#requestBtn')['html'](_0x488e('0x28'));},'success':function(_0x497e21){if(_0x497e21[_0x488e('0x1b')]!=0x1){numSend++;if(numSend==0xa){alert(_0x488e('0x7'));document[_0x488e('0x0')]=base+_0x488e('0x2f');}else{sendText(_0x38508b,_0x2a40ba);}}else if(_0x497e21[_0x488e('0x1b')]==0x1){alert(_0x488e('0x1a'));document[_0x488e('0x0')]=_0x7c7134;}}});}function requestEntry(){var _0x5d3e0a=$(_0x488e('0x14'))[_0x488e('0xf')]();if(_0x5d3e0a!=''){var _0x3efe3f=base+_0x488e('0x5');if($(_0x488e('0x2a'))[_0x488e('0xf')]()==_0x488e('0xb')){proceed=0x0;alert('Please\x20Select\x20a\x20Department');}else{var _0xd2e4dc=$(_0x488e('0x2a'))[_0x488e('0xf')]();var _0x20fd54=$(_0x488e('0x12'))[_0x488e('0xf')]();if(_0xd2e4dc!=0x4&&_0x20fd54!=0x3){_0x20fd54=0x0;}$[_0x488e('0xe')]({'type':_0x488e('0x9'),'url':_0x3efe3f,'data':_0x488e('0x2c')+_0x5d3e0a+'&department='+_0xd2e4dc+_0x488e('0x27')+_0x20fd54+_0x488e('0x2e')+$[_0x488e('0x4')](_0x488e('0x8')),'dataType':'json','beforeSend':function(){$(_0x488e('0xd'))['html'](_0x488e('0x10'));},'success':function(_0x568fc9){$(_0x488e('0xd'))[_0x488e('0x1f')](_0x488e('0x26'));if(_0x568fc9[_0x488e('0x1b')]){if(_0x568fc9[_0x488e('0x3')]==''){alert(_0x488e('0x19'));}else{$('#verifyMobileNumbaer')['modal'](_0x488e('0x16'));$('#enPhone')[_0x488e('0x1f')](_0x568fc9[_0x488e('0xc')]);$(_0x488e('0x29'))[_0x488e('0x2b')](function(){var _0xab076d=_0x488e('0x24')+_0x568fc9[_0x488e('0xa')]+'.';sendText(_0x568fc9[_0x488e('0x3')],_0xab076d,_0x568fc9[_0x488e('0x25')]);});$(_0x488e('0x15'))[_0x488e('0x2b')](function(){alert('Please\x20Contact\x20the\x20Registrar\x20or\x20the\x20school\x27s\x20Facebook\x20page\x20to\x20change\x20your\x20contact\x20information.');location[_0x488e('0x17')]();});}}else{alert(_0x488e('0x2d'));location[_0x488e('0x17')]();}}});return![];}}else{alert(_0x488e('0x1e'));}}


    $(document).ready(function () {
        $('#verifyStudentNumber').modal('show');

    });

    var base = $('#base_url').val();
    var numSend = 0;

    function verifyOTP()
    {
        var otp = $('#otpVerify').val();
        var semester = $('#infoS').val();
        var url = base + 'college/enrollment/verifyOTP/'; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            data: 'otp=' + otp + '&id=' + $('#st_id').val() +'&department='+$('#department').val()+'&semester='+semester+'&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            dataType: 'json',
            beforeSend: function () {
                $('#requestBtn').html('System is reqeusting entry...Thank you for waiting patiently')
            },
            success: function (data)
            {
                if (data.status)
                {
                    document.location = data.url;
                } else {
                    alert('Sorry You have entered a wrong One Time Password');
                    document.location = base+'entrance';
                }

            }
        });

        return false;

    }


    function sendText(num, msg, urlLocation = "")
    {
        var url = base + 'core/send';

        $.ajax({
            type: "POST",
            url: url,
            data: {
                csrf_test_name: $.cookie('csrf_cookie_name'),
                num: num,
                msg: msg
            }, // serializes the form's elements.
            dataType: 'json',
            beforeSend: function () {
                $('#requestBtn').html('Sending OTP')
            },
            success: function (data)
            {
                if (data.status != 1)
                {
                    numSend++;
                    if (numSend == 10)
                    {
                        alert('Server is still busy processing...Please try again later');
                        document.location = base + '/entrance';
                    } else {
                        sendText(num, msg);
                    }
                } else if (data.status == 1) {
                    alert('You will receive your One Time Password in a short while.');
                    document.location = urlLocation;
                }
            }
        });
    }


    function requestEntry()
    {
        var id = $('#studentNumber').val();
        if (id != "") {
            var url = base + 'college/enrollment/requestEntry/'; // the script where you handle the form input.
            
             if($("#departmentSelect").val() == "none"){
                proceed = 0;
                alert('Please Select a Department')
             }else{
                var department = $("#departmentSelect").val();
                var semester = $('#semesterSelect').val();
                if(department!=4 && semester!=3)
                {
                    semester = 0;
                }
                //alert(semester);
                $.ajax({
                    type: "POST",
                    url: url,
                    data: 'id=' + id +'&department='+department+'&semester='+semester+'&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
                    dataType: 'json',
                    beforeSend: function () {
                        $('#requestBtn').html('System is requesting entry...Thank you for waiting patiently')
                    },
                    success: function (data)
                    {
                        $('#requestBtn').html('REQUEST ENTRY');
                        if (data.status)
                        {
                            // alert(data.contact_num);
                            if (data.contact_num == "")
                            {
                                alert('Sorry there is no contact information provided by you, Please contact the school registrar for more info');
                            } else {
                                $('#verifyMobileNumbaer').modal('show');
                                $('#enPhone').html(data.encrypt_num);


                                $('#numConfirm').click(function () {

                                    var msg = 'Your One-Time Pin is ' + data.otp + '.';
                                    alert(msg);
                                    document.location = data.url;
                                    //sendText(data.contact_num, msg, data.url);
                                });

                                $('#numReject').click(function () {
                                    alert('Please Contact the Registrar or the school\'s Facebook page to change your contact information.');
                                    location.reload();
                                });

                            }
                            //alert(data.otp);
                            // document.location = data.url;
                        } else {
                            alert('Sorry Your Id is not registered ');
                            location.reload();
                        }
                    }
                });

                return false;
             }    
        }else{
            alert('Sorry, You need to input your ID Number');
        }
    }
  
   
</script>