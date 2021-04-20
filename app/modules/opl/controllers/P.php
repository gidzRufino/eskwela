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
class P extends MX_Controller {
    //put your code here
    
    
    function __construct() {
        parent::__construct();
        $this->load->model('p_model');
        $this->load->model('opl_model');
    }

    private function post($name) {
        return $this->input->post($name);
    }
    
    public function discussionBoard($school_year = NULL, $grade_level = NULL, $section = NULL, $subject = NULL) {
        $classDetails = json_decode(Modules::run('opl/opl_variables/getClassDetails', $grade_level, $section, $subject, $school_year));
        $data = array(
            'isClass' => FALSE,
            'school_year' => $school_year,
            'gradeDetails' => $classDetails->basicInfo,
            'subjectDetails' => $classDetails->subjectDetails,
            'headerTitle' => 'Discussion Board',
            'main_header' => '',
            'title' => 'e-sKwela Online Platform for Learning',
            'modules' => 'opl',
            'main_content' => 'parent/discussionBoard',
            'grade_level'   =>  $grade_level,
            'section_id'    =>  $section,
            'subject_id'    =>  $subject
        );
        $data['discussionDetails'] = $this->p_model->getDiscussionBoard($school_year, $grade_level, $section, $subject);
        
        echo Modules::run('templates/opl_content', $data);
    }
    
    
    public function uploadPaymentReceipt()
    {       
            
            $school_year = $this->post('school_year');
            $paymentRemarks = $this->post('payment_remarks');
            $paymentCenter = $this->post('paymentCenter');
            $semester = $this->post('semester');
            $st_id = $this->post('st_id');
            $file = $this->post('userfile');
            $data['error'] = ''; //initialize image upload error array to empty

            $config['upload_path'] = 'uploads/'.$school_year.DIRECTORY_SEPARATOR.'students'.DIRECTORY_SEPARATOR. base64_decode($st_id).DIRECTORY_SEPARATOR.'online_payments';
            if(!is_dir($config['upload_path'].'/')):
                mkdir($config['upload_path'],0777,TRUE);
            endif;
            $config['overwrite'] = FALSE;
            $config['allowed_types'] = '*';
            $config['max_size'] = '3072';
            $config['maintain_ratio'] = TRUE;  
            $config['quality'] = '50%';  
            $config['width'] = 200;  
            $this->load->library('upload', $config);

             // If upload failed, display error
            if (!$this->upload->do_upload()) {
                $data['error'] = $this->upload->display_errors();
                //print_r($data);
               echo $config['upload_path'].'/';
                //$this->load->view('csvindex', $data);
            } else {
                $file_data = $this->upload->data();
                
                //print_r($file_data);
                $ext = $file_data['file_ext'];
                $link = $config['upload_path'].'/'.$file;
                if($this->p_model->savePaymentReceipt(base64_decode($st_id),$link, $file_data['file_name'], $paymentCenter, $paymentRemarks, $school_year, $semester)):
                    echo 'Thank you for uploading. We will just notify you once your payment is confirm.';
                    if($this->session->department==4):
                        $student = $this->p_model->getStudentDetails(base64_decode($st_id), $semester, $school_year);
                    else:
                        $student = $this->p_model->getStudentDetailsBasicEd(base64_decode($st_id), $semester, $school_year);
                    endif;
                    
                    $remarks = $student->firstname.' '.$student->lastname.' has uploaded a payment receipt to the system';
                    Modules::run('notification_system/systemNotification', 5, $remarks);
                endif;
            }
        
    }
    
    public function getPadalaCenters($school_year)
    {
        return $this->p_model->getPadalaCenters($school_year);
    }
    
    function getFinanceAccounts()
    {
        $data['headerTitle'] = 'Finance Account';
        $data['child_links'] = $this->session->child_links;
        $data['main_content'] = 'parent/financeRecords';
        $data['modules'] = 'opl';
        echo Modules::run('templates/opl_content', $data);
        
    }
    
    function requestEntry()
    {
        $uname = $this->post('u');
        $pass = $this->post('p');
        
        $request = $this->p_model->requestEntry($uname);
        
        $isVerified = password_verify($pass, $request->pword);
        if($isVerified):
            $parentDetails = $this->p_model->parentDetails($request->parent_id);
            $this->setParentData($parentDetails);
            echo json_encode(array('url' => 'opl/p/dashboard', 'status' => true,'msg' => 'Access Granted'));
        else:
            echo json_encode(array('url' => 'entrance', 'status' => false,'msg' => 'Access Denied'));
        endif;
        
        
    }
    
    function classBulletin($gradeLevel, $section_id, $school_year)
    {
        $data['school_year'] = $school_year;
        $data['sectionDetails'] = Modules::run('opl/opl_variables/getSectionDetails', $gradeLevel, $section_id, $school_year);
        $data['headerTitle'] = $data['sectionDetails']->level.' - '.$data['sectionDetails']->section;
        $data['main_content'] = 'parent/subjectDashboard';
        $data['modules'] = 'opl';
        echo Modules::run('templates/opl_content', $data);
    }
    
    function getSingleStudent($st_id, $school_year = NULL, $sem = NULL)
    {
        $student = $this->p_model->getBasicStudent($st_id, $school_year,$semester);
        return $student;
    }
    
    function myChildren()
    {
        $this->load->view('parent/widgets/student');
    }
    
    public function getPost() {
        $postDetails = $this->opl_model->getPost();
        return $postDetails;
    }
    
