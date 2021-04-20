<div id="verifyOTP" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-header" style="background:#000; border-radius:15px 15px 0 0; ">
            <h1 class="text-center"style="height:30px; color: white ;font-size: 20px;"><?php echo $settings->set_school_name ?></h1>
            <h4 class="text-center" style="color:white;">Final Account Verification</h4>
         </div>
        <div class="modal-body" style="background: white;">
            <div class="form-group">
                <label for="input" style="text-align: center">Verification Code</label>
                <input class="form-control" name="otp" type="password" id="otp" placeholder="Enter Verification Code" />
            </div>
            <div class="control-group hide" id="verifyConfirmWrapper">
                <span id="verifyConfirmation"></span> <br />
                <button class="btn btn-warning btn-xs">Request Another OTP</button>
                
            </div>
        </div>
        <div class="modal-footer" style="background: white;">
            <div class="form-group success">
                <div class="controls">
                    <button id="parentRequestBtn" onclick="verifyParentOTP()" class="btn btn-info btn-block" aria-hidden="true" >VERIFY</button>
                </div>
            </div>
        </div>
    </div>    
</div>
<div id="parentLogin" style="display: none;">
    <div class="modal-body">
        <div class="form-group">
            <label for="input" style="text-align: center">Username</label>
            <input class="form-control"  name="parentUname" type="text" id="parentUname" placeholder="Type Your Username Here" />
        </div>
        <div class="form-group">
            <label for="input" style="text-align: center">Password</label>
            <input class="form-control" onkeypress="if (event.keyCode == 13) {
                        requestParentEntry()}"  name="parentPass" type="password" id="parentPass" placeholder="Type Your Password Here" />
        </div>

        <div class="form-group">
            <a style="font-size: 12px;" data-toggle="modal" href="#signUp">
                <i class="fa fa-pencil"></i>
                Sign Up for an Account
            </a> 
        </div>

    </div>
    <div class="modal-footer">
        <div class="form-group success">
            <div class="controls">
                <button id="parentRequestBtn" onclick="requestParentEntry()" class="btn btn-info btn-block" aria-hidden="true" >REQUEST ENTRY</button>
            </div>
        </div>
    </div>
</div>
<div id="signUp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-header" style="background:#000; border-radius:15px 15px 0 0; ">
            <h1 class="text-center"style="height:30px; color: white ;font-size: 20px;"><?php echo $settings->set_school_name ?></h1>
            <h4 class="text-center" style="color:white;">Sign Up for a New Account</h4>
         </div>
          <div style="background: #fff; border-radius:0 0 15px 15px ; padding: 0px 10px 10px;" class="modal-body" >
            <fieldset >
                <label class="text-center text-info">Please Enter the Emergency Contact Number Registered</label>
                <div style="margin:0 25%; " >
                    <div style="margin-bottom: 15px;">
                        <div id="verifyP" class="form-group input-group ">
                            <input onblur="verifyParent($('#verify').val())" onkeypress="if(event.keycode==13){verifyParent(this.value)}" class="error form-control" id="verify" placeholder="Enter Email or Phone Number" type="text">
                            <span class="input-group-btn">
                                <button class="btn btn-default">
                                    <i id="verify_icon" onclick="verifyParent($('#verify').val())" class="fa fa-search" style="font-size:20px;"></i>
                                </button>   
                            </span>
                        </div>

                        <div id="confirm">
                             <input type="hidden" id="vResults" value="0" />
                             <input type="hidden" id="parent_id" value="0" />
                             <span id="confirmation"></span>
                        </div>

                    </div>
                    <div id="inputForms" class="hidden" >
                        <div class="form-group input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input class="form-control" id="regUname"  placeholder="username" type="text">
                        </div>

                       <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            <input class="form-control"  id="pass0" placeholder="password" type="password" >
                       </div>
                        <div id="pass_error">
                            <div class="form-group input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                                <input onkeyup="checkPass(this.value, $('#pass0').val())" class="error form-control"  id="pass1" placeholder="Retype Password" type="password">

                            </div>
                            <div>
                                 <span id="pass_error_msg"></span>
                           </div>
                        </div>
                    </div>

                </div>

                </fieldset>

            <!--</form>-->  

          <div class="modal-footer">
              <div class="form-group">
                  <div class="controls">
                      <button class="btn"  onclick="location.reload()">Cancel</button>
                      <button id="verifySubmit" onclick="getRegister()"class="btn success hide">SUBMIT</button>
                      <div id="resultSection" class="help-block success" ></div>
                  </div>

              </div>
           </div> 
    </div>

  </div>
