<?php

class MYPDF extends Pdf {

    //Page header
    public function Header() {
        // Logo
        $section = Modules::run('registrar/getSectionById', segment_3);
        $this->SetTitle('School Form 5 (SF 5) Report on Promotion & Level of Proficiency');
//                $this->SetTopMargin(4);
        $image_file = K_PATH_IMAGES . '/depEd_logo.jpg';
        $this->Image($image_file, 5, 25, 30, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->SetY(25);
        $this->Cell(0, 0, 'School Form 5 (SF 5) Report on Promotion & Level of Proficiency', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln();

        $this->SetFont('helvetica', 'n', 8);
        // Title
//        $this->SetXY(40, 15);
        $this->Cell(0, 15, '(This replaces Forms 18-E1, 18-E2, 18A and List of Graduates)', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        $settings = Modules::run('main/getSet');
        $nextYear = segment_4 + 1;


        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(50, 40);
        $this->Cell(0, 4.3, "Region: " . $settings->region, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetY(40);
        $this->Cell(0, 4.3, "Division: " . $settings->division, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetXY(225, 40);
        $this->Cell(0, 4.3, "District: " . $settings->district, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetXY(50, 47);
        $this->Cell(0, 4.3, "School ID : " . $settings->school_id, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetY(47);
        $this->Cell(0, 4.3, "School Year : " . segment_4 . '-' . $nextYear, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetXY(217, 47);
        $this->Cell(0, 4.3, "Curriculum: K - 12", 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetXY(50, 52);
        $this->Cell(0, 4.3, "School Name : " . $settings->set_school_name, 0, 0, 'L');
        $this->SetXY(215.5, 52);
        $this->Cell(0, 4.3, "Grade Level: " . $section->level, 0, 0, 'L');
        $this->SetXY(275, 52);
        $this->Cell(0, 4.3, "Section: " . $section->section, 0, 0, 'L');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom

        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'School Form 5: Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetLeftMargin(3);
$pdf->SetRightMargin(3);
$section = Modules::run('registrar/getSectionById', segment_3);
$settings = Modules::run('main/getSet');
$num_of_pages = $pdf->getAliasNbPages();
$currentPage = $pdf->getAliasNumPage();

function getFinalGrade($grade) {
    $plg = Modules::run('gradingsystem/getLetterGrade', $grade);
    foreach ($plg->result() as $plg) {
        if ($grade >= $plg->from_grade && $grade <= $plg->to_grade) {


            $grade = $plg->letter_grade;

            if ($grade != "" || $grade != 0 || $grade < 60):
                $grade = $grade;
            else:
                $grade = "";
            endif;
        }
    }

    return $grade;
}

function getStatus($score, $irr = NULL) {
    if ($score >= 75):
        $status = 'Promoted';
    else:
        $status = 'Retained';
    endif;
    if ($irr == 'inc'):
        $status = 'Irregular';
    endif;

    return $status;
}

function getSideNames($pdf) {
    $adviser = Modules::run('academic/getAdvisory', NULL, segment_4, segment_3);
    $principal = Modules::run('hr/getEmployeeByPosition', 'Grade School Principal');
    $pdf->SetXY(240, 60);
    $pdf->SetFont('helvetica', 'N', 7);
    $pdf->MultiCell(80, 8, 'Prepared By:', 0, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->MultiCell(80, 8, strtoupper($adviser->row()->firstname . ' ' . substr($adviser->row()->middlename, 0, 1) . '. ' . $adviser->row()->lastname), "B", 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    $pdf->SetFont('helvetica', 'N', 7);
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, 'Class Adviser', 0, 'C', 0, 0, '', '', true, 0, false, true, 8, 'T');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, '(Name and Signature)', 0, 'C', 0, 0, '', '', true, 0, false, true, 8, 'T');

    //
    $pdf->Ln(6);
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, 'CERTIFIED CORRECT & SUBMITTED:', 0, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln(6);
    $pdf->SetX(240);
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->MultiCell(80, 8, strtoupper($principal->firstname . ' ' . substr($principal->middlename, 0, 1) . '. ' . $principal->lastname), "B", 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->SetFont('helvetica', 'N', 7);
    $pdf->MultiCell(80, 8, 'School Head', 0, 'C', 0, 0, '', '', true, 0, false, true, 8, 'T');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, '(Name and Signature)', 0, 'C', 0, 0, '', '', true, 0, false, true, 8, 'T');

    //
    $pdf->Ln(6);
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, 'REVIEWED BY:', 0, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln(6);
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, '', "B", 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, 'Division Representative', 0, 'C', 0, 0, '', '', true, 0, false, true, 8, 'T');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, '(Name and Signature)', 0, 'C', 0, 0, '', '', true, 0, false, true, 8, 'T');

    //
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, 'GUIDELINES:', 0, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 8, '1. For All Grade/Year Levels', 0, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 10, '2. To be prepared by the Adviser. Final rating per subject area should be taken from the record of subject teachers. The class adviser should compute for the General Average. ', 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 10, '3. On the summary table, reflect the total number of learners promoted, retained and *irregular (*for grade 7 onwards only) and the level of proficiency according to the individual General Average.', 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 10, '4. Must tally with the total enrollment report as of End of School Year GESP /GSSP (EBEIS).', 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T');
    $pdf->Ln();
    $pdf->SetX(240);
    $pdf->MultiCell(80, 10, '5. Protocols of validation & submission is under the discretion of the Schools Division Superintendent.', 0, 'L', 0, 0, '', '', true, 0, false, true, 10, 'T');
}

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// remove default header/footer
$resolution = array(216, 330);
$pdf->AddPage('L', $resolution);

$mad = 0;
$mpr = 0;
$map =  0;
$mdev = 0;
$mbeg = 0;

$fad = 0;
$fpr = 0;
$fap =  0;
$fdev = 0;
$fbeg = 0;

$mirregular =0;
$firregular =0;
$miretained = 0;
$firetained = 0;
$mPromoted = 0;
$fPromoted = 0;
foreach($male->result() as $s){
    $st_id = ($s->lrn==""?$s->st_id:$s->lrn);
    $FA = Modules::run('gradingsystem/getFinalAverage',$s->st_id, segment_4);
    $INC = Modules::run('reports/getINC', $s->st_id, segment_4);
    $maINC = json_decode($INC);
    if($FA>=75):
        $mPromoted += 1;
    else:
        $miretained += 1;
    endif;
    if($maINC->bsp>0):
        $mirregular +=1;
    endif;
    if($maINC->bs>0):
        if($maINC->bsp>0):
        else:
            $mirregular +=1;
        endif;
    endif;
    if($section->grade_level_id!=18):
        switch (getFinalGrade($FA)):
            case 'A':
                $mad += 1;
                break;
            case 'P':
                $mpr +=1;
                break;
            case 'AP':
                $map +=1;
                break;
            case 'D':
                $mdev += 1;
                break;
            case 'B':
                $mbeg += 1;
                break;
            default :
                $mad = 0;
                $mpr = 0;
                $map =  0;
                $mdev = 0;
                $mbeg = 0;
                break;
        endswitch;
    else:
        if($FA>=90):
            $mad += 1;
        else:
            if($FA>=85 && $FA<90):
                $mpr +=1;
            else:
                if($FA>=80 && $FA<85):
                    $map +=1;
                else:
                    if($FA>=75 && $FA<80):
                        $mdev +=1;
                    else:
                        if($FA<75):
                            $mbeg +=1;
                        endif;
                    endif;
                endif;
            endif;
        endif;
    endif;
}
foreach($female->result() as $f){
    $FA = Modules::run('gradingsystem/getFinalAverage',$f->st_id, segment_4);
    $INC = Modules::run('reports/getINC', $f->st_id, segment_4);
    $finINC = json_decode($INC);
    if($FA>=75):
        $fPromoted += 1;
    else:
        $firetained += 1;
    endif;
    if($finINC->bsp>0):
        $firregular +=1;
    endif;
    if($finINC->bs>0):
        if($finINC->bsp>0):
        else:
            $firregular +=1;
        endif;  
    endif;
    if($section->grade_level_id!=18):
//        switch (getFinalGrade($FA)):
//            case 'A':
//                $fad += 1;
//                break;
//            case 'P':
//                $fpr +=1;
//                break;
//            case 'AP':
//                $fap +=1;
//                break;
//            case 'D':
//                $fdev += 1;
//                break;
//            case 'B':
//                $fbeg += 1;
//                break;
//            default :
//                $fad = 0;
//                $fpr = 0;
//                $fap =  0;
//                $fdev = 0;
//                $fbeg = 0;
//            break;
//        endswitch;
        if($FA>=90):
            $fad += 1;
        else:
            if($FA>=85 && $FA<90):
                $fpr +=1;
            else:
                if($FA>=80 && $FA<85):
                    $fap +=1;
                else:
                    if($FA>=75 && $FA<80):
                        $fdev +=1;
                    else:
                        if($FA>=75 && $FA<80):
                            $fbeg +=1;
                        endif;
                    endif;
                endif;
            endif;
        endif;
   else:
        if($FA>=90):
            $fad += 1;
        else:
            if($FA>=85 && $FA<90):
                $fpr +=1;
            else:
                if($FA>=80 && $FA<85):
                    $fap +=1;
                else:
                    if($FA>=75 && $FA<80):
                        $fdev +=1;
                    else:
                        if($FA>=75 && $FA<80):
                            $fbeg +=1;
                        endif;
                    endif;
                endif;
            endif;
        endif;
    endif;
}
// right side summary
// Summary Table
$pdf->SetXY(240, 95);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(80, 8, 'SUMMARY TABLE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 8, 'STATUS',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, 'MALE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, 'FEMALE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, 'TOTAL',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 8, 'PROMOTED',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, ($mPromoted - ($mirregular)),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, ($fPromoted - ($firregular)),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, (($mPromoted+$fPromoted)-(($mirregular+$firregular))),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'pro_m',($mPromoted - ($mirregular)), $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'pro_f',($fPromoted - ($firregular)), $settings->school_year);
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 8, 'IRREGULAR',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, $mirregular,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, $firregular,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, $mirregular+$firregular,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'irr_m',$mirregular, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'irr_f',$firregular, $settings->school_year);
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 8, 'RETAINED',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, $miretained,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, $firetained,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, $miretained+$firetained,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 're_m',$miretained, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 're_f',$firetained, $settings->school_year);
$pdf->Ln(10);
// Proficiency Table
$pdf->SetX(240);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->MultiCell(80, 8, 'LEVEL OF PROFICIENCY',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, 'MALE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, 'FEMALE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->MultiCell(20, 8, 'TOTAL',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 10, 'BEGINNNING
(B: 74% and below)',1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mbeg,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $fbeg,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mbeg+$fbeg,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'b_m',$mbeg, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'b_f',$fbeg, $settings->school_year);
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 10, 'DEVELOPING
(D: 75%-79%)',1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mdev,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $fdev,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mdev+$fdev,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'd_m',$mdev, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'd_f',$fdev, $settings->school_year);
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 10, 'APPROACHING PROFICIENCY
(AP: 80%-84%)',1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $map,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $fap,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $map+$fap,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,$section->section_id, 'ap_m',$map, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,$section->section_id, 'ap_f',$fap, $settings->school_year);
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 10, 'PROFICIENT
(P: 85% -89%)',1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mpr,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $fpr,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mpr+$fpr,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'p_m',$mpr, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'p_f',$fpr, $settings->school_year);
$pdf->Ln();
$pdf->SetX(240);
$pdf->MultiCell(20, 10, 'ADVANCED
(A: 90%  and above)',1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mad,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $fad,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
$pdf->MultiCell(20, 10, $mad+$fad,1, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M');
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'a_m',$mad, $settings->school_year);
    Modules::run('gradingsystem/savePromotion', $section->grade_level_id,segment_3, 'a_f',$fad, $settings->school_year);

// end of Level of Proficiency


//end of right side summary

$pdf->SetY(65);
$pdf->SetFont('helvetica', 'B', 7);
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->MultiCell(25, 13.5, 'LRN','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->MultiCell(70, 13.5, 'NAME
(Last Name, First Name, Middle Name) ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->SetFont('helvetica', 'N', 7);
$pdf->MultiCell(30, 13.5, 'GENERAL AVERAGE (Numerical Value in 2 decimal places and 3 decimal places for honor learners, and Descriptive Letter)','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(30, 13.5, 'ACTION TAKEN: PROMOTED, IRREGULAR or RETAINED','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->SetFont('helvetica', 'N', 7);
$pdf->MultiCell(80, 16, 'INCOMPLETE SUBJECT/S 
(This column is for K to 12 Curriculum and remaining RBEC in High School. Elementary grades level that are still implementing RBEC need not to fill up these columns)',1, 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
$pdf->Ln();
$pdf->MultiCell(25, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->MultiCell(70, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->SetFont('helvetica', 'N', 7);
$pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
$pdf->SetFont('helvetica', 'N', 7);
$pdf->MultiCell(40, 16, 'From previous school years completed as of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
$pdf->MultiCell(40, 16, 'As of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
$pdf->Ln();

$y = 0;
$totalColumn = 0;
foreach($male->result() as $s){
    $FA = Modules::run('gradingsystem/getFinalAverage',$s->st_id, segment_4);
    $FC = NULL;
    $INC = Modules::run('reports/getINC', $s->st_id, segment_4);
    $maINC = json_decode($INC);
    if($maINC->bsp>0 || $maINC->bs>0):
        $FC = 'inc';
    endif;
    $totalColumn++;
    $y++;
    $pdf->MultiCell(25, 8, ($s->lrn==""?$s->st_id:$s->lrn),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->MultiCell(70, 8, strtoupper($s->lastname.', '.$s->firstname.' '.$s->middlename),1, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->SetFont('helvetica', 1, 8);
    if($section->grade_level_id==18):
        $pdf->MultiCell(30, 8, $FA,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    else:
        $pdf->MultiCell(30, 8, $FA.' / '.getFinalGrade($FA),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    endif;
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->MultiCell(30, 8, strtoupper(getStatus($FA, $FC)),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->SetFont('helvetica', 'N', 7);
    $pdf->MultiCell(40, 8, $maINC->backSubp,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->MultiCell(40, 8, $maINC->backSubject,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    
    if($y==13):
        $y=0;
        $pdf->AddPage();         
        $pdf->SetY(65);
        $pdf->SetFont('helvetica', 'B', 7);
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->MultiCell(25, 13.5, 'LRN ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, 'NAME
        (Last Name, First Name, Middle Name) ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, 'GENERAL AVERAGE (Numerical Value in 2 decimal places and 3 decimal places for honor learners, and Descriptive Letter)','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, 'ACTION TAKEN: PROMOTED, IRREGULAR or RETAINED','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(80, 16, 'INCOMPLETE SUBJECT/S 
        (This column is for K to 12 Curriculum and remaining RBEC in High School. Elementary grades level that are still implementing RBEC need not to fill up these columns)',1, 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
        $pdf->MultiCell(25, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(40, 16, 'From previous school years completed as of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->MultiCell(40, 16, 'As of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
    endif; 
}
for($i=1;$i<=3;$i++)
{
    $totalColumn++;
    $y++;
    if($i==2):
            $pdf->MultiCell(25, 8, $male->num_rows(),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->MultiCell(70, 8, '<<<    TOTAL MALE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln(); 
        else:
            $pdf->MultiCell(25, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(70, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln();   
    endif;
    if($totalColumn==16):
        $y=0;
        $pdf->AddPage();         
        $pdf->SetY(65);
        $pdf->SetFont('helvetica', 'B', 7);
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->MultiCell(25, 13.5, 'LRN ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, 'NAME
        (Last Name, First Name, Middle Name) ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, 'GENERAL AVERAGE (Numerical Value in 2 decimal places and 3 decimal places for honor learners, and Descriptive Letter)','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, 'ACTION TAKEN: PROMOTED, IRREGULAR or RETAINED','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(80, 16, 'INCOMPLETE SUBJECT/S 
        (This column is for K to 12 Curriculum and remaining RBEC in High School. Elementary grades level that are still implementing RBEC need not to fill up these columns)',1, 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
        $pdf->MultiCell(25, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(40, 16, 'From previous school years completed as of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->MultiCell(40, 16, 'As of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
    endif;

}

foreach($female->result() as $f){
    $y++;
    $FA = Modules::run('gradingsystem/getFinalAverage',$f->st_id, segment_4);
    $FC = NULL;
    $INC = Modules::run('reports/getINC', $f->st_id, segment_4);
    $finINC = json_decode($INC);
    
    if($finINC->bsp>0 || $finINC->bs>0):
        $FC = 'inc';
    endif;
    $pdf->SetFont('helvetica', 'N', 7);
    $pdf->MultiCell(25, 8, ($f->lrn==""?$f->st_id:$f->lrn),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->MultiCell(70, 8, strtoupper($f->lastname.', '.$f->firstname.' '.$f->middlename),1, 'L', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->SetFont('helvetica', 1, 8);
    if($section->grade_level_id==18):
        $pdf->MultiCell(30, 8, $FA,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    else:
        $pdf->MultiCell(30, 8, $FA.' / '.getFinalGrade($FA),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    endif;
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->MultiCell(30, 8, strtoupper(getStatus($FA, $FC)),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->SetFont('helvetica', 'N', 7);
    $pdf->MultiCell(40, 8, $finINC->backSubp,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->MultiCell(40, 8, $finINC->backSubject,1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
    $pdf->Ln();
    
    if($y==13):
        $y=0;
        $pdf->AddPage();
        $pdf->SetY(65);
        $pdf->SetFont('helvetica', 'B', 7);
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->MultiCell(25, 13.5, 'LRN','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, 'NAME
        (Last Name, First Name, Middle Name) ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, 'GENERAL AVERAGE (Numerical Value in 2 decimal places and 3 decimal places for honor learners, and Descriptive Letter)','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, 'ACTION TAKEN: PROMOTED, IRREGULAR or RETAINED','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(80, 16, 'INCOMPLETE SUBJECT/S 
        (This column is for K to 12 Curriculum and remaining RBEC in High School. Elementary grades level that are still implementing RBEC need not to fill up these columns)',1, 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
        $pdf->MultiCell(25, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(40, 16, 'From previous school years completed as of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->MultiCell(40, 16, 'As of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
    endif;
    
}

for($i=1;$i<=2;$i++)
{
    $totalColumn++;
    $y++;
    
    if($y==13):
        $y=0;
        $pdf->AddPage();
        $pdf->SetY(65);
        $pdf->SetFont('helvetica', 'B', 7);
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->MultiCell(25, 13.5, 'LRN','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, 'NAME
        (Last Name, First Name, Middle Name) ','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, 'GENERAL AVERAGE (Numerical Value in 2 decimal places and 3 decimal places for honor learners, and Descriptive Letter)','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, 'ACTION TAKEN: PROMOTED, IRREGULAR or RETAINED','LTR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(80, 16, 'INCOMPLETE SUBJECT/S 
        (This column is for K to 12 Curriculum and remaining RBEC in High School. Elementary grades level that are still implementing RBEC need not to fill up these columns)',1, 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
        $pdf->MultiCell(25, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->MultiCell(70, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->MultiCell(30, 13.5, '','LBR', 'C', 0, 0, '', '', true, 0, false, true, 27, 'M');
        $pdf->SetFont('helvetica', 'N', 7);
        $pdf->MultiCell(40, 16, 'From previous school years completed as of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->MultiCell(40, 16, 'As of end of current School Year','LBR', 'C', 0, 0, '', '', true, 0, false, true, 16, 'M');
        $pdf->Ln();
    endif;
    if($i==2):
            $pdf->MultiCell(25, 8, $female->num_rows(),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->MultiCell(70, 8, '<<<    TOTAL FEMALE',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln(); 
            $pdf->MultiCell(25, 8, $male->num_rows()+$female->num_rows(),1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->MultiCell(70, 8, '<<<   COMBINED',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln();
        else:
            $pdf->MultiCell(25, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(70, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(30, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, '',1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln();   
    endif;
     

}
if (($male->num_rows() + $female->num_rows()) < 10):
    $pdf->AddPage();
endif;

    getSideNames($pdf);
//$html = Modules::run('reports/form1');
//
//$pdf->writeHTML($html, true, false, true, false, '');
// ---------------------------------------------------------
// set default header data
//Close and output PDF document
$pdf->Output('DepEdForm5_' . $section->level . '_' . $section->section . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
