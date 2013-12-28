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

    public function mapview($type,$buyer_id){

        if($type == 'buyer'){
            $table = $this->config->item('jayon_buyers_table');
        }else{
            $table = $this->config->item('incoming_delivery_table');
        }

        $this->db->where('id',$buyer_id);
        $this->db->select('id,buyer_name,buyerdeliveryzone,buyerdeliverycity,shipping_address,recipient_name,shipping_zip,directions,dir_lat ,dir_lon ,latitude ,longitude');
        $buyer = $this->db->get($table)->row_array();


        $data['page_title'] = 'Set Location';
        $data['id'] = $buyer['id'];
        $data['latitude'] = $buyer['latitude'];
        $data['longitude'] = $buyer['longitude'];

        unset($buyer['id']);
        unset($buyer['latitude']);
        unset($buyer['longitude']);

        $data['buyer'] = $buyer;
        $data['type'] = $type;

        $this->load->view('auth/pages/setlocation',$data); // Load the view
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
			//$gt = ($total < $gt)?$gt:$total;
			$dsc = str_replace(array(',','.'), '', $data['main_info']['total_discount']);
			$tax = str_replace(array(',','.'), '',$data['main_info']['total_tax']);
			$dc = str_replace(array(',','.'), '',$data['main_info']['delivery_cost']);
			$cod = str_replace(array(',','.'), '',$data['main_info']['cod_cost']);

			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$chg = ($gt - $dsc) + $tax + $dc + $cod;

            if($data['main_info']['delivery_type'] == 'COD' || $data['main_info']['delivery_type'] == 'CCOD'){
                $cclass = ' bigtype';
            }else{
                $cclass = '';
            }


			$this->table->add_row(
					array('data'=>'Total Price',
						'colspan'=>3,
						'class'=>'lsums'.$cclass
						),
                    array('data'=>number_format($gt,2,',','.'),
                        'colspan'=>3,
                        'class'=>$cclass
                        )

			);

				$this->table->add_row(
					array('data'=>'Total Discount',
						'colspan'=>3,
						'class'=>'lsums'
						),
                    array(
                        'data'=>number_format($dsc,2,',','.'),
                        'class'=>'lsums'
                        )
				);

				$this->table->add_row(
					array('data'=>'Total Tax',
						'colspan'=>3,
						'class'=>'lsums'
						),
                    number_format($tax,2,',','.')
				);

				/*
				if($data['main_info']['delivery_bearer'] == 'merchant'){
					$chg -= $dc;
					$dc = 0;
				}
				*/

				$this->table->add_row(
					array('data'=>'Dibayar oleh '.$data['main_info']['delivery_bearer'],
						'colspan'=>2,
						'class'=>'lsums'
						),
					array('data'=>'Delivery Charge',
                        'class'=>'lsums'.$cclass
						),
					array('data'=>number_format($dc,2,',','.'),
						'class'=>'editable'.$cclass,
						'id'=>'delivery_cost'
					)
				);

				/*
				if($data['main_info']['cod_bearer'] == 'merchant'){
					$chg -= $cod;
					$cod = 0;
				}
				*/

				$this->table->add_row(
					array('data'=>'Dibayar oleh '.$data['main_info']['cod_bearer'],
						'colspan'=>2,
						'class'=>'lsums'
						),
					array('data'=>'COD Surcharge',
                        'class'=>'lsums'.$cclass
						),
					array('data'=>number_format($cod,2,',','.'),
						'class'=>'editable'.$cclass,
						'id'=>'cod_cost'
					)
				);

				$this->table->add_row(
					array('data'=>'Total Charges',
						'colspan'=>3,
                        'class'=>'lsums'.$cclass
						),
                    array('data'=>number_format($chg,2,',','.'),
                        'class'=>'editable'.$cclass,
                        'id'=>'delivery_cost'
                        )
				);

			$data['grand_total'] = $gt;
			$data['grand_discount'] = $d;

			$qr_data = $delivery_id."|".$data['main_info']['merchant_trans_id'];

			$this->gc_qrcode->size(75)
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
            $this->db->where('app_id',$data['main_info']['application_id']);

            if($data['main_info']['delivery_type'] == 'PS'){
                $weights = $this->db->get($this->config->item('jayon_pickup_fee_table'));
            }else{
                $weights = $this->db->get($this->config->item('jayon_delivery_fee_table'));
            }

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
				'CCOD'=>'Credit Card On Delivery',
				'Delivery Only'=>'Delivery Only',
				'PS'=>'Pick Up Supply'
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
			//$gt = ($total < $gt)?$gt:$total;
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
						'id'=>'total_charges',
                        'class'=>'bigtype'
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
				'colspan'=>'13'
			)
		);


		if($type == 'Merchant' || $type == 'Global'){
			$this->table->set_heading(
				'No.',
				'Merchant Trans ID',
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status',
				'Goods Price',
				'Disc',
				'Tax',
				'Delivery Chg',
				'COD Surchg',
				'Payable Value'
			); // Setting headings for the table

		}else if($type == 'Courier'){
			$this->table->set_heading(
				'No.',
				'Merchant Trans ID',
				'Delivery ID',
				'Merchant Name',
				'Store',
				'Delivery Date',
				'Status',
				'Delivery Chg',
				'COD Surchg',
				'Payable Value'
			); // Setting headings for the table
		}


		$seq = 1;
		$total_billing = 0;
		$total_delivery = 0;
		$total_cod = 0;

		//print_r($rows->result());

		foreach($rows->result() as $r){

			$total = str_replace(array(',','.'), '', $r->total_price);
			$dsc = str_replace(array(',','.'), '', $r->total_discount);
			$tax = str_replace(array(',','.'), '',$r->total_tax);
			$dc = str_replace(array(',','.'), '',$r->delivery_cost);
			$cod = str_replace(array(',','.'), '',$r->cod_cost);

			$total = (int)$total;
			$dsc = (int)$dsc;
			$tax = (int)$tax;
			$dc = (int)$dc;
			$cod = (int)$cod;

			$payable = 0;


			if($r->status == $this->config->item('trans_status_mobile_delivered')){
				if($type == 'Merchant' || $type == 'Global'){
					$payable = ($total - $dsc) + $tax;
					// + $dc + $cod;
				}else if($type == 'Courier'){
					$payable = ($dc + $cod) * 0.1;
				}
				$total_billing += (int)str_replace('.','',$payable);
			}else if(
				$r->status == $this->config->item('trans_status_mobile_revoked') ||
				$r->status == $this->config->item('trans_status_mobile_rescheduled') ||
				$r->status == $this->config->item('trans_status_mobile_noshow'))
			{
				//TBA
			}

			$total_delivery += (int)str_replace('.','',$dc);
			$total_cod += (int)str_replace('.','',$cod);

			if($type == 'Merchant' || $type == 'Global'){
				$this->table->add_row(
					$seq,
					$r->merchant_trans_id,
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status,
					number_format((int)str_replace('.','',$total),2,',','.'),
					number_format((int)str_replace('.','',$dsc),2,',','.'),
					number_format((int)str_replace('.','',$tax),2,',','.'),
					number_format((int)str_replace('.','',$dc),2,',','.'),
					number_format((int)str_replace('.','',$cod),2,',','.'),
					number_format((int)str_replace('.','',$payable),2,',','.')
				);

			}else if($type == 'Courier'){
				$this->table->add_row(
					$seq,
					$r->merchant_trans_id,
					$r->delivery_id,
					$r->merchant,
					$r->app_name.'<hr />'.$r->domain,
					$r->assignment_date,
					$r->status,
					number_format((int)str_replace('.','',$dc),2,',','.'),
					number_format((int)str_replace('.','',$cod),2,',','.'),
					number_format((int)str_replace('.','',$payable),2,',','.')
				);
			}

			$seq++;
		}

		if($type == 'Merchant' || $type == 'Global'){
			$total_span = 10;
			$say_span = 12;

		}else if($type == 'Courier'){
			$total_span = 7;
			$say_span = 9;
		}


		$this->table->add_row(
			array('data'=>'Total','colspan'=>$total_span),
			number_format($total_delivery,2,',','.'),
			number_format($total_cod,2,',','.'),
			number_format($total_billing,2,',','.')
		);

		$this->table->add_row(
			'Terbilang',
			array('data'=>'&nbsp;','colspan'=>$say_span)
		);

		if($type == 'Merchant' || $type == 'Global'){
			$this->table->add_row(
				'Payable',
				array('data'=>$this->number_words->to_words($total_billing).' rupiah',
					'colspan'=>$say_span)
			);
		}

		$this->table->add_row(
			'Delivery Charge',
			array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
				'colspan'=>$say_span)
		);

		$this->table->add_row(
			'COD Surcharge',
			array('data'=>$this->number_words->to_words($total_cod).' rupiah',
				'colspan'=>$say_span)
		);

		$recontab = $this->table->generate();
		$data['recontab'] = $recontab;

		if($pdf){
			$html = $this->load->view('print/reconciliation',$data,true);
			$pdf_name = $type.'_'.$to.'_'.$from.'_'.$id;
			pdf_create($html, $pdf_name.'.pdf','A4','landscape', true);
		}else{
			$this->load->view('print/reconciliation',$data); // Load the view
		}

	}


}

?>