<?php
    $subjects = Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year);
?>
<div class="card card-widget">
    <div class="card-header">
        <h6>Quick Post</h6>
    </div>
    <div class="card-body">
        <div class="col-12">
            <div class="col-12 form-group">
                <textarea class="textarea" id="postDetails" style="font-size: 14px; line-height: 15px; border: 1px solid #dddddd; padding: 10px;"></textarea>
            </div>
            <div class="col-lg-1 float-right mr-1">
                <button type="button" class="btn btn-flat btn-primary" onclick="submitQuickPost()">POST</button>
            </div>
            <div class="col-lg-2 float-right">
                <div class="form-group">
                    <select class="form-control" id="postTarget" onchange="setStudentExceptions($(this).val())">
                        <option value="0">All</option>
                        <?php if($this->session->isOplAdmin == 1):?>
                            <option value="1">Teachers</option>
                        <?php endif; ?>
                            <option value="2">Parents</option>
                        <?php if($this->session->isOplAdmin == 1):?>
                            <option value="3">Grades or Courses</option>
                        <?php endif; ?>
                        <option value="4">Sections and Classes</option>
                    </select>
                    <input type="hidden" id="postTargetID" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="selectInclusion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Select Included Levels</h5>
            </div>
            <div class="modal-body overflow-auto" style="height: 500px;">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                        <input type="search" class="form-control form-control-sm border-left-0" placeholder="Search Class..." />
                    </div>
                </div>
                <div class="form-group row" id="classSelection">
                    <div class="text-center col-12 mt-5 text-success">
                        <i class="fa fa-circle-notch fa-spin fa-5x"></i>
                        <p class="text-dark">LOADING... PLEASE WAIT...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm float-right" onclick="getGrades(this)">Accept</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="selectSections">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Select Included Classes</h5>
            </div>
            <div class="modal-body overflow-auto" style="height: 500px;">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text bg-transparent">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                        <input type="search" class="form-control form-control-sm border-left-0" fac-id="<?php echo ($this->session->isOplAdmin) ? 'NULL' : $this->session->employee_id; ?>" onkeyup="if(event.which === 13) searchSection(this)" placeholder="Search Class..." />
                    </div>
                </div>
                <div class="form-group row" id="sectionSelect">
                    <div class="text-center col-12 mt-5 text-success">
                        <i class="fa fa-circle-notch fa-spin fa-5x"></i>
                        <p class="text-dark">LOADING... PLEASE WAIT...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm float-right" onclick="getClasses(this)">Accept</button>
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    
    function searchSection(inp){
        var val = $(inp).val(),
                emp_id = $(inp).attr('fac-id');
        $.ajax(
        {
            url: "<?php echo site_url('opl/gradeLevelList/classes/'); ?>"+emp_id+"/"+val,
            type: "GET",
            success: function(data){
                 if(data !== null){
                     $("#sectionSelect").html(data);
                 }
            }
       });
    }
    
    function getClasses(btn){
        var parent = $(btn).parent().prev(),
            classes = parent.find(".classes"),
                list = '',
                u = parent.find(".classes:checked");
        if(u.length != 0){
            $.each(classes, function(idx, value){
                if($(value).is(":checked")){
                    list += $(value).attr('sub-id')+",";
                }
            });
            $("#postTargetID").val(list.slice(0, -1));
            parent.parent().parent().parent().modal('hide');
        }else{
            if($("#postTarget").val() === '3' || $("#postTarget") === 3){
                alert("You must select a Grade or Course to proceed");
            }else if($("#postTarget").val() === '4' || $("#postTarget") === 4){
                alert("You must select a Section to proceed");
            }
        }
    }
    
    function getGrades(btn){
        var parent = $(btn).parent().prev(),
                classes = parent.find(".classes"),
                list = '',
                u = parent.find(".classes:checked");
        if(u.length != 0){
            $.each(classes, function(idx, value){
                if($(value).is(":checked")){
                    list += $(value).attr('sub-id')+",";
                }
            });
            $("#postTargetID").val(list.slice(0, -1));
            parent.parent().parent().parent().modal('hide');
        }else{
            if($("#postTarget").val() === '3' || $("#postTarget") === 3){
                alert("You must select a Grade or Course to proceed");
            }else if($("#postTarget").val() === '4' || $("#postTarget") === 4){
                alert("You must select a Section to proceed");
            }
        }
    }
    
    function recheckAll(check){
        $(".classes").prop("checked", $(check).is(":checked"));
    }
    
    function doCheckAll(check){
        var parent = $(check).parent().parent().parent().parent(),
            c = parent.find('.classes'),
            u = parent.find(".classes:checked");
        
        console.info(c.length);
        console.info(u.length);
        if(c.length === u.length){
            parent.find("#allCheck").prop("checked", true);
        }else{
            parent.find("#allCheck").prop("checked", false);
        }
    }
    
    function setStudentExceptions(type){
        switch(type){
            case '3':
                $.ajax(
                {
                   url: "<?php echo site_url('opl/gradeLevelList/grades/'); echo (!$this->session->isOplAdmin) ? $this->session->username : '' ?>",
                   type: "GET",
                   success: function(data){
                        if(data !== null){
                            $("#classSelection").html(data);
                        }
                   }
                });
                $("#selectInclusion").modal();
            break;
            case '4':
                $.ajax(
                {
                   url: "<?php echo site_url('opl/gradeLevelList/classes/'); echo (!$this->session->isOplAdmin) ? $this->session->username : '' ?>",
                   type: "GET",
                   success: function(data){
                        if(data !== null){
                            $("#sectionSelect").html(data);
                        }
                   }
                });
                $("#selectSections").modal();
            break;
        }
    }
