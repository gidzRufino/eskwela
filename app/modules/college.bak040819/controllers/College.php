<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Registrar
 *
 * @author genesis
 */
class College extends MX_Controller {
    //put your codeupdateEnrollmentStatus here
    
    
    function __construct() {
        parent::__construct();
        $this->load->model('college_model');
    	$this->load->model('get_college_model');
	$this->load->library('pagination');
        $this->load->library('Pdf');
        $this->load->library('router');
        
    }
    
    
    public function getTeams($school_year, $sem, $team_id)
    {
        $data['students'] = $this->get_college_model->getTeams($school_year, $sem, $team_id);
        $this->load->view('teamMembers', $data);
    }
    
    public function printStudentsPerCourse($course_id, $year_level, $sem, $school_year)
    {
        $data['students'] = $this->getStudentPerCourse($course_id, $sem, $school_year, NULL, NULL, $year_level,1);
        $this->load->view('studentListPerCourse', $data);
    }
    
    public function getStudentPerCourse($course_id, $sem, $year, $limit=NULL, $offset=NULL, $level=NULL, $status=NULL)
    {
        
        $students = $this->get_college_model->getAllCollegeStudents($limit, $offset,  $year, $sem, $course_id, $level, $status);
        return $students;
    }
    
    public function index()
    {
        
        $menu = $this->get_college_model->getMenuAccess($this->session->userdata('user_id'));
        $data['pmenu'] = $menu;
        $data['menus'] = ($menu!=""?explode(',', $menu->cma_personal_access):"");
        $data['modules'] = 'college';
        switch ($this->session->userdata('position')):
            default:
                $data['main_content'] = 'default';
            break;
            case 'Dean':
                if(count(explode(',', $menu->cma_personal_access))==0):
                    $data['menus'] = explode(',', '9');
                endif;
                $data['main_content'] = 'default';
            break;    
            case 'Faculty':
                if(count(explode(',', $menu->cma_personal_access))==0):
                    $data['menus'] = explode(',', '9');
                endif;
                $data['totalStudents'] = $this->getStudentsPerTeacher($this->session->userdata('employee_id'));
                $data['main_content'] = 'faculty/teachers_dashboard';
            break;    
            case 'Part-Time Faculty':
                if(count(explode(',', $menu->cma_personal_access))==0):
                    $data['menus'] = explode(',', '9');
                endif;
                $data['totalStudents'] = $this->getStudentsPerTeacher($this->session->userdata('employee_id'));
                $data['main_content'] = 'faculty/teachers_dashboard';
            break;    
        endswitch;
        echo Modules::run('templates/college_content', $data);
    }
    
    function enrollmentReport($school_year=NULL, $sem=NULL)
    {
        if($sem==NULL):
            $sem = Modules::run('main/getSemester');
        endif;
        $data['school_year'] = ($school_year==NULL?$this->session->userdata('school_year'):$school_year);
 
        $data['ro_year'] = $this->getROYear();
        $data['sem'] = $sem;
        $data['modules'] = 'college';
        $data['main_content'] = 'enrollmentReport';
        
        echo Modules::run('templates/college_content', $data);
    }
    
    function saveAdmissionRemarks()
    {
        $user_id = $this->input->post('user_id');
        $sem = $this->input->post('semester');
        $code = $this->input->post('code');
        $array = array(
           'status' => $code 
        );
        
        $status = $this->college_model->saveAdmissionRemarks($array, $user_id, $sem);
        if($status):
            echo 'Status Successfully Updated';
        else:
            echo 'Sorry Something went wrong';
        endif;
    }
            
