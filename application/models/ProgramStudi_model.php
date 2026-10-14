<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProgramStudi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Dapatkan statistik ringkas master prodi
     */
    public function get_stats() {
        $total  = (int)$this->db->count_all('master_prodi');
        $active = (int)$this->db->where('is_active', 1)->count_all_results('master_prodi');
        $nonactive = $total - $active;

        $total_mhs = 0;
        if ($this->db->table_exists('mahasiswa')) {
            $total_mhs = (int)$this->db->count_all('mahasiswa');
        }

        $total_dosen = 0;
        if ($this->db->table_exists('user')) {
            $total_dosen = (int)$this->db->where('role_id', 3)->count_all_results('user');
        }

        return [
            'total'       => $total,
            'active'      => $active,
            'nonactive'   => $nonactive,
            'total_mhs'   => $total_mhs,
            'total_dosen' => $total_dosen
        ];
    }

    /**
     * Ambil semua data prodi dengan filter search & status
     */
    public function get_all($search = '', $status = 'all') {
        $this->db->select('*');
        $this->db->from('master_prodi');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode_prodi', $search);
            $this->db->or_like('nama_prodi', $search);
            $this->db->or_like('keterangan', $search);
            $this->db->group_end();
        }

        if ($status === 'active') {
            $this->db->where('is_active', 1);
        } elseif ($status === 'nonactive') {
            $this->db->where('is_active', 0);
        }

        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('id', 'ASC');
        $list = $this->db->get()->result_array();

        // Hitung relasi mahasiswa & user yang menggunakan prodi ini
        foreach ($list as &$item) {
            $nama = $item['nama_prodi'];
            $kode = $item['kode_prodi'];

            $mhs_count = 0;
            if ($this->db->table_exists('mahasiswa')) {
                $this->db->group_start();
                $this->db->where('prodi', $nama);
                $this->db->or_like('prodi', $kode);
                $this->db->group_end();
                $mhs_count = $this->db->count_all_results('mahasiswa');
            }

            $user_count = 0;
            if ($this->db->table_exists('user')) {
                $this->db->group_start();
                $this->db->where('prodi', $nama);
                $this->db->or_like('prodi', $kode);
                $this->db->group_end();
                $user_count = $this->db->count_all_results('user');
            }

            $item['total_mahasiswa'] = $mhs_count;
            $item['total_user']      = $user_count;
        }

        return $list;
    }

    /**
     * Ambil 1 record prodi berdasarkan ID
     */
    public function get_by_id($id) {
        return $this->db->get_where('master_prodi', ['id' => (int)$id])->row_array();
    }

    /**
     * Simpan / Tambah / Update data prodi
     */
    public function save($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (!empty($id)) {
            $this->db->where('id', (int)$id)->update('master_prodi', $data);
            return (int)$id;
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('master_prodi', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Toggle status aktif / non-aktif prodi
     */
    public function toggle_status($id) {
        $row = $this->get_by_id($id);
        if (!$row) return false;

        $new_status = ($row['is_active'] == 1) ? 0 : 1;
        $this->db->where('id', (int)$id)->update('master_prodi', [
            'is_active'  => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return $new_status;
    }

    /**
     * Hapus prodi berdasarkan ID
     */
    public function delete($id) {
        return $this->db->where('id', (int)$id)->delete('master_prodi');
    }
}
