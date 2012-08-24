<?php

function get_delivery_id($sequence,$merchant_id){
	$CI =& get_instance();
	$year_count = str_pad($sequence, $CI->config->item('year_sequence_pad'), '0', STR_PAD_LEFT);
	$merchant_id = str_pad($merchant_id, $CI->config->item('merchant_id_pad'), '0', STR_PAD_LEFT);
	$delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;

	return $delivery_id;
}

function get_yearly_sequence()
{
	$CI =& get_instance();

	$year = date('Y',time());

	$q = $CI->db->select('sequence')->where('year',$year)->get($CI->config->item('sequence_table'));
	if($q->num_rows() > 0){

	}else{
		$CI->db->insert($CI->config->item('sequence_table'),array('year'=>$year,'sequence'=>1));
		return 1;
	}
}

function get_zones($col = '*',$flatten = true){
	$CI =& get_instance();
	$CI->db->where('is_on',1);
	$q = $CI->db->select($col)->get('districts');
	if($flatten){
		foreach($q->result_array() as $val){
			$result[$val[$col]] = $val[$col];
		}
		return $result;
	}else{
		return $q->result_array();
	}
}

function get_courier($id = null,$flatten = true){
	$CI =& get_instance();
	if(!is_null($id)){
		 $CI->db->where('id',$id);	
	}

	$q = $CI->db->select(array('id','fullname'))->get('couriers');
	
	if($flatten){
		foreach($q->result_array() as $val){
			$result[$val['id']] = $val['fullname'];
		}
		return $result;
	}if(!is_null($id)){
		return $q->row_array();
	}else{
		return $q->result_array();
	}
}

function get_merchant($id = null,$flatten = true){
	$CI =& get_instance();
	if(!is_null($id)){
		 $CI->db->where('id',$id);	
	}

	$CI->db->where('group_id',user_group_id('merchant'));	

	$q = $CI->db->select(array('id','fullname','merchantname'))->get('members');
	if($flatten){
		foreach($q->result_array() as $val){
			$result[$val['id']] = $val['fullname'];
		}
		return $result;
	}if(!is_null($id)){
		return $q->row_array();
	}else{
		return $q->result_array();
	}
}

function get_city_status(){
	$CI =& get_instance();
	$CI->db->select('city');
	$CI->db->where('is_on',1);
	$CI->db->distinct('city');
	$q = $CI->db->get('districts');

	$res = array();
	foreach($q->result_array() as $r){
		$res[] = $r['city'];
	}

	return $res;
}

function get_zone_options(){
	$CI =& get_instance();
	$CI->db->where('is_on',1);
	$q = $CI->db->select('district,city')->get('districts');

	$result = array();

	$city = '';
	foreach($q->result_array() as $val){
		$result[$val['city']][$val['district']] = $val['district'];
	}

	//print_r($result);

	return $result;
}

function ajax_find_zones($zone,$col = 'district'){
	$CI =& get_instance();
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->where('is_on',1)->get('districts');
	return $q->result_array();
}

function ajax_find_zones_by_city($zone,$city,$col = 'district'){
	$CI =& get_instance();
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)
		->where('city',$city)
		->like($col,$zone)
		->get('districts');
	return $q->result_array();
}

function ajax_find_cities($zone,$col = 'city'){
	$CI =& get_instance();
	$CI->db->distinct();
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->get('districts');
	return $q->result_array();
}

function ajax_find_provinces($zone,$col = 'province'){
	$CI =& get_instance();
	$CI->db->distinct();
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->get('districts');
	return $q->result_array();
}

function ajax_find_countries($zone,$col = 'country'){
	$CI =& get_instance();
	$CI->db->distinct();
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->get('districts');
	return $q->result_array();
}

