<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perwalian_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->ensure_schema();
    }

    /**
     * Pastikan tabel-tabel perwalian tersedia di database (auto-migration)
     */
    public function ensure_schema() {
        $this->load->dbforge();

        // 1. master_mata_kuliah
        if (!$this->db->table_exists('master_mata_kuliah')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `master_mata_kuliah` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_mk` VARCHAR(30) NOT NULL UNIQUE,
                  `nama_mk` VARCHAR(255) NOT NULL,
                  `sks` INT NOT NULL DEFAULT 3,
                  `semester` INT NOT NULL DEFAULT 1,
                  `prodi` VARCHAR(100) NULL,
                  `jenis` VARCHAR(30) DEFAULT 'Wajib',
                  `kurikulum` VARCHAR(20) DEFAULT '2024',
                  `is_active` TINYINT(1) DEFAULT 1,
                  `created_at` DATETIME NULL,
                  `updated_at` DATETIME NULL,
                  INDEX `idx_mk_prodi` (`prodi`),
                  INDEX `idx_mk_semester` (`semester`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 2. perwalian
        if (!$this->db->table_exists('perwalian')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `perwalian` (
                  `id_perwalian` INT AUTO_INCREMENT PRIMARY KEY,
                  `nim` VARCHAR(30) NOT NULL,
                  `nama_mahasiswa` VARCHAR(150) NULL,
                  `prodi` VARCHAR(100) NULL,
                  `nip_dosen_wali` VARCHAR(50) NULL,
                  `nama_dosen_wali` VARCHAR(150) NULL,
                  `tahun_ajaran` VARCHAR(20) NOT NULL DEFAULT '2024/2025',
                  `semester` VARCHAR(20) NOT NULL DEFAULT 'Ganjil',
                  `status_tahap1` ENUM('Draft', 'Diajukan', 'Disetujui', 'Revisi') DEFAULT 'Draft',
                  `status_tahap2` ENUM('Belum Dinilai', 'Proses Penilaian', 'Selesai') DEFAULT 'Belum Dinilai',
                  `file_krs` VARCHAR(255) NULL,
                  `catatan_mahasiswa` TEXT NULL,
                  `catatan_wali` TEXT NULL,
                  `tgl_pengajuan` DATETIME NULL,
                  `tgl_approval_tahap1` DATETIME NULL,
                  `created_at` DATETIME NULL,
                  `updated_at` DATETIME NULL,
                  INDEX `idx_perwalian_nim` (`nim`),
                  INDEX `idx_perwalian_wali` (`nip_dosen_wali`),
                  INDEX `idx_perwalian_periode` (`tahun_ajaran`, `semester`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 3. perwalian_detail
        if (!$this->db->table_exists('perwalian_detail')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `perwalian_detail` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `id_perwalian` INT NOT NULL,
                  `kode_mk` VARCHAR(30) NOT NULL,
                  `nama_mk` VARCHAR(255) NOT NULL,
                  `sks` INT NOT NULL DEFAULT 3,
                  `status_tahap` ENUM('akan_diambil', 'sudah_diambil') NOT NULL DEFAULT 'akan_diambil',
                  `nilai_huruf` VARCHAR(5) NULL,
                  `status_kelulusan` ENUM('Belum Ditentukan', 'Lulus', 'Tidak Lulus') NOT NULL DEFAULT 'Belum Ditentukan',
                  `is_imported` TINYINT(1) DEFAULT 0,
                  `created_at` DATETIME NULL,
                  `updated_at` DATETIME NULL,
                  INDEX `idx_detail_perwalian` (`id_perwalian`),
                  INDEX `idx_detail_mk` (`kode_mk`),
                  INDEX `idx_detail_status_tahap` (`status_tahap`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 4. Pastikan collation tabel-tabel perwalian selaras dengan tabel user/mahasiswa (utf8mb4_general_ci)
        try {
            if ($this->db->table_exists('perwalian')) {
                $check = $this->db->query("SELECT TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perwalian'")->row_array();
                if (!empty($check['TABLE_COLLATION']) && $check['TABLE_COLLATION'] !== 'utf8mb4_general_ci') {
                    $this->db->query("ALTER TABLE `master_mata_kuliah` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
                    $this->db->query("ALTER TABLE `perwalian` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
                    $this->db->query("ALTER TABLE `perwalian_detail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika user DB tidak memiliki privilege ALTER
        }
    }

    // =========================================================================
    // MASTER MATA KULIAH (ADMIN PRODI)
    // =========================================================================

    public function get_mata_kuliah($filters = [], $limit = 500, $offset = 0) {
        $this->db->from('master_mata_kuliah');

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('kode_mk', $s)
                ->or_like('nama_mk', $s)
                ->or_like('prodi', $s)
            ->group_end();
        }

        if (!empty($filters['prodi']) && $filters['prodi'] !== 'all') {
            $this->db->group_start()
                ->where('prodi', $filters['prodi'])
                ->or_where('prodi', '')
                ->or_where('prodi IS NULL', null, false)
            ->group_end();
        }

        if (!empty($filters['semester']) && $filters['semester'] !== 'all') {
            $this->db->where('semester', (int)$filters['semester']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', (int)$filters['is_active']);
        }

        $this->db->order_by('semester', 'ASC');
        $this->db->order_by('kode_mk', 'ASC');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result_array();
    }

    public function get_mata_kuliah_by_id($id) {
        return $this->db->get_where('master_mata_kuliah', ['id' => $id])->row_array();
    }

    public function get_mata_kuliah_by_kode($kode_mk) {
        return $this->db->get_where('master_mata_kuliah', ['kode_mk' => trim($kode_mk)])->row_array();
    }

    public function save_mata_kuliah($data) {
        $now = date('Y-m-d H:i:s');
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        $payload = [
            'kode_mk'    => strtoupper(trim($data['kode_mk'] ?? '')),
            'nama_mk'    => trim($data['nama_mk'] ?? ''),
            'sks'        => max(1, (int)($data['sks'] ?? 3)),
            'semester'   => max(1, (int)($data['semester'] ?? 1)),
            'prodi'      => trim($data['prodi'] ?? ''),
            'jenis'      => !empty($data['jenis']) ? trim($data['jenis']) : 'Wajib',
            'kurikulum'  => !empty($data['kurikulum']) ? trim($data['kurikulum']) : '2024',
            'is_active'  => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'updated_at' => $now,
        ];

        if ($id > 0) {
            $this->db->where('id', $id)->update('master_mata_kuliah', $payload);
            return $id;
        } else {
            $payload['created_at'] = $now;
            $this->db->insert('master_mata_kuliah', $payload);
            return $this->db->insert_id();
        }
    }

    public function delete_mata_kuliah($id) {
        return $this->db->where('id', (int)$id)->delete('master_mata_kuliah');
    }

    /**
     * Bulk Upsert Mata Kuliah dari Excel / JSON
     */
    public function upsert_mata_kuliah_bulk($rows) {
        if (empty($rows)) return ['inserted' => 0, 'updated' => 0];

        $now = date('Y-m-d H:i:s');
        $inserted = 0;
        $updated = 0;

        foreach ($rows as $r) {
            $kode = strtoupper(trim($r['kode_mk'] ?? ($r['kode'] ?? ($r['Kode MK'] ?? ''))));
            $nama = trim($r['nama_mk'] ?? ($r['nama'] ?? ($r['Nama Mata Kuliah'] ?? '')));
            if (empty($kode) || empty($nama)) continue;

            $sks = max(1, (int)($r['sks'] ?? ($r['SKS'] ?? 3)));
            $semester = max(1, (int)($r['semester'] ?? ($r['Semester'] ?? 1)));
            $prodi = trim($r['prodi'] ?? ($r['Prodi'] ?? ($r['Program Studi'] ?? '')));
            $jenis = trim($r['jenis'] ?? ($r['Jenis'] ?? ($r['Sifat'] ?? 'Wajib')));
            $kurikulum = trim($r['kurikulum'] ?? ($r['Kurikulum'] ?? '2024'));

            $existing = $this->get_mata_kuliah_by_kode($kode);
            if ($existing) {
                $this->db->where('id', $existing['id'])->update('master_mata_kuliah', [
                    'nama_mk'    => $nama,
                    'sks'        => $sks,
                    'semester'   => $semester,
                    'prodi'      => !empty($prodi) ? $prodi : $existing['prodi'],
                    'jenis'      => !empty($jenis) ? $jenis : $existing['jenis'],
                    'kurikulum'  => !empty($kurikulum) ? $kurikulum : $existing['kurikulum'],
                    'updated_at' => $now,
                ]);
                $updated++;
            } else {
                $this->db->insert('master_mata_kuliah', [
                    'kode_mk'    => $kode,
                    'nama_mk'    => $nama,
                    'sks'        => $sks,
                    'semester'   => $semester,
                    'prodi'      => $prodi,
                    'jenis'      => $jenis,
                    'kurikulum'  => $kurikulum,
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }
        }

        return ['inserted' => $inserted, 'updated' => $updated];
    }

    // =========================================================================
    // MASTER MAHASISWA & DOSEN WALI (ADMIN PRODI)
    // =========================================================================

    public function get_dosen_wali_options($prodi = null) {
        $this->db->select('id, name, nip, username, email, prodi')
                 ->from('user')
                 ->where('role_id', 3); // Dosen

        if (!empty($prodi) && $prodi !== 'all') {
            $this->db->group_start()
                ->where('prodi', $prodi)
                ->or_where('prodi', '')
                ->or_where('prodi IS NULL', null, false)
            ->group_end();
        }

        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function assign_dosen_wali($nim, $nip_dosen) {
        $dosen = $this->db->get_where('user', ['nip' => $nip_dosen])->row_array();
        if (!$dosen) {
            $dosen = $this->db->get_where('user', ['username' => $nip_dosen])->row_array();
        }
        $nama_dosen = $dosen ? $dosen['name'] : '';

        // Update di tabel user/mahasiswa
        if ($this->db->table_exists('user')) {
            $this->db->where('nim', $nim)->or_where('username', $nim);
            $this->db->update('user', ['dosen_wali' => $nip_dosen]);
        }
        if ($this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim)->update('mahasiswa', ['dosen_wali' => $nip_dosen]);
        }

        // Update juga pada perwalian aktif
        $this->db->where('nim', $nim)->update('perwalian', [
            'nip_dosen_wali'  => $nip_dosen,
            'nama_dosen_wali' => $nama_dosen,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    // =========================================================================
    // PERWALIAN & KRS KOLEKTIF (IMPORT ADMIN PRODI)
    // =========================================================================

    /**
     * Bulk Import KRS dari SIAKAD / i-Gracias oleh Admin Prodi
     * Setiap baris: [nim, kode_mk, tahun_ajaran, semester]
     */
    public function upsert_krs_bulk($rows, $defaultTahun = '2024/2025', $defaultSemester = 'Ganjil') {
        if (empty($rows)) return ['processed' => 0, 'new_perwalian' => 0, 'courses_added' => 0];

        $now = date('Y-m-d H:i:s');
        $processed = 0;
        $new_perwalian = 0;
        $courses_added = 0;

        foreach ($rows as $r) {
            $nim = trim((string)($r['nim'] ?? ($r['NIM'] ?? ($r['nim_mahasiswa'] ?? ''))));
            $kode_mk = strtoupper(trim((string)($r['kode_mk'] ?? ($r['kode'] ?? ($r['Kode MK'] ?? '')))));
            if (empty($nim) || empty($kode_mk)) continue;

            $tahun = trim((string)($r['tahun_ajaran'] ?? ($r['tahun'] ?? $defaultTahun)));
            $smt = trim((string)($r['semester'] ?? $defaultSemester));

            // Ambil / buat perwalian mahasiswa
            $perwalian = $this->get_active_perwalian_by_nim($nim, $tahun, $smt);
            if (!empty($perwalian['is_new'])) {
                $new_perwalian++;
            }
            $id_perwalian = $perwalian['id_perwalian'];

            // Dapatkan detail master MK
            $mk = $this->get_mata_kuliah_by_kode($kode_mk);
            $nama_mk = $mk ? $mk['nama_mk'] : ($r['nama_mk'] ?? $kode_mk);
            $sks = $mk ? (int)$mk['sks'] : (int)($r['sks'] ?? 3);

            // Cek apakah MK sudah ada di perwalian_detail
            $existDetail = $this->db->get_where('perwalian_detail', [
                'id_perwalian' => $id_perwalian,
                'kode_mk'      => $kode_mk,
                'status_tahap' => 'akan_diambil',
            ])->row_array();

            if (!$existDetail) {
                $this->db->insert('perwalian_detail', [
                    'id_perwalian'     => $id_perwalian,
                    'kode_mk'          => $kode_mk,
                    'nama_mk'          => $nama_mk,
                    'sks'              => $sks,
                    'status_tahap'     => 'akan_diambil',
                    'nilai_huruf'      => null,
                    'status_kelulusan' => 'Belum Ditentukan',
                    'is_imported'      => 1,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $courses_added++;
            }
            $processed++;
        }

        return [
            'processed'       => $processed,
            'new_perwalian'   => $new_perwalian,
            'courses_added'   => $courses_added,
        ];
    }

    // =========================================================================
    // MODUL PERWALIAN MAHASISWA
    // =========================================================================

    /**
     * Ambil atau inisialisasi perwalian aktif mahasiswa untuk semester berjalan
     */
    public function get_active_perwalian_by_nim($nim, $tahun_ajaran = '2024/2025', $semester = 'Ganjil') {
        $nim = trim((string)$nim);
        $perwalian = $this->db->get_where('perwalian', [
            'nim'          => $nim,
            'tahun_ajaran' => $tahun_ajaran,
            'semester'     => $semester,
        ])->row_array();

        if ($perwalian) {
            $perwalian['is_new'] = false;
            return $perwalian;
        }

        // Cari data mahasiswa & dosen wali dari tabel user
        $u = $this->db->get_where('user', ['nim' => $nim])->row_array();
        if (!$u) {
            $u = $this->db->get_where('user', ['username' => $nim])->row_array();
        }

        $nama_mhs = $u ? $u['name'] : 'Mahasiswa ' . $nim;
        $prodi = $u ? ($u['prodi'] ?? '') : '';
        $nip_wali = $u ? ($u['dosen_wali'] ?? '') : '';
        $nama_wali = '';

        if (!empty($nip_wali)) {
            $d = $this->db->get_where('user', ['nip' => $nip_wali])->row_array();
            if (!$d) $d = $this->db->get_where('user', ['username' => $nip_wali])->row_array();
            if ($d) $nama_wali = $d['name'];
        }

        $now = date('Y-m-d H:i:s');
        $payload = [
            'nim'             => $nim,
            'nama_mahasiswa'  => $nama_mhs,
            'prodi'           => $prodi,
            'nip_dosen_wali'  => $nip_wali,
            'nama_dosen_wali' => $nama_wali,
            'tahun_ajaran'    => $tahun_ajaran,
            'semester'        => $semester,
            'status_tahap1'   => 'Draft',
            'status_tahap2'   => 'Belum Dinilai',
            'file_krs'        => null,
            'catatan_mahasiswa' => null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        $this->db->insert('perwalian', $payload);
        $payload['id_perwalian'] = $this->db->insert_id();
        $payload['is_new'] = true;

        return $payload;
    }

    public function get_perwalian_by_id($id_perwalian) {
        return $this->db->get_where('perwalian', ['id_perwalian' => (int)$id_perwalian])->row_array();
    }

    /**
     * Ambil mata kuliah dalam perwalian tertentu
     * @param int $id_perwalian
     * @param string|null $status_tahap 'akan_diambil' | 'sudah_diambil' | null (all)
     */
    public function get_perwalian_detail($id_perwalian, $status_tahap = null) {
        $this->db->from('perwalian_detail')
                 ->where('id_perwalian', (int)$id_perwalian);

        if (!empty($status_tahap)) {
            $this->db->where('status_tahap', $status_tahap);
        }

        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Ambil riwayat seluruh MK yang SUDAH diambil oleh mahasiswa (dari semester-semester sebelumnya)
     */
    public function get_riwayat_mk_sudah_diambil($nim) {
        $this->db->select('pd.*, p.tahun_ajaran, p.semester as periode_semester')
                 ->from('perwalian_detail pd')
                 ->join('perwalian p', 'pd.id_perwalian = p.id_perwalian', 'inner')
                 ->where('p.nim', trim($nim))
                 ->where('pd.status_tahap', 'sudah_diambil')
                 ->order_by('pd.id', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Simpan / Perbarui Mata Kuliah Kontrak Mandiri oleh Mahasiswa (Tahap 1)
     */
    public function sync_kontrak_mahasiswa($id_perwalian, $selected_mk_codes = []) {
        $now = date('Y-m-d H:i:s');
        $id_perwalian = (int)$id_perwalian;

        // Ambil daftar saat ini yang berstatus 'akan_diambil'
        $current = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'status_tahap' => 'akan_diambil',
        ])->result_array();

        $currentMap = [];
        foreach ($current as $c) {
            $currentMap[$c['kode_mk']] = $c;
        }

        // Hapus MK yang tidak ada lagi di selected_mk_codes
        foreach ($current as $c) {
            if (!in_array($c['kode_mk'], $selected_mk_codes, true)) {
                $this->db->where('id', $c['id'])->delete('perwalian_detail');
            }
        }

        // Tambahkan MK baru yang belum ada
        foreach ($selected_mk_codes as $kode) {
            $kode = strtoupper(trim($kode));
            if (empty($kode)) continue;
            if (!isset($currentMap[$kode])) {
                $mk = $this->get_mata_kuliah_by_kode($kode);
                $nama_mk = $mk ? $mk['nama_mk'] : $kode;
                $sks = $mk ? (int)$mk['sks'] : 3;

                $this->db->insert('perwalian_detail', [
                    'id_perwalian'     => $id_perwalian,
                    'kode_mk'          => $kode,
                    'nama_mk'          => $nama_mk,
                    'sks'              => $sks,
                    'status_tahap'     => 'akan_diambil',
                    'nilai_huruf'      => null,
                    'status_kelulusan' => 'Belum Ditentukan',
                    'is_imported'      => 0,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }

        $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
            'updated_at' => $now,
        ]);

        return true;
    }

    /**
     * Tambah satu MK ke kontrak mahasiswa
     */
    public function add_mk_to_kontrak($id_perwalian, $kode_mk) {
        $kode_mk = strtoupper(trim($kode_mk));
        $id_perwalian = (int)$id_perwalian;

        $exists = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'kode_mk'      => $kode_mk,
            'status_tahap' => 'akan_diambil',
        ])->row_array();

        if ($exists) return true;

        $mk = $this->get_mata_kuliah_by_kode($kode_mk);
        $nama_mk = $mk ? $mk['nama_mk'] : $kode_mk;
        $sks = $mk ? (int)$mk['sks'] : 3;
        $now = date('Y-m-d H:i:s');

        return $this->db->insert('perwalian_detail', [
            'id_perwalian'     => $id_perwalian,
            'kode_mk'          => $kode_mk,
            'nama_mk'          => $nama_mk,
            'sks'              => $sks,
            'status_tahap'     => 'akan_diambil',
            'nilai_huruf'      => null,
            'status_kelulusan' => 'Belum Ditentukan',
            'is_imported'      => 0,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }

    /**
     * Hapus satu MK dari kontrak
     */
    public function remove_mk_from_kontrak($id_perwalian, $kode_mk) {
        return $this->db->where([
            'id_perwalian' => (int)$id_perwalian,
            'kode_mk'      => trim($kode_mk),
            'status_tahap' => 'akan_diambil',
        ])->delete('perwalian_detail');
    }

    /**
     * Upload Bukti KRS Asli dari Kampus
     */
    public function update_bukti_krs($id_perwalian, $filename) {
        return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
            'file_krs'   => $filename,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Submit Pengajuan KRS Tahap 1 ke Dosen Wali
     */
    public function submit_perwalian_tahap1($id_perwalian, $catatan_mhs = '') {
        $now = date('Y-m-d H:i:s');
        return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
            'status_tahap1'     => 'Diajukan',
            'catatan_mahasiswa' => trim($catatan_mhs),
            'tgl_pengajuan'     => $now,
            'updated_at'        => $now,
        ]);
    }

    // =========================================================================
    // MONITORING PERWALIAN (ADMIN PRODI & STATISTIK)
    // =========================================================================

    public function get_monitoring_list($filters = []) {
        $this->db->select('p.*, MAX(u.name) as nama_lengkap, MAX(u.email) as email, MAX(u.prodi) as user_prodi, COUNT(pd.id) as total_mk, COALESCE(SUM(pd.sks), 0) as total_sks')
                 ->from('perwalian p')
                 ->join('user u', 'p.nim = u.nim OR p.nim = u.username', 'left')
                 ->join('perwalian_detail pd', 'p.id_perwalian = pd.id_perwalian AND pd.status_tahap = "akan_diambil"', 'left')
                 ->group_by('p.id_perwalian');

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('p.nim', $s)
                ->or_like('p.nama_mahasiswa', $s)
                ->or_like('p.nama_dosen_wali', $s)
            ->group_end();
        }

        if (!empty($filters['status_tahap1']) && $filters['status_tahap1'] !== 'all') {
            $this->db->where('p.status_tahap1', $filters['status_tahap1']);
        }

        if (!empty($filters['tahun_ajaran']) && $filters['tahun_ajaran'] !== 'all') {
            $this->db->where('p.tahun_ajaran', $filters['tahun_ajaran']);
        }

        if (!empty($filters['semester']) && $filters['semester'] !== 'all') {
            $this->db->where('p.semester', $filters['semester']);
        }

        if (!empty($filters['prodi']) && $filters['prodi'] !== 'all') {
            $this->db->group_start()
                ->where('p.prodi', $filters['prodi'])
                ->or_where('p.prodi', '')
                ->or_where('p.prodi IS NULL', null, false)
                ->or_like('p.prodi', $filters['prodi'])
            ->group_end();
        }

        $this->db->order_by('p.updated_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_monitoring_stats($tahun = '2024/2025', $semester = 'Ganjil') {
        $this->db->where('tahun_ajaran', $tahun)->where('semester', $semester);
        $all = $this->db->get('perwalian')->result_array();

        $stats = [
            'total'     => count($all),
            'draft'     => 0,
            'diajukan'  => 0,
            'disetujui' => 0,
            'revisi'    => 0,
        ];

        foreach ($all as $item) {
            $st = strtolower($item['status_tahap1'] ?? 'draft');
            if ($st === 'diajukan') $stats['diajukan']++;
            elseif ($st === 'disetujui') $stats['disetujui']++;
            elseif ($st === 'revisi') $stats['revisi']++;
            else $stats['draft']++;
        }

        return $stats;
    }

    // =========================================================================
    // HELPER UNTUK ALIF (APPROVAL DOSEN WALI & TAHAP 2 EVALUASI NILAI)
    // =========================================================================

    public function get_perwalian_by_dosen($nip_dosen, $filters = []) {
        $this->db->select('p.*, COUNT(pd.id) as total_mk, COALESCE(SUM(pd.sks), 0) as total_sks')
                 ->from('perwalian p')
                 ->join('perwalian_detail pd', 'p.id_perwalian = pd.id_perwalian AND pd.status_tahap = "akan_diambil"', 'left')
                 ->where('p.nip_dosen_wali', trim($nip_dosen))
                 ->group_by('p.id_perwalian');

        if (!empty($filters['status_tahap1']) && $filters['status_tahap1'] !== 'all') {
            $this->db->where('p.status_tahap1', $filters['status_tahap1']);
        }

        $this->db->order_by('p.updated_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function approve_tahap1($id_perwalian, $status = 'Disetujui', $catatan = '') {
        $now = date('Y-m-d H:i:s');
        return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
            'status_tahap1'       => $status,
            'catatan_wali'        => trim($catatan),
            'tgl_approval_tahap1' => ($status === 'Disetujui') ? $now : null,
            'updated_at'          => $now,
        ]);
    }

    /**
     * Update nilai per mata kuliah (Tahap 2: A, AB, B, BC, C = Lulus; D, E = Tidak Lulus)
     */
    public function update_nilai_detail($id_detail, $nilai_huruf, $status_kelulusan = null) {
        $nilai_huruf = strtoupper(trim($nilai_huruf));

        if ($status_kelulusan === null) {
            // Logika default: A, AB, B, BC, C -> Lulus; D, E -> Tidak Lulus
            if (in_array($nilai_huruf, ['A', 'AB', 'B', 'BC', 'C'], true)) {
                $status_kelulusan = 'Lulus';
            } elseif (in_array($nilai_huruf, ['D', 'E'], true)) {
                $status_kelulusan = 'Tidak Lulus';
            } else {
                $status_kelulusan = 'Belum Ditentukan';
            }
        }

        return $this->db->where('id', (int)$id_detail)->update('perwalian_detail', [
            'nilai_huruf'      => $nilai_huruf,
            'status_kelulusan' => $status_kelulusan,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
    }
}
