<?php

class Retur extends Application
{

    public function __construct()
    {
        parent::__construct();
        $this->ag_auth->restrict('admin'); // restrict this controller to admins only
        $this->table_tpl = array(
            'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="reportTable stickyHeader" id="toClone">'
        );
        $this->table->set_template($this->table_tpl);

        $this->breadcrumb->add_crumb('Home','admin/dashboard');
        $this->breadcrumb->add_crumb('Custom','admin/custom');

    }

    public function index(){
        //$this->breadcrumb->add_crumb('Reports','admin/reports/daily');
        $this->breadcrumb->add_crumb('Statistics','admin/reports');

        $year = date('Y',time());
        $month = date('m',time());

        $page['period'] = ' - '.date('M Y',time());

        $page['page_title'] = 'Monthly Statistics';
        $this->ag_auth->view('reports/index',$page); // Load the view
    }


    //manifest

    public function report($type = null,$deliverytype = null,$zone = null,$merchant = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null,$par4 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $mid = (is_null($merchant))?'noid':$merchant;
        $deliverytype = (is_null($deliverytype))?'noid':$deliverytype;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $data['getparams'] = array(
            'type'=> $type ,
            'deliverytype'=>$deliverytype,
            'zone'=> $zone,
            'merchant'=> $merchant,
            'year'=> $year ,
            'scope'=>$scope ,
            'par1'=> $par1 ,
            'par2'=> $par2 ,
            'par3'=> $par3 ,
            'par4'=> $par4
            );

        $pdf = null;

        if($scope == 'month'){
            $days = cal_days_in_month(CAL_GREGORIAN, $par1, $year);
            $from = date('Y-m-d', strtotime($year.'/'.$par1.'/1'));
            $to =   date('Y-m-d', strtotime($year.'/'.$par1.'/'.$days));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = $par1;
            $data['week'] = 1;
        }else if($scope == 'week'){
            $from = date('Y-m-d', strtotime('1 Jan '.$year.' +'.($par1 - 1).' weeks'));
            $to = date('Y-m-d', strtotime('1 Jan '.$year.' +'.$par1.' weeks - 1 day'));
            $pdf = $par2;
            $invdate = $par3;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = $par1;
        }else if($scope == 'date'){
            $from = $par1;
            $to = $par2;
            $pdf = $par3;
            $invdate = $par4;

            $data['getparams']['par3'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }else{
            $from = date('Y-m-d',time());
            $to = date('Y-m-d',time());
            $pdf = null;
            $invdate = null;

            $data['getparams']['par2'] = 'pdf';

            $data['month'] = 1;
            $data['week'] = 1;
        }

        $data['year'] = $year;
        $data['from'] = $from;
        $data['to'] = $to;

        $clist = get_device_list();

        $cs = array('noid'=>'All');
        foreach ($clist as $ckey) {
            $cs[$ckey->id] = $ckey->identifier;
        }

        $data['zone'] = urldecode($zone);
        $data['merchants'] = $cs;
        $data['id'] = $id;


        $mclist = get_merchant(null,false);

        $mcs = array('noid'=>'All');
        foreach ($mclist as $mckey) {
            $mcs[$mckey['id']] = $mckey['merchantname'].' - '.$mckey['fullname'];
        }

        $data['deliverytypes'] = $this->config->item('deliverytype_selector');

        $data['merchantlist'] = $mcs;
        $data['mid'] = $mid;

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';

            $data['merchantname'] = 'All Device';

        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_devices_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->identifier;
            $data['type_name'] = $user->identifier;
            $data['bank_account'] = 'n/a';

            $data['merchantname'] = $user->identifier;
        }

        if($deliverytype == 'noid'){
            $data['dtype'] = 'All Type';
        }else{
            $data['dtype'] = $deliverytype;
        }

        if($mid == 'noid'){
            $data['merchantinfo'] = 'All Merchant';
        }else{
            $member = $this->db->where('id',$mid)->get($this->config->item('jayon_members_table'))->row();
            //print $this->db->last_query();
            //$data['type'] = $member->merchantname.' - '.$member->fullname;
            //$data['type_name'] = $member->fullname;
            //$data['bank_account'] = 'n/a';

            $data['merchantinfo'] = $member->merchantname;
        }

        if($data['zone'] == 'all'){
            $data['zone'] = 'All zones';
        }

        if(is_null($invdate)){
            $data['invdate'] = '-';
            $data['invdatenum'] = '-';
        }else{
            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate)) ;
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $mtab = $this->config->item('assigned_delivery_table');

