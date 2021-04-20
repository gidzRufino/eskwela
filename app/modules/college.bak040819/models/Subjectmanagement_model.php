<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of main
 *
 * @author genesis
 */
class subjectmanagement_model extends MX_Controller {
    //put your code here
    
    function getSubjectPerId($sub_id)
    {
        $this->db->where('s_id', $sub_id);
        $q = $this->db->get('c_subjects');
        return $q->row();
    }
    
    function getSubjectOfferedPerSem($sem)
    {
        $this->db->join('c_subjects', 'c_section.sec_sub_id = c_subjects.s_id','left');
        $this->db->where('sec_sem', $sem);
        $q = $this->db->get('c_section');
        return $q->result();
    }
            
    function getStudentsPerSectionRaw($sec_id, $sem)
    {
        $this->db->join('profile','profile_students_c_load.cl_user_id = profile.user_id','left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        $this->db->join('c_section', 'profile_students_c_load.cl_section = c_section.sec_id', 'left');
        $this->db->where('sec_sem', $sem);
        $this->db->where('cl_section', $sec_id);
        $this->db->order_by('lastname','ASC');
        $this->db->order_by('firstname','ASC');
        $this->db->group_by('profile.user_id');
        $q = $this->db->get('profile_students_c_load');
        return $q;
    }
            
    function getStudentsPerSectionPrint($sec_id, $sem)
    {
        $this->db->join('profile','profile_students_c_load.cl_user_id = profile.user_id','left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        $this->db->join('c_section', 'profile_students_c_load.cl_section = c_section.sec_id', 'left');
        $this->db->where('sec_sem', $sem);
        $this->db->where('cl_section', $sec_id);
        $this->db->order_by('lastname','ASC');
        $this->db->order_by('firstname','ASC');
        $this->db->group_by('profile.user_id');
        $q = $this->db->get('profile_students_c_load');
        return $q->result();
    }
            
    function getStudentsPerSection($sec_id, $sem)
    {
        //$this->db->join('profile','profile_students_c_load.cl_user_id = profile.user_id','left');
        $this->db->join('c_section', 'profile_students_c_load.cl_section = c_section.sec_id', 'left');
        ($sem!=NULL?$this->db->where('sec_sem', $sem):'');
        $this->db->where('cl_section', $sec_id);
        $q = $this->db->get('profile_students_c_load', $sec_id);
        return $q;
    }
    function searchSubjectResult($value)
    {
        $this->db->select('*');
        $this->db->from('c_subjects');
        $this->db->like('LOWER(s_desc_title)', strtolower($value), 'both');
        $this->db->or_like('LOWER(sub_code)', strtolower($value), 'both');
        $this->db->limit(20);
        $query = $this->db->get();
        return $query;
    }
    
    function editSection($sec_id, $details)
    {
        $this->db->where('sec_id', $sec_id);
        $this->db->update('c_section', $details);
        return TRUE;
    }
            
    function deleteSection($sec_id)
    {
        $this->db->where('sec_id', $sec_id);
        if($this->db->delete('c_section')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getSectionPerSubject($sub_id)
    {
        $this->db->join('c_courses', 'c_section.course_id = c_courses.course_id','right');
        $this->db->join('c_subjects', 'c_section.sec_sub_id = c_subjects.s_id','left');
        $this->db->where('sec_sub_id', $sub_id);
        $q = $this->db->get('c_section');
        return $q->result();
    }
    
    function getAssignSubject($teacher_id)
    {
        $this->db->where('faculty_id',$teacher_id);
        $q = $this->db->get('c_schedule');
        return $q->result();
    }
    
    function saveAssignedSubject($array, $spc_id, $section_id, $teacher_id)
    {
        $this->db->where('spc_id', $spc_id);
        $this->db->where('section_id', $section_id);
        $this->db->where('faculty_id', $teacher_id);
        $q = $this->db->get('c_faculty_assignment');
        if($q->num_rows()>0):
            $this->db->where('spc_id', $spc_id);
            $this->db->where('section_id', $section_id);
            $this->db->where('faculty_id', $teacher_id);
            if($this->db->update('c_faculty_assignment', $array)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:    
            if($this->db->insert('c_faculty_assignment', $array)):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }
    
    function assignSubject($teacher_id, $sched_code)
    {
        $this->db->where('sched_gcode', $sched_code);
        $this->db->where('faculty_id','');
        $q = $this->db->get('c_schedule');
        if($q->num_rows()>0):
            $this->db->where('sched_gcode', $sched_code);
            $this->db->update('c_schedule', array('faculty_id' => $teacher_id));
            return TRUE;
        else:
            return FALSE;
        endif;
    }
            
    function searchSubject($value, $course_id)
    {
        $this->db->select('*');
        $this->db->from('c_subjects');
        $this->db->join('c_subjects_per_course', 'c_subjects.s_id = c_subjects_per_course.spc_sub_id','left');
        $this->db->join('c_section', 'c_subjects.s_id = c_section.sec_sub_id', 'inner');
        $this->db->join('c_schedule', 'c_subjects_per_course.spc_course_id = c_schedule.cs_spc_id','left');
        $this->db->where('c_subjects_per_course.spc_course_id', $course_id);
        $this->db->like('LOWER(s_desc_title)', strtolower($value), 'both');
        $this->db->or_like('LOWER(sub_code)', strtolower($value), 'both');
        $this->db->group_by('c_section.sec_id');
        $query = $this->db->get();
        return $query->result();
    }
    
    function getSectionById($id=NULL)
    {
        $this->db->where('sec_id', $id);
        $q = $this->db->get('c_section');
        return $q->row();
    }
    
    function getSubjectsOffered($g=NULL)
    {
        $this->db->where('sec_sub_id', $g);
        $q = $this->db->get('c_section');
        return $q;
    }
    
    
    function addSection($details)
    {
         if($this->db->insert('c_section', $details)):
             return TRUE;
         else:
             return FALSE;
         endif;
    }
    
    function lastSecId()
    {
        $this->db->order_by('sec_id', 'DESC');
        $q = $this->db->get('c_section');
        return $q->row();
    }
    
    function approveLoad($adm_id)
    {
        $this->db->where('cl_adm_id', $adm_id);
        if($this->db->update('profile_students_c_load', array('is_final' => 1))):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
            
    function getLoadedSubject($adm)
    {
        $this->db->where('cl_adm_id', $adm);
        $this->db->join('c_subjects','c_subjects.s_id = profile_students_c_load.cl_sub_id', 'inner');
        $this->db->join('c_section', 'profile_students_c_load.cl_section = c_section.sec_id', 'inner');
        $q = $this->db->get('profile_students_c_load');
        return $q->result();
    }
    
    
    function checkIfSubjectExist($student_id, $subject_id)
    {
        $this->db->where('cl_user_id', $student_id);
        $this->db->where('cl_sub_id', $subject_id);
        $q = $this->db->get('profile_students_c_load');
        if($q->num_rows()>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function removeLoadedSubject($subject_id, $adm_id)
    {
        $this->db->where('cl_sub_id', $subject_id);
        $this->db->where('cl_adm_id', $adm_id);
        $q = $this->db->get('profile_students_c_load');
        if($q->num_rows()>0):
            $this->db->where('cl_sub_id', $subject_id);
            $this->db->where('cl_adm_id', $adm_id);
            if($this->db->delete('profile_students_c_load')):
                return true;
            endif;
        else:
            return FALSE;
        endif;    
    }
    
    function saveLoadedSubject($subject_id, $adm_id, $load)
    {
        $this->db->where('cl_sub_id', $subject_id);
        $this->db->where('cl_adm_id', $adm_id);
        $q = $this->db->get('profile_students_c_load');
        if($q->num_rows()==0):
            if($this->db->insert('profile_students_c_load', $load)):
                return true;
            endif;
        else:
            return FALSE;
        endif;    
    }
    
    function prerequisteCheck($st_id, $subject)
    {
        $this->db->where('cl_user_id', $st_id);
        $this->db->where('sub_code', $subject);
        $this->db->join('c_subjects','c_subjects.s_id = profile_students_c_load.cl_sub_id', 'left');
        $q = $this->db->get('profile_students_c_load');
        if($q->num_rows()>0):
            return TRUE;
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
    
    function getSubjectsPerCourse($course)
    {
       
        $year = $this->session->userdata('school_year');
       
        
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->from('c_subjects_per_course');
        $this->db->join('c_subjects','c_subjects.s_id = c_subjects_per_course.spc_sub_id', 'left');
        $this->db->where('spc_course_id', $course);
        $q = $this->db->get();
        if($q->num_rows()>0):
            return $q->result();
        else:
            return FALSE;
        endif;
    }
    
    function getCollegeSubjects($sem)
    {
        ($sem==NULL?"":$this->db->where('spc_sem_id', $sem));
        $this->db->join('c_subjects_per_course','c_subjects.s_id = c_subjects_per_course.spc_sub_id', 'left');
        $q = $this->db->get('c_subjects');
        return $q;
    }
    
    function collegeSubjects($limit=NULL, $offset=NULL)
    {
        if($limit!=NULL||$offset=NULL){
                $this->db->limit($limit, $offset);	
        }
        $q = $this->db->get('c_subjects');
        return $q;
    }
    
    function addCollegeSubject($subjects, $sDesc, $sCode)
    {
        $this->db->where('s_desc_title', $sDesc);
        $this->db->where('sub_code', $sCode);
        $q = $this->db->get('c_subjects');
        if($q->num_rows()>0):
            return FALSE;
        else:
            if($this->db->insert('c_subjects', $subjects)):
                return TRUE;
            else:
                return FALSE;
            endif;
            
        endif;
    }
    
    function editCollegeSubject($subjects, $sDesc, $sCode,$s_id)
    {
        $this->db->where('s_id', $s_id);
        $query = $this->db->get('c_subjects');
        if($query->num_rows()>0):
                $this->db->where('s_id', $s_id);
                if($this->db->update('c_subjects', $subjects)):
                    return TRUE;
                else:
                    return FALSE;
                endif;
        else:
            return FALSE;
        endif;
    }
    
}
