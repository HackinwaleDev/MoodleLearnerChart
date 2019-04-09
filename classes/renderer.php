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
 * Graphic report renderer.
 *
 * @package    report_chart
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;

require_login();

/**
 * Graphic report renderer class.
 *
 * @package    report_chart
 * @copyright  2015 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_chart_renderer extends plugin_renderer_base {

    /** @var report_chart_renderable instance of report chart renderable. */
    protected $renderable;

	protected $user;
	
	protected $studenid;
    /**
     * Renderer constructor.
     *
     * @param report_chart_renderable $renderable chart report renderable instance.
     */
    protected function render_report_chart(report_chart_renderable $renderable) {
		global $USER;
		$this->studenid="";
		$this->renderable = $renderable;
		$this->user=$USER;
        //$this->report_selector_form();
    }
	
	
	public function setUser($user){
		$this->user=$user;
	}
	public function setSelect($studentid){
		$this->studentid=$studentid;
	}
    /**
     * This function is used to generate and display course filter.
     *
     */
    public function report_selector_form() {
        $renderable = $this->renderable;
        $courses = $renderable->get_course_list();
        $selectedcourseid = empty($renderable->course) ? 0 : $renderable->course->id;

        echo html_writer::start_tag('form', array('class' => 'logselecform', 'action' => 'course.php', 'method' => 'get'));
        echo html_writer::start_div();
        echo html_writer::label(get_string('selectacourse'), 'courseid', false);
        echo html_writer::select($courses, "id", $selectedcourseid, null, array('id' => 'courseid'));
        echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('generate', 'report_chart')));
        echo html_writer::end_div();
        echo html_writer::end_tag('form');
    }
    /**
     * Display course related chart reports.
     */
    public function report_generate_charts() {
        $renderable = $this->renderable;
        echo $renderable->get_gcharts_data();
        echo $renderable->mostactiveusers;
        echo $renderable->mosttriggeredevents;
        echo $renderable->activitybyperiod;
		echo $renderable->coursegrade;
    }

    /**
     * Display site related chart reports.
     */
    public function report_course_activity_chart() {
        $this->renderable->get_courses_activity();
		echo $this->renderable->coursegrade; 
    }
	public function report_chart() {
        global $USER;
        global $DB;
		
		$output = '';
		$graphicbox='';

		//is teacher?
		$thisuser=new stdClass();
		$thisuser->roleid=5;
		$sql="select r.roleid as roleid,r.contextid,u.id,u.username from {role_assignments} r,{user} u where r.userid=".$USER->id." AND u.id=".$USER->id;
		$user=$DB->get_records_sql($sql);
		foreach($user as $user){
			$thisuser=$user;
		}
		if($thisuser->roleid==3||$thisuser->roleid==1){
			 /*
				$selecteduser=new stdClass();
				$selecteduser->id=3;
				$selecteduser->username="student1";	
			 */
			 //find all student in this course.
			 $sql="select r.userid,u.id,u.username from {role_assignments} r,{user} u where r.roleid=5 AND r.contextid=".$thisuser->contextid." AND r.userid=u.id";
			 $users=$DB->get_records_sql($sql);
			 
			 $usersincourse=array();
			 $i=0;
			 foreach($users as $useri){
				 $usersincourse[$i]=new stdClass();
				 $usersincourse[$i]->id=$useri->id;
				 $usersincourse[$i]->username=$useri->username;
				 $i++;
			 }	 
			 $option='';
			 foreach($usersincourse as $usersincoursei){
				 $option.='<option value ="'.$usersincoursei->id.'" label="'.$usersincoursei->username.'">'.$usersincoursei->username.'</option>';
			 }
			 
			 $graphicbox.='
			 <script type="text/javascript" src="./js/jquery.js"></script>
			 <script type="text/javascript" src="./js/selectstudent.js"></script>
				<div>
					<select id="selectstudent">'
					.$option.
                   '</select>
				</div>
			 ';
			 if(!empty($this->studentid)){
				 $graphicbox.='<script type="text/javascript">
								document.getElementById("selectstudent").value='.$this->studentid.';
                              </script>';
				 $this->renderable->setUser($this->user);			  
			 }
			 else{	 
			 //default the first student.
				 if(!empty($usersincourse)){
					$selecteduser=$usersincourse[0];
					$this->renderable->setUser($selecteduser);
			     }
			 }
		}
		
        $this->renderable->get_courses_report();
	
        $graphicbox.='<div style="overflow:hidden;">
						<div style="overflow:hidden;padding-bottom:20px;"> 
							<div style="float:left;width:50%;min-width:600px;overflow:hidden;">'.$this->renderable->coursegrade.'</div>
							<div style="float:left;width:50%;min-width:600px;overflow:hidden;"><div style="margin:40px 40px 5px 40px;font-size:13px"><strong>Accuracy By Type</strong></div>'.$this->renderable->gettablebytype.'</div>
						</div>	
						
						<div style="overflow:hidden;padding-bottom:20px;"> 
							<div style="float:left;width:50%;min-width:600px">'.$this->renderable->accuracyclassifybytopic.'</div>
							<div style="float:left;width:50%;min-width:600px">'.$this->renderable->peertopeercomparison.'</div>
						</div>
						
						<div style="overflow:hidden;padding-bottom:50px;">  
							<div style="float:left;width:50%;min-width:600px">'.$this->renderable->getcoursegradetrend1.'</div>
							<div style="float:left;width:50%;min-width:600px">'.$this->renderable->getcoursespeedlimittrend.'</div>
						</div>
						
						<div style="overflow:hidden;padding-bottom:20px;padding-left:120px;">  
							<div style="float:left;width:50%;min-width:600px">'.$this->renderable->getcoursespeedmeter.'</div>
							<div style="float:left;width:50%;min-width:600px"></div>
						</div>
					  </div>';	

		
		$output.= $this->box($graphicbox);
		return $output;
    }
}
