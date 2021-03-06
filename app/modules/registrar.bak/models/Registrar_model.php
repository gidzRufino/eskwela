<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class registrar_model extends CI_Model {
    
    
    public function __construct() {
        parent::__construct();
    }
    
    function editAddressInfo($add, $user_id, $school_year, $address_id)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('address_id', ($address_id!=""?$address_id:$user_id));
        $q = $this->db->get('profile_address_info');
        
        if($q->num_rows()>0):
            $this->db->where('address_id', ($address_id!=""?$address_id:$user_id));
            $this->db->update('profile_address_info', $add);
            
            $this->db->where('user_id', $user_id);
            $this->db->update('profile', array('add_id' => $q->row()->address_id ));
        else:
            $this->db->insert('profile_address_info', $add);
        
            $this->db->where('user_id', $user_id);
            $this->db->update('profile', array('add_id' => $user_id));
        endif;
        return;
        
        
        
    }
            
    function getOccupation($id)
    {
        $this->db->where('occ_id', $id);
        $q = $this->db->get('profile_occupation');
        return $q->row();
    }
            
    function editOccupation($occupation, $p_id, $mf, $school_year)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->select('*');
        $this->db->from('profile_occupation');
        $this->db->where('occupation', $occupation);
        $query = $this->db->get();
        if($query->num_rows()>0):
            $occ_id = $query->row()->occ_id;
            $this->setUpdateOccupation($occ_id, $p_id, $mf, $school_year);
        else:    
            $occ_id = $this->saveOccupation($occupation, $school_year);
            $this->setUpdateOccupation($occ_id, $p_id, $mf, $school_year);
        endif;
    }
    
    
    function saveOccupation($occupation, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('occupation', $occupation);
        $query = $this->db->get('profile_occupation');
        if($query->num_rows()==0):
            $this->db->insert('profile_occupation', array('occupation' => $occupation));
            $occ_id = $this->db->insert_id();
        else:
            $occ_id = $query->row()->occ_id;
        endif;
        
        return $occ_id;
    }
    
    
    function setUpdateOccupation($occ_id, $id,$mf, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
          $data = array(
                $mf.'_occ' => $occ_id
            );   
        $this->db->where('p_id', $id);
        $this->db->update('profile_parent', $data);    
    }
    
            
    function newProfileParents(){
        $this->db = $this->eskwela->db(2019);
        $parent = $this->db->get('profile_parents')->result();
        
        foreach($parent as $p):
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
            
            if($q->num_rows() > 0):
                return false;
            else:
                $data = array(
                    'u_id' => $p->parent_id,
                    'f_lastname' => $father->lastname,
                    'f_firstname' => $father->firstname,
                    'f_middlename' => $father->middlename,
                    'f_mobile' => ($f_contact->cd_mobile!=NULL?$f_contact->cd_mobile:''),
                    'f_office_name' => $p->f_office_name,
                    'f_office_address_id' => $p->f_office_address_id,
                    'm_lastname' => $mother->lastname,
                    'm_firstname' => $mother->firstname,
                    'm_middlename' => $mother->middlename,
                    'm_mobile' => ($m_contact->cd_mobile!=NULL?$m_contact->cd_mobile:''),
                    'm_office_name' => $p->m_office_name,
                    'm_office_address_id' => $p->m_office_address_id,
                    'ice_name' => $p->ice_name,
                    'ice_contact' => $p->ice_contact,
                    'guardian' => $p->guardian
                );
                if($p->parent_id!=0):
                    $this->db->insert('profile_parent', $data);
                endif;
            
            endif;
        endforeach;
    }
    
    public function saveParentDetails($parentDetails)
    {
        $this->db->insert('profile_parent', $parentDetails);
        return $this->db->insert_id();
    }
    
    public function checkIdIfExist($st_id)
    {
        $this->db->where('st_id', $st_id);
        $q = $this->db->get('profile_students');
        if($q->num_rows()>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }


    public function saveContacts($user_id, $mobile, $column=NULL)
    {
        if($column==NULL):
            $details = array('cd_mobile' => $mobile);
        else:
            $details = array($column => $mobile);
        endif;
        $this->db->where('cd_mobile', $mobile);
        $q = $this->db->get('profile_contact_details');
        if($q->num_rows()>0):
            $contact_id = $q->row()->contact_id;
            $this->db->where('contact_id', $contact_id);
            $this->db->update('profile_contact_details', $details);
        else:    
            $this->db->insert('profile_contact_details', $details); 
            $contact_id = $this->db->insert_id();
            $this->db->where('user_id', $user_id);
            $this->db->update('profile', array('contact_id'=>$contact_id));
        endif;
        return $contact_id;
    }


    public function isEnrolled($id, $year)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->where('school_year', $year);
        $this->db->where('profile_students.st_id', $id);
        $this->db->or_where('lrn', $id);
        $this->db->where('profile_students_admission.status', 1);
        $this->db->join('profile_students_admission','profile_students_admission.user_id = profile_students.user_id', 'left');
        $q = $this->db->get('profile_students');
        if($q->num_rows()>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function saveStudentAdmission($details, $user_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('user_id', $user_id);
        $this->db->where('school_year', $school_year);
        $q = $this->db->get('profile_students_admission');
        if($q->num_rows()>0):
            return FALSE;
        else:
            $this->db->insert('profile_students_admission', $details);
            $runScript = $this->db->last_query();
            Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        endif;
    }
    
    function saveBasicInformation($details, $lastname, $firstname, $middlename)
    {
        $this->db->where('lastname', $lastname);
        $this->db->where('firstname', $firstname);
        $this->db->where('middlename', $middlename);
        $q = $this->db->get('profile');
        if($q->num_rows()>0):
            $response = array(
                'status' => FALSE,
                'id'     => 0,
            );
            return json_encode($response);
        else:
            $this->db->insert('profile', $details);
            $response = array(
              'status'  => TRUE,
              'id'      => $this->db->insert_id()  
            );
            return json_encode($response);
        endif;
    }
    
    
    function setCollegeInfo($uid, $profile_id, $course,  $year, $en_date, $school_year, $semester, $sla, $asla)
    {
        $data1 = array(
            'st_id' => $uid,
            'user_id' => $profile_id,
            'parent_id' => $profile_id, 
            'status' => 0,
       ) ;
        
       $this->db->insert('profile_students', $data1); 
        
       $data = array(
            'school_year'       => $school_year,
            'semester'          => $semester,
            'st_id'             => $uid,
            'user_id'           => $profile_id,
            'course_id'         => $course, 
            'year_level'        => $year,
            'date_admitted'     => $en_date,
            'status'            => 0,
            'school_last_attend'=> $sla,
            'sla_address_id'    => $asla,
       ) ;
       
        $this->db->insert('profile_students_c_admission', $data);
    }
    
    
    function getLatestCollegeNum($level_id)
    {
        $this->db->select('*');
        $this->db->from('profile_students_c_admission');
        //$this->db->where('course_id', $level_id);
        $this->db->order_by('admission_id','DESC');
        $query=$this->db->get();
        return $query->row();
    }
    
    function saveReligion($religion)
    {
        $this->db->where('religion', $religion);
        $query = $this->db->get('religion');
        if($query->num_rows()>0):
            $rel_id = $query->row()->rel_id;
            return $rel_id;
        else:
            $details=array('religion'=>$religion);
            $this->db->insert('religion', $details);
            return $this->db->insert_id();
        endif;
    }
    
    function saveMLM($details, $month, $year, $grade_id, $code)
    {
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $this->db->where('mlm_grade_id', $grade_id);
        $this->db->where('code_indicator', $code);
        $query = $this->db->get('profile_students_mlm');
        if($query->num_rows()>0):
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $this->db->where('mlm_grade_id', $grade_id);
            $this->db->where('code_indicator', $code);
            $this->db->update('profile_students_mlm', $details);
            
            $return  = array(
                'action' => 'update',
                'id'     => $query->row()->id
            );
        else:
            $this->db->insert('profile_students_mlm', $details);
            $return  = array(
                'action' => 'insert',
                'id'     => $this->db->insert_id()
            );
        endif;
            return $return;
    }
    
    function checkID($id)
    {
        $this->db->where('user_id', $id);
        $query=$this->db->get('profile');
        if($query->num_rows()>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    
    function getUserId($user_id)
    {
        $this->db->select('st_id');
        $this->db->select('user_id');
        $this->db->from('profile_students');
        $this->db->where('st_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function updateProfileByUserId($user_id, $details)
    {
        $this->db->where('user_id', $user_id);
        $q = $this->db->get('profile');
        if($q->num_rows()>0):
            $this->db->where('user_id', $user_id);
            $this->db->update('profile', $details);
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function updateProfile($rowid, $details)
    {
        $this->db->where('rowid', $rowid);
        $this->db->update('profile', $details);
        return TRUE;
    }
    
    function updateProfileInfo($user_id, $details)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('profile_students', $details);
        return TRUE;
    }
    function updateProfileAdmission($user_id, $details)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('profile_students_admission', $details);
        return TRUE;
    }
    
    function editStudentInfo($user_id, $details)
    {
        $this->db->where('st_id', $user_id);
        $this->db->update('profile_students', $details);
        return TRUE;
    }
    
    function updateGradingAttendanceDetails($rowid, $details, $table)
    {
        $this->db->where('st_id', $rowid);
        $this->db->update($table, $details);
        return TRUE;
    }
    
    
    function updateFinanceDetails($rowid, $details, $table)
    {
        $this->db->where('stud_id', $rowid);
        $this->db->update($table, $details);
        return TRUE;
    }
    
    function updateProfileDetails($rowid, $details, $table)
    {
        $this->db->where('user_id', $rowid);
        $this->db->update($table, $details);
        return TRUE;
    }

    function getDeptCode($level_id)
    {
        $this->db->select('*');
        $this->db->from('grade_level');
        $this->db->where('grade_id', $level_id);
        $query=$this->db->get();
        return $query->row();
    }
    
    function importLatestId()
    {
        $this->db->order_by('temp_id','DESC');
        $g = $this->db->get('profile_temp_id');
        if($g->num_rows()>0):
            return $g->row()->generated_id;
        endif;
    }
    
    function getLatestIDs()
    {
        $q = $this->db->get('profile_temp_id');
        if($q->num_rows()==0):
            $this->db->select('admission_id');    
            $this->db->select('st_id'); 
            $this->db->from('profile_students_c_admission');
            $this->db->order_by('admission_id','DESC');
            $query = $this->db->get();
            $generated_id = $query->row()->st_id;
        else:
            $this->db->select('*');
            $this->db->from('profile_temp_id');
            //$this->db->where('course_id', $level_id);
            $this->db->order_by('temp_id','DESC');
            $query=$this->db->get();
            $generated_id = $query->row()->generated_id;
        endif;
        
        return json_encode(array('num_rows'=>$query->num_rows(), 'generated_id' => ($generated_id==NULL?$this->session->userdata('school_year').'00001':$generated_id)));
    }
    
    function generateTempId($st_id, $lastThree)
    {
        $this->db->where('generated_id', $st_id);
        $g = $this->db->get('profile_temp_id');
        if($g->num_rows()>0):
            $gen_id = $st_id + 1;
            $lastThree = $lastThree +1;
            $this->db->insert('profile_temp_id', array('generated_id'=> $gen_id));
        else:
             $gen_id = $st_id;
             $this->db->insert('profile_temp_id', array('generated_id'=> $gen_id));
        endif;
        
        return $lastThree;
    }
    
    function getLatestID($level_id=NULL)
    {
        $this->db->select('user_id');
        $this->db->from('profile');
        $this->db->order_by('user_id', 'DESC');
        $query = $this->db->get();
        if($query->num_rows()>0){
          return $query->row();  
        }else{
          return false;  
        }
        
    }
    
    function getGradeLevel($dept_id=NULL, $option=NULL)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        if($dept_id!=0):
            if($dept_id!=NULL && $option==NULL):
                $this->db->where('dept_id <=', $dept_id);
            endif;
            if($option!=NULL):
                $this->db->where('dept_id', $dept_id);
            endif;
        endif;
        $this->db->order_by('order','ASC'); 
        $query = $this->db->get('grade_level');
        return $query->result();
    }
    
    function getGradeLevelByLevelCode($code)
    {
        $this->db->where('levelCode', $code);
        $query = $this->db->get('grade_level');
        return $query->row()->grade_id;
    }
       
    function getGradeLevelBySectionId($id)
    {
        $this->db->where('section_id', $id);
        $query = $this->db->get('section');
        return $query->row();
    }
    
    function getGradeLevelById($id)
    {
        $this->db->where('grade_id', $id);
        $query = $this->db->get('grade_level');
        return $query->row();
    }
       
    
    function getSection()
    {
        $query = $this->db->get('section');
        return $query->result();
    }
    
    function saveSection($details)
    {
        $this->db->insert('section',$details);
        return;
    }
    
    function getAllSection($section_id, $option)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('section');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        if($section_id!=NULL):
            $this->db->where('section_id', $section_id);
        endif;
        if($option!=NULL):
            $this->db->where('dept_id <=', $option);
        endif;
        $this->db->order_by('grade_level.order', 'ASC');
        $query = $this->db->get();
        return $query;
    }
    
    function getSectionById($id)
    {
        $this->db->select('*');
        $this->db->from('section');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where('section.section_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getSectionByLevel($level, $sy=NULL)
    {
        if($sy==NULL):
            $sy = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($sy);
        $this->db->select('*');
        $this->db->select('section.section_id as s_id');
        $this->db->from('section');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('schedule', 'section.schedule_id = schedule.sched_id', 'left');
        $this->db->where('grade_level_id', $level);
        $query = $this->db->get();
        return $query;
    }
    
    function getEducAttain()
    {
       $query = $this->db->get('profile_educ_attain');
        return $query->result(); 
    }
    
    function getPhysician()
    {
        $this->db->select('*');
        $this->db->from('profile_med_physician');
        $this->db->join('profile_contact_details', 'profile_med_physician.physician_contact_id = profile_contact_details.contact_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }
    
    function savePhysician($name, $number)
    {
        $data = array(
            'physician' => $name
        );
        $this->db->insert('profile_med_physician', $data);
        
        $this->setContacts($number, 'cd_mobile');

        
    }
    function updatePhysician($p_id, $con_id)
    {
          $data = array(
                'physician_contact_id' => $con_id
            );   
        $this->db->where('physician_id', $p_id);
        $this->db->update('profile_med_physician', $data);    
    }
    
   //Admission process  
    function saveProfile($data, $user_id=NULL, $school_year = NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        if($user_id!=NULL):
            $this->db->where('user_id', $user_id);
            $q = $this->db->get('profile');
            if($q->num_rows()>0):
                return $q->row()->user_id;
            else:
                $this->db->insert('profile', $data);
                return $this->db->insert_id();
            endif;
        else:
            $this->db->insert('profile', $data);
            return $this->db->insert_id();
        endif;
    }
    
    function saveEdate($data)
    {
        $this->db->select('*');
        $this->db->from('calendar');
        $this->db->where('cal_date', $data);    
        $query = $this->db->get();
        if($query->num_rows()>0):
            return $query->row()->cal_id;
        else:
            $date = array(
                'cal_date' => $data
            );
            
            $this->db->insert('calendar', $date); 
            return $this->db->insert_id();
        endif;
    }
    
    function setStudInfo($uid, $profile_id, $section,  $grade_level, $motherTongue, $en_date, $school_year, $sla, $asla, $lrn)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
       if($lrn==NULL):
           $lrn="";
       endif;
       $data = array(
            'st_id' => $uid,
            'user_id' => $profile_id,
            'lrn' => $lrn,
            'parent_id' => 0, 
            'section_id' => $section,
            'mother_tongue_id' => $motherTongue,
            'status' => 1,
       ) ;
       $this->db->insert('profile_students', $data); 
       $this->setAdmissionDate($en_date, $profile_id, $school_year, $grade_level, $section, $sla, $asla, $uid);
    }
    
    function setAdmissionDate($en_date, $profile_id, $school_year, $grade_level_id, $section_id, $sla, $asla, $uid)
    {
        $data = array(
            'school_year'   => $school_year,
            'date_admitted' =>  $en_date,
            'user_id'       =>  $profile_id,
            'grade_level_id'=> $grade_level_id,
            'section_id'    => $section_id,
            'status'        => 1,
            'school_last_attend' => $sla,
            'sla_address'        => $asla,
            'st_id'              => $uid
        );
        
        $this->db->insert('profile_students_admission', $data);
    }
    
    function setBarangay($barangay, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('barangay', $barangay);
        $query = $this->db->get('barangay');
        if($query->num_rows()>0):
            return $query->row()->barangay_id;
        else:
            $data = array('barangay' => $barangay);
            $this->db->insert('barangay', $data);
        
            return $this->db->insert_id();
        endif;
    }
    
    function setCity($city, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('mun_city', $city);
        $query = $this->db->get('cities');
        return $query->row();
    }


    function setAddress($data, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->insert('profile_address_info', $data);
        
        return $this->db->insert_id();
    }
    
    function setUpdateAddress($add_id, $id, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
          $data = array(
                'add_id' => $add_id
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);    
    }
    
    function setUpdateOfficeAddress($add_id, $id, $m_p, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
          $data = array(
                'add_id' => $add_id
            );   
        $this->db->where($m_p.'_office_address_id', $id);
        $this->db->update('profile', $data);    
    }
    
    function setContacts($mobile, $email, $school_year = NULL, $user_id = null) {
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));

        $this->db->where('contact_id', $user_id);
        $c = $this->db->get('profile_contact_details')->row();
        if ($c->num_rows == 0):
            $data1 = array(
                'contact_id' => $user_id,
                'cd_phone' => 0,
                'cd_mobile' => $mobile,
                'cd_email' => $email
            );

            $this->db->insert('profile_contact_details', $data1);
        else:
            $data1 = array(
                'cd_phone' => 0,
                'cd_mobile' => $mobile,
                'cd_email' => $email
            );
            $this->db->where('contact_id', $user_id);
            $this->db->update('profile_contact_details', $data1);
        endif;

//        $data1 = array(
//            'cd_phone' => 0,
//            'cd_mobile' => $mobile,
//            'cd_email' => $email
//        );
//        $this->db->insert('profile_contact_details', $data1);
//
//        return $this->db->insert_id();
    }
    
     function setUpdateContact($id, $school_year = NULL) {
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $data = array(
            'contact_id' => $id
        );
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);
    }
    
    function saveDate($data, $id, $type , $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $date = array(
                'cal_date' => $data
            );
            
        $this->db->insert('calendar', $date);   
            
        $this->setDate($data, $id, $type);   
        
        return;
    }
    
    
    function setDate($data, $id, $type, $school_year = NULL) {
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $this->db->where('user_id', $id);
        $this->db->update('profile', array($type => $data));
//        $this->db->select('*');
//        $this->db->from('calendar');
//        $this->db->where('cal_date', $data);
//        $this->db->order_by('cal_id', 'desc');
//        $query = $this->db->get();
//        if ($query->num_rows() == 0) {
//            $this->saveDate($data, $id, $type);
//        } else {
//
//            $cal_id = $query->row()->cal_id;
//
//            $this->setUpdateDate($cal_id, $id, $type);
//        }
//        return;
    }
    
    function setUpdateDate($cal_id, $id, $type, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
          $data = array(
                $type => $cal_id
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);    
        return;
    }
    
    function setParentsPro($data, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->insert('profile_parents', $data);
        
        return $this->db->insert_id();
    }
    
    function getParents($parent_id, $option, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('parent_id', $parent_id);
        $q = $this->db->get('profile_parents');
        if($q->num_rows()>0):
            if($option=='f'):
                return $q->row()->father_id;
            else:
                return $q->row()->mother_id;
            endif;
        else:    
            return 0;
        endif;
    }
    
    function updateParents($parent_id, $data, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('parent_id', $parent_id);
        $q = $this->db->get('profile_parents');
        if($q->num_rows()>0):
            $this->db->where('parent_id', $parent_id);
            $this->db->update('profile_parents', $data);
        else:
            $this->db->insert('profile_parents', $data);
        endif;
        return;
    }
        
    function updateParentPro($parent_id, $student_id, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
          $data = array(
                'parent_id' => $parent_id
            );   
        $this->db->where('user_id', $student_id);
        $this->db->update('profile_students', $data);
        return;
    }
    
    function setOccupation($data, $id , $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $occupation = array(
                'occupation' => $data
            );
            
        $this->db->insert('profile_occupation', $occupation);   
            
        $this->chooseOcc($data, $id);    
    }
    
    
    function chooseEduc($data, $id){
        $data = array(
                'educ_attain_id' => $data
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);
    }
    
    function saveMed($btype, $allergies, $others, $physician, $height, $weight, $userid)
    {
        $data = array(
            'user_id' => $userid,
            'physician_id' => $physician,
            'allergies' => $allergies,
            'other_important' => $others,
            'blood_type' => $btype,
            'height' => $height,
            'weight' => $weight
        );
        
        $this->db->insert('profile_medical', $data);
        return;
        
    }
        
    function saveNewValue($table, $details, $school_year=NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->insert($table, $details);
        return;
    }
    
        function editParentInfo($nameArray, $parent_id, $school_year, $user_id)
    {
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $this->db->where('p_id', $parent_id);
        $q = $this->db->get('profile_parent');
        if($q->num_rows()>0):
            $this->db->where('p_id', $parent_id);
            if($this->db->update('profile_parent', $nameArray)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            if($this->db->insert('profile_parent', $nameArray)):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;    
        
        
    }
    
    function editBasicInfo($details, $rowid, $table, $where, $user_id=NULL)
    {
        $this->db->where($where, $rowid);
        
        if($this->db->update($table, $details)):
            
            $runScript = $this->db->last_query();
            Modules::run('web_sync/saveRunScript', $runScript);
            
            $this->db->where($where, $rowid);
            $query = $this->db->get($table);
            return $query->row();

        endif;
    }
    
    function deleteProfile($column, $id, $table, $school_year=NULL)
    {
        if($school_year==NULL):
            $school_year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($school_year);
        $this->db->where($column, $id);    
        $this->db->delete($table);
        
        $runScript = $this->db->last_query();
        Modules::run('web_sync/saveRunScript', $runScript);
        
        return;
    }
    
    function parentOveride()
    {
        $this->db->select('user_id');
        $this->db->select('parent_id');
        $this->db->from('profile');
        $query = $this->db->get();
        
        $i=0;
        foreach ($query->result() as $row):
            $i++;
            $details = array('parent_id'=>$row->parent_id);
            
            $this->db->where('user_id', $row->user_id);
            $this->db->update('profile_students', $details);
            
        endforeach;
        return $i;
    }
    
    function assignOveride()
    {
        $this->db->select('faculty_id');
        $this->db->from('faculty_assign');
        $query = $this->db->get();
        
        foreach($query->result() as $row):
            if(substr($row->faculty_id, 0,1)!='0'):
                $details = array('faculty_id' => '0'.$row->faculty_id);

                $this->db->where('faculty_id', $row->faculty_id);
                $this->db->update('faculty_assign', $details);
            endif;
        endforeach;
    }
    
    function adviserOveride()
    {
        $this->db->select('faculty_id');
        $this->db->from('advisory');
        $query = $this->db->get();
        
        foreach($query->result() as $row):
                $details = array('position_details' => 1);

                $this->db->where('employee_id', $row->faculty_id);
                $this->db->update('profile_employee', $details);
        endforeach;
    }
    
   function editRemarks()
   {
       $this->db->select('remark_to');
        $this->db->select('code_indicator_id');
        $this->db->from('admission_remarks');
        $this->db->where('code_indicator_id', 1);
        $query = $this->db->get();
        
        $i=0;
        foreach ($query->result() as $row):
            $i++;
            $details = array('status'=>0);
            
            $this->db->where('st_id', $row->remark_to);
            $this->db->update('profile_students', $details);
            
        endforeach;
        return $i;
   }
   
   function getlrn(){
       $this->db = $this->eskwela->db(2017);
       $this->db->where('lrn !=','');
       return $this->db->get('profile_students')->result();
   }
   
   function lrnMigrate($data,$userID){
       $this->db = $this->eskwela->db(2018);
       $this->db->where('user_id',$userID);
       $this->db->update('profile_students',$data);
   }
}

