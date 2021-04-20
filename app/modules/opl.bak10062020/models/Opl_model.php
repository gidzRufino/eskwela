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
    
    function resetStudentAnswers($stid, $code){
        $gs = $this->db->where('st_id', $stid)
                ->where('assess_id', $code)
                ->get('gs_raw_score');
        if($gs->num_rows() != 0):
            $this->db->where('ts_task_id', $code)
                ->where('ts_submitted_by', $stid)
                ->delete('opl_task_submitted');
            if($this->db->affected_rows() != 0):
                $this->db->where('st_id', $stid)
                    ->where('assess_id', $code)
                    ->delete('gs_raw_score');
                if($this->db->affected_rows() != 0):
                    return "You have successfully resetted the students answers";
                else:
                    return "Something went wrong while trying to reset raw score. Please remove if response has already been deleted.";
                endif;
            else:
                return "Something went wrong while trying to reset student response. Please try again later.";
            endif;
        else:
            return "This submitted task has already been invalidated, please inform your I.T";
        endif;
    }
    
    function addReader($id, $discuss_id){
        $id = strval($id);
        $check = $this->db->where('dis_sys_code = '.$discuss_id.' AND FIND_IN_SET('.$id.', dis_read_by)')
                ->get('opl_discussion');
        if($check->num_rows() == 0):
            $fill = $this->db->where('dis_sys_code', $discuss_id)
                ->get('opl_discussion');
            $fill = $fill->row();
            if(strlen($fill->dis_read_by) != 0):
                $id = $fill->dis_read_by.",".$id;
            endif;
            $this->db->where('dis_sys_code', $discuss_id)
                ->update('opl_discussion', array('dis_read_by' => $id));
        endif;
    }
    
    function highlightPost($id, $type){
        $order = $this->db->where('op_highlights', 1)
                ->order_by('op_order', 'DESC')
                ->limit(1)
                ->get('opl_posts');
        if($order->num_rows() != 0):
            if($type != 0):
                $next = $order->row()->op_order + 1;
                $this->db->where('op_id', $id)
                        ->update('opl_posts',
                                array(
                                   'op_highlights'  =>  $type,
                                    'op_order'      =>  $next
                                ));
            else:
                $this->db->where('op_id', $id)
                        ->update('opl_posts',
                                array(
                                   'op_highlights'  =>  $type,
                                    'op_order'      =>  NULL
                                ));
            endif;
        else:
            $this->db->where('op_id', $id)
                ->update('opl_posts', array(
                    'op_highlights' =>  $type,
                    'op_order'      =>  ($type != 0) ? 0 : NULL
                ));
        endif;
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getSubjectAssigned($username, $search){
        $q = $this->db->join('subjects', 'faculty_assign.subject_id = subjects.subject_id')
                ->where('faculty_assign.faculty_id', $username);
        if($search != NULL):
            $q->where('esk_subjects.subject LIKE "%'.$search.'%" OR esk_subjects.short_code "%'.$search.'%"');
        endif;
        return $q->get('faculty_assign');
    }
    
    function deletePost($post_id){
        $this->db->where('op_id', $post_id)
                ->delete('opl_posts');
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getClasses($username, $search){
        $q = $this->db->join('c_subjects', 'c_section.sec_sub_id = c_subjects.s_id')
                ->order_by('c_subjects.s_id', 'ASC');
        if($username != NULL):
            $q->join('c_schedule', 'c_section.sec_id = c_schedule.section_id')
                ->where('c_schedule.faculty_id', $username)
                ->group_by('c_section.sec_id');
        endif;
        if($search != NULL):
            $q->where('esk_c_subjects.s_desc_title LIKE "%'.$search.'%" OR esk_c_subjects.sub_code LIKE "%'.$search.'%" OR esk_c_section.section LIKE "%'.$search.'%" OR esk_c_section.sec_id LIKE "%'.$search.'%"');
        endif;
        return $q->get('c_section');
    }
    
    function getSection($username, $search){
        $q = $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id')
                ->order_by('grade_level.order', 'ASC');
        if($username != NULL):
            $q->join('faculty_assign', 'section.section_id = faculty_assign.section_id')
                ->where('faculty_id', $username)
                ->group_by('section.section_id');
        endif;
        if($search != NULL):
            $this->db->where('esk_section.section LIKE "%'.$search.'%" OR esk_grade_level.level LIKE "%'.$search.'%"');
        endif;
        return $q->get('section');
    }

    function uploadFile($code, $data, $type){
        switch($type):
            case 'task':
                $this->db->where('task_code', $code)
                        ->update('opl_tasks', $data);
            break;
            case 'unit':
                $this->db->where('ou_opl_code', $code)
                            ->update('opl_units', $data);
            break;
            case 'discussion':
                $this->db->where('dis_sys_code', $code)
                            ->update('opl_discussion', $data);
            break;
        endswitch;
        if($this->db->affected_rows() != 0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
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


    function deleteRubric($code)
    {
        $this->db->where('ruid', $code)
                ->delete('opl_rubric_info');
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
        $this->db = $this->eskwela->db(($school_year != NULL) ? $school_year : $this->session->school_year);
        $this->db->join('profile_employee', 'opl_discussion.dis_author_id = profile_employee.employee_id');
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id');
        $this->db->where('dis_sys_code', $discuss_id);
        return $this->db->get('opl_discussion')->row();

    }

    function getPost($id, $grade = NULL, $section = NULL, $subject = NULL)
    {
        if(!$this->session->isOplAdmin):
            $this->db->select('*, profile_employee.employee_id AS empid');
            $this->db->join('profile_employee', 'opl_posts.op_owner_id = profile_employee.employee_id', 'left');
            $this->db->join('profile', 'profile_employee.user_id = profile.user_id', 'left');
            $this->db->where('opl_posts.op_owner_id', $id);
            if($grade == NULL):
                $this->db->or_where('op_target_type', 0);
                $this->db->or_where('op_target_type', 1);
            endif;
            if($grade != NULL):
                $this->db->or_where('(op_target_type = 3 AND FIND_IN_SET("'.$grade.'", op_target_ids))');
            endif;
            if($grade != NULL && $section != NULL):
                $this->db->or_where('(op_target_type = 4 AND FIND_IN_SET("'.$grade.'-'.$section.'", op_target_ids))');
                $this->db->or_where('(op_target_type = 5 AND op_target_ids LIKE("%'.$grade.'-'.$section.'%"))');
            endif;
            if($subject != NULL):
                $this->db->or_where('(op_target_type = 5 AND FIND_IN_SET("'.$grade.'-'.$section.'-'.$subject.'", op_target_ids))');
            endif;
            $this->db->group_by('op_id');
            $this->db->order_by('op_highlights', 'DESC');
            $this->db->order_by('op_order', 'ASC');
            $this->db->order_by('op_timestamp','DESC');
            $this->db->limit(10);
            $post = $this->db->get('opl_posts');
        else:
            $this->db->join('profile_employee', 'opl_posts.op_owner_id = profile_employee.employee_id');
            $this->db->join('profile', 'profile_employee.user_id = profile.user_id');
            $this->db->group_by('op_id');
            $this->db->order_by('op_highlights', 'DESC');
            $this->db->order_by('op_order', 'ASC');
            $this->db->order_by('op_timestamp', 'DESC');
            $this->db->limit(10);
            $post = $this->db->get('opl_posts');
        endif;
//        echo $this->db->last_query();
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

        $this->db = $this->eskwela->db(($school_year != NULL) ? $school_year : $this->session->school_year);
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
        $this->db = $this->eskwela->db($school_year != NULL ? $school_year : $this->session->school_year);
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
