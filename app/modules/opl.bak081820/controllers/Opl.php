<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Opl
 *
 * @author genesisrufino
 */
class Opl extends MX_Controller {
    //put your code here


    function __construct() {
        parent::__construct();
        $this->load->model('opl_model');
        
        if (!$this->session->is_logged_in):
            redirect('login');
        endif;
    }

    private function post($name) {
        return $this->input->post($name);
    }
    
    function getNumDiscussion($grade_level,  $subject, $school_year)
    {
        $discussions = $this->opl_model->getNumDiscussions($grade_level, $subject, $school_year);
        return $discussions;
    }
    
    function getTasks($grade_level, $section, $subject, $school_year)
    {
        $tasks = $this->opl_model->getNumTask($grade_level, $section, $subject, $school_year);
        return $tasks;
    }
    
    function sectionDashboardForAdmin($gradeLevel,$section_id, $school_year)
    {
        if($this->session->isOplAdmin):
            $data['school_year'] = $school_year;
            $data['sectionDetails'] = Modules::run('opl/opl_variables/getSectionDetails', $gradeLevel, $section_id, $school_year);
            $data['headerTitle'] = $data['sectionDetails']->level.' - '.$data['sectionDetails']->section;
            $data['main_content'] = 'admin/sectionDashboard';
            $data['modules'] = 'opl';
            echo Modules::run('templates/opl_content', $data);
        else:
            redirect('opl');
        endif;
    }

    function searchTasks(){
        $search = $this->post('value');
        $grade = $this->post('grade');
        $section = $this->post('section');
        $subject = $this->post('subject');
        $tasks = $this->opl_model->searchTasks($search, $grade, $section, $subject);
        $discData = array('tasks'=>$tasks);
        echo $this->load->view('tasks/tr', $discData);
    }

    function searchDiscussion(){
        $search = $this->post('value');
        $grade = $this->post('grade');
        $section = $this->post('section');
        $subject = $this->post('subject');
        $disc = $this->opl_model->searchDiscussion($search, $grade, $section, $subject);
        $discData = array('discussionDetails'=>$disc);
        echo $this->load->view('disBoardBody', $discData);
    }

    function searchBar($link, $grade, $section, $subject){
        $data = array(
            'grade_id'      =>  $grade,
            'section_id'    =>  $section,
            'subject_id'    =>  $subject
        );
        switch(strtolower($link)):
            case 'discussionboard':
                return $this->load->view('discussionSearch', $data);
            break;
            case 'classbulletin':
                if(strtolower($this->uri->segment(4)) == 'list'):
                    return $this->load->view('tasks/taskSearch', $data);
                endif;
            break;
        endswitch;
    }

    
    public function events()
    {
       if(!$this->session->userdata('is_logged_in')){
          ?>
             <script type="text/javascript">
                document.location = "<?php echo base_url() ?>"
             </script>
          <?php
       }else{
          date_default_timezone_set('Asia/Manila');
          $result = $this->opl_model->show_events();
          $emp_id = $this->session->userdata('employee_id');
          $stud = $this->session->userdata('isStudent');
          if ($stud) {
             $details = $this->session->userdata('details');
             $grade = $details->grade_level_id;
             $section = $details->section_id;
          }

          $tdate = date('YmdHis');
          foreach ($result as $row) {
             $show = false; $blah='fa';
             $taskdate = date('YmdHis', strtotime($row->task_start_time));
             if ($emp_id) {
                if ($row->task_author_id==$emp_id) {
                   $show = true;$blah ='tru';
                }
             }elseif ($stud) {
                if ($row->task_grade_id==$grade&&$row->task_section_id==$section&&$taskdate<=$tdate) {
                   $show = true;
                }
             }

              if ($show) {

                 $sdate = strtotime($row->task_start_time);
                 $edate = strtotime($row->task_end_time);
                 $type = $row->task_type;
                 switch ($type) {
                    case '1':
                       $bg = "#3c8dbc";
                       break;
                    case '2':
                       $bg = "#f39c12";
                       break;
                    case '3':
                       $bg = "#00c0ef";
                       break;
                    case '4':
                       $bg = "#f56954";
                       break;
                    default:
                       $bg = "#ef9f00";
                       break;
                 }
                $url = base_url().'opl/viewTaskDetails/'.$row->task_code.'/'.$row->task_grade_id.'/'.$row->task_section_id.'/'.$row->task_subject_id;
                $start = date('Y-m-d H:i', $sdate);
                $end = date('Y-m-d H:i', $edate);
                $title = $row->short_code.': '.$row->task_title;
                $data[] = array(
                   'id' 	=> $row->task_code,
                   'title'	=> $title,
                   'start' => $start,
                   'end'	=> $end,
                   'backgroundColor' 	=> $bg,
                   'borderColor'		=> $bg,
                   'url'	=> $url,
                );
             }
          }
           $sy = $this->session->userdata('school_year');
          $discussions = $this->opl_model->show_discussion();
          foreach ($discussions as $discus) {
             $showd = false;
             $discusdate = date('YmdHis', strtotime($discus->dis_start_date));
             if ($emp_id) {
                if ($discus->dis_author_id==$emp_id) {
                   $showd = true;
                }
             }elseif ($stud) {
                if ($discus->dis_grade_id==$grade&&$discus->dis_section_id==$section&&$discusdate<=$tdate) {
                   $showd = true;
                }
             }

             if ($showd) {
                $durl = base_url().'opl/discussionDetails/'.$discus->dis_sys_code.'/'.$sy;
                $ddate = strtotime($discus->dis_start_date);
                $discdate = date('Y-m-d', $ddate);
                $startd = date('Y-m-d H:i', $ddate);
                $endd = $discdate.' 17:00';
                $dtitle = $discus->short_code.': '.$discus->dis_title;
                $dbg = '#A9A9A9';
                $data[] = array(
                  'id' 	=> $discus->dis_sys_code,
                  'title'	=> $dtitle,
                  'start' => $startd,
                  'end'	=> $endd,
                  'backgroundColor' 	=> $dbg,
                  'borderColor'		=> $dbg,
                  'url'	=> $durl,
                );
             }
          }
          $calendar = $this->opl_model->show_cal_events();
          $cbg = "#13cc10"; // green
          foreach ($calendar as $cal) {
             $cal_date = date($cal->event_date);
            $curl = '#';
            $cstart = $cal_date.' 8:00';
            $cend = $cal_date.' 17:00';
            $data[] = array(
               'id' 	=> $cal->id,
               'title'	=> $cal->event,
               'start' => $cstart,
               'end'	=> $cend,
               'backgroundColor' 	=> $cbg,
               'borderColor'		=> $cbg,
               'url'	=> $curl,
            );
          }
          echo json_encode($data);
       }
    }

