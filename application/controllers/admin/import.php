<?php

class Import extends Application
{
    public $ordermap = array(
        'ordertime'=>'',
        'buyerdeliveryzone'=>'',
        'buyerdeliverycity'=>'',
        'buyerdeliveryslot'=>1,
        'buyerdeliverytime'=>1,
        'assigntime'=>'',
        'timeslot'=>1,
        'assignment_zone'=>'',
        'assignment_city'=>'',
        'assignment_seq'=>'',
        'delivery_id'=>'',
        'delivery_cost'=>'',
        'cod_cost'=>'',
        'width'=>'',
        'height'=>'',
        'length'=>'',
        'weight'=>'',
        'actual_weight'=>'',
        'delivery_type'=>'',
        'currency'=>'IDR',
        'total_price'=>'',
        //'fixed_discount'=>'',
        //'total_discount'=>'',
        //'total_tax'=>'',
        'chargeable_amount'=>'',
        'delivery_bearer'=>'',
        'cod_bearer'=>'',
        'cod_method'=>'',
        'ccod_method'=>'',
        'application_id'=>'',
        'application_key'=>'',
        'buyer_id'=>'',
        'merchant_id'=>'',
        'merchant_trans_id'=>'',
        //'courier_id'=>'',
        //'device_id'=>'',
        'buyer_name'=>'',
        'email'=>'',
        'recipient_name'=>'',
        'shipping_address'=>'',
        'shipping_zip'=>'',
        'directions'=>'',
        //'dir_lat'=>'',
        //'dir_lon'=>'',
        'phone'=>'',
        'mobile1'=>'',
        'mobile2'=>'',
        'status'=>'pending',
        'laststatus'=>'pending',
        //'change_actor'=>'',
        //'actor_history'=>'',
        'delivery_note'=>'',
        //'reciever_name'=>'',
        //'reciever_picture'=>'',
        //'undersign'=>'',
        //'latitude'=>'',
        //'longitude'=>'',
        //'reschedule_ref'=>'',
        //'revoke_ref'=>'',
        //'reattemp'=>'',
        //'show_merchant'=>'',
        //'show_shop'=>'',
        'is_pickup'=>0,
        'is_import'=>1
    );


    public function __construct()
    {
        parent::__construct();
        $this->ag_auth->restrict('admin'); // restrict this controller to admins only
        $this->table_tpl = array(
            'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
        );
        $this->table->set_template($this->table_tpl);

        $this->breadcrumb->add_crumb('Home','admin/dashboard');

    }

    public function index()
    {
        $this->breadcrumb->add_crumb('Import','admin/import');

        $page['page_title'] = 'Import';
        $this->ag_auth->view('import/upload',$page); // Load the view
    }

