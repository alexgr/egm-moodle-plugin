<?php
/**
 * Prints a particular instance of edugamemaker
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_edugamemaker
 * @copyright  2017 Alex Gradinaru <contact@alexgr.ro>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... edugamemaker instance ID - it should be named as the first character of the module.
$status = optional_param('status', '', PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('edugamemaker', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $edugamemaker  = $DB->get_record('edugamemaker', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $edugamemaker  = $DB->get_record('edugamemaker', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $edugamemaker->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('edugamemaker', $edugamemaker->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_edugamemaker\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $edugamemaker);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/edugamemaker/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($edugamemaker->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('edugamemaker-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($edugamemaker->intro) {
    echo $OUTPUT->box(format_module_intro('edugamemaker', $edugamemaker, $cm->id), 'generalbox mod_introbox', 'edugamemakerintro');
}

// Replace the following lines with you own code.
echo $OUTPUT->heading(format_string($edugamemaker->name));


echo $OUTPUT->box_start('');



$educonnector=new EduGameMakerConnect($PAGE->context,$cm,$course);
?>
<div id="edugamemaker"></div>

        <link href="http://gamemaker.local/assets/css/main.css" rel="stylesheet" />
        <link id="theme" href="http://gamemaker.local/assets/css/light.css" rel="stylesheet" />
        <link id="theme" href="http://gamemaker.local/assets/css/dark.css" rel="stylesheet" />

        <script src="http://gamemaker.local/assets/three.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/libs/system.min.js"></script>

        <script src="http://gamemaker.local/assets/modules/js/controls/EditorControls.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/controls/TransformControls.js"></script>



        <script src="http://gamemaker.local/assets/modules/js/renderers/Projector.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/renderers/CanvasRenderer.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/renderers/RaytracingRenderer.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/renderers/SoftwareRenderer.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/renderers/SVGRenderer.js"></script>


        <script src="http://gamemaker.local/assets/js/libs/signals.min.js"></script>
        <script src="http://gamemaker.local/assets/js/libs/ui.js"></script>
        <script src="http://gamemaker.local/assets/js/libs/ui.three.js"></script>

        <script src="http://gamemaker.local/assets/js/libs/app.js"></script>
        <script src="http://gamemaker.local/assets/js/Player.js"></script>
        <script src="http://gamemaker.local/assets/js/Script.js"></script>

        <script src="http://gamemaker.local/assets/modules/js/effects/VREffect.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/controls/VRControls.js"></script>
        <script src="http://gamemaker.local/assets/modules/js/vr/WebVR.js"></script>

        <script src="http://gamemaker.local/assets/js/Storage.js"></script>

        <script src="http://gamemaker.local/assets/js/Editor.js"></script>
        <script src="http://gamemaker.local/assets/js/Config.js"></script>
        <script src="http://gamemaker.local/assets/js/History.js"></script>
        <script src="http://gamemaker.local/assets/js/Loader.js"></script>

        <script src="http://gamemaker.local/assets/js/Sidebar.js"></script>
        <script src="http://gamemaker.local/assets/js/Sidebar.Project.js"></script>



        <script src="http://gamemaker.local/assets/js/Viewport.js"></script>
        <script src="http://gamemaker.local/assets/js/Viewport.Info.js"></script>

        <script src="http://gamemaker.local/assets/js/Command.js"></script>


        <script src="http://gamemaker.local/assets/js/libs/html2canvas.js"></script>
        <script src="http://gamemaker.local/assets/js/libs/three.html.js"></script>

        <script>

            window.URL = window.URL || window.webkitURL;
            window.BlobBuilder = window.BlobBuilder || window.WebKitBlobBuilder || window.MozBlobBuilder;

            Number.prototype.format = function (){
                return this.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
            };

            //
            var container = document.getElementById( 'edugamemaker' );
            var editor = new Editor();

            editor.clear();
            editor.fromJSON( <?php echo $educonnector->getInstanceData(); ?> );

            var viewport = new Viewport( editor );
            container.appendChild( viewport.dom );


            var player = new Player( editor );
            container.appendChild( player.dom );

            new Sidebar.Project( editor )

    
            function onWindowResize( event ) {

                editor.signals.windowResize.dispatch();

            }

            window.addEventListener( 'resize', onWindowResize, false );

            onWindowResize();

            editor.signals.startPlayer.dispatch();

        </script>
<?php


echo $OUTPUT->box_end();

// Finish the page.
echo $OUTPUT->footer();