    function getCoursePerNumYear($num)
    {
        $courses = $this->college_model->getCoursePerNumYear($num);
        return $courses;
    }
    
        
    function checkEnrollees()
    {
//        $q = $this->db->query('Select esk_profile.user_id as uid, lastname, esk_profile_students.st_id from esk_profile 
//                                left join esk_profile_students_admission on esk_profile.user_id = esk_profile_students_admission.user_id
//                                left join esk_profile_students on esk_profile.user_id = esk_profile_students.user_id
//                                where account_type = 5 and not exists(Select * from esk_profile_students_admission where esk_profile.user_id = esk_profile_students_admission.user_id order by lastname)
//                            ');
        $q = $this->db->query('Select esk_profile_students.st_id, esk_profile.user_id, lastname, firstname from esk_profile_students 
                                left join esk_profile on esk_profile_students.user_id = esk_profile.user_id
                                left join esk_profile_students_c_admission on esk_profile_students.user_id = esk_profile_students_c_admission.user_id
                                where account_type = 5 and not exists(Select * from esk_profile_students_c_admission where esk_profile_students.user_id = esk_profile_students_c_admission.user_id order by lastname)
                            ');
       // print_r($q->result());
        echo '<br />';
        echo '<br />';
        $i=1;
        foreach ($q->result() as $qr):
            $i++;
            echo $qr->user_id.' - '.$qr->lastname.' - '.$qr->firstname.' - '.$qr->st_id.'<br />';
//            $this->db->where('user_id', $qr->user_id);
//            if($this->db->delete('profile')):
//                echo $qr->user_id.' successfully deleted <br />';
//            endif;
//            $this->db->where('user_id', $qr->user_id);
//            if($this->db->delete('profile_students')):
//                echo $qr->user_id.' successfully deleted <br />';
//            endif;
        endforeach;
//        echo 'Deleted '. $i.' accounts';
    }
    
    
    function cleanEnrollees()
    {
        $q = $this->db->query('Select esk_profile_students.st_id, esk_profile.user_id as uid, lastname from esk_profile 
                                left join esk_profile_students on esk_profile.user_id = esk_profile_students.user_id
                                where account_type = 5 and not exists(Select * from esk_profile_students where esk_profile.user_id = esk_profile_students.user_id order by lastname)
                            ');
        foreach ($q->result() as $qr):
            echo $qr->uid.' - '.$qr->lastname.' - '.$qr->firstname.' - '.$qr->st_id.'<br />';
//            $this->db->where('user_id', $qr->uid);
//            if($this->db->delete('profile')):
//                echo $qr->uid.' successfully deleted <br />';
//            endif;
        endforeach;
    }
    
    function scanStudent($rfid)
    {
        $students = $this->getSingleStudentByRfid($rfid);
        echo json_encode(array('st_id' => base64_encode($students->row()->st_id)));
    }
    
    
    function getStudentsPerTeacher($teacher_id, $school_year=NULL, $semester=NULL, $status=NULL)
    {
        $sem = Modules::run('main/getSemester');
        $courses = Modules::run('college/schedule/getCourseAssignPerTeacher', $teacher_id);
        $students = 0;
        foreach ($courses as $c):
            $students += $this->getTotalCollegeStudents(($school_year==NULL?$this->session->userdata('school_year'):$school_year), ($semester==NULL?$sem:$semester), ($status==NULL?1:$status), $c->spc_course_id, $c->year_level);
        endforeach;
        return $students;
    }
    
    function updateEnrollmentStatus($st_id, $sy, $sem, $status)
    {
        $stat = $this->get_college_model->updateEnrollmentStatus($st_id, $sy, $sem,array('status' => $status));
        return $stat;
    }
    
    function getAdmissionDetails($st_id, $sem, $sy)
    {
        $admission = $this->get_college_model->getAdmissionDetails($st_id, $sem, $sy);
        return $admission;
    }
    
    
    function facultyAssignment($school_year = NULL)
    {
           $user_id = $this->session->userdata('user_id');
           $data['getEmployee'] = Modules::run('hr/getEmployee', base64_encode($user_id)); 
           $data['employeeList'] = Modules::run('hr/getEmployees'); 
           $data['subjects'] = Modules::run('subjectmanagement/getCollegeSubjects');
           $data['grade'] = Modules::run('registrar/getGradeLevel');
           $data['section'] = Modules::run('registrar/getAllSection');
           //$data['getAssignment'] = $this->mySubject($user_id);
           //$data['getAllAssignment'] = $this->getAssignment(NULL, $school_year);
           
        if(Modules::run('main/isMobile'))
        {
           if(!$this->session->userdata('is_logged_in')){
               echo Modules::run('mobile/index');
           }else{
                $data['modules'] = "academic";
                $data['main_content'] = 'mobile/mySubjects';
                echo Modules::run('mobile/main_content', $data);
           }
            
        }else
        {
            if(!$this->session->userdata('is_logged_in')){
                Modules::run('login');
            }else{
               $data['main_content'] = 'faculty/subject_assignment';
               $data['modules'] = 'college';
               echo Modules::run('templates/college_content', $data);	
            }
        }
        
    }
    
    
    function getAssignedSubjectCollege()
    {
        $id = $this->post('id');
        $data['id'] = $id;
        $data['assignment'] = $this->getAssignment($id, $this->session->userdata('school_year'));
        $this->load->view('college/subjectAssigned', $data);
    }
    
    public function getMenuByPosition($position)
    {
        $menu = $this->get_college_model->getMenuByPosition($position);
        return $menu;
    }
    
    public function getMenu($menu_id)
    {
        $menu = $this->get_college_model->getMenu($menu_id);
        return $menu;
    }
    
    function rooms()
    {
        $data['rooms'] = $this->college_model->getRooms();
        $data['modules'] = 'college';
        $data['main_content'] = 'rooms/default';
        echo Modules::run('templates/college_content', $data);
    }
    
    function getRooms()
    {
        $rooms = $this->college_model->getRooms();
        return $rooms;
    }
    
    public function settings()
    {
        
    }
    
    public function deleteROStudent()
    {
        $user_id = $this->input->post('st_id');
        $adm_id = $this->input->post('adm_id');
        $sy = $this->input->post('sy');
        if($sy==''):
            $sy = $this->session->userdata('school_year');
        endif;
        
        if($this->get_college_model->deleteROStudent($user_id, $sy, $adm_id)):
            echo 'Successfully Deleted';
        else:
            echo 'Error has occured';
        endif;
        
    }
    
    function isEnrolled($id, $year)
    {
        $result = $this->college_model->isEnrolled($id, $year);
        return $result;
    }
     
    function viewCollegeDetails($id, $sem=NULL, $year=NULL)
    {
        if($sem==NULL):
            $sem = Modules::run('main/getSemester');
        endif;
        $id = base64_decode($id);
        $year = ($year==NULL?$this->session->school_year:$year);
        
        $students = $this->get_college_model->getSingleCollegeStudent($id, $year, $sem);  
        if($this->session->userdata('position')=='Parent'):
            $data['editable'] = 'hide';
        elseif($this->session->userdata('position_id')==65):
            $data['editable'] = 'hide';
        else:
            $data['editable'] = '';
        endif;
       
        $data['students'] = $this->get_college_model->getSingleCollegeStudent($id, $year, $sem);  
        $data['m'] = $this->get_college_model->getMother($students->pid); 
        $data['f'] = $this->get_college_model->getFather($students->pid); 
        $data['date_admitted'] = $this->get_college_model->getDateEnrolled($students->u_id);
        $data['option'] = 'individual';
        $data['religion'] = Modules::run('main/getReligion');
        $data['st_id']=$id;
        $data['educ_attain'] = $this->college_model->getEducAttain();
        $data['modules'] = 'college';
        
        if($this->session->position=='Registrar Staff'):
            $data['main_content'] = 'collegeInfoAcademe';
        else:   
            $data['main_content'] = 'collegeInfo';
        endif;
        echo Modules::run('templates/college_content', $data);
    }
    
    function getTotalCollegeStudents($school_year, $semester, $status, $course=NULL, $level=NULL, $gender = NULL)
    {
        $total = $this->get_college_model->getTotalCollegeStudents($course, $level, $school_year, $semester, $status, $gender);
        //echo $total;
       return $total;
    }
    
    function getCollegeStudentsByYear($school_year, $sem){
        $result = $this->get_college_model->getCollegeStudentsByYear($school_year, $sem);
        return $result;
    }
    
    function getCollegeStudentsBySemester($school_year, $semester)
    {
            $result = $this->get_college_model->getAllCollegeStudents('','',$school_year, $semester);
            //print_r($result->result());
            $config['base_url'] = base_url('college/getCollegeStudentsBySemester/'.$school_year.'/'.$semester);
            $config['total_rows'] = $result->num_rows();
            $config['per_page'] = 10;
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
                    
            $page = $this->get_college_model->getAllCollegeStudents($config['per_page'], $this->uri->segment(5),$school_year, $semester );
            $data['students'] = $page->result();
            $data['num_of_students'] = $page->num_rows();
            $data['school_year'] = $school_year;
            
            $this->load->view('collegeStudentTable', $data);
    }
    
    function getStudents($sem=NULL)
    {
        if($sem==NULL):
            $sem = Modules::run('main/getSemester');
        endif;
        $seg = $this->uri->segment(4);
        $base_url = base_url('college/getStudents/'.$sem);
        
//        if($this->uri->segment(4)):
//            
//        else:
//            $seg = $this->uri->segment(3);
//            $base_url = base_url('college/getStudents/');
//        endif;
        if($this->session->userdata('position_id')!=4):
            //$result = $this->get_college_model->getAllCollegeStudents('','',Null, Null, $this->session->userdata('school_year'), $sem);
            $result = $this->get_college_model->getAllCollegeStudents('', '', $this->session->userdata('school_year'), $sem);
            $config['base_url'] = $base_url;
            $config['total_rows'] = $result->num_rows();
            $config['per_page'] = 10;
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
            
            $page = $this->get_college_model->getAllCollegeStudents($config['per_page'], $seg, $this->session->userdata('school_year'), $sem );
            $data['students'] = $page->result();
            $data['course']  = Modules::run('coursemanagement/getCourses');
            $data['num_of_students'] = $page->num_rows();
            $data['ro_year'] = $this->getROYear();
            $data['main_content'] = 'collegeStudents';
            $data['modules'] = 'college'; 
            echo Modules::run('templates/college_content', $data);
            
       else:
            $data['students'] = $this->get_college_model->getStudentListForParent($this->session->userdata('parent_id'));
            $data['main_content'] = 'parentRoster';
            $data['modules'] = 'college';
            echo Modules::run('templates/main_content', $data);

       endif; 
    }
    
    public function getROYear()
    {
        $year = $this->get_college_model->getROYear();
        return $year;
    }
        
    
    function getStudentListForParents($parent_id=NULL)
    {
        $students = $this->get_college_model->getStudentListForParent($parent_id);
        return $students;
    }
    
    function getLatestCollegeNum($level_id)
    {
        $id = $this->college_model->getLatestCollegeNum($level_id);
        $ids = json_decode($id);
        if($ids->num_rows==0):
          $last_three = '000';  
        else:
          $last_three = substr($ids->generated_id, -4);  
        endif;
        
        $temp_id = $this->college_model->generateTempId($ids->generated_id, abs($last_three));
        
        echo json_encode(array('status' => TRUE, 'id' => $temp_id));
    }
    

    public function saveCollegeRO()
    {
        $settings = Modules::run('main/getSet');
        $course = $this->input->post('course_id');
        $semester = $this->input->post('semester');
        $school_year = $this->input->post('school_year');
        $year_level = $this->input->post('year_level');
        if($semester==1):
            $year_level=$year_level+1;
            $sy = $school_year+1;
        else:
            $sy = $school_year;
        endif;
        $st_id = $this->input->post('st_id');
        $user_id = $this->input->post('user_id');
        $profile = $this->get_college_model->getPreviousRecord('profile', 'user_id', $user_id,  $school_year, $settings );
        
        if(!$this->get_college_model->checkCollegeRO($user_id, $semester, $school_year)):
            
            if($semester==1):    
                $profile = $this->get_college_model->getPreviousRecord('profile', 'user_id', $user_id,  $school_year, $settings );
                $profile_students = $this->get_college_model->getPreviousRecord('profile_students','user_id',$profile->user_id, $school_year, $settings);
                $profile_address = $this->get_college_model->getPreviousRecord('profile_address_info','address_id',$profile->add_id, $school_year, $settings);
                $profile_contact = $this->get_college_model->getPreviousRecord('profile_contact_details','contact_id',$profile->contact_id, $school_year, $settings);
                $profile_parents = $this->get_college_model->getPreviousRecord('profile_parents','parent_id',$profile->user_id, $school_year, $settings);
                $bdate = $this->get_college_model->getPreviousRecord('calendar','cal_id',$profile->bdate_id, $school_year, $settings);

                //print_r($profile_parents);
                if($profile_parents->guardian==0):
                    $f_profile = $this->get_college_model->getPreviousRecord('profile','user_id', $profile_parents->father_id, $school_year, $settings);
                    $m_profile = $this->get_college_model->getPreviousRecord('profile','user_id', $profile_parents->mother_id, $school_year, $settings);
                else:
                    $g_profile = $this->get_college_model->getPreviousRecord('profile','user_id',$profile_parents->guardian, $school_year, $settings);
                endif;

                $date = strtr($bdate->cal_date, '-', '/');
                $date = date('Y-m-d', strtotime($date));
                    $this->get_college_model->insertData($profile, 'profile','user_id', $profile->user_id);
                    $this->get_college_model->insertData($profile_students, 'profile_students','user_id', $profile->user_id);
                    //$this->get_college_model->insertData($profile_parents, 'profile_parents');
                    $parent_details = array(
                        'parent_id'             => $profile->user_id,
                        'father_id'             => $profile_parents->father_id,
                        'mother_id'             => $profile_parents->mother_id,
                        'f_office_name'         => $profile_parents->f_office_name,
                        'f_office_address_id'   => $profile_parents->f_office_address_id,
                        'm_office_name'         => $profile_parents->m_office_name,
                        'm_office_address_id'   => $profile_parents->m_office_address_id

                    );

                    $this->get_college_model->insertData($parent_details, 'profile_parents', 'parent_id', $profile->user_id);

                    $this->get_college_model->insertData($m_profile, 'profile','user_id', $profile_parents->father_id);
                    $this->get_college_model->insertData($f_profile, 'profile','user_id', $profile_parents->mother_id);
                    //$dateItems = explode('-', $bdate->cal_date);
                    $bCal = array(
                        'cal_id'    => $bdate->cal_id,
                        'cal_date'  => $date
                    );

                    Modules::run('calendar/saveCalendar', $bCal, $bdate->cal_id);

                    $date_id = Modules::run('calendar/saveDate', date('Y-m-d'));


                    $this->get_college_model->insertData($profile_address, 'profile_address_info','address_id',$profile->add_id);
                    $this->get_college_model->insertData($profile_contact, 'profile_contact_details','contact_id',$profile->contact_id);

                endif;

            $details = array(
                'school_year' => $sy,
                'semester' => $semester,
                'date_admitted' => date('Y-m-d'),
                'user_id' => $user_id,
                'course_id' => $course,
                'year_level' => $year_level,
                'status'    => 0,
                'st_id'    => $st_id,
            );
           
            $ro_id = $this->get_college_model->saveCollegeRO($details);
            if(!$ro_id):
                echo 'Sorry, something went wrong, Please Try Again';
            else:    
                Modules::run('web_sync/updateSyncController', 'profile_students_c_admission', 'admission_id', $ro_id['data'], 'create', 2);
                echo 'Successfully Enrolled';
            endif;
               
        
        else:
            echo 'Student Record Already Exist';
        endif;
    }
        
    function getRfidByStid($st_id)
    {
        $rfid = $this->get_college_model->getRfidByStid($st_id);
        return $rfid;
    }
    
    function getBasicInfo($user_id, $school_year)
    {
        $student = $this->get_college_model->getBasicInfo($user_id, $school_year);
        return $student;
    }
    
        
    function getBasicStudent($user_id, $school_year = NULL)
    {
        $student = $this->get_college_model->getBasicStudent($user_id, $school_year);
        return $student;
    }
    
    function saveNewValue()
    {
        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $value  = $this->input->post('value');
        $id  = $this->input->post('pk');
        $retrieve  = $this->input->post('retrieve');
        
        $nValue = array(
            $column => $value
        );
        
        $this->college_model->saveNewValue($table, $nValue);
        $module = 'main/'.$retrieve;
        $newValue = Modules::run($module);
        //echo $module;
        //print_r($newValue);
        foreach($newValue as $row)
             {
        ?>                                
            <option value="<?php echo $row->$id ?>"><?php echo $row->$column ?></option>
        <?php
             }
    }
        
    function deleteAllStudent($school_year)
    {
        $students = $this->get_college_model->getStudents(NULL,NULL,NULL,1,$school_year);
        //print_r($students);
        foreach ($students->result() as $sts):
            $lrn = $this->get_college_model->getLrnByID($sts->uid, $school_year);
            echo $sts->uid.' '.$sts->psid.'<br/>';
            $this->college_model->deleteProfile('user_id', $sts->psid, 'esk_profile', $school_year);
            $this->college_model->deleteProfile('address_id', $sts->add_id, 'esk_profile_address_info', $school_year);
            $this->college_model->deleteProfile('contact_id', $sts->con_id, 'esk_profile_contact_details', $school_year);
            $this->college_model->deleteProfile('user_id', $sts->psid, 'esk_profile_students', $school_year);
            $this->college_model->deleteProfile('user_id', $sts->psid, 'esk_profile_students_admission', $school_year);
            $this->college_model->deleteProfile('st_id', $lrn->st_id, 'esk_attendance_sheet_manual', $school_year);
            $this->college_model->deleteProfile('st_id', $lrn->st_id, 'esk_gs_raw_score', $school_year);
        endforeach;
        echo 'Students Records successfully Deleted';
    }
    
    
    function deleteID()
    {
        $user_id = $this->input->post('user_id');
        $st_id = $this->input->post('st_id');
        $lrn = $this->get_college_model->getLrnByID($st_id);
        if($this->session->userdata('username')==$user_id):
            $this->college_model->deleteProfile('user_id', $st_id, 'esk_profile');
            $this->college_model->deleteProfile('user_id', $st_id, 'esk_profile_students');
            $this->college_model->deleteProfile('user_id', $st_id, 'esk_profile_students_c_admission)');
            
            Modules::run('notification_system/department_notification', 5, "This Teacher ( $user_id ) Deleted this student ( $st_id ) from the list ");
            
            echo json_encode(array('status' => TRUE, 'msg' => "You have Successfully Deleted this student ( $st_id ) from the List"));
        else:
            echo json_encode(array('status' => FALSE, 'msg' => "It Seems that the ID you enter didn't match with your records"));
        endif;
        
    }
    
    function checkID()
    {
        $id = $this->input->post('id');
        $idExist=$this->college_model->checkID($id);
        if($idExist)
        {
            echo json_encode(array('status' => TRUE, 'msg' => 'Sorry, This ID Number Already Exist'));
        }else{
            echo json_encode(array('status' => FALSE, 'msg' => ''));
        }
    }
        
    function getMother($id)
    {
        $mother = $this->get_college_model->getMother($id);
        return $mother;
    }
    
    function getFather($id)
    {
        $mother = $this->get_college_model->getFather($id);
        return $mother;
        
    }
     
    function getSingleStudent($user_id, $year=NULL, $sem=NULL)
    {
        $student = $this->get_college_model->getSingleCollegeStudent($user_id, $year, $sem);
        return $student;
    }
    
    function getSingleStudentByRfid($user_id, $year=NULL)
    {
        $student = $this->get_college_model->getSingleStudentByRfid($user_id,$year);
        return $student;
    }

    function viewDetails($id, $year=NULL)
    {
        $id = base64_decode($id);
        $students = $this->get_college_model->getSingleStudent($id, $year);  
        if($this->session->userdata('position')=='Parent'):
            $data['editable'] = 'hide';
        else:
            $data['editable'] = '';
        endif;
        
        $data['ro_year'] = $this->getROYear();
        $data['students'] = $this->get_college_model->getSingleStudent($id, $year);  
        $data['m'] = $this->get_college_model->getMother($students->pid); 
        $data['f'] = $this->get_college_model->getFather($students->pid); 
        $data['medical'] = $this->get_college_model->getMedical($id); 
        $data['date_admitted'] = $this->get_college_model->getDateEnrolled($students->u_id);
        $data['option'] = 'individual';
        $data['religion'] = Modules::run('main/getReligion');
        $data['st_id']=$id;
        $data['educ_attain'] = $this->college_model->getEducAttain();
        
        if(Modules::run('main/isMobile')):
            $this->load->view('mobile/individualRecords', $data);
        else:
            $data['modules'] = 'college';
            $data['main_content'] = 'studentInfo';
            echo Modules::run('templates/main_content', $data);
        endif;
        
    }
    
    function admission()
    {   
        if(!$this->session->userdata('is_logged_in')){
                ?>
                     <script type="text/javascript">
                        document.location = "<?php echo base_url()?>"
                     </script>
                <?php

             }else{
                $data['settings'] = Modules::run('main/getSet');
                $data['course']  = Modules::run('coursemanagement/getCourses');
                $data['ro_year'] = $this->getROYear(); 
                $data['cities'] = Modules::run('main/getCities');
                $data['provinces'] = Modules::run('main/getProvinces');
                $data['religion'] = Modules::run('main/getReligion');
                $data['physician'] = $this->college_model->getPhysician(); 
                $data['educ_attain'] = $this->college_model->getEducAttain();
                $data['modules'] = "college";
                $data['main_content'] = 'admission';
                echo Modules::run('templates/college_content', $data);
             }
    }

    
    function saveBasicInformation($details, $lastname, $firstname, $middlename)
    {
        $result = $this->college_model->saveBasicInformation($details, $lastname, $firstname, $middlename);
        $response = json_decode($result);
        
        return $response;
    }
    
    public function saveAdmission()
    {
        if($this->input->post('inputLRN')==0){
            $st_id = $this->input->post('inputIdNum');
        }else{
            $st_id = $this->input->post('inputLRN');
        }
        
        $processAdmission = Modules::load('registrar/registrardbprocess/');
        
        $school_year = $this->input->post('inputCSY');
        $enDate = $this->input->post('inputCEdate');
        $items = array(
            'lastname'         => $this->input->post('inputCLastName'),
            'firstname'        => $this->input->post('inputCFirstName'),
            'middlename'       => $this->input->post('inputCMiddleName'),
            'add_id'           => 0,
            'sex'              => $this->input->post('inputCGender'),
            'rel_id'           => $this->input->post('inputCReligion'),
            'bdate_id'         => 0,
            'contact_id'       => 0,
            'status'           => 0,
            'nationality'      => $this->input->post('inputCNationality'),     
            'account_type'     => 5,
            'occupation_id'    => 1

       ); 

        $profile_id = $this->college_model->saveProfile($items);
        
        
        //saves the basic info
        
        $enroll_date = $this->college_model->saveEdate($enDate);
        
        $idExist = $this->college_model->checkIdIfExist($st_id);
        if($idExist):
            $st_id = $st_id+1;
        endif;
        
        $processAdmission->setCollegeInfo($st_id, $profile_id, $this->input->post('getCourse'),  $this->input->post('inputYear'), $enDate, $school_year, $this->input->post('inputSemester'), ($this->input->post('inputSLA')==""?"(No Entry)":$this->input->post('inputSLA')), ($this->input->post('inputAddressSLA')==""?"":$this->input->post('inputAddressSLA')));
        
        $barangay_id = $processAdmission->setBarangay($this->input->post('inputBarangay'));
        
        $add = array(
            'street'                => $this->input->post('inputStreet'),
            'barangay_id'              => $barangay_id,
            'city_id'                  => $this->input->post('inputMunCity'),
            'province_id'              => $this->input->post('inputPID'),
            'country'               => 'Philippines',
            'zip_code'              => $this->input->post('inputPostal'),
        );
        
        $address_id = $processAdmission->setAddress($add, $profile_id);
        
        //saves the basic contact
        $contact_id = $processAdmission->setContacts($this->input->post('inputPhone'), $this->input->post('inputEmail'), $profile_id);
        
        //saves the birthday
        $date = $this->input->post('inputBdate');
        $this->college_model->setDate($date, $profile_id, 'bdate_id');
        
        // Save Parents details
        $PExist = $this->input->post('inputFExist');

       if($PExist>0){ // If Parent Already Exist
           $this->college_model->updateParentPro($PExist, $st_id);
           echo 'parent exist';
       }else{
           
           $pgSelect = $this->input->post('pgSelect');     
           if($pgSelect==1){
                    $guardian = array(
                        'lastname'         => $this->input->post('inputGLName'),
                        'firstname'        => $this->input->post('inputGFName'),
                        'middlename'       => $this->input->post('inputGMName'),
                        'add_id'           => $address_id,
                        'sex'              => $this->input->post('inputGuardGender'),
                        'nationality'      => $this->input->post('inputNationality'),     
                        'account_type'     => 4,
                    );
                
                    $guardian_id = $processAdmission->saveProfile($guardian);        
        
                    //$processAdmission->setParentsPro($profile_id, $guardian_id,0, 1, $this->input->post('relationship')); 

                    $processAdmission->setContacts($this->input->post('inputG_num'), $this->input->post('inputGEmail'), $guardian_id);

                    $processAdmission->chooseOcc($this->input->post('inputG_occ'), $guardian_id);

                    $processAdmission->chooseEduc($this->input->post('inputGeduc'), $guardian_id);
                    
                    
                    
                Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $guardian_id, 'create', 2);
            
        }
//        else
//        {
             $father = array(
                        'lastname'         => $this->input->post('inputFLName'),
                        'firstname'        => $this->input->post('inputFName'),
                        'middlename'       => $this->input->post('inputFMName'),
                        'add_id'           => $address_id,
                        'sex'              => 'Male',
                        'nationality'      => $this->input->post('inputNationality'),     
                        'account_type'     => 4,
                    );
             
            $father_id = $processAdmission->saveProfile($father);
             
            $processAdmission->setContacts($this->input->post('inputF_num'), $this->input->post('inputPEmail'), $father_id);

            $processAdmission->chooseOcc($this->input->post('inputF_occ'), $father_id);

            $processAdmission->chooseEduc($this->input->post('inputFeduc'), $father_id);
            
            //saves the Father's office address

            $fbarangay_id = $processAdmission->setBarangay(($this->input->post('f_officeBarangay')!=""?$this->input->post('f_officeBarangay'):$this->input->post('inputBarangay')));

            $faddOffice = array(
                'street'                => $this->input->post('f_officeStreet'),
                'barangay_id'              => $fbarangay_id,
                'city_id'                  => ($this->input->post('f_officeMunCity')!=""?$this->input->post('f_officeMunCity'):$this->input->post('inputMunCity')),
                'province_id'              => ($this->input->post('f_officePID')!=""?$this->input->post('f_officePID'):$this->input->post('inputPID')),
                'country'               => 'Philippines',
                'zip_code'              => '',
            );

            $fOfficeAddress_id = $processAdmission->setOfficeAddress($faddOffice ) ;
             
             $mother = array(
                        'lastname'         => $this->input->post('inputMLName'),
                        'firstname'        => $this->input->post('inputMother'),
                        'middlename'       => $this->input->post('inputMMName'),
                        'add_id'           => $address_id,
                        'sex'              => 'Female',
                        'nationality'      => $this->input->post('inputNationality'),     
                        'account_type'     => 4,
                    );
             
             $mother_id = $processAdmission->saveProfile($mother);
             
            
             
             $processAdmission->setContacts($this->input->post('inputM_num'), $this->input->post('inputPEmail'), $mother_id);

            $processAdmission->chooseOcc($this->input->post('inputM_occ'), $mother_id);

            $processAdmission->chooseEduc($this->input->post('inputMeduc'), $mother_id);
            
            //saves the Mother's office address

            $mbarangay_id = $processAdmission->setBarangay(($this->input->post('m_officeBarangay')!=""?$this->input->post('m_officeBarangay'):$this->input->post('inputBarangay')));

            $maddOffice = array(
                'street'                => $this->input->post('m_officeStreet'),
                'barangay_id'              => $mbarangay_id,
                'city_id'                  => ($this->input->post('m_officeMunCity')!=""?$this->input->post('m_officeMunCity'):$this->input->post('inputMunCity')),
                'province_id'              => ($this->input->post('m_officePID')!=""?$this->input->post('m_officePID'):$this->input->post('inputPID')),
                'country'               => 'Philippines',
                'zip_code'              => '',
            );

            $mOfficeAddress_id = $processAdmission->setOfficeAddress($maddOffice) ;
             
            $processAdmission->setParentsPro($profile_id, $father_id, $mother_id, $this->input->post('f_officeName'), $fOfficeAddress_id, $this->input->post('f_officeName'), $mOfficeAddress_id,  ($guardian_id!=NULL?$guardian_id:0), $this->input->post('relationship'));
             
             
            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $father_id, 'create', 2);
            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $mother_id, 'create', 2);
            
            //}
            
        }
       //Medical information
       $this->college_model->saveMed($this->input->post('inputBType'), 
       $this->input->post('inputAllergies'), 
       $this->input->post('inputOtherMedInfo'), 
       $this->input->post('inputFPhy'), 
       $this->input->post('height'), 
       $this->input->post('weight'), 
       $profile_id );
       
