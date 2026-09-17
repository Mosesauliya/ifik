<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifikasi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->helper('url');
    }

    /**
     * Halaman Publik Surat Resmi Peminjaman Ruangan (Langsung Format Surat PDF Resmi)
     */
    public function surat($id)
    {
        $data['booking'] = $this->Booking_model->get_booking_by_id($id);

        if (!$data['booking']) {
            show_404();
            return;
        }

        $cleanId = preg_replace('/[^0-9]/', '', (string)$data['booking']->id);
        if (empty($cleanId)) $cleanId = '0001';

        $data['title'] = 'Surat Resmi Peminjaman Ruangan - ' . ($data['booking']->kode_ruangan ?? 'IFIK');
        $data['nomor_surat'] = 'SURAT/LAB-IFIK/' . date('Y', strtotime($data['booking']->created_at)) . '/' . sprintf('%04d', (int)$cleanId);
        $data['qr_data'] = site_url('verifikasi/surat/' . $id);
        $data['penandatangan'] = $this->Booking_model->get_penandatangan($data['booking']->status);

        $this->load->view('kaur/surat_resmi', $data);
    }

    /**
     * Tampilan Dokumen Asli / Cetak Surat Resmi (PDF Printable View)
     */
    public function cetak($id)
    {
        $data['booking'] = $this->Booking_model->get_booking_by_id($id);

        if (!$data['booking']) {
            show_404();
            return;
        }

        $cleanId = preg_replace('/[^0-9]/', '', (string)$data['booking']->id);
        if (empty($cleanId)) $cleanId = '0001';

        $data['title'] = 'Surat Resmi Peminjaman Ruangan - ' . ($data['booking']->kode_ruangan ?? 'IFIK');
        $data['nomor_surat'] = 'SURAT/LAB-IFIK/' . date('Y', strtotime($data['booking']->created_at)) . '/' . sprintf('%04d', (int)$cleanId);
        $data['qr_data'] = site_url('verifikasi/surat/' . $id);
        $data['penandatangan'] = $this->Booking_model->get_penandatangan($data['booking']->status);

        $this->load->view('kaur/surat_resmi', $data);
    }
}
