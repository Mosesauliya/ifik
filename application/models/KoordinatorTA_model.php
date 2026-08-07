<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get List All Mahasiswa Mendaftar TA untuk Koordinator TA
    public function get_all_mahasiswa_ta() {
        if (!$this->db->table_exists('mahasiswa') || !$this->db->table_exists('pendaftaran_ta')) {
            return $this->get_mock_mahasiswa_list();
        }

        $this->db->select('m.*, p.status_approval_wali, p.status_approval_admin, p.status_approval_koor, p.status_approval_kk, p.current_stage, p.judul_1, p.created_at as tgl_daftar');
        $this->db->from('mahasiswa m');
        $this->db->join('pendaftaran_ta p', 'p.nim = m.nim', 'inner');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        if (empty($result)) {
            return $this->get_mock_mahasiswa_list();
        }

        return $result;
    }

    private function get_mock_mahasiswa_list() {
        return array(
            array('nim' => '1301210001', 'nama_depan' => 'Alif', 'nama_belakang' => 'Muzakky', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Pengembangan Sistem Informasi IFIK Berbasis Web', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210002', 'nama_depan' => 'Nazril', 'nama_belakang' => 'Fadillah', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Rancang Bangun Interface Portal Akademik DKV Telkom University', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210003', 'nama_depan' => 'Moses', 'nama_belakang' => 'Aulia', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Sistem Monitoring Bimbingan Tugas Akhir Real-time', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Rejected', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210004', 'nama_depan' => 'Rivan', 'nama_belakang' => 'Arshavin', 'konsentrasi_dkv' => 'Multimedia', 'judul_1' => 'Analisis Sentimen Feedback Perkuliahan Menggunakan NLP', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210005', 'nama_depan' => 'Budi', 'nama_belakang' => 'Pratama', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Deteksi Dini Penyakit Tanaman Menggunakan Deep Learning', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210006', 'nama_depan' => 'Siti', 'nama_belakang' => 'Nurhaliza', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Perancangan Visual Identity Produk UMKM Lokal', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210007', 'nama_depan' => 'Dewi', 'nama_belakang' => 'Lestari', 'konsentrasi_dkv' => 'Multimedia', 'judul_1' => 'Pengembangan Game 3D Edukasi Kebudayaan Nusantara', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Rejected', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210008', 'nama_depan' => 'Fajar', 'nama_belakang' => 'Nugraha', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Sistem Rekomendasi Tempat Wisata dengan Collaborative Filtering', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Approved', 'current_stage' => 'Selesai Approval'),
            array('nim' => '1301210009', 'nama_depan' => 'Gita', 'nama_belakang' => 'Gutawa', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Ilustrasi Buku Cerita Rakyat Berbasis Augmented Reality', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210010', 'nama_depan' => 'Hendra', 'nama_belakang' => 'Setiawan', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Aplikasi Manajemen Stok Barang Berbasis Cloud Native', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210011', 'nama_depan' => 'Indah', 'nama_belakang' => 'Permata', 'konsentrasi_dkv' => 'Multimedia', 'judul_1' => 'Animasi 2D Edukasi Pencegahan Cyberbullying pada Remaja', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210012', 'nama_depan' => 'Joko', 'nama_belakang' => 'Anwar', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Sistem Informasi Absensi Wajah Real-time Menggunakan OpenCV', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Rejected', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210013', 'nama_depan' => 'Kiki', 'nama_belakang' => 'Fatmala', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Redesain Kemasan Ramah Lingkungan untuk Produk Organik', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210014', 'nama_depan' => 'Lukman', 'nama_belakang' => 'Sardi', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Optimasi Routing Kurir Menggunakan Algoritma Genetika', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210015', 'nama_depan' => 'Maya', 'nama_belakang' => 'Septha', 'konsentrasi_dkv' => 'Multimedia', 'judul_1' => 'Virtual Tour Kampus Telkom University 360 Derajat', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Approved', 'current_stage' => 'Selesai Approval'),
            array('nim' => '1301210016', 'nama_depan' => 'Naufal', 'nama_belakang' => 'Zaky', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Predictive Maintenance Peralatan Pabrik dengan Machine Learning', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210017', 'nama_depan' => 'Olga', 'nama_belakang' => 'Lidya', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Studi Semiotika Visual Pada Kampanye Sosial Lingkungan', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210018', 'nama_depan' => 'Putri', 'nama_belakang' => 'Marino', 'konsentrasi_dkv' => 'Multimedia', 'judul_1' => 'Interactive Storytelling App Untuk Anak Usia Dini', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210019', 'nama_depan' => 'Qory', 'nama_belakang' => 'Gore', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Platform E-Commerce Produk Digital Berbasis Microservices', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Rejected', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210020', 'nama_depan' => 'Rizky', 'nama_belakang' => 'Febian', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Branding Festival Seni Kreatif Jawa Barat 2026', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210021', 'nama_depan' => 'Syafiq', 'nama_belakang' => 'Razaq', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Chatbot Pelayanan Akademik Kampus Menggunakan LLM', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210022', 'nama_depan' => 'Titi', 'nama_belakang' => 'Kamal', 'konsentrasi_dkv' => 'Multimedia', 'judul_1' => 'Efek Visual (VFX) Pada Film Pendek Genre Fantasi', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210023', 'nama_depan' => 'Umar', 'nama_belakang' => 'Wirahadikusumah', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Sistem Deteksi Anomali Lalu Lintas Jaringan dengan Deep Learning', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA'),
            array('nim' => '1301210024', 'nama_depan' => 'Vina', 'nama_belakang' => 'Panduwinata', 'konsentrasi_dkv' => 'Desain Grafis', 'judul_1' => 'Typographic Design System Untuk Platform Media Online', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Approved', 'status_approval_kk' => 'Pending', 'current_stage' => 'Ketua KK'),
            array('nim' => '1301210025', 'nama_depan' => 'Wira', 'nama_belakang' => 'Nagara', 'konsentrasi_dkv' => 'Informatika', 'judul_1' => 'Aplikasi Pemantauan Kesehatan Mental Berbasis Mobile', 'status_approval_wali' => 'Approved', 'status_approval_admin' => 'Approved', 'status_approval_koor' => 'Pending', 'status_approval_kk' => 'Pending', 'current_stage' => 'Koordinator TA')
        );
    }


    // Get Detail Mahasiswa dan Pendaftaran TA untuk Koordinator TA
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('mahasiswa') || !$this->db->table_exists('pendaftaran_ta')) {
            return $this->get_mock_detail($nim);
        }

        $this->db->select('m.*, p.*');
        $this->db->from('mahasiswa m');
        $this->db->join('pendaftaran_ta p', 'p.nim = m.nim', 'left');
        $this->db->where('m.nim', $nim);
        $query = $this->db->get();
        $row = $query->row_array();

        return $row ? $row : $this->get_mock_detail($nim);
    }

    private function get_mock_detail($nim) {
        $mock_names = array(
            '1301210001' => array('Rivan', 'Arshavin', 'Informatika', 'Pengembangan Sistem Informasi IFIK Berbasis Web', 'Pending'),
            '1301210002' => array('Sarah', 'Amalia', 'Desain Grafis', 'Rancang Bangun Interface Portal Akademik DKV Telkom University', 'Approved'),
            '1301210003' => array('Budi', 'Santoso', 'Informatika', 'Sistem Monitoring Bimbingan Tugas Akhir Real-time', 'Rejected'),
            '1301210004' => array('Anita', 'Wijaya', 'Multimedia', 'Analisis Sentimen Feedback Perkuliahan Menggunakan NLP', 'Pending')
        );

        $info = isset($mock_names[$nim]) ? $mock_names[$nim] : array('Mahasiswa', 'IFIK', 'Informatika', 'Pengembangan Sistem Informasi IFIK Berbasis Web', 'Pending');

        return array(
            'nim' => $nim,
            'nama_depan' => $info[0],
            'nama_belakang' => $info[1],
            'konsentrasi_dkv' => $info[2],
            'alamat' => 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung, Jawa Barat',
            'judul_1' => $info[3],
            'judul_2' => 'Rancang Bangun Modul Mahasiswa dan Dosen Wali IFIK',
            'judul_3' => 'Implementasi Workflow Approval Pendaftaran Tugas Akhir',
            'judul_en' => 'Development of Web-Based IFIK Information System',
            'status_approval_wali' => 'Approved',
            'catatan_wali' => 'Berkas persyaratan lengkap, judul disetujui untuk diproses ke tahap berikutnya.',
            'status_approval_admin' => 'Approved',
            'catatan_admin' => 'Verifikasi administrasi & SKS memenuhi syarat.',
            'status_approval_koor' => $info[4],
            'catatan_koor' => ($info[4] === 'Rejected') ? 'Judul 1 perlu diperjelas batasan masalah dan metodologi pengujian.' : '',
            'status_approval_kk' => 'Pending',
            'catatan_kk' => '',
            'current_stage' => ($info[4] === 'Approved') ? 'Ketua KK' : 'Koordinator TA'
        );
    }

    // Approval / Reject Pendaftaran TA oleh Koordinator TA
    public function update_approval_koor($nim, $status, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) return true;

        $data = array(
            'status_approval_koor' => $status, // 'Approved' / 'Rejected'
            'catatan_koor'         => $catatan,
            'updated_at'           => date('Y-m-d H:i:s')
        );

        // Jika disetujui, lanjut ke status berikutnya (Ketua KK)
        if ($status === 'Approved') {
            $data['current_stage'] = 'Ketua KK';
        } else {
            $data['current_stage'] = 'Koordinator TA';
        }

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }
}
