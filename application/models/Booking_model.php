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
        // Tampilkan semua kecuali yang Ditolak dan Dibatalkan
        $this->db->where_not_in('peminjaman.status', ['Ditolak', 'Dibatalkan']);
        $this->db->order_by('peminjaman.tanggal_mulai', 'ASC');
        $this->db->order_by('peminjaman.jam_mulai', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Check if a room booking conflicts with existing non-rejected/non-cancelled bookings
     */
    public function check_conflict($id_ruangan, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai, $ignore_id = null)
    {
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->where('peminjaman.id_ruangan', $id_ruangan);
        $this->db->where_not_in('peminjaman.status', ['Ditolak', 'Dibatalkan']);

        // Check date range overlap
        $this->db->where('peminjaman.tanggal_mulai <=', $tanggal_selesai);
        $this->db->where('peminjaman.tanggal_selesai >=', $tanggal_mulai);

        // Check time range overlap (strict overlap: start < existing_end AND end > existing_start)
        $this->db->where('peminjaman.jam_mulai <', $jam_selesai);
        $this->db->where('peminjaman.jam_selesai >', $jam_mulai);

        if ($ignore_id !== null) {
            $this->db->where('peminjaman.id !=', $ignore_id);
        }

        return $this->db->get()->result();
    }
}

