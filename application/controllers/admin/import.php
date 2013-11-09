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
        $config['upload_path'] = './upload/';
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

            //var_dump($xdata);

            $headidx = 1;

            foreach($xdata['cells'] as $dt){
                if(in_array('id', $dt)){
                    $head = $dt;
                    break;
                }
                $headidx++;
            }

            $orderdata = array();

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

            for($i = $headidx; $i < $xdata['numRows'];$i++){
                $temp = $xdata['cells'][$i];
                $line = array();
                for($j = 0;$j < $xdata['numCols'];$j++){

                    if(in_array($head[$j], $datetimefields )){
                        $line[ $head[$j] ] = date('Y-m-d H:i:s', $this->xls->toPHPdate($temp[$j]) ) ;
                    }elseif(in_array($head[$j], $datefields )){
                        $line[ $head[$j] ] = date('Y-m-d', $this->xls->toPHPdate($temp[$j]) ) ;
                    }else{
                        $line[ $head[$j] ] = $temp[$j];
                    }
                }

                $orderdata[] = $line;

            }

            $orderdata = array('head'=>$head,'data'=>$orderdata);

            $jsonfile = date('d-m-Y-H-i-s',time());

            file_put_contents('./json/'.$jsonfile.'.json', json_encode($orderdata));

            redirect('admin/import/preview/'.$jsonfile, 'location' );

        }

    }

    public function preview($jsonfile)
    {
        $json = file_get_contents('./json/'.$jsonfile.'.json');

        //print_r(json_decode($json));

        $json = json_decode($json,true);

        //print_r($json);

        $this->load->library('table');


        $heads = array_merge(array('<input type="checkbox" id="select_all">'),$json['head']);

        $this->table->set_heading($heads);

        $cells = array();

        $idx = 0;

        foreach($json['data'] as $cell){
            $cells[] = array_merge(array('<input type="checkbox" class="selector" id="'.$idx.'" value="'.$idx.'">'),$cell);
            $idx++;
        }

        $page['cells'] = $cells;


        $this->breadcrumb->add_crumb('Import','admin/import');

        $page['page_title'] = 'Import Preview';
        $this->ag_auth->view('import/preview',$page); // Load the view
    }

}

?>