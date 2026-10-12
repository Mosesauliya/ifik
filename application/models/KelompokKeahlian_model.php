<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KelompokKeahlian_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_init_table();
    }

    /**
     * Inisialisasi dan migrasi tabel kelompok_keahlian secara otomatis
     */
    private function _init_table() {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        try {
            if (!$this->db->table_exists('kelompok_keahlian')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `kelompok_keahlian` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `kode_kk` VARCHAR(50) NOT NULL UNIQUE,
                    `nama_kk` VARCHAR(150) NOT NULL,
                    `deskripsi` TEXT NULL,
                    `ketua_kk` VARCHAR(150) NULL,
                    `nip_ketua` VARCHAR(50) NULL,
                    `is_active` TINYINT(1) DEFAULT 1,
                    `urutan` INT DEFAULT 1,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            } else {
                // Ensure all expected columns exist
                $fields = $this->db->list_fields('kelompok_keahlian');

                if (!in_array('deskripsi', $fields)) {
                    $this->db->query("ALTER TABLE `kelompok_keahlian` ADD COLUMN `deskripsi` TEXT NULL AFTER `nama_kk`");
                }
                if (!in_array('ketua_kk', $fields)) {
                    $this->db->query("ALTER TABLE `kelompok_keahlian` ADD COLUMN `ketua_kk` VARCHAR(150) NULL AFTER `deskripsi`");
                }
                if (!in_array('nip_ketua', $fields)) {
                    $this->db->query("ALTER TABLE `kelompok_keahlian` ADD COLUMN `nip_ketua` VARCHAR(50) NULL AFTER `ketua_kk`");
                }
                if (!in_array('is_active', $fields)) {
                    $this->db->query("ALTER TABLE `kelompok_keahlian` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `nip_ketua`");
                }
                if (!in_array('urutan', $fields)) {
                    $this->db->query("ALTER TABLE `kelompok_keahlian` ADD COLUMN `urutan` INT DEFAULT 1 AFTER `is_active`");
                }
                if (!in_array('updated_at', $fields)) {
                    $this->db->query("ALTER TABLE `kelompok_keahlian` ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
                }
            }

            // Seed default KK jika tabel masih kosong
            if ($this->db->count_all('kelompok_keahlian') === 0) {
                $default_kk = [
                    [
                        'kode_kk'    => 'DKV',
                        'nama_kk'    => 'Creative Design & Visual Communication',
                        'deskripsi'  => 'Fokus riset dan pengkaryaan desain komunikasi visual, grafis digital, animasi, dan branding inovatif.',
                        'ketua_kk'   => '',
                        'nip_ketua'  => '',
                        'is_active'  => 1,
                        'urutan'     => 1
                    ],
                    [
                        'kode_kk'    => 'DMI',
                        'nama_kk'    => 'Digital Media & Interactive Tech',
                        'deskripsi'  => 'Pengembangan teknologi media digital interaktif, UI/UX modern, game technology, dan augmented reality.',
                        'ketua_kk'   => '',
                        'nip_ketua'  => '',
                        'is_active'  => 1,
                        'urutan'     => 2
                    ],
                    [
                        'kode_kk'    => 'CPI',
                        'nama_kk'    => 'Craft & Product Innovation',
                        'deskripsi'  => 'Inovasi produk kriya modern, desain furnitur, kerajinan tekstil bernilai ekonomi, dan material kreatif.',
                        'ketua_kk'   => '',
                        'nip_ketua'  => '',
                        'is_active'  => 1,
                        'urutan'     => 3
                    ]
                ];
                $this->db->insert_batch('kelompok_keahlian', $default_kk);
            }
        } catch (Throwable $e) {
            // Silently fallback if db error
        }

        $this->db->db_debug = $prev_debug;
    }

    /**
     * Ambil semua data Kelompok Keahlian beserta agregasi total dosen dan mahasiswa
     */
    public function get_all($search = null, $status = 'all') {
        $this->_init_table();

        $this->db->from('kelompok_keahlian');

        if ($status !== 'all') {
            $this->db->where('is_active', ($status === 'active' ? 1 : 0));
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama_kk', $search);
            $this->db->or_like('kode_kk', $search);
            $this->db->or_like('ketua_kk', $search);
            $this->db->or_like('deskripsi', $search);
            $this->db->group_end();
        }

        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('id', 'ASC');

        $query = $this->db->get();
        $list = $query ? $query->result_array() : [];

        // Agregasi jumlah dosen & mahasiswa per KK
        foreach ($list as &$item) {
            $id_kk   = (int)$item['id'];
            $kode_kk = trim($item['kode_kk']);

            // 1. Hitung total dosen yang terasosiasi ke KK ini
            $total_dosen = 0;
            if ($this->db->table_exists('user')) {
                $conditions = [];
                if ($this->db->field_exists('koordinator', 'user')) {
                    $conditions[] = "`koordinator` = " . $this->db->escape($kode_kk);
                }
                if ($this->db->field_exists('kelompok_keahlian', 'user')) {
                    $conditions[] = "`kelompok_keahlian` = " . $this->db->escape($kode_kk);
                }
                if ($this->db->field_exists('id_kk', 'user')) {
                    $conditions[] = "`id_kk` = " . (int)$id_kk;
                }

                if (!empty($conditions)) {
                    $this->db->from('user');
                    $this->db->where_in('role_id', [2, 3, 6, 7, 9]); // Dosen / Kaur / Koor / PIC / Ketua KK
                    $this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
                    $total_dosen = $this->db->count_all_results();
                }
            }
            $item['total_dosen'] = $total_dosen;

            // 2. Hitung total pendaftar TA yang berada di bawah KK ini
            $total_ta = 0;
            if ($this->db->table_exists('pendaftaran_ta')) {
                $this->db->from('pendaftaran_ta');
                if ($this->db->field_exists('id_kk', 'pendaftaran_ta')) {
                    $this->db->where('id_kk', $id_kk);
                    $total_ta = $this->db->count_all_results();
                }
            }
            $item['total_ta'] = $total_ta;
        }
        unset($item);

        return $list;
    }

    /**
     * Ambil data KK aktif untuk dropdown/select
     */
    public function get_active_options() {
        $this->_init_table();
        $this->db->where('is_active', 1);
        $this->db->order_by('urutan', 'ASC');
        $this->db->order_by('nama_kk', 'ASC');
        $query = $this->db->get('kelompok_keahlian');
        return $query ? $query->result_array() : [];
    }

    /**
     * Ambil detail KK berdasarkan ID
     */
    public function get_by_id($id) {
        $this->_init_table();
        return $this->db->get_where('kelompok_keahlian', ['id' => (int)$id])->row_array();
    }

    /**
     * Ambil detail KK berdasarkan Kode KK
     */
    public function get_by_kode($kode) {
        $this->_init_table();
        return $this->db->get_where('kelompok_keahlian', ['kode_kk' => trim($kode)])->row_array();
    }

    /**
     * Simpan / Tambah / Update data KK
     */
    public function save($data, $id = null) {
        $this->_init_table();

        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!empty($id)) {
            $this->db->where('id', (int)$id);
            return $this->db->update('kelompok_keahlian', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('kelompok_keahlian', $data);
        }
    }

    /**
     * Toggle status aktif KK
     */
    public function toggle_status($id) {
        $this->_init_table();
        $row = $this->get_by_id($id);
        if (!$row) return false;

        $new_status = ((int)$row['is_active'] === 1) ? 0 : 1;
        $this->db->where('id', (int)$id);
        return $this->db->update('kelompok_keahlian', [
            'is_active'  => $new_status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Hapus data KK (dengan proteksi integritas relasi)
     */
    public function delete($id) {
        $this->_init_table();
        $row = $this->get_by_id($id);
        if (!$row) return ['status' => false, 'message' => 'Data Kelompok Keahlian tidak ditemukan.'];

        $id_kk   = (int)$id;
        $kode_kk = trim($row['kode_kk']);

        // Cek apakah KK masih digunakan oleh Dosen
        if ($this->db->table_exists('user')) {
            $conditions = [];
            if ($this->db->field_exists('koordinator', 'user')) {
                $conditions[] = "`koordinator` = " . $this->db->escape($kode_kk);
            }
            if ($this->db->field_exists('kelompok_keahlian', 'user')) {
                $conditions[] = "`kelompok_keahlian` = " . $this->db->escape($kode_kk);
            }
            if ($this->db->field_exists('id_kk', 'user')) {
                $conditions[] = "`id_kk` = " . (int)$id_kk;
            }

            if (!empty($conditions)) {
                $this->db->from('user');
                $this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
                $dosenCount = $this->db->count_all_results();

                if ($dosenCount > 0) {
                    return [
                        'status' => false,
                        'message' => "Tidak dapat menghapus KK [{$kode_kk}] karena masih ada {$dosenCount} akun dosen yang terhubung. Silakan nonaktifkan status KK atau alihkan dosen ke KK lain terlebih dahulu."
                    ];
                }
            }
        }

        // Cek apakah KK masih digunakan pada pendaftaran TA
        if ($this->db->table_exists('pendaftaran_ta')) {
            $this->db->from('pendaftaran_ta');
            if ($this->db->field_exists('id_kk', 'pendaftaran_ta')) {
                $this->db->where('id_kk', $id_kk);
                $taCount = $this->db->count_all_results();
                if ($taCount > 0) {
                    return [
                        'status' => false,
                        'message' => "Tidak dapat menghapus KK [{$kode_kk}] karena terdapat {$taCount} berkas/pendaftaran Tugas Akhir terkait. Anda dapat menonaktifkan statusnya agar tidak dipilih lagi."
                    ];
                }
            }
        }

        $this->db->where('id', $id_kk);
        $deleted = $this->db->delete('kelompok_keahlian');

        return [
            'status'  => $deleted,
            'message' => $deleted ? "Kelompok Keahlian [{$kode_kk}] berhasil dihapus." : "Gagal menghapus data dari database."
        ];
    }

    /**
     * Hitung ringkasan statistik
     */
    public function get_stats() {
        $this->_init_table();

        $total_kk    = $this->db->count_all('kelompok_keahlian');
        $active_kk   = $this->db->where('is_active', 1)->count_all_results('kelompok_keahlian');
        $inactive_kk = $total_kk - $active_kk;

        // Total dosen yang sudah memiliki KK
        $total_dosen = 0;
        if ($this->db->table_exists('user')) {
            $conditions = [];
            if ($this->db->field_exists('koordinator', 'user')) {
                $conditions[] = "(`koordinator` IS NOT NULL AND `koordinator` != '')";
            }
            if ($this->db->field_exists('kelompok_keahlian', 'user')) {
                $conditions[] = "(`kelompok_keahlian` IS NOT NULL AND `kelompok_keahlian` != '')";
            }
            if ($this->db->field_exists('id_kk', 'user')) {
                $conditions[] = "(`id_kk` IS NOT NULL AND `id_kk` > 0)";
            }

            if (!empty($conditions)) {
                $this->db->from('user');
                $this->db->where_in('role_id', [2, 3, 6, 7, 9]);
                $this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
                $total_dosen = $this->db->count_all_results();
            }
        }

        return [
            'total_kk'    => $total_kk,
            'active_kk'   => $active_kk,
            'inactive_kk' => $inactive_kk,
            'total_dosen' => $total_dosen
        ];
    }

    /**
     * Ambil anggota (dosen & mahasiswa) yang terhubung ke suatu KK
     */
    public function get_members_by_kk($id) {
        $this->_init_table();
        $kk = $this->get_by_id($id);
        if (!$kk) return ['kk' => null, 'dosen' => [], 'mahasiswa' => []];

        $id_kk   = (int)$id;
        $kode_kk = trim($kk['kode_kk']);

        $dosenList = [];
        if ($this->db->table_exists('user')) {
            $conditions = [];
            if ($this->db->field_exists('koordinator', 'user')) {
                $conditions[] = "`koordinator` = " . $this->db->escape($kode_kk);
            }
            if ($this->db->field_exists('kelompok_keahlian', 'user')) {
                $conditions[] = "`kelompok_keahlian` = " . $this->db->escape($kode_kk);
            }
            if ($this->db->field_exists('id_kk', 'user')) {
                $conditions[] = "`id_kk` = " . (int)$id_kk;
            }

            if (!empty($conditions)) {
                $this->db->select('id, name, nip, email, kode_dosen, no_telp, koordinator, role_id');
                $this->db->from('user');
                $this->db->where_in('role_id', [2, 3, 6, 7, 9]);
                $this->db->where('(' . implode(' OR ', $conditions) . ')', null, false);
                $this->db->order_by('name', 'ASC');
                $q = $this->db->get();
                $dosenList = $q ? $q->result_array() : [];
            }
        }

        $mhsList = [];
        if ($this->db->table_exists('pendaftaran_ta')) {
            $this->db->select('p.id, p.nim, p.judul_1, p.is_submitted, p.created_at, m.nama_depan, m.nama_belakang, m.prodi');
            $this->db->from('pendaftaran_ta p');
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
            $this->db->where('p.id_kk', $id_kk);
            $this->db->order_by('p.created_at', 'DESC');
            $this->db->limit(50);
            $q2 = $this->db->get();
            $mhsList = $q2 ? $q2->result_array() : [];
        }

        return [
            'kk'        => $kk,
            'dosen'     => $dosenList,
            'mahasiswa' => $mhsList
        ];
    }

    /**
     * Ambil daftar nama & NIP dosen untuk pilihan Ketua KK
     */
    public function get_dosen_candidates() {
        if (!$this->db->table_exists('user')) return [];

        $this->db->select('id, name, nip, email, kode_dosen');
        $this->db->from('user');
        $this->db->where_in('role_id', [2, 3, 6, 7, 9]);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query ? $query->result_array() : [];
    }
}
