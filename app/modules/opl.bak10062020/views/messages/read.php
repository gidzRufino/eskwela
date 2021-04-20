<?php
$recipients = Modules::run('opl/messages/getRecipients', $msg->opl_msg_id);
foreach ($recipients as $r):
    $s[] = base64_encode($r->st_id);
    $name[] = $r->firstname . ' ' . $r->lastname . ' ';
endforeach;

$r_id = implode(',', $s);
?>
<div class="card card-primary card-outline">
    <div class="card-header">
        Read Message <?php echo $this->session->st_id ?>
    </div>
    <div class="card-body">
        <div class="mailbox-read-info">
            <?php $mSender = Modules::run('opl/messages/getSender', base64_encode($msg->sender)); ?>
            <h5>Subject: <?php echo $msg->subject_msg; ?></h5>
            <h6>From: <?php echo ucwords(strtolower($mSender->firstname . ' ' . $mSender->lastname)); ?>
                <span class="mailbox-read-time float-right"><?php echo date('M d, Y H:i:s a', strtotime($msg->date_sent)) ?></span>
            </h6>
            <h6>
                <span class="pull-left">To:&nbsp;</span>
                <?php foreach ($name as $n): ?>
                    <ul class="nav nav-pills pull-left" role='tablist'>
                        <li class=""><span class="badge badge-secondary"><?php echo $n; ?></span>&nbsp;</li>
                    </ul>
                <?php endforeach; ?>
                <br/>
            </h6>
        </div>
        <div class="mailbox-read-message">
            <?php echo html_entity_decode($msg->content) ?>
        </div>
        <div>
            <?php
            $uploads = explode(',', $msg->uploads);
            foreach ($uploads as $u):
                ?>
                <a href="<?php echo base_url() . 'opl/messages/downloadFile/' . base64_encode($u) . '/' . $mSender->account_type . '/' . base64_encode($msg->sender) . '/' . strtotime(date('Y-m-d', strtotime($msg->date_sent))) ?>">
                    <span class="container pointer" style="padding-right: 20px"><?php
                        $ext = end(explode('.', $u));
                        switch ($ext):
                            case 'xls':
                            case 'xlsx':
                                ?>
                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php echo base_url() . 'images/icons/excel.png' ?>" />
                                <?php
                                break;
                            case 'doc':
                            case 'docx':
                                ?>
                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php echo base_url() . 'images/icons/word.png' ?>" />
                                <?php
                                break;
                            case 'pdf':
                                ?>
                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php echo base_url() . 'images/icons/pdf.png' ?>" />
                                <?php
                                break;
                            case 'png':
                            case 'jpg':
                            case 'jpeg':
                                ?>
                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php
                                echo base_url() . 'UPLOADPATH' . $this->session->school_year . '/' . ($mSender->account_type == 5 ? 'students' : 'faculty') . '/' . $msg->sender . '/subjects/' . strtotime(date('Y-m-d', strtotime($msg->date_sent))) . '/' . $u;
                                ?>" />
                                     <?php
                                     break;
                             endswitch;
                             ?>
                        <div class="overlay"><?php echo $u; ?></div>
                    </span>
                </a><?php
            endforeach;
            ?>
        </div>
        <div class="card-footer">
            <?php
            $msgReply = Modules::run('opl/messages/getMsgReply', $msg->opl_msg_id);
            foreach ($msgReply as $mr):
                $rSender = Modules::run('opl/messages/getSender', base64_encode($mr->sender));
                ?>
                <div class="card card-blue card-outline" id="">
                    <div class="card card-header">
                        <h6 style="font-weight: <?php echo (base64_decode($mid) == $mr->msg_recpt_id ? 'bold' : '') ?>">From: <?php echo ucwords(strtolower($rSender->firstname . ' ' . $rSender->lastname)); ?>
                            <span class="mailbox-read-time float-right"><?php echo date('M d, Y H:i:s a', strtotime($mr->date_sent)) ?></span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mailbox-read-message">
                            <?php echo html_entity_decode($mr->content); ?>
                        </div>
                        <div>
                            <?php
                            $rUploads = explode(',', $mr->uploads);
                            foreach ($rUploads as $u):
                                ?>
                                <a href="<?php echo base_url() . 'opl/messages/downloadFile/' . base64_encode($u) . '/' . $rSender->account_type . '/' . base64_encode($mr->sender) . '/' . strtotime(date('Y-m-d', strtotime($mr->date_sent))) ?>">
                                    <span class="container pointer" style="padding-right: 20px"><?php
                                        $ext = end(explode('.', $u));
                                        switch ($ext):
                                            case 'xls':
                                            case 'xlsx':
                                                ?>
                                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php echo base_url() . 'images/icons/excel.png' ?>" />
                                                <?php
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                ?>
                                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php echo base_url() . 'images/icons/word.png' ?>" />
                                                <?php
                                                break;
                                            case 'pdf':
                                                ?>
                                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php echo base_url() . 'images/icons/pdf.png' ?>" />
                                                <?php
                                                break;
                                            case 'png':
                                            case 'jpg':
                                            case 'jpeg':
                                                ?>
                                                <img class="border border-default img-thumbnail" style="width:100px; height: 100px; border:5px solid #fff; padding-right: 10px" src="<?php
                                                echo base_url() . 'UPLOADPATH' . $this->session->school_year . '/' . ($rSender->account_type == 5 ? 'students' : 'faculty') . '/' . $mr->sender . '/subjects/' . strtotime(date('Y-m-d', strtotime($mr->date_sent))) . '/' . $u;
                                                ?>" />
                                                     <?php
                                                     break;
                                             endswitch;
                                             ?>
                                        <div class="overlay"><?php echo $u; ?></div>
                                    </span>
                                </a><?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>           

            <div class="float-right">
                <button type="button" class="btn btn-default" id="msgReply" onclick="$('#replyMsg').show(), $(this).hide(), $('#deleteMsg').hide()"><i class="fas fa-reply"></i> Reply</button>
            </div>
            <button type="button" class="btn btn-default" id="deleteMsg"><i class="fas fa-trash-alt"></i> Delete</button>
            <div class="card card-blue card-outline" id="replyMsg" style="display: none">
                <?php $rSender = Modules::run('opl/messages/getSender', base64_encode($this->session->st_id != '' ? $this->session->st_id : $this->session->employee_id)); ?>
                <div class="card-header">
                    <h6><i class="fas fa-reply"></i> <?php echo ucfirst(strtolower($mSender->firstname . ' ' . $mSender->lastname)); ?>
                        <span class="float-right pointer" onclick="$('#replyMsg').hide(), $('#msgReply').show(), $('#deleteMsg').show()"><i class="fas fa-times"></i></span>
                    </h6>
                </div>
                <div class="card-body">
                    <textarea class="textarea" id="contentReply" placeholder="Hey! What's Up!"
                              style="font-size: 14px; line-height: 50px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <div class="row-fluid">
                            <div class="col-md-4">
                                <input name="files[]" type="file" multiple="multiple" class="maxsize-1024" id="uploadFile"/>
                                <div id="output"><ul></ul></div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm float-right" onclick="sendReply('<?php echo $rSender->account_type ?>')">Send</button><br/>
                    <input type="hidden" id="recipient_id" value="<?php echo base64_encode($msg->sender) ?>" />
                    <input type="hidden" id="sender_id" value="<?php echo base64_encode($this->session->st_id != '' ? $this->session->st_id : $this->session->employee_id) ?>" />
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .container {
        position: relative;
        width: 50%;
        max-width: 300px;
    }

    .image {
        display: block;
        width: 100%;
        height: auto;
    }

    .overlay {
        position: absolute; 
        top: 40px;
        color: #f1f1f1; 
        width: 100%;
        /*transition: .5s ease;*/
        opacity:1;
        color: black;
        font-size: 10px;
        /*padding: 20px;*/
        text-align: center;
    }

