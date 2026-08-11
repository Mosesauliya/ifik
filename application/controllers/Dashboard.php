<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
        // Load the URL helper if it's not loaded globally, since we need base_url()
        $this->load->helper('url');
        $this->load->model('Booking_model');
        
        $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $this->db->where('status', 'Tersedia');
        $data['ruangan'] = $this->db->get('ruangan')->result();
        
        $this->load->view('dashboard/index', $data);
    }

    public function ajukan_booking()
    {
        $this->load->model('Booking_model');
        $this->load->model('User_model');
        
        $nama_lengkap = $this->input->post('nama_lengkap', true);
        $id_ruangan = $this->input->post('id_ruangan', true);
        $tanggal_peminjaman = $this->input->post('tanggal_peminjaman', true); // Hanya 1 hari
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);
        $keterangan = $this->input->post('keterangan', true);

        if(empty($nama_lengkap) || empty($id_ruangan) || empty($tanggal_peminjaman) || empty($jam_mulai) || empty($jam_selesai)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap lengkapi semua field wajib!']);
            return;
        }

        // Validasi ke Database Users
        if (!$this->User_model->check_user_exists($nama_lengkap)) {
            echo json_encode(['status' => 'error', 'message' => 'Validasi Gagal: Nama atau NIM/NIDN tidak terdaftar di dalam sistem.']);
            return;
        }

        $data_peminjaman = array(
            'nama_lengkap' => $nama_lengkap,
            'id_ruangan' => $id_ruangan,
            'keterangan' => $keterangan,
            'tanggal_mulai' => $tanggal_peminjaman,
            'tanggal_selesai' => $tanggal_peminjaman, // Sesuai aturan publik 1 hari
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->Booking_model->insert_booking($data_peminjaman);

        if($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Booking berhasil diajukan dan menunggu persetujuan Admin!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengajukan booking']);
        }
    }
}
