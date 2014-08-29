<?php

require_once('TesseractOCR.php');

class Ocr{

    $ocr = null;

    public function __construct($file){
        $this->ocr = new TesseractOCR($file);
        $this->ocr->setTempDir(realpath('temp'));
    }

    public function execute(){
        $result = $this->ocr->recognize();
        return $result;
    }

}


?>