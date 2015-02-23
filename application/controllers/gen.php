<?php

class Gen extends Application
{
    public function __construct()
    {
        parent::__construct();
    }

    public function rev($month,$year){

        set_time_limit(0);

        $aggregate = array();

        $lookupname = array();

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for($d = 1; $d < $days + 1; $d++){
            $date = $year.'-'.str_pad($month,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT);
            //print($date);
            $this->db->select('assignment_date,merchant_id,m.merchantname as merchant_name, m.fullname as fullname,delivery_type,status,cod_cost,delivery_cost,total_price,chargeable_amount,actual_weight,application_id,application_key')
                ->join('members as m',$this->config->item('incoming_delivery_table').'.merchant_id=m.id','left')
                ->like('assignment_date',$date,'both')
                ->from($this->config->item('incoming_delivery_table'));

            $result = $this->db->get()->result();

            //print_r($result);
            //print $this->db->last_query();

            foreach($result as $r){

                $lookupname[$r->merchant_id] = $r->merchant_name.' - '.$r->fullname;

                $r->delivery_type = (strtoupper($r->delivery_type) == 'DO')? 'Delivery Only': $r->delivery_type ;

                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['count'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['count'] += 1;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['count'] = 1;
                }

                if($r->total_price == 0 || is_null($r->total_price) || $r->total_price == ''){
                    if($r->chargeable_amount > 0){
                        $r->total_price = $r->chargeable_amount;
                    }
                }

                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['total_price'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['total_price'] += $r->total_price;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['total_price'] = $r->total_price;
                }

                if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                    if($r->cod_cost == 0 || is_null($r->cod_cost) || $r->cod_cost == ''){
                        try{
                            //$app_id = get_app_id_from_key($r->application_key);
                            $r->cod_cost = get_cod_tariff($r->total_price,$r->application_id);
                        }catch(Exception $e){

                        }
                    }
                }else{
                    $r->cod_cost;
                }


                if($r->delivery_cost == 0 || is_null($r->delivery_cost) || $r->delivery_cost == ''){
                    try{
                        $r->delivery_cost = get_weight_tariff($r->actual_weight, $r->delivery_type ,$r->application_id);
                    }catch(Exception $e){

                    }
                }


                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['cod_cost'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['cod_cost'] += $r->cod_cost;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['cod_cost'] = $r->cod_cost;
                }


                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['delivery_cost'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['delivery_cost'] += $r->delivery_cost;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->status][$r->delivery_type]['delivery_cost'] = $r->delivery_cost;
                }
            }

            //print_r($aggregate);

            $existingdates = array_keys($aggregate);

