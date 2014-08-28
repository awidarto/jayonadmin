<?php
require_once('../libraries/TesseractOCR.php');
$files = glob( realpath('../../public/pickup/*_address.jpg') );

foreach($files as $file){
    print($file);
    $tesseract = new TesseractOCR('images/some-words.jpg');
    echo $tesseract->recognize();
}

?>