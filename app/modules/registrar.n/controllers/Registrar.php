<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of Registrar
 *
 * @author genesis
 */
class Registrar extends MX_Controller {

    //put your code here

    protected $processAdmission;

    function __construct() {
        parent::__construct();
        $this->load->model('registrar_model');
        $this->load->model('get_registrar_model');
        $this->load->library('pagination');
        $this->load->library('Pdf');
        $this->processAdmission = Modules::load('registrar/registrardbprocess/');
    }
    
        
    private function post($name)
    {
        return $this->input->post($name);
    }
    
    
    function editOccupation()
    {
        $occupation = $this->post('value');
        $p_id = $this->post('owner');
        $mf = $this->post('mf');
        $sy = $this->input->post('sy');
        
        $this->registrar->editOccupation($occupation, $p_id, $mf, $sy);
        
        
        
        echo $occupation;
    }
            
    function editParentInfo()
    {
        $firstname  = $this->post('firstname');
        $lastname   = $this->post('lastname');
        $middlename = $this->post('middlename');
        $pos        = $this->post('pos');
        $parent_id  = $this->post('parent_id');
        $school_year= $this->post('sy');
        
        $nameArray = array(
            $pos.'_firstname'   => $firstname,
            $pos.'_lastname'    => $lastname,
            $pos.'_middlename'  => $middlename
        );
        
        if($this->registrar_model->editParentInfo($nameArray, $parent_id, $school_year)):
            echo $firstname.' '.$lastname;
        endif;
    }

    function update_student_stType() {
        $type = $this->input->post("st_type");
        $uid = $this->input->post("st_userid");
        $schoolyear = $this->input->post("school_year");

        $query = $this->get_registrar_model->update_student_stType($type, $uid, $schoolyear);
        if ($query):
            echo 'Student Account Type Successfully Updated';
        else:
            echo 'Sorry Something went Wrong';
        endif;
    }

    function updateStudentStatus($admission_id) {
        $status = $this->get_registrar_model->updateStudentStatus($admission_id);
        return $status;
    }

    function updateParentProfile() {
        $this->db->select('profile_students.user_id as u_id');
        $this->db->select('profile_students.parent_id as p1');
        $this->db->select('profile_parents.parent_id as p2');
        $this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        $this->db->order_by('user_id', 'ASC');
        $students = $this->db->get('profile_students');
        //print_r($students->result());
        foreach ($students->result() as $s):
            echo $s->u_id . ' | ' . $s->p2 . ' - ' . $s->p1 . '<br />';

            $data = array(
                'parent_id' => $s->u_id
            );

            $this->db->where('user_id', $s->u_id);
            if ($this->db->update('profile_students', $data)):
                echo $s->u_id . ' in students is updated <br />';
                $this->db->where('parent_id', $s->p2);
                $this->db->update('profile_parents', $data);
            endif;



        endforeach;
    }

    function updateAdmission() {
        $i = 1;
        $q = $this->db->get('profile_students');
        foreach ($q->result() as $adm):
            $i++;
            $user_id = $adm->user_id;
            $this->db->where('user_id', $user_id);
            $this->db->update('profile_students_admission', array('st_id' => $adm->st_id));
        endforeach;
        echo 'successfully updated ' . $i . ' students';
    }

