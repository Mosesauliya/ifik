<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends CI_Controller {

	public function detail($id = 1)
	{
        $this->load->helper('url');
		$this->load->view('news/detail');
	}
}
