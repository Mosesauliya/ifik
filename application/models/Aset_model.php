<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Aset_model (Project IFIK)
 * Mengelola data aset/barang laboratorium dan sinkronisasi stok peminjaman
 */
class Aset_model extends CI_Model {

    private $table = 'aset';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil semua aset
     */
    public function get_all_aset() {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, "#ea5b1a" AS warna, "bi-box" AS icon');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->order_by('aset.id_aset', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Ambil semua aset dengan urutan tertentu
     */
    public function get_all_aset_ordered($order_by = 'id_aset', $order = 'DESC') {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, "#ea5b1a" AS warna, "bi-box" AS icon');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->order_by($order_by, $order);
        return $this->db->get()->result();
    }

    /**
     * Pencarian ringan untuk combobox distribusi.
     */
    public function search_for_distribution($keyword = '', $limit = 20) {
        $keyword = trim((string) $keyword);
        $this->db->select('aset.id_aset, aset.id_ruangan, aset.nama_aset, aset.kode_aset, aset.jumlah_tersedia, aset.kondisi, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.jumlah_tersedia >', 0);
        if ($keyword !== '') {
            $this->db->group_start()
                ->like('aset.nama_aset', $keyword)
                ->or_like('aset.kode_aset', $keyword)
                ->or_like('COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan', $keyword)
                ->group_end();
        }
        $this->db->order_by('aset.id_aset', 'DESC');
        $this->db->limit(max(1, min(30, (int) $limit)));
        return $this->db->get()->result();
    }

    /**
     * Pencarian ringan untuk combobox maintenance.
     */
    public function search_for_maintenance($keyword = '', $limit = 20) {
        $keyword = trim((string) $keyword);
        $this->db->select('aset.id_aset, aset.id_ruangan, aset.nama_aset, aset.kode_aset, aset.jumlah_total, aset.kondisi, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        if ($keyword !== '') {
            $this->db->group_start()
                ->like('aset.nama_aset', $keyword)
                ->or_like('aset.kode_aset', $keyword)
                ->or_like('COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan', $keyword)
                ->group_end();
        }
        $this->db->order_by('aset.id_aset', 'DESC');
        $this->db->limit(max(1, min(30, (int) $limit)));
        return $this->db->get()->result();
    }

    /**
     * Indeks ringan untuk pencarian barang pada beranda.
     */
    public function get_dashboard_search_index() {
        $this->db->select('
            aset.id_aset,
            aset.id_ruangan,
            aset.nama_aset,
            aset.kode_aset,
            aset.deskripsi,
            aset.jumlah_total,
            aset.jumlah_tersedia,
            COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan
        ');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.nama_aset IS NOT NULL', null, false);
        $this->db->where('aset.nama_aset !=', '');
        $this->db->order_by('aset.nama_aset', 'ASC');
        $this->db->order_by('COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Ambil barang yang sering dipinjam
     */
    public function get_popular_items($limit = 10, $offset = 0) {
        $this->db->select('
            aset.*, 
            COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, 
            "#ea5b1a" AS warna, 
            "bi-box" AS icon,
            (SELECT COUNT(*) FROM peminjaman_barang WHERE id_aset = aset.id_aset) as total_peminjaman
        ');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.jumlah_total >', 0);
        $this->db->order_by('total_peminjaman', 'DESC');
        $this->db->order_by('aset.id_aset', 'DESC'); 
        $this->db->order_by('aset.nama_aset', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * Count popular items untuk pagination
     */
    public function count_popular_items() {
        $this->db->from($this->table);
        $this->db->where('jumlah_total >', 0);
        return $this->db->count_all_results();
    }

    /**
     * Ambil aset berdasarkan ID
     */
    public function get_aset_by_id($id) {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, "#ea5b1a" AS warna, "bi-box" AS icon');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.id_aset', (int) $id);
        return $this->db->get()->row();
    }

    /**
     * Ambil sekaligus kunci stok aset selama transaksi serah terima.
     */
    public function get_aset_by_id_for_update($id) {
        return $this->db
            ->query('SELECT * FROM `' . $this->table . '` WHERE `id_aset` = ? LIMIT 1 FOR UPDATE', [(int) $id])
            ->row();
    }

    /**
     * Ambil aset berdasarkan ruangan
     */
    public function get_aset_by_ruangan($id_ruangan, $limit = null, $exclude_id = null) {
        $this->db->select('
            aset.*, 
            COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, 
            "#ea5b1a" AS warna,
            "bi-box" AS icon,
            (SELECT COUNT(*) FROM peminjaman_barang WHERE id_aset = aset.id_aset) as total_peminjaman
        ');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.id_ruangan', $id_ruangan);
        
        if ($exclude_id !== null) {
            $this->db->where('aset.id_aset !=', $exclude_id);
        }
        
        $this->db->order_by('aset.nama_aset', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Ambil aset terkait (berdasarkan kategori yang sama)
     */
    public function get_related_aset($id_ruangan, $id_aset, $limit = 4) {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, "#ea5b1a" AS warna, "bi-box" AS icon');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.id_ruangan', $id_ruangan);
        $this->db->where('aset.id_aset !=', $id_aset);
        $this->db->where('aset.jumlah_total >', 0);
        $this->db->order_by('RAND()');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Search/cari aset
     */
    public function search_aset($keyword) {
        $this->db->select('
            aset.*, 
            COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan, 
            "#ea5b1a" AS warna,
            "bi-box" AS icon,
            (SELECT COUNT(*) FROM peminjaman_barang WHERE id_aset = aset.id_aset) as total_peminjaman
        ');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        
        $this->db->group_start();
        $this->db->like('aset.nama_aset', $keyword);
        $this->db->or_like('aset.kode_aset', $keyword);
        $this->db->or_like('COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan', $keyword);
        $this->db->or_like('aset.deskripsi', $keyword);
        $this->db->group_end();
        
        $this->db->order_by('aset.nama_aset', 'ASC');
        $this->db->limit(50);
        return $this->db->get()->result();
    }

    /**
     * Ambil riwayat peminjaman aset
     */
    public function get_riwayat_peminjaman($id_aset, $limit = 10) {
        $this->db->select('
            peminjaman_barang.*, 
            peminjam.nama_peminjam, 
            peminjam.nim_nip
        ');
        $this->db->from('peminjaman_barang');
        $this->db->join('peminjam', 'peminjam.id_peminjam = peminjaman_barang.id_peminjam', 'left');
        $this->db->where('peminjaman_barang.id_aset', $id_aset);
        $this->db->order_by('peminjaman_barang.tanggal_pinjam', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Update jumlah aset tersedia saat peminjaman
     */
    public function update_jumlah_tersedia($id_aset, $jumlah) {
        return $this->reserve_stock($id_aset, $jumlah);
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

    /**
     * Kembalikan jumlah aset tersedia saat pengembalian
     */
    public function kembalikan_jumlah_tersedia($id_aset, $jumlah) {
        return $this->return_borrowed_stock($id_aset, $jumlah, true);
    }

    /**
     * Increment total peminjaman
     */
    public function increment_total_peminjaman($id_aset) {
        $this->db->set('total_peminjaman', 'total_peminjaman + 1', FALSE);
        $this->db->where('id_aset', (int) $id_aset);
        return $this->db->update($this->table);
    }

    /**
     * Update kondisi aset
     */
    public function update_kondisi($id_aset, $kondisi) {
        $this->db->where('id_aset', (int) $id_aset);
        return $this->db->update($this->table, ['kondisi' => $kondisi]);
    }

    /**
     * ============== FITUR GAMBAR ==============
     */

    /**
     * Upload gambar aset
     */
    public function upload_gambar($file) {
        $config['upload_path'] = './uploads/aset/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;
        
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('gambar')) {
            $upload_data = $this->upload->data();
            return 'uploads/aset/' . $upload_data['file_name'];
        } else {
            return false;
        }
    }

    /**
     * Hapus gambar aset
     */
    public function hapus_gambar($gambar_path) {
        if (!empty($gambar_path) && file_exists('./' . $gambar_path)) {
            return unlink('./' . $gambar_path);
        }
        return false;
    }

    /**
     * Update gambar aset
     */
    public function update_gambar($id_aset, $gambar_baru) {
        $aset = $this->get_aset_by_id($id_aset);
        
        if ($aset && !empty($aset->gambar)) {
            $this->hapus_gambar($aset->gambar);
        }
        
        $this->db->where('id_aset', (int) $id_aset);
        return $this->db->update($this->table, ['gambar' => $gambar_baru]);
    }

    /**
     * Hapus gambar aset tanpa menghapus data aset
     */
    public function hapus_gambar_aset($id_aset) {
        $aset = $this->get_aset_by_id($id_aset);
        
        if ($aset && !empty($aset->gambar)) {
            $this->hapus_gambar($aset->gambar);
            
            $this->db->where('id_aset', (int) $id_aset);
            return $this->db->update($this->table, ['gambar' => null]);
        }
        
        return false;
    }

    /**
     * Ambil semua aset yang memiliki gambar
     */
    public function get_aset_with_gambar() {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.gambar IS NOT NULL');
        $this->db->where('aset.gambar !=', '');
        $this->db->order_by('aset.id_aset', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Ambil aset berdasarkan gambar (untuk galeri)
     */
    public function get_aset_galeri($limit = 12, $offset = 0) {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.gambar IS NOT NULL');
        $this->db->where('aset.gambar !=', '');
        $this->db->order_by('aset.id_aset', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    /**
     * Hitung total aset yang memiliki gambar (untuk pagination galeri)
     */
    public function count_aset_galeri() {
        $this->db->from($this->table);
        $this->db->where('gambar IS NOT NULL');
        $this->db->where('gambar !=', '');
        return $this->db->count_all_results();
    }

    /**
     * ============== STATISTIK ==============
     */

    /**
     * Hitung total aset
     */
    public function count_all() {
        return $this->db->count_all($this->table);
    }

    /**
     * Hitung total aset berdasarkan ruangan
     */
    public function count_by_ruangan($id_ruangan) {
        $this->db->where('id_ruangan', $id_ruangan);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Hitung total aset yang tersedia
     */
    public function count_tersedia() {
        $this->db->where('jumlah_tersedia >', 0);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Hitung total aset yang dipinjam
     */
    public function count_dipinjam() {
        $sql = "SELECT COUNT(DISTINCT id_aset) as total FROM peminjaman_barang WHERE status = 'Dipinjam'";
        $result = $this->db->query($sql)->row();
        return $result ? $result->total : 0;
    }

    /**
     * Ambil aset dengan stok menipis (kurang dari 3)
     */
    public function get_stok_menipis($limit = 10) {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.jumlah_tersedia <', 3);
        $this->db->where('aset.jumlah_tersedia >', 0);
        $this->db->order_by('aset.jumlah_tersedia', 'ASC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Ambil aset dengan stok habis
     */
    public function get_stok_habis($limit = 10) {
        $this->db->select('aset.*, COALESCE(ruangan.ruangan, "Umum") AS nama_ruangan');
        $this->db->from($this->table);
        $this->db->join('ruangan', 'ruangan.id = aset.id_ruangan', 'left');
        $this->db->where('aset.jumlah_tersedia', 0);
        $this->db->order_by('aset.nama_aset', 'ASC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * ============== BULK OPERATIONS ==============
     */

    public function insert_batch($data_array) {
        return $this->db->insert_batch($this->table, $data_array);
    }

    public function update_batch($data_array, $key = 'id_aset') {
        return $this->db->update_batch($this->table, $data_array, $key);
    }

    /**
     * ============== VALIDASI ==============
     */

    public function is_kode_aset_exists($kode_aset, $exclude_id = null) {
        $this->db->where('kode_aset', $kode_aset);
        
        if ($exclude_id !== null) {
            $this->db->where('id_aset !=', (int) $exclude_id);
        }
        
        $count = $this->db->count_all_results($this->table);
        return $count > 0;
    }

    public function can_delete($id_aset) {
        $this->db->where('id_aset', (int) $id_aset);
        $this->db->where('status', 'Dipinjam');
        $active_peminjaman = $this->db->count_all_results('peminjaman_barang');
        
        return $active_peminjaman === 0;
    }
}
