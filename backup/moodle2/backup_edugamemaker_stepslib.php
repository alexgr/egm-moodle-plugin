<?php
/**
 * Define all the backup steps that will be used by the backup_edugamemaker_activity_task
 *
 * @package   mod_edugamemaker
 * @category  backup
 * @copyright 2016 Alex Gradinaru <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete edugamemaker structure for backup, with file and id annotations
 *
 * @package   mod_edugamemaker
 * @category  backup
 * @copyright 2016 Alex Gradinaru <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_edugamemaker_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the edugamemaker instance.
        $edugamemaker = new backup_nested_element('edugamemaker', array('id'), array(
            'name', 'intro', 'introformat', 'grade'));

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $edugamemaker->set_source_table('edugamemaker', array('id' => backup::VAR_ACTIVITYID));

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $edugamemaker->annotate_files('mod_edugamemaker', 'intro', null);

        // Return the root element (edugamemaker), wrapped into standard activity structure.
        return $this->prepare_activity_structure($edugamemaker);
    }
}
