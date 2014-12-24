<?php

class Holidays extends Application
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
			'holiday','holidayname'
			);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_holidays_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_holidays_table'));

		$data = $this->db->limit($limit_count, $limit_offset)->order_by($columns[$sort_col],$sort_dir)->get($this->config->item('jayon_holidays_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = anchor("admin/holidays/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/holidays/edit/".$key['id']."/", "Edit"); // Build actions links
			$aadata[] = array($key['holiday'],$key['holidayname'],$edit.' '.$delete); // Adding row to table
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

		$this->breadcrumb->add_crumb('Holidays','admin/holidays/manage');

		$data = $this->db->get($this->config->item('jayon_holidays_table'));
		$result = $data->result_array();
		$this->table->set_heading('Holiday Date', 'Holiday Name','Actions'); // Setting headings for the table

		foreach($result as $value => $key)
		{
			$delete = anchor("admin/holidays/delete/".$key['id']."/", "Delete"); // Build actions links
			$edit = anchor("admin/holidays/edit/".$key['id']."/", "Edit"); // Build actions links
			$this->table->add_row($key['holiday'],$key['holidayname'],$edit.' '.$delete); // Adding row to table
		}
        $pd = get_print_default();

        if($pd){
            $page['resolution'] = $pd['res'];
            $page['cell_width'] = $pd['cell_width'];
            $page['cell_height'] = $pd['cell_height'];
            $page['columns'] = $pd['col'];
            $page['margin_right'] = $pd['mright'];
            $page['margin_bottom'] = $pd['mbottom'];
            $page['font_size'] = $pd['fsize'];
            $page['code_type'] = $pd['codetype'];
        }else{
            $page['resolution'] = 150;
            $page['cell_width'] = 480;
            $page['cell_height'] = 245;
            $page['columns'] = 2;
            $page['margin_right'] = 18;
            $page['margin_bottom'] = 10;
            $page['font_size'] = 12;
            $page['code_type'] = 'barcode';
        }


		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/holidays/ajaxmanage';
		$page['add_button'] = array('link'=>'admin/holidays/add','label'=>'Add New Holiday');
		$page['page_title'] = 'Manage Holidays';
		$this->ag_auth->view('ajaxlistview',$page); // Load the view
	}

	public function delete($id)
	{
		$this->db->where('id', $id)->delete($this->config->item('jayon_holidays_table'));
		$page['page_title'] = 'Delete Holiday';
		$this->ag_auth->view('holidays/delete_success',$page);
	}

	public function get_holidays($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_holidays_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}

	public function update_holiday($id,$data){
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_holidays_table'),$data);
		return $this->db->affected_rows();
	}


	public function add()
	{
		$this->form_validation->set_rules('holiday', 'Holiday', 'required|min_length[6]');
		$this->form_validation->set_rules('holidayname', 'Holiday Name', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$data['page_title'] = 'Add Holiday';
			$this->ag_auth->view('holidays/add',$data);
		}
		else
		{
			$dataset['holiday'] = set_value('holiday');
			$dataset['holidayname'] = set_value('holidayname');

			if($this->db->insert($this->config->item('jayon_holidays_table'),$dataset) === TRUE)
			{
				$data['message'] = "The holiday date has now been set.";
				$data['page_title'] = 'Add Holiday';
				$data['back_url'] = anchor('admin/holidays/manage','Back to list');
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The holiday date can not be set.";
				$data['page_title'] = 'Add Holiday Error';
				$data['back_url'] = anchor('admin/holidays/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

	public function edit($id)
	{
		$this->form_validation->set_rules('holiday', 'Holiday', 'required|min_length[6]');
		$this->form_validation->set_rules('holidayname', 'Holiday Name', 'trim|xss_clean');

		$user = $this->get_holidays($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(group_id('courier')=>group_desc('courier'));
			$data['page_title'] = 'Edit Holiday';
			$this->ag_auth->view('holidays/edit',$data);
		}
		else
		{
			$dataset['holiday'] = set_value('holiday');
			$dataset['holidayname'] = set_value('holidayname');

			if($this->db->where('id',$id)->update($this->config->item('jayon_holidays_table'),$dataset) == TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$data['message'] = "The holiday date has now updated.";
				$data['page_title'] = 'Edit Holiday';
				$data['back_url'] = anchor('admin/holidays/manage','Back to list');
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The holiday date can not be updated.";
				$data['page_title'] = 'Edit Holiday Error';
				$data['back_url'] = anchor('admin/holidays/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

}

?>