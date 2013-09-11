<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * JEX API client 2.0
 *
 * JEX client to access JEX API v2 in RESTful way
 *
 * @author Andy Awidarto <andy.awidarto@gmail.com>
 */
class Jexclient {

    public $data = '';

    public $baseurl = '';

    public $endpoint = 'order';

    public $returnformat = 'json';

    public $method = 'POST';

    public $params = array();

    public function __construct()
    {

    }

    public function base($baseurl)
    {
        $this->baseurl = $baseurl;
        return $this;
    }

    public function data($data, $inputformat = 'array')
    {
        if($inputformat == 'array'){
            $this->data = json_encode($data);
        }else{
            $this->data = $data;
        }

        return $this;
    }

    public function addparam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function setmethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function format($returnformat)
    {
        $this->returnformat = $returnformat;
        return $this;
    }

    public function send()
    {

        $data_string = $this->data;

        $paramstring = null;
        if(count($this->params) > 1)
        {
            $paramstring = array();
            foreach ($this->params as $key => $value) {
                $paramstring[] = $key.'/'.$value;
            }
            $paramstring[] = 'format/'.$this->returnformat;
            $paramstring = implode('/',$paramstring);
        }else{
            $paramstring = 'format/'.$this->returnformat;
        }

        $full_url = $this->baseurl.'/'.$this->endpoint.'/'.$paramstring;

        $ch = curl_init($full_url);

        if($this->method == 'POST')
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }


}
