<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Excel Import Library
 *
 * Wrapper class for generating a QR code using the Google Charts API.
 *
 * @author Eric Famiglietti <eric.famiglietti@gmail.com>
 * @link   http://ericfamiglietti.com/
 */
class Xls {

    public $workbook;

    public $controller = 'project';

    public $sheetname = 'ORDERS';

    function __construct()
    {
        require_once('PHPExcel.php');
        require_once('PHPExcel/IOFactory.php');
    }

    public function setController($controller){
        $this->controller = $controller;
    }

    public function toPHPdate($date){
        return PHPExcel_Shared_Date::ExcelToPHP($date);
    }

    public function load($filename, $ext = '.xls'){

        if( $ext == '.xlsx'){
            $objReader = new PHPExcel_Reader_Excel2007();
        }else{
            $objReader = new PHPExcel_Reader_Excel5();
        }

        //$objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);

        $xls_array = array();
        $cell_array = array();

        $numrows = 0;
        $numcols = 0;

        $last_col = '';
        $last_row = '';

        $sheets = array();

        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            //echo '- ' . $worksheet->getTitle() . "\r\n";
            $cell_array = array();

            $numrows = 0;
            $numcols = 0;

            $last_col = '';
            $last_row = '';


            $sheetname = $worksheet->getTitle();

            //if(preg_match('/^'.$sheetname.'/', $worksheet->getTitle()) OR preg_match('/Data/', $worksheet->getTitle())){

            foreach ($worksheet->getRowIterator() as $row) {
                //echo '    - Row number: ' . $row->getRowIndex() . "\r\n";

                $last_row = $row->getRowIndex();


                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true); // Loop all cells, even if it is not set
                $numcols = 0;
                foreach ($cellIterator as $cell) {
                    $cell_array[$numrows][$numcols] = $cell->getCalculatedValue() ;
                    $numcols++;
                    $last_col = $cell->getCoordinate();
                }

                $numrows++;
            }

            //}

            $xls_array['numRows'] = $numrows - 1;
            $xls_array['numCols'] = $numcols - 1;

            $xls_array['lastRow'] = $last_row;
            $xls_array['lastCol'] = $last_col;

            $xls_array['cells'] = $cell_array;

            $sheets[$sheetname] = $xls_array;

        }




        return $sheets;
    }

    public function xload($filename){
        $objPHPExcel = PHPExcel_IOFactory::load($filename);

        return $objPHPExcel;
    }

    function xxload()
    {
        // Path to the template file
        $template_location = 'resources/template.xls';

        $xls_reader = PHPExcel_IOFactory::createReader('Excel5');
        $this->workbook = $xls_reader->load($template_location);

        var_dump($this->workbook); // Yea, successfully load the data
    }

}
