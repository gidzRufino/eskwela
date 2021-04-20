<section class="card card-cyan card-outline">
    <div class="card-header">
        <p><i class="fas fa-info-circle fa-2x"></i> Discussions are a good way to encourage your students to think critically about the topic or subject. This is also the page where
    you can put the details of your lessons.</p>
    </div>
    
    <table class="table table-info table-striped">
        <tr>
            <th class="text-center" style="width:20px;">#</th>
            <th class="text-center col-lg-3">Title</th>
            <th class="text-center col-lg-4">Details</th>
            <th class="text-center col-lg-2">Read By</th>
            <th class="text-center col-lg-2">Comments</th>
        </tr>
        <?php 
            $i=1;
            foreach($discussionDetails as $discussion):
                
        ?>
        <tr class="pointer" onclick="document.location='<?php echo base_url('opl/discussionDetails/'.$discussion->dis_sys_code.'/'.$this->session->school_year)?>'">
            <td class="text-center"><?php echo $i++; ?></td>
            <td class="text-center col-lg-3"><?php echo ucwords(strtolower($discussion->dis_title)) ?></td>
            <td class="text-center col-lg-3"><?php echo substr($discussion->dis_details,0,50).'...' ?></td>
            <td class="text-center col-lg-3"><?php echo '0 students' ?></td>
            <td class="text-center col-lg-3"><?php echo '0 comments' ?></td>
        </tr>
        
        <?php
            endforeach;
            ?>
    </table>
</section>