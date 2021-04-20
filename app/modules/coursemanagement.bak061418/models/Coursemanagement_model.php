<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of main
 *
 * @author genesis
 */
class coursemanagement_model extends MX_Controller {
    //put your code here
    
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
    
    function editCollegeLevel($details, $st_id)
    {
        $this->db->where('user_id', $st_id);
        $q = $this->db->get('profile_students_c_admission');
        if($q->num_rows()>0):
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
