<?php

class Img extends Application
{
    public function __construct()
    {
        parent::__construct();
    }

    public function qr($string,$type = 'base64'){
        if($type == 'base64'){
            $string = base64_decode($string);
        }
        $params['data'] = $string;
        $params['level'] = 'L';
        $params['size'] = 10;
        header("Content-Type: image/png");
        $this->ciqrcode->generate($params);
    }

    public function barcode($text){

        $this->load->library('barcode');

        $text = base64_decode($text);

        $barcode = new Barcode();
        $barcode->make($text,'code128',40, 'horizontal' ,true);
        return $barcode->render('jpg',$text);
    }

    public function qrtest($in){

        $string = array();

        $string[] = '000245-29-102014-00066702';
        $string[] = 'BCV098765342';
        $string[] = 'BCV098765342|000245-29-102014-00066702';
        $string[] = '000245-29-102014-00066702|BCV098765342';

        $params['data'] = $string[$in];
        $params['level'] = 'L';
        $params['size'] = 10;
        header("Content-Type: image/png");
        $this->ciqrcode->generate($params);

    }

}

?>