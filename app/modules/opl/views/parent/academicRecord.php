<?php
$children = explode(',', $child_links);

?>
<div class="col-lg-12">
    <div class="row">
        <?php
        foreach ($children as $c):
            $isEnrolled = Modules::run('registrar/isEnrolled', $c, $this->session->school_year);
            if (!$isEnrolled):
                $school_year = $this->session->userdata('school_year') - 1;
            else:
                $school_year = $this->session->userdata('school_year');
            endif;

            $childDepartment = Modules::run('registrar/getStudentDepartment', $c, $school_year);
            $student = Modules::run('registrar/getSingleStudent', $c, $school_year);
            if ($student):
                if ($student->grade_id >= 2 && $student->grade_id <= 11): //-----   Grade school and Junior High school ---- //
                    ?>
                    <div class="col-md-6">
                        <div class="card" style="padding: 5px">
                            <div class="card-header bg-gradient-lightblue">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="card-img-top img-circle img-responsive" style="width: 100px; border: 1px" src="<?php
                                        if ($students->avatar != ""):echo base_url() . 'uploads/' . $student->avatar;
                                        else:echo base_url() . 'uploads/noImage.png';
                                        endif;
                                        ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <span id="name" class="pull-right" style="color:#FFF;"><?php echo strtoupper($student->firstname . " " . $student->lastname) ?></span><br>
                                        <span><?php echo $student->level ?> - <?php echo $student->section ?></span><br>
                                        <span><?php echo $student->st_id ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-bordered">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="7" style="text-align: center">Class Record</th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2" style="text-align: center">Subject</th>
                                                <th colspan="4" style="text-align: center">Grading Period</th>
                                                <th rowspan="2" style="text-align: center">Final Grade</th>
                                                <th rowspan="2" style="text-align: center">Remarks</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align: center">1st</th>
                                                <th style="text-align: center">2nd</th>
                                                <th style="text-align: center">3rd</th>
                                                <th style="text-align: center">4th</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $subject_ids = Modules::run('academic/getSpecificSubjectPerlevel', $student->grade_id);
                                            $mp = 0;
                                            foreach ($subject_ids as $sp):
                                                $singleSub = Modules::run('academic/getSpecificSubjects', $sp->sub_id);
                                                $first = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 1, $school_year);
                                                $second = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 2, $school_year);
                                                $third = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 3, $school_year);
                                                $fourth = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 4, $school_year);

                                                if ($singleSub->parent_subject == 11):
                                                    $mgrd1 += $first->row()->final_rating;
                                                    $mgrd2 += $second->row()->final_rating;
                                                    $mgrd3 += $third->row()->final_rating;
                                                    $mgrd4 += $fourth->row()->final_rating;
                                                    $mp += 1;
                                                endif;
                                            endforeach;

                                            $fmgrd1 = round($mgrd1 / $mp);
                                            $fmgrd2 = round($mgrd2 / $mp);
                                            $fmgrd3 = round($mgrd3 / $mp);
                                            $fmgrd4 = round($mgrd4 / $mp);
                                            $fmGrade = round(($fmgrd1 + $fmgrd2 + $fmgrd3 + $fmgrd4) / 4);

                                            foreach ($subject_ids as $s):
                                                $singleSub = Modules::run('academic/getSpecificSubjects', $s->sub_id);
                                                $first = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 1, $school_year);
                                                $second = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 2, $school_year);
                                                $third = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 3, $school_year);
                                                $fourth = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 4, $school_year);

                                                $fGrade = round(($first->row()->final_rating + $second->row()->final_rating + $third->row()->final_rating + $fourth->row()->final_rating) / 4);

                                                if ($singleSub->parent_subject != 11):
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $singleSub->short_code ?>
                                                        </td>
                                                        <td><?php echo $first->row()->final_rating; ?></td>
                                                        <td><?php echo $second->row()->final_rating; ?></td>
                                                        <td><?php echo $third->row()->final_rating; ?></td>
                                                        <td><?php echo $fourth->row()->final_rating; ?></td>
                                                        <td><?php echo ($fourth->row()->final_rating == '' ? '' : $fGrade) ?></td>
                                                        <td><?php echo ($fourth->row()->final_rating == '' ? '' : ($fGrade < 75 ? 'FAILED' : 'PASSED')); ?></td>
                                                    </tr>
                                                    <?php
                                                else:
                                                    if ($singleSub->subject_id == 13):
                                                        ?>
                                                        <tr>
                                                            <td>MAPEH</td>
                                                            <td><?php echo $fmgrd1; ?></td>
                                                            <td><?php echo $fmgrd2; ?></td>
                                                            <td><?php echo $fmgrd3; ?></td>
                                                            <td><?php echo $fmgrd4; ?></td>
                                                            <td><?php echo ($fmgrd4 == '' ? '' : $fmGrade) ?></td>
                                                            <td><?php echo ($fmgrd4 == '' ? '' : ($fmGrade < 75 ? 'FAILED' : 'PASSED')); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $singleSub->short_code ?></td>
                                                            <td><?php echo $first->row()->final_rating; ?></td>
                                                            <td><?php echo $second->row()->final_rating; ?></td>
                                                            <td><?php echo $third->row()->final_rating; ?></td>
                                                            <td><?php echo $fourth->row()->final_rating; ?></td>
                                                            <td><?php echo ($fourth->row()->final_rating == '' ? '' : $fGrade) ?></td>
                                                            <td><?php echo ($fourth->row()->final_rating == '' ? '' : ($fGrade < 75 ? 'FAILED' : 'PASSED')); ?></td>
                                                        </tr>
                                                        <?php
                                                    else:
                                                        ?>
                                                        <tr>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $singleSub->short_code ?></td>
                                                            <td><?php echo $first->row()->final_rating; ?></td>
                                                            <td><?php echo $second->row()->final_rating; ?></td>
                                                            <td><?php echo $third->row()->final_rating; ?></td>
                                                            <td><?php echo $fourth->row()->final_rating; ?></td>
                                                            <td><?php echo ($fourth->row()->final_rating == '' ? '' : $fGrade) ?></td>
                                                            <td><?php echo ($fourth->row()->final_rating == '' ? '' : ($fGrade < 75 ? 'FAILED' : 'PASSED')); ?></td>
                                                        </tr>
                                                    <?php
                                                    endif;

                                                endif;
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $mgrd1 = 0;
                    $mgrd2 = 0;
                    $mgrd3 = 0;
                    $mgrd4 = 0;
                else: // Senior High School Class Card
                    ?>
                    <div class="col-md-10">
                        <div class="card" style="padding: 5px">
                            <div class="card-header bg-gradient-lightblue">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img class="card-img-top img-circle img-responsive" style="width: 100px; border: 1px" src="<?php
                                        if ($students->avatar != ""):echo base_url() . 'uploads/' . $student->avatar;
                                        else:echo base_url() . 'uploads/noImage.png';
                                        endif;
                                        ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <span id="name" class="pull-right" style="color:#FFF;"><?php echo strtoupper($student->firstname . " " . $student->lastname) ?></span><br>
                                        <span><?php echo $student->level ?> - <?php echo $student->section ?></span><br>
                                        <span><?php echo $student->st_id ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-bordered">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Subject</th>
                                                <th colspan="2">First Semester</th>
                                                <th rowspan="2" style="width: 50px">Semester Final Grade</th>
                                                <th rowspan="2" style="width: 200px">Remarks</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align: center">1st</th>
                                                <th style="text-align: center">2nd</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $coreSubs1 = Modules::run('subjectmanagement/getSHSubjects', $student->grade_id, 1, $student->strnd_id, 1);
                                            $appliedSubs1 = Modules::run('subjectmanagement/getSHSubjects', $student->grade_id, 1, $student->strnd_id);
                                            ?><tr><td colspan="5" style="text-align: left">Core Subjects</td></tr><?php
                                            $subj1 = 0;
                                            $coreTotal1 = 0;
                                            $appliedTotal1 = 0;
                                            foreach ($coreSubs1 as $c1):
                                                $subj1++;
                                                $singleSub = Modules::run('academic/getSpecificSubjects', $c1->sh_sub_id);
                                                $firstGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 1, $school_year);
                                                $secondGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 2, $school_year);
                                                ?>
                                                <tr>
                                                    <td style="text-align: left"><?php echo $c1->short_code ?></td>
                                                    <td><?php echo ($firstGrade->row()->final_rating == '' ? '' : $firstGrade->row()->final_rating) ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : $secondGrade->row()->final_rating) ?></td>
                                                    <?php
                                                    $semFinalGrade = ($secondGrade->row()->final_rating == '' ? '' : round(($firstGrade->row()->final_rating + $secondGrade->row()->final_rating) / 2));
                                                    $coreTotal1 += $semFinalGrade;
                                                    ?>

                                                    <td><?php echo $semFinalGrade ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : ($semFinalGrade < 75 ? 'Failed' : 'Passed')) ?></td>
                                                </tr>
                                                <?php
                                            endforeach;
                                            ?><tr><td colspan="5" style="text-align: left">Applied and Specialized Subjects</td></tr><?php
                                            foreach ($appliedSubs1 as $app1):
                                                $subj1++;
                                                $singleSub = Modules::run('academic/getSpecificSubjects', $app1->sh_sub_id);
                                                $firstGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 1, $school_year);
                                                $secondGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 2, $school_year);
                                                ?>
                                                <tr>
                                                    <td style="text-align: left"><?php echo $app1->short_code ?></td>
                                                    <td><?php echo ($firstGrade->row()->final_rating == '' ? '' : $firstGrade->row()->final_rating) ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : $secondGrade->row()->final_rating) ?></td>
                                                    <?php
                                                    $semFinalGrade = ($secondGrade->row()->final_rating == '' ? '' : round(($firstGrade->row()->final_rating + $secondGrade->row()->final_rating) / 2));
                                                    $appliedTotal1 += $semFinalGrade;
                                                    ?>
                                                    <td><?php echo $semFinalGrade ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : ($semFinalGrade < 75 ? 'Failed' : 'Passed')) ?></td>
                                                </tr>
                                                <?php
                                            endforeach;
                                            $genAve = round(($coreTotal1 + $appliedTotal1) / $subj1);
                                            ?>
                                            <tr>
                                                <td colspan="4" style="text-align: right">General Average for the Semester</td>
                                                <td><?php echo ($genAve != 0 ? $genAve : '') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-bordered">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Subject</th>
                                                <th colspan="2">Second Semester</th>
                                                <th rowspan="2" style="width: 50px">Semester Final Grade</th>
                                                <th rowspan="2" style="width: 200px">Remarks</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align: center">1st</th>
                                                <th style="text-align: center">2nd</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $coreSubs2 = Modules::run('subjectmanagement/getSHSubjects', $student->grade_id, 2, $student->strnd_id, 1);
                                            $appliedSubs2 = Modules::run('subjectmanagement/getSHSubjects', $student->grade_id, 2, $student->strnd_id);
                                            ?><tr><td colspan="5" style="text-align: left">Core Subjects</td></tr><?php
                                            $subj2 = 0;
                                            $coreTotal2 = 0;
                                            $appliedTotal2 = 0;
                                            foreach ($coreSubs2 as $c2):
                                                $subj2++;
                                                $singleSub = Modules::run('academic/getSpecificSubjects', $c2->sh_sub_id);
                                                $firstGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 1, $school_year);
                                                $secondGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 2, $school_year);
                                                ?>
                                                <tr>
                                                    <td style="text-align: left"><?php echo $c2->short_code ?></td>
                                                    <td><?php echo ($firstGrade->row()->final_rating == '' ? '' : $firstGrade->row()->final_rating) ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : $secondGrade->row()->final_rating) ?></td>
                                                    <?php
                                                    $semFinalGrade = ($secondGrade->row()->final_rating == '' ? '' : round(($firstGrade->row()->final_rating + $secondGrade->row()->final_rating) / 2));
                                                    $coreTotal2 += $semFinalGrade;
                                                    ?>

                                                    <td><?php echo $semFinalGrade ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : ($semFinalGrade < 75 ? 'Failed' : 'Passed')) ?></td>
                                                </tr>
                                                <?php
                                            endforeach;
                                            ?><tr><td colspan="5" style="text-align: left">Applied and Specialized Subjects</td></tr><?php
                                            foreach ($appliedSubs2 as $app2):
                                                $subj2++;
                                                $singleSub = Modules::run('academic/getSpecificSubjects', $app2->sh_sub_id);
                                                $firstGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 1, $school_year);
                                                $secondGrade = Modules::run('gradingsystem/getFinalGrade', $student->st_id, $singleSub->subject_id, 2, $school_year);
                                                ?>
                                                <tr>
                                                    <td style="text-align: left"><?php echo $app2->short_code ?></td>
                                                    <td><?php echo ($firstGrade->row()->final_rating == '' ? '' : $firstGrade->row()->final_rating) ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : $secondGrade->row()->final_rating) ?></td>
                                                    <?php
                                                    $semFinalGrade = ($secondGrade->row()->final_rating == '' ? '' : round(($firstGrade->row()->final_rating + $secondGrade->row()->final_rating) / 2));
                                                    $appliedTotal2 += $semFinalGrade;
                                                    ?>
                                                    <td><?php echo round(($firstGrade->row()->final_rating + $secondGrade->row()->final_rating) / 2) ?></td>
                                                    <td><?php echo ($secondGrade->row()->final_rating == '' ? '' : ($semFinalGrade < 75 ? 'Failed' : 'Passed')) ?></td>
                                                </tr>
                                                <?php
                                            endforeach;
                                            $genAve = round(($coreTotal2 + $appliedTotal2) / $subj2);
                                            ?>
                                            <tr>
                                                <td colspan="4" style="text-align: right">General Average for the Semester</td>
                                                <td><?php echo ($genAve != 0 ? $genAve : '') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endif;
            endif;
        endforeach;
        ?>
    </div>
</div>

<style type="text/css">
    th, td{
        text-align: center;
    }
</style>