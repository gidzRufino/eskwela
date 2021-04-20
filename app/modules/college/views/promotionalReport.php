<?php

class MYPDF extends Pdf {

    //Page header
    public function Header() {
        // Logo
        $settings = Modules::run('main/getSet');
        $next = segment_5 + 1;

        switch (segment_4):
            case 1:
                $sem = 'First Semester';
                break;
            case 2:
                $sem = 'Second Semester';
                break;
            case 3:
                $sem = 'Summer';
                break;
        endswitch;

        if ($this->page == 1):
            //$this->SetTitle('Grading Sheet in '.$subject->subject);

            $image_file = K_PATH_IMAGES . '/pilgrim.jpg';
            $this->Image($image_file, 145, 12, 18, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            $image_file = K_PATH_IMAGES . '/uccp.jpg';
            $this->Image($image_file, 238, 12, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

            $this->SetTopMargin(12);
            $this->Ln(5);
            $this->SetX(10);
            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(0, 0, $settings->set_school_name, 0, false, 'C', 0, '', 0, false, 'M', 'T');
            $this->Ln();
            $this->SetFont('helvetica', 'N', 9);
            $this->Cell(0, 0, 'United Church of Christ in the Philippines', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->Ln();
            $this->SetFont('helvetica', 'n', 8);
            $this->Cell(0, 15, $settings->set_school_address, 0, false, 'C', 0, '', 0, false, 'M', 'M');

            $this->SetTitle(strtoupper($settings->short_name));

            $this->Ln(3);
            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(0, 4.3, "Promotional Report", 0, 0, 'C');
            $this->Ln(5);
            $this->SetFont('helvetica', 'N', 12);
            $this->Cell(0, 4.3, $sem . ', ' . segment_5 . ' - ' . $next, 0, 0, 'C');

        endif;
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

//variables




$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// remove default header/footer
$resolution = array(400, 216);
$pdf->AddPage('L', $resolution);

$pdf->SetY(30);


$pdf->setCellPaddings(1, 1, 1, 1);


$pdf->Ln(10);
$pdf->SetFont('helvetica', 'N', 10);
$pdf->SetX(5);
$pdf->MultiCell(30, 5, 'Program Name: ', 'TBL', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(250, 5, $students->row()->course, 'TB', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(110, 5, '', 'TRB', 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();
$pdf->SetX(5);
$pdf->MultiCell(120, 5, '', 'TBL', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(270, 5, '', 'TRB', 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();
// set cell padding

$pdf->SetX(5);
$pdf->MultiCell(70, 11, 'Name of Student', 1, 'C', 0, 0, '', '', true, 0, false, true, 11, 'M');
$pdf->MultiCell(10, 11, 'Sex', 1, 'C', 0, 0, '', '', true, 0, false, true, 11, 'M');
for ($sub = 1; $sub <= 9; $sub++):
    $pdf->MultiCell(20, 11, 'Subject Code', 1, 'C', 0, 0, '', '', true, 0, true, true, 11, 'M');
    $pdf->MultiCell(14.5, 11, 'Grade', 1, 'C', 0, 0, '', '', true, 0, false, true, 11, 'M');
endfor;
$pdf->Ln();

$x = 0;
$y = 0;
$cnt = 0;
foreach ($students->result() as $st):
    $nextCourse = FALSE;

    if ($st->status):
        $loadedSubject = Modules::run('college/subjectmanagement/getLoadedSubject', $st->admission_id, segment_4, segment_5); 
        $middlename = ($st->middlename != "" || $st->middlename != NULL ? $st->middlename . ' ' : '');

        $y++;
        $cnt++;
        $pdf->SetFont('helvetica', 'N', 10);
        $pdf->SetX(5);
        $pdf->MultiCell(10, 5, $cnt, 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $pdf->MultiCell(60, 5, ($y == 1 ? ucwords(strtolower($st->lastname)) . ', ' . ucwords(strtolower($st->firstname)) . ' ' . ucwords(strtolower($middlename)) : ""), 1, 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $pdf->MultiCell(10, 5, ($y == 1 ? substr($st->sex, 0, 1) : ''), 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $count = count($loadedSubject);


        // 5.0 = Failed
        // 6.0 = INC
        // 7.0 = NG
        // 8.0 = Dropped
        
        $subcount = 1;
        foreach ($loadedSubject as $subject):
            $subcount++;
            $grade = Modules::run('college/gradingsystem/getFinalGrade', $st->uid, segment_6, $subject->s_id, segment_4, segment_5, 4);
            switch (TRUE):
                case $grade->gsa_grade <= 5:
                    $fgrade = ($grade->gsa_grade != 0 ? number_format($grade->gsa_grade, 1) : '');
                    break;
                case $grade->gsa_grade == 6:
                    $fgrade = 'INC';
                    break;
                case $grade->gsa_grade == 7:
                    $fgrade = 'NG';
                    break;
                case $grade->gsa_grade == 8:
                    $fgrade = 'DRP';
                    break;
            endswitch;

            $pdf->MultiCell(20, 5, $subject->sub_code, 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
            $pdf->MultiCell(14.5, 5, $subcount, 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
            
            if($subcount == 10):
                $subcount=1;
                $pdf->Ln();
                $pdf->setX(85);
                $x++;
            endif;
            
        endforeach;
        $scount = 9 - $count;

        for ($sub = 1; $sub <= $scount; $sub++):
            $pdf->MultiCell(20, 5, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
            $pdf->MultiCell(14.5, 5, '', 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
        endfor;
        if($count > 9 || $count < 9):
            $pdf->Ln();
            $x++;
        endif;

        switch ($pdf->PageNo()):
            case 1:
                if ($x == 10):
                    $pdf->AddPage('L', $resolution);
                    $pdf->Ln(10);

                    $x = 1;
                endif;
                break;
            case $pdf->getAliasNbPages():
                if ($x == 10):
                    $pdf->AddPage('L', $resolution);
                    $pdf->Ln(10);

                    $x = 1;
                endif;
                break;
            default :
                if ($x == 24):
                    $pdf->AddPage('L', $resolution);
                    $pdf->Ln(10);

                    $x = 1;
                endif;
                break;
        endswitch;

        $y = 0;
        $sub = 1;
    endif;
endforeach;





//Close and output PDF document
$pdf->Output('Promotional Report.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
