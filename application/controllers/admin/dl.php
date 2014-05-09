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

        $mtab = $this->config->item('assigned_delivery_table');
        $columns = 'assignment_date,
            buyerdeliveryzone,
            delivery_id,
            buyer_name,
            shipping_address,
            '.$mtab.'.phone,
            status,
            '.$mtab.'.merchant_id,
            merchant_trans_id';


        $search = false;
        foreach ($filter as $f) {
            if(!preg_match('/^Search/i', $f['value'])){
                $field = str_replace('search_', '', $f['name']);
                if($field == 'device'){
                    $this->db->like('d.identifier', $f['value'],'both');
                }elseif($field == 'merchant'){
                    $this->db->like('m.merchantname', $f['value'],'both');
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

        $this->db->group_start()
            ->where('status',$this->config->item('trans_status_admin_courierassigned'))
            ->or_where('status',$this->config->item('trans_status_mobile_pickedup'))
            ->or_where('status',$this->config->item('trans_status_mobile_enroute'))
            ->or_where('pending_count >', 0)
            ->group_end();
        $data = $this->db->order_by('assignment_date','desc')
            ->order_by('device','asc')
            ->order_by('courier','asc')
            ->order_by('buyerdeliverycity','asc')
            ->order_by('buyerdeliveryzone','asc')
            //->order_by($sort,$sort_dir)
            ->get($this->config->item('assigned_delivery_table'));

        $sdata = $data->result_array();

        $fname = date('Y-m-d',time()).'_inprogress.csv';

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

    public function delivered(){

        $filter = $this->input->post('datafilter');
        $sorts = $this->input->post('sort');

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
                }else{
                    $this->db->like($field, $f['value'],'both');
                }

                $search = true;
            }
        }

        $this->db->select($this->config->item('assigned_delivery_table').'.*,m.merchantname as merchant,a.application_name as app_name,d.identifier as device,c.fullname as courier');
        //$this->db->join('members as b',$this->config->item('assigned_delivery_table').'.buyer_id=b.id','left');
        $this->db->join('members as m',$this->config->item('assigned_delivery_table').'.merchant_id=m.id','left');
        $this->db->join('applications as a',$this->config->item('assigned_delivery_table').'.application_id=a.id','left');
        $this->db->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left');
        $this->db->join('couriers as c',$this->config->item('assigned_delivery_table').'.courier_id=c.id','left');

        if($search){
            $this->db->and_();
        }

        $this->db->group_start()
            ->where('status',$this->config->item('trans_status_admin_courierassigned'))
            ->or_where('status',$this->config->item('trans_status_mobile_pickedup'))
            ->or_where('status',$this->config->item('trans_status_mobile_enroute'))
            ->or_where('pending_count >', 0)
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

}

?>