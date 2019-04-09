<?php
/*
* GERA GRAFICO DE ESTATISTICAS USANDO API GOOGLE  CHARTS
* CRIADO POR RANIELLY FERREIRA
* WWW.RFS.NET.BR 
* raniellyferreira@rfs.net.br
*
* v. 1.2.2 BETA
* ULTIMA MODIFICAÇÃO: 14/02/2013

** HISTÓRICO DE VERSÕES
1.0.0
- Criado
- Compatibilidade:
	- LineChart,PieChart,GeoChart,Gauge,TreeMap,CandlestickChart

1.0.1
- Corrigido bug na função array_to_jsarray();
- Corrigido bug na função array_to_jsobject();

1.1.4
- Adicionado parametro para alteração do tipo de grafico;
- Adicionado compatibilidade com:
	- ScatterChart, ComboChart, BarChart, ColumnChart, AreaChart 
- Função array_to_jsobject() melhorada;
- Função array_to_jsarray() melhorada;
- Adicionado a opção para criar div automaticamente;
- Função generate() melhorada;
- parametro TAG opcional, se nao setado à função generate(), ele cria id automaticamente, somente se a div for criada automaticamente;
- Função set_options() criada, para setar as opções, parametro options da função gerenate() foi removido;
- Adicionado opções para div de div_class, div_height, div_width, se create_div estiver TRUE, carregar com a função load();

1.1.5
- Correção de erros;

1.2.1
- Adicionado compatibilidade com Controls and Dashboards;

1.2.2
- Correção de erros;

-- COMPATÍVEL
	- LineChart
	- PieChart
	- GeoChart
	- Gauge
	- TreeMap
	- CandlestickChart
	- ScatterChart
	- ComboChart
	- BarChart
	- ColumnChart
	- AreaChart
	-- Controls and Dashboards
	
-- NÃO COMPATÍVEL
	- Table

*/

class Gcharts 
{
	//Instance variables here
	public $library_loaded 		= FALSE;
	public $library2_loaded 	= FALSE;
	public $create_div			= TRUE;
	public $dashboard_div		= NULL;
	public $class_dashboard_div	= NULL;
	public $filter_div			= NULL;
	public $class_filter_div	= NULL;
	public $chart_div			= NULL;
	public $class_chart_div		= NULL;
	public $open_js_tag			= TRUE;
	public $graphic_type 		= 'LineChart'; //LineChart,PieChart,ColumnChart,AreaChart,TreeMap,ScatterChart,Gauge,GeoChart,ComboChart,BarChart,CandlestickChart,Table
	public $control_type		= 'NumberRangeFilter';
	private $gen_options 		= array();
	private $control_options 	= array();
	private $use_dashboard		= FALSE;
	
	//I guess this is how PHP creates constructor, right?
	function __contruct($array = array())
	{
		$this->load($array);
	}
	
	
	public function load_options($options = array())
	{
		if((bool) !$options)
		{
			return false;
		}
		
		$this->options = $options;
		return TRUE;
	}
	
	public function load($array = array())
	{
		if((bool) !$array)
		{
			return false;
		}
		
		foreach(array('library_loaded','library2_loaded','graphic_type','create_div','dashboard_div','filter_div','chart_div','class_filter_div','class_dashboard_div','class_chart_div','open_js_tag','control_type') as $p)
		{
			if(isset($array[$p]))
			{
				if($p == 'graphic_type')
				{
					$this->set_graphic_type($array[$p]);
					continue;
				}
				$this->$p = $array[$p];
			}
		}
	}
	//load the d3js library
	public function load_library()
	{
		if(!$this->library_loaded)
		{
			$this->library_loaded = TRUE;
			// return '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
			return '<script type="text/javascript" src="https://d3js.org/d3.v5.min.js"></script>';
		}
		return NULL;
	}
	//load the charts library
	public function load_library2()
	{
		if(!$this->library2_loaded)
		{
			$this->library2_loaded = TRUE;
			// return '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
			return '<script type="text/javascript" src="lib/js/charts.js"></script>';
		}
		return NULL;
	}
	public function set_graphic_type($type = NULL)
	{
		if(is_null($type)) return false;
		
		$type = strtolower(trim($type));
		
		$types = array(
		'linechart' 		=> 'LineChart',
		'piechart' 			=> 'PieChart',
		'ringchart' 		=> 'RingChart',
		'areachart' 		=> 'AreaChart',
		'donutchart' 		=> 'DonutChart',
		'scatterchart'		=> 'ScatterChart',
		'gauge' 			=> 'Gauge',
		'geochart' 			=> 'GeoChart',
		'combochart' 		=> 'ComboChart',
		'barchart' 			=> 'BarChart',
		'candlestickchart' 	=> 'CandlestickChart',
		'table' 			=> 'Table');
		
		if(!in_array($type,array_keys($types)))
		{
			exit('Error: can not recognize. ['.$type.']');
		}
		
		$this->graphic_type = $types[$type];
		return true;
	}
	
