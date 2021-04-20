<table style="width:100%; padding:0; margin:0;" align="center" class='table table-striped table-bordered'>
                                <tr>
                                    <td style="width:5%; text-align: center;"><h6 id="att_total" style="margin:0px;"><?php echo $records->num_rows() ?></h6></td>
                                    <td>Name of Student</td>
                                    <td>Remarks
                                    <a style="font-size:10px; float: right;" class="help-inline pull-right" 
                                    rel="clickover" 
                                    data-content=" 
                                         <div style='width:100%;'>
                                         <h6>Add Attendance Remark</h6>

                                         <select name='inputRemark' id='inputRemark' class='' required>
                                            <option>Select Remark</option>                     
                                            <option value='1'>Late</option>                     
                                            <option value='2'>Cutting Classes</option>                     

                                          </select>
                                         <div style='margin:5px 0;'>
                                         <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                                         <a href='#' id='Department' data-dismiss='clickover' onclick='saveAttendanceRemarks()' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                                         </div>
                                          "   
                                    class="btn" data-toggle="modal" href="#"> ADD REMARK</a>
                                    </td>
                                </tr>
                                <?php
                                    $i = 1;
                                    //print_r($records);
                                    foreach ($records->result() as $row)
                                    {
                                        $remarks = Modules::run('attendance/getAttendanceRemark', $row->st_id, $row->date); 
                                ?>
                                <tr id="<?php echo $row->user_id; ?>_tr" onmouseout="$('#delete_<?php echo $row->user_id ?>').hide()" onmouseover="$('#delete_<?php echo $row->user_id ?>').show()" >
                                    <td style="padding:5px 0 5px 5px">
                                        <?php if($this->session->userdata('attend_auto')): ?>
                                            <input id="<?php echo $row->user_id; ?>" name="remarksRadio" onclick="getMe('<?php echo $row->u_rfid; ?>')" type="radio" />
                                        <?php else: ?>
                                            <input id="<?php echo $row->user_id; ?>" name="remarksRadio" onclick="getMe('<?php echo $row->st_id; ?>')" type="radio" />
                                        <?php endif; ?>
                                    </td>
                                    <td id="remarks_<?php echo $row->rfid ?>_td"  style="padding:5px 0 5px 5px">
                                        <h6 style="margin:0px; ">
                                            <a id="<?php echo $row->user_id; ?>_name" href="<?php echo base_url();?>registrar/viewDetails/<?php echo base64_encode($row->st_id) ?>"><?php echo strtoupper($row->lastname.', '.$row->firstname) ?></a>

                                        </h6>

                                    </td>

                                    <td>
                                        <strong class="pull-left"><?php echo $remarks->row()->category_name ?></strong>
                                        <?php if($remarks->row()->remarks!=0): ?>
                                            <strong class="pull-right">Remark Given By: <a href="<?php echo base_url().'hr/viewTeacherInfo/'.  base64_encode($remarks->row()->remarks_from) ?>" ><?php echo $remarks->row()->remarks_from ?></a></strong>
                                        <?php endif; ?>
                                        <i style="display:none;" id="delete_<?php echo $row->user_id ?>" class="fa fa-close pull-right pointer" onclick="deleteAttendance('<?php echo $row->att_id ?>','<?php echo $row->user_id;  ?>' )"></i>     
                                    </td>

                                </tr>
                                 <?php
                                    }
                                 ?>
                            </table>