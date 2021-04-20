<?php
class MYPDF extends Pdf {
    
	//Page header
	public function Header() {
		// Logo
                $settings = Modules::run('main/getSet');
                $nextYear = $settings->school_year + 1;
                
                $this->SetTitle(strtoupper($settings->set_school_name));
                $this->SetTopMargin(4);
                $this->Ln(5);
        }

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
                
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// remove default header/footer
$resolution= array(330,216);
$pdf->AddPage('P', $resolution);

$semester = Modules::run('main/getSemester');
if($sem==NULL):
    $sem = $semester;
endif;

//variables
$settings = Modules::run('main/getSet');
$student = Modules::run('college/getSingleStudent', $st_id, $this->session->userdata('school_year'), $sem);
$next = $settings->school_year + 1;

switch ($student->year_level):
    case 1:
        $year_level = '1st';
    break;
    case 2:
        $year_level = '2nd';
    break;
    case 3: 
        $year_level = '3rd';
    break;
    case 4:
        $year_level = '4th';
    break;
    case 5:
        $year_level = '5th';
    break;    
endswitch;

switch($student->semester):
    case 1:
        $sem = '1ST';
    break;
    case 2:
        $sem = '2ND';
    break;
    case 3:
        $sem = 'SUMMER';
    break;
endswitch;

$pdf->SetXY(5,5);
$pdf->SetFont('helvetica', 'B', 9);
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->MultiCell(100, 0, $settings->set_school_name,0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'B', 17);
$pdf->SetXY(100,10);
$pdf->MultiCell(110, 0,'STATEMENT OF ACCOUNT',0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(4);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 9);
$pdf->MultiCell(100, 0, $settings->set_school_address,0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(8);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 9);
$pdf->MultiCell(100, 0, '225 - 5543',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->Ln(10);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 12);
$pdf->MultiCell(100, 0,'Name: '. strtoupper($student->firstname.' '.($student->middlename!=""?substr($student->middlename).'. ':""). $student->lastname),0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(10, 0,'',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(90, 0,'Course / Year : '. strtoupper($student->short_code.' '.$year_level.' YR'),0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(5);
$pdf->SetX(5);
$pdf->MultiCell(100, 0,'Semester: '.$sem.' SEMESTER '.$settings->school_year.' - '.$next,0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

//CHARGES / FEES

$loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $student->admission_id);

$totalUnits = 0;
foreach ($loadedSubject as $sl):
    $totalUnits += ($sl->s_lect_unit + $sl->s_lab_unit);
endforeach;

$plan = Modules::run('college/finance/getPlanByCourse', $student->course_id, $student->year_level);
$tuition = Modules::run('college/finance/getChargesByCategory',1, $student->semester, $student->school_year, $plan->fin_plan_id );
$charges = Modules::run('college/finance/financeChargesByPlan',$student->year_level, $student->school_year, $sem, $plan->fin_plan_id );
//$misc = Modules::run('college/finance/getChargesByCategory',2, $student->semester, $student->school_year, $plan->fin_plan_id );
foreach ($charges as $c):
    $next = $c->school_year + 1;
        $totalCharges += ($c->item_id<=1 || $c->item_id<=2?0:$c->amount); 
endforeach;
$totalExtra = 0;
$extraCharges = Modules::run('college/finance/getExtraFinanceCharges',$student->uid, $student->semester, $student->school_year);
if($extraCharges->num_rows()>0):
    foreach ($extraCharges->result() as $ec):
        $totalExtra += $ec->extra_amount;
    endforeach;
endif;
$lab = Modules::run('college/finance/getLaboratoryFee',$student->uid, $student->semester, $student->school_year);

$over = Modules::run('college/finance/overPayment',$student->uid, $student->semester, $student->school_year);

$discounts = Modules::run('college/finance/getDiscountsByItemId', $student->uid, $student->semester, $student->school_year);
//$totalDiscount = ($tuition->row()->amount*$totalUnits) * $discounts->disc_amount;
//$totalDiscount = ($discounts->disc_type==0?($tuition->row()->amount*$totalUnits) * $discounts->disc_amount:$discounts->disc_amount);

$totalFees = (($tuition->row()->amount*$totalUnits)+$totalCharges+$lab->row()->extra_amount);


$pdf->Ln(8);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'B', 13);
$pdf->MultiCell(40, 0,'CHARGES:',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(5);
$pdf->SetX(10);
$pdf->SetFont('helvetica', 'N', 12);
$pdf->MultiCell(65, 0,'TUITION ('.$totalUnits.' UNITS @ '.(number_format($tuition->row()->amount,2,'.',',')).')',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(85, 0,'----------------------------------------------------------',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(40, 0,(number_format($tuition->row()->amount*$totalUnits,2,'.',',')),0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(5);
$pdf->SetX(10);
$pdf->MultiCell(65, 0,'MISCELLANEOUS FEES',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(85, 0,'----------------------------------------------------------',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(40, 0,(number_format($totalCharges,2,'.',',')),0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(5);
$pdf->SetX(10);
$pdf->MultiCell(65, 0,'LABORATORY FEES',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(85, 0,'----------------------------------------------------------',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(40, 0,(number_format($lab->row()->extra_amount,2,'.',',')),0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln(8);
$pdf->SetX(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->MultiCell(65, 0,'TOTAL',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(85, 0,'',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(40, 0,(number_format($totalFees,2,'.',',')),0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

if(segment_6!=1):
    $transaction = Modules::run('college/finance/getTransactionByRefNumber', $student->uid, segment_5, $student->school_year);
    $paymentTotal = 0;
    $i = 1;
    if($transaction->num_rows()>0):
        $pdf->Ln(15);
        $pdf->SetX(5);
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->MultiCell(65, 0,'DISCOUNTS:',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

    
        $totalDiscount = 0;
        foreach ($transaction->result() as $tr):
            $i++;
        if($tr->t_type==2):
            $totalDiscount = $totalDiscount + $tr->subTotal ;
            $pdf->MultiCell(50, 0,'',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(50, 0,'('.(number_format($totalDiscount,2,'.',',')).')',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
        endif;
        endforeach;
        
        if($over):
            $pdf->Ln(10);
            $pdf->SetX(5);
            $pdf->SetFont('helvetica', 'B', 13);
            $pdf->MultiCell(65, 0,'OVERPAYMENT:',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(50, 0,'',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(50, 0,'('.(number_format(abs($over->row()->extra_amount),2,'.',',')).')',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
        endif;
    else:

    endif;
    $pdf->Ln(10);
    $pdf->SetX(5);
    $pdf->SetFont('helvetica', 'B', 13);
    $pdf->MultiCell(40, 0,'PAYMENTS:',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

    $pdf->Ln();
    $pdf->SetX(20);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->MultiCell(50, 0,'DATE',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
    $pdf->MultiCell(50, 0,'RECEIPT NO.',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
    $pdf->MultiCell(50, 0,'AMOUNT',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');



    $transaction = Modules::run('college/finance/getTransactionByRefNumber', $student->uid, segment_5, $student->school_year);
    $paymentTotal = 0;
    $i = 1;
    if($transaction->num_rows()>0):
        $balance = 0;
        foreach ($transaction->result() as $tr):
            $i++;
        if($tr->t_type!=2):
            $totalPayments = $totalPayments + $tr->subTotal ;
            
            $pdf->Ln(5);
            $pdf->SetX(20);
            $pdf->SetFont('helvetica', 'N', 12);
            $pdf->MultiCell(50, 0,date('F d, Y', strtotime($tr->t_date)),0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(50, 0,$tr->ref_number,0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(50, 0,(number_format($tr->subTotal,2,'.',',')),0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
        endif;
        endforeach;
    else:

    endif;

    if($totalPayments!=0):
        $pdf->Ln(8);
        $pdf->SetX(30);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->MultiCell(50, 0,'Total:',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
        $pdf->MultiCell(130, 0,(number_format($totalPayments,2,'.',',')),0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
    endif;    

    $outstandingBalance = (($totalFees + $over->row()->extra_amount) - $totalPayments)-$totalDiscount;
   

    $pdf->Ln(15);
    $pdf->SetX(10);
    $pdf->MultiCell(65, 0,'OUTSTANDING BALANCE',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
    $pdf->MultiCell(85, 0,'----------------------------------------------------------',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
    $pdf->MultiCell(40, 0,(number_format($outstandingBalance,2,'.',',')),0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
endif;

$pdf->Ln(15);
$pdf->SetX(10);
$pdf->SetFont('helvetica', 'N', 12);
$pdf->MultiCell(40, 0,'',0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(120, 0,'------------------- NOTHING FOLLOWS -------------------',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(40, 0,'',0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

$cashier = Modules::run('hr/getEmployeeByPosition', 'Cashier');

$pdf->Ln(20);
$pdf->SetX(10);
$pdf->SetFont('helvetica', 'N', 11);
$pdf->MultiCell(140, 0,'',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(50, 0, strtoupper($cashier->firstname.' '.$cashier->lastname),0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('helvetica', 'N', 11);
$pdf->MultiCell(140, 0,'',0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(50, 0, 'Cashier','T', 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->SetAlpha(0.1);
$image_file = K_PATH_IMAGES.'/'.$settings->set_logo;
$pdf->Image($image_file, 65, 20, 100, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$image_file = K_PATH_IMAGES.'/'.$settings->set_logo;
$pdf->Image($image_file, 65, 20+$data['x'], 100, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Close and output PDF document
$pdf->Output('studyLoad.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