function ajax_find_courier($zone,$col = 'fullname',$idcol = 'id'){
	$CI =& get_instance();
	$q = $CI->db->select($idcol.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->get('couriers');
	return $q->result_array();
}

function ajax_find_merchants($zone,$col = 'fullname',$idcol = 'id'){
	$CI =& get_instance();
	$group_id = user_group_id('merchant');

	$q = $CI->db->select($idcol.' as id ,merchantname as value, concat_ws(\'::\',merchantname,fullname,email) as label, merchantname as merchantname,fullname as fullname,email as email',false)
		->like('fullname',$zone)
		->or_like('merchantname',$zone)
		->or_like('username',$zone)
		->or_like('email',$zone)
		->where('group_id',$group_id)
		->distinct()
		->get('members');
	return $q->result_array();
}

function ajax_find_buyer($zone,$col = 'fullname',$idcol = 'id'){
	$CI =& get_instance();
	$group_id = user_group_id('buyer');
	$q = $CI->db->select($idcol.' as id ,'.$col.' as label, '.$col.' as value, email as email, concat_ws(\',\',street,district,province,city,country) as shipping, phone as phone',false)
		->like('fullname',$zone)
		->or_like('merchantname',$zone)
		->or_like('username',$zone)
		->or_like('email',$zone)
		->where('group_id',$group_id)
		->distinct()
		->get('members');
	return $q->result_array();
}

function ajax_find_buyer_email($zone,$col = 'fullname',$idcol = 'id'){
	$CI =& get_instance();
	$group_id = user_group_id('buyer');

	$q = $CI->db->select($idcol.' as id ,email as label, email as value, fullname as fullname, concat_ws(\',\',street,district,province,city,country) as shipping,phone as phone',false)
		->like('email',$zone)
		->where('group_id',$group_id)
		->distinct()
		->get('members');
	return $q->result_array();
}

function ajax_find_device($zone,$col = 'descriptor'){
	$CI =& get_instance();
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->get('devices');
	return $q->result_array();
}

function user_group_id($group)
{
	$CI =& get_instance();

	$CI->db->select('id');
	$CI->db->where('title',$group);
	$result = $CI->db->get($CI->ag_auth->config['auth_group_table']);
	$row = $result->row();
	return $row->id;
}

function get_weight_range($tariff,$app_id = null){
	$CI =& get_instance();

	if($tariff > 0){
		$CI->db->select('kg_from,kg_to');
		$CI->db->where('total',$tariff);
		$result = $CI->db->get($CI->config->item('jayon_delivery_fee_table'));
		$row = $result->row();
		return $row->kg_from.' kg - '.$row->kg_to.' kg';		
	}else{
		return 0;
	}
}

function get_cod_tariff($total_price,$app_id = null){
	$CI =& get_instance();

	$CI->db->select_max('to_price','max');
	$result = $CI->db->get($CI->config->item('jayon_cod_fee_table'));
	$row = $result->row();

	if($total_price > $row->max){
		$CI->db->select_max('surcharge');
		$result = $CI->db->get($CI->config->item('jayon_cod_fee_table'));
		$row = $result->row();
	}else{
		$CI->db->select('surcharge');
		$CI->db->where('from_price <= ',$total_price);
		$CI->db->where('to_price >= ',$total_price);
		$result = $CI->db->get($CI->config->item('jayon_cod_fee_table'));
		$row = $result->row();			
	}

	return $row->surcharge;

}

function get_cod_table($app_id){
	$CI =& get_instance();

	$CI->db->where('app_id',$app_id);
	$CI->db->order_by('seq','asc');
	$result = $CI->db->get($CI->config->item('jayon_cod_fee_table'));

	if($result->num_rows() > 0){
		return $result->result();
	}else{
		return false;
	}

}

function get_delivery_charge_table($app_id){
	$CI =& get_instance();

	$CI->db->where('app_id',$app_id);
	$CI->db->order_by('seq','asc');
	$result = $CI->db->get($CI->config->item('jayon_delivery_fee_table'));

	if($result->num_rows() > 0){
		return $result->result();
	}else{
		return false;
	}

}

function get_slot_max(){

	$CI =& get_instance();

	$CI->db->where('is_on',1);
	$slots = $CI->db->get($CI->config->item('jayon_timeslots_table'));

	$slot = array();

	if($slots->num_rows() > 0){
		$slot[0] = 0;
		foreach ($slots->result() as $r) {
			$slot[$r->slot_no] = $r->time_to.':00';
		}
	}

	return $slot;
}

function get_slot_select(){

	$CI =& get_instance();

	$CI->db->where('is_on',1);
	$slots = $CI->db->get($CI->config->item('jayon_timeslots_table'));

	if($slots->num_rows() > 0){
		$slot[0] = 'Select delivery slot';
		foreach ($slots->result() as $r) {
			$slot[$r->slot_no] = $r->time_from.':00 - '.$r->time_to.':00';
		}
	}else{
		$slot[0] = 'Select delivery slot';
	}

	$select = form_dropdown('buyerdeliverytime',$slot,null,'id="buyerdeliverytime"');

	return $select;
}

function get_slot_range($slot){
	$CI =& get_instance();

	if($slot > 0){
		$CI->db->select('time_from,time_to');
		$CI->db->where('slot_no',$slot);
		$CI->db->where('is_on',1);
		$result = $CI->db->get($CI->config->item('jayon_timeslots_table'));
		$row = $result->row();
		return $row->time_from.':00 - '.$row->time_to.':00';		
	}else{
		return 0;
	}
}

function get_slot_count(){
	$CI =& get_instance();

	$CI->db->where('is_on',1);
	$slots = $CI->db->count_all_results($CI->config->item('jayon_timeslots_table'));

	return $slots;
}

function get_app_id_from_key($key){
	$CI =& get_instance();

	$CI->db->select('id');
	$CI->db->where('key',$key);
	$result = $CI->db->get($CI->config->item('applications_table'));

	if($result->num_rows() > 0){
		$res = $result->row();
		return $res->id;
	}else{
		return 0;
	}

}

function get_option($key){
	$CI =& get_instance();

	$CI->db->select('val');
	$CI->db->where('key',$key);
	$result = $CI->db->get($CI->config->item('jayon_options_table'));
	$row = $result->row();
	return $row->val;
}

function getdateblock($month = null, $city = null){
	$CI =& get_instance();
	$blocking = array();
	$month = (is_null($month))?date('m',time()):$month;
	$year = date('Y',time());

	//determine max daily capacity by city ( as devices are assigned by city )
	if(!is_null($city)){
		$devnum = $CI->db
			->where('city',$city)
			->count_all_results($CI->config->item('jayon_devices_table'));
	}else{
		$devnum = 1;
	}

	$maxcap = get_option('daily_shifts') * get_option('quota_per_shift') * $devnum;

	//get holidays

	$holidays = getholidays();
	$weekend_on = get_option('weekend_delivery');
	$holiday_on = get_option('holiday_delivery');

	for($m = $month; $m < ($month + 2);$m++){
		for($i = 1;$i < 32;$i++){
			//print $date."\r\n";
			if(checkdate($m,$i,$year)){
				//check weekends
				$month = str_pad($m,2,'0',STR_PAD_LEFT);
				$day = str_pad($i,2,'0',STR_PAD_LEFT);
				$date = $year.'-'.$month.'-'.$day;
				$day = getdate(strtotime($date));
				//print_r($day)."\r\n";
				if(($day['weekday'] == 'Sunday' || $day['weekday'] == 'Saturday') && !$weekend_on){
					//print $date." : ".$slot."\r\n";
					$blocking[$date] = 'weekend';
				}else if(in_array($date, $holidays) && !$holiday_on){
					$blocking[$date] = 'holiday';
				}else if(overquota($date)){
					$blocking[$date] = 'overquota';
				}else{
					$blocking[$date] = 'open';
				}
			}
		}
	}
	return json_encode($blocking);
}

function overquota($date){
	$CI =& get_instance();

	$CI->db->where('is_on',1);
	$devcount = $CI->db->count_all($CI->config->item('jayon_devices_table'));
	$slots = get_slot_count();
	$shifts = (int) get_option('quota_per_shift');

	$dailyquota = $devcount * $slots * $shifts;

	/*
	$CI->db->like('buyerdeliverytime',$date);
	$CI->db->where('assignment_date','0000-00-00');
	$CI->db->or_where('assignment_date',$date);
	$CI->db->from($CI->config->item('incoming_delivery_table'));
	$quota = $CI->db->count_all_results();
	*/

	$sqlf = "SELECT COUNT( * ) AS  numrows FROM %s WHERE  (buyerdeliverytime LIKE  '%s%%' AND  assignment_date =  '%s') OR  assignment_date =  '%s'";
	$sql = sprintf($sqlf,$CI->config->item('incoming_delivery_table'),$date,'0000-00-00',$date);
	$quota = $CI->db->query($sql);
	$quota = $quota->row()->numrows;

	if($dailyquota >= $quota){
		return false;
	}else{
		return true;
	}	
}

function getholidays(){
	$CI =& get_instance();
	$CI->db->select('holiday');
	$h = array();
	$holidays = $CI->db->get($CI->config->item('jayon_holidays_table'));
	foreach ($holidays->result_array() as $key => $value) {
		$h[] = $value['holiday'];
	}
	return $h;
}

function checkdateblock($date = null, $city = null){
	$CI =& get_instance();
	if(is_null($date)){
		return false;
	}else{

		if(!is_null($city)){
			$devnum = $CI->db
				->where('city',$city)
				->count_all_results($CI->config->item('jayon_devices_table'));
		}else{
			$devnum = 1;
		}

		$maxcap = get_option('daily_shifts') * get_option('quota_per_shift') * $devnum;

		//get holidays

		$hdays = $CI->db->select('holiday')
			->where('holiday',$date)
			->get($CI->config->item('jayon_holidays_table'));

		if($hdays->num_rows() > 0){
			$isholiday = true;
		}else{
			$isholiday = false;
		}

		$dat = explode('-',$date);

		$day = getdate(strtotime($date));

		if($day['weekday'] == 'Sunday' || $day['weekday'] == 'Saturday'){
			return 'weekend';
		}else if($isholiday){
			return 'holiday';
		}else{
			$slot = $CI->db
				->where('assignment_date',$date)
				->count_all_results($CI->config->item('assigned_delivery_table'));
			$slot = ($slot < $maxcap)?'open':'full';
			return $slot;
		}
	}
}


function get_thumbnail($delivery_id){
	$CI =& get_instance();

	if(file_exists($CI->config->item('picture_path').$delivery_id.'.jpg')){
		if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg')){
			$thumbnail = base_url().'public/reciever-thumb/th_'.$delivery_id.'.jpg';
			$thumbnail = sprintf('<img src="%s" />',$thumbnail);					
		}else{
			$thumbnail = $CI->ag_asset->load_image('th_nopic.jpg');
		}
	}else{
		$thumbnail = $CI->ag_asset->load_image('th_nopic.jpg');
	}

	return $thumbnail;
}

