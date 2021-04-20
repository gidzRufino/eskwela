<?php

// ------------------------------ OTHER CREDENTIAL PRESENTED ------------------------------
// ------------------------------ SCHOLATIC RECORD ------------------------------
if ($i == 2):
    $pdf->AddPage();
    $pdf->SetY(5);
endif;
//if ($nYear > $year):
//    $sYear = '';
//else:
//    $sYear = $nYear;
//endif;


switch ($i):
    case 0:
    case 2:
        $sem = 1;
        $grade1 = 'first';
        $grade2 = 'second';
        break;
    case 1:
    case 3:
        $sem = 2;
        $grade1 = 'third';
        $grade2 = 'fourth';
        break;
endswitch;

$stID = $student->sprp_st_id;
$sRec = Modules::run('sf10/getSPRrec', $stID, $sYear);
$str_id = Modules::run('sf10/getStrand', $strand, 2);

if ($i == 2):
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->MultiCell(12, 5, "NAME:", '', 'L', 0, 0, '10', '', true, 0, false, true, 5, 'M');
    $pdf->MultiCell(90, 5, strtoupper($student->sprp_lastname . ',' . $student->sprp_firstname . ' ' . ($student->sprp_middlename != '' ? substr($student->sprp_middlename, 0, 2) . '.' : '')), '', 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
    $pdf->Ln();
endif;

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(230, 5, ($i == 0 || $i == 1 ? "F I R S T   S E M E S T E R" : "S E C O N D   S E M E S T E R"), '', 'C', 0, 0, '10', '', true, 0, false, true, 5, 'M');
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(23, 4, "Track / Strand:", '', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(102, 4, '', '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln();

$pdf->MultiCell(23, 4, "Grade Eleven:", '', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(102, 4, '', '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, "School Year:", '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, '', '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln();

$pdf->MultiCell(23, 4, "School:", '', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(102, 4, '', '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, "Address:", '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, '', '', 'L', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln(5);

// ------------------------------ SCHOLATIC RECORD ------------------------------
// ------------------------------ LEARNING AREAS ------------------------------


$aRec = Modules::run('sf10/getAcademicRecords', $stID, $sYear, $sRec->grade_level_id, $sem);

$pdf->SetFont('helvetica', 'B', 7);
$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(30, 12, "GRADE", '1', 'C', 1, 0, '10', '', true, 0, false, true, 12, 'M');
$pdf->MultiCell(115, 12, "SUBJECTS", '1', 'C', 1, 0, '', '', true, 0, false, true, 12, 'M');
$pdf->MultiCell(40, 6, "Quarter", '1', 'C', 1, 0, '', '', true, 0, false, true, 6, 'M');
$pdf->MultiCell(25, 12, "SEMESTER Final Grade", '1', 'C', 1, 0, '', '', true, 0, false, true, 12, 'M');
$pdf->MultiCell(20, 12, "REMARKS", '1', 'C', 1, 0, '', '', true, 0, false, true, 12, 'M');
$pdf->Ln(6);

$pdf->MultiCell(30, 6, '', '', 'C', 0, 0, '10', '', true, 0, false, true, 6, 'M');
$pdf->MultiCell(115, 6, "", '', 'C', 0, 0, '', '', true, 0, false, true, 6, 'M');
$pdf->MultiCell(20, 6, "1st", 1, 'C', 1, 0, '', '', true, 0, false, true, 6, 'M');
$pdf->MultiCell(20, 6, "2nd", 1, 'C', 1, 0, '', '', true, 0, false, true, 6, 'M');
$pdf->MultiCell(25, 6, "", '', 'C', 0, 0, '', '', true, 0, false, true, 6, 'M');
$pdf->MultiCell(20, 6, "", '', 'C', 0, 0, '', '', true, 0, false, true, 6, 'M');
$pdf->Ln();

$totalSubj = 0;
$isCore = '';
$totalCoreGrade = 0;
$totalAplSemGrade = 0;
$pdf->SetFont('helvetica', 'R', 6);
$pdf->SetFillColor(255, 255, 255);

if (count($aRec->result()) > 2):
    foreach ($aRec->result() as $sl):
        if ($sl->sem == $sem):
            $totalSubj++;
//        if ($sl->is_core == 1):
            $pdf->MultiCell(30, 4, ($sl->is_core == 1 ? 'CORE' : 'APPLIED'), '1', 'C', 1, 0, '10', '', true, 0, false, true, 4, 'M');
            $pdf->MultiCell(115, 4, $sl->subject, '1', 'L', 1, 0, '', '', true, 0, false, true, 4, 'M');
            $pdf->MultiCell(20, 4, $sl->$grade1, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
            $pdf->MultiCell(20, 4, $sl->$grade2, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
            $coreSemGrade = round(($sl->$grade1 + $sl->$grade2) / 2, 2);
            $totalCoreGrade += $coreSemGrade;
            $pdf->MultiCell(25, 4, $coreSemGrade, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
            $pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
            $pdf->Ln();
//        endif;
//    endforeach;
//    foreach ($aRec->result() as $sl):
//        if ($sl->is_core != 1):
//            $pdf->MultiCell(30, 4, 'APPLIED', '1', 'C', 1, 0, '10', '', true, 0, false, true, 4, 'M');
//            $pdf->MultiCell(115, 4, $sl->subject, '1', 'L', 1, 0, '', '', true, 0, false, true, 4, 'M');
//            $pdf->MultiCell(20, 4, $sl->first, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
//            $pdf->MultiCell(20, 4, $sl->second, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
//            $aplSemGrade = round(($sl->first + $sl->second) / 2, 2);
//            $totalAplSemGrade += $aplSemGrade;
//            $pdf->MultiCell(25, 4, $aplSemGrade, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
//            $pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
//            $pdf->Ln();
//        endif;
        endif;
    endforeach;

    $pdf->MultiCell(185, 4, 'Gen. Average ', '1', 'R', 1, 0, '10', '', true, 0, false, true, 4, 'M');
    $genAveSem = round(($totalCoreGrade + $totalAplSemGrade) / $totalSubj, 1);
    $pdf->MultiCell(25, 4, $genAveSem, '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
    $pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
else:
    for ($b = 0; $b < 12; $b++):
        $pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '10', '', true, 0, false, true, 4, 'M');
        $pdf->MultiCell(115, 4, '', '1', 'L', 1, 0, '', '', true, 0, false, true, 4, 'M');
        $pdf->MultiCell(20, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
        $pdf->MultiCell(20, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
        $pdf->MultiCell(25, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
        $pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
        $pdf->Ln();
    endfor;
endif;

$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(30, 4, "", 'LR', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, ($i == 0 || $i == 2 ? 'June' : 'Nov.'), '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, ($i == 0 || $i == 2 ? 'July' : 'Dec.'), '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, ($i == 0 || $i == 2 ? 'Aug.' : 'Jan.'), '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, ($i == 0 || $i == 2 ? 'Sept.' : 'Feb.'), '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, ($i == 0 || $i == 2 ? 'Oct.' : 'Mar.'), '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, 'TOTAL', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln();
$pdf->MultiCell(30, 4, "Days of School:", 'LR', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln();

$pdf->MultiCell(30, 4, "Days Present:", 'BLR', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(30, 4, '', '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(20, 4, "", '1', 'C', 1, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln();

$pdf->MultiCell(28, 4, "Remarks:", '0', 'L', 0, 0, '10', '', true, 0, false, true, 4, 'M');
$pdf->MultiCell(182, 4, "", '', 'C', 0, 0, '', '', true, 0, false, true, 4, 'M');
$pdf->Ln(6);


