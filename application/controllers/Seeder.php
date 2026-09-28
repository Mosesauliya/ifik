<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Seeder Controller
 * Digunakan sekali untuk setup akun Super Admin.
 * Akses: /seeder/setup_super_admin
 * Setelah dijalankan, hapus file ini dari server demi keamanan.
 */
class Seeder extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // Keamanan: hanya bisa diakses dari localhost
        $allowed_ips = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
        $remote_ip   = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!in_array($remote_ip, $allowed_ips)) {
            http_response_code(403);
            die('<h2>403 Forbidden</h2><p>Seeder hanya bisa diakses dari localhost.</p>');
        }
    }

    /**
     * Setup akun Super Admin (role_id = 22 sudah ada di DB)
     * URL: /seeder/setup_super_admin
     */
    public function setup_super_admin()
    {
        $log = [];
        $tbl_user = $this->db->table_exists('user') ? 'user' : 'users';

        // role_id=22 sudah ada di DB, langsung buat akun saja
        $email         = 'superadmin@telkomuniversity.ac.id';
        $plainPassword = 'SuperAdmin#2025';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT, ['cost' => 10]);

        $existingUser = $this->db->get_where($tbl_user, ['id' => 'super-admin-01'])->row();

        // Cek juga by email
        if (!$existingUser) {
            $existingUser = $this->db->get_where($tbl_user, ['email' => $email])->row();
        }

        if (!$existingUser) {
            $userData = [
                'id'       => 'super-admin-01',
                'username' => 'superadmin',
                'name'     => 'Super Admin',
                'email'    => $email,
                'password' => $hashedPassword,
                'role_id'  => 22,
                'status'   => 'active',
            ];
            if ($this->db->field_exists('password_changed', $tbl_user)) $userData['password_changed'] = 1;
            if ($this->db->field_exists('is_active', $tbl_user))        $userData['is_active']        = 1;
            if ($this->db->field_exists('nidn_nim', $tbl_user))         $userData['nidn_nim']         = 'SA-001';
            if ($this->db->field_exists('nim', $tbl_user))              $userData['nim']              = 'SA-001';
            if ($this->db->field_exists('date_created', $tbl_user))     $userData['date_created']     = time();
            if ($this->db->field_exists('created_at', $tbl_user))       $userData['created_at']       = date('Y-m-d H:i:s');
            if ($this->db->field_exists('updated_at', $tbl_user))       $userData['updated_at']       = date('Y-m-d H:i:s');

            $this->db->insert($tbl_user, $userData);
            $affected = $this->db->affected_rows();

            if ($affected > 0) {
                $log[] = '✅ Akun Super Admin BERHASIL dibuat!';
                $log[] = '   ID       : super-admin-01';
                $log[] = '   Username : superadmin';
                $log[] = '   Email    : ' . $email;
                $log[] = '   Password : ' . $plainPassword;
                $log[] = '   Role ID  : 22 (Super Admin)';
            } else {
                $log[] = '❌ Gagal insert! Error: ' . $this->db->error()['message'];
            }
        } else {
            // Update password & role
            $this->db->where('id', $existingUser->id);
            $this->db->update($tbl_user, [
                'password' => $hashedPassword,
                'role_id'  => 22,
                'status'   => 'active',
            ]);
            $log[] = 'ℹ️  Akun sudah ada (id=' . $existingUser->id . '). Password & role diperbarui.';
            $log[] = '   Email    : ' . $email;
            $log[] = '   Password : ' . $plainPassword;
            $log[] = '   Role ID  : 22 (Super Admin)';
        }

        echo '<pre style="font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;font-size:14px;">';
        echo '<b style="color:#4ec9b0;font-size:16px;">=== IFIK Super Admin Seeder ===</b>' . "\n\n";
        foreach ($log as $line) {
            echo $line . "\n";
        }
        echo "\n";
        echo '<b style="color:#ce9178;">⚠️  PENTING: Setelah seeder berhasil, hapus file</b>' . "\n";
        echo '<b style="color:#ce9178;">   application/controllers/Seeder.php dari server!</b>' . "\n";
        echo '</pre>';
    }
}
