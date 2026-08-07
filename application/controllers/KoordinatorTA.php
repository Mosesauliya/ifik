<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KoordinatorTA_model');
        $this->load->helper(array('form', 'url', 'text'));
    }

    // Dashboard Koordinator TA: Daftar Mahasiswa Mendaftar Tugas Akhir
    public function index() {
        $nip_koor = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19800202002'; // Mock NIP Koordinator TA
        $data['title'] = 'Dashboard Koordinator TA';
        $data['nip_koor'] = $nip_koor;
        $data['list_mahasiswa'] = $this->KoordinatorTA_model->get_all_mahasiswa_ta();

        $this->load->view('koordinator_ta/dashboard', $data);
    }

    // Detail Mahasiswa & Approval Koordinator TA
    public function detail_mahasiswa($nim) {
        $data['title'] = 'Detail & Approval Koordinator TA';
        $data['detail'] = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);

        if ($this->input->post('action')) {
            $status  = $this->input->post('status'); // 'Approved' atau 'Rejected'
            $catatan = trim($this->input->post('catatan_koor') ?? '');

            if ($status === 'Rejected' && empty($catatan)) {
                $this->session->set_flashdata('error', 'Catatan revisi / alasan penolakan wajib diisi jika memilih Reject!');
                redirect('koordinatorta/detail_mahasiswa/' . $nim);
                return;
            }

            $this->KoordinatorTA_model->update_approval_koor($nim, $status, $catatan);
            $this->session->set_flashdata('success', 'Status approval Koordinator TA berhasil diperbarui!');
            redirect('koordinatorta/detail_mahasiswa/' . $nim);
        }

        $this->load->view('koordinator_ta/detail_mahasiswa', $data);
    }
}
