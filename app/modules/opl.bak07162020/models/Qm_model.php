<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Qm_model
 *
 * @author genesisrufino
 */
class Qm_model extends CI_Model {
    //put your code here
    
    function removeQuizItems($qDetails, $quiz_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('qi_sys_code',$quiz_id);
        if($this->db->update('opl_qm_quiz_items', $qDetails)):
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function saveRawScore($rawScoreDetails, $assess_id, $st_id,  $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('assess_id', $assess_id);
        $this->db->where('st_id', $st_id);
        $q = $this->db->get('gs_raw_score');
        if($q->num_rows()==0):
            if($this->db->insert('gs_raw_score', $rawScoreDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript, $school_year);
                return 1;
            else:
                return 0;
            endif;
        else:
            $this->db->where('assess_id', $assess_id);
            $this->db->where('st_id', $st_id);
            if($this->db->update('gs_raw_score', $rawScoreDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript, $school_year);
                return 2;
            else:
                return 0;
            endif;
            
        endif;
        
    }
    
    function getRawScore($assess_id, $st_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('assess_id', $assess_id);
        $this->db->where('st_id', $st_id);
       // return ($this->db->get('gs_raw_score')->row()?$this->db->get('gs_raw_score')->row()->raw_score:0);
        return $this->db->get('gs_raw_score')->row();
    }
    
    function checkAnswer($q_code, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('sys_code', $q_code);
        return $this->db->get('opl_qm_qq')->row();
    }
    
    function searchAQuiz($value, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->like('LOWER(qi_title)', strtolower($value));
        return $this->db->get('opl_qm_quiz_items')->result();
    }
    
    function getSingleQuestion($sys_code, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('sys_code', $sys_code);
        return $this->db->get('opl_qm_qq')->row();
        
    }
    
    function searchQuestions($value, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->like('LOWER(plain_question)', strtolower($value));
        return $this->db->get('opl_qm_qq')->result();
        
    }
            
    function getQuestionItems($itemCode, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('sys_code', $itemCode);
        return $this->db->get('opl_qm_qq')->row();
    }
            
    function getQuizDetails($quizCode, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('qi_sys_code', $quizCode);
        return $this->db->get('opl_qm_quiz_items')->row();
    }
    
    function deleteQuiz($quizCode, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('qi_sys_code', $quizCode);
        if($this->db->delete('opl_qm_quiz_items')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getAllQuizzes($teacher_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('qi_created_by', $teacher_id);
        $q = $this->db->get('opl_qm_quiz_items');
        return $q->result();
        
    }
    
    function deleteQuestion($sys_code, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('sys_code', $sys_code);
        if($this->db->delete('opl_qm_qq')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function questionsList($teachers_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->join('opl_qm_type', 'opl_qm_qq.qq_qt_id = opl_qm_type.qm_type_id');
        return $this->db->where('created_by', $teachers_id)
                 ->get('opl_qm_qq')->result();
    }
    
    function getSkillsList()
    {
        $q = $this->db->get('opl_qm_skills');
        return $q->result();
    }
    
    function getSkills($qq_id)
    {
        $this->db->where('qq_id', $qq_id);
        $q = $this->db->get('opl_qm_qq');
        return $q->row();
    }
    
    function getTags($qq_id)
    {
        $this->db->where('qci_qq_id', $qq_id);
        $q = $this->db->get('opl_qm_cat_items');
        return $q->row();
    }
    
    function saveSkills($q_id, $details)
    {
        $this->db->where('qq_id', $q_id);
        $this->db->update('opl_qm_qq', $details);
    }
    
    function saveTags($q_id, $details)
    {
        $this->db->where('qci_qq_id', $q_id);
        $q = $this->db->get('opl_qm_cat_items');
        if($q->num_rows()>0):
            $this->db->where('qci_qq_id', $q_id);
            $this->db->update('opl_qm_cat_items', $details);
        else:
            $this->db->insert('opl_qm_cat_items', $details);
        endif;
    }
    
    function getAccessLevel($user_id)
    {
        $this->db->where('opl_qm_ua_user_id', $user_id);
        $q = $this->db->get('opl_qm_user_access');
        if(!empty($q->row())):
            return $q->row()->opl_qm_ua_access_id;
        else:
            return 4;
        endif;
    }
    
    function getAllQuestions($cat)
    {
        $q = $this->db->get('opl_qm_qq');
        return $q->result();
    }
    
    function publishQuiz($data, $id)
    {
        $this->db->where('qi_id', $id);
        $this->db->update('opl_qm_quiz_items', $data);
        return;
    }
//    
//    function checkAnswer($qq_id, $ans)
//    {
//        $this->db->where('qs_qq_id', $qq_id);
//        $this->db->where('ans_flag', 1);
//        $this->db->where('LOWER(qs_selection)', strtolower($ans));
//        $q = $this->db->get('opl_qm_quiz_selection');
//        if($q->num_rows()>0):
//            $result = array('result'=> TRUE, 'id'=> $qq_id);
//        else:
//            $result = array('result'=> FALSE, 'id'=> $qq_id);
//        endif;
//        return json_encode($result);
//    }
    
    function getQuizAnswer($qq_id, $flag)
    {
        $this->db->where('qs_qq_id', $qq_id);
        $this->db->where('ans_flag', $flag);
        $q = $this->db->get('opl_qm_quiz_selection');
        return $q;
    }
            
    function getQuizQuestions($item_id)
    {
        $this->db->where('qq_id', $item_id);
        $q = $this->db->get('opl_qm_qq');
        return $q->row();
    }
    
    function saveAnswer($answer)
    {
        $this->db->insert('opl_qm_quiz_selection',$answer);
        return;
    }
    
    function updateQuizItems($qi_id, $details)
    {
        $this->db->where('qi_id', $qi_id);
        $this->db->update('opl_qm_quiz_items', $details);
        return;
    }
    function checkQI($id)
    {
        $this->db->select('qi_id');
        $this->db->select('qi_qq_ids');
        $this->db->from('opl_qm_quiz_items');
        $this->db->where('qi_id', $id);
        $q = $this->db->get();
        if($q->num_rows()>0):
            $result = array('exist'=>TRUE, 'qq_ids'=>$q->row()->qi_qq_ids);
        else:
            $result = array('exist'=>FALSE);
        endif;
        return json_encode($result);
            
    }

    function addQuestions($array)
    {
        if($this->db->insert('opl_qm_qq', $array)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getQQids($systemCode)
    {
        $this->db->where('qi_sys_code', $systemCode);
        $q = $this->db->get('opl_qm_quiz_items')->row();
        return $q;
    }
    
    function addQuiz($details)
    {
        if($this->db->insert('opl_qm_quiz_items', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function updateQuiz($details, $systemCode)
    {
        $this->db->where('qi_sys_code', $systemCode);
        $this->db->update('opl_qm_quiz_items', $details);
    }
            
    
    function getSpecificQuiz($item_id)
    {
        $this->db->where('qi_id', $item_id);
        $q = $this->db->get('opl_qm_quiz_items');
        return $q->row();
    }
    
    function getQuizList($id, $option)
    {
        
        if($option!=NULL):
            $this->db->where('qi_is_public', $option);
        else:
            $this->db->where('qi_created_by', $id);
        endif;
        $q = $this->db->get('opl_qm_quiz_items');
        return $q;
    }
    function getQuizItem($id)
    {
        $this->db->where('qi_created_by', $id);
        $this->db->order_by('qi_date_created', 'ASC');
        $q = $this->db->get('opl_qm_quiz_items');
        return $q->result();
    }
    
    function saveQuizItem($data)
    {
        if($this->db->insert('opl_qm_quiz_items', $data)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function saveQCI($data)
    {
        $this->db->insert('opl_qm_cat_items', $data);
        return $this->db->insert_id();
    }
    
    function saveCategory($cat, $data)
    {
        $this->db->where('opl_qm_category', $cat);
        $q = $this->db->get('opl_qm_cat');
        if($q->num_rows()==0):
            $this->db->insert('opl_qm_cat', $data);
            return $this->db->insert_id();
        endif;
    }
    function getQuizCat($id = NULL)
    {
        if($id==NULL):
            $q = $this->db->get('opl_qm_cat');
            return $q->result();
        else:
            $this->db->where('opl_qm_cat_id', $id);
            $q = $this->db->get('opl_qm_cat');
            return $q->row();
        endif;
        
    }
    
    function getQuizType()
    {
        $q = $this->db->get('opl_qm_type');
        return $q->result();
    }
    
}
