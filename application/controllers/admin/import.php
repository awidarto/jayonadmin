<?php

class Import extends Application
{

    public function __construct()
    {
        parent::__construct();
        $this->ag_auth->restrict('admin'); // restrict this controller to admins only
        $this->table_tpl = array(
            'table_open' => '<table border="0" cellpadding="4" cellspacing="0" class="dataTable">'
        );
        $this->table->set_template($this->table_tpl);

        $this->breadcrumb->add_crumb('Home','admin/dashboard');

    }

    public function index()
    {
        $this->breadcrumb->add_crumb('Import','admin/import');

        $page['page_title'] = 'Import';
        $this->ag_auth->view('import/upload',$page); // Load the view
    }

    public function upload()
    {
        $config['upload_path'] = FCPATH.'upload/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = '1000';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());

            print_r($error);
        }
        else
        {
            $this->load->library('xls');

            $data = $this->upload->data();

            $xdata = $this->xls->load($data['full_path'],$data['file_ext']);

            $merchant_id = $this->input->post('merchant_id');
            $merchant_name = $this->input->post('merchant_name');

            //var_dump($xdata);

            //exit();

            $sheetdata = array();

            $sheetdata['merchant_id'] = $merchant_id;
            $sheetdata['merchant_name'] = $merchant_name;

            foreach($xdata as $sheet=>$row){

                $headidx = 1;
                $dataidx = 2;

                $xdata = $row;

                /*
                foreach($xdata['cells'] as $dt){
                    if(in_array('id', $dt)){
                        $head = $dt;
                        break;
                    }
                    $headidx++;
                }
                */

                $label = $row['cells'][$headidx - 1];
                $head = $row['cells'][$headidx];

                //print_r($head);


                $datetimefields = array(
                    'created',
                    'ordertime',
                    'buyerdeliverytime',
                    'assigntime',
                    'deliverytime'
                );

                $datefields = array(
                    'assignment_date'
                );

                //print_r($row);

                $orderdata = array();

                for($i = $dataidx; $i < $row['numRows'];$i++){
                    $temp = $row['cells'][$i];
                    $line = array();
                    for($j = 0;$j < count($head);$j++){

                        /*
                        if(in_array($head[$j], $datetimefields )){
                            $line[ $head[$j] ] = date('Y-m-d h:i:s', $this->xls->toPHPdate($temp[$j]) ) ;
                        }elseif(in_array($head[$j], $datefields )){
                            $line[ $head[$j] ] = date('Y-m-d', $this->xls->toPHPdate($temp[$j]) ) ;
                        }else{
                        */
                            $line[ $head[$j] ] = $temp[$j];
                        //}
                    }

                    $orderdata[] = $line;

                }

                $orderdata = array('label'=>$label,'head'=>$head,'data'=>$orderdata);

                $sheetdata[$sheet] = $orderdata;

            }

            //print_r($sheetdata);

            $jsonfile = date('d-m-Y-h-i-s',time());

            file_put_contents(FCPATH.'json/'.$jsonfile.'.json', json_encode($sheetdata));

            redirect('admin/import/preview/'.$merchant_id.'/'.$jsonfile, 'location' );

        }

    }

    public function preview($merchant_id,$jsonfile)
    {
        $json = file_get_contents(FCPATH.'json/'.$jsonfile.'.json');

        //print_r(json_decode($json));

        $json = json_decode($json,true);

        //print_r($json);

        $merchant_id = $json['merchant_id'];
        $merchant_name = $json['merchant_name'];

        unset($json['merchant_id']);
        unset($json['merchant_name']);

        $tables = array();

        foreach($json as $sheet=>$rows){

            $this->load->library('table');

            $heads = array_merge(array('<input type="checkbox" id="select_all">'),$rows['head']);

            $this->table->set_heading(array('data'=>'SHEET : '.$sheet,'colspan'=>100));
            $this->table->set_subheading($heads);

            $cells = array();

            $idx = 0;

            foreach($rows['data'] as $cell){
                $cells[] = array_merge(array('<input type="checkbox" class="selector" id="'.$idx.'" value="'.$idx.'">'),$cell);
                $idx++;
            }

            $tables[$sheet] = $this->table->generate($cells);

        }

        $page['tables'] = $tables;

        $page['merchant_id'] = $merchant_id;
        $page['merchant_name'] = $merchant_name;

        //$page['app_select'] = $app_select;

        $this->breadcrumb->add_crumb('Import','admin/import');

        $page['page_title'] = 'Import Preview';
        $this->ag_auth->view('import/preview',$page); // Load the view
    }

}

?>