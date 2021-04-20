<!--subject Course-->
<div id="addFinanceOption" class="modal fade" style="width:20%; margin:10px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Add Finance Charges
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>Finance Item</label> <br />
                <select style="width:90%;"  name="inputFinItems" id="inputFinItems" required>
                  <option value="0">Select Item</option> 
                    <?php 
                           foreach ($fin_items as $i)
                             {   
                       ?>                        
                            <option value="<?php echo $i->item_id; ?>"><?php echo $i->item_description; ?></option>
                    <?php }?>
                </select>
                <button onclick="$('#addItemModal').modal('show')" class="btn btn-xs btn-info pull-right"><i class="fa fa-plus fa-fw"></i></button>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="text" id="fin_amount" class="form-control" onclick="$(this).val('')" placeholder="Amount" />
                <input type="hidden" id="course_id" />
                <input type="hidden" id="year_level" />
            </div>
            <div class="form-group">
                <label>School Year</label> <br />
                <select style="width:100%;"  name="inputCSY" id="inputCSY" required>
                  <option value="0">Select School Year</option> 
                    <?php 
                           foreach ($ro_years as $ro)
                             {   
                               $next = ($ro->ro_years+1);
                       ?>                        
                            <option value="<?php echo $ro->ro_years; ?>"><?php echo $ro->ro_years.' - '.$next; ?></option>
                    <?php }?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">Semester</label>
                <select tabindex="-1" id="inputSem" name="inputSem"  class="col-lg-12">
                   <option>Select Semester</option>
                   <option value="1">First Semester</option>
                   <option value="2">Second Semester</option>
                   <option value="3">Summer</option>
                   
               </select>
             </div>
        </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='addFinanceCharges()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-left'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-left'>Cancel</button>&nbsp;&nbsp;
        </div>
        
    </div>
        
</div>


<div id="addItemModal" class="modal fade" style="width:15%; margin:30px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="panel panel-yellow">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Add Finance Item    
        </div>
         <div class="panel-body">
            <div class="form-group">
                <label>Item</label>
                <input type="text" id="fin_item" class="form-control" placeholder="Item" />
            </div>
         </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='addItems()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-left'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-left'>Cancel</button>&nbsp;&nbsp;
        </div>
     </div>
</div>

<div id="editFinItem" class="modal fade" style="width:15%; margin:30px auto 0;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="panel panel-yellow">
        <div class="panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Edit <span id="fin_desc"></span>
            <input type="hidden" id="charge_id" />
        </div>
         <div class="panel-body">
            <div class="form-group">
                <label>Amount</label>
                <input type="text" id="edit_fin_amount" class="form-control" onclick="this.placeholder=$(this).val(), $(this).val('')" placeholder="Item" />
            </div>
         </div>
        <div class="panel-footer clearfix">
            <a href='#'data-dismiss='modal' onclick='editFinanceCharges()' style='margin-right:10px; color: white' class='btn btn-xs btn-success pull-right'>Save</a>
            <button data-dismiss='modal' class='btn btn-xs btn-danger pull-right' style="margin-right:10px;" >Cancel</button>&nbsp;&nbsp;
        </div>
     </div>
</div>


<div id="deleteFinCharges"  style="width:35%; margin: 50px auto;"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-red" style='width:100%;'>
        <div class="panel-heading">
            <h3>
                <i class="fa fa-info-circle fa-fw"></i> Are you Sure you want to delete this finance item, this might be connected to other accounts in the system, make sure you know what you are doing
                ? Please note also that you can't undo this action.
            </h3>
        </div>
        <div class="panel-body">
                <input type="hidden" id="del_charge_id" />
            <div style='margin:5px 0;'>
            <a href='#' id='<?php echo $g->grade_id ?>' data-dismiss='modal' onclick='deleteFinanceCharges()' style='margin-right:10px; color: white;' class='btn btn-xs btn-danger pull-left'>Delete</a>
            <button data-dismiss='modal' class='btn btn-xs btn-warning pull-left'>Cancel</button>&nbsp;&nbsp;
            </div>

        </div>
    </div>
</div>




<script type="text/javascript">
    

         
    function addItems()
     {
         var finItem = $('#fin_item').val();
         
        var url = "<?php echo base_url().'college/finance/addFinanceItem'?>"; // the script where you handle the form input.

        $.ajax({
               type: "POST",
               url: url,
               //dataType:'json',
               data: "finItem="+finItem+'&csrf_test_name='+$.cookie('csrf_cookie_name'), // serializes the form's elements.
               success: function(data)
               {
                   $('#inputFinItems').append(data);
                   $('#addItemModal').modal('hide');
               }
             });

        return false; 
     }
     
     
     

    
    $(document).ready(function() {

          
          $("#inputFinItems").select2();
          $("#inputGradeModal").select2();
          $("#inputCSubject").select2();
    });
</script>

<!-- End of Schedule Modal-->
