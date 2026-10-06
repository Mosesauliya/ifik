<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Aset_model (Project IFIK)
 * Diadaptasi persis dari SCM FIK untuk mengelola stok aset laboratorium
 */
class Aset_model extends CI_Model {

    private $table = 'aset';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_aset_by_id($id_aset) {
        return $this->db->get_where($this->table, ['id_aset' => (int) $id_aset])->row();
    }

    public function reserve_stock($id_aset, $jumlah) {
        $jumlah = (int) $jumlah;
        if ($jumlah < 1) return false;

        $this->db->set('jumlah_tersedia', 'jumlah_tersedia - ' . $jumlah, false);
        $this->db->set('jumlah_reserved', 'jumlah_reserved + ' . $jumlah, false);
        $this->db->where('id_aset', (int) $id_aset);
        $this->db->where('jumlah_tersedia >=', $jumlah);
        $this->db->where('(jumlah_total - jumlah_reserved - jumlah_dipinjam) >= ' . $jumlah, null, false);
        $updated = $this->db->update($this->table);
        return $updated && $this->db->affected_rows() === 1;
    }

    public function release_reserved_stock($id_aset, $jumlah) {
        $jumlah = (int) $jumlah;
        if ($jumlah < 1) return false;

        $this->db->set('jumlah_tersedia', 'LEAST(jumlah_total, jumlah_tersedia + ' . $jumlah . ')', false);
        $this->db->set('jumlah_reserved', 'jumlah_reserved - ' . $jumlah, false);
        $this->db->where('id_aset', (int) $id_aset);
        $this->db->where('jumlah_reserved >=', $jumlah);
        $updated = $this->db->update($this->table);
        return $updated && $this->db->affected_rows() === 1;
    }

    public function reserved_to_borrowed($id_aset, $reserved_amount, $borrowed_amount) {
        $reserved_amount = (int) $reserved_amount;
        $borrowed_amount = (int) $borrowed_amount;
        if ($reserved_amount < 1 || $borrowed_amount < 0 || $borrowed_amount > $reserved_amount) return false;

        $released = $reserved_amount - $borrowed_amount;
        $this->db->set('jumlah_tersedia', 'LEAST(jumlah_total, jumlah_tersedia + ' . $released . ')', false);
        $this->db->set('jumlah_reserved', 'jumlah_reserved - ' . $reserved_amount, false);
        $this->db->set('jumlah_dipinjam', 'jumlah_dipinjam + ' . $borrowed_amount, false);
        $this->db->where('id_aset', (int) $id_aset);
        $this->db->where('jumlah_reserved >=', $reserved_amount);
        $this->db->where('(jumlah_dipinjam + ' . $borrowed_amount . ') <= jumlah_total', null, false);
        $updated = $this->db->update($this->table);
        return $updated && $this->db->affected_rows() === 1;
    }

    public function return_borrowed_stock($id_aset, $jumlah, $make_available = true) {
        $jumlah = (int) $jumlah;
        if ($jumlah < 1) return false;

        if ($make_available) {
            $this->db->set('jumlah_tersedia', 'LEAST(jumlah_total, jumlah_tersedia + ' . $jumlah . ')', false);
        }
        $this->db->set('jumlah_dipinjam', 'jumlah_dipinjam - ' . $jumlah, false);
        $this->db->where('id_aset', (int) $id_aset);
        $this->db->where('jumlah_dipinjam >=', $jumlah);
        $updated = $this->db->update($this->table);
        return $updated && $this->db->affected_rows() === 1;
    }

    public function increment_total_peminjaman($id_aset) {
        $this->db->set('total_peminjaman', 'total_peminjaman + 1', FALSE);
        $this->db->where('id_aset', (int) $id_aset);
        return $this->db->update($this->table);
    }
}