function delivery_log($data){
	$CI =& get_instance();
	$CI->db->insert($CI->config->item('delivery_log_table'),$data);
	return true;
}

function access_log($data){
	$CI =& get_instance();
	$CI->db->insert($CI->config->item('access_log_table'),$data);
	return true;
}
function full_reschedule($delivery_id, $datachanged){
	$CI =& get_instance();
	$old_order = $CI->db->where('delivery_id',$delivery_id)->get($CI->config->item('assigned_delivery_table'));
	$old_order = $old_order->row_array();

	$new_order = array_replace($old_order,$datachanged);
	$new_order['status'] = $CI->config->item('trans_status_new');
	$new_order['reschedule_ref'] = $delivery_id;
	$new_order['reattemp'] = (int) $new_order['reattemp'] + 1;
	unset($new_order['delivery_id']);
	unset($new_order['id']);

	$old_delivery_id = $delivery_id;

	$CI->db->insert($CI->config->item('assigned_delivery_table'),$new_order);

	$sequence = $this->db->insert_id();

	$year_count = str_pad($sequence, 10, '0', STR_PAD_LEFT);
	$merchant_id = str_pad($new_order['merchant_id'], 8, '0', STR_PAD_LEFT);
	$delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;

	$this->db->where('id',$sequence)->update($this->config->item('assigned_delivery_table'),array('delivery_id'=>$delivery_id));

	$old_details = $CI->db->where('delivery_id',$old_delivery_id)->get($CI->config->item('delivery_details_table'));

	foreach ($old_details->result_array() as $detail){
		$detail['delivery_id'] = $delivery_id;
		$CI->db->insert($CI->config->item('delivery_details_table'),$detail);
	}

	return true;
}

