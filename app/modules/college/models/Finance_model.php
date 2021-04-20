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
    
    function checkIfEnrolled($st_id, $sem, $school_year)
    {
        $this->load->dbutil();
        $settings = $this->eskwela->getSet();
        
        $db_name = DB_PREFIX.strtolower($settings->short_name).'_'.$school_year; // local db
        //$db_name = 'eskwelap_'.strtolower($settings->short_name).'_'.$school_year; // live production
        
        if($this->dbutil->database_exists($db_name))
        {
            $this->db = $this->eskwela->db($school_year);
            $this->db->select('*');
            $this->db->select('profile_students_c_admission.status as status');
            $this->db->where('st_id', $st_id);
            $this->db->where('semester', $sem);
            $this->db->where('school_year', $school_year);
            $q = $this->db->get('profile_students_c_admission');
            if($q->num_rows() > 0):
                $jsonArray = array(
                    'isEnrolled'    => TRUE,
                    'details'       => $q->row(),
                    'dbExist'       => TRUE,
                );
            else:
                $jsonArray = array(
                    'isEnrolled'    => FALSE,
                    'details'       => [],
                    'dbExist'       => TRUE,
                );
            endif;

        }else {
                $jsonArray = array(
                    'isEnrolled'    => FALSE,
                    'details'       => [],
                    'dbExist'       => FALSE,
                );

            
        }
        
        return json_encode($jsonArray);
    }
    
    function getCollegeStudent($st_id, $school_year, $semester)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->where('profile_students_c_admission.st_id', $st_id);
        $this->db->where('profile_students_c_admission.semester', $semester);
        
        $query = $this->db->get('profile_students_c_admission');
        return $query->row();
    }
    
    function getBasicStudent($st_id, $school_year, $semester)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->join('profile_students', 'profile_students.user_id = profile_students_admission.user_id','left');
        $this->db->join('profile', 'profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->where('profile_students_admission.st_id', $st_id);
        $this->db->where('profile_students_admission.semester', $semester);
        $this->db->group_by('profile_students_admission.user_id');
        
        $query = $this->db->get('profile_students_admission');
        return $query->row();
    }
    
    function getStudentList($dept_id, $semester, $school_year, $course_id)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->where('semester', $semester);
        if($dept_id < 4):
            $this->db->where('profile_students_admission.status', 1);
            $this->db->where('profile_students_admission.grade_level_id', $course_id);
            $this->db->select('firstname,lastname,level,profile_students_admission.st_id, profile_students_admission.user_id, grade_id');
            $this->db->join('grade_level','profile_students_admission.grade_level_id = grade_level.grade_id','left');
            $this->db->join('profile','profile_students_admission.user_id = profile.user_id','left');
            $this->db->order_by('grade_id','ASC');
            $this->db->order_by('lastname','ASC');
            $this->db->order_by('firstname','ASC');
            $q = $this->db->get('profile_students_admission');
        else:
            $this->db->where('profile_students_c_admission.status', 1);
            $this->db->where('profile_students_c_admission.course_id', $course_id);
            $this->db->select('firstname,lastname,short_code, year_level, profile_students_c_admission.st_id, profile_students_c_admission.user_id, profile_students_c_admission.course_id');
            $this->db->join('c_courses','profile_students_c_admission.course_id = c_courses.course_id','left');
            $this->db->join('profile','profile_students_c_admission.user_id = profile.user_id','left');
            $this->db->order_by('year_level', 'ASC');
            $this->db->order_by('lastname','ASC');
            $this->db->order_by('firstname','ASC');
            //$this->db->limit(50);
            $q = $this->db->get('profile_students_c_admission');
        endif;
        return $q->result();
        
    }
    
    function getCashPayments($from, $to, $item_id, $fused_cat)
    {
        $this->db->select('SUM(t_amount) as amount');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('t_type', 0);
        ($item_id!=NULL?$this->db->where('t_charge_id', $item_id):'');
        ($fused_cat!=NULL?$this->db->where('fused_category', $fused_cat):'');
        $this->db->join('c_finance_bank','c_finance_transactions.bank_id = c_finance_bank.fbank_id','left');
        $q = $this->db->get('c_finance_transactions');
        return $q->row();
    }
    
    function getCollectionItems($from, $to)
    {
        $this->db->select('*');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('t_type <=', 1);
        $this->db->group_by('c_finance_items.item_id');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function generateAssessementForElementary($limit, $year, $level, $status )
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_admission.status as status');
        $this->db->select('profile_students.st_id as stid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id','left');
        if($year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $year);
        endif; 
            $this->db->where('dept_id <=', 2);
        
        $this->db->where('account_type', 5);
        ($status!=NULL?$this->db->where('profile_students_admission.status', $status):"");
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->group_by('profile.user_id');

        if($limit!=NULL){
                $this->db->limit($limit);	
        }
		
        $query = $this->db->get();
        return $query->result();
    }
    
    function generateAssessementForJuniorHigh($limit, $year, $level, $status )
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_admission.status as status');
        $this->db->select('profile_students.st_id as stid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id','left');
        if($year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $year);
        endif; 
            $this->db->where('dept_id', 3);
        
        $this->db->where('account_type', 5);
        ($status!=NULL?$this->db->where('profile_students_admission.status', $status):"");
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->group_by('profile.user_id');

        if($limit!=NULL){
                $this->db->limit($limit);	
        }
		
        $query = $this->db->get();
        return $query->result();
    }
    
    function generateAssessementForSeniorHigh($limit, $year, $level, $status )
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_admission.status as status');
        $this->db->select('profile_students.st_id as stid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id','left');
        if($year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $year);
        endif; 
            $this->db->where('dept_id', 4);
        
        $this->db->where('account_type', 5);
        ($status!=NULL?$this->db->where('profile_students_admission.status', $status):"");
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->group_by('profile.user_id');

        if($limit!=NULL){
                $this->db->limit($limit);	
        }
		
        $query = $this->db->get();
        return $query->result();
    }
    
    function getStudentsForAssessmentBasicEd($limit, $year, $level, $status )
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_admission.status as status');
        $this->db->select('profile_students.st_id as stid');
        $this->db->select('profile_students_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id','left');
        $this->db->join('grade_level', 'profile_students_admission.grade_level_id = grade_level.grade_id','left');
        if($year==NULL):
            $this->db->where('profile_students_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_admission.school_year', $year);
        endif; 
        if($level!=NULL):
            $this->db->where('grade_level_id', $level);
        endif;
        
        $this->db->where('account_type', 5);
        ($status!=NULL?$this->db->where('profile_students_admission.status', $status):"");
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->group_by('profile.user_id');

        if($limit!=NULL){
                $this->db->limit($limit);	
        }
		
        $query = $this->db->get();
        return $query->result();
    }
    
    function getAllFinanceCharges($sem, $school_year, $item_id = NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('semester', $sem);
        $this->db->where('school_year', $school_year);
        $q = $this->db->get('c_finance_charges');
        return $q->result();
    }
    
    function checkStudentAdmission($user_id)
    {
        $this->db->select('st_id');
        $this->db->where('st_id', $user_id);
        $q = $this->db->get('profile_students_c_admission');
        return $q->row();
        
    }
    
    function getDiscountType()
    {
        return $this->db->get('c_finance_scholar_type')
                        ->result();
    }
    
    function getStudentsForAssessment($limit, $year, $sem, $course, $level, $status, $department )
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('*');
        $this->db->select('profile_students_c_admission.status as status');
        $this->db->select('profile_students.st_id as stid');
        $this->db->select('profile_students_c_admission.user_id as psid');
        $this->db->select('profile.user_id as u_id');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id','left');
        $this->db->join('c_courses', 'profile_students_c_admission.course_id = c_courses.course_id','left');
        if($year==NULL):
            $this->db->where('profile_students_c_admission.school_year', $this->session->userdata('school_year'));
        else:
            $this->db->where('profile_students_c_admission.school_year', $year);
        endif;
        if($course!=NULL):
            $this->db->where('profile_students_c_admission.course_id', $course);
        endif;
        if($sem!=NULL):
            $this->db->where('semester', $sem);
        endif;
        if($level!=NULL):
            $this->db->where('year_level', $level);
        endif;
        
        $this->db->where('account_type', 5);
        ($department==NULL?$this->db->where('num_years >', 1):$this->db->where('num_years', 0));
        ($status!=NULL?$this->db->where('profile_students_c_admission.status', $status):"");
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->group_by('profile.user_id');

        if($limit!=NULL){
                $this->db->limit($limit);	
        }
		
        $query = $this->db->get();
        return $query->result();
    }
    
    function saveTransferFunds($transferDetails, $transferSchoolYear)
    {
        $this->db = $this->eskwela->db($transferSchoolYear);
        if($this->db->insert('c_finance_fund_transfer', $transferDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function updateFundTransfer($remainingFundUpdate, $transfer_trans_id, $transferSchoolYear, $fundRemaining)
    {   
        $this->db = $this->eskwela->db($transferSchoolYear);
        $this->db->where('trans_id', $transfer_trans_id);
        if($fundRemaining==0):
            if($this->db->delete('c_finance_transactions')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            if($this->db->update('c_finance_transactions', $remainingFundUpdate)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
        
        
    }
    
    function searchFundTransferAccount($value, $year)
    {
                
                $this->db->select('profile_students.st_id as st_id');
                $this->db->select('profile.user_id as uid');
                $this->db->select('lastname');
                $this->db->select('firstname');
                $this->db->select('middlename');
                $this->db->select('account_type');
                $this->db->from('profile_students');
                $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
                $this->db->like('lastname', $value, 'both'); 
                $this->db->order_by('lastname', 'ASC');
                $this->db->limit(10);
                
                $this->db->where('account_type', 5);

                $query = $this->db->get();
        return $query->result();
        
        
    }
    
    function removeCashDenomination($id)
    {
        $this->db->where('fdcb_id', $id);
        if($this->db->delete('c_finance_daily_cash_breakdown')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
            
    function getCashBreakdownDetails($date)
    {
        $this->db->where('fdcb_date', $date);
        $this->db->join('c_finance_cash_denomination', 'c_finance_daily_cash_breakdown.fdcb_denom_id = c_finance_cash_denomination.cd_id');
        $this->db->order_by('cd_id','DESC');
        $q = $this->db->get('c_finance_daily_cash_breakdown');
        return $q->result();
    }
    
    function getOtherAccountDetails($st_id)
    {
        $this->db->where('fo_account_id', $st_id);
        $q = $this->db->get('c_finance_others');
        return $q->row();
    }
    
    function updateORSeries($or_num)
    {
        $this->db->where('or_current', ($or_num-1));
        $this->db->update('c_finance_or_series', array('or_current'=> ($or_num), 'or_last_printing' => date('Y-m-d g:i:s')));
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        return;
    }
    
    function saveAccount($lastname, $firstname, $sel_sy)
    {
         $this->db->where('fo_lastname', $lastname);
         $this->db->where('fo_firstname', $firstname);
         $q = $this->db->get('c_finance_others');
         if($q->num_rows()>0):
             return json_encode(array('isRegistered'=> TRUE, 'account_number' => $q->row()->fo_account_id));
        else:
            $account_number = date('dmyHis');
            $this->db->insert('c_finance_others', array('fo_account_id' => $account_number, 'fo_lastname' => $lastname, 'fo_firstname'  => $firstname, 'fo_grade_level' => '', 'fo_school_year' => $sel_sy,'fo_dateCreated' => date('Y-m-d g:i:s')));
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return json_encode(array('isRegistered'=> FALSE, 'account_number' => $account_number));
        endif;
    }
    
    function getTotalCollectionPerItem($teller_id, $item_id, $date, $t_type,$school_year)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->select('*');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        ($teller_id!=NULL?$this->db->where('t_em_id', $teller_id):'');
        $this->db->where("t_date", $date);
        $this->db->where('t_type', $t_type);
        $this->db->where('t_charge_id', $item_id);
        $q = $this->db->get('c_finance_transactions');
        return $q->result();
    }
    
    function printCollectionReportPerTeller($from, $to, $teller_id, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        //$this->db->select('SUM(t_amount) as amount');
        $this->db->join('c_finance_bank', 'c_finance_transactions.bank_id = c_finance_bank.fbank_id', 'left');
        $this->db->where('t_em_id', $teller_id);
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
         $this->db->where('t_type <=', 1);
        $this->db->where('t_school_year', $school_year);
        $this->db->order_by('ref_number','ASC');
        $q = $this->db->get('c_finance_transactions');
        return $q->result();
    }
    
    function getFinanceItemByID($item_id, $school_year)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        return $this->db->where('item_id', $item_id)
                        ->get('c_finance_items')
                        ->row();
    }
    
    function getTransactionByOR($or_num, $school_year)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        //$this->db->where('t_date', date('Y-m-d'));
        $this->db->where('ref_number', $or_num);
        $this->db->join('c_finance_bank', 'c_finance_transactions.bank_id = c_finance_bank.fbank_id', 'left');
       // $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'inner');
        $q = $this->db->get('c_finance_transactions');
        return $q->result();
    }
    
    function searchFinanceStaff($value, $school_year)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->select('*');
        $this->db->select('profile_employee.employee_id as uid');
        $this->db->from('profile_employee'); 
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id', 'left');
        $this->db->join('profile_position', 'profile_employee.position_id = profile_position.position_id', 'left');       
        $this->db->join('user_accounts', 'profile_employee.employee_id = user_accounts.u_id', 'left');       
        $this->db->where('profile_position.position_dept_id', 7);
        $this->db->where('account_type !=', 4);
        $this->db->where('account_type !=', 5);
        $this->db->order_by('profile_position.p_rank', 'DESC');
        $this->db->order_by('lastname', 'ASC');
        $this->db->order_by('firstname', 'ASC');
        $this->db->like('LOWER(lastname)', strtolower($value), 'both');
        $query = $this->db->get();
        return $query->result();
    }
    
    function getLaboratoryFees()
    {
        return $this->db->where('category_id', 3)
                        ->get('c_finance_items')
                        ->result();
    }
    
    function getSingleSeries($employee_id)
    {
        $this->db = $this->eskwela->db($this->session->school_year);
        $this->db->where('or_cashier_id', $employee_id);
        $this->db->where('or_status', 0);
        $q = $this->db->get('c_finance_or_series');
        return $q->row();
    }
    
    function useSeries($id, $employee_id)
    {
        $this->db->where('or_cashier_id', $employee_id);
        $q = $this->db->get('c_finance_or_series');
        if($q->num_rows()>0):
            $this->db->where('or_cashier_id', $employee_id);
            $this->db->update('c_finance_or_series', array('or_cashier_id' => 0));
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        endif;
        
        $this->db->where('or_id', $id);
        if($this->db->update('c_finance_or_series', array('or_cashier_id' => $employee_id))):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function getSettings()
    {
        $this->db = $this->eskwela->db($this->session->school_year);
        $q = $this->db->get('c_finance_settings');
        return $q->row();
    }
    
    function getAllSeries()
    {
        $q = $this->db->get('c_finance_or_series');
        return $q->result();
    }
    
    function updateSeries($details, $series_id)
    {
        $this->db->where('or_id', $series_id);
        $this->db->update('c_finance_or_series', $details);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        return 'Series Successfully Updated';
    }
    
    function addSeries($details, $begin, $end)
    {
        $this->db->where('or_begin >=', $begin);
        $this->db->where('or_end <=', $end);
        $q = $this->db->get('c_finance_or_series');
        if($q->num_rows()>0):
            $msg = 'Sorry this series is already loaded in the system';
        else:
            $this->db->insert('c_finance_or_series', $details);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            $msg = 'Series Successfully Added';
        endif;
        return $msg;
    }
       
    
    function getFusedPayments($st_id, $sem, $school_year, $cat_id, $type)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        ($type==NULL?$this->db->where('t_type <=', 1):$this->db->where('t_type',$type));
        ($cat_id!=NULL?$this->db->where('c_finance_transactions.fused_category', $cat_id):'');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function editCategory($catDetails, $cat_id)
    {
        $this->db->where('fin_cat_id', $cat_id);
        if($this->db->update('c_finance_category', $catDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function addCategory($catDetails, $category, $school_year = NULL)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->where('fin_category', $category);
        if($this->db->get('c_finance_category')->num_rows() == 0):
            if($this->db->insert('c_finance_category', $catDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            return FALSE;
        endif; 
    }
    
    function saveFusedFees($fusedDetails, $cat_id)
    {
        $this->db->where('fin_cat_id', $cat_id);
        if($this->db->update('c_finance_category', $fusedDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function editItem($itemDetails, $item_id, $school_year = NULL)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->where('item_id', $item_id);
        if($this->db->update('c_finance_items', $itemDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function addItem($itemDetails, $itemDescription, $school_year = NULL)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->where('item_description', $itemDescription);
        if($this->db->get('c_finance_items')->num_rows() == 0):
            if($this->db->insert('c_finance_items', $itemDetails)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            return FALSE;
        endif;    
    }
    
    function deleteItem($item_id, $school_year = NULL)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->where('item_id', $item_id);
        if($this->db->delete('c_finance_items')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
            
    function cancelReceipt($receiptNumber)
    {
        $this->db->where('ref_number', $receiptNumber);
        if($this->db->update('c_finance_transactions', array('t_type' => 3, 't_remarks' => 'Cancelled Receipt' ))):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getTransactionByCategory($st_id, $sem, $school_year, $cat_id, $type, $item_id)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->select('t_type');
        $this->db->select('SUM(t_amount) as amount');
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_school_year', $school_year);
        $this->db->where('t_sem', $sem);
        if($type==NULL):
            $this->db->where('t_type <', 2);
        else:
            $this->db->where('t_type', $type);
        endif;
        ($cat_id!=NULL?$this->db->where('c_finance_items.category_id', $cat_id):'');
        ($item_id!=NULL?$this->db->where('c_finance_transactions.t_charge_id', $item_id):'');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_transactions');
        return $q->row();
    }
    
    function financeLog()
    {
        $this->db->join('profile_employee', 'c_finance_logs.account_id = profile_employee.employee_id', 'left');
        $this->db->join('profile', 'profile_employee.user_id = profile.user_id', 'left');
        $this->db->order_by('log_id', 'DESC');
        $q = $this->db->get('c_finance_logs');
        return $q->result();
    }
    
    function saveEditTransaction($details, $trans_id)
    {
        $this->db->where('trans_id', $trans_id);
        if($this->db->update('c_finance_transactions', $details)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function loadFinanceTransaction($trans_id, $school_year = NULL)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('trans_id', $trans_id);
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_transactions');
        return $q->row();
    }
       
    function getBank($id)
    {
        ($id!=NULL?$this->db->where('fbank_id', $id):'');
        $q = $this->db->get('c_finance_bank');
        return ($id!=NULL?$q->row():$q->result());
    }
    
    function getCashbreakdown($date)
    {
        $this->db->where('fdcb_date', $date);
        $this->db->join('c_finance_cash_denomination', 'c_finance_daily_cash_breakdown.fdcb_denom_id = c_finance_cash_denomination.cd_id');
        $this->db->order_by('cd_id','DESC');
        $q = $this->db->get('c_finance_daily_cash_breakdown');
        return $q->result();
    }
    
    function saveCashBreakDown($details, $id, $date)
    {
        $this->db->where('fdcb_denom_id', $id);
        $this->db->where('fdcb_date', $date);
        if($this->db->get('c_finance_daily_cash_breakdown')->num_rows()==0):
            $this->db->insert('c_finance_daily_cash_breakdown', $details);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        else:
            $this->db->where('fdcb_denom_id', $id);
            $this->db->where('fdcb_date', $date);
            $this->db->update('c_finance_daily_cash_breakdown', $details);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        endif;
    }
    
     function getCashDenomination()
    {
        $this->db->order_by('cd_id','DESC');
        $q = $this->db->get('c_finance_cash_denomination');
        return $q->result();
    }
    
    function getChequePayments($from, $to, $item_id, $fused_cat)
    {
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('t_type', 1);
        ($item_id!=NULL?$this->db->where('t_charge_id', $item_id):'');
        ($fused_cat!=NULL?$this->db->where('fused_category', $fused_cat):'');
        $this->db->join('c_finance_bank','c_finance_transactions.bank_id = c_finance_bank.fbank_id','left');
        $q = $this->db->get('c_finance_transactions');
        return $q->result();
    }
    
    function getOnlinePayments($from, $to, $item_id, $fused_cat)
    {
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        $this->db->where('t_type', 4);
        ($item_id!=NULL?$this->db->where('t_charge_id', $item_id):'');
        ($fused_cat!=NULL?$this->db->where('fused_category', $fused_cat):'');
        $this->db->join('c_finance_bank','c_finance_transactions.bank_id = c_finance_bank.fbank_id','left');
        $q = $this->db->get('c_finance_transactions');
        return $q->result();
    }
    
    function getEncashments($teller_id, $date)
    {
        $this->db->where('encash_teller_id', $teller_id);
        $this->db->where('encash_date', $date);
        $this->db->join('c_finance_bank','c_finance_encashments.encash_bank_id = c_finance_bank.fbank_id','left');
        $q = $this->db->get('c_finance_encashments');
        return $q->result();
    }
    
    function saveEncashments($details)
    {
        if($this->db->insert('c_finance_encashments', $details)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getBanks()
    {
        $this->db->order_by('bank_name', 'ASC');
        $q = $this->db->get('c_finance_bank');
        return $q->result();
    }
    
    function addBank($data, $bank_name)
    {
        $this->db->where('bank_name', $bank_name);
        $q = $this->db->get('c_finance_bank');
        if($q->num_rows()==0):
            $this->db->insert('c_finance_bank', $data);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            $result = json_encode(array('id'=>$this->db->insert_id(), 'value' => $bank_name, 'status' => TRUE));
        else:
            $result = json_encode(array('id'=>$q->row()->fbank_id, 'value' => $q->row()->bank_name, 'status' => FALSE));
        endif;
        return $result;
    }
    
    function getCollectibles($limit, $offset, $school_year, $st_id=NULL, $semester=NULL)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('profile_students.st_id');
        $this->db->select('balance');
        $this->db->select('lastname');
        $this->db->select('firstname');
        $this->db->select('fc_semester');
        $this->db->select('profile.user_id as uid');
        $this->db->select('profile_students_c_admission.status');
        $this->db->from('c_finance_collectible');
        $this->db->join('profile_students', 'c_finance_collectible.fc_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id', 'left');
        
        $this->db->where('profile_students_c_admission.status !=', 0);
        ($st_id!=NULL?$this->db->where('fc_st_id', $st_id):'');
        $this->db->where('fc_school_year', $school_year);
        ($semester!=NULL?$this->db->where('fc_semester', $semester):'');
        $this->db->where('balance !=', 0);
        $this->db->where('balance <', 200000);
        $this->db->order_by('lastname', 'ASC');
        
        if($limit!=NULL || $offset=NULL){
            $this->db->limit($limit, $offset);	
        }
        
        $q = $this->db->get();
        return $q;
    }
    
    function updateCollectibles($details, $st_id, $sy, $semester)
    {
        $this->db->where('fc_st_id', $st_id);
        $this->db->where('fc_school_year', $sy);
        ($semester!=0?$this->db->where('fc_semester', $semester):'');
        $q = $this->db->get('c_finance_collectible');
        if($q->num_rows()>0):
            $this->db->where('fc_st_id', $st_id);
            $this->db->where('fc_school_year', $sy);
            ($semester!=0?$this->db->where('fc_semester', $semester):'');
            $this->db->update('c_finance_collectible', $details);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        else:
            $this->db->insert('c_finance_collectible', $details);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        endif;
    }
    
    function getStudents($limit, $offset, $year, $sem)
    {
        $this->db = $this->eskwela->db($year);
        $this->db->select('profile_students.st_id');
        $this->db->select('lastname');
        $this->db->select('firstname');
        $this->db->select('profile.user_id as uid');
        $this->db->select('year_level');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_c_admission', 'profile.user_id = profile_students_c_admission.user_id', 'left');
        $this->db->where('account_type', 5);
        $this->db->where('profile_students_c_admission.status !=', 0);
        $this->db->where('profile_students_c_admission.school_year', $year);
        $this->db->where('profile_students_c_admission.semester', $sem);
        $this->db->order_by('lastname', 'ASC');

        if($limit!=""||$offset=""){
            $this->db->limit($limit, $offset);	
        }
        
        $q = $this->db->get();
        return $q;
    }
    
    function approvePromisory($details, $prom_id)
    {
        $this->db->where('fr_id', $prom_id );
        if($this->db->update('c_finance_request', $details)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
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
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function saveFinanceLog($logDetails)
    {
        $this->db->insert('c_finance_logs', $logDetails);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        return;
    }
    
    
    function getExtraChargesByCategory($cat_id,$sem, $school_year, $user_id)
    {
        
        ($user_id!=NULL?$this->db->where('extra_st_id', $user_id):'');
        $this->db->where('extra_school_year', $school_year);
        $this->db->where('extra_sem', $sem);
        $this->db->where('category_id', $cat_id);
        $this->db->select('SUM(extra_amount) as amount');
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
    }
    
    function getChargesByCategory($cat_id,$sem, $school_year, $plan_id)
    {
        $this->db = $this->eskwela->db($school_year);
        ($plan_id!=NULL?$this->db->where('plan_id', $plan_id):'');
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
        $this->db->where('t_type <=', 1);
        $this->db->group_by('c_finance_transactions.t_charge_id');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    
    function getCollectionDetails($from, $to, $report_type=NULL)
    {
        $this->db->select('*');
        $this->db->select('SUM(t_amount) as amount');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        ($report_type!=NULL?$this->db->where('t_receipt_type', $report_type):$this->db->where('t_type', 0));
        $this->db->group_by('c_finance_items.item_id');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getCollection($from, $to, $report_type=NULL)
    {
        $this->db->select('*');
       // $this->db->select('SUM(t_amount) as amount');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->join('profile_students', 'c_finance_transactions.t_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->where("t_date between'".($from==NULL?date('Y-m-d'):$from) . "' and'" . ($to==NULL?date('Y-m-d'):$to)  . "'");
        ($report_type!=NULL?$this->db->where('t_type', $report_type):$this->db->where('t_type <=', 1));
        $this->db->order_by('t_date','ASC');
        $this->db->order_by('ref_number', 'ASC');
        //$this->db->group_by('ref_number');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
        
    function deleteDiscount($school_year, $st_id, $item_id)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('disc_st_id', $st_id);
        $this->db->where('disc_school_year', $school_year);
        $this->db->where('disc_item_id', $item_id);
        if($this->db->delete('c_finance_discounts')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
        
    }
    
    function deleteTransaction($trans_id, $school_year = NULL)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->where('trans_id', $trans_id);
        if($this->db->delete('c_finance_transactions')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
            
    
    function deleteExtraCharges($trans_id)
    {
        $this->db->where('extra_id', $trans_id);
        if($this->db->delete('c_finance_extra')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
             
    function saveTransaction($column, $school_year=NULL)
    {
        $db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        if($db->insert('c_finance_transactions', $column)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return $db->insert_id();
        else:
            return FALSE;
        endif;
    }
    
    function getTransactionByItemId($st_id, $sem, $school_year, $item_id, $type)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        ($type==NULL?$this->db->where('t_type <=', 1):$this->db->where('t_type',$type));
        ($item_id!=NULL?$this->db->where('c_finance_items.item_id', $item_id):'');
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->group_by('item_id');
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
        $this->db->join('c_finance_category', 'c_finance_transactions.fused_category = c_finance_category.fin_cat_id','left');
        $q = $this->db->get('c_finance_transactions');
        return $q;
    }
    
    function getTransaction($st_id, $sem, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->from('c_finance_transactions');
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->order_by('t_date', 'ASC');
        $q = $this->db->get();
        return $q;
    }
    
    function getTransactionByRefNumber($st_id, $sem, $school_year, $t_type)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->select('SUM(t_amount) as subTotal');
        $this->db->from('c_finance_transactions');
        $this->db->where('t_st_id', $st_id);
        $this->db->where('t_sem', $sem);
        $this->db->where('t_school_year', $school_year);
        ($t_type!=NULL?$this->db->where('t_type', $t_type):$this->db->where('t_type <', 2));
        $this->db->join('c_finance_items', 'c_finance_transactions.t_charge_id = c_finance_items.item_id', 'left');
        $this->db->order_by('t_timestamp', 'ASC');
        //($t_type==NULL?$this->db->group_by('t_date'):'');
        $this->db->group_by('t_date');
        $q = $this->db->get();
        return $q;
    }
    
    function getDiscountsById($id)
    {
        $this->db->where('disc_id', $id);
        $this->db->join('c_finance_scholar_type', 'c_finance_discounts.disc_category = c_finance_scholar_type.schlr_id','left');
        $q = $this->db->get('c_finance_discounts');
        return $q->row();
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
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function applyDiscounts($discount)
    {
        if($this->db->insert('c_finance_discounts', $discount)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return $this->db->insert_id();
        else:
            return FALSE;
        endif;
    }
    
    function overPayment($st_id, $sem, $school_year)
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
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
    
    function getExtraFinanceChargesForCR($st_id, $sem, $school_year, $extra_id)
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
        if($extra_id!=NULL):
            $this->db->where('extra_item_id', $extra_id);
            $this->db->select('SUM(extra_amount) as amount');
        endif;
        $this->db->group_by('extra_item_id');
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
        
    }
    
    function getExtraFinanceCharges($st_id, $sem, $school_year, $extra_id)
    {
        $this->db->where('extra_st_id', $st_id);
        $this->db->where('extra_sem', $sem);
        $this->db->where('extra_school_year', $school_year);
        if($extra_id!=NULL):
            $this->db->where('extra_item_id', $extra_id);
            $this->db->select('SUM(extra_amount) as amount');
        endif;
        $this->db->join('c_finance_items', 'c_finance_extra.extra_item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_extra');
        return $q;
        
    }
    
    function addExtraFinanceCharges($charge, $school_year)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        if($this->db->insert('c_finance_extra', $charge)):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
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
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
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
    function deleteFinanceCharges($charge_id, $school_year)
    {
        $this->db = ($school_year==NULL?$this->eskwela->db($this->session->school_year):$this->eskwela->db($school_year));
        $this->db->where('charge_id', $charge_id);
        if($this->db->delete('c_finance_charges')):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function editFinanceCharges($charge_id, $amount, $semester, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('charge_id', $charge_id);
        if($this->db->update('c_finance_charges', array('amount'=> $amount))):
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function financeChargesByPlan($school_year, $sem, $plan,$year_level)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $sem);
        ($plan==NULL?"":$this->db->where('c_finance_charges.plan_id', $plan));
        ($year_level==NULL?"":$this->db->where('fin_year_level', $year_level));
        $this->db->join('c_finance_charges', 'c_finance_plan.fin_plan_id = c_finance_charges.plan_id','left');
        $this->db->join('c_finance_items', 'c_finance_charges.item_id = c_finance_items.item_id', 'left');
        $this->db->join('c_finance_category', 'c_finance_items.category_id = c_finance_category.fin_cat_id','left');
        $this->db->order_by('order', 'DESC');
        $q = $this->db->get('c_finance_plan');
        return $q->result();
        
    }
    
    function financeCharges($course_id, $year_level, $school_year, $sem)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('fin_course_id', $course_id);
        $this->db->where('fin_year_level', $year_level);
        $this->db->where('school_year', $school_year);
        $this->db->where('semester', $sem);
        $this->db->join('c_finance_charges', 'c_finance_plan.fin_plan_id = c_finance_charges.plan_id','left');
        $this->db->join('c_finance_items', 'c_finance_charges.item_id = c_finance_items.item_id', 'left');
        $q = $this->db->get('c_finance_plan');
        return $q->result();
        
    }
    
    function addPlan($plan, $course_id, $grade_level, $school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('fin_course_id', $course_id);
        $this->db->where('fin_year_level', $grade_level);
        $this->db->where('is_college', 1);
        $q = $this->db->get('c_finance_plan');
        if($q->num_rows()>0):
            $this->db->where('fin_course_id', $course_id);
            $this->db->where('fin_year_level', $grade_level);
            $this->db->where('is_college', 1);
            $q1 = $this->db->get('c_finance_plan');
            return $q1->row()->fin_plan_id;
        else:
            $this->db->insert('c_finance_plan', $plan);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            return $this->db->insert_id();
        endif;
    }
    
    function addFinanceItem($item)
    {
        $this->db->where('item_description', $item);
        $q = $this->db->get('c_finance_items');
        if($q->num_rows()==0):
            $this->db->insert('c_finance_items', array('item_description'=>$item, 'dept_id' => 4));
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
            $result = json_encode(array('id'=>$this->db->insert_id(), 'value' => $item, 'status' => TRUE));
        else:
            $result = json_encode(array('id'=>$q->row()->item_id, 'value' => $q->row()->item_description, 'status' => FALSE));
        endif;
        return $result;
    }
    function getFinanceCharges($sy, $sem)
    {
        $this->db = $this->eskwela->db($sy);
        $this->db->where('school_year', $sy);
        $this->db->where('semester', $sem);
        $this->db->join('c_finance_items','c_finance_charges.item_id = c_finance_items.item_id','left');
        $q = $this->db->get('c_finance_charges');
        return $q;
    }
    
    function addFinanceCharges($charge, $plan_id, $item, $sem, $sy)
    {
        $this->db = $this->eskwela->db($sy);
        $this->db->where('item_id', $item);
        $this->db->where('plan_id', $plan_id);
        $this->db->where('semester', $sem);
        $this->db->where('school_year', $sy);
        $q = $this->db->get('c_finance_charges');
        if($q->num_rows()>0):
            return FALSE;
        else:
            $this->db->insert('c_finance_charges', $charge);
                $runScript = $this->db->last_query();
                Modules::run('web_sync/saveRunScript', $runScript);
        endif;
        
        
    }
            
    function getFinItems($school_year = NULL)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->school_year:$school_year);
        $this->db->join('c_finance_category','c_finance_items.category_id = c_finance_category.fin_cat_id','left');
        $this->db->join('fin_department','c_finance_items.dept_id = fin_department.dept_id','left');
        $this->db->order_by('order', 'DESC');
        $this->db->order_by('item_id', 'ASC');
        $q = $this->db->get('c_finance_items');
        return $q->result();
    }
    
    function getFinCategory($id)
    {
        ($id!=NULL?$this->db->where('fin_cat_id', $id):'');
        $q = $this->db->get('c_finance_category');
        return ($id!=NULL?$q->row():$q->result());
    }
}
