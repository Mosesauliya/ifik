<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->_ensure_tables();
    }

    private function _ensure_tables()
    {
        // Amankan agar tidak memodifikasi atau ALTER TABLE ruangan di db_ifik_baru
        return;


        if (!$this->db->table_exists('peminjaman')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `peminjaman` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `id_user` INT NULL,
                `id_ruangan` INT NOT NULL,
                `nama_lengkap` VARCHAR(150) NOT NULL,
                `keterangan` TEXT NULL,
                `tanggal_mulai` DATE NOT NULL,
                `tanggal_selesai` DATE NOT NULL,
                `jam_mulai` TIME NOT NULL,
                `jam_selesai` TIME NOT NULL,
                `status` VARCHAR(50) DEFAULT 'Pending',
                `alasan_penolakan` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->db->query("INSERT IGNORE INTO `peminjaman` (`id_ruangan`, `nama_lengkap`, `keterangan`, `tanggal_mulai`, `tanggal_selesai`, `jam_mulai`, `jam_selesai`, `status`) VALUES
                (1, 'Alif Mahasiswa', 'Kegiatan Pameran Interaktif 3D', CURDATE(), CURDATE(), '08:00:00', '12:00:00', 'Disetujui Admin')");
        } else {
            // Auto-migrasi: pastikan kolom id_user tersedia
            $fields_peminjaman = $this->db->list_fields('peminjaman');
            if (!in_array('id_user', $fields_peminjaman)) {
                $this->db->query("ALTER TABLE `peminjaman` ADD COLUMN `id_user` INT NULL AFTER `id`");
            }
        }

        if (!$this->db->table_exists('slot_waktu')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `slot_waktu` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nama_slot` VARCHAR(50) NOT NULL,
                `jam_mulai` TIME NOT NULL,
                `jam_selesai` TIME NOT NULL,
                `urutan` INT DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->db->query("INSERT IGNORE INTO `slot_waktu` (`id`, `nama_slot`, `jam_mulai`, `jam_selesai`, `urutan`) VALUES
                (1, 'Sesi Pagi 1', '08:00:00', '10:00:00', 1),
                (2, 'Sesi Pagi 2', '10:00:00', '12:00:00', 2),
                (3, 'Sesi Siang 1', '13:00:00', '15:00:00', 3),
                (4, 'Sesi Siang 2', '15:00:00', '17:00:00', 4)");
        }
    }

    public function _join_kategori($alias = 'k', $room_alias = 'r')
    {
        if ($this->db->table_exists('kategori_ruangan')) {
            $this->db->join("kategori_ruangan {$alias}", "{$alias}.id = {$room_alias}.id_kategori", 'left');
        } elseif ($this->db->table_exists('kategori')) {
            $this->db->join("kategori {$alias}", "{$alias}.id_kategori = {$room_alias}.id_kategori", 'left');
        }
    }

    public function _join_kategori_table($room_col = 'ruangan.id_kategori')
    {
        if ($this->db->table_exists('kategori_ruangan')) {
            $this->db->join('kategori_ruangan k', "k.id = {$room_col}", 'left');
        } elseif ($this->db->table_exists('kategori')) {
            $this->db->join('kategori k', "k.id_kategori = {$room_col}", 'left');
        }
    }

    public function get_all_kategori()
    {
        if ($this->db->table_exists('kategori_ruangan')) {
            return $this->db->get('kategori_ruangan')->result();
        } elseif ($this->db->table_exists('kategori')) {
            $this->db->select('*, id_kategori AS id');
            return $this->db->get('kategori')->result();
        }
        return [];
    }

    public function get_all_ruangan()
    {
        $this->db->select('r.id, r.id_kategori, r.ruangan, r.kapasitas, r.akses, r.images, r.date, k.nama_kategori');
        $this->db->from('ruangan r');
        if ($this->db->table_exists('kategori_ruangan')) {
            $this->db->join('kategori_ruangan k', 'k.id = r.id_kategori', 'left');
        } elseif ($this->db->table_exists('kategori')) {
            $this->db->join('kategori k', 'k.id_kategori = r.id_kategori', 'left');
        }
        $this->db->order_by('r.id', 'ASC');
        $results = $this->db->get()->result();
        foreach ($results as &$r) {
            $r->nama_ruangan = $r->ruangan;
            $r->kode_ruangan = $r->id;
            $r->status = !empty($r->akses) ? $r->akses : 'Tersedia';
            $r->foto = $r->images;
            $r->lokasi = 'Gedung Sebatik (FIK)';
            $r->model_3d = '';
            $r->tagline = '';
            $r->jumlah_unit = ($r->kapasitas ?: '0') . ' Orang';
            $r->jam_operasional = '08:00 - 17:00 WIB';
            $r->deskripsi = '';
            $r->spesifikasi_fasilitas = '';
            $r->tata_tertib = '';
            $r->nama_kategori = !empty($r->nama_kategori) ? $r->nama_kategori : 'Umum';
            $this->parse_room_media($r);
        }
        return $results;
    }

    public function get_ruangan_by_kategori($id_kategori)
    {
        $this->db->select('r.id, r.id_kategori, r.ruangan, r.kapasitas, r.akses, r.images, r.date, k.nama_kategori');
        $this->db->from('ruangan r');
        if ($this->db->table_exists('kategori_ruangan')) {
            $this->db->join('kategori_ruangan k', 'k.id = r.id_kategori', 'left');
        } elseif ($this->db->table_exists('kategori')) {
            $this->db->join('kategori k', 'k.id_kategori = r.id_kategori', 'left');
        }
        $this->db->where('r.id_kategori', $id_kategori);
        $results = $this->db->get()->result();
        foreach ($results as &$r) {
            $r->nama_ruangan = $r->ruangan;
            $r->kode_ruangan = $r->id;
            $r->status = !empty($r->akses) ? $r->akses : 'Tersedia';
            $r->foto = $r->images;
            $r->lokasi = 'Gedung Sebatik (FIK)';
            $r->model_3d = '';
            $r->tagline = '';
            $r->jumlah_unit = ($r->kapasitas ?: '0') . ' Orang';
            $r->jam_operasional = '08:00 - 17:00 WIB';
            $r->deskripsi = '';
            $r->spesifikasi_fasilitas = '';
            $r->tata_tertib = '';
            $r->nama_kategori = !empty($r->nama_kategori) ? $r->nama_kategori : 'Umum';
            $this->parse_room_media($r);
        }
        return $results;
    }

    /**
     * Parsing field images yang bisa menyimpan foto dan/atau model_3d dipisahkan pipe '|'
     * Format: foto_path|model_3d_path
     */
    public function parse_room_media(&$r)
    {
        if (!$r) return;
        $raw = isset($r->images) ? trim((string)$r->images) : (isset($r->foto) ? trim((string)$r->foto) : '');
        if (strpos($raw, '|') !== false) {
            list($foto, $model) = explode('|', $raw, 2);
            $r->foto = !empty($foto) ? trim($foto) : '';
            $r->model_3d = !empty($model) ? trim($model) : '';
        } else {
            $ext = strtolower(pathinfo($raw, PATHINFO_EXTENSION));
            if (in_array($ext, ['glb', 'gltf', 'fbx', 'obj'])) {
                $r->foto = '';
                $r->model_3d = $raw;
            } else {
                $r->foto = $raw;
                if (!isset($r->model_3d) || empty($r->model_3d)) {
                    $r->model_3d = '';
                }
            }
        }
    }


    public function get_all_slot_waktu()
    {
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('slot_waktu')->result();
    }

    public function get_all_peminjaman()
    {
        // Prioritaskan tabel booking asli db_ifik_baru
        if ($this->db->table_exists('booking')) {
            $user_table = $this->db->table_exists('user') ? 'user' : 'users';
            $this->db->select('b.id, b.id_peminjam, b.id_ruangan, b.keterangan, b.date, b.time, b.status, b.komentar, b.date_created, r.ruangan, r.id AS kode_ruangan, r.id_kategori, r.kapasitas, u.name AS user_name');
            $this->db->from('booking b');
            $this->db->join('ruangan r', 'r.id = b.id_ruangan', 'left');
            $this->db->join("{$user_table} u", 'u.id = b.id_peminjam', 'left');
            $this->db->order_by('b.date', 'DESC');
            $raw = $this->db->get()->result();
            foreach ($raw as &$b) {
                $b->id_user = $b->id_peminjam;
                $b->nama_lengkap = !empty($b->user_name) ? $b->user_name : 'Pengguna';
                $b->tanggal_mulai = $b->date;
                $b->tanggal_selesai = $b->date;
                $b->jam_mulai = '08:00';
                $b->jam_selesai = '17:00';
                $b->alasan_penolakan = $b->komentar;
                $b->created_at = $b->date_created;
                $b->nama_ruangan = $b->ruangan;
                $b->lokasi = 'Gedung Sebatik (FIK)';
                $b->foto = 'default.jpg';
                $b->nama_kategori = 'Umum';
            }
            return $raw;
        }

        if ($this->db->table_exists('peminjaman')) {
            $this->db->select('peminjaman.*, ruangan.ruangan AS nama_ruangan, ruangan.id AS kode_ruangan, ruangan.id_kategori, "Gedung Sebatik (FIK)" AS lokasi, ruangan.kapasitas');
            $this->db->from('peminjaman');
            $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
            $this->db->order_by('peminjaman.created_at', 'DESC');
            $results = $this->db->get()->result();
            foreach ($results as &$p) {
                $p->foto = 'default.jpg';
                $p->nama_kategori = 'Umum';
            }
            return $results;
        }

        return [];
    }

    public function get_peminjaman_by_user($user_id, $nama_lengkap = null)
    {
        if ($this->db->table_exists('peminjaman')) {
            $this->db->select('peminjaman.*, ruangan.ruangan AS nama_ruangan, ruangan.id AS kode_ruangan, ruangan.id_kategori, "Gedung Sebatik (FIK)" AS lokasi, ruangan.kapasitas, ruangan.images AS foto, COALESCE(k.nama_kategori, "Umum") AS nama_kategori');
            $this->db->from('peminjaman');
            $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
            $this->_join_kategori_table('ruangan.id_kategori');

            $this->db->group_start();
            if (!empty($user_id)) {
                $this->db->where('peminjaman.id_user', $user_id);
            }
            if (!empty($nama_lengkap)) {
                if (!empty($user_id)) {
                    $this->db->or_where('LOWER(TRIM(peminjaman.nama_lengkap))', strtolower(trim($nama_lengkap)));
                } else {
                    $this->db->where('LOWER(TRIM(peminjaman.nama_lengkap))', strtolower(trim($nama_lengkap)));
                }
            }
            $this->db->group_end();

            $this->db->order_by('peminjaman.created_at', 'DESC');
            return $this->db->get()->result();
        }

        if ($this->db->table_exists('booking')) {
            $user_table = $this->db->table_exists('user') ? 'user' : 'users';
            $this->db->select("
                b.id,
                b.id_peminjam AS id_user,
                COALESCE(u.name, 'Pengguna') AS nama_lengkap,
                b.id_ruangan,
                b.keterangan,
                b.date AS tanggal_mulai,
                b.date AS tanggal_selesai,
                '08:00' AS jam_mulai,
                '17:00' AS jam_selesai,
                b.time,
                b.status,
                b.komentar AS alasan_penolakan,
                b.date_created AS created_at,
                r.ruangan AS nama_ruangan,
                r.id AS kode_ruangan,
                r.id_kategori,
                'Gedung Sebatik (FIK)' AS lokasi,
                r.kapasitas,
                r.images AS foto,
                COALESCE(k.nama_kategori, 'Umum') AS nama_kategori
            ", FALSE);
            $this->db->from('booking b');
            $this->db->join('ruangan r', 'r.id = b.id_ruangan', 'left');
            $this->_join_kategori('k', 'r');
            $this->db->join("{$user_table} u", 'u.id = b.id_peminjam', 'left');

            if (!empty($user_id)) {
                $this->db->where('b.id_peminjam', $user_id);
            }
            $this->db->order_by('b.date_created', 'DESC');
            return $this->db->get()->result();
        }

        return [];
    }

    public function cancel_booking($id, $user_id = null, $nama_lengkap = null)
    {
        $table = $this->db->table_exists('peminjaman') ? 'peminjaman' : ($this->db->table_exists('booking') ? 'booking' : null);
        if (!$table) return false;

        $this->db->where('id', $id);
        $this->db->where('status', 'Pending');

        if ($table === 'booking') {
            if (!empty($user_id)) {
                $this->db->where('id_peminjam', $user_id);
            }
        } else {
            if (!empty($user_id) || !empty($nama_lengkap)) {
                $this->db->group_start();
                if (!empty($user_id)) {
                    $this->db->where('id_user', $user_id);
                }
                if (!empty($nama_lengkap)) {
                    if (!empty($user_id)) {
                        $this->db->or_group_start();
                        $this->db->where('id_user IS NULL', null, false);
                        $this->db->where('LOWER(nama_lengkap)', strtolower(trim($nama_lengkap)));
                        $this->db->group_end();
                    } else {
                        $this->db->where('LOWER(nama_lengkap)', strtolower(trim($nama_lengkap)));
                    }
                }
                $this->db->group_end();
            }
        }

        return $this->db->delete($table);
    }

    public function insert_booking($data)
    {
        $this->db->trans_start();

        if ($this->db->table_exists('peminjaman')) {
            $this->db->insert('peminjaman', $data);
        } elseif ($this->db->table_exists('booking')) {
            $booking_data = array(
                'id'               => 'BK-' . time() . '-' . rand(10, 99),
                'id_peminjam'      => $data['id_user'] ?? ($this->session->userdata('user_id') ?: '1'),
                'id_ruangan'       => $data['id_ruangan'],
                'date'             => $data['tanggal_mulai'],
                'date_declined'    => '1970-01-01',
                'time'             => ($data['jam_mulai'] ?? '08:00') . ' - ' . ($data['jam_selesai'] ?? '17:00'),
                'keterangan'       => $data['keterangan'] ?? '',
                'status'           => $data['status'] ?? 'Pending',
                'date_created'     => date('Y-m-d H:i:s'),
                'laboran'          => '',
                'komentar'         => '',
                'tanggal_accepted' => '1970-01-01'
            );
            $this->db->insert('booking', $booking_data);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_status($id, $status, $alasan = null)
    {
        if ($this->db->table_exists('peminjaman')) {
            $data = array('status' => $status);
            if ($alasan !== null) {
                $data['alasan_penolakan'] = $alasan;
            }
            $this->db->where('id', $id);
            return $this->db->update('peminjaman', $data);
        } elseif ($this->db->table_exists('booking')) {
            $data = array('status' => $status);
            if ($alasan !== null) {
                $data['komentar'] = $alasan;
            }
            $this->db->where('id', $id);
            return $this->db->update('booking', $data);
        }
        return false;
    }

    public function batch_update_status($ids, $status, $alasan = null)
    {
        if (empty($ids) || !is_array($ids)) return false;

        if ($this->db->table_exists('peminjaman')) {
            $data = array('status' => $status);
            if ($alasan !== null) {
                $data['alasan_penolakan'] = $alasan;
            }
            $this->db->where_in('id', $ids);
            return $this->db->update('peminjaman', $data);
        } elseif ($this->db->table_exists('booking')) {
            $data = array('status' => $status);
            if ($alasan !== null) {
                $data['komentar'] = $alasan;
            }
            $this->db->where_in('id', $ids);
            return $this->db->update('booking', $data);
        }
        return false;
    }

    public function delete_booking($id)
    {
        $table = $this->db->table_exists('peminjaman') ? 'peminjaman' : ($this->db->table_exists('booking') ? 'booking' : null);
        if (!$table) return false;
        $this->db->where('id', $id);
        return $this->db->delete($table);
    }

    public function batch_delete_booking($ids)
    {
        if (empty($ids) || !is_array($ids)) return false;
        $table = $this->db->table_exists('peminjaman') ? 'peminjaman' : ($this->db->table_exists('booking') ? 'booking' : null);
        if (!$table) return false;
        $this->db->where_in('id', $ids);
        return $this->db->delete($table);
    }

    public function get_approved_bookings()
    {
        $this->db->select('b.id, b.id_peminjam, b.id_ruangan, b.keterangan, b.date, b.time, b.status, b.komentar, b.date_created, r.ruangan, r.id AS kode_ruangan, r.id_kategori, r.kapasitas, u.name AS user_name');
        $this->db->from('booking b');
        $this->db->join('ruangan r', 'r.id = b.id_ruangan', 'left');
        $this->db->join('user u', 'u.id = b.id_peminjam', 'left');
        $this->db->order_by('b.date', 'ASC');
        $raw = $this->db->get()->result();
        foreach ($raw as &$b) {
            $b->id_user = $b->id_peminjam;
            $b->nama_lengkap = !empty($b->user_name) ? $b->user_name : 'Pengguna';
            $b->tanggal_mulai = $b->date;
            $b->tanggal_selesai = $b->date;
            $b->jam_mulai = '08:00';
            $b->jam_selesai = '17:00';
            $b->alasan_penolakan = $b->komentar;
            $b->created_at = $b->date_created;
            $b->nama_ruangan = $b->ruangan;
            $b->lokasi = 'Gedung Sebatik (FIK)';
            $b->foto = 'default.jpg';
            $b->nama_kategori = 'Umum';
        }
        return $raw;
    }

    /**
     * Check if a room booking conflicts with existing non-rejected/non-cancelled bookings
     */
    public function check_conflict($id_ruangan, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai, $ignore_id = null)
    {
        if ($this->db->table_exists('peminjaman')) {
            $this->db->select('peminjaman.*, ruangan.ruangan AS nama_ruangan, ruangan.id AS kode_ruangan');
            $this->db->from('peminjaman');
            $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
            $this->db->where('peminjaman.id_ruangan', $id_ruangan);
            
            $this->db->where_not_in('peminjaman.status', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan']);
            $this->db->where("peminjaman.status NOT LIKE '%Ditolak%'", NULL, FALSE);
            $this->db->where("peminjaman.status NOT LIKE '%Dibatalkan%'", NULL, FALSE);

            $this->db->where('peminjaman.tanggal_mulai <=', $tanggal_selesai);
            $this->db->where('peminjaman.tanggal_selesai >=', $tanggal_mulai);

            $this->db->where('peminjaman.jam_mulai <', $jam_selesai);
            $this->db->where('peminjaman.jam_selesai >', $jam_mulai);

            if ($ignore_id !== null) {
                $this->db->where('peminjaman.id !=', $ignore_id);
            }

            return $this->db->get()->result();
        }

        if ($this->db->table_exists('booking')) {
            $this->db->select('b.*, r.ruangan AS nama_ruangan, r.id AS kode_ruangan');
            $this->db->from('booking b');
            $this->db->join('ruangan r', 'r.id = b.id_ruangan', 'left');
            $this->db->where('b.id_ruangan', $id_ruangan);
            $this->db->where('b.date >=', $tanggal_mulai);
            $this->db->where('b.date <=', $tanggal_selesai);
            $this->db->where_not_in('b.status', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan']);

            if ($ignore_id !== null) {
                $this->db->where('b.id !=', $ignore_id);
            }

            return $this->db->get()->result();
        }

        return [];
    }


    /**
     * Dapatkan informasi penandatangan resmi surat (Laboran vs Ka. Ur) beserta tanda tangan digitalnya
     */
    public function get_penandatangan($status = '')
    {
        $is_laboran = (stripos($status, 'Laboran') !== false);
        $target_role = $is_laboran ? 2 : 3;

        // Cek jika session user yang sedang login sesuai dengan role penandatangan
        $current_user_id = $this->session->userdata('user_id');
        $current_role_id = $this->session->userdata('role_id');
        
        $penandatangan = null;

        if ($current_user_id && $current_role_id == $target_role) {
            $user = $this->db->get_where('users', ['id' => $current_user_id])->row();
            if ($user) {
                $penandatangan = [
                    'role_id'       => $user->role_id,
                    'jabatan'       => $is_laboran ? 'Laboran / Pengelola Laboratorium' : 'Kepala Urusan Laboratorium',
                    'jabatan_resmi' => $is_laboran ? 'Laboran / Petugas Pengelola Fasilitas Laboratorium' : 'Kepala Urusan / Kepala Laboratorium',
                    'nama'          => $user->name,
                    'nip'           => $user->nidn_nim ?: '-',
                    'tanda_tangan'  => $user->tanda_tangan
                ];
            }
        }

        if (!$penandatangan) {
            // Cari user aktif dengan role yang sesuai (prioritaskan yang memiliki file tanda tangan digital)
            $this->db->where('role_id', $target_role);
            $this->db->order_by("(tanda_tangan IS NOT NULL AND tanda_tangan != '')", 'DESC', false);
            $this->db->order_by('id', 'ASC');
            $user = $this->db->get('users')->row();

            if ($user) {
                $penandatangan = [
                    'role_id'       => $user->role_id,
                    'jabatan'       => $is_laboran ? 'Laboran / Pengelola Laboratorium' : 'Kepala Urusan Laboratorium',
                    'jabatan_resmi' => $is_laboran ? 'Laboran / Petugas Pengelola Fasilitas Laboratorium' : 'Kepala Urusan / Kepala Laboratorium',
                    'nama'          => $user->name,
                    'nip'           => $user->nidn_nim ?: '-',
                    'tanda_tangan'  => $user->tanda_tangan
                ];
            } else {
                $penandatangan = [
                    'role_id'       => $target_role,
                    'jabatan'       => $is_laboran ? 'Laboran / Pengelola Laboratorium' : 'Kepala Urusan Laboratorium',
                    'jabatan_resmi' => $is_laboran ? 'Laboran / Petugas Pengelola Fasilitas Laboratorium' : 'Kepala Urusan / Kepala Laboratorium',
                    'nama'          => $is_laboran ? 'Laboran FIK' : 'Kaur / Ka. Lab FIK',
                    'nip'           => $is_laboran ? '19850101004' : '198203152010121002',
                    'tanda_tangan'  => null
                ];
            }
        }

        return $penandatangan;
    }
}

