<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorKP extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Kp_model');
        $this->load->helper(array('form', 'url', 'text'));
        $this->load->library('session');

        // Deteksi request AJAX
        $isAjax = $this->input->is_ajax_request() ||
                  (strpos($this->uri->uri_string(), 'ajax_') !== false) ||
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // 1. Cek status login
        if (!$this->session->userdata('logged_in')) {
            if ($isAjax) {
                $this->output->set_status_header(401)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Sesi berakhir. Silakan login kembali.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Koordinator KP.');
            redirect('login');
            return;
        }

        // 2. Cek Role: Role 23 (Koordinator KP), Role 1 (Admin System), atau Role 22 (Super Admin)
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 23 && $role_id !== 1 && $role_id !== 22) {
            if ($isAjax) {
                $this->output->set_status_header(403)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Halaman ini khusus untuk Koordinator KP dan Admin.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus untuk Koordinator KP dan Admin.');
            redirect('login');
            return;
        }
    }

    /**
     * Index: Default redirect ke menu Plotting Pembimbing KP
     */
    public function index() {
        $this->plotting();
    }

    /**
     * Halaman Utama: Plotting Dosen Pembimbing 1 KP
     * Mahasiswa yang tampil HANYA yang persyaratannya sudah di-ACC oleh Admin Layanan (LAA) di Tab 1.
     */
    public function plotting() {
        $data_plotting = $this->Kp_model->get_mahasiswa_for_plotting_kp();
        $dosen_list    = $this->Kp_model->get_dosen_list_kp();
        $sub_tab       = $this->input->get('subtab') ?: 'belum_diplot';

        $data['title']        = 'Plotting Pembimbing 1 Kerja Praktik (KP) - Koordinator KP';
        $data['sub_tab']      = $sub_tab;
        $data['belum_diplot'] = $data_plotting['belum_diplot'];
        $data['sudah_diplot'] = $data_plotting['sudah_diplot'];
        $data['total']        = $data_plotting['total'];
        $data['dosen_list']   = $dosen_list;

        $this->load->view('koordinator_kp/plotting', $data);
    }

    /**
     * AJAX Endpoint: Simpan Plotting Pembimbing 1
     */
    public function ajax_plot_pembimbing() {
        $nim              = trim((string)$this->input->post('nim'));
        $dosen_identifier = trim((string)$this->input->post('dosen_identifier'));

        if (empty($nim)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'NIM Mahasiswa wajib disertakan.']));
            return;
        }

        if (empty($dosen_identifier)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Silakan pilih Dosen Pembimbing 1 terlebih dahulu.']));
            return;
        }

        $res = $this->Kp_model->plot_pembimbing_kp($nim, $dosen_identifier);

        $this->output->set_content_type('application/json')
            ->set_output(json_encode($res));
    }

    /**
     * AJAX Endpoint: Ambil data realtime Plotting KP
     */
    public function ajax_get_plotting_data() {
        $data_plotting = $this->Kp_model->get_mahasiswa_for_plotting_kp();
        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status'       => true,
                'belum_diplot' => $data_plotting['belum_diplot'],
                'sudah_diplot' => $data_plotting['sudah_diplot'],
                'total'        => $data_plotting['total']
            ]));
    }
}
