<?php
class Ajaxpos extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

    public function mapsearch($type, $buyer_id){

        $keyword = $this->input->post('search');

        if($type == 'buyer'){
            $table = $this->config->item('jayon_buyers_table');
        }else{
            $table = $this->config->item('incoming_delivery_table');
        }

        $this->db->where('id',$buyer_id);
        $this->db->select('id,buyer_name,delivery_id,buyerdeliveryzone,buyerdeliverycity,shipping_address,recipient_name,shipping_zip,directions,dir_lat ,dir_lon ,latitude ,longitude');
        $buyer = $this->db->get($table)->row_array();


        if($keyword != ''){
            $suggestql = 'SELECT shipping_address, buyer_name ,latitude, longitude, delivery_id
                FROM  delivery_order_active
                WHERE (
                    (
                        STRCMP( SUBSTRING( SOUNDEX(  shipping_address ) , 1, 20 ) , SUBSTRING( SOUNDEX( ? ) , 1, 20 ) ) =0
                        OR  STRCMP( SUBSTRING( SOUNDEX(  shipping_address ) , 1, 23 ) , SUBSTRING( SOUNDEX( ? ) , 1, 23 ) ) = 0
                    )
                )
                OR shipping_address LIKE ?
                AND buyerdeliverycity = ?
                AND buyerdeliveryzone = ?
                AND delivery_id != ?
                AND latitude !=0 AND longitude !=0';

                $suggestquery = $this->db->query( $suggestql, array($buyer['shipping_address'],$buyer['shipping_address'],'%'.$keyword.'%',$buyer['buyerdeliverycity'],$buyer['buyerdeliveryzone'],$buyer['delivery_id']) );

        }else{

            $suggestql = 'SELECT SUBSTRING( SOUNDEX( shipping_address ) , 1, 20 ) ,  shipping_address, buyer_name ,latitude, longitude,delivery_id
                FROM  delivery_order_active
                WHERE ( STRCMP( SUBSTRING( SOUNDEX(  shipping_address ) , 1, 20 ) , SUBSTRING( SOUNDEX( ? ) , 1, 20 ) ) =0
                OR  STRCMP( SUBSTRING( SOUNDEX(  shipping_address ) , 1, 23 ) , SUBSTRING( SOUNDEX( ? ) , 1, 23 ) ) = 0 )
                AND buyerdeliverycity = ?
                AND buyerdeliveryzone = ?
                AND delivery_id != ?
                AND latitude !=0 AND longitude !=0';

                $suggestquery = $this->db->query( $suggestql, array($buyer['shipping_address'],$buyer['shipping_address'],$buyer['buyerdeliverycity'],$buyer['buyerdeliveryzone'],$buyer['delivery_id']) );

        }

        $last_query = $this->db->last_query();

        $suggestions = $suggestquery->result_array();

                    $l = '';
                    foreach ($suggestions as $val){
                        $l .= '<li>';
                        $l .= $val['buyer_name'].'<br />';
                        $l .= 'delivery id : '.$val['delivery_id'].'<br />';
                        $l .= '<i>'.$val['shipping_address'].'</i><br />';
                        $l .= '<b>'.$val['latitude'].','.$val['longitude'].'</b>';
                        $l .= '<span class="use-loc" data-lat="'.$val['latitude'].'" data-lon="'.$val['longitude'].'" >use</span>';
                        $l .= '</li>';
                    }

        print json_encode(array('result'=>'OK','data'=>$l, 'q'=>$last_query));

    }


	public function getmapmarker(){

		$device_name = $this->input->post('device_identifier');
		$timestamp = $this->input->post('timestamp');
		$courier = $this->input->post('courier');
		$status = $this->input->post('status');

		$device_name = ($device_name == 'Search device')?'':$device_name;
		$timestamp = ($timestamp == 'Search timestamp')?'':$timestamp;
		$courier = ($courier == 'Search courier')?'':$courier;
		$status = ($status == 'Search status')?'':$status;

		$this->db->distinct();
		$this->db->select('identifier');

		if($device_name != ''){
			$this->db->like('identifier',$device_name);
		}

		$devices = $this->db->get($this->config->item('location_log_table'))
			->result();

		$locations = array();

		$paths = array();

		$pathdummy = array();

		foreach($devices as $d){

			$mapcolor = get_device_color($d->identifier);

			$this->db
				->select('id,identifier,timestamp,latitude as lat,longitude as lng,status')
				->where('identifier',$d->identifier);

			if($timestamp == ''){
				$this->db->like('timestamp',date('Y-m-d',time()),'after');
			}else{
				$this->db->like('timestamp',$timestamp,'after');
			}

			if($status != ''){
				$this->db->like('status',$status,'after');
			}

				//->like('timestamp','2012-09-03','after')
				//->limit(10,0)
			$loc = $this->db
				->order_by('timestamp','desc')
				->get($this->config->item('location_log_table'));

			if($loc->num_rows() > 0){
				$path = array();
				$loc = $loc->result();
				foreach($loc as $l){
					$lat = (double)$l->lat;
					$lng = (double)$l->lng;

					if($lat != 0 && $lng != 0){
						$locations[] = array(
							'data'=>array(
                                    'id'=>$l->id,
									'lat'=>$lat,
									'lng'=>$lng,
									'timestamp'=>$l->timestamp,
									'identifier'=>$l->identifier,
									'status'=>$l->status
								)
							);
						$path[] = array(
								$lat,
								$lng
							);
						$pathdummy[] = array(
								$l->identifier,
								$l->timestamp,
								$lat,
								$lng
							);
					}
				}
				$paths[]=array('color'=>$mapcolor,'poly'=>$path);
			}
		}

		print json_encode(array('result'=>'ok','locations'=>$locations,'paths'=>$paths, 'pathdummy'=>$pathdummy, 'q'=>$this->db->last_query() ));

	}

    public function getroutemarker(){

        $device_name = $this->input->post('device_identifier');
        $timestamp = $this->input->post('timestamp');
        $address = $this->input->post('address');
        $limit_count = $this->input->post('limit');
        $delivery_status = $this->input->post('delivery_status');
        $limit_offset = 0;

        $device_name = ($device_name == 'Search device')?'':$device_name;
        $timestamp = ($timestamp == 'Search delivery date')?'':$timestamp;
        $address = ($address == 'Search address')?'':$address;
        $delivery_status = ($delivery_status == 'Search status')?'':$delivery_status;

        $this->db->distinct();
        $this->db->select('identifier');

        $devices = $this->db->get($this->config->item('location_log_table'))
            ->result();

        $locations = array();

        $paths = array();

        $pathdummy = array();

        //get points

        $this->db->select($this->config->item('assigned_delivery_table').'.*,d.identifier as identifier');
        $this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');

        if($device_name != ''){
            $this->db->like('d.identifier',$device_name);
        }

        $loc = $this->db
            ->where('longitude != ',0)->where('latitude != ',0)
            ->like('assignment_date',$timestamp,'after')
            ->like('status',$delivery_status)
            ->limit($limit_count, $limit_offset)
            ->order_by('assignment_date','desc')
            ->order_by('assignment_seq','asc')
            //->order_by($columns[$sort_col],$sort_dir)
            ->get($this->config->item('assigned_delivery_table'));

        $mapcolor = '#FF000';

            if($loc->num_rows() > 0){
                $path = array();
                $loc = $loc->result();
                foreach($loc as $l){
                    $lat = (double)$l->latitude;
                    $lng = (double)$l->longitude;

                    if($lat != 0 && $lng != 0){
                        $locations[] = array(
                            'data'=>array(
                                    'id'=>$l->id,
                                    'lat'=>$lat,
                                    'lng'=>$lng,
                                    'timestamp'=>$l->created,
                                    'identifier'=>$l->buyer_name,
                                    'note'=>$l->delivery_note,
                                    'directions'=>$l->directions,
                                    'address'=>$l->shipping_address
                                )
                            );
                        $path[] = array(
                                $lat,
                                $lng
                            );
                        $pathdummy[] = array(
                                $l->buyer_name,
                                $l->created,
                                $lat,
                                $lng
                            );
                    }
                }
                $paths = array('color'=>$mapcolor,'poly'=>$path);

            }


        print json_encode(array('result'=>'ok','locations'=>$locations,'paths'=>$paths, 'pathdummy'=>$pathdummy, 'q'=>$this->db->last_query() ));

    }

    public function seq(){
        $ids = $this->input->post('ids');
        $seq = $this->input->post('seq');

        $idx = 0;
        foreach($ids as $id){
            $this->db->where('id',$id)
                ->update($this->config->item('incoming_delivery_table'),
                    array('assignment_seq'=>$seq[$idx]));
            $idx++;
        }

        print json_encode(array('result'=>'ok',
            'q'=>$this->db->last_query() ));

    }


    public function getdistmarker(){

        $device_name = $this->input->post('device_identifier');
        $timefrom = $this->input->post('timefrom');
        $timeto = $this->input->post('timeto');
        $courier = $this->input->post('courier');
        $status = $this->input->post('status');

        $device_name = ($device_name == 'Search device')?'':$device_name;
        //        $timestamp = ($timestamp == 'Search timestamp')?'':$timestamp;
        //        $courier = ($courier == 'Search courier')?'':$courier;
        //        $status = ($status == 'Search status')?'':$status;

        $this->db->distinct();
        $this->db->select('identifier');

        if($device_name != ''){
            $this->db->like('identifier',$device_name);
        }

        $devices = $this->db->get($this->config->item('location_log_table'))
            ->result();

        $locations = array();

        $paths = array();

        $pathdummy = array();

        //get points

            $this->db->select('buyer_name,
                created,
                latitude as lat,longitude as lng,
                shipping_address,
                phone, mobile1, mobile2,
                directions, delivery_note')
                ->where('longitude != ',0)->where('latitude != ',0);


            if($timefrom == ''){
                $this->db->like('created',date('Y-m-d',time()),'after');
            }else{
                $column = 'created';
                $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $timefrom, $timeto);

                $this->db->where($daterange, null, false);
                $this->db->where($column.' != ','0000-00-00');
            }

                //->like('timestamp','2012-09-03','after')
                //->limit(10,0)
            $loc = $this->db
                ->order_by('created','desc')
                ->get($this->config->item('jayon_buyers_table'));

            if($loc->num_rows() > 0){
                $path = array();
                $loc = $loc->result();
                foreach($loc as $l){
                    $lat = (double)$l->lat;
                    $lng = (double)$l->lng;

                    if($lat != 0 && $lng != 0){
                        $locations[] = array(
                            'data'=>array(
                                    'lat'=>$lat,
                                    'lng'=>$lng,
                                    'timestamp'=>$l->created,
                                    'identifier'=>$l->buyer_name,
                                    'note'=>$l->delivery_note,
                                    'directions'=>$l->directions,
                                    'address'=>$l->shipping_address
                                )
                            );
                        $path[] = array(
                                $lat,
                                $lng
                            );
                        $pathdummy[] = array(
                                $l->buyer_name,
                                $l->created,
                                $lat,
                                $lng
                            );
                    }
                }
            }


        print json_encode(array('result'=>'ok','locations'=>$locations,'paths'=>$paths, 'pathdummy'=>$pathdummy, 'q'=>$this->db->last_query() ));

    }

	public function ajaxlog(){
		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'timestamp',
			'device_id',
			'identifier',
			'courier_id',
			'latitude',
			'longitude',
			'status',
			'notes'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('location_log_table'));

		$count_display_all = $this->db
			->count_all_results($this->config->item('location_log_table'));

		//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like($this->config->item('location_log_table').'.timestamp',$this->input->post('sSearch_0'));
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('d.identifier',$this->input->post('sSearch_1'));
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('c.courier',$this->input->post('sSearch_2'));
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like($this->config->item('location_log_table').'.status',$this->input->post('sSearch_3'));
		}

		$this->db->select('*,d.identifier as identifier,c.fullname as courier');
		$this->db->join('devices as d',$this->config->item('location_log_table').'.device_id=d.id','left');
		$this->db->join('couriers as c',$this->config->item('location_log_table').'.courier_id=c.id','left');


		$data = $this->db
			->limit($limit_count, $limit_offset)
			->order_by($this->config->item('location_log_table').'.timestamp','desc')
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('location_log_table'));

		//print $this->db->last_query();

		//->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

		$result = $data->result_array();

		$aadata = array();

		foreach($result as $value => $key)
		{

			$aadata[] = array(
				$key['timestamp'],
				$key['identifier'],
				$key['courier'],
				colorizelatlon($key['latitude']),
				colorizelatlon($key['longitude']),
				$key['status']
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

    public function ajaxrouter(){
        $limit_count = $this->input->post('iDisplayLength');
        $limit_offset = $this->input->post('iDisplayStart');

        $sort_col = $this->input->post('iSortCol_0');
        $sort_dir = $this->input->post('sSortDir_0');

        $columns = array(
            'assignment_date',
            'assignment_seq',
            'device_id',
            'shipping_address',
            'latitude',
            'status'
        );

        // get total count result

        //search column
        if($this->input->post('sSearch') != ''){
            $srch = $this->input->post('sSearch');
            //$this->db->like('buyerdeliveryzone',$srch);
            $this->db->or_like('buyerdeliverytime',$srch);
            $this->db->or_like('delivery_id',$srch);
        }

        if($this->input->post('sSearch_0') != ''){
            $this->db->like($this->config->item('assigned_delivery_table').'.assignment_date',$this->input->post('sSearch_0'));
        }

        if($this->input->post('sSearch_1') != ''){
            $this->db->like('d.identifier',$this->input->post('sSearch_1'));
        }


        if($this->input->post('sSearch_2') != ''){
            $this->db->like($this->config->item('assigned_delivery_table').'.shipping_address',$this->input->post('sSearch_2'));
        }

        if($this->input->post('sSearch_3') != ''){
            $this->db->like($this->config->item('assigned_delivery_table').'.status',$this->input->post('sSearch_3'));
        }

        $this->db->select($this->config->item('assigned_delivery_table').'.*,d.identifier as identifier');
        $this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');

        $cdb = clone $this->db;

        $count_all = $cdb->from($this->config->item('assigned_delivery_table'))->count_all_results();

        $this->db
            ->limit($limit_count, $limit_offset)
            ->order_by('assignment_seq','asc')
            ->order_by('assignment_date','desc')
            ->order_by($columns[$sort_col],$sort_dir);

        $cdr = clone $this->db;

        $data = $this->db->get($this->config->item('assigned_delivery_table'));

        $count_display_all = $cdr
            ->from($this->config->item('assigned_delivery_table'))
            ->count_all_results();

        //print $this->db->last_query();

        //->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

        $result = $data->result_array();

        $aadata = array();

        $style = 'style="cursor:pointer;padding:2px;display:block;"';

        foreach($result as $value => $key)
        {

            $lat = ($key['latitude'] == 0)? 'Set Loc':$key['latitude'];
            $lon = ($key['longitude'] == 0)? '':$key['longitude'];

            $class = ($lat == 'Set Loc')?' red':' green';

            $pos = '<span id="'.$key['id'].'" '.$style.' class="locpick'.$class.'">'.$lat.'</span><br />';
            $pos .= '<span id="'.$key['id'].'" '.$style.' class="locpick">'.$lon.'</span>';
            $pos .= '<br /><span class="copyloc orange" style="display:inline-block;cursor:pointer;" id="'.$key['latitude'].'_'.$key['longitude'].'" >Copy</span>';
            $pos .= '&nbsp;&nbsp;<span class="pasteloc green" style="display:inline-block;cursor:pointer;" id="'.$key['id'].'" >Paste</span>';

            $sclass = ($key['status'] == 'delivered')?'green':'orange';

            $aadata[] = array(
                $key['assignment_date'],
                '<input type="text" style="width:25px;" class="inseq" id="'.$key['id'].'" value="'.$key['assignment_seq'].'">',
                $key['identifier'],
                '<b>'.$key['buyer_name'].'</b><br />'.$key['shipping_address'],
                $pos,
                '<span class="'.$sclass.'">'.$key['status'].'</span>'
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

    public function ajaxdistrib(){
        $limit_count = $this->input->post('iDisplayLength');
        $limit_offset = $this->input->post('iDisplayStart');

        $sort_col = $this->input->post('iSortCol_0');
        $sort_dir = $this->input->post('sSortDir_0');

        $columns = array(
            'timestamp',
            'device_id',
            'identifier',
            'courier_id',
            'latitude',
            'longitude',
            'status',
            'notes'
        );

        // get total count result
        $count_all = $this->db->count_all($this->config->item('location_log_table'));

        $count_display_all = $this->db
            ->count_all_results($this->config->item('location_log_table'));

        //search column
        if($this->input->post('sSearch') != ''){
            $srch = $this->input->post('sSearch');
            //$this->db->like('buyerdeliveryzone',$srch);
            $this->db->or_like('buyerdeliverytime',$srch);
            $this->db->or_like('delivery_id',$srch);
        }

        if($this->input->post('sSearch_0') != ''){
            $this->db->like($this->config->item('location_log_table').'.timestamp',$this->input->post('sSearch_0'));
        }


        if($this->input->post('sSearch_1') != ''){
            $this->db->like('d.identifier',$this->input->post('sSearch_1'));
        }

        if($this->input->post('sSearch_2') != ''){
            $this->db->like('c.courier',$this->input->post('sSearch_2'));
        }

        if($this->input->post('sSearch_3') != ''){
            $this->db->like($this->config->item('location_log_table').'.status',$this->input->post('sSearch_3'));
        }

        $this->db->select('*,d.identifier as identifier,c.fullname as courier');
        $this->db->join('devices as d',$this->config->item('location_log_table').'.device_id=d.id','left');
        $this->db->join('couriers as c',$this->config->item('location_log_table').'.courier_id=c.id','left');


        $data = $this->db
            ->limit($limit_count, $limit_offset)
            ->order_by($this->config->item('location_log_table').'.timestamp','desc')
            ->order_by($columns[$sort_col],$sort_dir)
            ->get($this->config->item('location_log_table'));

        //print $this->db->last_query();

        //->group_by(array('buyerdeliverytime','buyerdeliveryzone'))

        $result = $data->result_array();

        $aadata = array();

        foreach($result as $value => $key)
        {

            $aadata[] = array(
                $key['timestamp'],
                $key['identifier'],
                $key['courier'],
                $key['latitude'],
                $key['longitude'],
                $key['status']
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

}

?>