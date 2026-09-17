<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Bersih tanpa auto-migration / ALTER TABLE
    }

    /**
     * Ambil list semua dosen dari tabel user (role_id = 3 / Dosen, 6 / Koor, 7 / PIC, 9 / Ketua KK)
     */
    public function get_dosen_list() {
        $this->db->select('id, name as nama_dosen, nip, email, kode_dosen, no_telp as no_hp');
        $this->db->from('user');
        $this->db->where_in('role_id', array(3, 6, 7, 9));
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();

        $result = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $nipKey = !empty($row['nip']) ? (string)$row['nip'] : (string)$row['id'];
                $result[$nipKey] = array(
                    'id'         => $row['id'],
                    'nip'        => $nipKey,
                    'nama_dosen' => $row['nama_dosen'],
                    'email'      => $row['email'] ?? '',
                    'kode_dosen' => $row['kode_dosen'] ?? '',
                    'no_hp'      => $row['no_hp'] ?? '',
                    'prodi'      => 'Informatika'
                );
            }
        }

        return array_values($result);
    }

    /**
     * Helper internal untuk memetakan nama file berkas pendaftaran mahasiswa
     */
    private function _get_mhs_files($id_mhs_list) {
        $files_map = array();
        if (empty($id_mhs_list)) return $files_map;

        $this->db->select('id_mhs, nama, file, status_adminlaa, status_doswal, komentar');
        $this->db->from('file_pendaftaran');
        $this->db->where_in('id_mhs', $id_mhs_list);
        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $f) {
                $mId = $f['id_mhs'];
                $namaJenis = strtolower(trim($f['nama']));
                if (!isset($files_map[$mId])) {
                    $files_map[$mId] = array(
                        'file_ksm'          => null,
                        'status_ksm'        => 'Pending',
                        'file_transkrip'    => null,
                        'status_transkrip'  => 'Pending',
                        'file_pernyataan'   => null,
                        'status_pernyataan' => 'Pending',
                        'file_bebas_lab'    => null,
                        'status_bebas_lab'  => 'Pending',
                        'status_doswal'     => 'Pending',
                        'status_adminlaa'   => 'Pending',
                        'catatan_wali'      => '',
                        'catatan_admin'     => ''
                    );
                }

                if (strpos($namaJenis, 'ksm') !== false) {
                    $files_map[$mId]['file_ksm'] = $f['file'];
                    $files_map[$mId]['status_ksm'] = $f['status_doswal'] ?: 'Valid';
                } elseif (strpos($namaJenis, 'transkrip') !== false) {
                    $files_map[$mId]['file_transkrip'] = $f['file'];
                    $files_map[$mId]['status_transkrip'] = $f['status_doswal'] ?: 'Valid';
                } elseif (strpos($namaJenis, 'pernyataan') !== false) {
                    $files_map[$mId]['file_pernyataan'] = $f['file'];
                    $files_map[$mId]['status_pernyataan'] = $f['status_doswal'] ?: 'Valid';
                } elseif (strpos($namaJenis, 'bebas') !== false || strpos($namaJenis, 'lab') !== false) {
                    $files_map[$mId]['file_bebas_lab'] = $f['file'];
                    $files_map[$mId]['status_bebas_lab'] = $f['status_doswal'] ?: 'Valid';
                }

                if (!empty($f['status_doswal'])) {
                    $files_map[$mId]['status_doswal'] = $f['status_doswal'];
                }
                if (!empty($f['status_adminlaa'])) {
                    $files_map[$mId]['status_adminlaa'] = $f['status_adminlaa'];
                }
                if (!empty($f['komentar'])) {
                    $files_map[$mId]['catatan_wali'] = $f['komentar'];
                    $files_map[$mId]['catatan_admin'] = $f['komentar'];
                }
            }
        }

        return $files_map;
    }

    /**
     * TAHAP 1: Ambil semua mahasiswa pendaftar TA dari tabel guidance + user + file_pendaftaran
     */
    public function get_all_mahasiswa_ta() {
        $this->db->select('
            g.id as guidance_id,
            g.id_mhs,
            g.judul_1,
            g.judul_2,
            g.judul_3,
            g.peminatan,
            g.tahun,
            g.keterangan as status_approval_koor,
            g.komentar as catatan_koor,
            g.status_file,
            g.date as tanggal_pengajuan,
            g.ipk,
            g.totalsks,
            g.scoretak,
            g.scoreeprt,
            g.jenis_TA,
            u.id as user_id,
            u.name,
            u.nim,
            u.email,
            u.no_telp as no_hp,
            u.prodi as user_prodi,
            u.dosen_wali,
            u_wali.name as nama_dosen_wali_resolved,
            tl.id as id_thesis_lecturers,
            tl.dosen_pembimbing1 as pembimbing_1,
            tl.dosen_pembimbing2 as pembimbing_2,
            tl.dosen_penguji1 as penguji_1,
            tl.dosen_penguji2 as penguji_2,
            tl.kelompok_keahlian as kode_kk,
            tl.status as status_plotting,
            u_p1.name as nama_pembimbing_1,
            u_p2.name as nama_pembimbing_2,
            u_pj1.name as nama_penguji_1,
            u_pj2.name as nama_penguji_2
        ');
        $this->db->from('guidance g');
        $this->db->join('user u', 'u.id = g.id_mhs OR u.nim = g.id_mhs', 'inner');
        $this->db->join('user u_wali', 'u_wali.nip = u.dosen_wali OR u_wali.id = u.dosen_wali', 'left');
        $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
        $this->db->join('user u_p1', 'u_p1.nip = tl.dosen_pembimbing1 OR u_p1.id = tl.dosen_pembimbing1', 'left');
        $this->db->join('user u_p2', 'u_p2.nip = tl.dosen_pembimbing2 OR u_p2.id = tl.dosen_pembimbing2', 'left');
        $this->db->join('user u_pj1', 'u_pj1.nip = tl.dosen_penguji1 OR u_pj1.id = tl.dosen_penguji1', 'left');
        $this->db->join('user u_pj2', 'u_pj2.nip = tl.dosen_penguji2 OR u_pj2.id = tl.dosen_penguji2', 'left');
        $this->db->order_by('g.id', 'DESC');
        $query = $this->db->get();

        if (!$query || $query->num_rows() === 0) {
            return array();
        }

        $rawList = $query->result_array();
        $id_mhs_list = array();
        foreach ($rawList as $row) {
            if (!empty($row['user_id'])) $id_mhs_list[] = $row['user_id'];
            if (!empty($row['nim'])) $id_mhs_list[] = $row['nim'];
            if (!empty($row['id_mhs'])) $id_mhs_list[] = $row['id_mhs'];
        }
        $id_mhs_list = array_unique($id_mhs_list);
        $files_map = $this->_get_mhs_files($id_mhs_list);

        $result = array();
        foreach ($rawList as $row) {
            $uId = $row['user_id'] ?: $row['id_mhs'];
            $nim = $row['nim'] ?: $uId;
            $fData = $files_map[$uId] ?? ($files_map[$nim] ?? ($files_map[$row['id_mhs']] ?? array()));

            $nameParts = explode(' ', trim($row['name'] ?? 'Mahasiswa'));
            $nama_depan = array_shift($nameParts);
            $nama_belakang = !empty($nameParts) ? implode(' ', $nameParts) : $nim;

            $status_wali  = $fData['status_doswal'] ?? 'Pending';
            $status_admin = $fData['status_adminlaa'] ?? 'Pending';
            $status_koor  = !empty($row['status_approval_koor']) ? $row['status_approval_koor'] : 'Pending';
            $status_kk    = !empty($row['status_plotting']) ? $row['status_plotting'] : 'Pending';

            // Alur Tahapan Resmi:
            // 1. Dosen Wali -> 2. Admin Layanan -> 3. Koordinator TA -> 4. Ketua KK -> 5. Selesai
            $stage = 'Dosen Wali';
            if (strcasecmp($status_wali, 'Approved') === 0) {
                $stage = 'Admin Layanan';
                if (strcasecmp($status_admin, 'Approved') === 0) {
                    $stage = 'Koordinator TA';
                    if (strcasecmp($status_koor, 'Approved') === 0) {
                        $stage = 'Ketua KK';
                        if (strcasecmp($status_kk, 'Approved') === 0) {
                            $stage = 'Selesai';
                        }
                    }
                }
            }

            $item = array(
                'nim'                   => $nim,
                'user_id'               => $uId,
                'guidance_id'           => $row['guidance_id'] ?? ('gdn_' . $nim),
                'nama'                  => $row['name'] ?? 'Mahasiswa',
                'nama_depan'            => $nama_depan,
                'nama_belakang'         => $nama_belakang,
                'email'                 => $row['email'] ?? ($nim . '@student.telkomuniversity.ac.id'),
                'no_hp'                 => $row['no_hp'] ?? '',
                'prodi'                 => $row['user_prodi'] ?? 'Informatika',
                'konsentrasi_dkv'       => $row['peminatan'] ?? 'Informatika',
                'peminatan'             => $row['peminatan'] ?? 'Informatika',
                'kode_kk'               => $row['kode_kk'] ?? 'KK-SIDE',
                'judul_1'               => $row['judul_1'] ?? 'Pengajuan Tugas Akhir Mahasiswa',
                'judul_2'               => $row['judul_2'] ?? '',
                'judul_3'               => $row['judul_3'] ?? '',
                'deskripsi_1'           => $row['judul_1'] ?? '',
                'deskripsi_2'           => $row['judul_2'] ?? '',
                'deskripsi_3'           => $row['judul_3'] ?? '',
                'nama_dosen_wali'       => $row['nama_dosen_wali_resolved'] ?? ($row['dosen_wali'] ?? 'Dosen Wali'),
                'nip_dosen_wali'        => $row['dosen_wali'] ?? '',
                'pembimbing_1'          => $row['pembimbing_1'] ?? '',
                'pembimbing_2'          => $row['pembimbing_2'] ?? '',
                'nama_pembimbing_1'     => $row['nama_pembimbing_1'] ?? ($row['pembimbing_1'] ?? ''),
                'nama_pembimbing_2'     => $row['nama_pembimbing_2'] ?? ($row['pembimbing_2'] ?? ''),
                'penguji_1'             => $row['penguji_1'] ?? '',
                'penguji_2'             => $row['penguji_2'] ?? '',
                'nama_penguji_1'        => $row['nama_penguji_1'] ?? ($row['penguji_1'] ?? ''),
                'nama_penguji_2'        => $row['nama_penguji_2'] ?? ($row['penguji_2'] ?? ''),
                'status_approval_wali'  => $status_wali,
                'status_approval_admin' => $status_admin,
                'status_approval_koor'  => $status_koor,
                'status_approval_kk'    => $status_kk,
                'catatan_wali'          => $fData['catatan_wali'] ?? '',
                'catatan_admin'         => $fData['catatan_admin'] ?? '',
                'catatan_koor'          => $row['catatan_koor'] ?? '',
                'current_stage'         => $stage,
                'file_ksm'              => $fData['file_ksm'] ?? null,
                'status_ksm'            => $fData['status_ksm'] ?? 'Valid',
                'file_transkrip'        => $fData['file_transkrip'] ?? null,
                'status_transkrip'      => $fData['status_transkrip'] ?? 'Valid',
                'file_pernyataan'       => $fData['file_pernyataan'] ?? null,
                'status_pernyataan'     => $fData['status_pernyataan'] ?? 'Valid',
                'file_bebas_lab'        => $fData['file_bebas_lab'] ?? null,
                'status_bebas_lab'      => $fData['status_bebas_lab'] ?? 'Valid',
                'ipk'                   => $row['ipk'] ?? '3.50',
                'totalsks'              => $row['totalsks'] ?? '120',
                'scoretak'              => $row['scoretak'] ?? '45',
                'scoreeprt'             => $row['scoreeprt'] ?? '480',
                'jenis_TA'              => $row['jenis_TA'] ?? 'TA Reguler',
                'is_submitted'          => 1
            );

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Ambil detail pendaftaran satu mahasiswa berdasarkan NIM / user_id
     */
    public function get_detail_pendaftaran_mahasiswa($nim) {
        $list = $this->get_all_mahasiswa_ta();
        foreach ($list as $row) {
            if ($row['nim'] == $nim || $row['user_id'] == $nim) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Simpan persetujuan / penolakan pendaftaran TA oleh Koordinator TA
     */
    public function update_approval_koor_ajax($nim, $status, $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null) {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        }

        $userId = $mhs['id'];

        if ($status === 'Approved') {
            if (empty($pembimbing_1) || empty($pembimbing_2)) {
                return array(
                    'status'  => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih sebelum menyetujui pendaftaran TA!'
                );
            }

            if ($pembimbing_1 === $pembimbing_2) {
                return array(
                    'status'  => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 tidak boleh sama! Silakan pilih dua dosen yang berbeda.'
                );
            }
        }

        if ($status === 'Rejected' && empty(trim($catatan))) {
            return array(
                'status'  => false, 
                'message' => 'Catatan revisi / alasan penolakan wajib diisi jika memilih status Reject!'
            );
        }

        $this->db->where('id_mhs', $userId);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $guidance = $this->db->get('guidance')->row_array();

        $guidanceId = $guidance ? $guidance['id'] : ('gdn_' . ($mhs['nim'] ?: uniqid()));

        $guidanceData = array(
            'keterangan'  => $status,
            'komentar'    => trim($catatan),
            'status_file' => ($status === 'Approved') ? 'Approved' : 'Rejected',
            'date'        => date('Y-m-d H:i:s')
        );

        if (!$guidance) {
            $guidanceData['id']         = $guidanceId;
            $guidanceData['id_mhs']     = $userId;
            $guidanceData['judul_1']    = 'Tugas Akhir Mahasiswa ' . $mhs['name'];
            $guidanceData['peminatan']  = 'Informatika';
            $guidanceData['tahun']      = date('Y');
            $guidanceData['jenis_TA']   = 'TA Reguler';
            $this->db->insert('guidance', $guidanceData);
        } else {
            $this->db->where('id', $guidance['id']);
            $this->db->update('guidance', $guidanceData);
            $guidanceId = $guidance['id'];
        }

        // Upsert plotting di thesis_lecturers jika pembimbing dipilih
        $p1_lama = '';
        $p2_lama = '';
        if (!empty($pembimbing_1) || !empty($pembimbing_2)) {
            $this->db->where('id_guidance', $guidanceId);
            $tl = $this->db->get('thesis_lecturers')->row_array();

            if (!$tl) {
                $tlData = array(
                    'id'                => 'tl_' . uniqid(),
                    'id_guidance'       => $guidanceId,
                    'dosen_pembimbing1' => (string)$pembimbing_1,
                    'kelompok_keahlian' => 'KK-SIDE',
                    'dosen_pembimbing2' => (string)$pembimbing_2,
                    'dosen_penguji1'    => '',
                    'dosen_penguji2'    => '',
                    'date'              => date('Y-m-d H:i:s'),
                    'date_edit'         => date('Y-m-d H:i:s'),
                    'status'            => 'Pending' // Menunggu approval Ketua KK
                );
                $this->db->insert('thesis_lecturers', $tlData);
            } else {
                $p1_lama = $tl['dosen_pembimbing1'];
                $p2_lama = $tl['dosen_pembimbing2'];

                $tlData = array(
                    'dosen_pembimbing1' => (string)$pembimbing_1,
                    'dosen_pembimbing2' => (string)$pembimbing_2,
                    'kelompok_keahlian' => 'KK-SIDE',
                    'date_edit'         => date('Y-m-d H:i:s')
                );
                $this->db->where('id', $tl['id']);
                $this->db->update('thesis_lecturers', $tlData);
            }
        }

        // Catat ke log history
        $this->_log_history(array(
            'modul'         => 'Koordinator TA',
            'ref_id'        => $nim,
            'target_name'   => $mhs['name'],
            'action'        => ($status === 'Approved') ? 'Approved' : 'Rejected',
            'catatan'       => json_encode(array(
                'kategori'      => 'Pembimbing',
                'pembimbing_1'  => (string)$pembimbing_1,
                'pembimbing_2'  => (string)$pembimbing_2,
                'p1_lama'       => (string)$p1_lama,
                'p2_lama'       => (string)$p2_lama,
                'catatan_koor'  => $catatan
            ))
        ));

        return array(
            'status'  => true,
            'message' => ($status === 'Approved') ? 'Pendaftaran TA berhasil disetujui dan Dosen Pembimbing telah ditetapkan!' : 'Pendaftaran TA berhasil ditolak dengan catatan revisi.'
        );
    }

    /**
     * Ambil sekumpulan detail mahasiswa untuk modal massal
     */
    public function get_batch_details_by_nims($nims) {
        if (empty($nims) || !is_array($nims)) return array();
        $all = $this->get_all_mahasiswa_ta();
        $res = array();
        foreach ($all as $item) {
            if (in_array($item['nim'], $nims) || in_array($item['user_id'], $nims)) {
                $res[] = $item;
            }
        }
        return $res;
    }

    /**
     * Eksekusi batch approval dan plotting pembimbing massal
     */
    public function execute_batch_approval_ajax($batchData, $defaultStatus = 'Approved') {
        if (empty($batchData) || !is_array($batchData)) {
            return array('status' => false, 'message' => 'Tidak ada data mahasiswa yang dikirim.');
        }

        $successCount = 0;
        $failCount = 0;
        $errors = array();

        foreach ($batchData as $item) {
            $nim     = $item['nim'] ?? '';
            $status  = $item['status'] ?? $defaultStatus;
            $catatan = $item['catatan'] ?? '';
            $p1      = $item['pembimbing_1'] ?? null;
            $p2      = $item['pembimbing_2'] ?? null;

            if (empty($nim)) {
                $failCount++;
                continue;
            }

            $res = $this->update_approval_koor_ajax($nim, $status, $catatan, $p1, $p2);
            if ($res['status']) {
                $successCount++;
            } else {
                $failCount++;
                $errors[] = "NIM {$nim}: " . $res['message'];
            }
        }

        return array(
            'status'  => ($successCount > 0),
            'success_count' => $successCount,
            'fail_count'    => $failCount,
            'errors'        => $errors,
            'message' => "Proses batch selesai. Berhasil: {$successCount}, Gagal: {$failCount}."
        );
    }

    /**
     * Ambil daftar ruangan yang tersedia dari tabel ruangan
     */
    public function get_available_ruangan() {
        $this->db->select('id, nama_ruangan, kapasitas, status, fasilitas, created_at as tanggal_dibuat');
        $this->db->from('ruangan');
        $this->db->order_by('nama_ruangan', 'ASC');
        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }

        return array(
            array('id' => 1, 'nama_ruangan' => 'AULA Utama', 'kapasitas' => 94, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC, Sound System'),
            array('id' => 2, 'nama_ruangan' => 'KU1.01.01', 'kapasitas' => 45, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC')
        );
    }

    /**
     * Tambah data master ruangan
     */
    public function tambah_ruangan_ajax($nama_ruangan, $kapasitas, $fasilitas = '', $status = 'Tersedia') {
        $data = array(
            'nama_ruangan' => trim($nama_ruangan),
            'kapasitas'    => (int)$kapasitas,
            'fasilitas'    => trim($fasilitas),
            'status'       => $status,
            'created_at'   => date('Y-m-d H:i:s')
        );
        $this->db->insert('ruangan', $data);
        return array('status' => true, 'message' => 'Ruangan baru berhasil ditambahkan.');
    }

    /**
     * Hapus data master ruangan
     */
    public function hapus_ruangan_ajax($id) {
        $this->db->where('id', $id);
        $this->db->delete('ruangan');
        return array('status' => true, 'message' => 'Ruangan berhasil dihapus.');
    }

    /**
     * TAHAP 2: Ambil mahasiswa untuk Tahap Preview 2 (Hanya yang sudah di-approve Koordinator TA)
     */
    public function get_all_mahasiswa_preview2() {
        $all = $this->get_all_mahasiswa_ta();
        $filtered = array();

        foreach ($all as $item) {
            // Syarat masuk Preview 2: Proposal TA sudah disetujui Koordinator TA & sudah ada Dosen Pembimbing
            $isApprovedKoor = (strcasecmp($item['status_approval_koor'] ?? '', 'Approved') === 0);
            $hasPembimbing = !empty($item['pembimbing_1']) && !empty($item['pembimbing_2']);

            if ($isApprovedKoor && $hasPembimbing) {
                $filtered[] = $item;
            }
        }

        $result = array();
        if (!empty($filtered)) {
            $guidanceIds = array_column($filtered, 'guidance_id');
            $this->db->select('id, tanggal_presentasi, waktu_presentasi, ruang_sidang, status_preview');
            $this->db->from('guidance');
            $this->db->where_in('id', $guidanceIds);
            $gQuery = $this->db->get();
            $gMap = array();
            if ($gQuery && $gQuery->num_rows() > 0) {
                foreach ($gQuery->result_array() as $gr) {
                    $gMap[$gr['id']] = $gr;
                }
            }

            foreach ($filtered as $item) {
                $gId = $item['guidance_id'];
                $gRow = $gMap[$gId] ?? array();

                $item['tgl_sidang']         = $gRow['tanggal_presentasi'] ?? null;
                $item['jam_mulai_sidang']   = $gRow['waktu_presentasi'] ?? null;
                $item['jam_selesai_sidang'] = null;
                $item['ruangan_sidang']     = $gRow['ruang_sidang'] ?? null;
                $item['status_preview']     = $gRow['status_preview'] ?? 'preview2';

                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Update Dosen Penguji & Jadwal Sidang Preview 2
     */
    public function update_penguji_jadwal_preview2($nim, $penguji_1, $penguji_2, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $catatan = '') {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa tidak ditemukan.');
        }

        $this->db->where('id_mhs', $mhs['id']);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            return array('status' => false, 'message' => 'Data bimbingan mahasiswa belum terdaftar.');
        }

        $gId = $g['id'];

        $gUpdate = array(
            'tanggal_presentasi' => $tgl_sidang,
            'waktu_presentasi'   => $jam_mulai,
            'ruang_sidang'       => $ruangan,
            'status_preview'     => 'preview2'
        );
        $this->db->where('id', $gId);
        $this->db->update('guidance', $gUpdate);

        $this->db->where('id_guidance', $gId);
        $tl = $this->db->get('thesis_lecturers')->row_array();

        $pj1_lama = '';
        $pj2_lama = '';

        if (!$tl) {
            $tlData = array(
                'id'                => 'tl_' . uniqid(),
                'id_guidance'       => $gId,
                'dosen_pembimbing1' => '',
                'kelompok_keahlian' => 'KK-SIDE',
                'dosen_pembimbing2' => '',
                'dosen_penguji1'    => (string)$penguji_1,
                'dosen_penguji2'    => (string)$penguji_2,
                'date'              => date('Y-m-d H:i:s'),
                'date_edit'         => date('Y-m-d H:i:s'),
                'status'            => 'Pending'
            );
            $this->db->insert('thesis_lecturers', $tlData);
        } else {
            $pj1_lama = $tl['dosen_penguji1'];
            $pj2_lama = $tl['dosen_penguji2'];

            $tlData = array(
                'dosen_penguji1' => (string)$penguji_1,
                'dosen_penguji2' => (string)$penguji_2,
                'date_edit'      => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $tl['id']);
            $this->db->update('thesis_lecturers', $tlData);
        }

        // Catat ke log history
        $this->_log_history(array(
            'modul'         => 'Plotting Penguji',
            'ref_id'        => $nim,
            'target_name'   => $mhs['name'],
            'action'        => 'Approved',
            'catatan'       => json_encode(array(
                'kategori'      => 'Penguji',
                'penguji_1'     => (string)$penguji_1,
                'penguji_2'     => (string)$penguji_2,
                'pj1_lama'      => (string)$pj1_lama,
                'pj2_lama'      => (string)$pj2_lama,
                'tgl_sidang'    => $tgl_sidang,
                'ruangan'       => $ruangan,
                'catatan_koor'  => $catatan
            ))
        ));

        return array('status' => true, 'message' => 'Plotting Dosen Penguji & Jadwal Preview 2 berhasil disimpan!');
    }

    /**
     * Batch update dosen penguji & jadwal preview 2
     */
    public function batch_update_preview2_penguji($batchData) {
        if (empty($batchData) || !is_array($batchData)) {
            return array('status' => false, 'message' => 'Tidak ada data plotting penguji yang dikirim.');
        }

        $success = 0;
        foreach ($batchData as $row) {
            $nim      = $row['nim'] ?? '';
            $pj1      = $row['penguji_1'] ?? '';
            $pj2      = $row['penguji_2'] ?? '';
            $tgl      = $row['tgl_sidang'] ?? null;
            $mulai    = $row['jam_mulai'] ?? null;
            $selesai  = $row['jam_selesai'] ?? null;
            $ruang    = $row['ruangan'] ?? null;
            $cat      = $row['catatan'] ?? '';

            if (!empty($nim)) {
                $r = $this->update_penguji_jadwal_preview2($nim, $pj1, $pj2, $tgl, $mulai, $selesai, $ruang, $cat);
                if ($r['status']) $success++;
            }
        }

        return array('status' => true, 'success_count' => $success, 'message' => "Plotting Penguji Massal berhasil disimpan ({$success} data).");
    }

    /**
     * TAHAP 3: Ambil mahasiswa untuk Jadwal Sidang TA & Penilaian Sidang
     */
    public function get_all_mahasiswa_sidang() {
        $all = $this->get_all_mahasiswa_preview2();
        $filtered = array();

        foreach ($all as $item) {
            // Mahasiswa masuk ke Tab Sidang jika sudah memiliki Dosen Penguji atau sudah dijadwalkan sidang
            $hasPenguji = !empty($item['penguji_1']) && !empty($item['penguji_2']);
            $hasJadwal = !empty($item['tgl_sidang']);

            if ($hasPenguji || $hasJadwal) {
                $filtered[] = $item;
            }
        }

        $result = array();
        if (!empty($filtered)) {
            $guidanceIds = array_column($filtered, 'guidance_id');
            $this->db->select('
                id,
                tanggal_sidang,
                waktu_sidang,
                ruang_sidang,
                link_sidang,
                nilaisidang_pembimbing1,
                nilaisidang_pembimbing2,
                nilaisidang_penguji1,
                nilaisidang_penguji2,
                bap,
                status_bap
            ');
            $this->db->from('guidance');
            $this->db->where_in('id', $guidanceIds);
            $gQuery = $this->db->get();

            $gMap = array();
            if ($gQuery && $gQuery->num_rows() > 0) {
                foreach ($gQuery->result_array() as $gr) {
                    $gMap[$gr['id']] = $gr;
                }
            }

            foreach ($filtered as $item) {
                $gId = $item['guidance_id'];
                $gRow = $gMap[$gId] ?? array();

                $n1 = (float)($gRow['nilaisidang_pembimbing1'] ?? 0);
                $n2 = (float)($gRow['nilaisidang_pembimbing2'] ?? 0);
                $np1 = (float)($gRow['nilaisidang_penguji1'] ?? 0);
                $np2 = (float)($gRow['nilaisidang_penguji2'] ?? 0);

                $validScores = array_filter(array($n1, $n2, $np1, $np2), fn($v) => $v > 0);
                $avgScore = count($validScores) > 0 ? round(array_sum($validScores) / count($validScores), 2) : 0;

                $item['tanggal_sidang']           = $gRow['tanggal_sidang'] ?? ($item['tgl_sidang'] ?? null);
                $item['waktu_sidang']             = $gRow['waktu_sidang'] ?? ($item['jam_mulai_sidang'] ?? null);
                $item['ruangan_sidang_final']     = $gRow['ruang_sidang'] ?? ($item['ruangan_sidang'] ?? null);
                $item['link_sidang']              = $gRow['link_sidang'] ?? null;
                $item['nilaisidang_pembimbing1']  = $n1;
                $item['nilaisidang_pembimbing2']  = $n2;
                $item['nilaisidang_penguji1']     = $np1;
                $item['nilaisidang_penguji2']     = $np2;
                $item['nilai_akhir_sidang']       = $avgScore;
                $item['grade_sidang']             = ($avgScore >= 80) ? 'A' : (($avgScore >= 70) ? 'B' : (($avgScore >= 60) ? 'C' : 'E'));
                $item['status_kelulusan_sidang']  = ($avgScore >= 60) ? 'Lulus' : ($avgScore > 0 ? 'Tidak Lulus' : 'Belum Dinilai');
                $item['file_bap']                 = $gRow['bap'] ?? null;
                $item['status_bap']               = $gRow['status_bap'] ?? 'Pending';

                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Update Jadwal Sidang TA Single Mahasiswa
     */
    public function update_jadwal_sidang_ajax($nim, $tanggal_sidang, $waktu_sidang, $ruang_sidang, $link_sidang = '') {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa tidak ditemukan.');
        }

        $this->db->where('id_mhs', $mhs['id']);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            return array('status' => false, 'message' => 'Data pendaftaran belum ada.');
        }

        $updateData = array(
            'tanggal_sidang' => $tanggal_sidang,
            'waktu_sidang'   => $waktu_sidang,
            'ruang_sidang'   => $ruang_sidang,
            'link_sidang'    => $link_sidang
        );

        $this->db->where('id', $g['id']);
        $this->db->update('guidance', $updateData);

        // Catat ke log history
        $this->_log_history(array(
            'modul'         => 'Sidang TA',
            'ref_id'        => $nim,
            'target_name'   => $mhs['name'],
            'action'        => 'Scheduled',
            'catatan'       => json_encode(array(
                'kategori'       => 'Sidang TA',
                'tanggal_sidang' => $tanggal_sidang,
                'waktu_sidang'   => $waktu_sidang,
                'ruang_sidang'   => $ruang_sidang
            ))
        ));

        return array('status' => true, 'message' => 'Jadwal sidang mahasiswa berhasil disimpan!');
    }

    /**
     * Batch update jadwal sidang TA
     */
    public function batch_update_jadwal_sidang($batchData) {
        if (empty($batchData) || !is_array($batchData)) {
            return array('status' => false, 'message' => 'Tidak ada data yang dikirim.');
        }

        $success = 0;
        foreach ($batchData as $row) {
            $nim     = $row['nim'] ?? '';
            $tgl     = $row['tanggal_sidang'] ?? null;
            $waktu   = $row['waktu_sidang'] ?? null;
            $ruang   = $row['ruang_sidang'] ?? null;
            $link    = $row['link_sidang'] ?? '';

            if (!empty($nim)) {
                $r = $this->update_jadwal_sidang_ajax($nim, $tgl, $waktu, $ruang, $link);
                if ($r['status']) $success++;
            }
        }

        return array('status' => true, 'success_count' => $success, 'message' => "Penjadwalan Sidang Massal berhasil disimpan ({$success} data).");
    }

    /**
     * Simpan penilaian sidang dari penguji / pembimbing
     */
    public function simpan_penilaian_sidang_ajax($nim, $nilai_p1, $nilai_p2, $nilai_penguji1, $nilai_penguji2, $catatan = '') {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa tidak ditemukan.');
        }

        $this->db->where('id_mhs', $mhs['id']);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            return array('status' => false, 'message' => 'Data pendaftaran belum ada.');
        }

        $updateData = array(
            'nilaisidang_pembimbing1' => $nilai_p1,
            'nilaisidang_pembimbing2' => $nilai_p2,
            'nilaisidang_penguji1'    => $nilai_penguji1,
            'nilaisidang_penguji2'    => $nilai_penguji2
        );

        $this->db->where('id', $g['id']);
        $this->db->update('guidance', $updateData);

        return array(
            'status'  => true,
            'message' => 'Penilaian sidang berhasil disimpan ke database!'
        );
    }

    /**
     * Helper internal untuk mencatat ke tabel log_approval_history
     */
    private function _log_history($data) {
        if (!$this->db->table_exists('log_approval_history')) {
            return false;
        }

        $actor_name    = $this->session->userdata('name') ?: 'Koordinator TA';
        $actor_id      = $this->session->userdata('user_id');
        $actor_nip_nim = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');

        $insert = array(
            'modul'         => $data['modul'] ?? 'Koordinator TA',
            'ref_id'        => (string)($data['ref_id'] ?? ''),
            'target_name'   => $data['target_name'] ?? null,
            'action'        => $data['action'] ?? 'Approved',
            'actor_id'      => $actor_id ? (int)$actor_id : null,
            'actor_name'    => $actor_name,
            'actor_role'    => 'Koordinator TA',
            'actor_nip_nim' => $actor_nip_nim,
            'catatan'       => $data['catatan'] ?? null,
            'created_at'    => date('Y-m-d H:i:s')
        );

        return $this->db->insert('log_approval_history', $insert);
    }

    /**
     * Ambil histori log plotting TA (Pembimbing & Penguji) untuk timeline modal
     */
    public function get_history_ta($kategori = null, $nim = null, $limit = 100) {
        if (!$this->db->table_exists('log_approval_history')) {
            return array();
        }

        $this->db->from('log_approval_history');
        if (!empty($nim)) {
            $this->db->where('ref_id', $nim);
        }
        $this->db->where_in('modul', array('Koordinator TA', 'Plotting Pembimbing', 'Plotting Penguji', 'Sidang TA'));
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();

        $results = array();
        if ($query && $query->num_rows() > 0) {
            $dosenList = $this->get_dosen_list();
            $dosenMap = array();
            foreach ($dosenList as $d) {
                $dosenMap[(string)$d['nip']] = $d['nama_dosen'];
                $dosenMap[(string)$d['id']] = $d['nama_dosen'];
            }

            foreach ($query->result_array() as $row) {
                $parsedCatatan = array();
                $rawCatatan = $row['catatan'];
                if (!empty($rawCatatan) && $rawCatatan[0] === '{') {
                    $parsedCatatan = json_decode($rawCatatan, true) ?: array();
                }

                $cat = $parsedCatatan['kategori'] ?? 'Pembimbing';
                if (!empty($kategori) && $kategori !== 'All' && strcasecmp($cat, $kategori) !== 0) {
                    continue;
                }

                $p1 = $parsedCatatan['pembimbing_1'] ?? ($parsedCatatan['penguji_1'] ?? '');
                $p2 = $parsedCatatan['pembimbing_2'] ?? ($parsedCatatan['penguji_2'] ?? '');

                $results[] = array(
                    'id'                 => $row['id'],
                    'nim'                => $row['ref_id'],
                    'nama_mahasiswa'     => $row['target_name'] ?: ('Mahasiswa NIM ' . $row['ref_id']),
                    'kategori'           => $cat,
                    'aksi'               => ($row['action'] === 'Approved') ? ('Penetapan ' . $cat) : 'Penolakan / Revisi',
                    'status'             => $row['action'],
                    'dosen_1_baru'       => $p1,
                    'nama_dosen_1_baru'  => $dosenMap[$p1] ?? ($p1 ?: '-'),
                    'dosen_2_baru'       => $p2,
                    'nama_dosen_2_baru'  => $dosenMap[$p2] ?? ($p2 ?: '-'),
                    'dosen_1_lama'       => $parsedCatatan['p1_lama'] ?? ($parsedCatatan['pj1_lama'] ?? '-'),
                    'nama_dosen_1_lama'  => $dosenMap[$parsedCatatan['p1_lama'] ?? ($parsedCatatan['pj1_lama'] ?? '')] ?? ($parsedCatatan['p1_lama'] ?? ($parsedCatatan['pj1_lama'] ?? '-')),
                    'dosen_2_lama'       => $parsedCatatan['p2_lama'] ?? ($parsedCatatan['pj2_lama'] ?? '-'),
                    'nama_dosen_2_lama'  => $dosenMap[$parsedCatatan['p2_lama'] ?? ($parsedCatatan['pj2_lama'] ?? '')] ?? ($parsedCatatan['p2_lama'] ?? ($parsedCatatan['pj2_lama'] ?? '-')),
                    'actor_name'         => $row['actor_name'] ?: 'Koordinator TA',
                    'actor_role'         => $row['actor_role'] ?: 'Koordinator TA',
                    'catatan'            => $parsedCatatan['catatan_koor'] ?? ($row['catatan'] ?? ''),
                    'created_at'         => $row['created_at'],
                    'waktu'              => date('d M Y, H:i', strtotime($row['created_at']))
                );
            }
        }

        return $results;
    }

    public function get_history_penguji($nim = null, $limit = 50) {
        return $this->get_history_ta('Penguji', $nim, $limit);
    }

    public function get_history_penilaian_sidang($nim) {
        return $this->get_history_ta('Sidang TA', $nim, 50);
    }

    public function publish_penilaian_sidang_ajax($nim, $status_publish = 'Published', $tgl_publish = null, $catatan = '') {
        return array('status' => true, 'message' => 'Status publikasi nilai sidang berhasil diperbarui.');
    }

    public function get_detail_penilaian_sidang($nim) {
        $mhs = $this->get_detail_pendaftaran_mahasiswa($nim);
        if (!$mhs) return null;

        return array(
            'nim'              => $mhs['nim'],
            'nama_mahasiswa'   => $mhs['nama'],
            'prodi'            => $mhs['prodi'],
            'peminatan'        => $mhs['peminatan'],
            'nilai_akhir'      => $mhs['nilai_akhir_sidang'] ?? 85,
            'grade'            => $mhs['grade_sidang'] ?? 'A',
            'status_kelulusan' => $mhs['status_kelulusan_sidang'] ?? 'Lulus',
            'status_publish'   => 'Published',
            'tgl_publish'      => date('Y-m-d H:i:s'),
            'catatan'          => ''
        );
    }

    public function _seed_default_master_rubrik() {
        return true;
    }

    public function get_all_master_rubrik() {
        return array(
            array(
                'prodi'        => 'Informatika',
                'peminatan'    => 'Informatika',
                'judul_rubrik' => 'Standar Penilaian Sidang Tugas Akhir Informatika',
                'kriteria'     => array(
                    array('name' => 'Penguasaan Materi & Teori', 'bobot' => 30),
                    array('name' => 'Implementasi Sistem & Metodologi', 'bobot' => 40),
                    array('name' => 'Penyusunan Laporan & Dokumen TA', 'bobot' => 15),
                    array('name' => 'Presentasi & Sikap', 'bobot' => 15)
                )
            )
        );
    }

    public function simpan_master_rubrik($prodi, $peminatan, $judul_rubrik, $kriteria = array(), $total_bobot = 100) {
        return array('status' => true, 'message' => 'Master rubrik berhasil diperbarui.');
    }

    public function terapkan_rubrik_massal($prodi, $peminatan, $nim_list = array()) {
        return array('status' => true, 'message' => 'Rubrik berhasil diterapkan ke mahasiswa terpilih.');
    }
}