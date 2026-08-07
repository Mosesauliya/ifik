<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get List Mahasiswa Bimbingan Wali
    public function get_mahasiswa_bimbingan($nip_dosen) {
        if (!$this->db->table_exists('mahasiswa')) {
            return array(
                array(
                    'nim' => '1301210001',
                    'nama_depan' => 'Rivan',
                    'nama_belakang' => 'Arshavin',
                    'judul_1' => 'Pengembangan Sistem Informasi IFIK Berbasis Web',
                    'status_approval_wali' => 'Pending',
                    'current_stage' => 'Dosen Wali'
                )
            );
        }
        $this->db->select('m.*, p.status_approval_wali, p.status_approval_admin, p.status_approval_koor, p.status_approval_kk, p.judul_1, p.created_at as tgl_daftar');
        $this->db->from('mahasiswa m');
        $this->db->join('pendaftaran_ta p', 'p.nim = m.nim', 'left');
        $this->db->where('m.nip_dosen_wali', $nip_dosen);
        $query = $this->db->get();
        return $query->result_array();
    }

    // Get Detail Mahasiswa dan Pendaftaran TA
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('mahasiswa')) {
            return array(
                'nim' => $nim,
                'nama_depan' => 'Rivan',
                'nama_belakang' => 'Arshavin',
                'konsentrasi_dkv' => 'Informatika',
                'alamat' => 'Jl. Telekomunikasi No. 1, Bandung',
                'judul_1' => 'Pengembangan Sistem Informasi IFIK Berbasis Web',
                'judul_2' => 'Rancang Bangun Modul Mahasiswa dan Dosen Wali IFIK',
                'judul_3' => 'Implementasi Workflow Approval Pendaftaran Tugas Akhir',
                'judul_en' => 'Development of Web-Based IFIK Information System',
                'status_approval_wali' => 'Pending',
                'catatan_wali' => ''
            );
        }
        $this->db->select('m.*, p.*');
        $this->db->from('mahasiswa m');
        $this->db->join('pendaftaran_ta p', 'p.nim = m.nim', 'left');
        $this->db->where('m.nim', $nim);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Approval / Reject Pendaftaran TA oleh Dosen Wali
    public function update_approval_wali($nim, $status, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) return true;
        $data = array(
            'status_approval_wali' => $status, // 'Approved' / 'Rejected'
            'catatan_wali'         => $catatan,
            'updated_at'           => date('Y-m-d H:i:s')
        );

        // Jika disetujui, lanjut ke status berikutnya (Admin Layanan)
        if ($status === 'Approved') {
            $data['current_stage'] = 'Admin Layanan';
        }

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }
}
