<?php

class Graphs extends Application
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('plot');
	}

	public function monthlygraph($status = 'all',$span = 'half'){
		$lineplot = $this->plot->plot(600,300);

		$year = date('Y',time());
		$month = date('m',time());

		if(is_null($status) || $status == 'all'){
			$status = null;
		}else{
			$status = array('status'=>$status);
		}
		//$month = '08';
		$series = getmonthlydatacounttypearray($year,$month,$status,null,$span);
		//$series = getmonthlydatacounttypearray($year,$month,$status,null);
		//$tseries = getmonthlydatacountarray($year,$month,$status,null);
		//$series = getmonthlydatacountarray($year,$month,$status,null);

		//print_r($series);

		$lineplot->SetPlotType('stackedbars');
		$lineplot->setShading(0);
		$lineplot->SetDataType('text-data');
		$lineplot->SetDataValues($series);

		$lineplot->SetYDataLabelPos('plotin');

		# With Y data labels, we don't need Y ticks or their labels, so turn them off.
		//$lineplot->SetYTickLabelPos('none');
		//$lineplot->SetYTickPos('none');		

		$lineplot->SetYTickIncrement(1);
		$lineplot->SetPrecisionY(0);

		$lineplot->SetLegend(array('COD','Delivery Only'));

		//Turn off X axis ticks and labels because they get in the way:
		$lineplot->SetXTickLabelPos('none');
		$lineplot->SetXTickPos('none');
		$lineplot->SetYDataLabelPos('plotstack');
		$lineplot->SetMarginsPixels(null,null,70,null);
		$lineplot->SetLegendPosition(1, 0, 'image', 1,0,-5,5);
		$lineplot->setLegendReverse(true);
		
		//Draw it
		$lineplot->DrawGraph();
	}

	public function rangegraph($status = null,$from = null, $to = null){
		$lineplot = $this->plot->plot(500,450);

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

		$lineplot->SetPlotType('stackedbars');
		$lineplot->setShading(0);
		$lineplot->SetDataType('text-data');
		$lineplot->SetDataValues($series);

		$lineplot->SetYDataLabelPos('plotin');

		# With Y data labels, we don't need Y ticks or their labels, so turn them off.
		//$lineplot->SetYTickLabelPos('none');
		//$lineplot->SetYTickPos('none');		

		$lineplot->SetYTickIncrement(1);
		$lineplot->SetPrecisionY(0);
		$lineplot->SetLegend(array('COD','CCOD','PS','Delivery Only'));

		//Turn off X axis ticks and labels because they get in the way:
		$lineplot->SetXTickLabelPos('none');
		$lineplot->SetXTickPos('none');
		$lineplot->SetXDataLabelAngle(90);
		$lineplot->SetYDataLabelPos('plotstack');
		$lineplot->SetMarginsPixels(null,null,90,null);
		$lineplot->SetLegendPosition(1, 0, 'image', 1,0,-15,5);
		$lineplot->setLegendReverse(true);

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
				$cityselect = array('buyerdeliverycity'=>$city,'status'=>'all');
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

	public function topxgraph($top,$status = 'all',$from = null, $to = null){
		$lineplot = $this->plot->plot(500,230);

		$year = date('Y',time());
		$month = date('m',time());

		$from = date('Y-m-d',strtotime($from));
		$to = date('Y-m-d',strtotime($to));


		$this->db->distinct();
		$this->db->select('m.merchantname as merchant_id,count(*) as count');
		$this->db->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left');
		$this->db->from($this->config->item('delivered_delivery_table'));

		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s' and '%s' ", $column, $from, $to);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');
		$this->db->group_by('merchant_id');
		$this->db->limit($top);

		$merchants = $this->db->get()->result_array();

		//print $this->db->last_query();

		$piedata = array();
		$agg = array();
		$legend = array();

		foreach($merchants as $m){
			$agg[] = $m['count'];
		}

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
			$plot->SetLegend($legend);
			foreach($merchants as $m){
				$piedata[] = array($m['merchant_id'],$m['count']);
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

		$plot->SetPieLabelType(array('percent', 'label'), 'custom', 
				function($str){
					list($percent, $label) = explode(' ', $str, 2);
    				return sprintf('%s (%.1f%%)', $label, $percent);
				}
			);

		$plot->DrawGraph();

	}

}

?>