        $this->db->select('assignment_date,'.$mtab.'.created,deliverytime,delivery_id,'.$mtab.'.merchant_id as merchant_id,cod_bearer,delivery_bearer,buyer_name,buyerdeliveryzone,pending_count,c.fullname as courier_name,'.$mtab.'.phone,'.$mtab.'.mobile1,'.$mtab.'.mobile2,merchant_trans_id,m.merchantname as merchant_name, m.fullname as fullname, a.application_name as app_name, a.domain as domain ,delivery_type,shipping_address,delivery_note,status,pickup_status,warehouse_status,cod_cost,delivery_cost,total_price,chargeable_amount,total_tax,total_discount')
            ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
            ->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left')
            ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
            ->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
            //->like('assignment_date',$date,'before')
            ->from($this->config->item('incoming_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.device_id',$id);
        }

        if($deliverytype != 'noid'){
            if($deliverytype == 'DO'){
                $deliverytype = 'Delivery Only';
                $this->db->where($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype);
            }else if($deliverytype == 'COD'){
                $this->db->like($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype,'before');
            }
        }

        if($mid != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.merchant_id',$mid);
        }

        if($zone != 'all'){
            $zone = urldecode($zone);
            $this->db->where($this->config->item('assigned_delivery_table').'.buyerdeliveryzone',$zone);
        }


        /*
        $this->db->and_();
        $this->db->group_start()
            ->where('delivery_type','COD')
            ->or_where('delivery_type','CCOD')
            ->group_end();
        */

        $this->db->and_();
        $this->db->group_start()
            ->where('status',$this->config->item('trans_status_mobile_return'))
            //->or_where('status',$this->config->item('trans_status_mobile_pickedup'))
            //->or_where('status',$this->config->item('trans_status_mobile_enroute'))
            //->or_()
            //    ->group_start()
            //        ->where('status',$this->config->item('trans_status_new'))
            //        ->where('pending_count >', 0)
            //    ->group_end()
            ->group_end();

        $this->db->order_by('buyerdeliverycity','asc')->order_by('buyerdeliveryzone','asc');

        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();

            // Open the output stream
            $fh = fopen('php://output', 'w');

            // Start output buffering (to capture stream contents)
            ob_start();

            // Loop over the * to export
            if (! empty($result)) {
                $headers = array_keys($result[0]);
                    fputcsv($fh, $headers);
                foreach ($result as $item) {
                    fputcsv($fh, $item);
                }
            }

            // Get the contents of the output buffer
            $string = ob_get_clean();

            $filename = str_replace('/', '_', uri_string()).'.csv';

            // Output CSV-specific headers
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            header('Content-Transfer-Encoding: binary');

