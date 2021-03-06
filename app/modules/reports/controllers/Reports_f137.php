<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of print
 *
 * @author genesis
 */
class reports_f137 extends MX_Controller {
    //put your code here

    public function __construct() {
        parent::__construct();
        $this->load->library('Pdf');
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->model('reports_model_f137');
        $this->load->model('reports_model');
        set_time_limit(300) ;
       
    }
    
    private function post($name)
    {
        return $this->input->post($name);
    }
    
    
    function gsUpdateBySection($section_id)
    {
        $getStudents = $this->reports_model_f137->getStudents($section_id);
        $i=0;
        foreach ($getStudents->result() as $student):
            $details = array('grading' => 1);
            $this->db->where('st_id', $student->st_id);
            $this->db->where('subject_id', 9);
            $this->db->where('school_year', 2018);
            $this->db->where('grading', 0);
            
            if($this->db->update('gs_final_card', $details)):
                $i++;
            endif;
            
        endforeach;
        echo 'Successfully updated '.$i.' records';
    }
    
    function deleteDups()
    {
        $query = "DELETE t1 from esk_gs_raw_score as t1, esk_gs_raw_score as t2 WHERE t1.raw_id < t2.raw_id AND t1.assess_id = t2.assess_id AND t1.st_id = t2.st_id";
        if($this->db->query($query)):
            echo 'Successfully Deleted Duplicates';
        endif;
    }
    
    function uploadDeportment()
    {
        $this->load->library("excel");
        //here i used microsoft excel 2007
        $data['error'] = ''; //initialize image upload error array to empty
        
        $config['upload_path'] = 'uploads';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

         // If upload failed, display error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();

            ?>
                <script type="text/javascript">
                    alert('Error: <?php echo $data['error'] ?>');
                    document.location = '<?php echo base_url('reports/generateForm137') ?>'
                </script>
            <?php
        } else {

            $file_data = $this->upload->data();
            $file_path = 'uploads/'.$file_data['file_name'];
            switch ($file_data['file_ext']):
                case '.xls':    
                case '.XLS':
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                break;    
                case '.xlsx':
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                break;    
            endswitch;
            //set to read only
            $objReader->setReadDataOnly(true);
            //load excel file
            $objPHPExcel = $objReader->load($file_path);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); 

            
            $num_rows = $objWorksheet->getHighestRow();
            
            $school_year = $objWorksheet->getCellByColumnAndRow(0,1)->getValue();
            $term = $objWorksheet->getCellByColumnAndRow(0,2)->getValue();
            $n = 0;
            for($st=7; $st<=($num_rows); $st++):
                for($col=2; $col<=15; $col++):
                    
