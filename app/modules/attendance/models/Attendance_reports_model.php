<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attendance_model
 *
 * @author genesis
 */
class attendance_reports_model extends CI_Model {
    //put your code here
    
    function updateTimeAttendace($newTime, $tid) {
        $this->db->where('att_id', $tid);
        $this->db->update('attendance_sheet', $newTime);
    }

    function updateSPR($details, $sprid, $tbleName) {
        $this->db->where('spr_id', $sprid);
        $this->db->update($tbleName, $details);
    }
    
    function getGradeLevel($school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $query = $this->db->get('grade_level');
        return $query->result();
    }
    
    function getAllSection($school_year)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->select('*');
        $this->db->from('section');
        $this->db->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $query = $this->db->get();
        return $query;
    }
    
    function getStudents($section_id)
    {
        $this->db->where('gs_spr.grade_level_id', $section_id);
        $this->db->where('school_year', $this->session->school_year);
        $this->db->join('profile_students', 'profile_students.st_id = gs_spr.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id','left');
        $query = $this->db->get('gs_spr');
        return $query;
    }
    
    function getStudentsByLevel($section_id) {
        $this->db->where('gs_spr.grade_level_id', $section_id);
        $this->db->where('school_year' , $this->session->school_year);
        $this->db->join('profile_students', 'profile_students.st_id = gs_spr.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('gs_spr_attendance', 'gs_spr_attendance.spr_id = gs_spr.spr_id', 'left');
        $query = $this->db->get('gs_spr');
        return $query;
    }
    
    function getStudentsTardy($section_id) {
        $this->db->where('gs_spr.grade_level_id', $section_id);
        $this->db->where('school_year', $this->session->school_year);
        $this->db->join('profile_students', 'profile_students.st_id = gs_spr.st_id', 'left');
        $this->db->join('profile', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('gs_spr_attendance_tardy', 'gs_spr_attendance_tardy.spr_id = gs_spr.spr_id', 'left');
        $query = $this->db->get('gs_spr');
        return $query;
    }
    
    function getTardy($st_id, $month = NULL, $school_year = NULL){
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $this->db->where('st_id', $st_id);
        $id = $this->db->get('gs_spr')->row();

        $this->db->where('spr_id', $id->spr_id);
        $spr_id = $this->db->get('gs_spr_attendance_tardy')->row();
        if ($spr_id):
            return $spr_id;
        else:
            $tardy = array(
                'spr_tardy_id' => $this->eskwela->codeCheck('gs_spr_attendance_tardy', 'spr_tardy_id', $this->eskwela->code()),
                'spr_id' => $id->spr_id
            );
            $this->db->insert('gs_spr_attendance_tardy', $tardy);

            $this->db->where('spr_id', $id->spr_id);
            return $this->db->get('gs_spr_attendance_tardy')->row();
        endif;
        /*if($year==Null):
            $year = date('Y');
        else:
            if($month < 7 ):
                $year = $this->session->school_year +1;
            endif;
        endif;
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
            $from = $year.'-'.$month . '-' . '01';
            $to = $year.'-'.$month . '-' . $num_of_days;
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('remarks_category', 'attendance_sheet.remarks = remarks_category.cat_id', 'left');
        $this->db->where('att_st_id', $st_id);
        $this->db->where('remarks', 1);
        $this->db->where("date between '" . $from . "' and'" . $to . "'");
        $query = $this->db->get();
        return $query;*/
    }
    
    function getAttendancePerStudent($st_id, $grade_level, $year)
    {
        $this->db->join('gs_spr_attendance', 'gs_spr.spr_id = gs_spr_attendance.spr_id','left');
        $this->db->where('st_id', $st_id);
        $this->db->where('school_year', $year);
        $this->db->where('grade_level_id', $grade_level);
        
        $q = $this->db->get('gs_spr');
        return $q->row();

    }
    
    function getSPRAttPerLevel()
    {
        $q= $this->db->get('gs_spr_attendance');
        return $q;
    }
    
    function saveAttSPR($spr_id, $details)
    {
        $this->db->where('spr_id', $spr_id);
        $q= $this->db->get('gs_spr_attendance');
        if($q->num_rows() > 0):
            $this->db->where('spr_id', $spr_id);
            if($this->db->update('gs_spr_attendance', $details)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            if($this->db->insert('gs_spr_attendance', $details)):
                return TRUE;
            else:
                return FALSE;
            endif;
            
        endif;    
    }
    
    function saveAttTardy($spr_id, $details) {
        $this->db->where('spr_id', $spr_id);
        $q = $this->db->get('gs_spr_attendance_tardy');
        if ($q->num_rows() > 0):
            $this->db->where('spr_id', $spr_id);
            if ($this->db->update('gs_spr_attendance_tardy', $details)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            if ($this->db->insert('gs_spr_attendance_tardy', $details)):
                return TRUE;
            else:
                return FALSE;
            endif;

        endif;
    }
    
    function getSPRDetails($level, $year)
    {
        $this->db->where('school_year', $year);
        $this->db->where('grade_level_id', $level);
        $spr = $this->db->get('gs_spr');
        return $spr;
    }
    
    function saveSPRDetails($st_id, $year, $grade_level)
    {
        $settings = Modules::run('main/getSet');
        $this->db->where('st_id', $st_id);
        $this->db->where('school_year', $year);
        $this->db->where('grade_level_id', $grade_level);
        $spr = $this->db->get('gs_spr');
        if($spr->num_rows() == 0):
            $sprDetails = array(
                'st_id' => $st_id,
                'grade_level_id' => $grade_level,
                'school_name'    => $settings->set_school_name,
                'school_year'    => $year,
            );
            
            $this->db->insert('gs_spr', $sprDetails);
        endif;    
    }
    
    function saveAttendancePerStudentPerMonth($st_id, $grade_level, $month, $att, $year)
    {
        $settings = Modules::run('main/getSet');
        $this->db->where('st_id', $st_id);
        $this->db->where('school_year', $year);
        $this->db->where('grade_level_id', $grade_level);
        $spr = $this->db->get('gs_spr');
        if($spr->num_rows() == 0):
            $sprDetails = array(
                'st_id' => $st_id,
                'grade_level_id' => $grade_level,
                'school_name'    => $settings->set_school_name,
                'school_year'    => $year,
            );
            
            $this->db->insert('gs_spr', $sprDetails);
            $spr_id = $this->db->insert_id();
            
            $sprAttDetails = array(
                'spr_id' => $spr_id,
                $month => $att
            );
            
            if($this->db->insert('gs_spr_attendance', $sprAttDetails)):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            $sprAttDetails = array(
                'spr_id' => $spr->row()->spr_id,
                $month => $att
            );
            $this->db->where('spr_id', $spr->row()->spr_id);
            if($this->db->update('gs_spr_attendance', $sprAttDetails)):
                return TRUE;
            else:
                return FALSE;
            endif;
        
        endif;
    }
            
    function removeFromLate($att_id, $details)
    {
        $this->db->where('att_id', $att_id);
        if($this->db->update('attendance_sheet', $details)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function getAttendancePerSection($section, $date=NULL)
    {
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile_students', 'attendance_sheet.att_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', ' profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->where('attendance_sheet.date', $date);
        $this->db->where('account_type', 5);
        ($section!=NULL?$this->db->where('profile_students_admission.section_id', $section):'');
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
       
        return $query;       
   }
    
    function getAttendance($gradeLevel, $date=NULL)
    {
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile_students', 'attendance_sheet.att_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', ' profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->where('attendance_sheet.date', $date);
        $this->db->where('account_type', 5);
        if($gradeLevel!=NULL):
            $this->db->where('profile_students_admission.grade_level_id', $gradeLevel);
        endif;
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
       
        return $query;       
   }
   
   function dailyTardyPerSection($date, $section, $school_year)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->userdata('school_year'):$school_year);
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile_students', 'attendance_sheet.att_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', ' profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        ($section!=NULL?$this->db->where('profile_students_admission.section_id', $section):'');
        $this->db->where('attendance_sheet.date',$date);
        $this->db->where('remarks', 1);
        $this->db->where('account_type', 5);
        $query = $this->db->get();
        return $query;
    }
   
    function dailyTardyPerLevel($date, $grade_id, $section, $school_year)
    {
        $this->db = $this->eskwela->db($school_year==NULL?$this->session->userdata('school_year'):$school_year);
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('remarks_category', 'attendance_sheet.remarks = remarks_category.cat_id', 'left'); 
        $this->db->join('profile_students', 'attendance_sheet.att_st_id = profile_students.st_id', 'left');
        $this->db->join('profile', ' profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        ($grade_id!=NULL?$this->db->where('profile_students_admission.grade_level_id', $grade_id):'');
        ($section!=NULL?$this->db->where('profile_students_admission.section_id', $section):'');
        $this->db->where('attendance_sheet.date',$date);
        $this->db->where('remarks', 1);
        $this->db->where('account_type', 5);
        $query = $this->db->get();
        return $query;
    }
    
     function getIndividualMonthlyAttendance($student_id, $month, $year, $sy) {
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
//            $from = $month . '/' . '01' . '/' . date('Y');
//            $to = $month . '/' . $num_of_days . '/' . $year;
            $from = $year.'-'.$month.'-01';
            $to = $year.'-'.$month.'-'.$num_of_days;
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        
        $monthName = date('M', strtotime($from));

        $this->update_attendance_sheet_spr($student_id, $from, $to, $sy);

        $this->db = $this->eskwela->db($sy);
        $this->db->select('*');
        $this->db->from('attendance_sheet_'.$monthName);
        $this->db->where('att_st_id', $student_id);
        $this->db->where("date between '" . $from . "' and'" . $to . "'");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function update_attendance_sheet_spr($student_id, $from, $to, $sy)
    {
        
        $monthName = date('M', strtotime($from));
        $this->db = $this->eskwela->db($sy);
        $this->db->set('counted', 1);
        $this->db->where('att_st_id', $student_id);
        $this->db->where("date between '" . $from . "' and'" . $to . "'");
        $this->db->update('attendance_sheet_'.$monthName);    
    }
    
        
}
