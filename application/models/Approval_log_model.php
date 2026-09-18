<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_log_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Catat log persetujuan / penolakan baru
     */
    public function log($data) {
        $modul       = $data['modul'] ?? 'Umum';
        $ref_id      = $data['ref_id'] ?? '';
        $target_name = $data['target_name'] ?? NULL;
        $action      = $data['action'] ?? 'Approved';
        $catatan     = $data['catatan'] ?? NULL;

        // Ambil data aktor dari session jika tidak disediakan secara eksplisit
        $actor_id      = $data['actor_id'] ?? $this->session->userdata('user_id');
        $actor_name    = $data['actor_name'] ?? $this->session->userdata('name');
        $actor_nip_nim = $data['actor_nip_nim'] ?? ($this->session->userdata('nidn_nim') ?: ($this->session->userdata('nip') ?: $this->session->userdata('nim')));
        
        $actor_role    = $data['actor_role'] ?? NULL;
        if (empty($actor_role)) {
            $role_id = $this->session->userdata('role_id');
            switch ($role_id) {
                case 1: $actor_role = 'Admin Panel'; break;
                case 2: $actor_role = 'Ka. Ur'; break;
                case 3: $actor_role = 'Dosen'; break;
                case 4: $actor_role = 'Mahasiswa'; break;
                case 5: $actor_role = 'Admin LAA'; break;
                case 6: $actor_role = 'Koordinator TA'; break;
                case 21: $actor_role = 'Laboran'; break;
                default: $actor_role = $modul; break;
            }
        }

        if (empty($actor_name)) {
            $actor_name = $actor_role;
        }

        $insert_data = array(
            'modul'         => $modul,
            'ref_id'        => (string)$ref_id,
            'target_name'   => $target_name,
            'action'        => $action,
            'actor_id'      => $actor_id ? (int)$actor_id : NULL,
            'actor_name'    => $actor_name,
            'actor_role'    => $actor_role,
            'actor_nip_nim' => $actor_nip_nim,
            'catatan'       => $catatan,
            'created_at'    => date('Y-m-d H:i:s')
        );

        return $this->db->insert('log_approval_history', $insert_data);
    }

    /**
     * Ambil semua data log history dengan pagination dan filter
     */
    public function get_all_logs($filter_modul = null, $filter_action = null, $search = null, $limit = 20, $offset = 0) {
        if (!empty($filter_modul) && $filter_modul !== 'all') {
            $this->db->where('modul', $filter_modul);
        }

        if (!empty($filter_action) && $filter_action !== 'all') {
            $this->db->where('action', $filter_action);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ref_id', $search);
            $this->db->or_like('target_name', $search);
            $this->db->or_like('actor_name', $search);
            $this->db->or_like('actor_role', $search);
            $this->db->or_like('actor_nip_nim', $search);
            $this->db->or_like('catatan', $search);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get('log_approval_history')->result_array();
    }

    /**
     * Hitung total data log untuk pagination
     */
    public function count_logs($filter_modul = null, $filter_action = null, $search = null) {
        if (!empty($filter_modul) && $filter_modul !== 'all') {
            $this->db->where('modul', $filter_modul);
        }

        if (!empty($filter_action) && $filter_action !== 'all') {
            $this->db->where('action', $filter_action);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ref_id', $search);
            $this->db->or_like('target_name', $search);
            $this->db->or_like('actor_name', $search);
            $this->db->or_like('actor_role', $search);
            $this->db->or_like('actor_nip_nim', $search);
            $this->db->or_like('catatan', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results('log_approval_history');
    }

    /**
     * Ambil riwayat log spesifik berdasarkan referensi (misal NIM mahasiswa atau ID Booking)
     */
    public function get_logs_by_ref($ref_id, $modul = null) {
        $this->db->where('ref_id', (string)$ref_id);
        if (!empty($modul)) {
            $this->db->where('modul', $modul);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('log_approval_history')->result_array();
    }

    /**
     * Autocomplete search untuk Log History Approval
     */
    public function autocomplete_search($term) {
        if (empty($term)) return array();

        $this->db->group_start();
        $this->db->like('ref_id', $term);
        $this->db->or_like('target_name', $term);
        $this->db->or_like('actor_name', $term);
        $this->db->or_like('actor_role', $term);
        $this->db->or_like('actor_nip_nim', $term);
        $this->db->or_like('catatan', $term);
        $this->db->group_end();

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(8);
        return $this->db->get('log_approval_history')->result_array();
    }

    /**
     * Hitung metrik statistik ringkas untuk Log History
     */
    public function get_log_stats() {
        $total    = $this->db->count_all_results('log_approval_history');
        $approved = $this->db->where('action', 'Approved')->count_all_results('log_approval_history');
        $rejected = $this->db->where('action', 'Rejected')->count_all_results('log_approval_history');
        $reset    = $this->db->where('action', 'Reset')->count_all_results('log_approval_history');

        return array(
            'total'    => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'reset'    => $reset
        );
    }
}