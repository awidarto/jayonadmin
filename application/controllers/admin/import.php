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

    }

}

?>