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
			redirect('dashboard');
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

	public function onboarding()
	{
		$data['title'] = 'Onboarding & Lengkapi Akun — FIK Portal';
		$data['dosen_wali_list'] = array(
			'19850101' => 'Dr. Ir. Ahmad Yani, M.T.',
			'19880205' => 'Prof. Siti Aminah, Ph.D.',
			'19900312' => 'Hendra Kusuma, S.T., M.T.',
			'19920720' => 'Dra. Nurul Hidayah, M.Ds.',
			'19941108' => 'Rian Pratama, S.Kom., M.T.',
			'19960415' => 'Maya Indriani, S.Ds., M.A.'
		);
		$data['konsentrasi_list'] = array(
			'Desain Komunikasi Visual',
			'Informatika (Teknologi Informasi)',
			'Rekayasa Perangkat Lunak',
			'Desain Produk',
			'Desain Interior',
			'Kriya Tekstil & Fashion'
		);
		$this->load->view('auth/onboarding', $data);
	}
}


