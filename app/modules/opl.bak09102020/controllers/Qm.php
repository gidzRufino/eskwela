<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Qm
 *
 * @author genesisrufino
 */
class Qm extends MX_Controller {
    //put your code here
    
    public function __construct() {
        parent::__construct();
        $this->load->model('qm_model');
        if(!$this->session->is_logged_in):
            redirect('login');
        endif;
        
    }
    
    private function post($name)
    {
        return $this->input->post($name);
    }
    
    function index()
    {
        
    }


    function getAnswerKey($q_code, $school_year)
    {
        $ans = $this->qm_model->checkAnswer($q_code, $school_year);
        return $ans->qq_answer;
    }
    
    function removeQuizItem()
    {
        $quiz_id = $this->post('quiz_id');
        $quiz_items = $this->post('quiz_items');
        $school_year = $this->post('school_year');
        
        $qDetails = array(
            'qi_qq_ids' => $quiz_items
        );
        
        if($this->qm_model->removeQuizItems($qDetails, $quiz_id, $school_year)):
            echo 'Successfully Removed';
        else:
            echo 'Something went wrong, Please try again later.';
        endif;
    }
    
    function saveRawScore()
    {
        $assess_id = $this->post('task_code');
        $st_id = $this->post('st_id');
        $score = $this->post('score');
        $ans_id = $this->post('ans_id');
        
        
        $rawScoreDetails = array(
            'raw_id'    => $ans_id,
            'st_id'     => $st_id,
            'raw_score' => $score,
            'assess_id' => $assess_id,
        );
        $result = $this->qm_model->saveRawScore($rawScoreDetails, $assess_id, $st_id, $this->session->school_year);
        if($result==0):
            echo 'Sorry Something went wrong';
        elseif($result==1):
            echo 'Score was saved successfully';
        else:
            echo 'Score was updated successfully';
        endif;
    }
    
    function getRawScore($assess_id, $st_id, $school_year)
    {
        return $this->qm_model->getRawScore($assess_id, $st_id, $school_year);
    }
    
    function checkAnswer($answer, $q_code, $school_year)
    {
        $ans = $this->qm_model->checkAnswer($q_code, $school_year);
        
        if(trim(strtolower($answer))== trim(strtolower($ans->qq_answer))):
            return TRUE;
        else:
            return FALSE;
        endif;
    }
    
    function searchAQuiz($value, $school_year)
    {
        $quiz = $this->qm_model->searchAQuiz($value, $school_year);
        echo '<ul>';
        foreach ($quiz as $q):
        ?>
           <li style="font-size:18px;" data-dismiss="modal" onclick="$('#searchQuestions').hide(), $('#searchBox').val(&quot;<?php echo $q->qi_title ?>&quot;), $('#quiz_id').val('<?php echo $q->qi_sys_code ?>')" ><?php echo $q->qi_title ?></li>   
        <?php        
        endforeach;
        echo '</ul>';
        
    }
  
    function getSingleQuestion($sys_code, $school_year, $quiz_id = NULL)
    {
        $question = $this->qm_model->getSingleQuestion(base64_decode($sys_code), $school_year);
        if($quiz_id!=NULL):
            $qq_id = $this->qm_model->getQQids($quiz_id);
            if($qq_id->qi_qq_ids==""):
                $quizItems = array('qi_qq_ids' => base64_decode($sys_code));
            else:
                $quizItems = array('qi_qq_ids' => $qq_id->qi_qq_ids.','.base64_decode($sys_code));
            endif;
            $this->qm_model->updateQuiz($quizItems, $quiz_id);
        endif;
        
        echo $question->question;
    }
    
    function searchQuestions($value, $school_year)
    {
        $questions = $this->qm_model->searchQuestions(urldecode($value), $school_year);
        echo '<ul>';
        foreach ($questions as $q):
        ?>
           <li style="font-size:18px;" data-dismiss="modal" onclick="$('#searchQuestions').hide(), $('#searchBox').val(&quot;<?php echo $q->plain_question ?>&quot;), getSingleQuestion('<?php echo base64_encode($q->sys_code) ?>')" ><?php echo $q->plain_question ?></li>   
        <?php        
        endforeach;
        echo '</ul>';
    }
    
    function getQuestionItems($itemCode, $school_year)
    {
        $questionItems = $this->qm_model->getQuestionItems($itemCode, $school_year);
        return $questionItems;
    }
    
    function getQuizDetails($quizCode, $school_year)
    {
        return $this->qm_model->getQuizDetails($quizCode, $school_year);
    }
    
    function quizDetails($quizCode, $school_year)
    {
        $questionsDetails = array(
            'isClass'           => FALSE,
            'quizDetails'       => $this->qm_model->getQuizDetails($quizCode, $school_year),
            'school_year'       => $school_year,
            'grade_level'       => [],
            'subject_id'        => [],
            'gradeDetails'      => [],
            'subjectDetails'    => [],
            'getSubjects'       => Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year),
            'quiz_type'         => $this->qm_model->getQuizType(),
            'headerTitle'       => 'Assessment Details',
            'main_header'       => '',
            'title'             => 'e-sKwela Online Platform for Learning',
            'modules'           => 'opl',
            'main_content'      => 'qm/quizDetails'
        );
        
        echo Modules::run('templates/opl_content', $questionsDetails);
    }
    
    function deleteQuiz()
    {
        $quizCode = $this->post('quizCode');
        $school_year = $this->post('school_year');
        
        if($this->qm_model->deleteQuiz($quizCode, $school_year)):
            echo 'Successfully Deleted';
        else:
            echo 'Something went wrong, please try again later or contact support@thecsscore.com';
        endif;
    }
    
    function getAllQuizzes($teacher_id, $school_year)
    {
        $quizes = $this->qm_model->getAllQuizzes($teacher_id, $school_year);
        return $quizes;
    }
            
    function deleteQuestion()
    {
        $school_year = $this->post('school_year');
        if($this->qm_model->deleteQuestion($this->post('sys_code'), $school_year)):
            echo 'Successfully Deleted';
        else:
            echo 'Something went wrong, please try again later or contact support@thecsscore.com';
        endif;
    }
    
    function questionsList($teachers_id, $school_year = NULL)
    {
        $questionsDetails = array(
            'questions'         => $this->qm_model->questionsList(base64_decode($teachers_id), $school_year),
            'isClass'           => FALSE,
            'school_year'       => $school_year,
            'grade_level'       => [],
            'subject_id'        => [],
            'gradeDetails'      => [],
            'subjectDetails'    => [],
            'headerTitle'       => 'Questions Bank',
            'main_header'       => '',
            'title'             => 'e-sKwela Online Platform for Learning',
            'modules'           => 'opl',
            'main_content'      => 'qm/questionsList'
        );
        
        echo Modules::run('templates/opl_content', $questionsDetails);
        
    }
    
    
    function create($school_year)
    {
        $data = array(
            'isClass'           => FALSE,
            'school_year'       => $school_year,
            'grade_level'       => [],
            'subject_id'        => [],
            'gradeDetails'      => [],
            'subjectDetails'    => [],
            'headerTitle'       => 'Create a Quiz ',
            'quiz_type'         => $this->qm_model->getQuizType(),
            'getSubjects'       => Modules::run('opl/opl_widgets/mySubject', $this->session->username, $school_year),
            'main_header'       => '',
            'title'             => 'e-sKwela Online Platform for Learning',
            'modules'           => 'opl',
            'main_content'      => 'qm/create'
        );
        
        echo Modules::run('templates/opl_content', $data);
    }
    
    function createQuiz()
    {
        $quizSystemCode = $this->eskwela->code();
                
        $subGradSec = explode('-', $this->post('subGradeSec'));
        $quizItems = array(
            'qi_grade_level_id'  => $subGradSec[1],
            'qi_section_id'      => $subGradSec[2],
            'qi_qq_ids'          => '',
            'qi_date_created'    => date('Y-m-d g:i:s'),
            'qi_created_by'      => $this->session->username,
//            'qi_date_activation' => $this->post('startDate'),
//            'qi_date_expired'    => $this->post('endDate'),
            'qi_title'           => $this->post('quizTitle'),
            'qi_sys_code'        => $quizSystemCode
        );
        if($this->qm_model->addQuiz($quizItems)):
            echo json_encode(array('quiz_code'=> $quizSystemCode));
        endif;
        
    }
            
    function addQuestions()
    {
       $sysCode = $this->post('q_code');
       $details = array(
           'question'       => $this->post('question'),
           'plain_question' => $this->post('plain_q'),
           'qq_answer'      => $this->post('answer'),
           'qq_qt_id'       => $this->post('qtype'),
           'created_by'     => $this->session->username,
           'sys_code'       => $sysCode
       );
       
       if($this->qm_model->addQuestions($details)):
            if($this->post('quizTitle')!=""):
                if($this->post('quiz_id')==0):
                    $quizSystemCode = $this->eskwela->code();
                
                    $subGradSec = explode('-', $this->post('subGradeSec'));
                    $quizItems = array(
                        'sys_code'           => $quizSystemCode,
                        'qi_grade_level_id'  => $subGradSec[1],
                        'qi_section_id'      => $subGradSec[2],
                        'qi_qq_ids'          => $sysCode,
                        'qi_date_created'    => date('Y-m-d g:i:s'),
                        'qi_created_by'      => $this->session->username,
                        'qi_date_activation' => $this->post('startDate'),
                        'qi_date_expired'    => $this->post('endDate'),
                        'qi_title'           => $this->post('quizTitle'),
                        'qi_sys_code'        => $quizSystemCode
                    );
                    $this->qm_model->addQuiz($quizItems);
                else:
                    $quizSystemCode = $this->post('quiz_id');
                    $qq_id = $this->qm_model->getQQids($quizSystemCode);
                    if($qq_id->qi_qq_ids==""):
                        $quizItems = array('qi_qq_ids' => $sysCode);
                    else:
                        $quizItems = array('qi_qq_ids' => $qq_id->qi_qq_ids.','.$sysCode);
                    endif;
                    $this->qm_model->updateQuiz($quizItems, $quizSystemCode);
                endif;
            endif;    
           echo json_encode(array('sysCode' => $sysCode, 'quizCode' => $quizSystemCode));
       else:
           echo 'Sorry Something went wrong';
       endif;
        
    }
    
    function loadCode()
    {
        echo $this->eskwela->code();
    }
}