                    $st_id = $objWorksheet->getCellByColumnAndRow(0,$st)->getValue();
                    $dprt_code = $objWorksheet->getCellByColumnAndRow($col,3)->getValue();
                    $dprt_grade = $objWorksheet->getCellByColumnAndRow($col,$st)->getValue();
                    //echo $dprt_grade.' | ';
                    if($dprt_code!=NULL):
                        $n++;
                        Modules::run('gradingsystem/saveBH', $st_id, $dprt_grade, $term, $school_year, $dprt_code);
                    endif;
                endfor;
                //echo '<br />';
            endfor;
            
        }
     
            ?>
                <script type="text/javascript">
                    alert('Records Successfully Uploaded');
                    document.location = '<?php echo base_url('reports') ?>'
                </script>
            <?php
     
    /**  

     * @param type $value
     * @param type $term
     * @param type $year     */
    }
    
    function exportStudentListForDeportment($value, $term, $year=NULL)
    {
        $this->load->library('eskwela');
        $this->load->library('excel');
        $this->load->helper('download');
        $settings = $this->eskwela->getSet();
        
        $year = ($year==NULL?$this->session->school_year:$year);
        
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $section = Modules::run('registrar/getSectionById', $value);
        $deportment = Modules::run('gradingsystem/getBehaviorRate');
        
        $this->excel->getActiveSheet()->setTitle($section->level.'-'.$section->section);
        
        $this->excel->getActiveSheet()->setCellValue('A1', $year);
        $this->excel->getActiveSheet()->setCellValue('A2', $term);
        $this->excel->getActiveSheet()->setCellValue('A3', '[ Do not Edit or Delete this Row ]');
        $this->excel->getActiveSheet()->setCellValue('A4', '[ Do not Edit or Delete this Row ]');
        
        
        $this->excel->getActiveSheet()->setCellValue('C3', 18);
        $this->excel->getActiveSheet()->setCellValue('D3', 19);
        $this->excel->getActiveSheet()->setCellValue('E3', 20);
        $this->excel->getActiveSheet()->setCellValue('F3', 21);
        $this->excel->getActiveSheet()->setCellValue('G3', 22);
        $this->excel->getActiveSheet()->setCellValue('H3', 23);
        $this->excel->getActiveSheet()->setCellValue('I3', 24);
        $this->excel->getActiveSheet()->setCellValue('J3', 25);
        $this->excel->getActiveSheet()->setCellValue('K3', 26);
        $this->excel->getActiveSheet()->setCellValue('L3', 27);
        $this->excel->getActiveSheet()->setCellValue('M3', 28);
        $this->excel->getActiveSheet()->setCellValue('N3', 29);
        $this->excel->getActiveSheet()->setCellValue('O3', 30);
        
        $this->excel->getActiveSheet()->setCellValue('C4', 'Cooperation');
        $this->excel->getActiveSheet()->setCellValue('D4', 'Diligence');
        $this->excel->getActiveSheet()->setCellValue('E4', 'Honesty');
        $this->excel->getActiveSheet()->setCellValue('F4', 'Leadership');
        $this->excel->getActiveSheet()->setCellValue('G4', 'Obedience');
        $this->excel->getActiveSheet()->setCellValue('H4', 'Speak in English Policy');
        $this->excel->getActiveSheet()->setCellValue('I4', 'School Rules and Regulation');
        $this->excel->getActiveSheet()->setCellValue('J4', 'Anti-Bullying Policy');
        $this->excel->getActiveSheet()->setCellValue('K4', 'Promptness');
        $this->excel->getActiveSheet()->setCellValue('L4', 'Prudence');
        $this->excel->getActiveSheet()->setCellValue('M4', 'Punctuality');
        $this->excel->getActiveSheet()->setCellValue('N4', 'Respect for Others');
        $this->excel->getActiveSheet()->setCellValue('O4', 'Responsibility');

        $this->excel->getActiveSheet()->setCellValue('A6', 'ID Number');
        $this->excel->getActiveSheet()->setCellValue('B6', 'STUDENT NAME');
        
        $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        
        $students = $this->reports_model_f137->getPreviousStudent($value, $year);
        //$students = $this->get_registrar_model->getAllCollegeStudents(600, 0, $level_id, NULL, $sy);
        $column = 6;
        foreach ($students as $s):
            $column++;
            $this->excel->getActiveSheet()->setCellValue('A'.$column, $s->stid);
            $this->excel->getActiveSheet()->getStyle('A'.$column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->setCellValue('B'.$column, strtoupper($s->lastname.', '.$s->firstname.' '.substr($s->middlename, 0,1).'. '));
        endforeach;
        
        $filename=$settings->short_name.'_'.$year.'_'.$s->level.'_'.$s->section.'.xls'; //save our workbook as this file name
       // $filename=$settings->short_name.'_'.$sy.'_'.$s->course_id.'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    function getSubjectId($subject)
    {
        $subject_id = $this->reports_model_f137->getSubjectId($subject);
        return $subject_id;
    }
    
    function getGradeLevelId($level)
    {
        $grade_id = $this->reports_model_f137->getGradeLevelId($level);
        return $grade_id;
    }
    
    function getLatestIDNum($year)
    {
        $idNum = $this->reports_model_f137->getLatestIDNum($year);
        return $idNum+1;
    }
    
    function uploadF137()
    {
        $this->load->library("excel");
        //here i used microsoft excel 2007
        $data['error'] = ''; //initialize image upload error array to empty
        
        $config['upload_path'] = 'uploads';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

         // If upload failed, display error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();

            ?>
                <script type="text/javascript">
                    alert('Error: <?php echo $data['error'] ?>');
                    document.location = '<?php echo base_url('reports/generateForm137') ?>'
                </script>
            <?php
        } else {

            $file_data = $this->upload->data();
            $file_path = 'uploads/'.$file_data['file_name'];
            switch ($file_data['file_ext']):
                case '.xls':    
                case '.XLS':
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                break;    
                case '.xlsx':
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                break;    
            endswitch;
            //set to read only
            $objReader->setReadDataOnly(true);
            //load excel file
            $objPHPExcel = $objReader->load($file_path);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); 

            
            $num_rows = $objWorksheet->getHighestRow();

//            $num_rows  = 8;


           $gradeLevel = $objWorksheet->getCellByColumnAndRow(0,1)->getValue();
           $grade_id = $this->getGradeLevelId($gradeLevel);
           $school_year = $objWorksheet->getCellByColumnAndRow(2,1)->getValue();
//           $school = $objWorksheet->getCellByColumnAndRow(3,1)->getValue();
//           $adviser = $objWorksheet->getCellByColumnAndRow(0,2)->getValue();
           $numberOfColumns = PHPExcel_Cell::columnIndexFromString($objPHPExcel->setActiveSheetIndex(0)->getHighestColumn());
           
           $uploadOption = $this->post('uploadOption');
        
            $n = 0;
            for($st=4; $st<=($num_rows); $st++):

                if($st>=5):
                    $st_id = $objWorksheet->getCellByColumnAndRow(0,$st)->getValue();
                    if($st_id!=NULL):
                        $name = $objWorksheet->getCellByColumnAndRow(1,$st)->getValue();
                        $section = $objWorksheet->getCellByColumnAndRow(2,$st)->getValue();
                        $adviser = $objWorksheet->getCellByColumnAndRow(3,$st)->getValue();
                        $school = $objWorksheet->getCellByColumnAndRow(4,$st)->getValue();

                        $nameItems = explode( ",", $name );
                        $lastname = $nameItems[0];
                        $remName = $nameItems[1];
                        $last_word_start = strrpos ( $remName , " ") + 1;
                        $last_word_end = strlen($remName) - 1;
                        $middlename = substr($remName, $last_word_start, $last_word_end);
                        $firstnames = explode( " ", $remName );
                        array_splice( $firstnames, -2 );
                        $firstname = implode( " ", $firstnames );
                        $exist = $this->reports_model_f137->checkAcadExist($st_id, $school_year);
                        if($st_id==0):
                            if($uploadOption=='0'):
                                $latestId = $this->getLatestIDNum($school_year);
                                switch (TRUE):
                                    case $latestId < 10:
                                        $brd = '000';
                                    break;
                                    case $latestId < 100:
                                        $brd = '00';

                                    break;
                                    case $latestId < 1000:
                                        $brd = '0';
                                    break;
                                    default :
                                        $brd = '';
                                    break;    

                                endswitch;

                                $array = array(
                                    'sprp_st_id'        => $school_year.$grade_id.$brd.$latestId,
                                    'sprp_lrn'          => '',
                                    'sprp_lastname'     => trim($lastname),
                                    'sprp_firstname'    => trim($firstname),
                                    'sprp_middlename'   => trim($middlename)
                                    );
                                $profileSave = $this->savePersonalInfo($array, trim($lastname), trim($firstname), trim($middlename),$school_year);
                                $st_id = $school_year.$grade_id.$brd.$latestId;   
                            endif;
                        else:
                            $array = array(
                                'sprp_st_id'        => $st_id,
                                'sprp_lrn'          => '',
                                'sprp_lastname'     => trim($lastname),
                                'sprp_firstname'    => trim($firstname),
                                'sprp_middlename'   => trim($middlename)
                                );
                            $profileSave = $this->savePersonalInfo($array, trim($lastname), trim($firstname), trim($middlename),$school_year); 
                        endif;
                        if(!$exist):
                            if($uploadOption=='0'):
                                $spr = array(
                                 'st_id'            => $st_id,
                                 'grade_level_id'   => $grade_id,
                                 'section'          => $section,
                                 'school_name'      => $school,
                                 'spr_adviser'      => $adviser,
                                 'school_year'      => $school_year
                                );

                                $spr_id = $this->reports_model_f137->saveSPR($spr, $school_year);
                                //print_r($spr);
                            endif;    
                        else:
                            $spr_id = $exist;
                        endif;
                    endif;    

    //                        echo '<br />';
    //                        echo $st_id.' - '.$firstname.' '.$middlename.' '.$lastname.'<br />';
    //                          echo $numberOfColumns;
                    if($uploadOption=='0'):
                        // Academic Grades
                        for($col=5; $col<=($numberOfColumns-2); $col++):
                            if($objWorksheet->getCellByColumnAndRow($col,3)->getValue()!=NULL):
                                $subject_id = $this->getSubjectId($objWorksheet->getCellByColumnAndRow($col,3)->getValue());
                                $firstQG = $objWorksheet->getCellByColumnAndRow($col,$st)->getValue();
                                $secondQG = $objWorksheet->getCellByColumnAndRow($col+1,$st)->getValue();
                                $thirdQG = $objWorksheet->getCellByColumnAndRow($col+2,$st)->getValue();
                                $fourthQG = $objWorksheet->getCellByColumnAndRow($col+3,$st)->getValue();
                                $aveGrade = $objWorksheet->getCellByColumnAndRow($col+4,$st)->getValue();

                                $ar = array(
                                    'spr_id'        => $spr_id,
                                    'subject_id'    => $subject_id,
                                    'first'         => ($firstQG==NULL?0:$firstQG),
                                    'second'        => ($secondQG==NULL?0:$secondQG),
                                    'third'         => ($thirdQG==NULL?0:$thirdQG),
                                    'fourth'        => ($fourthQG==NULL?0:$fourthQG),
                                    'avg'           => ($aveGrade==NULL?0:$aveGrade)
                                );

                                $this->reports_model_f137->saveAR($ar, $spr_id, $subject_id, $school_year);
                                $n++;
    //                                echo $col.' | ';
    //                                echo $subject_id.' | '.$firstQG.' | '.$secondQG.' | '.$thirdQG.' | '.$fourthQG.' | '.$aveGrade.'<br />';
                            endif;
                        endfor;
                        $spr = array(
                            'gen_ave' => $objWorksheet->getCellByColumnAndRow($numberOfColumns-1,$st)->getValue()
                        );
                        
                        $this->reports_model_f137->updateBasicSPR($spr_id, $spr, $school_year);
                    else:
                        if($st_id==0):
                            $spr_id = $this->reports_model_f137->getPersonalInfoByName(trim($lastname), trim($firstname));
                            // echo $spr_id;
                        endif;

                        for($col=2; $col<=($numberOfColumns-2); $col++):
                            if($objWorksheet->getCellByColumnAndRow($col,3)->getValue()!=NULL):
                                $monthName = $objWorksheet->getCellByColumnAndRow($col,3)->getValue();
                                $presentDays = $objWorksheet->getCellByColumnAndRow($col,$st)->getValue();
                                $tardy = $objWorksheet->getCellByColumnAndRow($col+1,$st)->getValue();

                                if($presentDays!=NULL):
                                    $attDetails = array(
                                        'spr_id'    => $spr_id,
                                        $monthName  => $presentDays
                                    );

                                    $this->reports_model_f137->saveAttendanceDetails($attDetails, $spr_id);
                                endif;

                                if($tardy!=NULL):
                                    $tardyDetails = array(
                                        'spr_id'    => $spr_id,
                                        $monthName  => $tardy
                                    );

                                    $this->reports_model_f137->saveTardyDetails($tardyDetails, $spr_id);
                                endif;

    //                                echo $monthName.' | '.$presentDays.' | '.$tardy.' | ';
                            endif;
                        endfor;    
                            echo '<br /><br />';
                    endif;  
                endif;
            endfor;
            ?>
                <script type="text/javascript">
                    alert('Successfully Uploaded <?php echo $n ?> records');
                    document.location = '<?php echo base_url('reports/generateForm137') ?>'
                </script>
            <?php

        }
    }
    
    function exportStudentListToExcell($value, $year=NULL)
    {
        $this->load->library('eskwela');
        $this->load->library('excel');
        $this->load->helper('download');
        $settings = $this->eskwela->getSet();
        
        $year = ($year==NULL?$this->session->school_year:$year);
        
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $section = Modules::run('registrar/getSectionById', $value);
        
        $this->excel->getActiveSheet()->setTitle($section->level.'-'.$section->section);
        
        $this->excel->getActiveSheet()->setCellValue('A1', "[REPLACE GRADE LEVEL HERE] ex. GRADE 5");
        $this->excel->getActiveSheet()->setCellValue('C1', "[REPLACE SCHOOL YEAR] ex. for 2017-2018 just put 2017");
        $this->excel->getActiveSheet()->setCellValue('D1', "[REPLACE SCHOOL NAME] ex. PRECIOUS INTERNATIONAL SCHOOL OF DAVAO");
        
        $this->excel->getActiveSheet()->setCellValue('A2', "[Teacher's Name] ");
        
        $this->excel->getActiveSheet()->setCellValue('A4', 'ID Number');
        $this->excel->getActiveSheet()->setCellValue('B4', 'STUDENT NAME');
        $this->excel->getActiveSheet()->setCellValue('C4', 'SECTION');
        
        
        $this->excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $this->excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        
        $students = $this->reports_model_f137->getPreviousStudent($value, $year);
        //$students = $this->get_registrar_model->getAllCollegeStudents(600, 0, $level_id, NULL, $sy);
        $column = 4;
        foreach ($students as $s):
            $column++;
            $this->excel->getActiveSheet()->setCellValue('A'.$column, $s->stid);
            $this->excel->getActiveSheet()->getStyle('A'.$column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->setCellValue('B'.$column, strtoupper($s->lastname.', '.$s->firstname.' '.substr($s->middlename, 0,1).'. '));
        endforeach;
        
        $filename=$settings->short_name.'_'.$year.'_'.$s->level.'_'.$s->section.'.xls'; //save our workbook as this file name
       // $filename=$settings->short_name.'_'.$sy.'_'.$s->course_id.'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');
    }
    
    function generateF137($st_id, $status, $year = NULL)
    {
        if($status==0):
            $student = $this->reports_model_f137->getSingleStudent(base64_decode($st_id), $this->session->school_year);
            $data['m'] = $this->reports_model_f137->getMother($student->user_id); 
            $data['f'] = $this->reports_model_f137->getFather($student->user_id);
        else:
            $student = $this->reports_model_f137->getStudentInfo(base64_decode($st_id),$year);
            
        endif;
        $data['edHistory'] = $this->reports_model->getEdHistory(base64_decode($st_id));
        $data['student'] = $student;
        $data['sy'] = $year;
        if($student->grade_level_id<=7):
           $this->load->view('form137/formElementary', $data);
        else:    
           $this->load->view('form137/formJuniorHigh', $data);
        endif;
        
    }
    
    public function checkAcad($grade_id, $st_id)
    {
        $acad = $this->reports_model_f137->checkAcad($grade_id, base64_decode($st_id));
        
        $details = array(
            'school_year'   => $acad->school_year,
            'school_name'   => $acad->school_name,
            'spr_id'        => $acad->spr_id
        );
        
        echo json_encode($details);
        
    }
    
    public function saveAcademicRecords($user_id = NULL)
    {
        $school_year = $this->post('school_year');
        $school = $this->post('school');
        $first = $this->post('first');
        $second = $this->post('second');
        $third = $this->post('third');
        $fourth = $this->post('fourth');
        $average = $this->post('average');
        $generalAverage = $this->post('generalAverage');
        $subject_id = $this->post('subject_id');
        $grade_id = $this->post('grade_id');
        $spr_id = $this->post('spr_id');
        
        if($spr_id==""):
            
            $spr = array(
                'st_id'             => base64_decode($user_id),
                'grade_level_id'    => $grade_id,
                'school_name'       => $school,
                'school_year'       => $school_year
            );
        
            $spr_id = $this->reports_model->saveSPR($spr);
           
            $ar = array(
                'spr_id'        => $spr_id,
                'subject_id'    => $subject_id,
                'first'         => $first,
                'second'        => $second,
                'third'         => $third,
                'fourth'        => $fourth,
                'avg'           => $average
            );
        
            $this->reports_model->saveAR($ar);
        else:
            $spr = array(
                'gen_ave'           => $average
            );
            $ar = array(
                'spr_id'        => $spr_id,
                'subject_id'    => $subject_id,
                'first'         => $first,
                'second'        => $second,
                'third'         => $third,
                'fourth'        => $fourth,
                'avg'           => $average
            );
        
            $this->reports_model->saveAR($ar, $spr_id, $subject_id);
            
            if($generalAverage!=''):
                $this->reports_model->updateBasicSPR($spr_id, $spr);
            endif;
            
            
        endif;
        
        $data['acadRecords'] = $this->getAcadRecords(base64_decode($user_id), $school_year,  $grade_id = NULL);
        
        $this->load->view('form137/academicRecordsModal', $data);
        
        
    }
    
    function showAcadRecordsModal($user_id, $grade_id = NULL,$school_year = NULL)
    {
        $ar['acadRecords'] = $this->reports_model_f137->getAcadRecords(base64_decode($user_id), $school_year, $grade_id);
       
        $this->load->view('form137/academicRecordsModal', $ar);
    }
    
    function showAcadRecords($user_id, $grade_id = NULL,$school_year = NULL)
    {
        $ar['acadRecords'] = $this->reports_model_f137->getAcadRecords(base64_decode($user_id), $school_year, $grade_id);
       
        $this->load->view('form137/academicRecords', $ar);
    }
    
    function getAcadRecords($user_id, $school_year, $grade_level = NULL)
    {
        $acadRecords = $this->reports_model->getAcadRecords($user_id, $school_year, $grade_level);
        return $acadRecords;
    }
    
    function getAcademicRecords($user_id, $school_year, $grade_level = NULL) {
        $acadRecords = $this->reports_model_f137->getAcadRecords($user_id, $school_year, $grade_level);
        return $acadRecords;
    }
    
    function getPersonalInfo($st_id, $status, $year=NULL)
    {
        if($status==0):
            $student = $this->reports_model_f137->getSingleStudent(base64_decode($st_id), $this->session->school_year);
            $data['m'] = $this->reports_model_f137->getMother($student->user_id); 
            $data['f'] = $this->reports_model_f137->getFather($student->user_id);
        else:
            $student = $this->reports_model_f137->getStudentInfo(base64_decode($st_id),$year);
        endif;
         
        $data['grade'] = Modules::run('registrar/getGradeLevel');
        $data['subjects'] = Modules::run('academic/getSubjects');
        $data['student'] = $student;
        $data['status'] = $status;
        $data['dataSY'] = $year;
        $data['modules'] = "reports";
        $data['main_content'] = 'form137/personalInfo';
        echo Modules::run('templates/main_content', $data);
    }
    
    function savePersonalInfo($array, $lastname, $firstname, $middlename=NULL, $school_year = NULL)
    {
        $profile_id = $this->reports_model_f137->saveNewInfo($array, $firstname, $lastname, $middlename,$school_year);
        
        if(!$profile_id):
            return FALSE;
        else:
            return TRUE;
        endif;
    }
    
    function saveNewInfo()
    {
        $lastname = $this->post('inputLastName');
        $firstname = $this->post('inputFirstName');
        $middlename = $this->post('inputMiddleName');
        
        $newDetails = array(
            'sprp_st_id'        => $this->post('inputIdNum'),
            'sprp_lrn'          => $this->post('inputLRN'),
            'sprp_lastname'     => $lastname,
            'sprp_firstname'    => $firstname,
            'sprp_middlename'   => $middlename,
            'sprp_father'       => $this->post('nameOfFather'),
            'sprp_father_occ'   => $this->post('fatherOcc'),
            'sprp_mother'       => $this->post('nameOfMother'),
            'sprp_mother_occ'   => $this->post('motherOcc'),
            'sprp_bdate'        => $this->post('sprBdate'),
            'sprp_bplace'       => $this->post('inputPlaceOfBirth'),
            'sprp_nationality'  => $this->post('inputNationality')
        );
        
        $profile_id = $this->reports_model_f137->saveNewInfo($newDetails, $firstname, $lastname, $middlename = NULL);
        
        if(!$profile_id):
            echo 'This student is already registered in the system';
        else:
            $barangay_id = $this->reports_model_f137->setBarangay($this->post('inputBarangay'));
            
            $add = array(
                'address_id'            => $profile_id,
                'street'                => $this->post('inputStreet'),
                'barangay_id'           => $barangay_id,
                'city_id'               => $this->post('inputMunCity'),
                'province_id'           => $this->post('inputPID'),
                'country'               => $this->post('country'),
                'zip_code'              => $this->post('inputPostal'),
            );
            
            $this->reports_model_f137->setAddress($add, $profile_id);
            
            echo 'Successfully Saved';
        endif;
    }
    
    public function getNewInfo()
    {
        
        $data['cities'] = Modules::run('main/getCities');
        $data['provinces'] = Modules::run('main/getProvinces');
        $data['religion'] = Modules::run('main/getReligion');
        $data['grade'] = Modules::run('registrar/getGradeLevel');
        $data['modules'] = "reports";
        $data['main_content'] = 'form137/newInfo';
        echo Modules::run('templates/main_content', $data);
    }
    
    
    function searchStudent($value, $year=NULL)
    {
        $student = json_decode($this->reports_model_f137->searchStudent($value, $year));
        echo '<ul>';
        if($student->result):
            foreach ($student->result as $s):
            ?>
                    <li style="font-size:18px;" onclick="$('#searchName').hide(), $('#searchBox').val('<?php echo $s->firstname.' '.$s->lastname ?>'), loadStudentDetails('<?php echo base64_encode($s->st_id) ?>', <?php echo $student->status ?>,'<?php echo $year ?>')" ><?php echo strtoupper($s->lastname.', '.$s->firstname) ?></li>   
            <?php        
            endforeach;
            echo '</ul>';
        endif;    
        
    }
    
    public function editBasicInfo() {
        $sy = $this->input->post('sy');
        $user_id = $this->input->post('user_id');
        $rowid = $this->input->post('rowid');
        $pos = $this->input->post('pos');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $middlename = $this->input->post('middlename');
        $nameExt = $this->input->post('nameExt');

        $details = array(
            'sprp_firstname' => $firstname,
            'sprp_middlename' => $middlename,
            'sprp_lastname' => $lastname,
            'sprp_extname' => $nameExt
        );

        $this->reports_model_f137->updateBasicInfo($details, $user_id, $sy);
    }
    
    function editInfo(){
        $newVal = $this->input->post('newVal');
        $owner = $this->input->post('owner');
        $sy = $this->input->post('sy');
        $field = $this->input->post('field');
        $tbl_name = $this->input->post('tbl_name');
        $stid = $this->input->post('stid');
        //print_r($bDate . ' ' . $owner . ' ' . $sy);
        $this->reports_model_f137->editInfo($newVal, base64_decode($owner), $sy, $tbl_name, $field, $stid);
        
    }
    
    function getSPRrec($stID, $year) {
        return $this->reports_model_f137->getSPRrec($stID, $year);
    }

}