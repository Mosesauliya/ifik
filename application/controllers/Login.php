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

	public function forgot_password()
	{
		$this->load->view('auth/forgot_password');
	}

	public function send_reset_link()
	{
		$email = $this->input->post('email');

		if (!empty($email)) {
			$data['success'] = 'Instruksi reset password telah dikirim ke email ' . htmlspecialchars($email);
			$this->load->view('auth/forgot_password', $data);
		} else {
			$data['error'] = 'Silakan masukkan email yang valid.';
			$this->load->view('auth/forgot_password', $data);
		}
	}
}

