<?php

class Gen extends Application
{
    public function __construct()
    {
        parent::__construct();
    }

    public function rev($month,$year){

        $aggregate = array();

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        for($d = 1; $d < $days + 1; $d++){
            $date = $year.'-'.str_pad($month,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT);
            print($date);
            $this->db->select('assignment_date,merchant_id,delivery_type,status,cod_cost,delivery_cost,total_price')
                ->like('assignment_date',$date,'before')
                ->from($this->config->item('incoming_delivery_table'));

            $result = $this->db->get()->result();

            foreach($result as $r){

                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['count'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['count'] += 1;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['count'] = 1;
                }

                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['total_price'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['total_price'] += $r->total_price;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['total_price'] = $r->total_price;
                }


                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['cod_cost'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['cod_cost'] += $r->cod_cost;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['cod_cost'] = $r->cod_cost;
                }


                if(isset($aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['delivery_cost'])){
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['delivery_cost'] += $r->delivery_cost;
                }else{
                    $aggregate[$r->assignment_date][$r->merchant_id][$r->delivery_type][$r->status]['delivery_cost'] = $r->delivery_cost;
                }
            }

            print_r($aggregate);

        }
    }

}

?>