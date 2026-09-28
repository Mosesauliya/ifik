<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Seeder Controller
 * Digunakan sekali untuk setup data awal (role & akun super admin).
 * Akses: /seeder/setup_super_admin
 * Setelah dijalankan, controller ini bisa dihapus dari server demi keamanan.
 */
class Seeder extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // Keamanan sederhana: hanya bisa diakses dari localhost
        $allowed_ips = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
        $remote_ip   = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!in_array($remote_ip, $allowed_ips)) {
            http_response_code(403);
            die('<h2>403 Forbidden</h2><p>Seeder hanya bisa diakses dari localhost.</p>');
        }
    }

    /**
     * Setup role Super Admin + akun Super Admin
     * URL: /seeder/setup_super_admin
     */
    public function setup_super_admin()
    {
        $log = [];
        $tbl_user = $this->db->table_exists('user') ? 'user' : 'users';
        $tbl_role = $this->db->table_exists('user_role') ? 'user_role' : 'roles';

        // ─── 1. Insert / update role Super Admin (id=99) ───────────────────
        $roleField = $this->db->field_exists('role', $tbl_role) ? 'role' : 'name';
        $existingRole = $this->db->get_where($tbl_role, ['id' => 99])->row();

        if (!$existingRole) {
            $roleData = ['id' => 99, $roleField => 'super_admin'];
            // Coba tambahkan display_name jika ada kolom tsb
            if ($this->db->field_exists('display_name', $tbl_role)) {
                $roleData['display_name'] = 'Super Admin';
            }
            $this->db->insert($tbl_role, $roleData);
            $log[] = '✅ Role "Super Admin" (id=99) berhasil ditambahkan ke tabel ' . $tbl_role;
        } else {
            $log[] = 'ℹ️  Role id=99 sudah ada, dilewati.';
        }

        // ─── 2. Insert / update akun Super Admin ───────────────────────────
        $email          = 'superadmin@ifik.com';
        $plainPassword  = 'SuperAdmin#2025';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT, ['cost' => 10]);

        $existingUser = $this->db->get_where($tbl_user, ['id' => 'super-admin-01'])->row();

        if (!$existingUser) {
            $userData = [
                'id'       => 'super-admin-01',
                'username' => 'superadmin',
                'name'     => 'Super Admin',
                'email'    => $email,
                'password' => $hashedPassword,
                'role_id'  => 99,
                'status'   => 'active',
            ];
            // Tambah kolom opsional jika ada
            if ($this->db->field_exists('password_changed', $tbl_user)) $userData['password_changed'] = 1;
            if ($this->db->field_exists('is_active', $tbl_user))        $userData['is_active']        = 1;
            if ($this->db->field_exists('nidn_nim', $tbl_user))         $userData['nidn_nim']         = 'SA-001';
            if ($this->db->field_exists('nim', $tbl_user))              $userData['nim']              = 'SA-001';
            if ($this->db->field_exists('date_created', $tbl_user))     $userData['date_created']     = time();
            if ($this->db->field_exists('created_at', $tbl_user))       $userData['created_at']       = date('Y-m-d H:i:s');
            if ($this->db->field_exists('updated_at', $tbl_user))       $userData['updated_at']       = date('Y-m-d H:i:s');

            $this->db->insert($tbl_user, $userData);
            $log[] = '✅ Akun Super Admin berhasil dibuat!';
            $log[] = '   - ID       : super-admin-01';
            $log[] = '   - Username : superadmin';
            $log[] = '   - Email    : ' . $email;
            $log[] = '   - Password : ' . $plainPassword;
            $log[] = '   - Role ID  : 99 (Super Admin)';
        } else {
            // Update password & role saja, jangan buat duplikat
            $this->db->where('id', 'super-admin-01');
            $this->db->update($tbl_user, [
                'password' => $hashedPassword,
                'role_id'  => 99,
                'status'   => 'active',
            ]);
            $log[] = 'ℹ️  Akun super-admin-01 sudah ada. Password & role diperbarui.';
            $log[] = '   - Email    : ' . $email;
            $log[] = '   - Password : ' . $plainPassword;
        }

        // ─── 3. Tampilkan hasil ───────────────────────────────────────────
        echo '<pre style="font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;font-size:14px;">';
        echo '<b style="color:#4ec9b0;font-size:16px;">=== IFIK Super Admin Seeder ===</b>' . "\n\n";
        foreach ($log as $line) {
            echo $line . "\n";
        }
        echo "\n";
        echo '<b style="color:#ce9178;">⚠️  PENTING: Setelah seeder berhasil, hapus atau nonaktifkan file</b>' . "\n";
        echo '<b style="color:#ce9178;">   application/controllers/Seeder.php dari server untuk keamanan!</b>' . "\n";
        echo '</pre>';
    }
}
