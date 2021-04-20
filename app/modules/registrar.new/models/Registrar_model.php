<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class registrar_model extends CI_Model {
    
    
    public function __construct() {
        parent::__construct();
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

    public function showbday()
    {
        $sy = $this->session->userdata('school_year');
        $this->db->select('*');
        $this->db->from('profile_students_admission');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('profile', 'profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $this->db->where('profile_students_admission.school_year', $sy);
        $query = $this->db->get();
        return $query->result();
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
        $this->db->where('st_id', $id);
        $this->db->where('school_year', $year);
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
            'parent_id' => 0, 
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

    function getLatestIDs()
    {
//        $subString = 'SUBSTRING(u_id, -3)';
//        $this->db->select('*', FALSE);
//        $this->db->from('profile_info');
//        $this->db->join('profile', 'profile_info.u_id = profile.user_id', 'left');
//        $this->db->join('grade_level', 'profile_info.grade_level_id = grade_level.grade_id', 'left');
//        $this->db->where('account_type', 5);
//        $this->db->where('grade_level_id', $level_id);
//        $this->db->order_by($subString, 'DESC');
       // $query = $this->db->get();
        $this->db->order_by('st_id', 'DESC');
        $query = $this->db->get('profile_students');
        if($query->num_rows()>0){
          return $query->row();  
        }else{
          return false;  
        }
        
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
        if($dept_id!=NULL && $option==NULL):
            $this->db->where('dept_id <=', $dept_id);
        endif;
        if($option!=NULL):
            $this->db->where('dept_id', $dept_id);
        endif;
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
        //$this->db->order_by('grade_level.grade_id', 'ASC');
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

    function getSectionByLevel($level, $sy)
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
    function saveProfile($data)
    {
        $this->db->insert('profile', $data);
        return $this->db->insert_id();
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
       $this->setAdmissionDate($en_date, $profile_id, $school_year, $grade_level, $section, $sla, $asla);
    }
    
    function setAdmissionDate($en_date, $profile_id, $school_year, $grade_level_id, $section_id, $sla, $asla)
    {
        $data = array(
            'school_year'   => $school_year,
            'date_admitted' =>  $en_date,
            'user_id'       =>  $profile_id,
            'grade_level_id'=> $grade_level_id,
            'section_id'    => $section_id,
            'status'        => 1,
            'school_last_attend' => $sla,
            'sla_address'        => $asla
        );
        
        $this->db->insert('profile_students_admission', $data);
    }
    
    function setBarangay($barangay)
    {
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
    
    function setCity($city)
    {
        $this->db->where('mun_city', $city);
        $query = $this->db->get('cities');
        return $query->row();
    }


    function setAddress($data)
    {
        $this->db->insert('profile_address_info', $data);
        
        return $this->db->insert_id();
    }
    
    function setUpdateAddress($add_id, $id)
    {
          $data = array(
                'add_id' => $add_id
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);    
    }
    
    function setUpdateOfficeAddress($add_id, $id, $m_p)
    {
          $data = array(
                'add_id' => $add_id
            );   
        $this->db->where($m_p.'_office_address_id', $id);
        $this->db->update('profile', $data);    
    }
    
    function setContacts($mobile, $email)
    {
        $data1 = array(
                'cd_phone' => 0,
                'cd_mobile' => $mobile,
                'cd_email' => $email
            );
        $this->db->insert('profile_contact_details', $data1);
        
        return $this->db->insert_id();
    }
    
     function setUpdateContact($con_id, $id)
    {
          $data = array(
                'contact_id' => $con_id
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);    
    }
    
    function saveDate($data, $id, $type )
    {
        $date = array(
                'cal_date' => $data
            );
            
        $this->db->insert('calendar', $date);   
            
        $this->setDate($data, $id, $type);   
        
        return;
    }
    
    
    function setDate($data, $id, $type){
        $this->db->select('*');
        $this->db->from('calendar');
        $this->db->where('cal_date', $data);
        $this->db->order_by('cal_id', 'desc');
        $query = $this->db->get();
        if($query->num_rows()== 0)
        {
            $this->saveDate($data, $id, $type);
            
        }else{
            
            $cal_id = $query->row()->cal_id; 
            
            $this->setUpdateDate($cal_id, $id, $type);
        }
        return;
    }
    
    function setUpdateDate($cal_id, $id, $type)
    {
          $data = array(
                $type => $cal_id
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);    
        return;
    }
    
    function setParentsPro($data)
    {
        $this->db->insert('profile_parents', $data);
        
        return $this->db->insert_id();
    }
    
    function updateParents($parent_id, $data)
    {
        $this->db->where('parent_id', $parent_id);
        $this->db->update('profile_parents', $data);
        return;
    }
        
    function updateParentPro($parent_id, $student_id)
    {
          $data = array(
                'parent_id' => $parent_id
            );   
        $this->db->where('user_id', $student_id);
        $this->db->update('profile_students', $data);    
    }
    
    function chooseOcc($data, $id){
        $this->db->select('*');
        $this->db->from('profile_occupation');
        $this->db->where('occupation', $data);
        $this->db->order_by('occ_id', 'desc');
        $query = $this->db->get();
        if($query->num_rows()>0):
            $occ_id = $query->row()->occ_id;
            $this->setUpdateOccupation($occ_id, $id);
        
        else:    
            $this->setOccupation($data, $id);
        endif;
        
        
    }
    
    function setOccupation($data, $id )
    {
        $occupation = array(
                'occupation' => $data
            );
            
        $this->db->insert('profile_occupation', $occupation);   
            
        $this->chooseOcc($data, $id);    
    }
    
    function setUpdateOccupation($occ_id, $id)
    {
          $data = array(
                'occupation_id' => $occ_id
            );   
        $this->db->where('user_id', $id);
        $this->db->update('profile', $data);    
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
    
    
    
    function saveNewValue($table, $details)
    {
        $this->db->insert($table, $details);
        return;
    }

    function editparentinfo($user_id, $details)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('profile', $details);
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->where('profile.user_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function editBasicInfo($details, $rowid, $table, $where, $user_id=NULL)
    {
        if($rowid==""):
            $this->db->insert($table, $details);
            $id = $this->db->insert_id();
            if($table=='profile_address_info'):
                if($user_id!=NULL):
                    $this->db->where('user_id', $user_id);
                    $this->db->update('profile', array('add_id'=> $id));
                endif;
                $this->db->where($where, $rowid);

                $query = $this->db->get($table);
                return $query->row();
            endif;
        else:
            $this->db->where($where, $rowid);
        
            if($this->db->update($table, $details)):
                $this->db->select('*');
                $this->db->from($table);
                if($table=='profile_address_info'):
                    $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
                    $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
                    $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
                endif;
                $this->db->where($where, $rowid);

                $query = $this->db->get($table);
                return $query->row();

            endif;
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
}

