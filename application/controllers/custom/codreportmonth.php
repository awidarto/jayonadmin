<?php

class Codreportmonth extends Application
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

    }

    public function testxls(){
        $tmpl_data['_gmv'] = 100000000;
        $r = $this->compile_xls(array(),$tmpl_data, FCPATH.'public/xlstemplate/codreportmonth.xlsx');

        print_r($r);
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

    public function report($type = null,$deliverytype = null,$status = null,$year = null, $scope = null, $par1 = null, $par2 = null, $par3 = null,$par4 = null){

        $type = (is_null($type))?'Global':$type;
        $id = (is_null($type))?'noid':$type;
        $deliverytype = (is_null($deliverytype))?'noid':$deliverytype;
        $status = (is_null($status))?'all':$status;

        if(is_null($scope)){
            $id = 'noid';
            $scope = 'month';
            $year = date('Y',time());
            $par1 = date('m',time());
        }

        $data['getparams'] = array(
            'type'=> $type ,
            'deliverytype'=>$deliverytype,
            'status'=>$status,
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

        $clist = get_merchant(null,false);

        $cs = array('noid'=>'All');
        foreach ($clist as $ckey) {
            $cs[$ckey['id']] = $ckey['merchantname'].' - '.$ckey['fullname'];
        }

        $data['merchants'] = $cs;
        $data['id'] = $id;

        $data['statuslist'] = array_merge(array('all'=>'All'), $this->config->item('status_list') );
        $data['stid'] = $status;

        $data['deliverytypes'] = $this->config->item('deliverytype_selector');

        /* copied from print controller */

        $this->load->library('number_words');

        if($id == 'noid'){
            $data['type_name'] = '-';
            $data['bank_account'] = 'n/a';
            $data['type'] = 'Global';

            $data['merchantname'] = 'All Merchant';

        }else{
            $user = $this->db->where('id',$id)->get($this->config->item('jayon_members_table'))->row();
            //print $this->db->last_query();
            $data['type'] = $user->merchantname.' - '.$user->fullname;
            $data['type_name'] = $user->fullname;
            $data['bank_account'] = $user->bank.' '.$user->account_number.' a/n '.$user->account_name;

            $data['merchantname'] = $user->merchantname;
        }

        if(is_null($invdate)){
            $data['invdate'] = '-';
            $data['invdatenum'] = '-';
        }else{
            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate)) ;
        }

        if($deliverytype == 'noid'){
            $data['dtype'] = 'All Type';
        }else{
            $data['dtype'] = $deliverytype;
        }

        $data['period'] = $from.' s/d '.$to;

        $sfrom = date('Y-m-d',strtotime($from));
        $sto = date('Y-m-d',strtotime($to));

        $this->db->select('assignment_date,delivery_id,'.$this->config->item('assigned_delivery_table').'.merchant_id as merchant_id,buyer_name,merchant_trans_id,m.merchantname as merchant_name, m.fullname as fullname, a.application_name as app_name, a.domain as domain ,delivery_type,status,fulfillment_code,cod_cost,delivery_cost,total_price,total_tax,total_discount,chargeable_amount,actual_weight,application_id,application_key')
            ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
            ->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left')
            ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
            ->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left')
            //->like('assignment_date',$date,'before')
            ->from($this->config->item('incoming_delivery_table'));

        $column = 'assignment_date';
        $daterange = sprintf("`%s`between '%s 00:00:00' and '%s 23:59:59' ", $column, $sfrom, $sto);

        $this->db->where($daterange, null, false);
        $this->db->where($column.' != ','0000-00-00');

        if($id != 'noid'){
            $this->db->where($this->config->item('assigned_delivery_table').'.merchant_id',$id);
        }

        if($deliverytype != 'noid'){
            if($deliverytype == 'DO'){
                $deliverytype = 'Delivery Only';
                $this->db->where($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype);
            }else if($deliverytype == 'COD'){
                $this->db->like($this->config->item('assigned_delivery_table').'.delivery_type',$deliverytype,'before');
            }
        }

        if($status != 'all'){
                $this->db->where('status',   $status);
        }else{
            $this->db->and_();
            $this->db->group_start();
                $this->db->where('status',   $this->config->item('trans_status_mobile_delivered'));
                /*
                $this->db->or_where('status',$this->config->item('trans_status_mobile_revoked'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_noshow'));
                $this->db->or_where('status',$this->config->item('trans_status_mobile_rescheduled'));
                */
            $this->db->group_end();

        }

        $this->db->order_by('merchant_name', 'asc');
        $this->db->order_by('assignment_date', 'asc');
        $this->db->order_by('delivery_type', 'asc');

        //print $this->db->last_query();

        if($pdf == 'csv'){

            $result = $this->db->get()->result_array();


            foreach ($result as $r){

                if($r['total_price'] == 0 || is_null($r['total_price']) || $r['total_price'] == ''){
                    if($r['chargeable_amount'] > 0){
                        $r['total_price'] = $r['chargeable_amount'];
                    }
                }

                $app_id = $r['application_id'];

                if($r['delivery_type'] == 'COD' || $r['delivery_type'] == 'CCOD'){
                    if($r['cod_cost'] == 0 || is_null($r['cod_cost']) || $r['cod_cost'] == ''){
                        try{
                            //$app_id = get_app_id_from_key($r['application_key']);
                            $r['cod_cost'] = get_cod_tariff($r['total_price'],$app_id);
                        }catch(Exception $e){

                        }
                    }
                }else{
                    $r['cod_cost'] = 0;
                }



                if($r['delivery_cost'] == 0 || is_null($r['delivery_cost']) || $r['delivery_cost'] == ''){
                    try{
                        $r['delivery_cost'] = get_weight_tariff($r['actual_weight'], $r['delivery_type'] ,$app_id);
                    }catch(Exception $e){

                    }
                }

                # code...
            }

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

        $xls = array();

        //exit();
        if($pdf == 'print' || $pdf == 'pdf' || $pdf == 'xls'){
            /*
            $this->table->set_heading(
                'No.',
                'Delivery Time',
                'Delivery ID',
                'Type',
                'Delivery Fee',
                'COD Surchg',
                'Buyer',
                'Kode Toko',
                'Status'
            ); // Setting headings for the table
            */

            $this->table->set_heading(
                'No.',
                'Merchant',
                'Delivery Date',
                'No Kode Penjualan Toko',
                'Fulfillment / Order ID',
                'Delivery ID',
                //'Merchant Name',
                'Store',
                'Buyer Name',
                'Delivery Type',
                'Status',
                'Package Value',
                'Disc',
                'Tax',
                'Delivery Chg',
                'COD Surchg',
                'COD Value'
            ); // Setting headings for the table

            $xls[] = array(
                'No.',
                'Merchant',
                'Delivery Date',
                'No Kode Penjualan Toko',
                'Fulfillment / Order ID',
                'Delivery ID',
                //'Merchant Name',
                'Store',
                'Buyer Name',
                'Delivery Type',
                'Status',
                'Package Value',
                'Disc',
                'Tax',
                'Delivery Chg',
                'COD Surchg',
                'COD Value'
            );

        }else{
            $this->table->set_heading(
                'No.',
                'Merchant',
                'Delivery Date',
                'No Kode Penjualan Toko',
                'Fulfillment / Order ID',
                'Delivery ID',
                //'Merchant Name',
                'Store',
                'Buyer Name',
                'Delivery Type',
                'Status',
                'Package Value',
                'Disc',
                'Tax',
                'Delivery Chg',
                'COD Surchg',
                'COD Value',
                'GMV'
            ); // Setting headings for the table

        }


        $seq = 1;
        $total_billing = 0;
        $total_delivery = 0;
        $total_cod = 0;

        $total_cod_val = 0;

        $total_payable = 0;

        $lastdate = '';

        $lastmerchant = '';
        $lasttxdate = '';

        $m_total = 0;
        $m_dsc = 0;
        $m_tax = 0;
        $m_dc = 0;
        $m_cod = 0;
        $m_codval = 0;
        $m_payable = 0;

        $first_row = true;

        foreach($rows->result() as $r){

            $app_id = $r->application_id;

            if($r->total_price == 0 || is_null($r->total_price) || $r->total_price == ''){
                if($r->chargeable_amount > 0){
                    $r->total_price = $r->chargeable_amount;
                }
            }


            if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                if($r->cod_cost == 0 || is_null($r->cod_cost) || $r->cod_cost == ''){
                    try{
                        //$app_id = get_app_id_from_key($r->application_key);
                        $r->cod_cost = get_cod_tariff($r->total_price,$app_id);
                    }catch(Exception $e){

                    }
                }

            }else{
                $r->cod_cost = 0;
            }


            if($r->delivery_cost == 0 || is_null($r->delivery_cost) || $r->delivery_cost == ''){
                try{
                    $r->delivery_cost = get_weight_tariff($r->actual_weight, $r->delivery_type ,$app_id);
                    //$r->delivery_cost = get_cod_tariff($r->total_price,$r->application_id);
                }catch(Exception $e){

                }
            }

            //$total = str_replace(array(',','.'), '', $r->total_price);
            //$dsc = str_replace(array(',','.'), '', $r->total_discount);
            //$tax = str_replace(array(',','.'), '',$r->total_tax);
            //$dc = str_replace(array(',','.'), '',$r->delivery_cost);
            //$cod = str_replace(array(',','.'), '',$r->cod_cost);
            //$charge = str_replace(array(',','.'), '',$r->chargeable_amount);

            $total =  $r->total_price;
            $dsc =  $r->total_discount;
            $tax = $r->total_tax;
            $dc = $r->delivery_cost;
            $cod = $r->cod_cost;
            $charge = $r->chargeable_amount;

            $total = (is_nan( (double)$total))?0:(double)$total;
            $dsc = (is_nan((double)$dsc))?0:(double)$dsc;
            $tax = (is_nan((double)$tax))?0:(double)$tax;
            $dc = (is_nan((double)$dc))?0:(double)$dc;
            $cod = (is_nan((double)$cod))?0:(double)$cod;
            $charge = (is_nan((double)$charge))?0:(double)$charge;

            if($total == 0 && $charge > 0){
                $total = $charge;
            }

            $payable = 0;

            $payable = ($total - $dsc) + $tax;

            $total_payable += ($total - $dsc) + $tax;

            $total_delivery += (int)str_replace('.','',$dc);
            $total_cod += (int)str_replace('.','',$cod);
            //$total_billing += (int)str_replace('.','',$payable);

            //$payable = str_replace('.','',$payable);

            $total_billing = $total_billing + (double)$payable;

            $codval = ($r->delivery_type == 'COD'|| $r->delivery_type == 'CCOD')?$payable:0;

            $total_cod_val += $codval;

            $m_total += $total;
            $m_dsc += $dsc;
            $m_tax += $tax;
            $m_dc += $dc;
            $m_cod += $cod;
            $m_codval += $codval;
            $m_payable += $m_payable;


            if($pdf == 'print' || $pdf == 'pdf' || $pdf == 'xls'){
                /*
                $this->table->add_row(
                    $seq,
                    date('d-m-Y',strtotime($r->assignment_date)),
                    $this->short_did($r->delivery_id),
                    $r->delivery_type,
                    array('data'=>idr($dc),'class'=>'currency'),
                    array('data'=>idr($cod),'class'=>'currency'),
                    $r->buyer_name,
                    $this->hide_trx($r->merchant_trans_id),
                    $r->status
                );
                */

                $this->table->add_row(
                    $seq,
                    $r->merchant_name,
                    date('d-m-Y',strtotime($r->assignment_date)),
                    $this->hide_trx($r->merchant_trans_id),
                    $r->fulfillment_code,
                    $this->short_did($r->delivery_id),
                    //$r->fullname.'<hr />'.$r->merchant_name,
                    $r->app_name.'<hr />'.$r->domain,
                    $r->buyer_name,
                    $r->delivery_type,
                    $r->status,
                    array('data'=>idr($total),'class'=>'currency'),
                    array('data'=>idr($dsc),'class'=>'currency'),
                    array('data'=>idr($tax),'class'=>'currency'),
                    array('data'=>idr($dc),'class'=>'currency'),
                    array('data'=>idr($cod),'class'=>'currency'),
                    array('data'=>idr($codval),'class'=>'currency')
                    //array('data'=>idr($payable),'class'=>'currency')
                );

                $xls[] = array(
                    $seq,
                    $r->merchant_name,
                    date('d-m-Y',strtotime($r->assignment_date)),
                    $this->hide_trx($r->merchant_trans_id),
                    $r->fulfillment_code,
                    $this->short_did($r->delivery_id),
                    //$r->fullname.'<hr />'.$r->merchant_name,
                    $r->app_name.' - '.$r->domain,
                    $r->buyer_name,
                    $r->delivery_type,
                    $r->status,
                    idr($total,false),
                    idr($dsc,false),
                    idr($tax,false),
                    idr($dc,false),
                    idr($cod,false),
                    idr($codval,false)
                    //array('data'=>idr($payable),'class'=>'currency')
                );

            }else{

                $this->table->add_row(
                    $seq,
                    ($lastmerchant != $r->merchant_name)?$r->merchant_name:'',
                    (($lasttxdate != $r->assignment_date) || ($lastmerchant != $r->merchant_name) )?date('d-m-Y',strtotime($r->assignment_date)):'',
                    $this->hide_trx($r->merchant_trans_id),
                    $r->fulfillment_code,
                    $this->short_did($r->delivery_id),
                    //$r->fullname.'<hr />'.$r->merchant_name,
                    $r->app_name.'<hr />'.$r->domain,
                    $r->buyer_name,
                    $r->delivery_type,
                    $r->status,
                    array('data'=>idr($total),'class'=>'currency'),
                    array('data'=>idr($dsc),'class'=>'currency'),
                    array('data'=>idr($tax),'class'=>'currency'),
                    array('data'=>idr($dc),'class'=>'currency'),
                    array('data'=>idr($cod),'class'=>'currency'),
                    array('data'=>idr($codval),'class'=>'currency'),
                    array('data'=>idr($payable),'class'=>'currency')
                );

                if($lastmerchant != $r->merchant_name){

                    $this->table->add_row(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        //'',
                        '',
                        '',
                        '',
                        '',
                        array('data'=>idr($total),'class'=>'currency'),
                        array('data'=>idr($dsc),'class'=>'currency'),
                        array('data'=>idr($tax),'class'=>'currency'),
                        array('data'=>idr($dc),'class'=>'currency'),
                        array('data'=>idr($cod),'class'=>'currency'),
                        array('data'=>idr($codval),'class'=>'currency'),
                        array('data'=>idr($payable),'class'=>'currency')
                    );

                }

            }

            $lastmerchant = $r->merchant_name;

            $lasttxdate = $r->assignment_date;

            $first_row = false;

            $seq++;
        }

            if($pdf == 'print' || $pdf == 'pdf'|| $pdf == 'xls'){
                /*
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
                */

                $this->table->add_row(
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
                    array('data'=>'Rp '.idr($total_delivery),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod_val),'class'=>'currency total')
                    //array('data'=>'Rp '.idr($total_payable),'class'=>'currency total')
                );

                $xls[] = array(
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
                    'Rp '.idr($total_delivery),
                    'Rp '.idr($total_cod),
                    'Rp '.idr($total_cod_val)
                    //array('data'=>'Rp '.idr($total_payable),'class'=>'currency total')
                );

            }else{
                $this->table->add_row(
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
                    array('data'=>'Rp '.idr($total_delivery),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_cod_val),'class'=>'currency total'),
                    array('data'=>'Rp '.idr($total_payable),'class'=>'currency')
                );
            }



        if($pdf == 'print' || $pdf == 'pdf'|| $pdf == 'xls'){

            $total_span = 2;
            $say_span = 4;

        }else{

            $total_span = 1;
            $say_span = 11;

        }

        /*
        $this->table->add_row(
            'Terbilang',
            array('data'=>'&nbsp;','colspan'=>$say_span)
        );

        if($type == 'Merchant' || $type == 'Global'){
            $this->table->add_row(
                array('data'=>'Payable',
                    'colspan'=>$total_span),
                array('data'=>idr($total_billing),
                    'colspan'=>$total_span,'class'=>'currency'),
                array('data'=>$this->number_words->to_words((double)$total_billing).' rupiah',
                    'colspan'=>$say_span)
            );
        }

        $this->table->add_row(
            array('data'=>'Delivery Charge',
                'colspan'=>$total_span),
            array('data'=>idr($total_delivery),
                'colspan'=>$total_span,'class'=>'currency'),
            array('data'=>$this->number_words->to_words($total_delivery).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'COD Surcharge',
                'colspan'=>$total_span),
            array('data'=>idr($total_cod),
                'colspan'=>$total_span,'class'=>'currency'),
            array('data'=>$this->number_words->to_words($total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        $this->table->add_row(
            array('data'=>'Grand Total',
                'colspan'=>$total_span),
            array('data'=>idr($total_delivery + $total_cod),
                'colspan'=>$total_span,'class'=>'currency'),
            array('data'=>$this->number_words->to_words($total_delivery + $total_cod).' rupiah',
                'colspan'=>$say_span)
        );

        */

        $recontab = $this->table->generate();
        $data['recontab'] = $recontab;

        /* TOP SUMMARY TABLE */

        $this->table->clear();

        $tmpl = array( 'table_open'  => '<table style="width:400px;" border="0" cellpadding="0" cellspacing="0" class="mytable">' );

        $this->table->set_template($tmpl);

        $this->table->add_row(
            array('data'=>'<h4>Summary</h4>','colspan'=>2)
        );

        if($type == 'Merchant' || $type == 'Global'){
            $this->table->add_row(
                array('data'=>'Payable'),
                array('data'=>idr((double)$total_billing),'class'=>'currency')
            );

        }

        $this->table->add_row(
            array('data'=>'Total COD Value','style'=>'padding-bottom:8px;'),
            '&nbsp;',
            array('data'=>idr($total_cod_val),'class'=>'currency','style'=>'padding-bottom:8px;')
        );


        $this->table->add_row(
            array('data'=>'Total Delivery Cost'),
            '&nbsp;',
            array('data'=>idr($total_delivery + $total_cod),'class'=>'currency')
        );


        $this->table->add_row(
            array('data'=>'Total Delivery Charge','style'=>'padding-left:20px;'),
            array('data'=>idr($total_delivery),'class'=>'currency'),
            '&nbsp;'
        );


        $this->table->add_row(
            array('data'=>'Total COD Surcharge','style'=>'padding-left:20px;'),
            array('data'=>idr($total_cod),'class'=>'currency'),
            '&nbsp;'
        );

        $this->table->add_row(
            array('data'=>'Total Transfered to Merchant'),
            '&nbsp;',
            array('data'=>idr( $total_cod_val - ($total_delivery + $total_cod)),'class'=>'currency')
        );

        $sumtab = $this->table->generate();
        $data['sumtab'] = $sumtab;


        /* end copy */

        $this->breadcrumb->add_crumb('COD Report','admin/custom/codreportmonth');

        $page['ajaxurl'] = 'admin/reports/ajaxreconciliation';

        $page['page_title'] = 'Merchant Reconciliations';

        $data['select_title'] = 'Merchant';

        $data['controller'] = 'custom/codreportmonth/report/';

        $data['last_query'] = $last_query;

        $data['grand_total'] = $total_delivery + $total_cod;

        $data['total_payable'] = $total_payable;

        $data['total_cod_val'] = $total_cod_val;

        $data['merchantname'] = str_replace( array('http','www.',':','/','.com','.net','.co.id'),'',$data['merchantname']);

        $mname = strtoupper(str_replace(' ','_',$data['merchantname']));

        $pdffilename = 'COD-'.$mname.'-'.$data['invdatenum'];


        $total_transfer = $total_cod_val - ($total_delivery + $total_cod);

        $txd['_gmv'] = idr((double)$total_billing,false);
        $txd['_total_delivery'] = idr($total_delivery + $total_cod,false);
        $txd['_total_do'] = idr($total_delivery,false);
        $txd['_total_cod'] = idr($total_cod,false);
        $txd['_total_transfer'] = idr($total_transfer,false);


        $txd['_att_to'] = $data['merchantname'];
        $txd['_date_from'] = $data['from'];
        $txd['_date_to'] = $data['to'];
        $txd['_doc_date'] = $data['invdate'];
        $txd['_doc_no'] = $data['invdatenum'];
        $txd['_total_transfer_say'] = $this->number_words->to_words((double) $total_transfer ).' rupiah';

        $txd['_account'] = $data['bank_account'];
        $txd['_payable'] = $data['merchantname'];
        $txd['_sender'] = 'Administrator';

        $xls = $this->compile_xls($xls, $txd, FCPATH.'public/xlstemplate/codreportmonth.xlsx');


        if($pdf == 'pdf' || $pdf == 'xls'){

            if($pdf == 'pdf'){
                $html = $this->load->view('auth/pages/custom/print/codreportmonthprint',$data,true);
                $pdf_name = $pdffilename;
                $pdfbuf = pdf_create($html, $pdf_name,'A4','landscape', false);
                file_put_contents(FCPATH.'public/custom/'.$pdf_name.'.pdf', $pdfbuf);
            }else{

                $pdf_name = $pdffilename;

                $this->load->library('xlswrite');
                $xlswrite = new Xlswrite();
                $xlswrite->setActiveSheetIndex(0);


                $colnames = $this->config->item('xls_columns');

                $colindex = 0;

                //print_r($colnames);

                for($i = 0;$i < count($xls);$i++ ){
                    for($j = 0; $j < count($xls[$i]);$j++ ){
                        $cellname = $colnames[$j];
                        $cellname .= ($i+1);
                        //print $cellname .' = '.$xls[$i][$j]."\r\n";
                        $xlswrite->getActiveSheet()->SetCellValue($cellname, $xls[$i][$j] );
                    }
                }

                $xlswrite->xlsx(FCPATH.'public/custom/'.$pdf_name.'.xlsx');

            }


            $data['invdate'] = iddate($invdate);
            $data['invdatenum'] = date('dmY',mysql_to_unix($invdate));


            $invdata = array(
                'merchant_id'=>$type,
                'merchantname'=>$data['merchantname'],
                'merchantinfo'=>$data['merchantname'],
                'period_from'=>$data['from'],
                'period_to'=>$data['to'],
                'release_date'=>$invdate,
                'doc_type'=>'jexcodreportmonth',
                'doc_number'=>$pdffilename,
                'note'=>'',
                'filename'=>$pdffilename
            );

            $inres = $this->db->insert($this->config->item('docs_table'),$invdata);

            if($pdf == 'pdf'){
                return array(file_exists(FCPATH.'public/custom/'.$pdf_name.'.pdf'), $pdf_name.'.pdf');
            }else{
                return array(file_exists(FCPATH.'public/custom/'.$pdf_name.'.xlsx'), $pdf_name.'.xlsx');
            }

        }else if($pdf == 'print'){
            $this->load->view('auth/pages/custom/print/codreportmonthprint',$data); // Load the view
        }else{
            $this->ag_auth->view('custom/codreportmonthgenerator',$data); // Load the view
        }
    }

    public function genreport(){
        $type = null;
        $deliverytype = null;
        $status = null;
        $year = null;
        $scope = null;
        $par1 = null;
        $par2 = null;
        $par3 = null;
        $par4 = null;

        $type = $this->input->post('type');
        $deliverytype = $this->input->post('deliverytype');
        $status = $this->input->post('status');
        $year = $this->input->post('year');
        $scope = $this->input->post('scope');
        $par1 = $this->input->post('par1');
        $par2 = $this->input->post('par2');
        $par3 = $this->input->post('par3');
        $par4 = $this->input->post('par4');

        $result = $this->report($type ,$deliverytype,$status,$year, $scope, $par1, $par2, $par3,$par4);

        $result[0] = ($result[0])?'OK':'FAILED';

        print json_encode(array('result'=>$result[0], 'file'=>$result[1]));

    }


    //manifest

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

    public function compile_xls($data, $tmpl_data ,$template_path, $ext = '.xlsx'){
            $this->load->library('xls');
            $this->xls->setSkipEmpty(false);
            $xdata = $this->xls->load($template_path,$ext);

            //print_r($xdata['Worksheet']['cells']);

            $cells = $xdata['Worksheet']['cells'];

            $splice_at = 0;

            for($i = 0; $i < count($cells); $i++){
                for($j = 0;$j < count($cells[$i]);$j++){
                    if(isset($tmpl_data[$cells[$i][$j]])){
                        $cells[$i][$j] = $tmpl_data[$cells[$i][$j]];
                    }

                    if( $cells[$i][$j] == '_space_' ){
                        $cells[$i][$j] = '';
                    }

                    if($cells[$i][$j] == '_table_'){
                        $splice_at = $i;
                    }
                }
            }

            //print $splice_at;

            //if pos is start, just merge them
            if($splice_at == 0) {
                $cells = array_merge($cells, $data);
            } else {

                if($splice_at >= (count($cells) - 1 )) {
                    $cells = array_merge($cells, $data);
                } else {
                    //split into head and tail, then merge head+inserted bit+tail
                    $head = array_slice($cells, 0, $splice_at);
                    $tail = array_slice($cells, $splice_at + 1 );
                    $cells = array_merge($head, $data, $tail);
                }
            }


        return $cells;

    }

}

?>
