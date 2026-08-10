<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
        // Load the URL helper if it's not loaded globally, since we need base_url()
        $this->load->helper('url');
        
		$this->load->view('dashboard/index');
	}

	public function lab_detail($id = 'multimedia')
	{
		$this->load->helper('url');
		$data['lab_key'] = strtolower($id);
		$this->load->view('dashboard/lab_detail', $data);
	}
}
