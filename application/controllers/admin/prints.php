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
			$main = $this->db
					->select('*,b.fullname as buyer,
						m.merchantname as merchant,
						a.street as m_street,
						a.contact_person as m_pic,
						a.district as m_district,
						a.city as m_city,
						a.province as m_province,
						a.country as m_country,
						a.zip as m_zip,
						a.phone as m_phone,
						a.mobile as m_mobile,
						a.application_name as app_name')
					->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left')
					->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left')
					->join('applications as a',$this->config->item('assigned_delivery_table').'.application_key=a.key','left')
					->where('delivery_id',$delivery_id)->get($this->config->item('assigned_delivery_table'));

					//print $this->db->last_query();

			$data['main_info'] = $main->row_array();

			$details = $this->db->where('delivery_id',$delivery_id)->order_by('unit_sequence','asc')->get($this->config->item('delivery_details_table'));

			$details = $details->result_array();

			$this->table->set_heading(
				'No.',		 	 	
				'Description',	 	 	 	 	 	 	 
				'Quantity',		
				'Total ('.$data['main_info']['currency'].')'		
				); // Setting headings for the table

			$d = 0;
			$gt = 0;

			foreach($details as $value => $key)
			{

				$this->table->add_row(
					(int)$key['unit_sequence'] + 1,		 	 	
					$key['unit_description'],	 	 	 	 	 	 	 
					$key['unit_quantity'],		
					number_format($key['unit_total'],2,',','.')			
				);

				$gt += $key['unit_total'];
				$d += $key['unit_discount'];

			}

			$gt = ($data['main_info']['total_price'] < $gt)?$gt:$data['main_info']['total_price'];
			$dsc = $data['main_info']['total_discount'];
			$tax = $data['main_info']['total_tax'];
			$cod = $data['main_info']['cod_cost'];
			$chg = ($gt - $dsc) + $tax + $cod;

			$this->table->add_row(
				'&nbsp;',		
				'&nbsp;',		
				'Total Price',		
				number_format($gt,2,',','.')
			);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Total Discount',		
					number_format($dsc,2,',','.')
				);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Total Tax',		
					number_format($data['main_info']['total_tax'],2,',','.')
				);


			if($cod == 0){
				$this->table->add_row(
					'','',
					'COD Charges','Paid by Merchant'
				);
			}else{
				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'COD Charges',		
					number_format($cod,2,',','.')
				);
			}

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Total Charges',		
					number_format($data['main_info']['chargeable_amount'],2,',','.')
				);

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

			//print_r($data['main_info']);

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