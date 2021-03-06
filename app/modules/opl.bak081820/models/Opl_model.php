<?php

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
class Opl_model extends MX_Controller {
    //put your code here
    
    function getNumDiscussions($grade_level, $subject, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('dis_grade_id', $grade_level);
        $this->db->where('dis_subject_id', $subject);
        $q = $this->db->get('opl_discussion');
        return $q->num_rows();
        
    }
    
    function getNumTask($grade_level, $section, $subject, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('task_grade_id', $grade_level);
        $this->db->where('task_section_id', $section);
        $this->db->where('task_subject_id', $subject);
        $q = $this->db->get('opl_tasks');
        return $q->num_rows();
    }
    
    function searchTasks($search, $grade, $section, $subject){
        return $this->db->where('task_grade_id', $grade)
                        ->where('task_section_id', $section)
                        ->where('task_subject_id', $subject)
                        ->where('(task_title LIKE "%'.$search.'%" OR task_details LIKE "%'.$search.'%")')
                        ->order_by('task_start_time', 'DESC')
                        ->get('opl_tasks')
                        ->result();
    }

    function searchDiscussion($search, $grade, $section, $subject){
        return $this->db->where('dis_grade_id', $grade)
                    ->where('dis_subject_id', $subject)
                    ->where('(dis_title LIKE "%'.$search.'%" OR dis_details LIKE "%'.$search.'%")')
                    ->order_by('dis_start_date', 'DESC')
                    ->get('opl_discussion')
                    ->result();
    }

