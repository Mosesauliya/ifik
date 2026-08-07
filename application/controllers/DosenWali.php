<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('DosenWali_model');
        $this->load->helper(array('form', 'url'));
    }

    // Dashboard Dosen Wali: Daftar Mahasiswa Bimbingan Akademik
    public function index() {
        $nip_dosen = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19850101001'; // Mock NIP Dosen Wali
        $data['title'] = 'Dashboard Dosen Wali';
        $data['list_mahasiswa'] = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);

        $this->load->view('dosen_wali/dashboard', $data);
    }

    // Detail Mahasiswa Bimbingan & Approval
    public function detail_mahasiswa($nim) {
        $data['title'] = 'Detail Mahasiswa & Approval Pendaftaran TA';
        $data['detail'] = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);

        if ($this->input->post('action')) {
            $status  = $this->input->post('status'); // 'Approved' atau 'Rejected'
            $catatan = trim($this->input->post('catatan_wali') ?? '');

            if ($status === 'Rejected' && empty($catatan)) {
                $this->session->set_flashdata('error', 'Alasan penolakan / catatan revisi wajib diisi jika memilih Reject!');
                redirect('dosenwali/detail_mahasiswa/' . $nim);
                return;
            }

            $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);
            $this->session->set_flashdata('success', 'Status approval pendaftaran TA berhasil diperbarui!');
            redirect('dosenwali/detail_mahasiswa/' . $nim);
        }

        $this->load->view('dosen_wali/detail_mahasiswa', $data);
    }
}
