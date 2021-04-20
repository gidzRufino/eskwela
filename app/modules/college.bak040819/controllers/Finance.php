<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Registrar
 *
 * @author genesis
 */
class Finance extends MX_Controller {
    //put your code here
    
    
    function __construct() {
        parent::__construct();
        $this->load->model('finance_model');
	$this->load->library('pagination');
        $this->load->library('Pdf');
    }
    
    private function post($name)
    {
        return $this->input->post($name);
    }
    
    function printPermit($st_id, $sem)
    {
        $data['st_id'] = base64_decode($st_id);
        $data['sem'] = $sem;
        
        $this->load->view('finance/examPermitWrapper', $data);
    }
    
    function printSOA($st_id, $sem, $school_year = NULL)
    {
        $data['st_id'] = base64_decode($st_id);
        $data['sem'] = $sem;
        
        $this->load->view('finance/reports/printSOA', $data);
    }
    
    function approvePromisory()
    {
        $prom_id = $this->post('prom_id');
        $amount = $this->post('amount');
        $action = $this->post('action');
        
        $details = array(
            'fr_approved'           => $action,
            'fr_approved_by'        => $this->session->userdata('employee_id'),
            'fr_allowable_amount'   => $amount,
            'approved_date'         => date('Y-m-d g:i:s')
        );
        if($this->finance_model->approvePromisory($details, $prom_id)):
            echo 'Successfully Submited';
        else:
            echo 'Sorry something went wrong';
        endif;
    }
    
    function getPromisoryRequest($st_id, $sem)
    {
        $year = $this->session->userdata('school_year');
        $request = $this->finance_model->getPromisoryRequest($year, $st_id, $sem);
        return $request;
    }
    
    function requestPromisory()
    {
        $st_id = $this->post('st_id');
        $sem = $this->post('semester');
        $sy = $this->session->userdata('school_year');
        $remarks = $this->post('remarks');
        $student = Modules::run('college/getSingleStudent', $st_id, $this->session->userdata('school_year'), $sem);
        
        $details = array(
            'fr_requesting_id'  => $st_id,
            'fr_remarks'        => $remarks,
            'fr_year'           => $sy,
            'fr_sem'            => $sem
        );
        
        if($this->finance_model->requestPromisory($details)):
            Modules::run('notification_system/department_notification', "Admin", $student->firstname.' '.$student->lastname.' has submitted a business office promisory note', base_url().'college/finance/accounts/'.base64_encode($st_id).'/'.$sem);
            echo 'Successfully Submitted';
        else:
            echo 'Sorry Something went Wrong';
        endif;
    }
    
    function getPaymentHistory($details, $balance, $student)
    {
        $data['student'] = $student;
        $data['details'] = $details;
        $data['total'] = $balance;
        $this->load->view('paymentHistory', $data);
    }
    
    function saveFinanceLog($id, $remarks)
    {
        $logDetails = array(
                'account_id' => $id,
                'remarks'    => $remarks
        );
        
        $this->finance_model->saveFinanceLog($logDetails);
    }
    
    function printCollectionReportPerItem($from=NULL, $to=NULL)
    {
        $this->load->library('pdf');
        $data['collection'] = $this->finance_model->printCollectionReportPerItem($from, $to);
        $this->load->view('finance/reports/printCollectionReportPerItem', $data);
    }
    
    function printCollectionReport($from=NULL, $to=NULL)
    {
        $this->load->library('pdf');
        $data['collection'] = $this->finance_model->getCollection($from, $to);
        $this->load->view('finance/reports/printCollectionReport', $data);
    }
    
    
    public function collectionReport($from=NULL, $to=NULL)
    {
        $data['collection'] = $this->finance_model->getCollection($from, $to);
        $data['modules'] = 'college';
        $data['main_content'] = 'finance/sales';
        echo Modules::run('templates/canteen_content', $data);
    }
    
    public function calculateItem()
    {
        $item_id = $this->post('item_id');
        $sem = $this->post('sem');
        $school_year = $this->post('school_year');
        $plan_id = $this->post('plan_id');
        $st_id = $this->post('st_id');
        $totalUnits = $this->post('totalUnits');
        
        $charges = $this->getChargesByItemId($item_id, $sem, $school_year, $plan_id);
        $charge = $charges->row()->amount;
        $item = $charges->row()->item_description;
        
        $transaction = $this->getTransaction($st_id, $sem, $school_year);
        if($transaction->num_rows()!=0):
            foreach ($transaction->result() as $chrg):
                $totalPayment += $chrg->t_amount;
            endforeach;
        else:
            $totalPayment = 0;
        endif;
        
        $totalRetail = ($item_id<2?$charge*$totalUnits:$charge) - $totalPayment;
        
        echo json_encode(array('totalPayment' => number_format($totalRetail,2,'.',','),'item' => $item));
 
    }
        
