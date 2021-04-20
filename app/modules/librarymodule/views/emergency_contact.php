<?php
class MYPDF extends Pdf {

    public function Header() {
        // Logo
        // $image_file = K_PATH_IMAGES.'/lca_logo.png';
        // $image_file = K_PATH_IMAGES.'/school_logo.png';
        // $this->Image($image_file, 20, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        // $this->SetFont('helvetica', 'B', 20);

      }
    public function Footer() {
      $settings = Modules::run('main/getSet');
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        // $settings = Modules::run('main/getSet');
        $this->SetFont('times', 'I', 10);
        // Page number
        // $this->Cell(0, 10, "Prepared and generated by $settings->set_school_name Administration through e-sKwela plus 2014.", 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'UTF-8', false);
$pdf->SetTitle('Collection Notice');
$pdf->SetSubject('Sets of Collection Notice');
$pdf->SetKeywords('Collection Notice, SOA, Statement of Accounts');
// $pdf->SetLeftMargin(5);
// $pdf->SetRightMargin(3);
define ('PDF_PAGE_FORMAT', 'A4');
// $page_format = array(
    // 'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 104, 'ury' => 241),
    // 'CropBox' => array ('llx' => 0, 'lly' => 0, 'urx' => 104, 'ury' => 241),
    // 'BleedBox' => array ('llx' => 5, 'lly' => 5, 'urx' => 100, 'ury' => 230),
    // 'TrimBox' => array ('llx' => 10, 'lly' => 10, 'urx' => 100, 'ury' => 228),
    // 'ArtBox' => array ('llx' => 15, 'lly' => 15, 'urx' => 98, 'ury' => 225),
    // 'Dur' => 3,
    // 'trans' => array(
    //     'D' => 1.5,
    //     'S' => 'Split',
    //     'Dm' => 'V',
    //     'M' => 'O'
    // ),
    // 'Rotate' => 0,
    // 'PZ' => 1,
// );

// $resolution = array(80,250);
// $fontname = $pdf->addTTFfont('/path-to-font/DejaVuSans.ttf', 'TrueTypeUnicode', '', 32);

// $pdf->SetY(45);
$pdf->SetFont('Courier', '', 6);
// $pdf->AddPage();
$pdf->AddPage('P', $page_format, false, false);

// set cell padding
$pdf->setCellPaddings(5, 15, 5, 15);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// set color for background
$pdf->SetFillColor(255, 255, 255);
$count = 0; $count2 = 0; $count0 = 0; $countec = 0;
foreach ($student_profile as $sp) {
    $sname = $sp->lastname.', '.$sp->firstname;
    $sgrade = $sp->level;
    $suser_id = $sp->user_id;
    $saddress = $sp->street.', '.$sp->barangay.', '.$sp->mun_city.', '.$sp->province.', Philippines';
    $ename = ""; $econtact = "";
    
    $epresent = 0;
    foreach ($ec_profile as $ep) {
        if ($suser_id==$ep->user_id) {
            $ename = $ep->ice_name;
            $econtact = $ep->ice_contact;
            $epresent = 1;
            $count = $count + 1; $count2 = $count2 + 1; $countec = $countec + 1;
        }
    }
    if ($ename=="" || $ename == null) {
        $count0 = $count0 + 1;
    }
    if ($count==4) {
        $pdf->Ln(29);
        $count = 0;
    }
    if ($count2==36) {
        $pdf->AddPage();
        $count2 = 0;
    }
if ($epresent==1) {
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$content = $sname.'
IN CASE OF EMERGENCY

'.$ename.'

'.$econtact.'
'.$saddress;

    // $pdf->Write(0, $sname, '', 0, 'L', true, 0, false, false, 0);
    // $pdf->Write(0, 'In case of emergency', '', 0, 'L', true, 0, false, false, 0);
    // $pdf->MultiCell(50, 30, $txt, 1, 'C', 1, 1, '' ,'', true, 0, false, true, 0, 'M');
    // $pdf->MultiCell(55, 40, $content, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'M');
    // $pdf->MultiCell(55, 40, $content, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'M');
    $pdf->MultiCell(45, 27, $content, 1, 'C', 1, 0, '', '', true, 0, false, false, 'T');
    // $pdf->MultiCell(55, 5, $content, 1, 'L', 1, 0, '', '', true);
}

        
    }


// $txt = 'This is just a test and the quick brown fox jumps over it right?
// -
// there is a great restriction. do you still approvie of it?';
// $i=0;
// for ($i=1; $i < 10; $i++) { 
//     $pdf->MultiCell(50, 30, $txt, 1, 'C', 1, 1, '' ,'', true, 0, false, true, 0, 'M');
//     $pdf->MultiCell(55, 5, '[LEFT] '.$txt, 1, 'L', 1, 0, '', '', true);
//     // $pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - MIDDLE] '.$txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'M');
//     // $pdf->MultiCell(55, 15, '[LEFT] '.$txt.'-'.$i, 1, 'L', 1, 0, '', '', true);
//     // $pdf->Cell(45, 10, 'TEST CELL STRETCH: force scaling:'.$txt.'-'.$i, 1, 1, 'C', 0, '', 2);
//     // $pdf->Ln(5);
// }

    $html = '
    <table>
        <tr>
            <th>Number of no contacts: '.$count0.'</th>
        </tr>
        <tr>
            <th>Number of emergency contact boxes: '.$countec.'</th>
        </tr>
    </table>
    ';

    // output the HTML content
    $pdf->writeHTML($html, true, 0, true, 0);
    // $pdf->AddPage();
    // } //forloop
    // } // if($az_level==$our_level)
// } // foreach ($accountz as $az) 


// force print dialog
$js .= 'print(true);';

// set javascript
$pdf->IncludeJS($js);


// reset pointer to the last page
$pdf->lastPage();

$title = 'or.pdf';

//Close and output PDF document
$pdf->Output($title, 'I');


//============================================================+
// END OF FILE
//============================================================+

