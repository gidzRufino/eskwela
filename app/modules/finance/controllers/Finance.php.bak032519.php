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
        if(!$this->session->has_userdata('is_logged_in') || !$this->session->is_logged_in){
            header('Location: '.base_url());
            exit;
        }
    }
    
    private function post($name)
    {
        return $this->input->post($name);
    }
    
    function saveRefundTransaction()
    {
        $origAmount = $this->post('origAmount');
        $details = array(
            'ref_number'    => $this->post('ref_number'),
            't_charge_id'   => $this->post('item_id'),
            't_amount'      => $origAmount-$this->post('amount'),
            't_date'        => $this->post('trans_date'),
            't_receipt_type'=> $this->post('receipt')
        );
    
        if($this->finance_model->saveRefundTransaction($details, $this->post('trans_id'), $this->post('school_year'), $origAmount, $this->post('amount'))):
            if($this->post('amount')==$origAmount):
                Modules::run('web_sync/updateSyncController', 'c_finance_transactions', 'trans_id', $this->post('trans_id'), 'delete', 6);
            else:
                Modules::run('web_sync/updateSyncController', 'c_finance_transactions', 'trans_id', $this->post('trans_id'), 'update', 6);
            endif;
            Modules::run('main/logActivity','FINANCE',  $this->session->userdata('name').' has process a refund transaction with reference #'.$this->post('ref_number').' amounting to '. number_format($this->post('amount'),2,'.',',').'.', $this->session->userdata('employee_id'), $this->post('school_year'));
            
            $this->saveFinanceLog($this->session->userdata('employee_id'), $this->session->userdata('name').' has process a refund transaction with reference #'.$this->post('ref_number').' amounting to '. number_format($this->post('amount'),2,'.',',').'.');
            echo 'Transaction Successfully Saved';
        else:
            echo 'Sorry, Something went wrong';
        endif;
        
    }
    
    function loadRefundTransaction()
    {
        $school_year = $this->post('school_year');
        $trans_id = $this->post('trans_id');
        
        $data['fin_items'] = $this->finance_model->getFinItems();
        $data['transaction'] = $this->finance_model->loadFinanceTransaction($trans_id, $school_year);
        $this->load->view('refundTransaction', $data);
    }

    function searchFinanceStaff($value, $year=NULL)
    {
        $employee = $this->finance_model->searchFinanceStaff($value, $year);
        echo '<ul>';
        foreach ($employee as $s):
        ?>
                <li style="font-size:18px;" onclick="$('#searchName').hide(), $('#searchBox').val('<?php echo $s->firstname.' '.$s->lastname ?>'), $('#assign_employee_id').val('<?php echo $s->employee_id ?>')" ><?php echo strtoupper($s->firstname.' '.$s->lastname) ?></li>   
        <?php        
        endforeach;
        echo '</ul>';
        
    }
    
    public function getIndividualTransactionByItemId($st_id, $sem, $school_year, $item_id=NULL, $type=NULL)
    {
        $transaction = $this->finance_model->getIndividualTransactionByItemId($st_id, $sem, $school_year, $item_id, $type);
        return $transaction;
    }
    
    function getFinanceRevenue($grade_id=NULL, $school_year = NULL)
    {
        $students = Modules::run('registrar/getAllStudentsForExternal', $grade_id, NULL, NULL, 1);
        
        $totalTutionPayment = 0;
        $totalTutionDiscount = 0;
        $tuitionC = 0;
        $registrationC = 0;
        $totalRegPayment = 0;
        $totalRegDiscount = 0;
        
        foreach ($students->result() as $st):
            
            $plan = Modules::run('finance/getPlanByCourse', $grade_id, 0);
            $charges = Modules::run('finance/financeChargesByPlan',0, $school_year, 0, $plan->fin_plan_id );
            foreach ($charges as $c):
                switch ($c->item_id):
                    case 1:
                        $tuitionC = $c->amount;
                    break;    
                    case 2:
                        $registrationC = $c->amount;
                    break;    
                endswitch;
            endforeach;
            $tuitionP = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 1);
            $tuitionD = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 1, 2);
            $regP = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 2);
            $regD = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 2, 2);
        
            if($tuitionP->num_rows()!=0):
                foreach ($tuitionP->result() as $chrg):
                        $totalTuitionPayment += $chrg->t_amount;
                endforeach;
            else:
                $totalTutionPayment = 0;
            endif;
            
            if($tuitionD->num_rows()!=0):
                foreach ($tuitionD->result() as $td):
                        $totalTuitionDiscount += $td->t_amount;
                endforeach;
            else:
                $totalTutionDiscount = 0;
            endif;
        
            if($regP->num_rows()!=0):
                foreach ($regP->result() as $chrg):
                        $totalRegPayment += $chrg->t_amount;
                endforeach;
            else:
                $totalTutionPayment = 0;
            endif;
            
            if($regD->num_rows()!=0):
                foreach ($regD->result() as $td):
                        $totalRegDiscount += $td->t_amount;
                endforeach;
            else:
                $totalRegDiscount = 0;
            endif;
            
        endforeach;
        
            $details = array(
                'tuitionC'          => $tuitionC,
                'tuitionDiscount'   => $totalTuitionDiscount,
                'tuitionPayment'    => $totalTuitionPayment,
                'registrationC'     => $registrationC,
                'regDiscount'       => $totalRegDiscount,
                'regPayment'        => $totalRegPayment
            );
            
            print_r($details);
            //echo json_encode($details);
    }
    
    function loadFinanceDetails($grade_id=NULL, $school_year = NULL)
    {
        $this->load->library('excel');
        
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(12);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1')->getNumberFormat('General');
        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        
        
        $this->excel->getActiveSheet()->setCellValue('A1', 'Lastname');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Firstname');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Tuition Charges');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Tuition Discount');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Tuition Payment');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Registration Charges');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Registration Discount');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Registration Payment');
        
        $students = Modules::run('registrar/getAllStudentsForExternal', $grade_id, NULL, NULL, 1);
        
        $totalPayment = 0;
        $totalTutionPayment = 0;
        $totalTutionDiscount = 0;
        $countColumn = 3;
        $tuitionC = 0;
        $registrationC = 0;
        $totalRegPayment = 0;
        $totalRegDiscount = 0;
        
        foreach ($students->result() as $st):
            
            $plan = Modules::run('finance/getPlanByCourse', $grade_id, 0);
            $charges = Modules::run('finance/financeChargesByPlan',0, $school_year, 0, $plan->fin_plan_id );
            foreach ($charges as $c):
                switch ($c->item_id):
                    case 1:
                        $tuitionC = $c->amount;
                    break;    
                    case 2:
                        $registrationC = $c->amount;
                    break;    
                endswitch;
            endforeach;
            $tuitionP = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 1);
            $tuitionD = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 1, 2);
            $regP = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 2);
            $regD = $this->getIndividualTransactionByItemId($st->st_id, 0, $school_year, 2, 2);
        
            if($tuitionP->num_rows()!=0):
                foreach ($tuitionP->result() as $chrg):
                        $totalTutionPayment += $chrg->t_amount;
                endforeach;
            else:
                $totalTutionPayment = 0;
            endif;
            
            if($tuitionD->num_rows()!=0):
                foreach ($tuitionD->result() as $td):
                        $totalTutionDiscount += $td->t_amount;
                endforeach;
            else:
                $totalTutionDiscount = 0;
            endif;
        
            if($regP->num_rows()!=0):
                foreach ($regP->result() as $chrg):
                        $totalRegPayment += $chrg->t_amount;
                endforeach;
            else:
                $totalTutionPayment = 0;
            endif;
            
            if($regD->num_rows()!=0):
                foreach ($regD->result() as $td):
                        $totalRegDiscount += $td->t_amount;
                endforeach;
            else:
                $totalRegDiscount = 0;
            endif;
            //$overAllPayment += $totalPayment;
            $countColumn++;
            $this->excel->getActiveSheet()->setCellValue('A'.$countColumn, strtoupper($st->lastname));
            $this->excel->getActiveSheet()->setCellValue('B'.$countColumn, strtoupper($st->firstname));
            $this->excel->getActiveSheet()->setCellValue('C'.$countColumn, strtoupper($tuitionC));
            $this->excel->getActiveSheet()->setCellValue('D'.$countColumn, strtoupper($totalTutionDiscount));
            $this->excel->getActiveSheet()->setCellValue('E'.$countColumn, strtoupper($totalTutionPayment));
            $this->excel->getActiveSheet()->setCellValue('F'.$countColumn, strtoupper($registrationC));
            $this->excel->getActiveSheet()->setCellValue('G'.$countColumn, strtoupper($totalRegDiscount));
            $this->excel->getActiveSheet()->setCellValue('H'.$countColumn, strtoupper($totalRegPayment));
            
            $totalPayment = 0;
            $tuitionC = 0;
            $totalTutionPayment = 0;
            $totalTutionDiscount = 0;
            $grade_level = $st->level;
            $totalRegPayment=0;
            $totalRegDiscount =0;
        endforeach;
        
        $this->excel->getActiveSheet()->setTitle($grade_level);
        
        $filename=$grade_level.' Tuition_Reg Summary.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    function getCollectionPerGradeLevel($grade_id,$school_year, $item_id)
    {
        $collection = $this->finance_model->getCollectionPerGradeLevel($grade_id,$school_year, $item_id);
    }
    
    function loadORDetails($ref_number, $school_year)
    {
        $data['or_number'] = urldecode($ref_number);
        $data['orDetails'] = $this->finance_model->loadORDetails(urldecode($ref_number), $school_year);
        $this->load->view('reports/searchOR', $data);
    }
    
    function searchReceipt($value=NULL, $school_year=NULL)
    {
        $result = $this->finance_model->searchReceipt($value, $school_year);
        echo '<ul>';
        foreach ($result as $r):
        ?>
            <li style="font-size:18px;" onclick="$('#searchReceipt').hide(), $('#searchReceiptBox').val('<?php echo $r->ref_number ?>'),loadORDetails('<?php echo $r->ref_number ?>', '<?php echo $school_year ?>')" ><?php echo $r->ref_number ?></li>   
        <?php        
        endforeach;
        echo '</ul>';
    }
    
    function getMOP($mop)
    {
        $mode = $this->finance_model->getMOP($mop);
        return $mode;
    }
    
    function saveMOP()
    {
        $user_id = $this->post('user_id');
        $mop = $this->post('mop');
        if($this->finance_model->saveMOP($user_id, $mop)):
            echo 'Successfully Updated';
        else:
            echo 'Something Went Wrong on MOP Assignment';
        endif;
    }
    
    function printRevenueCollection($option = NULL, $school_year = NULL)
    {
        $settings = Modules::run('main/getSet');
        $data['gradeLevel'] = Modules::run('registrar/getGradeLevel');
        $data['school_year'] = ($school_year==NULL?$this->session->school_year:$school_year);
        $data['next'] = $data['school_year'] + 1;

        $totalStudents = 0;
        $totalTuition = 0;
        $totalRegistration = 0;
        foreach($data['gradeLevel'] as $gl):
            $students = Modules::run('registrar/getAllStudentsForExternal', $gl->grade_id);
            $charges = $this->getFinanceCharges($gl->grade_id, 0, ($school_year==NULL?$this->session->school_year:$school_year), 0);
            foreach ($charges as $charge):
                switch($charge->item_id):
                    case 1:
                        $tuition = $charge->amount*$students->num_rows();
                        $totalTuition += $tuition;
                    break;
                    case 2:
                        $registration = $charge->amount * $students->num_rows();
                        $totalRegistration += $registration;
                    break;    
                endswitch;
            endforeach;
            $totalStudents += $students->num_rows();
            echo $gl->level.' - '.$students->num_rows().' | _____ '. number_format($tuition,2,'.',',').' | ____ '. number_format($registration,2,'.',',').'<br />';
           
        endforeach;
            echo '<br />'.$totalStudents.' | _____ '. number_format($totalTuition,2,'.',',').' | ____ '. number_format($totalRegistration,2,'.',',');
    }

    function saveEditTransaction()
    {
        $details = array(
            'ref_number'    => $this->post('ref_number'),
            't_charge_id'   => $this->post('item_id'),
            't_amount'      => $this->post('amount'),
            't_date'        => $this->post('trans_date'),
            't_receipt_type'=> $this->post('receipt')
        );
        
        if($this->finance_model->saveEditTransaction($details, $this->post('trans_id'), $this->post('school_year'))):
            Modules::run('web_sync/updateSyncController', 'c_finance_transactions', 'trans_id', $this->post('trans_id'), 'update', 6);
            Modules::run('main/logActivity','FINANCE',  $this->session->userdata('name').' has edit a transaction with reference #'.$this->post('ref_number').'.', $this->session->userdata('employee_id'), $this->post('school_year'));
            $this->saveFinanceLog($this->session->userdata('employee_id'), 'Has edit a transaction with reference #'.$this->post('ref_number').'.');
            
            
            echo 'Transaction Successfully Saved';
        else:
            echo 'Sorry, Something went wrong';
        endif;
        
    }
    
    function loadFinanceTransaction()
    {
        $school_year = $this->post('school_year');
        $trans_id = $this->post('trans_id');
        
        $data['fin_items'] = $this->finance_model->getFinItems();
        $data['transaction'] = $this->finance_model->loadFinanceTransaction($trans_id, $school_year);
        $this->load->view('editTransaction', $data);
    }
    
    function saveCashBreakDown()
    {
        $transaction = $this->post('items');
        $count = count(json_decode($transaction));
        $final = json_decode($transaction);
        
        $column = array();
        
        for ($x = 0; $x < $count; $x++) {
            //$items = explode('_', $final[$x]);
            $details = array(
                'fdcb_denom_id'     => $final[$x]->den_id,
                'fdcb_total_count'  => $final[$x]->count,
                'fdcb_date'         => date('Y-m-d')

            );
            
            array_push($column, $details);
        }
        //print_r($final) ;
        if($this->finance_model->saveCashBreakDown($column)):
            echo 'Successfully Saved';
        else:
            echo 'Sorry Something went wrong';
        endif;
    }
    
     function getCashDenomination()
    {
        $q = $this->db->get('c_finance_cash_denomination');
        return $q->result();
    }
    
    function getChequePayments($teller_id, $date)
    {
        $payments = $this->finance_model->getChequePayments($teller_id, $date);
        return $payments;
    }
    
    function getBank($id=NULL)
    {
        $bank = $this->finance_model->getBank($id);
        return $bank;
    }
    
    function getEncashments($teller_id, $date)
    {
        $encashments = $this->finance_model->getEncashments($teller_id, $date);
        return $encashments;
    }
    
    function saveEncashments()
    {
        $bank = $this->post('bank');
        $chequeNum = $this->post('chequeNumber');
        $chequeAmount = $this->post('chequeAmount');
        $chequeDate = $this->post('chequeDate');
        
        $details = array(
            'encash_bank_id'    => $bank,
            'encash_cheque_num' => $chequeNum,
            'encash_amount'     => $chequeAmount,
            'encash_date'       => $chequeDate,
            'encash_teller_id'  => $this->session->employee_id
        );
        
        if($this->finance_model->saveEncashments($details)):
            echo 'Cheque Encashments Successfully saved';
        else:
            echo 'Sorry something went wrong';
        endif;
    }
    
    function addBank()
    {
        $bank = $this->post('bank');
        $bankShortName = $this->post('bankShortName');
        $result = $this->finance_model->addBank(array('bank_name' => $bank, 'bank_short_name'=>$bankShortName), $bank);
        $result = json_decode($result);
        if($result->status):
            echo "<option value='$result->id'>$result->value</option>";
        endif;
    }
    
    public function getTotalCollectionPerItem($teller_id, $item_id, $date, $t_type)
    {
        $collection = $this->finance_model->getTotalCollectionPerItem($teller_id, $item_id, $date, $t_type);
        foreach ($collection as $col):
            $amount += $col->t_amount;
        endforeach;
        return $amount;
    }
    
    public function getTotalGroupCollection($from, $to, $receipt_type=NULL)
    {
        $collection = $this->finance_model->getTotalGroupCollection($from, $to, $receipt_type);
        return $collection;
    }


    private function getTransactionByOR($or_num, $school_year = NULL)
    {
        $trans = $this->finance_model->getTransactionByOR($or_num, $school_year);
        return $trans;
    }
    
    public function updateOR($or_num)
    {
        $this->finance_model->updateORSeries($or_num);
        return;
    }
    
    function getOtherAccountDetails($st_id)
    {
        $details = $this->finance_model->getOtherAccountDetails($st_id);
        return $details;
    }
    
    public function printOtherOR($st_id, $ornum, $transType)
    {
        $data['st_id'] = base64_decode($st_id);
        $data['ornum'] = $ornum;
        $data['transType'] = $transType;
        $data['transaction'] = $this->getTransactionByOR($ornum);
        $data['student'] = $this->getOtherAccountDetails(base64_decode($st_id));
        $data['modules'] = 'finance';
        $data['main_content'] = 'reports/printOtherOR';
        echo Modules::run('templates/print_content', $data);
       // $this->load->view('reports/printOR', $data);
    }
    
    public function printOR($st_id, $ornum, $transType, $amount, $school_year)
    {
        $data['st_id'] = base64_decode($st_id);
        $data['ornum'] = $ornum;
        $data['transType'] = $transType;
        $data['transaction'] = $this->getTransactionByOR($ornum, $school_year);
        $data['student'] = Modules::run('registrar/getSingleStudent', base64_decode($st_id), $school_year);
        $data['modules'] = 'finance';
        $data['main_content'] = 'reports/printOR';
        echo Modules::run('templates/print_content', $data);
       // $this->load->view('reports/printOR', $data);
    }
    
    public function setPrintSettings($v)
    {
        $this->finance_model->setPrintSettings($v);
        return;
    }
    
    public function getSettings()
    {
        $settings = $this->finance_model->getSettings();
        return $settings;
    }
    
    public function getSingleSeries($employee_id)
    {
        $series = $this->finance_model->getSingleSeries($employee_id);
        return $series;
    }


    public function useSeries()
    {
        if($this->finance_model->useSeries($this->post('id'), $this->post('employee_id'))):
            $this->saveFinanceLog($this->session->userdata('employee_id'), 'Assigned a Receipt Series id #'.$this->post('id').' to '.$this->post('employee_id'));
            echo 'Successfully Assigned';
        endif;
    }
    
    public function getAllSeries()
    {
        $series  = $this->finance_model->getAllSeries();
        return $series;
    }
    
    public function addSeries()
    {
        $begin      = $this->post('beginning');
        $end        = $this->post('ending');
        $current    = $this->post('current');
        $series_id   = $this->post('series_id');
        
        $details    = array(
            'or_begin'  => $begin,
            'or_end'    => $end,
            'or_current'=> $current,
            'or_id'     => $series_id
        );
        
        if($series_id==0):
            $msg = $this->finance_model->addSeries($details, $begin, $end, $series_id);
        else:
            $msg = $this->finance_model->updateSeries($details, $series_id);
        endif;
        
        echo $msg;
    }

    public function OR_series()
    {
        if($this->session->userdata('is_logged_in')):
            $data['ORSeries'] = $this->getAllSeries();
            $data['FinSettings'] = $this->getSettings();
            $data['main_content'] = "or_series";
            $data['modules'] = 'finance';
            echo Modules::run('templates/college_content', $data);
        else:
            redirect('login');
        endif;
    }

    public function updateCollectibles($user_id, $st_id, $balance, $sy, $semester = NULL)
    {
        $details = array(
            'fc_user_id'    => $user_id,
            'fc_st_id'      => $st_id,
            'balance'       => $balance,
            'last_update'   => date('Y-m-d G:i:s'),
            'fc_school_year'   => $sy,
            'fc_semester'      => ($semester==NULL?0:$semester)
        );
        
        $this->finance_model->updateCollectibles($details, $st_id, $sy, ($semester==NULL?0:$semester));
        return TRUE;
    }


    public function generateCollectibles($sy, $semester=NULL)
    {
        $allStudents  = $this->finance_model->getStudents("", "",$sy);
        $totalBalance = 0;
        foreach($allStudents->result() as $as):
            $AD = json_decode(Modules::run('finance/getRunningBalance', base64_encode($as->st_id), $sy));
            $balance = $AD->charges - $AD->payments;
            $this->updateCollectibles($as->uid, $as->st_id, $balance, $sy, $semester);
            $totalBalance += $balance;
        endforeach;
        
        echo json_encode(array('totalBalance' => number_format($totalBalance,2,'.',',')));
    }
    
    public function getCollectible($sy = NULL, $limit=NULL, $offset=NULL )
    {   
	$this->load->library('pagination');
       
        if($sy==NULL):
            $year = $this->session->userdata('school_year');
        else:
            $year = $sy;
        endif;
         //echo $year;
        if($this->uri->segment(4)==NULL):
            $seg = $this->uri->segment(4);
            $base_url = base_url('finance/getCollectible/'.$year);
        else:
            $seg = $this->uri->segment(5);
            $base_url = base_url('finance/getCollectible/'.$year.'/1');
        endif;
        $totalBalance = 0;
        $result = $this->finance_model->getCollectibles(NULL,NULL,$year);
        foreach ($result->result() as $r):
            $totalBalance += $r->balance;
        endforeach;
        $config['base_url'] = $base_url;
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 30;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';


        $this->pagination->initialize($config);
        $data['links'] = $this->pagination->create_links();

        $page = $this->finance_model->getCollectibles($config['per_page'], $seg, $year);
        $data['students'] = $page->result();
        $data['totalBalance'] = number_format($totalBalance, 2,'.',',');
        $data['school_year'] = $year;
        $data['modules'] = 'finance';
        $data['main_content'] = 'collectible';
        echo Modules::run('templates/canteen_content', $data);
    }
    
    public function getRunningBalance($st_id, $school_year, $sem=NULL)
    {
        $student = Modules::run('registrar/getSingleStudent', base64_decode($st_id), $school_year);

        $plan = Modules::run('finance/getPlanByCourse', $student->grade_id, 0);
        $charges = Modules::run('finance/financeChargesByPlan',0, $school_year, 0, $plan->fin_plan_id );
                
        $i=1;
        $total=0;
        $amount=0;
        if($student->u_id==""):
            $user_id = $student->us_id;
        else:
            $user_id = $student->u_id;
        endif;

        foreach ($charges as $c):
             $next = $c->school_year + 1;
            if($student->grade_id==12 || $student->grade_id==13):
                if($student->status !=2):
                    $total += $c->amount;
                else:
                    if($c->item_description!='Tuition Fee' && $c->item_description!='Misc Fee'): 
                        $total += $c->amount;
                    endif;
                endif;
            else:
                $total += $c->amount;
            endif;
        endforeach;
        $totalExtra = 0;
        $extraCharges = Modules::run('finance/getExtraFinanceCharges',$user_id, 0, $student->school_year);
        if($extraCharges->num_rows()>0):
            foreach ($extraCharges->result() as $ec):
            
                $totalExtra += $ec->extra_amount;
            endforeach;
            $total = $total + $totalExtra;
        endif;
        
        //transaction
        $transaction = Modules::run('college/finance/getTransaction', $student->uid, 0, $student->school_year);
        $paymentTotal = 0;
        if($transaction->num_rows()>0):
            foreach ($transaction->result() as $tr):
                $paymentTotal += $tr->t_amount ;
            endforeach;
        endif;
        
        $details = array(
           'charges' => $total,
           'payments' => $paymentTotal
        );
        
        return json_encode($details);
            
    }
        
    public function index()
    {
        if($this->session->userdata('is_logged_in')):
            $data['gradeLevel'] = Modules::run('registrar/getGradeLevel');
            $data['ro_years'] = Modules::run('registrar/getROYear');
            $data['now'] = $this->session->school_year;
            $data['nextYear'] = $this->session->school_year+1;
            $data['fin_items'] = $this->finance_model->getFinItems($this->session->school_year);
            $data['modules'] = 'finance';
            $data['main_content'] = 'finance_settings';
            echo Modules::run('templates/college_content', $data);
        else:
            redirect('login');
        endif;    
    }
    
    public function settings($school_year=NULL)
    {
        $data['gradeLevel'] = Modules::run('registrar/getGradeLevel');
        $data['ro_years'] = Modules::run('registrar/getROYear');
        $data['now'] = $school_year;
        $data['nextYear'] = $school_year+1;
        $data['fin_items'] = $this->finance_model->getFinItems($school_year);
        $data['modules'] = 'finance';
        $data['main_content'] = 'finance_settings';
        echo Modules::run('templates/college_content', $data);
        
    }
    
    function getFinanceItems()
    {
        $items = $this->finance_model->getFinItems();
        return $items;
    }
    
    function saveFinanceLog($id, $remarks)
    {
        $logDetails = array(
                'account_id' => $id,
                'remarks'    => $remarks
        );
        
        $this->finance_model->saveFinanceLog($logDetails);
    }
    
    function getFinanceAccount($user_id)
    {
        $result = $this->finance_model->getFinanceAccount($user_id);
        return $result;
    }
    
    function updateAccount()
    {
        $user_id = $this->post('user_id');
        $account = $this->post('account');
        
        $details = array(
            'actng_id' => $account
        );
        if($this->finance_model->updateAccount($user_id, $details)):
            echo 'Successfully Updated';
        else:
            echo 'Something went wrong';
        endif;
    }
    
    function getFinSet($school_year = NULL)
    {
        $result = $this->finance_model->getFinSet($school_year);
        return $result;
    }
    
    
    function printCollectionReportPerTeller($teller_id, $from=NULL, $to=NULL)
    {
        $this->load->library('pdf');
        $this->load->helper('file');
        $settings = Modules::run('main/getSet');
        $data['collection'] = $this->finance_model->printCollectionReportPerTeller($from, $to, base64_decode($teller_id));
        
        if(file_exists(APPPATH.'modules/finance/views/reports/'. strtolower($settings->short_name).'_printCollectionReportPerTeller.php')):
            $this->load->view('finance/reports/'. strtolower($settings->short_name).'_printCollectionReportPerTeller', $data);
        else:
            $this->load->view('finance/reports/printCollectionReportPerItem', $data);
        endif;
        
    }
    
    function printCollectionReport($from=NULL, $to=NULL, $report_type=NULL)
    {
        $this->load->library('pdf');
        switch ($report_type):
            case 1:
                $data['collection'] = $this->finance_model->getCollection($from, $to, $report_type);
                $this->load->view('finance/reports/printCollectionReportTuition', $data);
            break;    
            case 2:
                $data['collection'] = $this->finance_model->getCollection($from, $to, $report_type);   
                $data['RSDM'] = $this->finance_model->getRSDM();   
                $this->load->view('finance/reports/printCollectionReportRSDM', $data);
            break;    
            case 3:
                $data['collection'] = $this->finance_model->getCollection($from, $to, $report_type);   
                $this->load->view('finance/reports/printCollectionReportAR', $data);
            break;    
            case 4:
                $data['cashbreakdown'] = $this->finance_model->getCashbreakdown($from);   
                $this->load->view('finance/reports/printCashBreakdown', $data);
            break;    
            case 5:
                $data['collection'] = $this->finance_model->getChequePaymentsByRange($from, $to);
                $this->load->view('finance/reports/printChequePayments', $data);
            break;    
            default:
                $data['collection'] = $this->finance_model->getCollection($from, $to, $report_type);
                $this->load->view('finance/reports/printCollectionReport', $data);
            break;    
        endswitch;
        
    }
    
    public function getRSDMCollection($item_id, $st_id,$from=NULL, $to=NULL )
    {
        $collection = $this->finance_model->getRSDMCollection($item_id, $st_id,$from, $to);
        return $collection;
    }
    
    function getCollectionReport($from=NULL, $to=NULL)
    {
       $collection = $this->finance_model->getCollection($from, $to);
       return $collection;
    }
    
    
    public function collectionReport($from=NULL, $to=NULL, $report_type=NULL)
    {
        $settings = Modules::run('main/getSet');
        $data['ro_year'] = Modules::run('registrar/getROYear');
        $data['collection'] = $this->finance_model->getCollection($from, $to, $report_type);
        $data['modules'] = 'finance';
        if(file_exists(APPPATH.'modules/finance/views/'.strtolower($settings->short_name).'_sales.php')):
            $data['main_content'] = strtolower($settings->short_name).'_sales';
        else:
            $data['main_content'] = 'sales';
        endif;
        
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
        
        $charges = $this->getChargesByItemId($item_id, 0, $school_year, $plan_id);
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
    
    public function getTotalDiscount($st_id, $type)
    {
        $transaction = $this->finance_model->getTotalDiscount($st_id, $type);
        return $transaction;
    }
    
    public function getTransactionByItemId($st_id, $sem, $school_year, $item_id=NULL, $type=NULL)
    {
        $transaction = $this->finance_model->getTransactionByItemId($st_id, $sem, $school_year, $item_id, $type);
        return $transaction;
    }


    public function getDiscountsByItemId($st_id, $sem, $school_year, $item_id=NULL)
    {
        $disc = $this->finance_model->getDiscountsByItemId($st_id, $sem, $school_year, $item_id);
        return $disc;
    }
    
    public function getTransaction($st_id, $sem, $school_year)
    {
        $transaction = $this->finance_model->getTransaction($st_id, $sem, $school_year);
        return $transaction;
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
    
    public function deleteFinanceExtraCharge()
    {
        $school_year = $this->post('school_year');
        $trans_id = $this->post('trans_id');
        $delete_remarks = $this->post('delete_remarks');
        if($this->finance_model->deleteFinanceExtraCharge($trans_id, $school_year)):
            echo 'Successfully Deleted';
            $this->saveFinanceLog($this->session->userdata('employee_id'), $delete_remarks);
        else:
            echo 'An Error has Occur';
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
                if($this->finance_model->deleteTransaction($trans_id, $school_year)):
                    
                    $this->saveFinanceLog($this->session->userdata('employee_id'), 'Voided a Transaction with Discount : '.$delete_remarks);
                    
                    echo 'Successfully Deleted';
                else:
                    echo 'An Error has Occur';
                endif;
            endif;
        else:
            if($this->finance_model->deleteTransaction($trans_id, $school_year)):
                $this->saveFinanceLog($this->session->userdata('employee_id'), $delete_remarks);
                echo 'Successfully Deleted';
            else:
                echo 'An Error has Occur';
            endif;
        endif;
        
    }

    public function saveOtherTransaction()
    {
        $school_year = $this->post('school_year');
        $sel_sy = $this->post('sel_sy');
        $grade_level = $this->post('sel_grade_level');
        $semester = $this->post('sem');
        $or_num = $this->post('or_num');
        $chequeBank = $this->post('chequeBank');
        $inputCheque = $this->post('inputCheque');
        $lastname = $this->post('lastname');
        $firstname = $this->post('firstname');
        $transaction = $this->post('items');
        $transDate = $this->post('transDate');
        $receipt = $this->post('receipt');
        $t_remarks = $this->post('t_remarks');
        $transType = $this->post('transType');
        $count = count(json_decode($transaction));
        $final = json_decode($transaction);
        $column = array();
        
        // $account_number = date('ymdHis');
        // print_r('acct:'.$account_number);
        $account = json_decode($this->finance_model->saveAccount($lastname, $firstname, $sel_sy, $grade_level));
        // if($account->isRegistered):
        //     $account_number = $account->account_number;
        // endif;
        $account_number = $account;
        
        $success = 0;
        for ($x = 0; $x < $count; $x++) {
            
            $items = explode('_', $final[$x]);
            
            $details = array(
                'ref_number'        => $or_num,
                't_st_id'           => $account_number,
                't_em_id'           => $this->session->userdata('employee_id'),
                't_amount'          => $items[1],
                't_charge_id'       => $items[0],
                't_type'            => $transType,
                'bank_id'           => $chequeBank,
                'tr_cheque_num'     => $inputCheque,
                't_date'            => $transDate,
                't_sem'             => $semester,
                't_school_year'     => $school_year, 
                't_receipt_type'    => $receipt,
                't_remarks'         => $t_remarks,
                'acnt_type'         => 1
            );
            
            $result = $this->finance_model->saveTransaction($details);
            if(!$result):
            else:
                $success++;
                Modules::run('web_sync/updateSyncController', 'c_finance_transactions', 'trans_id', $result, 'create', 6);
            endif;
        }
        
            $this->updateOR($or_num);
            if($success==$count):
                echo json_encode(array('accountNumber' => base64_encode($account_number)));
            endif;
            
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
        $transType = $this->post('transType');
        $tcomment = $this->post('tcomment');
        $icomment = $this->post('icomment');
        $chequeNumber = $this->post('chequeNumber');
        $bank = $this->post('bank');
        $count = count(json_decode($transaction));
        $final = json_decode($transaction);
        $column = array();
        $success = 0;
        for ($x = 0; $x < $count; $x++) {
            
            $items = explode('_', $final[$x]);
            $comment = "";
            if ($items[0]=='1') {
                $comment = $tcomment;
            }
            if ($items[0]=='10') {
                $comment = $icomment;
            }
            
            $details = array(
                'ref_number'        => $or_num,
                't_st_id'           => $st_id,
                't_em_id'           => $this->session->userdata('employee_id'),
                't_amount'          => $items[1],
                't_charge_id'       => $items[0],
                't_type'            => $transType,
                'bank_id'           => $bank,
                'tr_cheque_num'     => $chequeNumber,
                't_date'            => $transDate,
                't_sem'             => $semester,
                't_school_year'     => $school_year, 
                't_receipt_type'    => $receipt,
                't_remarks'         => $t_remarks,
                't_comment'         => $comment,
            );
            
            //array_push($column, $details);
            //$this->finance_model->saveTransaction($details,$school_year);
            $result = $this->finance_model->saveTransaction($details,$school_year);
            if(!$result):
            else:
                $success++;
                Modules::run('web_sync/updateSyncController', 'c_finance_transactions', 'trans_id', $result, 'create', 6);
            endif;
            
        }
            if($this->post('isEnrolled')==0):
                $this->finance_model->updateStudentStatus($this->post('admission_id'), $school_year);
            endif;
            if($success==$count):
                echo 'Successfully Saved';
            endif;
//         if($this->finance_model->saveTransaction($column)):
//            if($this->post('isEnrolled')==0):
//                $this->finance_model->updateStudentStatus($this->post('admission_id'));
//            endif;
//            echo 'Successfully Saved';
//        else:
//            echo 'Sorry Something went wrong';
//        endif;
        
    }

    public function addFinanceTransaction($ref_num, $st_id, $amount, $charge_id, $t_type, $sem, $school_year, $date=NULL, $disc_id = NULL)
    {
        $details = array(
           'ref_number'     => $ref_num,
            't_st_id'       => $st_id,
            't_em_id'       => $this->session->userdata('employee_id'),
            't_amount'      => $amount,
            't_charge_id'   => $charge_id,
            't_type'        => $t_type,
            'disc_id'       => ($disc_id==NULL?0:$disc_id),
            't_date'        => ($date==NULL?date('Y-m-d'):$date),
            't_sem'         => $sem,
            't_school_year' => $school_year
        );
        
        $result = $this->finance_model->addFinanceTransaction($details, $school_year);
        if(!$result):
            return FALSE;
        else:
            Modules::run('web_sync/updateSyncController', 'c_finance_transactions', 'trans_id', $result, 'create', 6);
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
        
        $charge = $this->getChargesByItemId($item, $sem, $sy, $plan_id);
        $totalDiscount = $this->getTotalDiscount($st_id, 2);
//        if($item==1):
//            $charges = ($charge->row()->amount-$totalDiscount) * $amount;
//        else:
//            $charges = ($charge->row()->amount) * $amount;
//        endif;
        $charges = ($charge->row()->amount) * $amount;
        if($discount_type==1):
            $charges = $amount;
        endif;
        
        $discountDetails = array(
            'disc_st_id'        => $st_id,
            'disc_type'         => $discount_type,
            'disc_amount'       => $amount,
            'disc_item_id'      => $item,
            'disc_remarks'      => $remarks,
            'disc_sem'          => $sem,
            'disc_school_year'  => $sy
        );
        
        $disc_id = $this->finance_model->applyDiscounts($discountDetails, $school_year);
        Modules::run('web_sync/updateSyncController', 'c_finance_discounts', 'disc_id', $disc_id, 'create', 6);
        $this->addFinanceTransaction(date('Ymdgis'), $st_id, round($charges, 2, PHP_ROUND_HALF_DOWN), $item, 2, $sem, $sy, NULL, $disc_id);
        return TRUE;
        
        
    }
    
    function getExtraFinanceCharges($st_id, $sem, $school_year, $item_id=NULL)
    {
        $charges = $this->finance_model->getExtraFinanceCharges($st_id, $sem, $school_year, $item_id);
        return $charges;
    }
            
    function viewLoadAccountDetails($st_id, $school_year = NULL)
    {
        $this->load->helper('file');
        $settings = Modules::run('main/getSet');
        $data['settings'] = $settings;
        $data['financeSettings'] = $this->getSettings();
        $data['series'] = $this->getSingleSeries($this->session->userdata('employee_id'));
        $data['school_year'] = ($school_year==NULL?$this->session->userdata('school_year'):$school_year);
        $data['ro_years'] = Modules::run('registrar/getROYear');
        $data['fin_items'] = $this->finance_model->getFinItems($data['school_year']);
        $data['st_id'] = $st_id;
        if(file_exists(APPPATH.'modules/finance/views/'. strtolower($settings->short_name).'_viewFinanceAccounts.php')):
            $this->load->view('finance/'. strtolower($settings->short_name).'_viewFinanceAccounts', $data);
        else:
            $this->load->view('finance/viewAccountDetails', $data);
        endif;
    }
            
    function loadAccountDetails($st_id, $school_year = NULL)
    {
        $this->load->helper('file');
        $settings = Modules::run('main/getSet');
        $data['settings'] = $settings;
        $data['financeSettings'] = $this->getSettings();
        $data['series'] = $this->getSingleSeries($this->session->employee_id);
        $data['school_year'] = ($school_year==NULL?$this->session->userdata('school_year'):$school_year);
        $data['ro_years'] = Modules::run('registrar/getROYear');
        $data['fin_items'] = $this->finance_model->getFinItems($data['school_year']);
        $data['st_id'] = $st_id;
        if(file_exists(APPPATH.'modules/finance/views/'. strtolower($settings->short_name).'_accountDetails.php')):
            $this->load->view('finance/'. strtolower($settings->short_name).'_accountDetails', $data);
        else:
            $this->load->view('finance/accountDetails', $data);
        endif;
    }
    
    function setFinanceAccount($st_id, $school_year)
    {
        $student = Modules::run('registrar/getSingleStudent', $st_id, $school_year);
        $course = $student->grade_id;
        $plan = $this->getPlanByCourse($course, 0);
        
        $finDetails = array(
            'fin_st_id'         => $student->u_id,
            'fin_term_id'       => 0,
            'fin_plan_id'       => $plan->fin_plan_id,
            'fin_school_year'   => $school_year,
        );
        
        $this->finance_model->setFinanceAccount($finDetails, $student->u_id, $plan->fin_plan_id, $school_year, $sem);
        
        //Modules::run('college/updateEnrollmentStatus', $st_id, $this->session->userdata('school_year'), $sem, 1);  
        
        return;
    }
    
    function getPlanByCourse($course_id, $year_level)
    {
        $plan_id = $this->finance_model->getPlanByCourse($course_id, $year_level);
        return $plan_id;
    }
    
    function viewFinAccounts($id=NULL, $school_year=NULL)
    {
        if($this->session->userdata('is_logged_in')):
            $settings = Modules::run('main/getSet');
            $data['financeSettings'] = $this->getSettings();
            $data['series'] = $this->getSingleSeries($this->session->userdata('employee_id'));
            $data['id'] = $id;
            $data['school_year'] = ($school_year==NULL?$this->session->userdata('school_year'):$school_year);
            $data['modules'] = 'finance';
            $data['finSettings'] = $this->getSettings();
            $data['fin_items'] = $this->finance_model->getFinItems($data['school_year']);
            $data['getBanks'] = $this->finance_model->getBanks();
            if(file_exists(APPPATH.'modules/finance/views/'. strtolower($settings->short_name).'_viewFinanceAccounts.php')):
                $data['main_content'] = strtolower($settings->short_name).'_viewFinanceAccounts';
            else:
                $data['main_content'] = 'viewFinanceAccounts';
            endif;
            echo Modules::run('templates/canteen_content', $data);
       else:
           redirect('login');
       endif;
    }
    
    function accounts($id=NULL, $school_year=NULL)
    {
        if($this->session->userdata('is_logged_in')):
            $settings = Modules::run('main/getSet');
            $data['financeSettings'] = $this->getSettings();
            $data['series'] = $this->getSingleSeries($this->session->employee_id);
            $data['id'] = $id;
            $data['school_year'] = ($school_year==NULL?$this->session->userdata('school_year'):$school_year);
            $data['modules'] = 'finance';
            $data['gradeLevel'] = Modules::run('registrar/getGradeLevel', $settings->level_catered );
            $data['finSettings'] = $this->getSettings();
            $data['fin_items'] = $this->finance_model->getFinItems($data['school_year']);
            $data['getBanks'] = $this->finance_model->getBanks();
            if(file_exists(APPPATH.'modules/finance/views/'. strtolower($settings->short_name).'_financeAccounts.php')):
                $data['main_content'] = strtolower($settings->short_name).'_financeAccounts';
            else:
                $data['main_content'] = 'financeAccounts';
            endif;
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
        $school_year = $this->post('school_year');
        
        $success = $this->finance_model->editFinanceCharges($charge_id, $amount, $school_year);
        if($success):
            echo json_encode(array('status'=> TRUE, 'msg' => 'Successfully Updated', 'amount' => $amount));
            $this->saveFinanceLog($this->session->userdata('employee_id'), $this->post('log'));
        else:
            echo json_encode(array('status'=> TRUE, 'msg' => 'Sorry Something went wrong', 'amount' => 0));
        endif;
    }
    
    function financeCharges($course_id, $year_level, $school_year, $sem, $grade_level)
    {
        $data['grade_level'] = $grade_level;
        $data['year'] = $year_level;
        $data['grade_id'] = $course_id;
        $data['charges'] = $this->finance_model->financeCharges($course_id, $year_level, $school_year, $sem);
        $this->load->view('financeCharges', $data);
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
    
    public function addExtraFinanceCharges()
    {
        $item = $this->post('finItem');
        $sem = $this->post('semester');
        $amount = $this->post('finAmount');
        $sy = $this->post('school_year');
        $st_id= $this->post('st_id');
        $user_id= $this->post('user_id');
        $year_level = $this->post('year_level');
        $plan_id = $this->post('plan_id');
        $admission_id = $this->post('admission_id');
        
        
        $charge = array(
            'extra_st_id' => $user_id,
            'extra_item_id'    => $item,
            'extra_amount'  => $amount,
            'extra_sem' => $sem,
            'extra_school_year'     => $sy
        );
        $result = $this->finance_model->addExtraFinanceCharges($charge, $sy);
        if(!$result):
        else:    
            Modules::run('web_sync/updateSyncController', 'c_finance_extra', 'extra_id', $result, 'create', 6);
            $charges = Modules::run('college/finance/financeChargesByPlan',$year_level, $sy, $sem, $plan_id );
            $student = Modules::run('registrar/getSingleStudent', $st_id, $sy);
            $i=1;
            $total=0;

                foreach ($charges as $c):
                 $next = $c->school_year + 1;
                 $amount = $c->amount
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
                $extraCharges = Modules::run('finance/getExtraFinanceCharges',$user_id, $student->semester, $student->school_year);
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
        //$sem = $this->post('semester');
        $amount = $this->post('finAmount');
        $sy = $this->post('school_year');
        $course_id = $this->post('gradeLevel');
        $year_level = $this->post('year_level');
        
        
        $plan = array(
            'fin_course_id'     => $course_id,
            'fin_year_level'    => 0
        );
        
        $plan_id = $this->finance_model->addPlan($plan, $course_id, 0);
        
        $charge = array(
            'item_id' => $item,
            'amount'    => $amount,
            'semester'  => 0,
            'school_year' => $sy,
            'plan_id'     => $plan_id
        );
        
        $this->finance_model->addFinanceCharges($charge, $plan_id, $item, 0, $sy);
        echo Modules::run('finance/financeCharges', $course_id, 0, $sy, 0);
        
    }
    
    
   
   
}
