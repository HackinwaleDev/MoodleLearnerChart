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
 * Graphic report
 *
 * @package    report_chart
 * @copyright  2014 onwards Simey Lameze <lameze@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../config.php');
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/report/chart/lib/gcharts.php');
require_login();
/**
 * Graphic report class.
 *
 * Retrieve log data, organize in the required format and send to google charts API.
 *
 * @package    report_chart
 * @copyright  2015 onwards Simey Lameze <lameze@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_chart extends Gcharts {

    /**
     * @var int|null the course id.
     */
    protected $courseid;
    /**
     * @var int the current year.
     */
    protected $year;
    /**
     * @var \core\log\sql_SELECT_reader instance.
     */
    protected $logreader;
    /**
     * @var  string Log reader table name.
     */
    protected $logtable;

	
	protected $user;
    /**
     * Graphic report constructor.
     *
     * Retrieve events log data to be used by other methods.
     *
     * @param int|null $courseid course id.
     */
    public function __construct($courseid = null) {
		global $USER;
		//$this->user=new stdClass();
		$this->user=$USER;
        $this->courseid = $courseid;
        $this->year = date('Y');

        // Get the log manager.
        $logreader = get_log_manager()->get_readers();
        $logreader = reset($logreader);
        $this->logreader = $logreader;

        // Set the log table.
        $this->logtable = $logreader->get_internal_log_table_name();
    }

    public function setUser($user){
		//$this->user=new stdClass();
		$this->user=$user;
	}
   
	
	 // //my1
	// public function get_course_avggrade() {
        // global $DB;
		// 
		// 
		
		
	   // $data[0] = array('course', 'grade');
	   
        // $sql = "select id,shortname from {course}";	  
        // $courses = $DB->get_records_sql($sql);
		
		// $i = 1;
		// //every course avg grade
		// foreach ($courses as $course){
			// if($course->shortname!="moodle"){
				// $sql ="select id from {quiz} where course=".$course->id." order by timemodified limit 1";
			    // $coursequizs = $DB->get_records_sql($sql);
				// //get avggrade of the quiz of this course
				// foreach ($coursequizs as $coursequiz){
				    // $sqlcoursequizgrade ="select avg(sumgrades) as avggrade from {quiz_attempts} where quiz=".$coursequiz->id;
			        // $grades = $DB->get_records_sql($sqlcoursequizgrade);
					
					// foreach($grades as $grade){
					    // $data[$i] = array($course->shortname, (float)$grade->avggrade);
					// }
                    // $i++;
				// }
			// }
		// }
		// if(empty($data[1])){
			// $data[1] = array("NoRecord", (float)0);
		// }
        // $this->load(array('graphic_type' => 'ColumnChart'));
        // $this->set_options(array('title' => 'Exam performance summary'));
        // return $this->generate($data);
    // }
	// //endmy1
	
	// //my1 sevelquiz
	// public function get_course_grade() {
        // global $DB;
		// 
		// 
		
		
	   // $data[0] = array('course', 'Grade');
	   
        // $sql = "select id,shortname from {course}";	  
        // $courses = $DB->get_records_sql($sql);
		
		// $i = 1;
		// //every course avg grade
		// foreach ($courses as $course){
			// if($course->shortname!="moodle"){
				// $sql ="select id from {quiz} where course=".$course->id;
			    // $coursequizs = $DB->get_records_sql($sql);
				// //get  user grade of this quiz
				// $sumgrade=0;
				// $quizcount=0;
				// $avggrade=0;
				// //there will be many quizs of the course.
				// foreach ($coursequizs as $coursequiz){
				    // $sqlcoursequizgrade ="select sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        // $grade = $DB->get_records_sql($sqlcoursequizgrade);
					// foreach($grade as $grade){
					    // $sumgrade+=(float)$grade->sumgrades;
						// $quizcount++;
					// }
				// }
				// if($quizcount!=0){
					// $avggrade=(float)$sumgrade/$quizcount;
				// }
				// $data[$i] = array($course->shortname, (float)$avggrade);
				// $i++;
			// }
		// }
		// if(empty($data[1])){
			// $data[1] = array("NoRecord", (float)0);
		// }
        // $this->load(array('graphic_type' => 'ColumnChart'));
        // $this->set_options(array('title' => 'Exam performance summary(By subject)'));
        // return $this->generate($data);
    // }
	// //endmy1
	
  //my1 newlyquiz
	public function get_course_grade() {
        global $DB;
		
		
		$maxgrade=100;
		
	   $data[0] = array('course', 'Grade');
	   
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$i = 1;
		//every course avg grade
		foreach ($courses as $course){
			if($course->shortname!="moodle"){
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;
			    $coursequizs = $DB->get_records_sql($sql);
				//$newlyquiz=new stdClass();
				//find the newly quiz and
				//get user grade of this quiz
				$islastestquizgrade=0;
				$lastest=0;
				//$avggrade=0;
				//there will be many quizs of the course,we should find the lastest.
				foreach ($coursequizs as $coursequiz){
				    $sqlcoursequizgrade ="select sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $grade = $DB->get_records_sql($sqlcoursequizgrade);
					if(empty($grade)){
						continue;
					}else{
						$islastestquizgrade=1;
					}
					foreach($grade as $grade){
					    $lastest=(float)$grade->sumgrades;
					}
					if($islastestquizgrade!=0){
						break;
					}
				}
				$data[$i] = array($course->shortname, (float)$lastest);
				$i++;
			}
		}
		if(!isset($data)){
			$data[0] = array('course','Grade');
		}
		if(empty($data[1])){
			$data[1] = array("NoRecord", (float)0);
		}
		
		$colors=new stdClass();
		//$colors->color="['#e2431e', '#d3362d', '#e7711b']";
		$colors->color="['green','blue','red','black']";
        $this->load(array('graphic_type' => 'BarChart'));
        //$this->set_options(array('title' => '1Lastest Exam Performance Summary(By Subject)','legend'=>'bottom','hAxis'=>array('minValue'=>0,'maxValue'=>100,'format'=>'#','gridlines'=>array('count'=>6))));
		$this->set_options(
		                   array('color'=>'red',
						         'series'=>array('color'=>'black'),
								 'title' => 'Lastest Exam Performance Summary(By Subject)',
								 'legend'=>'none',
								 'hAxis'=>array('title'=>'Grade',
								                'minValue'=>0,
												'maxValue'=>$maxgrade,
												'format'=>'#',
												'gridlines'=>array('count'=>6)),
								 'colors'=>$colors
								 ));
        return $this->generate($data);
    }
	//endmy1
	
	
