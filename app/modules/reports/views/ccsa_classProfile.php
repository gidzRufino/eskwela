<?php

    class MYPDF extends Pdf {

    //Page header
    public function Header() {

    $section = Modules::run('registrar/getSectionById', segment_3);
        $settings = Modules::run('main/getSet');
        $adviser = Modules::run('academic/getAdvisory', '', segment_5, segment_3);
    // Logo$this->SetTopMargin(4);
        
        switch (segment_4){
            case 1:
                $term = 'FIRST QUARTER';
            break;
            case 2:
                $term = 'SECOND QUARTER';
            break;
            case 3:
                $term = 'THIRD QUARTER';
            break;
            case 4:
                $term = 'FOURTH QUARTER';
            break;
        }
        
        switch (TRUE):
            case ($section->grade_id <= 13 && $section->grade_id >= 12):
                $department = 'Senior High School';
            break;
            case ($section->grade_id <= 10 && $section->grade_id >= 8):
                $department = 'Junior High School';
            break;
            case ($section->grade_id < 8 && $section->grade_id >= 1 ):
                $department = 'Elementary';
            break;
        
        endswitch;
        
        $nextYear = segment_5 + 1;
        $this->SetFont('helvetica', 'B', 10);
        $this->MultiCell(180, 5, $settings->set_school_name, '', 'C', 0, 0, '', '', true);
        $this->Ln(7);
        $this->SetFont('helvetica', 'n', 8);
        $this->Cell(0, 15, $settings->set_school_address, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(5);
        
        $this->SetFont('helvetica', 'B', 10);
        $this->MultiCell(180, 5, 'Ad Form 3. CLASS PROFILE', '', 'C', 0, 0, '', '', true);
        $this->Ln(8);

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 0, $term.', S.Y. '.segment_5.' - '.$nextYear, 0, false, 'C', 0, '', 0, false, 'M', 'T');
        $this->Ln();

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 0, $department.' Department', 0, false, 'C', 0, '', 0, false, 'M', 'T');
        $this->Ln();
        
        $this->Cell(0, 0, $section->level.' - '.$section->section, 0, false, 'C', 0, '', 0, false, 'M', 'T');
    }

    // Page footer
    public function Footer() {
    // Position at 15 mm from bottom
    $this->SetY(-15);
    // Set font
    $this->SetFont('helvetica', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



$pdf->AddPage();
// define style for border
$border_style = array('all' => array('width' => 2, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'phase' => 0));

$pdf->SetY(40);


$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(20, 2, 'Class Size:', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(40, 2, $this->eskwela->numberTowords($students->num_rows()).' ( '.$students->num_rows().' )', 0, 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(100, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(25, 5, 'Average', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(25, 2, 'No. of', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(25, 5, '%age', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(14, 2, 'Grades:', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(94, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(12, 2, 'Grade', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(11, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(16, 2, 'Students', 'B', 'L', 0, 0, '', '', true);
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(30, 2, 'Honor`s List', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(103, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '2', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '7', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '1 Student F', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '94.13%', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '2 Student A', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '91.75%', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(34, 2, 'With Standard Grades', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(88, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '24', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '89', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'With Substandard Grade/s', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(82, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '1', 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '4', 'B', 'C', 0, 0, '', '', true);
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(40, 2, 'Total', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(93, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '27', 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '100', 'B', 'C', 0, 0, '', '', true);
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(21, 2, 'Attendance:', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 5, 'With Perfect Attendace', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(82, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '5', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '19', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(33, 2, 'BOYS:', '', 'R', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '1 Student A', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '2 Student S', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(33, 2, 'GIRLS:', '', 'R', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '1 Student F', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '2 Student J', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '3 Student G', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 5, 'With 6 or More Tardiness', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(82, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 5, 'With 6 or More Tardiness', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(55, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(14, 2, 'Number', 'B', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(30, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, '1 Student K', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(48, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(32, 2, '', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(35, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, 'Excused', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(43, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(32, 2, '2', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(35, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(20, 2, 'Unexcused', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(43, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(32, 2, '', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(35, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(27, 2, 'Personal Reason', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(36, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(32, 2, '4', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(35, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(27, 2, 'Got LRN', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(36, 2, '', '', 'R', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(18, 2, '2', 'B', 'R', 0, 0, '', '', true);
$pdf->MultiCell(18, 2, '', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '8', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '30', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'Others', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '14', 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '52', 'B', 'C', 0, 0, '', '', true);
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(40, 2, 'Total', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(94, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '27', 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '100', 'B', 'C', 0, 0, '', '', true);
$pdf->Ln(10);


$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(26, 5, 'Student Status:', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'Active', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'Dropped Out', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '--', '', 'C', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'Transferred Out', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '--', 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '--', 'B', 'C', 0, 0, '', '', true);
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(40, 2, 'Total', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(94, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(10, 2, '--', 'B', 'C', 0, 0, '', '', true);
$pdf->MultiCell(15, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->MultiCell(10, 2, '--', 'B', 'C', 0, 0, '', '', true);
$pdf->Ln(10);






$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(26, 5, 'Challenges:', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'With Behavioral Concerns', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, 'With Sanction/s', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, '1', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, '2', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->Ln();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(11, 2, '', '', '', 0, 0, '', '', true);
$pdf->MultiCell(40, 2, '3', '', 'C', 0, 0, '', '', true);
$pdf->MultiCell(83, 2, '', '', 'L', 0, 0, '', '', true);
$pdf->Ln(10);





$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(22, 2, 'Prepared By:', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(70, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(25, 2, ', Class Adviser', '', 'L', 0, 0, '', '', true);
$pdf->Ln(15);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(34, 2, 'Certified Correct By:', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(70, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(33, 2, ', Principal/Dept. Head', '', 'L', 0, 0, '', '', true);
$pdf->Ln(15);

$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(24, 2, 'Approved By:', '', 'L', 0, 0, '', '', true);
$pdf->MultiCell(70, 2, '', 'B', 'L', 0, 0, '', '', true);
$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell(25, 2, ', Director, SBE', '', 'L', 0, 0, '', '', true);
$pdf->Ln(15);

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
// reset pointer to the last page
$pdf->lastPage();

$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
