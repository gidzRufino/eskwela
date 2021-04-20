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

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// remove default header/footer
$resolution= array(280,216);
$pdf->AddPage('P', $resolution);

$data['pdf'] = $pdf;
$data['firstHalf'] = 'Prelim';
$data['y'] = 3;
$data['secondHalf'] = 'Midterm';

$this->load->view('examPermits', $data);

$data['pdf'] = $pdf;
$data['firstHalf'] = 'Semi-Final';
$data['y'] = 140;
$data['secondHalf'] = 'Final';

$this->load->view('examPermits', $data);

//Close and output PDF document


$pdf->Output('studyLoad.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

