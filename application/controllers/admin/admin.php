<?php

class Admin extends Application
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if(logged_in())
		{
			$this->breadcrumb->add_crumb('Home','admin/dashboard');
			$this->breadcrumb->add_crumb('Dashboard','admin/dashboard');

			$year = date('Y',time());
			$month = date('m',time());

			$devices = $this->db->distinct()
				->select('identifier')
				->get($this->config->item('location_log_table'))
				->result();

			$locations = array();

			foreach($devices as $d){
				$loc = $this->db
					->select('identifier,timestamp,latitude as lat,longitude as lng')
					->where('identifier',$d->identifier)
					->like('timestamp',date('Y-m-d',time()),'after')
					->limit(1,0)
					->order_by('timestamp','desc')
					->get($this->config->item('location_log_table'));

				if($loc->num_rows() > 0){
					$loc = $loc->row();

					$locations[] = array(
						'lat'=>(double)$loc->lat,
						'lng'=>(double)$loc->lng,
						'data'=>array(
								'timestamp'=>$loc->timestamp,
								'identifier'=>$loc->identifier
							)
						);
				}
			}

			$page['locdata'] = json_encode($locations);


			$page['period'] = ' - '.date('M Y',time());
			$page['page_title'] = 'Dashboard';
			$this->ag_auth->view('dashboard',$page);
		}
		else
		{
			$this->login();
		}
	}

	public function uichanges(){
		$last = $this->input->post('lastupdate');
		$last = date('Y-m-d H:i:s',abs($last));
		$total_changed = $this->db->where('created > ', $last)
			->count_all_results($this->config->item('incoming_delivery_table'));

		print json_encode(array('total_changed'=>$total_changed,'query'=>$this->db->last_query() ));
	}

	public function testmail(){
		$subject = 'Processed order';
		$to = 'andy.awidarto@gmail.com';
		$template = '';
		$data = '';
		if(send_notification($subject,$to,$template,$data)){
			print "notification sent";
		}else{
			print "failed to send notification";
		}
	}

    public function geoinsert(){

        set_time_limit(0);

        $delis = $this->db->where('status','delivered')
            ->select('delivery_id,latitude,longitude')
            ->get($this->config->item('delivery_log_table'));

        $delis = $delis->result();

        foreach ($delis as $o) {
            $geodata = array(
                'latitude'=>$o->latitude,
                'longitude'=>$o->longitude,
                'dir_lat'=>$o->latitude,
                'dir_lon'=>$o->longitude
                );

            $this->db->where('delivery_id',$o->delivery_id)
                ->update($this->config->item('jayon_buyers_table'),$geodata);
        }

    }

    public function geopic(){

        set_time_limit(0);

        $delis = $this->db
            ->where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_delivered'))
            ->or_where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_revoked'))
            ->or_where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_noshow'))
            ->select('delivery_id,latitude,longitude')
            ->get($this->config->item('delivered_delivery_table'));

        $delis = $delis->result();

        foreach ($delis as $o) {

            if(file_exists($this->config->item('picture_path').$o->delivery_id.'.jpg')){
                $exifdata = exif_read_data($CI->config->item('picture_path').$o->delivery_id.'.jpg');
                print_r($exifdata);
            }

            /*
            $geodata = array(
                'latitude'=>$o->latitude,
                'longitude'=>$o->longitude,
                'dir_lat'=>$o->latitude,
                'dir_lon'=>$o->longitude
                );
            */
            //$this->db->where('delivery_id',$o->delivery_id)
            //    ->update($this->config->item('jayon_buyers_table'),$geodata);
        }

    }

	public function monthlygraph($status = null){
		$this->load->library('plot');
		$lineplot = $this->plot->plot(500,130);

		$year = date('Y',time());
		$month = date('m',time());

		if(is_null($status)){
			$status = null;
		}else{
			$status = array('status'=>$status);
		}
		$series = getmonthlydatacountarray($year,$month,$status,null);
		//$series = getmonthlydatacountarray($year,$month,$status,null);

		$lineplot->SetPlotType('bars');
		$lineplot->setShading(0);
		$lineplot->SetDataValues($series);

		$lineplot->SetYDataLabelPos('plotin');

		# With Y data labels, we don't need Y ticks or their labels, so turn them off.
		//$lineplot->SetYTickLabelPos('none');
		//$lineplot->SetYTickPos('none');

		$lineplot->SetYTickIncrement(1);
		$lineplot->SetPrecisionY(0);

		//Turn off X axis ticks and labels because they get in the way:
		$lineplot->SetXTickLabelPos('none');
		$lineplot->SetXTickPos('none');

		//Draw it
		$lineplot->DrawGraph();
	}

	public function resetpass(){

		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');

		if($this->form_validation->run() == FALSE)
		{
			$this->ag_auth->view('resetpass');
		}
		else
		{
			$email = set_value('email');
			if($buyer = $this->check_email($email)){
				$password = random_string('alnum', 8);
				$dataset['password'] = $this->ag_auth->salt($password);
				$this->db->where('email',$email)->update($this->config->item('auth_user_table'),$dataset);

				$edata['fullname'] = $buyer->fullname;
				$edata['password'] = $password;
				$subject = 'Password reset request at Jayon Express.';
				send_notification($subject,$email,null,null,'resetpassd',$edata);
				$this->oi->add_success('New password has been sent to your email.');

			}else{
				$this->oi->add_error('Your email can not be found, please consider registering as new member.');
			}

			redirect('resetpass');
		}

	}

	public function changepass()
	{
		$this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'min_length[6]|matches[password]');

		$id = $this->session->userdata('userid');
		$user = $this->get_user($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Change Password';
			$this->ag_auth->view('editpass',$data);
		}
		else
		{
			$result = TRUE;

			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			if( $result = $this->update_user($id,$dataset))
			{
				$this->oi->add_success('Your password is now updated');
				redirect('admin/dashboard');

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$this->oi->add_error('Your password can not be changed.');
				redirect('admin/dashboard');
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	private function check_email($email){
		$em = $this->db->where('email',$email)->get($this->config->item('auth_user_table'));
		if($em->num_rows() > 0){
			return $em->row_array();
		}else{
			return false;
		}
	}

	private function get_user($id){
		$result = $this->db->where('id', $id)->get($this->ag_auth->config['auth_user_table']);
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}

	private function get_group(){
		$this->db->select('id,description');
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['description'];
		}
		return $res;
	}

	private function update_user($id,$data){
		$result = $this->db->where('id', $id)->update($this->ag_auth->config['auth_user_table'],$data);
		return $result;
	}

}

/* End of file: dashboard.php */
/* Location: application/controllers/admin/dashboard.php */