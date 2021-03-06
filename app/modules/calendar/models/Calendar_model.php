 <?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Calendar_model
 *
 * @author genesis
 */
class calendar_model extends CI_Model {
    //put your code here
    
    function editBdate($details, $cal_id)
    {
        $this->db->where('cal_id', $cal_id);
        $this->db->update('calendar', $details);
        return;
    }
    
    function getHolidays($month)
    {
        $this->db->select('*');
        $this->db->from('calendar_events');
        $this->db->where('category_id', 4);
        $this->db->like("event_date", $month, 'after');
        $query = $this->db->get();
        return $query->result();
    }
    function holidayExist($month, $year, $sy)
    {
        if($year==NULL):
            $year = date('Y');
        endif;
        if ($month != '') {
            $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
            $from = $year.'-'.$month.'-'.'01'  ;
            $to = $year.'-' . $month .'-'.$num_of_days ;
           // $to = $month . '/' . $num_of_days . '/' . '2013';
        }
        if($sy==NULL):
            $sy = $this->session->userdata('school_year');
        endif;
        $this->db = $this->eskwela->db($sy);
        $this->db->select('*');
        $this->db->from('calendar_events');
        $this->db->where('category_id', 4);
        $this->db->where("event_date between '" . $from . "' and'" . $to . "'");
        $query = $this->db->get();
        return $query;
        
    }
    
    function generate_calendar($year, $month)
    {
        $cal_pref = array(
            'show_next_prev' => TRUE,
            'next_prev_url'  => base_url().'calendar/showCal/',
            'day_type'      => 'long'
        );
        
        $cal_pref['template'] = $this->bigCalTemplate();
        
        $events = $this->getEvents($year, $month);
        
        $this->load->library('calendar', $cal_pref );
        
        return $this->calendar->generate($year, $month, $events);
    }
    
    public function getAllEvents($year, $month, $cat)
    {
        $events = array();
        $this->db->select('*');
        $this->db->from('calendar_events');
        $this->db->like('event_date',"$year-$month");
        if($cat!=NULL):
            $this->db->where('category_id', $cat);
        endif;
        $query = $this->db->get();
        $query = $query->result();
        
        
        return $query;
    }
    
    public function getEvents($year, $month)
    {
        $events = array();
        $this->db->select('*');
        $this->db->from('calendar_events');
        $this->db->like('event_date',"$year-$month");
        $query = $this->db->get();
        $query = $query->result();
        foreach ($query as $row){
             $day = (int) substr($row->event_date, 8, 2);
             $events[$day] = $row->event; 
        }
        
        return $events;
    }
    
    public function getPersonalEvent($st_id)
    {
        $this->db->select('*');
        $this->db->from('calendar_events');
        $this->db->where('event_date >=', date('Y').'-'.date('m'.'-'.date('d')));
        $this->db->where('person_involved', $st_id);
        $this->db->order_by('event_date', 'ASC');
        $this->db->order_by('time_start', 'ASC');
        $query = $this->db->get();
        if($query->num_rows()>0):
          return $query->result();  
        else:
            return FALSE;
        endif;
        
    }
    
