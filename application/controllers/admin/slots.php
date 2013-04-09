<?php

class Slots extends Application
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
		$this->breadcrumb->add_crumb('System','admin/apps/manage');
		
	}

	public function ajaxmanage(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');
		
		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'id', 'seq', 'time_from', 'time_to', 'slot_no', 'is_on'
			);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_timeslots_table'));
		$count_display_all = $this->db->count_all_results($this->config->item('jayon_timeslots_table'));

		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			$this->db->like('district',$srch);
			$this->db->or_like('city',$srch);
			$this->db->or_like('province',$srch);
			$this->db->or_like('country',$srch);
		}
		
		$data = $this->db->limit($limit_count, $limit_offset)
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_timeslots_table'));
		
		//print $this->db->last_query();
		
		$result = $data->result_array();
			
		$aadata = array();
		
		
		foreach($result as $value => $key)
		{

			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links

			$onstatus = ($key['is_on'] == 1)?'On':'Off';
			$colorclass = ($key['is_on'] == 1)?'':' red';

			$onswitch = '<span id="'.$key['id'].'" class="onswitch_link'.$colorclass.'" style="cursor:pointer;text-decoration:underline;">'.$onstatus.'</span>'; // Build actions links
			//$delete = anchor("admin/timeslots/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/slots/edit/".$key['id']."/", "Edit"); // Build actions links
			//$aadata[] = array($key['holiday'],$key['holidayname'],$edit.' '.$delete); // Adding row to table
			$aadata[] = array($key['id'],$key['seq'],$key['slot_no'],$key['time_from'],$key['time_to'],$onswitch,$edit.' '.$delete); // Adding row to table
		}
		
		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);
		
		print json_encode($result);
	}
	
	
	public function manage()
	{
	    $this->load->library('table');		

		$this->breadcrumb->add_crumb('Time Slots','admin/slots/manage');
			
		$this->table->set_heading('ID', 'Sequence','Slot','From','To','Status','Actions'); // Setting headings for the table

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/slots/ajaxmanage';
		$page['add_button'] = array('link'=>'admin/slots/add','label'=>'Add New Slot');
		$page['page_title'] = 'Manage timeslots';
		$this->ag_auth->view('slotajaxlistview',$page); // Load the view
	}

	public function ajaxtoggle()
	{
		$id = $this->input->post('id');
		$toggle = ($this->input->post('switchto') == 'On')?1:0;
		
		$dataset['is_on'] = $toggle;

		if($this->db->where('id',$id)->update($this->config->item('jayon_timeslots_table'),$dataset) == TRUE){
			print json_encode(array('result'=>'ok'));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}

	public function ajaxdelete()
	{
		$id = $this->input->post('id');
		
		if($this->db->where('id', $id)->delete($this->config->item('jayon_timeslots_table'))){
			print json_encode(array('result'=>'ok'));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}

	public function get_zones($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_timeslots_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}
	
	public function update_holiday($id,$data){
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_timeslots_table'),$data);
		return $this->db->affected_rows();
	}
	
	
	public function add()
	{
		//'id', 'seq', 'time_from', 'time_to', 'slot_no', 'is_on'
		$this->breadcrumb->add_crumb('Time Slots','admin/slots/manage');
		$this->breadcrumb->add_crumb('Add','admin/slots/add');

		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('time_from', 'Time From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('time_to', 'Time To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('slot_no', 'Slot No', 'required|trim|xss_clean');
				
		if($this->form_validation->run() == FALSE)
		{	
			$data['page_title'] = 'Add Time Slot';
			$this->ag_auth->view('slots/add',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['time_to'] = set_value('time_to');
			$dataset['time_from'] = set_value('time_from');
			$dataset['slot_no'] = set_value('slot_no');
			$dataset['is_on'] = 1;
			
			if($this->db->insert($this->config->item('jayon_timeslots_table'),$dataset) === TRUE)
			{
				$data['message'] = "The zone has now been set.";
				$data['page_title'] = 'Add Zone';
				$data['back_url'] = anchor('admin/slots/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The zone can not be set.";
				$data['page_title'] = 'Add Zone Error';
				$data['back_url'] = anchor('admin/slots/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->breadcrumb->add_crumb('Time Slots','admin/slots/manage');
		$this->breadcrumb->add_crumb('Add','admin/slots/edit/'.$id);

		$this->form_validation->set_rules('seq', 'Sequence', 'required|trim|xss_clean');
		$this->form_validation->set_rules('time_from', 'Time From', 'required|trim|xss_clean');
		$this->form_validation->set_rules('time_to', 'Time To', 'required|trim|xss_clean');
		$this->form_validation->set_rules('slot_no', 'Slot No', 'required|trim|xss_clean');
		
		$user = $this->get_zones($id);
		$data['user'] = $user;
				
		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Edit Time Slot';
			$this->ag_auth->view('slots/edit',$data);
		}
		else
		{
			$dataset['seq'] = set_value('seq');
			$dataset['time_to'] = set_value('time_to');
			$dataset['time_from'] = set_value('time_from');
			$dataset['slot_no'] = set_value('slot_no');
			
			if($this->db->where('id',$id)->update($this->config->item('jayon_timeslots_table'),$dataset) == TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$data['message'] = "The zone has now updated.";
				$data['page_title'] = 'Edit Zone';
				$data['back_url'] = anchor('admin/slots/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The zone can not be updated.";
				$data['page_title'] = 'Edit Zone Error';
				$data['back_url'] = anchor('admin/slots/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

}

?>