    function dashboard()
    {
        $data = array(
            'grade_level' => '',
            'section_id' => '',
            'subject_id' => '',
            'subjectDetails' => [],
            'post' => $this->getPost(),
            'isClass' => FALSE,
            'title' => 'Parent Dashboard',
            'headerTitle' => 'Parent Dashboard - School Bulletin',
            'main_header' => '',
            'main_content' => 'parent/default',
            'modules' => 'opl',
        );

        echo Modules::run('templates/opl_content', $data);
    }
    
    function setParentData($details)
    {
        $data = array(
            'is_logged_in'  => TRUE,
            'isParent'      => TRUE,
            'username'      => $details->uname,
            'user_id'       => $details->u_id,
            'basicInfo'     => $details,
            'usertype'      => 4,
            'father'        => $details->f_firstname.' '.$details->f_lastname,
            'mother'        => $details->m_firstname.' '.$details->m_lastname,
            'child_links'   => $details->child_links,
            'parent_id'     => $details->parent_id,
            'school_year'   => $this->eskwela->getSet()->school_year

        );
        $this->session->set_userdata($data);
        return;
    }
    
    function verifyOTP()
    {
        $parent_id = $this->post('parent_id');
        $otp = $this->post('otp');
        $parentDetails = $this->p_model->parentDetails($parent_id);
        $db_code = $parentDetails->verify_code;
        //print_r($parentDetails);
        $codeIsVerified = password_verify($otp, $db_code);
        if(!$codeIsVerified):
            echo json_encode(array('url' => 'entrance', 'status' => false, 'msg' => 'Sorry, You have entered a wrong OTP'));
        else:
            $this->setParentData($parentDetails);
            echo json_encode(array('url' => 'opl/p/dashboard', 'status' => true));
        endif;
    }
    
    function registerParent()
    {
        $details = $this->post('details');
        $u = $this->post('u');
        $p = $this->post('p');
        
        $pword = password_hash($p, PASSWORD_DEFAULT, ['cost' => 8]);
        
        $verify_code = $this->eskwela->code();
        
        $childLinks = '';
        $number = '';
        foreach ($details as $d):
            $childLinks .= $d['st_id'].',';
            $number = $d['ice_contact'];
            $parent_id = $d['p_id'];
        endforeach;
        
        $childIDs = rtrim($childLinks,',');
        
        $uaDetails = array(
            'u_id'          => $parent_id,
            'uname'         => $u,
            'pword'         => $pword,
            'utype'         => 4,
            'if_p'          => 1,
            'child_links'   => $childIDs,
            'parent_id'     => $parent_id,
            'verify_code'   => password_hash($verify_code, PASSWORD_DEFAULT, ['cost' => 8])
        );
        
        $msg = 'Please enter this verification code to the system : '. $verify_code;

        if($this->sendSMS($number, $msg)):
            $result = json_decode($this->p_model->registerParent($uaDetails, $u, $parent_id, $this->eskwela->getSet()->school_year));
            if($result->status):
                if(count($details) > 1):
                    $this->p_model->updateParentId($number, $parent_id, $this->eskwela->getSet()->school_year);
                endif;
                    echo json_encode(array('status' => true, 'msg' => $result->msg, 'parent_id' => $parent_id));

            else:
                echo json_encode(array('status' => false, 'msg' => $result->msg));
            endif;
        else:
            echo json_encode(array('status' => false, 'msg' => 'Something went wrong, Please try again later'));
        endif;
        
        
    }
    
    function verifyParent($data, $sy=NULL)
    {
            $ifVerified = $this->p_model->getVerified($data, ($sy==NULL?$this->eskwela->getSet()->school_year:$sy));

            echo $ifVerified;
    }
    
    
    function sendSMS($number, $msg = NULL)
    {
        if($msg==NULL):
            $msg = $this->post('msg');
        endif;
        $result = json_decode($this->sendConfirmation($number, $msg));
        $try = 0;
        $resultStatus = $result->status;

        while($resultStatus!=1):
            $try++;
            $result = json_decode($this->sendConfirmation($number, $msg));
            if($try==10):
                    echo 'Sorry Something went wrong, Please try again later';
                break;

            endif;
            $resultStatus = $result->status;
        endwhile;

        if($resultStatus):
            return true;
        else:
            return false;
        endif;
        
        
    }
    
    public function sendConfirmation($number=NULL, $msg=NULL) {
        $api = Modules::run('main/getSet');
        
        $message = strtoupper($api->short_name) . ' SMS: ' . urldecode($msg);
        $apicode = $api->apicode;
        $apipass = $api->api_pass;

        $result = $this->eskwela->itexmo($number, $message, $apicode, $apipass);
        if ($result == "") {
            $msg = "No server response";
            $stat = 0;
            $result = $msg;
        } else if ($result == 0) {
            $msg = "Message sent";
            $stat = 1;
            $result = $msg;
        } else {
            $msg = "error encountered";
            $stat = 2;
            $result = $msg;
        }
        return json_encode(array('number' => $number, 'msg' => $msg, 'status' => $stat));
    }
    
    public function getAcademicRecords() {
        $data['settings'] = Modules::run('main/getSet');
        $data['headerTitle'] = 'Academic Records';
        $data['title'] = 'Academic Records';
        $data['main_header'] = '';

        $data['child_links'] = $this->session->child_links;
        $data['modules'] = 'opl';
        switch ($data['settings']->short_name):
            case 'wiserkidz':
                $data['main_content'] = 'parent/' . $data['settings']->short_name . '_academicRecord';
                break;
            default :
                $data['main_content'] = 'parent/academicRecord';
                break;
        endswitch;

        echo Modules::run('templates/opl_content', $data);
    }
}