            exit($string);
        }


        $rows = $this->db->get();

        $trans = $rows->result();

        $last_query = $this->db->last_query();
        //print_r($result);


        //exit();

        //print_r($trans);

        //exit();

        if($pdf == 'print' || $pdf == 'pdf'){
            $this->table->set_heading(
                'No.',
                'Pembeli',
                'No Kode Toko',
                'Nama Barang',
                'Jumlah',
                'Keterangan'
            ); // Setting headings for the table

            /*
            $this->table->set_subheading(
                array('data'=>'Mohon tunjukkan kartu identitas untuk di foto sebagai bagian bukti penerimaan','style'=>'text-align:center;','colspan'=>13),
                /*
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                array('data'=>'TANDA TANGAN','style'=>'min-width:100px;'),
                array('data'=>'NAMA','style'=>'min-width:100px;')

            ); // Setting headings for the table
            */
        }else{
            $this->table->set_heading(
                'No.',
                'Pembeli',
                'No Kode Toko',
                'Nama Barang',
                'Jumlah',
                'Keterangan'
            ); // Setting headings for the table

            /*
            $this->table->set_subheading(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                array('data'=>'TANDA TANGAN','style'=>'min-width:100px;'),
                array('data'=>'NAMA','style'=>'min-width:100px;')

            ); // Setting headings for the table
            */
        }



        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $d = 0;
        $gt = 0;

        $lastdate = '';

        $courier_name = '';

        $counts = array(
                'Delivery Only'=>0,
                'COD'=>0,
                'CCOD'=>0,
                'PS'=>0,
                'pending'=>0
            );

        foreach($rows->result() as $r){

            $counts[$r->delivery_type] += 1;

            if( $r->pending_count > 0){
                $counts['pending'] += 1;
            }

            $courier_name = $r->courier_name;
            //$total = str_replace(array(',','.'), '', $r->total_price);
            //$dsc = str_replace(array(',','.'), '', $r->total_discount);
            //$tax = str_replace(array(',','.'), '',$r->total_tax);
            //$dc = str_replace(array(',','.'), '',$r->delivery_cost);
            //$cod = str_replace(array(',','.'), '',$r->cod_cost);

            $total = $r->total_price;
            $dsc = $r->total_discount;
            $tax = $r->total_tax;
            $dc = $r->delivery_cost;
            //$cod = $r->cod_cost;
            $cod = $r->total_price * 0.006;

            $total = (is_nan((double)$total))?0:(double)$total;
            $dsc = (is_nan((double)$dsc))?0:(double)$dsc;
            $tax = (is_nan((double)$tax))?0:(double)$tax;
            $dc = (is_nan((double)$dc))?0:(double)$dc;
            $cod = (is_nan((double)$cod))?0:(double)$cod;

            $payable = 0;


            $details = $this->db->where('delivery_id',$r->delivery_id)->order_by('unit_sequence','asc')->get($this->config->item('delivery_details_table'));

            $details = $details->result_array();


            $d = 0;
            $gt = 0;

            foreach($details as $value => $key)
            {

                $u_total = $key['unit_total'];
                $u_discount = $key['unit_discount'];
                $gt += (is_nan((double)$u_total))?0:(double)$u_total;
                $d += (is_nan((double)$u_discount))?0:(double)$u_discount;

            }

            if($gt == 0 ){
                if($total > 0 && $payable)
                $gt = $total;
            }

            $payable = $gt;

            $total_delivery += (double)$dc;
            $total_cod += (double)$cod;
            $total_billing += (double)$payable;

            $db = '';
            if($r->delivery_bearer == 'merchant'){
                $dc = 0;
                $db = 'M';
            }else{
                $db = 'B';
            }

            //force all DO to zero

            $cb = '';
            if($r->cod_bearer == 'merchant'){
                $cod = 0;
                $cb = 'M';
            }else{
                $cb = 'B';
            }

            $codclass = '';

            if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                $chg = ($gt - $dsc) + $tax + $dc + $cod;

                //$chg = $gt + $dc + $cod;

                $codclass = 'cod';

            }else{
                $dc = 0;
                $cod = 0;
                $chg = $dc;
            }




            if($pdf == 'print' || $pdf == 'pdf'){

                $this->table->add_row(
                    $seq,
                    $r->buyer_name,
                    $r->merchant_trans_id,
                    'PAKET '.$r->merchant_name,
                    1,
                    $r->delivery_note
                );

            }else{

                $this->table->add_row(
                    $seq,
                    $r->buyer_name,
                    $r->merchant_trans_id,
                    'PAKET '.$r->merchant_name,
                    1,
                    $r->delivery_note
                );


            }

            $seq++;
        }

        /*

            if($pdf == 'print' || $pdf == 'pdf'){
                $this->table->add_row(
                    '',
                    '',
                    '',
                    '',
                    array('data'=>'Rp '.idr($total_delivery),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod),'class'=>'currency total'),
                    '',
                    '',
                    ''
                );
            }
        */



        if($pdf == 'print' || $pdf == 'pdf'){

            $total_span = 2;
            $say_span = 6;

        }else{

            $total_span = 12;
            $say_span = 13;

        }

        /*
        $this->table->add_row(
            'Terbilang',
            array('data'=>'&nbsp;','colspan'=>$say_span)
        );

        if($type == 'Merchant' || $type == 'Global'){
            $this->table->add_row(
                'Payable',
                array('data'=>$this->number_words->to_words($total_billing).' rupiah',
                    'colspan'=>$say_span)
            );
        }

        $this->table->add_row(
            array('data'=>'Delivery Charge',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'COD Surcharge',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'Grand Total',
                'colspan'=>$total_span),
            array('data'=>$this->number_words->to_words($total_delivery + $total_cod).' rupiah',
                'colspan'=>$say_span)
        );
        */

        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        $data['summary_count'] = $counts;
        /* end copy */

        $this->breadcrumb->add_crumb('Return Report','custom/cod/report');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';
        $page['page_title'] = 'Return Report';
        $data['select_title'] = 'Device';
        $data['zone_select_title'] = 'Zone';


        $data['controller'] = 'custom/retur/report/';

        $data['last_query'] = $last_query;

        $data['grand_total'] = $total_delivery + $total_cod;


        $data['zones'] = array_merge( array('all'=>'All'), get_zone_options() ) ;

        $data['courier_name'] = $courier_name;

        $data['merchantname'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantname']);
        $data['merchantinfo'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantinfo']);

        $zonename = strtoupper(str_replace(' ', '_', $data['zone']));
        $mname = strtoupper(str_replace(' ','_',$data['merchantname']));
        $minfo = strtoupper(str_replace(' ','_',$data['merchantinfo']));

        $pdffilename = 'JSM-RETURN-'.$mname.'-'.$minfo.'-'.$zonename.'-'.$data['invdatenum'];

        if($pdf == 'pdf'){
            $html = $this->load->view('auth/pages/custom/print/returprint',$data,true);
            $pdf_name = $pdffilename;
            $pdfbuf = pdf_create($html, $pdf_name,'A3','landscape', false);

            file_put_contents(FCPATH.'public/custom/'.$pdf_name.'.pdf', $pdfbuf);

            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate));


            $invdata = array(
                'merchant_id'=>$type,
                'merchantname'=>$data['merchantname'],
                'merchantinfo'=>$data['merchantinfo'],
                'period_from'=>$data['from'],
                'period_to'=>$data['to'],
                'release_date'=>$invdate,
                'doc_type'=>'returnreport',
                'doc_number'=>$pdffilename,
                'note'=>'',
                'filename'=>$pdffilename
            );

            $inres = $this->db->insert($this->config->item('docs_table'),$invdata);

            return array(file_exists(FCPATH.'public/custom/'.$pdf_name.'.pdf'), $pdf_name.'.pdf');

        }else if($pdf == 'print'){
            $this->load->view('auth/pages/custom/print/returprint',$data); // Load the view
        }else{
            //$this->load->view('custom/returgenerator',$data); // Load the view
            $this->ag_auth->view('custom/returgenerator',$data); // Load the view
        }
    }

    public function genreport(){
        $type = null;
        $deliverytype = null;
        $zone = null;
        $merchant = null;
        $year = null;
        $scope = null;
        $par1 = null;
        $par2 = null;
        $par3 = null;
        $par4 = null;

        $type = $this->input->post('type');
        $deliverytype = $this->input->post('deliverytype');
        $zone = $this->input->post('zone');
        $merchant = $this->input->post('merchant');
        $year = $this->input->post('year');
        $scope = $this->input->post('scope');
        $par1 = $this->input->post('par1');
        $par2 = $this->input->post('par2');
        $par3 = $this->input->post('par3');
        $par4 = $this->input->post('par4');

        $result = $this->report($type,$deliverytype ,$zone,$merchant,$year, $scope, $par1, $par2, $par3,$par4);

        $result[0] = ($result[0])?'OK':'FAILED';

        print json_encode(array('result'=>$result[0], 'file'=>$result[1]));

    }

    public function hide_trx($trx_id){
        if(preg_match('/^TRX_/', $trx_id)){
            return '';
        }else{
            return $trx_id;
        }
    }

    public function short_did($did){
        $did = explode('-',$did);
        return array_pop($did);
    }

    public function split_phone($phone){
        return str_replace(array('/','#','|'), '<br />', $phone);
    }


}

?>
