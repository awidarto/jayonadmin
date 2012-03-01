<?php

class Device extends Application
{

	public function __construct()
	{
		parent::__construct();
		$this->ag_auth->restrict('admin'); // restrict this controller to admins only
		$this->table_tpl = array(
			'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
		);
		$this->table->set_template($this->table_tpl);

		$this->breadcrumb->add_crumb('Home','admin/dashboard');


		$this->devices = $this->getDevices();

		$this->load->library('curl');
		
		$this->api_url = $this->config->item('api_url');

		$this->api_key = 'efba0bc986a8bd56d735d1ec59a5d3e74c6bf36c';


	}

	public function ajaxorders(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'buyerdeliverytime',
			'buyerdeliveryzone',
			'buyerdeliverycity',
			'delivery_id',
			'merchant_trans_id',
			'app_name',
			'merchant',
			'buyer',
			'shipping_address',
			'phone',
			'status'
			);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_mobile_table'));

		$count_display_all = $this->db
			->count_all_results($this->config->item('jayon_mobile_table'));

		//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
		}

		$this->db->select('*');
		//$this->db->join('members as b',$this->config->item('incoming_delivery_table').'.buyer_id=b.id','left');
		//$this->db->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left');
		//$this->db->join('applications as a',$this->config->item('incoming_delivery_table').'.application_id=b.id','left');


		$data = $this->db
			->get($this->config->item('jayon_mobile_table'));

		//print $this->db->last_query();

		//->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

		$result = $data->result_array();

		$aadata = array();

		foreach($result as $value => $key)
		{
			$aadata[] = array(
				'<span id="'.$key['delivery_id'].'"><input type="hidden" value="'.$key['by_time'].'" id="cd_'.$key['delivery_id'].'">'.$key['delivery_id'].'</span><br />'.
				'<span>From : '.$key['mc_name'].'</span><br />'.
				'<span>To : '.$key['by_name'].'</span><br />'.
				'<span>Addr : '.$key['ship_addr'].'</span>'

			);
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function index()
	{
		$this->breadcrumb->add_crumb('Orders','admin/delivery/incoming');
		$this->breadcrumb->add_crumb('Incoming Orders','admin/delivery/incoming');

		$this->table->set_heading(
			'Order List'
			); // Setting headings for the table

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'mobile/device/ajaxorders';
		$page['page_title'] = 'Mobile Orders';
		$this->ag_auth->view('mobile/ajaxlistview',$page); // Load the view
	}

	public function details($delivery_id = null){
		$this->breadcrumb->add_crumb('Orders','admin/delivery/incoming');
		$this->breadcrumb->add_crumb('Incoming Orders','admin/delivery/incoming');

		$order = $this->db->where('delivery_id',$delivery_id)->get($this->config->item('jayon_mobile_table'));

		$page['order'] = $order->result_array();

		$page['sortdisable'] = '';
		$page['page_title'] = 'Mobile Orders Detail';
		$this->ag_auth->view('mobile/details',$page); // Load the view
	}

	public function location(){
		$this->breadcrumb->add_crumb('Orders','admin/delivery/incoming');
		$this->breadcrumb->add_crumb('Incoming Orders','admin/delivery/incoming');

		$page['sortdisable'] = '';
		$page['page_title'] = 'Mobile Location Detail';
		$this->ag_auth->view('mobile/location',$page); // Load the view
	}

	public function options(){
		$this->breadcrumb->add_crumb('Orders','admin/delivery/incoming');
		$this->breadcrumb->add_crumb('Incoming Orders','admin/delivery/incoming');

		$page['page_title'] = 'Mobile Orders Detail';
		$this->ag_auth->view('mobile/options',$page); // Load the view
	}

	public function ajaxlocation(){
		
		$loc['lat'] = $this->input->post('lat');
		$loc['lon'] = $this->input->post('lon');
		$loc['key'] = $this->api_key;

		$url = $this->api_url.'locpost/'.$this->api_key;
				
		$result = $this->curl->simple_post($url,array('loc'=>json_encode($loc)));

		print $result;

	}

	public function ajaxsync(){
		
		$loc['lat'] = $this->input->post('lat');
		$loc['lon'] = $this->input->post('lon');
		$loc['key'] = $this->api_key;

		$url = $this->api_url.'syncdata/'.$this->api_key;
				
		$result = $this->curl->simple_post($url,array('loc'=>json_encode($loc)));

		$rsdata = json_decode($result);

		$rsdata = $rsdata->data;

		foreach ($rsdata as $rs) {
			$data = get_object_vars($rs);
			$this->db->insert($this->config->item('jayon_mobile_table'),$data);
		}

		print $result;

	}


	/* zoning */

	public function ajaxcancel(){
		$delivery_id = $this->input->post('delivery_id');

		$actor = 'M:'.$this->session->userdata('userid');

		if(is_array($delivery_id)){
			foreach ($delivery_id as $d) {
				$this->db->where('delivery_id',$d)->update($this->config->item('incoming_delivery_table'),array('status'=>$this->config->item('trans_status_canceled'),'change_actor'=>$actor));
			}
		}else{
			$this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),array('status'=>$this->config->item('trans_status_canceled'),'change_actor'=>$actor));
		}

		print json_encode(array('result'=>'ok'));

		//send_notification('Cancelled Orders',$buyeremail,null,'rescheduled_order_buyer',$edata,null);

	}

	public function ajaxreschedule($condition = 'incoming'){
		// shoud be more complex !! not just updating status, but creating duplicate entry with different date and delivery ID

		$delivery_id = $this->input->post('delivery_id');
		$buyerdeliverytime = $this->input->post('buyerdeliverytime');

		if(is_array($delivery_id)){
			foreach ($delivery_id as $d) {
				$buyeremail[] = $this->do_revoke_reschedule($d,$buyerdeliverytime,$this->config->item('trans_status_rescheduled'),$condition);
			}
		}else{
			$buyeremail = $this->do_revoke_reschedule($delivery_id,$buyerdeliverytime,$this->config->item('trans_status_rescheduled'),$condition);
		}

		print json_encode(array('result'=>'ok'));

		//send_notification('Rescheduled Orders',$buyeremail,null,'rescheduled_order_buyer',null,null);
		//send_notification('Rescheduled Orders',$buyeremail,null,'rescheduled_order',$edata,null);

	}

	public function ajaxrevoke(){
		// shoud be more complex !! not just updating status, but creating duplicate entry with different date and delivery ID
		$delivery_id = $this->input->post('delivery_id');

		$actor = 'M:'.$this->session->userdata('userid');

		if(is_array($delivery_id)){
			foreach ($delivery_id as $d) {
				$this->db->where('delivery_id',$d)->update($this->config->item('incoming_delivery_table'),array('status'=>'revoked','change_actor'=>$actor));
				$buyeremail[] = $this->do_revoke_reschedule($d,null,$this->config->item('trans_status_revoked'),'incoming');
			}
		}else{
			$this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),array('status'=>'revoked','change_actor'=>$actor));
			$buyeremail = $this->do_revoke_reschedule($delivery_id,null,$this->config->item('trans_status_revoked'),'incoming');
		}

		print json_encode(array('result'=>'ok'));
		send_notification('Revoked Orders',$buyeremail,null,'rescheduled_order_buyer',$edata,null);

	}

	public function ajaxconfirm(){
		$delivery_id = $this->input->post('delivery_id');

		$actor = 'M:'.$this->session->userdata('userid');

		if(is_array($delivery_id)){
			foreach ($delivery_id as $d) {
				$this->db->where('delivery_id',$d)->update($this->config->item('incoming_delivery_table'),array('status'=>$this->config->item('trans_status_confirmed'),'change_actor'=>$actor));
			}
		}else{
			$this->db->where('delivery_id',$delivery_id)->update($this->config->item('incoming_delivery_table'),array('status'=>$this->config->item('trans_status_confirmed'),'change_actor'=>$actor));
		}

		print json_encode(array('result'=>'ok'));
	}
	
	public function ajaxdelivered()
	{
		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		// get total count result
		$count_all = $this->db
			->where('status',$this->config->item('trans_status_mobile_delivered'))
			->count_all($this->config->item('delivered_delivery_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('delivered_delivery_table'));

		$this->db->select('*,b.fullname as buyer,m.merchantname as merchant,a.application_name as app_name,d.identifier as device,c.fullname as courier');
		$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
		$this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
		$this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=b.id','left');
		$this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');


		$data = $this->db
			->where('status',$this->config->item('trans_status_mobile_delivered'))
			->limit($limit_count, $limit_offset)
			->get($this->config->item('delivered_delivery_table'));

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = anchor("admin/delivery/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/delivery/edit/".$key['id']."/", "Edit"); // Build actions links

			$aadata[] = array(
				'<span id="dt_'.$key['delivery_id'].'">'.$key['deliverytime'].'</span>',
				form_checkbox('assign[]',$key['delivery_id'],FALSE,'class="assign_check"').$key['delivery_id'],
				//$key['application_id'],
				$key['buyer'],
				$key['merchant'],
				$key['merchant_trans_id'],
				$key['courier'],
				$key['shipping_address'],
				$key['phone'],
				colorizestatus($key['status']),
				$key['reschedule_ref'],
				$key['revoke_ref']
			);
		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function delivered()
	{
		$this->breadcrumb->add_crumb('Orders','admin/delivery/incoming');
		$this->breadcrumb->add_crumb('Delivered Orders','admin/delivery/delivered');

		$data = $this->db->where('status','delivered')->get($this->config->item('delivered_delivery_table'));
		$result = $data->result_array();

		$this->table->set_heading(
			'Delivery Time',
			'Delivery ID',
			//'Application ID',
			'Buyer',
			'Merchant',
			'Merchant Trans ID',
			'Courier',
			'Shipping Address',
			'Phone',
			'Status',
			'Reschedule Ref',
			'Revoke Ref'
			); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_deliverytime" id="search_deliverytime" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_device" id="search_device" value="Search device" class="search_init" />',
			'<input type="text" name="search_deliveryid" value="Search delivery ID" class="search_init" />',
			'<input type="text" name="search_zone" id="search_zone" value="Search zone" class="search_init" />',
			form_button('do_archive','Archive Selection','id="doArchive"')
			);


		$page['ajaxurl'] = 'admin/delivery/ajaxdelivered';
		$page['page_title'] = 'Delivered Orders';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}

	private function getDevices(){
		$dev = $this->db->get($this->config->item('jayon_mobile_table'));
		return $dev->result_array();
	}

}

?>