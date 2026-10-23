<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DosenHelp Controller
 * Bantuan & Live Chat untuk role Dosen (role_id = 3)
 * Mengadaptasi pola KoordinatorTA help - Dosen dapat chat langsung ke
 * Laboran, Ka. Ur, dan Admin Layanan.
 */
class DosenHelp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'text']);
        $this->load->model('Help_chat_model');
        date_default_timezone_set('Asia/Jakarta');

        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        // Cek role: Dosen (3), Koordinator TA (6), PIC KK (7), Ketua KK (9), atau Admin (1)
        $role_id = (int)$this->session->userdata('role_id');
        $allowed = [1, 3, 6, 7, 9];
        $isDosen = in_array($role_id, $allowed, true) || !empty($this->session->userdata('dosen_wali'));
        if (!$isDosen) {
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus untuk Dosen.');
            redirect('login');
            return;
        }
    }

    /**
     * Halaman utama Bantuan & Live Chat untuk Dosen
     */
    public function index() {
        $profile = $this->_get_current_user_profile();
        $user_id = !empty($profile['id']) ? (string)$profile['id'] : ((string)$this->session->userdata('user_id') ?: (string)$this->session->userdata('id'));
        if (!$user_id) {
            $this->session->set_flashdata('error', 'Sesi login tidak valid. Silakan login kembali.');
            redirect('login');
            return;
        }
        $role_id   = (int)$this->session->userdata('role_id') ?: 3;
        $user_nama = !empty($profile['nama']) ? $profile['nama'] : ($this->session->userdata('name') ?: 'Dosen');
        $this->Help_chat_model->update_presence($user_id, $role_id, $user_nama, 'dosen', 1);

        $data['title']             = 'Bantuan & Live Chat - Portal Dosen';
        $data['stats']             = $this->Help_chat_model->get_stats(null, $user_id);
        $data['conversations']     = $this->Help_chat_model->get_conversations('all', '', null, $user_id);
        $data['channels_presence'] = $this->Help_chat_model->get_channels_presence();

        $this->load->view('dosen/help', $data);
    }

    /**
     * AJAX: Dapatkan atau buat channel langsung ke salah satu layanan (Laboran / Kaur / Admin Layanan)
     */
    public function help_get_channel_ajax() {
        header('Content-Type: application/json');

        $target_role = strtolower(trim($this->input->get('target', true) ?? 'laboran'));
        if (!in_array($target_role, ['laboran', 'kaur', 'admin_layanan'])) {
            $target_role = 'laboran';
        }

        $profile = $this->_get_current_user_profile();
        if (!$profile['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi login telah berakhir.']);
            exit;
        }

        $userId    = $profile['id'];
        $roleId    = (int)$this->session->userdata('role_id') ?: 3;
        $userNama  = $profile['nama'];
        $userEmail = $profile['email'];
        $userNip   = $profile['nip'];
        session_write_close();

        $this->Help_chat_model->update_presence($userId, $roleId, $userNama, 'dosen', 1);
        $channelsPresence = $this->Help_chat_model->get_channels_presence();

        $conv = $this->Help_chat_model->get_or_create_channel(
            $userId, $userNama, $userEmail, 'Dosen', $userNip, $target_role
        );

        // Mark pesan sebagai terbaca oleh user
        $this->Help_chat_model->mark_as_read_by_user($conv->id);

        $messages = $this->Help_chat_model->get_messages($conv->id);
        $formattedMessages = [];

        foreach ($messages as $m) {
            $formattedMessages[] = [
                'id'          => (string)$m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => htmlspecialchars($m->sender_name),
                'sender_role' => $m->sender_role,
                'is_me'       => in_array($m->sender_role, ['dosen', 'user']),
                'message'     => nl2br(htmlspecialchars($m->message)),
                'attachment'  => $m->attachment ? base_url('uploads/help_attachments/' . $m->attachment) : null,
                'is_read'     => (int)$m->is_read,
                'time'        => date('H:i', strtotime($m->created_at)),
                'date_full'   => date('d M Y, H:i', strtotime($m->created_at)),
                'created_at'  => $m->created_at
            ];
        }

        // Unread count untuk semua 3 channel
        $statsLaboran = $this->Help_chat_model->get_stats('laboran', $userId);
        $statsKaur    = $this->Help_chat_model->get_stats('kaur', $userId);
        $statsAdmin   = $this->Help_chat_model->get_stats('admin_layanan', $userId);

        echo json_encode([
            'status'       => 'success',
            'conversation' => [
                'id'                => (string)$conv->id,
                'target_role'       => htmlspecialchars($conv->target_role ?? $target_role),
                'target_role_label' => $this->_get_target_role_label($conv->target_role ?? $target_role),
                'topik'             => htmlspecialchars($conv->topik),
                'status'            => $conv->status,
                'last_message_time' => $this->_format_time_ago($conv->last_message_time ?? $conv->created_at),
                'created_at'        => date('d M Y, H:i', strtotime($conv->created_at))
            ],
            'messages'          => $formattedMessages,
            'channels_presence' => $channelsPresence,
            'unreads'           => [
                'laboran'       => $statsLaboran['unread'] ?? 0,
                'kaur'          => $statsKaur['unread'] ?? 0,
                'admin_layanan' => $statsAdmin['unread'] ?? 0
            ]
        ]);
        exit;
    }

    /**
     * AJAX: Buat percakapan baru atau kirim pesan pertama
     */
    public function help_create_chat_ajax() {
        header('Content-Type: application/json');

        $target_role = strtolower(trim($this->input->post('target_role') ?? 'laboran'));
        $topik       = trim($this->input->post('topik') ?? '');
        $message     = trim($this->input->post('message') ?? '');

        if (!in_array($target_role, ['laboran', 'kaur', 'admin_layanan'])) {
            $target_role = 'laboran';
        }

        if (empty($topik) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Topik dan pesan bantuan wajib diisi.']);
            exit;
        }

        $profile = $this->_get_current_user_profile();
        if (!$profile['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi login telah berakhir.']);
            exit;
        }

        $userId    = $profile['id'];
        $userNama  = $profile['nama'];
        $userEmail = $profile['email'];
        $userNip   = $profile['nip'];

        $conv_id = $this->Help_chat_model->create_conversation([
            'user_id'      => $userId,
            'user_nama'    => $userNama,
            'user_email'   => $userEmail,
            'user_role'    => 'Dosen',
            'user_nim_nip' => $userNip,
            'target_role'  => $target_role,
            'sender_role'  => 'dosen',
            'topik'        => $topik,
            'message'      => $message
        ]);

        if ($conv_id) {
            echo json_encode([
                'status'          => 'success',
                'conversation_id' => (string)$conv_id,
                'message'         => 'Permintaan bantuan berhasil dikirim ke ' . $this->_get_target_role_label($target_role) . '.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat percakapan bantuan.']);
        }
        exit;
    }

    /**
     * AJAX: Kirim pesan balasan
     */
    public function help_send_message_ajax() {
        header('Content-Type: application/json');

        $conversation_id = trim($this->input->post('conversation_id') ?? '');
        $message         = trim($this->input->post('message') ?? '');
        $target_role     = strtolower(trim($this->input->post('target_role') ?? 'laboran'));

        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Pesan tidak boleh kosong.']);
            exit;
        }

        $profile = $this->_get_current_user_profile();
        if (!$profile['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi login telah berakhir.']);
            exit;
        }

        $userId    = $profile['id'];
        $userNama  = $profile['nama'];
        $userEmail = $profile['email'];
        $userNip   = $profile['nip'];

        $conversation = !empty($conversation_id) ? $this->Help_chat_model->get_conversation_by_id($conversation_id) : null;
        if (!$conversation) {
            // Auto-heal / fallback: cari atau buat channel aktif untuk user ini
            $conversation = $this->Help_chat_model->get_or_create_channel($userId, $userNama, $userEmail, 'Dosen', $userNip, $target_role);
            if ($conversation) {
                $conversation_id = (string)$conversation->id;
            }
        }

        if (!$conversation) {
            echo json_encode(['status' => 'error', 'message' => 'Percakapan tidak ditemukan.']);
            exit;
        }
        $msgId = $this->Help_chat_model->send_message(
            $conversation_id,
            $userId,
            $userNama,
            'dosen',
            $message
        );

        if ($msgId) {
            echo json_encode([
                'status'          => 'success',
                'conversation_id' => $conversation_id,
                'message'         => 'Pesan terkirim.',
                'data'            => [
                    'id'          => (string)$msgId,
                    'sender_name' => $userNama,
                    'sender_role' => 'dosen',
                    'is_me'       => true,
                    'message'     => nl2br(htmlspecialchars($message)),
                    'time'        => date('H:i'),
                    'date_full'   => date('d M Y, H:i')
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengirim pesan.']);
        }
        exit;
    }
    public function help_toggle_status_ajax() {
        header('Content-Type: application/json');

        $conversation_id = $this->input->post('conversation_id', true);
        $status          = $this->input->post('status', true);

        if (empty($conversation_id) || !in_array($status, ['open', 'resolved'])) {
            echo json_encode(['status' => 'error', 'message' => 'Parameter status tidak valid.']);
            exit;
        }

        $profile = $this->_get_current_user_profile();
        if (!$profile['id']) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi login telah berakhir.']);
            exit;
        }

        $userId   = $profile['id'];
        $userNama = $profile['nama'];

        $updated = $this->Help_chat_model->update_status($conversation_id, $status);

        if ($updated) {
            $sysMsg = ($status === 'resolved')
                ? '[Sistem] Tiket bantuan ini telah ditandai Selesai oleh Dosen.'
                : '[Sistem] Tiket bantuan telah dibuka kembali oleh Dosen.';

            $this->Help_chat_model->send_message($conversation_id, $userId, $userNama, 'dosen', $sysMsg);

            echo json_encode([
                'status'     => 'success',
                'new_status' => $status,
                'message'    => 'Status tiket bantuan berhasil diperbarui.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status tiket.']);
        }
        exit;
    }

    /**
     * AJAX: Daftar preset topik bantuan per target role
     */
    public function help_quick_topics_ajax() {
        header('Content-Type: application/json');
        $target = $this->input->get('target', true) ?: 'laboran';

        $topics = [
            'laboran' => [
                'Kebutuhan Alat & Perangkat untuk Riset / Pengujian',
                'Peminjaman Ruang Lab untuk Kegiatan Praktikum / Demo',
                'Laporan Kerusakan Hardware atau Jaringan di Lab',
                'Permohonan Akses Komputer Khusus / Server Lab',
                'Konfirmasi Jadwal Penggunaan Ruang Laboratorium'
            ],
            'kaur' => [
                'Konsultasi Kebijakan Peminjaman Fasilitas Lab',
                'Pengajuan Surat Rekomendasi atau Izin Khusus',
                'Koordinasi Jadwal Penggunaan Ruang Sidang',
                'Informasi Prosedur Peminjaman Alat Resmi',
                'Permintaan Persetujuan Kegiatan di Luar Jam Operasional'
            ],
            'admin_layanan' => [
                'Verifikasi Berkas Mahasiswa Bimbingan',
                'Konfirmasi SK Dosen Pembimbing & Penguji',
                'Informasi Syarat Yudisium & Bebas Lab Mahasiswa',
                'Penerbitan Berita Acara Penilaian Sidang',
                'Update Data Mahasiswa & Status Kelulusan TA'
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'data'   => $topics[$target] ?? $topics['laboran']
        ]);
        exit;
    }

    /**
     * Helper: Label target role
     */
    private function _get_target_role_label($role) {
        switch ($role) {
            case 'laboran':       return 'Laboran (Lab & Alat)';
            case 'kaur':          return 'Ka. Ur (Kepala Urusan)';
            case 'admin_layanan': return 'Admin Layanan (LAA)';
            default:              return 'Staff Layanan';
        }
    }

    /**
     * Helper: Format waktu relatif
     */
    private function _format_time_ago($datetime) {
        if (empty($datetime)) return '-';
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60)          return 'Baru saja';
        elseif ($diff < 3600)    return floor($diff / 60) . ' mnt lalu';
        elseif ($diff < 86400)   return floor($diff / 3600) . ' jam lalu';
        elseif ($diff < 172800)  return 'Kemarin, ' . date('H:i', $timestamp);
        else                     return date('d M Y', $timestamp);
    }

    /**
     * AJAX: Heartbeat ping untuk memperbarui status online Dosen & mengambil status terkini petugas
     */
    public function help_heartbeat_ajax() {
        header('Content-Type: application/json');

        $profile   = $this->_get_current_user_profile();
        $userId    = $profile['id'] ?: $this->session->userdata('user_id');
        $roleId    = (int)$this->session->userdata('role_id') ?: 3;
        $userNama  = $profile['nama'] ?: 'Dosen';
        $isOnline  = (int)($this->input->post('is_online') !== null ? $this->input->post('is_online') : 1);
        session_write_close();

        if ($userId) {
            $this->Help_chat_model->update_presence($userId, $roleId, $userNama, 'dosen', $isOnline);
        }
        $channelsPresence = $this->Help_chat_model->get_channels_presence();

        echo json_encode([
            'status'            => 'success',
            'timestamp'         => date('Y-m-d H:i:s'),
            'channels_presence' => $channelsPresence
        ]);
        exit;
    }

    /**
     * Dapatkan profil dosen yang sedang login secara dinamis
     */
    private function _get_current_user_profile() {
        $userId = $this->session->userdata('user_id') ?: ($this->session->userdata('id') ?: null);
        $nama   = $this->session->userdata('name');
        $email  = $this->session->userdata('email');
        $nip    = $this->session->userdata('nip') ?: ($this->session->userdata('nidn_nim') ?: $this->session->userdata('username'));

        if (!$userId && ($nip || $email)) {
            $user = null;
            if ($nip) {
                $user = $this->db->get_where('user', ['nidn_nim' => $nip])->row();
                if (!$user) {
                    $user = $this->db->get_where('user', ['username' => $nip])->row();
                }
            }
            if (empty($user) && $email) {
                $user = $this->db->get_where('user', ['email' => $email])->row();
            }
            if (!empty($user)) {
                $userId = $user->id;
                $nama   = $nama ?: $user->name;
                $email  = $email ?: $user->email;
            }
        } elseif ($userId && (!$nama || !$email || !$nip)) {
            $user = $this->db->get_where('user', ['id' => $userId])->row();
            if ($user) {
                $nama  = $nama ?: $user->name;
                $email = $email ?: $user->email;
                $nip   = $nip ?: (!empty($user->nip) ? $user->nip : (!empty($user->nidn_nim) ? $user->nidn_nim : $user->username));
            }
        }

        return [
            'id'    => !empty($userId) ? (string)$userId : null,
            'nama'  => $nama ?: 'Dosen',
            'email' => $email ?: '',
            'nip'   => $nip ?: '-'
        ];
    }
}