function send_notification($subject,$to,$cc = null,$reply_to = null,$template = 'default',$data = null,$attachment = null){
	$CI =& get_instance();

	$config = array(
		'protocol' => 'smtp',
		'smtp_host' => $CI->config->item('smtp_host'),
		'smtp_port' => $CI->config->item('smtp_port'),
		'smtp_user' => $CI->config->item('notify_username'),
		'smtp_pass' => $CI->config->item('notify_password'),
		'charset'   => 'iso-8859-1',
		'mailtype'	=> 'html'
	);

	$CI->load->library('email',$config);

	$CI->email->set_newline("\r\n");

	$CI->email->from($CI->config->item('notify_username'), 'Jayon Express Notification');

	if(is_null($data)){
		$data['type'] = 'notification';
	}

	if(!is_null($reply_to) && $reply_to != ''){
		$CI->email->reply_to($reply_to);
	}

	if(is_array($to)){
		foreach($to as $em){
			$CI->email->to($em);
		}
		$log['to'] = implode(';',$to);
	}else{
		$CI->email->to($to);
		$log['to'] = $to;			
	}

	if(!is_null($cc) && $cc != ''){
		if(is_array($cc)){
			foreach ($cc as $cm) {
				$CI->email->cc($cm);
			}
			$log['cc'] = implode(';',$cc);
		}else{
			$CI->email->cc($cc);
			$log['cc'] = $cc;			
		}
	}

	if(!is_null($attachment)){
		if(is_array($attachment)){
			foreach($attachment as $att){
				$CI->email->attach($att);
			}
			$log['att'] = implode(';',$attachment);
		}else{
			$CI->email->attach($attachment);			
			$log['att'] = $attachment;			
		}
	}

	$CI->email->cc('admin@jayonexpress.com');
	$CI->email->subject($subject);

	$body = $CI->load->view('email/'.$template,$data,TRUE);

	$CI->email->message($body);

	$result = $CI->email->send();

	$log['timestamp'] = date('Y-m-d h:i:s',time());
	$log['from'] = $CI->config->item('notify_username');
	$log['subject'] = $subject;
	$log['body'] = $body;
	$log['delivery_id'] = (isset($data['delivery_id']))?$data['delivery_id']:'-';
	$log['status'] = (isset($data['status']))?$data['delivery_id']:'-';
	$log['msg_status'] = $result;

	$CI->db->insert($CI->config->item('jayon_email_outbox_table'),$log);

	return $result;
}