	public function set_options($options = array())
	{
		if((bool) !$options)
		{
			return array();
		}
		
		$this->gen_options = $options;
		return true;
	}
	
	public function set_control_options($options = array())
	{
		if((bool) !$options)
		{
			return array();
		}
		
		$this->control_options = $options;
		return true;
	}
	
	public function generate($data)
	{
		if((bool) !$data)
		{
			return false;
		}
		
		if(is_null($this->chart_div))
		{
			$key = $this->gerarkey(10);
			$this->chart_div = 'gcharts_'.$key;
		}
		
		if($this->dashboard_div === TRUE)
		{
			$this->dashboard_div = 'dashboard_'.$key;
			$this->use_dashboard = TRUE;
		}
		
		if($this->filter_div === TRUE)
		{
			$this->filter_div = 'filter_'.$key;
		}
		
		if($this->use_dashboard === FALSE)
		{
			return $this->GenWithoutDashboard($data);
		} else
		{
			return $this->GenWithDashboard($data);
		}
		
		return false;
	}
	
	private function GenWithDashboard($data)
	{
		
		$js = NULL;
		//load the libraries to suppoer the chart being drawn
		$js .= $this->load_library()."\n";
		$js .= $this->load_library2()."\n";
		
		if($this->open_js_tag === TRUE)
		{
			$js .= '<script type="text/javascript">'."\n";
		}
		
		
		// Set a callback to run when the Google Visualization API is loaded.
		$js .= 'drawDashboard();'."\n";
		
		// Callback that creates and populates a data table,
		// instantiates a dashboard, a range slider and a pie chart,
		// passes in the data and draws it.
		$js .= 'function drawDashboard() {'."\n";
		
		
		// Create our data table.
		$js .= 'var data = '.$this->array_to_jsarray($data).';'."\n";
		$js .= 'var containerId = '.$this->chart_div.';'."\n";
		$js .= 'var chartType = '.$this->graphic_type.';'."\n";
		
		//log the data here
		var_dump($this->array_to_jsarray($data));
		var_dump($this->array_to_jsobject($data));
        // Draw the dashboard.
        $js .= "learnersDashboard(chartType, containerId);\n";
		
		$js .= '}';
			
		if($this->open_js_tag === TRUE)
		{
        	$js .= '</script>'."\n";
		}
		
		/* CRIA AS DIVS */
		if($this->create_div === TRUE)
		{
			/* DASHBOARD DIV */
			if(!is_null($this->dashboard_div))
			{
				$js .= '<div id="'.$this->dashboard_div.'" style="margin:5px 5px 5px 5px;" class="'.$this->class_dashboard_div.'">';
			}
			
			/* FILTER DIV */
			if(!is_null($this->filter_div))
			{
				$js .= '<div style="margin:5px 5px 5px 5px;"; id="'.$this->filter_div.'" class="'.$this->class_filter_div.'"></div>';
			}
			
			
			/* CHART DIV */
			$js .= '<div id="'.$this->chart_div.'" style="margin:5px 5px 5px 5px;" class="'.$this->class_chart_div.'"></div>';
			
			/* DASHBOARD CLOSE DIV */
			if(!is_null($this->dashboard_div))
			{
				$js .= '</div>';
			}
			
		} // FIM CREATE DIV
		$this->clean();
		return $js;
	} //end GenWithDashboard
	
