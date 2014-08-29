<?php

require_once('TesseractOCR.php');

class Ocr{

    public var $ocr = null;

    public function __construct($file){
        $this->ocr = new TesseractOCR($file);
        $this->ocr->setTempDir(realpath(APPPATH.'daemon/temp'));
    }

    public function execute(){
        $result = $this->ocr->recognize();
        return $result;
    }

}


?>