    function checkEnrollees() {
//        $q = $this->db->query('Select esk_profile.user_id as uid, lastname, esk_profile_students.st_id from esk_profile 
//                                left join esk_profile_students_admission on esk_profile.user_id = esk_profile_students_admission.user_id
//                                left join esk_profile_students on esk_profile.user_id = esk_profile_students.user_id
//                                where account_type = 5 and not exists(Select * from esk_profile_students_admission where esk_profile.user_id = esk_profile_students_admission.user_id order by lastname)
//                            ');
        $q = $this->db->query('Select esk_profile_students.st_id from esk_profile_students 
                                left join esk_profile on esk_profile_students.user_id = esk_profile.user_id
                                left join esk_profile_students_admission on esk_profile_students.st_id = esk_profile_students_admission.st_id
                                where account_type = 5 and not exists(Select * from esk_profile_students_admission where esk_profile_students.st_id = esk_profile_students_admission.st_id order by lastname)
                            ');
        print_r($q->result());
        echo '<br />';
        echo '<br />';
        foreach ($q->result() as $qr):
            echo $qr->uid . ' - ' . $qr->lastname . ' - ' . $qr->st_id . '<br />';
//            $this->db->where('user_id', $qr->uid);
//            if($this->db->delete('profile')):
//                echo $qr->uid.' successfully deleted <br />';
//            endif;
        endforeach;
    }

    function cleanEnrollees() {
        $q = $this->db->query('Select esk_profile.user_id as uid, lastname from esk_profile 
                                left join esk_profile_students on esk_profile.user_id = esk_profile_students.user_id
                                where account_type = 5 and not exists(Select * from esk_profile_students where esk_profile.user_id = esk_profile_students.user_id order by lastname)
                            ');
        foreach ($q->result() as $qr):
            $this->db->where('user_id', $qr->uid);
            if ($this->db->delete('profile')):
                echo $qr->uid . ' successfully deleted <br />';
            endif;
        endforeach;
    }

    function exportForId($level_id = NULL) {
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
        $this->excel->getActiveSheet()->setCellValue('C1', 'Grade Level');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Birthday');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Parents / Guardians');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Contact Number');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Address');
        $this->excel->getActiveSheet()->setCellValue('H1', '');
        $this->excel->getActiveSheet()->setCellValue('I1', 'LASTNAME');
        $this->excel->getActiveSheet()->setCellValue('J1', 'FIRSTNAME');
        $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        $students = $this->get_registrar_model->getAllStudents(600, 0, $level_id, NULL, $sy);
        //$students = $this->get_registrar_model->getAllCollegeStudents(600, 0, $level_id, NULL, $sy);
        $column = 1;
        foreach ($students->result() as $s):
            $column++;
            $father = Modules::run('registrar/getFather', $s->user_id);
            $mother = Modules::run('registrar/getMother', $s->user_id);
            if ($father->user_id != ""):
                $parentName = ($father->firstname == "" ? $s->ice_name : $father->firstname . ' ' . substr($father->middlename, 0, 1) . ' ' . $father->lastname);
            else:
                $parentName = ($mother->firstname == "" ? $s->ice_name : $mother->firstname . ' ' . substr($mother->middlename, 0, 1) . ' ' . $mother->lastname);
            endif;
            if ($father->cd_mobile != ""):
                $contact = ($father->cd_mobile == "" ? $s->ice_contact : $father->cd_mobile == "");
            else:
                $contact = ($mother->cd_mobile == "" ? $s->ice_contact : $mother->cd_mobile);
            endif;
            if ($s->street != ""):
                $address = $s->street . ', ' . $s->barangay . ', ' . $s->mun_city . ' ' . $s->province;
            else:
                $address = $s->barangay . ', ' . $s->mun_city . ' ' . $s->province;
            endif;
            $this->excel->getActiveSheet()->setCellValue('A' . $column, ($s->lrn == "" ? $s->uid : $s->uid));
            $this->excel->getActiveSheet()->setCellValue('B' . $column, strtoupper($s->firstname . ' ' . substr($s->middlename, 0, 1) . '. ' . $s->lastname));
            $this->excel->getActiveSheet()->setCellValue('C' . $column, $s->level);
            $this->excel->getActiveSheet()->setCellValue('D' . $column, date('F d, Y', strtotime($s->cal_date)));
            $this->excel->getActiveSheet()->setCellValue('E' . $column, strtoupper($parentName));
            $this->excel->getActiveSheet()->setCellValue('F' . $column, $s->ice_contact);
            $this->excel->getActiveSheet()->setCellValue('G' . $column, $address);
            $this->excel->getActiveSheet()->setCellValue('H' . $column, $s->user_id);
            $this->excel->getActiveSheet()->setCellValue('I' . $column, strtoupper($s->lastname));
            $this->excel->getActiveSheet()->setCellValue('J' . $column, strtoupper($s->firstname . ' ' . substr($s->middlename, 0, 1) . '. '));
        endforeach;

        $filename = $settings->short_name . '_' . $sy . '_' . $s->level . '.xls'; //save our workbook as this file name
        // $filename=$settings->short_name.'_'.$sy.'_'.$s->course_id.'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }

    function getSpecialization($specs_id = NULL) {
        $spec = $this->get_registrar_model->getSpecialization($specs_id);
        return $spec;
    }

    function getStudentDepartment($id, $year) {
        $result = $this->registrar_model->isEnrolled($id, $year);
        if ($result):
            return 'basic';
        else:
            $collegeExist = Modules::run('college/isEnrolled', $id, $year);
            if ($collegeExist):
                return 'college';
            else:
                return FALSE;
            endif;
        endif;
    }

    function isEnrolled($id, $year) {
        $result = $this->registrar_model->isEnrolled($id, $year);
        if ($result):
            return TRUE;
        else:
            $collegeExist = Modules::run('college/isEnrolled', $id, $year);
            if ($collegeExist):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }

    function viewCollegeDetails($id, $year = NULL) {
        $id = base64_decode($id);
        $students = $this->get_registrar_model->getSingleCollegeStudent($id, $year);
        if ($this->session->userdata('position') == 'Parent'):
            $data['editable'] = 'hide';
        else:
            $data['editable'] = '';
        endif;

        $data['students'] = $this->get_registrar_model->getSingleCollegeStudent($id, $year);
        $data['m'] = $this->get_registrar_model->getMother($students->pid);
        $data['f'] = $this->get_registrar_model->getFather($students->pid);
        $data['medical'] = $this->get_registrar_model->getMedical($id);
        $data['date_admitted'] = $this->get_registrar_model->getDateEnrolled($students->u_id);
        $data['option'] = 'individual';
        $data['religion'] = Modules::run('main/getReligion');
        $data['st_id'] = $id;
        $data['educ_attain'] = $this->registrar_model->getEducAttain();
        $data['modules'] = 'registrar';
        $data['main_content'] = 'collegeInfo';
        echo Modules::run('templates/main_content', $data);
    }

    function getTotalCollegeStudents($school_year, $semester, $status, $course = NULL, $level = NULL) {
        $total = $this->get_registrar_model->getTotalCollegeStudents($course, $level, $school_year, $semester, $status);
        return $total;
    }

    function getCollegeStudentsBySemester($school_year, $semester) {
        $result = $this->get_registrar_model->getAllCollegeStudents('', '', Null, Null, $school_year, $semester);
        $config['base_url'] = base_url('registrar/getCollegeStudents/' . $semester);
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

        $page = $this->get_registrar_model->getAllCollegeStudents($config['per_page'], $this->uri->segment(4), Null, Null, $school_year, $semester);
        $data['students'] = $page->result();
        $data['num_of_students'] = $page->num_rows();

        $this->load->view('collegeStudentTable', $data);
    }

    function getCollegeStudents($sem = NULL) {
        if ($sem == NULL):
            $sem = Modules::run('main/getSemester');
        endif;
        if ($this->uri->segment(4)):
            $seg = $this->uri->segment(4);
            $base_url = base_url('registrar/getCollegeStudents/' . $sem);
        else:
            $seg = $this->uri->segment(3);
            $base_url = base_url('registrar/getCollegeStudents/');
        endif;
        if ($this->session->userdata('position_id') != 4):
            $result = $this->get_registrar_model->getAllCollegeStudents('', '', Null, Null, $this->session->userdata('school_year'), $sem);
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

            $page = $this->get_registrar_model->getAllCollegeStudents($config['per_page'], $seg, Null, Null, $this->session->userdata('school_year'), $sem);
            $data['students'] = $page->result();
            $data['num_of_students'] = $page->num_rows();
            $data['grade'] = $this->getGradeLevel();
            $data['section'] = $this->getAllSection();
            $data['ro_year'] = $this->getROYear();
            if (Modules::run('main/isMobile')):
                if ($this->session->userdata('position_id') == 39):
                    redirect(base_url() . 'registrar/getAllStudentsBySection/' . $this->session->userdata('advisory'), $this->session->userdata('school_year'));
                endif;
            else:
                if ($this->session->userdata('position_id') == 39):

                    redirect(base_url() . 'registrar/getAllStudentsBySection/' . $this->session->userdata('advisory'), $this->session->userdata('school_year'));
                else:
                    $data['main_content'] = 'collegeStudents';
                    $data['modules'] = 'registrar';
                    echo Modules::run('templates/main_content', $data);
                endif;

            endif;

        else:
            $data['students'] = $this->get_registrar_model->getStudentListForParent($this->session->userdata('parent_id'));
            $data['main_content'] = 'parentRoster';
            $data['modules'] = 'registrar';
            echo Modules::run('templates/main_content', $data);

        endif;
    }

    function getStudentListForParents($parent_id = NULL) {
        $students = $this->get_registrar_model->getStudentListForParent($parent_id);
        return $students;
    }

    function getAllStudentsByLevel($grade_level = NULL, $section_id = null, $year = NULL) {
        $result = $this->get_registrar_model->getAllStudentsByLevel($grade_level, $section_id, $year);
        return $result;
        //echo $result->num_rows();
    }

    function getAllStudentsBasicInfoByGender($section_id = null, $gender = null, $status = NULL, $year = NULL) {
        $result = $this->get_registrar_model->getAllStudentsBasicInfoByGender($section_id, $gender, $status, $year);
        return $result;
        //echo $result->num_rows();
    }

    public function getNumberOfStudentPerSection($section_id, $year = NULL, $status = NULL) {
        $count = $this->get_registrar_model->getNumberOfStudentPerSection($section_id, $year, $status);
        return $count;
    }

    public function getMLM($year, $month, $grade_id, $code) {
        $mlm = $this->get_registrar_model->getMLM($year, $month, $grade_id, $code);
        return $mlm;
    }

    public function saveMLM($m, $f, $grade_id, $month, $year, $code) {
        $details = array(
            'year' => $year,
            'month' => $month,
            'mlm_grade_id' => $grade_id,
            'm' => $m,
            'f' => $f,
            'code_indicator' => $code,
        );

        $mlm_id = $this->registrar_model->saveMLM($details, $month, $year, $grade_id, $code);

        if ($mlm_id['action'] == 'update'):
            Modules::run('web_sync/updateSyncController', 'profile_students_mlm', 'id', $mlm_id['id'], 'update', 2);
        else:
            Modules::run('web_sync/updateSyncController', 'profile_students_mlm', 'id', $mlm_id['id'], 'create', 2);
        endif;

        return;
    }

    public function getMMG($value = NULL, $sy = NULL) {
        $data['month'] = $value;
        $data['school_year'] = $sy;
        $this->load->view('mmgraph', $data);
    }

    public function getStudentPerRO($ro, $section, $sy = NULL) {
        $student = $this->get_registrar_model->getStudentPerRO($ro, $section, $sy);
        return $student;
        //echo $student;
    }

    public function deleteROStudent() {
        $user_id = $this->input->post('st_id');
        $adm_id = $this->input->post('adm_id');
        $sy = $this->input->post('sy');
        if ($sy == ''):
            $sy = $this->session->userdata('school_year');
        endif;

        if ($this->get_registrar_model->deleteROStudent($user_id, $sy, $adm_id)):
            $student = $this->getBasicInfo($user_id, $sy);
            Modules::run('main/logActivity','DELETE',  $this->session->userdata('name').' has deleted a student named '.$student->lastname.', '.$student->firstname, $this->session->userdata('user_id'));
            echo 'Successfully Deleted';
        else:
            echo 'Error has occured';
        endif;
    }

    public function saveCollegeRO() {
        $course = $this->input->post('course_id');
        $semester = $this->input->post('semester');
        $school_year = $this->input->post('school_year');
        $year_level = $this->input->post('year_level');
        if ($semester == 1):
            $year_level = $year_level + 1;
        endif;
        $st_id = $this->input->post('st_id');
        $user_id = $this->input->post('user_id');

        if (!$this->get_registrar_model->checkCollegeRO($user_id, $semester, $school_year)):
            $details = array(
                'school_year' => $school_year,
                'semester' => $semester,
                'date_admitted' => date('Y-m-d'),
                'user_id' => $user_id,
                'course_id' => $course,
                'year_level' => $year_level,
                'status' => 1,
                'st_id' => $st_id,
            );

            $ro_id = $this->get_registrar_model->saveCollegeRO($details);
            if (!$ro_id):
                echo 'Sorry, something went wrong, Please Try Again';
            else:
                Modules::run('web_sync/updateSyncController', 'profile_students_c_admission', 'admission_id', $ro_id['data'], 'create', 2);
                echo 'Successfully Enrolled';
            endif;


        else:
            echo 'Student Record Already Exist';
        endif;
    }

    public function saveRO() {
        $settings = Modules::run('main/getSet');
        $grade_id = $this->input->post('grade_id');
        $section_id = $this->input->post('section_id');
        $st_id = $this->input->post('st_id');
        $school_year = $this->input->post('school_year');

        $profile = $this->get_registrar_model->getPreviousRecord('profile', 'user_id', $st_id, $school_year, $settings);
        $profile_students = $this->get_registrar_model->getPreviousRecord('profile_students', 'user_id', $profile->user_id, $school_year, $settings);
        $profile_address = $this->get_registrar_model->getPreviousRecord('profile_address_info', 'address_id', $profile->add_id, $school_year, $settings);
        $profile_contact = $this->get_registrar_model->getPreviousRecord('profile_contact_details', 'contact_id', $profile->contact_id, $school_year, $settings);
        $profile_parents = $this->get_registrar_model->getPreviousRecord('profile_parents', 'parent_id', $profile->user_id, $school_year, $settings);
        $bdate = $this->get_registrar_model->getPreviousRecord('calendar', 'cal_id', $profile->bdate_id, $school_year, $settings);

        //print_r($profile_parents);
        if ($profile_parents->guardian == 0):
            $f_profile = $this->get_registrar_model->getPreviousRecord('profile', 'user_id', $profile_parents->father_id, $school_year, $settings);
            $m_profile = $this->get_registrar_model->getPreviousRecord('profile', 'user_id', $profile_parents->mother_id, $school_year, $settings);
        else:
            $g_profile = $this->get_registrar_model->getPreviousRecord('profile', 'user_id', $profile_parents->guardian, $school_year, $settings);
        endif;

        $date = strtr($bdate->cal_date, '-', '/');
        $date = date('Y-m-d', strtotime($date));
        if (!empty($profile)):
            $this->get_registrar_model->insertData($profile, 'profile');
            $this->get_registrar_model->insertData($profile_students, 'profile_students');
            //$this->get_registrar_model->insertData($profile_parents, 'profile_parents');
            $parent_details = array(
                'parent_id' => $profile->user_id,
                'father_id' => $profile_parents->father_id,
                'mother_id' => $profile_parents->mother_id,
                'f_office_name' => $profile_parents->f_office_name,
                'f_office_address_id' => $profile_parents->f_office_address_id,
                'm_office_name' => $profile_parents->m_office_name,
                'm_office_address_id' => $profile_parents->m_office_address_id,
                'ice_name' => $profile_parents->ice_name,
                'ice_contact' => $profile_parents->ice_contact
            );

            $this->get_registrar_model->insertData($parent_details, 'profile_parents');

            $this->get_registrar_model->insertData($m_profile, 'profile');
            $this->get_registrar_model->insertData($f_profile, 'profile');
            //$dateItems = explode('-', $bdate->cal_date);
            $bCal = array(
                'cal_id' => $bdate->cal_id,
                'cal_date' => $date
            );

            Modules::run('calendar/saveCalendar', $bCal, $bdate->cal_id);

            $date_id = Modules::run('calendar/saveDate', date('Y-m-d'));
            $sy = $school_year + 1;

            Modules::run('main/detect_column', 'esk_profile_students_admission', 'st_type');

            $admission = array(
                'school_year' => $sy,
                'user_id' => $profile->user_id,
                'grade_level_id' => $grade_id,
                'section_id' => $section_id,
                'status' => 0,
                'school_last_attend' => strtoupper($settings->set_school_name),
                'sla_address' => strtoupper($settings->set_school_address),
                'st_id' => $profile_students->st_id,
                'st_type' => 1
            );

            $this->get_registrar_model->insertData($profile_address, 'profile_address_info', 'address_id', $profile->add_id);
            $this->get_registrar_model->insertData($profile_contact, 'profile_contact_details', 'contact_id', $profile->contact_id);

            if ($this->registrar_model->saveStudentAdmission($admission, $profile->user_id, $sy)):
                echo 'Successfully Saved';
            else:
                echo 'Student is Already on the list';
            endif;
        endif;

        //print_r($profile);
    }

    public function getStudentByYear($year) {
        $result = $this->get_registrar_model->getStudentBySY($year, NULL, NULL);

        $config['base_url'] = base_url('registrar/getStudentByYear/' . $year);
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 10;
        $config["uri_segment"] = 4;
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

        $page = $this->get_registrar_model->getStudentBySY($year, $config['per_page'], $this->uri->segment(4));
        $active = $this->get_registrar_model->getStudentBySY($year, NULL, NULL, 1);
        $data['links'] = $this->pagination->create_links();
        $data['students'] = $page->result();
        $data['num_of_students'] = $result->num_rows();
        $data['grade'] = $this->getGradeLevel(NULL, NULL);
        $data['section'] = $this->getAllSection();
        $data['num_of_students'] = $active->num_rows();
        $data['allStudents'] = $result->num_rows();
        $data['ro_year'] = $this->getROYear();
        $data['main_content'] = 'ro_roster';
        $data['modules'] = 'registrar';
        $data['year'] = $year;
        echo Modules::run('templates/main_content', $data);
    }

    public function getROYear() {
        $year = $this->get_registrar_model->getROYear();
        return $year;
    }

    function getRfidByStid($st_id) {
        $rfid = $this->get_registrar_model->getRfidByStid($st_id);
        return $rfid;
    }

    function getBasicInfo($user_id, $school_year) {
        $student = $this->get_registrar_model->getBasicInfo($user_id, $school_year);
        return $student;
    }

    function getBasicStudent($user_id, $school_year = NULL) {
        $student = $this->get_registrar_model->getBasicStudent($user_id, $school_year);
        return $student;
    }

    function getStudentForCard($limit, $offset, $section_id, $school_year = NULL) {
        $students = $this->get_registrar_model->getAllStudentsForCard($limit, $offset, $section_id, $school_year);
        //print_r($students->result());
        return $students;
    }

    function printIdCard($section_id, $limit, $offset) {

        if ($offset != 0):
            $offset = $offset + 4;
        endif;
        $data['students'] = $this->get_registrar_model->getAllStudentsForID($limit, $offset, NULL, $section_id);
        $this->load->view('printId', $data);
    }

    function printIdCardBack($section_id, $limit, $offset) {
        $data['students'] = $this->get_registrar_model->getAllStudentsForID($limit, $offset, NULL, $section_id);
        $this->load->view('printId_back', $data);
    }

    function saveNewValue() {
        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $value = $this->input->post('value');
        $id = $this->input->post('pk');
        $retrieve = $this->input->post('retrieve');

        $nValue = array(
            $column => $value
        );

        $this->registrar_model->saveNewValue($table, $nValue);
        $module = 'main/' . $retrieve;
        $newValue = Modules::run($module);
        //echo $module;
        //print_r($newValue);
        foreach ($newValue as $row) {
            ?>                                
            <option value="<?php echo $row->$id ?>"><?php echo $row->$column ?></option>
            <?php
        }
    }

    function editIdNumber() {
        $origIdNumber = $this->input->post('origIdNumber');
        $editedIdNumber = $this->input->post('editedIdNumber');
        $row = $this->registrar_model->getUserId($origIdNumber);
        $updatedIDNumber = array(
            'user_id' => $editedIdNumber
        );
        if ($this->registrar_model->updateProfileInfo($row->user_id, array('st_id' => $editedIdNumber))):
            $this->registrar_model->updateProfileAdmission($row->user_id, array('st_id' => $editedIdNumber));
            $this->registrar_model->updateProfileDetails($origIdNumber, $updatedIDNumber, 'profile_medical');
            $this->registrar_model->updateGradingAttendanceDetails($origIdNumber, array('st_id' => $editedIdNumber), 'gs_raw_score');
            if (!$this->session->userdata('attend_auto')):
                $this->registrar_model->updateGradingAttendanceDetails($origIdNumber, array('st_id' => $editedIdNumber), 'attendance_sheet_manual');
            else:
//                  use to update finance details
                $this->registrar_model->updateFinanceDetails($origIdNumber, array('stud_id' => $editedIdNumber), 'fin_accounts');
                $this->registrar_model->updateFinanceDetails($origIdNumber, array('stud_id' => $editedIdNumber), 'fin_transaction');
                $this->registrar_model->updateFinanceDetails($origIdNumber, array('stud_id' => $editedIdNumber), 'fin_extra');
                $this->registrar_model->updateFinanceDetails($origIdNumber, array('stud_id' => $editedIdNumber), 'fin_discount');
            endif;
            $this->registrar_model->updateGradingAttendanceDetails($origIdNumber, array('st_id' => $editedIdNumber), 'gs_final_assessment');
            $this->registrar_model->updateGradingAttendanceDetails($origIdNumber, array('st_id' => $editedIdNumber), 'gs_final_card');
            $this->registrar_model->updateGradingAttendanceDetails($origIdNumber, array('st_id' => $editedIdNumber), 'gs_incomplete_subjects');


            echo $editedIdNumber;
        endif;
    }

    function deleteAllStudent($school_year) {
        $students = $this->get_registrar_model->getStudents(NULL, NULL, NULL, 1, $school_year);
        //print_r($students);
        foreach ($students->result() as $sts):
            $lrn = $this->get_registrar_model->getLrnByID($sts->uid, $school_year);
            echo $sts->uid . ' ' . $sts->psid . '<br/>';
            $this->registrar_model->deleteProfile('user_id', $sts->psid, 'esk_profile', $school_year);
            $this->registrar_model->deleteProfile('address_id', $sts->add_id, 'esk_profile_address_info', $school_year);
            $this->registrar_model->deleteProfile('contact_id', $sts->con_id, 'esk_profile_contact_details', $school_year);
            $this->registrar_model->deleteProfile('user_id', $sts->psid, 'esk_profile_students', $school_year);
            $this->registrar_model->deleteProfile('user_id', $sts->psid, 'esk_profile_students_admission', $school_year);
            $this->registrar_model->deleteProfile('st_id', $lrn->st_id, 'esk_attendance_sheet_manual', $school_year);
            $this->registrar_model->deleteProfile('st_id', $lrn->st_id, 'esk_gs_raw_score', $school_year);
        endforeach;
        echo 'Students Records successfully Deleted';
    }

    function deleteID() {
        $user_id = $this->input->post('user_id');
        $st_id = $this->input->post('st_id');
        $school_year = $this->input->post('school_year');
        $lrn = $this->get_registrar_model->getLrnByID($st_id);
        if ($this->session->userdata('username') == $user_id):
            $this->registrar_model->deleteProfile('user_id', $st_id, 'esk_profile', $school_year);
            $this->registrar_model->deleteProfile('user_id', $st_id, 'esk_profile_students', $school_year);
            $this->registrar_model->deleteProfile('user_id', $lrn->uid, 'esk_profile_students_admission', $school_year);
            $this->registrar_model->deleteProfile('st_id', $lrn->st_id, 'esk_attendance_sheet_manual', $school_year);
            $this->registrar_model->deleteProfile('st_id', $lrn->st_id, 'esk_gs_raw_score', $school_year);

            Modules::run('notification_system/department_notification', 5, "This Teacher ( $user_id ) Deleted this student ( $st_id ) from the list ");

            echo json_encode(array('status' => TRUE, 'msg' => "You have Successfully Deleted this student ( $st_id ) from the List"));
        else:
            echo json_encode(array('status' => FALSE, 'msg' => "It Seems that the ID you enter didn't match with your records"));
        endif;
    }

    function checkID() {
        $id = $this->input->post('id');
        $idExist = $this->registrar_model->checkID($id);
        if ($idExist) {
            echo json_encode(array('status' => TRUE, 'msg' => 'Sorry, This ID Number Already Exist'));
        } else {
            echo json_encode(array('status' => FALSE, 'msg' => ''));
        }
    }

    function getSectionById($id) {
        $section = $this->get_registrar_model->getSectionById($id);
        return $section;
    }

    function getSection($id) {
        $section = $this->get_registrar_model->getSection($id);
        return $section;
    }

    function getMother($id) {
        $mother = $this->get_registrar_model->getMother($id);
        return $mother;
    }

    function getFather($id) {
        $mother = $this->get_registrar_model->getFather($id);
        return $mother;
    }

    function getSingleStudent($user_id, $year = NULL) {
        $student = $this->get_registrar_model->getSingleStudent($user_id, $year);
        return $student;
    }

    function scanStudent($rfid) {
        $students = $this->getSingleStudentByRfid($rfid);
        echo json_encode(array('st_id' => base64_encode($students->row()->st_id)));
    }

    function getSingleStudentByRfid($user_id, $year = NULL) {
        $student = $this->get_registrar_model->getSingleStudentByRfid($user_id, $year);
        return $student;
    }

    function getTotalStudents() {
        $students = $this->get_registrar_model->getAllStudents('', '', Null, Null);
        return $students;
    }

    function getAllStudents() {
        $this->load->helper('file');
        if ($this->session->userdata('position_id') != 4):
            $result = $this->get_registrar_model->getAllStudents('', '', Null, Null);
            $config['base_url'] = base_url('registrar/getAllStudents');
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

            $page = $this->get_registrar_model->getAllStudents($config['per_page'], $this->uri->segment(3), Null, Null);
            $data['students'] = $page->result();
            $data['num_of_students'] = $result->num_rows();
            $data['grade'] = $this->getGradeLevel();
            $data['section'] = $this->getAllSection();
            $data['ro_year'] = $this->getROYear();
            if (Modules::run('main/isMobile')):
                if ($this->session->userdata('position_id') == 39):

                    redirect(base_url() . 'registrar/getAllStudentsBySection/' . $this->session->userdata('advisory'), $this->session->userdata('school_year'));
                endif;
            else:
                if ($this->session->userdata('position_id') == 39):

                    redirect(base_url() . 'registrar/getAllStudentsBySection/' . $this->session->userdata('advisory'), $this->session->userdata('school_year'));
                else:
                    $data['main_content'] = 'roster';
                    $data['modules'] = 'registrar';
                    echo Modules::run('templates/main_content', $data);
                endif;

            endif;

        else:
            $data['students'] = $this->get_registrar_model->getStudentListForParent($this->session->userdata('parent_id'));
            if (Modules::run('main/isMobile')):
                $this->load->view('mobile/parentRoster', $data);
            else:
                redirect('pp/students');

            endif;

        endif;
    }

    function getAllStudentsBySection($section_id = NULL, $year = NULL) {

        if ($this->session->userdata('position_id') != 4):
            $result = $this->get_registrar_model->getAllStudents('', '', Null, $section_id, $year);
            $config['base_url'] = base_url('registrar/getAllStudentsBySection/' . $section_id . '/' . $year);
            $config['total_rows'] = $result->num_rows();
            $config['per_page'] = 70;
            $config["uri_segment"] = 5;
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

            $page = $this->get_registrar_model->getAllStudents($config['per_page'], $this->uri->segment(5), Null, $section_id, $year);

            $data['links'] = $this->pagination->create_links();
            $data['num_of_students'] = $result->num_rows();
            $data['students'] = $page->result();
            $data['grade'] = $this->getGradeLevel();
            $data['section_id'] = $section_id;
            $data['year'] = $year;
            $data['section'] = $this->getAllSection();
            $data['ro_year'] = $this->getROYear();


            if (Modules::run('main/isMobile')):
                $data['modules'] = "registrar";
                $data['main_content'] = 'mobile/studentListForAdvisers';
                echo Modules::run('mobile/main_content', $data);
            else:
                $data['main_content'] = 'roster';
                $data['modules'] = 'registrar';
                echo Modules::run('templates/main_content', $data);
            endif;

        else:

            if (!Modules::run('main/isMobile')):
                $data['students'] = $this->get_registrar_model->getStudentListForParent($this->session->userdata('parent_id'));
                $data['main_content'] = 'parentRoster';
                $data['modules'] = 'registrar';
                echo Modules::run('templates/main_content', $data);

            else:
                $data['students'] = $this->get_registrar_model->getStudentListForParent($this->session->userdata('parent_id'));
                $data['modules'] = "registrar";
                $data['main_content'] = 'mobile/parentRoster';
                echo Modules::run('mobile/main_content', $data);

            endif;

        endif;
    }

    function getStudentListForParent() {
        $students = $this->get_registrar_model->getStudentListForParent($this->session->userdata('parent_id'));
        return $students;
    }

    function getAllStudentsForExternal($grade_id = null, $section_id = null, $gender = null, $status = NULL, $year = NULL) {
        if ($year == NULL):
            $settings = Modules::run('main/getSet');
            $year = $settings->school_year;
        endif;
        $result = $this->get_registrar_model->getStudents($grade_id, $section_id, $gender, $status, $year);
        return $result;
    }

    function getAllStudentsByGradeLevel($grade_id = null) {
        $result = $this->getAllStudentsForExternal($grade_id, NULL, NULL, '', $this->session->userdata('school_year'));
        $config['base_url'] = base_url('registrar/getAllStudentsByGradeLevel/' . $grade_id);
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 10;
        $config["uri_segment"] = 4;
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

        $page = $this->get_registrar_model->getAllStudents($config['per_page'], $this->uri->segment(4), $grade_id, Null);
        $data['links'] = $this->pagination->create_links();
        $data['students'] = $page->result();
        $data['num_of_students'] = $result->num_rows();
        $data['grade'] = $this->getGradeLevel();
        $data['section'] = $this->getAllSection();
        $data['ro_year'] = $this->getROYear();
        $data['main_content'] = 'roster';
        $data['modules'] = 'registrar';
        $data['grade_id'] = $grade_id;
        echo Modules::run('templates/main_content', $data);
    }

    function getStudentsByGradeLevel($grade_id = null, $section_id = null, $gender = null, $status = NULL, $school_year = NULL) {
        $students = $this->get_registrar_model->getStudents($grade_id, $section_id, $gender, $status, $school_year);
        //print_r($students->num_rows);
        return $students;
    }

    function getStudents_w_o_section($grade_id, $section_id, $year) {
        $students = $this->get_registrar_model->getAllStudentsByLevel($grade_id, $section_id, $year);
        $i = 1;
        foreach ($students->result() as $s):
            echo $i++ . ' ' . $s->lastname . ', ' . $s->firstname . '<br />';
        endforeach;
    }

    function getNumberOfStudentsPerMonth($gender, $month = NULL) {
        $students = $this->get_registrar_model->getNumberOfStudentsPerMonth($month, $gender);
        return $students;
    }

    function getAllStudentsByGender($section_id = null, $gender = null, $status = NULL, $year = NULL) {
        $result = $this->get_registrar_model->getAllStudentsByGender($section_id, $gender, $status, $year);
        return $result;
        //echo $result->num_rows();
    }

    function getAllStudentsByGenderForAttendance($section_id = null, $gender = null, $status) {
        $result = $this->get_registrar_model->getAllStudentsByGenderForAttendance($section_id, $gender, $status);
        return $result;
        //echo $result->num_rows();
    }

    function getAllStudentsInNormalView() {
        $students = $this->get_registrar_model->getStudentListForParent($this->session->userdata('parent_id'));
        return $students;
    }

    function viewDetails($id, $year = NULL) {
        $id = base64_decode($id);
        $students = $this->get_registrar_model->getSingleStudent($id, $year);
        if ($students->u_id == ""):
            $user_id = $students->us_id;
        else:
            $user_id = $students->u_id;
        endif;
        if ($this->session->userdata('position') == 'Parent'):
            $data['editable'] = 'hide';
        else:
            $data['editable'] = '';
        endif;

        $data['ro_year'] = $this->getROYear();
        $data['students'] = $this->get_registrar_model->getSingleStudent($id, $year);
        $data['m'] = $this->get_registrar_model->getMother($user_id);
        $data['f'] = $this->get_registrar_model->getFather($user_id);
        $data['medical'] = $this->get_registrar_model->getMedical($id);
        $data['date_admitted'] = $this->get_registrar_model->getDateEnrolled($students->u_id);
        $data['option'] = 'individual';
        $data['religion'] = Modules::run('main/getReligion');
        $data['motherTongue'] = Modules::run('main/getMotherTongue');
        $data['ethnicGroup'] = Modules::run('main/getEthnicGroup');
        $data['st_id'] = $id;
        $data['educ_attain'] = $this->registrar_model->getEducAttain();

        if (Modules::run('main/isMobile')):
            $this->load->view('mobile/individualRecords', $data);
        else:
            $data['modules'] = 'registrar';
            $data['main_content'] = 'studentInfo';
            echo Modules::run('templates/main_content', $data);
        endif;
    }

    function getLatestIdNum($level_id) {
        $id = $this->registrar_model->getLatestIDs();
        $ids = json_decode($id);
        $deptCode = $this->registrar_model->getDeptCode($level_id);
        if ($ids->num_rows == 0):
            $last_three = '000';
        else:
            $last_three = substr($ids->generated_id, -4);
        endif;

        $temp_id = $this->registrar_model->generateTempId($ids->generated_id, abs($last_three));

        echo json_encode(array('status' => TRUE, 'id' => $temp_id, 'deptCode' => $deptCode->deptCode));
    }

    function importLatestId() {
        $id = $this->registrar_model->importLatestId();
        return $id;
    }

    function getLatestIdNums($level_id) {
        $deptCode = $this->registrar_model->getDeptCode($level_id);
        $id = $this->registrar_model->getLatestID($deptCode->deptCode);
        echo json_encode(array('status' => TRUE, 'id' => $id->user_id, 'deptCode' => $deptCode->deptCode));
    }

    function admission() {
        if (!$this->session->userdata('is_logged_in')) {
            ?>
            <script type="text/javascript">
                document.location = "<?php echo base_url() ?>"
            </script>
            <?php
        } else {
            $data['settings'] = Modules::run('main/getSet');
            if ($data['settings']->level_catered == 5):
                $data['course'] = Modules::run('coursemanagement/getCourses');
            endif;
            $data['ro_year'] = $this->getROYear();
            $data['cities'] = Modules::run('main/getCities');
            $data['provinces'] = Modules::run('main/getProvinces');
            $data['religion'] = Modules::run('main/getReligion');
            $data['motherTongue'] = Modules::run('main/getMotherTongue');
            $data['ethnicGroup'] = Modules::run('main/getEthnicGroup');
            $data['grade'] = $this->registrar_model->getGradeLevel();
            $data['physician'] = $this->registrar_model->getPhysician();
            $data['educ_attain'] = $this->registrar_model->getEducAttain();
            $data['modules'] = "registrar";
            if (file_exists(APPPATH . 'modules/registrar/views/' . strtolower($data['settings']->short_name) . '_admission.php')):
                $data['main_content'] = strtolower($data['settings']->short_name) . '_admission';
            else:
                $data['main_content'] = 'admission';
            endif;
            echo Modules::run('templates/main_content', $data);
        }
    }

    function admission1() {
        if (!$this->session->userdata('is_logged_in')) {
            ?>
            <script type="text/javascript">
                document.location = "<?php echo base_url() ?>"
            </script>
            <?php
        } else {
            $data['ro_year'] = $this->getROYear();
            $data['cities'] = Modules::run('main/getCities');
            $data['provinces'] = Modules::run('main/getProvinces');
            $data['religion'] = Modules::run('main/getReligion');
            $data['motherTongue'] = Modules::run('main/getMotherTongue');
            $data['ethnicGroup'] = Modules::run('main/getEthnicGroup');
            $data['grade'] = $this->registrar_model->getGradeLevel();
            $data['physician'] = $this->registrar_model->getPhysician();
            $data['settings'] = Modules::run('main/getSet');
            $data['educ_attain'] = $this->registrar_model->getEducAttain();
            $data['modules'] = "registrar";
            $data['main_content'] = 'admission';
            echo Modules::run('templates/main_content', $data);
        }
    }

    function getGradeLevel($dept = NULL, $option = NULL) {
        $gradeLevel = $this->registrar_model->getGradeLevel($dept, $option);
        return $gradeLevel;
    }

    function getGradeLevelByLevelCode($grade_id) {
        $grade_id = $this->registrar_model->getGradeLevelByLevelCode($grade_id);
        return $grade_id;
    }

    function getGradeLevelBySectionId($section_id) {
        $grade_id = $this->registrar_model->getGradeLevelBySectionId($section_id);
        return $grade_id;
    }

    function getGradeLevelById($grade_id) {
        $grade_id = $this->registrar_model->getGradeLevelById($grade_id);
        return $grade_id;
    }

    function getSectionBySubject($grade_id) {
        $section = $this->registrar_model->getSectionByLevel($grade_id);
        return $section->row();
    }

    function saveBasicInformation($details, $lastname, $firstname, $middlename) {
        $result = $this->registrar_model->saveBasicInformation($details, $lastname, $firstname, $middlename);
        $response = json_decode($result);

        return $response;
    }

//    public function saveAdmission() {
//        if ($this->input->post('inputLRN') == 0) {
//            $st_id = $this->input->post('inputIdNum');
//        } else {
//            $st_id = $this->input->post('inputLRN');
//        }
//        //$st_id = $this->input->post('inputIdNum');
//        $grade_level = $this->input->post('inputGrade');
//        $section = $this->input->post('inputSection');
//        $motherTongue = $this->input->post('addMotherTongue');
//        $processAdmission = Modules::load('registrar/registrardbprocess/');
//
//        $school_year = $this->input->post('inputSY');
//        $enDate = $this->input->post('inputEdate');
//        $items = array(
//            'lastname' => $this->input->post('inputLastName'),
//            'firstname' => $this->input->post('inputFirstName'),
//            'middlename' => $this->input->post('inputMiddleName'),
//            'add_id' => 0,
//            'sex' => $this->input->post('inputGender'),
//            'rel_id' => $this->input->post('inputReligion'),
//            'bdate_id' => 0,
//            'contact_id' => 0,
//            'status' => 0,
//            'nationality' => $this->input->post('inputNationality'),
//            'bplace_id' => $this->input->post('inputPlaceOfBirth'),
//            'account_type' => 5,
//            'ethnic_group_id' => $this->input->post('addEthnicGroup'),
//            'occupation_id' => 1
//        );
//        $profile_id = $this->registrar_model->saveProfile($items, NULL, $school_year);
//
//
//        //saves the basic info
//
//        $enroll_date = $this->registrar_model->saveEdate($enDate);
//
//        $idExist = $this->registrar_model->checkIdIfExist($st_id);
//        if ($idExist):
//            $st_id = $st_id + 1;
//        endif;
//        $processAdmission->setStudInfo($st_id, $profile_id, $section, $grade_level, $motherTongue, $enroll_date, $school_year, $this->input->post('inputSLA'), $this->input->post('inputAddressSLA'));
//
//        $barangay_id = $processAdmission->setBarangay($this->input->post('inputBarangay'), $school_year);
//
//        $add = array(
//            'address_id' => $profile_id,
//            'street' => $this->input->post('inputStreet'),
//            'barangay_id' => $barangay_id,
//            'city_id' => $this->input->post('inputMunCity'),
//            'province_id' => $this->input->post('inputPID'),
//            'country' => 'Philippines',
//            'zip_code' => $this->input->post('inputPostal'),
//        );
//
//        $address_id = $processAdmission->setAddress($add, $profile_id, $school_year);
//
//        //saves the basic contact
//        $contact_id = $processAdmission->setContacts($this->input->post('inputPhone'), $this->input->post('inputEmail'), $profile_id, $school_year);
//
//        //saves the birthday
//        $date = $this->input->post('inputBdate');
//        $this->registrar_model->setDate($date, $profile_id, 'bdate_id', $school_year);
//
//        // Save Parents details
//        $parent_id = $processAdmission->saveParentsPro($profile_id, $school_year);
////        $PExist = $this->input->post('inputFExist');
////
////       if($PExist>0){ // If Parent Already Exist
////           $this->registrar_model->updateParentPro($PExist, $st_id);
////           echo 'parent exist';
////       }else{
//
//        $pgSelect = $this->input->post('pgSelect');
//        if ($pgSelect == 1) {
//            $guardian = array(
//                'lastname' => $this->input->post('inputGLName'),
//                'firstname' => $this->input->post('inputGFName'),
//                'middlename' => $this->input->post('inputGMName'),
//                'add_id' => $address_id,
//                'sex' => $this->input->post('inputGuardGender'),
//                'nationality' => $this->input->post('inputNationality'),
//                'account_type' => 4,
//            );
//
//            $guardian_id = $processAdmission->saveProfile($guardian, $school_year);
//
//            //$processAdmission->setParentsPro($profile_id, $guardian_id,0, 1, $this->input->post('relationship')); 
//
//            $processAdmission->setContacts($this->input->post('inputG_num'), $this->input->post('inputGEmail'), $guardian_id, $school_year);
//
//            $processAdmission->chooseOcc($this->input->post('inputG_occ'), $guardian_id);
//
//            $processAdmission->chooseEduc($this->input->post('inputGeduc'), $guardian_id);
//
//
//
//            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $guardian_id, 'create', 2);
//        }
////        else
////        {
//        $father = array(
//            'lastname' => $this->input->post('inputFLName'),
//            'firstname' => $this->input->post('inputFName'),
//            'middlename' => $this->input->post('inputFMName'),
//            'add_id' => $address_id,
//            'sex' => 'Male',
//            'nationality' => $this->input->post('inputNationality'),
//            'account_type' => 4,
//        );
//
//        $father_id = $this->registrar_model->saveProfile($father, NULL, $school_year);
//
//        $processAdmission->setContacts($this->input->post('inputF_num'), $this->input->post('inputPEmail'), $father_id, $school_year);
//
//        $processAdmission->chooseOcc($this->input->post('inputF_occ'), $father_id);
//
//        $processAdmission->chooseEduc($this->input->post('inputFeduc'), $father_id);
//
//        //saves the Father's office address
//        if ($this->input->post('f_officeBarangay') != ""):
//            $fbarangay_id = $processAdmission->setBarangay($this->input->post('f_officeBarangay'), $school_year);
//        else:
//            $fbarangay_id = 0;
//        endif;
//
//        $faddOffice = array(
//            'street' => $this->input->post('f_officeStreet'),
//            'barangay_id' => $fbarangay_id,
//            'city_id' => $this->input->post('f_officeMunCity'),
//            'province_id' => $this->input->post('f_officePID'),
//            'country' => 'Philippines',
//            'zip_code' => '',
//        );
//
//        $fOfficeAddress_id = $processAdmission->setOfficeAddress($faddOffice, $school_year);
//
//        $processAdmission->updateParentsPro($parent_id, $father_id, $this->input->post('f_officeName'), $fOfficeAddress_id, 'f');
//        $mother = array(
//            'lastname' => $this->input->post('inputMLName'),
//            'firstname' => $this->input->post('inputMother'),
//            'middlename' => $this->input->post('inputMMName'),
//            'add_id' => $address_id,
//            'sex' => 'Female',
//            'nationality' => $this->input->post('inputNationality'),
//            'account_type' => 4,
//        );
//
//        $mother_id = $this->registrar_model->saveProfile($mother, NULL, $school_year);
//        $processAdmission->setContacts($this->input->post('inputM_num'), $this->input->post('inputPEmail'), $mother_id, $school_year);
//
//        $processAdmission->chooseOcc($this->input->post('inputM_occ'), $mother_id);
//
//        $processAdmission->chooseEduc($this->input->post('inputMeduc'), $mother_id);
//
//        //saves the Mother's office address
//        if ($this->input->post('m_officeBarangay') != ""):
//            $mbarangay_id = $processAdmission->setBarangay($this->input->post('m_officeBarangay'), $school_year);
//        else:
//            $mbarangay_id = 0;
//        endif;
//
//        $maddOffice = array(
//            'street' => $this->input->post('m_officeStreet'),
//            'barangay_id' => $mbarangay_id,
//            'city_id' => $this->input->post('m_officeMunCity'),
//            'province_id' => $this->input->post('m_officePID'),
//            'country' => 'Philippines',
//            'zip_code' => '',
//        );
//
//        $mOfficeAddress_id = $processAdmission->setOfficeAddress($maddOffice, $school_year);
//        $processAdmission->updateParentsPro($parent_id, $mother_id, $this->input->post('m_officeName'), $mOfficeAddress_id, 'm');
//        //$processAdmission->setParentsPro($profile_id, $father_id, $mother_id, $this->input->post('f_officeName'), $fOfficeAddress_id, $this->input->post('f_officeName'), $mOfficeAddress_id,  $guardian_id, $this->input->post('relationship'));
//
//
//        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $father_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $mother_id, 'create', 2);
//
//        //}
//        //}
//        //Medical information
//        $this->registrar_model->saveMed($this->input->post('inputBType'), $this->input->post('inputAllergies'), $this->input->post('inputOtherMedInfo'), $this->input->post('inputFPhy'), $this->input->post('height'), $this->input->post('weight'), $profile_id);
//
//        Modules::run('web_sync/updateSyncController', 'calendar', 'cal_id', $profile_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile_students', 'user_id', $profile_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $address_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $fOfficeAddress_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $mOfficeAddress_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile_contact_details', 'contact_id', $contact_id, 'create', 2);
//
//        Modules::run('web_sync/updateSyncController', 'profile_medical', 'user_id', $profile_id, 'create', 2);
//        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $profile_id, 'create', 2);
//        return;
//    }
    
    public function saveAdmission() {
        if ($this->input->post('inputLRN') == 0) {
            $st_id = $this->input->post('inputIdNum');
        } else {
            $st_id = $this->input->post('inputLRN');
        }
        //$st_id = $this->input->post('inputIdNum');
        $grade_level = $this->input->post('inputGrade');
        $section = $this->input->post('inputSection');
        $motherTongue = $this->input->post('addMotherTongue');
        $processAdmission = Modules::load('registrar/registrardbprocess/');

        $school_year = $this->input->post('inputSY');
        $enDate = $this->input->post('inputEdate');
        $items = array(
            'lastname' => $this->input->post('inputLastName'),
            'firstname' => $this->input->post('inputFirstName'),
            'middlename' => $this->input->post('inputMiddleName'),
            'add_id' => 0,
            'sex' => $this->input->post('inputGender'),
            'rel_id' => $this->input->post('inputReligion'),
            'bdate_id' => $this->input->post('inputBdate'),
            'contact_id' => 0,
            'status' => 0,
            'nationality' => $this->input->post('inputNationality'),
            'bplace_id' => $this->input->post('inputPlaceOfBirth'),
            'account_type' => 5,
            'ethnic_group_id' => $this->input->post('addEthnicGroup'),
            'occupation_id' => 1
        );
        $profile_id = $this->registrar_model->saveProfile($items, NULL, $school_year);


        //saves the basic info

        $enroll_date = $this->registrar_model->saveEdate($enDate);

        $idExist = $this->registrar_model->checkIdIfExist($st_id);
        if ($idExist):
            $st_id = $st_id + 1;
        endif;
        $processAdmission->setStudInfo($st_id, $profile_id, $section, $grade_level, $motherTongue, $enroll_date, $school_year, $this->input->post('inputSLA'), $this->input->post('inputAddressSLA'));

        $barangay_id = $processAdmission->setBarangay($this->input->post('inputBarangay'), $school_year);

        $add = array(
            'address_id' => $profile_id,
            'street' => $this->input->post('inputStreet'),
            'barangay_id' => $barangay_id,
            'city_id' => $this->input->post('inputMunCity'),
            'province_id' => $this->input->post('inputPID'),
            'country' => 'Philippines',
            'zip_code' => $this->input->post('inputPostal'),
        );

        $address_id = $processAdmission->setAddress($add, $profile_id, $school_year);

        //saves the basic contact
        //$contact_id = $processAdmission->setContacts($this->input->post('inputPhone'), $this->input->post('inputEmail'), $profile_id, $school_year);
        $processAdmission->setContacts($this->input->post('inputPhone'), $this->input->post('inputEmail'), $profile_id, $school_year);

        $this->get_registrar_model->updateContact($profile_id);

        //saves the birthday
//        $date = $this->input->post('inputBdate');
//        $this->registrar_model->setDate($date, $profile_id, 'bdate_id', $school_year);
//
//        // Save Parents details
//        $parent_id = $processAdmission->saveParentsPro($profile_id, $school_year);
//        $PExist = $this->input->post('inputFExist');
//
//       if($PExist>0){ // If Parent Already Exist
//           $this->registrar_model->updateParentPro($PExist, $st_id);
//           echo 'parent exist';
//       }else{

        $pgSelect = $this->input->post('pgSelect');
        if ($pgSelect == 1) {
            $guardian = array(
                'lastname' => $this->input->post('inputGLName'),
                'firstname' => $this->input->post('inputGFName'),
                'middlename' => $this->input->post('inputGMName'),
                'add_id' => $address_id,
                'sex' => $this->input->post('inputGuardGender'),
                'nationality' => $this->input->post('inputNationality'),
                'account_type' => 4,
            );

            $guardian_id = $processAdmission->saveProfile($guardian, $school_year);

            //$processAdmission->setParentsPro($profile_id, $guardian_id,0, 1, $this->input->post('relationship')); 

            $processAdmission->setContacts($this->input->post('inputG_num'), $this->input->post('inputGEmail'), $guardian_id, $school_year);

            $processAdmission->chooseOcc($this->input->post('inputG_occ'), $guardian_id);

            $processAdmission->chooseEduc($this->input->post('inputGeduc'), $guardian_id);



            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $guardian_id, 'create', 2);
        }
//        else
//        {
        $father = array(
            'u_id' => $profile_id,
            'f_lastname' => $this->input->post('inputFLName'),
            'f_firstname' => $this->input->post('inputFName'),
            'f_middlename' => $this->input->post('inputFMName'),
            'f_add_id' => $address_id,
            'f_mobile' => $this->input->post('inputF_num'),
            'f_occ' => $this->input->post('inputF_occ'),
            'f_educ' => $this->input->post('inputFeduc')
        );

        $this->registrar_model->saveProfileParent($father, $profile_id, $school_year);
        //$this->get_registrar_model->updateContact($father_id);

//        $processAdmission->setContacts($this->input->post('inputF_num'), $this->input->post('inputPEmail'), $father_id, $school_year);
//
//        $processAdmission->chooseOcc($this->input->post('inputF_occ'), $father_id);
//
//        $processAdmission->chooseEduc($this->input->post('inputFeduc'), $father_id);

        //saves the Father's office address
        if ($this->input->post('f_officeBarangay') != ""):
            $fbarangay_id = $processAdmission->setBarangay($this->input->post('f_officeBarangay'), $school_year);
        else:
            $fbarangay_id = 0;
        endif;

        $faddOffice = array(
            'street' => $this->input->post('f_officeStreet'),
            'barangay_id' => $fbarangay_id,
            'city_id' => $this->input->post('f_officeMunCity'),
            'province_id' => $this->input->post('f_officePID'),
            'country' => 'Philippines',
            'zip_code' => '',
        );

        $fOfficeAddress_id = $processAdmission->setOfficeAddress($faddOffice, $school_year);

        $processAdmission->updateParentsPro($parent_id, $father_id, $this->input->post('f_officeName'), $fOfficeAddress_id, 'f');
        $mother = array(
            'u_id' => $profile_id,
            'm_lastname' => $this->input->post('inputMLName'),
            'm_firstname' => $this->input->post('inputMother'),
            'm_middlename' => $this->input->post('inputMMName'),
            'm_add_id' => $address_id,
            'm_mobile' => $this->input->post('inputM_num'),
            'm_occ' => $this->input->post('inputM_occ'),
            'm_educ' => $this->input->post('inputMeduc')
        );

        $this->registrar_model->saveProfileParent($mother, $profile_id, $school_year);

//        $this->get_registrar_model->updateContact($mother_id);
//
//        $processAdmission->setContacts($this->input->post('inputM_num'), $this->input->post('inputPEmail'), $mother_id, $school_year);
//
//        $processAdmission->chooseOcc($this->input->post('inputM_occ'), $mother_id);
//
//        $processAdmission->chooseEduc($this->input->post('inputMeduc'), $mother_id);

        //saves the Mother's office address
        if ($this->input->post('m_officeBarangay') != ""):
            $mbarangay_id = $processAdmission->setBarangay($this->input->post('m_officeBarangay'), $school_year);
        else:
            $mbarangay_id = 0;
        endif;

        $maddOffice = array(
            'street' => $this->input->post('m_officeStreet'),
            'barangay_id' => $mbarangay_id,
            'city_id' => $this->input->post('m_officeMunCity'),
            'province_id' => $this->input->post('m_officePID'),
            'country' => 'Philippines',
            'zip_code' => '',
        );

        $mOfficeAddress_id = $processAdmission->setOfficeAddress($maddOffice, $school_year);
        $processAdmission->updateParentsPro($parent_id, $mother_id, $this->input->post('m_officeName'), $mOfficeAddress_id, 'm');
        //$processAdmission->setParentsPro($profile_id, $father_id, $mother_id, $this->input->post('f_officeName'), $fOfficeAddress_id, $this->input->post('f_officeName'), $mOfficeAddress_id,  $guardian_id, $this->input->post('relationship'));


        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $father_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $mother_id, 'create', 2);

