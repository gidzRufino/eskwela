<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of main
 *
 * @author genesis
 */
class coursemanagement_model extends MX_Controller {
    //put your code here
    
    function getnstpstudents($sem, $school_year, $subject_id)
    {
       $this->db->where('s_id', $subject_id);
       $this->db->where('semester', $sem);
       $this->db->where('school_year', $school_year);
       $this->db->where('profile_students_c_admission.status', 1);
       $this->db->join('profile_students_c_load','c_subjects.s_id = profile_students_c_load.cl_sub_id', 'left');
       $this->db->join('profile_students_c_admission', 'profile_students_c_load.cl_adm_id = profile_students_c_admission.admission_id','left');
       $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
       $this->db->join('profile', 'profile_students_c_admission.user_id = profile.user_id', 'left');
        $this->db->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'cities.province_id  = provinces.id', 'left'); 
        $this->db->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
       $this->db->order_by('lastname','ASC');
       $this->db->order_by('firstname', 'ASC');
       $q = $this->db->get('c_subjects');
       return $q;
       
    }
    
    function removeSubjectFromCourse($id)
    {
        $this->db->where('spc_id', $id);
        if($this->db->delete('c_subjects_per_course')):
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function getCoursePerId($course_id)
    {
        $this->db->where('course_id', $course_id);
        $q = $this->db->get('c_courses');
        return $q->row();
    }
    
    function selectSubjectsPerCourse($course, $year_level, $sem, $year)
    {
        if($year==NULL):
            $year = $this->session->userdata('school_year');
        endif;
        
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->from('c_subjects_per_course');
        $this->db->join('c_subjects','c_subjects.s_id = c_subjects_per_course.spc_sub_id', 'left');
        $this->db->where('spc_course_id', $course);
        $this->db->where('spc_sem_id', $sem);
        $this->db->where('year_level', $year_level);
        $this->db->where('school_year', $year);
        $q = $this->db->get();
        if($q->num_rows()>0):
            return $q->result();
        else:
            return FALSE;
        endif;
    }
    
    function addSubjectPerCourse($spc, $sub_id, $course_id, $sem_id, $year_level, $school_year)
    {
        $this->db->where('spc_sub_id', $sub_id);
        $this->db->where('spc_course_id', $course_id);
        $this->db->where('spc_sem_id', $sem_id);
        $this->db->where('year_level', $year_level);
        $this->db->where('school_year', $school_year);
        $q = $this->db->get('c_subjects_per_course');
        if($q->num_rows()>0):
            return FALSE;
        else:
        $this->db->insert('c_subjects_per_course', $spc);
            return TRUE;
        endif;
    }
    function getSpecificSubjectPerCourse($course, $year_level, $sub_id, $sem, $year)
    {
        if($year==NULL):
            $year = $this->session->userdata('school_year');
        endif;
        
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->from('c_subjects_per_course');
        $this->db->where('spc_sub_id', $sub_id);
        $this->db->where('spc_course_id', $course);
        $this->db->where('spc_sem_id', $sem);
        $this->db->where('year_level', $year_level);
        $this->db->where('school_year', $year);
        $q = $this->db->get();
        if($q->num_rows()>0):
            return $q->row()->spc_id;
        else:
            return FALSE;
        endif;
    }
    function getSubjectPerCourse($course, $year_level, $sem, $year)
    {
        if($year==NULL):
            $year = $this->session->userdata('school_year');
        endif;
        
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->from('c_subjects_per_course');
        $this->db->join('c_subjects','c_subjects.s_id = c_subjects_per_course.spc_sub_id', 'left');
        $this->db->where('spc_course_id', $course);
        $this->db->where('spc_sem_id', $sem);
        $this->db->where('year_level', $year_level);
        $this->db->where('school_year', $year);
        $q = $this->db->get();
        if($q->num_rows()>0):
            return $q->result();
        else:
            return FALSE;
        endif;
    }
    
    function editCollegeLevel($details, $st_id, $admission_id)
    {
        $this->db->where('admission_id', $admission_id);
        $this->db->where('user_id', $st_id);
        $q = $this->db->get('profile_students_c_admission');
        if($q->num_rows()>0):
            $this->db->where('admission_id', $admission_id);
            $this->db->where('user_id', $st_id);
            $this->db->update('profile_students_c_admission', $details);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getCourse()
    {
        $q = $this->db->get('c_courses');
        return $q->result();
    }
    function addCourse($details, $section)
    {
        $this->db->where('course', $section);
        $q = $this->db->get('c_courses');
        if($q->num_rows()> 0):
            return FALSE;
        else:
            $this->db->insert('c_courses', $details);
            return TRUE;
        endif;
    }
    
    function addSection($details, $section, $grade_id)
    {
        $this->db->where('section', $section);
        $this->db->where('grade_level_id', $grade_id);
        $q = $this->db->get('section');
        if($q->num_rows()> 0):
            return FALSE;
        else:
            $this->db->insert('section', $details);
            return TRUE;
        endif;
    }


    function getDepartment()
    {
        $q = $this->db->get('level_department');
        return $q->result();
    }
    
    function deleteSection($section_id)
    {
        $this->db->where('section_id', $section_id);
        $this->db->delete('section');
        return;
    }
}

?>
