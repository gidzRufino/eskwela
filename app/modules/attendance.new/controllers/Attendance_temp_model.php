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
class attendance_temp_model extends CI_Model {
    //put your code here
    
     function attendanceCheck($id, $date, $year=NULL)
     {
         $this->db = $this->eskwela->db($year);
         $this->db->select('*');
         $this->db->from('attendance_sheet');
         if($date==""):
            $this->db->where('date', date("Y-m-d"));
         else:
            $this->db->where('date', $date);
         endif;
         $this->db->where('att_st_id', $id);
         $query = $this->db->get();

         return $query;
         
     }
     
     function manualAttendanceCheck($id, $date=NULL)
     {
         $this->db = $this->eskwela->db($this->session->userdata('school_year'));
         $this->db->select('*');
         $this->db->from('attendance_sheet_manual');
         if($date==NULL):
            $this->db->where('date', date("Y-m-d"));
         else:
            $this->db->where('date', $date);
         endif;
         $this->db->where('st_id', $id);
         $query = $this->db->get();

         return $query;
         
     }
     
     
    
     function getIndividualMonthlyAttendance($student_id, $month, $year=null, $sy) {
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
//            $from = $month . '/' . '01' . '/' . date('Y');
//            $to = $month . '/' . $num_of_days . '/' . $year;
            $from = $year.'-'.$month.'-01';
            $to = $year.'-'.$month.'-'.$num_of_days;
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        if($sy==NULL):
            $sy = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($sy);
        $this->db->select('*');
          $this->db->from('attendance_sheet');
          $this->db->where('att_st_id', $student_id);
        if ($month != '') {
            $this->db->where("date between '" . $from . "' and'" . $to . "'");
        }
        $this->db->order_by('date', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    
    function getAttendanceDetails($teacher,$student)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->select('profile_students.st_id as uid');
        $this->db->from('attendance_sheet');
        $this->db->join('profile', 'profile.rfid = attendance_sheet.u_rfid', 'left');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'grade_level.grade_id = section.grade_level_id', 'left');
        $this->db->where('account_type', 5);
        $this->db->where('date', date("Y-m-d"));
        if($teacher!=1){
            $this->db->where('grade_level.level', $teacher);
        }
        $this->db->where('profile.user_id', $student);
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    function getEmployeeAttendance($date)
    {
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        
            $this->db->select('*');
            $this->db->from('attendance_sheet');
            $this->db->join('profile_employee', 'attendance_sheet.att_st_id = profile_employee.employee_id', 'inner');
            $this->db->join('profile', 'profile_employee.user_id = profile.user_id', 'inner');
            $this->db->where('attendance_sheet.date', $date);
            $this->db->where('account_type !=', 5);

            $this->db->order_by('timestamp', 'DESC');
            $query = $this->db->get();
        
        
       
        return $query;       
   }
   
    function getEmployeeAttendanceManual($date)
    {
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile', 'attendance_sheet.u_rfid = profile.rfid', 'left');
        $this->db->join('profile_employee', 'profile.user_id = profile_employee.user_id', 'left');
        $this->db->where('attendance_sheet.date', $date);
        $this->db->where('account_type !=', 5);

        $this->db->order_by('timestamp', 'DESC');
        $query = $this->db->get();
       
        return $query;       
   }
   
    function getAttendanceAutoManual($section, $date=NULL)
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
        if($section!=NULL):
            $this->db->where('profile_students_admission.section_id', $section);
        endif;
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
       
        return $query;       
   }
   
