<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
        // Load the URL helper if it's not loaded globally, since we need base_url()
        $this->load->helper('url');
        $this->load->model('Booking_model');
        
        $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        
        $this->db->where('status', 'Tersedia');
        $data['ruangan'] = $this->db->get('ruangan')->result();
        
        $this->load->view('dashboard/index', $data);
    }

    public function lab_detail($id = 'multimedia')
    {
        $this->load->helper('url');
        $data['lab_key'] = strtolower($id);
        $this->load->view('dashboard/lab_detail', $data);
    }

    public function ajukan_booking()
    {
        header('Content-Type: application/json');
        $this->load->model('Booking_model');
        
        $nama_lengkap = $this->input->post('nama_lengkap', true);
        $id_ruangan = $this->input->post('id_ruangan', true);
        $keterangan = $this->input->post('keterangan', true);
        $tanggal_range = $this->input->post('tanggal_peminjaman', true);
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);

        if(empty($nama_lengkap) || empty($id_ruangan) || empty($tanggal_range) || empty($jam_mulai) || empty($jam_selesai)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap lengkapi semua field wajib!']);
            return;
        }

        // Parse date range (format: "YYYY-MM-DD" atau "YYYY-MM-DD to YYYY-MM-DD")
        $tgl_arr = explode(' to ', $tanggal_range);
        $tanggal_mulai = $tgl_arr[0];
        $tanggal_selesai = isset($tgl_arr[1]) ? $tgl_arr[1] : $tgl_arr[0];

        // Validasi Role: non-admin hanya bisa 1 hari
        $role_id = $this->session->userdata('role_id');
        if ($role_id != 1) {
            $tanggal_selesai = $tanggal_mulai;
        }

        $data_peminjaman = array(
            'nama_lengkap' => $nama_lengkap,
            'id_ruangan' => $id_ruangan,
            'keterangan' => $keterangan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->Booking_model->insert_booking($data_peminjaman);

        if($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Booking berhasil diajukan dan menunggu persetujuan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengajukan booking']);
        }
    }
}
}
