<div class="row">
    <div class="col-lg-12">
        <h3 style="margin-top: 20px;" class="page-header">List of Employees
            <div class="btn-group pull-right" role="group" aria-label="">
                <button type="button" class="btn btn-default" onclick="document.location = '<?php echo base_url('hr/payroll') ?>'">Payroll</button>

           <?php if($this->session->userdata('position_id')==1 || $this->session->userdata('position_id')==43): ?>
            <a id="CSVExportBtn" href="<?php echo base_url().'reports/exportTeachersToCsv' ?>" class="btn btn-default btn-success">Export To CSV </a>
                    <a href="#importCsv" data-toggle="modal"  id="uploadAssessment" class="btn btn-warning pull-right" >
                        <i class="fa fa-upload"></i>
                    </a>
                <?php endif; ?>
            </div>

        </h3>


    </div>
</div>


<div class="row-fluid"  id="pagination-table" >
    <div class="col-lg-6">
        <?php
        echo $links;
        ?>
    </div>
    <div class="form-group col-lg-4 pull-right" id='searchBox' style="margin:20px 0;">
        <div class="controls">
            <input autocomplete="off"  class="form-control" onkeydown="searchTeacher(this.value)"  name="searchTeacher" type="text" id="searchTeacher" placeholder="Search Teacher's Family Name" required>
            <input type="hidden" id="teacher_id" name="teacher_id" value="0" />
        </div>
        <div style="min-height: 30px; background: #FFF; width:230px; position:absolute; z-index: 2000; display: none;" class="resultOverflow" id="teacherSearch">

        </div>
    </div>
    <table style="font-size: 12px;" class="table table-striped table-bordered">
        <tr>
            <td></td>
            <td>USER ID</td>
            <td>LAST NAME</td>
            <td>FIRST NAME</td>
            <td>MIDDLE NAME</td>
            <td>POSITION</td>
            <td>STATUS</td>
            <?php 
            $access = $this->session->userdata('position_id');
            if($access==1||$access==2||$access==84||$access==43): ?>
                <td>PW</td>
                <td>Action</td>
            <?php endif; ?>
        </tr>

        <tbody  id="tableDetails">
            <?php
            //print_r($employee);
            foreach ($employee as $s) {
                //echo $s->isActive;
                ?>


                <tr>
                    <td style="width:60px; text-align: center;"><img class="img-circle" style="width:50px;" src="<?php echo base_url() . 'uploads/' . ($s->avatar == "" ? 'noImage.png' : $s->avatar) ?>" /></td>
                    <td><a href="<?php echo base_url('hr/viewTeacherInfo/' . base64_encode($s->uid)) ?>"><?php echo $s->uid; ?></a></td>
                    <td><?php echo strtoupper($s->lastname); ?></td>
                    <td><?php echo strtoupper($s->firstname); ?></td>
                    <td><?php echo strtoupper($s->middlename); ?></td>
                    <td><?php echo $s->position; ?></td>
                    <td style="text-align: center; padding-top: 10px">
                        <?php if ($s->isActive == 1): ?>
                            <span class="badge badge-pill badge-light nowrap clickover pointer" style="cursor: pointer; background-color: green;" rel="clickover"
                                  data-content="
                                      <?php
                                      $data['eStat'] = $s->isActive;
                                      $data['eid'] = $s->employee_id;
                                      $this->load->view('emStatus', $data) ?>">
                                <i class="fa fa-check-circle" title="Active"></i>  Active
                            </span>
                        <?php elseif ($s->isActive == 2): ?>
                            <span class="badge badge-pill badge-light nowrap clickover pointer" style="cursor: pointer; background-color: darkorange;" rel="clickover"
                                  data-content="
                                      <?php
                                      $data['eStat'] = $s->isActive;
                                      $data['eid'] = $s->employee_id;
                                      $this->load->view('emStatus', $data) ?>">
                                <i class="fa fa-exclamation-circle" title="Suspended"></i> Suspended
                            </span>
                        <?php elseif ($s->isActive == 3): ?>
                            <span class="badge badge-pill badge-light nowrap clickover pointer" style="cursor: pointer; background-color: red;" rel="clickover"
                                  data-content="
                                      <?php
                                      $data['eStat'] = $s->isActive;
                                      $data['eid'] = $s->employee_id;
                                      $this->load->view('emStatus', $data) ?>">
                                <i class="fa fa-times-circle" title="Resigned"></i> Resigned
                            </span>
                        <?php else: ?>
                            <span class="badge badge-pill badge-light nowrap clickover pointer" style="cursor: pointer; background-color: gray;" rel="clickover"
                                  data-content="
                                      <?php
                                      $data['eStat'] = $s->isActive;
                                      $data['eid'] = $s->employee_id;
                                      $this->load->view('emStatus', $data) ?>">
                                <i class="fa fa-minus-circle" title="Deactivated"></i> Deactivated
                            </span>
                        <?php endif; ?>

                       
                    </td>

                    <?php if ($this->session->userdata('is_admin') && $this->session->position != "Cashier") { ?>
                        <td><?php echo $s->secret_key; ?></td>
                        <td>
                            <?php if ($s->rfid == "" || $s->rfid == "NULL"): ?>
                                <a href="#addId" data-toggle="modal"  onclick="showAddRFIDForm('<?php echo $s->user_id ?>', 'RFID', '<?php echo $s->uid ?>')" >Add RFID</a>
                            <?php else: ?>
                                <a href="#addId" data-toggle="modal"   onclick="showAddRFIDForm('<?php echo $s->user_id ?>', '<?php echo $s->rfid ?>', '<?php echo $s->uid ?>')" >Edit RFID</a>
                            <?php endif; ?>
                            <a href="<?php echo base_url('hr/viewTeacherInfo/' . base64_encode($s->uid)) ?>#dtr">View DTR</a>
                            <a href="#" onclick="deleteEmployee('<?php echo $s->user_id ?>', '<?php echo $s->employee_id ?>')">Delete Employee</a>
                        </td>
                    <?php } ?>
                </tr> 
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
<!-- Add Identification Card -->
<div id="addId" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div style='width:450px; height:auto; margin:50px auto 0;  background:white;'>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"  aria-hidden="true">&times;</button>
            <h3 id="myModalLabel">Scan Employee's Identification Card</h3>
        </div>
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label" for="input">CARD NUMBER:</label>
                <div class="controls">
                    <input type="text" id="inputCard" onclick="this.value = ''" placeholder="RFID" required>
                    <input type="hidden" id="stud_id" >
                    <input type="hidden" id="emp_id" >
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal">Close</button>
            <button onclick="updateProfile('<?php echo base64_encode('user_id') ?>', '<?php echo base64_encode('esk_profile') ?>', 'rfid')" class="btn btn-primary">Save </button>
            <div id="resultSection" class="help-block" ></div>
        </div>
    </div> 
</div>
<div id="importCsv" style="width:350px; margin: 10px auto 0;" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="panel panel-green">
        <div class="panel-heading">
            <h4>Upload Teachers CSV</h4>
        </div>
        <?php
        $attributes = array('class' => '', 'id' => 'importCSV', 'style' => 'margin-top:20px;');
        echo form_open_multipart(base_url() . 'hr/importTeachers', $attributes);
        ?>
        <div class="panel-body">

            <input style="height:30px" type="file" name="userfile" ><br />
            <input type="submit" name="submit" value="UPLOAD" class="btn btn-success">
        </div>
        <?php
        echo form_close();
        ?>
    </div>

</div>

<script type="text/javascript">

    function showAddRFIDForm(id, st_id, emp_id)
    {
        $('#stud_id').val(id)
        $('#emp_id').val(emp_id)
        $("#inputCard").attr('placeholder', st_id);
        document.getElementById("inputCard").focus()
    }
    function updateProfile(pk, table, column)
    {
        var url = "<?php echo base_url() . 'users/editProfile/' ?>"; // the script where you handle the form input.
        var pk_id = $('#stud_id').val();
        var emp_id = $('#emp_id').val();
        var value = $('#inputCard').val()
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'id=' + pk_id + '&column=' + column + '&value=' + value + '&tbl=' + table + '&pk=' + pk + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                alert('RFID Successfully Saved');
                // updateAttendanceFormat(emp_id, value)
                location.reload();
            }
        });

        return false; // avoid to execute the actual submit of the form.
    }

    function updateAttendanceFormat(user_id, rfid)
    {
        var url = "<?php echo base_url() . 'attendance/updateAttendanceFormat/' ?>" + user_id + '/' + rfid; // the script where you handle the form input.
        $.ajax({
            type: "POST",
            url: url,
            // dataType: 'json',
            data: 'id=' + user_id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                alert(data);
                location.reload();
            }
        });

        return false; // avoid to execute the actual submit of the form.
    }

    function deleteEmployee(user_id, employee_id)
    {
        var rsure = confirm("Are you Sure You Want to delete Employee # ( " + user_id + " ) from the list?");
        if (rsure == true) {
            var url = "<?php echo base_url() . 'hr/deleteEmployee/' ?>"; // the script where you handle the form input.

            $.ajax({
                type: "POST",
                url: url,
                data: "employee_id=" + employee_id + "&user_id=" + user_id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
                // dataType: 'json',
                success: function (data)
                {
                    alert(data)
                    location.reload();
                }
            });

            return false;
        } else {
            location.reload();
        }

    }


    function searchTeacher(value)
    {
        var url = "<?php echo base_url() . 'hr/searchEmployees/' ?>";
        $.ajax({
            type: "POST",
            url: url,
            data: "value=" + value + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            beforeSend: function () {
            },
            success: function (data)
            {
                $('#tableDetails').html(data);
            }
        });

        return false;
    }
    
    function updateStatus() {
        var st_id = $('#eid').val();
        var status = $('#editEmStat').val();
        //alert(st_id + ' ' + status);

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() . 'hr/updateEmStatus' ?>',
            data: 'eid=' + st_id + '&status=' + status + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
            success: function (data)
            {
                location.reload();
            }
        })
    }
</script>