    function getCollegeAttendance($date=NULL)
    {
        $sem = Modules::run('main/getSemester');
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile', 'attendance_sheet.u_rfid = profile.rfid', 'inner');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'inner');
        $this->db->join('profile_students_c_admission', ' profile_students_c_admission.user_id = profile.user_id', 'inner');
        $this->db->where('attendance_sheet.date', $date);
        $this->db->where('semester', $sem);
        $this->db->where('account_type', 5);
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
        if($query->num_rows()==0):
            if($date==NULL):
                $date = date("Y-m-d");
            endif;

            $this->db->select('*');
            $this->db->from('attendance_sheet');
            $this->db->join('profile_students', 'attendance_sheet.u_rfid = profile_students.st_id', 'left');
            $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'inner');
            $this->db->join('profile_students_c_admission', ' profile_students_c_admission.user_id = profile.user_id', 'inner');
            $this->db->where('attendance_sheet.date', $date);
            $this->db->where('semester', $sem);
            $this->db->where('account_type', 5);
            $this->db->order_by('lastname', 'ASC');
            $query = $this->db->get();
        endif;
        return $query;
        
   }
   
    function getAttendance($section, $date=NULL)
    {
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile', 'attendance_sheet.u_rfid = profile.rfid', 'left');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'inner');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->where('attendance_sheet.date', $date);
        $this->db->where('account_type', 5);
        //$this->db->where('profile_info.grade_level_id', $level);
        if($section!=NULL):
            $this->db->where('profile_students_admission.section_id', $section);
        endif;
        $this->db->order_by('lastname', 'ASC');
        $query = $this->db->get();
        if($query->num_rows()==0):
            if($date==NULL):
                $date = date("Y-m-d");
            endif;
            $this->db->select('*');
            $this->db->from('attendance_sheet');
            $this->db->join('profile_students', 'attendance_sheet.u_rfid = profile_students.st_id', 'left');
            $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
            $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'inner');
            $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
            $this->db->where('attendance_sheet.date', $date);
            $this->db->where('account_type', 5);
            //$this->db->where('profile_info.grade_level_id', $level);
            if($section!=NULL):
                $this->db->where('profile_students_admission.section_id', $section);
            endif;
            $this->db->order_by('lastname', 'ASC');
            $query = $this->db->get();
        endif;
        return $query;
        
   }
   
    function getManualAttendance($section, $date=NULL)
    {
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('attendance_sheet_manual', 'profile_students.st_id = attendance_sheet_manual.st_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'grade_level.grade_id = section.grade_level_id', 'left');
       // $this->db->join('daily_remarks', 'daily_remarks.remark_to_id = profile.user_id', 'left');
        $this->db->where('attendance_sheet_manual.date', "$date");
        $this->db->where('account_type', 5);
        //$this->db->where('profile_info.grade_level_id', $level);
        $this->db->where('profile_students_admission.section_id', $section);
        $this->db->where('profile_students.status !=', 0);
        $this->db->order_by('lastname', 'ASC');
        
        $query = $this->db->get();
       
        return $query;       
   }
   
    function getManualAttendanceWidget($section, $date)
    {

        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('attendance_sheet_manual', 'profile_students.st_id = attendance_sheet_manual.st_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'grade_level.grade_id = section.grade_level_id', 'left');
       // $this->db->join('daily_remarks', 'daily_remarks.remark_to_id = profile.user_id', 'left');
        $this->db->where('attendance_sheet_manual.date', $date);
        $this->db->where('account_type', 5);
        //$this->db->where('profile_info.grade_level_id', $level);
        if($section!=NULL):
            $this->db->where('profile_students_admission.section_id', $section);
        endif;
        $this->db->where('profile_students_admission.status !=', 0);
        $this->db->order_by('lastname', 'ASC');
        
        $query = $this->db->get();
       
        return $query;       
   }
   
   function getAbsents($section, $attend_auto, $date)
    {
       if($date==null):
           $date = date("Y-m-d");
       endif;
        
        $attendance_sheet = 'esk_attendance_sheet';
        $column_id = 'att_st_id';
        $profile = 'esk_profile_students.st_id';
        
        $year = $this->session->userdata('school_year');
        
        $this->db = $this->eskwela->db($year);
        $query = $this->db->query("Select * from esk_profile_students 
            left join esk_profile on esk_profile.user_id = esk_profile_students.user_id
            left join esk_profile_students_admission on esk_profile.user_id = esk_profile_students_admission.user_id
            where esk_profile_students_admission.section_id = $section
            and esk_profile_students_admission.school_year = $year
            and esk_profile_students_admission.status != 0
            and not exists(Select * from $attendance_sheet 
            where $profile = $attendance_sheet.$column_id
            and $attendance_sheet.date = '$date'
            ) order by sex, lastname ASC");
        return $query->result();
    }
    
    
    function saveManualAttendance($details)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->insert('attendance_sheet_manual', $details);
        return $this->db->insert_id(); 
    }
    
    function updateManualAttendance($details, $rfid, $date=NULL)
    {
        $settings = $this->eskwela->getSet();
        $this->db = $this->eskwela->db($settings->school_year);
        if($date==null):
           $date = date("Y-m-d");
        endif;
        $this->db->where('date', $date);
        $this->db->where('st_id', $rfid);
        $this->db->update('attendance_sheet_manual', $details);
        
        
    }
    
    function saveTimeAttendance($details, $rfid, $date=NULL)
    {
//        $settings = $this->eskwela->getSet();
//        $this->db = $this->eskwela->db($settings->school_year);
        if($date==null):
           $date = date("Y-m-d");
        endif;
        $this->db->where('date', $date);
        $this->db->where('u_rfid', $rfid);
        $q = $this->db->get('attendance_sheet');
        if($q->num_rows()>0):
            $this->updateTimeAttendance($details, $rfid, $date);
            return $rfid;
        else:
            $this->db->insert('attendance_sheet', $details); 
        
            return $this->db->insert_id(); 
        endif;
        
    }
    
    function updateTimeAttendance($details, $rfid, $date){
        
        $settings = $this->eskwela->getSet();
        $this->db = $this->eskwela->db($settings->school_year);
        if($date==NULL):
            $date = date("Y-m-d");
        endif;
        $this->db->where('date', $date);
        $this->db->where('att_st_id', $rfid);
        $this->db->update('attendance_sheet', $details);

        $this->db->select('att_id');
        $this->db->select('att_st_id');
        $this->db->select('u_rfid');
        $this->db->from('attendance_sheet');
        $this->db->where('date', $date);
        $this->db->where('att_st_id', $rfid);
        $query = $this->db->get();
        return $query->row();
    }
    
    function saveTimeLog($details)
    {
        $settings = $this->eskwela->getSet();
        $this->db = $this->eskwela->db($settings->school_year);
        $this->db->insert('attendance_log_book ', $details);
        return $this->db->insert_id();
        
    }
    
    function checkTimeLog($st_id)
    {
        $settings = $this->eskwela->getSet();
        $this->db = $this->eskwela->db($settings->school_year);
        $this->db->select('rfid');
        $this->db->select('time');
        $this->db->select('date');
        $this->db->select('log_id');
        $this->db->from('attendance_log_book');
        $this->db->where('rfid', $st_id);
        $this->db->where('date', date("Y-m-d"));
        $this->db->order_by('log_id', 'DESC');
        $query = $this->db->get();
        return $query;
        
    }
   
    function ifPresentManual($st_id, $day, $month, $year=Null, $sy)
    {
//        if($day<10):
//            $day = '0'.$day;
//        else:
//            $day = $day;
//        endif;
        
        if($year==NULL):
            $year = date('Y');
        endif;
        if($sy==NULL):
            $sy = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($sy);
        
        $date = $year.'-'.$month.'-'.$day;
        $this->db->where('st_id', $st_id);
        $this->db->where('date', $date);
        $query = $this->db->get('attendance_sheet_manual');
        
        if($query->num_rows()>0):
            return true;
        else:
            return FALSE;
        endif;
    }
    
    function ifPresent($st_id, $day, $month, $year=Null, $sy = NULL)
    {
        $day = abs($day);
        if($day<10):
            $day = '0'.$day;
        else:
            $day = $day;
        endif;
        
        
        if($year==NULL):
            $year = date('Y');
        endif;
        
        if($sy==NULL):
            $sy = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($sy);
        
        $date = $year.'-'.$month.'-'.$day;
        $this->db->where('u_rfid', $st_id);
        $this->db->where('date', $date);
        $query = $this->db->get('attendance_sheet');
        
        if($query->num_rows()>0):
            return true;
        else:
            $date = $year.'-'.$month.'-'.$day;
            $this->db->where('att_st_id', $st_id);
            $this->db->where('date', $date);
            $query2 = $this->db->get('attendance_sheet');
            if($query2->num_rows()>0):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }
   
    function getDailyTotalByGenderManual($date, $section, $gender)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('attendance_sheet_manual', 'profile_students.st_id = attendance_sheet_manual.st_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'grade_level.grade_id = section.grade_level_id', 'left');
        $this->db->where('attendance_sheet_manual.date', "$date");
        $this->db->where('account_type', 5);
        $this->db->where('profile_students.section_id', $section);
        $this->db->where('profile_students.status !=', 0);
        if($gender!=NULL):
            $this->db->where('profile.sex', $gender);
        endif;
            $this->db->group_by('attendance_sheet_manual.st_id');
        
        $query = $this->db->get();
       
        return $query->num_rows();       
   }
   
    function getDailyTotalByGender($date, $section, $gender)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('profile_students');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('attendance_sheet', 'profile_students.st_id = attendance_sheet.att_st_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'grade_level.grade_id = section.grade_level_id', 'left');
       // $this->db->join('daily_remarks', 'daily_remarks.remark_to_id = profile.user_id', 'left');
        $this->db->where('attendance_sheet.date', "$date");
        $this->db->where('account_type', 5);
        //$this->db->where('profile_info.grade_level_id', $level);
        $this->db->where('profile_students_admission.section_id', $section);
        $this->db->where('profile_students.status !=', 0);
        if($gender!=NULL):
            $this->db->where('profile.sex', $gender);
                $this->db->group_by('attendance_sheet.att_st_id');
        endif;
        
        $query = $this->db->get();
       
        return $query->num_rows();       
   }
   
   function getPresentByDateManual($section, $date)
   {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->join('profile_students', 'profile.user_id = profile_students.user_id', 'left');
        $this->db->join('attendance_sheet', 'profile_students.st_id = attendance_sheet.att_st_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $this->db->join('grade_level', 'grade_level.grade_id = section.grade_level_id', 'left');
       // $this->db->join('daily_remarks', 'daily_remarks.remark_to_id = profile.user_id', 'left');
        $this->db->where('attendance_sheet_manual.date', "$date");
        $this->db->where('account_type', 5);
        //$this->db->where('profile_info.grade_level_id', $level);
        $this->db->where('profile_students_admission.section_id', $section);
        $this->db->where('profile_students.status !=', 0);
        $this->db->order_by('lastname', 'ASC');
        
        $query = $this->db->get();
       
        return $query;       
   }
   
   function getPresentByDate($section, $date)
   {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('profile_students', 'profile_students.st_id = attendance_sheet.att_st_id', 'left');
        $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $this->db->join('profile_students_admission', ' profile_students_admission.user_id = profile.user_id', 'left');
        $this->db->where('attendance_sheet.date', $date);
        $this->db->where('profile_students_admission.section_id', $section);
        $this->db->where('profile_students_admission.status !=', 0);
        $this->db->where('account_type', 5);
        $this->db->order_by('lastname', 'ASC');
        
        $query = $this->db->get();
       
        return $query;       
   }
   
   function getAbsentByDate($section, $date, $attend_auto)
   {
       $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        //$date = date("Y-m-d");
        if($attend_auto){
            $attendance_sheet = 'esk_attendance_sheet';
            $column_id = 'u_rfid';
            $profile = 'esk_profile.rfid';
        }else{
            $attendance_sheet = 'esk_attendance_sheet_manual';
            $column_id = 'st_id';
            $profile = 'esk_profile_students.st_id';
        }
        
        $query = $this->db->query("Select * from esk_profile_students 
            left join esk_profile on esk_profile.user_id = esk_profile_students.user_id
            left join esk_profile_students_admission on esk_profile.user_id = esk_profile_students_admission.user_id
            where esk_profile_students_admission.section_id = $section
            and not exists(Select * from $attendance_sheet 
            where $profile = $attendance_sheet.$column_id
            and $attendance_sheet.date = '$date' and esk_profile_students_admission.status != 0
            ) order by sex, lastname ASC");
        return $query->result();
   }
   
   function getAbsentByDatePerGender($section, $date, $attend_auto, $gender)
   {
       $this->db = $this->eskwela->db($this->session->userdata('school_year'));
       if($attend_auto):
            $this->db->select('*'); 
            $this->db->from('attendance_sheet');
            $this->db->join('profile_students', 'attendance_sheet.st_id = profile_students.st_id', 'left');
            $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
            $this->db->where('section_id', $section);
            $this->db->where('attendance_sheet.date', $date);
            $this->db->where('sex', $gender);
            $this->db->group_by('attendance_sheet.st_id');
       else:
            $this->db->select('*'); 
            $this->db->from('attendance_sheet_manual');
            $this->db->join('profile_students', 'attendance_sheet_manual.st_id = profile_students.st_id', 'left');
            $this->db->join('profile', 'profile_students.user_id = profile.user_id', 'left');
            $this->db->where('section_id', $section);
            $this->db->where('attendance_sheet_manual.date', $date);
            $this->db->where('sex', $gender);
            $this->db->group_by('attendance_sheet_manual.st_id');
       endif;
       
            $this->db->where('profile_students.status !=', 0);
            $query = $this->db->get(); 
            return $query;
   }
   
   function addAttendanceRemarkManual($details, $st_id, $date){
       
       $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('date', $date);
        $this->db->where('st_id', $st_id);
        $this->db->update('attendance_sheet_manual', $details);
        return TRUE;
        
        
    }
    
   function addAttendanceRemark($details, $rfid, $date){
       
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('date', $date);
        $this->db->where('att_st_id', $rfid);
        $this->db->update('attendance_sheet', $details);
        return TRUE;
        
        
    }
    
    function getAttendanceRemarkManual($st_id, $date)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet_manual');
        $this->db->join('remarks_category', 'attendance_sheet_manual.remarks = remarks_category.cat_id', 'left');
        $this->db->where('date', $date);
        $this->db->where('st_id', $st_id);
        $query = $this->db->get();
        return $query;
    }
    
    function getAttendanceRemark($st_id, $date)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('remarks_category', 'attendance_sheet.remarks = remarks_category.cat_id', 'left');
        $this->db->where('date', $date);
        $this->db->where('att_st_id', $st_id);
        $query = $this->db->get();
        return $query;
    }
    
    function getTardyManual($st_id, $month, $year)
    {
        
        if($year==Null):
            $year = date('Y');
        endif;
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
            $from = $year.'-'.$month . '-' . '01';
            $to = $year.'-'.$month . '-' . $num_of_days;
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet_manual');
        $this->db->join('remarks_category', 'attendance_sheet_manual.remarks = remarks_category.cat_id', 'left');
        $this->db->where("date between '" . $from . "' and'" . $to . "'");
        $this->db->where('st_id', $st_id);
        $this->db->where('remarks', 1);
        $query = $this->db->get();
        return $query;
    }
    
    function getTardy($st_id, $month, $year)
    {
        if($year==Null):
            $year = date('Y');
        endif;
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
            $from = $year.'-'.$month . '-' . '01';
            $to = $year.'-'.$month . '-' . $num_of_days;
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->from('attendance_sheet');
        $this->db->join('remarks_category', 'attendance_sheet.remarks = remarks_category.cat_id', 'left');
        $this->db->where("date between '" . $from . "' and'" . $to . "'");
        $this->db->where('u_rfid', $st_id);
        $this->db->where('remarks', 1);
        $query = $this->db->get();
        return $query;
    }
    
    function deleteAttendanceManual($att_id)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('att_id', $att_id);
        $this->db->delete('attendance_sheet_manual');
        return TRUE;
    }
    
    function deleteAttendance($att_id)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('att_id', $att_id);
        $this->db->delete('attendance_sheet');
        return TRUE;
    }
    
    function saveMonthlyAttendanceSummary($details)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->insert('attendance_summary', $details);
        return;
    }
    
    function checkMonthlyAttendanceSummary($month, $section_id)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('section_id', $section_id);
        $this->db->where('month', $month);
        $query = $this->db->get('attendance_summary');
        if($query->num_rows()>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function updateMonthlyAttendanceSummary($month, $section_id, $data, $attend_auto)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('section_id', $section_id);
        $this->db->where('month', $month);
        $this->db->where('attend_auto', $attend_auto);
        $this->db->update('attendance_summary', $data);
        return;
    }
    
    function getMonthlyAttendanceSummary($month, $section_id, $attend_auto, $school_year)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->where('section_id', $section_id);
        $this->db->where('month', $month);
        $this->db->where('attend_auto', $attend_auto);
        $this->db->where('school_year', $school_year);
        $query = $this->db->get('attendance_summary');
        return $query->row();
    }
    
    function getMonthlyAttendanceSummaryPerLevel($month, $grade_id, $attend_auto)
    {
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('*');
        $this->db->select('SUM(ave_male_total) as male_total');
        $this->db->select('SUM(ave_female_total) as female_total');
        $this->db->select('SUM(percent_male) as percent_male');
        $this->db->select('SUM(percent_female) as percent_female');
        $this->db->from('attendance_summary');
        $this->db->join('section', 'attendance_summary.section_id = section.section_id', 'left');
        $this->db->where('grade_level_id', $grade_id);
        $this->db->where('month', $month);
        $this->db->where('attend_auto', $attend_auto);
        $query = $this->db->get();
        return $query->row();
    }
    
    function getMonthlyStatus($month=Null, $grade_id=Null,$code_id = NULL)
    {
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, date('Y')));
            $from = $month . '/' . '01' . '/' . date('Y');
            $to = $month . '/' . $num_of_days . '/' . date('Y');
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        //$this->db->select('deped_code_indicator.id');
        $this->db = $this->eskwela->db($this->session->userdata('school_year'));
        $this->db->select('admission_remarks.remark_date');
        $this->db->select('admission_remarks.code_indicator_id');
        $this->db->select('admission_remarks.remark_to');
        $this->db->select('esk_section.grade_level_id');
        $this->db->select('profile_students.st_id');
        $this->db->from('profile_students');
        $this->db->join('admission_remarks', 'profile_students.st_id = admission_remarks.remark_to', 'left');
        $this->db->join('esk_section', 'profile_students.st_id = admission_remarks.remark_to', 'left');
        $this->db->where('code_indicator_id', $code_id);
        $this->db->where("admission_remarks.remark_date between '" . $from . "' and'" . $to . "'");
        $this->db->where('esk_section.grade_level_id', $grade_id);
        $query = $this->db->get();
        return $query;
        
    }
}
