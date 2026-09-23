<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KoordinatorTA_model');
        $this->load->helper(array('form', 'url', 'text'));

        // Deteksi apakah request berasal dari AJAX / background fetch
        $uriString = $this->uri->uri_string();
        $isAjax = $this->input->is_ajax_request() || 
                  (strpos($uriString, 'ajax_') !== false) ||
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // 1. Cek Login
        if (!$this->session->userdata('logged_in')) {
            if ($isAjax) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses halaman Koordinator TA.');
            redirect('login');
            return;
        }

        // 2. Cek Role (Hanya Role 6 = Koordinator TA, atau Role 1 = Admin)
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 6 && $role_id !== 1) {
            if ($isAjax) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => false, 'message' => 'Akses ditolak. Halaman ini khusus untuk Koordinator TA.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus untuk Koordinator TA.');
            redirect('login');
            return;
        }
    }

    // Dashboard Koordinator TA: Daftar Mahasiswa Mendaftar Tugas Akhir, Plotting Preview 2, & Penjadwalan Sidang
    public function index() {
        $nip_koor = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');
        $data['title'] = 'Dashboard Koordinator TA';
        $data['nip_koor'] = $nip_koor;
        $data['list_mahasiswa'] = $this->KoordinatorTA_model->get_all_mahasiswa_ta();
        $data['list_preview2'] = $this->KoordinatorTA_model->get_all_mahasiswa_preview2();
        $data['list_sidang'] = $this->KoordinatorTA_model->get_all_mahasiswa_sidang();
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();
        $data['ruangan_list'] = $this->KoordinatorTA_model->get_available_ruangan();

        $this->load->view('koordinator_ta/dashboard', $data);
    }

    // Halaman Monitoring Status Peserta Tugas Akhir (Semua Tahapan: Dosen Wali -> Lulus)
    public function monitoring() {
        $nip_koor = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');
        $data['title'] = 'Monitoring Status Peserta Tugas Akhir';
        $data['nip_koor'] = $nip_koor;
        $data['list_peserta'] = $this->KoordinatorTA_model->get_monitoring_peserta_ta();
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();

        $this->load->view('koordinator_ta/monitoring', $data);
    }

    // Alias route status_peserta_ta
    public function status_peserta_ta() {
        $this->monitoring();
    }

    public function pengaturan_jalur() {
        $data['title'] = 'Pengaturan Jalur Sidang & Non-Sidang (Dinamis)';
        $this->load->model('Rekomendasi_model');
        $data['options'] = $this->Rekomendasi_model->get_all_options();
        $data['options_grouped'] = $this->Rekomendasi_model->get_all_options_grouped();
        $this->load->view('admin_layanan/pengaturan_jalur', $data);
    }


    // Detail Mahasiswa & Approval Koordinator TA

    public function detail_mahasiswa($nim) {
        $data['title'] = 'Detail & Approval Koordinator TA';
        $data['detail'] = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();
        $this->load->model('Rekomendasi_model');
        $data['latest_rekomendasi'] = $this->Rekomendasi_model->get_latest_submission($nim);



        // Fallback POST standard
        if ($this->input->post('action')) {
            $status       = $this->input->post('status'); // 'Approved' atau 'Rejected'
            $catatan      = trim($this->input->post('catatan_koor') ?? '');
            $pembimbing_1 = $this->input->post('pembimbing_1');
            $pembimbing_2 = $this->input->post('pembimbing_2');

            $res = $this->KoordinatorTA_model->update_approval_koor_ajax($nim, $status, $catatan, $pembimbing_1, $pembimbing_2);
            if ($res['status']) {
                $this->session->set_flashdata('success', $res['message']);
            } else {
                $this->session->set_flashdata('error', $res['message']);
            }
            redirect('koordinatorta/detail_mahasiswa/' . $nim);
            return;
        }

        $this->load->view('koordinator_ta/detail_mahasiswa', $data);
    }

    // AJAX Endpoint: Approval Koordinator TA dengan Validasi LAA & Pembimbing 1 & 2
    public function ajax_approval() {
        header('Content-Type: application/json');

        $nim          = $this->input->post('nim');
        $status       = $this->input->post('status'); // 'Approved' / 'Rejected'
        $catatan      = trim($this->input->post('catatan_koor') ?? '');
        $pembimbing_1 = $this->input->post('pembimbing_1');
        $pembimbing_2 = $this->input->post('pembimbing_2');

        if (empty($nim) || empty($status)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Parameter tidak lengkap.'
            ));
            return;
        }

        $result = $this->KoordinatorTA_model->update_approval_koor_ajax($nim, $status, $catatan, $pembimbing_1, $pembimbing_2);
        echo json_encode($result);
    }

    // AJAX Endpoint: Ambil Detail Berkas & Profil Lengkap untuk Cek Dokumen Massal
    public function ajax_get_batch_details() {
        header('Content-Type: application/json');

        $nims_raw = $this->input->post('nims');
        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array('status' => false, 'message' => 'Tidak ada NIM terpilih.'));
            return;
        }

        $list = $this->KoordinatorTA_model->get_batch_details_by_nims($nims);
        $data = array();

        $resolve_pdf_url = function($filename) {
            if (empty($filename)) {
                return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
            }
            if (strpos($filename, 'uploads/') === 0 && file_exists(FCPATH . $filename)) {
                return base_url($filename);
            }
            $sub_path = 'uploads/persyaratan_ta/' . $filename;
            if (file_exists(FCPATH . $sub_path)) {
                return base_url($sub_path);
            }
            return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
        };

        foreach ($list as $r) {
            $full_name = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            if (empty($full_name)) $full_name = 'Mahasiswa ' . $r['nim'];

            $data[] = array(
                'nim'                   => $r['nim'],
                'nama'                  => htmlspecialchars($full_name),
                'prodi'                 => htmlspecialchars($r['prodi'] ?? $r['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual'),
                'konsentrasi'           => htmlspecialchars($r['konsentrasi_dkv'] ?? '-'),
                'kode_kk'               => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'email'                 => htmlspecialchars($r['email'] ?? ''),
                'nama_dosen_wali'       => htmlspecialchars($r['nama_dosen_wali'] ?? 'Dosen Wali'),
                'nip_dosen_wali'        => htmlspecialchars($r['nip_dosen_wali'] ?? ''),
                'judul'                 => htmlspecialchars($r['judul_1'] ?? ''),
                'judul_1'               => htmlspecialchars($r['judul_1'] ?? ''),
                'judul_2'               => htmlspecialchars($r['judul_2'] ?? ''),
                'judul_3'               => htmlspecialchars($r['judul_3'] ?? ''),
                'judul_en'              => htmlspecialchars($r['judul_en'] ?? ''),
                'deskripsi_1'           => htmlspecialchars($r['deskripsi_1'] ?? ''),
                'deskripsi_2'           => htmlspecialchars($r['deskripsi_2'] ?? ''),
                'deskripsi_3'           => htmlspecialchars($r['deskripsi_3'] ?? ''),
                'status_approval_wali'  => $r['status_approval_wali'] ?? 'Pending',
                'status_approval_admin' => $r['status_approval_admin'] ?? 'Pending',
                'status_approval_koor'  => $r['status_approval_koor'] ?? 'Pending',
                'catatan_wali'          => htmlspecialchars($r['catatan_wali'] ?? ''),
                'catatan_admin'         => htmlspecialchars($r['catatan_admin'] ?? ''),
                'catatan_koor'          => htmlspecialchars($r['catatan_koor'] ?? ''),
                'pembimbing_1'          => $r['pembimbing_1'] ?? '',
                'pembimbing_2'          => $r['pembimbing_2'] ?? '',
                'current_stage'         => $r['current_stage'] ?? 'Koordinator TA',
                'files' => array(
                    'ksm'        => array('title' => 'KSM (Kartu Studi Mahasiswa)', 'name' => $r['file_ksm'] ?? 'ksm_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_ksm'] ?? ''), 'status' => $r['status_ksm'] ?? 'Valid'),
                    'transkrip'  => array('title' => 'Transkrip Nilai Akademik', 'name' => $r['file_transkrip'] ?? 'transkrip_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_transkrip'] ?? ''), 'status' => $r['status_transkrip'] ?? 'Valid'),
                    'pernyataan' => array('title' => 'Surat Pernyataan Mahasiswa', 'name' => $r['file_pernyataan'] ?? 'pernyataan_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_pernyataan'] ?? ''), 'status' => $r['status_pernyataan'] ?? 'Valid'),
                    'bebas_lab'  => array('title' => 'Surat Bebas Laboratorium & Perpus', 'name' => $r['file_bebas_lab'] ?? 'bebas_lab_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_bebas_lab'] ?? ''), 'status' => $r['status_bebas_lab'] ?? 'Valid')
                )
            );
        }

        echo json_encode(array('status' => true, 'data' => $data));
    }

    // AJAX Endpoint: Batch Approval Koordinator TA (Multi-Select & Per-Mahasiswa Plotting)
    public function ajax_batch_approval() {
        header('Content-Type: application/json');

        $nims_raw      = $this->input->post('nims'); // JSON string atau array
        $status        = $this->input->post('status') ?: 'Approved'; // Default: Approved
        $catatan       = trim($this->input->post('catatan_koor') ?? '');
        $pembimbing_1  = $this->input->post('pembimbing_1');
        $pembimbing_2  = $this->input->post('pembimbing_2');
        $penguji_1     = $this->input->post('penguji_1');
        $penguji_2     = $this->input->post('penguji_2');
        $plottings_raw = $this->input->post('plottings');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);
        $plottings = is_array($plottings_raw) ? $plottings_raw : json_decode($plottings_raw ?? '[]', true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih setidaknya satu mahasiswa untuk diproses.'
            ));
            return;
        }

        $result = $this->KoordinatorTA_model->batch_approval_koor_ajax($nims, $status, $catatan, $pembimbing_1, $pembimbing_2, $penguji_1, $penguji_2, $plottings);
        echo json_encode($result);
    }

    // AJAX Endpoint: Realtime Live Status Polling
    public function ajax_realtime_status($nim) {
        header('Content-Type: application/json');

        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM tidak ditemukan'));
            return;
        }

        $detail = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);
        if (!$detail) {
            echo json_encode(array('status' => false, 'message' => 'Data tidak ditemukan'));
            return;
        }

        $stWali  = $detail['status_approval_wali'] ?? 'Pending';
        $stAdmin = $detail['status_approval_admin'] ?? 'Pending';
        $stKoor  = $detail['status_approval_koor'] ?? 'Pending';
        $stKk    = $detail['status_approval_kk'] ?? 'Pending';

        // Hitung stage aktif secara akurat
        $activeStageNum = 1;
        $tahapTerakhir = 'Dosen Wali';

        if ($stWali === 'Approved') {
            $activeStageNum = 2;
            $tahapTerakhir = 'Admin Layanan';
            if ($stAdmin === 'Approved') {
                $activeStageNum = 3;
                $tahapTerakhir = 'Koordinator TA';
                if ($stKoor === 'Approved') {
                    $activeStageNum = 4;
                    $tahapTerakhir = 'Ketua KK';
                    if ($stKk === 'Approved') {
                        $activeStageNum = 5;
                        $tahapTerakhir = 'Selesai (Disetujui Semua)';
                    }
                }
            }
        }

        echo json_encode(array(
            'status'                => true,
            'nim'                   => $nim,
            'status_approval_wali'  => $stWali,
            'status_approval_admin' => $stAdmin,
            'status_approval_koor'  => $stKoor,
            'status_approval_kk'    => $stKk,
            'catatan_wali'          => $detail['catatan_wali'] ?? '',
            'catatan_admin'         => $detail['catatan_admin'] ?? '',
            'catatan_koor'          => $detail['catatan_koor'] ?? '',
            'pembimbing_1'          => $detail['pembimbing_1'] ?? '',
            'pembimbing_2'          => $detail['pembimbing_2'] ?? '',
            'activeStageNum'        => $activeStageNum,
            'tahapTerakhir'         => $tahapTerakhir,
            'isWaliApproved'        => ($stWali === 'Approved'),
            'isLAAApproved'         => ($stAdmin === 'Approved'),
            'isKoorApproved'        => ($stKoor === 'Approved'),
            'isKkApproved'          => ($stKk === 'Approved')
        ));
    }

    // AJAX Endpoint: Realtime Dashboard Sync (Background Polling for Table & Stat Cards)
    public function ajax_realtime_dashboard() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_ta();

        $totalMhs = count($list);
        $siapDiplotCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;
        $kkApprovedCount = 0;

        foreach ($list as $row) {
            $stKoor = $row['status_approval_koor'] ?? 'Pending';
            $stWali = $row['status_approval_wali'] ?? 'Pending';
            $stAdmin = $row['status_approval_admin'] ?? 'Pending';
            $stKk = $row['status_approval_kk'] ?? 'Pending';

            $isWaliApproved = (strcasecmp($stWali, 'Approved') === 0);
            $isAdminApproved = (strcasecmp($stAdmin, 'Approved') === 0);

            if (strcasecmp($stKoor, 'Approved') === 0) {
                $approvedCount++;
            } else if (strcasecmp($stKoor, 'Rejected') === 0) {
                $rejectedCount++;
            } else {
                if ($isWaliApproved && $isAdminApproved) {
                    $siapDiplotCount++;
                }
            }

            if (strcasecmp($stKk, 'Approved') === 0) {
                $kkApprovedCount++;
            }
        }

        echo json_encode(array(
            'status' => true,
            'data'   => $list,
            'stats'  => array(
                'total'       => $totalMhs,
                'siap_diplot' => $siapDiplotCount,
                'pending'     => $siapDiplotCount,
                'approved'    => $approvedCount,
                'rejected'    => $rejectedCount,
                'kk_approved' => $kkApprovedCount
            )
        ));
    }

    // AJAX Endpoint: Update Dosen Penguji & Jadwal Sidang Preview 2 (Single Mahasiswa)
    public function ajax_update_preview2_penguji() {
        header('Content-Type: application/json');

        $nim          = $this->input->post('nim');
        $penguji_1    = $this->input->post('penguji_1');
        $penguji_2    = $this->input->post('penguji_2');
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        if (empty($nim) || empty($penguji_1) || empty($penguji_2)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'NIM, Dosen Penguji 1, dan Dosen Penguji 2 wajib dipilih.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->update_penguji_jadwal_preview2($nim, $penguji_1, $penguji_2, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Batch Update Dosen Penguji Preview 2 (Multi-Select & Per-Mahasiswa Plotting)
    public function ajax_batch_preview2_penguji() {
        header('Content-Type: application/json');

        $nims_raw      = $this->input->post('nims');
        $penguji_1     = $this->input->post('penguji_1');
        $penguji_2     = $this->input->post('penguji_2');
        $plottings_raw = $this->input->post('plottings');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw ?? '[]', true);
        $plottings = is_array($plottings_raw) ? $plottings_raw : json_decode($plottings_raw ?? '[]', true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih mahasiswa serta tentukan Dosen Penguji 1 & 2.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->batch_penguji_preview2_ajax($nims, $penguji_1, $penguji_2, null, null, null, null, $plottings);
        echo json_encode($res);
    }

    // AJAX Endpoint: Realtime Sync Data Preview 2
    public function ajax_realtime_preview2() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_preview2();

        $totalP2        = count($list);
        $pengujiLengkap = 0;
        $belumPenguji   = 0;

        foreach ($list as $row) {
            $hasP1 = !empty($row['penguji_1']);
            $hasP2 = !empty($row['penguji_2']);
            if ($hasP1 && $hasP2) {
                $pengujiLengkap++;
            } else {
                $belumPenguji++;
            }
        }

        $pctLengkap = $totalP2 > 0 ? round(($pengujiLengkap / $totalP2) * 100) : 0;
        $pctBelum   = $totalP2 > 0 ? round(($belumPenguji   / $totalP2) * 100) : 0;

        echo json_encode(array(
            'status' => true,
            'data'   => $list,
            'stats'  => array(
                'total'       => $totalP2,
                'terjadwal'   => $pengujiLengkap,
                'belum_set'   => $belumPenguji,
                'pct_lengkap' => $pctLengkap,
                'pct_belum'   => $pctBelum
            )
        ));
    }

    // AJAX Endpoint: Ambil Histori Log Perubahan TA (Pembimbing & Penguji)
    public function ajax_get_history_ta() {
        header('Content-Type: application/json');

        $kategori = $this->input->get('kategori') ?: $this->input->post('kategori');
        $nim      = $this->input->get('nim') ?: $this->input->post('nim');
        $limit    = (int)($this->input->get('limit') ?: $this->input->post('limit') ?: 100);

        $logs = $this->KoordinatorTA_model->get_history_ta($kategori, $nim, $limit);

        echo json_encode(array(
            'status'   => true,
            'kategori' => $kategori,
            'data'     => $logs
        ));
    }

    // AJAX Endpoint: Ambil Histori Log Perubahan Penugasan Dosen Penguji (Wrapper)
    public function ajax_get_history_penguji() {
        header('Content-Type: application/json');

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        $logs = $this->KoordinatorTA_model->get_history_penguji($nim);

        echo json_encode(array(
            'status' => true,
            'data'   => $logs
        ));
    }

    // =========================================================
    // TAHAP 3: AJAX PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN
    // =========================================================

    // AJAX Endpoint: Update Jadwal Sidang TA Single Mahasiswa
    public function ajax_update_jadwal_sidang() {
        header('Content-Type: application/json');

        $nim          = $this->input->post('nim');
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        if (empty($nim) || empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'NIM, Tanggal Sidang, Jam Mulai, dan Ruangan Sidang wajib diisi.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->update_jadwal_sidang_ajax($nim, $tgl_sidang, $jam_mulai, $ruangan, '', $jam_selesai);
        echo json_encode($res);
    }

    // AJAX Endpoint: Batch Update Jadwal Sidang TA (Per-Mahasiswa Schedules)
    public function ajax_batch_jadwal_sidang() {
        header('Content-Type: application/json');

        $schedules_raw = $this->input->post('schedules');
        $schedules = is_array($schedules_raw) ? $schedules_raw : json_decode($schedules_raw ?? '[]', true);

        // Fallback jika dikirim nims + global fields
        $nims_raw = $this->input->post('nims');
        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw ?? '[]', true);
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        if (empty($schedules) && !empty($nims)) {
            $schedules = array();
            foreach ($nims as $n) {
                $schedules[] = array(
                    'nim'                => $n,
                    'tgl_sidang'         => $tgl_sidang,
                    'jam_mulai_sidang'   => $jam_mulai,
                    'jam_selesai_sidang' => $jam_selesai,
                    'ruangan_sidang'     => $ruangan
                );
            }
        }

        if (empty($schedules) || !is_array($schedules)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih mahasiswa dan atur jadwal sidang untuk masing-masing mahasiswa.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->batch_jadwal_sidang_per_mhs_ajax($schedules);
        echo json_encode($res);
    }

    // AJAX Endpoint: Realtime Sync Data Sidang TA
    public function ajax_realtime_sidang() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_sidang();
        $ruangan = $this->KoordinatorTA_model->get_available_ruangan();

        $totalSidang = count($list);
        $terjadwalCount = 0;
        $belumSetCount = 0;
        $sudahDinilaiCount = 0;

        foreach ($list as $row) {
            $st = $row['status_sidang'] ?? 'Belum Dijadwalkan';
            if ($st === 'Terjadwal') $terjadwalCount++;
            else $belumSetCount++;

            $nilai = $row['nilai_akhir_sidang'] ?? null;
            if (!empty($nilai) || !empty($row['grade_sidang']) || ($row['status_kelulusan_sidang'] ?? '') === 'Lulus') {
                $sudahDinilaiCount++;
            }
        }

        echo json_encode(array(
            'status'  => true,
            'data'    => $list,
            'ruangan' => $ruangan,
            'stats'   => array(
                'total'         => $totalSidang,
                'terjadwal'     => $terjadwalCount,
                'belum_set'     => $belumSetCount,
                'sudah_dinilai' => $sudahDinilaiCount,
                'ruangan_cnt'   => count($ruangan)
            )
        ));
    }

    // AJAX Endpoint: Tambah Ruangan Sidang Baru Dinamis
    public function ajax_tambah_ruangan() {
        header('Content-Type: application/json');

        $kode_ruangan = $this->input->post('kode_ruangan');
        $nama_ruangan = $this->input->post('nama_ruangan');
        $lokasi       = $this->input->post('lokasi');
        $kapasitas    = $this->input->post('kapasitas');

        if (empty($kode_ruangan) || empty($nama_ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Kode Ruangan dan Nama Ruangan wajib diisi.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->tambah_ruangan_ajax($kode_ruangan, $nama_ruangan, $lokasi, $kapasitas);
        echo json_encode($res);
    }

    // AJAX Endpoint: Hapus Ruangan Sidang Dinamis
    public function ajax_hapus_ruangan() {
        header('Content-Type: application/json');

        $id_ruangan = $this->input->post('id_ruangan');
        if (empty($id_ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'ID Ruangan tidak ditemukan.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->hapus_ruangan_ajax($id_ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Ambil Daftar Ruangan Aktif untuk Auto-Refresh Dropdown
    public function ajax_get_ruangan_list() {
        header('Content-Type: application/json');

        $ruangan = $this->KoordinatorTA_model->get_available_ruangan();
        echo json_encode(array(
            'status' => true,
            'data'   => $ruangan
        ));
    }

    // AJAX Endpoint: Simpan Penilaian Akhir Sidang TA
    public function ajax_simpan_penilaian_sidang() {
        header('Content-Type: application/json');

        $nim              = $this->input->post('nim');
        $prodi            = $this->input->post('prodi');
        $peminatan        = $this->input->post('peminatan');
        $nilai_akhir      = $this->input->post('nilai_akhir');
        $grade            = $this->input->post('grade');
        $status_kelulusan = $this->input->post('status_kelulusan');
        $detail_penilaian = $this->input->post('detail_penilaian');
        $catatan          = $this->input->post('catatan');
        $status_publish   = $this->input->post('status_publish') ?: 'Draft';
        $tgl_publish      = $this->input->post('tgl_publish') ?: null;
        $tahun_akademik   = $this->input->post('tahun_akademik') ?: null;

        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM mahasiswa wajib disertakan.'));
            return;
        }

        if (is_string($detail_penilaian)) {
            $decoded = json_decode($detail_penilaian, true);
            if (is_array($decoded)) {
                $detail_penilaian = $decoded;
            }
        }

        $res = $this->KoordinatorTA_model->simpan_penilaian_sidang_ajax(
            $nim,
            $prodi,
            $peminatan,
            $nilai_akhir,
            $grade,
            $status_kelulusan,
            $detail_penilaian,
            $catatan,
            $status_publish,
            $tgl_publish,
            $tahun_akademik
        );

        echo json_encode($res);
    }

    // AJAX Endpoint: Publish / Republish / Set Jadwal Publikasi Nilai Sidang
    public function ajax_publish_penilaian_sidang() {
        header('Content-Type: application/json');

        $nim            = $this->input->post('nim');
        $status_publish = $this->input->post('status_publish') ?: 'Published';
        $tgl_publish    = $this->input->post('tgl_publish') ?: null;
        $catatan        = $this->input->post('catatan') ?: '';

        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM mahasiswa wajib diisi.'));
            return;
        }

        $res = $this->KoordinatorTA_model->publish_penilaian_sidang_ajax($nim, $status_publish, $tgl_publish, $catatan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Batch / Publikasi Nilai Massal Mahasiswa Terpilih
    public function ajax_batch_publish_nilai() {
        header('Content-Type: application/json');

        $nims_raw       = $this->input->post('nims');
        $status_publish = $this->input->post('status_publish') ?: 'Published';
        $tgl_publish    = $this->input->post('tgl_publish') ?: null;
        $catatan        = $this->input->post('catatan') ?: '';

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array('status' => false, 'message' => 'Pilih setidaknya satu mahasiswa untuk dipublikasikan nilainya.'));
            return;
        }

        $res = $this->KoordinatorTA_model->batch_publish_nilai_ajax($nims, $status_publish, $tgl_publish, $catatan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Ambil Riwayat Log Versi Penilaian Mahasiswa
    public function ajax_get_history_penilaian_sidang() {
        header('Content-Type: application/json');

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM mahasiswa wajib diisi.'));
            return;
        }

        $history = $this->KoordinatorTA_model->get_history_penilaian_sidang($nim);
        echo json_encode(array(
            'status' => true,
            'data'   => $history
        ));
    }

    // AJAX Endpoint: Ambil Detail Penilaian Sidang Mahasiswa
    public function ajax_get_detail_penilaian_sidang() {
        header('Content-Type: application/json');

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM mahasiswa wajib diisi.'));
            return;
        }

        $detail = $this->KoordinatorTA_model->get_detail_penilaian_sidang($nim);
        if (!$detail) {
            echo json_encode(array('status' => false, 'message' => 'Data mahasiswa tidak ditemukan.'));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'data'   => $detail
        ));
    }

    // AJAX Endpoint: Ambil Semua Master Rubrik Dinamis
    public function ajax_get_all_master_rubrik() {
        header('Content-Type: application/json');
        $rubriks = $this->KoordinatorTA_model->get_all_master_rubrik();
        echo json_encode(array(
            'status' => true,
            'data'   => $rubriks
        ));
    }

    // AJAX Endpoint: Simpan / Update Master Rubrik Dinamis
    public function ajax_simpan_master_rubrik() {
        header('Content-Type: application/json');

        $prodi        = $this->input->post('prodi');
        $peminatan    = $this->input->post('peminatan');
        $judul_rubrik = $this->input->post('judul_rubrik');
        $kriteria_raw = $this->input->post('kriteria');

        $kriteria = is_string($kriteria_raw) ? json_decode($kriteria_raw, true) : $kriteria_raw;

        $res = $this->KoordinatorTA_model->simpan_master_rubrik($prodi, $peminatan, $judul_rubrik, $kriteria);
        echo json_encode($res);
    }

    // AJAX Endpoint: Terapkan Rubrik Secara Massal
    public function ajax_terapkan_rubrik_massal() {
        header('Content-Type: application/json');

        $prodi     = $this->input->post('prodi');
        $peminatan = $this->input->post('peminatan');
        $nim_list  = $this->input->post('nim_list');

        if (is_string($nim_list)) {
            $decoded = json_decode($nim_list, true);
            if (is_array($decoded)) {
                $nim_list = $decoded;
            } else if (!empty($nim_list)) {
                $nim_list = explode(',', $nim_list);
            }
        }

        $res = $this->KoordinatorTA_model->terapkan_rubrik_massal($prodi, $peminatan, $nim_list);
        echo json_encode($res);
    }

    // AJAX Endpoint: Reset ke Master Rubrik Default
    public function ajax_reset_default_rubrik() {
        header('Content-Type: application/json');
        $this->KoordinatorTA_model->_seed_default_master_rubrik();
        $rubriks = $this->KoordinatorTA_model->get_all_master_rubrik();
        echo json_encode(array(
            'status'  => true,
            'message' => 'Seluruh master rubrik penilaian prodi & peminatan berhasil direset ke standar kurikulum default!',
            'data'    => $rubriks
        ));
    }

    // =========================================================================
    // MODUL CHAT HELP: KOORDINATOR TA -> TRIO (LABORAN / KA. UR / ADMIN LAYANAN)
    // =========================================================================

    /**
     * Halaman Utama Bantuan & Live Chat Koordinator TA
     */
    public function help() {
        $this->load->model('Help_chat_model');
        $nip_koor = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');
        $user_id = $this->session->userdata('user_id') ?: 6;

        $data['title'] = 'Bantuan & Live Chat - Koordinator TA';
        $data['nip_koor'] = $nip_koor;
        $data['stats'] = $this->Help_chat_model->get_stats(null, $user_id);
        $data['conversations'] = $this->Help_chat_model->get_conversations('all', '', null, $user_id);

        $this->load->view('koordinator_ta/help', $data);
    }

    /**
     * AJAX: Ambil daftar percakapan Koordinator TA dengan filter target, status, & pencarian
     */
    public function help_get_conversations_ajax() {
        header('Content-Type: application/json');
        $this->load->model('Help_chat_model');

        $user_id = $this->session->userdata('user_id') ?: 6;
        $target_role = $this->input->get('target', true) ?: 'all';
        $status = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');

        $conversations = $this->Help_chat_model->get_conversations($status, $search, $target_role, $user_id);
        $stats = $this->Help_chat_model->get_stats($target_role, $user_id);

        $formatted = [];
        foreach ($conversations as $c) {
            $formatted[] = [
                'id'                => (int)$c->id,
                'target_role'       => htmlspecialchars($c->target_role ?? 'laboran'),
                'target_role_label' => $this->_get_target_role_label($c->target_role ?? 'laboran'),
                'topik'             => htmlspecialchars($c->topik ?? 'Bantuan Umum'),
                'status'            => $c->status,
                'last_message'      => htmlspecialchars($c->last_message ?? ''),
                'last_message_time' => $this->_format_help_time_ago($c->last_message_time ?? $c->created_at),
                'unread_user'       => (int)$c->unread_user,
                'unread_laboran'    => (int)$c->unread_laboran,
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
     * AJAX: Dapatkan atau buat channel chat langsung dengan salah satu Trio (Laboran / Ka. Ur / Admin Layanan)
     */
    public function help_get_channel_ajax() {
        header('Content-Type: application/json');
        $this->load->model('Help_chat_model');

        $target_role = strtolower(trim($this->input->get('target', true) ?? 'laboran'));
        if (!in_array($target_role, ['laboran', 'kaur', 'admin_layanan'])) {
            $target_role = 'laboran';
        }

        $userId    = $this->session->userdata('user_id') ?: 6;
        $userNama  = $this->session->userdata('name') ?: 'Dr. Koordinator TA, M.T.';
        $userEmail = $this->session->userdata('email') ?: 'koordinator.ta@telkomuniversity.ac.id';
        $userNip   = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');

        $conv = $this->Help_chat_model->get_or_create_channel($userId, $userNama, $userEmail, 'Koordinator TA', $userNip, $target_role);

        // Mark as read
        $this->Help_chat_model->mark_as_read_by_user($conv->id);

        $messages = $this->Help_chat_model->get_messages($conv->id);
        $formattedMessages = [];

        foreach ($messages as $m) {
            $formattedMessages[] = [
                'id'          => (int)$m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => htmlspecialchars($m->sender_name),
                'sender_role' => $m->sender_role,
                'is_me'       => in_array($m->sender_role, ['koordinator_ta', 'user']),
                'message'     => nl2br(htmlspecialchars($m->message)),
                'attachment'  => $m->attachment ? base_url('uploads/help_attachments/' . $m->attachment) : null,
                'is_read'     => (int)$m->is_read,
                'time'        => date('H:i', strtotime($m->created_at)),
                'date_full'   => date('d M Y, H:i', strtotime($m->created_at)),
                'created_at'  => $m->created_at
            ];
        }

        // Get unread counts for all 3 channels
        $statsLaboran = $this->Help_chat_model->get_stats('laboran', $userId);
        $statsKaur = $this->Help_chat_model->get_stats('kaur', $userId);
        $statsAdmin = $this->Help_chat_model->get_stats('admin_layanan', $userId);

        echo json_encode([
            'status'       => 'success',
            'conversation' => [
                'id'                => (int)$conv->id,
                'target_role'       => htmlspecialchars($conv->target_role ?? $target_role),
                'target_role_label' => $this->_get_target_role_label($conv->target_role ?? $target_role),
                'topik'             => htmlspecialchars($conv->topik),
                'status'            => $conv->status,
                'last_message_time' => $this->_format_help_time_ago($conv->last_message_time ?? $conv->created_at),
                'created_at'        => date('d M Y, H:i', strtotime($conv->created_at))
            ],
            'messages'     => $formattedMessages,
            'unreads'      => [
                'laboran'       => $statsLaboran['unread'] ?? 0,
                'kaur'          => $statsKaur['unread'] ?? 0,
                'admin_layanan' => $statsAdmin['unread'] ?? 0
            ]
        ]);
        exit;
    }

    /**
     * AJAX: Ambil detail pesan percakapan & auto mark read oleh Koordinator TA
     */
    public function help_get_messages_ajax($conversation_id) {
        header('Content-Type: application/json');
        $this->load->model('Help_chat_model');

        $conversation = $this->Help_chat_model->get_conversation_by_id($conversation_id);
        if (!$conversation) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Percakapan tidak ditemukan.'
            ]);
            exit;
        }

        // Tandai pesan sudah dibaca oleh user/Koor
        $this->Help_chat_model->mark_as_read_by_user($conversation_id);

        $messages = $this->Help_chat_model->get_messages($conversation_id);
        $formattedMessages = [];

        foreach ($messages as $m) {
            $formattedMessages[] = [
                'id'          => (int)$m->id,
                'sender_id'   => $m->sender_id,
                'sender_name' => htmlspecialchars($m->sender_name),
                'sender_role' => $m->sender_role,
                'is_me'       => in_array($m->sender_role, ['koordinator_ta', 'user']),
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
                'target_role'       => htmlspecialchars($conversation->target_role ?? 'laboran'),
                'target_role_label' => $this->_get_target_role_label($conversation->target_role ?? 'laboran'),
                'topik'             => htmlspecialchars($conversation->topik),
                'status'            => $conversation->status,
                'user_nama'         => htmlspecialchars($conversation->user_nama),
                'last_message_time' => $this->_format_help_time_ago($conversation->last_message_time ?? $conversation->created_at),
                'created_at'        => date('d M Y, H:i', strtotime($conversation->created_at))
            ],
            'messages'     => $formattedMessages
        ]);
        exit;
    }

    /**
     * AJAX: Buat tiket bantuan baru dari Koordinator TA ke salah satu Trio
     */
    public function help_create_chat_ajax() {
        header('Content-Type: application/json');
        $this->load->model('Help_chat_model');

        $target_role = strtolower(trim($this->input->post('target_role', true) ?? 'laboran'));
        $topik       = trim($this->input->post('topik', true) ?? '');
        $message     = trim($this->input->post('message', true) ?? '');

        if (!in_array($target_role, ['laboran', 'kaur', 'admin_layanan'])) {
            $target_role = 'laboran';
        }

        if (empty($topik) || empty($message)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Topik dan pesan bantuan wajib diisi.'
            ]);
            exit;
        }

        $userId    = $this->session->userdata('user_id') ?: 6;
        $userNama  = $this->session->userdata('name') ?: 'Dr. Koordinator TA, M.T.';
        $userEmail = $this->session->userdata('email') ?: 'koordinator.ta@telkomuniversity.ac.id';
        $userNip   = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');

        $conv_id = $this->Help_chat_model->create_conversation([
            'user_id'      => $userId,
            'user_nama'    => $userNama,
            'user_email'   => $userEmail,
            'user_role'    => 'Koordinator TA',
            'user_nim_nip' => $userNip,
            'target_role'  => $target_role,
            'sender_role'  => 'koordinator_ta',
            'topik'        => $topik,
            'message'      => $message
        ]);

        if ($conv_id) {
            echo json_encode([
                'status'          => 'success',
                'conversation_id' => (int)$conv_id,
                'message'         => 'Permintaan bantuan berhasil dikirim ke ' . $this->_get_target_role_label($target_role) . '.'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal membuat percakapan bantuan.'
            ]);
        }
        exit;
    }

    /**
     * AJAX: Kirim pesan balasan dalam percakapan
     */
    public function help_send_message_ajax() {
        header('Content-Type: application/json');
        $this->load->model('Help_chat_model');

        $conversation_id = $this->input->post('conversation_id', true);
        $message         = trim($this->input->post('message', true) ?? '');

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

        $userId   = $this->session->userdata('user_id') ?: 6;
        $userNama = $this->session->userdata('name') ?: 'Dr. Koordinator TA, M.T.';

        $msgId = $this->Help_chat_model->send_message(
            $conversation_id,
            $userId,
            $userNama,
            'koordinator_ta',
            $message
        );

        if ($msgId) {
            echo json_encode([
                'status'  => 'success',
                'message' => 'Pesan terkirim.',
                'data'    => [
                    'id'          => $msgId,
                    'sender_name' => $userNama,
                    'sender_role' => 'koordinator_ta',
                    'is_me'       => true,
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
     * AJAX: Toggle status Open / Resolved
     */
    public function help_toggle_status_ajax() {
        header('Content-Type: application/json');
        $this->load->model('Help_chat_model');

        $conversation_id = $this->input->post('conversation_id', true);
        $status          = $this->input->post('status', true);

        if (empty($conversation_id) || !in_array($status, ['open', 'resolved'])) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Parameter status tidak valid.'
            ]);
            exit;
        }

        $userId   = $this->session->userdata('user_id') ?: 6;
        $userNama = $this->session->userdata('name') ?: 'Dr. Koordinator TA, M.T.';

        $updated = $this->Help_chat_model->update_status($conversation_id, $status);

        if ($updated) {
            $sysMsg = ($status === 'resolved') 
                ? '[Sistem] Tiket bantuan ini telah ditandai Selesai oleh Koordinator TA.'
                : '[Sistem] Tiket bantuan telah dibuka kembali oleh Koordinator TA.';

            $this->Help_chat_model->send_message(
                $conversation_id,
                $userId,
                $userNama,
                'koordinator_ta',
                $sysMsg
            );

            echo json_encode([
                'status'     => 'success',
                'new_status' => $status,
                'message'    => 'Status tiket bantuan berhasil diperbarui.'
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status tiket.'
            ]);
        }
        exit;
    }

    /**
     * AJAX: Daftar preset topik bantuan untuk tiap target role
     */
    public function help_quick_topics_ajax() {
        header('Content-Type: application/json');
        $target = $this->input->get('target', true) ?: 'laboran';

        $topics = [
            'laboran' => [
                'Kebutuhan Alat & Sensor untuk Pengujian TA',
                'Pengecekan Kesiapan Ruang Lab untuk Demo Sidang',
                'Permohonan Akses Komputer High-End / Server Lab',
                'Laporan Kendala Jaringan LAN / Hardware di Ruang Uji',
                'Konfirmasi Jadwal Penggunaan Lab Riset TA'
            ],
            'kaur' => [
                'Validasi & Rekomendasi Jadwal Sidang Gelombang Baru',
                'Pengajuan Izin Khusus Penggunaan Fasilitas Lab',
                'Koordinasi Kebijakan Ruang Sidang & Penilaian',
                'Persetujuan Surat Rekomendasi Bebas Lab Mahasiswa',
                'Permintaan Perangkat Cadangan untuk Sidang Hybrid'
            ],
            'admin_layanan' => [
                'Verifikasi Berkas Pendaftaran TA Mahasiswa',
                'Validasi SK Dosen Pembimbing & Penguji',
                'Cek Kelengkapan Bebas Lab & Persyaratan Yudisium',
                'Penerbitan Berita Acara & Nilai Akhir Sidang',
                'Sinkronisasi Data Mahasiswa Lulus TA'
            ]
        ];

        echo json_encode([
            'status' => 'success',
            'data'   => $topics[$target] ?? $topics['laboran']
        ]);
        exit;
    }

    /**
     * Helper Label Target Role
     */
    private function _get_target_role_label($role) {
        switch ($role) {
            case 'laboran': return 'Laboran (Lab & Alat)';
            case 'kaur': return 'Ka. Ur (Kepala Urusan)';
            case 'admin_layanan': return 'Admin Layanan (LAA)';
            default: return 'Staff Layanan';
        }
    }

    /**
     * Helper pemformat waktu relatif
     */
    private function _format_help_time_ago($datetime) {
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