    public function getEvent($year, $month, $day, $st_id)
    {
        $this->db->select('*');
        $this->db->from('calendar_events');
        if($month>=date('m') || $day>=date('d')):
            if($day==NULL):
                $this->db->like('event_date', "$year-$month"); 
                 else:
                $this->db->like('event_date', "$year-$month-$day");                 
             endif;
        endif;
        $involved = array('All', $st_id);
        $this->db->where('event_date >=', date('Y').'-'.date('m'.'-'.date('d')));
        $this->db->where_in('person_involved', $involved);
        $this->db->order_by('event_date', 'ASC');
        $this->db->order_by('time_start', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getSpecificEvent($year, $month, $day, $st_id)
    {
        $involved = array('All', $st_id);
        $this->db->select('*');
        $this->db->from('calendar_events');
        $this->db->where('event_date', "$year-$month-$day"); 
        $this->db->where_in('person_involved', $involved); 
        $this->db->order_by('event_date', 'ASC');
        $this->db->order_by('time_start', 'ASC');
        $query = $this->db->get();
        if($query->num_rows()>0):
            return $query->result();
            else:
            return FALSE;
        endif;
        
    }

    public function getEventCategory()
    {
        $query = $this->db->get('calendar_events_category');
        return $query->result();
    }

    public function saveEvent($details)
    {
        $this->db->insert('calendar_events', $details);
        return TRUE;
    }
    
    public function deleteEvent($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('calendar_events');
        return;
    }

    public function checkIfDateExist($date)
    {
        $this->db->where('event_date', $date);
        $query = $this->db->get('calendar_events');
        if($query->num_rows()>0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    public function checkDateExist($date)
    {
        $this->db->where('cal_date', $date);
        $query = $this->db->get('calendar');
        return $query;
    }
    
    public function saveDate($details)
    {
        $this->db->insert('calendar_events', $details);
        return $this->db->insert_id();
    }
    
    public function saveBDate($details, $cal_id, $school_year = NULL)
    {
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $this->db->where('user_id',$cal_id);
        $this->db->update('profile',$details);
        /*if($cal_id==NULL):
            $this->db->insert('calendar', $details);
            return $this->db->insert_id();
        else:
            $this->db->where('cal_id', $cal_id);
            if($this->db->update('calendar', $details)):
                return $cal_id;
            endif;
        endif;*/
    }
    
    public function bigCalTemplate()
    {
        $year = $this->uri->segment(3);
       
        $temp = '

        {table_open}<table border="0" class="calendar table table-striped table-bordered " cellpadding="0" cellspacing="0">{/table_open}

        {heading_row_start}<tr>{/heading_row_start}

        {heading_previous_cell}<th><a class="prev_next" href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="month_title text-center" colspan="{colspan}">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a class="prev_next" href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

        {heading_row_end}</tr>{/heading_row_end}

        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td style="vertical-align:middle">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}

        {cal_row_start}<tr>{/cal_row_start}
        {cal_cell_start}<td style="vertical-align:middle" class="addEvent">{/cal_cell_start}

        {cal_cell_content}
            <div class="event_parent">
                <a class="has_event" onclick="getEvents({day})" href="#">{day}</a>
            </div>    
        {/cal_cell_content}
        {cal_cell_content_today}<a class="highlight" onclick="getEvents({day})" href="#">{day}</a>{/cal_cell_content_today}
        {cal_cell_no_content}<a class="no_event" onclick="getEvents({day})" href="#">{day}</a>{/cal_cell_no_content}
        {cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}

        {cal_cell_blank}&nbsp;{/cal_cell_blank}

        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}

        {table_close}</table>{/table_close}
     ';
        return $temp;
    }
    
    public function generate_calWidget($year, $month)
    {
        $cal_pref = array(
            'show_next_prev' => TRUE,
            'next_prev_url'  => base_url().'calendar/showCal/',
            'day_type'      => 'short'
        );
        
        $cal_pref['template'] = $this->calWidget();
        
        $events = $this->getEvents($year, $month);
        
        $this->load->library('calendar', $cal_pref );
        
        return $this->calendar->generate($year, $month, $events);
    }
    public function calWidget()
    {
        $temp = '

        {table_open}<table style="padding:0;" border="0" class="smallCalendar table table-striped table-bordered text-center" cellpadding="0" cellspacing="0">{/table_open}

        {heading_row_start}<tr>{/heading_row_start}

        {heading_previous_cell}<th><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
        {heading_title_cell}<th class="month_title text-center" colspan="{colspan}">{heading}</th>{/heading_title_cell}
        {heading_next_cell}<th><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

        {heading_row_end}</tr>{/heading_row_end}

        {week_row_start}<tr>{/week_row_start}
        {week_day_cell}<td style="width:14.2%">{week_day}</td>{/week_day_cell}
        {week_row_end}</tr>{/week_row_end}

        {cal_row_start}<tr>{/cal_row_start}
        {cal_cell_start}<td style="width:14.2%">{/cal_cell_start}

        {cal_cell_content}<a class="has_event" style="background:#5CB85C; padding:10px; color:white" href="../calendar/showCal">{day}</a>{/cal_cell_content}
        {cal_cell_content_today}<div class="highlight"><a href="calendar/showCal">{day}</a></div>{/cal_cell_content_today}

        {cal_cell_no_content}<span>{day}</span>{/cal_cell_no_content}
        {cal_cell_no_content_today}<a href="calendar/showCal" style="background:#1CD5FF; padding:10px; color:white">{day}</a>{/cal_cell_no_content_today}

        {cal_cell_blank}&nbsp;{/cal_cell_blank}

        {cal_cell_end}</td>{/cal_cell_end}
        {cal_row_end}</tr>{/cal_row_end}

        {table_close}</table>{/table_close}
     ';
        return $temp;
    }
}

