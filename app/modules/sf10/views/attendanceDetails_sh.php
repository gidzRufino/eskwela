<div class="col-lg-12 no-padding" style="margin-bottom: 10px;">
    <div class="alert alert-success" style="margin-bottom: 0; padding: 3px;">
        <h4 class="text-center">Attendance Record
            <a href="#" onclick="$('#attendanceOveride<?php echo $semester ?>').modal('show')" id="addAttendance2">
                <i style="font-size: 25px;" class="pull-right fa fa-clock-o pointer"></i>
            </a>  
        </h4>
    </div>
    <div id="attendRecordsBody">
            <table class="table table-bordered">
                <tr>
                    <th colspan="12" class="text-center alert-danger">Number of School Days <?php echo ($for_school?'':'Present') ?>
                        <small id="confirmMsg" class="muted text-info"></i> </small></th>
                </tr>
                <?php if($semester==1): ?>
                <tr>
                    <?php
                        for($i=6; $i<=10; $i++):
                            $m = ($i<10?'0'.$i:$i);
                            $monthName = date('F', strtotime(date('Y-'.($m>12?(($m-12)<10?'0'.($m-12):($m-12)):$m).'-01')));
                    ?>
                            <td class="text-center"><?php echo $monthName ?></td>
                    <?php
                        endfor;
                        
                    ?>
                </tr>
                <?php
                    if($attendanceDetails):
                ?>
                <tr>
                    <td class="text-center"><?php echo $attendanceDetails->row()->June ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->July ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->August ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->September ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->October ?></td>
                </tr>
                <?php endif; 
                else:
                ?>
                
                <tr>
                    <?php
                        for($i=11; $i<=15; $i++):
                            $m = ($i<10?'0'.$i:$i);
                            $monthName = date('F', strtotime(date('Y-'.($m>12?(($m-12)<10?'0'.($m-12):($m-12)):$m).'-01')));
                    ?>
                            <td class="text-center"><?php echo $monthName ?></td>
                    <?php
                        endfor;
                    
                    ?>
                </tr>
                <?php
                if($attendanceDetails):
                ?>
                <tr>
                    <td class="text-center"><?php echo $attendanceDetails->row()->November ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->December ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->January ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->February ?></td>
                    <td class="text-center"><?php echo $attendanceDetails->row()->March ?></td>
                </tr>
                <?php endif; 
                endif;
                ?>
            </table>    
    </div>
</div>