//mycode2
		public function get_accuracy_classify_by_type() {
        global $DB;
		
		
		
	   $data[0] = array('course', 'Accuracy');
	   //select course id
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$i = 1;
		//for every course
		foreach ($courses as $course){
			if($course->shortname!="moodle"){
				//select newly quiz, for every course, one quiz is new.
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;;
			    $coursequizs = $DB->get_records_sql($sql);
				$findattemptid=0;
				//several quizs,find the latest
				foreach ($coursequizs as $coursequiz){
					//select all attempts of this quiz
					//select the uniqueid by quizid and this->user->id;
				    $sqlthisquizattemps ="select uniqueid from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $attemptid = $DB->get_records_sql($sqlthisquizattemps);					
					//for all attempt,count the accuracy by the testtype. 
					if(empty($attemptid)){
						continue;
					}else{
						$findattemptid=1;
					}					
					$testtypes = array();
					$j=0;
					foreach($attemptid as $attemptid){	
					//acconding to the mld_quiz_attempts->uniqueid,find the usersquestionresponses
			        //in the mdl_question_attempts by questionusageid.
					$sqluserquestionresponses ="select questionid,rightanswer,responsesummary from {question_attempts} where questionusageid=".$attemptid->uniqueid;
					$usersquestionresponses=$DB->get_records_sql($sqluserquestionresponses);   
					    //for each userquestionresponses, find the testtype of the question count its accuracy.
						//for each question, find its testtype and count uesrreponses is right or no.
						//for a student, his/her attempts include many questions as follow.
						//for the every question
						foreach($usersquestionresponses as $userquestionresponses){
							//get testtype of this question.
						    $sqlquestion="select id,testtype from {question} where id=".$userquestionresponses->questionid;
						    $questions=$DB->get_records_sql($sqlquestion);
						    //get testtype of this question. execute only once.
							foreach ($questions as $question)
							{
								if($question->testtype=="NULL"||$question->testtype=="null"||$question->testtype==""){
									$question->testtype="other";
								}
								//int $flag, 1 if the testtype in testtypes else 0; 
								$flag=0;
								//$question->testtype="null";
								//$question->rightanswer="123";
								//$question->responsesummary="123";
								if(empty($testtypes)){
									$testtypes[$j]=new stdClass();		
										$testtypes[$j]->testtype=$question->testtype;
									$testtypes[$j]->questionsum=1;
									$testtypes[$j]->rightanswernum=0;
									if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
										$testtypes[$j]->rightanswernum++;
									}
									$j++;
								}
								else{
									foreach ($testtypes as $testtype){
										
										if($testtype->testtype==$question->testtype){
											$flag=1;
											$testtype->questionsum++;
											if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
												$testtype->rightanswernum++;
											}
										}
									}							
									if($flag==0){
										$testtypes[$j]=new stdClass();	
											$testtypes[$j]->testtype=$question->testtype;		
										$testtypes[$j]->questionsum=1;
										$testtypes[$j]->rightanswernum=0;
										if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
											$testtypes[$j]->rightanswernum++;
										}
										$j++;
									}
								}
							}					
					    }				
					}
					if(!(empty($testtypes))){
						foreach($testtypes as $testtype){
							$data[$i] = array($course->shortname."_".$testtype->testtype, (float)$testtype->rightanswernum*1.0/$testtype->questionsum);
							$i++;
						}
						break;
					}
					if($findattemptid!=0){
						break;
					}		
				}
			}
		}
		if(!isset($data)){
			$data[0] = array('course','Accuracy');
		}		
		if(empty($data[1])){
			$data[1] = array("NoRecord", (float)0);
		}
        $this->load(array('graphic_type' => 'BarChart'));
        $this->set_options(array('title' => 'Accuracy By Test Type',
		                         'legend'=>'none',
								 'hAxis'=>array('title'=>'Accuracy',
								                'minValue'=>0,
												'format'=>'percent',
												'maxValue'=>1,
												'gridlines'=>array('count'=>6)),
								));
        return $this->generate($data);
    }
	//endmycode2
	
	