</div>  

<script type="text/javascript"> 
    
    var details = {};
    var parent_id = 0;
    
    function requestParentEntry()
    {
        var u = $('#parentUname').val();
        var p = $('#parentPass').val();
        
        var url = base+'opl/p/requestEntry';
        $.ajax({
           type: "POST",
           url: url,
           dataType:'json',
           data: {
               u          : u,
               p          : p,
               csrf_test_name : $.cookie('csrf_cookie_name')
           }, // serializes the form's elements.
           success: function(data)
           {
               alert(data.msg);
               document.location = base+data.url;
           }
        });
    }
    
    function verifyParentOTP()
    {
        var OTP = $('#otp').val();
        
        var url = base+'opl/p/verifyOTP/' ;
        $.ajax({
           type: "POST",
           url: url,
           dataType:'json',
           data: {
               otp      : OTP,
               parent_id  : parent_id,
               csrf_test_name : $.cookie('csrf_cookie_name')
           }, // serializes the form's elements.
           success: function(data)
           {
               if(data.status)
               {
                   document.location = base+data.url;
               }else{
                    alert(data.msg);
                    $('#verifyConfirmWrapper').removeClass('hide');
                    $('#verifyConfirmation').attr('style',"color:red");
                    $('#verifyConfirmation').html(data.msg);
                   
               }
           }
        });
        
    }
    
    function verifyParent(value)
    {
        $('#verify_icon').removeClass('fa-search')
        $('#verify_icon').addClass('fa-spinner fa-spin');
        var url = base+'opl/p/verifyParent/'+value+'/' ;
          $.ajax({
           type: "GET",
           url: url,
           dataType:'json',
           data: 'value='+value, // serializes the form's elements.
           success: function(data)
           {
                $('#loadingModal').modal('hide');
              if(data.status){
                    $('#inputForms').removeClass('hidden')
                    $('#confirmation').html('Hi Parent/Guardian! Please indicate below your desired Username and Password')
                    $('#confirmation').attr('style',"color:#3C6AC4");
                    $('#vResults').val(1);
                    details = data.details;
                    $('#verify_icon').removeClass('fa-spinner fa-spin')
                    $('#verify_icon').addClass('fa-search');
              }else{
                   // $('#regUname').attr('disabled', 'disabled');
                    $('#verifyP').addClass('form-group error');
                    $('#confirmation').attr('style',"color:red");
                    $('#confirmation').html('Sorry, This number cannot be found, Please Contact the School\'s Registrar.');
                    $('#confirmation').fadeOut(5000)
                    $('#verify_icon').removeClass('fa-spinner fa-spin')
                    $('#verify_icon').addClass('fa-search');
                    setTimeout(function(){
                        $("#verify").focus();
                    }, 0);
                   
                    

                }

           }
         });
      
    }
    
    function getRegister()
    {
        var u = $('#regUname').val();
        var p = $('#pass1').val();
        var url = base+'opl/p/registerParent/' ;
          $.ajax({
           type: "POST",
           url: url,
           dataType:'json',
           data: {
               u        : u,
               p        : p,
               details  : details,
               csrf_test_name : $.cookie('csrf_cookie_name')
            }, // serializes the form's elements.
            beforeSend: function(){
                 $('#loadingModal').modal('show');
            },
           success: function(data)
           {
                 $('#loadingModal').modal('hide');
               if(data.status)
               {
                   $('#signUp').modal('hide');
                   $('#verifyOTP').modal('show');
                   parent_id = data.parent_id;
               }else{
                   if(data.codeError=='2')
                   {
                       alert(data.msg);
                       location.reload();
                   }else{
                       alert(data.msg);
                   }
               }
           }
         });
      
    }
    
    
    function checkPass(pass, pass1)
    {
        if(pass1.length == pass.length){
            if(pass!==pass1)
                {
                    $('#pass_error_msg').html("Sorry Password did not match")
                    $('#pass_error_msg').attr('style',"color:red");
                    $('#pass_error').addClass('form-group error');
                    $('#pass_error_msg').fadeOut(5000);

                }else{
                    setTimeout(function(){
                            $('#pass_error').removeClass('error');
                            $('#verifySubmit').removeClass('hide');
                            console.log(JSON.stringify(details));
                    }, 0);


                }
        }
        
    }
 
</script> 