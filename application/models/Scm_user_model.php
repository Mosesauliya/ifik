<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model: Scm_user_model
 * Mengelola sinkronisasi dan pembacaan data pengguna pada modul Peminjaman Barang SCM
 */
class Scm_user_model extends CI_Model {

    private $table = 'users';
    private $primaryKey = 'id_user';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database('peminjaman', TRUE);
        $this->ensure_academic_identity_schema();
    }

    private function ensure_academic_identity_schema()
    {
        if (!$this->db->table_exists($this->table)) return;

        if (!$this->db->field_exists('prodi', $this->table)) {
            $this->db->query("ALTER TABLE `{$this->table}` ADD `prodi` varchar(120) DEFAULT NULL AFTER `role`");
            $this->db->query("ALTER TABLE `{$this->table}` ADD INDEX `idx_users_role_prodi` (`role`, `prodi`)");
        }
        if (!$this->db->field_exists('jenis_pengguna', $this->table)) {
            $this->db->query("ALTER TABLE `{$this->table}` ADD `jenis_pengguna` varchar(20) DEFAULT NULL AFTER `prodi`");
        }
    }

    /**
     * Sinkronisasi session login IFIK dengan tabel users di db_peminjamanbarang
     */
    public function sync_session_user($session)
    {
        $nim_nip = (string) ($session->userdata('nidn_nim') ?: $session->userdata('username') ?: $session->userdata('email') ?: 'USER001');
        $nama    = (string) ($session->userdata('name') ?: $session->userdata('nama') ?: 'Pengguna FIK');
        $email   = (string) ($session->userdata('email') ?: '');

        $existing = $this->get_user_by_username($nim_nip);
        if (!$existing) {
            $this->db->insert($this->table, [
                'nim_nip'        => $nim_nip,
                'nama_lengkap'   => $nama,
                'email'          => $email,
                'password'       => password_hash('123456', PASSWORD_BCRYPT),
                'role'           => 'user',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $existing = $this->get_user_by_username($nim_nip);
        }

        if ($existing) {
            $session->set_userdata([
                'id_user'  => (int) $existing->id_user,
                'nama'     => $existing->nama_lengkap ?: $nama,
                'username' => $existing->nim_nip,
                'role'     => $existing->role ?: 'user',
            ]);
        }

        return $existing;
    }

    public function get_user_by_username($username)
    {
        return $this->db
                    ->where('nim_nip', $username)
                    ->get($this->table)
                    ->row();
    }

    public function get_user_by_id($id_user)
    {
        return $this->db
                    ->where($this->primaryKey, $id_user)
                    ->get($this->table)
                    ->row();
    }

    public function get_kaprodi_by_prodi($prodi)
    {
        $row = $this->db
                    ->where('role', 'kaprodi')
                    ->where('prodi', $prodi)
                    ->order_by('id_user', 'ASC')
                    ->get($this->table)
                    ->row();

        if (!$row) {
            // Fallback ke akun kaprodi umum jika prodi spesifik belum diset
            $row = $this->db
                        ->where('role', 'kaprodi')
                        ->order_by('id_user', 'ASC')
                        ->get($this->table)
                        ->row();
        }

        return $row;
    }

    public function update_user($id_user, $data)
    {
        return $this->db
                    ->where($this->primaryKey, $id_user)
                    ->update($this->table, $data);
    }
}