	private function GenWithoutDashboard($data)
	{
		$js = NULL;
		
		$js .= $this->load_library()."\n";
		$js .= $this->load_library2()."\n";
		
		if($this->open_js_tag === TRUE)
		{
			$js .= '<script type="text/javascript">'."\n";
		}
		
		// Load the Visualization API and the controls package.
		$js .= 'google.charts.load("current",{packages:["corechart","gauge"]});'."\n";
		
	
		// Set a callback to run when the Google Visualization API is loaded.
		$js .= 'google.charts.setOnLoadCallback(drawChart);'."\n";
		
		// Callback that creates and populates a data table,
		// instantiates a dashboard, a range slider and a pie chart,
		// passes in the data and draws it.
		$js .= 'function drawChart() {'."\n";
		
		// Create our data table.
		$js .= 'var data = '.$this->array_to_jsarray($data).';'."\n";
		$js .= 'var containerId = document.getElementById('.$this->chart_div.');'."\n";
		$js .= 'var chartType = '.$this->graphic_type.';'."\n";
		
		//log the data here
		var_dump($this->array_to_jsarray($data));
		var_dump($this->array_to_jsobject($data));
        // Draw the dashboard.
        $js .= "learnersDashboard(chartType, containerId);\n";
		
		
		//Generate the options.
		$js .= 'var options = '."\n";
		$js .= $this->array_to_jsobject($this->gen_options);
		$js .= ';'."\n";
		
		$js .= '}';
			
		if($this->open_js_tag === TRUE)
		{
        	$js .= '</script>'."\n";
		}
		
		/* CRIA AS DIVS */
		if($this->create_div === TRUE)
		{
			/* CHART DIV */
		    $js .= '<div id="'.$this->chart_div.'" class="'.$this->class_chart_div.'" style="width: 600px; height: 400px;"></div>';
		} // FIM CREATE DIV
		
		$this->clean();
		return $js;
	}
	
/* 	public function callTableWithoutDashboard()
	{
		return $this->TableWithoutDashboard();
	}
	
	private function TableWithoutDashboard()
	{
		if(is_null($this->chart_div))
		{
			$key = $this->gerarkey(10);
			$this->chart_div = 'gcharts_'.$key;
		}
		$js = NULL;
		
		$js .= $this->load_library()."\n";
		
		if($this->open_js_tag === TRUE)
		{
			$js .= '<script type="text/javascript">'."\n";
		}
		
		// Load the Visualization API and the controls package.
		$js .= 'google.charts.load("current",{packages:["corechart","table"]});'."\n";
		
	
		// Set a callback to run when the Google Visualization API is loaded.
		$js .= 'google.charts.setOnLoadCallback(drawChart);'."\n";
		
		// Callback that creates and populates a data table,
		// instantiates a dashboard, a range slider and a pie chart,
		// passes in the data and draws it.
		$js .= 'function drawChart() {'."\n";
		// Create our data table.
		
		
			
$js.="var data = new google.visualization.DataTable();
data.addColumn('string', '');
data.addColumn('number', 'READ');
data.addColumn('number', 'WRITE');
data.addColumn('number', 'ORAL');
data.addColumn('number', 'GENG');
data.addColumn('number', 'TOTAL');
data.addRows([
  ['ENG', 80,60,90,75,85],
  ['CHI', 65,49,60,80,68],
]);";
$js .= "var chart = new google.visualization.Table(document.getElementById('".$this->chart_div."'));\n";

//chart2
$key = $this->gerarkey(10);
$chart2_div = 'gcharts_'.$key;
$js .= "var chart2 = new google.visualization.Table(document.getElementById('".$chart2_div."'));\n";


$js.="
var formatter = new google.visualization.ColorFormat();
formatter.addRange(0, 40, 'black', '#e87478');
formatter.addRange(40, 45, 'black', 'orange');
formatter.addRange(45, 50, 'black', 'red');
var count=Object.keys(data).length;
for(var i=0;i<count;i++){
	formatter.format(data, i);
}

var data2 = new google.visualization.DataTable();
data2.addColumn('string', 'course');
data2.addColumn('number', 'paper1');
data2.addColumn('number', 'paper2');
data2.addColumn('string', 'LAB');
data2.addRows([
  ['MATH', 42,52,''],
  ['PHY', 65,32,'B'],
]);
var formatter2 = new google.visualization.ColorFormat();
formatter2.addRange(0, 40, 'black', '#e87478');
formatter2.addRange(40, 45, 'black', 'orange');
formatter2.addRange(45, 50, 'black', 'red');
var count2=Object.keys(data2).length-1;



formatter2.format(data2, 1);
formatter2.format(data2, 2);

chart.draw(data, {allowHtml: true, showRowNumber: false, width: '100%', height: '100%'});
chart2.draw(data2,{allowHtml: true, showRowNumber: false, width: '100%', height: '100%'});
";
$js .= '}';
			
		if($this->open_js_tag === TRUE)
		{
        	$js .= '</script>'."\n";
		}
		
		// CRIA AS DIVS 
		if($this->create_div === TRUE)
		{
			// CHART DIV
			
		    $js .= '<div id="'.$this->chart_div.'" class="'.$this->class_chart_div.'" style=" text-align:center; width:440px; height:150px; margin:2px 40px;"></div>';
			$js .= '<div id="'.$chart2_div.'" class="'.$this->class_chart_div.'" style="text-align:center; width: 440px; height:150px; margin:4px 40px;"></div>';
		} // FIM CREATE DIV
		
		$this->clean();
        return $js;
	} */


	
	/**  commenting this function
	*public function DrawTable($data,$data2)
	*{
	*	return $this->Table($data,$data2);
	*}
	*/
	// private function Table($data,$data2)
	// {
	// 	//table1div
	// 	if(is_null($this->chart_div))
	// 	{
	// 		$key = $this->gerarkey(10);
	// 		$this->chart_div = 'gcharts_'.$key;
	// 	}
	// 	//table2
	// 	$key = $this->gerarkey(10);
    //     $tablechart2_div = 'gcharts_'.$key;
		
