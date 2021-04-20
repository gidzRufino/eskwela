<?php

 if (!defined('BASEPATH'))
	 exit('No direct script access allowed');

 /*
  * To change this license header, choose License Headers in Project Properties.
  * To change this template file, choose Tools | Templates
  * and open the template in the editor.
  */

 /**
  * Description of Payroll
  *
  * @author genru
  */
 class Payroll extends MX_Controller {

    public function __construct() {
         parent::__construct();
         $this->load->model('payroll_model');
         $this->load->model('hr_model');
         $this->load->library('pdf');
    }

    private function post($name) {
         return $this->input->post($name);
    }

    public function index() {
         $data['modules'] = 'hr';
         $data['main_content'] = 'payroll/default';
         echo Modules::run('templates/main_content', $data);
    }
    
    function getAdditionalIncome($pc_code)
    {
        $additionalIncome = $this->payroll_model->getAdditionalIncome($pc_code);
        return $additionalIncome;
    }
    
    function recalculatePayrollCharges()
    {
        $profile_id = $this->post('pro_id');
        $pc_code = $this->post('pc_code');
        
        if($this->payroll_model->recalculatePayrollCharges($pc_code, $profile_id)):
            echo 'Successfully Recalculated';
        else:
            echo 'Sorry Something went wrong';
        endif;
    }
    
    function getAmortValue($profile_id, $item_id)
    {
        $amortValue = $this->payroll_model->getAmortValue($profile_id, $item_id);
        return $amortValue;
    }
    
    function updateAmortizationStatus($profile_id, $item_id, $amount, $pc_code)
    {
        if($this->payroll_model->updateAmortizationStatus($profile_id, $item_id, $amount, $pc_code)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function saveAmortization()
    {
        $terms = $this->input->post('paymentPerTerm');
        $no_terms = $this->input->post('no_terms');
        $item = $this->input->post('item');
        $em_id = $this->input->post('em_id');
        
        $details = array(
            'pa_item_id'        => $item,
            'pa_em_id'          => $em_id,
            'pa_total_amount'   => $this->input->post('amount'),
            'pa_amort_amount'   => $terms,
            'pa_num_payments'   => $no_terms,
            'pa_date_started'   => $this->post('applied_date'),
            'pa_payment_made'   => $terms,
            'pa_pp_id'          => $this->post('pp_id')
        );
        
        $return = $this->payroll_model->saveAmortization($details);
        $return = json_decode($return);
        if($return->status):
           
            echo json_encode(array('status' => TRUE, 'msg' => 'Successfully Added'));
            //Modules::run('notification_system/sendNotification',1, 3, $em_id, 'Admin', 'request for Loan Application Approval', date('Y-m-d'), base_url().'hr/settings');
        else:
            echo json_encode(array('status' => FALSE));
        endif;
    }
    
    function getAmortizedDeduction($pc_code)
    {
        $deduction = $this->payroll_model->getAmortizedDeduction($pc_code);
        return $deduction;
    }
    
    function getEPaySlip()
    {
        
        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $pc_code = $this->payroll_model->getPCode($dateFrom, $dateTo);
        
        $data['pc_code'] = $pc_code;
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['owners_id'] = $this->input->post('owners_id');
        $data['employee_id'] = $this->post('employee_id');
        
        $this->load->view('payroll/e-payslip', $data);
    }
            
    function getPaymentByAmortID($pc_amort_id)
    {
        $amortizationPayment = $this->payroll_model->getPaymentByAmortID($pc_amort_id);
        return $amortizationPayment;
    }
    
//    function saveAmortization()
//    {
//        $terms = $this->input->post('terms');
//        $no_terms = $this->input->post('no_terms');
//        $item = $this->input->post('item');
//        $em_id = $this->input->post('em_id');
//        
//        $details = array(
//            'pa_item_id'        => $item,
//            'pa_em_id'          => $em_id,
//            'pa_total_amount'   => $this->input->post('amount'),
//            'pa_amort_amount'   => $no_terms,
//            'pa_date_started'   => $this->post('applied_date'),
//            'pa_payment_type'   => $terms
//        );
//        
//        $return = $this->payroll_model->saveAmortization($details);
//        $return = json_decode($return);
//        if($return->status):
//           
//            echo json_encode(array('status' => TRUE, 'msg' => 'Successfully Added'));
//            //Modules::run('notification_system/sendNotification',1, 3, $em_id, 'Admin', 'request for Loan Application Approval', date('Y-m-d'), base_url().'hr/settings');
//        else:
//            echo json_encode(array('status' => FALSE));
//        endif;
//    }
    
    function getPaymentTerms()
    {
        $terms = $this->payroll_model->getPaymentTerms();
        return $terms;
    }
    
    function getOD_list()
    {
        $od = $this->payroll_model->getOtherDeductions();
        return $od;
    }
         
    function loanAmortization($user_id=NULL, $status=NULL, $isgroup = 0)
    {
        $item = $this->payroll_model->loanAmortization($user_id, $status, $isgroup);
        return $item;
    }
         
         
    function setAdditionalIncome() {
     $details = array(
             'pc_profile_id'    => $this->post('pc_profile_id'),
             'pc_amount'        => $this->post('pc_amount'),
             'pc_code'          => $this->post('pc_code'),
             'pc_item_id'       => $this->post('pc_item_id')
     );

    $this->payroll_model->setAdditionalIncome($details, $this->post('pc_profile_id'), $this->post('pc_item_id'), $this->post('pc_code'), $this->post('pc_amount'));



    }

    function getCurrentListOfCharges($pc_code, $profile_id = NULL)
    {
         $charges = $this->payroll_model->getCurrentListOfCharges($pc_code, $profile_id);
         return $charges;

    }

    function getTotalLeaveSpent($em_id, $type_id = NULL, $l_pp_id = NULL)
    {
     $leaveSpent = $this->payroll_model->getTotalLeaveSpent($em_id, $type_id, $l_pp_id);
     return $leaveSpent;
    }

    function approvedManHours()
    {
    $em_id = $this->post('em_id');
    $from = $this->post('from');
    $to = $this->post('to');
    $mhCat = $this->post('mhCat');
    $totalHours = $this->post('totalHours');
    $lc_hours = $this->post('lc_hours');
    $lc_available = $this->post('lc_available');

    $pp = $this->payroll_model->checkPP($from, $to);
    if($pp):
        $pmh_details = array(
            'pmh_em_id'     => $em_id,
            'pmh_pp_id'     => $pp->per_id,
            'pmh_pmt_id'    => $mhCat,
            'pmh_num_hours' => $totalHours,
            'approved_by'   => $this->session->employee_id
        );

    else:
        $itemsFrom = explode('-', $from);
        $itemsTo = explode('-', $to);

        $ppdetails = array(
           'per_from'   => $from,
           'per_to'    => $to,
           'per_code'  => $itemsFrom[2].$itemsTo[2].$itemsFrom[0].$itemsFrom[1]
        );
        $this->payroll_model->setPayrollPeriod($from, $to, $ppdetails);

        $pp = $this->payroll_model->checkPP($from, $to);

        $pmh_details = array(
            'pmh_em_id'     => $em_id,
            'pmh_pp_id'     => $pp->per_id,
            'pmh_pmt_id'    => $mhCat,
            'pmh_num_hours' => $totalHours,
            'approved_by'   => $this->session->employee_id
        );

    endif;

        if($lc_hours!=0):

            $pmhlc_details = array(
                'pmh_em_id'     => $em_id,
                'pmh_pp_id'     => $pp->per_id,
                'pmh_pmt_id'    => 0,
                'pmh_num_hours' => $lc_hours,
                'approved_by'   => $this->session->employee_id
            );
            $this->payroll_model->saveManHours($pmhlc_details, $em_id, $pp->per_id, 0);

            $lcAppDetails = array(
                'l_em_id'       => $em_id,
                'l_type_id'     => 1,
                'l_num_hours'   => $lc_hours,
                'l_pp_id'       => $pp->per_id,
                'l_aprv_hr'     => $this->session->employee_id,
                'l_aprv_date_hr'=> date('Y-m-d g:i:s'),
                'l_aprv_fin'    => 1,
            );

            if($this->payroll_model->updateLeaveCredit($lcAppDetails, $em_id, $pp->per_id, 1)):
            endif;

        endif;

       if($this->payroll_model->saveManHours($pmh_details, $em_id, $pp->per_id, $mhCat)):
           $employee = Modules::run('hr/getEmployeeName', $em_id);
            Modules::run('main/logActivity','HUMAN RESOURCE',  $this->session->userdata('name').' has submitted a Daily Time Record of'. strtoupper($employee->firstname.' '.$employee->lastname).' to Payroll for this payroll period ['.$from.' - '.$to.']' , $this->session->userdata('employee_id'), $this->session->school_year);

            echo 'Successfully Approved';
       else:
           echo 'Something went wrong';
       endif;
    }

    function getSSSTableEquivalent($salary) {
         $equivalent = $this->payroll_model->getSSSTableEquivalent();
         if ($salary >= 20000):
                 return 800;
         else:
         foreach ($equivalent as $eq):
                 if ($salary >= $eq->ssst_from && $salary <= $eq->ssst_to) {
                         return $eq->ssst_ee;
                 }
         endforeach;

         endif;
    }

    function loadPayrollDeduction() {
         //$accountNumber = $this->post('accountNumber');
         $fromDate = $this->post('dateFrom');
         $toDate = $this->post('dateTo');
         $user_id = $this->post('owners_id');
         $data['pc_code'] = $this->post('pc_code');
         $data['grossPay'] = $this->post('grossPay');
         $data['netPay'] = $this->post('partialNet');
         $data['employee_id'] = $this->post('employee_id');
         //$data['accountNumber'] = $accountNumber;
         $data['startDate'] = $fromDate;
         $data['endDate'] = $toDate;
         $data['owners_id'] = $user_id;

         $this->load->view('payroll/deductions', $data);
    }

    function getManHoursByPP($pp_id) {
    //        $data['pp_id'] = $this->post('pp_id');
         $data['pp_id'] = $pp_id;
         $data['employees'] = Modules::run('hr/getEmployees', 20, 0)->result();
         $data['manHoursCat'] = $this->payroll_model->getManHoursCat();
         $this->load->view('payroll/search', $data);
    }

    function searchEmployee() {
         $value = $this->input->post('value');
         $data['pp_id'] = $this->post('pp_id');
         $data['employees'] = $this->hr_model->searchEmployees($value);
         $data['manHoursCat'] = $this->payroll_model->getManHoursCat();
         $this->load->view('payroll/search', $data);
    }

    function getManHours($employee_id, $pp_id, $pmt_id) {
         $manHour = $this->payroll_model->getManHours($employee_id, $pp_id, $pmt_id);
         return $manHour;
    }

    function saveManHours() {
         $st_id = $this->post('st_id');
         $pp_id = $this->post('pp_id');
         $pmt_id = $this->post('pmt_id');
         $num_hours = $this->post('num_hours');
         $pmh_amount = $this->post('amount');

         $manHourDetails = array(
                 'pmh_st_id' => $st_id,
                 'pmh_pp_id' => $pp_id,
                 'pmh_pmt_id' => $pmt_id,
                 'pmh_num_hours' => $num_hours,
                 'pmh_amount' => $pmh_amount
         );

         $this->payroll_model->saveManHours($manHourDetails, $st_id, $pp_id, $pmt_id);
         return;
    }

    function getManHourTypeByCat($cat_id) {
         $types = $this->payroll_model->getManHourTypeByCat($cat_id);
         return $types;
    }

    function manHours($pp_id = NULL) {
         $data['pp_id'] = $pp_id;
         $data['employees'] = Modules::run('hr/getEmployees', 20, 0);
         $data['payrollPeriod'] = $this->payroll_model->getPayrollPeriod();
         $data['manHoursCat'] = $this->payroll_model->getManHoursCat();
         $data['modules'] = 'hr';
         $data['main_content'] = 'payroll/manHours';
         echo Modules::run('templates/canteen_content', $data);
    }

    function saveShifts() {
         $group_id = $this->post('group_id');
         $shift_id = $this->post('shift_id');

         $current_shift_id = $this->payroll_model->getShiftId($group_id);
         $current_group_id = $this->payroll_model->getGroupId($shift_id);

         $saveShiftDetails = array(
                 'shift_id' => $shift_id
         );

         $this->payroll_model->saveShifts($saveShiftDetails, $group_id);

         $secondShiftDetails = array(
                 'shift_id' => $current_shift_id
         );

         $data = $this->payroll_model->saveShifts($secondShiftDetails, $current_group_id, $current_shift_id);

         $json = array(
                 'group_id' => $current_group_id,
                 'ps_from' => date('g:i:s a', strtotime($data->ps_from)),
                 'ps_to' => date('g:i:s a', strtotime($data->ps_to)),
         );

         echo json_encode($json);
    }

    function getPayrollShift() {
         $payrollShift = $this->payroll_model->getPayrollShift();
         return $payrollShift;
    }

    function getRawTimeShifting() {
         $timeShift = $this->payroll_model->getRawTimeShifting();
         return $timeShift;
    }

    function getTimeShifting($user_id) {
         $timeShift = $this->payroll_model->getTimeShifting($user_id);
         return $timeShift;
    }

    function saveShiftGroup() {
         $user_id = $this->post('user_id');
         $groupings = $this->post('groupings');

         $details = array(
                 'time_group_id' => $groupings
         );

         $this->payroll_model->saveShiftGroup($details, $user_id);
    }

    function getShiftGroupings() {
         $groupings = $this->payroll_model->getShiftGroupings();
         return $groupings;
    }

    function payrollToExcel($startDate, $endDate, $pc_code = NULL) {
         $this->load->helper('file');
         $settings = Modules::run('main/getSet');


         $data['getPayrollReport'] = $this->payroll_model->getPayrollReport();


         $this->load->library('excel');
         $this->load->helper('download');

         $sdateItems = explode('-', $startDate);
         $dateItems = explode('-', $endDate);
         $data['startD'] = $sdateItems[2];
         $data['endD'] = $dateItems[2];

         $data['paymentSchedule'] = $this->hr_model->getPaymentSchedule();
         $data['defaultDeductions'] = $this->payroll_model->getDefaultDeductions();
         $data['hrdb'] = Modules::load('hr/hrdbprocess/');
         $data['startDate'] = $startDate;
         $data['endDate'] = $endDate;
         $data['pc_code'] = $pc_code;



         $this->excel->setActiveSheetIndex(0);
         //name the worksheet

         if (file_exists(APPPATH . 'modules/hr/views/payroll/' . strtolower($settings->short_name) . '_payrollToExcel.php')):
                 $this->load->view('payroll/' . strtolower($settings->short_name) . '_payrollToExcel', $data);
         else:
                 $this->load->view('payroll/payrollToExcel', $data);
         endif;

         $filename = 'payroll.xls'; //save our workbook as this file name
         // $filename=$settings->short_name.'_'.$sy.'_'.$s->course_id.'.xls'; //save our workbook as this file name
         header('Content-Type: application/vnd.ms-excel'); //mime type
         header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
         header('Cache-Control: max-age=0'); //no cache
         //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
         //if you want to save it as .XLSX Excel 2007 format
         $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
         //force user to download the Excel file without writing it to server's HD
         $objWriter->save('php://output');
    }

    public function printPayroll($startDate, $endDate, $pc_code = NULL) {
         $this->load->helper('file');
         $settings = Modules::run('main/getSet');

         $sdateItems = explode('-', $startDate);
         $dateItems = explode('-', $endDate);
         $data['startD'] = $sdateItems[2];
         $data['endD'] = $dateItems[2];

         $data['paymentSchedule'] = $this->hr_model->getPaymentSchedule();
         $data['defaultDeductions'] = $this->payroll_model->getDefaultDeductions();
         $data['hrdb'] = Modules::load('hr/hrdbprocess/');
         $data['startDate'] = $startDate;
         $data['endDate'] = $endDate;


         $firstDay = Modules::run('main/getFirstLastDay', date("F", mktime(0, 0, 0, date('m', strtotime($startDate)), 10)), date('Y', strtotime($startDate)), 'first');
         $lastDay = Modules::run('main/getFirstLastDay', date("F", mktime(0, 0, 0, date('m', strtotime($endDate)), 10)), date('Y', strtotime($endDate)), 'last');
    //            $firstDayName = date('D',  strtotime('first Day of '.date("F", mktime(0, 0, 0, segment_4, 10)).' '.date('Y', strtotime($startDate))));
         $data['hrdb'] = Modules::load('hr/hrdbprocess/');

         //calculation of the Basic Pay and the benefits
         $data['workdays'] = $data['hrdb']->getWorkdays($startDate, $endDate, TRUE);

         $data['daysInAMonth'] = $data['hrdb']->getWorkdays(date('Y-m', strtotime($startDate)) . '-0' . $firstDay, date('Y-m', strtotime($endDate)) . '-' . $lastDay, TRUE);

         $data['getPayrollReport'] = $this->payroll_model->getPayrollReport();
         $data['pc_code'] = $pc_code;
         if (file_exists(APPPATH . 'modules/hr/views/payroll/' . strtolower($settings->short_name) . '_printPayroll.php')):
                 $this->load->view('payroll/' . strtolower($settings->short_name) . '_printPayroll', $data);
         else:
                 $this->load->view('payroll/printPayroll', $data);
         endif;
    }

    function checkTransaction($profile_id, $pc_code) {
         $trans = $this->payroll_model->checkTransaction($profile_id, $pc_code);
         return $trans;
    }

    function getPayrollProfile($em_id) {
         $profile = $this->payroll_model->getPayrollProfile($em_id);
         return $profile;
    }

    function releasePayroll() {
         $em_id = $this->post('em_id');
         $pc_code = $this->post('pc_code');
         $netPay = $this->post('netPay');

         $payrollProfile = $this->getPayrollProfile($em_id);

         $details = array(
                 'ptrans_status' => 1,
                 'ptrans_date_release' => date('Y-m-d G:i:s')
         );
         $saved = $this->payroll_model->releasePayroll($details, $em_id, $pc_code);
         if ($saved):
                 Modules::run('accountingsystem/createJournalEntry', $payrollProfile->pp_act_to, 'Net Payroll of employee ' . $em_id, $netPay, 1);
                 Modules::run('accountingsystem/createJournalEntry', $payrollProfile->pp_debit_to, 'Net Payroll of employee ' . $em_id, $netPay, 0);
                 echo 'Successfully Posted';
         else:
                 echo 'Sorry something went wrong';
         endif;
    }

    function approvePayroll() {
         $em_id = $this->post('em_id');
         $netPay = $this->post('netPay');
         $pc_code = $this->post('pc_code');

         $details = array(
                 'ptrans_profile_id'    => $em_id,
                 'ptrans_pay_code'      => $pc_code,
                 'ptrans_amount'        => $netPay,
                 'ptrans_timestamp'     => date('Y-m-d G:i:s'),
                 'ptrans_status'        => 1
         );

         $saved = $this->payroll_model->approvePayroll($details, $em_id, $pc_code);
         if ($saved):
                 return TRUE;
         else:
                 return FALSE;
         endif;
    }

    function generatePayrollProfile() {
         $this->payroll_model->generatePayrollProfile();
         return;
    }

    function getStatBen($sg_id, $statBen_id) {
         $stats = $this->payroll_model->getStatBen($sg_id, $statBen_id);
         return $stats;
    }

    function setStatBen() {
         $salary = $this->post('salary_grade');
         $statBen = $this->post('statBen');
         $amount = $this->post('amount');
         $ddDate = $this->post('ddDate');

         $details = array(
                 'stat_item_id' => $statBen,
                 'stat_sg_id' => $salary,
                 'stat_amount' => $amount,
                 'stat_ded_sched' => $ddDate
         );

         $saved = $this->payroll_model->setStatBen($details, $statBen, $salary);
         if ($saved):
                 echo 'Successfully Set';
         else:
                 echo 'Sorry Something went wrong';
         endif;
    }

    function addPayrollItems() {
         $itemName = $this->post('itemName');
         $itemType = $this->post('itemType');
         $itemCat = $this->post('itemCat');

         $details = array(
                 'pi_item_name' => $itemName,
                 'pi_item_type' => $itemType,
                 'pi_item_cat' => $itemCat
         );

         $saved = $this->payroll_model->addPayrollItems($details, $itemName);
         if ($saved):
                 echo 'Successfully Added';
         else:
                 echo 'Items already exists';
         endif;
    }

    function getPayrollItems($cat = 1) {
         $items = $this->payroll_model->getPayrollItems($cat);
         return $items;
    }

    function settings() {
         $this->load->helper('file');
         $settings = Modules::run('main/getSet');
         $data['defaultDeductions'] = $this->payroll_model->getDefaultDeductions();
         $data['paymentSchedule'] = $this->hr_model->getPaymentSchedule();
         $data['salaryGrade'] = $this->hr_model->getSalaryGrade();
         $data['time_settings'] = Modules::run('hr/payroll/getRawTimeShifting');
         $data['hrdb'] = Modules::load('hr/hrdbprocess/');
         $data['modules'] = 'hr';

         if (file_exists(APPPATH . 'modules/hr/views/payroll/' . strtolower($settings->short_name) . '_settings.php')):
                 $data['main_content'] = 'payroll/' . strtolower($settings->short_name) . '_settings';
         else:
                 $data['main_content'] = 'payroll/settings';
         endif;

         echo Modules::run('templates/main_content', $data);
    }

    function saveSG() {
         $em_id = $this->post('em_id');
         $salary = $this->post('salary_grade');

         $details = array(
                 'pp_em_id' => $em_id,
                 'pp_sg_id' => $salary
         );

         $sg = $this->payroll_model->saveSG($details, $em_id);
         return $sg;
    }

    function getPayrollReport($pc_code, $withGroup = NULL)
    {
     $payrollReport = $this->payroll_model->getPayrollReport($pc_code, $withGroup);
     return $payrollReport;
    }

    function generatePayrollReport($pc_code = NULL, $startDate = NULL, $endDate = NULL) {
         $this->load->helper('file');
         $settings = Modules::run('main/getSet');

         $sdateItems = explode('-', $startDate);
         $dateItems = explode('-', $endDate);
         $data['startD'] = $sdateItems[2];
         $data['endD'] = $dateItems[2];
         $data['paymentSchedule'] = $this->hr_model->getPaymentSchedule();
         $data['defaultDeductions'] = $this->payroll_model->getDefaultDeductions();
         $data['hrdb'] = Modules::load('hr/hrdbprocess/');
         $data['startDate'] = $startDate;
         $data['endDate'] = $endDate;
         $data['paySched'] = ($sdateItems[2]>15?2:1);
        // $data['transactionNumber'] = Modules::run('coopmanagement/loadReferenceNumber');
         
        $amortization = Modules::run('hr/payroll/loanAmortization', NULL, 0, 1);
        foreach ($amortization as $amort):
            if($amort->pa_payment_type==1):
              if($data['paySched']==1):
                $this->setPayrollCharges($amort->user_id, $amort->pa_item_id, $amort-> pa_amort_amount, $pc_code, $amort->pa_id);
              endif;
            else:
                $this->setPayrollCharges($amort->user_id, $amort->pa_item_id, $amort-> pa_amort_amount, $pc_code, $amort->pa_id);
            endif;
        endforeach;
         
         $data['getPayrollReport'] = $this->payroll_model->getPayrollReport($pc_code);
         $data['manHoursCat'] = $this->payroll_model->getManHoursCat();
         $data['pc_code'] = $pc_code;
         if (file_exists(APPPATH . 'modules/hr/views/payroll/' . strtolower($settings->short_name) . '_payroll_report_table.php')):
            $this->load->view('payroll/'. strtolower($settings->short_name).'_payroll_report_table', $data);
         else:
            $this->load->view('payroll/payroll_report_table', $data);
         endif;
    }

    public function addDeduction() {
         $em_id = $this->post('em_id');
         $pc_code = $this->post('pc_code');
         $amount = $this->post('amount');
         $item_id = $this->post('item_id');
         $amort_id = $this->post('amort_id');
         
         $this->setPayrollCharges($em_id, $item_id, $amount, $pc_code, $amort_id);
    }

    public function getPayrollDefaults($paySched = NULL) {
         $defaults = $this->payroll_model->getPayrollDefaults($paySched);
         return $defaults;
    }

    public function getPayrollCharges($pc_code, $profile_id = NULL) {
         $charges = $this->payroll_model->getPayrollCharges($pc_code, $profile_id);
         return $charges;
    }

    public function getPayrollChargesByItem($item_id, $pc_code, $profile_id = NULL) {
         $charges = $this->payroll_model->getPayrollChargesByItem($item_id, $pc_code, $profile_id);
         return $charges;
    }

    public function getTotalPayrollChargesByItem($item_id, $pc_code) {
         $charges = $this->payroll_model->getTotalPayrollChargesByItem($item_id, $pc_code);
         return $charges;
    }

    public function setPayrollCharges($profile_id, $item_id, $pc_amount, $pc_code, $pc_amort_id = 0) {
         $details = array(
                 'pc_profile_id'    => $profile_id,
                 'pc_amount'        => $pc_amount,
                 'pc_code'          => $pc_code,
                 'pc_item_id'       => $item_id,
                 'pc_amort_id'      => $pc_amort_id
         );

         if($pc_amount!=0):
            $this->payroll_model->setPayrollCharges($details, $profile_id, $item_id, $pc_code, $pc_amount, $pc_amort_id);
         endif;
    }

    public function setPayrollPeriod() {
         $fromDate = $this->post('fromDate');
         $toDate = $this->post('toDate');
         $pc_code = $this->post('pc_code');

         $details = array(
                 'per_from' => $fromDate,
                 'per_to' => $toDate,
                 'per_code' => $pc_code
         );

         $saved = $this->payroll_model->setPayrollPeriod($fromDate, $toDate, $details);
         if ($saved):
                 echo 'Payroll Successfully Set';
         else:
                 echo 'Payroll Period has already been set';
         endif;
    }

    public function create($pc_code = NULL, $startDate = NULL, $endDate = NULL) {
         $data['startDate'] = $startDate;
         $data['endDate'] = $endDate;
         $data['pc_code'] = $pc_code;
         $data['payrollPeriod'] = $this->payroll_model->getPayrollPeriod();
         $data['modules'] = 'hr';
         $data['main_content'] = 'payroll/create';
         echo Modules::run('templates/canteen_content', $data);
    }

    function updateDeduction() {
         $details = array(
                 'pc_profile_id'    => $this->post('pc_profile_id'),
                 'pc_amount'        => $this->post('pc_amount'),
                 'pc_code'          => $this->post('pc_code'),
                 'pc_item_id'       => $this->post('pc_item_id'),
                 'pc_amort_id'      => $this->post('amort_id')
         );
         
         
         if($this->post('amort_id')!=0):
             $this->updateAmortizationStatus($this->post('em_id'), $this->post('pc_item_id'), $this->post('pc_amount'), $this->post('pc_code'));
         endif;
         
        $this->payroll_model->setPayrollCharges($details, $this->post('pc_profile_id'), $this->post('pc_item_id'), $this->post('pc_code'), $this->post('pc_amount'),$this->post('amort_id'));



    }

    function getPayrollChargesById($userProfile, $itemID, $pcCode){
         return $this->payroll_model->getPayrollChargesById($userProfile, $itemID, $pcCode);
    }
 }
 