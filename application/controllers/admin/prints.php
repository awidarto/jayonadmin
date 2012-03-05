<?php

class Prints extends Application
{
	
	public function __construct()
	{
		parent::__construct();
		$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
		);
		$this->table->set_template($this->table_tpl);
	    
	}
	
		public function deliveryslip($delivery_id,$pdf = false)
		{
			$main = $this->db->where('delivery_id',$delivery_id)->get($this->config->item('assigned_delivery_table'));

			$data['main_info'] = $main->row_array();

			$details = $this->db->where('delivery_id',$delivery_id)->order_by('unit_sequence','asc')->get($this->config->item('delivery_details_table'));

			$details = $details->result_array();

			$this->table->set_heading(
				'No.',		 	 	
				'Description',	 	 	 	 	 	 	 
				'Quantity',		
				'Total'			
				); // Setting headings for the table

			$d = 0;
			$gt = 0;

			foreach($details as $value => $key)
			{

				$this->table->add_row(
					(int)$key['unit_sequence'] + 1,		 	 	
					$key['unit_description'],	 	 	 	 	 	 	 
					$key['unit_quantity'],		
					$key['unit_total']			
				);

				$gt += $key['unit_total'];
				$d += $key['unit_discount'];

			}

			$this->table->add_row(
				'&nbsp;',		
				'&nbsp;',		
				'Total',		
				$gt
			);

			if($data['main_info']['cod_cost'] == 0){
				$this->table->add_row(
					'','',
					'COD','Paid by Merchant'
				);
			}else{
				$this->table->add_row(
				'&nbsp;',		
				'&nbsp;',		
				'COD',		
				$data['main_info']['currency'].' '.$data['main_info']['cod_cost']
			);

			}

			$data['grand_total'] = $gt;
			$data['grand_discount'] = $d;

			$qr_data = $delivery_id."|".$data['main_info']['merchant_trans_id'];

			$this->gc_qrcode->size(200)
                ->data($qr_data)
                ->output_encoding('UTF-8')
                ->error_correction_level('L')
                ->margin(0);

            $data['qr'] = $this->gc_qrcode->img();

            $this->gc_qrcode->clear();

			$data['page_title'] = 'Delivery Orders';

			if($pdf){
				$html = $this->load->view('print/deliveryslip',$data,true);
				//print $html; // Load the view
				pdf_create($html, $delivery_id.'.pdf','A4','landscape', true); 
			}else{
				$this->load->view('print/deliveryslip',$data); // Load the view
			}
		}
	}

?>