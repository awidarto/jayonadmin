<?php
require_once('../libraries/TesseractOCR.php');
$files = glob( '../../public/pickup/*_address.jpg' );

foreach($files as $file){
    print($file);
    $tesseract = new TesseractOCR($file);
    $tesseract->setTempDir(realpath('temp'));
    echo $tesseract->recognize();
}

?>