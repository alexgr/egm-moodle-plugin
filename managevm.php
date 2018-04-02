<?php

/**
 * manage openstack vm
 *
 * @package    mod_edugamemaker
 * @copyright  2017 Alex Gradinaru <contact@alexgr.ro>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... openstack instance ID - it should be named as the first character of the module.
$action = optional_param('action', '', PARAM_NOTAGS);

$url = new moodle_url('/mod/openstack/managevm.php', array('id'=>$id));
if ($action !== '') {
    $url->param('action', $action);
}

if ($id) {
    $cm         = get_coursemodule_from_id('openstack', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $openstack  = $DB->get_record('openstack', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $openstack  = $DB->get_record('openstack', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $openstack->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('openstack', $openstack->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_openstack\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $openstack);
$event->trigger();

$osc=new OpenstackConnect($PAGE->context, $cm, $course);

switch ($action) {

	case 'resume':
		$osc->resumeInstance();
		break;

	case 'suspend':
		$osc->suspendInstance();		
		break;

	case 'terminate':
		$osc->terminateInstance();
		break;
}

$mapurl = new moodle_url('/mod/openstack/view.php', array('id'=>$id));
redirect($mapurl->out(false));
