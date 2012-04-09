<?php

class Order extends Application
{
	
	public function __construct()
	{
		parent::__construct();
		//$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">',
			'row_start' => '<tr class="detail_row">',
			'tbody_open' => '<tbody id="detail_body">'
		);
		$this->table->set_template($this->table_tpl);
	    
	}

	public function neworder()
	{

			$this->table->set_heading(
				'No.',		 	 	
				'Description',	 	 	 	 	 	 	 
				'Quantity',		
				'Total '.form_dropdown('currency',array('IDR','USD'))		
				); // Setting headings for the table

			$d = 0;
			$gt = 0;

			/*
			$this->table->set_template($tpl);

			$this->table->add_row(
				'',	 	 	 	 	 	 	 
				array('data'=>form_input(array('id'=>'description','name'=>'description')),
					'class'=>'item_form'
				),		
				array('data'=>form_input(array('id'=>'quantity','name'=>'quantity')),
					'class'=>'item_form'
				),				 	 	
				array('data'=>form_input(array('id'=>'unit_total','name'=>'unit_total')).form_button(array('id'=>'add_item','name'=>'add_item','content'=>'+')),
					'class'=>'item_form'
				)		 	 	
			);

			$this->table->add_row(
				'&nbsp;',		
				'&nbsp;',		
				array('data'=>'Total Price',
					'class'=>'sums',
					'id'=>'total_charges'
				),		
				array('data'=>form_input(array('id'=>'total_price','name'=>'total_price','class'=>'sum_input')),
					'class'=>'sums',
					'id'=>'total_price'
				)		

			);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					array('data'=>'Total Discount',
						'class'=>'sums',
						'id'=>'total_charges'
					),		
					array('data'=>form_input(array('id'=>'total_discount','name'=>'total_discount','class'=>'sum_input')),
						'class'=>'sums',
						'id'=>'total_discount'
					)		
				);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					array('data'=>'Total Tax',
						'class'=>'sums',
						'id'=>'total_charges'
					),		
					array('data'=>form_input(array('id'=>'total_tax','name'=>'total_tax','class'=>'sum_input')),
						'class'=>'sums',
						'id'=>'total_tax'
					)		
				);


				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					array('data'=>'COD Charges',
						'class'=>'sums',
						'id'=>'total_charges'
					),		
					array('data'=>form_input(array('id'=>'cod_cost','name'=>'cod_cost','class'=>'sum_input')),
						'class'=>'sums',
						'id'=>'cod_cost'
					)		
				);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					array('data'=>'Total Charges',
						'class'=>'sums',
						'id'=>'total_charges'
					),		
					array('data'=>form_input(array('id'=>'total_charges','name'=>'total_charges','class'=>'sum_input')),
						'class'=>'sums',
						'id'=>'total_charges'
					)		

				);
			*/

			$data['grand_total'] = $gt;
			$data['grand_discount'] = $d;

			$data['page_title'] = 'New Delivery Orders';

			//print_r($data['main_info']);

			$this->load->view('auth/pages/neworderform',$data); // Load the view
		}

}

?>