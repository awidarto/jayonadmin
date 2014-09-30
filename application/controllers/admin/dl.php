<?php

/**
*
*/
class Dl extends Application
{

    function __construct()
    {
        parent::__construct();        # code...
    }

    public function dispatch(){

        $filter = $this->input->post('datafilter');
        $sorts = $this->input->post('sort');


        $mtab = $this->config->item('assigned_delivery_table');

        //generated columns
        $columns = 'assignment_date,
            buyerdeliveryzone,
            delivery_id,
            buyer_name,
            shipping_address,
            '.$mtab.'.phone as phone,
            status,
            '.$mtab.'.merchant_id as merchant_id,
            merchant_trans_id,
            delivery_type,
            delivery_cost,
            cod_cost,
            total_price';

        $columns .= ',buyerdeliverycity,
            latitude,
            longitude,
            '.$mtab.'.id as id,
            recipient_name,
            '.$mtab.'.mobile1 as mobile1,
            '.$mtab.'.mobile2 as mobile2,
            weight,
            width,
            length,
            height,
            application_id,
            pending_count,
            chargeable_amount';

        // sorting columns
        $sort = $sorts[0];
        $sort_dir = $sorts[1];

        $sortcolumns = array(
            'assignment_date',
            'buyerdeliveryzone',
            'delivery_id',
            'device',
            'courier',
            'buyer',
            'shipping_address',
            'phone',
            '',
            'status',
            'merchant_id',
            'merchant_trans_id'
            );

        $search = false;
        foreach ($filter as $f) {
            if(!preg_match('/^Search/i', $f['value'])){
                $field = str_replace('search_', '', $f['name']);
                if($field == 'device'){
                    $this->db->like('d.identifier', $f['value'],'both');
                }elseif($field == 'merchant'){
                    $this->db->like('m.merchantname', $f['value'],'both');
                }elseif($field == 'buyer'){
                    $this->db->like('buyer_name', $f['value'],'both');
                }else{
                    $this->db->like($field, $f['value'],'both');
                }

                $search = true;
            }
        }

        $this->db->select($columns.',m.merchantname as merchant,a.application_name as app_name,d.identifier as device,c.fullname as courier');
        //$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
        $this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
        $this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
        $this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
        $this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');

        if($search){
            $this->db->and_();
        }

        /*
        $this->db->group_start()
            ->where('status',$this->config->item('trans_status_admin_courierassigned'))
            ->or_where('status',$this->config->item('trans_status_mobile_pickedup'))
            ->or_where('status',$this->config->item('trans_status_mobile_enroute'))
            ->or_where('pending_count >', 0)
            ->group_end();
        */

        $this->db->group_start()
                ->where('status',$this->config->item('trans_status_admin_courierassigned'))
                ->or_where('status',$this->config->item('trans_status_mobile_pickedup'))
                ->or_where('status',$this->config->item('trans_status_mobile_enroute'))
                ->or_()
                    ->group_start()
                        ->where('status',$this->config->item('trans_status_new'))
                        ->where('pending_count >', 0)
                    ->group_end()
            ->group_end();

        $data = $this->db
            ->order_by('assignment_date','desc')
            ->order_by('device','asc')
            ->order_by('courier','asc')
            ->order_by('buyerdeliverycity','asc')
            ->order_by('buyerdeliveryzone','asc')
            ->get($this->config->item('assigned_delivery_table'));

        /*
        $data = $this->db->order_by('assignment_date','desc')
            ->order_by('device','asc')
            ->order_by('courier','asc')
            ->order_by('buyerdeliverycity','asc')
            ->order_by('buyerdeliveryzone','asc')
            //->order_by($sort,$sort_dir)
            ->get($this->config->item('assigned_delivery_table'));
        */

            $last_query = $this->db->last_query();

        $sdata = $data->result_array();

        //print_r($sdata);

        $aadata = array();

        $bardate = '';
        $bardev = '';
        $barcourier = '';
        $barcity = '';
        $barzone = '';

        $num = 0;

        foreach($sdata as $key)
        {
            $num++;

            $datefield = ($bardate == $key['assignment_date'])?'':$key['assignment_date'];
            $devicefield = ($bardev == $key['device'])?'':$key['device'];

            //$courierlink = '<span class="change_courier" id="'.$key['assignment_date'].'_'.$key['device_id'].'_'.$key['courier_id'].'" style="cursor:pointer;text-decoration:underline;" >'.$key['courier'].'</span>';

            $courierfield = ($barcourier == $key['courier'] && $barzone == $key['buyerdeliveryzone'])?'':$key['courier'];
            $cityfield = ($barcity == $key['buyerdeliverycity'])?'':$key['buyerdeliverycity'];
            $zonefield = ($barzone == $key['buyerdeliveryzone'])?'':$key['buyerdeliveryzone'];


            $lat = ($key['latitude'] == 0)? 'Set Loc':$key['latitude'];
            $lon = ($key['longitude'] == 0)? '':$key['longitude'];

            $style = 'style="cursor:pointer;padding:2px;display:block;"';
            $class = ($lat == 'Set Loc')?' red':'';

            $direction = '<span id="'.$key['id'].'" '.$style.' class="locpick'.$class.'">'.$lat.' '.$lon.'</span>';

            $thumbnail = get_thumbnail($key['delivery_id'],'thumb_multi');

            $thumbstat = colorizestatus($key['status']);
            if($key['status'] == 'pending'){
                $thumbstat .= '<br />'.$thumbnail;
            }

            $phones = ($key['phone'] == '')?'':$key['phone'];
            $phones .= ($key['mobile1'] == '')?'':';'.$key['mobile1'];
            $phones .= ($key['mobile2'] == '')?'':';'.$key['mobile2'];

            $aadata[] = array(
                $num,
                $datefield,
                $devicefield,
                $courierfield,
                $key['delivery_type'],
                ($key['delivery_type'] == 'COD')?(double)$key['chargeable_amount']:'',
                $cityfield,
                $zonefield,
                $key['merchant'],
                $key['buyer_name'],
                $key['recipient_name'],
                $key['shipping_address'].'<br />'.$direction,
                $phones,
                $key['delivery_id'],
                $this->hide_trx($key['merchant_trans_id']),
                $key['delivery_cost'],
                ($key['delivery_type'] == 'COD')?$key['cod_cost']:'',
                $key['width'].' x '.$key['height'].' x '.$key['length'],
                (double)$key['width']*(double)$key['height']*(double)$key['length'],
                get_weight_range($key['weight'],$key['application_id']),
                $key['status'],
                $key['pending_count']
            );

            $bardate = $key['assignment_date'];
            $bardev =   $key['device'];
            $barcourier =   $key['courier'];
            $barcity =  $key['buyerdeliverycity'];
            $barzone =  $key['buyerdeliveryzone'];


        }


        $fname = date('Y-m-d',time()).'_inprogress.csv';

        $fp = fopen(FCPATH.'public/dl/'.$fname, 'w');

        $headrow = array(
            '#',
            'Delivery Date',
            'Device',
            'Courier',
            'Type',
            'COD Value',
            'City',
            'Zone',
            'Merchant',
            'Buyer',
            'Delivered To',
            'Shipping Address',
            'Phone',
            'Delivery ID',
            'No Kode Penjualan Toko',
            'Delivery Fee',
            'COD Surcharge',
            'W x H x L',
            'Volume',
            'Weight Range',
            'Status',
            'Pending'
        );


        $head = 0;
        foreach ($aadata as $fields) {
            if($head == 0){
                $heads = $headrow;
                fputcsv($fp, $heads, ',' , '"');
                fputcsv($fp, $fields, ',' , '"');
            }else{
                fputcsv($fp, $fields, ',' , '"');
            }
            $head++;
        }

        fclose($fp);

        $urlcsv = base_url().'admin/dl/out/'.$fname;
        $result = array( 'status'=>'OK','data'=>array('urlcsv'=>$urlcsv), 'q'=>$last_query );
        print json_encode($result);
    }

