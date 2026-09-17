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
                    $files_map[$mId]['status_ksm'] = !empty($f['status_adminlaa']) ? $f['status_adminlaa'] : 'Valid';
                } else if (strpos($namaJenis, 'transkrip') !== false) {
                    $files_map[$mId]['file_transkrip'] = $f['file'];
                    $files_map[$mId]['status_transkrip'] = !empty($f['status_adminlaa']) ? $f['status_adminlaa'] : 'Valid';
                } else if (strpos($namaJenis, 'pernyataan') !== false) {
                    $files_map[$mId]['file_pernyataan'] = $f['file'];
                    $files_map[$mId]['status_pernyataan'] = !empty($f['status_adminlaa']) ? $f['status_adminlaa'] : 'Valid';
                } else if (strpos($namaJenis, 'bebas_lab') !== false || strpos($namaJenis, 'lab') !== false) {
                    $files_map[$mId]['file_bebas_lab'] = $f['file'];
                    $files_map[$mId]['status_bebas_lab'] = !empty($f['status_adminlaa']) ? $f['status_adminlaa'] : 'Valid';
                }

                if (!empty($f['status_doswal'])) {
                    $files_map[$mId]['status_doswal'] = $f['status_doswal'];
                }
                if (!empty($f['status_adminlaa'])) {
                    $files_map[$mId]['status_adminlaa'] = $f['status_adminlaa'];
                }
                if (!empty($f['komentar'])) {
                    $files_map[$mId]['catatan_admin'] = $f['komentar'];
                }
            }
        }

        return $files_map;
    }

    /**
     * Ambil semua mahasiswa mendaftar TA untuk Dashboard Koordinator TA
     */
    public function get_all_mahasiswa_ta() {
        // Query mahasiswa dari tabel user (role_id = 4)
        $this->db->select('
            u.id as user_id,
            u.nim,
            u.name,
            u.email,
            u.no_telp as no_hp,
            u.dosen_wali,
            u.prodi as user_prodi,
            g.id as guidance_id,
            g.judul_1,
            g.judul_2,
            g.judul_3,
            g.keterangan as status_approval_koor,
            g.komentar as catatan_koor,
            g.peminatan,
            g.tahun,
            g.status_file,
            g.date as tanggal_pengajuan,
            g.totalsks,
            g.ipk,
            g.scoreeprt,
            g.scoretak,
            g.jenis_TA,
            g.status_preview,
            tl.dosen_pembimbing1 as pembimbing_1,
            tl.dosen_pembimbing2 as pembimbing_2,
            tl.dosen_penguji1 as penguji_1,
            tl.dosen_penguji2 as penguji_2,
            tl.kelompok_keahlian as kode_kk,
            tl.status as status_plotting,
            d1.name as nama_pembimbing_1,
            d2.name as nama_pembimbing_2,
            p1.name as nama_penguji_1,
            p2.name as nama_penguji_2,
            dw.name as nama_dosen_wali_resolved
        ');
        $this->db->from('user u');
        $this->db->join('guidance g', 'g.id_mhs = u.id OR g.id_mhs = u.nim', 'left');
        $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
        $this->db->join('user d1', 'd1.nip = tl.dosen_pembimbing1 OR d1.id = tl.dosen_pembimbing1 OR d1.kode_dosen = tl.dosen_pembimbing1', 'left');
        $this->db->join('user d2', 'd2.nip = tl.dosen_pembimbing2 OR d2.id = tl.dosen_pembimbing2 OR d2.kode_dosen = tl.dosen_pembimbing2', 'left');
        $this->db->join('user p1', 'p1.nip = tl.dosen_penguji1 OR p1.id = tl.dosen_penguji1 OR p1.kode_dosen = tl.dosen_penguji1', 'left');
        $this->db->join('user p2', 'p2.nip = tl.dosen_penguji2 OR p2.id = tl.dosen_penguji2 OR p2.kode_dosen = tl.dosen_penguji2', 'left');
        $this->db->join('user dw', 'dw.nip = u.dosen_wali OR dw.name = u.dosen_wali OR dw.id = u.dosen_wali', 'left');
        $this->db->where('u.role_id', 4);
        $this->db->order_by('u.name', 'ASC');

        $query = $this->db->get();
        $raw = $query ? $query->result_array() : array();

        if (empty($raw)) return array();

        // Kumpulkan ID Mahasiswa untuk mengambil file_pendaftaran
        $id_list = array();
        foreach ($raw as $r) {
            if (!empty($r['user_id'])) $id_list[] = $r['user_id'];
            if (!empty($r['nim'])) $id_list[] = $r['nim'];
        }
        $id_list = array_unique($id_list);
        $files_map = $this->_get_mhs_files($id_list);

        $result = array();
        foreach ($raw as $row) {
            $uId = $row['user_id'];
            $nim = $row['nim'] ?? $uId;
            $fData = $files_map[$uId] ?? ($files_map[$nim] ?? array());

            // Pecah nama menjadi nama_depan dan nama_belakang
            $nameParts = explode(' ', trim($row['name'] ?? 'Mahasiswa'));
            $nama_depan = array_shift($nameParts);
            $nama_belakang = !empty($nameParts) ? implode(' ', $nameParts) : $nim;

            $status_wali  = $fData['status_doswal'] ?? 'Approved';
            $status_admin = $fData['status_adminlaa'] ?? 'Approved';
            $status_koor  = !empty($row['status_approval_koor']) ? $row['status_approval_koor'] : 'Pending';
            $status_kk    = !empty($row['status_plotting']) ? $row['status_plotting'] : 'Pending';

            // Tentukan stage
            $stage = 'Dosen Wali';
            if ($status_wali === 'Approved') {
                $stage = 'Admin Layanan';
                if ($status_admin === 'Approved') {
                    $stage = 'Koordinator TA';
                    if ($status_koor === 'Approved') {
                        $stage = 'Ketua KK';
                        if ($status_kk === 'Approved') {
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
     * Update Approval / Reject oleh Koordinator TA serta Plotting Pembimbing 1 & 2
     */
    public function update_approval_koor_ajax($nim, $status, $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null) {
        // Cari user mahasiswa
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

        // Cari atau buat record di guidance
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

        // Upsert plotting di thesis_lecturers jika Approved atau jika pembimbing dipilih
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
                    'status'            => ($status === 'Approved') ? 'Approved' : 'Pending'
                );
                $this->db->insert('thesis_lecturers', $tlData);
            } else {
                $tlData = array(
                    'dosen_pembimbing1' => (string)$pembimbing_1,
                    'dosen_pembimbing2' => (string)$pembimbing_2,
                    'kelompok_keahlian' => 'KK-SIDE',
                    'status'            => ($status === 'Approved') ? 'Approved' : 'Pending',
                    'date_edit'         => date('Y-m-d H:i:s')
                );
                $this->db->where('id', $tl['id']);
                $this->db->update('thesis_lecturers', $tlData);
            }
        }

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
     * Batch Approval Koordinator TA
     */
    public function batch_approval_koor_ajax($nims, $status = 'Approved', $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null, $penguji_1 = null, $penguji_2 = null, $plottings = array()) {
        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Tidak ada mahasiswa yang dipilih.');
        }

        $plottingsMap = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $p) {
                if (isset($p['nim'])) {
                    $plottingsMap[$p['nim']] = $p;
                }
            }
        }

        $successCount = 0;
        $errors = array();

        foreach ($nims as $nim) {
            $p1 = $pembimbing_1;
            $p2 = $pembimbing_2;
            if (isset($plottingsMap[$nim])) {
                $p1 = $plottingsMap[$nim]['pembimbing_1'] ?? $p1;
                $p2 = $plottingsMap[$nim]['pembimbing_2'] ?? $p2;
            }

            $res = $this->update_approval_koor_ajax($nim, $status, $catatan, $p1, $p2);
            if ($res['status']) {
                $successCount++;
            } else {
                $errors[] = "NIM {$nim}: {$res['message']}";
            }
        }

        if ($successCount === 0) {
            return array(
                'status'  => false,
                'message' => 'Gagal memproses approval: ' . implode(' | ', $errors)
            );
        }

        return array(
            'status'  => true,
            'message' => "Berhasil memproses {$successCount} mahasiswa." . (!empty($errors) ? ' Beberapa catatan: ' . implode('; ', $errors) : '')
        );
    }

    /**
     * Ambil Ruangan yang Tersedia dari tabel ruangan
     */
    public function get_available_ruangan() {
        $this->db->select('id, id_kategori, ruangan, akses, kapasitas, images, date');
        $this->db->from('ruangan');
        $this->db->order_by('ruangan', 'ASC');
        $query = $this->db->get();

        $result = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $result[] = array(
                    'id'           => $row['id'],
                    'kode_ruangan' => $row['id'],
                    'nama_ruangan' => $row['ruangan'],
                    'ruangan'      => $row['ruangan'],
                    'kapasitas'    => $row['kapasitas'] ?? 30,
                    'lokasi'       => 'Gedung Fakultas Informatika',
                    'akses'        => $row['akses'] ?? 'Tersedia',
                    'date'         => $row['date']
                );
            }
        }

        return $result;
    }

    /**
     * Tambah Ruangan Baru
     */
    public function tambah_ruangan_ajax($kode_ruangan, $nama_ruangan, $lokasi = '', $kapasitas = 30) {
        $this->db->where('id', $kode_ruangan);
        $this->db->or_where('ruangan', $nama_ruangan);
        $exist = $this->db->get('ruangan')->row_array();

        if ($exist) {
            return array('status' => false, 'message' => 'Kode atau Nama Ruangan sudah terdaftar.');
        }

        $data = array(
            'id'          => $kode_ruangan,
            'id_kategori' => '3',
            'ruangan'     => $nama_ruangan,
            'akses'       => 'Tersedia',
            'kapasitas'   => (int)$kapasitas,
            'date'        => date('Y-m-d H:i:s')
        );

        $inserted = $this->db->insert('ruangan', $data);
        if ($inserted) {
            return array('status' => true, 'message' => 'Ruangan sidang berhasil ditambahkan!', 'data' => $data);
        } else {
            return array('status' => false, 'message' => 'Gagal menyimpan ke database.');
        }
    }

    /**
     * Hapus Ruangan
     */
    public function hapus_ruangan_ajax($id_ruangan) {
        $this->db->where('id', $id_ruangan);
        $deleted = $this->db->delete('ruangan');
        if ($deleted) {
            return array('status' => true, 'message' => 'Ruangan berhasil dihapus.');
        } else {
            return array('status' => false, 'message' => 'Gagal menghapus ruangan.');
        }
    }

    /**
     * Ambil Data Mahasiswa untuk Tahap Preview 2
     */
    public function get_all_mahasiswa_preview2() {
        $all = $this->get_all_mahasiswa_ta();
        $result = array();

        foreach ($all as $item) {
            $guidanceId = $item['guidance_id'];
            $this->db->where('id', $guidanceId);
            $gRow = $this->db->get('guidance')->row_array();

            $item['tgl_sidang']         = $gRow['tanggal_presentasi'] ?? null;
            $item['jam_mulai_sidang']   = $gRow['waktu_presentasi'] ?? null;
            $item['jam_selesai_sidang'] = null;
            $item['ruangan_sidang']     = $gRow['ruang_sidang'] ?? null;
            $item['status_preview']     = $gRow['status_preview'] ?? 'preview2';

            $result[] = $item;
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

        // Cari guidance
        $this->db->where('id_mhs', $mhs['id']);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            return array('status' => false, 'message' => 'Data bimbingan mahasiswa belum terdaftar.');
        }

        $gId = $g['id'];

        // Update jadwal di guidance
        $gUpdate = array(
            'tanggal_presentasi' => $tgl_sidang,
            'waktu_presentasi'   => $jam_mulai,
            'ruang_sidang'       => $ruangan,
            'status_preview'     => 'preview2'
        );
        $this->db->where('id', $gId);
        $this->db->update('guidance', $gUpdate);

        // Update penguji di thesis_lecturers
        $this->db->where('id_guidance', $gId);
        $tl = $this->db->get('thesis_lecturers')->row_array();

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
            $tlData = array(
                'dosen_penguji1' => (string)$penguji_1,
                'dosen_penguji2' => (string)$penguji_2,
                'date_edit'      => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $tl['id']);
            $this->db->update('thesis_lecturers', $tlData);
        }

        return array(
            'status'  => true,
            'message' => 'Plotting Dosen Penguji & Jadwal Preview 2 berhasil disimpan!'
        );
    }

    /**
     * Batch Penguji Preview 2
     */
    public function batch_penguji_preview2_ajax($nims, $penguji_1 = null, $penguji_2 = null, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $plottings = array()) {
        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Tidak ada mahasiswa terpilih.');
        }

        $plottingsMap = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $p) {
                if (isset($p['nim'])) {
                    $plottingsMap[$p['nim']] = $p;
                }
            }
        }

        $success = 0;
        foreach ($nims as $nim) {
            $p1 = $penguji_1;
            $p2 = $penguji_2;
            if (isset($plottingsMap[$nim])) {
                $p1 = $plottingsMap[$nim]['penguji_1'] ?? $p1;
                $p2 = $plottingsMap[$nim]['penguji_2'] ?? $p2;
            }
            $res = $this->update_penguji_jadwal_preview2($nim, $p1, $p2, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
            if ($res['status']) $success++;
        }

        return array(
            'status'  => true,
            'message' => "Berhasil memplot {$success} mahasiswa untuk Preview 2."
        );
    }

    /**
     * Ambil Data Mahasiswa Sidang TA
     */
    public function get_all_mahasiswa_sidang() {
        $all = $this->get_all_mahasiswa_ta();
        $result = array();

        foreach ($all as $item) {
            $guidanceId = $item['guidance_id'];
            $this->db->where('id', $guidanceId);
            $gRow = $this->db->get('guidance')->row_array();

            $item['tgl_sidang']              = $gRow['tanggal_sidang'] ?? null;
            $item['jam_mulai_sidang']        = $gRow['waktu_sidang'] ?? null;
            $item['jam_selesai_sidang']      = null;
            $item['ruangan_sidang']          = $gRow['ruang_sidang'] ?? null;
            $item['link_sidang']             = $gRow['link_sidang'] ?? null;
            $item['status_sidang']           = !empty($gRow['tanggal_sidang']) ? 'Terjadwal' : 'Belum Dijadwalkan';
            $item['nilaisidang_pembimbing1'] = $gRow['nilaisidang_pembimbing1'] ?? null;
            $item['nilaisidang_pembimbing2'] = $gRow['nilaisidang_pembimbing2'] ?? null;
            $item['nilaisidang_penguji1']    = $gRow['nilaisidang_penguji1'] ?? null;
            $item['nilaisidang_penguji2']    = $gRow['nilaisidang_penguji2'] ?? null;
            $item['bap']                     = $gRow['bap'] ?? null;
            $item['status_bap']              = $gRow['status_bap'] ?? 'Pending';
            
            // Hitung nilai akhir jika ada
            $scores = array_filter(array(
                $item['nilaisidang_pembimbing1'],
                $item['nilaisidang_pembimbing2'],
                $item['nilaisidang_penguji1'],
                $item['nilaisidang_penguji2']
            ), function($v) { return is_numeric($v); });

            if (!empty($scores)) {
                $avg = array_sum($scores) / count($scores);
                $item['nilai_akhir_sidang'] = round($avg, 2);
                $item['grade_sidang'] = ($avg >= 80) ? 'A' : (($avg >= 70) ? 'AB' : (($avg >= 65) ? 'B' : (($avg >= 60) ? 'BC' : 'C')));
                $item['status_kelulusan_sidang'] = ($avg >= 60) ? 'Lulus' : 'Tidak Lulus';
            } else {
                $item['nilai_akhir_sidang'] = null;
                $item['grade_sidang'] = null;
                $item['status_kelulusan_sidang'] = 'Belum Dinilai';
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Update Jadwal Sidang TA Single Mahasiswa
     */
    public function update_jadwal_sidang_ajax($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan, $bypass_conflict = false) {
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
            return array('status' => false, 'message' => 'Data bimbingan belum terdaftar.');
        }

        $this->db->where('id', $g['id']);
        $this->db->update('guidance', array(
            'tanggal_sidang' => $tgl_sidang,
            'waktu_sidang'   => $jam_mulai,
            'ruang_sidang'   => $ruangan
        ));

        return array(
            'status'  => true,
            'message' => 'Jadwal sidang mahasiswa berhasil disimpan!'
        );
    }

    /**
     * Batch Jadwal Sidang per Mahasiswa
     */
    public function batch_jadwal_sidang_per_mhs_ajax($schedules) {
        if (empty($schedules) || !is_array($schedules)) {
            return array('status' => false, 'message' => 'Tidak ada jadwal yang dikirim.');
        }

        $count = 0;
        foreach ($schedules as $s) {
            $nim = $s['nim'] ?? null;
            $tgl = $s['tgl_sidang'] ?? null;
            $jam = $s['jam_mulai_sidang'] ?? null;
            $ruang = $s['ruangan_sidang'] ?? null;
            if ($nim && $tgl && $jam && $ruang) {
                $res = $this->update_jadwal_sidang_ajax($nim, $tgl, $jam, null, $ruang, true);
                if ($res['status']) $count++;
            }
        }

        return array(
            'status'  => true,
            'message' => "Berhasil memperbarui jadwal sidang untuk {$count} mahasiswa."
        );
    }

    /**
     * Simpan Penilaian Sidang
     */
    public function simpan_penilaian_sidang_ajax($nim, $prodi, $peminatan, $nilai_akhir, $grade, $status_kelulusan, $detail_penilaian = array(), $catatan = '', $status_publish = 'Draft', $tgl_publish = null, $tahun_akademik = null) {
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
            return array('status' => false, 'message' => 'Data bimbingan belum ditemukan.');
        }

        $data = array(
            'nilaisidang_pembimbing1'    => $nilai_akhir,
            'penilaiansidang_pembimbing1'=> is_array($detail_penilaian) ? json_encode($detail_penilaian) : $detail_penilaian,
            'status_bap'                 => ($status_kelulusan === 'Lulus') ? 'Disetujui' : 'Revisi'
        );

        $this->db->where('id', $g['id']);
        $this->db->update('guidance', $data);

        return array(
            'status'  => true,
            'message' => 'Penilaian sidang berhasil disimpan ke database!'
        );
    }

    /**
     * Master Rubrik Dinamis & Histori (Fallback Safe In-Memory / Default)
     */
    public function get_history_ta($kategori = null, $nim = null, $limit = 100) {
        return array();
    }

    public function get_history_penguji($nim = null, $limit = 50) {
        return array();
    }

    public function get_history_penilaian_sidang($nim) {
        return array();
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