<?php

class Manifests extends Application
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
        $this->breadcrumb->add_crumb('Manifest','admin/manifests/listing');

    }

    public function listing()
    {

        $this->breadcrumb->add_crumb('Released','admin/manifests/listing');

        $this->load->library('table');

        $this->table->set_heading(
            '#',
            'Device',
            'Period_from',
            'Period_to',
            'Release_date',
            'Invoice_number',
            'Note',
            'Filename',
            'Created',
            'Actions'); // Setting headings for the table

        $this->table->set_footing(
            '',
            '<input type="text" name="search_device" id="search_device" value="Search devices" class="search_init" />',
            '<input type="text" name="search_email" id="search_email" value="Search zone" class="search_init" />',
            '<input type="text" name="search_from" value="Search from" class="search_init" />',
            '<input type="text" name="search_to" value="Search to" class="search_init" />',
            '<input type="text" name="search_release_date" value="Search release date" class="search_init" />',
            '<input type="text" name="search_note" value="Search Note" class="search_init" />',
            '<input type="text" name="search_filename" value="Search filename" class="search_init" />',
            '<input type="text" name="search_created" id="search_timestamp" value="Search created" class="search_init" />',
            ''
            );

        $page['sortdisable'] = '';
        $page['ajaxurl'] = 'admin/invoices/ajaxmanage';
        $page['add_button'] = array('link'=>'admin/members/add','label'=>'Add New Member');
        $page['page_title'] = 'Released Manifests';
        $this->ag_auth->view('memberajaxlistview',$page); // Load the view
    }

    public function ajaxmanage(){

        $limit_count = $this->input->post('iDisplayLength');
        $limit_offset = $this->input->post('iDisplayStart');

        $sort_col = $this->input->post('iSortCol_0');
        $sort_dir = $this->input->post('sSortDir_0');

        $columns = array(
            'merchantname',
            'period_from',
            'period_to',
            'release_date',
            'invoice_number',
            'note',
            'filename',
            'created'
        );

        $this->db->select('*')
            ->from($this->config->item('manifest_table'));

        $dbca = clone $this->db;

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
            $this->db->like('identifier',$this->input->post('sSearch_0'));
            $search = true;
        }


        if($this->input->post('sSearch_1') != ''){
            $this->db->like('period_from',$this->input->post('sSearch_1'));
            $search = true;
        }

        if($this->input->post('sSearch_2') != ''){
            $this->db->like('period_to',$this->input->post('sSearch_2'));
            $search = true;
        }

        if($this->input->post('sSearch_3') != ''){
            $this->db->like('release_date',$this->input->post('sSearch_3'));
            $search = true;
        }

        if($this->input->post('sSearch_4') != ''){
            $this->db->like('invoice_number',$this->input->post('sSearch_4'));
            $search = true;
        }

        if($this->input->post('sSearch_5') != ''){
            $this->db->like('note',$this->input->post('sSearch_5'));
            $search = true;
        }
        if($this->input->post('sSearch_6') != ''){
            $this->db->like('filename',$this->input->post('sSearch_6'));
            $search = true;
        }


        if($search){
            //$this->db->and_();
        }


        $dbcr = clone $this->db;

        $this->db->order_by($columns[$sort_col],$sort_dir);

        $data = $this->db->limit($limit_count, $limit_offset)
            ->get();

        $last_query = $this->db->last_query();

        $result = $data->result_array();

        $count_all = $dbca->count_all_results();
        $count_display_all = $dbcr->count_all_results();

        //print $this->db->last_query();

        $aadata = array();

        $num = $limit_offset;

        foreach($result as $value => $key)
        {
            $num++;

            $delete = anchor("admin/members/delete/".$key['id']."/", "Delete"); // Build actions links
            $editpass = anchor("admin/members/editpass/".$key['id']."/", "Password"); // Build actions links
            $dl = anchor(base_url().'public/manifests/'.$key['filename'].'.pdf', 'Download pdf', array('target'=>'_blank')); // Build actions links

            $edit = anchor("admin/members/edit/".$key['id']."/", "Edit"); // Build actions links
            $detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", $key['merchantname']); // Build detail links

            $aadata[] = array(
                $num,
                $key['merchantname'],
                $key['period_from'],
                $key['period_to'],
                $key['release_date'],
                $key['invoice_number'],
                $key['note'],
                $key['filename'],
                $key['created'],
                $dl.''
                //$edit.' '.$editpass.' '.$delete
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
            $edit = anchor("admin/members/merchant/edit/".$key['id']."/", "Edit"); // Build actions links
            $detail = form_checkbox('assign[]',$key['id'],FALSE,'class="assign_check"').' '.anchor("admin/members/details/".$key['id']."/", '<span id="un_'.$key['id'].'">'.$key['username'].'</span>'); // Build detail links

            $groupname = ($key['group_id'] == group_id('pendingmerchant'))?sprintf('<span class="red">%s</span>',user_group_desc($key['group_id']) ):user_group_desc($key['group_id']);

            $aadata[] = array(
                $detail,
                $key['email'],
                $key['fullname'],
                $key['merchantname'],
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
                $addapp.' '.$edit.' '.$editpass.' '.$delete
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

}

?>