	// 	$js = NULL;
		
	// 	$js .= $this->load_library()."\n";
		
	// 	if($this->open_js_tag === TRUE)
	// 	{
	// 		$js .= '<script type="text/javascript">'."\n";
	// 	}
		
	// 	// Load the Visualization API and the controls package.
	// 	$js .= 'google.charts.load("current",{packages:["corechart","table"]});'."\n";
		
	
	// 	// Set a callback to run when the Google Visualization API is loaded.
	// 	$js .= 'google.charts.setOnLoadCallback(drawChart);'."\n";
		
	// 	// Callback that creates and populates a data table,
	// 	// instantiates a dashboard, a range slider and a pie chart,
	// 	// passes in the data and draws it.
	// 	$js .= 'function drawChart() {'."\n";
		
	// 	// Create our data table.
	// 	$js .= 'var data = google.visualization.arrayToDataTable('.$this->array_to_jsarray($data).');'."\n";
	// 	$js .= 'var data2 = google.visualization.arrayToDataTable('.$this->array_to_jsarray($data2).');'."\n";
	
	// 	//chart1(table1)
	// 	$js .= "var chart = new google.visualization.Table(document.getElementById('".$this->chart_div."'));\n";
	// 	//chart2(table2)
	// 	$js .= "var chart2 = new google.visualization.Table(document.getElementById('".$tablechart2_div."'));\n";


	// 	$js.="
	// 		var formatter = new google.visualization.ColorFormat();
	// 		formatter.addRange(0, 40, 'black', '#e87478');
	// 		formatter.addRange(40, 45, 'black', 'orange');
	// 		formatter.addRange(45, 50, 'black', 'red');
	// 		for(var i=1;i<6;i++){
	// 			formatter.format(data, i);
	// 		}

	// 		var formatter2 = new google.visualization.ColorFormat();
	// 		formatter2.addRange(0, 40, 'black', '#e87478');
	// 		formatter2.addRange(40, 45, 'black', 'orange');
	// 		formatter2.addRange(45, 50, 'black', 'red');
	// 		formatter2.format(data2, 1);
	// 		formatter2.format(data2, 2);

