<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of main
 *
 * @author genesis
 */
class subjectmanagement extends MX_Controller {
    //put your code here
    
    private $get;
    //private $set;
    function __construct() {
        parent::__construct();
	$this->load->library('pagination');
	$this->load->library('pdf');
        $this->get = $this->load->model('subjectmanagement_model');
        $this->load->library('Mobile_Detect');
        //$device = new Mobile_Detect();
    }
     
    private function getInput($name)
    {
        return $this->input->post($name);
    }
    
    public function getStudyLoad($admission_id, $sem)
    {
        $data['subjects'] = $this->getLoadedSubject($admission_id, $sem);
        $this->load->view('subjectmanagement/viewStudyLoad', $data);
    }
    
    public function printStudentsPerSubject($subject_id, $section_id, $sem, $school_year)
    {
        $data['students'] = $this->getStudentsPerSectionPrint($section_id, $sem);
//       / print_r($data);
        $this->load->view('enrollmentListPerSubject', $data);
    }
    
    public function getSubjectPerId($sub_id)
    {
        $subjects = $this->subjectmanagement_model->getSubjectPerId($sub_id);
        return $subjects;
    }

    public function getSubjectOfferedPerSem($sem)
    {
        $subjects = $this->subjectmanagement_model->getSubjectOfferedPerSem($sem);
        foreach($subjects as $s):
            ?>
            <option value="<?php echo $s->s_id ?>"><?php echo $s->sub_code.' [ '.$s->s_desc_title.' ]' ?></option>
            <?php
        endforeach;
    }
    
    public function getStudentsPerSectionRaw($sec_id, $sem=NULL)
    {
        $students = $this->subjectmanagement_model->getStudentsPerSectionRaw($sec_id, $sem);
        return $students;
        
    }
    
    public function getStudentsPerSectionPrint($sec_id, $sem=NULL)
    {
        $students = $this->subjectmanagement_model->getStudentsPerSectionPrint($sec_id, $sem);
        
       // echo count($students);
        return $students;
        
    }

        public function getStudentsPerSection($sec_id, $sem=NULL)
    {
        $students = $this->subjectmanagement_model->getStudentsPerSection($sec_id, $sem);
        return $students;
    }
    
    public function editSection()
    {
        $sec_id = $this->getInput('sec_id');
        $section = $this->getInput('value');
        $details = array('section' => $section);
        if($this->subjectmanagement_model->editSection($sec_id, $details)):
            return TRUE;
        endif;
    }
    
    public function deleteSection($sec_id)
    {
        $deleted = $this->subjectmanagement_model->deleteSection($sec_id);
        if($deleted):
            $json = array('msg' => 'Successfully Deleted');
        else:
            $json = array('msg' => 'Sorry Something went wrong in Deleting this section');
            
        endif;
        
        echo json_encode($json);
    }
    
    public function getSectionPerSubjectDrop($sub_id)
    {
        $section = $this->subjectmanagement_model->getSectionPerSubject($sub_id);
        foreach ($section as $sec):
            if(!empty($section)):
            ?>
            <option value="<?php echo $sec->sec_id ?>"><?php echo $sec->section ?></option>
            <?php
            else:
                echo '<option>Sorry No Section is Open for this subject </option>';
            endif;
        endforeach;
    }
    
    public function getSectionPerSubject($sub_id)
    {
        $data['section'] = $this->subjectmanagement_model->getSectionPerSubject($sub_id);
        $this->load->view('college/subjectmanagement/sectionPerSubject', $data);
    }
            
