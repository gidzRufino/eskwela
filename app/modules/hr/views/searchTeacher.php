<?php
    //print_r($employee);
foreach ($employee as $s)
{
       //echo $s->isActive;
?>
<tr>
    <td style="width:60px; text-align: center;"><img class="img-circle" style="width:50px;" src="<?php echo base_url().'uploads/'.($s->avatar==""?'noImage.png':$s->avatar)  ?>" /></td>
    <td><a href="<?php echo base_url('hr/viewTeacherInfo/'.base64_encode($s->uid)) ?>"><?php echo $s->uid; ?></a></td>
    <td><?php echo strtoupper($s->lastname); ?></td>
    <td><?php echo strtoupper($s->firstname); ?></td>
    <td><?php echo strtoupper($s->middlename); ?></td>
    <td><?php echo $s->position; ?></td>
    <?php if($this->session->userdata('is_admin')): ?>
    <td style="text-align: center;">
        <?php 
        if($s->isActive){
            ?>
        <img onclick="deactivate('<?php echo $s->uid ?>')" style="cursor: pointer;width:20px" src="<?php echo base_url() ?>images/official.png" alt="official" />
        <?php
        }else{
        ?>
        <img onclick="activate('<?php echo $s->uid ?>')" style="cursor: pointer;width:20px"  src="<?php echo base_url() ?>images/unofficial.png" alt="unofficial" />
        <?php
        }
        ?>
    </td>

    <td><?php echo $s->secret_key; ?></td>
    <td>
        <?php if($s->rfid==""||$s->rfid=="NULL"):?>
        <a href="#addId" data-toggle="modal"  onclick="showAddRFIDForm('<?php echo $s->user_id ?>','RFID','<?php echo $s->uid ?>')" >Add RFID</a>
        <?php else: ?>
        <a href="#addId" data-toggle="modal"   onclick="showAddRFIDForm('<?php echo $s->user_id ?>','<?php echo $s->rfid ?>','<?php echo $s->uid ?>')" >Edit RFID</a>
        <?php endif; ?>
        <a href="<?php echo base_url('hr/viewTeacherInfo/'.base64_encode($s->uid)) ?>#dtr">View DTR</a>
        <a href="#" onclick="deleteEmployee('<?php echo $s->user_id ?>','<?php echo $s->employee_id ?>')">Delete Employee</a>
    </td>
    <?php endif; ?>
</tr> 
<?php 
    } 
?>