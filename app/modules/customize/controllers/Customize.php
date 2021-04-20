<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

    public function printReportCard($short_name, $student, $admitted, $school_year, $grading) {
        $data['short_name'] = $short_name;
        $data['admitted'] = $admitted;
        $data['term'] = $grading;
        $data['student'] = $student;
        $data['sy'] = $school_year;
        $this->load->view($short_name . '/reportCard', $data);
    }

    public function generateReportCard($short_name, $student, $term, $behavior, $core, $school_year) {
        $data['core_val'] = $core;
        $data['behavior'] = $behavior;
        $data['term'] = $term;
        $data['short_name'] = $short_name;
        $data['student'] = $student;
        $data['sy'] = $school_year;
        $this->load->view($short_name . '/generateReportCard', $data);
    }

    function getBHrate() {
        $data['stid'] = $this->input->post('stid');
        $data['term'] = $this->input->post('term');
        $data['sy'] = $this->input->post('sy');
        $id = $this->input->post('id');
        $short_name = $this->input->post('short_name');
        $data['bhrate'] = Modules::run('reports/getBhRate', $id);
        $this->load->view($short_name . '/displayCoreValues', $data);
    }

    function eligForm($short_name, $stid) {
        $data['school'] = $this->customize_model->getPreSkulInfo(base64_decode($stid));
        $this->load->view($short_name . '/eligForm', $data);
    }

    function printF137($data) {
        $short_name = $data['settings']->short_name;
        $this->load->view($short_name . '/gs_frontpage', $data);
    }

    function getPreSchoolInfo($stid) {
        return $this->customize_model->getPreSkulInfo(base64_decode($stid));
    }

    function cotMsg($name, $level) {
        ?>
        <h3>TO WHOM IT MAY CONCERN:</h3>
        <p style="text-indent: 10em; font-size: 14px">This is to certify that this is a true <b>Elementary School Record</b> of <u>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $name ?>&nbsp;&nbsp;&nbsp;&nbsp;</u>.</p>
        <p style="font-size: 14px">He/She is eligible for transfer and admission to <b><u>&nbsp;&nbsp;&nbsp;<?php echo $level ?>&nbsp;&nbsp;&nbsp;&nbsp;</u></b></p>
        <?php
    }

}
