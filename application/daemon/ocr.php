<?php
require_once('/var/www/beta2/jayonadmin/libraries/TesseractOCR.php');

$files = glob( '/var/www/beta2/jayonadmin/public/pickup/*_address.jpg' );

foreach($files as $file){
    print($file);
    $tesseract = new TesseractOCR($file);
    $tesseract->setTempDir(realpath('temp'));
    $savefile = str_replace('pickup', 'ocr', $file);
    $savefile = str_replace('.jpg', '.txt', $savefile);
    if(file_exists($savefile)){

    }else{
        $result = $tesseract->recognize();
        file_put_contents($savefile, $result);
    }
}

?>