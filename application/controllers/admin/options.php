<?php

class Options extends Application
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
			'key','val'
			);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_options_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_options_table'));

		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			$this->db->like('key',$srch);
			$this->db->or_like('val',$srch);
		}
		
		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_options_table'));
		
		//print $this->db->last_query();
		
		$result = $data->result_array();
			
		$aadata = array();
		
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/options/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/options/edit/".$key['id']."/", "Edit"); // Build actions links
			$aadata[] = array($this->to_label($key['key']),$key['val'],$edit); // Adding row to table
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

		$this->breadcrumb->add_crumb('Options','admin/options/manage');
			
		$data = $this->db->get($this->config->item('jayon_options_table'));
		$result = $data->result_array();
		$this->table->set_heading('Option', 'Value','Actions'); // Setting headings for the table
		
		foreach($result as $value => $key)
		{
			$delete = anchor("admin/options/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/options/edit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row($key['key'],$key['val'],$edit); // Adding row to table
		}

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/options/ajaxmanage';
		$page['page_title'] = 'Manage Options';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}
	
	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_options_table'));
		$page['page_title'] = 'Delete Option';
		$this->ag_auth->view('options/delete_success',$page);
	}

	public function get_options($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_options_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}
	
	public function update_holiday($id,$data){
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_options_table'),$data);
		return $this->db->affected_rows();
	}
	
	
	public function add()
	{
		$this->form_validation->set_rules('holiday', 'Option', 'required|min_length[6]');
		$this->form_validation->set_rules('holidayname', 'Option Name', 'trim|xss_clean');
				
		if($this->form_validation->run() == FALSE)
		{	
			$data['page_title'] = 'Add Option';
			$this->ag_auth->view('options/add',$data);
		}
		else
		{
			$dataset['holiday'] = set_value('holiday');
			$dataset['holidayname'] = set_value('holidayname');
			
			if($this->db->insert($this->config->item('jayon_options_table'),$dataset) === TRUE)
			{
				$data['message'] = "The holiday has now been set.";
				$data['page_title'] = 'Add Option';
				$data['back_url'] = anchor('admin/options/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The option can not be set.";
				$data['page_title'] = 'Add Option Error';
				$data['back_url'] = anchor('admin/options/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('key', 'Option', 'required|min_length[6]');
		$this->form_validation->set_rules('val', 'Option Name', 'trim|xss_clean');
		
		$user = $this->get_options($id);
		$user['keylabel'] = $this->to_label($user['key']);
		$data['user'] = $user;

				
		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Edit Option';
			$this->ag_auth->view('options/edit',$data);
		}
		else
		{
			$dataset['key'] = set_value('key');
			$dataset['val'] = set_value('val');

			if($this->db->where('id',$id)->update($this->config->item('jayon_options_table'),$dataset) == TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$data['message'] = "The option has now updated.";
				$data['page_title'] = 'Edit Option';
				$data['back_url'] = anchor('admin/options/manage','Back to list');
				$this->ag_auth->view('message', $data);
				
			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The option can not be updated.";
				$data['page_title'] = 'Edit Option Error';
				$data['back_url'] = anchor('admin/options/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)
		
	} // public function register()
	
	private function to_label($key){
		return ucwords(str_replace('_',' ',$key));
	}

}

?>