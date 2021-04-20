<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of calendar
 *
 * @author genesis
 */
class calendar extends MX_Controller {
    //put your code here
    public function __construct() {
        parent::__construct();
        
        //$this->form_validation->CI =& $this;
        $this->load->model('calendar_model');
        //$this->load->library('pagination');
    }
    
    public function saveCalendar($details, $cal_id=NULL)
    {
        $this->calendar_model->saveBdate($details, $cal_id);
        return;
    }
    
    public function saveDate($date)
    {
        $details = array('cal_date' => $date);
        $id = $this->calendar_model->saveBDate($details);
        return $id;
    }
    
    public function showCal($year= NULL, $month=NULL)
    {
        $data['event_category'] = $this->calendar_model->getEventCategory();
        $data['main_content'] = 'fullCalendar';
        $data['modules'] = 'calendar';
        if($this->session->userdata('dept_id')==10):
            echo Modules::run('templates/college_content', $data);
        else:
            echo Modules::run('templates/main_content', $data);    
        endif;
    }
    
    public function getAllEvents($year = NULL, $month=NULL, $cat=NULL)
    {
        $event = $this->calendar_model->getAllEvents($year, $month, $cat);
        return $event;
    }
    
    public function showCals($year=NULL, $month=NULL)
    {
        $data['showCal'] = $this->calendar_model->generate_calendar($year, $month);
        $data['event_category'] = $this->calendar_model->getEventCategory();
        $data['main_content'] = 'default';
        $data['modules'] = 'calendar';
        echo Modules::run('templates/main_content', $data);	

    }
    
    public function getEvent($year=NULL, $month=NULL, $day =NULL, $st_id=NULL )
    {
        $data['event'] = $this->calendar_model->getEvent($year, $month, $day, $st_id);
        $event = $this->calendar_model->getEvent($year, $month, $day, $st_id);
        //print_r($event);
        
        $this->load->view('events', $data);
    }
    
    public function getSpecificEvent($year=NULL, $month=NULL, $day =NULL, $st_id=NULL )
    {
        $event = $this->calendar_model->getSpecificEvent($year, $month, $day, $st_id);
        if(!$event):
            echo '<h6 style="text-align:center;">No Event for This Day</h6>';
            
        else:
             $data['event'] = $this->calendar_model->getSpecificEvent($year, $month, $day, $st_id);
            //print_r($event);

            $this->load->view('events', $data); 
        endif;
    }
    
    function dateExist($date)
    {
        $exist = $this->calendar_model->checkIfDateExist($date);
        if($exist):
            return TRUE;
        else:
            return FALSE;
        endif;
    }


    public function getPersonalEvent($st_id)
    {
        $event = $this->calendar_model->getPersonalEvent($st_id);
        if(!$event):
        
        else:
           $data['event'] = $this->calendar_model->getPersonalEvent($st_id);
            //print_r($event);

            $this->load->view('events', $data); 
        endif;
        
    }
    
    public function getCalWidget($year=NULL, $month=NULL)
    {
        $showCal = $this->calendar_model->generate_calWidget($year, $month);
        return $showCal;	

    }
   
    public function addEvent()
    {
        $dateFrom = $this->input->post('fromDate');
        $dateTo = $this->input->post('toDate');
                $event =$this->input->post('event');
                $evFrom = $this->input->post('ev_from');
                $evTo=$this->input->post('ev_to');
                $category= $this->input->post('category');
                $person_involved = $this->input->post('person_involved');
        
        $days = ceil(abs(strtotime($dateTo) - strtotime($dateFrom)) / 86400);
        for($d=0; $d<$days; $d++):
           $m = date('Y-m', strtotime($dateFrom));
           $day = date('d', strtotime($dateFrom));
           $day = $day + $d;
           $date = $m.'-'.$day;
            $array = array(
                'event_date'      => $date,
                'event'      => $event,
                'time_start'      => $evFrom,
                'id'            =>  $this->eskwela->codeCheck('calendar_events', 'id', $this->eskwela->code()),
                'time_end'      => $evTo,
                'category_id'      => $category,
                'person_involved'      => $person_involved
            );
        
            $this->calendar_model->saveEvent($array);

         //  unset($date);
        endfor;
        

    }
    
    public function saveEvent($date, $event, $evFrom, $evTo, $category, $person_involved)
    {
            $array = array(
                'event_date'      => $date,
                'event'      => $event,
                'time_start'      => $evFrom,
                'time_end'      => $evTo,
                'category_id'      => $category,
                'person_involved'      => $person_involved
            );
        
            $this->calendar_model->saveEvent($array);
    }
    
    public function deleteEvent($id)
    {
        $this->calendar_model->deleteEvent($id);
        echo 'Event Successfully Deleted';
    }
    
    public function getHolidays($month)
    {
        $holiday = $this->calendar_model->getHolidays($month);
        //print_r($holidays);
        //print_r(array($holidays));
        return $holiday;
    }
    
    public function holidayExist($month, $year=NULL, $sy=NULL)
    {
        $holiday = $this->calendar_model->holidayExist($month, $year, $sy);
        //echo $sd - $holiday->num_rows;
        //print_r($holiday->result());
        return $holiday;
    }
    
    public function editBdate()
    {
        $date = $this->input->post('bDate');
        $owner = $this->input->post('owner');
        $sy = $this->input->post('sy');

        $array = array(
                'temp_bdate' => $date
            );
        $this->calendar_model->saveBDate($array,$owner,$sy);
        
        /*$ifExist = $this->calendar_model->checkDateExist($date);
        
        if($ifExist->num_rows()>0):
            Modules::run('users/updateBasicInfo', $owner, 'user_id', 'bdate_id', $ifExist->row()->cal_id, 'esk_profile');
            Modules::run('web_sync/updateSyncController', 'calendar', 'cal_id', $ifExist->row()->cal_id, 'create', 5);
        else:
            $array = array(
                'cal_date' => $date
            );

            $id = $this->calendar_model->saveBDate($array);
            Modules::run('users/updateBasicInfo', $owner, 'user_id', 'bdate_id', $id, 'esk_profile');
        endif;*/
        
        Modules::run('web_sync/updateSyncController', 'profile', 'user_id', $owner, 'update', 5);

    }
    
    public function editEndate()
    {
        $date = $this->input->post('enDate');
        $owner = $this->input->post('owner');
        
        $ifExist = $this->calendar_model->checkDateExist($date);
        
        if($ifExist->num_rows()>0):
            Modules::run('users/updateBasicInfo', $owner, 'user_id', 'date_admitted', $ifExist->row()->cal_id, 'esk_profile_students_admission');
        else:
            $array = array(
                'cal_date' => $date
            );

            $id = $this->calendar_model->saveBDate($array);
            Modules::run('users/updateBasicInfo', $owner, 'user_id', 'date_admitted', $id, 'esk_profile_students_admission');
        endif;
        
        

    }
}
