<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_kategori()
    {
        return $this->db->get('kategori_ruangan')->result();
    }

    public function get_ruangan_by_kategori($id_kategori)
    {
        $this->db->where('id_kategori', $id_kategori);
        $this->db->where('status', 'Tersedia');
        return $this->db->get('ruangan')->result();
    }

    public function get_all_slot_waktu()
    {
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('slot_waktu')->result();
    }

    public function get_all_peminjaman()
    {
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->order_by('peminjaman.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function insert_booking($data)
    {
        $this->db->trans_start();

        // Insert into peminjaman
        $this->db->insert('peminjaman', $data);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update_status($id, $status, $alasan = null)
    {
        $data = array('status' => $status);
        if ($alasan !== null) {
            $data['alasan_penolakan'] = $alasan;
        }

        $this->db->where('id', $id);
        return $this->db->update('peminjaman', $data);
    }

    public function delete_booking($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('peminjaman');
    }

    public function get_approved_bookings()
    {
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->like('peminjaman.status', 'Disetujui');
        $this->db->order_by('peminjaman.tanggal_mulai', 'ASC');
        $this->db->order_by('peminjaman.jam_mulai', 'ASC');
        return $this->db->get()->result();
    }
}