    function setBasicOplSessions($subject_id, $grade_level, $section, $school_year)
    {
            $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_level, $section, $subject_id, $school_year));
            $opl_sessions = array(
                'subjectDetails'    => $classDetails->subjectDetails,
                'section'           => $section,
                'grade_level'       => $grade_level,
                'school_year'       => $school_year,
                'subject_id'        => $subject_id

            );

            if($this->session->has_userdata('oplSessions')):
                $this->session->unset_userdata('oplSessions');
            endif;

            $this->session->set_userdata('oplSessions', $opl_sessions);
    }

    function setOplSessions($subject_id, $section, $semester, $school_year, $isCollege = NULL)
    {
        if($isCollege!=NULL):
            $subject = Modules::run('college/subjectmanagement/getSubjectPerId', $subject_id, $school_year);
            $opl_sessions = array(
                'subjectDetails'    => $subject,
                'section'           => $section,
                'semester'          => $semester,
                'school_year'       => $school_year,
                'subject_id'        => $subject_id

            );

            if($this->session->has_userdata('oplSessions')):
                $this->session->unset_userdata('oplSessions');
            endif;

            $this->session->set_userdata('oplSessions', $opl_sessions);
        endif;
    }

    function deleteCriteria()
    {
        $rcid = $this->post('rcid');
        $school_year = $this->post('school_year');

        if($this->opl_model->deleteCriteria($rcid, $school_year)):
            echo 'Deleted Successfully';
        else:
            echo 'Sorry Something went wrong';
        endif;
    }

    function getRubricMarkings($st_id, $ref_id, $rcid, $school_year, $rdid = NULL)
    {
        $markings = $this->opl_model->getRubricMarkings($st_id, $ref_id, $rcid, $school_year, $rdid);
        return $markings;
    }

    function saveRubricMarkings()
    {
        $rubricArray = array(
            'srid'          => $this->eskwela->code(),
            'sr_stid'       => $this->post('sr_stid'),
            'sr_ref_id'     => $this->post('sr_ref_id'),
            'sr_rdid'       => $this->post('sr_rdid'),
            'sr_point'      => $this->post('sr_point'),
            'sr_comment'    => $this->post('sr_comment'),
            'is_question'   => $this->post('is_question')
        );
        if($this->opl_model->saveRubricMarkings($rubricArray, $this->post('sr_stid'),$this->post('sr_ref_id'),$this->post('rcid'), $this->post('school_year'))):
            echo 'Successfully Saved';
        else:
            echo 'Sorry Something went wrong, Please try again later';
        endif;
    }


    function searchARubric($value, $school_year)
    {
        $rubric = $this->opl_model->searchARubric($value, $school_year);
        echo '<ul>';
        foreach ($rubric as $r):
        ?>
           <li style="font-size:18px;" data-dismiss="modal" onclick="$('#searchRubric').hide(), $('#rubricBox').val(&quot;<?php echo $r->ru_alias ?>&quot;), $('#ruid').val('<?php echo $r->ruid ?>')" ><?php echo $r->ru_alias ?></li>
        <?php
        endforeach;
        echo '</ul>';

    }


    public function rubricDetails($school_year, $ruid = NULL)
    {
        if (!$this->session->is_logged_in):
            redirect('login');
        else:
            if($ruid!=NULL):
                $data['ruid'] = $ruid;
                $data['rubricDetails'] = $this->opl_model->getRubricInfo($ruid, $school_year);
            else:
                $data['ruid'] = '';
                $data['rubricDetails'] = [];
            endif;

            $data['title'] = 'Learn Manager';
            $data['subjectDetails'] = [];
            $data['gradeDetails'] = [];
            $data['main_header'] = '';
            $data['isClass'] = FALSE;
            $data['getSubjects'] = Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year);
            $data['school_year'] = $school_year;
            $data['headerTitle'] = 'Rubric Details';
            $data['main_content'] = 'rubric/rubricDetails';
            $data['modules'] = 'opl';
            $data['task_type'] = Modules::run('opl/opl_variables/getTaskType');
            echo Modules::run('templates/opl_content', $data);
        endif;
    }

    public function getRubricScaleDescription($rd_rcid, $school_year)
    {
        $scaleDescription = $this->opl_model->getRubricScaleDescription($rd_rcid, $school_year);
        return $scaleDescription;
    }

    public function getRubricCriteria($ruid, $school_year)
    {
        $criteria = $this->opl_model->getRubricCriteria($ruid, $school_year);
        return $criteria;
    }

    public function rubricList($school_year)
    {
        $data['list'] = $this->opl_model->getRubricList($this->session->username, $school_year);
        $data['title'] = 'Learn Manager';
        $data['subjectDetails'] = [];
        $data['gradeDetails'] = [];
        $data['main_header'] = '';
        $data['isClass'] = FALSE;
        $data['school_year'] = $school_year;
        $data['headerTitle'] = 'List of Available Rubric';
        $data['main_content'] = 'rubric/rubricList';
        $data['modules'] = 'opl';
        echo Modules::run('templates/opl_content', $data);
    }

    public function saveCriteria()
    {
        if($this->post('rcid')==0):
            $rcid = $this->eskwela->code();
        else:
            $rcid = $this->post('rcid');
        endif;
        $school_year = $this->post('school_year');
        $criteriaDetails = array(
            'rcid'          => $rcid,
            'rc_ruid'       => $this->post('ruid'),
            'rc_criteria'   => $this->post('criteriaName'),
            'rc_percentage' => $this->post('criteriaPercentage')
        );

        if($this->opl_model->saveCriteria($criteriaDetails, $school_year, $this->post('rcid'), $rcid, $this->post('ruid'), $this->post('criteriaName'))):
            $scales = json_decode($this->post('scales'));
            $sc = 0;
            foreach ($scales as $s):
                $sc++;
                $rdDetails = array(
                    'rdid'      => $this->eskwela->code(),
                    'rd_rcid'   => $rcid,
                    'rd_scale'  => $s->rd_scale,
                    'rd_description' => ($s->rd_description!=NULL?$s->rd_description:'')
                );

                if($this->opl_model->saveDescription($rdDetails, $school_year)):
                endif;

            endforeach;
            if(count($scales)==$sc):
                echo 'Successfully Saved';
            else:
                echo 'Something went wrong, Please try again later';
            endif;
        endif;
    }

    public function addRubric()
    {
        $ruid = $this->eskwela->code();
        $details = array(
            'ruid'          => $ruid,
            'ru_alias'      => $this->post('title'),
            'ru_access'     => 0,
            'ru_type'       => $this->post('type'),
            'ru_auth_id'   => $this->session->username,
            'ru_remarks'    => '',
            'ri_scale'      => $this->post('scale')
        );

        if($this->opl_model->addRubric($details, $this->post('school_year'))):
            echo json_encode(array('msg' => 'Successfully Added', 'code' => $ruid));
        endif;
    }


    public function createRubric($school_year, $ruid = NULL)
    {
        if (!$this->session->is_logged_in):
            redirect('login');
        else:
            if($ruid!=NULL):
                $data['ruid'] = $ruid;
                $data['rubricDetails'] = $this->opl_model->getRubricInfo($ruid, $school_year);
            else:
                $data['ruid'] = '';
                $data['rubricDetails'] = [];
            endif;

            $data['title'] = 'Learn Manager';
            $data['subjectDetails'] = [];
            $data['gradeDetails'] = [];
            $data['main_header'] = '';
            $data['isClass'] = FALSE;
            $data['getSubjects'] = Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year);
            $data['school_year'] = $school_year;
            $data['headerTitle'] = 'Create A Rubric';
            $data['main_content'] = 'rubric/createRubric';
            $data['modules'] = 'opl';
            echo Modules::run('templates/opl_content', $data);
        endif;
    }

    function downloads($link)
    {
        if (file_exists(base64_decode($link))):
            $this->load->helper('download');
            force_download(base64_decode($link), NULL);
        endif;
    }

    function printAdmission()
    {
        $studentDetails = array(

            'details'   => Modules::run('registrar/getSingleStudent', 2020110003 ,null,null),
            'main_content' => 'printAdmission',
            'modules' => 'opl',
        );

        // print_r(Modules::run('registrar/getSingleStudent', 2020110003 ,null,null));

        echo Modules::run('templates/print_content', $studentDetails);

    }

    function printTask($task_code, $st_id = NULL)
    {

        $taskDetails = array(
            'st_id'     => $st_id,
            'details'   => $this->opl_model->fetchTaskByCode($task_code)->row(),
            'main_content' => 'tasks/printTask',
            'modules' => 'opl',
        );

        echo Modules::run('templates/print_content', $taskDetails);

    }

    function printDiscussion($discussion_code, $st_id = NULL)
    {

        $taskDetails = array(
            'st_id'     => $st_id,
            'details'   => $this->opl_model->getDiscussionByCode($discussion_code)->row(),
            'main_content' => 'printDiscussion',
            'modules' => 'opl',
        );

        echo Modules::run('templates/print_content', $taskDetails);

    }

    function getCommentsById($com_id)
    {
        $comment = $this->opl_model->getCommentById($com_id);
        return $comment;
    }

    function fetchRawComments($com_id, $school_year, $isReply = FALSE)
    {
        $data['comment']   = $this->getCommentsById($com_id, $school_year);
        $data['isReply'] = $isReply;
        $this->load->view('comments/rawComment', $data);
    }

    function fetchDiscussComments(){
        $school_year = $this->session->school_year;
        $com_to = $this->post('com_to');
        $comments = $this->opl_model->getComments($com_to, $school_year);
        $replies = $this->opl_model->getReply($com_to, $school_year);
        $comCount = $this->post('comCount');
        foreach ($replies as $reply):
            $rep['com'][] = Modules::run('opl/fetchRawComments', $reply->com_id, $school_year, TRUE);
            $rep['sys_code'][]=$reply->com_sys_code;
            $rep['replyTo'][]=$reply->com_replyto_id;
        endforeach;
        foreach ($comments as $comment):
            $com['com'][] = Modules::run('opl/fetchRawComments', $comment->com_id, $school_year);

        endforeach;


        if((count($comments)+count($replies)) > $comCount):
            $data = array(
                'commentDetails' => $com,
                'replyDetails'   => $rep,
                'comCount'       => (count($comments)+count($replies)),
                'comments'       => $comments,
                'hasNewComment' => TRUE
            );
            echo json_encode($data);
        else:    
            $data = array(
                'hasNewComment' => FALSE,
                'comCount'       => (count($comments)+count($replies))
            );
            echo json_encode($data);
        endif;
    }

    function fetchComments(){
        $school_year = $this->session->school_year;
        $com_to = $this->post('com_to');
        $data['comments']   = $this->opl_model->getComments($com_to, $school_year);
        $data['com_to']     = $com_to;
        $this->load->view('comments', $data);
    }

    public function getAssessCatDropdown($details=NULL, $year = NULL) {

        $detailItem = explode('-', $details);
        $gs_settings = $this->opl_model->getSettings($year);
        if($gs_settings->customized):
            $cat = $this->opl_model->getCustomAssessCategory($detailItem[0], $year);
            foreach ($cat as $category) {
                ?>
                <option value="<?php echo $category->code ?>"><?php echo $category->sub_component ?></option>

            <?php
            }
        else:
            $cat = $this->opl_model->getAssessCategory($detailItem[0], $year);
            foreach ($cat as $category) {
                ?>
                <option value="<?php echo $category->code ?>"><?php echo $category->component ?></option>

            <?php
            }
        endif;

    }

    public function discussionPDF($code) {
        $this->load->library('Pdf');
        $this->load->view('discussionPDF.php', array("discussion" => $this->opl_model->getDiscussionByCode($code)->row()));
    }

    public function taskPDF($code) {
        $this->load->library('Pdf');
        $this->load->view("tasks/pdftask.php", array("task" => $this->opl_model->fetchTaskByCode($code)->row()));
    }

    function streamComments() {

    }

    function deleteDiscussion() {
        $id = $this->post('discussionid');
        $response = $this->opl_model->deleteDiscussion($id);
        if ($response == TRUE):
            echo json_encode(array("message" => "Successfully removed discussion"));
        else:
            echo json_encode(array("message" => "Something went wrong with deleting the discussion"));
        endif;
    }

    function editDiscussion() {
        $id = $this->post('discussionID');
        $data = array(
            'dis_title' => $this->post('discussTitle'),
            'dis_details' => $this->post('discussDetails'),
            'dis_unit_id' => $this->post('unitLink'),
            'dis_start_date' => $this->post('startDate') . " " . $this->post('timeStart') . ":00",
        );
        $response = $this->opl_model->editDiscussion($id, $data);
        if ($response == TRUE):
            echo json_encode(array("message" => "Successfully edited discussion"));
        else:
            echo json_encode(array("message" => "Something went wrong with editing the discussion"));
        endif;
    }

    function deleteUnit() {
        $id = $this->post('lessonid');
        $response = $this->opl_model->deleteUnit($id);
        if ($response == TRUE):
            echo json_encode(array("message" => "Successfully removed unit"));
        else:
            echo json_encode(array("message" => "Something went wrong with deleting the unit"));
        endif;
    }

    function editUnit() {
        $id = $this->post('unitID');
        $data = array(
            'ou_unit_title' => $this->post('unitTitle'),
            'ou_unit_objectives' => $this->post('unitObjectives'),
            'ou_unit_overview' => $this->post('unitOverview'),
            'ou_subject_id' => $this->post('subjects'),
            'ou_grade_level_id' => $this->post('gradeLevel')
        );
        $response = $this->opl_model->editUnit($id, $data);
        if ($response == TRUE):
            echo json_encode(array("message" => "Successfully edited unit"));
        else:
            echo json_encode(array("message" => "Something went wrong with editing the unit"));
        endif;
    }

    function deleteTasks() {
        $code = $this->post('code');
        $response = $this->opl_model->deleteTasks($code);
        if ($response == TRUE):
            echo json_encode(array("message" => "Successfully removed tasks"));
        else:
            echo json_encode(array("message" => "Something went wrong with deleting the tasks"));
        endif;
    }

    public function updateTasks() {
        $taskCode = $this->post('taskCode');
        $level = explode("-", $this->post('taskGrade'));
        $data = array(
            'task_title' => $this->post('taskTitle'),
            'task_details' => $this->post('taskDetails'),
            'task_type' => $this->post('taskType'),
            'task_section_id' => $level[2],
            'task_subject_id' => $level[0],
            'task_grade_id' => $level[1],
            'task_lesson_id' => $this->post('taskUnitLink'),
            'task_start_time' => date('Y-m-d', strtotime($this->post('taskStartDate'))) . ' ' . date('G:i:s', strtotime($this->post('taskTimeStart'))),
            'task_end_time' => date('Y-m-d', strtotime($this->post('taskEndDate'))) . ' ' . date('G:i:s', strtotime($this->post('taskTimeEnd')))
        );
        $response = $this->opl_model->updateTasks($taskCode, $data);
        if ($response == TRUE) {
            // echo json_encode(array("message" => $data));
            echo json_encode(array("message" => "Successfully Updated"));
        } else {
            // echo json_encode(array("message" => $response));
            echo json_encode(array("message" => "Something went wrong please try again"));
        }
    }

    public function discussionDetails($discuss_id, $school_year) {
        $taskDetails = array(
            'isClass' => FALSE,
            'discussionDetails' => $this->opl_model->getDiscussionDetails($discuss_id, $school_year),
            'gradeDetails' => [],
            'subjectDetails' => [],
            'school_year' => $school_year,
            'headerTitle' => 'Discussion Details',
            'main_header' => '',
            'title' => 'e-sKwela Online Platform for Learning',
            'main_content' => 'discussionDetails',
            'modules' => 'opl',
        );

        echo Modules::run('templates/opl_content', $taskDetails);
    }

    public function getPost() {
        $postDetails = $this->opl_model->getPost();
        return $postDetails;
    }

    public function submitQuickPost() {
        $owner_id = ($this->session->isStudent ? $this->session->details->st_id : $this->session->username);
        $postDetails = $this->post('postDetails');

        $postArray = array(
            'op_owner_id' => $owner_id,
            'op_post' => $postDetails,
            'op_is_public' => 1
        );

        if ($this->opl_model->submitPost($postArray, $this->session->school_year)):
            echo 'Successfully Submitted';
        else:
            echo 'Something went wrong, please try again later';
        endif;
    }

    public function sendDiscussComment() {
        $comType = $this->post('com_type');
        $comDetails = $this->post('com_details');
        $comFrom = $this->post('com_from');
        $comTo = $this->post('com_to');
        $com_syscode = $this->eskwela->code();
        $com_isStudent = $this->post('is_student');

        $commentDetails = array(
            'com_type' => $comType,
            'com_details' => $comDetails,
            'com_from' => $comFrom,
            'com_to' => $comTo,
            'com_sys_code' => $com_syscode,
            'com_isStudent' => $com_isStudent
        );

        if ($this->opl_model->sendComment($commentDetails)):
            
            $remarks = $this->session->name.' has sent a comment on a discussion';
            Modules::run('notification_system/sendNotification',8,3,'system',$this->session->oplSessions['section'], $remarks);
            echo Modules::run('opl/opl_variables/getDiscussComments', $comTo, $comType, $this->session->school_year);
        endif;
    }

    public function sendComment() {
        $comType = $this->post('com_type');
        $comDetails = $this->post('com_details');
        $comFrom = $this->post('com_from');
        $comTo = $this->post('com_to');
        $com_syscode = $this->eskwela->code();
        $com_isStudent = $this->post('is_student');

        $commentDetails = array(
            'com_type' => $comType,
            'com_details' => $comDetails,
            'com_from' => $comFrom,
            'com_to' => $comTo,
            'com_sys_code' => $com_syscode,
            'com_isStudent' => $com_isStudent
        );

        if ($this->opl_model->sendComment($commentDetails)):
            echo Modules::run('opl/opl_variables/getComments', $comTo, $comType, $this->session->school_year);
        endif;
    }

    public function viewTaskDetails($task_id, $grade_level, $section, $subject, $school_year = NULL) {
        $sy = ($school_year == NULL ? $this->session->school_year : $school_year);
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_level, $section, $subject, $sy));
        $taskDetails = array(
            'isClass' => FALSE,
            'task' => $this->opl_model->getTaskDetails($task_id, $school_year),
            'gradeDetails' => $classDetails->basicInfo,
            'subjectDetails' => $classDetails->subjectDetails,
            'school_year' => $sy,
            'headerTitle' => 'Task Details',
            'main_header' => '',
            'title' => 'e-sKwela Online Platform for Learning',
            'main_content' => 'tasks/taskDetails',
            'modules' => 'opl',
        );

        echo Modules::run('templates/opl_content', $taskDetails);
    }

    public function getTaskByCode($code, $school_year) {
        $task = Modules::run('opl/opl_variables/getTaskByCode', $code, $school_year);
        foreach ($task as $t):
            echo '<li> <a href="' . base_url('opl/viewTaskDetails/') . $t->task_code . '/' . $t->task_grade_id . '/' . $t->task_section_id . '/' . $t->task_subject_id . '">' . $t->task_title . '</a></li>';
        endforeach;
    }

    public function getUnits($grade_level, $subject, $school_year) {
        $unitLessons = $this->opl_model->getUnits($grade_level, $subject, $school_year);
        return $unitLessons;
    }

    function getUnitDetails($code, $school_year) {
        $unitDetails = $this->opl_model->getUnitDetails($code, $school_year);

        $array = array('unitDetails' => $unitDetails, 'tasks' => Modules::run('opl/getTaskByCode', $code, $school_year));

        echo json_encode($array);
    }

    public function saveUnit() {
        $unitDetails = array(
            'ou_unit_title' => $this->post('unitTitle'),
            'ou_unit_objectives' => $this->post('unitObjectives'),
            'ou_unit_overview' => $this->post('unitOverview'),
            'ou_subject_id' => $this->post('subject_id'),
            'ou_grade_level_id' => $this->post('grade_level_id'),
            'ou_owners_id' => $this->session->username,
            'ou_opl_code' => $this->eskwela->code()
        );
        $isSave = $this->opl_model->saveUnit($unitDetails, $this->post('unitTitle'), $this->post('school_year'));

        if ($isSave):
            echo 'Lesson Successfully Added';
        elseif ($isSave == 2):
            echo 'Lesson Title Already Exist';
        else:
            echo 'Sorry Something Went Wrong';

        endif;
    }

    public function addDiscussion($code = NULL) {
        $subGrSec = explode('-', $this->post('gradeLevel'));
        $discussionDetails = array(
            'dis_title'         => $this->post('discussTitle'),
            'dis_details'       => $this->post('discussDetails'),
            'dis_unit_id'       => $this->post('unitID'),
            'dis_subject_id'    => $subGrSec[0],
            'dis_grade_id'      => $subGrSec[1],
            'dis_start_date'    => $this->post('startDate'),
            'dis_author_id'     => $this->session->username,
            'dis_sys_code'      => $this->eskwela->code()
        );

        if ($this->opl_model->addDiscussion($discussionDetails, $code, $this->post('school_year'))):
            $remarks = $this->session->name.' has added a lesson for Discussion ';
            Modules::run('notification_system/sendNotification',8,3,'system',$subGrSec[2], $remarks);
            echo 'Successfully Saved';
        else:
            echo 'Sorry, Something went wrong. Please try again later';
        endif;
    }

    public function discussionBoard($school_year = NULL, $employee_id = NULL, $grade_level = NULL, $section = NULL, $subject = NULL) {
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_level, $section, $subject, $school_year));
        $data = array(
            'unitDetails' => Modules::run('opl/opl_variables/getAllUnits', $this->session->username, $school_year, $grade_level),
            'isClass' => FALSE,
            'school_year' => $school_year,
            'gradeDetails' => $classDetails->basicInfo,
            'subjectDetails' => $classDetails->subjectDetails,
            'discussionDetails' => Modules::run('opl/opl_variables/getDiscussionList', ($employee_id == NULL ? $this->session->username : $employee_id)),
            'headerTitle' => 'Discussion Board',
            'main_header' => '',
            'title' => 'e-sKwela Online Platform for Learning',
            'modules' => 'opl',
            'main_content' => 'discussionBoard',
            'grade_level'   =>  $grade_level,
            'section_id'    =>  $section,
            'subject_id'    =>  $subject
        );

        echo Modules::run('templates/opl_content', $data);
    }

    public function newDiscussion($school_year = NULL) {
        $data = array(
            'unitDetails' => Modules::run('opl/opl_variables/getAllUnits', $this->session->username, $school_year),
            'isClass' => FALSE,
            'school_year' => $school_year,
            'gradeDetails' => [],
            'subjectDetails' => [],
            'getSubjects' => Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year),
            'headerTitle' => 'Create A Discussion',
            'main_header' => '',
            'title' => 'e-sKwela Online Platform for Learning',
            'modules' => 'opl',
            'main_content' => 'createDiscussion'
        );

        echo Modules::run('templates/opl_content', $data);
    }

    public function addTask() {
        $subGradSec = explode('-', $this->post('subGradeSec'));
        $sys_code = $this->eskwela->code();
        $school_year = $this->session->school_year;

        $hasUpload = $this->post('hasUpload');
        if($hasUpload==1):
            $file = $this->post('userfile');
            $data['error'] = ''; //initialize image upload error array to empty

            $config['upload_path'] = UPLOADPATH.$school_year.DIRECTORY_SEPARATOR.'faculty'.DIRECTORY_SEPARATOR.$this->session->username.DIRECTORY_SEPARATOR.$subGradSec[0].DIRECTORY_SEPARATOR.'task';
            if(!is_dir($config['upload_path'].'/')):
                mkdir($config['upload_path'],0777,TRUE);
            endif;

            $config['overwrite'] = TRUE;
            $config['allowed_types'] = '*';
            $config['max_size'] = '3072';
            $config['maintain_ratio'] = TRUE;
            $config['quality'] = '50%';
            $config['width'] = 200;
            $this->load->library('upload', $config);

             // If upload failed, display error
            if (!$this->upload->do_upload()) {
                $data['error'] = $this->upload->display_errors();
                print_r($data);
                echo $config['upload_path'].'/';
                //$this->load->view('csvindex', $data);
            } else {
                $file_data = $this->upload->data();
                $ext = $file_data['file_ext'];
                $filename = $file_data['file_name'];
                $uploaded_file = $filename;
            }
        else:
            $uploaded_file = '';
        endif;

        $taskDetails = array(
            'task_code' => $sys_code,
            'task_type' => $this->post('taskType'),
            'task_submission_type'  => $this->post('task_submission'),
            'task_author_id' => $this->session->username,
            'task_details' => $this->post('postDetails'),
            'task_section_id' => $subGradSec[2],
            'task_subject_id' => $subGradSec[0],
            'task_grade_id' => $subGradSec[1],
            'task_title' => $this->post('postTitle'),
            'task_lesson_id' => $this->post('unitLink'),
            'task_start_time' => date('Y-m-d', strtotime($this->post('startDate'))) . ' ' . date('G:i:s', strtotime($this->post('timeStart'))),
            'task_end_time' => date('Y-m-d', strtotime($this->post('deadlineDate'))) . ' ' . date('G:i:s', strtotime($this->post('timeDeadline'))),
            'task_is_online'    => $this->post('task_is_online'),
            'task_online_link'  => $this->post('task_online_link'),
            'task_total_score'  => $this->post('numItems'),
            'task_attachments' => $uploaded_file,
            'marking_type'  => $this->post('markingType'),
            'marking_link'  => $this->post('ruid')
        );

        if ($this->opl_model->addTask($taskDetails, $this->post('school_year'))):
            $details = array(
                'assess_id'     => $sys_code,
                'assess_title' => $this->input->post('postTitle'),
                'assess_date' => $this->post('startDate'),
                'section_id' => $subGradSec[2],
                'subject_id' => $subGradSec[0],
                'faculty_id' => $this->session->employee_id,
                'no_items' => $this->input->post('numItems'),
                'quiz_cat' => $this->input->post('gsComponent'),
                'term' => $this->input->post('inputTerm'),
                'school_year' => $this->post('school_year'),
                'gs_strnd_id' => 0
            );

            $this->opl_model->saveAssessment($details, $this->post('school_year'));

            echo json_encode(array('msg' => 'Successfully Posted', 'grade_id'=> $subGradSec[1],'section_id' => $subGradSec[2],'subject_id' => $subGradSec[0]));

            $remarks = $this->session->name.' has add a task for you to work on. Deadline would be on '.date('F d, Y g:i a', strtotime($this->post('deadlineDate').' '.$this->post('timeDeadline')));
            Modules::run('notification_system/sendNotification',8,3,'system',$subGradSec[2], $remarks);
        else:
            echo json_encode(array('msg' => 'Sorry, Something went wrong, Please try again later', 'grade_id'=> $subGradSec[1],'section_id' => $subGradSec[2],'subject_id' => $subGradSec[0]));
        endif;
    }

    public function index() {
        if(!$this->session->has_userdata('isOplAdmin')):
            if( $this->session->position=="Super Administrator"  ||
                $this->session->position=="School Administrator"):
                $isOplAdmin = array('isOplAdmin' => TRUE);
            else:
                $isOplAdmin = array('isOplAdmin' => FALSE);
            endif; 
            $this->session->set_userdata($isOplAdmin);
        endif;
        
        $data = array(
            'isClass' => FALSE,
            'post' => $this->getPost(),
            'subjectDetails' => [],
            'headerTitle' => 'Dashboard - School Bulletin',
            'main_header' => '<strong>Sneak Peek!</strong> Welcome to <strong>e-sKwela LEARN MANAGER</strong>.',
            'title' => 'e-sKwela Online Platform for Learning',
            'main_content' => 'default',
            'modules' => 'opl',
        );

        echo Modules::run('templates/opl_content', $data);
    }

    public function createTask($school_year = NULL) {
        $data['title'] = 'e-sKwela Online Platform for Learning';
        $data['subjectDetails'] = [];
        $data['gradeDetails'] = [];
        $data['main_header'] = '';
        $data['isClass'] = FALSE;
        $data['getSubjects'] = Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year);
        $data['school_year'] = $school_year;
        $data['headerTitle'] = 'New Task';
        $data['main_content'] = 'tasks/addTask';
        $data['modules'] = 'opl';
        $data['task_type'] = Modules::run('opl/opl_variables/getTaskType');
        echo Modules::run('templates/opl_content', $data);
    }

    public function classBulletin($school_year = NULL, $task = NULL, $grade_level, $section, $subject) {
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_level, $section, $subject, $school_year));
        
        $this->setBasicOplSessions($subject, $grade_level, $section, $school_year);
        
        $data = array(
            'isClass' => FALSE,
            'school_year' => $school_year,
            'gradeDetails' => $classDetails->basicInfo,
            'subjectDetails' => $classDetails->subjectDetails,
            // 'unitDetails'       => $classDetails->unitDetails,
            'headerTitle' => $classDetails->subjectDetails->subject . ' - ' . $classDetails->basicInfo->level . ' [ ' . $classDetails->basicInfo->section . ' ] Class Bulletin',
            'main_header' => '<strong>Sneak Peek!</strong> Welcome to <strong>e-sKwela Learn Manager </strong>.',
            'title' => 'e-sKwela Learn Manager',
            'modules' => 'opl',
        );
        
        
        switch ($task):
            case 'Students':
                $data['main_header'] = '';
                $data['students'] = Modules::run('opl/opl_variables/getStudents', $grade_level, $section, $school_year);
                $data['unitDetails'] = Modules::run('opl/opl_variables/getAllUnits', $this->session->username, $school_year, $grade_level);
                $data['headerTitle'] = $classDetails->subjectDetails->subject . ' - ' . $classDetails->basicInfo->level . ' [ ' . $classDetails->basicInfo->section . ' ] Students';
                $data['main_content'] = 'classList';
                break;
            case 'List':
                if ($grade_level != NULL):
                    $data['headerTitle'] = $classDetails->subjectDetails->subject . ' - ' . $classDetails->basicInfo->level . ' [ ' . $classDetails->basicInfo->section . ' ] Task List';
                    $data['main_header'] = '';
                    $data['subject_id'] = $subject;
                    $data['grade_level'] = $grade_level;
                    $data['section_id'] = $section;
                    $data['tasks'] = $this->opl_model->getTask($grade_level, $section, $subject, $school_year);
                    $data['main_content'] = 'tasks/taskList';
                else:
                    $data['main_content'] = 'units/unitList';
                    ?>
                    <script type="text/javascript">
                        alert('Please Select a Class');
                        history.back();
                    </script>
                <?php

                endif;
                break;
            default:
                $data['subject_id'] = $subject;
                $data['grade_level'] = $grade_level;
                $data['section_id'] = $section;
                $data['isStudent'] = FALSE;
                $data['postDetails'] = $this->opl_model->getTask($grade_level, $section, $subject, $school_year);
                $data['main_content'] = 'classBulletin';
            break;
        endswitch;

        echo Modules::run('templates/opl_content', $data);
    }

    function unitView($school_year = NULL, $task = NULL, $grade_level = NULL, $section = NULL, $subject = NULL) {
        if ($school_year == NULL):
            $school_year = $this->session->school_year;
        endif;
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_level, $section, $subject, $school_year));
        $data = array(
            'isClass' => FALSE,
            'gradeLevel' => Modules::run('opl/opl_variables/getGradeLevel', NULL, $school_year),
            'main_header' => '',
            'school_year' => $school_year,
            'gradeDetails' => $classDetails->basicInfo,
            'subjectDetails' => $classDetails->subjectDetails,
            'headerTitle' => $classDetails->subjectDetails->subject . ' - ' . $classDetails->basicInfo->level . ' [ ' . $classDetails->basicInfo->section . ' ]',
            'title' => 'e-sKwela Online Platform for Learning',
            'modules' => 'opl',
        );
        switch ($task):
            case 'Add':
                $data['subject_id'] = $subject;
                $data['grade_level'] = $grade_level;
                $data['getSubjects'] = Modules::run('opl/opl_variables/getSubjects');
                $data['main_content'] = 'units/addUnit';
                break;
            case 'List':
                if ($grade_level != NULL):
                    $data['subject_id'] = $subject;
                    $data['grade_level'] = $grade_level;
                    $data['getUnits'] = $this->getUnits($grade_level, $subject, $school_year);
                    $data['main_content'] = 'units/unitList';
                else:
                    $data['main_content'] = 'units/unitList';
                    ?>
                    <script type="text/javascript">
                        alert('Please Select a Class');
                        history.back();
                    </script>
                <?php

                endif;
                break;
            default:
                $data['postDetails'] = $this->opl_model->getTask($section, $subject, $school_year);
                $data['main_content'] = 'unitView';
                break;
        endswitch;

        echo Modules::run('templates/opl_content', $data);
    }

    function task_menu() {
        $this->load->view('class_menu');
    }

}
