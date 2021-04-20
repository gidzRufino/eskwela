<?php

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {

        // Logo

       
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
    }

}

// create new PDF document

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
$resolution = array(235, 175);
$pdf->AddPage('L', $resolution);
switch (segment_5) {
    case 1:
    case 2:
        $sem = 1;
        $term = 'First';
        $one = '1st';
        $two = '2nd';
        break;
    case 3:
    case 4:
        $sem = 2;
        $term = 'Second';
        $one = '3rd';
        $two = '4th';
        break;
}

$settings = Modules::run('main/getSet');
$strand_id = $this->uri->segment(8);
$strandCode = Modules::run('subjectmanagement/getStrandCode',$strand_id);
$section = Modules::run('registrar/getSectionById', segment_4);
$coreSubs = Modules::run('subjectmanagement/getSHSubjects', $section->grade_id, $sem, $strand_id, 1);   
$appliedSubs = Modules::run('subjectmanagement/getSHSubjects', $section->grade_id, $sem, $strand_id);
$subject_ids = Modules::run('academic/getSpecificSubjectPerlevel', $section->grade_id);
$students = Modules::run('registrar/getStudentForCard', '', segment_6, segment_4);
$sy = $this->session->userdata('school_year');
$quarter = segment_5;
$adviser = Modules::run('academic/getAdviser',segment_4,$section->grade_id,$sy);
$nxtYr = $sy + 1;

// -----------------------------------------------------------------------------
foreach ($students->result() as $mStudent):
    $data['mStudent'] = $mStudent;
    $data['section'] = $section;
    $data['grade_id'] = $section->grade_id;
    $data['subject_ids'] = $subject_ids;
    $data['adviser'] = $adviser;
    $data['sy'] = $sy;
    $data['nxtYr'] = $nxtYr;
    $data['pdf'] = $pdf;
    $data['x'] = 5;
    $data['y'] = 0;
    $data['term'] = $quarter;
    $data['coreSubs'] = $coreSubs;
    $data['appliedSubs'] = $appliedSubs;
    $data['shStrand'] = $strandCode;
    
    $this->load->view('pisd_cardDetails_sh', $data);
    $pdf->AddPage();
endforeach;

// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
