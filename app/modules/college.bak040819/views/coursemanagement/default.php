<div class="row">
    <div class="col-lg-12">
        <h3 style="margin:10px 0;" class="page-header">Course Management Settings
            <div class="btn-group pull-right" role="group" aria-label="">
                <button type="button" class="btn btn-default" onclick="document.location='<?php echo base_url('college') ?>'">Dashboard</button>
                <button type="button" class="btn btn-default" onclick="getAdd('Course')">Add Course</button>
              </div>
        </h3>
    </div>
     <div class="col-lg-12 no-padding">
        <div class="col-lg-12">
            <div style="width:70%; margin: 50px auto">
                <div class="col-lg-12">
                    <div class="col-lg-3"></div>
                    <div class="col-lg-6">
                        <div class="alert alert-info">
                            <h4 class="no-margin text-center"><i class="fa fa-info-circle fa-fw"></i> Click Course to View the Subjects Offered. </h4>
                        </div>
                    </div>
                </div>
                
            <table class="table table-striped table-hover col-lg-6">
                <thead>
                    <tr>
                        <th colspan="3" style="text-align:center;"><H4>COURSES OFFERED</H4></th>
                    </tr>
                    <tr>
                        <th style="width:50%;">Course</th>
                        <th style="width:50%;">Short Code</th>
                        <th></th>
                        <!--<th style="width:10%;">Pre-requisite</th>-->
                    </tr>
                </thead>
                <tbody id="subjectsWrapper" style="overflow-y: scroll;">
                    <?php foreach($courses as $c): ?>
                    <tr class="pointer" onmouseover="$('#sec_name').val('<?php echo $c->course ?>'), $('#course_id').val('<?php echo $c->course_id ?>')" id="<?php echo $c->course_id ?>_li" onclick="$('#collegeCourseSubject').modal('show'), loadSubject('<?php echo $c->course ?>')">
                        <td><?php echo strtoupper($c->course) ?></td>
                        <td><?php echo $c->short_code ?></td>
                    </tr>
                    <?php endforeach; ?>

                </tbody>
    <!--            <tfoot>
                    <tr>
                        <th colspan="3">
                            <?php echo $links ?>
                        </th>
                        <th>

                        </th>
                    </tr>
                </tfoot>-->
            </table>
            </div>

        </div>
    </div> 
</div>
<input type="hidden" id="sec_id" />
<input type="hidden" id="sec_name" />
<input type="hidden" id="grade_id" />
<?php 
$data['ro_year'] = Modules::run('registrar/getROYear'); 
$data['collegeSubjects'] = Modules::run('subjectmanagement/getCollegeSubjects');
$this->load->view('modalForms', $data); ?>
<script type="text/javascript">
    
    $(document).ready(function(){
        $('#dcms_tab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      })
    })
    
    function getAdd(data)
    {
        $('#add'+data).modal('show');
    }
    
    function loadSubject(course)
    {
        $('#courseTitle').html(course);
        var url = '<?php echo base_url().'college/coursemanagement/loadSubject/' ?>'+$('#course_id').val()
         $.ajax({
               type: "GET",
               url: url,
               dataType: 'json',
               data: '', // serializes the form's elements.
               success: function(data)
               {
                   $('#11_Sem').html(data.fyfs);
                   $('#12_Sem').html(data.fyss);
                   $('#1_Sum').html(data.fys);
                   $('#21_Sem').html(data.syfs);
                   $('#22_Sem').html(data.syss);
                   $('#2_Sum').html(data.sys);
                   $('#31_Sem').html(data.tyfs);
                   $('#32_Sem').html(data.tyss);
                   $('#3_Sum').html(data.tys);
                   $('#41_Sem').html(data.fryfs);
                   $('#42_Sem').html(data.fryss);
                   $('#4_Sum').html(data.frys);
               }
             });

        return false;
    }
    
    function getLevel(level)
    {
        alert(level)
        if(level=='k12'){
            $('#k12').show()
            $('#college').hide()
        }else if(level=='college'){
            $('#k12').hide()
            $('#college').removClass('hide')
            
        }
    }
    
    function removeSubjectFromCourse(value)
    {
        
        var url = '<?php echo base_url().'college/coursemanagement/removeSubjectFromCourse/' ?>'+value;
             $.ajax({
                   type: "GET",
                   url: url,
                   data: '', // serializes the form's elements.
                   success: function(data)
                   {    
                       alert(data)
                       $('#tr_'+value).hide();
                   }
                 });

            return false;
    }
    
    function addSection()
    {
        var section = $('#txtAddSection').val()
        var grade_id = $('#grade_id').val()
        var url = '<?php echo base_url().'coursemanagement/addSection/' ?>'+section+'/'+grade_id;
             $.ajax({
                   type: "GET",
                   url: url,
                   dataType: 'json',
                   data: '', // serializes the form's elements.
                   success: function(data)
                   {
                       alert(data.msg)
                       if(data.status){
                            $('#'+grade_id+'_section').append('<li>'+section+'</li>');
                       }
                   }
                 });

            return false;
    }
    
    function addCourse()
    {
        var course = $('#inputCourse').val()
        var short_code = $('#inputShortCode').val()
        var url = '<?php echo base_url().'coursemanagement/addCourse/' ?>'+course+'/'+short_code;
             $.ajax({
                   type: "GET",
                   url: url,
                   dataType: 'json',
                   data: '', // serializes the form's elements.
                   success: function(data)
                   {
                       alert(data.msg)
                       if(data.status){
                            $('#college').append('<li>'+course+'</li>');
                       }
                   }
                 });

            return false;
    }
    
    function deleteSection()
    {
        var section_id = $('#sec_id').val()
        var section = $('#sec_name').val()
        var answer = confirm("Do you really want to delete this Section name '"+section+"'? You cannot undo this action, so be careful.");
        if(answer==true){
            var url = '<?php echo base_url().'coursemanagement/deleteSection/' ?>'+section_id;
             $.ajax({
                   type: "GET",
                   url: url,
                   data: '', // serializes the form's elements.
                   success: function(data)
                   {
                       alert('Successfully Deleted')
                       $('#'+section_id+'_li').hide();
                   }
                 });

            return false;
        }
    }
</script>
<?php $this->load->view('coursemanagement_modal'); ?>