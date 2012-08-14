<?php

class Graphs extends Application
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('plot');
	}

	public function monthlygraph($status = null){
		$lineplot = $this->plot->plot(500,130);

		$year = date('Y',time());
		$month = date('m',time());

		if(is_null($status)){
			$status = null;
		}else{
			$status = array('status'=>$status);
		}
		$series = getmonthlydatacountarray($year,$month,$status,null);
		//$series = getmonthlydatacountarray($year,$month,$status,null);

		$lineplot->SetPlotType('bars');
		$lineplot->setShading(0);
		$lineplot->SetDataValues($series);

		$lineplot->SetYDataLabelPos('plotin');

		# With Y data labels, we don't need Y ticks or their labels, so turn them off.
		//$lineplot->SetYTickLabelPos('none');
		//$lineplot->SetYTickPos('none');		

		$lineplot->SetYTickIncrement(1);
		$lineplot->SetPrecisionY(0);

		//Turn off X axis ticks and labels because they get in the way:
		$lineplot->SetXTickLabelPos('none');
		$lineplot->SetXTickPos('none');

		//Draw it
		$lineplot->DrawGraph();
	}

	public function rangegraph($status = null,$from = null, $to = null){
		$lineplot = $this->plot->plot(500,230);

		$year = date('Y',time());
		$month = date('m',time());

		if(is_null($status)){
			$status = null;
		}else{
			$status = array('status'=>$status);
		}
		//$series = getmonthlydatacountarray($year,$month,$status,null);
		//$series = getmonthlydatacountarray($year,$month,$status,null);
		$series = getrangedatacountarray($year,$from,$to,$status);

		//print_r($series);

		$lineplot->SetPlotType('bars');
		$lineplot->setShading(0);
		$lineplot->SetDataValues($series);

		$lineplot->SetYDataLabelPos('plotin');

		# With Y data labels, we don't need Y ticks or their labels, so turn them off.
		//$lineplot->SetYTickLabelPos('none');
		//$lineplot->SetYTickPos('none');		

		$lineplot->SetYTickIncrement(1);
		$lineplot->SetPrecisionY(0);

		//Turn off X axis ticks and labels because they get in the way:
		$lineplot->SetXTickLabelPos('none');
		$lineplot->SetXTickPos('none');
		$lineplot->SetXDataLabelAngle(90);

		//Draw it
		$lineplot->DrawGraph();
	}

	public function citydistgraph($status,$from = null, $to = null){
		$lineplot = $this->plot->plot(500,230);

		$year = date('Y',time());
		$month = date('m',time());

		$agg = array();

		$cities = get_city_status();

		foreach($cities as $city){
			if($status == 'all'){
				$cityselect = array('buyerdeliverycity'=>$city);
			}else{
				$cityselect = array('buyerdeliverycity'=>$city,'status'=>$status);
			}
			$result = getrangedatacountarray($year,$from,$to,$cityselect);

			$aggr = 0;
			foreach($result as $r){
				$aggr += $r[1];
			}

			$agg[$city] = $aggr;
		}

		$piedata = array();
		$agsum = array_sum($agg);

		if($status == 'all'){
			$plot = new PHPlot(800,450);
			$plot->SetLegendPosition(1, 0, 'plot', 1.01, 0, -5, 5);
		}else{
			$plot = new PHPlot(500,250);
			$plot->SetYAxisPosition(-100);
			$plot->SetLegendPosition(1, 0, 'plot', 1.01, 0, -5, 5);
		}

		if($agsum == 0){
			$plot->SetLegend(array('No Data'));
			$piedata[] = array('No Data',100);
		}else{
			$plot->SetLegend($cities);
			foreach($agg as $key=>$val){
				//$piedata[] = array($key,($val / $agsum)*100);
				$piedata[] = array($key,$val);
			}
		}

		//print_r($piedata);

		$plot->SetImageBorderType('none');
		$plot->SetDataType('text-data-single');
		$plot->SetDataValues($piedata);
		$plot->SetPlotType('pie');

		$plot->SetShading(0);
		$plot->SetLabelScalePosition(0.25);
		$plot->SetPieAutoSize(true);
		//$plot->SetPieLabelType('value','data',2);

		$plot->SetPieLabelType(array('percent', 'value'), 'custom', 
				function($str){
					list($percent, $label) = explode(' ', $str, 2);
    				return sprintf('%s (%.1f%%)', $label, $percent);
				}
			);

		$plot->DrawGraph();

	}

}

?>