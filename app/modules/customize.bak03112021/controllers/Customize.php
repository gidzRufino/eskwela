<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of academic
 *
 * @author genesis
 */
class customize extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('Pdf');
        $this->load->library('csvimport');
        $this->load->library('csvreader');
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->model('customize_model');
    }
    
    public function printReportCard($short_name, $student, $admitted, $school_year, $grading){
        $data['short_name'] = $short_name;
        $data['admitted'] = $admitted;
        $data['term'] = $grading;
        $data['student'] = $student;
        $data['sy'] = $school_year;
        $this->load->view($short_name . '/reportCard', $data);
    }
    
    public function generateReportCard($short_name, $student, $term, $behavior, $core, $school_year){
        $data['core_val'] = $core;
        $data['behavior'] = $behavior;
        $data['term'] = $term;
        $data['short_name'] = $short_name;
        $data['student'] = $student;
        $data['sy'] = $school_year;
        $this->load->view($short_name . '/generateReportCard', $data);
    }
    
    function getBHrate(){
        $data['stid'] = $this->input->post('stid');
        $data['term'] = $this->input->post('term');
        $data['sy'] = $this->input->post('sy');
        $id = $this->input->post('id');
        $short_name = $this->input->post('short_name');
        $data['bhrate'] = Modules::run('reports/getBhRate', $id);
        $this->load->view($short_name . '/displayCoreValues', $data);
    }
    
}