    public function getTransactionByRefNumber($st_id, $sem, $school_year)
    {
        $transaction = $this->finance_model->getTransactionByRefNumber($st_id, $sem, $school_year);
        return $transaction;
    }
    
    public function getTransactionByItemId($st_id, $sem, $school_year, $item_id)
    {
        $transaction = $this->finance_model->getTransactionByItemId($st_id, $sem, $school_year, $item_id);
        return $transaction;
    }


    public function getDiscountsByDate($st_id, $sem, $school_year, $date=NULL)
    {
        $disc = $this->finance_model->getDiscountsByDate($st_id, $sem, $school_year, $date);
        return $disc;
    }

    public function getDiscountsByItemId($st_id, $sem, $school_year, $item_id=NULL)
    {
        $disc = $this->finance_model->getDiscountsByItemId($st_id, $sem, $school_year, $item_id);
        return $disc;
    }
    
    public function getTransactionByDate($st_id, $sem, $school_year, $date)
    {
        $transaction = $this->finance_model->getTransactionByDate($st_id, $sem, $school_year, $date);
        return $transaction;
    }
    
    public function getTransaction($st_id, $sem, $school_year)
    {
        $transaction = $this->finance_model->getTransaction($st_id, $sem, $school_year);
        return $transaction;
    }
    
    public function getBalance($st_id, $sem, $school_year)
    {
        
        $student = Modules::run('college/getSingleStudent', urldecode($st_id), $school_year);
        $plan = Modules::run('college/finance/getPlanByCourse', $student->course_id, $student->year_level);
        $charges = Modules::run('college/finance/financeChargesByPlan',$student->year_level, $school_year, $sem, $plan->fin_plan_id );
        $payments = Modules::run('college/finance/getTransactionByRefNumber', $student->uid, $student->semester,$school_year);
        //print_r($student->uid);

        $loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $student->admission_id);
        $totalUnits = 0;
        foreach ($loadedSubject as $sl):
            $totalUnits += ($sl->s_lect_unit + $sl->s_lab_unit);
        endforeach;
        
        foreach ($charges as $c):
            $amount = ($c->item_id<=1 || $c->item_id<=2?$c->amount*$totalUnits:$c->amount);
            $total += $amount;
            //echo $amount.'<br />';
            
        endforeach;
        $extraCharges = $this->getExtraFinanceCharges(urldecode($st_id), $sem, $school_year);
        if($extraCharges->num_rows()>0):
               foreach ($extraCharges->result() as $ec):
                    $totalExtra += $ec->extra_amount;
                    //echo $ec->extra_amount;
               endforeach;
               $total = $total + $totalExtra;
            endif;
        foreach ($payments->result() as $tr):
           $totalPayments += $tr->subTotal ;
        endforeach;
        
        $totalCharges = $total;
        
