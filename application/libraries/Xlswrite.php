<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Excel Import Library
 *
 * Wrapper class for generating a QR code using the Google Charts API.
 *
 * @author Eric Famiglietti <eric.famiglietti@gmail.com>
 * @link   http://ericfamiglietti.com/
 */
require_once('PHPExcel.php');
require_once('PHPExcel/IOFactory.php');
require_once('PHPExcel/Writer/Excel2007.php');
require_once('PHPExcel/Writer/Excel5.php');

class Xlswrite extends PHPExcel{

    private $CI;                // CodeIgniter instance

    public $workbook;

    public $controller = 'project';

    public $sheetname = 'ORDERS';

    function __construct()
    {
        $this->CI = &get_instance();
        parent::__construct();
    }

    public function xls($filename){
        $objWriter = new PHPExcel_Writer_Excel5($this);
        $objWriter->save($filename);
    }

    public function xlsx($filename){
        $objWriter = new PHPExcel_Writer_Excel2007($this);
        $objWriter->save($filename);
    }

    public function setController($controller){
        $this->controller = $controller;
    }

    public function toPHPdate($date){
        return PHPExcel_Shared_Date::ExcelToPHP($date);
    }

}
