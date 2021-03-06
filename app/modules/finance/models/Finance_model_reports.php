<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of schedule_model
 *
 * @author genesis
 */
class Finance_model_reports extends CI_Model {
    //put your code here
    
    function getCollectionOrder($from, $to, $report_type, $school_year)
    {
        if($school_year==NULL):
            $school_year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id', 'left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('item_id', $report_type);    
        $this->db->where('t_type <=', 1);
        $this->db->order_by('grade_level.order','ASC');
        $this->db->order_by('t_date','ASC');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function deleteFinancePenalty($pen_id)
    {
        $this->db->where('pen_id', $pen_id);
        if($this->db->delete('c_finance_penalty')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    function getPenalty($st_id, $school_year, $status, $month)
    {
        $this->db->where('pen_st_id', $st_id);
        $this->db->where('pen_isPaid', $status);
        ($month!=NULL?$this->db->where('pen_month', $month):'');
        $this->db->where('pen_school_year', $school_year);
        $q = $this->db->get('c_finance_penalty');
        return $q;
    }
    
    function savePenalty($st_id, $month, $amount, $school_year)
    {
        $details = array(
            'pen_st_id'         => $st_id,
            'pen_month'         => $month,
            'pen_amount'        => $amount,
            'pen_school_year'   => ($school_year==NULL?$this->session->school_year:$school_year)
        );
        
        $this->db->where('pen_st_id', $st_id);
        $this->db->where('pen_month', $month);
        $this->db->where('pen_school_year', $school_year);
        $q = $this->db->get('c_finance_penalty');
        if($q->num_rows()==0):
            $this->db->insert('c_finance_penalty', $details);
        endif;
        
        return;
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
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','inner');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id', 'left');
        $this->db->where('profile_students_admission.school_year', $year);
        
        if($grade_id!=Null && $grade_id!=0){
            $this->db->where('profile_students_admission.grade_level_id', $grade_id);
        }
        if($section!=Null){
            $this->db->where('profile_students_admission.section_id', $section);
        }
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
    
    
    function getCollection($from, $to, $report_type, $school_year)
    {
        if($school_year==NULL):
            $school_year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('item_id', $report_type);    
        $this->db->where('t_type <=', 1);
        $this->db->order_by('t_date','ASC');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getChargesPerPlan($item_id)
    {
        $this->db->where('c_finance_charges.item_id', $item_id);
        $this->db->join('c_finance_plan','c_finance_charges.plan_id = c_finance_plan.fin_plan_id','left');
        $q = $this->db->get('c_finance_charges');
        return $q->result();
    }
            
    function getFinanceChargesPerItem($school_year)
    {
        if($school_year==NULL):
            $school_year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('school_year', $school_year);
        $this->db->join('c_finance_items','c_finance_charges.item_id = c_finance_items.item_id','left');
        $this->db->group_by('c_finance_charges.item_id');
        $q = $this->db->get('c_finance_charges');
        return $q;
    }
    
    function getTotalCollectionPerGradeLevel($grade_id, $option, $item_id, $trans_type, $date, $school_year)
    {
        if($school_year==NULL):
            $school_year = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->select('SUM(t_amount) as amount');
        $this->db->join('profile_students_admission','c_finance_transactions.t_st_id = profile_students_admission.st_id','left'); 
        ($grade_id!=NULL?$this->db->where('grade_level_id', $grade_id):'');
        ($trans_type==0?$this->db->where('t_type <=', 1):$this->db->where('t_type', $trans_type));
        $this->db->where('t_date <=', $date);
        $this->db->where('acnt_type', 0);
        $this->db->where('profile_students_admission.status', 1);
        switch ($option):
            case 1:
                $this->db->where('t_charge_id', 1);
            break;
        
            case 2:
            case 3:
                $this->db->where('t_charge_id', 1);
            break;
            case 4:   
                $this->db->where('t_charge_id', 3);
            break;
            case 5:   
                $this->db->where('t_charge_id', $item_id);
            break;
        endswitch;
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
            
    function getGradeLevel($option = NULL)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        switch ($option):   
            case 2:
                $this->db->where('dept_id <=', 2);
            break;
            case 3:    
                $this->db->where('dept_id >', 2);
            break;
            case 6:
            break; 
        endswitch;
        $this->db->order_by('order','ASC'); 
        $query = $this->db->get('grade_level');
        return $query->result();
    }
    
}
