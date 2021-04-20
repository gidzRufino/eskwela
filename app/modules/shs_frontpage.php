<?php

//require_once('tcpdf_include.php');
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {

        //Logo
        if ($this->page == 1):
            $this->SetFont('helvetica', 'B', 15);
            $this->MultiCell(210, 5, 'PILGRIM CHRISTIAN COLLEGE', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 8);
            $this->MultiCell(210, 3, 'UNITED CHURCH OF CHRIST IN THE PHILIPPINES', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 8);
            $this->MultiCell(210, 3, '(Founded 1948)', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 8);
            $this->MultiCell(210, 3, 'Capsitrano-Akut St., Cagayan de Oro City 9000, Philippines', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 8);
            $this->MultiCell(210, 3, 'Tel. Nos. (088) 856 4239 (088) 856 1335 / 72 44 99 / Telefax (088) 856 4232', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 8);
            $this->MultiCell(210, 5, 'Email: pilgrimchristiancollege@ymail.com / Website: www.pcc-cdo.com', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $this->SetFont('helvetica', 'B', 12);
            $this->MultiCell(210, 5, 'OFFICIAL TRANSCRIPT OF RECORDS', '', 'C', 0, 0, '', '', true);
            $this->Ln();
            $image_file = K_PATH_IMAGES . '/pilgrim.png';
            $this->Image($image_file, 20, 5, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $image_file = K_PATH_IMAGES . '/depEd_logo.jpg';
            $this->Image($image_file, 200, 5, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        endif;
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}

// create new PDF document
$pdf = new MYPDF('P', 'mm', array('250', '350'), true, 'UTF-8', false);
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
// define style for border
// set font
$pdf->AddPage('P', '');
$pdf->Ln();

$border_style = array('all' => array('width' => 2, 'cap' => 'square', 'join' => 'miter', 'dash' => 0, 'phase' => 0));

$pdf->MultiCell(10, 5, "", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln(15);



// ------------------------------ LEARNER`S PERSONAL INFORMATION ------------------------------
$pdf->SetAlpha(0.2);
$pdf->SetFillColor(0,0,0);
$pdf->Image(base_url() . 'images/forms/pilgrim.png', 180, 40, 30, 30);
$pdf->SetAlpha(1);
$aveMapeh = 0;
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(12, 5, "NAME:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(90, 5, strtoupper($student->sprp_lastname . ',' . $student->sprp_firstname . ' ' . ($student->sprp_middlename != '' ? substr($student->sprp_middlename, 0,2) . '.' : '')), '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(18, 5, "Nationality:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 5, strtoupper($student->nationality), '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(20, 5, "Date of Birth:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(91, 5, date('m/d/Y', strtotime($student->sprp_bdate)), '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(10, 5, "Sex:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(17, 5, "Birthplace:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(88, 5, date('m/d/Y', strtotime($student->sprp_bdate)), '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(15, 5, "Religion:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(24, 5, "Home Address:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(78, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(19, 5, "Tel./Cel.No:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(24, 5, "Father's Name:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(77, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(19, 5, "Occupation:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(24, 5, "Mother's Name:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(77, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(19, 5, "Occupation:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln(10);

$pdf->MultiCell(35, 5, "Intermediate Competed:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(90, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(20, 5, "School Year:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(20, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(20, 5, "General Ave:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(40, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(55, 5, "Junior High School Completed:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(70, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(20, 5, "School Year:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(20, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(20, 5, "General Ave:", '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(40, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln();

$pdf->MultiCell(10, 5, "LRN:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(70, 5, '', '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(230, 5, "S  E  C  O  N  D  A  R  Y    R  E  C  O  R  D  S", '', 'C', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->Ln(5);

// ------------------------------ LEARNER`S PERSONAL INFORMATION ------------------------------
// ------------------------------ ELIGIBILITY FOR JUNIOR HIGH SCHOOL ENROLLMENT ------------------------------


$nYear = ($student->grade_level_id == 12 ? $pYear : ($pYear - 1));

for ($i = 0; $i < 4; $i++):
    switch ($i):
        case 0:
            $x = 10;
            $y = '';
            break;
        case 1:
            $x = 10;
            $y = 255;
            break;
        case 2:
            $x = 10;
            $y = 10;
            break;
        case 3:
            $x = 10;
            $y = 170;
            break;
        case 4:
            $x = 10;
            $y = 5;
            break;
    endswitch;
//    $data['subjectList'] = Modules::run('subjectmanagement/getAllSHSubjects', $student->grade_level_id, (($i == 0 || $i == 1) ? '1' : '2'), $strand_id);
    $data['strand'] = $strand_id->str_id;
    $data['i'] = $i;
    $data['x'] = $x;
    $data['y'] = $y;
    $data['pdf'] = $pdf;
    $data['student'] = $student;
    $data['year'] = $year;
    $data['sYear'] = ($i == 0 || $i == 1 ? $nYear : ($nYear + 1));

    $this->load->view('shs_main', $data);
endfor;
$pdf->Ln(5);
// ------------------------------ CERTIFICATION ------------------------------

$pdf->SetFont('helvetica', 'R', 7);
$pdf->MultiCell(200, 15, "This is to certify that the foregoing is a true copy of the Academic Records of and the student is eligible for transfer and admission to", '', 'L', 1, 0, '10', '', true, 0, false, true, 15, 'M');
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(200, 15, "REMARKS:", '', 'L', 1, 0, '10', '', true, 0, false, true, 15, 'M');

$pdf->Ln(20);
$pdf->SetFont('helvetica', 'R', 7);
$pdf->MultiCell(23, 15, "Not Valid without the College Seal", '', 'L', 1, 0, '10', '', true, 0, false, true, 15, 'M');
$pdf->MultiCell(165, 5, "", '', 'L', 1, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(30, 4, "JOY M. CASINO", 'B', 'C', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln();

$pdf->MultiCell(23, 15, "", '', 'L', 1, 0, '10', '', true, 0, false, true, 15, 'M');
$pdf->MultiCell(163, 5, "", '', 'L', 1, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->MultiCell(35, 4, "OIC-Office of the Registrar", '', 'C', 0, 0, '', '', true, 0, false, true, 4, 'M');





//
// ------------------------------ CERTIFICATION ------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
// reset pointer to the last page


function levelDesc($gradeID) {
    switch ($gradeID):
        case 1:
            return 'Kinder';
        case 2:
            return 'Grade 1';
        case 3:
            return 'Grade 2';
        case 4:
            return 'Grade 3';
        case 5:
            return 'Grade 4';
        case 6:
            return 'Grade 5';
        case 7:
            return 'Grade 6';
        case 8:
            return 'Grade 7';
        case 9:
            return 'Grade 8';
        case 10:
            return 'Grade 9';
        case 11:
            return 'Grade 10';
        case 12:
            return 'Grade 11';
        case 13:
            return 'Grade 12';
    endswitch;
}

//$data['pdf'] = $pdf;
//$data['student'] = $student;
//$data['year'] = $year;
//
//$this->load->view('gs_main', $data);
$pdf->Output('example_001.pdf', 'I');