</script>
    
<div id="quickPost">

</div>
<?php if(count($post) != 1): ?>
<style>
    @media (min-width: 34em){
        .card-columns{
                -webkit-column-count: 1;
                -moz-column-count: 1;
                column-count: 1;
        }
    }
    @media (min-width: 75em){
        .card-columns{
                -webkit-column-count: 2;
                -moz-column-count: 2;
                column-count: 2;
        }
    }
</style>
<?php else: ?>
<style>
    @media (min-width: 34em){
        .card-columns{
                -webkit-column-count: 1;
                -moz-column-count: 1;
                column-count: 1;
        }
    }
</style>
<?php endif; ?>
<section class="card-columns">
<?php
//print_r($this->session->details);

//if (count($post) > 1):
//    $col = 'col-lg-6';
//else:
//    $col = 'col-lg-12';
//endif;

foreach($post as $p): 
    $avatar = site_url('images'.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.$this->eskwela->getSet()->set_logo);
    if($p->avatar != NULL || $p->avatar != ""):
        if(file_exists(FCPATH."uploads".DIRECTORY_SEPARATOR.$p->avatar)):
            $avatar = site_url('uploads'.DIRECTORY_SEPARATOR.$p->avatar);
        endif;
        if(file_exists(FCPATH."uploads".$this->session->school_year.DIRECTORY_SEPARATOR.'faculty'.DIRECTORY_SEPARATOR.$p->employee_id.DIRECTORY_SEPARATOR.$p->avatar)):
            $avatar = site_url('uploads'.$this->session->school_year.DIRECTORY_SEPARATOR.'faculty'.DIRECTORY_SEPARATOR.$p->employee_id.DIRECTORY_SEPARATOR.$p->avatar);
        endif;
    endif;
?>

    <div class="card direct-chat direct-chat-primary">
      <div class="card-header ui-sortable-handle">
        <div class="user-block">
            <img class="img-circle" width="50" src="<?php echo $avatar ?>" alt="User Image">
            <span class="username"><a href="#"><?php echo ucwords(strtolower($p->firstname.' '.$p->lastname)); ?></a></span>
            <span class="description">Shared publicly - <time class="timeago" datetime="<?php echo $p->op_timestamp; ?>"><?php echo date('F d, Y g:i a', strtotime($p->op_timestamp)) ?></time> </span>
            
        </div>
          <?php if($p->op_owner_id == $this->session->username || $this->session->isOplAdmin || strcmp($this->session->position, "School Administrator") == 0): ?>
          <button type="button" class="btn btn-outline-danger btn-xs float-right" title="Delete Posts" post-id="<?php echo $p->op_id; ?>" onclick="readyDelete(this)"><i class="fa fa-trash fa-xs"></i></button>
          <?php endif; ?>
      </div>
      <!-- /.card-header -->
      <div class="card-body m-2">
            <?php echo $p->op_post; ?>
      </div>
      <!-- /.card-body -->
      <div class="card-footer pt-1 pb-1" style="background: #F0F0F0">
            <a class="text-xs text-primary" style="cursor: pointer;"><i class="fa fa-thumbs-up fa-xs"></i> Like</a>
      </div>
      <!-- /.card-footer-->
    </div>
<?php endforeach; ?>
</section>
<div class="modal rounded" id="confirmDelete">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Post?</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this post?</p>
                <small class="text-muted">Note: This action cannot be undone</small>
                <button type="button" class="btn btn-success btn-sm float-right" id="deletePostBtn" onclick="deletePost(this)">Proceed</button>
                <button type="button" class="btn btn-light btn-sm float-right mr-2" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script type='text/javascript'>
    
    function deletePost(btn){
        var id = $(btn).attr('post-id'),
                modal = $("#confirmDelete");
        $.ajax(
                {
                    url: "<?php echo site_url('opl/deletePost'); ?>",
                    type: "POST",
                    data:{
                        postid: id,
                        csrf_test_name: $.cookie('csrf_cookie_name')
                    },
                    success: function(data){
                        alert(data);
                        location.reload();
                    }
                })
    }
    
    function readyDelete(btn){
        var id = $(btn).attr('post-id'),
                modal = $("#confirmDelete");
        modal.find("#deletePostBtn").attr('post-id', id);
        modal.modal('show');
    }
    
    $(function () {
        $('.textarea').summernote({
            placeholder: "Hey! What's Up! Anything Interesting?"
        });

        submitQuickPost = function ()
        {
            var base = $('#base').val(),
                    post = $('#postDetails').val(),
                    url = base + 'opl/submitQuickPost',
                    target = $("#postTarget").val(),
                    targetid = $('#postTargetID').val();
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    postDetails: post,
                    type: target,
                    targets: targetid,
                    csrf_test_name: $.cookie('csrf_cookie_name')
                }, // serializes the form's elements.
                //dataType: 'json',
                beforeSend: function () {
                    $('#loadingModal').modal('show');
                },
                success: function (data)
                {
                    alert(data);
                    location.reload();
                }
            });

        }

//        $(".timeago").timeago();
    });
</script>