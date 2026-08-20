<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelolaruangan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url', 'file'));
        $this->load->model('Booking_model');
        
        // Autentikasi Khusus Admin System (role_id = 1)
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        
        if ($this->session->userdata('role_id') != 1) {
            $this->session->set_flashdata('error', 'Hanya Admin System yang dapat mengakses halaman Kelola Ruangan.');
            redirect('dashboard');
        }
    }

    public function index()
    {
        $data['title'] = 'Kelola Data Ruangan & Laboratorium';
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        
        // Fetch ketersediaan ruangan gabung dengan nama kategori
        $this->db->select('ruangan.*, kategori_ruangan.nama_kategori');
        $this->db->from('ruangan');
        $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
        $this->db->order_by('ruangan.id', 'ASC');
        $data['ruangan'] = $this->db->get()->result();

        $this->load->view('admin/ruangan/index', $data);
    }

    private function _upload_file($field_name, $upload_path, $allowed_types)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return null;
        }

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = '*'; // Allow all valid images & 3D binary files (.glb, .fbx, .gltf, .obj)
        $config['max_size']      = 102400; // Max 100MB for 3D models
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config, TRUE);

        if ($this->upload->do_upload($field_name)) {
            $data = $this->upload->data();
            return $upload_path . $data['file_name'];
        } else {
            log_message('error', 'Upload Error: ' . $this->upload->display_errors());
        }
        return null;
    }

    public function tambah()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $nama_ruangan    = $this->input->post('nama_ruangan', true);
        $kode_ruangan    = $this->input->post('kode_ruangan', true);
        $id_kategori     = $this->input->post('id_kategori', true);
        $kapasitas       = $this->input->post('kapasitas', true);
        $lokasi          = $this->input->post('lokasi', true);
        $status          = $this->input->post('status', true);
        $tagline         = $this->input->post('tagline', true);
        $jumlah_unit     = $this->input->post('jumlah_unit', true);
        $jam_operasional = $this->input->post('jam_operasional', true);
        $deskripsi       = $this->input->post('deskripsi', true);
        $spesifikasi_fasilitas = $this->input->post('spesifikasi_fasilitas', true);
        $tata_tertib     = $this->input->post('tata_tertib', true);

        if (empty($nama_ruangan) || empty($kode_ruangan) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan, Kode Ruangan, dan Kategori!']);
            return;
        }

        // Upload foto & 3D model jika diupload
        $foto_path     = $this->_upload_file('foto', 'uploads/ruangan/foto/', 'jpg|jpeg|png|webp|gif');
        $model_3d_path = $this->_upload_file('model_3d', 'uploads/ruangan/models/', 'glb|fbx|gltf|obj|bin');

        $data_ruangan = array(
            'nama_ruangan'          => $nama_ruangan,
            'kode_ruangan'          => strtoupper($kode_ruangan),
            'id_kategori'           => $id_kategori,
            'kapasitas'             => $kapasitas ? $kapasitas : 30,
            'lokasi'                => $lokasi ? $lokasi : 'Gedung Sebatik (FIK)',
            'status'                => $status ? $status : 'Tersedia',
            'tagline'               => $tagline,
            'jumlah_unit'          => $jumlah_unit,
            'jam_operasional'       => $jam_operasional,
            'deskripsi'             => $deskripsi,
            'spesifikasi_fasilitas' => $spesifikasi_fasilitas,
            'tata_tertib'           => $tata_tertib
        );

        if ($foto_path) {
            $data_ruangan['foto'] = $foto_path;
        }
        if ($model_3d_path) {
            $data_ruangan['model_3d'] = $model_3d_path;
        }

        $insert = $this->db->insert('ruangan', $data_ruangan);

        if ($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Ruangan baru & berkas berhasil ditambahkan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan ruangan baru.']);
        }
    }

    public function update($id)
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $nama_ruangan    = $this->input->post('nama_ruangan', true);
        $kode_ruangan    = $this->input->post('kode_ruangan', true);
        $id_kategori     = $this->input->post('id_kategori', true);
        $kapasitas       = $this->input->post('kapasitas', true);
        $lokasi          = $this->input->post('lokasi', true);
        $status          = $this->input->post('status', true);
        $tagline         = $this->input->post('tagline', true);
        $jumlah_unit     = $this->input->post('jumlah_unit', true);
        $jam_operasional = $this->input->post('jam_operasional', true);
        $deskripsi       = $this->input->post('deskripsi', true);
        $spesifikasi_fasilitas = $this->input->post('spesifikasi_fasilitas', true);
        $tata_tertib     = $this->input->post('tata_tertib', true);

        if (empty($nama_ruangan) || empty($kode_ruangan) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan, Kode Ruangan, dan Kategori!']);
            return;
        }

        // Upload foto & 3D model jika diubah
        $foto_path     = $this->_upload_file('foto', 'uploads/ruangan/foto/', 'jpg|jpeg|png|webp|gif');
        $model_3d_path = $this->_upload_file('model_3d', 'uploads/ruangan/models/', 'glb|fbx|gltf|obj|bin');

        $data_ruangan = array(
            'nama_ruangan'          => $nama_ruangan,
            'kode_ruangan'          => strtoupper($kode_ruangan),
            'id_kategori'           => $id_kategori,
            'kapasitas'             => $kapasitas,
            'lokasi'                => $lokasi,
            'status'                => $status,
            'tagline'               => $tagline,
            'jumlah_unit'          => $jumlah_unit,
            'jam_operasional'       => $jam_operasional,
            'deskripsi'             => $deskripsi,
            'spesifikasi_fasilitas' => $spesifikasi_fasilitas,
            'tata_tertib'           => $tata_tertib
        );

        if ($foto_path) {
            $data_ruangan['foto'] = $foto_path;
        }
        if ($model_3d_path) {
            $data_ruangan['model_3d'] = $model_3d_path;
        }

        $this->db->where('id', $id);
        $update = $this->db->update('ruangan', $data_ruangan);

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Data ruangan & berkas berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data ruangan.']);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');

        $this->db->where('id', $id);
        $delete = $this->db->delete('ruangan');

        if ($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus ruangan.']);
        }
    }
}
