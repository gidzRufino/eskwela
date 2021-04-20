<div class="col-lg-6">
    <div class="col-lg-12 pull-left">
        <b>Grading System used:</b><br />
        <input id="order" name="gs_type" type="radio" checked  value="2" /> DepEd Order #8 s.2015<br /><br />
        <div class="panel panel-green">
            <div class="panel-heading clearfix">
                <h5>Raw Score Transmutation
                    <!--<i onclick="saveTransmutation()" class="pull-right pointer fa fa-2x fa-save"></i>-->
                </h5>
            </div>
            <div class="panel-body">
                <p>Raw score are being transmuted this way:<br />
                    <br />
                    <b>Percentage Score(PS)</b> = ((Learner's Total RS / Highest Possible Score) * 100%);
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-12 pull-left">
        <div class="panel panel-red">
            <div class="panel-heading clearfix">
                <h5>Weight of Components for each Subject
                    <i onclick="$('#addDOSubjects').modal('show')"class="pull-right pointer fa fa-2x fa-plus"></i>
                </h5>
            </div>
            <div class="panel-body">
                <div id="DO_Container1">
                    <table class="table table-bordered">
                        <tr>
                            <th>Subjects</th>
                            <?php foreach ($components as $c): ?>
                                <th><?php echo $c->component ?></th>
                            <?php endforeach; ?>
                            <th style="width:75px;"></th>
                        </tr>
                        <?php
                        foreach ($subjects as $sub):
                            if ($sub->subject != ""):
                                ?>
                                <tr>
                                    <td><?php echo $sub->subject; ?></td>
                                    <?php foreach ($components as $cp): ?>
                                        <td>
                                            <?php
                                            $cmp = Modules::run('gradingsystem/new_gs/componentPerSubject', $sub->subject_id, $cp->id);
                                            echo ($cmp->weight == 0 ? "" : ($cmp->weight * 100) . ' %');
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td><button onclick="showEditAssessWeight('<?php echo $sub->subject_id; ?>', '<?php echo $this->session->userdata('school_year'); ?>', '<?php echo $sub->subject ?>', '<?php echo $sub->code ?>')" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></button></td>
                                </tr>

                                <?php
                            endif;
                        endforeach;
                        ?>
                    </table>
                </div>
                <br />

            </div>
        </div>

    </div>

</div>

<div class="col-lg-6 pull-right">
    <b>Learner's Observed Values</b><br />
    <input <?php echo $bhs0 ?> id="bhS0" name="behS" type="radio" onclick="saveGS('customized_beh_settings', this.value)"  value="0" /> Default &nbsp;
    <input <?php echo $bhs1 ?> id="bhS1" name="behS" type="radio"  onclick="saveGS('customized_beh_settings', this.value)" value="1" /> Customized<br /><br />

    <?php echo Modules::run('gradingsystem/behSettings') ?>
</div>
<?php $this->load->view('addAssessment'); ?>
<?php $this->load->view('addDO8_subjects'); ?>
<?php $this->load->view('behavior_settings_modal'); ?>
<input type="hidden" id="prevSelected" value="" />

<script type="text/javascript">

    $(function () {
        $('#addCriteriaBtn').clickover({
            placement: 'top',
            html: true
        });

        $('#inputSubjectID').select2();
    })

    function showEditAssessWeight(subject, school_year, sub_title, code)
    {
        $('#editDOSubjects').modal('show')
        $('#code').val(code)
        $('#subject_id').val(subject);
        $('#school_year').val(school_year)
        $('#sub_title').html(sub_title)
                ;
    }

    function editSubjectWeight()
    {
        var code = $('#code').val();
        var subject_id = $('#subject_id').val();
        var assessment = $('#editAssessment').val()
        var weight = $('#editWeight').val()
        var url = '<?php echo base_url() . 'gradingsystem/new_gs/editSubjectWeight' ?>';
        $.ajax({
            type: "POST",
            url: url,
            //dataType: 'json',
            data: 'subject_id=' + subject_id + '&assessment=' + assessment + '&weight=' + weight + '&code=' + code + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                alert(data)
                location.reload()
            }
        })
    }

    function addSubjectWeight()
    {
        var subject_id = $('#inputSubjectID').val()
        var assessment = $('#inputAssessment').val()
        var weight = $('#inputWeight').val()
        var proceed = 1;

        if (subject_id == 0) {
            errorMsg('Please Select Subject', 'red', 3000);
            proceed = 0;
        } else if (assessment == 0) {
            errorMsg('Please Select Assessment', 'red', 3000);
            proceed = 0;
        } else if (weight == '') {
            errorMsg('Please Enter weight in decimal', 'red', 3000);
            proceed = 0;
        }

        if (proceed == 1) {
            var url = '<?php echo base_url() . 'gradingsystem/new_gs/addSubjectWeight' ?>';
            $.ajax({
                type: "POST",
                url: url,
                dataType: 'json',
                data: 'subject_id=' + subject_id + '&assessment=' + assessment + '&weight=' + weight + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
                success: function (data)
                {
                    errorMsg(data.msg, 'red', 5000);
                    setTimeout(location.reload.bind(location), 6000);
                }
            });
        }
    }

    function errorMsg(msg, color, tDelay) {
        $('#promptMsg').show().delay(tDelay).queue(function (n) {
            $(this).hide();
            n();
        });
        $('#promptMsg').css('color', color);
        $('#promptMsg').text('Alert: ' + msg);
    }

    function selectGS(value)
    {
        switch (value) {
            case '1':
                $('#kpup_body').removeClass('hide');
                $('#DO_8').addClass('hide')
                break;
            case '2':

                $('#kpup_body').addClass('hide');
                $('#DO_8').removeClass('hide')
                break;
        }

        var url = "<?php echo base_url() . 'gradingsystem/saveGS/' ?>";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'gs_used=' + value + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {

            }
        });
    }

    function getAssessment(value)
    {

        var url = "<?php echo base_url() . 'gradingsystem/new_gs/selectAssessment/' ?>";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'value=' + value + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {

            }
        });
    }

    function saveTransmutation() {

        if ($('#bybase').is(':checked'))
        {
            var byBase = 1
            var base = $('#base').val()
            var formula = ""
        }
        if ($('#byform').is(':checked'))
        {
            byBase = 0
            base = ""
            formula = $('#formula').val()
        }

        var url = "<?php echo base_url() . 'gradingsystem/saveTransmutation/' ?>";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'byBase=' + byBase + '&base=' + base + '&formula=' + formula + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                alert(data.msg)
                location.reload()
            }
        });
    }

    function saveCriteria()
    {
        var subject_id = $('#getSubject').val()

    }

    function addCriteria() {

        var subject_id = $('#getSubject').val()
        var subject_name = $('#getSubject :selected').text()
        var k = $('#' + subject_id + '_kpupsContainer_1').val()

        if ($('#' + subject_id + '_kpupsContainer_1').length > 0) {
            var proc = $('#' + subject_id + '_kpupsContainer_2').val()
            var u = $('#' + subject_id + '_kpupsContainer_3').val()
            var prod = $('#' + subject_id + '_kpupsContainer_4').val()



            if (k == "") {
                k = 15
            }
            if (proc == "") {
                proc = 25
            }
            if (u == "") {
                u = 30
            }
            if (prod == "") {
                prod = 30
            }

            var kpups = parseInt(k) + parseInt(proc) + parseInt(u) + parseInt(prod);
            //alert(kpups)
            if (kpups >= 100) {
                alert('Sorry, You Can\'t add a Criteria')
                location.reload()
            } else {
                $('#addCriteriaBtn').clickover({
                    auto_close: 1000
                });
                var name = $('#addedCriteria').val();
                var i = $('#p_kpupsContainer_' + subject_id).find('input').length + 1;
                var kpupsContainer = $('#p_kpupsContainer_' + subject_id);
                $('<input type="text" id="' + subject_id + '_kpupsContainer_' + i + '" size="8" name="' + name + '" value="" placeholder="' + name.charAt(0) + ' %" /><i class="pointer fa fa-trash" title="kpupsContainer" onclick="removed(this,' + i + ')"></i>').appendTo(kpupsContainer);
                $('#addCriteriaBtn').attr('count', i);
                i++;
            }
        } else {
            $('<label>' + subject_name + ' :</label><br /><p id="p_kpupsContainer_' + subject_id + '">\n\
                \n\
                </p>').appendTo($('#kpupsContainer'))

            var kpupsContainer = $('#p_kpupsContainer_' + subject_id);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_1" size="8" name="Knowledge" value="15" placeholder="K %" />').appendTo(kpupsContainer);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_2" size="8" name="Process" value="25" placeholder="P %" />').appendTo(kpupsContainer);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_3" size="8" name="Understanding" value="30" placeholder="U %" />').appendTo(kpupsContainer);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_4" size="8" name="Product" value="30" placeholder="P %" />').appendTo(kpupsContainer);
            $('#addCriteriaBtn').attr('count', 4);
        }



    }

    function addSubjectComponent() {

        var subject_id = $('#getSubject').val()
        var subject_name = $('#getSubject :selected').text()
        var k = $('#' + subject_id + '_kpupsContainer_1').val()

        if ($('#' + subject_id + '_kpupsContainer_1').length > 0) {
            var proc = $('#' + subject_id + '_kpupsContainer_2').val()
            var u = $('#' + subject_id + '_kpupsContainer_3').val()
            var prod = $('#' + subject_id + '_kpupsContainer_4').val()



            if (k == "") {
                k = 15
            }
            if (proc == "") {
                proc = 25
            }
            if (u == "") {
                u = 30
            }
            if (prod == "") {
                prod = 30
            }

            var kpups = parseInt(k) + parseInt(proc) + parseInt(u) + parseInt(prod);
            //alert(kpups)
            if (kpups >= 100) {
                alert('Sorry, You Can\'t add a Criteria')
                location.reload()
            } else {
                $('#addCriteriaBtn').clickover({
                    auto_close: 1000
                });
                var name = $('#addedCriteria').val();
                var i = $('#p_kpupsContainer_' + subject_id).find('input').length + 1;
                var kpupsContainer = $('#p_kpupsContainer_' + subject_id);
                $('<input type="text" id="' + subject_id + '_kpupsContainer_' + i + '" size="8" name="' + name + '" value="" placeholder="' + name.charAt(0) + ' %" /><i class="pointer fa fa-trash" title="kpupsContainer" onclick="removed(this,' + i + ')"></i>').appendTo(kpupsContainer);
                $('#addCriteriaBtn').attr('count', i);
                i++;
            }
        } else {
            $('<label>' + subject_name + ' :</label><br /><p id="p_kpupsContainer_' + subject_id + '">\n\
                \n\
                </p>').appendTo($('#kpupsContainer'))

            var kpupsContainer = $('#p_kpupsContainer_' + subject_id);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_1" size="8" name="Knowledge" value="15" placeholder="K %" />').appendTo(kpupsContainer);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_2" size="8" name="Process" value="25" placeholder="P %" />').appendTo(kpupsContainer);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_3" size="8" name="Understanding" value="30" placeholder="U %" />').appendTo(kpupsContainer);
            $('<input style="margin-right:10px;" class="text-center" type="text" id="' + subject_id + '_kpupsContainer_4" size="8" name="Product" value="30" placeholder="P %" />').appendTo(kpupsContainer);
            $('#addCriteriaBtn').attr('count', 4);
        }



    }

    function removed(e, i, id)
    {
        var name = $('#kpupsContainer_' + i).attr('name')
        if (i > 0) {

            $(e).parents('p').remove();
            i--;
            $('#addCriteriaBtn').attr('count', i);
        }
        var url = "<?php echo base_url() . 'gradingsystem/deleteKPUPS/' ?>";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: 'custom=' + i + '&name=' + name + '&id=' + id + '&csrf_test_name=' + $.cookie('csrf_cookie_name'), // serializes the form's elements.
            success: function (data)
            {
                if (data.status)
                {
                    alert(data.msg)
                    location.reload()
                } else
                {
                    console.log(data.msg)
                }
            }
        });
    }


    function addComponent()
    {
        $.ajax({
            type: "POST",
            url: "<?php echo base_url() . 'gradingsystem/addComponent' ?>",
            data: "component=" + $('#component').val() + '&csrf_test_name=' + $.cookie('csrf_cookie_name'),
            dataType: 'json',
            success: function (data)
            {
                if (data.status) {
                    $('#inputAssessment').append('<option value="' + data.id + '"> ' + $('#component').val() + '</option>');
                    alert('Successfully Added')
                } else {
                    alert('Sorry Component already exist');
                }

                $('#addComponent').modal('hide');
            }

        });
    }

</script>