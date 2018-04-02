<?php

/**
 * Internal library of functions for module edugamemaker
 *
 * All the edugamemaker specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_edugamemaker
 * @copyright  2017 Alex Gradinaru <contact@alexgr.ro>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 *function edugamemaker_do_something_useful(array $things) {
 *    return new stdClass();
 *}
 */


require 'vendor/autoload.php';
use GuzzleHttp\Client;

class EduGameMakerConnect //extends AnotherClass
{

	private $context;
	private $course;
	private $coursemodule;

	private $instance;
	private $config;
	private $client;

	private $userdata;
	private $projects;
	private $game;

	function __construct($coursemodulecontext, $coursemodule, $course)
	{  

		$this->context = $coursemodulecontext;
        $this->course = $course;

        // Ensure that $this->coursemodule is a cm_info object (or null).
        $this->coursemodule = cm_info::create($coursemodule);

        $this->config = $this->get_admin_config();
		$this->instance = $this->get_instance();

		//connect to edugamemaker
		$this->client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'http://gamemaker.local',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
		    'headers' => [
		      'verify'          => false,
		      'allow_redirects' => true,
		      'headers'         => [
		        'Accept'        => "application/json",
		        'Authorization' => "Bearer {$this->config->apikey}"
		      ]
		    ]
		]);
	}

	
	/**
     * Create a new edugamemaker instance
     *
     */
	public function createInstance()
	{
		global $DB,$USER;

		$record = new stdClass();
		$record->userid      	= $USER->id;
		$record->assignment      	= $this->get_instance()->id;
		$record->status      		= 1;
		$record->attemptnumber      	= 1;
		$record->timecreated      	= time();
		$record->timemodified      	= time();
		$lastinsertid = $DB->insert_record('edugamemaker_user_data', $record, false);

		$this->userdata = $record;
		$this->userdata->id = $lastinsertid;

		//$DB->update_record($table, $dataobject, $bulk=false)
	}

	/**
     * Suspend instance for user
     *
     */
	public function suspendInstance()
	{
		global $DB;

		$this->userdata->status = 0;
		$DB->update_record('edugamemaker_user_data', $this->userdata);
	}

	/**
     * Resume instance for user or create if not available
     *
     */
	public function resumeInstance()
	{
		global $DB;
		
		$this->userdata->status = 1;
		$DB->update_record('edugamemaker_user_data', $this->userdata);		
		
	}

	/**
     * Get the edugamemaker available projects
     *
     * @return array projects
     */
	public function getProjects()
	{ 
		$res = $this->client->request('GET', '/projects');
		return $res->getBody();
	}

	
	/**
     * Get the edugamemaker project
     *
     * @return stdClass project
     */
	public function getProject($id)
	{
		$res = $this->client->request('GET', '/project/'.$id);
		return $res->getBody();
	}

	/**
     * Get the edugamemaker project
     *
     * @return stdClass project
     */
	public function getInstanceData()
	{
		//todo - return from database
		$res = $this->client->request('GET', '/game/'.$this->instance->edugamemakergameid);
		return $res->getBody();
	}

	/**
     * Get the settings for the current instance of this assignment
     *
     * @return stdClass The settings
     */
    public function get_instance() {
        global $DB;
        if ($this->instance) {
            return $this->instance;
        }
        if ($this->get_course_module()) {
            $params = array('id' => $this->get_course_module()->instance);
            $this->instance = $DB->get_record('edugamemaker', $params, '*', MUST_EXIST);
        }
        if (!$this->instance) {
           // throw new coding_exception('Improper use of the edugamemaker class. ' .
                                      // 'Cannot load the edugamemaker record.');
        }
        return $this->instance;
    }

    /**
     * Load and cache the admin config for this module.
     *
     * @return stdClass the plugin config
     */
    public function get_admin_config() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = get_config('edugamemaker');
        return $this->config;
    }

    /**
     * Get the context of the current course.
     *
     * @return mixed context|null The course context
     */
    public function get_course_context() {
        if (!$this->context && !$this->course) {
            throw new coding_exception('Improper use of the assignment class. ' .
                                       'Cannot load the course context.');
        }
        if ($this->context) {
            return $this->context->get_course_context();
        } else {
            return context_course::instance($this->course->id);
        }
    }


    /**
     * Get the current course module.
     *
     * @return cm_info|null The course module or null if not known
     */
    public function get_course_module() {
        if ($this->coursemodule) {
            return $this->coursemodule;
        }

        if (!$this->context) {
            return null;
        }

        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $modinfo = get_fast_modinfo($this->get_course());
            $this->coursemodule = $modinfo->get_cm($this->context->instanceid);
            return $this->coursemodule;
        }
        return null;
    }

    /**
     * Get context module.
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Get the current course.
     *
     * @return mixed stdClass|null The course
     */
    public function get_course() {
        global $DB;

        if ($this->course) {
            return $this->course;
        }

        if (!$this->context) {
            return null;
        }
        $params = array('id' => $this->get_course_context()->instanceid);
        $this->course = $DB->get_record('course', $params, '*', MUST_EXIST);

        return $this->course;
    }

    /**
     * Set the submitted form data.
     *
     * @param stdClass $data The form data (instance)
     */
    public function set_instance(stdClass $data) {
        $this->instance = $data;
    }

    /**
     * Set the context.
     *
     * @param context $context The new context
     */
    public function set_context(context $context) {
        $this->context = $context;
    }

    /**
     * Set the course data.
     *
     * @param stdClass $course The course data
     */
    public function set_course(stdClass $course) {
        $this->course = $course;
    }
}