function send_admin($subject,$to,$cc = null,$template = 'default',$data = '',$attachment = null){
	$CI =& get_instance();

	$config = array(
		'protocol' => 'smtp',
		'smtp_host' => $CI->config->item('smtp_host'),
		'smtp_port' => $CI->config->item('smtp_port'),
		'smtp_user' => $CI->config->item('admin_username'),
		'smtp_pass' => $CI->config->item('admin_password'),
		'charset'   => 'iso-8859-1',
		'mailtype'	=> 'html'
	);

	$CI->load->library('email',$config);

	$CI->email->set_newline("\r\n");

	$CI->email->from($CI->config->item('notify_username'), 'Jayon Express Admin');

	if(is_null($data)){
		$data['type'] = 'adminmessage';
	}

	//admin message should reply to admin
	$CI->email->reply_to($CI->config->item('admin_username'));

	if(is_array($to)){
		foreach($to as $em){
			$CI->email->to($em);
		}
		$log['to'] = implode(';',$to);
	}else{
		$CI->email->to($to);
		$log['to'] = $to;			
	}

	if(!is_null($cc)){
		if(is_array($cc)){
			foreach ($cc as $cm) {
				$CI->email->cc($cm);
			}
			$log['cc'] = implode(';',$cc);
		}else{
			$CI->email->cc($cc);
			$log['cc'] = $cc;			
		}
	}

	if(!is_null($attachment)){
		if(is_array($attachment)){
			foreach($attachment as $att){
				$CI->email->attach($att);
			}
			$log['att'] = implode(';',$attachment);
		}else{
			$CI->email->attach($attachment);			
			$log['att'] = $attachment;			
		}
	}

	$CI->email->cc('admin@jayonexpress.com');
	$CI->email->subject($subject);

	$body = $CI->load->view('email/'.$template,$data,TRUE);

	$CI->email->message($body);

	$result = $CI->email->send();

	$log['timestamp'] = date('Y-m-d h:i:s',time());
	$log['from'] = $CI->config->item('admin_username');
	$log['subject'] = $subject;
	$log['body'] = $body;
	$log['delivery_id'] = (isset($data['delivery_id']))?$data['delivery_id']:'-';
	$log['status'] = (isset($data['status']))?$data['delivery_id']:'-';
	$log['msg_status'] = $result;

	$CI->db->insert($CI->config->item('jayon_email_outbox_table'),$log);

	return $result;
}

