<?php
/**
 * Defines backup_edugamemaker_activity_task class
 *
 * @package   mod_edugamemaker
 * @category  backup
 * @copyright 2016 Alex Gradinaru <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/edugamemaker/backup/moodle2/backup_edugamemaker_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the edugamemaker instance
 *
 * @package   mod_edugamemaker
 * @category  backup
 * @copyright 2016 Alex Gradinaru <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_edugamemaker_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the edugamemaker.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_edugamemaker_activity_structure_step('edugamemaker_structure', 'edugamemaker.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of edugamemakers.
        $search = '/('.$base.'\/mod\/edugamemaker\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@EDUGAMEMAKERINDEX*$2@$', $content);

        // Link to edugamemaker view by moduleid.
        $search = '/('.$base.'\/mod\/edugamemaker\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@EDUGAMEMAKERVIEWBYID*$2@$', $content);

        return $content;
    }
}