// //my3
	// public function get_accuracy_classify_by_topic() {
        // global $DB;
	   // $data[0] = array('course', 'Accuracy');
	   // //select course id
        // $sql = "select id,shortname from {course}";	  
        // $courses = $DB->get_records_sql($sql);
		
		// $i = 1;
		// //for every course
		// foreach ($courses as $course){
			// if($course->shortname!="moodle"){
				// //select newly quiz, for every course, one quiz is new.
				// $sql ="select id from {quiz} where course=".$course->id." order by timemodified desc limit 1";
			    // $newlyquizid = $DB->get_records_sql($sql);
				
				// //for every course, one quiz is new. so this execute once.
				// foreach ($newlyquizid as $newlyquizid){
					// //select all attempts of this quiz
				    // $sqlthisquizattemps ="select uniqueid from {quiz_attempts} where quiz=".$newlyquizid->id;
			        // $attemptsids = $DB->get_records_sql($sqlthisquizattemps);
					
					// //for all attempt,count the accuracy by the topic. 
					
					// $topics = array();
					// $j=0;
					// foreach($attemptsids as $attemptid){	
					// //acconding to the mld_quiz_attempts->uniqueid,find the usersquestionresponses
			        // //in the mdl_question_attempts by questionusageid.
					// $sqluserquestionresponses ="select questionid,rightanswer,responsesummary from {question_attempts} where questionusageid=".$attemptid->uniqueid;
					// $usersquestionresponses=$DB->get_records_sql($sqluserquestionresponses);   
					    // //for each userquestionresponses, find the topic of the question count its accuracy.
						// //for each question, find its topic and count uesrreponses is right or no.
						// //for a student, his/her attempts include many questions as follow.
						// //for the every question
						// foreach($usersquestionresponses as $userquestionresponses){
							// //get topic of this question.
						    // $sqlquestion="select id,topic from {question} where id=".$userquestionresponses->questionid;
						    // $questions=$DB->get_records_sql($sqlquestion);
						    // //get topic of this question. execute only once.
							// foreach ($questions as $question)
							// {
								// if($question->topic=="NULL"||$question->topic=="null"||$question->topic==""){
									// $question->topic="other";
								// }
								// //int $flag, 1 if the topic in topics else 0; 
								// $flag=0;
								// //$question->topic="null";
								// //$question->rightanswer="123";
								// //$question->responsesummary="123";
								// if(empty($topics)){
									// $topics[$j]=new stdClass();		
										// $topics[$j]->topic=$question->topic;
									// $topics[$j]->questionsum=1;
									// $topics[$j]->rightanswernum=0;
									// if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
										// $topics[$j]->rightanswernum++;
									// }
									// $j++;
								// }
								// else{
									// foreach ($topics as $topic){
										
										// if($topic->topic==$question->topic){
											// $flag=1;
											// $topic->questionsum++;
											// if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
												// $topic->rightanswernum++;
											// }
										// }
									// }							
									// if($flag==0){
										// $topics[$j]=new stdClass();	
											// $topics[$j]->topic=$question->topic;		
										// $topics[$j]->questionsum=1;
										// $topics[$j]->rightanswernum=0;
										// if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
											// $topics[$j]->rightanswernum++;
										// }
										// $j++;
									// }
								// }
							// }					
					    // }				
					// }
					// foreach($topics as $topic){
						// $data[$i] = array($course->shortname."_".$topic->topic, (float)$topic->rightanswernum*1.0/$topic->questionsum);
						// $i++;
					// }			
				// }
			// }
		// }
		// if(empty($data[1])){
			// $data[1] = array("NoRecord", (float)0);
		// }
        // $this->load(array('graphic_type' => 'ColumnChart'));
        // $this->set_options(array('title' => 'Accuracy_By_Test_topic'));
        // return $this->generate($data);
    // }
	// //endmy3

	//my3  //lastest Accuracy_by_topic
	public function get_accuracy_classify_by_topic() {
        global $DB;
		
		
	   $role=new stdClass();
	   $role->rolestring="{'role':'annotationText'}";
	   $data[0] = array('course',$role,'Accuracy');
	   //select course id
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);

		
		$i = 1;
		//for every course
		foreach ($courses as $course){
			if($course->shortname!="moodle"){
				//select newly quiz, for every course, one quiz is new.
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;;
			    $coursequizs = $DB->get_records_sql($sql);
				$findattemptid=0;
				//several quizs,find the latest
				foreach ($coursequizs as $coursequiz){
					//select all attempts of this quiz
					//select the uniqueid by quizid and this->user->id;
				    $sqlthisquizattemps ="select uniqueid from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $attemptid = $DB->get_records_sql($sqlthisquizattemps);					
					//for all attempt,count the accuracy by the topic. 
					if(empty($attemptid)){
						continue;
					}	
					else{
						$findattemptid=1;
					}
					$topics = array();
					$j=0;
					foreach($attemptid as $attemptid){	
					//acconding to the mld_quiz_attempts->uniqueid,find the usersquestionresponses
			        //in the mdl_question_attempts by questionusageid.
					$sqluserquestionresponses ="select questionid,rightanswer,responsesummary from {question_attempts} where questionusageid=".$attemptid->uniqueid;
					$usersquestionresponses=$DB->get_records_sql($sqluserquestionresponses);   
					    //for each userquestionresponses, find the topic of the question count its accuracy.
						//for each question, find its topic and count uesrreponses is right or no.
						//for a student, his/her attempts include many questions as follow.
						//for the every question
						foreach($usersquestionresponses as $userquestionresponses){
							//get topic of this question.
						    $sqlquestion="select id,topic from {question} where id=".$userquestionresponses->questionid;
						    $questions=$DB->get_records_sql($sqlquestion);
						    //get topic of this question. execute only once.
							foreach ($questions as $question)
							{
								if($question->topic=="NULL"||$question->topic=="null"||$question->topic==""){
									$question->topic="other";
								}
								//int $flag, 1 if the topic in topics else 0; 
								$flag=0;
								//$question->topic="null";
								//$question->rightanswer="123";
								//$question->responsesummary="123";
								if(empty($topics)){
									$topics[$j]=new stdClass();		
										$topics[$j]->topic=$question->topic;
									$topics[$j]->questionsum=1;
									$topics[$j]->rightanswernum=0;
									if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
										$topics[$j]->rightanswernum++;
									}
									$j++;
								}
								else{
									foreach ($topics as $topic){
										
										if($topic->topic==$question->topic){
											$flag=1;
											$topic->questionsum++;
											if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
												$topic->rightanswernum++;
											}
										}
									}							
									if($flag==0){
										$topics[$j]=new stdClass();	
											$topics[$j]->topic=$question->topic;		
										$topics[$j]->questionsum=1;
										$topics[$j]->rightanswernum=0;
										if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
											$topics[$j]->rightanswernum++;
										}
										$j++;
									}
								}
							}					
					    }				
					}
					if(!(empty($topics))){
						foreach($topics as $topic){
							$data[$i] = array($topic->topic,$course->shortname,(float)($topic->rightanswernum*1.0/$topic->questionsum));
							$i++;
						}
						break;
					}
					if($findattemptid!=0){
						break;
					}
						
				}
			}
		}
		if(!isset($data)){
			$data[0] = array('course','','Accuracy');
		}
		if(empty($data[1])){
			$data[1] = array("NoRecord",'NoRecord',(float)0);
		}