        Modules::run('web_sync/updateSyncController', 'calendar', 'cal_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_students', 'user_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $address_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $fOfficeAddress_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $mOfficeAddress_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_contact_details', 'contact_id', $contact_id, 'create', 2);
        
        Modules::run('web_sync/updateSyncController', 'profile_medical', 'user_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $profile_id, 'create', 2);
        return;

    }
    
    public function setBirthdate($date, $id, $column)
    {
        $this->college_model->setDate($date, $id, $column);
    }
    
    function saveSectionValue()
    {
        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $value  = $this->input->post('value');
        $grade_id  = $this->input->post('pk');
        
        
        $nValue = array(
            $column => $value,
            'grade_level_id' => $grade_id
        );
        
        $this->college_model->saveNewValue($table, $nValue);
        $newValue = $this->getSectionByGradeId($grade_id);
        //echo $module;
        //print_r($newValue);
        foreach ($newValue->result() as $row)
        {
            ?>
            <li><?php echo $row->section  ?></li>
            <?php
        }
    }
    
    
    function getSectionByGradeId($grade_id)
    {
        $section = $this->college_model->getSectionByLevel($grade_id);
        return $section;
        
    }
    
    function getAllSection($section_id=NULL, $option=NULL)
    {
        $section = $this->college_model->getAllSection($section_id, $option);
        return $section;
    }
    