</style>

<script type='text/javascript'>
    function sendReply(accType) {
        var sender = $('#sender_id').val();
        var recipient_id = $('#recipient_id').val();
        var msg_id = '<?php echo $msg->opl_msg_id ?>';
        var content = $('#contentReply').val();
        var subjMsg = '<?php echo $msg->subject_msg ?>';
        var subj_id = '<?php echo $subj_id ?>';
        var url = '<?php echo base_url() . 'opl/messages/replyMsg' ?>';
        var recipient = recipient_id + ',' + '<?php echo $r_id ?>';
        var arr = [];
        var ins = document.getElementById('uploadFile').files.length;
        for (var x = 0; x < ins; x++) {
            arr.push(document.getElementById('uploadFile').files[x].name);
        }

//        alert(recipient + ' ' + sender + ' ' + msg_id);

        $.ajax({
            type: 'POST',
            data: 'sender=' + sender + '&recipient=' + recipient + '&msg_id=' + msg_id + '&content=' + content + '&subjMsg=' + subjMsg + '&subj_id=' + subj_id + '&arrUpload=' + arr + '&accType=' + accType + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
            url: url,
            success: function (data) {
                alert(data);
                location.reload();
            }
        });

    }

    function errorMsg(msg, color, tDelay) {
        $('#promptMsg').show().delay(tDelay).queue(function (n) {
            $(this).hide();
            n();
        });
        $('#promptMsg').css('color', color);
        $('#promptMsg').text('Alert: ' + msg);
    }

    function updateTime(value) {
        alert(value);
    }

    $(function () {
        $('.textarea').summernote({
//            placeholder: "Hey! What's Up! Anything Interesting?"
        });

    });


    $(document).ready(function () {
        $("#recipient_id").select2({maximumSelectionSize: 1});
    });

    $('#uploadFile').on('change', function (e) {
        e.preventDefault();
        var form_data = new FormData();
        var ins = document.getElementById('uploadFile').files.length;
        for (var x = 0; x < ins; x++) {
            form_data.append('files[]', document.getElementById('uploadFile').files[x]);
            $("#output ul").append("<li>" + document.getElementById('uploadFile').files[x].name + "</li>");
        }

        $.ajax({
            url: '<?php echo base_url() . 'opl/messages/upload_files' ?>',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'POST',
            success: function (response) {

            }
        });
    });

</script>