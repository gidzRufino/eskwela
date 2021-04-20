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
class Finance_model extends CI_Model {
    //put your code here
    
    function approvePromisory($details, $prom_id)
    {
        $this->db->where('fr_id', $prom_id );
        if($this->db->update('c_finance_request', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getPromisoryRequest($year, $st_id, $sem)
    {
        $this->db->where('fr_sem', $sem);
        $this->db->where('fr_requesting_id', $st_id);
        $this->db->where('fr_year', $year);
        $this->db->order_by('fr_id', 'DESC');
        $this->db->limit(1);
        $q = $this->db->get('c_finance_request');
        return $q;
    }
    
    function requestPromisory($details)
    {
        if($this->db->insert('c_finance_request', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function saveFinanceLog($logDetails)
    {
        $this->db->insert('c_finance_logs', $logDetails);
        return;
    }
    
    
    function getChargesByCategory($cat_id,$sem, $school_year, $plan_id)
    {
        
        $this->db->where('plan_id', $plan_id);
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $sem);
        $this->db->where('category_id', $cat_id);
        $this->db->select('SUM(amount) as amount');
        $this->db->join('c_finance_items', 'c_finance_charges.item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_charges');
        return $q;
    }
    
    function getLabInExtra()
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
    }
    
    function printCollectionReportPerItem($from, $to)
    {
        $this->db->select('*');
        $this->db->select('SUM(t_amount) as amount');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('t_type', 0);
        $this->db->group_by('c_finance_transactions.t_charge_id');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getCollection($from, $to)
    {
        $this->db->select('*');
        $this->db->select('SUM(t_amount) as amount');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('t_type', 0);
        $this->db->group_by('ref_number');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
        
    function deleteDiscount($school_year, $st_id, $item_id)
    {
        $this->db->where('disc_st_id', $st_id);
        $this->db->where('disc_school_year', $school_year);
        $this->db->where('disc_item_id', $item_id);
        if($this->db->delete('c_finance_discounts')):
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function deleteTransaction($trans_id)
    {
        $this->db->where('trans_id', $trans_id);
        if($this->db->delete('c_finance_transactions')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
            
    
    function deleteExtraCharges($trans_id)
    {
        $this->db->where('extra_id', $trans_id);
        if($this->db->delete('c_finance_extra')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
            
    function saveTransaction($column)
    {
        $result = $this->db->insert_batch('c_finance_transactions', $column);
        if($result):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getTransactionByItemId($st_id, $sem, $school_year, $item_id)
    {
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        $this->db->where('t_charge_id', $school_year);
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getTransactionByDate($st_id, $sem, $school_year, $date)
    {
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        $this->db->where('t_date', $date);
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getTransaction($st_id, $sem, $school_year)
    {
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->order_by('t_date', 'ASC');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getTransactionByRefNumber($st_id, $sem, $school_year)
    {
        $this->db->select('*');
        $this->db->select('SUM(t_amount) as subTotal');
        $this->db->from('c_finance_transactions');
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        $this->db->order_by('t_date', 'ASC');
        $this->db->group_by('t_date');
        $q = $this->db->get();
        return $q;
    }
    
    function getDiscountsByItemId($st_id, $sem, $school_year, $item_id)
    {
        $this->db->where('disc_st_id', $st_id);
        $this->db->where('disc_sem', $sem);
        $this->db->where('disc_school_year', $school_year);
        ($item_id==NULL?"":$this->db->where('disc_item_id', $item_id));
        $q = $this->db->get('c_finance_discounts');
        return $q->row();
    }
    
    function getChargesByItemId($item_id,$sem, $school_year, $plan_id)
    {
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $sem);
        $this->db->where('item_id', $item_id);
        $this->db->where('plan_id', $plan_id);
        $q = $this->db->get('c_finance_charges');
        return $q;
    }
            
    function addFinanceTransaction($transaction)
    {
        if($this->db->insert('c_finance_transactions', $transaction)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function applyDiscounts($discount)
    {
        if($this->db->insert('c_finance_discounts', $discount)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function overPayment($st_id, $sem, $school_year)
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
        $this->db->where('category_id', 4);
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
    }
    
    function getLaboratoryFee($st_id, $sem, $school_year)
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
        $this->db->where('category_id', 3);
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
    }
    
    function getExtraFinanceCharges($st_id, $sem, $school_year)
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
        
    }
    
    function addExtraFinanceCharges($charge)
    {
        if($this->db->insert('c_finance_extra', $charge)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function setFinanceAccount($finDetails, $st_id, $plan, $school_year, $sem)
    {
        $this->db->where('fin_st_id', $st_id);
        $this->db->where('fin_term_id', $sem);
        $this->db->where('fin_plan_id', $plan);
        $this->db->where('fin_school_year', $school_year);
        $q = $this->db->get('c_finance_accounts');
        if($q->num_rows()==0):
            $this->db->insert('c_finance_accounts', $finDetails);
        endif;
        
        return;
    }
    
    function getPlanByCourse($course_id, $level)
    {
        $this->db->where('fin_course_id', $course_id);
        $this->db->where('fin_year_level', $level);
        $q = $this->db->get('c_finance_plan');
        return $q->row();
    }
    function deleteFinanceCharges($charge_id)
    {
        $this->db->where('charge_id', $charge_id);
        if($this->db->delete('c_finance_charges')):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function editFinanceCharges($charge_id, $amount)
    {
        $this->db->where('charge_id', $charge_id);
        if($this->db->update('c_finance_charges', array('amount'=> $amount))):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function financeChargesByPlan($school_year, $sem, $plan,$year_level)
    {
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $sem);
        ($plan==NULL?"":$this->db->where('c_finance_charges.plan_id', $plan));
        ($year_level==NULL?"":$this->db->where('fin_year_level', $year_level));
        $this->db->join('c_finance_charges', 'c_finance_plan.fin_plan_id = c_finance_charges.plan_id','left');
        $this->db->join('c_finance_items', 'c_finance_charges.item_id = c_finance_items.item_id', 'left');
        $this->db->order_by('order', 'ASC');
        $q = $this->db->get('c_finance_plan');
        return $q->result();
        
    }
    
    function financeCharges($course_id, $year_level, $school_year, $sem)
    {
        $this->db->where('fin_course_id', $course_id);
        $this->db->where('fin_year_level', $year_level);
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $sem);
        $this->db->join('c_finance_charges', 'c_finance_plan.fin_plan_id = c_finance_charges.plan_id','left');
        $this->db->join('c_finance_items', 'c_finance_charges.item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_plan');
        return $q->result();
        
    }
    
    function addPlan($plan, $course_id, $grade_level)
    {
        $this->db->where('fin_course_id', $course_id);
        $this->db->where('fin_year_level', $grade_level);
        $q = $this->db->get('c_finance_plan');
        if($q->num_rows()>0):
            $this->db->where('fin_course_id', $course_id);
            $this->db->where('fin_year_level', $grade_level);
            $q1 = $this->db->get('c_finance_plan');
            return $q1->row()->fin_plan_id;
        else:
            $this->db->insert('c_finance_plan', $plan);
            return $this->db->insert_id();
        endif;
    }
    
    function addFinanceItem($item)
    {
        $this->db->where('item_description', $item);
        $q = $this->db->get('c_finance_items');
        if($q->num_rows()==0):
            $this->db->insert('c_finance_items', array('item_description'=>$item, 'dept_id' => 4));
            $result = json_encode(array('id'=>$this->db->insert_id(), 'value' => $item, 'status' => TRUE));
        else:
            $result = json_encode(array('id'=>$q->row()->item_id, 'value' => $q->row()->item_description, 'status' => FALSE));
        endif;
        return $result;
    }
    function getFinanceCharges($sy, $sem)
    {
        $this->db->where('school_year', $sy);
        $this->db->where('semester', $sem);
        $this->db->join('c_finance_items','c_finance_charges.item_id = c_finance_items.item_id','left');
        $q = $this->db->get('c_finance_charges');
        return $q;
    }
    
    function addFinanceCharges($charge, $plan_id, $item, $sem, $sy)
    {
        $this->db->where('item_id', $item);
        $this->db->where('plan_id', $plan_id);
        $this->db->where('semester', $sem);
        $this->db->where('school_year', $sy);
        $q = $this->db->get('c_finance_charges');
        if($q->num_rows()>0):
            return FALSE;
        else:
            $this->db->insert('c_finance_charges', $charge);
        endif;
        
        
    }
            
    function getFinItems($dept_desc)
    {
        $this->db->where('dept_description', $dept_desc);
        $this->db->join('fin_department','c_finance_items.dept_id = fin_department.dept_id','left');
        $this->db->order_by('order', 'DESC');
        $q = $this->db->get('c_finance_items');
        return $q->result();
    }
}