    public function enrollmentListing()
    {
        if(!$this->session->userdata('is_logged_in')){
                ?>
                     <script type="text/javascript">
                        document.location = "<?php echo base_url()?>"
                     </script>
                <?php

             }else{
                $data['main_content'] = 'enrollmentList';                   
                $data['grade'] = $this->getGradeLevel(); 
                $data['modules'] = 'college';
                echo Modules::run('templates/main_content', $data);	
             }
    }
    
    function nstpEL($semester, $school_year)
    {
        $this->load->library('eskwela');
        $this->load->library('excel');
        $this->load->helper('download');
        $settings = $this->eskwela->getSet();
        
        switch($semester):
            case 1:
                $sem = '1st Semester';
            break;
            case 2:
                $sem = '2nd Semester';
            break;
            case 3:
                $sem = 'Summer';
            break;
        endswitch;
        $data['settings']= $settings;
        $data['semester'] = $semester;
        $data['sem'] = $sem;
        $data['school_year'] = $school_year;
        $data['lts']     = 185;
        $data['subject'] = Modules::run('college/coursemanagement/getNstpStudents', $semester, $school_year, $data['lts']);
        $data['subject_cwts'] = Modules::run('college/coursemanagement/getNstpStudents', $semester, $school_year, 151);
        
        $this->load->view('nstpEL_lts', $data);
        $this->load->view('nstpEL_cwts', $data);
        
        $filename=$settings->short_name.'_NSTP Enrollment List_'.$sem.'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
     function exportForId($course_id = NULL)
    {
        $this->load->library('eskwela');
        $this->load->library('excel');
        $this->load->helper('download');
        $sy = $this->session->userdata('school_year');
        $settings = $this->eskwela->getSet();
        
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Students');
        $this->excel->getActiveSheet()->setCellValue('A1', 'Student ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Name');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Course');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Birthday');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Parents / Guardians');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Contact Number');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Address');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Student Contact');
        $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        
        $students = $this->get_college_model->getAllCollegeStudents(600, 0, $sy, 1, $course_id);
        $column = 1;
        foreach ($students->result() as $s):
            $column++;
            $father = Modules::run('registrar/getFather', $s->parent_id);
            $mother = Modules::run('registrar/getMother', $s->parent_id);
            if($father->user_id!=""):
                $parentName = $father->firstname.' '.substr($father->middlename, 0,1).' '.$father->lastname;
            else:
                $parentName = $mother->firstname.' '.substr($mother->middlename, 0,1).' '.$mother->lastname;
            endif;
            if($father->cd_mobile!=""):
                $contact = $father->cd_mobile;
            else:
                $contact = $mother->cd_mobile;
            endif;
            if($s->street!=""):
                $address = $s->street.', '.$s->barangay.', '.$s->mun_city.' '.$s->province;
            else:
                $address = $s->barangay.', '.$s->mun_city.' '.$s->province;
            endif;
            $this->excel->getActiveSheet()->setCellValue('A'.$column,$s->st_id);
            $this->excel->getActiveSheet()->setCellValue('B'.$column,  ucfirst($s->firstname.' '.substr($s->middlename, 0,1).'. '.$s->lastname));
            $this->excel->getActiveSheet()->setCellValue('C'.$column,  $s->short_code.' - '.$s->year_level);
            $this->excel->getActiveSheet()->setCellValue('D'.$column, date('F d, Y', strtotime($s->cal_date)));
            $this->excel->getActiveSheet()->setCellValue('E'.$column, ucfirst($parentName));
            $this->excel->getActiveSheet()->setCellValue('F'.$column,  $contact);
            $this->excel->getActiveSheet()->setCellValue('G'.$column,  $address);
            $this->excel->getActiveSheet()->setCellValue('H'.$column,  $s->user_id);
            $this->excel->getActiveSheet()->setCellValue('I'.$column,  $s->cd_mobile);
        endforeach;
        
        $filename=$settings->short_name.'_'.$sy.'_'.$s->short_code.'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
        
   
   
   
   
}
