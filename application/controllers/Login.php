<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$this->load->view('auth/login');
	}

	public function authenticate()
	{
		$identity = $this->input->post('identity');
		$password = $this->input->post('password');

		if (!empty($identity) && !empty($password)) {
			// Successful login demo
			redirect('welcome');
		} else {
			$data['error'] = 'Silakan masukkan Email / ID dan Password yang valid.';
			$this->load->view('auth/login', $data);
		}
	}
}
