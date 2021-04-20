<?php
    switch ($sem):
        case 1:
            $semester = 'First Semester';
        break;
        case 2:
            $semester = 'Second Semester';
        break;
        case 3:
            $semester = 'Summer';
        break;
    endswitch;
?>
<div class="col-lg-12">
    <h3 style="margin:10px 0;" class="page-header">Finance Accounts
            <input type="text" id="rfid" style="position: absolute; left:-1000px;" onchange="scanStudents(this.value)" onload="self.focus();" />
        
        <div class="btn-group pull-right" role="group" aria-label="">
                
            <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url('college') ?>'">Dashboard</button>
            <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url('college/finance') ?>'">Settings</button>
            <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url('college/finance/collectionReport') ?>'">Reports</button>
          </div>
    </h3>
    <div class='col-lg-12'>
        
            <div class="row" id="searchWrapper">
                <div class="col-lg-2"></div>
                <div class="input-group col-lg-8">
                    <input type="text" onkeyup="search(this.value)" id="searchBox"  class="form-control input-lg" aria-label="..." placeholder="Search Name Here" >
                    
                    <div onblur="$(this).hide()" style="min-height: 30px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; z-index: 1000; position: absolute; width: 85%; display: none; margin-top: 45px;" class="resultOverflow" id="searchName">

                    </div>
                    <div class="input-group-btn">
                      <button style="height: 46px; width: 150px;" type="button" class="btn btn-default dropdown-toggle" id="btnControl" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $semester ?> <span class="caret"></span></button>
                      <ul class="dropdown-menu dropdown-menu-right">
                          <li onclick="$('#btnControl').html('First Semester <span class=\'caret\'></span>'), $('#inputSem').val(1)"><a href="#">First Semester</a></li>
                        <li onclick="$('#btnControl').html('Second Semester <span class=\'caret\'></span>'), $('#inputSem').val(2)"><a href="#">Second Semester</a></li>
                        <li onclick="$('#btnControl').html('Summer  <span class=\'caret\'></span>'), $('#inputSem').val(3)"><a href="#">Summer</a></li>
                      </ul> 
                      <input type="hidden" id="inputSem" value="<?php echo $sem ?>" />
                    </div><!-- /btn-group -->
                </div>
                
    
            </div>
            
        <div class='col-lg-12'  id="AccountBody">
            <?php if($id!=NULL): 
                echo Modules::run('college/finance/loadAccountDetails', $id, $sem);
            endif;
?>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    $(document).ready(function(){
        setFocus();
        $('#inputSem').select2();
    })
    
   function loadDetails(st_id)
   {
       var sem = $('#inputSem').val();
       var url = '<?php echo base_url().'college/finance/loadAccountDetails/' ?>'+st_id+'/'+sem;
            $.ajax({
               type: "GET",
               url: url,
               data: "id="+st_id, // serializes the form's elements.
               success: function(data)
               {
                    // $('#AccountBody').html(data);
                    document.location = '<?php echo base_url().'college/finance/accounts/' ?>'+st_id+'/'+sem;
               }
             });

        return false;
   }

   function search(value)
      {
          
          var url = '<?php echo base_url().'search/searchStudentAccounts/' ?>'+value;
            $.ajax({
               type: "GET",
               url: url,
               data: "id="+value, // serializes the form's elements.
               success: function(data)
               {
                     $('#searchName').show();
                     $('#searchName').html(data);
               }
             });

        return false;
      }
     
     
    function numberWithCommas(x) {
            if(x==null){
                x = 0;
            }
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        function setFocus()
    {
        window.setTimeout(function () { 
            document.getElementById("rfid").focus();
        }, 500);
    }
    
    
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
                       document.location = '<?php echo base_url('college/finance/accounts/') ?>'+data.st_id
                       //console.log(data)
                   }
                 });

            return false;  
    }    
     
    

</script>

<?php $this->load->view('financeModals'); 