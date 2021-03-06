<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of main
 *
 * @author genesis 
 */
class Main extends MX_Controller {

    //put your code here
    function __construct() {
        parent::__construct();
        $this->load->model('main_model');
        $this->load->library('Mobile_Detect');
        $this->load->library('audit_trail');
        //$device = new Mobile_Detect();
    }

    function updateSchoolDates(){
        $schoolid = $this->input->post('school');
        ($this->input->post('type') == 1) ? $data['bosy'] = $this->input->post('date') : $data['eosy'] =$this->input->post('date');
        if($this->main_model->updateSchoolDates($schoolid, $data) == TRUE):
            echo json_encode(array('status'=>1,'msg'=>"You have successfully update the school dates"));
        else:
            echo json_encode(array('status'=>0,'msg'=>"Something went wrong. Please try again or contact an administrator if the problem persists."));
        endif;
    }

    function hasPk($table) {
        $fields = $this->db->field_data($table);
        $key = 0;
        foreach ($fields as $field) {
            $key += $field->primary_key;
        }
        if ($key > 0):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function getAutoIncColumn($table) {
        $sql = $this->db->query("show columns from $table where extra like '%auto_increment%'");

        return $sql->row();
    }

    function dbUpdate($school_year) {
        $this->db = $this->eskwela->db($school_year);
        $tables = $this->db->list_tables();
        foreach ($tables as $table):
            echo $table . ' - ';
            $field = $this->getAutoIncColumn($table);
            if ($field):
                $column1 = $field->Field;
                if ($table != "esk_profile" || $table != "esk_c_section" || $column1 != ""):
                    $this->executeUpdate($table, $column1);
                endif;
            endif;
            echo '<br />';
        endforeach;
    }

    function executeUpdate($table, $column1, $school_year =NULL) {
        $this->db = $this->eskwela->db(($school_year==NULL?$this->eskwela->getSet()->school_year:$school_year));
        if ($this->db->table_exists($table)) {
            $column2 = $table . '_code';
            $query1 = "ALTER TABLE $table ADD $column2 INT NOT NULL COMMENT 'new auto_inc';";
            $query7 = "UPDATE $table SET $table.$column2 = $table.$column1;";
            $query2 = "ALTER TABLE `$table` ADD UNIQUE(`$column1`)";
            $query3 = "ALTER TABLE $table DROP PRIMARY KEY, ADD PRIMARY KEY($column2);";
            $query5 = "ALTER TABLE `$table` CHANGE `$column1` `$column1` varchar(55) NOT NULL; ";
            $query6 = "ALTER TABLE `$table` CHANGE `$column2` `$column2` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'new auto_inc';";


            if ($this->db->query($query1)):
                if ($this->db->query($query7)):
                    if ($this->hasPk($table)):
                        if ($this->db->query($query2)):
                            if ($this->db->query($query3)):
                                if ($this->db->query($query5)):
                                    if ($this->db->query($query6)):
                                        echo 'All Queries are successfully Generated';
                                    endif;
                                endif;
                            endif;
                        else:
                            if ($this->db->query($query2)):
                                if ($this->db->query($query5)):
                                    if ($this->db->query($query6)):
                                        echo 'All Queries are successfully Generated';
                                    endif;
                                endif;
                            endif;

                        endif;
                    endif;
                endif;
            endif;
        }
    }

    function editTimeSettings($inAM, $outAM, $inPM, $outPM, $id, $option) {
        $result = $this->main_model->editTimeSettings($inAM, $outAM, $inPM, $outPM, $id, $option);
        if ($result > 0):
            $data = array('msg' => 'Time Successfuly Updated');
        else:
            $data = array('msg' => 'An Error Occured During Update');
        endif;
        echo json_encode($data);
    }

    function getTimeSettingsPerSection() {
        return $this->main_model->getTimeSettingsPerSection();
    }

    function editQuarterSettings($from, $to, $id) {
        $holidays = Modules::run('calendar/getHolidaysPerQuarter', $from, $to);
        $start = strtotime($from);
        $end = strtotime($to);
        if ($start > $end):
            echo 'Start date is in the future!';
        else:
            $no_days = 0;
            $weekends = 0;
            while ($start <= $end):
                $no_days++;
                $what_day = date('N', $start);
                if ($what_day > 5):
                    $weekends++;
                endif;
                $start += 86400;
            endwhile;
            $working_days = $no_days - $weekends;
            return $this->main_model->editQuarterSettings($from, $to, $id, $holidays, $working_days);
        endif;
    }

    function getQuarterSettingsByID($id) {
        return $this->main_model->getQuarterSettingsByID($id);
    }

    private function system_version() {
        $version = $this->main_model->system_version();
        echo $version;
    }

    public function checkVersion($version = NULL) {
        $this->load->library('eskwelaupdates');
        if ($version == NULL):
            do {
                do {
                    $version = file_get_contents('http://eskwelacampus.com/main/currentUpdate');
                    $head = $this->eskwelaupdates->parseHeaders($http_response_header);
                } while ($head != '200');

                $currentVersion = $this->system_version();
                $v = version_compare($currentVersion, $version);
            } while ($v > 0);
        endif;

        switch ($v):
            case -1:
                $this->updateSystem($version);
                break;
            case 0:
                echo 'System is up to date [ ' . $currentVersion . ' ]';
                break;
        endswitch;
    }

    function refreshIdGeneration() {
        $status = $this->main_model->refreshIdGeneration();
        return $status;
        //echo ($status?'Successfully Refreshed':'Id is still Active');
    }

    function updateSystem($version = NULL) {
        $this->load->library('eskwelaupdates');
        $this->load->helper('directory');
        $currentVersion = $this->system_version();
        $content = json_decode($this->eskwelaupdates->getFiles(str_replace('.', '', $version)));
        foreach ($content->data as $key => $value) {
            $raw = file_get_contents($content->raw . $value);
            file_put_contents($content->file_path . $value, $raw);
        }

        $map = directory_map($content->file_path);
        foreach ($map as $key => $value) {
            $name = explode('_', $value);
            $mod_name = strtolower($name[0]);
            $class_name = $name[1];
            $filename = str_replace('.txt', '', $name[2]);
            rename($content->file_path . $value, APPPATH . 'modules/' . $mod_name . '/' . $class_name . '/' . $filename);
            if (chmod(APPPATH . 'modules/' . $mod_name . '/' . $class_name . '/' . $filename, 0777)):
                echo 'modules/' . $mod_name . '/' . $class_name . '/' . $filename . ' successfully updated';
            endif;
        }
        $sysdetails = array('system_version' => $version);
        $this->main_model->saveSystemUpdate($sysdetails);
    }

    private function getComponent($component) {
        $this->db->where('component', $component);
        $comp = $this->db->get('gs_component');
        return $comp;
    }

    public function updateAssessCat() {
        $cat = $this->db->get('gs_asses_category');
        $catResult = $cat->result();

        foreach ($catResult as $catR):
            $comp = $this->getComponent($catR->category_name);
            if ($comp->num_rows() > 0):
                $upCompDetails = array('component_id' => $comp->row()->id);

                $this->db->where('code', $catR->code);
                if ($this->db->update('gs_asses_category', $upCompDetails)):
                    echo $catR->subject_id . ' - ' . $comp->row()->component . ' is updated.<br />';
                endif;
            else:
                echo $catR->subject_id . '<br />';
            endif;
        endforeach;
    }

    public function detect_column($table, $column, $data = NULL) {
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'";
        $q = $this->db->query($sql);
        if ($q->num_rows() > 0):
            return TRUE;
        else:
            $update = "ALTER TABLE $table ADD $column INT NOT NULL ;";
            if ($this->db->query($update)):
                $upd = ($data == NULL ? array($column => 0) : $data);
                $this->db->update($table, $upd);
                return TRUE;
            else:
                return false;
            endif;

        endif;
    }

    function activateRfid() {
        $data['modules'] = 'main';
        $data['main_content'] = 'activateRfid';
        echo Modules::run('templates/main_content', $data);
    }

    function showLRNUPload() {
        $data['modules'] = 'main';
        $data['main_content'] = 'lrnUpload';
        echo Modules::run('templates/main_content', $data);
    }

    function uploadRFID() {
        //load library phpExcel
        $this->load->library("excel");
        //here i used microsoft excel 2007
        $data['error'] = ''; //initialize image upload error array to empty

        $config['upload_path'] = 'uploads';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        // If upload failed, display error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();
            print_r($data);
            //$this->load->view('csvindex', $data);
        } else {

            $file_data = $this->upload->data();
            $file_path = 'uploads/' . $file_data['file_name'];
            switch ($file_data['file_ext']):
                case '.xls':
                case '.XLS':
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                    break;
                case '.xlsx':
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    break;
            endswitch;
            //set to read only
            $objReader->setReadDataOnly(true);
            //load excel file
            $objPHPExcel = $objReader->load($file_path);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);


            $num_rows = $objWorksheet->getHighestRow();
            $i = 1;
            ?>

            <?php
            for ($st = 2; $st <= ($num_rows); $st++) {

                $st_id = $objWorksheet->getCellByColumnAndRow(0, $st)->getValue();
                $rfid = $objWorksheet->getCellByColumnAndRow(7, $st)->getValue();
                if ($rfid != NULL):
                    $details = array('rfid' => $rfid);
                    if ($this->main_model->editProfile('profile_students.st_id', $st_id, $details)):
                        $i++;
                    else:
                        echo $objWorksheet->getCellByColumnAndRow(8, $st)->getValue() . ' is not updated <br />';
                    endif;
                endif;
            }
            echo $i . ' number of students updated';
        }
    }

    function updateLRN() {
        //load library phpExcel
        $this->load->library("excel");
        //here i used microsoft excel 2007
        $data['error'] = ''; //initialize image upload error array to empty

        $config['upload_path'] = 'uploads';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['max_size'] = '10000';

        $this->load->library('upload', $config);

        // If upload failed, display error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();
            print_r($data);
            //$this->load->view('csvindex', $data);
        } else {

            $file_data = $this->upload->data();
            $file_path = 'uploads/' . $file_data['file_name'];
            switch ($file_data['file_ext']):
                case '.xls':
                case '.XLS':
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                    break;
                case '.xlsx':
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    break;
            endswitch;
            //set to read only
            $objReader->setReadDataOnly(true);
            //load excel file
            $objPHPExcel = $objReader->load($file_path);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(1);


            $num_rows = $objWorksheet->getHighestRow();
            $i = 1;
            ?>

            <?php
            for ($st = 1; $st <= ($num_rows); $st++) {

                $lrn = $objWorksheet->getCellByColumnAndRow(0, $st)->getValue();
                $st_id = $objWorksheet->getCellByColumnAndRow(7, $st)->getValue();
                if ($lrn != NULL):
                    $details = array('lrn' => $lrn);
                    if (Modules::run('registrar/editStudentInfo', $st_id, $details)):
                        $i++;
                    else:
                        echo $objWorksheet->getCellByColumnAndRow(8, $st)->getValue() . ' is not updated <br />';
                    endif;
                endif;
            }
            echo $i . ' number of students updated';
        }
    }

    function logActivity($title, $msg, $user_id) {
//        $path = realpath(APPPATH);
//        $this->audit_trail->lfile($path.'/modules/logs/'.$this->session->userdata('user_id').'.txt');
//        $this->audit_trail->lwrite($msg,$title);

        $this->main_model->logActivity($title, $msg, $user_id);
        return;
    }

    function readLog($user_id = NULL) {
        $path = realpath(APPPATH);
        if ($user_id == NULL):
            $data['logs'] = $this->audit_trail->lread($path . '/modules/logs/' . $this->session->userdata('user_id') . '.txt');
        endif;
        $data['modules'] = 'main';
        $data['main_content'] = 'viewLogs';
        echo Modules::run('templates/main_content', $data);
    }

    function viewUpdates() {

        $data['modules'] = "main";
        $data['main_content'] = 'updates';
        echo Modules::run('templates/main_content', $data);
    }

    function writeLog($from, $remarks) {
        $details = array(
            'account_id' => $from,
            'remarks' => $remarks
        );

        $this->main_model->writeLog($details);
    }

    function checkPortal($website) {
        $this->load->library('eskwela');
        $string = explode('/', base_url());
        if ($string[2] != "localhost"):
            $settings = $this->eskwela->getSet();
            $curr_time = strtotime(date('Y-m-d G:i:s'));
            $set_time = strtotime($settings->last_ping);
            $time = round(abs(($curr_time - $set_time) / 60));
            if ($time < 5):
                echo json_encode(array('status' => TRUE, 'msg' => 'Online'));
            else:
                Modules::run('login/clientCheckIn', 0);
                echo json_encode(array('status' => TRUE, 'msg' => 'Offline'));
            endif;
        else:
            if (!$socket = @fsockopen(base64_decode($website), 80, $errno, $errstr, 30)) {

                echo json_encode(
                        array(
                            'status' => FALSE,
                            'msg' => 'Offline'
                        )
                );
            } else {

                echo json_encode(array('status' => TRUE, 'msg' => 'Online'));

                fclose($socket);
            }
        endif;
    }

    function getDevice() {
        $device = new Mobile_Detect();
        return $device;
    }

    function isMobile() {
        $device = new Mobile_Detect();
        if ($device->isMobile()):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function isTablet() {
        $device = new Mobile_Detect();
        if ($device->isTablet()):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function getSemester() {
        if (date('m') == 11 || date('m') == 12 || date('m') == 1 || date('m') == 2 || date('m') == 3):
            $semester = 2;
        elseif (date('m') >= 6 && date('m') <= 10):
            $semester = 1;
        else:
            $semester = 3;
        endif;

        return $semester;
    }

    function index() {
        if($this->session->isStudent):
            redirect("opl/student");
        endif;
        if($this->session->isParent):
            redirect("opl/p/dashboard");
        endif;
        if ($this->isMobile()) {

            if (!$this->session->userdata('is_logged_in')) {
                echo Modules::run('mobile/index');
            } else {
                $this->dashboard();
            }
        } else {
            if (!$this->session->userdata('is_logged_in')) {
                echo Modules::run('login');
            } else {
                $this->dashboard();
            }
        }
    }

    function getMotherTongue() {
        $motherTongue = $this->main_model->getMotherTongue();
        return $motherTongue;
    }

    function getEthnicGroup() {
        $getEthnicGroup = $this->main_model->getEthnicGroup();
        return $getEthnicGroup;
    }

    function getSet() {

        $siteSettings = $this->main_model->getSet();
        return $siteSettings;
    }

    function getReligion() {
        $religion = $this->main_model->getReligion();
        return $religion;
    }

    function getCities($city = NULL) {
        $city = $this->main_model->getCities($city);
        return $city;
    }

    function getProvinces() {
        $provinces = $this->main_model->getProvinces();
        return $provinces;
    }

    function getProvince($id) {
        $province = $this->main_model->getProvince($id);

        echo json_encode(array('name' => $province->province, 'id' => $province->pid));
    }

    function getSectionSettings() {
        $data['grade'] = Modules::run('registrar/getGradeLevel');
        $this->load->view('sectionSettings', $data);
    }

    function getSiteName() {

        $siteSettings = $this->main_model->getSet();
        return $siteSettings->set_school_name;
    }

    function getHolidays() {
        $holidays = $this->main_model->getHolidays();
        return $holidays;
    }

    function dashboard() {
        if($this->session->isStudent):
            redirect("opl/student");
        endif;
        if($this->session->isParent):
            redirect("opl/p/dashboard");
        endif;
        // print_r($this->session->userdata());
        if ($this->isMobile()) {
            if (!$this->session->userdata('is_logged_in')) {
                echo Modules::run('mobile/index');
            } else {
                $data['students'] = Modules::run('registrar/getStudentListForParent');
                $data['modules'] = "mobile";
                $data['main_content'] = 'dashboard';
                echo Modules::run('mobile/main_content', $data);
            }
        } else {
            if ($this->session->userdata('is_logged_in')) {
                $data['settings'] = $this->getSet();
                $data['modules'] = "main";
                switch ($this->session->userdata('position')):
                    case 'Parent':
                        redirect('pp/dashboard');
                        break;
                    case 'Grade School Faculty':
                    case 'High School Faculty':
                    case 'RDPO Director':
                    case 'Faculty':
                        $data['main_content'] = 'faculty_dashboard';
                        echo Modules::run('templates/main_content', $data);
                        break;
                    default :
                        $data['main_content'] = 'dashboard';
                        echo Modules::run('templates/main_content', $data);
                        break;
                endswitch;
            }else {
                echo Modules::run('login');
            }
        }
    }

    function monthName($m_id = NULL) {
        echo date("F", strtotime(date("d-$m_id-y")));
    }

    function getMonthName($m_id = NULL) {
        return date("F", strtotime(date("d-$m_id-y")));
    }

    function getNumberOfSchoolDays($firstDay = NULL, $lastDay = NULL, $month = NULL, $year = NULL) {
        if ($year == NULL):
            $year = date('Y');
        endif;
        $numberOfSchoolDays = 0;
        $monthName = date('F', mktime(0, 0, 0, abs("$month"), 01, $year));
        for ($x = $firstDay; $x <= $lastDay; $x++) {

            if ($month != NULL):
                $day = date('D', strtotime($x . ' ' . $monthName . ' ' . $year));
            else:
                $day = date('D', strtotime(date('F') . '-' . $x . '-' . $year));
            endif;


            if ($day == 'Sat' || $day == 'Sun') {
                
            } else {
                $numberOfSchoolDays++;
            }
        }
        // echo $numberOfSchoolDays;
        return $numberOfSchoolDays;
    }

    function getFirstLastDay($month = null, $year = null, $firstlast = null) {
        $firstLast = date('j', strtotime($firstlast . ' Day of ' . $month . ' ' . $year));
        echo $firstLast;
        return $firstLast;
    }

    function schoolSettings() {
        if ($this->session->userdata('is_logged_in')) {
            $data['settings'] = $this->getSet();
            $settings = $this->getSet();
            $data['gs_settings'] = Modules::run('gradingsystem/getSet', $this->session->userdata('school_year'));
            $next = $settings->school_year + 1;
            $data['sy'] = $settings->school_year . ' - ' . $next;
            $data['modules'] = "main";
            $data['main_content'] = 'settings';
            echo Modules::run('templates/schedule_content', $data);
        } else {
            echo Modules::run('login');
        }
    }

    function quarterSettings() {
        $data['getQuarterSettings'] = $this->main_model->getQuarterSettings();
        $this->load->view('quarterSettings', $data);
    }

    function subjectSettings($grade_level = NULL) {
        $data['GradeLevel'] = Modules::run('registrar/getGradeLevel');
        $data['department'] = Modules::run('coursemanagement/getDepartment');
        $data['subject'] = Modules::run('academic/getSubjects');
        $data['modules'] = "main";
        $data['main_content'] = 'subjectSettings';
        echo Modules::run('templates/main_content', $data);
    }

    function removeSubject() {
        $gradeLevel = $this->input->post('gradeLevel');
        $subject_id = $this->input->post('subject_id');

        if ($this->main_model->removeSubject($subject_id)):
            echo 'Successfully Removed';
        else:
            echo 'Something went Wrong, Sorry...';
        endif;
    }

    function saveSubjectPerLevel() {
        $gradeLevel = $this->input->post('gradeLevel');
        $addedSubjects = $this->input->post('addSubjects');
        $subjects = $this->input->post('subjects');
        $subs = explode(',', $addedSubjects);
        foreach ($subs as $s) {
            $subject_id = Modules::run('academic/getSubjectId', $s);
            if (!$this->main_model->checkSubjectPerLevel($gradeLevel, $subject_id)):
                $details = array(
                    'id'    =>  $this->eskwela->codeCheck('subjects_settings', 'id', $this->eskwela->code()),
                    'grade_level_id' => $gradeLevel,
                    'sub_id' => $subject_id
                );
                $this->main_model->saveSubjectPerLevel($details);
            endif;
        }
        $subject = Modules::run('academic/getSpecificSubjectPerlevel', $gradeLevel);
        foreach ($subject as $s) {
            $singleSub = Modules::run('academic/getSpecificSubjects', $s->sub_id);
            ?>
            <li><?php echo $singleSub->subject ?></li>
            <?php
        }
    }

    function saveSubjectSettings() {
        $gradeLevel = $this->input->post('gradeLevel');
        $addedSubjects = $this->input->post('addSubjects');
        $subjects = $this->input->post('subjects');
        $subject = explode(',', $addedSubjects);
        if ($this->main_model->checkSubjectSettings($gradeLevel)):
        //$this->main_model->deleteSubjectSettings($gradeLevel);
        endif;

        foreach ($subject as $s) {
            $subject_id = Modules::run('academic/getSubjectId', $s);

            if ($this->main_model->checkSubjectSettings($gradeLevel)):
                $subjectResults = Modules::run('academic/getSpecificSubjectPerlevel', $gradeLevel);
                if (in_array($subject_id, $subjects)):
                    $details = array(
                        'grade_level_id' => $gradeLevel,
                        'subject_id' => $subjects
                    );
                    $all_subjects = $subjects;
                else:
                    $details = array(
                        'grade_level_id' => $gradeLevel,
                        'subject_id' => $subjectResults->subject_id . ',' . $subject_id
                    );
                    $all_subjects = $subjects . ',' . $subject_id;

                endif;


                $this->main_model->updateSubjectSettings($details, $gradeLevel);

            else:
                $details = array(
                    'id'    =>  $this->eskwela->codeCheck('subject_settings', 'id', $this->eskwela->code()),
                    'grade_level_id' => $gradeLevel,
                    'subject_id' => $subject_id
                );
                $all_subjects = $subject_id;

                $this->main_model->saveSubjectSettings($details);
            endif;
        }
        $subject_added = explode(',', $all_subjects);

        foreach ($subject_added as $s) {
            $singleSub = Modules::run('academic/getSpecificSubjects', $s);
            ?>
            <li><?php echo $singleSub->subject ?></li>
            <?php
        }
        //echo $all_subjects;
    }

    function getCurrentQuarter() {
        $settings = $this->main_model->getQuarterSettings();
        //print_r($settings);
        foreach ($settings as $gq) {

            $dateFrom = DateTime::createFromFormat('m-d-Y', $gq->from_date)->format('Y-m-d');
            $dateTo = DateTime::createFromFormat('m-d-Y', $gq->to_date)->format('Y-m-d');
            $now = strtotime(date('Y-m-d'));


            if ($now >= strtotime($dateFrom) && $now <= strtotime($dateTo)):
                return $gq->quarter_id;
            else:
                echo 'Sorry School Year has ended ';
            endif;
        }
    }

    function inLineEdit() {

        $column = $this->input->post('column');
        $value = $this->input->post('value');
        $school_year = $this->session->userdata('school_year');

        $this->main_model->editInLineInfo($column, $value, $school_year);

        echo json_encode(array('status' => TRUE, 'msg' => $value));
    }

    function getRemarksCategory() {
        $remarksCategory = $this->main_model->getRemarksCategory();
        return $remarksCategory;
    }

    function showAdminRemarksForm() {
        //$data['st_id'] = $st_id;
        $data['codeIndicators'] = $this->main_model->getCodeIndicators();
        $this->load->view('adminRemarks', $data);
    }

    function saveAdmissionRemarks() {
        $code_indicator = $this->input->post('codeIndicator_id');
        $required_information = $this->input->post('required_information');
        $st_id = $this->input->post('st_id');
        $user_id = $this->input->post('user_id');
        $effectivityDate = $this->input->post('effectivity_date');

        $checkRemarks = $this->getAdmissionRemarks($st_id);
        if ($checkRemarks->num_rows() > 0) {

            $details = array(
                'remarks' => $required_information,
                'code_indicator_id' => $code_indicator,
                'remark_to' => $st_id,
                'remark_date' => $effectivityDate
            );

            $this->main_model->updateAdmissionRemarks($details, $st_id);
        } else {
            $details = array(
                'remark_id' => $this->eskwela->code(),
                'remarks' => $required_information,
                'code_indicator_id' => $code_indicator,
                'remark_to' => $st_id,
                'remark_date' => $effectivityDate
            );

            $this->main_model->saveAdmissionRemarks($details);
        }

        if ($code_indicator == 1 || $code_indicator == 3) {
            $this->singleColumnEdit('esk_profile_students_admission', 'status', 0, 'user_id', $user_id);
        } else {
            $this->singleColumnEdit('esk_profile_students_admission', 'status', 1, 'user_id', $user_id);
        }



        $remarks = $this->getAdmissionRemarks($st_id);
        $remarks = $remarks->row();
        echo $remarks->code . ' ' . $remarks->remarks . ' - ' . $remarks->row()->remark_date;
        ;
    }

    function getAdmissionRemarks($st_id = null, $month) {
        $remarks = $this->main_model->getAdmissionRemarks($st_id, $month);
        return $remarks;
    }

    function deleteAdmissionRemark($st_id = null, $code_indicator = null) {
        $this->main_model->deleteAdmissionRemark($st_id);
        if ($code_indicator == 1 || $code_indicator == 3) {
            $this->singleColumnEdit('profile_info', 'admitted', 1, 'u_id', $st_id);
        }
    }

    function singleColumnEdit($table, $column, $value, $pk, $pk_value) {
        $this->main_model->singleColumnEdit($table, $column, $value, $pk, $pk_value);
        return;
    }

    public function accessControl() {
        if (!$this->session->userdata('is_logged_in')) {
            ?>
            <script type="text/javascript">
                document.location = "<?php echo base_url() ?>"
            </script>
            <?php
        } else {
            $data['main_content'] = 'accessControl';
            //$data['dashboardAccess'] = Modules::run('nav/getDashboardList'); 
            $data['menuAccess'] = Modules::run('nav/getMenuList');
            $data['position'] = Modules::run('hr/getAllPosition', NULL);
            $data['modules'] = "main";
            echo Modules::run('templates/main_content', $data);
        }
    }

    public function getPositionAccess($position_id = NULL) {
        $data['position_id'] = $position_id;
        //$data['dashboardAccess'] = Modules::run('nav/getDashboardList'); 
        $data['menuAccess'] = Modules::run('nav/getMenuList');
        $data['positionAccess'] = Modules::run('nav/positionAccess', $position_id);
        $this->load->view('accessList', $data);
    }

    public function saveAccess() {
        $position_id = $this->input->post('position_id');
        $column = $this->input->post('column');
        $value = $this->input->post('id');
        $accessValue = $this->input->post('accessValue');
        $accessName = $this->input->post('accessName');

        $exist = $this->main_model->checkAccess($position_id);
        if (!$exist):
            $array = array(
                'position_id' => $position_id,
                $column => $value,
            );
        else:
            $currentValue = $accessValue . ',' . $value;
            $sorValue = explode(',', $currentValue);
            $arrayUnique = array_unique($sorValue);
            sort($arrayUnique);
            $arval = implode(',', $arrayUnique);
            $array = array(
                $column => $arval,
            );
        endif;

        $this->main_model->saveAccess($position_id, $array);
        ?>
        <div id="<?php echo $value ?>" column="<?php echo $column ?>" accessValue="<?php echo $accessValue ?>" onclick="unAssignAccess('<?php echo $value ?>', '<?php echo $accessValue ?>'), $(this).fadeOut(500)"  style='cursor:pointer; margin-bottom:5px;' class='alert alert-success alert-dismissable span11'>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <div id="un_<?php echo $value ?>_name" class="notify">
                <?php echo $accessName; ?>
            </div>    

        </div>            
        <?php
    }

    public function unlinkAccess() {
        $position_id = $this->input->post('position_id');
        $column = $this->input->post('column');
        $value = $this->input->post('id');
        $accessValue = $this->input->post('accessValue');
        $accessName = $this->input->post('accessName');

        $array = array(
            $column => $value,
        );
        $this->main_model->saveAccess($position_id, $array);
        ?>
        <div val="<?php echo $value ?>" column="<?php echo $column ?>" accessValue="<?php echo $accessValue ?>" onclick="assignAccess(this.value)" style='cursor:pointer; margin-bottom:5px;' class='alert alert-danger alert-dismissable span11'>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&DoubleLeftArrow;</button>
            <div id="<?php echo $value ?>_name" class="notify">
                <?php echo $accessName ?>
            </div>    

        </div>              

        <?php
    }

    public function sms() {
        if (!$this->session->userdata('is_logged_in')) {
            ?>
            <script type="text/javascript">
                document.location = "<?php echo base_url() ?>"
            </script>
            <?php
        } else {
            //$this->load->model('messaging_model');  
            $getMessagingModel = Modules::run('messaging/getAllNumbers');
            $data['departments'] = Modules::run('hr/getDepartment');
            $data['getAllNumbers'] = $getMessagingModel;
            $data['main_content'] = 'sms';

            $data['modules'] = "main";
            echo Modules::run('templates/main_content', $data);
        }
    }

    function backup() {
        $settings = $this->getSet();
        $shortName = str_replace(' ', '', $settings->short_name);
        $this->load->dbutil();
        $backup = $this->dbutil->backup();
        $this->load->helper('file');
        $timestamp = date('Ymd-Gis');
        //echo $backup;
        write_file('db_backup/' . $shortName . '-' . $timestamp . '.sql', $backup);
        $this->load->helper('download');
        force_download($shortName . '-' . $timestamp . '.sql', $backup);
    }

    function showUploadForm($user_id) {
        $data['user_id'] = $user_id;
        $this->load->view('uploadForm', $data);
    }

    function do_upload() {
        $sy = $this->input->post('syUpload');
        $upload_option = $this->input->post('picture_option');
        $location = $this->input->post('location');
        $id = $this->input->post('id');
        $filePic = $this->input->post('userfile');
        $fileMime = $this->input->post('imgMime');
        $baseImg_to_php = explode(',', $filePic);
        $filename = $id;
        $imgData = base64_decode($baseImg_to_php[1]);
        $filePath_img = ($upload_option == 'sign' ? 'uploads/sign/' . $filename . '.' . $fileMime : 'uploads/' . $filename . '.' . $fileMime);
        file_put_contents($filePath_img, $imgData);

        ($upload_option != 'sign' ? $this->main_model->setImage($id, $filename . '.' . $fileMime, $sy) : '');
        ?>
        <script>
            alert('upload successfully');
            document.location = "<?php echo base_url() . $location ?>";
        </script>    
        <?php
    }

    /*
      function do_upload()
      {
      $upload_option = $this->input->post('picture_option');
      $location = $this->input->post('location');
      $id=  $this->input->post('id');
      $config['file_name'] = $id;
      $config['upload_path'] = ($upload_option=='sign'?'uploads/sign':'uploads');
      $config['overwrite'] = TRUE;
      $config['allowed_types'] = '*';
      $config['max_size']	= '300';
      $config['max_width']  = '1024';
      $config['max_height']  = '768';
      $this->load->library('upload', $config);
      $this->upload->initialize($config);


      if (!$this->upload->do_upload())
      {
      $error = array('error' => $this->upload->display_errors());
      ?>
      <script>
      alert('<?php echo $error['error'] ?>');
      document.location="<?php echo base_url().$location?>";
      </script>
      <?php

      }
      else
      {
      $img_data = $this->upload->data();
      $ext = $img_data['file_ext'];
      ($upload_option!='sign'?$this->main_model->setImage($id,$id.$ext):'');

      ?>
      <script>
      alert('upload successfully');
      document.location="<?php echo base_url().$location?>";
      </script>
      <?php
      }
      } */

    public function showStats() {
        $data['main_content'] = 'schoolStats';
        $data['modules'] = "main";
        $data['grade_level'] = Modules::run('registrar/getGradeLevel');
        echo Modules::run('templates/main_content', $data);
    }

    public function crop($id) {
        $data['students'] = Modules::run('registrar/getSingleStudent', base64_decode($id));
        $data['main_content'] = 'crop';
        $data['modules'] = "main";
        echo Modules::run('templates/main_content', $data);
    }

    public function processCropping($image, $id) {
        $data['x'] = $this->input->post('x');
        $data['y'] = $this->input->post('y');
        $data['w'] = $this->input->post('w');
        $data['h'] = $this->input->post('h');

        $config['image_library'] = 'gd2';
        //$path =  'uploads/apache.jpg';
        $config['source_image'] = 'uploads/' . $image; //http://localhost/resume/uploads/apache.jpg
        // $config['create_thumb'] = TRUE;
        //$config['new_image'] = './uploads/new_image.jpg';
        $config['maintain_ratio'] = FALSE;
        $config['width'] = $data['h'];
        $config['height'] = $data['w'];
        $config['x_axis'] = $data['x'];
        $config['y_axis'] = $data['y'];

        $this->load->library('image_lib', $config);

        if (!$this->image_lib->crop()) {
            echo $this->image_lib->display_errors();
        } else {
            redirect('registrar/viewDetails/' . $id);
            ?>
            <script type="text/javascript">
                alert('Image successfully cropped');
            </script>
            <?php
        }
    }

    /*    public function changeCollate()
      {
      ////        if($this->db->query("ALTER TABLE esk_profile_employee  CHANGE `user_id` `user_id` INT( 11 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL")):
      ////            echo 'success';
      ////        endif;
      $getTables = $this->db->query("SHOW TABLES");
      $collation = 'CHARACTER SET utf8 COLLATE utf8_general_ci';
      $dbname = 'Tables_in_eskwelaV3';
      foreach($getTables->result() as $table):
      //echo $table->$dbname.'<br />';
      $result = $this->db->query('SHOW COLUMNS FROM '.$table->$dbname);
      $table1 = $table->$dbname;
      //$this->db->query()
      if($this->db->query("ALTER TABLE $table1 DEFAULT $collation")):
      echo "ALTER TABLE $table1 DEFAULT $collation<br />";
      endif;
      //print_r($result);
      /*           foreach($result->result() as $row):

      if($row->Null=='NO'):
      $null = 'NOT NULL ;';
      else:
      $null = 'DEFAULT NULL ;';
      endif;
      // if($row->Type=="int(11)"):
      //    if($this->db->query("ALTER TABLE $table1 CHANGE $row->Field $row->Field $row->Type UNSIGNED $null")):
      //          echo "ALTER TABLE $table1 CHANGE $row->Field $row->Field $row->Type UNSIGNED $null <br />";
      //      endif;
      //   endif;
      //               if($row->Type!="int(11)" && $row->Type != "int(15)" && $row->Type != "int(10)" && $row->Type != "FLOAT" && $row->Type != "float" && $row->Type != "date" && $row->Type != "double"  && $row->Type != "tinyint(1)"   && $row->Type != "tinyint(4)"  && $row->Type != "int(100)" && $row->Type != "int(55)" && $row->Type != "timestamp" ):
      //                   if($this->db->query("ALTER TABLE $table1 CHANGE $row->Field $row->Field $row->Type $collation $null")):
      //                    echo "ALTER TABLE $table1 CHANGE $row->Field $row->Field $row->Type $collation $null <br />";
      //               endif;
      //            endif;
      //
      endforeach;
      //
      endforeach;
      ////        $result = $this->db->query('SHOW COLUMNS FROM esk_profile_employee');
      ////       // print_r($result);
      ////
      ////        foreach($result->result() as $row):
      ////
      ////            if($row->Null=='NO'):
      ////                $null = 'NOT NULL ;';
      ////            else:
      ////                $null = 'DEFAULT NULL ;';
      ////            endif;
      ////            if($row->Type!="int(11)"|| $row->Type="FLOAT"):
      ////                if($this->db->query("ALTER TABLE esk_profile_employee CHANGE $row->Field $row->Field $row->Type $collation $null")):
      ////                    echo "ALTER TABLE esk_profile_employee CHANGE $row->Field $row->Field $row->Type $collation $null <br />";
      ////                endif;
      ////            endif;
      ////
      ////        endforeach;
      } */

//    
//    
//    
//     function gradeLevelOveride()
//    {
//        for($i=16;$i>=11; $i--):
//            $this->db->where('grade_id', $i);
//            $update1 = array(
//                'grade_id' => $i+8
//            );
//            if($this->db->update('grade_level', $update1)):
//                echo " grade_id $i is successfully updated; <br />";
//            endif;
//        endfor;
//        
//        for($i=24;$i>=19; $i--):
//            $this->db->where('grade_id', $i);
//            $update1 = array(
//                'grade_id' => $i-11
//            );
//            if($this->db->update('grade_level', $update1)):
//                echo " grade_id $i is successfully updated; <br />";
//            endif;
//        endfor;
//        
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_level_id', $i);
//            $update1 = array(
//                'grade_level_id' => $i-3
//            );
//            if($this->db->update('section', $update1)):
//                echo " grade_id $i is successfully updated; <br />";
//            endif;
//        endfor;
//        
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_level_id', $i);
//            $update1 = array(
//                'grade_level_id' => $i-3
//            );
//            if($this->db->update('profile_students_admission', $update1)):
//                echo " grade_id $i of table profile_students_admission is successfully updated; <br />";
//            endif;
//        endfor;
//        
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_id', $i);
//            $update1 = array(
//                'grade_id' => $i-3
//            );
//            if($this->db->update('gs_summary_rplp', $update1)):
//                echo " grade_id $i of table gs_summary_rplp is successfully updated; <br />";
//            endif;
//        endfor;
//        
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_level_id', $i);
//            $update1 = array(
//                'grade_level_id' => $i-3
//            );
//            if($this->db->update('gs_spr', $update1)):
//                echo " grade_id $i of table gs_spr is successfully updated; <br />";
//            endif;
//        endfor;
//        
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_id', $i);
//            $update1 = array(
//                'grade_id' => $i-3
//            );
//            if($this->db->update('advisory', $update1)):
//                echo " grade_id $i of table advisory is successfully updated; <br />";
//            endif;
//        endfor;
//       
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_level_id', $i);
//            $update1 = array(
//                'grade_level_id' => $i-3
//            );
//            if($this->db->update('faculty_assign', $update1)):
//                echo " grade_id $i of table faculty_assign is successfully updated; <br />";
//            endif;
//        endfor;
//        
//         for($i=16;$i>=11; $i--):
//            $this->db->where('grade_level_id', $i);
//            $update1 = array(
//                'grade_level_id' => $i-3
//            );
//            if($this->db->update('subject_settings', $update1)):
//                echo " grade_id $i of table subject_settings is successfully updated; <br />";
//            endif;
//        endfor;
//   }

    function subjectOveride() {
        $query = $this->db->get('subject_settings');
        foreach ($query->result() as $q):
            //echo $q->subject_id.' <br />';
            $subs = explode(',', $q->subject_id);
            foreach ($subs as $s) {
                $subject_id = Modules::run('academic/getSubjectId', $s);
                if (!$this->main_model->checkSubjectPerLevel($q->grade_level_id, $s)):
                    $details = array(
                        'grade_level_id' => $q->grade_level_id,
                        'sub_id' => $s
                    );
                    $this->main_model->saveSubjectPerLevel($details);
                endif;
            }

            echo 'gradelevel_id = ' . $q->grade_level_id . ' is updated <br />';
        endforeach;
    }

    function updateGSettings() {
        $sql = "ALTER TABLE `esk_gs_settings`  ADD `customized_card` INT NOT NULL COMMENT '0=default; 1=customized';";
        if ($this->db->query($sql)):
            echo 'GS Settings Succesfully Updated';
        endif;
    }

    function getPreviousStudent($option, $value, $year) {
        $settings = $this->getSet();
        $db = strtolower('eskwela.' . $settings->short_name . '_' . $year);
        $db_config = array(
            'dsn' => '',
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => 'root',
            'database' => $db,
            'dbprefix' => 'esk_',
            'dbdriver' => 'mysqli',
        );

        $db_details = $this->load->database($db_config, TRUE);
        $db_details->select('*');
        $db_details->select('profile.user_id as uid');
        $db_details->select('profile_students.parent_id as pid');
        $db_details->select('lastname');
        $db_details->select('firstname');
        $db_details->select('middlename');
        $db_details->select('level');
        $db_details->select('section');
        $db_details->select('profile.sex as sex');
        $db_details->from('profile_students');
        $db_details->join('profile', 'profile_students.user_id = profile.user_id', 'left');
        $db_details->join('profile_address_info', 'profile.add_id  = profile_address_info.address_id', 'left');
        $db_details->join('profile_contact_details', 'profile.contact_id = profile_contact_details.contact_id', 'left');
        $db_details->join('barangay', 'profile_address_info.barangay_id  = barangay.barangay_id', 'left');
        $db_details->join('cities', 'profile_address_info.city_id  = cities.id', 'left');
        $db_details->join('provinces', 'profile_address_info.province_id  = provinces.id', 'left');
        $db_details->join('profile_students_admission', 'profile.user_id = profile_students_admission.user_id', 'left');
        $db_details->join('section', 'section.section_id = profile_students_admission.section_id', 'left');
        $db_details->join('grade_level', 'section.grade_level_id = grade_level.grade_id', 'left');
        $db_details->join('calendar', 'profile.bdate_id = calendar.cal_id', 'left');
        $db_details->join('profile_parents', 'profile_students.parent_id = profile_parents.parent_id', 'left');
        $db_details->like($option, $value, 'both');
        $query = $db_details->get();
        //echo $db.'<br />';
        print_r($query->result());
    }

    //--------------------------- Enreollment Requirements functions ---------------------------------------------------------------------- ///

    function addEnrollmentReq($req) {
        $this->main_model->addEnrollmentReq(str_replace("%20", " ", $req));
    }

    function getAllEnrollmentReq() {
        $list = $this->main_model->getAllEnrollmentReq();
        $data['list'] = $list;
        echo $this->load->view('list_requirements', $data);
    }

    function editReqList() {
        $id = $this->input->post('id');
        $value = $this->input->post('value');
        $option = $this->input->post('opt');
        $this->main_model->editDelReqList($id, $option, $value);
    }

    function deleteReq($id, $option) {
        $this->main_model->editDelReqList($id, $option);
    }

    function listRequirements() {
        return $this->main_model->getAllEnrollmentReq();
    }

    function checkForDuplicate($id, $dept) {
        $res = $this->main_model->checkForDuplicate($id, $dept);
        echo $res->num_rows();
    }

    function insertListPerDept($id, $dept) {
        $this->main_model->insertListPerDept($id, $dept);
    }

    function deptTable($dept) {
        switch ($dept):
            case 1:
                return 'enrollment_preschool';
            case 2:
                return 'enrollment_elem_req';
            case 3:
                return 'enrollment_jhs_req';
            case 4:
                return 'enrollment_shs_req';
            case 5:
                return 'enrollment_college_req';
        endswitch;
    }

    function department($lvl) {
        if ($lvl == 14 && $lvl == 15 && $lvl == 1):
            return 1;
        elseif ($lvl >= 2 && $lvl <= 7):
            return 2;
        elseif ($lvl >= 8 && $lvl <= 11):
            return 3;
        elseif ($lvl >= 12 && $lvl <= 13):
            return 4;
        else:
            return 5;
        endif;
    }

    function checkPerDeptList($id) {
        $data['dept'] = $id;
        $data['list'] = $this->main_model->checkPerDeptList($id);
        echo $this->load->view('reqListPerDept', $data);
    }

    function deleteItem($id, $dept) {
        $this->main_model->deleteItem($id, $dept);
    }

    function displayCheckListPerDept($dept, $opt){
        
        $deptNum = ($opt != 1 ? $this->department($dept) : $dept);
        return $this->main_model->checkPerDeptList($deptNum);
    }

    //---------------------------------- End of Enrollment requirements functions ------------------------------------//
}