function colorizestatus($status){

	$colors = config_item('status_colors');
	if($status == '' || !in_array($status, array_keys($colors))){
		$class = 'brown';
		$status = 'N/A';
	}else{
		$class = $colors[$status];
	}

	return sprintf('<span class="%s">%s</span>',$class,$status);
}

function getmonthlydatacount($year,$month,$where = null,$merchant_id = null){
	$CI =& get_instance();

	$series = array();
	$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

	$data = array();
	for($i = 1 ; $i <= $num;$i++){

		if($i > 9){
			$day = $i;
		}else{
			$day = '0'.$i;
		}

		//print $day."\r\n";

		$date = $year.'-'.$month.'-'.$day;

		$CI->db->like('assignment_date', $date);
		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}
		if(!is_null($where)){
			$CI->db->where($where);
		}
		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$count = $CI->db->count_all_results();

		//print $CI->db->last_query();

		$timestamp = strtotime($date);
		$timestamp = (double)$timestamp;
		$series[] = array('x'=>$timestamp,'y'=>$count);
	}

	$series = str_replace('"', '', json_encode($series)) ;
	return $series;
}

function getmonthlydatacountarray($year,$month,$where = null,$merchant_id = null){
	$CI =& get_instance();

	$series = array();
	$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

	$data = array();
	for($i = 1 ; $i <= $num;$i++){

		if($i > 9){
			$day = $i;
		}else{
			$day = '0'.$i;
		}

		//print $day."\r\n";

		$date = $year.'-'.$month.'-'.$day;

		/*
		if(is_null($where)){
			$CI->db->like('ordertime', $date, 'after');
		}else{
			if($where['status'] == 'confirmed' || $where['status'] == 'pending'){
				$CI->db->like('buyerdeliverytime', $date, 'after');
				$CI->db->where($where);
			}else{
				$CI->db->like('assignment_date', $date, 'after');
				$CI->db->where($where);
			}
		}
		*/

		$CI->db->like('ordertime', $date, 'after');		

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}

		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$count = $CI->db->count_all_results();

		//print $CI->db->last_query();

		//$timestamp = strtotime($date);
		//$timestamp = (double)$timestamp;
		$series[] = array($day,$count);
	}

	//$series = str_replace('"', '', json_encode($series)) ;
	return $series;
}

function getrangedatacountarray($year,$from,$to,$where = null,$merchant_id = null){
	$CI =& get_instance();

	$series = array();
	//$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

	$start = strtotime($from);
	$end = strtotime($to);

	if($start == $end){
		$num = 1;
	}else{
		$num = ceil(abs($end - $start) / 86400);
	}


	$data = array();
	for($i = 1 ; $i <= $num;$i++){

		if($i > 9){
			$day = $i;
		}else{
			$day = '0'.$i;
		}

		//print $day."\r\n";

		//$date = $year.'-'.$month.'-'.$day;

		$date = date('Y-m-d',$start + ( $i * 86400));

		/*
		if(is_null($where)){
			$CI->db->like('ordertime', $date, 'after');
		}else{
			if($where['status'] == 'confirmed' || $where['status'] == 'pending'){
				$CI->db->like('buyerdeliverytime', $date, 'after');
				$CI->db->where($where);
			}else{
				$CI->db->like('assignment_date', $date, 'after');
				$CI->db->where($where);
			}
		}
		*/
		if(isset($where['status']) && $where['status'] == 'all'){
			unset($where['status']);
		}

		if(isset($where['status']) && $where['status'] == 'incoming'){
			$column = 'ordertime';
			unset($where['status']);
		}else{
			$column = 'assignment_date';
		}

		//$daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $from, $to);

		//$CI->db->where($daterange, null, false);
		$CI->db->like($column,$date,'after');
		$CI->db->where($column.' != ','0000-00-00');

		//$CI->db->like('ordertime', $date, 'after');		

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}

		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$count = $CI->db->count_all_results();

		//print $CI->db->last_query();

		//$timestamp = strtotime($date);
		//$timestamp = (double)$timestamp;
		$series[] = array($date,$count);
	}

	//$series = str_replace('"', '', json_encode($series)) ;
	return $series;
}
	
?>