    public function delivered(){

        $filter = $this->input->post('datafilter');
        $sorts = $this->input->post('sort');

        $mtab = $this->config->item('assigned_delivery_table');

        $columns = $mtab.'.id as id,delivery_type,
                buyerdeliverycity,
                buyerdeliveryzone,
                buyer_name,
                recipient_name,
                shipping_address,
                '.$mtab.'.phone,
                '.$mtab.'.mobile1,
                '.$mtab.'.mobile2,
                delivery_note,
                status,
                device_id,
                deliverytime,
                chargeable_amount,
                delivery_id,
                merchant_trans_id,
                delivery_cost,
                delivery_type,cod_cost,
                reschedule_ref,
                revoke_ref';

        $sort = $sorts[0];
        $sort_dir = $sorts[1];

        $search = false;
        foreach ($filter as $f) {
            if(!preg_match('/^Search/i', $f['value'])){
                $field = str_replace('search_', '', $f['name']);
                if($field == 'device'){
                    $this->db->like('d.identifier', $f['value'],'both');
                }elseif($field == 'merchant'){
                    $this->db->like('m.merchantname', $f['value'],'both');
                }elseif($field == 'buyer'){
                    $this->db->like('buyer_name', $f['value'],'both');
                }else{
                    $this->db->like($field, $f['value'],'both');
                }

                $search = true;
            }
        }

        $this->db->select($columns.',m.merchantname as merchant,a.application_name as app_name,d.identifier as device,c.fullname as courier');
        //$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
        $this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
        $this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
        $this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
        $this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');

        if($search == false){
            $this->db->like($mtab.'.assignment_date', date('Y-m',time()),'after' );
            $this->db->and_();
        }

        if($search){
            $this->db->and_();
        }

        $this->db->group_start()
            ->where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_delivered'))
            ->or_where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_revoked'))
            ->or_where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_noshow'))
            ->group_end();

        $data = $this->db->order_by('assignment_date','desc')
            ->order_by('device','asc')
            ->order_by('courier','asc')
            ->order_by('buyerdeliverycity','asc')
            ->order_by('buyerdeliveryzone','asc')
            //->order_by($sort,$sort_dir)
            ->get($this->config->item('assigned_delivery_table'));

        $sdata = $data->result_array();

        $fname = date('Y-m-d',time()).'_delivery_status.csv';

        $fp = fopen(FCPATH.'public/dl/'.$fname, 'w');

        $head = 0;
        foreach ($sdata as $fields) {
            if($head == 0){
                $heads = array_keys($fields);
                fputcsv($fp, $heads, ',' , '"');
                fputcsv($fp, $fields, ',' , '"');
            }else{
                fputcsv($fp, $fields, ',' , '"');
            }
            $head++;
        }

        fclose($fp);

        $urlcsv = base_url().'admin/dl/out/'.$fname;
        $result = array( 'status'=>'OK','data'=>array('urlcsv'=>$urlcsv) );
        print json_encode($result);



    }

    public function out($filename){

        $csv = file_get_contents(FCPATH.'public/dl/'.$filename);

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Expires: 0');
        header('Pragma: public');

        $fh = @fopen( 'php://output', 'w' );

        fwrite($fh, $csv);

        // Close the file
        fclose($fh);
        // Make sure nothing else is sent, our file is done
        exit;

    }

    public function hide_trx($trx_id){
        if(preg_match('/^TRX_/', $trx_id) || preg_match('/^UP_/', $trx_id)){
            return '';
        }else{
            return $trx_id;
        }
    }


}

?>