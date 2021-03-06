<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class hr extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        //$this->load->library('form_validation');
        //$this->form_validation->CI =& $this;
        $this->load->model('hr_model');
        $this->load->model('payroll_model');
        $this->load->library('pagination');
        $this->load->library('Pdf');
        
    }
    
    function post($name)
    {
        return $this->input->post($name);
    }
    
    function getNumberOfDaysWork($startDate, $endDate)
    {
        $days = 0;
        for($totalDays=date('d', strtotime($startDate)); $totalDays <= date('d', strtotime($endDate)); $totalDays++):
            $date = date('Y-m-d', strtotime(date('Y-m-'.$totalDays)));
            if(date('D', strtotime($date))!="Sun" && date('D', strtotime($date))!="Sat"):
                $days++;
            endif;
        endfor;
        return $days;
    }
    

    function getDepartmentAssociates($dept_id){
        return $this->hr_model->fetchDepartmentAssociates($dept_id);
    }
    
    function getPersonName($user_id){
        $tmp = $this->hr_model->fetchPersonName($user_id);
        return strtoupper($tmp->firstname." ".$tmp->middlename[0].". ".$tmp->lastname);
    }
    
    function getSalaryType()
    {
        $salary = $this->hr_model->getListOfSalary();
        return $salary;
    }
    
    public function editDeptPosition($pid, $newPosition, $action)
    {
        switch ($action):
            case 1:
                if($this->hr_model->editPositionName($pid, urldecode($newPosition))):
                    echo 'Successfully Change';
                endif;
            break;    
            case 2:
                if($this->hr_model->deletePosition($pid)):
                    echo 'Successfully Deleted';
                endif;
            break;    
        endswitch;
    }
    
    
    public function editBasicInfo() 
    {
        $rowid = $this->input->post('rowid');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $middlename = $this->input->post('middlename');

        $details = array(
            'firstname' => $firstname,
            'middlename' => $middlename,
            'lastname' => $lastname,
        );
        
        if($this->hr_model->editBasicInfo($details, $rowid)):
            echo $firstname.' '.$lastname;
        endif;
    }
    
    function generateId()
    {
        $dateHired = $this->post('value');
        $latestIdNum = $this->hr_model->getLatestId();
       
        $em_id = str_replace('-', '', $dateHired).$latestIdNum;
        
        echo $em_id;
    }
    
    
    function getEmployeeDailyAttendance($em_id, $date)
    {
        $attendance = $this->payroll_model->getEmployeeDailyAttendance($em_id, $date);
        return $attendance;
    }
    
    function getWeeklyAttendance($date=NULL,$page=null)
    {
        //echo $date;
        
        $result = $this->payroll_model->getAllEmployee('','');
        $config['base_url'] = base_url('hr/getWeeklyAttendance/'.($date!=NULL?$date:date('Y-m-d')));
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 10;
        $config['num_links'] = 5;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);
        $page = $this->payroll_model->getAllEmployee($config['per_page'], $page);
        
        $data['date'] = ($date!=NULL?$date:date('Y-m-d'));
        $data['presents'] = Modules::run('attendance/getNumberOfEmployeePresents', ($date!=NULL?$date:date('Y-m-d')), TRUE);
        $data['employees'] = $page;
        $data['links'] = $this->pagination->create_links();
        $data['main_content'] = 'weeklyAttendance';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);
    }
    
    function time_for_week_day($day_name, $ref_time=null){
        $monday = strtotime(date('o-\WW',$ref_time));
        if(substr(strtoupper($day_name),0,3) === "MON")
            return $monday;
        else
            return strtotime("next $day_name",$monday);
    }
    
    function daysOftheWeek($monday)
    {
        $details = array(
           'Monday'     => date('Y-m-d',$monday),
           'Tuesday'    => date('Y-m-d', strtotime('+1 day',$monday)),
           'Wednesday'  => date('Y-m-d', strtotime('+2 day',$monday)),
           'Thursday'   => date('Y-m-d', strtotime('+3 day',$monday)),
           'Friday'     => date('Y-m-d', strtotime('+4 day',$monday)),
           'Saturday'   => date('Y-m-d', strtotime('+5 day',$monday)),
        ) ;
        
        return $details;
    }
    
    function zkTest()
    {
        $this->load->library('zklib/zklib');
        $zk = new ZKLib("192.168.3.101", 4370);
        $ret = $zk->connect();
        sleep(1);
        if ( $ret ): 
            $zk->disableDevice();
            sleep(1);
            
            $data['zk'] = $zk;
        else:
            
        endif;
        $data['main_content'] = 'defaultZk';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);
    }

    function convertToHoursMins($time, $format = '%2dhr %02d min') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        if($hours <= 0):
            return sprintf('%02d min', $minutes);
        else:
            return sprintf($format, $hours, $minutes);
        endif;
    }
    
    
    function getDailyDTRSummary($fromDate=NULL, $toDate=NULL)
    {
        if($fromDate==NULL):
            $fromDate = date('Y-m-d');
            $toDate = date('Y-m-d');
        endif;
        
        $data['presents'] = Modules::run('attendance/getNumberOfEmployeePresents', $fromDate, TRUE);
        $data['date'] = $fromDate;
        $data['main_content'] = 'dailyDTRSummary';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);
    }
    
    function editHrTime()
    {
        $date       = date('M', strtotime($this->post('date')));
        $newDate    = $this->post('date');
        $att_id     = $this->input->post('att_id');
        $time       = date('Gi', strtotime($this->input->post('timeEdit')));
        $column     = $this->input->post('time_option');
        $newTime    = $this->post('newTime');
        $rfid       = $this->post('rfid');
        $st_id      = $this->post('st_id');
        
        if($newTime==1):
            Modules::run('attendance/saveTimeAttendance', $rfid, 1, $column, $time, $newDate, $st_id);
        else:
            if($this->hr_model->editHrTime($column, $att_id, $time, $date)):
                echo 'Success';
            else:
                echo 'Failed to Update';
            endif;
        endif;        
    }
    
    
    function saveContacts()
    {
        $user_id = $this->input->post('user_id');
        $mobile = $this->input->post('mobile_no');
        $column = $this->input->post('column');
        $sy = $this->input->post('sy');
        $contact_id = $this->hr_model->saveContacts($user_id, $mobile, $column, $sy);
        echo $contact_id;
    }
    
            
    function loadView($id)
    {
        $data['basicInfo'] = $this->hr_model->getBasicInfo($id);
        $this->load->view('mobile/teachersInfoMobile', $data);
    }
    
    function checkIfIDExist($id)
    {
        $exist = $this->hr_model->checkIfIDExist($id);
        //echo $exist;
        return $exist;
    }
    
    function deleteEducBak($id)
    {
        if($this->hr_model->deleteEducBak($id)):
            echo 'Information Succesfully Deleted';
        endif;
    }
    
    function deleteEmployee()
    {
        $user_id= $this->input->post('user_id');
        $em_id= $this->input->post('employee_id');
        
        if($this->hr_model->deleteProfile('user_id', $user_id, 'esk_profile_employee')):
            $this->hr_model->deleteProfile('user_id', $user_id, 'esk_profile');
            $this->hr_model->deleteProfile('eb_employee_id', $em_id, 'esk_profile_employee_education');
            $this->hr_model->deleteProfile('faculty_id', $em_id, 'esk_faculty_assign');
            $this->hr_model->deleteProfile('u_id', $em_id, 'esk_user_accounts');
            echo 'Successfully Deleted';
        
        else:
            echo 'An error has occured';
        endif;
        
    }
    
    function getEmployeeName($id)
    {
        $em = $this->hr_model->getEmployeeName($id);
        return $em;
    }
    
    function updateODTrans($od_trans_id, $column, $c_value)
    {
        $this->hr_model->updateODTrans($od_trans_id, $column, $c_value);
        return;
    }


    function getPayrollTrans($em_id, $start_date, $endDate)
    {
        $payroll = $this->hr_model->getPayrollTrans($em_id, date("Y-m-d", strtotime($start_date)), date("Y-m-d", strtotime($endDate))); 
        return $payroll;
    }
    function savePR_transaction()
    {
        $date_fr = date("Y-m-d", strtotime($this->input->post('startDate')));
        $date_to = date("Y-m-d", strtotime($this->input->post('endDate')));
        $details = array(
            'p_acct_id' => $this->input->post('em_id'),
            'p_sg_id'   => $this->input->post('sg_id'),
            'p_od_id'   => $this->input->post('od_id'),
            'p_date_fr' => $date_fr,
            'p_date_to' => $date_to,
            'p_approved'=> $this->input->post('approved')
        );
        
        
        
        if($this->hr_model->savePR_transaction($details)):
            $this->updateODTrans($this->input->post('od_id'), 'credit', $this->input->post('credit_amount'));
            return True;
        endif;
    }
    
    function approvedLoanApp()
    {
        $details = array(
            'approved'  => $this->input->post('approved'),
            'due_date'  => $this->input->post('dueDate'),
        );
        
        if($this->hr_model->approvedLoanApp($details, $this->input->post('user_id'), $this->input->post('od_id'))):
           if($this->input->post('approved')==0):
               echo 'Successfully Updated';
           else:
               echo 'Successfully Approved';
                $trans = array(
                    'od_deduct_id' => $this->input->post('od_id'),
                    'charge'       => $this->input->post('amount'),
                );
                $this->hr_model->saveOdTrans($trans);
               
            Modules::run('notification_system/sendNotification',1, 3, 'Admin',$this->input->post('user_id') , 'Congratulations, Your School Loan was approved.', date('Y-m-d'), base_url().'hr/settings');
           endif;
        endif;
    }
    
    function getSingleLoanApp($user_id, $od_id, $stats)
    {
        if($stats==0):
            $result = $this->hr_model->getSingleLoanApp($user_id, $od_id);
            $return = json_decode($result);
            
            if($return->status):
                $details = array(
                    'status' => FALSE,
                    'item' => $return->data->odi_items,
                    'id' => $return->data->od_id,
                    'terms' => $return->data->odp_terms,
                    'accnt_id' => $return->data->od_acnt_id,
                    'no_terms' => $return->data->no_terms,
                    'trans_date' => date("F d, Y / g:i a", strtotime($return->data->od_timestamp)),
                );
            
            echo json_encode($details);
                
            endif;
        endif;
    }
    
    
    function getPersonalLoanRequest($user_id=NULL)
    {
        $item = $this->hr_model->getPersonalLoanRequest($user_id);
        return $item;
    }
    
    function getLoanRequest($item_id=NULL,$user_id=NULL)
    {
        $item = json_decode($this->hr_model->getLoanRequest($item_id, $user_id));
        $data['loanList'] = $item->data;
        $this->load->view('payroll/loanList_table', $data);
    }
    
    function saveLoan()
    {
        $terms = $this->input->post('terms');
        $no_terms = $this->input->post('no_terms');
        $item = $this->input->post('item');
        $em_id = $this->input->post('em_id');
        
        $details = array(
            'od_item_id' => $item,
            'od_acnt_id' => $em_id,
            'od_principal_amount' => $this->input->post('amount'),
            'od_terms_id' => $terms,
            'no_terms' => $no_terms,
        );
        
        $return = $this->hr_model->saveLoan($details);
        $return = json_decode($return);
        if($return->status):
           
            echo json_encode(array('status' => TRUE, 'msg' => 'Successfully Added'));
            //Modules::run('notification_system/sendNotification',1, 3, $em_id, 'Admin', 'request for Loan Application Approval', date('Y-m-d'), base_url().'hr/settings');
        else:
            echo json_encode(array('status' => FALSE));
        endif;
    }
    
    function getOdTerms()
    {
        $od = $this->hr_model->getOdTerms();
        return $od;
    }
    
    function getOD_list()
    {
        $od = $this->hr_model->getOtherDeductions();
        return $od;
    }


    function saveDeduction()
    {
        $details = array(
            'odi_items' => $this->input->post('value'),
            'interest' => $this->input->post('interest')
        );
        
        $return = $this->hr_model->saveDeduction($details, $this->input->post('value'));
        $return = json_decode($return);
        if($return->status):
            $data = array(
                'status'    => TRUE,
                'msg'       => 'Successfully Added',
                'id'        => $return->id,
            );
        else:
            $data = array(
                'status'    => FALSE,
                'msg'       => 'Sorry, Deduction Type Already Exist'
            );
        endif;
        
        echo json_encode($data);
    }
    
    function addSG()
    {
        $details = array(
            'salary' => $this->input->post('value')
        );
        
        $return = $this->hr_model->addSG($details, $this->input->post('value'));
        $return = json_decode($return);
        if($return->status):
            $data = array(
                'status'    => TRUE,
                'msg'       => 'Successfully Added',
                'id'        => $return->id,
                'value'     => number_format($this->input->post('value'),2,'.',',')
            );
        else:
            $data = array(
                'status'    => FALSE,
                'msg'       => 'Sorry, Salary Grade Already Exist'
            );
        endif;
        
        echo json_encode($data);
        
    }
    
    function savePtype()
    {
        $em_id = $this->input->post('em_id');
        $payType = $this->input->post('payroll_type');
        
        $details = array(
            'pay_type' => $payType
        );
        
        $sg = $this->hr_model->saveSG($details, $em_id);
        return $sg;
    }
    
    function saveSG()
    {
        $em_id = $this->input->post('em_id');
        $salary = $this->input->post('salary_grade');
        
        $details = array(
            'pg_id' => $salary
        );
        
        $sg = $this->hr_model->saveSG($details, $em_id);
        return $sg;
    }
    
    function getSalaryGrade()
    {
        $salary = $this->hr_model->getListOfSalary();
        return $salary;
    }
    
    function educHis($eb_id = NULL)
    {
        if($eb_id==NULL):
            $eb_id = $this->input->post('id');
        endif;
        
        $educHis = $this->hr_model->getEducationHistory(NULL, $eb_id);
        
        $educResult = $educHis->row();
        $return = array(
            'eb_id'     => $eb_id,
            'level'     => $educResult->eb_level_id,
            'school_id'    => $educResult->eb_school_id,
            'school'    => $educResult->school_name,
            'school_add'    => $educResult->school_add,
            'course'    => $educResult->course,
            'course_id'    => $educResult->course_id,
            'year_grad'    => $educResult->eb_year_grad,
            'year_from'    => $educResult->eb_dates_from,
            'year_to'    => $educResult->eb_dates_to,
            'highest_earn'    => $educResult->eb_highest_earn,
        );
        echo json_encode($return);
    }
    
    function addEducHis()
    {
        if($this->input->post('courseId')==0)
            {
              $coursedata = array(
                  'course'  =>  $this->input->post('inputCourse'),
              );

              $course_id = $this->hr_model->saveCourse($coursedata); 
            }else{
              $course_id =  $this->input->post('courseId'); 
            }

            // saves the College Information
            if($this->input->post('collegeId')==0)
            {
              $collegedata = array(
                  'school_name'  =>  $this->input->post('inputNameOfSchool'),
                  'school_add'  => $this->input->post('inputAddressOfSchool'),
              );

              $college_id = $this->hr_model->saveCollege($collegedata); 
            }else{
              $college_id =  $this->input->post('collegeId'); 
            }

        if($this->input->post('eb_id')==0):
            //saves the Academic Info
            
            $details = array(
                'eb_employee_id'    => $this->input->post('employee_id'),
                'eb_level_id'       => $this->input->post('educLevel'),
                'eb_school_id'      => $college_id,
                'eb_course_id'      => $course_id,
                'eb_year_grad'      => $this->input->post('inputYearGraduated'),
                'eb_highest_earn'   => $this->input->post('highestEarn'),
                'eb_dates_from'     => $this->input->post('yearsFrom'),
                'eb_dates_to'       => $this->input->post('yearsTo'),
            );

            $result = $this->hr_model->addEducHis($details, $this->input->post('employee_id'));
            if($result):
                $return = array(
                    'status'    => TRUE,
                    'msg'       => 'Successfully Added'
                );
            else:
                $return = array(
                    'status'    => FALSE
                );
            endif;
        else:
            $details = array(
                'eb_employee_id'    => $this->input->post('employee_id'),
                'eb_level_id'       => $this->input->post('educLevel'),
                'eb_school_id'      => $college_id,
                'eb_course_id'      => $course_id,
                'eb_year_grad'      => $this->input->post('inputYearGraduated'),
                'eb_highest_earn'   => $this->input->post('highestEarn'),
                'eb_dates_from'     => $this->input->post('yearsFrom'),
                'eb_dates_to'       => $this->input->post('yearsTo'),
            );

            $result = $this->hr_model->editEducHis($details, $this->input->post('eb_id'));
            if($result):
                $return = array(
                    'status'    => TRUE,
                    'msg'       => 'Successfully Edited'
                );
            else:
                $return = array(
                    'status'    => FALSE
                );
            endif;
        endif;
        echo json_encode($return);
    }
    
    function saveEducation($details, $em_id)
    {
        $this->hr_model->addEducHis($details, $em_id);
        return;
    }
    
    function getEducationHistory($employee_id)
    {
        $edHis = $this->hr_model->getEducationHistory($employee_id);
        return $edHis;
    }
    
        
    function getIndividualDTR($date = NULL, $t_id = NULL)
    {
        if($date==NULL):
            $date = $this->input->post('date');
            $t_id = $this->input->post('t_id');
        endif;
        $from = explode('-', $date);
        $mFrom = $from[1];
        $dFrom = $from[2];
        $yFrom = $from[0];
        $data['records']= $this->hr_model->searchDtrbyDate($date, $yFrom.'-'.$mFrom.'-31', $t_id); 
        $data['info'] = $this->getEmployee($this->input->post('owners_id'));
        $data['hrdb'] = Modules::load('hr/hrdbprocess/');
        //$data['records'] = $this->hr_model->getDTR($this->input->post('owners_id'));
        $this->load->view('payroll/editDTR_result', $data);
    }
    
    function getUpdatedPresent($date=NULL)
    {
        if($date==NULL):
            if($day==NULL)$day = date('d');
            if($month==NULL)$month = date('m');
            if($year==NULL)$year = date('Y');
            $date = $month.'/'.$day.'/'.$year;
        endif;
        //echo $date;
        
        $data['day'] = $day;
        $data['date'] = $date;
        $data['month'] = $month;
        $data['presents'] = Modules::run('attendance/getNumberOfEmployeePresents', $date, TRUE);
        $data['employees'] = $this->getEmployees();
        $this->load->view('getPresent', $data);
    }
    
    function saveManualHrAttendance($option = NULL) {
        $t_id = $this->input->post('t_id');
        $hour = $this->input->post('hour');
        $min = $this->input->post('min');
        $ampm = $this->input->post('ampm');
        $date = $this->input->post('date');
        $inout = $this->input->post('inout');
        $uid = $this->input->post('uid');

        $exist = $this->hr_model->checkIfPresent($date, $t_id);

        if (!$exist):
            if ($ampm == 'AM'):

                $time = $hour . $min;
                if ($inout == 'in'):
                    $column = 'time_in';

                    Modules::run('attendance/saveTimeAttendance', $t_id, 1, $column, $time, $date, $uid);
                else:
                    $column = 'time_out';
                    Modules::run('attendance/updateTimeAttendance', $t_id, $column, $time, 1, $date);
                endif;
            else:
                $time = ($hour + 12) . $min;
                if ($inout == 'in'):
                    $column = 'time_in_pm';

                    Modules::run('attendance/saveTimeAttendance', $t_id, 1, $column, $time, $date, $uid);
                else:
                    $column = 'time_out_pm';
                    Modules::run('attendance/updateTimeAttendance', $t_id, $column, $time, 1, $date);
                endif;

            endif;
        else:
            if ($ampm == 'AM'):

                $time = $hour . $min;
                if ($inout == 'in'):
                    $column = 'time_in';
                    if ($exist->time_in != ""):
                        if ($exist->approved == 1):
                            Modules::run('attendance/saveTimeAttendance', $t_id, 1, $column, $time, $date);
                        else:
                            ?>
                            <script type="text/javascript">
                                alert('Sorry, Altering DTR is not Allowed, Please Request for consideration');
                            </script>
                        <?php
                        endif;
                    else:
                        Modules::run('attendance/saveTimeAttendance', $t_id, 1, $column, $time, $date);
                    endif;
                else:
                    $column = 'time_out';
                    if ($exist->$column != ""):
                        if ($exist->approved == 1):
                            Modules::run('attendance/saveTimeAttendance', $t_id, 1, $column, $time, $date);
                        else:
                            ?>
                            <script type="text/javascript">
                                alert('Sorry, Altering DTR is not Allowed, Please Request for consideration');
                            </script>
                        <?php
                        endif;
                    else:
                        Modules::run('attendance/updateTimeAttendance', $t_id, $column, $time, 1, $date);
                    endif;
                endif;
            else:
                $time = ($hour + 12) . $min;
                if ($hour == 12):
                    $time = $hour . $min;
                endif;
                if ($inout == 'in'):
                    $column = 'time_in_pm';
                    if ($exist->$column != ""):
                        if ($exist->approved == 1):
                            Modules::run('attendance/updateTimeAttendance', $t_id, $column, $time, 1, $date);
                        else:
                            ?>
                            <script type="text/javascript">
                                alert('Sorry, Altering DTR is not Allowed, Please Request for consideration');
                            </script>
                        <?php
                        endif;
                    else:
                        Modules::run('attendance/updateTimeAttendance', $t_id, $column, $time, 1, $date);
                    endif;
                else:
                    $column = 'time_out_pm';
                    if ($exist->$column != ""):
                        if ($exist->approved == 1):
                            Modules::run('attendance/saveTimeAttendance', $t_id, 1, $column, $time, $date);
                        else:
                            ?>
                            <script type="text/javascript">
                                alert('Sorry, Altering DTR is not Allowed, Please Request for consideration');
                            </script>
                        <?php
                        endif;
                    else:
                        Modules::run('attendance/updateTimeAttendance', $t_id, $column, $time, 1, $date);
                    endif;
                endif;

            endif;
        endif;


        if ($option == NULL):
            $this->getUpdatedPresent($date);
        else:
            $this->getIndividualDTR($date, $option);
        endif;
    }
    
    function saveMinMaj()
    {
        $minmaj = $this->input->post('value');
        $id = $this->input->post('id');
        $option = $this->input->post('maj_min');
        
        $maj_id = $this->hr_model->checkMinMaj($minmaj);
        
        
        $details = array(
            'eb_'.$option.'_id' => $maj_id
        );
        
        $saved = $this->hr_model->saveMinMaj($id, $details);
        
        if($saved):
            echo 'successfully updated';
        else:
            echo 'Sorry, An internal Error Occured';
        endif;
        
    }
    
    function minMajSub()
    {
        $minor = $this->hr_model->minMajSub();
        return $minor;
    }
    
    function getMinorSubjects($employee_id)
    {
        $major = $this->hr_model->getMinorSubjects($employee_id);
        return $major;
    }
    
    function getMajorSubjects($employee_id)
    {
        $major = $this->hr_model->getMajorSubjects($employee_id);
        return $major;
    }
    
    function getDailyAttendance($date=NULL)
    {
        //echo $date;
        
        $data['date'] = ($date!=NULL?$date:date('Y-m-d'));
        $data['presents'] = Modules::run('attendance/getNumberOfEmployeePresents', ($date!=NULL?$date:date('Y-m-d')), TRUE);
        $data['employees'] = $this->getEmployees();
        $data['main_content'] = 'dailyAttendance';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);
    }
    
    function passSlip()
    {
        $this->load->view('passSlipModal');
    }
    
    function savePassSlip()
    {
        $reason_value = $this->input->post('inputReason');
        $approval = $this->input->post('isUpdated');
        switch ($approval):
            case 0:
                $hr = 0;
                $msg = 'Successfully Submitted';
                Modules::run('attendance/updateTimeAttendance', $this->input->post('inputEmployeeID'), 'approved', 0, 1, $this->input->post('inputDate'));
                Modules::run('notification_system/sendNotification',1, 2, $this->input->post('inputEmployeeID'), 'Admin', 'request for DTR consideration', date('Y-m-d'), base_url().'hr/passSlipList');
                break;
            case 1:
                $hr = 1;
                $msg = 'Successfully Done';
                Modules::run('attendance/updateTimeAttendance', $this->input->post('inputEmployeeID'), 'approved', 1, 1, $this->input->post('inputDate'));
                Modules::run('notification_system/sendNotification',1, 2, 'Admin', $this->input->post('inputEmployeeID'), 'Your request for DTR consideration has been approved', date('Y-m-d'), base_url().'hr/passSlipList');
                break;
        endswitch;
        if($reason_value==0):
            $reason_value = $this->input->post('inputReasonOthers');
        endif;
        $items = array(
            'reason' => $reason_value,              
            'place' => $this->input->post('inputPlace'),
            'date_issue' => $this->input->post('inputDate'),
            'employee_id' => $this->input->post('inputEmployeeID'),
            'authorized_by_hr' => $hr,
        );
        
        $pass_id = $this->hr_model->savePassSlip($items, $this->input->post('inputEmployeeID'), $this->input->post('inputDate'));
        
        Modules::run('attendance/updateTimeAttendance', $this->input->post('inputEmployeeID'), 'pass_slip_link', $pass_id, 1, $this->input->post('inputDate'))
        
        ?>
        <script type="text/javascript">
            alert('<?php echo $msg; ?>')
            window.history.back()
        </script>
        <?php
        
        
    }
    
    function passSlipList()
    {
        if($this->session->userdata('is_admin') || $this->session->userdata('is_superAdmin')):
            $data['pass_slip_list'] = $this->hr_model->getPassSlip();
        else:
            $data['pass_slip_list'] = $this->hr_model->getPassSlip($this->session->userdata('employee_id'));
        endif;
        $data['main_content'] = 'passSlipList';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);	
    }

    // Shows Add Employee Form
    function passGenerator() {
        $length = '6';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';    

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters)-1)];
        }

        return $string;
    }
    
    function settings()
    {
        $data['main_content'] = 'default';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);	
    }
    
    function editIdNumber()
    {
        $origIdNumber = $this->input->post('origIdNumber');
        $editedIdNumber = $this->input->post('editedIdNumber'); 
        $rowid = $this->hr_model->getUserId($origIdNumber);
        $updatedIDNumber = array(
            'user_id' => $editedIdNumber
        );
        $this->hr_model->updateProfile($rowid->rowid, $updatedIDNumber);
        $this->hr_model->updateProfileInfo($origIdNumber, array('u_id'=>$editedIdNumber));
        $this->hr_model->updateProfileEmployee($origIdNumber, $updatedIDNumber, 'stg_profile_edbak');
        if($this->hr_model->updateProfileEmployee($origIdNumber, $updatedIDNumber, 'stg_profile_employee')):
            $updateAccount = array(
                'u_id' => $editedIdNumber,    
                'uname' => $editedIdNumber    
            );  
            $this->hr_model->editAccount($origIdNumber, $updateAccount);
            echo $editedIdNumber;
        endif;
        
        
    }
    
    
    function viewTeacherInfo($id)
    {
        
        $data['basicInfo'] = $this->hr_model->getBasicInfo(base64_decode($id));
        $data['edHis'] = $this->getEducationHistory(base64_decode($id));
        $data['position']  = $this->hr_model->getDepartment();
        $data['main_content'] = 'teachersInfo';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);	
    }
    
    function editEmployeeInfo($st_id)
    {
       $details = array(
            'sss' => $this->input->post('sss'),
            'tin' => $this->input->post('tin'),
            'phil_health' => $this->input->post('philHealth'),
            'pag_ibig'  => $this->input->post('pag_ibig'),
       );
       
       $this->hr_model->updateEmployeeInfo($details, $st_id);
        
    }
    
    function editAcademicInfo()
    {
        $details = array(
            'course_id' => $this->input->post('course_id'),
            'c_school_id' => $this->input->post('school_id'),
        );
        
        $this->hr_model->updateAcademicInfo($details, $this->input->post('t_id'));
    }


    function getEmployeeByPosition($position, $position_id=NULL)
    {
        $basicInfo = $this->hr_model->getEmployeeByPosition($position,$position_id);
        return $basicInfo;
    }
    
    function getEmployee($id)
    {
        $basicInfo = $this->hr_model->getBasicInfo(base64_decode($id));
        return $basicInfo;
    }
    
    function searchEmployees()
    {
        $value = $this->input->post('value');
        $data['employee'] = $this->hr_model->searchEmployees($value);
        $this->load->view('searchTeacher',$data);
    }
    
    function getEmployees()
    {
        $result = $this->hr_model->getAllEmployee('','');
        return $result;
    }
    
    function getRawEmployeePerDepartment($dept_id=NULL)
    {
        $result = $this->hr_model->getEmployeePerDepartment($dept_id);
        return $result;
    }
    
    function getAllEmployee($option = NULL, $page = null) {
        $this->load->library('table');

        $result = $this->hr_model->getAllEmployee('', '', $option);
        $config['base_url'] = base_url('hr/getAllEmployee/' . $option);
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 10;
        $config['num_links'] = 5;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);
        $page = $this->hr_model->getAllEmployee($config['per_page'], $page, $option);
        $data['employee'] = $page->result();
        $data['option'] = ($option==NULL?1:$option);
        $data['links'] = $this->pagination->create_links();
        $data['main_content'] = 'teachersList';

        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);
    }
    
    public function addEmployee()
    {
        if(!$this->session->userdata('is_logged_in')){
            redirect(base_url());
        }else{ 
           $link = Modules::run('nav/checkAccess');
            if (!$link):
                echo Modules::run('templates/opl_header');
                echo '<div class="alert alert-danger">Warning: Accessing the page URL entered is strictly prohibited. Click <a href="' . base_url() . 'main/dashboard">HERE</a> to return to main page!</div>';
                die();
                echo Modules::run('templates/opl_footer');
            else:
                $data['cities'] = Modules::run('main/getCities');
                $data['provinces'] = Modules::run('main/getProvinces');
                $data['settings'] = Modules::run('main/getSet');
                $data['religion'] = Modules::run('main/getReligion');
                $data['position'] = $this->hr_model->getDepartment();
                $data['modules'] = "hr";
                $data['main_content'] = 'addEmployee';
                echo Modules::run('templates/main_content', $data);
            endif;
        }
    }
    
    //Save profile Information
    
    public function saveProfile()
    {
        
        //saves the address
        $barangay_id = $this->hr_model->setBarangay($this->input->post('inputBarangay'));
        $generatedCode = $this->eskwela->codeCheck('profile', 'user_id', $this->eskwela->code());
        
        $add = array(
            'address_id'            => $generatedCode,
            'street'                => $this->input->post('inputStreet'),
            'barangay_id'           => $barangay_id,
            'city_id'               => $this->input->post('inputMunCity'),
            'province_id'           => $this->input->post('inputPID'),
            'country'               => 'Philippines',
            'zip_code'              => $this->input->post('inputPostal'),
        );
        
        
        $address_id = $this->hr_model->setAddress($add);

        $fName = $this->input->post('inputFirstName');
        $mName = $this->input->post('inputMiddleName');
        $lName = $this->input->post('inputLastName');
        
        $st_id = $this->input->post('inputIdNum');
        $items = array(
            'user_id'           => $generatedCode,
             'lastname'         => $lName,
             'firstname'        => $fName,
             'middlename'       => $mName,
             'add_id'           => $generatedCode,
             'sex'              => $this->input->post('inputGender'),
             'rel_id'           => $this->input->post('inputReligion'),
             'temp_bdate'       => $this->input->post('inputBdate'),
             'contact_id'       => $generatedCode,
//             'status'           => 0,
             'nationality'      => $this->input->post('inputNationality'),     
             'account_type'     => $this->input->post('inputDepartment'),     

        );  
        //saves the basic info
        $profile_id = $this->hr_model->saveProfile($items);
        
        //saves more profile info
        
        $profileInfo = array(
            'employee_id'       => $st_id,
            'user_id'           => $generatedCode,
            'position_id'       => $this->input->post('inputPosition'),
            'awards_cert'       => 0,
            'sss'               => $this->input->post('inputSSS'),
            'phil_health'       => $this->input->post('inputPH'),
            'pag_ibig'          => $this->input->post('inputPagIbig'),
            'tin'               => $this->input->post('inputTin'),
	    'prc_id'            => $this->input->post('inputPRC'),
            'incase_name'       => $this->input->post('inputInCaseName'),
            'incase_contact'    => $this->input->post('inputInCaseContact'),
            'incase_relation'   => $this->input->post('inputInCaseRelation'),
            'work_details'      => 0,
            'date_hired'        => $this->input->post('inputDateHired'),
            'em_status'         => $this->input->post('inputEmploymentStatus'),
        );
        
        $this->hr_model->saveEmploymentDetails($profileInfo);
        
        
       //saves the basic contact
        $this->hr_model->setContacts($this->input->post('inputPhone'), $this->input->post('inputEmail'), $generatedCode);

        //saves the Academic Info
        if($this->input->post('courseId')==0)
        {
          $coursedata = array(
              'course'  =>  $this->input->post('inputCourse'),
          );
          
          $course_id = $this->hr_model->saveCourse($coursedata); 
        }else{
          $course_id =  $this->input->post('courseId'); 
        }
        
        // saves the College Information
        if($this->input->post('collegeId')==0)
        {
          $collegedata = array(
              'school_name'  =>  $this->input->post('inputNameOfSchool'),
              'school_add'  => $this->input->post('inputAddressOfSchool'),
          );
          
          $college_id = $this->hr_model->saveCollege($collegedata); 
        }else{
          $college_id =  $this->input->post('collegeId'); 
        }
        
        //new hr
            $details = array(
                'eb_employee_id'    => $st_id,
                'eb_level_id'       => 4,
                'eb_school_id'      => $college_id,
                'eb_course_id'      => $course_id,
                'eb_year_grad'      => $this->input->post('inputYearGraduated'),
                'eb_highest_earn'   => 'Graduated',
                'eb_dates_from'     => '',
                'eb_dates_to'       => '',
            );

            $this->hr_model->addEducHis($details, $st_id);
            
        //save Hr Status

        $uname = $this->input->post('inputIdNum');
        $key = $this->passGenerator();
        $accounts = array(
            'u_id'          => $uname,
            'uname'         => $uname,
            'pword'         => md5($key),
            'utype'         => $this->input->post('inputDepartment'),  
            'secret_key'    => $key,  
            'isActive'    => 1,  
        );
        
        $this->hr_model->saveAccounts($accounts);
        
        $name = Modules::run('hr/getPersonName', $this->session->user_id);
        $dep = $this->hr_model->getDepartmentByID($this->input->post('inputDepartment'));
        $pos = $this->hr_model->getPositionByID($this->input->post('inputPosition'));


        Modules::run('notification_system/sendNotification', 2, 3, 'system', $this->session->employee_id, $name." has hired ".strtoupper($fName." ".$mName." ".$lName)."(".$st_id.") to ".$dep->department." year - ".$pos->position, date('Y-m-d'));


        Modules::run('notification_system/sendNotification', 3, 3, 'system', 'Admin', $name." has hired ".strtoupper($fName." ".$mName." ".$lName)."(".$st_id.") to ".$dep->department." year - ".$pos->position, date('Y-m-d'));
        echo 'Profile Successfully Saved';
    }
    
    public function saveAccounts($accounts)
    {
        $this->hr_model->saveAccounts($accounts);
        return;
    }


    //save profile info
    public function saveProfileInfo($profileInfo)
    {
        $this->hr_model->saveMoreInfo($profileInfo);
    }
    
    public function setBirthdate($date, $id, $column)
    {
        $this->registrar_model->setDate($date, $id, $column);
    }

    //Get the Position of the user in the hr_model
    public function getUserType($user_id)
    {
        $position = $this->hr_model->getUserType($user_id);
        return $position;
    }
    
    //This function gets the position information details including department info's 
    
    public function getPositionInfo($user_id)
    {
        $position = $this->hr_model->getPositionInfo($user_id);
        return $position;
    }

    // Get the position by department
    public function getPosition($dept_id)
    {
        $position = $this->hr_model->getPosition($dept_id);
        foreach ($position as $p)
                              {   
            ?>                        
          <option value="<?php echo $p->position_id; ?>"><?php echo $p->position; ?></option>

          <?php }
    }
    
    public function getPositionbyDepartment($dept_id)
    {
        $position = $this->hr_model->getPosition($dept_id);
        return $position;
    }
    
    public function getAllPosition()
    {
        $position = $this->hr_model->getPosition("");
        return $position;
    }
    
    public function searchCollege()
    {
        $value = $this->input->post('value');
        $searchResult = $this->hr_model->searchCollege($value);
        if(!empty($searchResult)){
        ?>
          <ul>
              <?php
              foreach($searchResult as $sr)
              {
              ?>
              <li onclick="$('#collegeId').val(this.id),$('#inputNameOfSchool').val(this.innerHTML), $('#collegeSearch').hide(),$('#inputAddressOfSchool').val($('#<?php echo $sr->s_id ?>_add').val())" id="<?php echo $sr->s_id ?>"><?php echo $sr->school_name ?></li>
              <input type="hidden" value="<?php echo $sr->school_add ?>" id="<?php echo $sr->s_id ?>_add" />
              <?php  
              }
              ?>
          </ul>
          
       <?php
        }
    }
    
    public function searchCourse()
    {
        $value = $this->input->post('value');
        $searchResult = $this->hr_model->searchCourse($value);
        if(!empty($searchResult)){
        ?>
          <ul>
              <?php
              foreach($searchResult as $sr)
              {
              ?>
              <li onclick="$('#courseId').val(this.id),$('#inputCourse').val(this.innerHTML), $('#courseSearch').hide()" id="<?php echo $sr->course_id ?>"><?php echo $sr->course ?></li>
              <?php  
              }
              ?>
          </ul>
          
       <?php
        }
    }
    
    function editSGList_Deductions()
    {
        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        
        $this->hr_model->editSGList_Deductions($table, $column, $id, $value);
    }
    
    function editPayrollInfo()
    {
        $table = $this->input->post('tbl');
        $pk = $this->input->post('pk');
        $id = $this->input->post('id');
        $column = $this->input->post('column');
        $value  = $this->input->post('value');

        if($this->hr_model->editPayrollInfo($pk,$table, $id, $column, $value)):
            $payrollInfo = $this->hr_model->getPayrollInfo($id);
            ?>
               <h6 style="color:black; margin:3px 0;">Basic Pay: &nbsp;<span style="color:#BB0000;"><?php echo number_format($payrollInfo->salary, 2, '.', ','); ?></span> </h6>
                <br />
                <hr/>
                <h5>Deductions:</h5>
                <hr/>
                <div class="row">
                    <div class="span3 pull-left">
                        <h6 style="color:black; margin:3px 0;">SSS: &nbsp;<span style="color:#BB0000;"><?php echo number_format($payrollInfo->SSS, 2, '.', ','); ?></span> </h6>
                        <h6 style="color:black; margin:3px 0;">Philhealth: &nbsp;<span style="color:#BB0000;"><?php echo number_format($payrollInfo->phil_health, 2, '.', ','); ?></span> </h6> 
                    </div>
                    <div class="span2">
                        <h6 style="color:black; margin:3px 0;">Pag-Ibig: &nbsp;<span style="color:#BB0000;"><?php echo number_format($payrollInfo->pag_ibig, 2, '.', ','); ?></span> </h6>
                        <h6 style="color:black; margin:3px 0;">TIN: &nbsp;<span style="color:#BB0000;"><?php echo number_format($payrollInfo->tin, 2, '.', ','); ?></span> </h6>
                    </div>
                </div>

            <?php
         else:
             
          echo 'sorry something whent wrong!';
         endif;
        
    }
    
    function ConsolidatedPayrollDetails()
    {
        $data['main_content'] = 'payroll/consolidated_payroll_details';
        $data['modules'] = 'hr';
        echo Modules::run('templates/main_content', $data);
    }
    
    function generatePayrollReport($payType = NULL)
    {
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        
        $data['payType'] = $payType;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['hrdb'] = Modules::load('hr/hrdbprocess/');
        $data['getPayrollReport'] = $this->hr_model->getPayrollReport();
        $this->load->view('payroll/payroll_report_table', $data);
    }
    
    function generatePayrollReportDetails($payType = NULL)
    {
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        
        $data['payType'] = $payType;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['hrdb'] = Modules::load('hr/hrdbprocess/');
        $data['getPayrollReport'] = $this->hr_model->getPayrollReport();
        $this->load->view('payroll/payroll_report_table_details', $data);
    }
    
    function getSpecificAdvisory($uid = null,$section = null)
    {
        $advisory = $this->hr_model->getSpecificAdvisory($uid,$section);
        return $advisory;
    }
    
    function whereYouBelong()
    {
                $data['employeeList'] = $this->getEmployees(); 
                $data['whereYouBelong'] = $this->hr_model->whereYouBelongHead(); 
                $data['department'] = $this->getDepartment();

                $data['main_content'] = 'whereYouBelong';
                $data['modules'] = 'hr';
                echo Modules::run('templates/main_content', $data); 
    }
    
    function getAssociates($dhead)
    {
        $assoc = $this->hr_model->whereYouBelong($dhead);
        return $assoc;
    }
    
    function saveNewValue()
    {
        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $value  = $this->input->post('value');
        $id  = $this->input->post('pk');
        $retrieve  = $this->input->post('retrieve');
        $dept_id = $this->input->post('dept_id');
        
        $nValue = array(
            $column             => $value,
            'position_dept_id'  => $dept_id
        );
        
        $position_id = $this->hr_model->saveNewValue($table, $nValue);
        
        $userGroup_details = array(
            'position_id' => $position_id
        );
        $this->hr_model->saveNewValue('stg_user_groups', $userGroup_details);
        
        $position = Modules::run('hr/getPositionbyDepartment', $dept_id);
        
        foreach ($position as $pos){
        ?>
            <li><?php echo $pos->position ?></li>
        <?php
            }
    }
    
    function saveDepartment()
    {
        $department = $this->input->post('department');
        $customized_id = $this->input->post('customized_id');
        
        $nValue = array(
           'department' => $department,
           'customized_dept_id' => $customized_id
        );
        
        $dept_id = $this->hr_model->saveNewValue('department', $nValue);
        
        $dept = $this->hr_model->getDepartment();
         foreach($dept as $dept)
            {
            ?>
            <li class="parent" onmouseout="$('#<?php echo $dept->dept_id ?>_a').hide()"
                onmouseover="$('#<?php echo $dept->dept_id ?>_a').show()" 
                id="<?php echo $dept->dept_id ?>_li"><?php echo $dept->department ?> 
                <a style="display: none;" id="<?php echo $dept->dept_id ?>_a" class="help-inline pull-right" 
                  rel="clickover" 
                  data-content=" 
                       <div style='width:100%;'>
                       <h6>Add Position</h6>
                       <input type='text' id='add<?php echo $dept->dept_id ?>' />
                       <div style='margin:5px 0;'>
                       <button data-dismiss='clickover' class='btn btn-small btn-danger pull-right'>Cancel</button>&nbsp;&nbsp;
                       <a href='#' id='<?php echo $dept->dept_id ?>' data-dismiss='clickover' table='stg_profile_position' column='position' pk='position_id' retrieve='getPosition' onclick='saveNewValue(this.id)' style='margin-right:10px;' class='btn btn-small btn-success pull-right'>Save</a></div>
                       </div>
                        "   
                  class="btn" data-toggle="modal" href="#">Add Position</a>
            </li>
            <?php
                $position = Modules::run('hr/getPositionbyDepartment', $dept->dept_id);
                ?>
                <ol id="<?php echo $dept->dept_id ?>_ol" type="a">
                    <?php
                    foreach ($position as $pos){
                        ?>
                    <li><?php echo $pos->position ?></li>
                    <?php
                        }
                    ?>
                </ol>
        <?php    
            }
        
        
    }
    
    function getDepartment()
    {
        $dept = $this->hr_model->getDepartment();
        return $dept;
    }
    
    function dtr($id = NULL)
    {
        if(!$this->session->userdata('is_logged_in')){
            ?>
                 <script type="text/javascript">
                    document.location = "<?php echo base_url()?>"
                 </script>
            <?php

         }else{
            $data['info'] = $this->hr_model->getBasicInfo(base64_decode($id));
            $data['hrdb'] = Modules::load('hr/hrdbprocess/');
            $data['records'] = $this->hr_model->getDTR($id); 
            $this->load->view('payroll/myDTR', $data);
         }
    }
    
    function searchDTRbyDateForPayroll()
    {
        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
       // $dateF = str_replace('-', '/', $dateFrom);
        //$dateT = str_replace('-', '/', $dateTo);
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['owners_id'] = $this->input->post('owners_id');
        $data['records']= $this->hr_model->searchDtrbyDate($dateFrom, $dateTo, $this->input->post('owners_id')); 
        $data['info'] = $this->getEmployee($this->input->post('owners_id'));
        $data['hrdb'] = Modules::load('hr/hrdbprocess/');
        $this->load->view('payroll/payrollDtrEdit', $data);
    }
    
    function searchDtrbyDate()
    {
        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
       // $dateF = str_replace('-', '/', $dateFrom);
        //$dateT = str_replace('-', '/', $dateTo);
        $data['hoursRequired'] = ($this->getNumberOfDaysWork($dateFrom, $dateTo)*8);
        $data['records']= $this->hr_model->searchDtrbyDate($dateFrom, $dateTo, $this->input->post('owners_id')); 
        $data['info'] = $this->getEmployee(base64_encode($this->input->post('employee_id')));
        $data['hrdb'] = Modules::load('hr/hrdbprocess/');
        //$data['records'] = $this->hr_model->getDTR($this->input->post('owners_id'));
        $this->load->view('payroll/myDTR_table', $data);
    }
    
    function searchDtrbyDateForPrint($dateFrom, $dateTo, $t_id)
    {
        $records= $this->hr_model->searchDtrbyDate($dateFrom, $dateTo, $t_id); 
        return $records;
    }
    
    
    function printDTR($dateFrom, $dateTo, $t_id)
    {
        $dateF = str_replace('-', '/', $dateFrom);
        $dateT = str_replace('-', '/', $dateTo);
        $info = $this->getEmployee($t_id);
        $data['info'] = $this->getEmployee($t_id);
        $data['hrdb'] = Modules::load('hr/hrdbprocess/');
        
        $this->load->view('payroll/basicDTR', $data);
    }
    
    function deleteAssociates()
        {
            $data = $this->uri->segment(3);
            $item = explode(",", $data); 
            
            foreach($item as $dhID){
                $this->hr_model->deleteAssociates($dhID);
            }
            ?>
                <script type="text/javascript">
                    alert('Deleted!')
                    document.location = "<?php echo base_url('hr/whereYouBelong')?>"
                </script>
            <?php
        }
        
        function saveDepartmentHeadsAssociates()
        {
            $dept_id = $this->input->post('dept_id');
            $dhHead = $this->input->post('dhHead');
            $associate = $this->input->post('associate');
            $this->hr_model->saveDepartmentHeadsAssociates($dept_id,$dhHead, $associate);
        ?>
            <div style="position:absolute; top:30%; left:50%;" class="alert alert-error" id="notify" data-dismiss="alert-message">
                <h4>Associate Added</h4>

            </div>
            
                    
            <?php
                $whereYouBelong = $this->hr_model->whereYouBelongHead();
                foreach($whereYouBelong as $WYB){
             ?>
            <h4>Department Head: <?php echo $WYB->lastname.', '. $WYB->firstname ?> </h4>
            <ol>
            <?php
                $assoc = $this->hr_model->whereYouBelong($WYB->user_id);
                foreach ($assoc as $assoc){
            ?>
            
                <h5><li><input style="margin:0 10px;"  type="checkbox" value="<?php echo $assoc->dh_id ?>" /><?php echo $assoc->lastname.', '. $assoc->firstname ?></li></h5>
            
            <?php
                }
             echo '</ol>' ;  
                }
            ?>
        <?php        
            
        }
    
        function ifDepartmentHead($employee_id, $dept_id)
        {
            $ifDeptHead = $this->hr_model->ifDepartmentHead($employee_id, $dept_id);
            if($ifDeptHead){
                return TRUE;
            }else{
                return FALSE;
            }
        }
        
        function getIndividualNumbers($dept_id=Null)
        {
            $numbers = $this->hr_model->getIndividualNumbers($dept_id);
            return $numbers;
        }
        
        function importTeachers()
        {
            $processProfile = Modules::load('registrar/registrardbprocess/');
            $processHr = Modules::load('hr/hrdbprocess/');
            $this->load->library("excel");
            $data['error'] = ''; //initialize image upload error array to empty

            $config['upload_path'] = 'uploads';
            $config['overwrite'] = TRUE;
            $config['allowed_types'] = '*';
            $config['max_size'] = '1000';

            $this->load->library('upload', $config);


            // If upload failed, display error
            if (!$this->upload->do_upload()) {
                $data['error'] = $this->upload->display_errors();
                print_r($data);
                //$this->load->view('csvindex', $data);
            } else {
                $file_data = $this->upload->data();
                $file_path = 'uploads/'.$file_data['file_name'];

                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                //set to read only
                $objReader->setReadDataOnly(true);
                //load excel file
                $objPHPExcel = $objReader->load($file_path);
                $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); 
                $num_rows = $objWorksheet->getHighestRow();
                
                $i=20;
                for($st=3; $st<=($num_rows); $st++){
                    
                    if($i<10):
                        $ext = '00'.$i++;
                    endif;
                    if($i>=10 && $i<100):
                        $ext = '0'.$i++;
                    endif;
                    
                    $dateHired = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(11,$st)->getValue()));
                    $em_id = str_replace('-', '', $dateHired).$ext;
                    $em_id = ($objWorksheet->getCellByColumnAndRow(0,$st)->getValue()==NULL?$em_id:$objWorksheet->getCellByColumnAndRow(0,$st)->getValue());
                    $lastname = $objWorksheet->getCellByColumnAndRow(1,$st)->getValue();
                    $firstname = $objWorksheet->getCellByColumnAndRow(2,$st)->getValue();
                    $middlename = $objWorksheet->getCellByColumnAndRow(3,$st)->getValue();
                    $birthdate = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(4,$st)->getValue()));;
                    $birthplace = ($objWorksheet->getCellByColumnAndRow(5,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(5,$st)->getValue());
                    $gender = ($objWorksheet->getCellByColumnAndRow(6,$st)->getValue()=='M'?'Male':'Female');
                    $account_type = $objWorksheet->getCellByColumnAndRow(8,$st)->getValue();
                    $position_id = $objWorksheet->getCellByColumnAndRow(9,$st)->getValue();
                    $religion = ($objWorksheet->getCellByColumnAndRow(23,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(23,$st)->getValue());
                    
                    $rel_id = $processProfile->setReligion($religion);
                    $items = array(
                            'rfid'             => '',
                            'lastname'         => $lastname,
                            'firstname'        => $firstname,
                            'middlename'       => $middlename,
                            'add_id'           => 0,
                            'sex'              => $gender,
                            'rel_id'           => $rel_id,
                            'bdate_id'         => 0,
                            'contact_id'       => 0,
                            'status'           => 0,
                            'nationality'      =>'Filipino',     
                            'account_type'     => $account_type, 
                            'avatar'           => $em_id.'.jpg'    

                       );  
                    //saves the basic info
                    $profile_id = $processProfile->saveProfile($items);
                    
                    //saves more profile info
                    
                    $sss = ($objWorksheet->getCellByColumnAndRow(28,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(28,$st)->getValue());
                    $ph = ($objWorksheet->getCellByColumnAndRow(29,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(29,$st)->getValue());
                    $pagibig = ($objWorksheet->getCellByColumnAndRow(30,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(30,$st)->getValue());
                    $tin = ($objWorksheet->getCellByColumnAndRow(31,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(31,$st)->getValue());
                    $prc = ($objWorksheet->getCellByColumnAndRow(33,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(33,$st)->getValue());
                    
                    $incase_name = ($objWorksheet->getCellByColumnAndRow(24,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(24,$st)->getValue());
                    $incase_relation = ($objWorksheet->getCellByColumnAndRow(27,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(27,$st)->getValue());
                    
                    $profileInfo = array(
                        'employee_id'       => $em_id,
                        'user_id'           => $profile_id,
                        'position_id'       => $position_id,
                        'awards_cert'       => 0,
                        'sss'               => $sss,
                        'phil_health'       => $ph,
                        'pag_ibig'          => $pagibig,
                        'tin'               => $tin,
                        'prc_id'            => 0,
                        'incase_name'       => $incase_name,
                        'incase_contact'    => '',
                        'incase_relation'   => $incase_relation,
                        'work_details'      => 0,
                        'date_hired'        => $dateHired,
                        'em_status'         => 'Contractual',
                    );
                    
                    $processHr->saveEmploymentDetails($profileInfo);
                    
                    $street = ($objWorksheet->getCellByColumnAndRow(12,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(12,$st)->getValue());
                    $barangay = ($objWorksheet->getCellByColumnAndRow(13,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(13,$st)->getValue());
                    $city = ($objWorksheet->getCellByColumnAndRow(14,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(14,$st)->getValue());
                    $zipCode = ($objWorksheet->getCellByColumnAndRow(15,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(15,$st)->getValue());
                    $contact = "";
                    
                    //saves the address
                    $barangay_id = $processProfile->setBarangay($barangay);
                    $city = $processProfile->setCity($city);

                    $add = array(
                        'street'                => $street,
                        'barangay_id'           => $barangay_id,
                        'city_id'               => $city->id,
                        'province_id'           => $city->province_id,
                        'country'               => 'Philippines',
                        'zip_code'              => $zipCode,
                    );

                    $address_id = $processProfile->setAddress($add, $profile_id);

                                   //saves the basic contact
                    $processProfile->setContacts('', '', $profile_id);
                    //$processProfile->updateContact($row['contact_id'], $profile_id);

                    //saves the birthday
                    //$date = $row['birthday'];
                    $processProfile->setBdate($birthdate, $profile_id, 'bdate_id');
                    
                    $course = ($objWorksheet->getCellByColumnAndRow(19,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(19,$st)->getValue());
                    $schoolName = ($objWorksheet->getCellByColumnAndRow(18,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(18,$st)->getValue());
                    $schoolAddress = ($objWorksheet->getCellByColumnAndRow(22,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(22,$st)->getValue());
                    $yearGrad = ($objWorksheet->getCellByColumnAndRow(21,$st)->getValue()==NULL?'':$objWorksheet->getCellByColumnAndRow(21,$st)->getValue());
                     //saves the Academic Info
                     if($course!=NULL)
                        {
                          $coursedata = array(
                              'course'  =>  $course,
                          );

                          $course_id = $processHr->saveCourse($coursedata, $$course) ; 
                        }else{
                            $course_id = 0;
                        }


                    // saves the College Information

                    $collegedata = array(
                          'school_name'  =>  $schoolName,
                          'school_add'  => $schoolAddress,
                    );

                    $college_id = $processHr->saveCollege($collegedata, $schoolName);

                    $details = array(
                        'eb_employee_id'    => $em_id,
                        'eb_level_id'       => 4,
                        'eb_school_id'      => $college_id,
                        'eb_course_id'      => $course_id,
                        'eb_year_grad'      => $yearGrad,
                        'eb_highest_earn'   => 'Graduated',
                        'eb_dates_from'     => '',
                        'eb_dates_to'       => '',
                    );

                        Modules::run('hr/saveEducation', $details, $em_id);
                        $uname = $em_id;
                        $key = Modules::run('hr/passGenerator');
                        $accounts = array(
                            'u_id'          => $uname,
                            'uname'         => $uname,
                            'pword'         => md5($key),
                            'utype'         => $account_type,  
                            'secret_key'    => $key,  
                            'isActive'      => 1,  
                        );

                        $processHr->saveAccounts($accounts);
                    
                }
                ?>
                <script type="text/javascript">
                        alert('Uploaded Successfully');
                        document.location = '<?php echo base_url().'hr/getAllEmployee' ?>'
                </script>
                <?php

            }
        }
        
        function hrEdOveride()
        {
            $this->db->select('*');
            $this->db->from('profile_employee_edbak');
            $q = $this->db->get();
            
            foreach ($q->result() as $row):
                 $array = array(
                     'eb_employee_id' => $row->user_id,
                     'eb_level_id' => 4,
                     'eb_school_id' => $row->c_school_id,
                     'eb_course_id' => $row->course_id,
                     'eb_year_grad' => $row->c_year_grad,
                     'eb_highest_earn' => 'graduate',
                     'eb_major_id' => $row->major_id,
                     'eb_minor_id' => $row->minor_id,
                 );
                 if($this->db->insert('profile_employee_education', $array)):
                     echo $row->user_id.' educational background updated. <br />';
                 endif;
                 
                 
            endforeach;
        }
        
        function updateEmStatus(){
            $eid = $this->input->post('eid');
            $status = $this->input->post('status');
            
            $this->hr_model->updateEmStatus($eid, $status);
        }
        
        function getGSAccess($id) {
            return $this->hr_model->getGSAccess($id);
        }
        
        function changePass(){
            $emp_id = $this->input->post('emp_id');
            $newpass = $this->input->post('newpass');
            
            return $this->hr_model->changePass(base64_decode($emp_id), $newpass);
            
        }
    
    
}
 
/* End of file hr.php */
/* Location: ./application/modules/hr/controllers/hrs.php */
