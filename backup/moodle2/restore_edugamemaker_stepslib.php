<?php
/**
 * Define all the restore steps that will be used by the restore_edugamemaker_activity_task
 *
 * @package   mod_edugamemaker
 * @category  backup
 * @copyright 2016 Alex Gradinaru <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one edugamemaker activity
 *
 * @package   mod_edugamemaker
 * @category  backup
 * @copyright 2016 Alex Gradinaru <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_edugamemaker_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('edugamemaker', '/activity/edugamemaker');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_edugamemaker($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        // Create the edugamemaker instance.
        $newitemid = $DB->insert_record('edugamemaker', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add edugamemaker related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_edugamemaker', 'intro', null);
    }
}
