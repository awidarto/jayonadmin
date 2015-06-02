<?php

function iddate($date,$withyear = true){
    $idmonth = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    $tahun = substr($date, 0, 4);
    $bulan = substr($date, 5, 2);
    $tgl   = substr($date, 8, 2);

    if($withyear == true){
        $result = $tgl . " " . $idmonth[(int)$bulan-1] . " ". $tahun;
    }else{
        $result = $tgl . " " . $idmonth[(int)$bulan-1];
    }
    return $result;
}

function normalphone($number){
    $numbers = explode('/',$number);
    if(is_array($numbers)){
        $nums = array();
        foreach($numbers as $number){

            $number = str_replace(array('-',' ','(',')','[',']','{','}'), '', $number);

            if(preg_match('/^\+/', $number)){
                if( preg_match('/^\+62/', $number)){
                    $number = preg_replace('/^\+62|^620/', '62', $number);
                }else{
                    $number = preg_replace('/^\+/', '', $number);
                }
            }else if(preg_match('/^62/', $number)){
                $number = preg_replace('/^620/', '62', $number);
            }else if(preg_match('/^0/', $number)){
                $number = preg_replace('/^0/', '62', $number);
            }

            $nums[] = $number;
        }
        $number = implode('/',$nums);
    }else{

        $number = str_replace(array('-',' ','(',')'), '', $number);

        if(preg_match('/^\+/', $number)){
            if( preg_match('/^\+62/', $number)){
                $number = preg_replace('/^\+62|^620/', '62', $number);
            }else{
                $number = preg_replace('/^\+/', '', $number);
            }
        }else if(preg_match('/^62/', $number)){
            $number = preg_replace('/^620/', '62', $number);
        }else if(preg_match('/^0/', $number)){
            $number = preg_replace('/^0/', '62', $number);
        }
    }

    return $number;
}

function escapeVars($str, $replace = ''){
    $str = str_replace(array('http://','http'), '',$str);
    $str = str_replace(array('/','.',':',' '), $replace,$str);
    return $str;
}

function idr($in, $transform = true){
    if($in > 0){
        return number_format((double) $in,2,',','.');
    }else{
        $num = abs((double) $in);
        if($transform){
            return '<span style="color:red">('.number_format($num,2,',','.').')</span>' ;
        }else{
            return number_format($num,2,',','.') ;
        }
    }
}

function get_print_default($user_group = 'admin'){
    $CI =& get_instance();

    $user_id = $CI->session->userdata('userid');
    $user_group = user_group_id('admin');

    $df = $CI->db->where('user_id',$user_id)
            ->where('user_group',$user_group)
            ->get('print_defaults');

    //print $CI->db->last_query();

    $result = $df->row_array();

    return $result;
}


function get_apps($merchant_id){
    $CI =& get_instance();

    $apps = $CI->db->where('merchant_id',$merchant_id)
        ->get($CI->config->item('applications_table'))->result_array();

    return $apps;
}


function get_delivery_id($sequence,$merchant_id,$delivery_id = null){
	$CI =& get_instance();

    if(is_null($delivery_id) || $delivery_id == ''){
        $year_count = str_pad($sequence, $CI->config->item('year_sequence_pad'), '0', STR_PAD_LEFT);
        $merchant_id = str_pad($merchant_id, $CI->config->item('merchant_id_pad'), '0', STR_PAD_LEFT);
        $delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;
    }else{
        $dr = $CI->db->where('merchant_id',$merchant_id)
                ->where('awb_string',$delivery_id)
                ->where('is_used',0)
                ->from('awb_generated')
                ->get();

        if($dr->num_rows() > 0){

            $delivery_id = $delivery_id;
            $up = array('is_used'=>1, 'used_at'=>date('Y-m-d H:i:s', time() ) );

            $CI->db->where('awb_string', $delivery_id)->update('awb_generated',$up);

        }else{

            $year_count = str_pad($sequence, $CI->config->item('year_sequence_pad'), '0', STR_PAD_LEFT);
            $merchant_id = str_pad($merchant_id, $CI->config->item('merchant_id_pad'), '0', STR_PAD_LEFT);
            $delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;
        }
    }


	return $delivery_id;
}

