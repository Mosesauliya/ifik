<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LaboranHelp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'text']);
        $this->load->model('Help_chat_model');
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Halaman Utama Help Desk / Live Chat Laboran / Ka. Ur / Admin Layanan
     */
    public function index() {
        $roleId = (int)$this->session->userdata('role_id');
        $email = strtolower((string)$this->session->userdata('email'));
        $currentUri = trim(uri_string(), '/');

        $roleLabel = 'Panel Laboran';
        $channelTitle = 'Bantuan & Live Chat Lab';
        $channelDesc = 'Pusat help desk interaktif untuk menjawab pertanyaan, kendala praktikum, dan izin lab secara langsung.';
        $targetRole = 'laboran';

        if (strpos($currentUri, 'kaur') === 0 || ($roleId === 2 && strpos($email, 'laboran') === false)) {
            $roleLabel = 'Panel Ka. Ur / Ka Lab';
            $channelTitle = 'Bantuan & Live Chat';
            $channelDesc = 'Pusat help desk interaktif untuk konsultasi permohonan ruangan, persetujuan resmi, dan layanan lab.';
            $targetRole = 'kaur';
        } elseif (strpos($currentUri, 'adminlayanan') === 0 || $roleId === 5) {
            $roleLabel = 'Panel Admin Layanan';
            $channelTitle = 'Bantuan & Live Chat LAA';
            $channelDesc = 'Pusat help desk interaktif untuk menjawab pertanyaan dan kendala layanan administrasi akademik.';
            $targetRole = 'admin_layanan';
        } elseif ($roleId === 1) {
            $roleLabel = 'Panel Admin';
            $channelTitle = 'Bantuan & Live Chat';
            $channelDesc = 'Pusat help desk interaktif untuk memonitor percakapan dan bantuan sistem.';
            $targetRole = 'all';
        }

        $data['title'] = $channelTitle . ' - ' . $roleLabel;
        $data['panelRole'] = $roleLabel;
        $data['channelTitle'] = $channelTitle;
        $data['channelDesc'] = $channelDesc;
        $data['targetRole'] = $targetRole;
        $data['stats'] = $this->Help_chat_model->get_stats($targetRole);
        $data['conversations'] = $this->Help_chat_model->get_conversations('all', '', $targetRole);

        $this->load->view('laboran/help/index', $data);
    }

    /**
     * Endpoint AJAX: List percakapan & stats terupdate (untuk live polling)
     */
    public function get_conversations_ajax() {
        header('Content-Type: application/json');

        $status = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');
        $targetRole = $this->input->get('target_role', true);

        if (empty($targetRole)) {
            $roleId = (int)$this->session->userdata('role_id');
            $email = strtolower((string)$this->session->userdata('email'));
            if ($roleId === 2 && strpos($email, 'laboran') === false) {
                $targetRole = 'kaur';
            } elseif ($roleId === 5) {
                $targetRole = 'admin_layanan';
            } elseif ($roleId === 1) {
                $targetRole = 'all';
            } else {
                $targetRole = 'laboran';
            }
        }

        $conversations = $this->Help_chat_model->get_conversations($status, $search, $targetRole);
        $stats = $this->Help_chat_model->get_stats($targetRole);

        $formatted = [];
        foreach ($conversations as $c) {
            $formatted[] = [
                'id'                => (int)$c->id,
                'user_id'           => $c->user_id,
                'user_nama'         => htmlspecialchars($c->user_nama ?? 'Pengguna'),
                'user_email'        => htmlspecialchars($c->user_email ?? ''),
                'user_role'         => htmlspecialchars($c->user_role ?? 'Mahasiswa'),
                'user_nim_nip'      => htmlspecialchars($c->user_nim_nip ?? '-'),
                'topik'             => htmlspecialchars($c->topik ?? 'Bantuan Umum'),
                'status'            => $c->status,
                'last_message'      => htmlspecialchars($c->last_message ?? ''),
                'last_message_time' => $this->_format_time_ago($c->last_message_time ?? $c->created_at),
                'unread_laboran'    => (int)$c->unread_laboran,
                'unread_user'       => (int)$c->unread_user,
                'created_at'        => date('d M Y, H:i', strtotime($c->created_at))
            ];
        }

        echo json_encode([
            'status' => 'success',
            'stats'  => $stats,
            'data'   => $formatted
        ]);
        exit;
    }

    /**
     * Endpoint AJAX: Ambil detail percakapan dan seluruh isi pesan (auto-mark read)
     */
    public function get_messages_ajax($conversation_id) {
        header('Content-Type: application/json');

        $conversation = $this->Help_chat_model->get_conversation_by_id($conversation_id);
        if (!$conversation) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Percakapan tidak ditemukan.'
            ]);
            exit;
        }

        // Tandai sudah dibaca oleh laboran
        $this->Help_chat_model->mark_as_read_by_laboran($conversation_id);

        $messages = $this->Help_chat_model->get_messages($conversation_id);
        $formattedMessages = [];

        foreach ($messages as $m) {
            $formattedMessages[] = [
                'id'          => (int)$m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => htmlspecialchars($m->sender_name),
                'sender_role' => $m->sender_role,
                'message'     => nl2br(htmlspecialchars($m->message)),
                'attachment'  => $m->attachment ? base_url('uploads/help_attachments/' . $m->attachment) : null,
                'is_read'     => (int)$m->is_read,
                'time'        => date('H:i', strtotime($m->created_at)),
                'date_full'   => date('d M Y, H:i', strtotime($m->created_at)),
                'created_at'  => $m->created_at
            ];
        }

        echo json_encode([
            'status'       => 'success',
            'conversation' => [
                'id'                => (int)$conversation->id,
                'user_nama'         => htmlspecialchars($conversation->user_nama),
                'user_email'        => htmlspecialchars($conversation->user_email ?? ''),
                'user_role'         => htmlspecialchars($conversation->user_role ?? 'Mahasiswa'),
                'user_nim_nip'      => htmlspecialchars($conversation->user_nim_nip ?? '-'),
                'topik'             => htmlspecialchars($conversation->topik),
                'status'            => $conversation->status,
                'laboran_nama'      => htmlspecialchars($conversation->laboran_nama ?? 'Belum ada'),
                'last_message_time' => $this->_format_time_ago($conversation->last_message_time ?? $conversation->created_at),
                'created_at'        => date('d M Y, H:i', strtotime($conversation->created_at))
            ],
            'messages'     => $formattedMessages
        ]);
        exit;
    }

    /**
     * Endpoint AJAX: Kirim balasan pesan dari Laboran
     */
    public function send_message_ajax() {
        header('Content-Type: application/json');

        $conversation_id = $this->input->post('conversation_id', true);
        $message = trim($this->input->post('message', true) ?? '');

        if (empty($conversation_id) || empty($message)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Pesan tidak boleh kosong.'
            ]);
            exit;
        }

        $conversation = $this->Help_chat_model->get_conversation_by_id($conversation_id);
        if (!$conversation) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Percakapan tidak valid.'
            ]);
            exit;
        }

        // Ambil nama dan role pengirim dari session atau default
        $roleId = (int)$this->session->userdata('role_id');
        $email = strtolower((string)$this->session->userdata('email'));
        $senderRole = 'laboran';
        $defaultSenderName = 'Petugas Laboran';

        if ($roleId === 2 && strpos($email, 'laboran') === false) {
            $senderRole = 'kaur';
            $defaultSenderName = 'Ka. Ur Laboratorium';
        } elseif ($roleId === 5) {
            $senderRole = 'admin_layanan';
            $defaultSenderName = 'Admin Layanan Akademik';
        } elseif ($roleId === 1) {
            $senderRole = 'admin';
            $defaultSenderName = 'Admin Sistem';
        }

        $laboranId = $this->session->userdata('user_id') ?: 1;
        $laboranName = $this->session->userdata('name') ?: $defaultSenderName;

        $msgId = $this->Help_chat_model->send_message(
            $conversation_id,
            $laboranId,
            $laboranName,
            $senderRole,
            $message
        );

        if ($msgId) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Pesan terkirim.',
                'data'    => [
                    'id'          => $msgId,
                    'sender_name' => $laboranName,
                    'sender_role' => $senderRole,
                    'message'     => nl2br(htmlspecialchars($message)),
                    'time'        => date('H:i'),
                    'date_full'   => date('d M Y, H:i')
                ]
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal mengirim pesan.'
            ]);
        }
        exit;
    }

    /**
     * Endpoint AJAX: Toggle status open <-> resolved
     */
    public function toggle_status_ajax() {
        header('Content-Type: application/json');

        $conversation_id = $this->input->post('conversation_id', true);
        $status = $this->input->post('status', true);

        if (empty($conversation_id) || !in_array($status, ['open', 'resolved'])) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Parameter tidak valid.'
            ]);
            exit;
        }

        $laboranId = $this->session->userdata('user_id') ?: 1;
        $laboranName = $this->session->userdata('name') ?: 'Petugas Laboran';

        $updated = $this->Help_chat_model->update_status($conversation_id, $status, $laboranId, $laboranName);

        if ($updated) {
            // Jika resolved, tambahkan pesan sistem otomatis
            if ($status === 'resolved') {
                $this->Help_chat_model->send_message(
                    $conversation_id,
                    $laboranId,
                    $laboranName,
                    'laboran',
                    '[Sistem] Tiket bantuan ini telah ditandai Selesai oleh Laboran. Silakan kirim pesan baru jika masih membutuhkan bantuan.'
                );
            } else {
                $this->Help_chat_model->send_message(
                    $conversation_id,
                    $laboranId,
                    $laboranName,
                    'laboran',
                    '[Sistem] Tiket bantuan telah dibuka kembali oleh Laboran.'
                );
            }

            echo json_encode([
                'status'     => 'success',
                'new_status' => $status,
                'message'    => 'Status tiket bantuan berhasil diperbarui.'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status.'
            ]);
        }
        exit;
    }

    /**
     * Endpoint AJAX: Daftar Quick Replies (Template Balasan Cepat)
     */
    public function quick_replies_ajax() {
        header('Content-Type: application/json');

        $templates = [
            [
                'title' => 'Salam & Konfirmasi Permintaan',
                'text'  => "Halo! Terima kasih telah menghubungi Help Desk Lab IFIK. Permintaan/pertanyaan Anda sedang kami tindak lanjuti oleh tim petugas laboratorium."
            ],
            [
                'title' => 'SOP Peminjaman Ruangan',
                'text'  => "Untuk peminjaman ruangan Lab, silakan ajukan booking terlebih dahulu melalui menu 'Ajukan Peminjaman Ruangan' di portal IFIK minimal H-2 sebelum kegiatan."
            ],
            [
                'title' => 'Jam Operasional Laboratorium',
                'text'  => "Jam operasional layanan Laboratorium IFIK adalah hari Senin s/d Jumat pukul 08.00 - 16.30 WIB (Istirahat 12.00 - 13.00 WIB)."
            ],
            [
                'title' => 'Laporan Kerusakan & Pengecekan Alat',
                'text'  => "Laporan kendala hardware/alat lab telah dicatat. Tim teknisi kami akan segera melakukan inspeksi langsung ke ruangan lab terkait."
            ],
            [
                'title' => 'Penyelesaian Tiket Bantuan',
                'text'  => "Kendala telah terselesaikan dengan baik. Jika sudah tidak ada pertanyaan lain, kami izin menyelesaikan sesi bantuan ini. Terima kasih!"
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'data'   => $templates
        ]);
        exit;
    }

    /**
     * Endpoint API/AJAX: Pembuatan tiket chat baru oleh User / Mahasiswa / Dosen
     */
    public function create_chat_user_ajax() {
        header('Content-Type: application/json');

        $topik = trim($this->input->post('topik', true) ?? '');
        $message = trim($this->input->post('message', true) ?? '');

        if (empty($topik) || empty($message)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Topik dan pesan pertanyaan wajib diisi.'
            ]);
            exit;
        }

        // Ambil data user dari session atau POST data
        $userId = $this->session->userdata('user_id') ?: ($this->input->post('user_id', true) ?: null);
        $userNama = $this->session->userdata('name') ?: ($this->input->post('user_nama', true) ?: 'Pengguna IFIK');
        $userEmail = $this->session->userdata('email') ?: ($this->input->post('user_email', true) ?: null);
        $userNimNip = $this->session->userdata('nidn_nim') ?: ($this->session->userdata('nim') ?: ($this->input->post('user_nim_nip', true) ?: '-'));
        
        $roleId = (int)($this->session->userdata('role_id') ?? 4);
        $roleMap = [
            1 => 'Admin',
            2 => 'Kaur',
            3 => 'Dosen',
            4 => 'Mahasiswa',
            5 => 'Admin Layanan',
            6 => 'Koordinator TA',
            9 => 'Ketua KK',
            21 => 'Laboran'
        ];
        $userRole = $this->input->post('user_role', true) ?: ($roleMap[$roleId] ?? 'Mahasiswa');

        $conv_id = $this->Help_chat_model->create_conversation([
            'user_id'      => $userId,
            'user_nama'    => $userNama,
            'user_email'   => $userEmail,
            'user_role'    => $userRole,
            'user_nim_nip' => $userNimNip,
            'topik'        => $topik,
            'message'      => $message
        ]);

        if ($conv_id) {
            echo json_encode([
                'status'          => 'success',
                'conversation_id' => (int)$conv_id,
                'message'         => 'Pesan berhasil terkirim ke Help Desk Laboran.'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal membuat sesi bantuan.'
            ]);
        }
        exit;
    }

    /**
     * Endpoint API/AJAX: Pengiriman pesan balasan dari sisi User / Mahasiswa / Dosen
     */
    public function send_user_message_ajax() {
        header('Content-Type: application/json');

        $conversation_id = $this->input->post('conversation_id', true);
        $message = trim($this->input->post('message', true) ?? '');

        if (empty($conversation_id) || empty($message)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Pesan tidak boleh kosong.'
            ]);
            exit;
        }

        $conversation = $this->Help_chat_model->get_conversation_by_id($conversation_id);
        if (!$conversation) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Percakapan tidak ditemukan.'
            ]);
            exit;
        }

        $senderId = $this->session->userdata('user_id') ?: ($this->input->post('sender_id', true) ?: $conversation->user_id);
        $senderName = $this->session->userdata('name') ?: ($this->input->post('sender_name', true) ?: $conversation->user_nama);

        $msgId = $this->Help_chat_model->send_message(
            $conversation_id,
            $senderId,
            $senderName,
            'user',
            $message
        );

        if ($msgId) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Pesan terkirim ke Laboran.',
                'data'    => [
                    'id'          => $msgId,
                    'sender_name' => $senderName,
                    'sender_role' => 'user',
                    'message'     => nl2br(htmlspecialchars($message)),
                    'time'        => date('H:i'),
                    'date_full'   => date('d M Y, H:i')
                ]
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal mengirim pesan.'
            ]);
        }
        exit;
    }

    /**
     * Endpoint API/AJAX: Ambil daftar riwayat percakapan milik user yang sedang login
     */
    public function my_conversations_ajax() {
        header('Content-Type: application/json');

        $userId = $this->session->userdata('user_id');
        $userEmail = $this->session->userdata('email');

        $this->db->from('help_conversations');
        if ($userId) {
            $this->db->where('user_id', $userId);
            if ($userEmail) {
                $this->db->or_where('user_email', $userEmail);
            }
        } elseif ($userEmail) {
            $this->db->where('user_email', $userEmail);
        }
        $this->db->order_by('last_message_time', 'DESC');
        $conversations = $this->db->get()->result();

        $formatted = [];
        foreach ($conversations as $c) {
            $formatted[] = [
                'id'                => (int)$c->id,
                'topik'             => htmlspecialchars($c->topik),
                'status'            => $c->status,
                'last_message'      => htmlspecialchars($c->last_message ?? ''),
                'last_message_time' => $this->_format_time_ago($c->last_message_time ?? $c->created_at),
                'unread_user'       => (int)$c->unread_user,
                'created_at'        => date('d M Y, H:i', strtotime($c->created_at))
            ];
        }

        echo json_encode([
            'status' => 'success',
            'data'   => $formatted
        ]);
        exit;
    }

    /**
     * Endpoint AJAX: Buat demo simulasi chat baru (untuk testing laboran)
     */
    public function create_sample_ajax() {
        header('Content-Type: application/json');

        $topics = [
            'Izin Penggunaan PC High-End untuk Render 3D',
            'Pertanyaan Ketersediaan Sensor ESP32 di Lab IoT',
            'Koneksi Internet LAN Lab Software Terputus',
            'Peminjaman Mikrokontroler Arduino untuk Tugas Mata Kuliah',
            'Permintaan Akses Masuk Ruang Server Studio'
        ];

        $names = [
            ['nama' => 'Bintang Alamsyah', 'role' => 'Mahasiswa', 'nim' => '1301210998', 'email' => 'bintang@student.telkomuniversity.ac.id'],
            ['nama' => 'Siti Nurhaliza', 'role' => 'Mahasiswa', 'nim' => '1301210776', 'email' => 'siti@student.telkomuniversity.ac.id'],
            ['nama' => 'Fajar Nugraha, S.Kom., M.Cs.', 'role' => 'Dosen', 'nim' => '199008202022031002', 'email' => 'fajarnugraha@telkomuniversity.ac.id'],
            ['nama' => 'Diki Pratama', 'role' => 'Mahasiswa', 'nim' => '1301210332', 'email' => 'diki@student.telkomuniversity.ac.id']
        ];

        $person = $names[array_rand($names)];
        $topic = $topics[array_rand($topics)];

        $conv_id = $this->Help_chat_model->create_conversation([
            'user_id'      => rand(100, 999),
            'user_nama'    => $person['nama'],
            'user_email'   => $person['email'],
            'user_role'    => $person['role'],
            'user_nim_nip' => $person['nim'],
            'topik'        => $topic,
            'message'      => "Halo admin/laboran, saya ingin bertanya terkait {$topic}. Apakah bisa dibantu informasinya?"
        ]);

        echo json_encode([
            'status'          => 'success',
            'conversation_id' => $conv_id,
            'message'         => 'Simulasi chat baru berhasil dibuat.'
        ]);
        exit;
    }

    /**
     * Helper pemformat waktu relatif (misal: 5 mnt lalu, 2 jam lalu, Kemarin)
     */
    private function _format_time_ago($datetime) {
        if (empty($datetime)) return '-';

        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' mnt lalu';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' jam lalu';
        } elseif ($diff < 172800) {
            return 'Kemarin, ' . date('H:i', $timestamp);
        } else {
            return date('d M Y', $timestamp);
        }
    }
}