    public function upload()
    {
        $config['upload_path'] = FCPATH.'upload/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            print_r($error);
        }
        else
        {
            $this->load->library('xls');

            $data = $this->upload->data();

            $xdata = $this->xls->load($data['full_path'],$data['file_ext']);

            $merchant_id = $this->input->post('merchant_id');
            $merchant_name = $this->input->post('merchant_name');

            $head_index = $this->input->post('header_index');

            $label_index = $this->input->post('label_index');
            $header_index = $this->input->post('header_index');
            $data_index = $this->input->post('data_index');

            //var_dump($xdata);

            //exit();

            $sheetdata = array();

            $sheetdata['merchant_id'] = $merchant_id;
            $sheetdata['merchant_name'] = $merchant_name;

            foreach($xdata as $sheet=>$row){

                $headidx = $header_index;
                $dataidx = $data_index;

                $xdata = $row;

                /*
                foreach($xdata['cells'] as $dt){
                    if(in_array('id', $dt)){
                        $head = $dt;
                        break;
                    }
                    $headidx++;
                }
                */

                $label = $row['cells'][$label_index];
                $head = $row['cells'][$header_index];

                //print_r($head);

                $datetimefields = array(
                    'created',
                    'ordertime',
                    'buyerdeliverytime',
                    'assigntime',
                    'deliverytime'
                );

                $datefields = array(
                    'assignment_date'
                );

                //print_r($row);

                $orderdata = array();

                for($i = $dataidx; $i <= $row['numRows'];$i++){
                    $temp = $row['cells'][$i];
                    $line = array();
                    for($j = 0;$j < count($head);$j++){

                        /*
                        if(in_array($head[$j], $datetimefields )){
                            $line[ $head[$j] ] = date('Y-m-d h:i:s', $this->xls->toPHPdate($temp[$j]) ) ;
                        }elseif(in_array($head[$j], $datefields )){
                            $line[ $head[$j] ] = date('Y-m-d', $this->xls->toPHPdate($temp[$j]) ) ;
                        }else{
                        */
                            // shift to omit "no" field
                            $line[ $head[$j]] = $temp[$j];
                        //}
                    }

                    $buyerdeliverytime = PHPExcel_Shared_Date::ExcelToPHP($line['buyerdeliverytime']);
                    $line['buyerdeliverytime'] = date('Y-m-d h:i:s',$buyerdeliverytime);

                    $line['buyerdeliverycity'] = ucwords(strtolower($line['buyerdeliverycity']));
                    $line['buyerdeliveryzone'] = ucwords(strtolower($line['buyerdeliveryzone']));

                    if( strtoupper($line['delivery_type']) == 'DO'){
                        $line['delivery_type'] = 'Delivery Only';
                    }

                    $line['delivery_type'] = ucfirst($line['delivery_type']);

                    $index = random_string('alnum', 5);
                    $orderdata[$index] = $line;


                }


                $orderdata = array('sheetname'=>$sheet,'label'=>$label,'head'=>$head,'data'=>$orderdata);

                $sheet_id = random_string('alnum', 6);
                $sheetdata[$sheet_id] = $orderdata;

            }


            $jsonfile = date('d-m-Y-h-i-s',time());

            file_put_contents(FCPATH.'json/'.$jsonfile.'.json', json_encode($sheetdata));

            redirect('admin/import/preview/'.$merchant_id.'/'.$jsonfile, 'location' );

        }

    }

    public function preview($merchant_id,$jsonfile)
    {
        $json = file_get_contents(FCPATH.'json/'.$jsonfile.'.json');

        //print_r(json_decode($json));

        $json = json_decode($json,true);

        //print_r($json);

        $merchant_id = $json['merchant_id'];
        $merchant_name = $json['merchant_name'];

        unset($json['merchant_id']);
        unset($json['merchant_name']);

        $tables = array();

        $apps = get_apps($merchant_id);

        $app_select = array();



        foreach($json as $sheet_id=>$rows){

            //print $sheet_id.'<br />';

            $this->load->library('table');

            $app_select = array();

            foreach($apps as $app ){
                $app_select[ $sheet_id.'|'.$app['key']] = $app['application_name'];
            }

            $selector = form_dropdown('apps[]',$app_select);

            $heads = array_merge(array('<input type="checkbox" id="select_all">'),$rows['head']);

            $this->table->set_heading(array('data'=>'SHEET : '.$rows['sheetname'].' '.$selector,'colspan'=>100));
            $this->table->set_subheading($heads);

            $cells = array();

            $idx = 0;

            foreach($rows['data'] as $index=>$cell){
                $cells[] = array_merge(array('<input name="entry[]" type="checkbox" class="selector" id="'.$index.'" value="'.$index.'">'),$cell);
                $idx++;
            }

            $tables[$sheet_id] = $this->table->generate($cells);

        }


        $page['tables'] = $tables;

        $page['merchant_id'] = $merchant_id;
        $page['merchant_name'] = $merchant_name;

        $page['app_select'] = $app_select;

        $page['jsonfile'] = $jsonfile;

        $this->breadcrumb->add_crumb('Import','admin/import');

        $page['page_title'] = 'Import Preview';
        $this->ag_auth->view('import/preview',$page); // Load the view
    }

    public function commit()
    {
        //print_r($this->input->post());

        $jsonfile = $this->input->post('jsonfile');
        $entry = $this->input->post('entry');
        $apps = $this->input->post('apps');

        $app_entry = array();
        foreach($apps as $app){
            $p = explode('|', $app);
            $app_entry[$p[0]] = $p[1];
        }

        $json = file_get_contents(FCPATH.'json/'.$jsonfile.'.json');

        $json = json_decode($json,true);

        //print_r($json);

        $merchant_id = $json['merchant_id'];
        $merchant_name = $json['merchant_name'];

        unset($json['merchant_id']);
        unset($json['merchant_name']);

        foreach($json as $sheet_id=>$rows){

            $app_key = $app_entry[$sheet_id];
            $app_id = get_app_id_from_key($app_key);

            $order = $this->ordermap;
            foreach ($rows['data'] as $key => $line) {
                if(in_array($key, $entry)){

                    $line['delivery_type'] = ($line['delivery_type'] == 'DO')?'Delivery Only':$line['delivery_type'];
                    $line['actual_weight'] = $line['weight'];
                    $line['weight'] = get_weight_tariff($line['weight'], $line['delivery_type'] ,$app_id);

                    $trx_detail = array();
                    $trx_detail[0]['unit_description'] = $line['package_description'];
                    $trx_detail[0]['unit_price'] = $line['total_price'];
                    $trx_detail[0]['unit_quantity'] = 1;
                    $trx_detail[0]['unit_total'] = $line['total_price'] ;
                    $trx_detail[0]['unit_discount'] = 0;

                    unset($line['package_description']);
                    unset($line['no']);

                    foreach($line as $k=>$v){
                        $order[$k] = $v;
                    }

                    $order['zip'] = '-';

                    $order['merchant_id'] = $merchant_id;
                    $order['application_id'] = $app_id;
                    $order['application_key'] = $app_key;
                    $order['trx_detail'] = $trx_detail;

                    $trx_id = 'TRX_'.$merchant_id.'_'.str_replace(array(' ','.'), '', microtime());

                    print "order input: \r\n";
                    print_r($order);

                    $trx = json_encode($order);
                    $result = $this->order_save($trx,$app_key,$trx_id);

                    //print $result;

                }

            }

        }

        redirect('admin/delivery/incoming', 'location' );

    }

    public function ___order_save($indata)
    {
        $args = '';

        $in = (object) $indata;

        $buyer_id = 1;

        $args = 'p='.$in->merchant_trans_id;

        $is_new = false;

        $in->phone = ( isset( $in->phone ) && $in->phone != '')?normalphone( $in->phone ):'';
        $in->mobile1 = ( isset( $in->mobile1 ) && $in->mobile1 != '' )?normalphone( $in->mobile1 ):'';
        $in->mobile2 = ( isset( $in->mobile2 ) && $in->mobile2 != '' )?normalphone( $in->mobile2 ):'';


        if(isset($in->buyer_id) && $in->buyer_id != '' && $in->buyer_id > 1){

            $buyer_id = $in->buyer_id;
            $is_new = false;

        }else{

            if($in->email == '' || $in->email == '-' || !isset($in->email) || $in->email == 'noemail'){

                $in->email = 'noemail';
                $is_new = true;
                if( trim($in->phone.$in->mobile1.$in->mobile2) != ''){
                    if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){
                        $buyer_id = $buyer['id'];
                        $is_new = false;
                    }
                }

            }else if($buyer = $this->check_email($in->email)){

                $buyer_id = $buyer['id'];
                $is_new = false;

            }else if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){

                $buyer_id = $buyer['id'];
                $is_new = false;

            }

        }

        if(isset($in->transaction_id) && $in->transaction_id != ""){
            $transaction_id = $in->transaction_id;
        }


        if($is_new){
            $buyer_username = substr(strtolower(str_replace(' ','',$in->buyer_name)),0,6).random_string('numeric', 4);
            $dataset['username'] = $buyer_username;
            $dataset['email'] = $in->email;
            $dataset['phone'] = $in->phone;
            $dataset['mobile1'] = $in->mobile1;
            $dataset['mobile2'] = $in->mobile2;
            $dataset['fullname'] = $in->buyer_name;
            $password = random_string('alnum', 8);
            $dataset['password'] = $this->ag_auth->salt($password);
            $dataset['created'] = date('Y-m-d H:i:s',time());

            /*
            $dataset['province'] =
            $dataset['mobile']
            */

            $dataset['street'] = $in->shipping_address;
            $dataset['district'] = $in->buyerdeliveryzone;
            $dataset['city'] = $in->buyerdeliverycity;
            $dataset['country'] = 'Indonesia';
            $dataset['zip'] = $in->zip;

            //$buyer_id = $this->register_buyer($dataset);
            $is_new = true;
        }

        $order['created'] = date('Y-m-d H:i:s',time());
        $order['ordertime'] = date('Y-m-d H:i:s',time());
        $order['application_id'] = $in->application_id;
        $order['application_key'] = $in->application_key;
        $order['buyer_id'] = $buyer_id;
        $order['merchant_id'] = $in->merchant_id;
        $order['merchant_trans_id'] = trim($transaction_id);

        $order['buyer_name'] = $in->buyer_name;
        $order['recipient_name'] = $in->recipient_name;
        $order['email'] = $in->email;
        $order['directions'] = $in->directions;
        //$order['dir_lat'] = $in->dir_lat;
        //$order['dir_lon'] = $in->dir_lon;
        $order['buyerdeliverytime'] = $in->buyerdeliverytime;
        $order['buyerdeliveryslot'] = $in->buyerdeliveryslot;
        $order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
        $order['buyerdeliverycity'] = (is_null($in->buyerdeliverycity) || $in->buyerdeliverycity == '')?'Jakarta':$in->buyerdeliverycity;

        $order['currency'] = $in->currency;
        $order['total_price'] = (isset($in->total_price))?$in->total_price:0;
        $order['total_discount'] = (isset($in->total_discount))?$in->total_discount:0;
        $order['total_tax'] = (isset($in->total_tax))?$in->total_tax:0;
        $order['cod_cost'] = $in->cod_cost;
        $order['chargeable_amount'] = (isset($in->chargeable_amount))?$in->chargeable_amount:0;

        $order['shipping_address'] = $in->shipping_address;
        $order['shipping_zip'] = $in->zip;
        $order['phone'] = $in->phone;
        $order['mobile1'] = $in->mobile1;
        $order['mobile2'] = $in->mobile2;
        $order['status'] = $in->status;

        $order['width'] = $in->width;
        $order['height'] = $in->height;
        $order['length'] = $in->length;
        $order['weight'] = (isset($in->weight))?$in->weight:0;
        $order['delivery_type'] = $in->delivery_type;
        $order['delivery_cost'] = (isset($in->delivery_cost))?$in->delivery_cost:0;

        $order['cod_bearer'] = (isset($in->cod_bearer))?$in->cod_bearer:'merchant';
        $order['delivery_bearer'] = (isset($in->delivery_bearer))?$in->delivery_bearer:'merchant';

        $order['cod_method'] = (isset($in->cod_method))?$in->cod_method:'cash';
        $order['ccod_method'] = (isset($in->ccod_method))?$in->ccod_method:'full';

        if(isset($in->show_shop)){
            $order['show_shop'] = $in->show_shop;
        }

        if(isset($in->show_merchant)){
            $order['show_merchant'] = $in->show_merchant;
        }

        print 'to be inserted';
        print_r($order);

        /*
        $inres = $this->db->insert($this->config->item('incoming_delivery_table'),$order);
        $sequence = $this->db->insert_id();

        $delivery_id = get_delivery_id($sequence,$app->merchant_id);

        $nedata['fullname'] = $in->buyer_name;
        $nedata['merchant_trx_id'] = trim($transaction_id);
        $nedata['delivery_id'] = $delivery_id;
        $nedata['merchantname'] = $app->application_name;
        $nedata['app'] = $app;

        $order['delivery_id'] = $delivery_id;

        $this->save_buyer($order);

        $this->db->where('id',$sequence)->update($this->config->item('incoming_delivery_table'),array('delivery_id'=>$delivery_id));

        */
            $this->table_tpl = array(
                'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
            );
            $this->table->set_template($this->table_tpl);


            $this->table->set_heading(
                'No.',
                'Description',
                'Quantity',
                'Total'
                ); // Setting headings for the table

            $d = 0;
            $gt = 0;


        if($in->trx_detail){
            $seq = 0;

            foreach($in->trx_detail as $it){
                $item['ordertime'] = $order['ordertime'];
                $item['delivery_id'] = $delivery_id;
                $item['unit_sequence'] = $seq++;
                $item['unit_description'] = $it->unit_description;
                $item['unit_price'] = $it->unit_price;
                $item['unit_quantity'] = $it->unit_quantity;
                $item['unit_total'] = $it->unit_total;
                $item['unit_discount'] = $it->unit_discount;

                $rs = $this->db->insert($this->config->item('delivery_details_table'),$item);

                $this->table->add_row(
                    (int)$item['unit_sequence'] + 1,
                    $item['unit_description'],
                    $item['unit_quantity'],
                    $item['unit_total']
                );

                $u_total = str_replace(array(',','.'), '', $item['unit_total']);
                $u_discount = str_replace(array(',','.'), '', $item['unit_discount']);
                $gt += (int)$u_total;
                $d += (int)$u_discount;

            }

            $total = (isset($in->total_price) && $in->total_price > 0)?$in->total_price:0;
            $total = str_replace(array(',','.'), '', $total);
            $total = (int)$total;
            $gt = ($total < $gt)?$gt:$total;

            $disc = (isset($in->total_discount))?$in->total_discount:0;
            $tax = (isset($in->total_tax))?$in->total_tax:0;
            $cod = (isset($in->cod_cost))?$in->cod_cost:'Paid by merchant';

            $disc = str_replace(array(',','.'), '', $disc);
            $tax = str_replace(array(',','.'), '',$tax);
            $cod = str_replace(array(',','.'), '',$cod);

            $disc = (int)$disc;
            $tax = (int)$tax;
            $cod = (int)$cod;

            $chg = ($gt - $disc) + $tax + $cod;

            $this->table->add_row(
                '',
                '',
                'Total Price',
                number_format($gt,2,',','.')
            );

            $this->table->add_row(
                '',
                '',
                'Total Discount',
                number_format($disc,2,',','.')
            );

            $this->table->add_row(
                '',
                '',
                'Total Tax',
                number_format($tax,2,',','.')
            );


            if($cod == 0){
                $this->table->add_row(
                    '',
                    '',
                    'COD Charges',
                    'Paid by Merchant'
                );
            }else{
                $this->table->add_row(
                    '',
                    '',
                    'COD Charges',
                    number_format($cod,2,',','.')
                );
            }


            $this->table->add_row(
                '',
                '',
                'Total Charges',
                number_format($chg,2,',','.')
            );

            $nedata['detail'] = $this->table;

            print $this->table;

            //$result = json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id,'buyer_id'=>$buyer_id));

            return true;
        }else{
            $nedata['detail'] = false;

            //$result = json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));

            return false;
        }

        //print_r($app);
        /*
        if($app->notify_on_new_order == 1){
            send_notification('New Delivery Order - Jayon Express COD Service',$in->email,$app->cc_to,$app->reply_to,'order_submit',$nedata,null);
        }

        if($is_new == true){
            $edata['fullname'] = $dataset['fullname'];
            $edata['username'] = $buyer_username;
            $edata['password'] = $password;
            if($app->notify_on_new_member == 1 && $in->email != 'noemail'){
                send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,null,'new_member',$edata,null);
            }

        }
        */

        //$api_key = $this->get('key');
        //$transaction_id = $this->get('trx');

        if(is_null($api_key)){
            $result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
            return $result;
        }else{
            $app = $this->get_key_info(trim($api_key));

            if($app == false){
                $result = json_encode(array('status'=>'ERR:INVALIDKEY','timestamp'=>now()));
                return $result;
            }else{
            }
        }

        $this->log_access($api_key, __METHOD__ ,$result,$args);
    }

    // worker functions

    public function order_save($indata,$api_key,$transaction_id)
    {
        $args = '';

        //$api_key = $this->get('key');
        //$transaction_id = $this->get('trx');

        if(is_null($api_key)){
            $result = json_encode(array('status'=>'ERR:NOKEY','timestamp'=>now()));
            return $result;
        }else{
            $app = $this->get_key_info(trim($api_key));

            if($app == false){
                $result = json_encode(array('status'=>'ERR:INVALIDKEY','timestamp'=>now()));
                return $result;
            }else{
                //$in = $this->input->post('transaction_detail');
                //$in = file_get_contents('php://input');
                $in = $indata;

                //print $in;

                $buyer_id = 1;

                $args = 'p='.$in;

                $in = json_decode($in);

                print "order input to save: \r\n";
                print_r($in);

                $is_new = false;

                $in->phone = ( isset( $in->phone ) && $in->phone != '')?normalphone( $in->phone ):'';
                $in->mobile1 = ( isset( $in->mobile1 ) && $in->mobile1 != '' )?normalphone( $in->mobile1 ):'';
                $in->mobile2 = ( isset( $in->mobile2 ) && $in->mobile2 != '' )?normalphone( $in->mobile2 ):'';


                if(isset($in->buyer_id) && $in->buyer_id != '' && $in->buyer_id > 1){

                    $buyer_id = $in->buyer_id;
                    $is_new = false;

                }else{

                    if($in->email == '' || $in->email == '-' || !isset($in->email) || $in->email == 'noemail'){

                        $in->email = 'noemail';
                        $is_new = true;
                        if( trim($in->phone.$in->mobile1.$in->mobile2) != ''){
                            if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){
                                $buyer_id = $buyer['id'];
                                $is_new = false;
                            }
                        }

                    }else if($buyer = $this->check_email($in->email)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }else if($buyer = $this->check_phone($in->phone,$in->mobile1,$in->mobile2)){

                        $buyer_id = $buyer['id'];
                        $is_new = false;

                    }

                }

                if(isset($in->merchant_trans_id) && $in->merchant_trans_id != ""){
                    $transaction_id = $in->merchant_trans_id;
                }


                if($is_new){
                    $buyer_username = substr(strtolower(str_replace(' ','',$in->buyer_name)),0,6).random_string('numeric', 4);
                    $dataset['username'] = $buyer_username;
                    $dataset['email'] = $in->email;
                    $dataset['phone'] = $in->phone;
                    $dataset['mobile1'] = $in->mobile1;
                    $dataset['mobile2'] = $in->mobile2;
                    $dataset['fullname'] = $in->buyer_name;
                    $password = random_string('alnum', 8);
                    $dataset['password'] = $this->ag_auth->salt($password);
                    $dataset['created'] = date('Y-m-d H:i:s',time());

                    /*
                    $dataset['province'] =
                    $dataset['mobile']
                    */

                    $dataset['street'] = $in->shipping_address;
                    $dataset['district'] = $in->buyerdeliveryzone;
                    $dataset['city'] = $in->buyerdeliverycity;
                    $dataset['country'] = 'Indonesia';
                    $dataset['zip'] = $in->zip;

                    //$buyer_id = $this->register_buyer($dataset);
                    $is_new = true;
                }

                $order['created'] = date('Y-m-d H:i:s',time());
                $order['ordertime'] = date('Y-m-d H:i:s',time());
                $order['application_id'] = $app->id;
                $order['application_key'] = $app->key;
                $order['buyer_id'] = $buyer_id;
                $order['merchant_id'] = $app->merchant_id;
                $order['merchant_trans_id'] = trim($transaction_id);

                $order['buyer_name'] = $in->buyer_name;
                $order['recipient_name'] = $in->recipient_name;
                $order['email'] = $in->email;
                $order['directions'] = $in->directions;
                //$order['dir_lat'] = $in->dir_lat;
                //$order['dir_lon'] = $in->dir_lon;
                $order['buyerdeliverytime'] = $in->buyerdeliverytime;
                $order['buyerdeliveryslot'] = $in->buyerdeliveryslot;
                $order['buyerdeliveryzone'] = $in->buyerdeliveryzone;
                $order['buyerdeliverycity'] = (is_null($in->buyerdeliverycity) || $in->buyerdeliverycity == '')?'Jakarta':$in->buyerdeliverycity;

                $order['currency'] = $in->currency;
                $order['total_price'] = (isset($in->total_price))?$in->total_price:0;
                $order['total_discount'] = (isset($in->total_discount))?$in->total_discount:0;
                $order['total_tax'] = (isset($in->total_tax))?$in->total_tax:0;

                if($in->delivery_type == 'DO' || $in->delivery_type == 'Delivery Only'){
                    $order['cod_cost'] = 0;
                }else{
                    $order['cod_cost'] = get_cod_tariff($order['total_price'],$app->id);
                }

                $order['shipping_address'] = $in->shipping_address;
                $order['shipping_zip'] = $in->zip;
                $order['phone'] = $in->phone;
                $order['mobile1'] = $in->mobile1;
                $order['mobile2'] = $in->mobile2;
                $order['status'] = $in->status;

                $order['width'] = $in->width;
                $order['height'] = $in->height;
                $order['length'] = $in->length;
                $order['weight'] = (isset($in->weight))?$in->weight:0;
                $order['delivery_type'] = $in->delivery_type;

                $order['delivery_cost'] = $order['weight'];

                $order['cod_bearer'] = (isset($in->cod_bearer))?$in->cod_bearer:'merchant';
                $order['delivery_bearer'] = (isset($in->delivery_bearer))?$in->delivery_bearer:'merchant';

                $order['cod_method'] = (isset($in->cod_method))?$in->cod_method:'cash';
                $order['ccod_method'] = (isset($in->ccod_method))?$in->ccod_method:'full';

                // check out who is bearing the cost
                if($order['delivery_type'] == 'COD' || $order['delivery_type'] == 'CCOD'){
                    if($order['delivery_bearer'] == 'merchant'){
                        $dcost = 0;
                    }else{
                        $dcost = $order['delivery_cost'];
                    }

                    if($order['cod_bearer'] == 'merchant'){
                        $codcost = 0;
                    }else{
                        $codcost = $order['cod_cost'];
                    }

                    $order['chargeable_amount'] = $order['total_price'] + $dcost + $codcost;
                }else{

                    if($order['delivery_bearer'] == 'merchant'){
                        $dcost = 0;
                    }else{
                        $dcost = $order['delivery_cost'];
                    }

                    $order['chargeable_amount'] = $dcost;
                }

                if(isset($in->show_shop)){
                    $order['show_shop'] = $in->show_shop;
                }

                if(isset($in->show_merchant)){
                    $order['show_merchant'] = $in->show_merchant;
                }

                $order['is_import'] = 1;
                print_r($order);

                $inres = $this->db->insert($this->config->item('incoming_delivery_table'),$order);
                $sequence = $this->db->insert_id();

                $delivery_id = get_delivery_id($sequence,$app->merchant_id);

                $nedata['fullname'] = $in->buyer_name;
                $nedata['merchant_trx_id'] = trim($transaction_id);
                $nedata['delivery_id'] = $delivery_id;
                $nedata['merchantname'] = $app->application_name;
                $nedata['app'] = $app;

                $order['delivery_id'] = $delivery_id;

                $this->save_buyer($order);

                $this->db->where('id',$sequence)->update($this->config->item('incoming_delivery_table'),array('delivery_id'=>$delivery_id));

                    $this->table_tpl = array(
                        'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
                    );
                    $this->table->set_template($this->table_tpl);


                    $this->table->set_heading(
                        'No.',
                        'Description',
                        'Quantity',
                        'Total'
                        ); // Setting headings for the table

                    $d = 0;
                    $gt = 0;


                if($in->trx_detail){
                    $seq = 0;

                    foreach($in->trx_detail as $it){
                        $item['ordertime'] = $order['ordertime'];
                        $item['delivery_id'] = $delivery_id;
                        $item['unit_sequence'] = $seq++;
                        $item['unit_description'] = $it->unit_description;
                        $item['unit_price'] = $it->unit_price;
                        $item['unit_quantity'] = $it->unit_quantity;
                        $item['unit_total'] = $it->unit_total;
                        $item['unit_discount'] = $it->unit_discount;

                        $rs = $this->db->insert($this->config->item('delivery_details_table'),$item);

                        $this->table->add_row(
                            (int)$item['unit_sequence'] + 1,
                            $item['unit_description'],
                            $item['unit_quantity'],
                            $item['unit_total']
                        );

                        $u_total = str_replace(array(',','.'), '', $item['unit_total']);
                        $u_discount = str_replace(array(',','.'), '', $item['unit_discount']);
                        $gt += (int)$u_total;
                        $d += (int)$u_discount;

                    }

                    $total = (isset($in->total_price) && $in->total_price > 0)?$in->total_price:0;
                    $total = str_replace(array(',','.'), '', $total);
                    $total = (int)$total;
                    $gt = ($total < $gt)?$gt:$total;

                    $disc = (isset($in->total_discount))?$in->total_discount:0;
                    $tax = (isset($in->total_tax))?$in->total_tax:0;
                    $cod = (isset($in->cod_cost))?$in->cod_cost:'Paid by merchant';

                    $disc = str_replace(array(',','.'), '', $disc);
                    $tax = str_replace(array(',','.'), '',$tax);
                    $cod = str_replace(array(',','.'), '',$cod);

                    $disc = (int)$disc;
                    $tax = (int)$tax;
                    $cod = (int)$cod;

                    $chg = ($gt - $disc) + $tax + $cod;

                    $this->table->add_row(
                        '',
                        '',
                        'Total Price',
                        number_format($gt,2,',','.')
                    );

                    $this->table->add_row(
                        '',
                        '',
                        'Total Discount',
                        number_format($disc,2,',','.')
                    );

                    $this->table->add_row(
                        '',
                        '',
                        'Total Tax',
                        number_format($tax,2,',','.')
                    );


                    if($cod == 0){
                        $this->table->add_row(
                            '',
                            '',
                            'COD Charges',
                            'Paid by Merchant'
                        );
                    }else{
                        $this->table->add_row(
                            '',
                            '',
                            'COD Charges',
                            number_format($cod,2,',','.')
                        );
                    }


                    $this->table->add_row(
                        '',
                        '',
                        'Total Charges',
                        number_format($chg,2,',','.')
                    );

                    $nedata['detail'] = $this->table;

                    $result = json_encode(array('status'=>'OK:ORDERPOSTED','timestamp'=>now(),'delivery_id'=>$delivery_id,'buyer_id'=>$buyer_id));

                    return $result;
                }else{
                    $nedata['detail'] = false;

                    $result = json_encode(array('status'=>'OK:ORDERPOSTEDNODETAIL','timestamp'=>now(),'delivery_id'=>$delivery_id));

                    return $result;
                }

                //print_r($app);

                if($app->notify_on_new_order == 1){
                    send_notification('New Delivery Order - Jayon Express COD Service',$in->email,$app->cc_to,$app->reply_to,'order_submit',$nedata,null);
                }

                if($is_new == true){
                    $edata['fullname'] = $dataset['fullname'];
                    $edata['username'] = $buyer_username;
                    $edata['password'] = $password;
                    if($app->notify_on_new_member == 1 && $in->email != 'noemail'){
                        send_notification('New Member Registration - Jayon Express COD Service',$in->email,null,null,'new_member',$edata,null);
                    }

                }

            }
        }

        $this->log_access($api_key, __METHOD__ ,$result,$args);
    }

    //private supporting functions

    private function get_key_info($key){
        if(!is_null($key)){
            $this->db->where('key',$key);
            $result = $this->db->get($this->config->item('applications_table'));
            if($result->num_rows() > 0){
                $row = $result->row();
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function get_dev_info($key){
        if(!is_null($key)){
            $this->db->where('key',$key);
            $result = $this->db->get($this->config->item('jayon_devices_table'));
            if($result->num_rows() > 0){
                $row = $result->row();
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function get_dev_info_by_id($identifier){
        if(!is_null($identifier)){
            $this->db->where('identifier',$identifier);
            $result = $this->db->get($this->config->item('jayon_devices_table'));
            if($result->num_rows() > 0){
                $row = $result->row();
                return $row;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


    private function check_email($email){
        $em = $this->db->where('email',$email)->get($this->config->item('jayon_members_table'));
        if($em->num_rows() > 0){
            return $em->row_array();
        }else{
            return false;
        }
    }

    private function check_phone($phone, $mobile1, $mobile2){
        $em = $this->db->like('phone',$phone)
                ->or_like('mobile1',$mobile1)
                ->or_like('mobile2',$mobile2)
                ->get($this->config->item('jayon_members_table'));
        if($em->num_rows() > 0){
            return $em->row_array();
        }else{
            return false;
        }
    }


    private function register_buyer($dataset){
        $dataset['group_id'] = 5;

        if($this->db->insert($this->config->item('jayon_members_table'),$dataset)){
            return $this->db->insert_id();
        }else{
            return 0;
        }
    }


    private function save_buyer($ds){

        if(isset($ds['buyer_id']) && $ds['buyer_id'] != '' && $ds['buyer_id'] > 1){
            if($pid = $this->get_parent_buyer($ds['buyer_id'])){
                $bd['is_child_of'] = $pid;
                $this->update_group_count($pid);
            }
        }

        $bd['buyer_name']  =  $ds['buyer_name'];
        $bd['buyerdeliveryzone']  =  $ds['buyerdeliveryzone'];
        $bd['buyerdeliverycity']  =  $ds['buyerdeliverycity'];
        $bd['shipping_address']  =  $ds['shipping_address'];
        $bd['phone']  =  $ds['phone'];
        $bd['mobile1']  =  $ds['mobile1'];
        $bd['mobile2']  =  $ds['mobile2'];
        $bd['recipient_name']  =  $ds['recipient_name'];
        $bd['shipping_zip']  =  $ds['shipping_zip'];
        $bd['email']  =  $ds['email'];
        $bd['delivery_id']  =  $ds['delivery_id'];
        $bd['delivery_cost']  =  $ds['delivery_cost'];
        $bd['cod_cost']  =  $ds['cod_cost'];
        $bd['delivery_type']  =  $ds['delivery_type'];
        $bd['currency']  =  $ds['currency'];
        $bd['total_price']  =  $ds['total_price'];
        $bd['chargeable_amount']  =  $ds['chargeable_amount'];
        $bd['delivery_bearer']  =  $ds['delivery_bearer'];
        $bd['cod_bearer']  =  $ds['cod_bearer'];
        $bd['cod_method']  =  $ds['cod_method'];
        $bd['ccod_method']  =  $ds['ccod_method'];
        $bd['application_id']  =  $ds['application_id'];
        //$bd['buyer_id']  =  $ds['buyer_id'];
        $bd['merchant_id']  =  $ds['merchant_id'];
        $bd['merchant_trans_id']  =  $ds['merchant_trans_id'];
        //$bd['courier_id']  =  $ds['courier_id'];
        //$bd['device_id']  =  $ds['device_id'];
        $bd['directions']  =  $ds['directions'];
        //$bd['dir_lat']  =  $ds['dir_lat'];
        //$bd['dir_lon']  =  $ds['dir_lon'];
        //$bd['delivery_note']  =  $ds['delivery_note'];
        //$bd['latitude']  =  $ds['latitude'];
        //$bd['longitude']  =  $ds['longitude'];
        $bd['created']  =  $ds['created'];

        $bd['cluster_id'] = substr(md5(uniqid(rand(), true)), 0, 20 );

        if($this->db->insert($this->config->item('jayon_buyers_table'),$bd)){
            return $this->db->insert_id();
        }else{
            return 0;
        }
    }

    private function get_parent_buyer($id){
        $this->db->where('id',$id);
        $by = $this->db->get($this->config->item('jayon_buyers_table'));

        if($by->num_rows() > 0){

            $buyer = $by->row_array();
            if($buyer['is_parent'] == 1){
                $pid = $buyer['id'];
            }elseif($buyer['is_child_of'] > 0 && $buyer['is_parent'] == 0){
                $pid = $buyer['is_child_of'];
            }else{
                $pid = false;
            }

            return $pid;

        }else{
            return false;
        }

    }

    private function update_group_count($id){

        $this->db->where('is_child_of',$id);
        $groupcount = $this->db->count_all_results($this->config->item('jayon_buyers_table'));

        $dataup = array('group_count'=>($groupcount + 1) );

        $this->db->where('id',$id);

        if($res = $this->db->update($this->config->item('jayon_buyers_table'),$dataup) ){
            return $res;
        }else{
            return false;
        }

    }

    private function get_device($key){
        $dev = $this->db->where('key',$key)->get($this->config->item('jayon_mobile_table'));
        print_r($dev);
        print $this->db->last_query();
        return $dev->row_array();
    }

    private function get_group(){
        $this->db->select('id,description');
        $result = $this->db->get($this->ag_auth->config['auth_group_table']);
        foreach($result->result_array() as $row){
            $res[$row['id']] = $row['description'];
        }
        return $res;
    }

    private function log_access($api_key,$query,$result,$args = null){
        $data['timestamp'] = date('Y-m-d H:i:s',time());
        $data['accessor_ip'] = $this->accessor_ip;
        $data['api_key'] = (is_null($api_key))?'':$api_key;
        $data['query'] = $query;
        $data['result'] = $result;
        $data['args'] = (is_null($args))?'':$args;

        access_log($data);
    }

    private function admin_auth($username = null,$password = null){
        if(is_null($username) || is_null($password)){
            return false;
        }

        $password = $this->ag_auth->salt($password);
        $result = $this->db->where('username',$username)->where('password',$password)->get($this->ag_auth->config['auth_user_table']);

        if($result->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }


}

?>