/*         $this->load(array('graphic_type' => 'BarChart'));
        $this->set_options(array('title' => 'Accuracy By Test Topic',  
								 'hAxis'=>array('title'=>'Accuracy',
								                'minValue'=>0,
												'format'=>'percent',
												'maxValue'=>1,
												'gridlines'=>array('count'=>6)),
								'legend'=>'none'
								)); */
								
								
		$this->load(array('graphic_type' => 'BarChart',
		                  'dashboard_div'=>True,
						  'filter_div'=>True,
						  //'chart_div'=>True,
						  'control_type'=>'CategoryFilter'
						  ));

		$this->set_control_options(array(
		                         'filterColumnIndex'=>1,
								 'ui'=>array('allowMultiple'=>0,
								             'allowNone' =>0,
											 'label'=>'course'
											),		
								 ));	
        $this->set_options(array('title' => 'Accuracy By Test Topic',
								 'legend'=>'none',
								 'hAxis'=>array('title'=>'Accuracy',
								                'minValue'=>0,
												'format'=>'percent',
												'maxValue'=>1,
												'gridlines'=>array('count'=>6)),
								 'height'=>400,
								 'width'=>600,
								 'chartArea'=>array(
                                    'top'=>30,
									'bottom'=>70,
                                  )
								 ));																					
								
        return $this->generate($data);
    }
	//endmy3
	
	//my2
	// public function get_accuracy_classify_by_type() {
        // global $DB;
	   // $accuracyclassifybytype[0] = array('course', 'Accuracy');
	   // //select course id
        // $sql = "select id,shortname from {course}";	  
        // $courses = $DB->get_records_sql($sql);
		
		// $i = 1;
		// //for every course
		// foreach ($courses as $course){
			// if($course->shortname!="moodle"){
				// //select newly quiz, for every course, one quiz is new.
				// $sql ="select id as id from {quiz} where course=".$course->id." order by timemodified desc limit 1";
			    // $newlyquizid = $DB->get_records_sql($sql);
				
				// //for every course, one quiz is new. so this execute once.
				// foreach ($newlyquizid as $newlyquizid){
					// //select all attempts of this quiz
				    // $sqlthisquizattemps ="select uniqueid from {quiz_attempts} where quiz=".$newlyquizid->id;
			        // $attemptsids = $DB->get_records_sql($sqlthisquizattemps);
					
					// //for all attempt,count the accuracy by the type. 
					
					// $types = array();
					// $j=0;
					// foreach($attemptsids as $attemptid){	
					// //acconding to the mld_quiz_attempts->uniqueid,find the usersquestionresponses
			        // //in the mdl_question_attempts by questionusageid.
					// $sqluserquestionresponses ="select questionid,rightanswer,responsesummary from {question_attempts} where questionusageid=".$attemptid->uniqueid;
					// $usersquestionresponses=$DB->get_records_sql($sqluserquestionresponses);   
					    // //for each userquestionresponses, find the type of the question count its accuracy.
						// //for each question, find its type and count uesrreponses is right or no.
						// //for a student, his/her attempts include many questions as follow.
						// //for the every question
						// foreach($usersquestionresponses as $userquestionresponses){
							// //get type of this question.
						    // $sqlquestion="select id,catagory as type from {question} where id=".$userquestionresponses->questionid;
						    // $questions=$DB->get_records_sql($sqlquestion);
						    // //get type of this question. execute only once.
							// foreach ($questions as $question)
							// {
								// if($question->type=="NULL"||$question->type=="null"||$question->type==""){
									// $question->type="other";
								// }
								// //int $flag, 1 if the type in types else 0; 
								// $flag=0;
								// //$question->type="null";
								// //$question->rightanswer="123";
								// //$question->responsesummary="123";
								// if(empty($types)){
									// $types[$j]=new stdClass();		
										// $types[$j]->type=$question->type;
									// $types[$j]->questionsum=1;
									// $types[$j]->rightanswernum=0;
									// if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
										// $types[$j]->rightanswernum++;
									// }
									// $j++;
								// }
								// else{
									// foreach ($types as $type){
										
										// if($type->type==$question->type){
											// $flag=1;
											// $type->questionsum++;
											// if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
												// $type->rightanswernum++;
											// }
										// }
									// }							
									// if($flag==0){
										// $types[$j]=new stdClass();	
											// $types[$j]->type=$question->type;		
										// $types[$j]->questionsum=1;
										// $types[$j]->rightanswernum=0;
										// if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
											// $types[$j]->rightanswernum++;
										// }
										// $j++;
									// }
								// }
							// }					
					    // }				
					// }
					// foreach($types as $type){
						// $accuracyclassifybytype[$i] = array($course->shortname."_".$type->type, (float)$type->rightanswernum*1.0/$type->questionsum);
						// $i++;
					// }			
				// }
			// }
		// }
		// if(empty($accuracyclassifybytype[1])){
			// $accuracyclassifybytype[1] = array("NoRecord", (float)0);
		// }
        // $this->load(array('graphic_type' => 'ColumnChart'));
        // $this->set_options(array('title' => 'Accuracy_By_Test_type'));
        // return $this->generate($accuracyclassifybytype);
    // }
	//endmy2
	

	
	
/* 		    //my4 get_peer_to_peer_comparison
	public function get_peer_to_peer_comparison($coursek=1) {
        global $DB;
		
		
		
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		$data[0] = array('course','Standard Deviation','avggrade-std','avggrade','100','Standard Deviation','avggrade','avggrade+std','100','YourPosition');
		$i = 1;
        //$k = 0;

		//every course avg grade
		foreach ($courses as $course){		
			if($course->shortname!="moodle"){
				///$k++;
				//if($k!=$coursek){
				//	continue;
				//}
				//$data[0] = array('course', $course->shortname.'-'.'AVERAGE',$this->user->username);
				
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc limit 1" ;
			    $coursequizs = $DB->get_records_sql($sql);
				//$newlyquiz=new stdClass();
				//find the newly quiz and
				//get user grade of this quiz
				//$k++;
				$quizgrade=0;
				$j=0;
				
				//there may be many quizs sumgrade of the user,we should find the lastest.
				foreach ($coursequizs as $coursequiz){
				    $sqlcoursequizgrade ="select sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $grade = $DB->get_records_sql($sqlcoursequizgrade);
					
					if(empty($grade)){
						continue;
					}
					foreach($grade as $grade){
						$sqlcoursequizavggrade ="select STD(sumgrades) as stdgrade,avg(sumgrades) as avggrade from {quiz_attempts} where quiz=".$coursequiz->id;
						$avggrade = $DB->get_records_sql($sqlcoursequizavggrade);
						$quizgrade=(float)$grade->sumgrades;
						foreach($avggrade as $avggrade){
							$j++;
							$topvalue=$avggrade->avggrade+$avggrade->stdgrade;
							$avgvalue=$avggrade->avggrade;
							$bottomvalue=$avggrade->avggrade-$avggrade->stdgrade;
			
							$data[$i] = array($course->shortname,0,(float)($bottomvalue),(float)($avgvalue),100,0,(float)($avgvalue),(float)($topvalue),100,(float)$quizgrade);
							$i++;
					    }
					}
				}
				if($j==0)
				{
					$data[$i++] = array($course->shortname,0,0,0,100,0,0,0,100,0);
				}
			}
			//if($k==$coursek){
			//	break;
			//}
		}	
		
		if(!isset($data)){
			$data[0] = array('course','','','','','','','','','');
		}
		if(empty($data[1])){
			$data[1] = array("No Record.",0,0,0,0,0,0,0,0,0);
		}
		$colors=new stdClass();
		$colors->color="['#e2431e', '#d3362d', '#e7711b']";
		//'colors'=>$colors,
        $this->load(array('graphic_type' => 'ComboChart'));
        $this->set_options(array('title' => 'Peer_to_Peer_Comparison',
							'isStacked'=>'true',
		                    'seriesType'=>'scatter',
							'colors'=>$colors,
		                    'series'=>array(
							       '0'=>array('type'=>'candlesticks','color'=>'#f7d23c','isStacked'=>'true'),
								   '1'=>array('type'=>'candlesticks','color'=>'pink','isStacked'=>'true')
							        ),
							'legend'=>'bottom',
							'vAxis'=>array('title'=>'Grade','minValue'=>0,'format'=>'#'),
		                    'hAxis'=>array('title'=>'Course','minValue'=>0,'format'=>'#')							
							));
        return $this->generate($data);
				
    }
	//endmycode4 */
	
	
   //my4 get_peer_to_peer_comparison
	public function get_peer_to_peer_comparison() {
        global $DB;
		
		
		
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		$data[0] = array('course','STD','avggrade-std','avggrade+std','100','YourPosition');
		$i = 1;
        //$k = 0;

		//every course avg grade
		foreach ($courses as $course){		
			if($course->shortname!="moodle"){

				
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;
			    $coursequizs = $DB->get_records_sql($sql);

				$havajoinedlastestquiz=0;
				$quizgrade=0;
				$j=0;
				
				//there may be many quizs sumgrade of the user,we should find the lastest.
				foreach ($coursequizs as $coursequiz){
				    $sqlcoursequizgrade ="select sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $grade = $DB->get_records_sql($sqlcoursequizgrade);
					
					if(empty($grade)){
						continue;
					}else{
						$havajoinedlastestquiz=1;
					}
					foreach($grade as $grade){
						$sqlcoursequizavggrade ="select STD(sumgrades) as stdgrade,avg(sumgrades) as avggrade from {quiz_attempts} where quiz=".$coursequiz->id;
						$avggrade = $DB->get_records_sql($sqlcoursequizavggrade);
						$quizgrade=(float)$grade->sumgrades;
						foreach($avggrade as $avggrade){
							$j++;
							$topvalue=$avggrade->avggrade+$avggrade->stdgrade;
							$avgvalue=$avggrade->avggrade;
							$bottomvalue=$avggrade->avggrade-$avggrade->stdgrade;
			
							$data[$i++] = array($course->shortname,0,(float)($bottomvalue),(float)($topvalue),100,(float)$quizgrade);
					    }
					}
					if($havajoinedlastestquiz!=0){
						break;
					}
				}
				if($j==0)
				{
					$data[$i++] = array($course->shortname,0,0,0,0,0);
				}
			}
		}	
		
		if(!isset($data)){
			$data[0] = array('course','','','','','');
		}
		if(empty($data[1])){
			$data[1] = array("No Record.",0,0,0,0,0);
		}
		$colors=new stdClass();
		$colors->color="['#e2431e', '#d3362d', '#e7711b']";
		//'colors'=>$colors,
        $this->load(array('graphic_type' => 'ComboChart'));
        $this->set_options(array('title' => 'Peer To Peer Comparison',
							'isStacked'=>'true',
		                    'seriesType'=>'scatter',
							'colors'=>$colors,
		                    'series'=>array(
							       '0'=>array('type'=>'candlesticks','color'=>'blue')
							        ),
							'legend'=>'bottom',
							'vAxis'=>array('title'=>'Grade','minValue'=>0,'format'=>'#'),
		                    'hAxis'=>array('title'=>'Course','minValue'=>0,'format'=>'#'),
							'pointSize'=>'8',
							'height'=>420,
							'width'=>600,
							'chartArea'=>array(
                                    'top'=>30,
									'bottom'=>50,
                                  )							
							));
        return $this->generate($data);
				
    }
	//endmycode4
	
	
