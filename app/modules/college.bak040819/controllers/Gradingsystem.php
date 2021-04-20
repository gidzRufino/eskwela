<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Gradingsystem extends MX_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('gradingsystem_model');
    }
    
    function post($name)
    {
        return $this->input->post($name);
    }
    
    public function inValidateGrade()
    {
        $st_id = $this->post('st_id');
        $term_id = $this->post('term_id');
        $subject_id = $this->post('subject_id');
        if($this->gradingsystem_model->inValidateGrade($st_id, $term_id, $subject_id)):
            echo 'Successfully Updated';
        endif;
    }
    
    public function validateGrade()
    {
        $st_id = $this->post('st_id');
        $term_id = $this->post('term_id');
        $subject_id = $this->post('subject_id');
        $course_id = $this->post('course_id');
        $year_level = $this->post('year_level');
        $faculty_id = $this->post('faculty_id');
        $semester = $this->post('semester');
        $grade = $this->post('grade');
        $school_year = $this->post('school_year');
        if($this->gradingsystem_model->validateGrade($st_id, $term_id, $subject_id)):
            if($term_id==4):
                $finalGradeDetails = array(
                   'gsa_st_id'          => $st_id,
                   'gsa_course_id'      => $course_id,
                   'gsa_year_level'     => $year_level,
                   'gsa_sem'            => $semester,
                   'gsa_sub_id'         => $subject_id,
                   'gsa_term_id'        => $term_id,
                   'gsa_grade'          => $grade,
                   'gsa_school_year'    => $school_year,
                   'gsa_is_final'       => 1,
                   'teacher_id'         => $faculty_id
                   
                );
            
                if($this->gradingsystem_model->saveFinalGrade($finalGradeDetails, $st_id, $term_id,$subject_id,$faculty_id, $school_year, $semester)):
                    echo 'Successfully Saved';
                endif;
            else:
                echo 'Successfully Saved';
            endif;
        endif;
    }
    
    
    public function getTransmutation($numberGrade)
    {
        $plg = $this->gradingsystem_model->getTransmutation();
        foreach($plg as $plg){
            if( $numberGrade >= $plg->gstr_from && $numberGrade <= $plg->gstr_to){
                return number_format($plg->gstr_transmutation,2);
            }
        }
    }
    
    function getRecordedGrade($st_id, $category=NULL, $term=NULL, $semester=NULL, $school_year=NULL, $subject_id=NULL, $final=NULL)
    {
        $grades = $this->gradingsystem_model->getRecordedGrade($st_id, $category, $term, $semester, $school_year, $subject_id, $final);
        return $grades;
    }
    
    function recordGrade()
    {
        $st_id = $this->post('student_id');
        $category = $this->post('category_id');
        $term = $this->post('term');
        $grade = $this->post('grade');
        $semester = Modules::run('main/getSemester');
        $subject_id = $this->post('subject');
        
        $grade_details = array(
            'student_id'    => $st_id,
            'subject_id'    => $subject_id,
            'assess_cat_id' => $category,
            'grade'         => $grade,
            'term_id'       => $term,
            'school_year'   => $this->session->userdata('school_year'),
            'semester'      => $semester
        );
        
        if($this->gradingsystem_model->recordGrade($grade_details, $st_id, $subject_id, $category, $term, $semester)):
            echo json_encode(array('msg'=> 'Successfully Saved'));
        endif;
    }
    
    function index()
    {
        //$data['course'] = $this->gradingsystem_model->getTeachersSchedule($this->session->userdata('employee_id'));
        $data['teacher_id'] = $this->session->userdata('employee_id');
        $data['subjects'] = $this->gradingsystem_model->searchSubjectAssign($this->session->userdata('employee_id'));
        $data['modules'] = 'college/gradingsystem'; 
        $data['main_content'] = 'default';
        echo Modules::run('templates/college_content', $data);
    }
    
    function getTerm()
    {
        $term = $this->gradingsystem_model->getTerm();
        return $term;
    }
    
    function getAssessCategory()
    {
        $category = $this->gradingsystem_model->getAssessCategory();
        return $category;
    }
    
    function getIndividualAcademicRecord($details)
    {
        $data['details'] = $details;
        $data['term'] = $this->getTerm();
        $data['category'] = $this->getAssessCategory();
        $this->load->view('gradingsystem/individualRecords', $data);
    }
    
    function getStudentsPerSubject($fac_id, $sched_code, $sec_id, $sub_id, $sem =NULL)
    {
        $data['term'] = $this->gradingsystem_model->getTerm();
        $data['assessCategory'] = $this->gradingsystem_model->getAssessCategory();
        $data['section_id'] = $sec_id;
        $data['faculty_id'] = $fac_id;
        $data['subject_id'] = $sub_id;
        $sem = Modules::run('main/getSemester');
        $data['sem'] = $sem;
        $data['school_year'] = $this->session->userdata('school_year');
        $data['students'] = Modules::run('college/subjectmanagement/getStudentsPerSectionRaw', $sec_id, $sem);
        $this->load->view('gradingsystem/studentsPerSubject', $data);
    }
    
    function getTeacherAssignment($teacher_id, $semester)
    {
        $result = $this->gradingsystem_model->getTeacherAssignment($teacher_id, $semester);
        echo '<ul>';
            foreach ($result as $r):
                ?>
                    <li onclick="loadAssignment('<?php echo $teacher_id; ?>','<?php echo $r->sched_gcode; ?>','<?php echo $r->section_id; ?>'), $('#searchSubject').val('<?php echo $r->sub_code.' ( '.$r->section.' )' ?>'), $('#searchName').hide()"><?php echo $r->sub_code.' ( '.$r->section.' )' ?></li>
                <?php
            endforeach;
         echo '</ul>';
        
    }
    
    function searchSubjectAssign($teacher_id, $value)
    {
         $result = $this->gradingsystem_model->searchSubjectAssign($teacher_id, $value);
         echo '<ul>';
            foreach ($result as $r):
                ?>
                    <li onclick="loadAssignment('<?php echo $teacher_id; ?>','<?php echo $r->sched_gcode; ?>','<?php echo $r->section_id; ?>'), $('#searchSubject').val('<?php echo $r->sub_code.' ( '.$r->section.' )' ?>'), $('#searchName').hide()"><?php echo $r->sub_code.' ( '.$r->section.' )' ?></li>
                <?php
            endforeach;
         echo '</ul>';
    }
}

