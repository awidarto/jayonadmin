<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rest extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */


    public function index()
    {
        $rest = $this->jexclient
                    ->base('http://localhost/jayonadmindev/api/v2')
                    ->endpoint('test')
                    ->data(array('hello'=>true,'kawan'=>'semua'))
                    ->format('json')
                    ->send();
        print $rest;
    }

    public function order()
    {
        $rest = $this->jexclient
                    ->base('http://localhost/jayonadmindev/api/v2')
                    ->endpoint('order/key/456456356356')
                    ->data(array('hello'=>true,'kawan'=>'semua'))
                    ->format('json')
                    ->send();
        print $rest;

    }

    public function posttest(){

        $data = array("name" => "Hagrid", "age" => "36");
        $data_string = json_encode($data);

        $ch = curl_init('http://localhost/jayonadmindev/api/v2/test.json');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);

        print $result;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */