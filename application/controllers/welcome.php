<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function donothing(){
		return true;
	}

    public function testphone($num){

        print normalphone('(+6208675656464/02178078675/+44 ( 78 ) 77656465)');

        print "\r\n";

        print '<br />';

        print normalphone($num);
    }

    public function buyertransfer(){
        //$this->db->distinct();
        $this->db->select(
            'buyer_name
            ,buyerdeliveryzone
            ,buyerdeliverycity
            ,shipping_address
            ,phone
            ,mobile1
            ,mobile2
            ,recipient_name
            ,shipping_zip
            ,email
            ,delivery_id
            ,delivery_cost
            ,cod_cost
            ,delivery_type
            ,currency
            ,total_price
            ,chargeable_amount
            ,delivery_bearer
            ,cod_bearer
            ,cod_method
            ,ccod_method
            ,application_id
            ,buyer_id
            ,merchant_id
            ,merchant_trans_id
            ,courier_id
            ,device_id
            ,directions
            ,dir_lat
            ,dir_lon
            ,delivery_note
            ,latitude
            ,longitude
            ,created');
        $this->db->from($this->config->item('incoming_delivery_table'));
/*        $this->db->group_by(
            'buyer_name
            ,shipping_address
            ,phone
            ,mobile1
            ,mobile2
            ,recipient_name
            ,shipping_zip');
*/
        $res = $this->db->get();

        foreach($res->result_array() as $data){
            $this->save_buyer($data);
        }

    }

    private function save_buyer($ds){

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
        $bd['buyer_id']  =  $ds['buyer_id'];
        $bd['merchant_id']  =  $ds['merchant_id'];
        $bd['merchant_trans_id']  =  $ds['merchant_trans_id'];
        $bd['courier_id']  =  $ds['courier_id'];
        $bd['device_id']  =  $ds['device_id'];
        $bd['directions']  =  $ds['directions'];
        $bd['dir_lat']  =  $ds['dir_lat'];
        $bd['dir_lon']  =  $ds['dir_lon'];
        $bd['delivery_note']  =  $ds['delivery_note'];
        $bd['latitude']  =  $ds['latitude'];
        $bd['longitude']  =  $ds['longitude'];
        $bd['created']  =  $ds['created'];

        if($this->db->insert($this->config->item('jayon_buyers_table'),$bd)){
            return $this->db->insert_id();
        }else{
            return 0;
        }
    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */