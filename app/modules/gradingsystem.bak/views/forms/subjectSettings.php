<div id="subjectSettings" style="display: none;">
    <div class="modal-header">
        <div class="pull-right">
            <button class="btn" aria-hidden="true">Cancel</button>
            <button id="subjectSettingsUpdateBtn" class="btn btn-primary">Update</button>
        </div>
        <h4 id="myModalLabel">Subject Settings</h4>
    </div>
    <div class="modal-body">
    <form id="gradingSettingsForm" action="" method="post">  
        <ul class="nav nav-tabs" style="margin-bottom: 0" id="subjectSettingsTab">
            <li class="active"><a href="#curriculum" style="color:black;">
                    Curriculum & Criteria
                </a>
            </li>
            <li><a  href="#criteria" style="color:black;">
                    Percentage / Weights
                </a>
            </li>
        </ul>
        
        <div class="tab-content" style="margin-left: 0; margin-top:10px; overflow-y: hidden;">
            <div class="tab-pane active span7" id="curriculum">
                  <?php $this->load->view('gradingsystem/forms/curriculum'); ?>
            </div>
            <div class="tab-pane span7" id="criteria">
                <?php $this->load->view('gradingsystem/forms/weightSettings'); ?>
            </div>
        </div>
          
      </form> 
   </div>
</div>

<script type="text/javascript">
    
    $('#setGradeSettingsBtn').click(function() {
     
    var url = "<?php echo base_url().'index.php/gradingsystem/setGradeSetting' ?>"; // the script where you handle the form input.

    $.ajax({
           type: "POST",
           url: url,
           data: $("#gradingSettingsForm").serialize(), // serializes the form's elements.
           success: function(data)
           {
               //$("form#addCategoryForm")[0].reset();
                
              //$("#msgResult").innerHTML='<textarea id="inputMessage" name="message"  onclick="" placeholder="Type in Your Message..." style="margin-left:4px;width:550px; "></textarea>'
               //alert(data) // show response from the php script.
               //document.location = '<?php echo base_url()?>gradingsystem/'
               alert('Settings Successfully Saved')
               
               
           }
         });
    
    return false; // avoid to execute the actual submit of the form.
    })
    
    
    
    
</script>