function generate_delivery_id($sequence,$merchant_id,$date = null){
    $CI =& get_instance();
    $year_count = str_pad($sequence, $CI->config->item('year_sequence_pad'), '0', STR_PAD_LEFT);
    $merchant_id = str_pad($merchant_id, $CI->config->item('merchant_id_pad'), '0', STR_PAD_LEFT);
    if(is_null($date)){
        $delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;
    }else{
        $date = date('d-mY', strtotime($date) );
        $delivery_id = $merchant_id.'-'.$date.'-'.$year_count;
    }

    return $delivery_id;
}

function get_devices(){
	$CI =& get_instance();

	$identifiers = $CI->db->select('identifier')
		->get($CI->config->item('jayon_devices_table'))->result();

	return $identifiers;
}

function get_device_list(){
    $CI =& get_instance();

    $identifiers = $CI->db->select('id,identifier')
        ->get($CI->config->item('jayon_devices_table'))->result();

    return $identifiers;
}

function get_device_color($identifier){
	$CI =& get_instance();

	$col = $CI->db->select('color')
		->where('identifier',$identifier)
		->get($CI->config->item('jayon_devices_table'))->row();

	if($col){
		return $col->color;
	}else{
		return '#FF0000';
	}

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

function get_zone_options( $enc = false){
	$CI =& get_instance();
	$CI->db->where('is_on',1);
	$q = $CI->db->select('district,city')->get('districts');

	$result = array();

	$city = '';
	foreach($q->result_array() as $val){
        if($enc){
            $result[$val['city']][$val['district']] = urlencode($val['district']);
        }else{
            $result[$val['city']][$val['district']] = $val['district'];
        }
	}

	//print_r($result);

	return $result;
}

function get_city_options(){
    $CI =& get_instance();
    $CI->db->where('is_on',1);
    $q = $CI->db->select('district,city')->get('districts');

    $result = array();

    $city = '';
    foreach($q->result_array() as $val){
        $result[$val['city']] = $val['city'];
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

function ajax_find_buyer($zone,$col = 'buyer_name',$idcol = 'id',$merchant_id = null){
	$CI =& get_instance();


    $CI->db->distinct();
    if(!is_null($merchant_id)){
        $CI->db->where('buyers.merchant_id',$merchant_id);
    }
    $CI->db->and_()
        ->group_start()->like('buyers.buyer_name',$zone)
        ->or_like('buyers.email',$zone)
        ->group_end();

    $CI->db->select('buyers.'.$idcol.' as id ,buyers.'.$col.' as label, buyers.'.$col.' as value, buyers.merchant_id as merchant_id, buyers.email as email, buyers.shipping_address as shipping, buyers.mobile1 as mobile1,buyers.mobile2 as mobile2, buyers.phone as phone',false);
    //$CI->db->join($CI->config->item('assigned_delivery_table').' as m','buyers.id = m.buyer_id','left');


    $q = $CI->db->get('buyers');


	return $q->result_array();
}

function ajax_find_phone($zone,$col = 'phone',$idcol = 'id',$merchant_id = null){
    $CI =& get_instance();

    if(is_null($merchant_id)){
        return array();
    }else{
        $CI->db->distinct();
        $CI->db->where('buyers.merchant_id',$merchant_id)
            ->like('buyers.'.$col,$zone);
        $CI->db->select('buyers.'.$idcol.' as id ,concat_ws(\'::\',buyers.'.$col.', buyers.buyer_name) as label, buyers.'.$col.' as value, buyers.merchant_id as merchant_id, buyers.buyer_name as buyer_name, buyers.email as email, buyers.shipping_address as shipping, buyers.mobile1 as mobile1,buyers.mobile2 as mobile2, buyers.phone as phone',false);
        //$CI->db->join($CI->config->item('assigned_delivery_table').' as m','buyers.id = m.buyer_id','left');
        $q = $CI->db->get('buyers');
        return $q->result_array();
    }

}


function old_ajax_find_buyer($zone,$col = 'fullname',$idcol = 'id',$merchant_id = null){
	$CI =& get_instance();
	$group_id = user_group_id('buyer');
	$q = $CI->db->select($idcol.' as id ,'.$col.' as label, '.$col.' as value, email as email, concat_ws(\',\',street,district,province,city,country) as shipping, phone as phone',false)
		->like('fullname',$zone)
		->or_like('merchantname',$zone)
		->or_like('username',$zone)
		->or_like('email',$zone)
		->where('group_id',$group_id)
		->distinct()
		->get('buyers');
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

function user_group_desc($id)
{
    $CI =& get_instance();

    $CI->db->select('description');
    $CI->db->where('id',$id);
    $result = $CI->db->get($CI->ag_auth->config['auth_group_table']);
    $row = $result->row();
    return $row->description;
}


function get_weight_range($tariff,$app_id = null){
	$CI =& get_instance();

	if($tariff > 0){
		$CI->db->select('kg_from,kg_to');
		$CI->db->where('total',$tariff);
        if(!is_null($app_id)){
            $CI->db->where('app_id',$app_id);
        }
		$result = $CI->db->get($CI->config->item('jayon_delivery_fee_table'));
		if($result->num_rows() > 0){
			$row = $result->row();
			return $row->kg_from.' kg - '.$row->kg_to.' kg';
		}else{
			return 0;
		}
	}else{
		return 0;
	}
}

function get_weight_tariff($weight, $delivery_type ,$app_id = null){
    $CI =& get_instance();

    $weight = floatval($weight);

    if($weight > 0){
        $CI->db->select('total');
        if(!is_null($app_id)){
            $CI->db->where('app_id',$app_id);
        }
        $CI->db->where('kg_from <= ', $weight );
        $CI->db->where('kg_to >= ', $weight );
        if($delivery_type == 'PS'){
            $result = $CI->db->get($CI->config->item('jayon_pickup_fee_table'));
        }else{
            $result = $CI->db->get($CI->config->item('jayon_delivery_fee_table'));
        }
        if($result->num_rows() > 0){
            $row = $result->row();
            return $row->total;
        }else{
            return 0;
        }
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
		$CI->db->where('from_price <= ', doubleval($total_price) );
		$CI->db->where('to_price >= ', doubleval($total_price) );
        if(!is_null($app_id)){
            $CI->db->where('app_id',$app_id);
        }
		$result = $CI->db->get($CI->config->item('jayon_cod_fee_table'));
		$row = $result->row();
	}

    if(isset($row->surcharge)){
        return $row->surcharge;
    }else{
        return 0;
    }

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

function get_pickup_charge_table($app_id){
	$CI =& get_instance();

	$CI->db->where('app_id',$app_id);
	$CI->db->order_by('seq','asc');
	$result = $CI->db->get($CI->config->item('jayon_pickup_fee_table'));

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

    $maxholiday = getmaxholiday();

    $maxyear = date('Y',strtotime($maxholiday));
    $maxmonth = date('m',strtotime($maxholiday));

	$weekend_on = get_option('weekend_delivery');
	$holiday_on = get_option('holiday_delivery');

    for($y = $year; $y <= $maxyear ;$y++){
        if($y > $year){
            $month = 1;
        }
        for($m = $month; $m < ($month + 3);$m++){
            for($i = 1;$i < 32;$i++){
                //print $date."\r\n";
                if(checkdate($m,$i,$y)){
                    //check weekends
                    $month = str_pad($m,2,'0',STR_PAD_LEFT);
                    $day = str_pad($i,2,'0',STR_PAD_LEFT);
                    $date = $y.'-'.$month.'-'.$day;
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

    //$sqlf = "SELECT COUNT( * ) AS  numrows FROM %s WHERE  assignment_date =  '%s'";
    //$sql = sprintf($sqlf,$CI->config->item('incoming_delivery_table'),$date);

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

function getmaxholiday(){
    $CI =& get_instance();
    $CI->db->select_max('holiday');
    $maxholiday = $CI->db->get($CI->config->item('jayon_holidays_table'))->row();
    return $maxholiday->holiday;
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

function get_sign($delivery_id){
    $CI =& get_instance();

    if(file_exists($CI->config->item('picture_path').$delivery_id.'_sign.jpg')){
        $sthumbnail = base_url().'public/receiver/'.$delivery_id.'_sign.jpg';
        $thumbnail = sprintf('<img style="cursor:pointer;width:70px;height:auto;" class="sign" alt="'.$delivery_id.'" src="%s?'.time().'" />',$sthumbnail);
        return $thumbnail;
    }else{
        return '';
    }

}

function get_pusign($merchant_id, $app_id, $date){
    $CI =& get_instance();

    if(file_exists($CI->config->item('public_path').'pickup_sign/'.$merchant_id.'_'.$app_id.'_'.$date.'_sign.jpg')){
        $exist = true;
        $thumbnail = base_url().'public/pickup_sign/'.$merchant_id.'_'.$app_id.'_'.$date.'_sign.jpg';
    }else{
        $exist = false;
        $thumbnail = base_url().'assets/images/th_nopic.jpg';
    }

    return array('exist'=>$exist,'sign'=>$thumbnail) ;
}

function get_logo($merchant_id){
    $CI =& get_instance();

    if(file_exists($CI->config->item('public_path').'logo/logo_'.$merchant_id.'.jpg')){
        $exist = true;
        $thumbnail = base_url().'public/logo/logo_'.$merchant_id.'.jpg';
    }else{
        $exist = false;
        $thumbnail = base_url().'assets/images/th_nopic.jpg';
    }

    return array('exist'=>$exist,'logo'=>$thumbnail) ;
}

function get_thumbnail($delivery_id, $class = 'thumb'){
	$CI =& get_instance();

    $existingpic = glob($CI->config->item('picture_path').$delivery_id.'*.jpg');

    //print_r($existingpic);

    $pidx = count($existingpic);

    foreach($existingpic as $epic){
        if(!file_exists($CI->config->item('thumbnail_path').'th_'.$epic )){
            generate_thumbnail( str_replace('.jpg', '', $epic ) );
        }
    }

    if($pidx > 1){
        $ths = '';
        foreach($existingpic as $epic){
            $epic2 = str_replace($CI->config->item('picture_path'), '', $epic);


            //if(!file_exists($CI->config->item('thumbnail_path').'th_'.$epic )){
                $thumb = base_url().'public/receiver/'.$epic2;
                $ths .= sprintf('<img style="width:45px;35px;float:left;" alt="'.$epic2.'" src="%s?'.time().'" />',$thumb);
            //}
        }

        $class = 'thumb_multi';

        $thumper = '<img class="'.$class.'" style="width:100%;height:100%;" alt="'.$delivery_id.'" src="'.base_url().'assets/images/10.png" >';

        $ths .= '<div style="width:100%;height:100%;display:block;position:absolute;top:0px;left:0px;">'.$thumper.'</div>';

        $thumbnail = '<div style="width:100px;height:75px;clear:both;display:block;cursor:pointer;position:relative;border:thin solid brown;overflow-y:hidden;">'.$ths.'</div>';
    }else{
        if(file_exists($CI->config->item('picture_path').$delivery_id.'.jpg')){
            if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg')){
                $thumbnail = base_url().'public/receiver_thumb/th_'.$delivery_id.'.jpg';
                $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
            }else{
                if(generate_thumbnail($delivery_id)){
                    $thumbnail = base_url().'public/receiver_thumb/th_'.$delivery_id.'.jpg';
                    $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                }else{
                    $thumbnail = $CI->ag_asset->load_image('th_nopic.jpg');
                    $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
                }
            }
        }else{
            if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg')){
                if($pidx > 0){
                    $class = 'thumb_multi';
                }
                $thumbnail = base_url().'public/receiver_thumb/th_'.$delivery_id.'.jpg';
                $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
            }else{
                $thumbnail = base_url().'assets/images/th_nopic.jpg';
                $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
            }
        }
    }

    $has_sign = false;

    if(file_exists($CI->config->item('picture_path').$delivery_id.'_sign.jpg')){
        //if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'_sign.jpg')){
            $sthumbnail = base_url().'public/receiver/'.$delivery_id.'_sign.jpg';
            $thumbnail .= sprintf('<img style="cursor:pointer;width:100px;height:auto;" class="sign '.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" />',$sthumbnail);
        //}
        $has_sign = true;
    }

    if($has_sign){
        $gal = '<br />'.($pidx - 1).' pics & 1 signature';
    }else{
        $gal = '<br />'.$pidx.' pics, no signature';
    }

    if($pidx > 0){
        for($g = 0; $g < $pidx; $g++){
            $img = str_replace($CI->config->item('picture_path'), '', $existingpic[$g]);
            $gal .= '<input type="hidden" class="gal_'.$delivery_id.'" value="'.$img.'" >';
        }
    }

    $thumbnail = $thumbnail.$gal;

	return $thumbnail;
}
//pickup thumbnail
function get_puthumbnail($delivery_id, $class = 'thumb'){
    $CI =& get_instance();

    $existingpic = glob($CI->config->item('pickuppic_path').$delivery_id.'*.jpg');

    $pictures = array();

    if(file_exists(FCPATH.'public/pickup/'.$delivery_id.'_address.jpg')){
        $pictures[] = '<img src="'.base_url().'public/pickup/'.$delivery_id.'_address.jpg" style="width:100px;height:auto">';
    }

    if(file_exists(FCPATH.'public/pickup/'.$delivery_id.'_address.jpg')){
        $pictures[] = '<img src="'.base_url().'public/pickup/'.$delivery_id.'_1.jpg" style="width:100px;height:auto">';
    }

    if(file_exists(FCPATH.'public/pickup/'.$delivery_id.'_2.jpg')){
        $pictures[] = '<img src="'.base_url().'public/pickup/'.$delivery_id.'_2.jpg" style="width:100px;height:auto">';
    }

    if(file_exists(FCPATH.'public/pickup/'.$delivery_id.'_3.jpg')){
        $pictures[] = '<img src="'.base_url().'public/pickup/'.$delivery_id.'_3.jpg" style="width:100px;height:auto">';
    }

    //print_r($existingpic);

    $pidx = count($pictures);

    if($pidx > 0){

        for($g = 0; $g < $pidx; $g++){
            $img = str_replace($CI->config->item('picture_path'), '', $existingpic[$g]);
            $gal .= '<input type="hidden" class="gal_'.$delivery_id.'" value="'.$img.'" >';
        }

    }else{
        $thumbnail = base_url().'assets/images/th_nopic.jpg';
        $thumbnail = sprintf('<img style="cursor:pointer;" class="'.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" /><br /><span class="rotate" id="r_'.$delivery_id.'" style="cursor:pointer;"  >rotate CW</span>',$thumbnail);
    }

    $gal = '';
    /*
    $has_sign = false;

    if(file_exists($CI->config->item('picture_path').$delivery_id.'_sign.jpg')){
        //if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'_sign.jpg')){
            $sthumbnail = base_url().'public/receiver/'.$delivery_id.'_sign.jpg';
            $thumbnail .= sprintf('<img style="cursor:pointer;width:100px;height:auto;" class="sign '.$class.'" alt="'.$delivery_id.'" src="%s?'.time().'" />',$sthumbnail);
        //}
        $has_sign = true;
    }

    if($has_sign){
        $gal = '<br />'.($pidx - 1).' pics & 1 signature';
    }else{
        $gal = '<br />'.$pidx.' pics, no signature';
    }
    */

    $thumbnail = $thumbnail.$gal;

    return $thumbnail;
}

function generate_multithumb($pics, $out){
    $width = 100;
    $height = 75;

}

function generate_thumbnail($delivery_id){
    $CI =& get_instance();
    $un = true;

    $target_path = $CI->config->item('picture_path').$delivery_id.'.jpg';

    if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg')){
        $un = unlink($CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg');
    }

    if($un){
        $config['image_library'] = 'gd2';
        $config['source_image'] = $target_path;
        $config['new_image'] = $CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg';
        $config['create_thumb'] = false;
        $config['maintain_ratio'] = TRUE;
        $config['width']     = 100;
        $config['height']   = 75;

        $CI->load->library('image_lib', $config);

        $CI->image_lib->resize();

        if(file_exists($CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg')){
            return $CI->config->item('thumbnail_path').'th_'.$delivery_id.'.jpg';
        }else{
            return false;
        }

    }else{
        return false;
    }

}

function delivery_log($data,$upsert = false){
	$CI =& get_instance();
	if($upsert == true){
		$CI->db->where('sync_id = ',$data['sync_id']);
		$CI->db->where('device_id = ',$data['device_id']);
		$CI->db->where('timestamp = ',$data['timestamp']);
		$CI->db->from($CI->config->item('delivery_log_table'));

		$in = $CI->db->count_all_results();

		//print $in;

		if($in <= 0){
			$CI->db->insert($CI->config->item('delivery_log_table'),$data);
		}

	}else{
		$CI->db->insert($CI->config->item('delivery_log_table'),$data);
	}
	return true;
}

function access_log($data){
	$CI =& get_instance();
	$CI->db->insert($CI->config->item('access_log_table'),$data);
	return true;
}

/**
 * Returns an array of latitude and longitude from the Image file
 * @param image $file
 * @return multitype:number |boolean
 */
function read_gps_location($file){
    if (is_file($file)) {
        $info = exif_read_data($file);
        if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
            isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
            in_array($info['GPSLatitudeRef'], array('E','W','N','S')) && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))) {

            $GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
            $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

            $lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
            $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
            $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
            $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
            $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
            $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

            $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
            $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
            $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
            $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
            $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
            $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

            $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
            $lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

            //If the latitude is South, make it negative.
            //If the longitude is west, make it negative
            $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
            $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

            return array(
                'latitude' => $lat,
                'longitude' => $lng,
                'photolatitude' => $lat,
                'photolongitude' => $lng,
            );
        }
    }
    return false;
}

function full_reschedule($delivery_id, $datachanged){
	$CI =& get_instance();
	$old_order = $CI->db->where('delivery_id',$delivery_id)->get($CI->config->item('assigned_delivery_table'));
	$old_order = $old_order->row_array();

	if(empty($datachanged) || is_null($datachanged)){
        $new_order = $old_order;
    }else{
        $new_order = array_replace($old_order,$datachanged);
    }

	$new_order['status'] = $CI->config->item('trans_status_new');
	$new_order['reschedule_ref'] = $delivery_id;
	$new_order['reattemp'] = (int) $new_order['reattemp'] + 1;
	unset($new_order['delivery_id']);
	unset($new_order['id']);

	$old_delivery_id = $delivery_id;

	$CI->db->insert($CI->config->item('assigned_delivery_table'),$new_order);

	$sequence = $CI->db->insert_id();

	$year_count = str_pad($sequence, 10, '0', STR_PAD_LEFT);
	$merchant_id = str_pad($new_order['merchant_id'], 8, '0', STR_PAD_LEFT);
	$delivery_id = $merchant_id.'-'.date('d-mY',time()).'-'.$year_count;

	$CI->db->where('id',$sequence)->update($CI->config->item('assigned_delivery_table'),array('delivery_id'=>$delivery_id));

	$old_details = $CI->db->where('delivery_id',$old_delivery_id)->get($CI->config->item('delivery_details_table'));

	foreach ($old_details->result_array() as $detail){
        unset($detail['id']);
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

	if(is_null($data) || $data == ''){
		$data['type'] = 'notification';
	}{
        if(is_array($data)){
            $data['type'] = 'notification';
        }
    }

	if(!is_null($reply_to) && $reply_to != ''){
		$CI->email->reply_to($reply_to);
	}

	if(is_array($to)){
		/*
        foreach($to as $em){
			$CI->email->to($em);
		}
        */
        $CI->email->to($to);
		$log['to'] = implode(';',$to);
	}else{

		$CI->email->to($to);
		$log['to'] = $to;
	}

	if(!is_null($cc) && $cc != ''){
        if(is_array($cc)){
			/*
            foreach ($cc as $cm) {
				$CI->email->cc($cm);
			}
            */
            $cc[] = 'admin@jayonexpress.com';
            $CI->email->cc($cc);
			$log['cc'] = implode(';',$cc);
		}else{
            $cc .= ',admin@jayonexpress.com';
        	$CI->email->cc($cc);
			$log['cc'] = $cc;
		}
	}else{

        $CI->email->cc('admin@jayonexpress.com');
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

	//$CI->email->cc('admin@jayonexpress.com');
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

    $CI->email->clear(true); // clear data AND attchments before sending another email

    $debug = $CI->email->print_debugger();

    //print $debug;

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

function colorizestatus($status, $prefix = '', $suffix = ''){

	$colors = config_item('status_colors');
	if($status == '' || !in_array($status, array_keys($colors))){
		$class = 'brown';
		$status = 'N/A';
	}else{
		$class = $colors[$status];
	}

    $atatus = str_replace('_', ' ', $status);
    $status = $prefix.ucwords($status).$suffix;

	return sprintf('<span class="%s">%s</span>',$class,$status);
}

function colorizelatlon($lat, $lon, $field = 'lat', $id = 0){
    $CI =& get_instance();

    //$d = distance( $CI->config->item('origin_lat'), $CI->config->item('origin_lon'), $lat, $lon, 'K' );
    $d = 0;
    $loc_set = true;
    if($lat == 'Set Loc'){
        $loc_set = false;
    }else{
        $d = vincentyGreatCircleDistance( $CI->config->item('origin_lat'), $CI->config->item('origin_lon'), $lat, $lon );
    }

    //print $d;

    if($d < 1000 && $loc_set == true){

        if($field == 'lat'){
            return sprintf('<span id="%s" class="locpick %s">%s</span>',$id,'textred',$lat);
        }elseif ($field == 'lon') {
            return sprintf('<span id="%s" class="locpick %s">%s</span>',$id,'textred',$lon);
        }else{
            return sprintf('<span id="%s" class="locpick %s">%s</span>',$id,'textred',$lat.','.$lon);
        }
    }else{
        if($field == 'lat'){
            return $lat;
        }elseif ($field == 'lon') {
            return $lon;
        }else{
            return $lat.','.$lon;
        }

    }

}

function colorizetype($type, $prefix = '', $suffix = ''){

	if($type == 'COD'){
		$class = 'brown';
	}else if($type == 'CCOD'){
		$class = 'maroon';
	}else if($type == 'PS'){
		$class = 'green';
	}else{
		$class = 'red';
		$type = 'DO';
	}

    $type = $prefix.$type.$suffix;

    return sprintf('<span class="%s" style="text-align:center;">%s</span>',$class,$type);
}

function hide_trx($trx_id){
    if(preg_match('/^TRX_/', $trx_id) || preg_match('/^UP_/', $trx_id)){
        return '';
    }else{
        return $trx_id;
    }
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

function getmonthlydatacounttypearray($year,$month,$where = null,$merchant_id = null,$span = 'full'){
	$CI =& get_instance();

	$series = array();
	$data = array();
	$now = date('d',time());
	$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$start = 1;

	if($span == 'half'){
		if($now < 15){
			$num = $num/2;
			$start = 1;
		}else{
			$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$start = 15;
		}
	}

	for($i = $start ; $i <= $num;$i++){

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

		// get cod

		$CI->db->like('ordertime', $date, 'after');

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}

		$CI->db->where('delivery_type','COD');
		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$countcod = $CI->db->count_all_results();

		//print $CI->db->last_query();

		//$timestamp = strtotime($date);
		//$timestamp = (double)$timestamp;
		$series[] = array($day,$countcod,$count - $countcod);
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

		// total count
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

		// COD count
		$CI->db->like($column,$date,'after');
		$CI->db->where($column.' != ','0000-00-00');

		//$CI->db->like('ordertime', $date, 'after');

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}

		$CI->db->where('delivery_type','COD');

		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$countcod = $CI->db->count_all_results();


		// CCOD count
		$CI->db->like($column,$date,'after');
		$CI->db->where($column.' != ','0000-00-00');

		//$CI->db->like('ordertime', $date, 'after');

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}


		$CI->db->where('delivery_type','CCOD');

		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$countccod = $CI->db->count_all_results();


		// PS count
		$CI->db->like($column,$date,'after');
		$CI->db->where($column.' != ','0000-00-00');

		//$CI->db->like('ordertime', $date, 'after');

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}


		$CI->db->where('delivery_type','PS');

		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$countps = $CI->db->count_all_results();

		// DO count
		$CI->db->like($column,$date,'after');
		$CI->db->where($column.' != ','0000-00-00');

		//$CI->db->like('ordertime', $date, 'after');

		if(!is_null($where)){
			$CI->db->where($where);
		}

		if(!is_null($merchant_id)){
			$CI->db->where('merchant_id', $merchant_id);
		}

		$CI->db->where('delivery_type','Delivery Only');

		$CI->db->from($CI->config->item('incoming_delivery_table'));

		$countdo = $CI->db->count_all_results();

		//print $CI->db->last_query();

		//$timestamp = strtotime($date);
		//$timestamp = (double)$timestamp;
		$series[] = array($date,$countcod,$countccod,$countps,$countdo);
	}

	//$series = str_replace('"', '', json_encode($series)) ;
	return $series;
}

function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {
    $lat1 = (int) $lat1;
    $lon1 = (int) $lon1;
    $lat2 = (int) $lat2;
    $lon2 = (int) $lon2;
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
    if ($unit == 'K') {
        return ($miles * 1.609344);
    } else if ($unit == 'N') {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function vincentyGreatCircleDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}


?>