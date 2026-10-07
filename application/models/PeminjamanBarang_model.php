<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: PeminjamanBarang_model (Project IFIK)
 * Diadaptasi persis dari Peminjaman_model SCM FIK
 */
class PeminjamanBarang_model extends CI_Model {

    private $table_peminjaman = 'peminjaman';
    private $table_peminjam = 'peminjam';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Aset_model');
    }

    public function get_peminjaman_by_id($id) {
        $this->db->select('
            p.*,
            peminjam.nama_peminjam,
            peminjam.nim_nip,
            peminjam.jenis as jenis_peminjam,
            peminjam.prodi as prodi_peminjam
        ');
        $this->db->from($this->table_peminjaman . ' as p');
        $this->db->join('peminjam', 'peminjam.id_peminjam = p.id_peminjam', 'left');
        $this->db->where('p.id_peminjaman', (int) $id);
        
        $result = $this->db->get()->row();
        
        if ($result) {
            $this->db->select('
                p.id_peminjaman,
                p.id_aset,
                p.jumlah_pinjam,
                p.kondisi_saat_pinjam,
                a.nama_aset,
                a.kode_aset,
                a.id_ruangan,
                r.ruangan AS nama_ruangan_db
            ', FALSE);
            $this->db->from($this->table_peminjaman . ' as p');
            $this->db->join('aset a', 'a.id_aset = p.id_aset', 'left');
            $this->db->join('ruangan r', 'r.id = a.id_ruangan', 'left');
            if (!empty($result->group_id)) {
                $this->db->where('p.group_id', $result->group_id);
            } else {
                $this->db->where('p.id_peminjaman', $result->id_peminjaman);
            }
            $detail = $this->db->get()->result();
            
            $result->detail_barang = $detail;
            $result->total_jenis = count($detail);
            $result->total_jumlah = 0;
            foreach ($detail as $d) {
                $d->nama_ruangan = !empty($d->nama_ruangan_db) ? $d->nama_ruangan_db : (!empty($d->id_ruangan) ? $d->id_ruangan : '-');
                $result->total_jumlah += (int) $d->jumlah_pinjam;
            }
            $result->kegiatan = $result->keperluan ?? '-';
        }
        
        return $result;
    }

    public function get_peminjaman_by_group_id($group_id) {
        $this->db->select('MIN(id_peminjaman) as id_peminjaman');
        if (strpos((string) $group_id, 'single-') === 0) {
            $row = $this->db->where('id_peminjaman', (int) str_replace('single-', '', $group_id))->get($this->table_peminjaman)->row();
        } else {
            $row = $this->db->where('group_id', $group_id)->get($this->table_peminjaman)->row();
        }
        return $row && $row->id_peminjaman ? $this->get_peminjaman_by_id($row->id_peminjaman) : null;
    }

    public function get_peminjaman_by_group_id_for_update($group_id) {
        if (strpos((string) $group_id, 'single-') === 0) {
            $sql = 'SELECT id_peminjaman FROM `' . $this->table_peminjaman . '` WHERE id_peminjaman = ? LIMIT 1 FOR UPDATE';
            $row = $this->db->query($sql, [(int) str_replace('single-', '', $group_id)])->row();
        } else {
            $sql = 'SELECT id_peminjaman FROM `' . $this->table_peminjaman . '` WHERE group_id = ? ORDER BY id_peminjaman ASC LIMIT 1 FOR UPDATE';
            $row = $this->db->query($sql, [$group_id])->row();
        }

        return $row && $row->id_peminjaman ? $this->get_peminjaman_by_id($row->id_peminjaman) : null;
    }

    public function get_qr_payload($group_id) {
        return site_url('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
    }

    public function convert_reservation_to_borrowed($id_peminjaman, $jumlah_aktual) {
        $jumlah_aktual = max(0, (int) $jumlah_aktual);
        $this->db->trans_begin();
        $row = $this->db->query('SELECT * FROM `' . $this->table_peminjaman . '` WHERE id_peminjaman = ? LIMIT 1 FOR UPDATE', [(int) $id_peminjaman])->row();
        if (!$row || $jumlah_aktual > (int) $row->jumlah_pinjam) {
            $this->db->trans_rollback();
            return false;
        }

        $reserved = (int) $row->jumlah_pinjam;
        $state = (string) ($row->stock_allocation_status ?? 'none');
        if (in_array($state, ['none', 'awaiting_stock'], true)) {
            if (!$this->Aset_model->reserve_stock($row->id_aset, $reserved)) {
                $this->db->trans_rollback();
                return false;
            }
            $state = 'reserved';
        }
        if ($state !== 'reserved') {
            $this->db->trans_rollback();
            return false;
        }
        $ok = $jumlah_aktual === 0
            ? $this->Aset_model->release_reserved_stock($row->id_aset, $reserved)
            : $this->Aset_model->reserved_to_borrowed($row->id_aset, $reserved, $jumlah_aktual);
        if (!$ok) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->where('id_peminjaman', $row->id_peminjaman)->update($this->table_peminjaman, [
            'jumlah_pinjam' => $jumlah_aktual,
            'stock_allocation_status' => $jumlah_aktual > 0 ? 'borrowed' : 'released',
            'stock_released_at' => $jumlah_aktual > 0 ? null : date('Y-m-d H:i:s'),
        ]);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function return_stock_allocation($id_peminjaman, $jumlah_kembali, $make_available = true) {
        $jumlah_kembali = max(0, (int) $jumlah_kembali);
        $this->db->trans_begin();
        $row = $this->db->query('SELECT * FROM `' . $this->table_peminjaman . '` WHERE id_peminjaman = ? LIMIT 1 FOR UPDATE', [(int) $id_peminjaman])->row();
        if (!$row) {
            $this->db->trans_rollback();
            return false;
        }
        $state = (string) ($row->stock_allocation_status ?? 'none');
        if (in_array($state, ['returned', 'unavailable', 'partial_unavailable'], true)) {
            $this->db->trans_commit();
            return true;
        }
        $borrowed_qty = max(0, (int) $row->jumlah_pinjam);
        if ($state === 'released' && $borrowed_qty === 0) {
            $this->db->where('id_peminjaman', $row->id_peminjaman)->update($this->table_peminjaman, [
                'jumlah_kembali' => 0,
                'stock_allocation_status' => 'returned',
                'stock_released_at' => date('Y-m-d H:i:s'),
            ]);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            }
            $this->db->trans_commit();
            return true;
        }
        if ($state !== 'borrowed' || $jumlah_kembali > $borrowed_qty) {
            $this->db->trans_rollback();
            return false;
        }

        if ($make_available && $jumlah_kembali > 0
            && !$this->Aset_model->return_borrowed_stock($row->id_aset, $jumlah_kembali, true)) {
            $this->db->trans_rollback();
            return false;
        }
        $allocation_status = !$make_available
            ? 'unavailable'
            : ($jumlah_kembali === $borrowed_qty ? 'returned' : 'partial_unavailable');
        $this->db->where('id_peminjaman', $row->id_peminjaman)->update($this->table_peminjaman, [
            'jumlah_kembali' => $jumlah_kembali,
            'stock_allocation_status' => $allocation_status,
            'stock_released_at' => date('Y-m-d H:i:s'),
        ]);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function update_group_status($group_id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (strpos((string) $group_id, 'single-') === 0) {
            $this->db->where('id_peminjaman', (int) str_replace('single-', '', $group_id));
        } else {
            $this->db->where('group_id', $group_id);
        }
        return $this->db->update($this->table_peminjaman, $data);
    }

    public function insert_evidence($data) {
        if (!$this->db->table_exists('peminjaman_evidence')) {
            return false;
        }
        return $this->db->insert('peminjaman_evidence', $data);
    }

    public function create_notifikasi($recipient_role, $recipient_user_id, $judul, $pesan, $link = null, $reference_type = null, $reference_id = null) {
        if (!$this->db->table_exists('notifikasi_progress')) {
            return false;
        }
        return $this->db->insert('notifikasi_progress', [
            'recipient_role' => $recipient_role,
            'recipient_user_id' => $recipient_user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'link' => $link,
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
