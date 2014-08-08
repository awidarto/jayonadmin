<?php

class Img extends Application
{
    public function __construct()
    {
        parent::__construct();
    }

    public function qr($string){
        $string = base64_decode($string);
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

}

?>