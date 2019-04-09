<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Display site related chart events charts.
 *
 * @package    report_chart
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_login();

admin_externalpage_setup('report_chart');
$context = context_system::instance();
require_capability('report/chart:view', $context);
$actionurl = new moodle_url('/report/chart/index.php');
$PAGE->set_context($context);
$PAGE->set_url('/report/chart/index.php');
$PAGE->set_title(get_string('pluginname', 'report_chart'));
$PAGE->set_heading(get_string('pluginname', 'report_chart'));
$PAGE->set_pagelayout('report');
echo $OUTPUT->header();
$renderable = new report_chart_renderable();
$renderer = $PAGE->get_renderer('report_chart');
echo $renderer->render($renderable);

$studentid = isset($_POST['studentid'])?$_POST['studentid']:"";
$studentname = isset($_POST['studentname'])?$_POST['studentname']:"";
if(!empty($studentid)&&!empty($studentname)){
	$student=new stdClass();
	$student->id=$studentid;
	$student->username=$studentname;
	$renderer->setUser($student);
	$renderer->setSelect($studentid);
	//echo "<div>hello world2</div>";
}
//echo "<div>".$renderer->box($studentname)."</div>";
//echo $renderer->report_course_activity_chart();
echo $renderer->report_chart();
echo $OUTPUT->footer();
