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
 * Graphic report renderer class.
 *
 * @package    report_chart
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/report/chart/classes/report_chart.php');

require_login();

/**
 * Graphic report renderable class.
 *
 * @package    report_chart
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_chart_renderable implements renderable {

    /**
     * @var stdClass the course object.
     */
    public $course;
    /**
     * @var int|\stdClass controls course visibility.
     */
    public $showcourses;
    /**
     * @var  string stores users activity events return from google charts.
     */
    public $mostactiveusers;
    /**
     * @var  string stores the most triggered events return from google charts.
     */
    public $mosttriggeredevents;
    /**
     * @var  string stores the activity by period return from google charts.
     */
    public $activitybyperiod;
    /**
     * @var string stores the course activity return from google charts.
     */
    public $mostactivecourses;
	
	
    //my
	public $coursegrade;
	 
	public $accuracyclassifybytopic;
	 
	public $accuracyclassifybytype;
	  
	public $peertopeercomparison;
	  
	public $peertopeertrend;
	
	public $getcoursegradetrend1;
	
	public $getcoursegradetrend2;
	
	public $getcoursegradetrend3;
	
	public $getcoursespeedlimittrend;
	
	public $getcoursespeedmeter;
	public $gettablebytype;
	
	protected $user;
	
	//endmy
	//endmy

	
	
    /**
     * Constructor.
     *
     * @param stdClass|int $course (optional) course object or id.
     */
    public function __construct($course = null) {
		global $USER;
		//$this->user=new stdClass();
		$this->user=$USER;
		
        if (!empty($course)) {
            if (is_int($course)) {
                $course = get_course($course);
            }
            $this->course = $course;
        }
    }

	
	public function setUser($user){
		//$this->user=new stdClass();
		$this->user=$user;
	}
    /**
     * Return list of courses to show in selector.
     *
     * @return array list of courses.
     */
    public function get_course_list() {
        global $DB;

        $courses = array();
        $sitecontext = context_system::instance();
        // First check to see if we can override showcourses and showusers.
        $numcourses = $DB->count_records("course");
        if ($numcourses < COURSE_MAX_COURSES_PER_DROPDOWN && !$this->showcourses) {
            $this->showcourses = 1;
        }

        // Check if course filter should be shown.
        if ($this->showcourses) {
            if ($courserecords = $DB->get_records("course", null, "fullname", "id,shortname,fullname,category")) {
                foreach ($courserecords as $course) {
                    if ($course->id == SITEID) {
                        $courses[$course->id] = format_string($course->fullname) . ' (' . get_string('site') . ')';
                    } else {
                        $courses[$course->id] = format_string(get_course_display_name_for_list($course));
                    }
                }
            }
            core_collator::asort($courses);
        }
        return $courses;
    }

    /**
     * Displays course related graph charts.
     */
    public function get_gcharts_data() {
        $graphreport = new report_chart($this->course->id);

        // User Activity Pie Chart.
        $this->mostactiveusers = $graphreport->get_most_active_users();

        // Most triggered events. rename this attr
        $this->mosttriggeredevents = $graphreport->get_most_triggered_events();

        // Monthly user activity.
        $this->activitybyperiod = $graphreport->get_monthly_user_activity();
		
		//my
        $this->coursegrade = $graphreport->get_course_grade();
		
		//endmy
    }

    /**
     * Displays site related charts.
     */
    public function get_courses_report() {

        $graphreport = new report_chart();
		$graphreport->setUser($this->user);
		$this->coursegrade = $graphreport->get_course_grade();
		$this->gettablebytype = $graphreport->get_accuracy_classify_by_type2();
        //$this->accuracyclassifybytype = $graphreport->get_accuracy_classify_by_type();
		$this->accuracyclassifybytopic = $graphreport->get_accuracy_classify_by_topic();
		$this->peertopeercomparison = $graphreport->get_peer_to_peer_comparison();
		$this->getcoursegradetrend1 = $graphreport->get_course_grade_trend();
		$this->getcoursespeedlimittrend = $graphreport->get_course_speed_limit_trend();
		$this->getcoursespeedmeter = $graphreport->get_course_speedmeter();
	
    }
	
	
}