	// 		chart.draw(data, {allowHtml: true, showRowNumber: false, width: '100%', height: '100%'});
	// 		chart2.draw(data2,{allowHtml: true, showRowNumber: false, width: '100%', height: '100%'});
    //         ";
    //     $js .= '}';
			
	// 	if($this->open_js_tag === TRUE)
	// 	{
    //     	$js .= '</script>'."\n";
	// 	}
		
	// 	// CRIA AS DIVS 
	// 	if($this->create_div === TRUE)
	// 	{
	// 		// CHART DIV
	// 	    $js .= '<div id="'.$this->chart_div.'" class="'.$this->class_chart_div.'" style="text-align:center; width: 440px; height:150px; margin:2px 40px;"></div>';
	// 		$js .= '<div id="'.$tablechart2_div.'" class="'.$this->class_chart_div.'" style="text-align:center; width: 440px; height:150px; margin:4px 40px;"></div>';
	// 	} // FIM CREATE DIV
		
	// 	$this->clean();
    //     return $js;
	// }
	
	
	
	/*
	@INPUT array:
	$array = array('title' => 'My Title');
	or
	$array = array('title' => 'My Title','vAxis' => array('title' => 'Cups'));
	
	@OUTPUT string:
	{title: 'title'}
	or
	{title: 'My Title',
	vAxis: {title: 'Cups'}}
	*/
	private function array_to_jsobject($array = array())
	{
		if((bool) !$array)
		{
			return '{}';
		}
		
		$return = NULL;
		foreach($array as $k => $v)
		{
			if(is_array($v))
			{
				$return .= $k.": ".$this->array_to_jsobject($v).",";
			} else
			{
				if(is_string($v))
				{
					$return .= $k.": '".addslashes($v)."',";
				}else if(is_object($v))
				{
				    if(isset($v->color)){
						$return .= $k.": ".$v->color.",";
				    }
					else{
						$return .= $k.": ".$v.",";
					}
				}
				else
				{
					$return .= $k.": ".$v.",";
				}
			}
		}
		return '{'.trim($return,',').'}';
	}
	
	/*
	@INPUT matriz:
	$array = array(array('Year', 'Sales', 'Expenses'),
	array('2004',1000,400),
	array('2005',1170,460),
	array('2006',660,1120),
	array('2007',1030,540));
	
	@OUTPUT string:
	[['Year','Sales','Expenses'],['2004','1000','400'],['2005','1170','460'],['2006','660','1120'],['2007','1030','540']]
	*/
	private function array_to_jsarray($array = array())
	{
		if((bool) !$array)
		{
			return '[]';
		}
		
		$return = NULL;
		foreach($array as $k => $v)
		{
			if(is_array($v))
			{
				$return .= ','.$this->array_to_jsarray($v);
			} else
			{
				if(is_string($v))
				{
					$return .= ",'".addslashes($v)."'";
				}else if(is_object($v))
				{
				    if(isset($v->rolestring)){
						$return .= ",".$v->rolestring;
				    }
					else{
						$return .= ",".$v;
					}
				}else
				{
					$return .= ",".$v;
				}
			}
		}
		
		return '['.trim($return,',').']';
	}
	
	public function clean()
	{
		//$this->library_loaded 		= FALSE;
		$this->create_div			= TRUE;
		$this->dashboard_div		= NULL;
		$this->class_dashboard_div	= NULL;
		$this->filter_div			= NULL;
		$this->class_filter_div		= NULL;
		$this->chart_div			= NULL;
		$this->class_chart_div		= NULL;
		$this->open_js_tag			= TRUE;
		$this->graphic_type 		= 'LineChart';
		$this->control_type			= 'NumberRangeFilter';
		$this->gen_options 			= array();
		$this->control_options 		= array();
		$this->use_dashboard		= FALSE;
	}
	
	public function is_number($num)
	{
		if((bool) preg_match("/^([0-9\.])+$/i",$num)) return true; else return false;
	}
	
	public function gerarkey($length = 40) 
	{
		$key = NULL;
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRTWXYZ';
		for( $i = 0; $i < $length; ++$i )
		{
			$key .= $pattern{rand(0,58)};
		}
		return $key;
	}
	
}
?>