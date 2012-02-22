<?php

function logged_in()
{
	$CI =& get_instance();
	if($CI->ag_auth->logged_in() == TRUE)
	{
		return TRUE;
	}
	
	return FALSE;
}

function username()
{
	$CI =& get_instance();
	return $CI->session->userdata('username');
}

function user_group($group)
{
	$CI =& get_instance();
	
	//$system_group = $CI->ag_auth->config['auth_groups'][$group];
	$result = $CI->db->select('id')->where('title',$group)->get($CI->ag_auth->config['auth_group_table']);
	$row = $result->row();
	$system_group = $row->id;
	
	if($system_group === $CI->session->userdata('group_id'))
	{
		return TRUE;
	}
}

function group_id($group){
	$CI =& get_instance();
	
	//$system_group = $CI->ag_auth->config['auth_groups'][$group];
	$result = $CI->db->select('id')->where('title',$group)->get($CI->ag_auth->config['auth_group_table']);
	$row = $result->row();
	$system_group = $row->id;
	
	if($system_group > 0)
	{
		return $system_group;
	}else{
		return FALSE;
	}
	
}

function group_desc($group){
	$CI =& get_instance();
	
	//$system_group = $CI->ag_auth->config['auth_groups'][$group];
	$result = $CI->db->select('description')->where('title',$group)->get($CI->ag_auth->config['auth_group_table']);
	$row = $result->row();
	$system_group = $row->description;
	
	if($system_group != '')
	{
		return $system_group;
	}else{
		return FALSE;
	}
}


function user_table()
{
	$CI =& get_instance();
	
	return $CI->ag_auth->user_table;
}

function group_table()
{
	$CI =& get_instance();
	
	return $CI->ag_auth->group_table;
}

?>