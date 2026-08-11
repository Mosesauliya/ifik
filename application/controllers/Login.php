<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	public function index()
	{
		// If user is already logged in, redirect to dashboard
		if ($this->session->userdata('logged_in')) {
			redirect('dashboard');
		}
		$this->load->view('auth/login');
	}

	public function authenticate()
	{
		$identity = trim($this->input->post('identity', true));
		$password = $this->input->post('password');

		if (empty($identity) || empty($password)) {
			$this->session->set_flashdata('error', 'Silakan masukkan Email dan Password yang valid.');
			redirect('login');
			return;
		}

		// Validate domain @telkomuniversity.ac.id
		if (!preg_match('/^[^\s@]+@telkomuniversity\.ac\.id$/i', $identity)) {
			$this->session->set_flashdata('error', 'Email harus menggunakan domain @telkomuniversity.ac.id');
			redirect('login');
			return;
		}

		// Fetch user from database
		$user = $this->User_model->get_by_email($identity);

		if ($user && $user->status === 'active') {
			// Verify bcrypt password hash
			if (password_verify($password, $user->password)) {
				// Set session data
				$session_data = array(
					'user_id'    => $user->id,
					'role_id'    => $user->role_id,
					'name'       => $user->name,
					'email'      => $user->email,
					'nidn_nim'   => $user->nidn_nim,
					'status'     => $user->status,
					'logged_in'  => TRUE
				);
				$this->session->set_userdata($session_data);

				// Redirect to dashboard
				redirect('dashboard');
				return;
			}
		}

		// Invalid credentials or inactive status
		$this->session->set_flashdata('error', 'Email atau password salah.');
		redirect('login');
	}

	public function logout()
	{
		$this->session->unset_userdata(array('user_id', 'role_id', 'name', 'email', 'nidn_nim', 'status', 'logged_in'));
		$this->session->sess_destroy();
		redirect('login');
	}

	public function forgot_password()
	{
		$this->load->view('auth/forgot_password');
	}

	public function send_reset_link()
	{
		$email = trim($this->input->post('email', true));

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
