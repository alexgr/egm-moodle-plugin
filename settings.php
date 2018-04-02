<?php
/**
 * edugamemaker module admin settings and defaults
 *
 * @package    mod_edugamemaker
 * @copyright  2017 Alex Gradinaru <contact@alexgr.ro>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    //--- heading ---
    $settings->add(new admin_setting_heading('edugamemaker_settings', '', get_string('pluginname_desc', 'edugamemaker')));

    //--- connection settings ---
    $settings->add(new admin_setting_configtext('edugamemaker/username', get_string('username', 'edugamemaker'), get_string('configusername', 'edugamemaker'), ''));
    $settings->add(new admin_setting_configpasswordunmask('edugamemaker/apikey', get_string('apikey', 'edugamemaker'), get_string('configapikey', 'edugamemaker'), ''));

}
