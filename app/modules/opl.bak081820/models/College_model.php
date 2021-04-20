<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Opl_models
 *
 * @author genesisrufino
 */
class College_model extends MX_Controller {
    //put your code here

    function searchTasks($search, $grade, $section, $subject){
        return $this->db->where('task_grade_id', $grade)
                        ->where('task_section_id', $section)
                        ->where('task_subject_id', $subject)
                        ->where('(task_title LIKE "%'.$search.'%" OR task_details LIKE "%'.$search.'%")')
                        ->order_by('task_start_time', 'DESC')
                        ->get('opl_tasks')
                        ->result();
    }
    
    function addDiscussion($discussionDetails, $code, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($code==NULL):
            if($this->db->insert('opl_discussion', $discussionDetails)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            $this->db->where('dis_sys_code', $code);
            if($this->db->update('opl_discussion', $discussionDetails)):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }

    function saveAssessment($details, $school_year) {
        $this->db = $this->eskwela->db($school_year);
        $this->db->insert('gs_assessment', $details);
        $runScript = $this->db->last_query();
        Modules::run('web_sync/saveRunScript', $runScript, $school_year);
        
    }

    function addTask($details, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($this->db->insert('opl_tasks', $details)):
            $runScript = $this->db->last_query();
            Modules::run('web_sync/saveRunScript', $runScript, $school_year);
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function fetchLesson($subject_id,$grade_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('ou_subject_id', $subject_id);
        // $this->db->where('ou_grade_level_id', $grade_id);
        return $this->db->get('opl_units')->result();
    }
          
    function getStudentsPerSectionRaw($sec_id, $sem, $school_year)
    {
        $this->db = $this->eskwela->db(($school_year==NULL?$this->session->school_year:$school_year));
        $this->db->select('*');
        $this->db->select('profile_students_c_admission.school_year as sy');
        $this->db->select('profile_students_c_admission.course_id as c_id');
        $this->db->join('profile','profile_students_c_load.cl_user_id = profile.user_id','left');
        $this->db->join('profile_address_info','profile.add_id = profile_address_info.address_id','left');
        $this->db->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $this->db->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $this->db->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $this->db->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        $this->db->join('c_section', 'profile_students_c_load.cl_section = c_section.sec_id', 'left');
        $this->db->where('sec_sem', $sem);
        $this->db->where('cl_section', $sec_id);
        $this->db->where('is_final', 1);
        $this->db->order_by('lastname','ASC');
        $this->db->order_by('firstname','ASC');
        $this->db->group_by('profile.user_id');
        $q = $this->db->get('profile_students_c_load');
        return $q;
    }
    
    function getTeacherAssignment($teacher_id, $semester, $school_year)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $q = $this->db->join('c_section', 'c_schedule.section_id = c_section.sec_id', 'left')
                        ->join('c_subjects', 'c_section.sec_sub_id = c_subjects.s_id', 'left')
                        ->where('semester', $semester)
                        ->where('faculty_id', $teacher_id)
                        ->group_by('sched_gcode')
                        ->get('c_schedule');
        return $q->result();
    }
    
    function getTask($section, $subject, $school_year, $isStudent = NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('task_section_id', $section);
        $this->db->where('task_subject_id', $subject);
        ($isStudent!=NULL?$this->db->where('task_start_time <=', date('Y-m-d G:i:s')):'');
        ($isStudent!=NULL?$this->db->where('task_end_time >', date('Y-m-d G:i:s')):'');
        $this->db->join('profile_employee', 'opl_tasks.task_author_id = profile_employee.employee_id','left');
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id','left');
        $this->db->join('opl_task_type', 'opl_tasks.task_type = opl_task_type.tt_id','left');
        $this->db->order_by('task_start_time','DESC');
        $this->db->limit(15);
        $q = $this->db->get('opl_tasks');
        return $q->result();
    }
    
    function classDetails($subject_id, $semester, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        
    }
    
    function saveResponse($details, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($this->db->insert('opl_task_submitted', $details)):
            $runScript = $this->db->last_query();
            Modules::run('web_sync/saveRunScript', $runScript, $school_year);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function createResponse($details, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($this->db->insert('opl_task_submitted', $details)):
            $runScript = $this->db->last_query();
            Modules::run('web_sync/saveRunScript', $runScript, $school_year);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getSubjectList($grade_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('grade_level_id', $grade_id);
        $this->db->join('subjects','subjects_settings.sub_id = subjects.subject_id','left');
        return $this->db->get('subjects_settings')->result();
    }
    
}
