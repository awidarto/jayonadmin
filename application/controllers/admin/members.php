<?php

class Members extends Application
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
		$this->breadcrumb->add_crumb('Users','admin/users/manage');

	}

	public function manage()
	{

		$this->breadcrumb->add_crumb('Members','admin/members/manage');

		$this->load->library('table');

		$this->table->set_heading(
			'Username',
			'Email',
			'Full Name',
			//'Merchant Name',
			//'Bank Account',
			'Street',
			'District',
			'City',
			'Province',
			'Country',
			'ZIP',
			'Mobile',
			'Phone',
			'Group',
			'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_username" id="search_username" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search zone" class="search_init" />',
			'<input type="text" name="search_fullname" value="Search full name" class="search_init" />',
			'<input type="text" name="search_street" value="Search street" class="search_init" />',
			'<input type="text" name="search_district" value="Search district" class="search_init" />',
			'<input type="text" name="search_city" value="Search city" class="search_init" />',
			'<input type="text" name="search_province" value="Search province" class="search_init" />',
			'<input type="text" name="search_country" value="Search country" class="search_init" />',
			'<input type="text" name="search_zip" value="Search ZIP" class="search_init" />',
			'<input type="text" name="search_mobile" value="Search mobile" class="search_init" />',
			'<input type="text" name="search_phone" value="Search phone" class="search_init" />',
			'<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/members/ajaxmanage';
		$page['add_button'] = array('link'=>'admin/members/add','label'=>'Add New Member');
		$page['page_title'] = 'Manage Members';
		$this->ag_auth->view('memberajaxlistview',$page); // Load the view
	}

	public function ajaxmanage(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$columns = array(
			'username',
			'email',
			//'password',
			//'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
			'created',
			'groupname',
			'bank',
			'account_number',
			'account_name',
			'group_id',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);

		// get total count result
		$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));

		$this->db->select('*,g.description as groupname');
		$this->db->join('groups as g','members.group_id = g.id','left');

		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('street',$this->input->post('sSearch_3'));
			$search = true;
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('district',$this->input->post('sSearch_4'));
			$search = true;
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('city',$this->input->post('sSearch_5'));
			$search = true;
		}
		if($this->input->post('sSearch_6') != ''){
			$this->db->like('province',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('country',$this->input->post('sSearch_7'));
			$search = true;
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('zip',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('mobile',$this->input->post('sSearch_9'));
			$search = true;
		}

		if($this->input->post('sSearch_10') != ''){
			$this->db->like('phone',$this->input->post('sSearch_10'));
			$search = true;
		}

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('created',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($search){
			//$this->db->and_();
		}


		$data = $this->db
			->limit($limit_count, $limit_offset)
			->order_by($columns[$sort_col],$sort_dir)
			->get($this->config->item('jayon_members_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", $key['username']); // Build detail links

			$aadata[] = array(
				$detail,
			 	$key['email'],
			 	$key['fullname'],
				$key['street'],
				$key['district'],
				$key['city'],
				$key['province'],
				$key['country'],
				$key['zip'],
			 	//$key['merchantname'],
			 	//$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],
			 	$key['mobile'],
			 	$key['phone'],
			 	$key['created'],
			 	$key['groupname'],
			 	$edit.' '.$editpass.' '.$delete
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

	public function merchant()
	{

		$this->breadcrumb->add_crumb('Manage Merchants','admin/members/merchant');

		$this->load->library('table');

		$this->table->set_heading(
			'Username',
			'Email',
			'Full Name',
			'Merchant Name',
			'Bank Account',
			'Street',
			'District',
			'City',
			'Province',
			'Country',
			'ZIP',
			'Mobile',
			'Phone',
			'Group',
			'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_username" id="search_username" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search email" class="search_init" />',
			'<input type="text" name="search_fullname" value="Search full name" class="search_init" />',

            '<input type="text" name="search_merchant_name" value="Search merchant name" class="search_init" />',
            '<input type="text" name="search_bank" value="Search bank" class="search_init" />',

			'<input type="text" name="search_street" value="Search street" class="search_init" />',
			'<input type="text" name="search_district" value="Search district" class="search_init" />',
			'<input type="text" name="search_city" value="Search city" class="search_init" />',
			'<input type="text" name="search_province" value="Search province" class="search_init" />',
			'<input type="text" name="search_country" value="Search country" class="search_init" />',
			'<input type="text" name="search_zip" value="Search ZIP" class="search_init" />',
			'<input type="text" name="search_mobile" value="Search mobile" class="search_init" />',
			'<input type="text" name="search_phone" value="Search phone" class="search_init" />',
            '',
			'<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/members/ajaxmerchant';
		$page['add_button'] = array('link'=>'admin/members/merchant/add','label'=>'Add New Member');
        $page['group_button'] = false;
		$page['page_title'] = 'Manage Merchants';
		$this->ag_auth->view('memberajaxlistview',$page); // Load the view
	}

	public function ajaxmerchant(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$group_id = user_group_id('merchant');
        $pending_group_id = user_group_id('pendingmerchant');

		$columns = array(
			'username',
			'email',
			//'password',
			'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
            'group_id',
			'created',
			'bank',
			'account_number',
			'account_name',
            'groupname',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);

		// get total count result
		//$count_all = $this->db->count_all($this->config->item('jayon_members_table'));

		//$this->db->where('group_id',$group_id)
        //    ->or_where('group_id',$pending_group_id);

		//$count_display_all = $this->db->count_all_results($this->config->item('jayon_members_table'));

		//$this->db->select('*,g.description as groupname');
		//$this->db->join('groups as g','members.group_id = g.id','left');

		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

        if($this->input->post('sSearch_3') != ''){
            $this->db->like('merchantname',$this->input->post('sSearch_3'));
            $search = true;
        }

        if($this->input->post('sSearch_4') != ''){
            $this->db->like('bank',$this->input->post('sSearch_4'));
            $search = true;
        }

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('street',$this->input->post('sSearch_5'));
			$search = true;
		}

		if($this->input->post('sSearch_6') != ''){
			$this->db->like('district',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('city',$this->input->post('sSearch_7'));
			$search = true;
		}
		if($this->input->post('sSearch_8') != ''){
			$this->db->like('province',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('country',$this->input->post('sSearch_9'));
			$search = true;
		}

		if($this->input->post('sSearch_10') != ''){
			$this->db->like('zip',$this->input->post('sSearch_10'));
			$search = true;
		}

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('mobile',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($this->input->post('sSearch_12') != ''){
			$this->db->like('phone',$this->input->post('sSearch_12'));
			$search = true;
		}

		if($this->input->post('sSearch_13') != ''){
			$this->db->like('created',$this->input->post('sSearch_13'));
			$search = true;
		}

		if($search){
			$this->db->and_();
		}

        //$group_ids = array(group_id('merchant'),group_id('pendingmerchant'));
        $this->db->group_start();

		$this->db->where('group_id',$group_id)
            ->or_where('group_id',$pending_group_id);

        $this->db->group_end();

        $dbca = clone $this->db;

        $this->db->order_by('created','desc')
            ->order_by('group_id','desc')
			->order_by($columns[$sort_col],$sort_dir);

        $dbcr = clone $this->db;

        $data = $this->db->limit($limit_count, $limit_offset)->get($this->config->item('jayon_members_table'));

		$last_query = $this->db->last_query();

        $count_all = $dbca->count_all_results($this->config->item('jayon_members_table'));
        $count_display_all = $dbcr->count_all_results($this->config->item('jayon_members_table'));


		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchant/apps/manage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
            $uplogo = anchor("admin/members/logo/".$key['id']."/", "Upload Logo"); // Build actions links
			$edit = anchor("admin/members/merchant/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", '<span id="un_'.$key['id'].'">'.$key['username'].'</span>'); // Build detail links

            $groupname = ($key['group_id'] == group_id('pendingmerchant'))?sprintf('<span class="red">%s</span>',user_group_desc($key['group_id']) ):user_group_desc($key['group_id']);

            $logo = get_logo($key['id']);
            if($logo['exist'] == true){
                $logo = '<img src="'.$logo['logo'].'" />';
            }else{
                $logo = '';
            }

			$aadata[] = array(
				$detail,
			 	$key['email'],
			 	$key['fullname'],
                $logo.'<br />'.$key['merchantname'],
                $key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],
				$key['street'],
				$key['district'],
				$key['city'],
				$key['province'],
				$key['country'],
				$key['zip'],
			 	$key['mobile'],
			 	$key['phone'],
                $groupname,
			 	$key['created'],
			 	$addapp.' '.$edit.' '.$uplogo.' '.$editpass.' '.$delete
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata,
            'q'=>$last_query
		);

		print json_encode($result);
	}

	public function __buyer()
	{

		$this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');

		$this->load->library('table');

		$this->table->set_heading(
			'Username',
			'Email',
			'Full Name',
			//'Merchant Name',
			//'Bank Account',
			'Street',
			'District',
			'City',
			'Province',
			'Country',
			'ZIP',
			'Mobile',
			'Phone',
			//'Group',
			'Created',
			'Actions'); // Setting headings for the table

		$this->table->set_footing(
			'<input type="text" name="search_username" id="search_username" value="Search delivery time" class="search_init" />',
			'<input type="text" name="search_email" id="search_email" value="Search email" class="search_init" />',
			'<input type="text" name="search_fullname" value="Search full name" class="search_init" />',
			'<input type="text" name="search_street" value="Search street" class="search_init" />',
			'<input type="text" name="search_district" value="Search district" class="search_init" />',
			'<input type="text" name="search_city" value="Search city" class="search_init" />',
			'<input type="text" name="search_province" value="Search province" class="search_init" />',
			'<input type="text" name="search_country" value="Search country" class="search_init" />',
			'<input type="text" name="search_zip" value="Search ZIP" class="search_init" />',
			'<input type="text" name="search_mobile" value="Search mobile" class="search_init" />',
			'<input type="text" name="search_phone" value="Search phone" class="search_init" />',
			'<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
			form_button('do_setgroup','Set Group','id="doSetGroup"')
			);

		$page['sortdisable'] = '';
		$page['ajaxurl'] = 'admin/members/ajaxbuyer';
		$page['add_button'] = array('link'=>'admin/members/buyer/add','label'=>'Add New Member');
		$page['page_title'] = 'Manage Buyers';
		$this->ag_auth->view('memberajaxlistview',$page); // Load the view
	}

	public function ajaxbuyer(){

		$limit_count = $this->input->post('iDisplayLength');
		$limit_offset = $this->input->post('iDisplayStart');

		$sort_col = $this->input->post('iSortCol_0');
		$sort_dir = $this->input->post('sSortDir_0');

		$group_id = user_group_id('buyer');

		$columns = array(
			'username',
			'email',
			//'password',
			//'merchantname',
			'fullname',
			'street',
			'district',
			'city',
			'province',
			'country',
			'zip',
			'mobile',
			'phone',
			//'groupname',
			'created',
			'bank',
			'account_number',
			'account_name',
			'group_id',
			'token',
			'identifier',
			'merchant_request',
			'success',
			'fail'
		);


		$search = false;
				//search column
		if($this->input->post('sSearch') != ''){
			$srch = $this->input->post('sSearch');
			//$this->db->like('buyerdeliveryzone',$srch);
			$this->db->or_like('buyerdeliverytime',$srch);
			$this->db->or_like('delivery_id',$srch);
			$search = true;
		}

		if($this->input->post('sSearch_0') != ''){
			$this->db->like('username',$this->input->post('sSearch_0'));
			$search = true;
		}


		if($this->input->post('sSearch_1') != ''){
			$this->db->like('email',$this->input->post('sSearch_1'));
			$search = true;
		}

		if($this->input->post('sSearch_2') != ''){
			$this->db->like('fullname',$this->input->post('sSearch_2'));
			$search = true;
		}

		if($this->input->post('sSearch_3') != ''){
			$this->db->like('street',$this->input->post('sSearch_3'));
			$search = true;
		}

		if($this->input->post('sSearch_4') != ''){
			$this->db->like('district',$this->input->post('sSearch_4'));
			$search = true;
		}

		if($this->input->post('sSearch_5') != ''){
			$this->db->like('city',$this->input->post('sSearch_5'));
			$search = true;
		}
		if($this->input->post('sSearch_6') != ''){
			$this->db->like('province',$this->input->post('sSearch_6'));
			$search = true;
		}

		if($this->input->post('sSearch_7') != ''){
			$this->db->like('country',$this->input->post('sSearch_7'));
			$search = true;
		}

		if($this->input->post('sSearch_8') != ''){
			$this->db->like('zip',$this->input->post('sSearch_8'));
			$search = true;
		}

		if($this->input->post('sSearch_9') != ''){
			$this->db->like('mobile1',$this->input->post('sSearch_9'));
			$search = true;
		}

        if($this->input->post('sSearch_10') != ''){
            $this->db->like('mobile2',$this->input->post('sSearch_10'));
            $search = true;
        }

		if($this->input->post('sSearch_11') != ''){
			$this->db->like('phone',$this->input->post('sSearch_11'));
			$search = true;
		}

		if($this->input->post('sSearch_12') != ''){
			$this->db->like('created',$this->input->post('sSearch_12'));
			$search = true;
		}

		if($search){
			//$this->db->and_();
		}

        $this->db->where('group_id',$group_id)
            ->or_where('group_id',$pending_group_id);

        $dbca = clone $this->db;

        $this->db->limit($limit_count, $limit_offset)
            ->order_by('created','desc')
            ->order_by('group_id','desc')
            ->order_by($columns[$sort_col],$sort_dir);

        $dbcr = clone $this->db;

        $data = $this->db->get($this->config->item('jayon_members_table'));

        //print $this->db->last_query();

        $count_all = $dbca->count_all_results($this->config->item('jayon_members_table'));
        $count_display_all = $dbcr->count_all_results($this->config->item('jayon_members_table'));

		//print $this->db->last_query();

		$result = $data->result_array();

		$aadata = array();


		foreach($result as $value => $key)
		{
			$delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
			$editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
			if($key['group_id'] === group_id('merchant')){
				$addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
			}else{
				$addapp = '&nbsp'; // Build actions links
			}
			$edit = anchor("admin/members/buyer/edit/".$key['id']."/", "Edit"); // Build actions links
			$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", '<span id="un_'.$key['id'].'">'.$key['username'].'</span>'); // Build detail links

			$aadata[] = array(
				$detail,
			 	$key['email'],
			 	$key['fullname'],
				$key['street'],
				$key['district'],
				$key['city'],
				$key['province'],
				$key['country'],
				$key['zip'],
			 	//$key['merchantname'],
			 	//$key['bank'].'<br/>'.$key['account_number'].'<br/>'.$key['account_name'],
			 	$key['mobile'],
			 	$key['phone'],
			 	$key['created'],
			 	$edit.' '.$editpass.' '.$delete
			); // Adding row to table

		}

		$result = array(
			'sEcho'=> $this->input->post('sEcho'),
			'iTotalRecords'=>$count_all,
			'iTotalDisplayRecords'=> $count_display_all,
			'aaData'=>$aadata
		);

		print json_encode($result);
	}

    public function buyer()
    {

        $this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');

        $this->load->library('table');

        $this->table->set_heading(
            'Parent',
            'Child',
            'Buying Freq.',
            'Buyer Name',
            'Address',
            'City',
            'Zone',
            'Phone',
            'Mobile1',
            'Mobile2',
            'Email',
            //'Recipient',
            //'ZIP',
            'Created',
            'Latitude',
            'Longitude',
            'Actions'); // Setting headings for the table

        $this->table->set_footing(
            '',
            '',
            '',
            '<input type="text" name="search_buyer_name" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_shipping_address" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_buyerdeliverycity" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_buyerdeliveryzone" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_phone" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_mobile1" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_mobile2" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_email" id="search_username" value="Search delivery time" class="search_init" />',
            //'<input type="text" name="search_recipient_name" id="search_username" value="Search delivery time" class="search_init" />',
            //'<input type="text" name="search_shipping_zip" id="search_username" value="Search delivery time" class="search_init" />',
            '<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />'
            );

        $page['sortdisable'] = '0,1';
        $page['ajaxurl'] = 'admin/members/ajaxbuyers';
        $page['add_button'] = array('link'=>'admin/members/buyer/add','label'=>'Add New Member');
        $page['group_button'] = true;
        $page['page_title'] = 'Manage Buyers';
        $this->ag_auth->view('memberajaxlistview',$page); // Load the view
    }

    public function ajaxbuyers(){

        $limit_count = $this->input->post('iDisplayLength');
        $limit_offset = $this->input->post('iDisplayStart');

        $sort_col = $this->input->post('iSortCol_0');
        $sort_dir = $this->input->post('sSortDir_0');

        $group_id = user_group_id('buyer');

        $columns = array(
            'group_count'
            ,'buyer_name'
            ,'shipping_address'
            ,'buyerdeliverycity'
            ,'buyerdeliveryzone'
            ,'phone'
            ,'mobile1'
            ,'mobile2'
            ,'email'
            ,'recipient_name'
            ,'shipping_zip'
            ,'created'
            ,'latitude'
            ,'longitude'
            ,'delivery_id'
            ,'delivery_cost'
            ,'cod_cost'
            ,'delivery_type'
            ,'currency'
            ,'total_price'
            ,'chargeable_amount'
            ,'delivery_bearer'
            ,'cod_bearer'
            ,'cod_method'
            ,'ccod_method'
            ,'application_id'
            ,'buyer_id'
            ,'merchant_id'
            ,'merchant_trans_id'
            ,'courier_id'
            ,'device_id'
            ,'directions'
            ,'dir_lat'
            ,'dir_lon'
            ,'delivery_note'
        );

        //$this->db->distinct();
        $this->db->select(
            'id
            ,is_parent,
            ,is_child_of
            ,group_count
            ,cluster_id
            ,shipping_address
            ,buyer_name
            ,buyerdeliverycity
            ,buyerdeliveryzone
            ,phone
            ,mobile1
            ,mobile2
            ,recipient_name
            ,shipping_zip
            ,email
            ,latitude
            ,longitude
            ,created');

        $this->db->from($this->config->item('jayon_buyers_table'));

        $this->db->where('is_child_of',0);

        $dbca = clone $this->db;

        $search = false;

                //search column
        if($this->input->post('sSearch') != ''){
            $srch = $this->input->post('sSearch');
            //$this->db->like('buyerdeliveryzone',$srch);
            //$this->db->or_like('buyerdeliverytime',$srch);
            //$this->db->or_like('delivery_id',$srch);
            $search = true;
        }

        if($this->input->post('sSearch_0') != ''){
            $this->db->like('buyer_name',$this->input->post('sSearch_0'));
            $search = true;
        }

        if($this->input->post('sSearch_1') != ''){
            $this->db->like('shipping_address',$this->input->post('sSearch_1'));
            $search = true;
        }


        if($this->input->post('sSearch_2') != ''){
            $this->db->like('buyerdeliverycity',$this->input->post('sSearch_2'));
            $search = true;
        }

        if($this->input->post('sSearch_3') != ''){
            $this->db->like('buyerdeliveryzone',$this->input->post('sSearch_3'));
            $search = true;
        }

        if($this->input->post('sSearch_4') != ''){
            $this->db->like('phone',$this->input->post('sSearch_4'));
            $search = true;
        }

        if($this->input->post('sSearch_5') != ''){
            $this->db->like('mobile1',$this->input->post('sSearch_5'));
            $search = true;
        }
        if($this->input->post('sSearch_6') != ''){
            $this->db->like('mobile2',$this->input->post('sSearch_6'));
            $search = true;
        }

        if($this->input->post('sSearch_7') != ''){
            $this->db->like('email',$this->input->post('sSearch_7'));
            $search = true;
        }

        if($this->input->post('sSearch_8') != ''){
            $this->db->like('recipient_name',$this->input->post('sSearch_8'));
            $search = true;
        }

        if($this->input->post('sSearch_9') != ''){
            $this->db->like('shipping_zip',$this->input->post('sSearch_9'));
            $search = true;
        }

        if($this->input->post('sSearch_10') != ''){
            $this->db->like('created',$this->input->post('sSearch_10'));
            $search = true;
        }

        if($search){
            //$this->db->and_();
        }

        $this->db->order_by('created','desc');

        $this->db->order_by($columns[$sort_col],$sort_dir);

        $this->db->order_by(
            'shipping_address
            ,buyer_name
            ,buyerdeliverycity
            ,buyerdeliveryzone
            ,phone
            ,mobile1
            ,mobile2','desc'
            );


        $dbcr = clone $this->db;

        $data = $this->db->limit($limit_count, $limit_offset)
            ->get();

        $last_query = $this->db->last_query();

        $result = $data->result_array();

        $count_all = $dbca->count_all_results();
        $count_display_all = $dbcr->count_all_results();


        $aadata = array();


        foreach($result as $value => $key)
        {
            /*
            $delete = '<span id="'.$key['id'].'" class="delete_link" style="cursor:pointer;text-decoration:underline;">Delete</span>'; // Build actions links
            $editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
            if($key['group_id'] === group_id('merchant')){
                $addapp = anchor("admin/members/merchantmanage/".$key['id']."/", "Applications"); // Build actions links
            }else{
                $addapp = '&nbsp'; // Build actions links
            }
            */
            $delete = '';
            $edit = '';
            //$edit = anchor("admin/members/buyer/edit/".$key['id']."/", "Edit"); // Build actions links
            //$detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", '<span id="un_'.$key['id'].'">'.$key['username'].'</span>'); // Build detail links
            if($key['is_parent'] == 1){
                $child_selector = '<span class="view_group" id="'.$key['id'].'" >View cluster</span>';
            }else{
                $child_selector = '<input type="checkbox" name="child_select" class="child_select" id="'.$key['id'].'" value="'.$key['id'].'" />';
            }

            $style = 'style="cursor:pointer;padding:2px;display:block;"';

            $lat = ($key['latitude'] == 0)? 'Set Loc':$key['latitude'];
            $lon = ($key['longitude'] == 0)? '':$key['longitude'];

            $class = ($lat == 'Set Loc')?' red':'';

            $aadata[] = array(
                '<input type="radio" name="parent_check" class="parent_check" id="'.$key['id'].'" value="'.$key['id'].'" />',
                $child_selector,
                $key['group_count'],
                $key['buyer_name'],
                $key['shipping_address'],
                $key['buyerdeliveryzone'],
                $key['buyerdeliverycity'],
                $key['phone'],
                $key['mobile1'],
                $key['mobile2'],
                $key['email'],
                //$key['recipient_name'],
                //$key['shipping_zip'],
                $key['created'],
                '<span id="'.$key['id'].'" '.$style.' class="locpick'.$class.'">'.$lat.'</span>',
                '<span id="'.$key['id'].'" '.$style.' class="locpick">'.$lon.'</span>',
                $edit.' '.$delete
            ); // Adding row to table

        }

        $result = array(
            'sEcho'=> $this->input->post('sEcho'),
            'iTotalRecords'=>$count_all,
            'iTotalDisplayRecords'=> $count_display_all,
            'aaData'=>$aadata,
            'q'=>$last_query
        );

        print json_encode($result);
    }


	function details($id){
		$this->load->library('table');

		$user = $this->get_user($id);

		foreach($user as $key=>$val){
			$this->table->add_row($key,$val); // Adding row to table
		}

		$page['page_title'] = 'Member Info';
		$this->ag_auth->view('members/details',$page);
	}

	public function ajaxsetgroup(){
		$users = $this->input->post('users');
		$setgroup = $this->input->post('set_group');

		if(is_array($users)){
			foreach ($users as $u) {
				$this->db->where('id',$u)->update($this->config->item('jayon_members_table'),array('group_id'=>$setgroup));
				/*
				$data = array(
						'timestamp'=>date('Y-m-d h:i:s',time()),
						'report_timestamp'=>date('Y-m-d h:i:s',time()),
						'delivery_id'=>$d,
						'device_id'=>'',
						'courier_id'=>'',
						'actor_type'=>'AD',
						'actor_id'=>$this->session->userdata('userid'),
						'latitude'=>'',
						'longitude'=>'',
						'status'=>$this->config->item('trans_status_canceled'),
						'req_by' => $req_by,
						'req_name' => $req_name,
						'req_note' => $req_note,
						'notes'=>''
						);

				delivery_log($data);
				*/
			}
		}else{

			$this->db->where('id',$users)->update($this->config->item('jayon_members_table'),array('group_id'=>$setgroup));

			/*
			$data = array(
					'timestamp'=>date('Y-m-d h:i:s',time()),
					'report_timestamp'=>date('Y-m-d h:i:s',time()),
					'delivery_id'=>$delivery_id,
					'device_id'=>'',
					'courier_id'=>'',
					'actor_type'=>'AD',
					'actor_id'=>$this->session->userdata('userid'),
					'latitude'=>'',
					'longitude'=>'',
					'status'=>'change_group',
					'req_by' => $req_by,
					'req_name' => $req_name,
					'req_note' => $req_note,
					'notes'=>''
					);

			delivery_log($data);
			*/

		}

		print json_encode(array('result'=>'ok'));

		//send_notification('Cancelled Orders',$buyeremail,null,'rescheduled_order_buyer',$edata,null);

	}

	public function ajaxdelete()
	{
		$id = $this->input->post('id');

		if($this->db->where('id', $id)->delete($this->config->item('jayon_members_table'))){
			print json_encode(array('result'=>'ok'));
		}else{
			print json_encode(array('result'=>'failed'));
		}
	}

	public function get_user($id){
		$result = $this->db->where('id', $id)->get($this->config->item('jayon_members_table'));
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}

	public function get_group(){
		$this->db->select('id,description');
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['description'];
		}
		return $res;
	}

	public function get_group_description($id){
		$this->db->select('description');
		if(!is_null($id)){
			$this->db->where('id',$id);
		}
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		$row = $result->row();
		return $row->description;
	}

	public function update_user($id,$data){
		$result = $this->db->where('id', $id)->update($this->config->item('jayon_members_table'),$data);
		return $this->db->affected_rows();
	}


	public function add()
	{
		if(in_array('merchant',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Merchants','admin/members/merchant');
			$this->breadcrumb->add_crumb('Add Merchant','admin/members/merchant/add');
			$data['page_title'] = 'Add Merchant';

			$back_url = 'admin/members/merchant';
			$success_url = 'admin/members/merchant';
			$error_url = 'admin/members/merchant/add';

			$utype = 'Merchant';
		}else if(in_array('buyer',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');
			$this->breadcrumb->add_crumb('Add Buyer','admin/members/buyer/add');
			$data['page_title'] = 'Add Buyer';

			$back_url = 'admin/members/buyer';
			$success_url = 'admin/members/buyer';
			$error_url = 'admin/members/buyer/add';

			$utype = 'Buyer';
		}

		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]|callback_field_exists');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'required|min_length[6]|matches[password]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email|callback_field_exists');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('merchantname', 'Merchant Name', 'trim|xss_clean');
		$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean');
		$this->form_validation->set_rules('street', 'Street', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'required|trim|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');
		$this->form_validation->set_rules('zip', 'ZIP', 'required|trim|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');


		$this->form_validation->set_rules('same_as_personal_address', 'Same As Personal Address', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_street', 'Street', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_district', 'District', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_city', 'City', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_country', 'Country', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_zip', 'ZIP', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_phone', 'Phone Number', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_mobile', 'Mobile Number', 'trim|xss_clean');
        $this->form_validation->set_rules('mc_toscan', 'Use barcode scan', 'trim|xss_clean');
        $this->form_validation->set_rules('mc_pickup_time', 'Pick Up Time', 'trim|xss_clean');
        $this->form_validation->set_rules('mc_pickup_cutoff', 'Pick Up Cut Off', 'trim|xss_clean');

		$this->form_validation->set_rules('mc_first_order', 'First Order', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_last_order', 'Last order', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_unlimited_time', 'Mobile Number', 'trim|xss_clean');

        $this->form_validation->set_rules('delivery_bearer', 'Delivery Bearer', 'trim|xss_clean');
        $this->form_validation->set_rules('cod_surcharge_bearer', 'COD Bearer', 'trim|xss_clean');

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
                group_id('pendingmerchant')=>group_desc('pendingmerchant'),
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['back_url'] = anchor($back_url,'Cancel');
			$this->ag_auth->view('members/add',$data);
		}
		else
		{
			$username = set_value('username');
			$password = $this->ag_auth->salt(set_value('password'));
			$fullname = set_value('fullname');
			$merchantname = set_value('merchantname');
			$bank = set_value('bank');
			$account_number = set_value('account_number');
			$account_name = set_value('account_name');
			$street = set_value('street');
			$district = set_value('district');
			$province = set_value('province');
			$city = set_value('city');
			$country = set_value('country');
			$zip = set_value('zip');
			$phone= set_value('phone');
			$mobile= set_value('mobile');
			$email = set_value('email');

			$same_as_personal_address = set_value('same_as_personal_address');

			$mc_street = set_value('mc_street');
			$mc_district = set_value('mc_district');
			$mc_province = set_value('mc_province');
			$mc_city = set_value('mc_city');
			$mc_country = set_value('mc_country');
			$mc_zip = set_value('mc_zip');
			$mc_phone= set_value('mc_phone');
			$mc_mobile= set_value('mc_mobile');
            $mc_toscan= set_value('mc_toscan');
            $mc_pickup_time= set_value('mc_pickup_time');
            $mc_pickup_cutoff= set_value('mc_pickup_cutoff');

			$mc_first_order = set_value('mc_first_order');
			$mc_last_order = set_value('mc_last_order');
			$mc_unlimited_time = set_value('mc_unlimited_time');

            $delivery_bearer = set_value('delivery_bearer');
            $cod_bearer = set_value('cod_surcharge_bearer');


			$group_id = set_value('group_id');

			$dataset = array(
				'username'=>$username,
				'password'=>$password,
				'fullname'=>$fullname,
				'merchantname'=>$merchantname,
				'bank'=>$bank,
				'account_number'=>$account_number,
				'account_name'=>$account_name,
				'street'=>$street,
				'district'=>$district,
				'province'=>$province,
				'city'=>$city,
				'country'=>$country,
				'zip'=>$zip,
				'phone'=>$phone,
				'mobile'=>$mobile,
				'email'=>$email,

				'same_as_personal_address' =>$same_as_personal_address,
				'mc_street' =>$mc_street,
				'mc_district' =>$mc_district,
				'mc_province' =>$mc_province,
				'mc_city' =>$mc_city,
				'mc_country' =>$mc_country,
				'mc_zip' =>$mc_zip,
				'mc_phone'=>$mc_phone,
				'mc_mobile'=>$mc_mobile,
                'mc_toscan'=>$mc_toscan,
                'mc_pickup_time'=>$mc_pickup_time,
                'mc_pickup_cutoff'=>$mc_pickup_cutoff,

				'mc_first_order' => $mc_first_order,
				'mc_last_order' => $mc_last_order,
				'mc_unlimited_time' => $mc_unlimited_time,

                'mc_delivery_bearer'=>$delivery_bearer,
                'mc_cod_bearer'=>$cod_bearer,

				'group_id'=>$group_id,
				'created'=> date('Y-m-d h:i:s',time())

			);

			if($this->db->insert($this->config->item('jayon_members_table'),$dataset) === TRUE)
			{
				$data['message'] = "The user account has now been created.";
				$data['page_title'] = 'Add Member';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$data['message'] = "The user account has not been created.";
				$data['page_title'] = 'Add Member Error';
				$data['back_url'] = anchor('admin/members/manage','Back to list');
				$this->ag_auth->view('message', $data);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

	public function edit($id)
	{
		if(in_array('merchant',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Merchants','admin/members/merchant');
			$this->breadcrumb->add_crumb('Edit Merchant','admin/members/merchant/edit/'.$id);
			$data['page_title'] = 'Edit Merchant';

			$back_url = 'admin/members/merchant';
			$success_url = 'admin/members/merchant';
			$error_url = 'admin/members/merchant/edit/'.$id;

			$utype = 'Merchant';

		}else if(in_array('buyer',$this->uri->segment_array())){
			$this->breadcrumb->add_crumb('Manage Buyers','admin/members/buyer');
			$this->breadcrumb->add_crumb('Edit Buyer','admin/members/buyer/edit/'.$id);
			$data['page_title'] = 'Edit Buyer';

			$back_url = 'admin/members/buyer';
			$success_url = 'admin/members/buyer';
			$error_url = 'admin/members/buyer/edit/'.$id;

			$utype = 'Buyer';
		}

		$this->form_validation->set_rules('username', 'Username', 'required|min_length[6]');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');
		$this->form_validation->set_rules('fullname', 'Full Name', 'required|trim|xss_clean');
		$this->form_validation->set_rules('merchantname', 'Merchant Name', 'trim|xss_clean');
		$this->form_validation->set_rules('bank', 'Bank', 'trim|xss_clean');
		$this->form_validation->set_rules('account_name', 'Account Name', 'trim|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|xss_clean');
		$this->form_validation->set_rules('street', 'Street', 'required|trim|xss_clean');
		$this->form_validation->set_rules('district', 'District', 'required|trim|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'required|trim|xss_clean');
		$this->form_validation->set_rules('province', 'Province', 'required|trim|xss_clean');
		$this->form_validation->set_rules('country', 'Country', 'required|trim|xss_clean');
		$this->form_validation->set_rules('zip', 'ZIP', 'required|trim|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('mobile', 'Mobile Number', 'required|trim|xss_clean');
		$this->form_validation->set_rules('group_id', 'Group', 'trim');

		$this->form_validation->set_rules('same_as_personal_address', 'Same As Personal Address', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_street', 'Street', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_district', 'District', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_city', 'City', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_province', 'Province', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_country', 'Country', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_zip', 'ZIP', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_phone', 'Phone Number', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_mobile', 'Mobile Number', 'trim|xss_clean');
        $this->form_validation->set_rules('mc_toscan', 'Use barcode scan', 'trim|xss_clean');
        $this->form_validation->set_rules('mc_pickup_time', 'Pick Up Time', 'trim|xss_clean');
        $this->form_validation->set_rules('mc_pickup_cutoff', 'Pick Up Cut Off', 'trim|xss_clean');

		$this->form_validation->set_rules('mc_first_order', 'First Order', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_last_order', 'Last order', 'trim|xss_clean');
		$this->form_validation->set_rules('mc_unlimited_time', 'Mobile Number', 'trim|xss_clean');

        $this->form_validation->set_rules('delivery_bearer', 'Delivery Bearer', 'trim|xss_clean');
        $this->form_validation->set_rules('cod_surcharge_bearer', 'COD Bearer', 'trim|xss_clean');

		$user = $this->get_user($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = array(
                group_id('pendingmerchant')=>group_desc('pendingmerchant'),
				group_id('merchant')=>group_desc('merchant'),
				group_id('buyer')=>group_desc('buyer')
			);
			$data['back_url'] = anchor($back_url,'Cancel');
			$this->ag_auth->view('members/edit',$data);
		}
		else
		{

			$dataset['username'] = set_value('username');
			$dataset['fullname'] = set_value('fullname');
			$dataset['merchantname'] = set_value('merchantname');
			$dataset['bank'] = set_value('bank');
			$dataset['account_name'] = set_value('account_name');
			$dataset['account_number'] = set_value('account_number');
			$dataset['street'] = set_value('street');
			$dataset['district'] = set_value('district');
			$dataset['province'] = set_value('province');
			$dataset['city'] = set_value('city');
			$dataset['country'] = set_value('country');
			$dataset['zip'] = set_value('zip');
			$dataset['phone'] = set_value('phone');
			$dataset['mobile'] = set_value('mobile');
			$dataset['email'] = set_value('email');
			$dataset['group_id'] = set_value('group_id');

			$dataset['same_as_personal_address'] = set_value('same_as_personal_address');
			$dataset['mc_street'] = set_value('mc_street');
			$dataset['mc_district'] = set_value('mc_district');
			$dataset['mc_province'] = set_value('mc_province');
			$dataset['mc_city'] = set_value('mc_city');
			$dataset['mc_country'] = set_value('mc_country');
			$dataset['mc_zip'] = set_value('mc_zip');
			$dataset['mc_phone'] = set_value('mc_phone');
			$dataset['mc_mobile'] = set_value('mc_mobile');

			$dataset['mc_first_order'] = set_value('mc_first_order');
			$dataset['mc_last_order'] = set_value('mc_last_order');
			$dataset['mc_unlimited_time'] = set_value('mc_unlimited_time');

            $dataset['mc_toscan'] = set_value('mc_toscan');
            $dataset['mc_pickup_time'] = set_value('mc_pickup_time');
            $dataset['mc_pickup_cutoff'] = set_value('mc_pickup_cutoff');

            $dataset['mc_delivery_bearer'] = set_value('delivery_bearer');
            $dataset['mc_cod_bearer'] = set_value('cod_surcharge_bearer');

			if($this->db->where('id',$id)->update($this->config->item('jayon_members_table'),$dataset) === TRUE)
			//if($this->update_user($id,$dataset) === TRUE)
			{
				$this->oi->add_success($utype.' updated & saved');
				redirect($success_url);

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$this->oi->add_success('Failed to update '.$utype);
				redirect($error_url);
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()

    public function editpass($id)
    {
        $this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_conf]');
        $this->form_validation->set_rules('password_conf', 'Password Confirmation', 'min_length[6]|matches[password]');

        $user = $this->get_user($id);
        $data['user'] = $user;

        if($this->form_validation->run() == FALSE)
        {
            $data['groups'] = $this->get_group();
            $data['page_title'] = 'Change Member Password';
            $this->ag_auth->view('members/editpass',$data);
        }
        else
        {
            $result = TRUE;
            $dataset['password'] = $this->ag_auth->salt(set_value('password'));

            //if( $result = $this->update_user($id,$dataset))
            if($this->db->where('id',$id)->update($this->config->item('jayon_members_table'),$dataset) === TRUE)
            {
                $data['message'] = "The user password has now updated.";
                $data['page_title'] = 'Edit Member Password Success';
                $data['back_url'] = anchor('admin/members/manage','Back to list');
                $this->ag_auth->view('message', $data);

            } // if($this->ag_auth->register($username, $password, $email) === TRUE)
            else
            {
                $data['message'] = "The user account failed to update.";
                $data['page_title'] = 'Edit Member Password Error';
                $data['back_url'] = anchor('admin/members/manage','Back to list');
                $this->ag_auth->view('message', $data);
            }

        } // if($this->form_validation->run() == FALSE)

    } // public function register()

    public function logo($id)
    {
        $data['id'] = $id;
        $data['page_title'] = 'Upload Logo';
        $this->ag_auth->view('members/uploadlogo',$data);
    }

	public function logoupload($id)
	{

        $uconfig['upload_path'] = $this->config->item('public_path').'logo/';
        $uconfig['allowed_types'] = 'jpg|png';
        $uconfig['max_size'] = 0;
        $uconfig['max_width']  = 0;
        $uconfig['max_height']  = 0;

        $this->load->library('upload', $uconfig);

        if ( ! $this->upload->do_upload())
        {
            $err = $this->upload->display_errors('<p>', '</p>');

            $data['message'] = "The logo failed to upload.".$err;
            $data['page_title'] = 'Upload Logo Error';
            $data['back_url'] = anchor('admin/members/merchant','Back to list');
            $this->ag_auth->view('message', $data);
        }
        else
        {
            $upl = $this->upload->data();

                $target_path = $upl['full_path'];

                $config['image_library'] = 'gd2';
                $config['source_image'] = $target_path;
                $config['new_image'] = $upl['file_path'].'logo_'.$id.'.jpg';
                $config['create_thumb'] = false;
                $config['maintain_ratio'] = TRUE;
                $config['width']     = 100;
                $config['height']   = 75;

                $this->load->library('image_lib', $config);

                $this->image_lib->resize();


            $data['message'] = "The logo picture uploaded.";
            $data['page_title'] = 'Upload Logo Success';
            $data['back_url'] = anchor('admin/members/merchant','Back to list');
            $this->ag_auth->view('message', $data);
        }

	} // public function register()
	// WOKRING ON PROPER IMPLEMENTATION OF ADDING & EDITING USER ACCOUNTS
}

?>