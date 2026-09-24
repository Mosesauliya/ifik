<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help_chat_model extends CI_Model {

    private $table_conversations = 'help_conversations';
    private $table_messages = 'help_messages';

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->_ensure_tables();
    }

    /**
     * Pastikan tabel-tabel bantuan chat tersedia di database
     */
    private function _ensure_tables() {
        if (!$this->db->table_exists($this->table_conversations)) {
            $sql1 = "CREATE TABLE IF NOT EXISTS `{$this->table_conversations}` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NULL,
                `user_nama` VARCHAR(150) NOT NULL,
                `user_email` VARCHAR(150) NULL,
                `user_role` VARCHAR(50) DEFAULT 'Mahasiswa',
                `user_nim_nip` VARCHAR(50) NULL,
                `target_role` VARCHAR(50) DEFAULT 'laboran',
                `topik` VARCHAR(255) NOT NULL,
                `status` ENUM('open', 'resolved') DEFAULT 'open',
                `laboran_id` INT NULL,
                `laboran_nama` VARCHAR(150) NULL,
                `last_message` TEXT NULL,
                `last_message_time` DATETIME NULL,
                `unread_laboran` INT DEFAULT 0,
                `unread_user` INT DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (`status`),
                INDEX (`target_role`),
                INDEX (`user_id`),
                INDEX (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql1);
        } else {
            // Pastikan kolom target_role ada di tabel help_conversations
            if (!$this->db->field_exists('target_role', $this->table_conversations)) {
                $this->db->query("ALTER TABLE `{$this->table_conversations}` ADD COLUMN `target_role` VARCHAR(50) DEFAULT 'laboran' AFTER `user_nim_nip`;");
                $this->db->query("ALTER TABLE `{$this->table_conversations}` ADD INDEX (`target_role`);");
            }
        }

        if (!$this->db->table_exists($this->table_messages)) {
            $sql2 = "CREATE TABLE IF NOT EXISTS `{$this->table_messages}` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `conversation_id` INT NOT NULL,
                `sender_id` INT NULL,
                `sender_name` VARCHAR(150) NOT NULL,
                `sender_role` VARCHAR(50) NOT NULL,
                `message` TEXT NOT NULL,
                `attachment` VARCHAR(255) NULL,
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`conversation_id`),
                INDEX (`sender_role`),
                INDEX (`is_read`),
                INDEX (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql2);

            // Buat sample percakapan awal
            $this->_seed_initial_sample_data();
        } else {
            // Pastikan kolom sender_role bisa menyimpan string panjang seperti 'koordinator_ta', 'admin_layanan', 'laboran', 'kaur'
            $this->db->query("ALTER TABLE `{$this->table_messages}` MODIFY COLUMN `sender_role` VARCHAR(50) NOT NULL;");
        }

        // Bersihkan karakter ???? pada pesan yang sudah ada jika ada
        $this->db->query("UPDATE `{$this->table_messages}` SET `message` = REPLACE(`message`, '????', '[Sistem]') WHERE `message` LIKE '%????%';");
        $this->db->query("UPDATE `{$this->table_conversations}` SET `last_message` = REPLACE(`last_message`, '????', '[Sistem]') WHERE `last_message` LIKE '%????%';");
    }

    /**
     * Sample initial conversations and messages for demo / initial view
     */
    private function _seed_initial_sample_data() {
        $count = $this->db->count_all($this->table_conversations);
        if ($count > 0) {
            return;
        }

        // Sample 1: Koordinator TA -> Laboran
        $this->db->insert($this->table_conversations, [
            'user_id'           => 6,
            'user_nama'         => 'Dr. Koordinator TA, M.T.',
            'user_email'        => 'koordinator.ta@telkomuniversity.ac.id',
            'user_role'         => 'Koordinator TA',
            'user_nim_nip'      => '1987010102',
            'target_role'       => 'laboran',
            'topik'             => 'Pengecekan Kesiapan Lab IoT untuk Demo Sidang TA',
            'status'            => 'open',
            'last_message'      => 'Halo mas Laboran, mohon bantuan cek ketersediaan 4 unit PC & koneksi LAN di Lab IoT untuk jadwal demo besok ya.',
            'last_message_time' => date('Y-m-d H:i:s', strtotime('-25 minutes')),
            'unread_laboran'    => 1,
            'unread_user'       => 0,
            'created_at'        => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'updated_at'        => date('Y-m-d H:i:s', strtotime('-25 minutes'))
        ]);
        $c1_id = $this->db->insert_id();

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c1_id,
            'sender_id'       => 6,
            'sender_name'     => 'Dr. Koordinator TA, M.T.',
            'sender_role'     => 'koordinator_ta',
            'message'         => 'Selamat pagi mas Laboran, izin konfirmasi terkait kesiapan ruangan lab untuk sidang.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 hour'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c1_id,
            'sender_id'       => 6,
            'sender_name'     => 'Dr. Koordinator TA, M.T.',
            'sender_role'     => 'koordinator_ta',
            'message'         => 'Halo mas Laboran, mohon bantuan cek ketersediaan 4 unit PC & koneksi LAN di Lab IoT untuk jadwal demo besok ya.',
            'is_read'         => 0,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-25 minutes'))
        ]);

        // Sample 2: Koordinator TA -> Ka. Ur
        $this->db->insert($this->table_conversations, [
            'user_id'           => 6,
            'user_nama'         => 'Dr. Koordinator TA, M.T.',
            'user_email'        => 'koordinator.ta@telkomuniversity.ac.id',
            'user_role'         => 'Koordinator TA',
            'user_nim_nip'      => '1987010102',
            'target_role'       => 'kaur',
            'topik'             => 'Validasi & Rekomendasi Jadwal Sidang Gelombang 2',
            'status'            => 'open',
            'laboran_nama'      => 'Ka. Ur Laboratorium',
            'last_message'      => 'Baik pak Koor, berkas validasi jadwal sidang sudah kami review dan siap di-ACC.',
            'last_message_time' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
            'unread_laboran'    => 0,
            'unread_user'       => 1,
            'created_at'        => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'updated_at'        => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ]);
        $c2_id = $this->db->insert_id();

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c2_id,
            'sender_id'       => 6,
            'sender_name'     => 'Dr. Koordinator TA, M.T.',
            'sender_role'     => 'koordinator_ta',
            'message'         => 'Selamat siang pak Kaur, mohon review untuk draft jadwal sidang gelombang 2 yang menggunakan lab riset.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-2 hours'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c2_id,
            'sender_id'       => 2,
            'sender_name'     => 'Ka. Ur Laboratorium',
            'sender_role'     => 'kaur',
            'message'         => 'Baik pak Koor, berkas validasi jadwal sidang sudah kami review dan siap di-ACC.',
            'is_read'         => 0,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ]);

        // Sample 3: Koordinator TA -> Admin Layanan (Resolved)
        $this->db->insert($this->table_conversations, [
            'user_id'           => 6,
            'user_nama'         => 'Dr. Koordinator TA, M.T.',
            'user_email'        => 'koordinator.ta@telkomuniversity.ac.id',
            'user_role'         => 'Koordinator TA',
            'user_nim_nip'      => '1987010102',
            'target_role'       => 'admin_layanan',
            'topik'             => 'Kelengkapan Berkas Bebas Lab Mahasiswa Sidang',
            'status'            => 'resolved',
            'laboran_nama'      => 'Admin Layanan Akademik',
            'last_message'      => 'Sudah diverifikasi semua ya pak Koor, data mahasiswa terlampir sudah lengkap.',
            'last_message_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'unread_laboran'    => 0,
            'unread_user'       => 0,
            'created_at'        => date('Y-m-d H:i:s', strtotime('-2 days')),
            'updated_at'        => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
        $c3_id = $this->db->insert_id();

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c3_id,
            'sender_id'       => 6,
            'sender_name'     => 'Dr. Koordinator TA, M.T.',
            'sender_role'     => 'koordinator_ta',
            'message'         => 'Permisi tim Admin Layanan, mohon konfirmasi status bebas lab untuk 5 peserta sidang minggu ini.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-2 days'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c3_id,
            'sender_id'       => 5,
            'sender_name'     => 'Admin Layanan Akademik',
            'sender_role'     => 'admin_layanan',
            'message'         => 'Sudah diverifikasi semua ya pak Koor, data mahasiswa terlampir sudah lengkap.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
    }

    /**
     * Ambil statistik percakapan help desk
     */
    public function get_stats($target_role = null, $user_id = null) {
        $this->db->from($this->table_conversations);
        if (!empty($target_role) && $target_role !== 'all') {
            $this->db->where('target_role', $target_role);
        }
        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }
        $total = $this->db->count_all_results();

        $this->db->from($this->table_conversations);
        $this->db->where('status', 'open');
        if (!empty($target_role) && $target_role !== 'all') {
            $this->db->where('target_role', $target_role);
        }
        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }
        $open = $this->db->count_all_results();

        $this->db->from($this->table_conversations);
        $this->db->where('status', 'resolved');
        if (!empty($target_role) && $target_role !== 'all') {
            $this->db->where('target_role', $target_role);
        }
        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }
        $resolved = $this->db->count_all_results();

        // Unread counter
        $this->db->from($this->table_conversations);
        if (!empty($user_id)) {
            $this->db->select_sum('unread_user');
            $this->db->where('user_id', $user_id);
            if (!empty($target_role) && $target_role !== 'all') {
                $this->db->where('target_role', $target_role);
            }
            $unreadQuery = $this->db->get()->row();
            $unread = (int)($unreadQuery->unread_user ?? 0);
        } else {
            $this->db->select_sum('unread_laboran');
            if (!empty($target_role) && $target_role !== 'all') {
                $this->db->where('target_role', $target_role);
            }
            $unreadQuery = $this->db->get()->row();
            $unread = (int)($unreadQuery->unread_laboran ?? 0);
        }

        return [
            'total'    => (int)$total,
            'open'     => (int)$open,
            'resolved' => (int)$resolved,
            'unread'   => (int)$unread
        ];
    }

    /**
     * Ambil list percakapan dengan filter status, target role, user_id, dan pencarian
     */
    public function get_conversations($status = 'all', $search = '', $target_role = null, $user_id = null) {
        $this->db->from($this->table_conversations);

        if (!empty($status) && $status !== 'all') {
            $this->db->where('status', $status);
        }

        if (!empty($target_role) && $target_role !== 'all') {
            $this->db->where('target_role', $target_role);
        }

        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('user_nama', $search);
            $this->db->or_like('user_nim_nip', $search);
            $this->db->or_like('topik', $search);
            $this->db->or_like('last_message', $search);
            $this->db->group_end();
        }

        $this->db->order_by('last_message_time', 'DESC');
        $this->db->order_by('created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Ambil single percakapan berdasarkan ID
     */
    public function get_conversation_by_id($id) {
        $this->db->from($this->table_conversations);
        $this->db->where('id', (int)$id);
        return $this->db->get()->row();
    }

    /**
     * Ambil daftar pesan dalam sebuah percakapan
     */
    public function get_messages($conversation_id) {
        $this->db->from($this->table_messages);
        $this->db->where('conversation_id', (int)$conversation_id);
        $this->db->order_by('created_at', 'ASC');
        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Tandai semua pesan dari requester sebagai telah dibaca oleh staff/trio
     */
    public function mark_as_read_by_laboran($conversation_id) {
        $conversation_id = (int)$conversation_id;

        // Update status is_read di tabel help_messages
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where_in('sender_role', ['user', 'koordinator_ta', 'mahasiswa', 'dosen']);
        $this->db->where('is_read', 0);
        $this->db->update($this->table_messages, ['is_read' => 1]);

        // Reset unread_laboran pada help_conversations
        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, ['unread_laboran' => 0]);

        return true;
    }

    /**
     * Tandai semua pesan dari staff sebagai telah dibaca oleh pemohon (user / Koordinator TA)
     */
    public function mark_as_read_by_user($conversation_id) {
        $conversation_id = (int)$conversation_id;

        // Update status is_read di tabel help_messages
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where_in('sender_role', ['laboran', 'kaur', 'admin_layanan', 'staff', 'admin']);
        $this->db->where('is_read', 0);
        $this->db->update($this->table_messages, ['is_read' => 1]);

        // Reset unread_user pada help_conversations
        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, ['unread_user' => 0]);

        return true;
    }

    /**
     * Kirim pesan baru dalam percakapan
     */
    public function send_message($conversation_id, $sender_id, $sender_name, $sender_role, $message, $attachment = null) {
        $now = date('Y-m-d H:i:s');
        $conversation_id = (int)$conversation_id;

        $isStaff = in_array(strtolower($sender_role), ['laboran', 'kaur', 'admin_layanan', 'staff', 'admin']);

        $msgData = [
            'conversation_id' => $conversation_id,
            'sender_id'       => $sender_id,
            'sender_name'     => $sender_name,
            'sender_role'     => $sender_role,
            'message'         => trim($message),
            'attachment'      => $attachment,
            'is_read'         => $isStaff ? 0 : 0,
            'created_at'      => $now
        ];

        $this->db->insert($this->table_messages, $msgData);
        $message_id = $this->db->insert_id();

        // Update info percakapan utama
        $convUpdate = [
            'last_message'      => trim($message),
            'last_message_time' => $now,
            'updated_at'        => $now
        ];

        if ($isStaff) {
            $convUpdate['laboran_id'] = $sender_id;
            $convUpdate['laboran_nama'] = $sender_name;
            // Tambah counter unread untuk user
            $this->db->set('unread_user', 'unread_user + 1', FALSE);
        } else {
            // Tambah counter unread untuk staff (laboran/kaur/admin_layanan)
            $this->db->set('unread_laboran', 'unread_laboran + 1', FALSE);
        }

        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, $convUpdate);

        return $message_id;
    }

    /**
     * Update status percakapan (open / resolved)
     */
    public function update_status($conversation_id, $status, $laboran_id = null, $laboran_nama = null) {
        $conversation_id = (int)$conversation_id;
        $status = ($status === 'resolved') ? 'resolved' : 'open';

        $data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($laboran_id !== null) {
            $data['laboran_id'] = $laboran_id;
        }
        if ($laboran_nama !== null) {
            $data['laboran_nama'] = $laboran_nama;
        }

        $this->db->where('id', $conversation_id);
        return $this->db->update($this->table_conversations, $data);
    }

    /**
     * Dapatkan atau buat channel percakapan langsung dengan target role tertentu
     */
    public function get_or_create_channel($user_id, $user_nama, $user_email, $user_role, $user_nim_nip, $target_role) {
        $target_role = strtolower($target_role);
        if (!in_array($target_role, ['laboran', 'kaur', 'admin_layanan'])) {
            $target_role = 'laboran';
        }

        $this->db->from($this->table_conversations);
        $this->db->where('user_id', $user_id);
        $this->db->where('target_role', $target_role);
        $this->db->order_by('id', 'DESC');
        $conv = $this->db->get()->row();

        if ($conv) {
            return $conv;
        }

        $topikMap = [
            'laboran'       => 'Pusat Bantuan & Layanan Laboran',
            'kaur'          => 'Pusat Bantuan & Kebijakan Ka. Ur',
            'admin_layanan' => 'Pusat Bantuan & Layanan Akademik (LAA)'
        ];

        $conv_id = $this->create_conversation([
            'user_id'      => $user_id,
            'user_nama'    => $user_nama,
            'user_email'   => $user_email,
            'user_role'    => $user_role,
            'user_nim_nip' => $user_nim_nip,
            'target_role'  => $target_role,
            'topik'        => $topikMap[$target_role] ?? 'Pusat Bantuan Layanan',
            'message'      => ''
        ]);

        return $this->get_conversation_by_id($conv_id);
    }

    /**
     * Membuat percakapan bantuan baru
     */
    public function create_conversation($data) {
        $now = date('Y-m-d H:i:s');
        $targetRole = strtolower($data['target_role'] ?? 'laboran');
        if (!in_array($targetRole, ['laboran', 'kaur', 'admin_layanan'])) {
            $targetRole = 'laboran';
        }

        $convData = [
            'user_id'           => $data['user_id'] ?? null,
            'user_nama'         => $data['user_nama'] ?? 'Pengguna',
            'user_email'        => $data['user_email'] ?? null,
            'user_role'         => $data['user_role'] ?? 'Mahasiswa',
            'user_nim_nip'      => $data['user_nim_nip'] ?? null,
            'target_role'       => $targetRole,
            'topik'             => $data['topik'] ?? 'Bantuan Umum',
            'status'            => 'open',
            'last_message'      => $data['message'] ?? '',
            'last_message_time' => $now,
            'unread_laboran'    => 1,
            'unread_user'       => 0,
            'created_at'        => $now,
            'updated_at'        => $now
        ];

        $this->db->insert($this->table_conversations, $convData);
        $conv_id = $this->db->insert_id();

        if (!empty($data['message'])) {
            $this->db->insert($this->table_messages, [
                'conversation_id' => $conv_id,
                'sender_id'       => $data['user_id'] ?? null,
                'sender_name'     => $data['user_nama'] ?? 'Pengguna',
                'sender_role'     => $data['sender_role'] ?? ($data['user_role'] ?? 'user'),
                'message'         => trim($data['message']),
                'attachment'      => $data['attachment'] ?? null,
                'is_read'         => 0,
                'created_at'      => $now
            ]);
        }

        return $conv_id;
    }
}

