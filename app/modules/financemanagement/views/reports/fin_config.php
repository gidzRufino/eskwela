<?php
class MYPDF extends Pdf {

    public function Header() {
        // Logo
        // $image_file = K_PATH_IMAGES.'/lca_logo.png';
        $image_file = K_PATH_IMAGES.'/school_logo.png';
        $this->Image($image_file, 20, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);

      }
    public function Footer() {
      $settings = Modules::run('main/getSet');
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        // $settings = Modules::run('main/getSet');
        $this->SetFont('helvetica', 'I', 6);
        // Page number
        $this->Cell(0, 10, "Prepared and generated by $settings->set_school_name Administration through e-sKwela plus version 3.02 2014.", 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'UTF-8', false);
$pdf->SetTitle('Finance Settings for SY 2014-2015');
$pdf->SetSubject('Finance Settings for SY 2014-2015');
$pdf->SetKeywords('Settings, plans, Statement of Accounts');
$pdf->SetLeftMargin(5);
$pdf->SetRightMargin(3);
$resolution = array(218,297);

// $pdf->SetY(45);
$pdf->SetFont('helvetica', 'B', 10);

$school_name = $school_settings->set_school_name;
$address = $school_settings->set_school_address;
$cdate = date("F d, Y");

$rolling = '';
$detailing = '';
        
foreach($getLevel as $iLevel) { 
  $levelmain = $iLevel->grade_id;
  $levelz = $iLevel->level;
  $rolling = $rolling.'<table><tr><th colspan = "4" style="text-align:center; font-size: 14px;" >'.$levelz.'</th></tr>
  <tr><th>Item Descritpion</th><th>Amount</th><th>Payment Frequency</th><th>Plan</th></tr>';

  foreach($showInitsy as $si){ if($si->level_id==$iLevel->grade_id){
    $itDescID = $si->item_id;
      $initID = $si->init_id;
      $itemAmount = $si->item_amount;
      $schedID = $si->schedule_id;
      $syID = $si->sy_id;
      $impDate = $si->implement_date;
      $itPlan = $si->plan_id;  
    
  foreach($showItems as $sItem){ if($sItem->item_id==$itDescID){
      $itDescription = $sItem->item_description;
  }}

  foreach($sfrequency as $sFreq){ if($sFreq->schedule_id==$schedID){
      $itSchedule = $sFreq->schedule_description;
  }} 
  foreach($school_year as $sy){ if($sy->sy_id==$syID){
      $itSY = $sy->school_year;
  }} 
  foreach($showPlan as $sp){ if($sp->plan_id==$itPlan){
      $itPlanD = $sp->plan_description;
  }} 

  $rolling = $rolling.'<tr><td style="text-align:center;border: 1px solid black;">'.$itDescription.'</td><td style="text-align:center;border: 1px solid black;">'.number_format($itemAmount, 2, '.', ',').'</td><td style="text-align:center;border: 1px solid black;">'.$itScheduel.'</td><td style="text-align:center;border: 1px solid black;">'.$itPlanD.'</td></tr>'

}}

  $rolling = $rolling.'</table><br />'

} // foreach($getLevel as $iLevel) { 

$html = '
<table >
  <tr>
    <td>
      '.$rolling.'
    </td>
  </tr>
</table>
';

  // output the HTML content
  $pdf->writeHTML($html, true, 0, true, 0);
  // $pdf->AddPage();
  // } //forloop
//   } // if($az_level==$our_level)
// } // foreach ($accountz as $az) 

// reset pointer to the last page
$pdf->lastPage();

$title = $school_name.'_'.$my_grade.'_soa_'.$cdate.'.pdf';

//Close and output PDF document
$pdf->Output($title, 'I');


//============================================================+
// END OF FILE
//============================================================+

?>
