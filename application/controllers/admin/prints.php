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
	
		public function deliveryslip($delivery_id)
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
					$key['unit_sequence'],		 	 	
					$key['unit_description'],	 	 	 	 	 	 	 
					$key['unit_quantity'],		
					$key['unit_total']			
				);

				$gt += $key['unit_total'];
				$d += $key['unit_discount'];

			}

			$this->table->add_row(
				'&nbsp',		
				'&nbsp',		
				'Total',		
				$gt
			);

			$data['grand_total'] = $gt;
			$data['grand_discount'] = $d;


			$data['page_title'] = 'Delivery Orders';
			$this->load->view('print/deliveryslip',$data); // Load the view
		}
	}

?>