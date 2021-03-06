<?php

class MYPDF extends Pdf {

    //Page header
    public function Header() {
        // Logo

        $settings = Modules::run('main/getSet');
        $this->SetY(2);
        $this->SetX(10);
        $this->SetFont('helvetica', 'B', 11);
        // $image_file = K_PATH_IMAGES . '/' . $settings->set_logo;
        // $this->Image($image_file, 40, 5, 18, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        $this->SetX(10);
        $this->Ln(5);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 0, $settings->set_school_name, 0, false, 'C', 0, '', 0, false, 'M', 'T');
        $this->Ln();
        $this->SetFont('helvetica', 'n', 8);
        $this->Cell(0, 15, $settings->set_school_address, 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom

        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
// $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// remove default header/footer
$resolution = array(330, 216);
$pdf->AddPage('P', $resolution);

//variables
$settings = Modules::run('main/getSet');
$student = Modules::run('finance/getBasicStudent', $st_id, $school_year, $semester);
$next = $settings->school_year + 1;



$plan = Modules::run('finance/getPlanByCourse', $student->grade_id, 0, $student->st_type, $student->school_year);
$charges = Modules::run('finance/financeChargesByPlan', 0, $student->school_year, 0, $plan->fin_plan_id, $student->semester);

$extraCharges = Modules::run('finance/getExtraFinanceCharges', $student->user_id, $student->semester, $student->school_year);
// $charges = Modules::run('finance/financeChargesByPlan', 0, $student->school_year, 0, $plan->fin_plan_id, $student->semester);

$transaction = Modules::run('finance/getTransactionASC', $student->st_id, $student->semester, $student->school_year);



// print_r($extraCharges->result());

$misc = 0;
$reg = 0;
$techFee = 0;
$tuitionFee = 0;
$music = 0;
$total = 0;
$booksFee = 0;
$balance = 0;

$tempmisc = 0;
$tempreg = 0;
$temptechFee = 0;
$temptuitionFee = 0;
$tempmusic = 0;
$temptotal = 0;

foreach($charges as $c):
    if($c->item_description == 'Enrollment Fee'):
        $reg = $c->amount;
    endif;
    if($c->item_description == 'Miscellaneous'):
        $misc = $c->amount;
        $tempmisc = $misc;
    endif;
    if($c->item_description == 'Tuition Fee'):
        $tuitionFee = $c->amount;
        $temptuitionFee = $tuitionFee;
    endif;
    if($c->item_description == 'Technology Fee'):
        $techFee = $c->amount;
        $temptechFee = $techFee;
    endif;
    if($c->item_description == 'Music'):
        $music = $c->amount;
        $tempmusic = $music;
    endif;
    if(strpos($c->item_description, 'Textbooks') !== false || strpos($c->item_description, 'book') !== false || strpos($c->item_description, 'Book') !== false):
        $booksFee = $c->amount;
        $tempbooksFee = $booksFee;
    
    endif;
endforeach;

foreach($transaction->result() as $t):
    if ($t->t_type == 2):
        $discount = $discount + $t->t_amount;
    endif;

endforeach;





$pdf->SetXY(5, 5);
$pdf->SetFont('helvetica', 'B', 9);
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);


$pdf->SetY(30);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 0, 'STATEMENT OF ACCOUNT', 0, false, 'C', 0, '', 0, false, 'M', 'T');

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(5);
$pdf->SetX(5);
$pdf->Cell(0, 0, 'SY: ' . $settings->school_year . ' - ' . $next . ($student->semester == 3 ? '( SUMMER )' : ''), 0, false, 'C', 0, '', 0, false, 'M', 'T');
$pdf->Ln(5);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 12);
$pdf->MultiCell(120, 0, 'Name: ' . strtoupper($student->firstname . ' ' . ($student->middlename != "" ? substr($student->middlename, 0, 1) . '. ' : "") . $student->lastname), 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(10, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(100, 0, 'Grade Level : ' . strtoupper($student->level), 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->Ln(6);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 12);
$pdf->MultiCell(120, 0, 'GUARDIAN: ' . strtoupper( $student->ice_name ) , 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(10, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(100, 0, 'Tuition Fee : ' . number_format($tuitionFee, 2, '.', ','), 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->Ln(6);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 12);
$pdf->MultiCell(120, 0, 'CONTACT NUMBER: ' . $student->ice_contact, 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(10, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(100, 0, 'Discount: '.number_format($discount, 2, '.', ','), 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

//BOX
$pdf->Ln(10);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(20, 0, 'Date', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'O.R. No.', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'Reg.', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'Misc.', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, 'Tech. Fee ', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, 'Tuition Fee', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(12, 0, 'Music', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, 'Total', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'Balance', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(20, 0, 'Date', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(14, 0, 'Amount', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(21, 0, 'Books', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');


$tuitionFee -= $discount;
$total = $reg + $misc + $tuitionFee + $techFee;

$temptotal = $total;



$pdf->Ln();
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'N', 8);
$pdf->MultiCell(20, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, number_format($reg, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, number_format($misc, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, number_format($techFee, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, number_format($tuitionFee, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(12, 0, ($music == 0 ) ? '' : number_format($music, 2, '.', ',') , 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0,  number_format($total, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, '-', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(34, 0, 'Total Amount', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'N', 8);
$pdf->MultiCell(21, 0, number_format($booksFee, 2, '.', ','), 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

//Body
// if($discount != 0):
//     $pdf->Ln();
//     $pdf->SetX(5);
//     $pdf->SetFont('helvetica', 'N', 8);
//     $pdf->MultiCell(20, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(15, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(15, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(18, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(18, 0, number_format($discount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     // $pdf->MultiCell(12, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(18, 0, number_format($discount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(15, 0, number_format($temptotal -= $discount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

//     $pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

//     $pdf->MultiCell(20, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(21, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
// endif;
$lastDate ='';
$lastOR = 0;
$tempReg = 0;
$totalPayment = 0;
$tempTotalPaid =0;
$totalreg = 0;
$totalmisc = 0;
$totalmusic = 0;
$totalTuition = 0;
$totalTechFee = 0;

$totalBookFee = 0;

$b1y = 0;
$b1x = 0;

$b2y = 0;
$b2x = 0;

$b3y = 0;
$b3x = 0;

$b4y = 0;
$b4x = 0;

$b5y = 0;
$b5x = 0;

$b6y = 0;
$b6x = 0;

$b7y = 0;
$b7x = 0;

$b8y = 0;
$b8x = 0;
$b9y = 0;
$b9x = 0;
$b10y = 0;
$b10x = 0;
// $totalHolder = array();

// $tempHolder['testing'] = 'test value';
// array_push($totalHolder,$tempHolder );
// print_r($transaction->result());

foreach($transaction->result() as $i => $t):
    if ($t->t_type != 2 && $t->t_type != 3):
        $subPay = 0;
        if( $t->ref_number != $lastOR):
            if($totalPayment != 0 ):
                $tempTotalPaid += $totalPayment;
                $pdf->SetXY($b6x,$b6y);
                $pdf->MultiCell(18, 0, number_format($totalPayment, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
                $temptotal -= $totalPayment;
                $pdf->SetXY($b7x,$b7y);
                $pdf->MultiCell(15, 0, number_format($temptotal, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

            endif;
            $totalPayment = 0;
            $pdf->Ln();
            $pdf->SetX(5);
            $pdf->MultiCell(20, 0, $t->t_date, 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(15, 0, $t->ref_number, 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            if($t->item_description == 'Enrollment Fee'):
                $totalreg += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->MultiCell(15, 0, number_format($t->t_amount, 2, '.', ','), 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            else:
                $b1y = $pdf->GetY();
                $b1x = $pdf->GetX();
                $pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            if($t->item_description == 'Miscellaneous'):
                $totalmisc += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->MultiCell(15, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            else:
                $b2y = $pdf->GetY();
                $b2x = $pdf->GetX();
                $pdf->MultiCell(15, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            if($t->item_description == 'Technology Fee'):
                $totalTechFee += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->MultiCell(18, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            else:
                $b3y = $pdf->GetY();
                $b3x = $pdf->GetX();
                $pdf->MultiCell(18, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            if($t->item_description == 'Tuition Fee'):
                $totalTuition += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->MultiCell(18, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            else:
                $b4y = $pdf->GetY();
                $b4x = $pdf->GetX();
                $pdf->MultiCell(18, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            // if($t->item_description == 'Music Fee'):
            //     $totalmusic += $t->t_amount;
            //     $totalPayment += $t->t_amount;
            //     $pdf->MultiCell(12, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            // else:
            //     $b5y = $pdf->GetY();
            //     $b5x = $pdf->GetX();
            //     $pdf->MultiCell(12, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            // endif;
            $b6y = $pdf->GetY();
            $b6x = $pdf->GetX();
            //tota payment
            $pdf->MultiCell(18, 0, '', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            //balance
            $b7y = $pdf->GetY();
            $b7x = $pdf->GetX();
            $pdf->MultiCell(15, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            
            $pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

            if(strpos($t->item_description, 'Textbooks') !== false || strpos($t->item_description, 'book') !== false || strpos($t->item_description, 'Book') !== false):
                // $pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                $b8y = $pdf->GetY();
                $b8x = $pdf->GetX();
                $pdf->MultiCell(20, 0,$t->t_date, 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                
                $b9y = $pdf->GetY();
                $b9x = $pdf->GetX();
                $pdf->MultiCell(14, 0, number_format($t->t_amount, 2, '.', ','), 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                
                $b10y = $pdf->GetY();
                $b10x = $pdf->GetX();
                $totalBookFee += $t->t_amount;
                $tempbooksFee -= $t->t_amount;
                $pdf->MultiCell(21, 0, number_format($tempbooksFee , 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            
            else:
                $b8y = $pdf->GetY();
                $b8x = $pdf->GetX();
                $pdf->MultiCell(20, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                
                $b9y = $pdf->GetY();
                $b9x = $pdf->GetX();
                $pdf->MultiCell(14, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                
                $b10y = $pdf->GetY();
                $b10x = $pdf->GetX();
                $pdf->MultiCell(21, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            
            
            
            endif;

            $lastDate = $t->t_date;
            $lastOR = $t->ref_number;
            $lastDate = $t->t_date;
            $lastOR = $t->ref_number;
            if($i == count($transaction->result())-1):
                $tempTotalPaid += $totalPayment;
                $pdf->SetXY($b6x,$b6y);
                $pdf->MultiCell(18, 0, number_format($totalPayment, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
                $temptotal -= $totalPayment;
                $pdf->SetXY($b7x,$b7y);
                $pdf->MultiCell(15, 0, number_format($temptotal, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
        else:
            if($t->item_description == 'Enrollment Fee'):
                $totalreg+= $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->SetXY($b1x,$b1y);
                $pdf->MultiCell(15, 0, number_format($t->t_amount, 2, '.', ','), 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            if($t->item_description == 'Miscellaneous'):
                $totalmisc += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->SetXY($b2x,$b2y);
                $pdf->MultiCell(15, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            if($t->item_description == 'Technology Fee'):
                $totalTechFee += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->SetXY($b3x,$b3y);
                $pdf->MultiCell(18, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            if($t->item_description == 'Tuition Fee'):
                $totalTuition += $t->t_amount;
                $totalPayment += $t->t_amount;
                $pdf->SetXY($b4x,$b4y);
                $pdf->MultiCell(18, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
            // if($t->item_description == 'Music Fee'):
            //     $totalmusic += $t->t_amount;
            //     $totalPayment += $t->t_amount;
            //     $pdf->SetXY($b5x,$b5y);
            //     $pdf->MultiCell(12, 0, number_format($t->t_amount, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            // endif;
            if(strpos($t->item_description, 'Textbooks') !== false || strpos($t->item_description, 'book') !== false || strpos($t->item_description, 'Book') !== false):

                $totalBookFee += $t->t_amount;
                $pdf->SetXY($b8x,$b8y);
                $pdf->MultiCell(20, 0, $t->t_date, 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                
                $pdf->SetXY($b9x,$b9y);
                $pdf->MultiCell(14, 0, number_format($t->t_amount, 2, '.', ','), 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
                
                $pdf->SetXY($b10x,$b10y);
                $tempbooksFee -= $t->t_amount;
                $pdf->MultiCell(21, 0, number_format($tempbooksFee , 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            
            endif;
            if($i == count($transaction->result())-1 ):
                $tempTotalPaid += $totalPayment;
                $pdf->SetXY($b6x,$b6y);
                $pdf->MultiCell(18, 0, number_format($totalPayment, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');  
                $temptotal -= $totalPayment;
                $pdf->SetXY($b7x,$b7y);
                $pdf->MultiCell(15, 0, number_format($temptotal, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            endif;
        endif;
        

    endif;//type if


endforeach;

// print_r($totalHolder);

// $pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

// $pdf->MultiCell(20, 0, '4/7/2019', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(14, 0, '10725', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(21, 0, '1,548.00', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');




//box Footer 
$pdf->Ln();
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(20, 0, 'Total Paid', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'N', 8);
$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, number_format($totalreg, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, ($totalmisc!= 0 ) ? number_format($totalmisc, 2, '.', ',') : '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, number_format($totalTechFee, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, number_format($totalTuition, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(12, 0, ($totalmusic != 0 ) ? number_format($totalmusic, 2, '.', ',') : '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, number_format($tempTotalPaid, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(20, 0, 'Total Paid', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'N', 8);
$pdf->MultiCell(14, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(21, 0, number_format($totalBookFee, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->Ln();
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(20, 0, 'Balance', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'N', 8);
$pdf->MultiCell(15, 0, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, (($reg - $totalreg)  == 0 ) ? '-' : number_format($reg - $totalreg, 2, '.', ',') , 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, (($misc - $totalmisc)  == 0 ) ? '-' : number_format($misc - $totalmisc , 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, (($techFee - $totalTechFee)  == 0 ) ? '-' : number_format($techFee - $totalTechFee, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, (($tuitionFee - $totalTuition)  == 0 ) ? '-' : number_format($tuitionFee - $totalTuition, 2, '.', ',') , 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(12, 0, '-', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, number_format($temptotal, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(5, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(20, 0, 'Balance', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'N', 8);
$pdf->MultiCell(14, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(21, 0, ($tempbooksFee <= 0 ? 0 : number_format($tempbooksFee, 2, '.', ',')), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

//box end

$regMonthly = $reg - $totalreg ;
$miscMonthly = 0;
$techFeeMonthly = 0;
$tuitionMonthly = 0;

$arrears = 0;
// if(($reg - $totalreg)  != 0 ):

// endif;

$bosy = strtotime($settings->bosy);


$miscMonthly = $misc/4;
$techFeeMonthly = $techFee/10;
$tuitionMonthly = $tuitionFee/10;


// //misc
// if(date("m", strtotime("+4 month", $bosy)) <= date("m")):
//     // $arrears += ($misc - $totalmisc);
// else:
//     $month = (date("m") - date("m", strtotime("-1 month", $bosy)));
//     // $arrears += ($miscMonthly*$month) - $totalmisc; 
// endif;


// //tuition
// if(date("m", strtotime("+4 month", $bosy)) <= date("m")):
//     // $arrears += $tuitionFee - $totalTuition;
// else:
//     $month = (date("m") - date("m", strtotime("-1 month", $bosy)));
//     // $arrears += ($tuitionMonthly); 
// endif;
// $pdf->Ln(10);
// $pdf->SetX(5);
// $pdf->SetFont('helvetica', 'B', 10);
// $pdf->MultiCell(50, 0, 'Arrears: '. number_format($arrears, 2, '.', ','), 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$arrears = 0;

foreach($extraCharges->result() as $ec):
    $arrears += $ec->extra_amount;
endforeach;
// Monthly payment
$pdf->Ln(10);
$pdf->SetX(5);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(30, 0, 'Due for the month: ', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->Ln();
$pdf->SetX(25);
$pdf->SetFont('helvetica', 'B', 8);
// $pdf->MultiCell(12, 0, '', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'Reg.', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'Misc.', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, 'Tech. Fee', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, 'Tuition Fee', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(12, 0, 'Music', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, 'Sub-Total', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, 'Arrears', 1, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(5, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(20, 0, 'Books', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(14, 0, 'Php', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(21, 0, 'Total Payable', 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');


$pdf->Ln();
$pdf->SetX(25);
$pdf->SetFont('helvetica', 'N', 8);
// $pdf->MultiCell(12, 0, '', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, '', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');
$subtot = 0;

if(($reg - $totalreg)  != 0 ):
    if(($reg - $totalreg) <= $regMonthly):
        $regMonthly = $reg - $totalreg;
    endif;
    $subtot += $regMonthly;
endif;
if(($misc - $totalmisc)  != 0 ):
    if(($misc - $totalmisc) <= $miscMonthly):
        $miscMonthly = $misc - $totalmisc;
    endif;  
    if(strtotime(date("Y-m-d", strtotime("+4 month", $bosy))) <= strtotime(date("Y-m-d")) ):
        $miscMonthly = $misc - $totalmisc;
    endif;
    $subtot += $miscMonthly;
endif;
if(($techFee - $totalTechFee)  != 0 ):
    if(($techFee - $totalTechFee) <= $techFeeMonthly):
        $techFeeMonthly = $techFee - $totalTechFee;
    endif;
    // if(strtotime(date("Y-m-d", strtotime("+4 month", $bosy))) <= strtotime(date("Y-m-d")) ):
    //     $techFeeMonthly = $techFee - $totalTechFee;
    // endif;
    $subtot += $techFeeMonthly;
endif;
if(($tuitionFee - $totalTuition)  != 0 ):
    if(($tuitionFee - $totalTuition) <= $tuitionMonthly):
        $tuitionMonthly = $tuitionFee - $totalTuition;
    endif;
    $subtot += $tuitionMonthly;
endif;
$pdf->MultiCell(15, 0, (($reg - $totalreg)  == 0 ) ? '-' : number_format($regMonthly, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, (($misc - $totalmisc)  == 0 ) ? '-' : number_format($miscMonthly, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, (($techFee - $totalTechFee)  == 0 ) ? '-' : number_format($techFeeMonthly, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, (($tuitionFee - $totalTuition)  == 0 ) ? '-' : number_format($tuitionMonthly, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
// $pdf->MultiCell(12, 0, '-', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, number_format($subtot, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(15, 0, number_format($arrears, 2, '.', ','), 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->MultiCell(5, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');



$booksFeeMonthly = $booksFee/5;

$arrearsBooks = 0;
    //12 2020                                       01 2021
if(strtotime(date("Y-m-d", strtotime("+4 month", $bosy))) <= strtotime(date("Y-m-d")) ):
    $arrearsBooks += $tempbooksFee;
else:
    $arrearsBooks = $booksFeeMonthly;
endif;
$b = 0;
// if($tempbooksFee != 0):
//     $b = $booksFeeMonthly;
// endif;
$pdf->MultiCell(20, 0, number_format($arrearsBooks, 2, '.', ','), 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(14, 0, '', 1, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(21, 0, number_format($subtot + $arrearsBooks + $arrears , 2, '.', ','), 1, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');



$pdf->Ln(10);
$pdf->SetX(5);
$pdf->MultiCell(19, 0, 'Prepared By:', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(46, 0, '', 'B', 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(18, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(120, 0, 'PLEASE BRING YOUR STATEMENT OF ACCOUNT UPON PAYMENT. THANK YOU', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');

$pdf->Ln();
$pdf->SetX(5);
$pdf->MultiCell(19, 0, '', 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
$pdf->MultiCell(46, 0, 'Jean Siao - Reambonanza', 0, 'C', 0, 0, '', '', true, 0, false, true, 0, 'M');



// $pdf->SetAlpha(0.1);
// $image_file = K_PATH_IMAGES . $settings->set_logo;
// print($image_file);
// $pdf->Image($image_file, 65, 20, 100, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

// $image_file = K_PATH_IMAGES . '/' . $settings->set_logo;
// $pdf->Image($image_file, 65, 20, 100, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

//Close and output PDF document
$pdf->Output('Statement of Account.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
