<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImportEmail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'form', 'html'));
    }

    /**
     * Display the Admin Email Import & Token Generator Dashboard
     */
    public function index() {
        $data['title'] = 'Admin - Import Email & Token Dispatcher';
        $this->load->view('admin/import_email', $data);
    }
}
