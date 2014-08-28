<?php
require_once('../libraries/TesseractOCR.php');
$files = glob( '../../public/pickup/*_address.jpg' );

foreach($files as $file){
    print($file);
    $tesseract = new TesseractOCR($file);
    $tesseract->setTempDir(realpath('temp'));
    $result = $tesseract->recognize();
    $savefile = str_replace('pickup', 'ocr', $file);
    $savefile = str_replace('txt', 'jpg', $savefile);
    file_put_contents($savefile, $result);
}

?>