        //}
        //}
        //Medical information
        $this->registrar_model->saveMed($this->input->post('inputBType'), $this->input->post('inputAllergies'), $this->input->post('inputOtherMedInfo'), $this->input->post('inputFPhy'), $this->input->post('height'), $this->input->post('weight'), $profile_id);

        Modules::run('web_sync/updateSyncController', 'calendar', 'cal_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_students', 'user_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $address_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $fOfficeAddress_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $mOfficeAddress_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_contact_details', 'contact_id', $profile_id, 'create', 2);

        Modules::run('web_sync/updateSyncController', 'profile_medical', 'user_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $profile_id, 'create', 2);
        return;
    }

    public function saveAdmission1() {
        if ($this->input->post('inputLRN') == 0) {
            $st_id = $this->input->post('inputIdNum');
        } else {
            $st_id = $this->input->post('inputLRN');
        }
        $enDate = $this->input->post('inputEdate');
        $grade_level = $this->input->post('inputGrade');
        $section = $this->input->post('inputSection');

        $school_year = $this->input->post('inputSY');

        $motherTongue = $this->input->post('addMotherTongue');
        $processAdmission = Modules::load('registrar/registrardbprocess/');
        $items = array(
            'lastname' => $this->input->post('inputLastName'),
            'firstname' => $this->input->post('inputFirstName'),
            'middlename' => $this->input->post('inputMiddleName'),
            'add_id' => 0,
            'sex' => $this->input->post('inputGender'),
            'rel_id' => $this->input->post('inputReligion'),
            'bdate_id' => 0,
            'contact_id' => 0,
            'status' => 0,
            'nationality' => $this->input->post('inputNationality'),
            'account_type' => 5,
            'ethnic_group_id' => $this->input->post('addEthnicGroup'),
            'occupation_id' => 1
        );

        //saves the basic info
        $profile_id = $processAdmission->saveProfile($items);

        $enroll_date = $this->registrar_model->saveEdate($enDate);


        //saves the detail info
        $processAdmission->setStudInfo($st_id, $profile_id, $section, $grade_level, $motherTongue, $enroll_date, $school_year, $this->input->post('inputSLA'), $this->input->post('inputAddressSLA'));


        //saves the address

        $barangay_id = $processAdmission->setBarangay($this->input->post('inputBarangay'));

        $add = array(
            'street' => $this->input->post('inputStreet'),
            'barangay_id' => $barangay_id,
            'city_id' => $this->input->post('inputMunCity'),
            'province_id' => $this->input->post('inputPID'),
            'country' => 'Philippines',
            'zip_code' => $this->input->post('inputPostal'),
        );

        $address_id = $processAdmission->setAddress($add, $profile_id);


        //saves the basic contact
        $contact_id = $processAdmission->setContacts($this->input->post('inputPhone'), $this->input->post('inputEmail'), $profile_id);

        //saves the birthday
        $date = $this->input->post('inputBdate');
        $this->registrar_model->setDate($date, $profile_id, 'bdate_id');

        $parent_id = $processAdmission->saveParentsPro();
        // Save Parents details
        $PExist = $this->input->post('inputFExist');

        if ($PExist > 0) { // If Parent Already Exist
            $this->registrar_model->updateParentPro($PExist, $st_id);
            echo 'parent exist';
        } else {

            $pgSelect = $this->input->post('pgSelect');
            // if($pgSelect==1){
            $guardian = array(
                'lastname' => $this->input->post('inputGLName'),
                'firstname' => $this->input->post('inputGFName'),
                'middlename' => $this->input->post('inputGMName'),
                'add_id' => $address_id,
                'sex' => $this->input->post('inputGuardGender'),
                'nationality' => $this->input->post('inputNationality'),
                'account_type' => 4,
            );

            $guardian_id = $processAdmission->saveProfile($guardian);

            //$processAdmission->setParentsPro($profile_id, $guardian_id,0, 1, $this->input->post('relationship')); 

            $processAdmission->setContacts($this->input->post('inputG_num'), $this->input->post('inputGEmail'), $guardian_id);

            $processAdmission->chooseOcc($this->input->post('inputG_occ'), $guardian_id);

            $processAdmission->chooseEduc($this->input->post('inputGeduc'), $guardian_id);



            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $guardian_id, 'create', 2);

//        }
//        else
//        {
            $father = array(
                'lastname' => $this->input->post('inputFLName'),
                'firstname' => $this->input->post('inputFName'),
                'middlename' => $this->input->post('inputFMName'),
                'add_id' => $address_id,
                'sex' => 'Male',
                'nationality' => $this->input->post('inputNationality'),
                'account_type' => 4,
            );

            $father_id = $processAdmission->saveProfile($father);

            $processAdmission->setContacts($this->input->post('inputF_num'), $this->input->post('inputPEmail'), $father_id);

            $processAdmission->chooseOcc($this->input->post('inputF_occ'), $father_id);

            $processAdmission->chooseEduc($this->input->post('inputFeduc'), $father_id);

            //saves the Father's office address

            $fbarangay_id = $processAdmission->setBarangay($this->input->post('f_officeBarangay'));

            $faddOffice = array(
                'street' => $this->input->post('f_officeStreet'),
                'barangay_id' => $fbarangay_id,
                'city_id' => $this->input->post('f_officeMunCity'),
                'province_id' => $this->input->post('f_officePID'),
                'country' => 'Philippines',
                'zip_code' => '',
            );

            $fOfficeAddress_id = $processAdmission->setOfficeAddress($faddOffice);


            $mother = array(
                'lastname' => $this->input->post('inputMLName'),
                'firstname' => $this->input->post('inputMother'),
                'middlename' => $this->input->post('inputMMName'),
                'add_id' => $address_id,
                'sex' => 'Female',
                'nationality' => $this->input->post('inputNationality'),
                'account_type' => 4,
            );

            $mother_id = $processAdmission->saveProfile($mother);



            $processAdmission->setContacts($this->input->post('inputM_num'), $this->input->post('inputPEmail'), $mother_id);

            $processAdmission->chooseOcc($this->input->post('inputM_occ'), $mother_id);

            $processAdmission->chooseEduc($this->input->post('inputMeduc'), $mother_id);

            //saves the Mother's office address

            $mbarangay_id = $processAdmission->setBarangay($this->input->post('m_officeBarangay'));

            $maddOffice = array(
                'street' => $this->input->post('m_officeStreet'),
                'barangay_id' => $mbarangay_id,
                'city_id' => $this->input->post('m_officeMunCity'),
                'province_id' => $this->input->post('m_officePID'),
                'country' => 'Philippines',
                'zip_code' => '',
            );

            $mOfficeAddress_id = $processAdmission->setOfficeAddress($maddOffice);

            //$processAdmission->setParentsPro($profile_id, $father_id, $mother_id, $this->input->post('f_officeName'), $fOfficeAddress_id, $this->input->post('f_officeName'), $mOfficeAddress_id,  $guardian_id, $this->input->post('relationship'));


            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $father_id, 'create', 2);
            Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $mother_id, 'create', 2);

            //}
        }
        //Medical information
        $this->registrar_model->saveMed($this->input->post('inputBType'), $this->input->post('inputAllergies'), $this->input->post('inputOtherMedInfo'), $this->input->post('inputFPhy'), $this->input->post('height'), $this->input->post('weight'), $profile_id);

        Modules::run('web_sync/updateSyncController', 'calendar', 'cal_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_students', 'user_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $address_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $fOfficeAddress_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_address_info', 'address_id', $mOfficeAddress_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile_contact_details', 'contact_id', $contact_id, 'create', 2);

        Modules::run('web_sync/updateSyncController', 'profile_medical', 'user_id', $profile_id, 'create', 2);
        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $profile_id, 'create', 2);
        ?>
        <script type="text/javascript">
            alert("Student Information Saved <?php echo $parent_id ?>")
            var answer = confirm("do you want to admit more students?")

            if (answer == true) {

                document.location = "<?php echo base_url(); ?>registrar/admission"
            } else {

                document.location = "<?php echo base_url(); ?>main/dashboard"
            }
        </script>         
        <?php
    }

    public function setBirthdate($date, $id, $column) {
        $this->registrar_model->setDate($date, $id, $column);
    }

    function getSectionByGL($section) {
        $section = $this->registrar_model->getSectionByLevel($section);
        ?>
        <option value="0">Select Section</option>
        <option value="0">TBA</option>
        <?php
        foreach ($section->result() as $row) {
            ?>                                
            <option sec="<?php echo $row->section ?>" value="<?php echo $row->s_id ?>"><?php echo $row->section ?></option>
            <?php
        }
    }

    function saveSectionValue() {
        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $value = $this->input->post('value');
        $grade_id = $this->input->post('pk');


        $nValue = array(
            $column => $value,
            'grade_level_id' => $grade_id
        );

        $this->registrar_model->saveNewValue($table, $nValue);
        $newValue = $this->getSectionByGradeId($grade_id);
        //echo $module;
        //print_r($newValue);
        foreach ($newValue->result() as $row) {
            ?>
            <li><?php echo $row->section ?></li>
            <?php
        }
    }

    function getSectionByGradeId($grade_id, $sy = NULL) {
        $section = $this->registrar_model->getSectionByLevel($grade_id, $sy);
        return $section;
    }

    function getAllSection($section_id = NULL, $option = NULL) {
        $section = $this->registrar_model->getAllSection($section_id, $option);
        return $section;
    }

    public function enrollmentListing() {
        if (!$this->session->userdata('is_logged_in')) {
            ?>
            <script type="text/javascript">
                document.location = "<?php echo base_url() ?>"
            </script>
            <?php
        } else {
            $data['main_content'] = 'enrollmentList';
            $data['grade'] = $this->getGradeLevel();
            $data['modules'] = 'registrar';
            echo Modules::run('templates/main_content', $data);
        }
    }

    public function getLateEnrolleesByGender($sex) {
        $lateEnrollee = $this->get_registrar_model->getLateEnrolleesByGender($sex);
        return $lateEnrollee;
    }

    public function getStudentStatus($status, $sex, $section_id = NULL, $month = Null, $year = NULL, $option = NULL, $grade_level = NULL) {
        $studentStatus = $this->get_registrar_model->getStudentStatus($status, $sex, $month, $section_id, $year, $option, $grade_level);
        //echo $studentStatus->num_rows();
        return $studentStatus;
    }

    public function editAddressInfo() {
        //$processAdmission = Modules::load('registrar/registrardbprocess/');
        $address_id = $this->input->post('address_id');
        $street = $this->input->post('street');
        $barangay = $this->input->post('barangay');
        $city = $this->input->post('city');
        $province = $this->input->post('province');
        $zip_code = $this->input->post('zip_code');
        $user_id = $this->input->post('user_id');
        $sy = $this->input->post('sy');

        $barangay_id = $this->processAdmission->setBarangay($barangay, $sy);

        $add = array(
            'street' => $street,
            'barangay_id' => $barangay_id,
            'city_id' => $city,
            'province_id' => $province,
            'country' => 'Philippines',
            'zip_code' => $zip_code,
        );

        $updateBasicInfo = $this->registrar_model->editBasicInfo($add, $address_id, 'profile_address_info', 'address_id', $user_id, $sy);

        echo $updateBasicInfo->street . ', ' . $updateBasicInfo->barangay . ', ' . $updateBasicInfo->mun_city . ' ' . $updateBasicInfo->province . ', ' . $updateBasicInfo->zip_code;
    }

    public function editBasicInfo() {
        $processAdmission = Modules::load('registrar/registrardbprocess/');
        $user_id = $this->input->post('user_id');
        $rowid = $this->input->post('rowid');
        $pos = $this->input->post('pos');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $middlename = $this->input->post('middlename');
        $sy = $this->input->post('sy');

        switch ($pos):
            case 'f':
                $details = array(
                    'f_firstname' => $firstname,
                    'f_middlename' => $middlename,
                    'f_lastname' => $lastname
                );
                $updateBasicInfo = $this->registrar_model->editparentinfo($user_id, $details, $sy);
                echo strtoupper($updateBasicInfo->f_firstname . ' ' . $updateBasicInfo->f_lastname);
                break;
            case 'm':
                $details = array(
                    'm_firstname' => $firstname,
                    'm_middlename' => $middlename,
                    'm_lastname' => $lastname
                );
                $updateBasicInfo = $this->registrar_model->editparentinfo($user_id, $details, $sy);
                echo strtoupper($updateBasicInfo->m_firstname . ' ' . $updateBasicInfo->m_lastname);
                break;

        endswitch;

    }

    public function editStudentInfo($st_id, $data) {
        $update = $this->registrar_model->editStudentInfo($st_id, $data);
        return TRUE;
    }

    function sectionOveride() {
        $this->db->select('*');
        $this->db->select('profile_students.user_id as uid');
        $this->db->select('profile_students.section_id as sec_id');
        $this->db->from('profile_students');
        $this->db->join('section', 'section.section_id = profile_students.section_id', 'left');
        $query = $this->db->get();

        foreach ($query->result() as $q):
            $details = array(
                'section_id' => $q->section_id,
                'grade_level_id' => $q->grade_level_id,
                'status' => $q->status,
            );

            $this->db->where('user_id', $q->uid);
            if ($this->db->update('profile_students_admission', $details)):
                echo 'User ID:' . $q->uid . ' is updated <br />';
            else:
                echo 'An error occured';
            endif;
        endforeach;
    }

    function parentOveride() {
        $this->registrar_model->adviserOveride();
//       $result = $this->registrar_model->parentOveride();
//       echo $result.' data has been updated. Parent Overide Successful';
//        $this->registrar_model->assignOveride();
//       if($this->registrar_model->accountsOveride()):
//           echo 'Successfully Done';
//       endif;
    }

    function gs_overide() {
        $query = $this->db->get('profile_employee');
        foreach ($query->result() as $r):
            $details = array(
                'faculty_id' => $r->employee_id
            );
            $this->db->where('faculty_id', $r->user_id);
            if ($this->db->update('gs_assessment', $details)):
                echo $r->employee_id . ' assessments are successfully updated. <br />';
            endif;
        endforeach;
    }

    function checkNoExist() {
        $query = $this->db->query('Select st_id from esk_gs_final_assessment 
            where st_id not in(Select st_id from esk_profile_students)');
        echo $query->num_rows() . '<br />';
        foreach ($query->result() as $r):
            $this->db->where('st_id', $r->st_id);
            if ($this->db->delete('gs_final_assessment')):
                echo $r->st_id . ' is deleted.<br />';
            endif;
        endforeach;
    }

    public function calculateMonth($date1, $date2) {
        $begin = new DateTime($date1);
        $end = new DateTime($date2);
        $end = $end->modify('+1 month');

        $interval = DateInterval::createFromDateString('1 month');

        $period = new DatePeriod($begin, $interval, $end);
        $counter = 0;
        foreach ($period as $dt) {
            $counter++;
        }

        return $counter;
    }

    public function matchType() {
        $datani = $this->get_registrar_model->matchType();
        for ($x = 0; $x <= count($datani); $x++) {
            $ex = $this->get_registrar_model->getMatch($datani[$x]->st_id, $datani[$x]->lastname);
            if ($ex) {
                $match[] = $ex;
            }
        }

        for ($y = 0; $y <= count($match); $y++) {
            $why = $this->get_registrar_model->matchType($match[$y][0]['st_id']);
            if ($why) {
                $students[] = $why;
            }
        }

        $data['students'] = $students;
        $data['match'] = $match;

        $this->load->view('matchingType', $data);
    }

    /*
      public function getMatch(){
      $data = $this->get_registrar_model->getMatch();
      echo $data;
      return $data;
      }
     */

    function migrateLRN() {
        $oldData = $this->registrar_model->getlrn();
//        echo json_encode($oldData);
        foreach ($oldData as $old):
            echo $old->lrn . '<br>';
            $data = array('lrn' => $old->lrn);
            $this->registrar_model->lrnMigrate($data, $old->user_id);
        endforeach;
    }

    function getSingleStudentSPR($st_id, $sy) {
        return $this->get_registrar_model->getSingleStudentSPR($st_id, $sy);
    }
    
    function newProfileParents() {
        $this->get_registrar_model->newProfileParents();
    }

}