        $balance =  $totalCharges - $totalPayments;
        if($balance==0):
            echo json_encode(array('status'=>TRUE, 'balance' => $balance));
        else:
            $promisoryNote = $this->getPromisoryRequest(urldecode($st_id), $sem);
            if($promisoryNote->num_rows()>0 && $promisoryNote->row()->fr_approved):
                echo json_encode(array('status'=>TRUE));
            else:
                echo json_encode(array('status'=>FALSE));
            endif;
        endif;
        
    }
    
    public function getChargesByItemId($item_id, $sem, $school_year, $plan_id)
    {
          $charges = $this->finance_model->getChargesByItemId($item_id,$sem, $school_year, $plan_id);
          return $charges;
    }
    
    public function getChargesByCategory($cat_id, $sem, $school_year, $plan_id=NULL)
    {
          $charges = $this->finance_model->getChargesByCategory($cat_id,$sem, $school_year, $plan_id);
          return $charges;
    }

    public function saveTransaction()
    {
        $school_year = $this->post('school_year');
        $semester = $this->post('sem');
        $or_num = $this->post('or_num');
        $st_id = $this->post('st_id');
        $transaction = $this->post('items');
        $transDate = $this->post('transDate');
        $receipt = $this->post('receipt');
        $t_remarks = $this->post('t_remarks');
        $count = count(json_decode($transaction));
        $final = json_decode($transaction);
        $column = array();
        
        for ($x = 0; $x < $count; $x++) {
            
            $items = explode('_', $final[$x]);
            
            $details = array(
                'ref_number'        => $or_num,
                't_st_id'           => $st_id,
                't_em_id'           => $this->session->userdata('employee_id'),
                't_amount'          => $items[1],
                't_charge_id'       => $items[0],
                't_type'            => 0,
                't_date'            => $transDate,
                't_sem'             => $semester,
                't_school_year'     => $school_year, 
                't_receipt_type'    => $receipt,
                't_remarks'         => $t_remarks
            );
            
            array_push($column, $details);
        }
        
         if($this->finance_model->saveTransaction($column)):
            echo 'Successfully Saved';
        else:
            echo 'Sorry Something went wrong';
        endif;
    }

    public function deleteTransaction()
    {
        $school_year = $this->post('school_year');
        $st_id = $this->post('st_id');
        $trans_id = $this->post('trans_id');
        $item_id = $this->post('item_id');
        $trans_type = $this->post('trans_type');
        $delete_remarks = $this->post('delete_remarks');
        if($trans_type==2):
            if($this->finance_model->deleteDiscount($school_year, $st_id, $item_id)):
                if($this->finance_model->deleteTransaction($trans_id)):
                    
                    $this->saveFinanceLog($this->session->userdata('employee_id'), 'Voided a Transaction with Discount : '.$delete_remarks);
                    
                    echo 'Successfully Deleted';
                else:
                    echo 'An Error has Occur';
                endif;
            endif;
        else:
            if($this->finance_model->deleteTransaction($trans_id)):
                $this->saveFinanceLog($this->session->userdata('employee_id'), $delete_remarks);
                echo 'Successfully Deleted';
            else:
                echo 'An Error has Occur';
            endif;
        endif;
        
    }

    public function deleteExtraCharges()
    {
        $school_year = $this->post('school_year');
        $st_id = $this->post('st_id');
        $trans_id = $this->post('trans_id');
        $delete_remarks = $this->post('delete_remarks');
        if($this->finance_model->deleteExtraCharges($trans_id)):

            $this->saveFinanceLog($this->session->userdata('employee_id'), 'Deleted an Extra Charges : '.$delete_remarks);

            echo 'Successfully Deleted';
        else:
            echo 'An Error has Occur';
        endif;
        
    }
    
    public function addFinanceTransaction($ref_num, $st_id, $amount, $charge_id, $t_type, $sem, $school_year, $date=NULL)
    {
        $details = array(
           'ref_number'     => $ref_num,
            't_st_id'       => $st_id,
            't_em_id'       => $this->session->userdata('employee_id'),
            't_amount'      => $amount,
            't_charge_id'   => $charge_id,
            't_type'        => $t_type,
            't_date'        => ($date==NULL?date('Y-m-d'):$date),
            't_sem'         => $sem,
            't_school_year' => $school_year
        );
        
        if($this->finance_model->addFinanceTransaction($details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
     public function applyDiscounts()
    {
        $item = $this->post('finItem');
        $sem = $this->post('semester');
        $amount = $this->post('finAmount');
        $sy = $this->post('school_year');
        $st_id= $this->post('st_id');
        $discount_type = $this->post('discount_type');
        $remarks = $this->post('remarks');
        $plan_id = $this->post('plan_id');
        $admission_id = $this->post('admission_id');
        
        if($discount_type==0):
            $loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $admission_id);
            $charge = $this->getChargesByItemId($item, $sem, $sy, $plan_id);
            if($item==1 || $item==2):
                $totalUnits = 0;
                foreach ($loadedSubject as $sl):
                    $totalUnits += ($sl->s_lect_unit + $sl->s_lab_unit);
                endforeach;
                $charges = ($totalUnits * $charge->row()->amount) * $amount;
            endif;
        else:
            $charges = $amount;
        endif;
        
        $discountDetails = array(
            'disc_st_id'    => $st_id,
            'disc_type'     => $discount_type,
            'disc_amount'   => $amount,
            'disc_item_id'  => $item,
            'disc_remarks'  => $remarks,
            'disc_sem'      => $sem,
            'disc_school_year' => $sy
        );
        
        if($this->finance_model->applyDiscounts($discountDetails)):
            $this->addFinanceTransaction(date('Ymdgis'), $st_id, $charges, $item, 2, $sem, $sy);
            return TRUE;
        endif;
        
        
    }
    
    function overPayment($st_id, $sem, $school_year)
    {
        $charges = $this->finance_model->overPayment($st_id, $sem, $school_year);
        return $charges;
    }
    
    function getLaboratoryFee($st_id, $sem, $school_year)
    {
        $charges = $this->finance_model->getLaboratoryFee($st_id, $sem, $school_year);
        return $charges;
    }
    
    function getExtraFinanceCharges($st_id, $sem, $school_year)
    {
        $charges = $this->finance_model->getExtraFinanceCharges($st_id, $sem, $school_year);
        return $charges;
    }
            
    function loadAccountDetails($st_id, $sem)
    {
        $data['sem'] = $sem;
        $data['ro_years'] = Modules::run('registrar/getROYear');
        $data['fin_items'] = $this->finance_model->getFinItems('College');
        $data['st_id'] = $st_id;
        $this->load->view('finance/accountDetails', $data);
    }
    
    function setFinanceAccount($st_id, $sem)
    {
        $student = Modules::run('college/getSingleStudent', $st_id, $this->session->userdata('school_year'));
        $course = $student->course_id;
        $plan = $this->getPlanByCourse($course, $student->year_level);
        
        $finDetails = array(
            'fin_st_id'         => $st_id,
            'fin_term_id'       => $sem,
            'fin_plan_id'       => $plan->fin_plan_id,
            'fin_school_year'   => $this->session->userdata('school_year'),
        );
        
        $this->finance_model->setFinanceAccount($finDetails, $st_id, $plan->fin_plan_id, $this->session->userdata('school_year'), $sem);
        
        Modules::run('college/updateEnrollmentStatus', $st_id, $this->session->userdata('school_year'), $sem, 1);  
        
        return;
    }
    
    function getPlanByCourse($course_id, $year_level)
    {
        $plan_id = $this->finance_model->getPlanByCourse($course_id, $year_level);
        return $plan_id;
    }
    
    function accounts($id=NULL, $sem=NULL)
    {
        if($this->session->userdata('is_logged_in')):
            $semester = Modules::run('main/getSemester');
            if($sem==NULL):
                $sem = $semester;
            endif;
            $data['sem'] = $sem;
            $data['id'] = $id;
            $data['modules'] = 'college';
            $data['main_content'] = 'finance/financeAccounts';
            echo Modules::run('templates/canteen_content', $data);
       else:
           redirect('login');
       endif;
    }
    
    function getFinanceChargesWrapper()
    {
        $data['course_id'] = $this->post('course_id');
        $data['sem'] = $this->post('sem');
        $data['school_year'] = $this->session->userdata('school_year');
        $this->load->view('finance/financeChargesWrapper', $data);
    }
    
    function deleteFinanceCharges()
    {
        $charge_id = $this->post('charge_id');
        $success = $this->finance_model->deleteFinanceCharges($charge_id);
        if($success):
            echo 'Deleted Successfully';
        else:
            echo 'Something went wrong, Please contact csscore inc.';
        endif;
    }
            
    function editFinanceCharges()
    {
        $charge_id = $this->post('charge_id');
        $amount = $this->post('fin_amount');
        
        $success = $this->finance_model->editFinanceCharges($charge_id, $amount);
        if($success):
            echo json_encode(array('status'=> TRUE, 'msg' => 'Successfully Updated', 'amount' => $amount));
        else:
            echo json_encode(array('status'=> TRUE, 'msg' => 'Sorry Something went wrong', 'amount' => 0));
        endif;
    }
    
    function financeCharges($course_id, $year_level, $school_year, $sem)
    {
        $data['year'] = $year_level;
        $data['charges'] = $this->finance_model->financeCharges($course_id, $year_level, $school_year, $sem);
        $this->load->view('finance/financeCharges', $data);
    }
    
    function addFinanceItem()
    {
        $item = $this->post('finItem');
        $result = $this->finance_model->addFinanceItem($item);
        $result = json_decode($result);
        if($result->status):
            echo "<option value='$result->id'>$result->value</option>";
        endif;
    }
    
    function financeChargesByPlan($year_level=NULL, $school_year, $sem, $plan=NULL)
    {
        $charges = $this->finance_model->financeChargesByPlan($school_year, $sem, $plan, $year_level);
        return $charges;
    }
    
    function getFinanceCharges($course_id, $year_level, $school_year, $sem)
    {
        $charges = $this->finance_model->financeCharges($course_id, $year_level, $school_year, $sem);
        return $charges;
    }
    
    public function index()
    {
        $data['course'] = Modules::run('coursemanagement/getCourses');
        $data['ro_years'] = Modules::run('registrar/getROYear');
        $data['fin_items'] = $this->finance_model->getFinItems('College');
        $data['modules'] = 'college';
        $data['main_content'] = 'finance/finance_settings';
        echo Modules::run('templates/college_content', $data);
    }
    
    public function addExtraFinanceCharges()
    {
        $item = $this->post('finItem');
        $sem = $this->post('semester');
        $amount = $this->post('finAmount');
        $sy = $this->post('school_year');
        $st_id= $this->post('st_id');
        $year_level = $this->post('year_level');
        $plan_id = $this->post('plan_id');
        $admission_id = $this->post('admission_id');
        
        
        $charge = array(
            'extra_st_id' => $st_id,
            'extra_item_id'    => $item,
            'extra_amount'  => $amount,
            'extra_sem' => $sem,
            'extra_school_year'     => $sy
        );
        
        if($this->finance_model->addExtraFinanceCharges($charge)):
            $charges = Modules::run('college/finance/financeChargesByPlan',$year_level, $sy, $sem, $plan_id );
            $loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $admission_id);
            $totalUnits = 0;
            foreach ($loadedSubject as $sl):
                $totalUnits += ($sl->s_lect_unit + $sl->s_lab_unit);
            endforeach;
            $i=1;
            $total=0;
            $amount=0;

                foreach ($charges as $c):
                 $next = $c->school_year + 1;
                 $amount = ($c->item_id==14?$c->amount*$totalUnits:$c->amount);
             ?>
            <tr id="tr_<?php echo $c->charge_id ?>">
                <td><?php echo $i++;?></td>
                <td><?php echo $c->item_description ?></td>
                <td id="td_<?php echo $c->charge_id ?>" class="text-right"><?php echo number_format($amount, 2, '.',',') ?></td>
            </tr>
            <?php
                $total += $amount;
                endforeach;
                $totalExtra = 0;
                $extraCharges = Modules::run('college/finance/getExtraFinanceCharges',$student->uid, $student->semester, $student->school_year);
                if($extraCharges->num_rows()>0):
                    foreach ($extraCharges->result() as $ec):
                    ?>
                        <tr style="background: #0ff" id="trExtra_<?php echo $ec->extra_id ?>">
                            <td><?php echo $i++;?></td>
                            <td><?php echo $ec->item_description ?></td>
                            <td id="td_<?php echo $ec->extra_id ?>" class="text-right"><?php echo number_format($ec->extra_amount, 2, '.',',') ?></td>
                        </tr>
                    <?php
                    $totalExtra += $ec->extra_amount;
                    endforeach;
                    $total = $total + $totalExtra;
                endif;

                if($total!=0):
            ?>
            <tr style="background:yellow;">
                <th>TOTAL</th>
                <th></th>
                <th class="text-right"><?php echo number_format($total, 2, '.',',') ?></th>
                <th></th>
            </tr>
            <?php endif;
        endif;
        
    }
    
    public function addFinanceCharges()
    {
        $item = $this->post('finItem');
        $sem = $this->post('semester');
        $amount = $this->post('finAmount');
        $sy = $this->post('school_year');
        $course_id = $this->post('course_id');
        $year_level = $this->post('year_level');
        
        
        $plan = array(
            'fin_course_id'     => $course_id,
            'fin_year_level'    => $year_level
        );
        
        $plan_id = $this->finance_model->addPlan($plan, $course_id, $year_level);
        
        $charge = array(
            'item_id' => $item,
            'amount'    => $amount,
            'semester'  => $sem,
            'school_year' => $sy,
            'plan_id'     => $plan_id
        );
        
        $this->finance_model->addFinanceCharges($charge, $plan_id, $item, $sem, $sy);
        echo Modules::run('finance/financeCharges', $course_id, $year_level, $sy, $sem);
        
    }
    
    
   
   
}