//mycode5 allquizgrade
	public function get_course_grade_trend() {
        global $DB;
		
		
		
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$role=new stdClass();
		$role->rolestring="{'role':'annotationText'}";
		$k = 0;
		$data[0] = array('course',$role,'AVERAGE',$this->user->username);
		$i = 1;
		//every course avg grade
		 foreach ($courses as $course){		
			if($course->shortname!="moodle"){
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified" ;
			    $coursequizs = $DB->get_records_sql($sql);
				$quizgrade=0;
				$j=0;
				
				//there will be many quizs of the course,we should find the lastest.
				foreach ($coursequizs as $coursequiz){
				    $sqlcoursequizgrade ="select sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $grade = $DB->get_records_sql($sqlcoursequizgrade);
					
					if(empty($grade)){
						continue;
					}
					foreach($grade as $grade){
						$sqlcoursequizavggrade ="select avg(sumgrades) as avggrade from {quiz_attempts} where quiz=".$coursequiz->id;
						$avggrade = $DB->get_records_sql($sqlcoursequizavggrade);
						$quizgrade=(float)$grade->sumgrades;
						foreach($avggrade as $avggrade){
							$j++;
							$data[$i] = array(''.$j,$course->shortname,(float)$avggrade->avggrade,(float)$quizgrade);
							$i++;
					    }
					}
				}		
			}
		} 
		
		if(empty($data[1])){
			$data[1] = array('0','NoRecord',0,0);
		}
		
		$this->load(array('graphic_type' => 'LineChart',
		                  'dashboard_div'=>True,
						  'filter_div'=>True,
						  //'chart_div'=>True,
						  'control_type'=>'CategoryFilter'
						  ));

		$this->set_control_options(array(
		                         'filterColumnIndex'=>1,
								 'ui'=>array('allowMultiple'=>0,
								             'allowNone' =>0,
											 'label'=>'course'
											),		
								 ));	
        $this->set_options(array('title' => 'Grade Trend',
								 'legend'=>'bottom',
								 'hAxis'=>array('title'=>'Test'),
								 'pointSize'=>'8',
								 'height'=>400,
								 'width'=>600,
								 'chartArea'=>array(
                                    'top'=>30,
									'bottom'=>50,
                                  )
								 ));
							 
		return $this->generate($data);
				
    }
	//endmycode5


	
	
		//mycode6 timetaken
	public function get_course_speed_limit_trend() {
        global $DB;
		
		
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$role=new stdClass();
		$role->rolestring="{'role':'annotationText'}";
		$data[0] = array('course',$role,'TimeLimit',$this->user->username);
		$i = 1;
		$k = 0;
		
		//every course avg grade
		foreach ($courses as $course){		
			if($course->shortname!="moodle"){
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified" ;
			    $coursequizs = $DB->get_records_sql($sql);
				$quiztimetaken=0;
				$j=0;
				
				//there will be many quizs of the course,we should find the lastest.
				foreach ($coursequizs as $coursequiz){
				    $sqlcoursequizgrade ="select timefinish,timestart,sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $grade = $DB->get_records_sql($sqlcoursequizgrade);
					
					if(empty($grade)){
						continue;
					}
					foreach($grade as $grade){
						$sqlcoursequizavggrade ="select timelimit as timelimit from {quiz} where id=".$coursequiz->id;
						$timelimit = $DB->get_records_sql($sqlcoursequizavggrade);
						$quiztimetaken=(float)($grade->timefinish-$grade->timestart);
						foreach($timelimit as $timelimit){
							$j++;
							if($timelimit->timelimit<=0)
							{
								$timelimit->timelimit=0;
							}
							if($quiztimetaken<=0)
							{
								$quiztimetaken=0;
							}
							$data[$i++] = array(''.$j,$course->shortname,round((float)$timelimit->timelimit/60.0,1),round((float)$quiztimetaken/60.0,1));
					    }
					}
				}		
			}
		}
		
		if(!isset($data)){
			$data[0] = array('course','','AVERAGE',$this->user->username);
		}
		if(empty($data[1])){
			$data[1] = array("NoRecord.",'NoRecord',(float)0,0);
		}
      /*   $this->load(array('graphic_type' => 'LineChart'));
        $this->set_options(
		                   array('title' => 'Speed limit Trend','legend'=>'bottom',
					             'vAxis'=>array('title'=>'Time(min)','minValue'=>0),
					             'hAxis'=>array('title'=>'Test'),
								 'pointSize'=>'8',
								 'height'=>400,
								 'width'=>600,
								 'chartArea'=>array(
                                    'top'=>70,
                                  )
								 )); */
								 
		$this->load(array('graphic_type' => 'LineChart',
		                  'dashboard_div'=>True,
						  'filter_div'=>True,
						  //'chart_div'=>True,
						  'control_type'=>'CategoryFilter'
						  ));

		$this->set_control_options(array(
		                         'filterColumnIndex'=>1,
								 'ui'=>array('allowMultiple'=>0,
								             'allowNone' =>0,
											 'label'=>'course'
											),		
								 ));	
        $this->set_options(array('title' => 'Speed Trend',
								 'legend'=>'bottom',
								 'hAxis'=>array('title'=>'Test'),
								 'vAxis'=>array('title'=>'Time(min)','minValue'=>0),
								 'pointSize'=>'8',
								 'height'=>400,
								 'width'=>600,
								 'chartArea'=>array(
                                    'top'=>30,
									'bottom'=>50,
                                  )
								 ));
								 
        return $this->generate($data);
				
    }
	//endmycode7
	
			//mycode6 timetaken
	public function get_course_speedmeter() {
        global $DB;
		
		
		
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$role=new stdClass();
		$role->rolestring="{'role':'annotationText'}";
        $data[0] = array('Label',$role,'min');
		$i = 1;
		$TimeLimitToMinute=0;
		$MaxTimeLimitToMinute=10;
		$maxoneoftimelimitandtimetaken=0;
		$MaxOneOfTimeLimitAndTimeTaken=0;
		//every course avg grade
		foreach ($courses as $course){		
			if($course->shortname!="moodle"){
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;
			    $coursequizs = $DB->get_records_sql($sql);
				$quiztimetaken=0;
				$j=0;
				$flag=0;
				
				//there will be many quizs of the course,we should find the lastest.
				foreach ($coursequizs as $coursequiz){
				    $sqlcoursequizgrade ="select timefinish,timestart,sumgrades from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $grade = $DB->get_records_sql($sqlcoursequizgrade);
					
					if(empty($grade)){
						continue;
					}
					foreach($grade as $grade){
						$sqlcoursequizavggrade ="select timelimit as timelimit from {quiz} where id=".$coursequiz->id;
						$timelimit = $DB->get_records_sql($sqlcoursequizavggrade);
						$quiztimetaken=(float)($grade->timefinish-$grade->timestart)/60.0;
						foreach($timelimit as $timelimit){
							$j++;
							$quiztimelimit=$timelimit->timelimit/60;
							$TimeLimitToMinute=$quiztimelimit;
							if($MaxTimeLimitToMinute<$quiztimelimit){
			                    $MaxTimeLimitToMinute=$quiztimelimit;
                            }
						    $maxoneoftimelimitandtimetaken=$quiztimetaken;
							if($quiztimetaken<$quiztimelimit){
								$maxoneoftimelimitandtimetaken=$quiztimelimit;
							}
							if($maxoneoftimelimitandtimetaken>$MaxOneOfTimeLimitAndTimeTaken){
								$MaxOneOfTimeLimitAndTimeTaken=$maxoneoftimelimitandtimetaken;
							}
							$maxoneoftimelimitandtimetaken=round($maxoneoftimelimitandtimetaken,1);
							$MaxOneOfTimeLimitAndTimeTaken=round($MaxOneOfTimeLimitAndTimeTaken,1);
							if($quiztimelimit<0)
							{
								$quiztimelimit=0;
							}
							if($quiztimetaken<0)
							{
								$quiztimetaken=0;
							}
							$data[$i++] = array($quiztimelimit.'min',$course->shortname,round($quiztimetaken,1));
							$flag++;
					    }
					}
					if($flag!=0)
					{
						break;
					}
				}		
			}
		}
		
		if(!isset($data)){
			$data[0] = array('Label','','Value');
		}
		if(empty($data[1])){
			$data[1] = array("NoRecord.",'NoRecord',0);
		}
        if($MaxOneOfTimeLimitAndTimeTaken<0)
		{
			$MaxOneOfTimeLimitAndTimeTaken=0;
		}	
        if($TimeLimitToMinute<0)
		{
			$TimeLimitToMinute=0;
		}		
								  
		$this->load(array('graphic_type' => 'gauge',
		                  'dashboard_div'=>True,
						  'filter_div'=>True,
						  'control_type'=>'CategoryFilter'
						  ));

		$this->set_control_options(array(
		                         'filterColumnIndex'=>1,
								 'ui'=>array('allowMultiple'=>0,
								             'allowNone' =>0,
											 'label'=>'course'
											),		
								 ));	
        $this->set_options(array(
								 'pointSize'=>'8',
								 'height'=>300,
								 'width'=>300,
								 'chartArea'=>array(
                                    'top'=>30,
									'bottom'=>50,
                                  ),
								 'greenFrom'=>0,
								 'greenTo'=>$MaxTimeLimitToMinute,
								 'redFrom'=>$MaxTimeLimitToMinute,
								 'redTo'=>$MaxOneOfTimeLimitAndTimeTaken,
								 'minorTicks'=>10,
								 'max'=>round($MaxOneOfTimeLimitAndTimeTaken,1)
								 ));
        return $this->generate($data);
				
    }
	//endmycode7
	
	//mycode8 this is the table example
		public function get_accuracy_classify_by_type1() {
        global $DB;
		
		
		
	   $data[0] = array('course', 'Accuracy');
	   //select course id
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$i = 1;
		//for every course
		foreach ($courses as $course){
			if($course->shortname!="moodle"){
				//select newly quiz, for every course, one quiz is new.
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;;
			    $coursequizs = $DB->get_records_sql($sql);
				
				//several quizs,find the latest
				foreach ($coursequizs as $coursequiz){
					//select all attempts of this quiz
					//select the uniqueid by quizid and this->user->id;
				    $sqlthisquizattemps ="select uniqueid from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $attemptid = $DB->get_records_sql($sqlthisquizattemps);					
					//for all attempt,count the accuracy by the testtype. 
					if(empty($attemptid)){
						continue;
					}				
					$testtypes = array();
					$j=0;
					foreach($attemptid as $attemptid){	
					//acconding to the mld_quiz_attempts->uniqueid,find the usersquestionresponses
			        //in the mdl_question_attempts by questionusageid.
					$sqluserquestionresponses ="select questionid,rightanswer,responsesummary from {question_attempts} where questionusageid=".$attemptid->uniqueid;
					$usersquestionresponses=$DB->get_records_sql($sqluserquestionresponses);   
					    //for each userquestionresponses, find the testtype of the question count its accuracy.
						//for each question, find its testtype and count uesrreponses is right or no.
						//for a student, his/her attempts include many questions as follow.
						//for the every question
						foreach($usersquestionresponses as $userquestionresponses){
							//get testtype of this question.
						    $sqlquestion="select id,testtype from {question} where id=".$userquestionresponses->questionid;
						    $questions=$DB->get_records_sql($sqlquestion);
						    //get testtype of this question. execute only once.
							foreach ($questions as $question)
							{
								if($question->testtype=="NULL"||$question->testtype=="null"||$question->testtype==""){
									$question->testtype="other";
								}
								//int $flag, 1 if the testtype in testtypes else 0; 
								$flag=0;
								//$question->testtype="null";
								//$question->rightanswer="123";
								//$question->responsesummary="123";
								if(empty($testtypes)){
									$testtypes[$j]=new stdClass();		
										$testtypes[$j]->testtype=$question->testtype;
									$testtypes[$j]->questionsum=1;
									$testtypes[$j]->rightanswernum=0;
									if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
										$testtypes[$j]->rightanswernum++;
									}
									$j++;
								}
								else{
									foreach ($testtypes as $testtype){
										
										if($testtype->testtype==$question->testtype){
											$flag=1;
											$testtype->questionsum++;
											if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
												$testtype->rightanswernum++;
											}
										}
									}							
									if($flag==0){
										$testtypes[$j]=new stdClass();	
											$testtypes[$j]->testtype=$question->testtype;		
										$testtypes[$j]->questionsum=1;
										$testtypes[$j]->rightanswernum=0;
										if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
											$testtypes[$j]->rightanswernum++;
										}
										$j++;
									}
								}
							}					
					    }				
					}
					if(!(empty($testtypes))){
						foreach($testtypes as $testtype){
							$data[$i] = array($course->shortname."_".$testtype->testtype, (float)$testtype->rightanswernum*1.0/$testtype->questionsum);
							$i++;
						}
						break;
					}
				}
			}
		}
		if(!isset($data)){
			$data[0] = array('course','Accuracy');
		}		
		if(empty($data[1])){
			$data[1] = array("NoRecord", (float)0);
		}
        $this->load(array('graphic_type' => 'BarChart'));
        $this->set_options(array('title' => 'Accuracy By Test Type',
		                         'legend'=>'none',
								 'hAxis'=>array('title'=>'Accuracy',
								                'minValue'=>0,
												'format'=>'percent',
												'maxValue'=>1,
												'gridlines'=>array('count'=>6)),
								 'chartArea'=>array(
                                    //'left'=>200,
									//'right'=>200,
                                  ),				
								));
        //return $this->generate($data);
		return $this->callTableWithoutDashboard();
    }
	//endmycode8
	
	
	
	//mycode9 this is the table example2
		public function get_accuracy_classify_by_type2() {
        global $DB;
		
		

 		$data1[0] = array('course','READ','WRITE','ORAL','GENG','TOTAL');		
        $data2[0] = array('course','paper1','paper2','LAB');
	   //select course id
        $sql = "select id,shortname from {course}";	  
        $courses = $DB->get_records_sql($sql);
		
		$i=1;
		$k=1;
		//for every course
		foreach ($courses as $course){
			if($course->shortname!="moodle"){
				//select newly quiz, for every course, one quiz is new.
				$sql ="select id from {quiz} where course=".$course->id." order by timemodified desc" ;;
			    $coursequizs = $DB->get_records_sql($sql);
				$haveattempt=0;
				//several quizs,find the latest
				foreach ($coursequizs as $coursequiz){
					//select all attempts of this quiz
					//select the uniqueid by quizid and this->user->id;
				    $sqlthisquizattemps ="select uniqueid from {quiz_attempts} where quiz=".$coursequiz->id." and userid=".$this->user->id." order by timemodified desc limit 1";
			        $attemptid = $DB->get_records_sql($sqlthisquizattemps);					
					//for all attempt,count the accuracy by the testtype. 
					if(empty($attemptid)){
						continue;
					}				
					$testtypes = array();
					$j=0;
					if($course->shortname=="ENG"||$course->shortname=="CHI")
					{
						$j=5;
						/* $testtypes[0]=new stdClass();		
						$testtypes[0]->testtype="READ";
						$testtypes[0]->questionsum=10;
						$testtypes[0]->rightanswernum=8;
						
						$testtypes[1]=new stdClass();		
						$testtypes[1]->testtype="WRITE";
						$testtypes[1]->questionsum=10;
						$testtypes[1]->rightanswernum=5;
						
						$testtypes[2]=new stdClass();		
						$testtypes[2]->testtype="ORAL";
						$testtypes[2]->questionsum=10;
						$testtypes[2]->rightanswernum=4;
						
						$testtypes[3]=new stdClass();		
						$testtypes[3]->testtype="GENG";
						$testtypes[3]->questionsum=20;
						$testtypes[3]->rightanswernum=7; */
						
						$testtypes[0]=new stdClass();		
						$testtypes[0]->testtype="READ";
						$testtypes[0]->questionsum=0;
						$testtypes[0]->rightanswernum=0;
						
						$testtypes[1]=new stdClass();		
						$testtypes[1]->testtype="WRITE";
						$testtypes[1]->questionsum=0;
						$testtypes[1]->rightanswernum=0;
						
						$testtypes[2]=new stdClass();		
						$testtypes[2]->testtype="ORAL";
						$testtypes[2]->questionsum=0;
						$testtypes[2]->rightanswernum=0;
						
						$testtypes[3]=new stdClass();		
						$testtypes[3]->testtype="GENG";
						$testtypes[3]->questionsum=0;
						$testtypes[3]->rightanswernum=0;
						
						$testtypes[4]=new stdClass();		
						$testtypes[4]->testtype="TOTAL";
						$testtypes[4]->questionsum=0;
						$testtypes[4]->rightanswernum=0;	
					}else if($course->shortname=="MATH"||$course->shortname=="PHY")
					{
						$j=3;
						/* $testtypes[0]=new stdClass();		
						$testtypes[0]->testtype="paper1";
						$testtypes[0]->questionsum=100;
						$testtypes[0]->rightanswernum=34;
						
						$testtypes[1]=new stdClass();		
						$testtypes[1]->testtype="paper2";
						$testtypes[1]->questionsum=100;
						$testtypes[1]->rightanswernum=45; */
						
						$testtypes[0]=new stdClass();		
						$testtypes[0]->testtype="paper1";
						$testtypes[0]->questionsum=0;
						$testtypes[0]->rightanswernum=0;
						
						$testtypes[1]=new stdClass();		
						$testtypes[1]->testtype="paper2";
						$testtypes[1]->questionsum=0;
						$testtypes[1]->rightanswernum=0;
						
						$testtypes[2]=new stdClass();		
						$testtypes[2]->testtype="LAB";
						$testtypes[2]->questionsum=0;
						$testtypes[2]->rightanswernum=0;	
					}
					foreach($attemptid as $attemptid){
						$haveattempt=1;
					//acconding to the mld_quiz_attempts->uniqueid,find the usersquestionresponses
			        //in the mdl_question_attempts by questionusageid.
					$sqluserquestionresponses ="select questionid,rightanswer,responsesummary from {question_attempts} where questionusageid=".$attemptid->uniqueid;
					$usersquestionresponses=$DB->get_records_sql($sqluserquestionresponses);   
					    //for each userquestionresponses, find the testtype of the question count its accuracy.
						//for each question, find its testtype and count uesrreponses is right or no.
						//for a student, his/her attempts include many questions as follow.
						//for the every question
						foreach($usersquestionresponses as $userquestionresponses){
							//get testtype of this question.
						    $sqlquestion="select id,testtype from {question} where id=".$userquestionresponses->questionid;
						    $questions=$DB->get_records_sql($sqlquestion);
						    //get testtype of this question. execute only once.
							foreach ($questions as $question)
							{
								if($question->testtype=="NULL"||$question->testtype=="null"||$question->testtype==""){
									$question->testtype="other";
								}
								//int $flag, 1 if the testtype in testtypes else 0; 
								$flag=0;
								//$question->testtype="null";
								//$question->rightanswer="123";
								//$question->responsesummary="123";
								if(empty($testtypes)){
									$testtypes[$j]=new stdClass();		
									$testtypes[$j]->testtype=$question->testtype;
									$testtypes[$j]->questionsum=1;
									$testtypes[$j]->rightanswernum=0;
									if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
										$testtypes[$j]->rightanswernum++;
									}
									$j++;
								}
								else{
									foreach ($testtypes as $testtype){									
										if($testtype->testtype==$question->testtype){
											$flag=1;
											$testtype->questionsum++;
											if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
												$testtype->rightanswernum++;
											}
										}
									}							
/* 									if($flag==0){
										$testtypes[$j]=new stdClass();	
										$testtypes[$j]->testtype=$question->testtype;		
										$testtypes[$j]->questionsum=1;
										$testtypes[$j]->rightanswernum=0;
										if($userquestionresponses->rightanswer==$userquestionresponses->responsesummary){
											$testtypes[$j]->rightanswernum++;
										}
										$j++;
									} */
								}
							}					
					    }				
					}
					if($haveattempt==1){
							if($course->shortname=="ENG"||$course->shortname=="CHI"){
								//if don't have this type,set to -1.
								$READAccuracy=0;
								$WRITEAccuracy=0;
								$ORALAccuracy=0;
								$GENGAccuracy=0;
								$TOTALAccuracy=0;
								
								if($testtypes[0]->questionsum==0) { $READAccuracy=-1; }
								else{ 
									$testtypes[4]->rightanswernum+=$testtypes[0]->rightanswernum;
									$testtypes[4]->questionsum+=$testtypes[0]->questionsum;
									$READAccuracy=(float)$testtypes[0]->rightanswernum*100.0/$testtypes[0]->questionsum; 
								}
								
								if($testtypes[1]->questionsum==0) { $WRITEAccuracy=-1; }
								else{
									$testtypes[4]->rightanswernum+=$testtypes[1]->rightanswernum;
									$testtypes[4]->questionsum+=$testtypes[1]->questionsum;
									$WRITEAccuracy=(float)$testtypes[1]->rightanswernum*100.0/$testtypes[1]->questionsum; 
								}
								
								if($testtypes[2]->questionsum==0) { $ORALAccuracy=-1; }
								else{
									$testtypes[4]->rightanswernum+=$testtypes[2]->rightanswernum;
									$testtypes[4]->questionsum+=$testtypes[2]->questionsum;	
									$ORALAccuracy=(float)$testtypes[2]->rightanswernum*100.0/$testtypes[2]->questionsum; 
								}
								
								if($testtypes[3]->questionsum==0) { $GENGAccuracy=-1; }
								else{
									$testtypes[4]->rightanswernum+=$testtypes[3]->rightanswernum;
								    $testtypes[4]->questionsum+=$testtypes[3]->questionsum;
									$GENGAccuracy=(float)$testtypes[3]->rightanswernum*100.0/$testtypes[3]->questionsum; 
								}
								
								if($testtypes[4]->questionsum==0) { $TOTALAccuracy=-1; }
								else{
									$TOTALAccuracy=(float)$testtypes[4]->rightanswernum*100.0/$testtypes[4]->questionsum; 
								}
										
								$data1[$i++] = array(
											    $course->shortname,
												$READAccuracy,
												$WRITEAccuracy,
												$ORALAccuracy,
												$GENGAccuracy,
												$TOTALAccuracy);	
							}else if($course->shortname=="MATH"||$course->shortname=="PHY")
					        {
								$paper1Accuracy=0;
								$paper2Accuracy=0;
								$labstring="B";
								if($testtypes[0]->questionsum==0) { $paper1Accuracy=-1; }
								else{ $paper1Accuracy=(float)$testtypes[0]->rightanswernum*100.0/$testtypes[0]->questionsum; }
								
								if($testtypes[1]->questionsum==0) { $paper2Accuracy=-1; }
								else{ $paper2Accuracy=(float)$testtypes[1]->rightanswernum*100.0/$testtypes[1]->questionsum; }
								
								
								$data2[$k++] = array(
												$course->shortname,
												$paper1Accuracy,
												$paper2Accuracy,
												$labstring);
					        }
						break;
					}
				}
			}
		}
		if(!isset($data1)){
			$data1[0] = array('course','READ','WRITE','ORAL','GENG','TOTAL');
		}		
		if(empty($data1[1])){
			$data1[1] = array('ENG', -1,-1,-1,-1,-1);		
		    $data1[2] = array('CHI', -1,-1,-1,-1,-1);
		}
		
		if(!isset($data2)){
			$data2[0] = array('course','paper1','paper2','LAB');
		}		
		if(empty($data2[1])){
			$data2[1] = array('MATH', -1,-1,'null');		
		    $data2[2] = array('PHY', -1,-1,'null');
		}
        /* $this->load(array('graphic_type' => 'BarChart'));
        $this->set_options(array('title' => 'Accuracy By Test Type',
		                         'legend'=>'none',
								 'hAxis'=>array('title'=>'Accuracy',
								                'minValue'=>0,
												'format'=>'percent',
												'maxValue'=>1,
												'gridlines'=>array('count'=>6)),
								)); */

/*      //Test data
 		$data1[0] = array('course','READ','WRITE','ORAL','GENG','TOTAL');
		$data1[1] = array('ENG', 80,60,90,75,85);		
		$data1[2] = array('CHI', 65,49,60,80,68);
        $data2[0] = array('course','paper1','paper2','LAB');
		$data2[1] = array('MATH', 42,52,'');		
		$data2[2] = array('PHY', 65,30,'B'); */
		
		return $this->DrawTable($data1,$data2);
    }
	//endmycode9
	
	
	
	
	
	
	
}




