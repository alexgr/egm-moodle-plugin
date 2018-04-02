<?php
/**
 * Defines the version and other meta-info about the plugin
 *
 * Setting the $plugin->version to 0 prevents the plugin from being installed.
 * See https://docs.moodle.org/dev/version.php for more info.
 *
 * @package    mod_edugamemaker
 * @copyright  2017 Alex Gradinaru <contact@alexgr.ro>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_edubox';
$plugin->version = 2017051600; //The current module version (Date: YYYYMMDDXX).
$plugin->release = 'v0.01';
$plugin->requires = 2014051200;
$plugin->maturity = MATURITY_ALPHA;
$plugin->cron = 0;
$plugin->dependencies = array();
