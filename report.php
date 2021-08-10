<?php

global $DB ;

require_once("../../config.php");
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/editadvanced_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/webservice/lib.php');
require_once($CFG->dirroot.'/lib/moodlelib.php');
require_once($CFG->dirroot.'/course/lib.php');




$excel = optional_param('excel',0, PARAM_INT);
$courseid = optional_param('id',0, PARAM_INT);
$startdate = optional_param('startdate',0, PARAM_INT);
$enddate = optional_param('enddate',0, PARAM_INT);
$starttime = optional_param('startdate',0, PARAM_INT);
$endtime = optional_param('enddate',0, PARAM_INT);
$modid = optional_param('modid',0, PARAM_INT);


require_login();


$context = context_course::instance($courseid);

$course = $DB->get_record('course', array('id' => $courseid)) ;

$PAGE->set_url('/local/examscheck/report.php', array('id'=>$courseid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title('Duplicate IP Report');
$PAGE->set_heading($course->fullname);

//
//$PAGE->set_context($context);
//
//$PAGE->set_pagelayout('incourse');
//
//
//
//$PAGE->set_title("$course->shortname: ".get_string('participants'));
//$PAGE->set_heading($course->fullname);
//$PAGE->set_pagetype('course-view-' . $course->format);
//

// Expand the users node in the settings navigation when it exists because those pages


if(!is_siteadmin()){
    redirect('/') ;
}

class course_form extends moodleform {


    public function definition() {
        global $CFG,$DB,$remotedb;


        $mform = $this->_form;


        $current = $this->_customdata['current'];


        $context = context_course::instance($this->_customdata['cid']);
        $course = $DB->get_record('course', array('id' => $this->_customdata['cid'])) ;
        $modinfo = get_fast_modinfo($course);


        $headername =  $mform->addElement('header', 'report', 'Exams Report');


        $sql = "SELECT c.id, c.fullname FROM {course} c WHERE c.visible = 1 ";
        $courses = $DB->get_records_sql($sql);

        $courseoptions[0] = 'None';

        foreach ($courses as $c) {

            $courseoptions[$c->id] = format_string($c->fullname);
        }

       $selectCourses =  $mform->addElement('select', 'filter_courses', 'Course'.$this->_customdata['courseid'], $courseoptions);

        $mform->setDefault('filter_courses', $this->_customdata['cid']);

        $mform->setType('filter_courses', PARAM_INT);

        $selectCourses->freeze();



        $options = array('0'=>get_string('allactivities'));
        $modsused = array();

        foreach($modinfo->cms as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $modsused[$cm->modname] = true;
        }



        foreach ($modinfo->sections as $section=>$cmids) {
            foreach ($cmids as $cmid) {
                $cm = $modinfo->cms[$cmid];
                if (empty($modsused[$cm->modname]) or !$cm->uservisible) {
                    continue;
                }
                $options[$cm->id] = format_string($cm->name);
            }
        }
        $mform->addElement('select', 'modid', 'Course Activities', $options);



        $options = array('optional' => true);
        $mform->addElement('date_selector', 'startdate', get_string('enrolstartdate', 'enrol_self'), $options);
        $mform->setDefault('startdate', $this->_customdata['startDate']);
        $mform->addHelpButton('startdate', 'startdate', 'enrol_self');

        $options = array('optional' => true);
        $mform->addElement('time_selector', 'starttime', get_string('enrolstarttime', 'enrol_self'), $options);
        $mform->setDefault('starttime', $this->_customdata['startTime']);
        $mform->addHelpButton('starttime', 'starttime', 'enrol_self');

        $options = array('optional' => true);
        $mform->addElement('date_selector', 'enddate', get_string('enrolenddate', 'enrol_self'), $options);
        $mform->setDefault('enddate', $this->_customdata['endDate']);
        $mform->addHelpButton('enddate', 'enddate', 'enrol_self');

        $options = array('optional' => true);
        $mform->addElement('time_selector', 'endtime', get_string('enrolendtime', 'enrol_self'), $options);
        $mform->setDefault('endtime', $this->_customdata['endTime']);
        $mform->addHelpButton('endtime', 'endtime', 'enrol_self');

        $mform->addElement('hidden', 'id', $this->_customdata['cid']);

        $mform->addElement('submit',  'btnsave', 'Show Report');



        //$this->add_action_buttons(true,  get_string('add'));


    }
    //Custom validation should be added here
    function validation($data, $files) {
        global $USER ;
        $errors = parent::validation($data, $files);
        return $errors;
    }
}






$customdata = array('cid' => $courseid);



$mform = new course_form(null,$customdata);

$data= $mform->get_data();


$link = 'report.php?excel=1';



$sql = "select * from {logstore_standard_log} where courseid NOT IN (0,1) " ;


$callQuery  = false ;





if($data->startdate){
    $courseSDvalue = $data->startdate ;
}else{
    $courseSDvalue = $startdate ;
}



if($data->enddate){
    $courseEDvalue = $data->enddate ;
}else{
    $courseEDvalue = $enddate ;
}


if($data->modid){
    $activityid = $data->modid ;
}else{
    $activityid = $modid ;
}




if($courseid){
    $sql .=" and courseid = {$courseid} " ;

    $link .= '&id='.$courseid;

    $callQuery  = true ;

}




if($activityid){
    $sql .=" and contextinstanceid = {$activityid} " ;

    $link .= '&modid='.$activityid;

    $callQuery  = true ;

}



if($courseSDvalue && !$courseEDvalue){

    $sql .=" and FROM_UNIXTIME(timecreated, '%Y-%m-%d') = '".date("Y-m-d", $courseSDvalue)."'"  ;

    $dateSql =" and FROM_UNIXTIME(timecreated, '%Y-%m-%d') = '".date("Y-m-d", $courseSDvalue)."'"  ;


    $link .= '&startdate='.$courseSDvalue;


    $callQuery  = true ;

}elseif ($courseSDvalue & $courseEDvalue){



    $sql .=" and FROM_UNIXTIME(timecreated, '%Y-%m-%d') >= '".date("Y-m-d", $courseSDvalue)."' and FROM_UNIXTIME(timecreated, '%Y-%m-%d') <= '".date("Y-m-d", $courseEDvalue)."'"  ;
    $dateSql =" and FROM_UNIXTIME(timecreated, '%Y-%m-%d') >= '".date("Y-m-d", $courseSDvalue)."' and FROM_UNIXTIME(timecreated, '%Y-%m-%d') <= '".date("Y-m-d", $courseEDvalue)."'"  ;
    $link .= '&startdate='.$courseSDvalue;
    $link .= '&enddate='.$courseEDvalue;

    $callQuery  = true ;

}else{
    $dateSql =""  ;

}




if(!$callQuery){

    $sql .=" and 1 = 2 " ;

}














$content = '





<div class="ccnBlockContent" >


                
                
                
                <div>
                    
                    '.$mform->render().'
                    
                    
                </div>
                
                
                <div style="text-align: center ; margin-bottom: 5px;">
                    
                    <a href="'.$link.'" title="Export To Excel"><img src="'.$CFG->wwwroot.'/blocks/configurable_reports/export/xls/pix.gif" alt="xls" >XSL</a>
                    
                    
                </div>
               
                
              <table class="flexible reportlog generaltable generalbox">

              <thead>
                <tr role="row">
                  <th class="header c0" scope="col">Activity</th>
                  <th class="header c1" scope="col" >Users</th>
                  <th class="header c2" scope="col">Duplicate IP</th>
                  <th class="header c3" scope="col">Count</th>

                  
                </tr>
              </thead>
              <tbody>

            ';



$logsRecs = $DB->get_records_sql($sql);
$ipsArr = [] ;
foreach($logsRecs as $logRec){

    $ipsArr[$logRec->contextinstanceid][$logRec->ip][$logRec->userid] =  $logRec->userid ;//array('userid'=>,'activityid'=>$logRec->contextinstanceid );

}



$activityName = 'Announcement' ;
foreach($ipsArr as $aid=>$ipArr){
    $activityRec = $DB->get_record_sql('SELECT m.name,cm.instance from {course_modules} cm join  {modules} m on m.id = cm.module where cm.id ='.$aid);
    if($activityRec->name){
        $actSql = 'SELECT name from {'.$activityRec->name.'}  where id ='.$activityRec->instance ;

        $activityDetails = $DB->get_record_sql($actSql);

        $activityName = $activityDetails->name ;
    }

   //



    foreach($ipArr as $ip => $usersArr){
            if(count($usersArr) > 1 && $ip){

                $usersStr = implode(',',$usersArr);
                $logsRecs = $DB->get_record_sql('SELECT GROUP_CONCAT(concat(username," "))  as usernames FROM mdl_user where id in ('.$usersStr.')');



                $content .= "
                        <tr>
                          <th scope='col'>{$activityName}</th>
                          <th scope='col' style='white-space:pre-wrap; word-wrap:break-word'>{$logsRecs->usernames}</th>
                          
                          <th scope='col'>".hash('sha256', $ip)."</th>
                          <th scope='col'>".count($usersArr)."</th>
                          
                        </tr>
                        " ;

                $table['data'][] = [
                    $activityName,
                    $logsRecs->usernames,
                    hash('sha256', $ip),
                    count($usersArr)

                ];
            }

    }


}



$content .= '
            </tbody>
            </table>


</div>



            ';

if(!$excel){


    echo $OUTPUT->header();
    echo $OUTPUT->heading('IP Similarity Report');

    echo $content ;



    echo $OUTPUT->footer();

    exit ;
}






global $DB, $CFG;
require_once($CFG->dirroot.'/lib/excellib.class.php');

$table['head'] = [
    'activity',
    'users',
    'duplicate ip',
    'count'

];




$matrix = array();
$filename = 'report_'.(time()).'.xls';
$table = (object) $table ;
if (!empty($table->head)) {
    $countcols = count($table->head);
    $keys = array_keys($table->head);
    $lastkey = end($keys);
    foreach ($table->head as $key => $heading) {
        $matrix[0][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($heading))));
    }
}

if (!empty($table->data)) {
    foreach ($table->data as $rkey => $row) {
        foreach ($row as $key => $item) {
            $matrix[$rkey + 1][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($item))));
        }
    }
}

$downloadfilename = clean_filename($filename);
// Creating a workbook.
$workbook = new MoodleExcelWorkbook("-");
// Sending HTTP headers.
$workbook->send($downloadfilename);
// Adding the worksheet.
$myxls = $workbook->add_worksheet($filename);

foreach ($matrix as $ri => $col) {
    foreach ($col as $ci => $cv) {
        $myxls->write_string($ri, $ci, $cv);
    }
}

$workbook->close();
exit;



