<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once(dirname(__FILE__).'/locallib.php');

/**
 * Module instance settings form
 *
 * @package    mod_edugamemaker
 * @copyright  2017 Alex Gradinaru <contact@alexgr.ro>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_edugamemaker_mod_form extends moodleform_mod {

    public function __construct($current, $section, $cm, $course) {
        parent::__construct($current, $section, $cm, $course);
    }
    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $COURSE, $DB, $PAGE;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('edugamemakername', 'edugamemaker'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'edugamemakername', 'edugamemaker');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of edugamemaker settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        //$mform->addElement('static', 'label1', 'edugamemakersetting1', 'Your edugamemaker fields go here. Replace me!');

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('edugamemaker', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }

        $edugamemaker=new EduGameMakerConnect($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $edugamemaker->set_course($course);
        }


        $mform->addElement('header', 'edugamemakerfieldset', get_string('edugamemakerfieldset', 'edugamemaker'));
        
        
        $options = array();
        $projects= $edugamemaker->getProjects();

        foreach ($projects as $project) {

                $options[$project->id] = $project->name;

        }

        $mform->addElement('select', 'edugamemakergameid', get_string('edugamemakergameselect', 'edugamemaker'), $options);
        $mform->addHelpButton('edugamemakergameid', 'edugamemakergameselect', 'edugamemaker');

        //levels?


        //$mform->addElement('static', 'label2', 'edugamemakersetting2', 'Your edugamemaker fields go here. Replace me!');

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
