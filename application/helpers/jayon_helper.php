<?php

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

function get_zone_options(){
	$CI =& get_instance();
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
	$q = $CI->db->select($col.' as id ,'.$col.' as label, '.$col.' as value',false)->like($col,$zone)->get('districts');
	return $q->result_array();
}

function user_group_id($group)
{
	$CI =& get_instance();
	
	$this->db->select('id');
	$this->db->where('title',$group);
	$result = $this->db->get($this->ag_auth->config['auth_group_table']);
	$row = $result->row();
	return $row->id;
}

function xsend_notification($subject,$to,$template,$data){
	$CI =& get_instance();
	
	$config = array(
	    'protocol' => 'smtp',
	    'smtp_host' => $CI->config->item('smtp_host'),
	    'smtp_port' => $CI->config->item('smtp_port'),
	    'smtp_user' => $CI->config->item('notify_username'),
	    'smtp_pass' => $CI->config->item('notify_password'),
	    'charset'   => 'iso-8859-1'
	);
	
	$CI->load->library('email',$config);

	$CI->email->from($CI->config->item('notify_username'), 'Jayon Express Notification');
	$CI->email->to($to); 
	$CI->email->cc('admin@jayonexpress.com'); 
	$CI->email->subject($subject);
	$CI->email->message('Testing notification email.');	

	$result = $CI->email->send();
	
	return $result;
}


?>