    function assignSubject()
    {
        $teacher_id = $this->getInput('t_id');
        $sched_code = $this->getInput('sched_code');
        $section_id = $this->getInput('section_id');
        $spc_id     = $this->getInput('spc_id');
        
        if($sched_code==""):
            $array = array(
               'spc_id'     => $spc_id,
               'section_id' => $section_id,
               'faculty_id' => $teacher_id
            );
            $result = $this->subjectmanagement_model->saveAssignedSubject($array, $spc_id, $section_id, $teacher_id);
        else:    
            $this->subjectmanagement_model->assignSubject($teacher_id, $sched_code);
            $array = array(
               'spc_id'     => $spc_id,
               'section_id' => $section_id,
               'faculty_id' => $teacher_id,
               'schedule_id'=> $sched_code
            );
            $result = $this->subjectmanagement_model->saveAssignedSubject($array, $spc_id, $section_id, $teacher_id);
        endif;
        
        if($result):
            echo json_encode(array('status' => TRUE));
        else:
            echo json_encode(array('status' => FALSE));
        endif;
    }
    
    function getAssignedSubjectRaw($id)
    {
        $subjects = $this->subjectmanagement_model->getAssignSubject($id);
        return $subjects;
    }
    
    function getAssignedSubjectCollege()
    {
        $id = $this->getInput('id');
        $data['id'] = $id;
        $data['subjects'] = $this->subjectmanagement_model->getAssignSubject($id);
        echo Modules::run('college/schedule/getSchedulePerTeacher', $id);
    }
    
    function searchSubject($value=NULL)
    {
        $data['subjects'] = $this->subjectmanagement_model->searchSubject(urldecode($value));
        $this->load->view('subjectmanagement/searchSubjectTable', $data);
        
    }
    
    function loadSubject($value=NULL, $course_id=NULL, $sem = NULL)
    {
        $data['sem'] = $sem;
        $data['subjects'] = $this->subjectmanagement_model->searchSubject(urldecode($value), $course_id);
        $this->load->view('subjectmanagement/loadSubjectTable', $data);
        
    }
    
    function getSectionById($sec_id)
    {
        $section = $this->subjectmanagement_model->getSectionById($sec_id);
        return $section;
    }
    
    function getSectionDropDown($sub_id)
    {
        $section = $this->subjectmanagement_model->getSubjectsOffered($sub_id);
        foreach($section->result() as $s):
            ?>
                <option value="<?php echo $s->sec_id  ?>"><?php echo $s->section ?></option>
            <?php
        endforeach;
    }
            
    function getSubjectsOffered($sub_id)
    {
        $subjects = $this->subjectmanagement_model->getSubjectsOffered($sub_id);
        return $subjects;
    }
    
    function addSection()
    {
        $lastSecId = $this->subjectmanagement_model->lastSecId();
        $sy = $this->session->userdata('school_year');
        $course_id = $this->getInput('course_id');
        $subject_id = $this->getInput('sub_id');
        $semester = $this->getInput('semester');
        $sCode = $this->getInput('subCode');
        $sub_code = substr($sCode, 0, 3);
        $secCode = ($lastSecId->sec_id + 1);
        
        
        $details = array(
            'section'           => $sub_code.$secCode.'0'.$course_id,
            'course_id'         => $course_id,
            'sec_sem'           => $semester,
            'sec_school_year'   => $sy,
            'sec_sub_id'        => $subject_id
        );
        
        $success = $this->subjectmanagement_model->addSection($details);
        if($success):
            echo json_encode(array('msg' => 'Successfully Added'));
        else:
            echo json_encode(array('section' => 'Sorry Section Can\'t be added.'));
        endif;
    }
    
    function academicInformation($details)
    {
        $data['details'] = $details;
        $data['term'] = Modules::run('college/gradingsystem/getTerm');
        $data['category'] = Modules::run('college/gradingsystem/getAssessCategory');
        $this->load->view('subjectmanagement/academicInformation', $data);
        
    }
    
