<?php

require_once('TesseractOCR.php');

class Ocr{

    var $ocrinstance;

    public function __construct($file){
        $this->ocrinstance = new TesseractOCR($file);
        $this->ocrinstance->setTempDir( '/var/www/beta2/jayonadmin/'.APPPATH.'temp/' );
    }

    public function execute(){
        $result = $this->ocrinstance->recognize();
        return $result;
    }

}


?>