    function getRubricMarkings($st_id, $ref_id, $rcid, $school_year, $rdid)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->join('opl_rubric_description','opl_rubric_student.sr_rdid = opl_rubric_description.rdid', 'left');
        $this->db->where('sr_stid', $st_id);
        $this->db->where('sr_ref_id', $ref_id);
        $this->db->where('rd_rcid', $rcid);
        ($rdid!=NULL?$this->db->where('sr_rdid', $rdid):'');
        $q = $this->db->get('opl_rubric_student');
        return $q->row();

    }

    function show_events()
    {
      $this->db->select('*');
      $this->db->from('opl_tasks');
      $this->db->join('subjects', 'opl_tasks.task_subject_id = subjects.subject_id', 'left');
      $query = $this->db->get();
      return $query->result();
   }

   function show_cal_events()
   {
      $this->db->select('*');
      $this->db->from('calendar_events');
      $query = $this->db->get();
      return $query->result();
   }
   
   function show_discussion()
   {
      $this->db->select('*');
      $this->db->from('opl_discussion');
      $this->db->join('subjects', 'opl_discussion.dis_subject_id = subjects.subject_id', 'left');
      $q = $this->db->get();
      return $q->result();
   }

    function saveRubricMarkings($rubricArray, $st_id,$ref_id,$rcid, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->join('opl_rubric_description','opl_rubric_student.sr_rdid = opl_rubric_description.rdid', 'left');
        $this->db->where('sr_stid', $st_id);
        $this->db->where('sr_ref_id', $ref_id);
        $this->db->where('rd_rcid', $rcid);
        $q = $this->db->get('opl_rubric_student');
        if($q->num_rows()==0):
            if($this->db->insert('opl_rubric_student', $rubricArray)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            $this->db->where('srid', $q->row()->srid);
            if($this->db->update('opl_rubric_student', $rubricArray)):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }

    function searchARubric($value, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->like('LOWER(ru_alias)', strtolower($value));
        $this->db->limit(10);
        return $this->db->get('opl_rubric_info')->result();

    }

    function getRubricScaleDescription($rd_rcid, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('rd_rcid', $rd_rcid);
        $this->db->order_by('rd_scale','ASC');
        $q = $this->db->get('opl_rubric_description');
        return $q;
    }

    function getRubricCriteria($ruid, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->join('opl_rubric_info','opl_rubric_criteria.rc_ruid = opl_rubric_info.ruid', 'left');
        $this->db->where('rc_ruid', $ruid);
        $q = $this->db->get('opl_rubric_criteria');
        return $q;

    }

    function getRubricList($author, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('ru_auth_id', $author);
        return $this->db->get('opl_rubric_info')->result();
    }

    function saveDescription($rdDetails, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($this->db->insert('opl_rubric_description', $rdDetails)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function deleteCriteria($rcid, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('rcid',$rcid);
        if($this->db->delete('opl_rubric_criteria')):
            $this->db->where('rd_rcid', $rcid);
            if($this->db->delete('opl_rubric_description')):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            return FALSE;
        endif;
    }

    function saveCriteria($criteriaDetails, $school_year, $isEdit, $rcid, $ruid, $criteria)
    {
        $this->db = $this->eskwela->db($school_year);
        if($isEdit==0):
            $this->db->where('rc_ruid', $ruid);
            $this->db->where('rc_criteria', $criteria);
            $q = $this->db->get('opl_rubric_criteria');

            if($q->num_rows()==0):
                if($this->db->insert('opl_rubric_criteria', $criteriaDetails)):
                    return TRUE;
                else:
                    return FALSE;
                endif;
            else:

                $this->db->where('rc_ruid', $ruid);
                $this->db->where('rc_criteria', $criteria);
                if($this->db->update('opl_rubric_criteria', $criteriaDetails)):
                    return TRUE;
                else:
                    return FALSE;
                endif;

            endif;
        else:
            $this->db->where('rcid', $rcid);
            if($this->db->update('opl_rubric_criteria', $criteriaDetails)):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }

    function getRubricInfo($code, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('ruid', $code);
        return $this->db->get('opl_rubric_info')->row();
    }

    function addRubric($details, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($this->db->insert('opl_rubric_info', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    //comments

    function getCommentById($com_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('com_id', $com_id);
        return $this->db->get('opl_comments')->row();
    }

    function getReply($com_to,  $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('com_isReply', 1);
        $this->db->where('com_to', $com_to);
        $this->db->order_by('com_id','ASC');
        return $this->db->get('opl_comments')->result();
    }

    function getComments($com_to,  $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('com_isReply', 0);
        $this->db->where('com_to', $com_to);
        $this->db->order_by('com_id','DESC');
        return $this->db->get('opl_comments')->result();
    }

/*    start of grading system  */

    function saveAssessment($details, $school_year) {
        $this->db = $this->eskwela->db($school_year);
        $this->db->insert('gs_assessment', $details);
        $runScript = $this->db->last_query();
        Modules::run('web_sync/saveRunScript', $runScript, $school_year);

    }

    function getSettings($school_year = NULL) {
        if ($school_year == NULL):
            $school_year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('school_year', $school_year);
        $query = $this->db->get('gs_settings');
        return $query->row();
    }


    function getAssessCategory($subject_id, $school_year = NULL) {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->from('gs_asses_category');
        $this->db->join('gs_component', 'gs_asses_category.component_id = gs_component.id', 'left');
        if ($subject_id != NULL):
            $this->db->where('subject_id', $subject_id);
        endif;
        if ($school_year != NULL):
            $this->db->where('school_year', $school_year);
        endif;

        $query = $this->db->get();
        return $query->result();
    }

    function getAssessCategoryWithoutPreTest($dept = NULL) {
        $this->db->select('*');
        $this->db->from('gs_asses_category');
        if ($dept != NULL):
            $this->db->where('department', $dept);
        endif;
        $query = $this->db->get();
        return $query->result();
    }

/*    end of grading system  */

    function getDiscussionByCode($code){
        return $this->db->where('dis_sys_code', $code)
                        ->get('opl_discussion');
    }

    function fetchTaskByCode($code){
        return $this->db->where('task_code', $code)
                        ->get('opl_tasks');
    }

    function deleteDiscussion($id){
        $this->db->where('dis_sys_code', $id)
                    ->delete('opl_discussion');
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return $this->db->error();
        endif;
    }

    function editDiscussion($id, $data){
        $this->db->where('dis_sys_code', $id)
                    ->update('opl_discussion', $data);
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return $this->db->error();
        endif;
    }

    function deleteUnit($id){
        $this->db->where('ou_opl_code', $id)
                    ->delete('opl_units');
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return $this->db->error();
        endif;
    }

    function editUnit($id, $data)
    {
        $this->db->where('ou_opl_code', $id)
                    ->update('opl_units', $data);

        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return $this->db->error();
        endif;
    }

    function deleteTasks($code)
    {
        $this->db->where('task_code', $code)
                ->delete('opl_tasks');
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return $this->db->error();
        endif;
    }

    function updateTasks($code, $data){
        $this->db->where('task_code', $code)
                ->update('opl_tasks', $data);
        if($this->db->affected_rows() != 0){
            return TRUE;
        }else{
            return $this->db->error();
        }
    }

    function getDiscussionDetails($discuss_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->join('profile_employee', 'opl_discussion.dis_author_id = profile_employee.employee_id');
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id');
        $this->db->where('dis_sys_code', $discuss_id);
        return $this->db->get('opl_discussion')->row();

    }

    function getPost()
    {
        $this->db->join('profile_employee', 'opl_posts.op_owner_id = profile_employee.employee_id');
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id');
        $this->db->order_by('op_timestamp','DESC');
        $this->db->limit(10);
        $post = $this->db->get('opl_posts');
        return $post->result();
    }

    function submitPost($details, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        if($this->db->insert('opl_posts', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
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

    function sendComment($details)
    {
        if($this->db->insert('opl_comments', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function getTaskDetails($task_code, $school_year, $isStudent = NULL)
    {

        $this->db = $this->eskwela->db($school_year);
        $this->db->where('task_code', $task_code);
        ($isStudent!=NULL?$this->db->where('task_start_time <=', date('Y-m-d G:i:s')):'');
        $this->db->join('profile_employee', 'opl_tasks.task_author_id = profile_employee.employee_id','left');
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id','left');
        $this->db->join('opl_task_type', 'opl_tasks.task_type = opl_task_type.tt_id','left');
        $this->db->order_by('task_start_time','DESC');
        $q = $this->db->get('opl_tasks');
        return $q->row();
    }

    function getUnitDetails($code, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->join('subjects','opl_units.ou_subject_id = subjects.subject_id','left');
        $this->db->where('ou_opl_code', $code);
        $q = $this->db->get('opl_units');
        return $q->row();
    }


    function getUnits($grade_level, $subject, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('ou_subject_id', $subject);
        $this->db->where('ou_grade_level_id', $grade_level);
        $q = $this->db->get('opl_units');
        return $q->result();
    }

    function saveUnit($details, $unitTitle, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('ou_unit_title', $unitTitle);
        $q = $this->db->get('opl_units')->num_rows();
        if($q == 0):
            if($this->db->insert('opl_units', $details)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript, $school_year);
                return TRUE;
            else:
                return 0;
            endif;
        else:
            return 2;
        endif;
    }

    function getTask($grade_level, $section, $subject, $school_year, $isStudent = NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('task_grade_id', $grade_level);
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

    function getStudent($id,$year)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select("*");
        $this->db->from("profile");
        $this->db->join('profile_students','profile.user_id = profile_students.user_id');
        // $this->db->
        $this->db->where('st_id', $id);
        $q = $this->db->get();
        return $q->result();
    }
}
