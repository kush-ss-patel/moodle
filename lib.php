<?php


use core_user\output\myprofile\node;
use core_user\output\myprofile\tree;

function local_examscheck_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{

}


//function local_examscheck_extend_navigation(global_navigation $navigation)
//{
//    global $CFG, $PAGE, $USER,$DB;
//
//    if(is_siteadmin()) {
//
//        $incidents_view_nav_node = navigation_node::create(
//            'Exam check report',
//            new moodle_url($CFG->wwwroot . '/local/examscheck/report.php'),
//            navigation_node::TYPE_CONTAINER,
//            null,
//            null,
//            new pix_icon('e/bullet_list', '')
//        );
//
//        $incidents_node = $navigation->add_node($incidents_view_nav_node);
//        $incidents_node->showinflatnavigation = true;
//    }
//
//}


function local_examscheck_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context){

    if (has_capability('local/examscheck:managereport', $context)) {

        $url = new moodle_url('/local/examscheck/report.php', array('id' => $course->id));
        $parentnode->add('Exam Check', $url, '', null, 'examscheck', new pix_icon('e/bullet_list', ''));
    }
}