    function ifSubjectExist($student_id, $subject_id)
    {
        $exist = $this->subjectmanagement_model->checkIfSubjectExist($student_id, $subject_id);
        if($exist):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function printStudyLoad($st_id, $sem, $option=NULL)
    {
        $data['st_id'] = base64_decode($st_id);
        $data['sem']   = $sem;
        if($option==NULL):
            Modules::run('college/finance/setFinanceAccount', $data['st_id'],$sem);
        endif;
        $this->load->view('subjectmanagement/reports/printStudyLoad', $data);
    }
    
    function approveLoad($adm_id)
    {
        $approved = $this->subjectmanagement_model->approveLoad($adm_id);
        if($approved):
            echo json_encode(array('msg' => 'Study Load Approved Successfully'));
        else:
            echo json_encode(array('msg' => 'Sorry, Something is wrong'));
        endif;
    }
            
    function getLoadedSubject($adm_id, $sem)
    {
        $subjects = $this->subjectmanagement_model->getLoadedSubject($adm_id, $sem);
        return $subjects;
    }
    
    function getLoadedSubjectTemplate($admission_id)
    {
        $data['admission_id'] = $admission_id;
        $this->load->view('subjectmanagement/studyLoad', $data);
    }
    
    function removeLoadedSubject()
    {
        $subject_id = $this->getInput('subject_id');
        $adm_id     = $this->getInput('admission_id');
        
        $this->subjectmanagement_model->removeLoadedSubject($subject_id, $adm_id);
        return;
    }
    
    function saveLoadedSubject()
    {
        $subject_id = $this->getInput('subject_id');
        $st_id      = $this->getInput('student_id');
        $adm_id     = $this->getInput('admission_id');
        $section_id     = $this->getInput('section_id');
        $overide = $this->getInput('overide');
        $remarks = "";
        $overide_by = ($overide==0?"":$this->session->userdata('employee_id'));
        
        $load = array(
            'cl_adm_id'     => $adm_id,
            'cl_user_id'    => $st_id,
            'cl_sub_id'     => $subject_id,
            'cl_section'    => $section_id,
            'cl_overide'    => $overide,
            'cl_remarks'    => $remarks,
            'overide_by'    => $overide_by  
        );
        
        $this->subjectmanagement_model->saveLoadedSubject($subject_id, $adm_id, $load);
        echo Modules::run('college/subjectmanagement/getLoadedSubjectTemplate', $adm_id);
    }
    
    function prerequisteCheck()
    {
        $student_id = $this->getInput('student_id');
        $pre_req = $this->getInput('pre_req');
        
        if($pre_req!='None'):
            
            $pre_req_items = explode(',', $pre_req);
        
            foreach($pre_req_items as $prq):
                $isTaken = $this->subjectmanagement_model->prerequisteCheck($student_id, $prq );
                if($isTaken):
                    continue;
                else:
                    echo json_encode(array('status' => FALSE));
                    exit();
                endif;
            endforeach;
            echo json_encode(array('status' => TRUE));
        else:
            echo json_encode(array('status' => TRUE));
        endif;
    }
            
    function addSubjectLoad($student_info, $course_id, $year_level, $sem_id=NULL, $school_year=NULL)
    {
        $data['student'] = $student_info;
        $data['course_id'] = $course_id;
        $data['sem'] = $sem_id;
        $data['subjects'] = $this->subjectmanagement_model->getSubjectPerCourse($course_id, $year_level, $sem_id, $school_year);
        $this->load->view('subjectmanagement/subjectLoad', $data);
        
    }
            
    function editSubject()
    {
        $sub_id = $this->getInput('sub_id');
        $subject = $this->getInput('subject');
        
        $data = array(
          'subject' => $subject  
        );
        
        $result = $this->subjectmanagement_model->editsubject($sub_id, $data);
        if($result):
            return TRUE;
        endif;
    }
    
    function listOfSubjects()
    {
        $result = $this->subjectmanagement_model->collegeSubjects();
        $config['base_url'] = base_url('college/subjectmanagement/listOfSubjects/');
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 100;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $page = $this->subjectmanagement_model->collegeSubjects($config['per_page'], $this->uri->segment(4));
        $data['collegeSubjects'] = $page;
        
        $data['courses'] = Modules::run('coursemanagement/getCourses');
        $data['main_content'] = 'subjectmanagement/listOfSubjects';
        $data['modules'] = 'college';
        echo Modules::run('templates/college_content', $data);
    }
    
    
    function getAllSubjects()
    {
        $subject = $this->subjectmanagement_model->getSubjects();
        return $subject;
    }
    
    function getCollegeSubjects($sem = NULL)
    {
        $subject = $this->subjectmanagement_model->getCollegeSubjects($sem);
        return $subject;
    }
    
    function editCollegeSubject()
    {
        $sCode = $this->getInput('editSubjectCode');
        $sDesc = $this->getInput('editDesc');
        $lecU = $this->getInput('editLectureUnits');
        $labU = $this->getInput('editLabUnits');
        $inputPreR = $this->getInput('editPreR');
        $s_id = $this->getInput('editSubId');
        
        
        $subjects = array(
            's_desc_title'  => $sDesc,
            'sub_code'      => $sCode,
            's_lect_unit'   => $lecU,
            's_lab_unit'    => $labU,
            'pre_req'       => ($inputPreR!=""?$inputPreR:"None")
        );
        
        //print_r($subjects);
        
        if($this->subjectmanagement_model->editCollegeSubject($subjects, $sDesc, $sCode, $s_id)):
            echo 'Succesfully Updated';
        else:
            echo 'Sorry Subject Already Exist';
        endif;
        
        
    }
    
    function addCollegeSubject()
    {
        $sCode = $this->getInput('inputSubjectCode');
        $sDesc = $this->getInput('inputDesc');
        $lecU = $this->getInput('inputLectureUnits');
        $labU = $this->getInput('inputLabUnits');
        $inputPreR = $this->getInput('inputPreR');
        
        
        $subjects = array(
            's_desc_title'  => $sDesc,
            'sub_code'      => $sCode,
            's_lect_unit'   => $lecU,
            's_lab_unit'    => $labU,
            'pre_req'       => ($inputPreR!=""?$inputPreR:"None")
        );
        
        //print_r($subjects);
        
        if($this->subjectmanagement_model->addCollegeSubject($subjects, $sDesc, $sCode)):
            echo 'Succesfully Added';
        else:
            echo 'Sorry Subject Already Exist';
        endif;
        
        
    }
            
    function getSubjectsPerCourse($course_id=NULL)
    {
        $data['collegeSubjects'] = $this->subjectmanagement_model->getSubjectsPerCourse($course_id);
        $this->load->view('subjectmanagement/subjectsPerCourse', $data);
    }
    
    function index()
    {
        $result = $this->subjectmanagement_model->collegeSubjects();
        $config['base_url'] = base_url('college/subjectmanagement/index/');
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 15;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();
        $page = $this->subjectmanagement_model->collegeSubjects($config['per_page'], $this->uri->segment(4));
        $data['collegeSubjects'] = $page;
        
        $data['allSubjects'] = $result;
        $data['school_settings'] = Modules::run('main/getSet');
        $data['subject'] = Modules::run('academic/getSubjects');
        $data['courses'] = Modules::run('coursemanagement/getCourses');
        $data['main_content'] = 'subjectmanagement/default';
        $data['modules'] = 'college';
        echo Modules::run('templates/college_content', $data);
    }
    
    function searchSubjectResult($value)
    {
        $data['collegeSubjects'] = $this->subjectmanagement_model->searchSubjectResult($value);
        $this->load->view('subjectmanagement/searchResults', $data);
    }
    
    function getCourses()
    {
        $course = $this->subjectmanagement_model->getCourse();
        return $course;
    }
    
    function addCourse($course, $short_code)
    {
        $details = array(
            'course'        => urldecode($course),
            'short_code'    => urldecode($short_code)
        );  
        
        if($this->subjectmanagement_model->addCourse($details, $course)):
            echo json_encode(array('status' => TRUE, 'msg' => 'Successfully Added'));
        else:
            echo json_encode(array('status' => FALSE, 'msg' => 'Sorry Course Already Exist'));
        endif;
        
    }
    
    
    
}