            foreach($existingdates as $dt){
                $merchanttodate = array_keys($aggregate[$dt]);

                foreach($merchanttodate as $merchant){

                    //print_r($lookupname);

                    $existingstatus = array_keys($aggregate[$dt][$merchant]);

                    $merchant_name = $lookupname[$merchant];

                    foreach($existingstatus as $status){
                        $data = array();
                        $data['assignment_date'] = $dt;
                        $data['merchant_id'] = $merchant;
                        $data['merchant_name'] = $merchant_name;
                        $data['status'] = $status;

                        $data['do_delivery_cost'] = ( isset($aggregate[$dt][$merchant][$status]['Delivery Only']['delivery_cost']))?$aggregate[$dt][$merchant][$status]['Delivery Only']['delivery_cost']:0;
                        $data['do_cod_cost'] = ( isset($aggregate[$dt][$merchant][$status]['Delivery Only']['cod_cost']))?$aggregate[$dt][$merchant][$status]['Delivery Only']['cod_cost']:0;
                        $data['do_total_price'] = ( isset($aggregate[$dt][$merchant][$status]['Delivery Only']['total_price']))?$aggregate[$dt][$merchant][$status]['Delivery Only']['total_price']:0;
                        $data['do_count'] = ( isset($aggregate[$dt][$merchant][$status]['Delivery Only']['count']))?$aggregate[$dt][$merchant][$status]['Delivery Only']['count']:0;

                        $data['cod_delivery_cost'] = ( isset($aggregate[$dt][$merchant][$status]['COD']['delivery_cost']))?$aggregate[$dt][$merchant][$status]['COD']['delivery_cost']:0;

                        $data['cod_cod_cost'] = ( isset($aggregate[$dt][$merchant][$status]['COD']['cod_cost']))?$aggregate[$dt][$merchant][$status]['COD']['cod_cost']:0;
                        $data['cod_total_price'] = ( isset($aggregate[$dt][$merchant][$status]['COD']['total_price']))?$aggregate[$dt][$merchant][$status]['COD']['total_price']:0;
                        $data['cod_count'] = ( isset($aggregate[$dt][$merchant][$status]['COD']['count']))?$aggregate[$dt][$merchant][$status]['COD']['count']:0;

                        $data['ccod_delivery_cost'] = ( isset($aggregate[$dt][$merchant][$status]['CCOD']['delivery_cost']))?$aggregate[$dt][$merchant][$status]['CCOD']['delivery_cost']:0;
                        $data['ccod_cod_cost'] = ( isset($aggregate[$dt][$merchant][$status]['CCOD']['cod_cost']))?$aggregate[$dt][$merchant][$status]['CCOD']['cod_cost']:0;
                        $data['ccod_total_price'] = ( isset($aggregate[$dt][$merchant][$status]['CCOD']['total_price']))?$aggregate[$dt][$merchant][$status]['CCOD']['total_price']:0;
                        $data['ccod_count'] = ( isset($aggregate[$dt][$merchant][$status]['CCOD']['count']))?$aggregate[$dt][$merchant][$status]['CCOD']['count']:0;

                        $data['ps_delivery_cost'] = ( isset($aggregate[$dt][$merchant][$status]['PS']['delivery_cost']))?$aggregate[$dt][$merchant][$status]['PS']['delivery_cost']:0;
                        $data['ps_cod_cost'] = ( isset($aggregate[$dt][$merchant][$status]['PS']['cod_cost']))?$aggregate[$dt][$merchant][$status]['PS']['cod_cost']:0;
                        $data['ps_total_price'] = ( isset($aggregate[$dt][$merchant][$status]['PS']['total_price']))?$aggregate[$dt][$merchant][$status]['PS']['total_price']:0;
                        $data['ps_count'] = ( isset($aggregate[$dt][$merchant][$status]['PS']['count']))?$aggregate[$dt][$merchant][$status]['PS']['count']:0;

                        $ex = $this->db->where('assignment_date',$dt)
                            ->where('merchant_id',$merchant)
                            ->where('status',$status)
                            ->get($this->config->item('jayon_revenue_table'));

                        if($ex->num_rows() > 0){
                            $this->db->where('assignment_date',$dt)
                                ->where('merchant_id',$merchant)
                                ->where('status',$status)
                                ->update( $this->config->item('jayon_revenue_table'),$data );
                        }else{
                            $this->db->insert($this->config->item('jayon_revenue_table'),$data);
                        }

                    }

                }

            }

        }

        print json_encode(array('result'=>'OK'));

    }

    public function dev($month,$year){

        set_time_limit(0);

        $aggregate = array();

        $lookupname = array();

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for($d = 1; $d < $days + 1; $d++){

            $date = $year.'-'.str_pad($month,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT);
            //print($date);


            $sfrom = date('Y-m-d 00:00:00',strtotime($date));
            $sto = date('Y-m-d 23:59:59',strtotime($date));

            $column = 'assignment_date';
            $daterange = sprintf("`%s`between '%s%%' and '%s%%' ", $column, $sfrom, $sto);


            $this->db->select('assignment_date,device_id,d.identifier as device_name, delivery_type,status,cod_cost,delivery_cost,total_price,chargeable_amount,application_id,application_key')
                ->join('devices as d',$this->config->item('assigned_delivery_table').'.device_id=d.id','left')
                //->db->where($daterange, null, false);
                //->db->where($column.' != ','0000-00-00');
                ->like('assignment_date',$date,'before')
                ->from($this->config->item('incoming_delivery_table'));

            $result = $this->db->get()->result();

            foreach($result as $r){

                $lookupname[$r->device_id] = $r->device_name;

                $r->delivery_type = (strtoupper($r->delivery_type) == 'DO')? 'Delivery Only': $r->delivery_type ;

                if(isset($aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['count'])){
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['count'] += 1;
                }else{
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['count'] = 1;
                }

                if($r->total_price == 0 || is_null($r->total_price) || $r->total_price == ''){
                    if($r->chargeable_amount > 0){
                        $r->total_price = $r->chargeable_amount;
                    }
                }

                if(isset($aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['total_price'])){
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['total_price'] += $r->total_price;
                }else{
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['total_price'] = $r->total_price;
                }

                $app_id = $r->application_id;

                if($r->delivery_type == 'COD' || $r->delivery_type == 'CCOD'){
                    if($r->cod_cost == 0 || is_null($r->cod_cost) || $r->cod_cost == ''){
                        try{
                            //$app_id = get_app_id_from_key($r->application_key);
                            //$r->cod_cost = get_cod_tariff($r->total_price,$app_id);
                            $r->cod_cost = get_cod_tariff($r->total_price,$r->application_id);
                        }catch(Exception $e){

                        }
                    }
                }else{
                    $r->cod_cost;
                }


                if($r->delivery_cost == 0 || is_null($r->delivery_cost) || $r->delivery_cost == ''){
                    try{
                        $r->delivery_cost = get_weight_tariff($r->actual_weight, $r->delivery_type ,$r->application_id);
                        //$r->delivery_cost = get_cod_tariff($r->total_price,$r->application_id);
                    }catch(Exception $e){

                    }
                }

                if(isset($aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['cod_cost'])){
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['cod_cost'] += $r->cod_cost;
                }else{
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['cod_cost'] = $r->cod_cost;
                }


                if(isset($aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['delivery_cost'])){
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['delivery_cost'] += $r->delivery_cost;
                }else{
                    $aggregate[$r->assignment_date][$r->device_id][$r->status][$r->delivery_type]['delivery_cost'] = $r->delivery_cost;
                }
            }

            //print_r($aggregate);

            $existingdates = array_keys($aggregate);

            foreach($existingdates as $dt){
                $devicetodate = array_keys($aggregate[$dt]);

                foreach($devicetodate as $device){

                    //print_r($lookupname);

                    $existingstatus = array_keys($aggregate[$dt][$device]);

                    $device_name = $lookupname[$device];

                    foreach($existingstatus as $status){
                        $data = array();
                        $data['assignment_date'] = $dt;
                        $data['device_id'] = $device;
                        $data['device_name'] = $device_name;
                        $data['status'] = $status;

                        $data['do_delivery_cost'] = ( isset($aggregate[$dt][$device][$status]['Delivery Only']['delivery_cost']))?$aggregate[$dt][$device][$status]['Delivery Only']['delivery_cost']:0;
                        $data['do_cod_cost'] = ( isset($aggregate[$dt][$device][$status]['Delivery Only']['cod_cost']))?$aggregate[$dt][$device][$status]['Delivery Only']['cod_cost']:0;
                        $data['do_total_price'] = ( isset($aggregate[$dt][$device][$status]['Delivery Only']['total_price']))?$aggregate[$dt][$device][$status]['Delivery Only']['total_price']:0;
                        $data['do_count'] = ( isset($aggregate[$dt][$device][$status]['Delivery Only']['count']))?$aggregate[$dt][$device][$status]['Delivery Only']['count']:0;

                        $data['cod_delivery_cost'] = ( isset($aggregate[$dt][$device][$status]['COD']['delivery_cost']))?$aggregate[$dt][$device][$status]['COD']['delivery_cost']:0;
                        $data['cod_cod_cost'] = ( isset($aggregate[$dt][$device][$status]['COD']['cod_cost']))?$aggregate[$dt][$device][$status]['COD']['cod_cost']:0;
                        $data['cod_total_price'] = ( isset($aggregate[$dt][$device][$status]['COD']['total_price']))?$aggregate[$dt][$device][$status]['COD']['total_price']:0;
                        $data['cod_count'] = ( isset($aggregate[$dt][$device][$status]['COD']['count']))?$aggregate[$dt][$device][$status]['COD']['count']:0;

                        $data['ccod_delivery_cost'] = ( isset($aggregate[$dt][$device][$status]['CCOD']['delivery_cost']))?$aggregate[$dt][$device][$status]['CCOD']['delivery_cost']:0;
                        $data['ccod_cod_cost'] = ( isset($aggregate[$dt][$device][$status]['CCOD']['cod_cost']))?$aggregate[$dt][$device][$status]['CCOD']['cod_cost']:0;
                        $data['ccod_total_price'] = ( isset($aggregate[$dt][$device][$status]['CCOD']['total_price']))?$aggregate[$dt][$device][$status]['CCOD']['total_price']:0;
                        $data['ccod_count'] = ( isset($aggregate[$dt][$device][$status]['CCOD']['count']))?$aggregate[$dt][$device][$status]['CCOD']['count']:0;

                        $data['ps_delivery_cost'] = ( isset($aggregate[$dt][$device][$status]['PS']['delivery_cost']))?$aggregate[$dt][$device][$status]['PS']['delivery_cost']:0;
                        $data['ps_cod_cost'] = ( isset($aggregate[$dt][$device][$status]['PS']['cod_cost']))?$aggregate[$dt][$device][$status]['PS']['cod_cost']:0;
                        $data['ps_total_price'] = ( isset($aggregate[$dt][$device][$status]['PS']['total_price']))?$aggregate[$dt][$device][$status]['PS']['total_price']:0;
                        $data['ps_count'] = ( isset($aggregate[$dt][$device][$status]['PS']['count']))?$aggregate[$dt][$device][$status]['PS']['count']:0;

                        $ex = $this->db->where('assignment_date',$dt)
                            ->where('device_id',$device)
                            ->where('status',$status)
                            ->get($this->config->item('jayon_devicerecap_table'));

                        if($ex->num_rows() > 0){
                            $this->db->where('assignment_date',$dt)
                                ->where('device_id',$device)
                                ->where('status',$status)
                                ->update( $this->config->item('jayon_devicerecap_table'),$data );
                        }else{
                            $this->db->insert($this->config->item('jayon_devicerecap_table'),$data);
                        }

                    }

                }

            }

        }

        print json_encode(array('result'=>'OK'));

    }

}

?>