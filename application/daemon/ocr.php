<?php
require_once('/var/www/beta2/jayonadmin/application/libraries/TesseractOCR.php');

$files = glob( '/var/www/beta2/jayonadmin/public/pickup/*_address.jpg' );

foreach($files as $file){

    $tesseract = new TesseractOCR($file);
    $tesseract->setTempDir(realpath('temp'));
    $savefile = str_replace('pickup', 'ocr', $file);
    $savefile = str_replace('.jpg', '.txt', $savefile);
    if(file_exists($savefile)){
        $content = file_get_contents($savefile);
        if(trim($content) == ''){
            print $savefile." empty, retry\r\n";
            $result = $tesseract->recognize();
            file_put_contents($savefile, $result);
        }else{
            print $savefile." exists, skipping\r\n";
        }
    }else{
        $result = $tesseract->recognize();
        file_put_contents($savefile, $result);
    }
}

?>