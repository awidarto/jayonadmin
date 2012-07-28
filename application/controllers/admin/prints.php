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
					->select($this->config->item('assigned_delivery_table').'.*,b.fullname as buyer,
						m.merchantname as merchant,
						m.street as mc_street,
						m.fullname as mc_pic,
						m.district as mc_district,
						m.city as mc_city,
						m.province as mc_province,
						m.country as mc_country,
						m.zip as mc_zip,
						m.phone as mc_phone,
						m.mobile as mc_mobile,
						a.street as m_street,
						a.contact_person as m_pic,
						a.district as m_district,
						a.city as m_city,
						a.province as m_province,
						a.country as m_country,
						a.zip as m_zip,
						a.phone as m_phone,
						a.mobile as m_mobile,
						a.application_name as app_name,
						a.domain as app_domain,
						c.fullname as courier_name' )
					->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left')
					->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left')
					->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
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

				$u_total = str_replace(array(',','.'), '', $key['unit_total']);
				$u_discount = str_replace(array(',','.'), '', $key['unit_discount']);						
				$gt += (int)$u_total;
				$d += (int)$u_discount;

			}

			$total = str_replace(array(',','.'), '', $data['main_info']['total_price']);
			$total = (int)$total;
			$gt = ($total < $gt)?$gt:$total;
			$dsc = str_replace(array(',','.'), '', $data['main_info']['total_discount']);
			$tax = str_replace(array(',','.'), '',$data['main_info']['total_tax']);
			$dc = str_replace(array(',','.'), '',$data['main_info']['delivery_cost']);
			$cod = str_replace(array(',','.'), '',$data['main_info']['cod_cost']);

			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$chg = ($gt - $dsc) + $tax + $dc + $cod;

			$this->table->add_row(
					array('data'=>'Total Price',
						'colspan'=>3,
						'class'=>'lsums'
						),									
				number_format($gt,2,',','.')
			);

				$this->table->add_row(
					array('data'=>'Total Discount',
						'colspan'=>3,
						'class'=>'lsums'
						),										
					number_format($dsc,2,',','.')
				);

				$this->table->add_row(
					array('data'=>'Total Tax',
						'colspan'=>3,
						'class'=>'lsums'
						),	
					number_format($tax,2,',','.')
				);


				$this->table->add_row(
					array('data'=>'Delivery Charge',
						'colspan'=>3,
						'class'=>'lsums'
						),	
					array('data'=>number_format($dc,2,',','.'),
						'class'=>'editable',
						'id'=>'delivery_cost'
					)		
				);

				$this->table->add_row(
					array('data'=>'COD Surcharge',
						'colspan'=>3,
						'class'=>'lsums'
						),		
					array('data'=>number_format($cod,2,',','.'),
						'class'=>'editable',
						'id'=>'cod_cost'
					)		
				);

				$this->table->add_row(
					array('data'=>'Total Charges',
						'colspan'=>3,
						'class'=>'lsums'
						),		
					number_format($chg,2,',','.')
				);

			$data['grand_total'] = $gt;
			$data['grand_discount'] = $d;

			$qr_data = $delivery_id."|".$data['main_info']['merchant_trans_id'];

			$this->gc_qrcode->size(150)
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

	public function deliveryview($delivery_id,$pdf = false)
	{
			$main = $this->db
					->select($this->config->item('assigned_delivery_table').'.*,b.fullname as buyer,
						m.merchantname as merchant,
						m.street as mc_street,
						m.fullname as mc_pic,
						m.district as mc_district,
						m.city as mc_city,
						m.province as mc_province,
						m.country as mc_country,
						m.zip as mc_zip,
						m.phone as mc_phone,
						m.mobile as mc_mobile,
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

			//print_r($main->row_array());

			$this->db->select('seq,kg_from,kg_to,calculated_kg,tariff_kg,total');
			$this->db->order_by('seq','asc');
			$weights = $this->db->get($this->config->item('jayon_delivery_fee_table'));

			if($weights->num_rows() > 0){
				$weight[0] = 'Select weight range';
				foreach ($weights->result() as $r) {
					$weight[$r->total] = $r->kg_from.' kg - '.$r->kg_to.' kg';
				}
			}else{
				$weight[0] = 'Select weight range';
			}

			$weightselect = form_dropdown('package_weight',$weight,$data['main_info']['weight'],'id="package_weight"');

			$data['weightselect'] = $weightselect;

			$delivery_type = array(
				'0'=>'Select delivery type',
				'COD'=>'COD',
				'Delivery Only'=>'Delivery Only'
			);

			$typeselect = form_dropdown('delivery_type',$delivery_type,$data['main_info']['delivery_type'],'id="delivery_type_select"');

			$data['typeselect'] = $typeselect;



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

				$u_total = str_replace(array(',','.'), '', $key['unit_total']);
				$u_discount = str_replace(array(',','.'), '', $key['unit_discount']);						
				$gt += (int)$u_total;
				$d += (int)$u_discount;

			}

			$total = str_replace(array(',','.'), '', $data['main_info']['total_price']);
			$total = (int)$total;
			$gt = ($total < $gt)?$gt:$total;
			$dsc = str_replace(array(',','.'), '', $data['main_info']['total_discount']);
			$tax = str_replace(array(',','.'), '',$data['main_info']['total_tax']);
			$dc = str_replace(array(',','.'), '',$data['main_info']['delivery_cost']);
			$cod = str_replace(array(',','.'), '',$data['main_info']['cod_cost']);

			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$chg = ($gt - $dsc) + $tax + $dc + $cod;

			$this->table->add_row(
				'&nbsp;',		
				'&nbsp;',		
				'Total Price',		
				array('data'=>number_format($gt,2,',','.'),
					'id'=>'total_price'
				)		

			);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Total Discount',
					array('data'=>number_format($dsc,2,',','.'),
						'id'=>'total_discount'
					)		
				);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Total Tax',		
					array('data'=>number_format($tax,2,',','.'),
						'id'=>'total_tax'
					)		
				);


				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Delivery Charge',		
					array('data'=>number_format($dc,2,',','.'),
						'class'=>'editable',
						'id'=>'delivery_cost'
					)		
				);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'COD Surcharge',		
					array('data'=>number_format($cod,2,',','.'),
						'class'=>'editable',
						'id'=>'cod_cost'
					)		
				);

				$this->table->add_row(
					'&nbsp;',		
					'&nbsp;',		
					'Total Charges',		
					array('data'=>number_format($chg,2,',','.'),
						'id'=>'total_charges'
					)		

				);

			$data['grand_total'] = $gt;
			$data['grand_discount'] = $d;

			$qr_data = $delivery_id."|".$data['main_info']['merchant_trans_id'];

			$this->gc_qrcode->size(100)
                ->data($qr_data)
                ->output_encoding('UTF-8')
                ->error_correction_level('L')
                ->margin(0);

            $data['qr'] = $this->gc_qrcode->img();

            $this->gc_qrcode->clear();

			$data['page_title'] = 'Delivery Orders';

			//print_r($data['main_info']);

			if($pdf){
				$html = $this->load->view('print/deliveryview',$data,true);
				//print $html; // Load the view
				pdf_create($html, $delivery_id.'.pdf','A4','landscape', true); 
			}else{
				$this->load->view('print/deliveryview',$data); // Load the view
			}
		}

	public function reconciliation($from, $to ,$type,$id,$pdf = false){

		$this->load->library('number_words');

		if($id == 'noid'){
			$data['type_name'] = '-';
			$data['bank_account'] = 'n/a';
		}else{
			if($type == 'Merchant'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = ($user->account_number == '')?'n/a':$user->bank.' - '.$user->account_number.' - '.$user->account_name;
			}else if($type == 'Courier'){
				$user = $this->db->where('id',$id)->get($this->config->item('jayon_couriers_table'))->row();
				$data['type_name'] = $user->fullname;
				$data['bank_account'] = 'n/a';
			}
		}

		$data['type'] = $type;
		$data['period'] = $from.' s/d '.$to;

		$sfrom = date('Y-m-d',strtotime($from));
		$sto = date('Y-m-d',strtotime($to));

		$this->db->select($this->config->item('delivered_delivery_table').'.*,b.fullname as buyer,m.merchantname as merchant,a.domain as domain,a.application_name as app_name,d.identifier as device,c.fullname as courier');
		$this->db->from($this->config->item('delivered_delivery_table'));
		$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$column = 'assignment_date';
		$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

		$this->db->where($daterange, null, false);
		$this->db->where($column.' != ','0000-00-00');

		if($id != 'noid'){
			if($type == 'Merchant'){
				$this->db->where($this->config->item('delivered_delivery_table').'.merchant_id',$id);
			}else if($type == 'Courier'){
				$this->db->where($this->config->item('delivered_delivery_table').'.courier_id',$id);
			}
		}

		$this->db->and_();
		$this->db->group_start();
		$this->db->where('status',$this->config->item('trans_status_mobile_delivered'));
		$this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
		$this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
		$this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
		$this->db->group_end();

		$rows = $this->db->get();

		//print $this->db->last_query();

		$this->table->set_heading(
			array('data'=>'Delivery Details',
				'colspan'=>'7'
			)	
		);


		$this->table->set_heading(
			'No.',		 	 	
			'Merchant Trans ID',	 	 	 	 	 	 	 
			'Delivery ID',
			'Merchant Name',
			'Store',
			'Delivery Date',
			'Status',		
			'Value'		
		); // Setting headings for the table

		$seq = 1;
		$total_billing = 0;

		//print_r($rows->result());

		foreach($rows->result() as $r){
			$this->table->add_row(
				$seq,		
				$r->merchant_trans_id,		
				$r->delivery_id,
				$r->merchant,
				$r->app_name.'<hr />'.$r->domain,
				$r->assignment_date,
				$r->status,
				number_format((int)str_replace('.','',$r->total_price),2,',','.')
			);

			if($r->status == $this->config->item('trans_status_mobile_delivered')){
				$total_billing += (int)str_replace('.','',$r->total_price);
			}
			$seq++;
		}

		$this->table->add_row(
			array('data'=>'Total','colspan'=>7),
			number_format($total_billing,2,',','.')
		);

		$this->table->add_row(
			'Terbilang',
			array('data'=>$this->number_words->to_words($total_billing).' rupiah',
				'colspan'=>7)
		);

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		if($pdf){
			$html = $this->load->view('print/reconciliation',$data,true);
			//print $html; // Load the view

			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true); 
		}else{
			$this->load->view('print/reconciliation',$data); // Load the view
		}

	}


}

?>