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
class Messages extends MX_Controller {

    //put your code here


    function __construct() {
        parent::__construct();
        $this->load->model('student_model');
        $this->load->model('opl_model');
        $this->load->model('messages_model');
        $this->load->library('pagination');
        $this->load->library('upload');
        $this->load->helper('download');
        if (!$this->session->is_logged_in):
            redirect('entrance');
        endif;
    }

    public function index() {
        
    }

    function create_message($uid, $section) {
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $this->session->details->grade_id, $this->session->details->section_id, $subject, $school_year));
        $subj_teacher = Modules::run('academic/getSubjectTeacher', $subject, $this->session->details->section_id);
        $data['fAssign'] = $this->messages_model->getStudentListAssign(base64_decode($uid));
        $data['headerTitle'] = 'Compose Messagess';
//        $data['teacher'] = $subj_teacher;
//        $data['subjectDetails'] = $classDetails->subjectDetails;
//        $data['subj_id'] = $subject;
//        $data['grade_id'] = $this->session->details->grade_id;
//        $data['section_id'] = $this->session->details->section_id;
        $data['sec_id'] = $section;
        $data['students'] = Modules::run('opl/student/getStudentsBySection', NULL, base64_decode($section));
        $data['main_content'] = 'messages/compose';
        $data['modules'] = 'opl';

        echo Modules::run('templates/opl_content', $data);
    }

    function student_inbox($stid, $subject = NULL) {
        $result = $this->messages_model->student_inbox(base64_decode($stid), NULL, NULL, $subject);

        $config['base_url'] = base_url('opl/messages/student_inbox/' . $stid . '/' . $subject);
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 10;
        $config["uri_segment"] = 6;
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

        $page = $this->messages_model->student_inbox(base64_decode($stid), $config['per_page'], $this->uri->segment(6), $subject);
        $data['links'] = $this->pagination->create_links();

        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $this->session->details->grade_id, $this->session->details->section_id, $subject, $this->session->school_year));
        $data['headerTitle'] = $classDetails->subjectDetails->subject . ' - ' . $classDetails->basicInfo->level . ' [ ' . $classDetails->basicInfo->section . ' ] Class Bulletin';
        $data['gradeDetails'] = $classDetails->basicInfo;
        $data['subjectDetails'] = $classDetails->subjectDetails;
        $data['messages'] = $page->result();
        $data['main_content'] = 'messages/inbox';
        $data['modules'] = 'opl';

        echo Modules::run('templates/opl_content', $data);
    }

    function inbox($emid) {
        $result = $this->messages_model->inbox(base64_decode($emid));

        $config['base_url'] = base_url('opl/messages/inbox/' . $emid);
        $config['total_rows'] = $result->num_rows();
        $config['per_page'] = 10;
        $config["uri_segment"] = 5;
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

        $page = $this->messages_model->inbox(base64_decode($emid), $config['per_page'], $this->uri->segment(5));
        $data['links'] = $this->pagination->create_links();

        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_id, $section_id, $subject, $this->session->school_year));
        $data['headerTitle'] = 'Message Inbox';
        $data['gradeDetails'] = $classDetails->basicInfo;
        $data['subjectDetails'] = $classDetails->subjectDetails;
        $data['messages'] = $page->result();
        $data['main_content'] = 'messages/inbox';
        $data['modules'] = 'opl';

        echo Modules::run('templates/opl_content', $data);
    }

    function readMsge($id, $isReply = NULL, $msg_id = NULL) {
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_id, $section_id, $subject, $this->session->school_year));
//        $data['headerTitle'] = $classDetails->subjectDetails->subject . ' - ' . $classDetails->basicInfo->level . ' [ ' . $classDetails->basicInfo->section . ' ] Class Bulletin';
        $data['mid'] = $id;
        $data['subj_id'] = $subject;
        $data['gradeDetails'] = $classDetails->basicInfo;
        $data['subjectDetails'] = $classDetails->subjectDetails;
        $data['msg'] = $this->messages_model->readMsge(base64_decode($id), $isReply, base64_decode($msg_id));
        $data['main_content'] = 'messages/read';
        $data['modules'] = 'opl';

        echo Modules::run('templates/opl_content', $data);
    }

    function sendMsg() {
        $subjMsg = $this->input->post('subjMsg');
        $content = $this->input->post('content');
        $recipient = $this->input->post('recipient');
        $sender = $this->input->post('sender');
        $subj_id = $this->input->post('subj_id');
        $arrUpload = $this->input->post('arrUpload');
        $isStudent = $this->input->post('isStudent');
        $uploads = explode(',', $arrUpload);

        $q = $this->messages_model->sendMsg($subjMsg, $content, $recipient, base64_decode($sender), $subj_id, $arrUpload);
        if ($q):
            $file = strtotime(date('Y-m-d'));
            $source = 'uploads/temp/';
            $destination = UPLOADPATH . $this->session->school_year . DIRECTORY_SEPARATOR . ($isStudent == NULL ? 'faculty' : 'students') . DIRECTORY_SEPARATOR . base64_decode($sender) . DIRECTORY_SEPARATOR . 'subjects' . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR;
            if (!is_dir($destination)):
                mkdir($destination, 0777, TRUE);
            endif;
            foreach ($uploads as $u):
//                $file = strtotime(date('Y-m-d')) . ' - ' . $u;
                copy($source . $u, $destination . $u);
                unlink(FCPATH . $source . $u);
            endforeach;

            echo 'Message Sent!!!';
        else:
            echo 'An Error Occured';
        endif;
    }

    function getMsgReply($msg_id) {
        return $this->messages_model->getMsgReply($msg_id);
    }

    function replyMsg() {
        $sender = $this->input->post('sender');
        $recipient = $this->input->post('recipient');
        $msg_id = $this->input->post('msg_id');
        $content = $this->input->post('content');
        $subjMsg = $this->input->post('subjMsg');
        $subj_id = $this->input->post('subj_id');
        $arrUpload = $this->input->post('arrUpload');
        $accType = $this->input->post('accType');
        $uploads = explode(',', $arrUpload);

        $r = $this->messages_model->replyMsg(base64_decode($sender), $recipient, $msg_id, $content, $subjMsg, $subj_id, $arrUpload);
        if ($r):
            $file = strtotime(date('Y-m-d'));
            $source = 'uploads/temp/';
            $destination = UPLOADPATH . $this->session->school_year . DIRECTORY_SEPARATOR . ($accType != 5 ? 'faculty' : 'students') . DIRECTORY_SEPARATOR . base64_decode($sender) . DIRECTORY_SEPARATOR . 'subjects' . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR;
            if (!is_dir($destination)):
                mkdir($destination, 0777, TRUE);
            endif;
            foreach ($uploads as $u):
//                $file = strtotime(date('Y-m-d')) . ' - ' . $u;
                copy($source . $u, $destination . $u);
                unlink(FCPATH . $source . $u);
            endforeach;
            echo 'Message Sent!!!';
        else:
            echo 'Message Sending Failed';
        endif;
    }

    function getSender($id) {
        return $this->messages_model->getSender(base64_decode($id));
    }

    function getRecipients($msg_id) {
        return $this->messages_model->getRecipients($msg_id);
    }

    function getUnreadMsg($id, $subj_id) {
        return $this->messages_model->getUnreadMsg(base64_decode($id), $subj_id);
    }

    function uploadForm() {
        echo $this->load->view('opl/messages/upload');
    }

    function upload_files() {
        if (isset($_FILES['files']) && !empty($_FILES['files'])) {
            $no_files = count($_FILES["files"]['name']);
            for ($i = 0; $i < $no_files; $i++) {
                if ($_FILES["files"]["error"][$i] > 0) {
                    echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
                } else {
                    if (file_exists('uploads/temp/' . $_FILES["files"]["name"][$i])) {
                        echo 'File already exists : uploads/temp/' . $_FILES["files"]["name"][$i];
                    } else {
                        move_uploaded_file($_FILES["files"]["tmp_name"][$i], 'uploads/temp/' . $_FILES["files"]["name"][$i]);
                        echo 'File successfully uploaded : uploads/temp/' . $_FILES['files']['name'][$i] . ' ';
                    }
                }
            }
        } else {
            echo 'Please choose at least one file';
        }
    }

    function getRecipientOpt($section) {
        $result = $this->messages_model->getRecipientOpt(base64_decode($section));
        foreach ($result->result() as $r):
            echo '<option>' . $r->firstname . '</option>';
        endforeach;
    }

    function downloadFile($filename, $accType, $sender, $sent) {
        $name = base64_decode($filename);
        if ($name):
            $file = UPLOADPATH . $this->session->school_year . DIRECTORY_SEPARATOR . ($accType == 5 ? 'students' : 'faculty') . DIRECTORY_SEPARATOR . base64_decode($sender) . DIRECTORY_SEPARATOR . 'subjects' . DIRECTORY_SEPARATOR . $sent . DIRECTORY_SEPARATOR . $name;

            if (file_exists($file)) {
                // get file content
                $data = file_get_contents($file);
                //force download
                force_download($name, $data);
            } else {
                echo "file doesn't exist";
                // Redirect to base url
//                redirect(base_url());
            }
        endif;
    }

}
