<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dosen_bimbingan extends CI_Controller {

    /** Peta role UI => posisi model (1=P1, 2=P2, 3=U1, 4=U2) */
    private $role_map = ['p1' => 1, 'p2' => 2, 'u1' => 3, 'u2' => 4];

    /** Status review yang boleh disimpan oleh P1 */
    private $allowed_status = ['Approved', 'Revision', 'Pending'];

    /** Tahap yang boleh di-query dari dashboard dosen */
    private $allowed_tahap = ['Preview 1', 'Preview 2', 'Preview 3', 'Sidang'];

    public function __construct() {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->model('Rekomendasi_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));

        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    // 'status' ditambahkan agar konsisten dengan yang dibaca JS (res.status)
                    ->set_output(json_encode([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Sesi berakhir, silakan login kembali.'
                    ]));
                exit;
            }
            redirect('login');
            return;
        }
    }

    // =================================================================
    // HELPERS
    // =================================================================

    private function _get_current_nim() {
        return $this->session->userdata('nim')
            ?: ($this->session->userdata('nidn_nim')
            ?: ($this->session->userdata('username') ?: ''));
    }

    /**
     * Hanya Admin (1) dan Dosen (3) yang boleh review preview TA.
     * Role lain (4=Mahasiswa, 5=Admin LAA, 6=Koordinator, dst) DILARANG.
     */
    private function _is_authorized_reviewer() {
        $role_id = (int) $this->session->userdata('role_id');
        return in_array($role_id, [1, 3], true);
    }

    private function _is_admin() {
        return (int) $this->session->userdata('role_id') === 1;
    }

    private function _do_upload($field_name, $config) {
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        return null;
    }

    /** Pastikan posisi selalu 1..4, selain itu pakai default. */
    private function _clean_posisi($value, $default = 1) {
        $p = (int) $value;
        return ($p >= 1 && $p <= 4) ? $p : $default;
    }

    /** Ambil posisi dari query string (model_posisi diprioritaskan, lalu posisi). */
    private function _posisi_from_get() {
        $raw = $this->input->get('model_posisi') ?: $this->input->get('posisi');
        return $this->_clean_posisi($raw, 1);
    }

    /** Bersihkan HTML komentar (TinyMCE) agar aman ditampilkan lewat innerHTML. */
    private function _clean_comment($html) {
        $html = (string) $html;
        return $this->security->xss_clean($html);
    }

    /**
     * Ambil preview + cek apakah dosen yang login berhak me-review-nya pada posisi tsb.
     * Return array preview jika boleh, false jika tidak.
     * Admin (role_id 1) dibebaskan dari pengecekan penugasan.
     */
    private function _get_reviewable_preview($id_preview, $posisi) {
        if (!$this->_is_authorized_reviewer()) return false;

        $posisi = (int) $posisi;
        if ($posisi < 1 || $posisi > 4 || empty($id_preview)) return false;

        $preview = $this->Mahasiswa_model->get_preview_by_id($id_preview);
        if (empty($preview)) return false;

        if ($this->_is_admin()) return $preview;

        $dosen_id = $this->session->userdata('user_id');
        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
        $nims     = array_column($students, 'nim');

        return in_array($preview['nim'], $nims, true) ? $preview : false;
    }

    // =========================================================
    // DOMAIN 1: BIMBINGAN & PREVIEW TA (MAHASISWA)
    // =========================================================

    public function index() {
        $this->bimbingan();
    }

    public function bimbingan() {
        $nim = $this->_get_current_nim();

        $data['title']       = 'Bimbingan & Evaluasi Preview TA';
        $data['mahasiswa']   = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $pembimbing_penguji = $this->Mahasiswa_model->get_pembimbing_penguji($nim);

        $data['riwayat_preview1'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 1');
        $data['riwayat_preview2'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 2');
        $data['riwayat_preview3'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 3');
        $data['riwayat_sidang']   = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Sidang');

        $data['upload_count_p1']     = count($data['riwayat_preview1']);
        $data['upload_count_p2']     = count($data['riwayat_preview2']);
        $data['upload_count_p3']     = count($data['riwayat_preview3']);
        $data['upload_count_sidang'] = count($data['riwayat_sidang']);

        $data['latest_p1']     = $data['riwayat_preview1'][0] ?? null;
        $data['latest_p2']     = $data['riwayat_preview2'][0] ?? null;
        $data['latest_p3']     = $data['riwayat_preview3'][0] ?? null;
        $data['latest_sidang'] = $data['riwayat_sidang'][0] ?? null;

        $data['pembimbing_1'] = !empty($pembimbing_penguji['pembimbing_1']) ? $pembimbing_penguji['pembimbing_1'] : '';
        $data['pembimbing_2'] = !empty($pembimbing_penguji['pembimbing_2']) ? $pembimbing_penguji['pembimbing_2'] : '';
        $data['penguji_ta']   = !empty($pembimbing_penguji['penguji_1'])    ? $pembimbing_penguji['penguji_1']    : '';
        $data['penguji_1']    = !empty($pembimbing_penguji['penguji_1'])    ? $pembimbing_penguji['penguji_1']    : '';
        $data['penguji_2']    = !empty($pembimbing_penguji['penguji_2'])    ? $pembimbing_penguji['penguji_2']    : '';

        $detail_penilaian = null;
        if (!empty($data['pendaftaran']['detail_penilaian_sidang'])) {
            $raw_detail = $data['pendaftaran']['detail_penilaian_sidang'];
            if (is_array($raw_detail)) {
                $detail_penilaian = $raw_detail;
            } elseif (is_string($raw_detail)) {
                $dec = json_decode($raw_detail, true);
                if (is_array($dec)) $detail_penilaian = $dec;
            }
        }
        $data['detail_penilaian']       = $detail_penilaian;
        $data['is_pembimbing_assigned'] = (!empty($data['pembimbing_1']) && !empty($data['pembimbing_2']));

        $this->load->view('mahasiswa/bimbingan_preview1', $data);
    }

    public function preview1() {
        $this->bimbingan();
    }

    public function upload_preview() {
        $nim = $this->_get_current_nim();
        $tahap = $this->input->post('tahap_preview') ?: 'Preview 1';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx|zip';
        $config['max_size']      = 10240;
        $clean_tahap = str_replace(' ', '', strtoupper($tahap));
        $config['file_name']     = $clean_tahap . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_draft')) {
            $error_msg = $this->upload->display_errors('', '');
            $this->session->set_flashdata('error', 'Gagal mengunggah berkas: ' . $error_msg);
        } else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
            $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

            $data_insert = [
                'nim'               => $nim,
                'tahap_preview'     => $tahap,
                'file_draft'        => $file_name,
                'catatan_mahasiswa' => $catatan,
                'status_pembimbing' => 'Pending',
                'created_at'        => date('Y-m-d H:i:s')
            ];

            $this->Mahasiswa_model->save_upload_preview($data_insert);

            if (!empty($_FILES['rekomen_eviden']['name'])) {
                $ev_config['upload_path']   = './uploads/persyaratan_ta/';
                $ev_config['allowed_types'] = 'pdf|docx|doc';
                $ev_config['max_size']      = 10240;
                $ev_config['file_name']     = 'EVIDEN_' . $nim . '_' . time();

                $this->upload->initialize($ev_config);
                if ($this->upload->do_upload('rekomen_eviden')) {
                    $ev_data = $this->upload->data();
                    $ev_file = $ev_data['file_name'];
                    $rek_title = trim($this->input->post('rekomen_title') ?? 'Jalur Ekuivalensi Prestasi');

                    $this->Rekomendasi_model->save_submission([
                        'nim'                 => $nim,
                        'recommendation_type' => 'non_sidang',
                        'jalur_title'         => $rek_title,
                        'form_data_json'      => json_encode(['eviden' => $ev_file, 'uploaded_by' => 'mahasiswa']),
                        'catatan_dosen'       => 'Mahasiswa mengajukan kelulusan jalur Non-Sidang dengan dokumen bukti pendukung.',
                        'status'              => 'pending'
                    ]);
                }
            }

            $this->session->set_flashdata('success', "Draft Berkas {$tahap} berhasil diunggah! Menunggu peninjauan dari Dosen Penilai.");
        }

        redirect('mahasiswa/bimbingan');
    }

    public function upload_preview1() {
        $this->upload_preview();
    }

    public function upload_preview3() {
        $this->upload_preview3_ajax();
    }

    public function upload_preview3_ajax() {
        header('Content-Type: application/json');

        $nim = $this->_get_current_nim();
        $tahap = 'Preview 3';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx',
            'max_size'      => 10240,
        ];

        $this->load->library('upload');

        $config['file_name'] = 'PREVIEW3_SITASI_' . $nim . '_' . time();
        $file_sitasi = $this->_do_upload('file_sitasi', $config);

        $config['file_name'] = 'PREVIEW3_BIMBINGAN_' . $nim . '_' . time();
        $file_bimbingan = $this->_do_upload('file_bimbingan', $config);

        $config['file_name'] = 'PREVIEW3_PERSYARATAN_' . $nim . '_' . time();
        $file_persyaratan = $this->_do_upload('file_persyaratan', $config);

        if (!$file_sitasi || !$file_bimbingan || !$file_persyaratan) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal upload. Pastikan ketiga file terisi dan format PDF/DOC/DOCX maksimal 10MB. ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

        $data_insert = [
            'nim'                => $nim,
            'tahap_preview'      => $tahap,
            'file_draft'         => $file_sitasi,
            'file_sitasi'        => $file_sitasi,
            'file_bimbingan'     => $file_bimbingan,
            'file_persyaratan'   => $file_persyaratan,
            'catatan_mahasiswa'  => $catatan,
            'status_pembimbing'  => 'Pending',
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        echo json_encode([
            'status'  => true,
            'message' => 'Berkas Preview 3 berhasil diunggah. Menunggu review Pembimbing 1.'
        ]);
    }

    public function upload_sidang() {
        $this->upload_sidang_ajax();
    }

    public function upload_sidang_ajax() {
        header('Content-Type: application/json');

        $nim = $this->_get_current_nim();

        $upload_dir = './uploads/sidang/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx',
            'max_size'      => 10240,
            'file_name'     => 'SIDANG_' . $nim . '_' . time(),
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_sidang')) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal upload berkas sidang: ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $upload_data = $this->upload->data();
        $file_sidang = $upload_data['file_name'];
        $catatan = trim($this->input->post('catatan_sidang') ?? '');

        $data_insert = [
            'nim'               => $nim,
            'tahap_preview'     => 'Sidang',
            'file_draft'        => $file_sidang,
            'file_sidang'       => $file_sidang,
            'catatan_mahasiswa' => $catatan,
            'status_pembimbing' => 'Pending',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        echo json_encode([
            'status'  => true,
            'message' => 'Berkas Sidang Akhir berhasil diunggah.'
        ]);
    }

    // =========================================================
    // REVIEW PREVIEW (DOSEN)
    // =========================================================

    /**
     * Membangun data update + pesan sukses berdasarkan posisi.
     * Return [array $data, string $message] atau null jika posisi/status tidak valid.
     */
    private function _build_review_update($posisi, $catatan, $status) {
        $catatan = $this->_clean_comment($catatan);

        switch ((int) $posisi) {
            case 1:
                $data = ['catatan_pembimbing' => $catatan];
                // Status hanya diubah jika dikirim & valid. Tidak menimpa dengan null/kosong.
                if ($status !== null && $status !== '') {
                    if (!in_array($status, $this->allowed_status, true)) return null;
                    $data['status_pembimbing'] = $status;
                }
                return [$data, 'Review Pembimbing 1 berhasil disimpan.'];
            case 2:
                return [['catatan_pembimbing_2' => $catatan], 'Catatan Pembimbing 2 berhasil disimpan.'];
            case 3:
                return [['catatan_penguji_1' => $catatan], 'Catatan Penguji 1 berhasil disimpan.'];
            case 4:
                return [['catatan_penguji_2' => $catatan], 'Catatan Penguji 2 berhasil disimpan.'];
        }
        return null;
    }

    /** Versi non-AJAX (form submit biasa). */
    public function review_preview() {
        if (!$this->_is_authorized_reviewer()) {
            redirect('mahasiswa/bimbingan');
            return;
        }

        $id      = $this->input->post('id_preview');
        $posisi  = $this->_clean_posisi($this->input->post('posisi'), 0);
        $catatan = $this->input->post('catatan_pembimbing');
        $status  = $this->input->post('status_pembimbing');

        $role_back = array_search($posisi, $this->role_map, true) ?: 'p1';

        if (!$this->_get_reviewable_preview($id, $posisi)) {
            $this->session->set_flashdata('error', 'Anda tidak berhak me-review berkas ini.');
            redirect('mahasiswa/dosen_bimbingan?role=' . $role_back);
            return;
        }

        $built = $this->_build_review_update($posisi, $catatan, $status);
        if ($built === null) {
            $this->session->set_flashdata('error', 'Data review tidak valid.');
            redirect('mahasiswa/dosen_bimbingan?role=' . $role_back);
            return;
        }

        list($data, $message) = $built;
        $this->Mahasiswa_model->update_review_preview($id, $data);
        $this->session->set_flashdata('success', $message);

        // Redirect ke dashboard dosen (sebelumnya salah ke halaman mahasiswa)
        redirect('mahasiswa/dosen_bimbingan?role=' . $role_back);
    }

    public function review_preview_ajax() {
        header('Content-Type: application/json');
        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id      = $this->input->post('id_preview');
        $posisi  = $this->_clean_posisi($this->input->post('posisi'), 0);
        $catatan = $this->input->post('catatan_pembimbing');
        $status  = $this->input->post('status_pembimbing');

        if (empty($id)) {
            echo json_encode(['status' => false, 'message' => 'ID preview tidak valid']);
            return;
        }
        if ($posisi === 0) {
            echo json_encode(['status' => false, 'message' => 'Posisi tidak valid']);
            return;
        }

        // Validasi: dosen ini memang ditugaskan pada mahasiswa & posisi tersebut
        if (!$this->_get_reviewable_preview($id, $posisi)) {
            echo json_encode(['status' => false, 'message' => 'Anda tidak berhak me-review berkas ini.']);
            return;
        }

        $built = $this->_build_review_update($posisi, $catatan, $status);
        if ($built === null) {
            echo json_encode(['status' => false, 'message' => 'Status atau posisi tidak valid']);
            return;
        }

        list($data, $message) = $built;
        $this->Mahasiswa_model->update_review_preview($id, $data);

        echo json_encode(['status' => true, 'message' => $message]);
    }

    public function review_preview_batch_ajax() {
        header('Content-Type: application/json');
        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $ids    = $this->input->post('ids');
        $posisi = $this->_clean_posisi($this->input->post('posisi'), 0);

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => false, 'message' => 'Tidak ada data yang dipilih']);
            return;
        }
        if ($posisi === 0) {
            echo json_encode(['status' => false, 'message' => 'Posisi tidak valid']);
            return;
        }

        // Kolom komentar per posisi (untuk cek "hanya isi jika kosong")
        $comment_col = [
            2 => 'catatan_pembimbing_2',
            3 => 'catatan_penguji_1',
            4 => 'catatan_penguji_2',
        ];
        $label = [1 => 'P1', 2 => 'P2', 3 => 'Penguji 1', 4 => 'Penguji 2'];

        $processed = 0;
        $skipped   = 0;

        foreach ($ids as $id) {
            $preview = $this->_get_reviewable_preview($id, $posisi);
            if (!$preview) { $skipped++; continue; }

            if ($posisi === 1) {
                // Hanya ubah status. Komentar P1 yang sudah ada TIDAK dihapus.
                $this->Mahasiswa_model->update_review_preview($id, ['status_pembimbing' => 'Approved']);
                $processed++;
            } else {
                $col = $comment_col[$posisi];
                // Jangan menimpa komentar yang sudah ditulis dosen
                if (!empty(trim(strip_tags((string) ($preview[$col] ?? ''))))) {
                    $skipped++;
                    continue;
                }
                $this->Mahasiswa_model->update_review_preview($id, [$col => 'Telah ditinjau (massal)']);
                $processed++;
            }
        }

        if ($processed === 0) {
            echo json_encode(['status' => false, 'message' => 'Tidak ada berkas yang diproses (' . $skipped . ' dilewati).']);
            return;
        }

        $message = $processed . ' berkas berhasil diproses (' . $label[$posisi] . ').';
        if ($skipped > 0) $message .= ' ' . $skipped . ' dilewati (tidak berhak / sudah ada komentar).';

        echo json_encode(['status' => true, 'message' => $message]);
    }

    public function ajax_get_preview_log() {
        header('Content-Type: application/json');
        $nim   = $this->_get_current_nim();
        $tahap = $this->input->get('tahap');

        if (empty($tahap)) {
            echo json_encode(['status' => false, 'message' => 'Tahap tidak valid']);
            return;
        }

        $riwayat = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        echo json_encode(['status' => true, 'data' => $riwayat]);
    }

    public function upload_preview_ajax() {
        header('Content-Type: application/json');
        $nim   = $this->_get_current_nim();
        $tahap = $this->input->post('tahap_preview') ?: 'Preview 1';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx|zip';
        $config['max_size']      = 10240;
        $clean_tahap = str_replace(' ', '', strtoupper($tahap));
        $config['file_name']     = $clean_tahap . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_draft')) {
            echo json_encode([
                'status'  => false,
                'message' => $this->upload->display_errors('', '')
            ]);
            return;
        }

        $upload_data = $this->upload->data();
        $file_name = $upload_data['file_name'];
        $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

        $data_insert = [
            'nim'               => $nim,
            'tahap_preview'     => $tahap,
            'file_draft'        => $file_name,
            'catatan_mahasiswa' => $catatan,
            'status_pembimbing' => 'Pending',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        $riwayat = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        $latest  = $riwayat[0] ?? null;

        echo json_encode([
            'status'         => true,
            'message'        => "Draft Berkas {$tahap} berhasil diunggah!",
            'tahap'          => $tahap,
            'upload_count'   => count($riwayat),
            'latest_preview' => $latest,
            'riwayat'        => $riwayat
        ]);
    }

    // =========================================================
    // SSE MAHASISWA
    // =========================================================

    public function sse_mahasiswa_bimbingan() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        session_write_close();

        $nim = $this->_get_current_nim();
        $lastData = null;

        while (true) {
            $riwayat_p1     = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 1');
            $riwayat_p2     = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 2');
            $riwayat_p3     = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 3');
            $riwayat_sidang = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Sidang');

            $data = [
                'riwayat_p1'          => $riwayat_p1,
                'riwayat_p2'          => $riwayat_p2,
                'riwayat_p3'          => $riwayat_p3,
                'riwayat_sidang'      => $riwayat_sidang,
                'latest_p1'           => $riwayat_p1[0] ?? null,
                'latest_p2'           => $riwayat_p2[0] ?? null,
                'latest_p3'           => $riwayat_p3[0] ?? null,
                'latest_sidang'       => $riwayat_sidang[0] ?? null,
                'upload_count_p1'     => count($riwayat_p1),
                'upload_count_p2'     => count($riwayat_p2),
                'upload_count_p3'     => count($riwayat_p3),
                'upload_count_sidang' => count($riwayat_sidang),
                'is_p1_app'           => (bool)(($riwayat_p1[0]['status_pembimbing'] ?? null) == 'Approved'),
                'is_p2_app'           => (bool)(($riwayat_p2[0]['status_pembimbing'] ?? null) == 'Approved'),
                'is_p3_app'           => (bool)(($riwayat_p3[0]['status_pembimbing'] ?? null) == 'Approved'),
            ];

            $json = json_encode($data);
            if ($json !== $lastData) {
                echo "data: " . $json . "\n\n";
                ob_flush(); flush();
                $lastData = $json;
            }

            if (connection_aborted()) break;
            sleep(3);
        }
        exit;
    }

    // =========================================================
    // DOMAIN 2: DOSEN (BIMBINGAN & PENGUJI) — UNIFIED 4 ROLE
    // =========================================================

    /**
     * Render dashboard dosen untuk 4 role (p1, p2, u1, u2).
     * Nilai ?role= dibaca dari URL dan divalidasi terhadap whitelist,
     * sehingga tombol "Penguji 1/2" di hero tidak lagi dipaksa jadi P1.
     */
    private function _render_dosen_dashboard($default_role) {
        if (!$this->_is_authorized_reviewer()) {
            redirect('login');
            return;
        }

        $role = $this->input->get('role', TRUE);

        // Kompatibilitas link lama: ?posisi=1|2
        if (!$role && $this->input->get('posisi')) {
            $is_p2_like = ((int) $this->input->get('posisi') === 2);
            $is_penguji = in_array($default_role, ['u1', 'u2'], true);
            $role = $is_penguji ? ($is_p2_like ? 'u2' : 'u1') : ($is_p2_like ? 'p2' : 'p1');
        }

        if (!isset($this->role_map[$role])) $role = $default_role;

        $model_posisi   = $this->role_map[$role];
        $display_posisi = in_array($role, ['p1', 'u1'], true) ? 1 : 2;
        $is_pembimbing  = in_array($role, ['p1', 'p2'], true);

        // Data mahasiswa diambil via AJAX (ajax_get_dosen_bimbingan),
        // jadi tidak perlu query get_students_by_dosen + 3x get_riwayat_preview per mahasiswa di sini.
        $data = [
            'title'        => $is_pembimbing ? 'Dashboard Bimbingan Dosen' : 'Dashboard Dosen Penguji',
            'role'         => $role,
            'posisi'       => $display_posisi,
            'model_posisi' => $model_posisi,
        ];

        // View unified untuk 4 role
        $this->load->view('mahasiswa/dosen_bimbingan', $data);
    }

    public function dosen_bimbingan() {
        $this->_render_dosen_dashboard('p1');
    }

    public function dosen_penguji() {
        $this->_render_dosen_dashboard('u1');
    }

    public function ajax_get_dosen_bimbingan() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $dosen_id = $this->session->userdata('user_id');
        $posisi   = $this->_posisi_from_get();

        $tahap = $this->input->get('tahap') ?: 'Preview 1';
        if (!in_array($tahap, $this->allowed_tahap, true)) $tahap = 'Preview 1';

        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);

        $data  = [];
        $total = count($students);

        foreach ($students as $student) {
            $previews = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], $tahap);
            $latest   = !empty($previews) ? $previews[0] : null;

            $rekomen = $this->Rekomendasi_model->get_latest_submission($student['nim']);

            if ($latest && !empty($latest['file_draft'])) {
                // File sidang disimpan di uploads/sidang/, yang lain di uploads/preview_ta/
                if ($tahap === 'Sidang') {
                    $sidangFile = $latest['file_sidang'] ?? $latest['file_draft'];
                    $filePath = FCPATH . 'uploads/sidang/' . $sidangFile;
                } else {
                    $filePath = FCPATH . 'uploads/preview_ta/' . $latest['file_draft'];
                }
                if (!file_exists($filePath)) {
                    $latest['file_missing'] = true;
                }
            }

            $data[] = [
                'nim'              => $student['nim'],
                'nama_mahasiswa'   => $student['nama_mahasiswa'] ?? $student['nim'],
                'judul'            => $student['judul'] ?? '-',
                'konsentrasi_dkv'  => $student['konsentrasi_dkv'] ?? '',
                'latest_preview'   => $latest,
                'riwayat_previews' => $previews,
                'rekomendasi'      => $rekomen
            ];
        }

        echo json_encode([
            'status' => true,
            'data'   => $data,
            'stats'  => ['total' => $total]
        ]);
    }

    public function sse_dosen_bimbingan() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        // Ambil semua data session SEBELUM session ditutup
        $authorized = $this->_is_authorized_reviewer();
        $dosen_id   = $this->session->userdata('user_id');
        $posisi     = $this->_posisi_from_get();
        session_write_close();

        if (!$authorized) {
            echo "data: " . json_encode(['status' => false, 'message' => 'Unauthorized']) . "\n\n";
            ob_flush(); flush(); exit;
        }

        set_time_limit(0);
        $lastData = null;

        while (true) {
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            $data = [];
            foreach ($students as $student) {
                $p1 = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 1');
                $p2 = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 2');
                $p3 = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 3');
                $data[] = [
                    'nim'            => $student['nim'],
                    'nama_mahasiswa' => $student['nama_mahasiswa'] ?? $student['nim'],
                    'judul'          => $student['judul'] ?? '-',
                    'preview1'       => $p1,
                    'preview2'       => $p2,
                    'preview3'       => $p3
                ];
            }

            $json = json_encode($data);
            if ($json !== $lastData) {
                echo "data: " . $json . "\n\n";
                ob_flush(); flush();
                $lastData = $json;
            }

            if (connection_aborted()) break;
            sleep(3);
        }
        exit;
    }
}