<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of Registrar
 *
 * @author genesis
 */
class get_registrar_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        
    }
    
    function updateStudentStatus($admission_id)
    {
        $this->db->where('admission_id', $admission_id);
        if($this->db->update('profile_students_admission', array('status' => 1))):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function checkParentsID($lastname, $add_id)
    {
        $this->db->where('lastname', $lastname);
        $this->db->where('add_id', $add_id);
        $this->db->where('account_type', 4);
        $this->db->where('sex', 'Male');
        $f = $this->db->get('profile');
        if($f->num_rows()>0):
            $fdata = $f->row();
            $fname = $fdata->firstname;
            $flname = $fdata->lastname;
            $fuser_id = $fdata->user_id;
        else:
            $fname ="";
            $flname = "";
            $fuser_id = 0;
        endif;
        $this->db->where('lastname', $lastname);
        $this->db->where('add_id', $add_id);
        $this->db->where('account_type', 4);
        $this->db->where('sex', 'Female');
        $m = $this->db->get('profile');
        if($m->num_rows()>0):
            $mdata = $m->row();
            $mname = $mdata->firstname;
            $mlname = $mdata->lastname;
            $muser_id = $mdata->user_id;
        else:
            $mname ="";
            $mlname = "";
            $muser_id = 0;
        endif;
        
        $details = array(
            'fname'     => $fname,
            'flname'    => $flname,
            'fuser_id'  => $fuser_id,
            'mname'     => $mname,
            'mlname'    => $mlname,
            'muser_id'  => $muser_id
        );
        
        return json_encode($details);
        
    }
    
    function getSpecialization($specs_id)
    {
        if($specs_id!=NULL):
            $this->db->where('specialization_id', $specs_id);
            $q = $this->db->get('gs_specialization');
            return $q->row();
        else:
            $q = $this->db->get('gs_specialization');
            return $q->result();
        endif;
        
    }
    
    function getBasicInfo($user_id, $year)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->where('profile_students.user_id', $user_id);
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $query = $this->db->get('profile_students');
        return $query->row();
    }
    
    function insertData($details, $table, $column=NULL, $value=NULL)
    {
        if($column==NULL):
            if($this->db->insert($table, $details)):
               return TRUE;
            else:
                return FALSE;
            endif;
        else:
            $this->db->where($column, $value);
            if($this->db->update($table, $details)):
               return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }
    
    function getPreviousRecord($table, $columns, $value,  $school_year, $settings)
    {
       
        $db_details = $this->eskwela->db($school_year);
        $db_details->from($table);
        $db_details->where($columns, $value);
        $query = $db_details->get();
        return $query->row();
    }
    
    function getRfidByStid($st_id)
    {
        $this->db->where('profile_students.st_id', $st_id);
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $query = $this->db->get('profile_students');
        return $query->row();
    }
            
        
    function getSingleCollegeStudent($id, $year)
    {
        $this->db->select('*');
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile.user_id as u_id');
        $this->db->select('profile_parents.parent_id as pid');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left'); 
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('mother_tongue', 'profile_students.mother_tongue_id = mother_tongue.mt_id', 'left');
        $this->db->join('ethnic_group', 'profile.ethnic_group_id = ethnic_group.eg_id', 'left');
        $this->db->join('religion', 'profile.rel_id = religion.rel_id', 'left');
        $this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        if($year==NULL):
            $this->db->where('profile_students_c_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_c_admission.school_year', $year);
        endif;
        
        $this->db->where('profile_students.st_id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function getTotalCollegeStudents($course, $level, $school_year, $semester, $status)
    {
        
        $this->db->join('profile_students', 'profile_students_c_admission.user_id = profile_students.user_id', 'inner');
      //  $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'inner');
        if($course!=NULL):
            $this->db->where('course_id', $course);
        endif;
        if($level!=NULL):
            $this->db->where('year_level', $level);
        endif;
        $this->db->where('semester', $semester);
        $this->db->where('school_year', $school_year);
        $this->db->where('profile_students_c_admission.status', $status);
        $q = $this->db->get('profile_students_c_admission');
        return $q->num_rows();
    }
    
    	
    function getAllCollegeStudents($limit, $offset,  $course, $level, $year=NULL, $sem=NULL)
    {
        //$this->db->cache_on();
        
        $this->db->select('*');
        $this->db->select('profile_students_c_admission.status as status');
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile_students_c_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('religion', 'profile.rel_id = religion.rel_id', 'left');
        $this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        if($year==NULL):
            $this->db->where('profile_students_c_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_c_admission.school_year', $year);
        endif;
        if($sem!=NULL):
            $this->db->where('semester', $sem);
        endif;
        
        $this->db->where('account_type', 5);
        if(!$this->session->userdata('is_admin')){
            $this->db->where('profile_students_c_admission.status', 1);
        }
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('sex', 'DESC');
        //

        if($limit!=""||$offset=""){
                $this->db->limit($limit, $offset);	
        }
		
        $query = $this->db->get();
        return $query;
    }
    
        function getAllStudentsByLevel($grade_level, $section_id, $year=NULL)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('profile_students.st_id as st_id');
        $this->db->select('lastname');
        $this->db->select('firstname');
        $this->db->select('middlename');
        $this->db->select('profile_students_admission.section_id');
        $this->db->select('profile_students_admission.status');
        $this->db->select('profile_students_admission.school_year');
        $this->db->select('sex');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left'); 
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->where('profile_students_admission.status', 1);
        $this->db->order_by('lastname');
        if($grade_level!=NULL):
            $this->db->where('profile_students_admission.grade_level_id', $grade_level);
        endif;
        
        if($section_id!=NULL):
            $this->db->where('profile_students_admission.section_id', $section_id);
        endif;
        if($year!=NULL):
            $this->db->where('profile_students_admission.school_year', $year);
        else:
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        endif;
        $query = $this->db->get();
        return $query;
    }
    
    function getNumberOfStudentPerSection($section_id, $year, $status)
    {
        $this->db->select('profile_students.st_id');
        $this->db->select('profile_students_admission.section_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        if($section_id!=Null){
            $this->db->where('profile_students_admission.section_id', $section_id);
        }
        $this->db->where('profile_students_admission.school_year', $year);
        $this->db->where('profile_students_admission.status', $status);
        $query =  $this->db->get();
        return $query->num_rows();
    }

    function getMLM($year, $month, $grade_id, $code)
    {
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $this->db->where('mlm_grade_id', $grade_id);
        $this->db->where('code_indicator', $code);
        $query = $this->db->get('profile_students_mlm');
        return $query;
    }
    
    function getStudentPerRO($ro, $section_id, $sy)
    {
        $this->db = $this->eskwela->db($sy);
        $this->db->where('school_year', $ro);
        $this->db->where('section_id', $section_id);
        $query = $this->db->get('profile_students_admission');
        return $query->num_rows();
    }
    function deleteROStudent($user_id, $sy, $adm_id=NULL)
    {
        if($adm_id==NULL):
            $this->db->where('user_id', $user_id);
            $this->db->where('school_year', $sy);
        else:
            $this->db->where('admission_id', $adm_id);
        endif;
        if($this->db->delete('profile_students_admission')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }


    function checkCollegeRO($user_id, $semester, $school_year)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $semester);
        $query = $this->db->get('profile_students_c_admission');
        
        if($query->num_rows>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function checkRO($st_id, $school_year, $grade_id)
    {
        $this->db->where('user_id', $st_id);
        $this->db->where('school_year', $school_year);
        //$this->db->where('grade_level_id', $grade_id);
        $query = $this->db->get('profile_students_admission');
        
        if($query->num_rows>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function saveCollegeRO($details)
    {
        if($this->db->insert('profile_students_c_admission', $details)):
           $return = array(
               'status' => TRUE,
               'data'   => $this->db->insert_id(),
           );
           return $return;
        else:
            return FALSE;
        endif;
    }
    
    function saveRO($details)
    {
        if($this->db->insert('profile_students_admission', $details)):
           $return = array(
               'status' => TRUE,
               'data'   => $this->db->insert_id(),
           );
           return $return;
        else:
            return FALSE;
        endif;
    }
    
    function getStudentBySY($year,  $limit=NULL, $offset=NULL, $status=NULL)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile_students.status as status');
        $this->db->select('lastname');
        $this->db->select('firstname');
        $this->db->select('middlename');
        $this->db->select('grade_id');
        $this->db->select('section');
        $this->db->select('level');
        $this->db->select('sex');
        $this->db->select('ro_years');
        $this->db->select('rfid');
        //$this->db->select('*');
        
        $this->db->from('profile');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id','left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('profile_students_ro_years', 'profile_students_admission.school_year = profile_students_ro_years.ro_years','left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where('profile_students_ro_years.ro_years', $year);
        if($status!=NULL):
            $this->db->where('profile_students_admission.status', $status);
        endif;
        if($limit!=NULL||$offset=NULL){
           $this->db->limit($limit, $offset);	
        }
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('sex', 'DESC');
        $query = $this->db->get();
        return $query;
    }

    function getCollegeStudentBySY($limit, $offset,  $year, $sem, $course=NULL, $level=NULL, $status )
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_c_admission.status as status');
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile_students_c_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('religion', 'profile.rel_id = religion.rel_id', 'left');
        $this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        if($year==NULL):
            $this->db->where('profile_students_c_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_c_admission.school_year', $year);
        endif;
        if($course!=NULL):
            $this->db->where('profile_students_c_admission.course_id', $course);
        endif;
        if($sem!=NULL):
            $this->db->where('semester', $sem);
        endif;
        if($level!=NULL):
            $this->db->where('year_level', $level);
        endif;
        
        $this->db->where('account_type', 5);
        ($status!=NULL?$this->db->where('profile_students_c_admission.status', $status):"");
        $this->db->order_by('year_level', 'ASC');
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('sex', 'DESC');
        $this->db->group_by('profile.user_id');

        if($limit!=NULL||$offset=NULL){
                $this->db->limit($limit, $offset);  
        }
        
        $query = $this->db->get();
        return $query;
    }
    
    function getROYear()
    {
        $ro = $this->db->get('profile_students_ro_years');
        return $ro->result();
    }
    
    function getLrnByID($st_id, $sy=NULL)
    {
        $this->db = $this->eskwela->db($sy);
        $this->db->select('st_id');
        $this->db->select('profile.user_id as uid');
        $this->db->from('profile');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id','left');
        $this->db->where('profile.user_id', $st_id);
        $query = $this->db->get();
        return $query->row();
        
    }
    
    function getBasicStudent($st_id, $school_year=NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->from('profile_students');
        $this->db->join('profile_students_admission', 'profile_students.user_id = profile_students_admission.user_id','left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where('st_id', $st_id);
        if($school_year!=NULL):
            $this->db->where('profile_students_admission.school_year', $school_year);
        endif;
        $query = $this->db->get();
        return $query;
    }
    
    function getStudentListForParent($pid)
    {
        $this->db->select('*');
        $this->db->select('profile_students.st_id as uid');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('section', 'profile_students_admission.section_id = section.section_id', 'left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where('profile_students.parent_id', $pid);
        $this->db->where('profile_students_admission.school_year',  $this->session->userdata('school_year'));
        $this->db->order_by('profile_students_admission.grade_level_id', 'ASC');
        $query = $this->db->get();
        return $query->result();
        
    }
    
    function getAllStudentsForCard($limit, $offset, $section_id, $school_year=NULL)
    {
        $this->db->select('*');
        $this->db->select('profile_students_admission.school_year');
        $this->db->select('profile_students.st_id');
        $this->db->select('profile.user_id as uid');
        $this->db->select('profile_students.lrn');
        $this->db->select('firstname');
        $this->db->select('middlename');
        $this->db->select('lastname');
        $this->db->select('level');
	$this->db->select('bdate_id');
        $this->db->select('temp_bdate');
        $this->db->select('profile_students_admission.section_id');
        $this->db->select('account_type');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        if($section_id!=Null){
            $this->db->where('profile_students_admission.section_id', $section_id);
        }
        
        $this->db->where('account_type', 5);
        $this->db->where('profile_students_admission.school_year', ($school_year == NULL?$this->session->userdata('school_year'):$school_year));
        
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->order_by('sex', 'DESC');
        //

        if($limit!=""||$offset=""){
                $this->db->limit($limit, $offset);	
        }
		
        $query = $this->db->get();
        return $query;
    }
	
    function getAllStudentsForID($limit, $offset, $grade_id, $section)
    {
        $this->db->select('*');
        $this->db->select('profile.user_id as uid');
        $this->db->select('profile_students.parent_id as pid');
        $this->db->select('lastname');
        $this->db->select('firstname');
        $this->db->select('middlename');
        $this->db->select('level');
        $this->db->select('section');
        $this->db->select('profile.sex as sex');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        
        if($grade_id!=Null){
            $this->db->where('profile_students_admission.grade_level_id', $grade_id);
        }
        if($section!=Null){
            $this->db->where('profile_students_admission.section_id', $section);
        }
        $this->db->where('avatar !=', 'noImage.png');
        
        $this->db->where('account_type', 5);
        
        $this->db->where('profile_students_admission.status', 1);
        
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('sex', 'DESC');
        //

        if($limit!=""||$offset=""){
                $this->db->limit($limit, $offset);	
        }
		
        $query = $this->db->get();
        return $query;
    }
	
    function getAllStudents($limit, $offset, $grade_id, $section, $year=NULL)
    {
        //$this->db->cache_on();
        if($year==NULL):
            $year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_admission.status as stats');
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','inner');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('mother_tongue', 'profile_students.mother_tongue_id = mother_tongue.mt_id', 'left');
        $this->db->join('ethnic_group', 'profile.ethnic_group_id = ethnic_group.eg_id', 'left');
        $this->db->join('religion', 'profile.rel_id = religion.rel_id', 'left');
        //$this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        $this->db->join('profile_parents', 'profile.user_id = profile_parents.parent_id', 'left');
        if($year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $year);
        endif;
        
        if($grade_id!=Null && $grade_id!=0){
            $this->db->where('profile_students_admission.grade_level_id', $grade_id);
        }
        if($section!=Null){
            $this->db->where('profile_students_admission.section_id', $section);
        }
        $this->db->where('account_type', 5);
        if(!$this->session->userdata('is_admin')){
            $this->db->where('profile_students_admission.status', 1);
        }
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('sex', 'DESC');
        //

        if($limit!=""||$offset=""){
            $this->db->limit($limit, $offset);	
        }
		
        $query = $this->db->get();
        return $query;
    }
    
    function getNumberOfStudentsPerMonth($month, $gender)
    {
        $this->db->select('st_id');
        $this->db->select('profile_students_admission.status as stat');
        $this->db->select('sex');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $query = $this->db->get();
        return $query;
    }
    
    //function getStudentToDelete($school_year)
    
    function getStudentsForGS($grade_id, $section, $gender, $school_year=NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        if($school_year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $school_year);
        endif;
        
        if($gender!=Null){
            $this->db->where('sex', $gender);
        }
        if($grade_id!=Null){
            $this->db->where('profile_students_admission.grade_level_id', $grade_id);
        }
        
        if($section!=Null){
            $this->db->where('profile_students_admission.section_id', $section);
        }
        $this->db->where('account_type', 5);
	$this->db->order_by('sex', 'DESC');
	$this->db->order_by('lastname', 'ASC');

        $query = $this->db->get();
        return $query;
    }
    
    function getStudents($grade_id, $section, $gender, $status, $school_year=NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->select('profile_students.st_id as st_id');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile.contact_id as con_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('mother_tongue', 'profile_students.mother_tongue_id = mother_tongue.mt_id', 'left');
        $this->db->join('ethnic_group', 'profile.ethnic_group_id = ethnic_group.eg_id', 'left');
        $this->db->join('religion', 'profile.rel_id = religion.rel_id', 'left');
        $this->db->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        if($school_year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $school_year);
        endif;
        if($status!=NULL){
            $this->db->where('profile_students_admission.status', $status);
        }
        
        if($gender!=Null){
            $this->db->where('sex', $gender);
        }
        if($grade_id!=Null){
            $this->db->where('profile_students_admission.grade_level_id', $grade_id);
        }
        
        if($section!=Null){
            $this->db->where('profile_students_admission.section_id', $section);
        }
        $this->db->where('account_type', 5);
	//$this->db->order_by('sex', 'DESC');
	$this->db->order_by('lastname', 'ASC');

        $query = $this->db->get();
        return $query;
    }
    
    function getAllStudentsBasicInfoByGender($section_id, $gender, $status, $year=NULL)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('profile_students.st_id as st_id');
        $this->db->select('profile.user_id as u_id');
        $this->db->select('rfid');
        $this->db->select('lastname');
        $this->db->select('firstname');
        $this->db->select('middlename');
        $this->db->select('profile_students_admission.section_id');
        $this->db->select('profile_students_admission.status');
        $this->db->select('profile_students_admission.school_year');
        $this->db->select('sex');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left'); 
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        if($section_id!=NULL):
            $this->db->where('profile_students_admission.section_id', $section_id);
        endif;
        if($gender!=NULL):
            $this->db->where('sex', $gender);
        endif;
        if($status!=NULL){
            $this->db->where('profile_students_admission.status', $status);
        }
        if($year!=NULL):
            $this->db->where('profile_students_admission.school_year', $year);
        else:
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        endif;
        $this->db->order_by('sex', 'DESC');
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
        return $query;
    }
    
    function getAllStudentsByGender($section_id, $gender, $status, $year=NULL)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left'); 
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->where('profile_students_admission.section_id', $section_id);
        $this->db->where('sex', $gender);
        if($status!=NULL){
            $this->db->where('profile_students_admission.status', $status);
        }
        if($year!=NULL):
            $this->db->where('profile_students_admission.school_year', $year);
        else:
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        endif;
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $query = $this->db->get();
        return $query;
    }
    
    function getAllStudentsByGenderForAttendance($section_id, $gender, $status)
    {
        //$this->db->select('*');
        $this->db->select('st_id');
        $this->db->select('rfid');
        $this->db->select('profile.user_id');
        $this->db->select('firstname');
        $this->db->select('middlename');
        $this->db->select('lastname');
        $this->db->select('section_id');
        $this->db->select('sex');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        $this->db->where('profile_students_admission.section_id', $section_id);
        $this->db->where('sex', $gender);
        if($status!=""){
        $this->db->where('profile_students_admission.status', $status);
        }
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
        return $query;
    }
        
    function getSingleStudentSPR($st_id, $sy){
        $this->db = $this->eskwela->db($sy);
        $this->db->join('gs_spr', 'gs_spr.st_id = gs_spr_profile.sprp_st_id', 'left');
        $this->db->where('sprp_st_id', $st_id);
        return $this->db->get('gs_spr_profile')->row();
    }
    
    function getSingleStudent($id, $year) {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students.st_id as uid');
        $this->db->select('profile.user_id as u_id');
        $this->db->select('profile_students.user_id as us_id');
        $this->db->select('profile_students.parent_id as pid');
        $this->db->select('profile_students_admission.school_year as sy');
        $this->db->select('profile_students_admission.grade_level_id as gl_id');
        $this->db->select('profile_students_admission.str_id as strnd_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id', 'inner');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->join('mother_tongue', 'profile_students.mother_tongue_id = mother_tongue.mt_id', 'left');
        $this->db->join('ethnic_group', 'profile.ethnic_group_id = ethnic_group.eg_id', 'left');
        $this->db->join('religion', 'profile.rel_id = religion.rel_id', 'left');
        $this->db->join('profile_parent', 'profile.user_id = profile_parent.u_id', 'left');
        $this->db->join('profile_medical', 'profile.user_id = profile_medical.user_id', 'left');
        //$this->db->join('sh_strand','sh_strand.st_id = profile_students_admission.str_id','left');
        if ($year == NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $year);

        endif;
        $this->db->where('profile_students.st_id', $id);
        $this->db->or_where('profile_students.user_id', $id);
        $this->db->or_where('profile_students.lrn', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function getSingleStudentByRfid($rfid, $year=NULL)
    {
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->where('rfid', $rfid);
        $query = $this->db->get();
        if($query->row()->account_type==5):
            $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
            $this->db->where('rfid', $rfid);
            $q2 = $this->db->get('profile');
        else:
            $this->db->join('profile_employee', 'profile.user_id = profile_employee.user_id', 'left');
            $this->db->join('profile_position', 'profile_employee.position_id = profile_position.position_id', 'left');
            $this->db->where('rfid', $rfid);
            $q2 = $this->db->get('profile');
        endif;
        return $q2;
    }
    
    /*
    function getSingleStudentByRfid($rfid, $year=NULL)
    {
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->where('rfid', $rfid);
        $query = $this->db->get();
        if($query->row()->account_type==5):
            $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id', 'inner');
            //$this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id', 'inner');
            $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left'); 
            $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
            $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
            $this->db->where('rfid', $rfid);
            $q2 = $this->db->get('profile');
        else:
            $this->db->join('profile_employee', 'profile.user_id = profile_employee.user_id', 'left');
            $this->db->join('profile_position', 'profile_employee.position_id = profile_position.position_id', 'left');
            $this->db->where('rfid', $rfid);
            $q2 = $this->db->get('profile');
        endif;
        return $q2;
    }
    */

    
    function getMother($id) {
        $this->db->select('*');
        $this->db->from('profile_parent');
        $this->db->join('profile_address_info', 'profile_address_info.address_id = profile_parent.m_add_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id = barangay.barangay_id', 'left');
        $this->db->join('profile_educ_attain', 'profile_parent.m_educ = profile_educ_attain.ea_id', 'left');
        $this->db->join('profile_occupation', 'profile_parent.m_occ = profile_occupation.occ_id', 'left');
        $this->db->where('profile_parent.u_id', $id);
        return $this->db->get()->row();
//        $this->db->select('profile.user_id as mid');
//        $this->db->select('profile.contact_id as con_id');
//        $this->db->from('profile_parents');
//        $this->db->join('profile', 'profile_parents.mother_id = profile.user_id', 'left');
//        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
//        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
//        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
//        $this->db->join('profile_educ_attain', 'profile.educ_attain_id  = profile_educ_attain.ea_id', 'left');
//        $this->db->join('profile_occupation', 'profile.occupation_id  = profile_occupation.occ_id', 'left');
//        $this->db->where('profile_parents.parent_id', $id);
//        $query = $this->db->get();
//        return $query->row();
    }
    
    function getFather($id) {
        $this->db->select('*');
        $this->db->from('profile_parent');
        $this->db->join('profile_address_info', 'profile_address_info.address_id = profile_parent.f_add_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id = barangay.barangay_id', 'left');
        $this->db->join('profile_educ_attain', 'profile_parent.f_educ = profile_educ_attain.ea_id', 'left');
        $this->db->join('profile_occupation', 'profile_parent.f_occ = profile_occupation.occ_id', 'left');
        $this->db->where('profile_parent.u_id', $id);
        return $this->db->get()->row();
//        $this->db->select('*');
//        $this->db->select('profile.user_id as fid');
//        $this->db->select('profile.contact_id as con_id');
//        $this->db->from('profile_parents');
//        $this->db->join('profile', 'profile_parents.father_id = profile.user_id', 'left');
//        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
//        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
//        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
//        $this->db->join('profile_educ_attain', 'profile.educ_attain_id  = profile_educ_attain.ea_id', 'left');
//        $this->db->join('profile_occupation', 'profile.occupation_id  = profile_occupation.occ_id', 'left');
//        $this->db->where('profile_parents.parent_id', $id);
//        $query = $this->db->get();
//        return $query->row();
    }
    
    function getMedical($id)
    {
        $this->db->select('*');
        $this->db->from('profile_medical');
        $this->db->join('profile_med_physician', 'profile_medical.physician_id = profile_med_physician.physician_id', 'left');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function getSectionById($id)
    {
        $this->db->select('*');
        $this->db->from('section');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where('section_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getSection($id)
    {
        $this->db->select('*');
        $this->db->from('section');
        $this->db->where('section_id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function getDateEnrolled($id)
    {
        $this->db->select('*');
        $this->db->from('profile_students_admission');
        $this->db->join('calendar', 'profile_students_admission.date_admitted = calendar.cal_id', 'left');
        $this->db->where('user_id', $id);
        $query = $this->db->get();
        return $query->row()->cal_date;
    }
    
    function getLateEnrolleesByGender($sex)
    {
        $this->db->select('*');
        $this->db->from('admission_remarks');
        $this->db->join('deped_code_indicator', 'admission_remarks.code_indicator_id = deped_code_indicator.id', 'left');
        $this->db->join('profile', 'admission_remarks.remark_to = profile.user_id', 'left');
        $this->db->where('code_indicator_id', 4);
        $this->db->where('profile.sex', $sex);
        $query = $this->db->get();
        return $query;
    }
    
    function getStudentStatus($status, $sex, $month, $section_id, $year, $option, $grade_id)
    {
        if($year==NULL):
            $year = date('Y');
        endif;
        
        $this->db->select('*');
        $this->db->from('admission_remarks');
        $this->db->join('deped_code_indicator', 'admission_remarks.code_indicator_id = deped_code_indicator.id', 'left');
        $this->db->join('profile_students', 'admission_remarks.remark_to = profile_students.st_id', 'right');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id', 'left');
        $this->db->where('profile_students_admission.grade_level_id', $grade_id);
        $this->db->where('profile_students_admission.school_year', $year);
        $this->db->where('admission_remarks.rem_month', abs($month));
        $this->db->where('code_indicator_id', $status);
        $this->db->where('profile.sex', $sex);
        if($section_id!=NULL):
            $this->db->where('profile_students_admission.section_id', $section_id);
        endif;
        $query = $this->db->get();
        return $query;
    }

    function update_student_stType($type, $uid, $schoolyear){
        $details = array('st_type' => $type);
        $this->db->where('user_id',$uid);
        $this->db->where('school_year',$schoolyear);
        return ($this->db->update('profile_students_admission',$details)) ? TRUE : FALSE;
    }

    function matchType($id=Null){
        $this->details = $this->eskwela->db(2017);
        $this->details->select('*');
        $this->details->from('profile_students');
        $this->details->join('profile','profile.user_id = profile_students.user_id','left');
        if($id!=Null):
            $this->details->where('profile_students.st_id',$id);
        endif;
        $result = $this->details->get();
        return $result->result();
    }

    function getMatch($id,$lastname){
        $this->db->select('*');
        $this->db->from('profile_students_admission');
        $this->db->join('profile','profile.user_id = profile_students_admission.user_id', 'left');
        $this->db->where('profile_students_admission.st_id',$id);
        $this->db->where('profile.lastname!=',$lastname);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function updateContact($id){
        $this->db->where('user_id',$id);
        $this->db->update('profile', array('contact_id' => $id));
    }
    
    function newProfileParents() {
        $this->db = $this->eskwela->db(2019);
        $parent = $this->db->get('profile_parents')->result();

        foreach ($parent as $p):
            $this->db->where('user_id', $p->father_id);
            $father = $this->db->get('profile')->row();

            $this->db->where('contact_id', $father->contact_id);
            $f_contact = $this->db->get('profile_contact_details')->row();

            $this->db->where('user_id', $p->mother_id);
            $mother = $this->db->get('profile')->row();

            $this->db->where('contact_id', $mother->contact_id);
            $m_contact = $this->db->get('profile_contact_details')->row();

            $this->db->where('u_id', $p->parent_id);
            $q = $this->db->get('profile_parent');

            if ($q->num_rows() > 0):
                return false;
            else:
                $data = array(
                    'u_id' => $p->parent_id,
                    'f_lastname' => $father->lastname,
                    'f_firstname' => $father->firstname,
                    'f_middlename' => $father->middlename,
                    'f_mobile' => $f_contact->cd_mobile,
                    'f_add_id' => $father->add_id,
                    'f_occ' => $father->occupation_id,
                    'f_educ' => $father->educ_attain_id,
                    'f_office_name' => $p->f_office_name,
                    'f_office_address_id' => $p->f_office_address_id,
                    'm_lastname' => $mother->lastname,
                    'm_firstname' => $mother->firstname,
                    'm_middlename' => $mother->middlename,
                    'm_mobile' => $m_contact->cd_mobile,
                    'm_add_id' => $mother->add_id,
                    'm_occ' => $mother->occupation_id,
                    'm_educ' => $mother->educ_attain_id,
                    'm_office_name' => $p->m_office_name,
                    'm_office_address_id' => $p->m_office_address_id,
                    'ice_name' => $p->ice_name,
                    'ice_contact' => $p->ice_contact,
                    'guardian' => $p->guardian
                );

                $this->db->insert('profile_parent', $data);
            endif;
        endforeach;
    }

}
