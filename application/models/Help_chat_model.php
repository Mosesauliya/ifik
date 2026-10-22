<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help_chat_model extends CI_Model {

    private $table_conversations = 'help_conversations';
    private $table_messages      = 'help_messages';
    private $presence_file;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        @$this->db->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
        @$this->db->query("SET time_zone = '+07:00'");
        $this->presence_file = APPPATH . 'cache/help_presence_state.json';
        $this->_ensure_tables();
    }

    /**
     * Pastikan 2 tabel bantuan chat tersedia di database
     */
    private function _ensure_tables() {
        if (!$this->db->table_exists($this->table_conversations)) {
            $sql1 = "CREATE TABLE IF NOT EXISTS `{$this->table_conversations}` (
                `id` VARCHAR(64) NOT NULL PRIMARY KEY,
                `user_id` VARCHAR(64) NOT NULL,
                `target_role` VARCHAR(50) NOT NULL DEFAULT 'laboran',
                `last_message` TEXT NULL,
                `last_message_time` DATETIME NULL,
                `unread_staff` INT DEFAULT 0,
                `unread_user` INT DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_user_target` (`user_id`, `target_role`),
                INDEX `idx_target_role` (`target_role`),
                INDEX `idx_last_message_time` (`last_message_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql1);
        }

        if (!$this->db->table_exists($this->table_messages)) {
            $sql2 = "CREATE TABLE IF NOT EXISTS `{$this->table_messages}` (
                `id` VARCHAR(64) NOT NULL PRIMARY KEY,
                `conversation_id` VARCHAR(64) NOT NULL,
                `sender_id` VARCHAR(64) NOT NULL,
                `sender_name` VARCHAR(150) NOT NULL,
                `sender_role` VARCHAR(50) NOT NULL,
                `message` TEXT NOT NULL,
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_conversation_id` (`conversation_id`),
                INDEX `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql2);
        }
    }

    /**
     * Helper Cache: Baca data presence dari file JSON di application/cache
     */
    private function _get_presence_cache() {
        if (!file_exists($this->presence_file)) {
            return [];
        }
        $content = @file_get_contents($this->presence_file);
        if (empty($content)) {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Helper Cache: Tulis data presence ke file JSON di application/cache
     */
    private function _save_presence_cache(array $data) {
        // Prune data yang sudah kadaluarsa > 2 hari agar file tetap kecil (< 5KB)
        $cleanThreshold = time() - 172800;
        foreach ($data as $uid => $row) {
            if (isset($row['last_active_ts']) && $row['last_active_ts'] < $cleanThreshold) {
                unset($data[$uid]);
            }
        }
        @file_put_contents($this->presence_file, json_encode($data), LOCK_EX);
    }

    /**
     * Ambil statistik percakapan help desk
     */
    public function get_stats($target_role = null, $user_id = null) {
        $where = [];
        if (!empty($target_role) && $target_role !== 'all') {
            $where[] = "c.`target_role` = " . $this->db->escape($target_role);
        }
        if (!empty($user_id)) {
            $where[] = "c.`user_id` = " . $this->db->escape((string)$user_id);
        }
        $whereSql = !empty($where) ? " WHERE " . implode(' AND ', $where) : "";

        $unreadCol = !empty($user_id) ? 'unread_user' : 'unread_staff';

        $sql = "SELECT 
                    COUNT(*) as total,
                    COALESCE(SUM(c.`{$unreadCol}`), 0) as `unread`
                FROM `{$this->table_conversations}` c" . $whereSql;

        $res = $this->db->query($sql)->row();

        return [
            'total'    => (int)($res->total ?? 0),
            'open'     => (int)($res->total ?? 0),
            'resolved' => 0,
            'unread'   => (int)($res->unread ?? 0)
        ];
    }

    /**
     * Ambil list percakapan dengan join ke user dan filter
     */
    public function get_conversations($status = 'all', $search = '', $target_role = null, $user_id = null) {
        @$this->db->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
        @$this->db->query("SET time_zone = '+07:00'");

        $this->db->select("
            c.*,
            COALESCE(u.name, 'Pengguna') as user_nama,
            COALESCE(u.email, '') as user_email,
            COALESCE(r.role, 'User') as user_role,
            COALESCE(u.nip, u.nim, u.username, '-') as user_nim_nip,
            COALESCE(u.images, 'default.jpg') as user_image,
            c.unread_staff as unread_laboran,
            'open' as `status`,
            'Bantuan Umum' as `topik`,
            'Petugas' as `laboran_nama`
        ");
        $this->db->from("{$this->table_conversations} c");
        $this->db->join("user u", "u.id = c.user_id", "left");
        $this->db->join("user_role r", "r.id = u.role_id", "left");

        if (!empty($target_role) && $target_role !== 'all') {
            $this->db->where('c.target_role', $target_role);
        }

        if (!empty($user_id)) {
            $this->db->where('c.user_id', (string)$user_id);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('u.name', $search);
            $this->db->or_like('u.username', $search);
            $this->db->or_like('u.nip', $search);
            $this->db->or_like('u.nim', $search);
            $this->db->or_like('c.last_message', $search);
            $this->db->group_end();
        }

        $this->db->order_by('c.last_message_time', 'DESC');
        $this->db->order_by('c.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Ambil single percakapan berdasarkan ID (string uniqid)
     */
    public function get_conversation_by_id($id) {
        @$this->db->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
        @$this->db->query("SET time_zone = '+07:00'");

        $this->db->select("
            c.*,
            COALESCE(u.name, 'Pengguna') as user_nama,
            COALESCE(u.email, '') as user_email,
            COALESCE(r.role, 'User') as user_role,
            COALESCE(u.nip, u.nim, u.username, '-') as user_nim_nip,
            COALESCE(u.images, 'default.jpg') as user_image,
            c.unread_staff as unread_laboran,
            'open' as `status`,
            'Bantuan Umum' as `topik`,
            'Petugas' as `laboran_nama`
        ");
        $this->db->from("{$this->table_conversations} c");
        $this->db->join("user u", "u.id = c.user_id", "left");
        $this->db->join("user_role r", "r.id = u.role_id", "left");
        $this->db->where('c.id', (string)$id);

        return $this->db->get()->row();
    }

    /**
     * Ambil daftar pesan dalam sebuah percakapan
     */
    public function get_messages($conversation_id) {
        @$this->db->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
        @$this->db->query("SET time_zone = '+07:00'");

        $this->db->from($this->table_messages);
        $this->db->where('conversation_id', (string)$conversation_id);
        $this->db->order_by('created_at', 'ASC');

        $rows = $this->db->get()->result();
        foreach ($rows as &$r) {
            $r->attachment = null;
        }
        return $rows;
    }

    /**
     * Tandai semua pesan dari requester sebagai telah dibaca oleh staff/trio
     */
    public function mark_as_read_by_laboran($conversation_id) {
        $conversation_id = (string)$conversation_id;

        // Update status is_read di tabel help_messages
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where_in('sender_role', ['user', 'koordinator_ta', 'mahasiswa', 'dosen']);
        $this->db->where('is_read', 0);
        $this->db->update($this->table_messages, ['is_read' => 1]);

        // Reset unread_staff pada help_conversations
        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, ['unread_staff' => 0]);

        return true;
    }

    /**
     * Tandai semua pesan dari staff sebagai telah dibaca oleh pemohon
     */
    public function mark_as_read_by_user($conversation_id) {
        $conversation_id = (string)$conversation_id;

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
        $conversation_id = (string)$conversation_id;
        $sender_id = (string)$sender_id;

        @$this->db->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
        @$this->db->query("SET time_zone = '+07:00'");

        $isStaff = in_array(strtolower($sender_role), ['laboran', 'kaur', 'admin_layanan', 'staff', 'admin']);

        // Anti-duplicate protection: 2 detik
        $lastMsg = $this->db->select('id, sender_id, message, created_at')
                            ->from($this->table_messages)
                            ->where('conversation_id', $conversation_id)
                            ->order_by('created_at', 'DESC')
                            ->limit(1)
                            ->get()->row();
        if ($lastMsg && (string)$lastMsg->sender_id === $sender_id && trim($lastMsg->message) === trim($message)) {
            $diffSeconds = time() - strtotime($lastMsg->created_at);
            if ($diffSeconds <= 2) {
                return $lastMsg->id;
            }
        }

        $message_id = uniqid();
        $msgData = [
            'id'              => $message_id,
            'conversation_id' => $conversation_id,
            'sender_id'       => $sender_id,
            'sender_name'     => $sender_name,
            'sender_role'     => $sender_role,
            'message'         => trim($message),
            'is_read'         => 0,
            'created_at'      => $now
        ];

        $this->db->insert($this->table_messages, $msgData);

        // Update info percakapan utama
        $convUpdate = [
            'last_message'      => trim($message),
            'last_message_time' => $now,
            'updated_at'        => $now
        ];

        if ($isStaff) {
            $this->db->set('unread_user', 'unread_user + 1', FALSE);
        } else {
            $this->db->set('unread_staff', 'unread_staff + 1', FALSE);
        }

        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, $convUpdate);

        return $message_id;
    }

    /**
     * Update status percakapan (compatibility placeholder)
     */
    public function update_status($conversation_id, $status = 'open', $laboran_id = null, $laboran_nama = null) {
        $conversation_id = (string)$conversation_id;
        $this->db->where('id', $conversation_id);
        return $this->db->update($this->table_conversations, ['updated_at' => date('Y-m-d H:i:s')]);
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
        $this->db->where('user_id', (string)$user_id);
        $this->db->where('target_role', $target_role);
        $this->db->order_by('created_at', 'DESC');
        $conv = $this->db->get()->row();

        if ($conv) {
            return $this->get_conversation_by_id($conv->id);
        }

        $conv_id = $this->create_conversation([
            'user_id'      => (string)$user_id,
            'user_nama'    => $user_nama,
            'user_email'   => $user_email,
            'user_role'    => $user_role,
            'user_nim_nip' => $user_nim_nip,
            'target_role'  => $target_role,
            'message'      => ''
        ]);

        return $this->get_conversation_by_id($conv_id);
    }

    /**
     * Membuat percakapan bantuan baru menggunakan uniqid()
     */
    public function create_conversation($data) {
        $now = date('Y-m-d H:i:s');
        @$this->db->query("SET NAMES utf8mb4 COLLATE utf8mb4_general_ci");
        @$this->db->query("SET time_zone = '+07:00'");

        $targetRole = strtolower($data['target_role'] ?? 'laboran');
        if (!in_array($targetRole, ['laboran', 'kaur', 'admin_layanan'])) {
            $targetRole = 'laboran';
        }

        // Anti-duplicate protection: 3 detik
        if (!empty($data['user_id'])) {
            $recent = $this->db->select('id, last_message, created_at')
                               ->from($this->table_conversations)
                               ->where('user_id', (string)$data['user_id'])
                               ->where('target_role', $targetRole)
                               ->order_by('created_at', 'DESC')
                               ->limit(1)
                               ->get()->row();
            if ($recent && empty($data['message'])) {
                return $recent->id;
            }
            if ($recent && !empty($data['message']) && trim($recent->last_message) === trim($data['message'])) {
                $diff = time() - strtotime($recent->created_at);
                if ($diff <= 3) {
                    return $recent->id;
                }
            }
        }

        $conv_id = uniqid();
        $convData = [
            'id'                => $conv_id,
            'user_id'           => (string)$data['user_id'],
            'target_role'       => $targetRole,
            'last_message'      => trim($data['message'] ?? ''),
            'last_message_time' => $now,
            'unread_staff'      => !empty($data['message']) ? 1 : 0,
            'unread_user'       => 0,
            'created_at'        => $now,
            'updated_at'        => $now
        ];

        $this->db->insert($this->table_conversations, $convData);

        if (!empty($data['message'])) {
            $msg_id = uniqid();
            $this->db->insert($this->table_messages, [
                'id'              => $msg_id,
                'conversation_id' => $conv_id,
                'sender_id'       => (string)$data['user_id'],
                'sender_name'     => $data['user_nama'] ?? 'Pengguna',
                'sender_role'     => $data['sender_role'] ?? ($data['user_role'] ?? 'user'),
                'message'         => trim($data['message']),
                'is_read'         => 0,
                'created_at'      => $now
            ]);
        }

        return $conv_id;
    }

    /**
     * Update status kehadiran / online user atau petugas via File Cache (0 Query MySQL)
     */
    public function update_presence($user_id, $role_id = null, $user_nama = null, $target_role = null, $is_online = 1) {
        if (empty($user_id)) return false;

        $user_id = (string)$user_id;
        $now = date('Y-m-d H:i:s');
        $now_ts = time();
        $is_online = (int)$is_online;

        $cache = $this->_get_presence_cache();
        $prev = $cache[$user_id] ?? [];

        $cache[$user_id] = [
            'user_id'        => $user_id,
            'role_id'        => $role_id !== null ? (int)$role_id : ($prev['role_id'] ?? null),
            'target_role'    => $target_role !== null ? (string)$target_role : ($prev['target_role'] ?? null),
            'user_nama'      => $user_nama !== null ? (string)$user_nama : ($prev['user_nama'] ?? null),
            'last_active_at' => $now,
            'last_active_ts' => $now_ts,
            'is_online'      => $is_online
        ];

        $this->_save_presence_cache($cache);
        return true;
    }

    /**
     * Dapatkan status kehadiran untuk ketiga saluran petugas (laboran, kaur, admin_layanan) via File Cache
     */
    public function get_channels_presence($timeout_seconds = 90) {
        $now_ts = time();
        $threshold_ts = $now_ts - $timeout_seconds;

        $roleMap = [
            'laboran'       => 21,
            'kaur'          => 2,
            'admin_layanan' => 5
        ];

        $cache = $this->_get_presence_cache();
        $result = [];

        foreach ($roleMap as $target => $roleId) {
            $totalActive = 0;
            $latestTime = null;
            $latestTs = 0;

            foreach ($cache as $p) {
                $isTargetMatch = (isset($p['target_role']) && strtolower($p['target_role']) === $target) 
                               || (isset($p['role_id']) && (int)$p['role_id'] === $roleId);
                if (!$isTargetMatch) continue;

                $pTs = isset($p['last_active_ts']) ? (int)$p['last_active_ts'] : strtotime($p['last_active_at'] ?? '0');
                if ($pTs > $latestTs) {
                    $latestTs = $pTs;
                    $latestTime = $p['last_active_at'] ?? date('Y-m-d H:i:s', $pTs);
                }

                $isOnlineFlag = isset($p['is_online']) ? (int)$p['is_online'] === 1 : true;
                if ($isOnlineFlag && $pTs >= $threshold_ts) {
                    $totalActive++;
                }
            }

            $isOnline = ($totalActive > 0);
            $lastSeen = $isOnline ? 'Online' : ($latestTime ? $this->format_last_seen($latestTime) : 'Offline');

            $result[$target] = [
                'online'       => $isOnline,
                'status'       => $isOnline ? 'online' : 'offline',
                'label'        => $isOnline ? 'Online' : 'Offline',
                'last_seen'    => $lastSeen,
                'last_active'  => $latestTime,
                'active_count' => $totalActive
            ];
        }

        return $result;
    }

    /**
     * Dapatkan status kehadiran kumpulan user via File Cache (0 Query MySQL)
     */
    public function get_users_presence($user_ids, $timeout_seconds = 90, $user_names = []) {
        if (!is_array($user_ids)) $user_ids = [$user_ids];
        if (!is_array($user_names)) $user_names = [$user_names];

        $user_ids = array_values(array_filter(array_unique(array_map('strval', $user_ids))));
        $user_names = array_values(array_filter(array_unique(array_map('strval', $user_names))));

        if (empty($user_ids) && empty($user_names)) return [];

        $now_ts = time();
        $threshold_ts = $now_ts - $timeout_seconds;
        $cache = $this->_get_presence_cache();

        $map = [];
        foreach ($cache as $uid => $p) {
            $pTs = isset($p['last_active_ts']) ? (int)$p['last_active_ts'] : strtotime($p['last_active_at'] ?? '0');
            $isOnline = (isset($p['is_online']) ? (int)$p['is_online'] === 1 : true) && ($pTs >= $threshold_ts);
            $lastActive = $p['last_active_at'] ?? date('Y-m-d H:i:s', $pTs);

            $presenceData = [
                'online'      => $isOnline,
                'status'      => $isOnline ? 'online' : 'offline',
                'label'       => $isOnline ? 'Online' : 'Offline',
                'last_seen'   => $this->format_last_seen($lastActive),
                'last_active' => $lastActive
            ];

            $map[$uid] = $presenceData;
            if (!empty($p['user_nama'])) {
                $map[strtolower(trim($p['user_nama']))] = $presenceData;
            }
        }

        foreach ($user_ids as $uid) {
            if (!isset($map[$uid])) {
                $map[$uid] = [
                    'online'      => false,
                    'status'      => 'offline',
                    'label'       => 'Offline',
                    'last_seen'   => 'Offline',
                    'last_active' => null
                ];
            }
        }

        return $map;
    }

    /**
     * Helper format relatif waktu terakhir aktif (last seen)
     */
    public function format_last_seen($datetime) {
        if (empty($datetime)) return 'Offline';
        $time = strtotime($datetime);
        if (!$time) return 'Offline';

        $diff = time() - $time;
        if ($diff < 0) $diff = 0;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $mins = max(1, round($diff / 60));
            return $mins . ' menit yang lalu';
        } elseif ($diff < 86400) {
            $hours = round($diff / 3600);
            return $hours . ' jam yang lalu';
        } elseif ($diff < 172800) {
            return 'Kemarin pukul ' . date('H:i', $time);
        } else {
            return date('d M Y, H:i', $time);
        }
    }

}
