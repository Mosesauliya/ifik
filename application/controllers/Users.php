<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ============================================================================
 * UNIFIED CONTROLLER: Users
 * ============================================================================
 * Menyatukan seluruh fungsionalitas dan logika controller untuk:
 * 1. Mahasiswa (Dashboard, Pendaftaran TA, Bimbingan, Revisi, Nilai Sidang, Ticketing)
 * 2. Dosen Wali (Persetujuan Bimbingan, Validasi Berkas, Tanda Tangan Digital)
 * 3. Dosen Pembimbing & Penguji (Review Preview 1-3, Sidang, Scoring, SSE)
 */
class Users extends CI_Controller {

    /** Peta role UI => posisi model (1=P1, 2=P2, 3=U1, 4=U2) */
    private $role_map = ['p1' => 1, 'p2' => 2, 'u1' => 3, 'u2' => 4];

    /** Status review yang boleh disimpan oleh P1 */
    private $allowed_status = ['Approved', 'Revision', 'Pending'];

    /** Tahap yang boleh di-query dari dashboard dosen */
    private $allowed_tahap = ['Preview 1', 'Preview 2', 'Preview 3', 'Sidang'];

    public function __construct() {
        parent::__construct();
        // Load semua model, helper, dan library untuk Mahasiswa dan Dosen
        $this->load->model('Mahasiswa_model');
        $this->load->model('AdminLayanan_model');
        $this->load->model('DosenWali_model');
        $this->load->model('Rekomendasi_model');
        $this->load->helper(array('form', 'url', 'file', 'download', 'text'));
        $this->load->library(array('session', 'form_validation', 'upload'));
    }

    /**
     * Role-Aware Default Index Router
     */
    public function index() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $roleId = (int)$this->session->userdata('role_id');
        // Role 4 / Mahasiswa
        if ($roleId === 4) {
            return $this->dashboard_mahasiswa();
        }
        // Role 3 / Dosen, Role 2 / Kaur, Role 9 / Ketua KK
        if (in_array($roleId, [2, 3, 6, 7, 9])) {
            return $this->dosen_wali();
        }

        redirect('dashboard');
    }

    /**
     * Alias method untuk Dosen Wali
     */
    public function dosen() {
        return $this->dosen_wali();
    }

    /**
     * Alias method untuk Dosen Wali
     */
    public function wali() {
        return $this->dosen_wali();
    }

    // =========================================================================
    // SECTION 1: FITUR & METHOD MAHASISWA
    // =========================================================================

private function _get_current_nim() {
        $nim = $this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: ($this->session->userdata('username') ?: ''));
        if (empty($nim) && $this->session->userdata('user_id')) {
            $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
            if ($user_tbl) {
                $u = $this->db->get_where($user_tbl, ['id' => $this->session->userdata('user_id')])->row_array();
                if ($u) {
                    $nim = !empty($u['nim']) ? $u['nim'] : (!empty($u['nidn_nim']) ? $u['nidn_nim'] : ($u['username'] ?? ''));
                }
            }
        }
        return $nim;
    }

    private function _get_current_user_id() {
        $userId = $this->session->userdata('user_id');
        if (!$userId) {
            $nim = $this->_get_current_nim();
            $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
            if ($nim && $user_tbl) {
                $this->db->group_start();
                if ($this->db->field_exists('nim', $user_tbl)) $this->db->where('nim', $nim);
                if ($this->db->field_exists('nidn_nim', $user_tbl)) $this->db->or_where('nidn_nim', $nim);
                if ($this->db->field_exists('username', $user_tbl)) $this->db->or_where('username', $nim);
                $this->db->group_end();
                $u = $this->db->select('id')->limit(1)->get($user_tbl)->row_array();
                if ($u && !empty($u['id'])) {
                    $userId = $u['id'];
                }
            }
        }
        return $userId ? $userId : null;
    }

    private function _do_upload($field_name, $config, &$error = null) {
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        $error = strip_tags($this->upload->display_errors('', ''));
        return null;
    }

    // =========================================================
    // DASHBOARD & PROFIL
    // =========================================================

    public function dashboard_mahasiswa() {
        $nim = $this->_get_current_nim();
        $this->load->model('AdminLayanan_model');
        $data['title']          = 'Dashboard Mahasiswa';
        $data['mahasiswa']      = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran']    = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['student_berkas'] = $this->AdminLayanan_model->get_student_berkas_map($nim);

        $this->load->view('mahasiswa/dashboard', $data);
    }

    public function geodata() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Geodata Mahasiswa';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);

        if ($this->input->post()) {
            $update_data = array(
                'alamat'    => $this->input->post('alamat'),
                'latitude'  => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'kota'      => $this->input->post('kota'),
                'provinsi'  => $this->input->post('provinsi')
            );
            $this->Mahasiswa_model->update_geodata($nim, $update_data);
            $this->session->set_flashdata('success', 'Geodata berhasil diperbarui!');
            redirect('mahasiswa/geodata');
        }

        $this->load->view('mahasiswa/geodata', $data);
    }

    public function ganti_password() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Ganti Password';

        if ($this->input->post()) {
            $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
            $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password_baru]');

            if ($this->form_validation->run() === TRUE) {
                $salt = bin2hex(random_bytes(16));
                $password_hashed = password_hash($this->input->post('password_baru') . $salt, PASSWORD_BCRYPT);
                $this->Mahasiswa_model->update_password($nim, $password_hashed, $salt);
                $this->session->set_flashdata('success', 'Password berhasil diubah!');
                redirect('mahasiswa');
            }
        }

        $this->load->view('mahasiswa/ganti_password', $data);
    }

    public function translate_judul() {
        header('Content-Type: application/json');
        $text = trim($this->input->post('text') ?? '');

        if (empty($text)) {
            echo json_encode(['status' => 'error', 'message' => 'Teks judul tidak boleh kosong!']);
            return;
        }

        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=id&tl=en&dt=t&q=" . urlencode($text);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if (!$err && $http_code === 200 && !empty($response)) {
            $data = json_decode($response, true);
            $translated_text = '';
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data[0] as $segment) {
                    $translated_text .= $segment[0] ?? '';
                }
            }
            if (!empty($translated_text)) {
                echo json_encode(['status' => 'success', 'translated' => trim($translated_text)]);
                return;
            }
        }

        $url_fallback = "https://api.mymemory.translated.net/get?q=" . urlencode($text) . "&langpair=id|en";
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $url_fallback);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 3);
        $fallback_res = curl_exec($ch2);
        curl_close($ch2);

        if (!empty($fallback_res)) {
            $json = json_decode($fallback_res, true);
            $translated = $json['responseData']['translatedText'] ?? '';
            if ($translated) {
                echo json_encode(['status' => 'success', 'translated' => trim($translated)]);
                return;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'Gagal menerjemahkan secara online, silakan ketik judul Inggris secara manual.']);
    }

    // =========================================================
    // PENDAFTARAN TA
    // =========================================================

    public function detail_pendaftaran() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Detail Pengajuan Tugas Akhir';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $this->load->view('mahasiswa/detail_pendaftaran', $data);
    }

    public function edit_pendaftaran() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $has_ta = !empty($pendaftaran['judul_1']);

        if (!$has_ta) {
            redirect('mahasiswa/pendaftaran_ta');
            return;
        }

        $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
        $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
        $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
        $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
        $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
        $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
        $is_locked = !$has_revisi;

        $this->load->model('AdminLayanan_model');
        $data['title'] = $is_locked ? 'Detail / Ringkasan Formulir Tugas Akhir' : 'Edit Formulir Tugas Akhir (Revisi)';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $pendaftaran;
        $data['is_locked'] = $is_locked;
        $data['has_revisi'] = $has_revisi;
        $data['opsi_jenis_ta'] = $this->AdminLayanan_model->get_active_jenis_ta();

        if ($this->input->post()) {
            if ($is_locked) {
                $this->session->set_flashdata('error', 'Formulir saat ini terkunci (hanya lihat) karena pengajuan sedang dalam proses peninjauan.');
                redirect('mahasiswa/edit_pendaftaran');
                return;
            }

            $config['upload_path']   = './uploads/persyaratan_ta/';
            $config['allowed_types'] = 'pdf';
            $config['max_size']      = 5120;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            $file_step3 = $this->_do_upload('file_ksm', $config);
            $file_step4 = $this->_do_upload('file_transkrip', $config);
            $file_step5 = $this->_do_upload('file_pernyataan', $config);
            $file_step6 = $this->_do_upload('file_bebas_lab', $config);

            $new_w_status  = ($w_status === 'Rejected') ? 'Pending' : $w_status;
            $new_a_status  = ($a_status === 'Rejected') ? 'Pending' : $a_status;
            $new_k_status  = ($k_status === 'Rejected') ? 'Pending' : $k_status;
            $new_kk_status = ($kk_status === 'Rejected') ? 'Pending' : $kk_status;

            $posted_jenis_ta = $this->input->post('jenis_ta');
            if (empty($posted_jenis_ta) && !empty($pendaftaran['jenis_ta'])) {
                $posted_jenis_ta = $pendaftaran['jenis_ta'];
            }
            $posted_judul_1 = $this->input->post('judul_1');
            if (empty($posted_judul_1) && !empty($pendaftaran['judul_1'])) {
                $posted_judul_1 = $pendaftaran['judul_1'];
            }
            $posted_judul_2 = $this->input->post('judul_2') ?: ($pendaftaran['judul_2'] ?? '');
            $posted_judul_3 = $this->input->post('judul_3') ?: ($pendaftaran['judul_3'] ?? '');
            $posted_judul_en = $this->input->post('judul_en') ?: ($pendaftaran['judul_en'] ?? '');
            $posted_konsentrasi = $this->input->post('konsentrasi_dkv') ?: ($pendaftaran['konsentrasi_dkv'] ?? '');

            $data_ta = array(
                'nim'                  => $nim,
                'is_submitted'         => 1,
                'jenis_ta'             => $posted_jenis_ta,
                'judul_1'              => $posted_judul_1,
                'judul_2'              => $posted_judul_2,
                'judul_3'              => $posted_judul_3,
                'judul_en'             => $posted_judul_en,
                'konsentrasi_dkv'      => $posted_konsentrasi,
                'file_ksm'             => $file_step3 ? $file_step3 : $this->input->post('file_ksm_old'),
                'file_transkrip'       => $file_step4 ? $file_step4 : $this->input->post('file_transkrip_old'),
                'file_pernyataan'      => $file_step5 ? $file_step5 : $this->input->post('file_pernyataan_old'),
                'file_bebas_lab'       => $file_step6 ? $file_step6 : $this->input->post('file_bebas_lab_old'),
                'status_approval_wali' => $new_w_status,
                'status_approval_admin'=> $new_a_status,
                'status_approval_koor' => $new_k_status,
                'status_approval_kk'   => $new_kk_status,
                'status_judul'         => ($st_judul === 'Rejected') ? 'Pending' : $st_judul,
                'berkas_kurang'        => NULL,
                'created_at'           => $pendaftaran['created_at'] ?? date('Y-m-d H:i:s')
            );

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Perbaikan berkas & formulir Tugas Akhir berhasil dikirim untuk ditinjau ulang!');
            redirect('mahasiswa');
            return;
        }

        $this->load->view('mahasiswa/edit_pendaftaran', $data);
    }

    public function upload_revisi_berkas() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        if (!$pendaftaran) {
            redirect('mahasiswa');
            return;
        }

        $this->load->model('AdminLayanan_model');
        $active_syarat = $this->AdminLayanan_model->get_active_syarat_berkas();
        if (empty($active_syarat)) {
            $active_syarat = array(
                array('kode_berkas' => 'ksm'),
                array('kode_berkas' => 'transkrip'),
                array('kode_berkas' => 'pernyataan'),
                array('kode_berkas' => 'bebas_lab')
            );
        }

        $config['upload_path']   = './uploads/persyaratan_ta/';
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 5120;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        $updated_data = [];
        $uploaded_count = 0;
        $upload_errors = [];

        foreach ($active_syarat as $sb) {
            $k = $sb['kode_berkas'];
            $f = 'file_' . $k;
            if (!empty($_FILES[$f]['name'])) {
                $err = null;
                $new_file = $this->_do_upload($f, $config, $err);
                if ($new_file) {
                    $uploaded_count++;
                    $this->AdminLayanan_model->save_student_berkas($nim, $k, $new_file, 'Pending');

                    // 2. Simpan ke file_pendaftaran jika ada
                    if ($this->db->table_exists('file_pendaftaran')) {
                        $u_id = $this->_get_current_user_id();
                        $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
                        $update_fp = [
                            'file'            => 'uploads/persyaratan_ta/' . $new_file,
                            'status_adminlaa' => 'Pending',
                            'view_adminlaa'   => 0,
                            'view_doswal'     => 0,
                            'date_edit'       => date('Y-m-d H:i:s')
                        ];
                        if (($pendaftaran['status_approval_wali'] ?? '') !== 'Approved') {
                            $update_fp['status_doswal'] = 'Pending';
                            $update_fp['komentar']      = '';
                        }

                        $ex_fp = $this->db->group_start()
                            ->where_in('id_mhs', $target_ids)
                            ->group_end()
                            ->like('nama', $k)
                            ->get('file_pendaftaran')->row_array();

                        if ($ex_fp) {
                            $this->db->where('id', $ex_fp['id'])->update('file_pendaftaran', $update_fp);
                        } else {
                            $fp_fields = $this->db->list_fields('file_pendaftaran');
                            $new_fp = $update_fp;
                            if (in_array('id', $fp_fields)) $new_fp['id'] = 'fp_' . $nim . '_' . $k;
                            if (in_array('id_mhs', $fp_fields)) $new_fp['id_mhs'] = $u_id;
                            if (in_array('nama', $fp_fields)) $new_fp['nama'] = $k;
                            if (in_array('date', $fp_fields)) $new_fp['date'] = date('Y-m-d H:i:s');
                            $this->db->insert('file_pendaftaran', $new_fp);
                        }
                    }

                    // 3. Simpan ke pendaftaran_ta jika tabel dan kolomnya ada
                    if ($this->db->table_exists('pendaftaran_ta')) {
                        if ($this->db->field_exists($f, 'pendaftaran_ta')) {
                            $updated_data[$f] = $new_file;
                        }
                        $col_status  = 'status_file_' . $k;
                        $col_review  = 'review_file_' . $k;
                        $col_catatan = 'catatan_file_' . $k;
                        if (($pendaftaran['status_approval_wali'] ?? '') !== 'Approved') {
                            if ($this->db->field_exists($col_status, 'pendaftaran_ta')) {
                                $updated_data[$col_status] = 'Pending';
                            }
                            if ($this->db->field_exists($col_review, 'pendaftaran_ta')) {
                                $updated_data[$col_review] = 0;
                            }
                            if ($this->db->field_exists($col_catatan, 'pendaftaran_ta')) {
                                $updated_data[$col_catatan] = '';
                            }
                        }
                        $col_legacy_status = 'status_' . $k;
                        if ($this->db->field_exists($col_legacy_status, 'pendaftaran_ta')) {
                            $updated_data[$col_legacy_status] = 'Pending';
                        }
                    }
                } else {
                    $label = !empty($sb['nama_berkas']) ? $sb['nama_berkas'] : strtoupper($k);
                    $upload_errors[] = $label . ' (' . ($err ?: 'harus PDF dan maksimal 5 MB') . ')';
                }
            }
        }

        if ($this->input->post('judul_1')) {
            $new_j = trim($this->input->post('judul_1'));
            if (!empty($new_j)) {
                $updated_data['judul_1'] = $new_j;
                $updated_data['status_judul'] = 'Pending';
                $updated_data['catatan_judul'] = '';
                if ($this->db->table_exists('guidance')) {
                    $u_id = $this->_get_current_user_id();
                    $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                        'judul_1'    => $new_j,
                        'keterangan' => 'Pending',
                        'komentar'   => ''
                    ]);
                }
            }
        }

        if ($this->input->post('jenis_ta')) {
            $new_jen = trim($this->input->post('jenis_ta'));
            $updated_data['jenis_ta'] = $new_jen;
            $updated_data['status_jenis_ta'] = 'Pending';
            $updated_data['catatan_jenis_ta'] = '';
            if ($this->db->table_exists('guidance')) {
                $u_id = $this->_get_current_user_id();
                $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
                $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                    'jenis_TA' => $new_jen
                ]);
            }
        }

        if (!empty($updated_data) || $uploaded_count > 0) {
            $prev_w_st  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $prev_a_st  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $prev_k_st  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $prev_kk_st = $pendaftaran['status_approval_kk'] ?? 'Pending';

            if ($prev_w_st === 'Rejected') $updated_data['status_approval_wali'] = 'Pending';
            if ($prev_a_st === 'Rejected' || !empty($pendaftaran['berkas_kurang'])) {
                $updated_data['status_approval_admin'] = 'Pending';
                $updated_data['berkas_kurang'] = NULL;
                $updated_data['catatan_admin'] = '';
            }
            if ($prev_kk_st === 'Rejected') {
                $updated_data['status_approval_kk'] = 'Pending';
                $updated_data['catatan_kk'] = '';
            }

            $new_w_st  = $updated_data['status_approval_wali'] ?? $prev_w_st;
            $new_a_st  = $updated_data['status_approval_admin'] ?? $prev_a_st;
            $new_k_st  = $updated_data['status_approval_koor'] ?? $prev_k_st;
            $new_kk_st = $updated_data['status_approval_kk'] ?? $prev_kk_st;

            if ($new_w_st !== 'Approved') {
                $updated_data['current_stage'] = 'Dosen Wali';
            } else if ($new_a_st !== 'Approved') {
                $updated_data['current_stage'] = 'Admin Layanan';
            } else if ($new_k_st !== 'Approved') {
                $updated_data['current_stage'] = 'Koordinator TA';
            } else if ($new_kk_st !== 'Approved') {
                $updated_data['current_stage'] = 'Ketua KK';
            } else {
                $updated_data['current_stage'] = 'Selesai Approval';
            }

            $updated_data['updated_at'] = date('Y-m-d H:i:s');

            $latest_berkas_map = $this->AdminLayanan_model->get_student_berkas_map($nim);
            $has_any_rejected_left = false;
            foreach ($active_syarat as $sb) {
                $k = $sb['kode_berkas'];
                $col_status = 'status_file_' . $k;
                if (isset($updated_data[$col_status])) {
                    $st = $updated_data[$col_status];
                } else {
                    $ver_laa = $latest_berkas_map[$k]['status_verifikasi'] ?? ($pendaftaran['status_' . $k] ?? null);
                    if ($ver_laa === 'Invalid') {
                        $st = 'Rejected';
                    } else {
                        $st = $pendaftaran[$col_status] ?? 'Pending';
                    }
                }
                if ($st === 'Rejected') { $has_any_rejected_left = true; break; }
            }
            if (!$has_any_rejected_left && ($updated_data['status_judul'] ?? $pendaftaran['status_judul'] ?? 'Pending') === 'Rejected') {
                $has_any_rejected_left = true;
            }
            if (!$has_any_rejected_left && ($updated_data['status_jenis_ta'] ?? $pendaftaran['status_jenis_ta'] ?? 'Pending') === 'Rejected') {
                $has_any_rejected_left = true;
            }

            if (!$has_any_rejected_left) {
                $updated_data['catatan_wali'] = '';
                if ($this->db->table_exists('guidance')) {
                    $u_id = $this->_get_current_user_id();
                    $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                        'komentar' => ''
                    ]);
                }
            }

            if ($this->db->table_exists('pendaftaran_ta')) {
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $updated_data);
            }

            if (!empty($upload_errors)) {
                $msg = 'Sebagian berkas gagal diunggah: ' . implode('; ', $upload_errors) . '. Pastikan berkas berformat PDF dan ukuran maksimal 5 MB.';
                if ($uploaded_count > 0 || !empty($this->input->post('judul_1')) || !empty($this->input->post('jenis_ta'))) {
                    $this->session->set_flashdata('warning', $msg . ' Namun berkas yang valid telah berhasil disimpan.');
                } else {
                    $this->session->set_flashdata('error', $msg);
                }
            } else {
                $this->session->set_flashdata('success', 'Berhasil mengunggah ' . ($uploaded_count ? $uploaded_count . ' berkas perbaikan' : 'perubahan usulan') . ' untuk diverifikasi kembali!');
            }
        } else {
            if (!empty($upload_errors)) {
                $this->session->set_flashdata('error', 'Gagal mengunggah berkas: ' . implode('; ', $upload_errors) . '. Pastikan file berformat PDF dan ukuran maksimal 5 MB.');
            } else {
                $this->session->set_flashdata('error', 'Tidak ada file baru yang diunggah. Silakan pilih file PDF yang valid (Maksimal 5 MB).');
            }
        }

        redirect('mahasiswa');
    }

    public function pendaftaran_ta() {
        $nim = $this->_get_current_nim();

        $this->load->model('AdminLayanan_model');
        $student_berkas = $this->AdminLayanan_model->get_student_berkas_map($nim);
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $has_completed_submission = !empty($pendaftaran['is_submitted']);

        $has_revisi = false;
        $is_locked = false;

        if ($has_completed_submission) {
            $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
            $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
            $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
            // Jika sudah pernah diajukan (is_submitted), form pendaftaran utama tetap terkunci.
            // Mahasiswa yang memiliki catatan revisi harus mengunggah perbaikan melalui menu Perbaikan Berkas di Dashboard.
            $is_locked = true;
        }

        $has_ta = !empty($pendaftaran['jenis_ta']) || !empty($pendaftaran['judul_1']) || !empty($pendaftaran['file_ksm']) || !empty($student_berkas);

        $server_draft_step = 1;
        if (!empty($pendaftaran) || !empty($student_berkas)) {
            $saved_step = !empty($pendaftaran['draft_step']) ? (int)$pendaftaran['draft_step'] : 0;
            if ($saved_step >= 4) $saved_step = 2;

            if ($saved_step >= 1 && $saved_step <= 3) {
                $server_draft_step = $saved_step;
            } else {
                $has_any_file = !empty($pendaftaran['file_ksm']) || !empty($pendaftaran['file_transkrip']) || !empty($pendaftaran['file_pernyataan']) || !empty($pendaftaran['file_bebas_lab']) || !empty($student_berkas);
                $has_step1 = !empty($pendaftaran['jenis_ta']) && !empty($pendaftaran['judul_1']);
                if ($has_any_file) {
                    $server_draft_step = 2;
                } elseif ($has_step1) {
                    $server_draft_step = 2;
                } else {
                    $server_draft_step = 1;
                }
            }

            if ($server_draft_step > 3) $server_draft_step = 3;
            if ($server_draft_step < 1) $server_draft_step = 1;
        }

        $data['title']          = $is_locked ? 'Pendaftaran Tugas Akhir (Sedang Ditinjau)' : 'Pendaftaran Tugas Akhir (3 Step)';
        $data['mahasiswa']      = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran']    = $pendaftaran;
        $data['is_locked']      = $is_locked;
        $data['has_revisi']     = $has_revisi;
        $data['has_ta']         = $has_ta;
        $data['server_draft_step'] = $server_draft_step;
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['opsi_jenis_ta']  = $this->AdminLayanan_model->get_active_jenis_ta();
        $data['student_berkas'] = $student_berkas;

        if ($this->input->post()) {
            if ($is_locked) {
                $this->session->set_flashdata('error', 'Formulir pendaftaran telah dikunci karena sudah diajukan. Untuk melakukan perbaikan berkas yang direvisi, silakan gunakan menu Perbaikan Berkas di Dashboard.');
                redirect('mahasiswa');
                return;
            }

            $config['upload_path']   = './uploads/persyaratan_ta/';
            $config['allowed_types'] = 'pdf';
            $config['max_size']      = 5120;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            $active_syarat = $data['syarat_berkas'];
            $file_uploads = [];

            foreach ($active_syarat as $sb) {
                $kode = $sb['kode_berkas'];
                $field_name = 'file_' . $kode;
                $uploaded = $this->_do_upload($field_name, $config);

                if ($uploaded) {
                    $file_uploads[$kode] = $uploaded;
                    $this->AdminLayanan_model->save_student_berkas($nim, $kode, $uploaded, 'Pending', $sb['nama_berkas'] ?? null);
                } else {
                    $old = $this->input->post($field_name . '_old');
                    if ($old) {
                        $file_uploads[$kode] = $old;
                    }
                }
            }

            $file_step3 = $file_uploads['ksm'] ?? $this->_do_upload('file_ksm', $config);
            $file_step4 = $file_uploads['transkrip'] ?? $this->_do_upload('file_transkrip', $config);
            $file_step5 = $file_uploads['pernyataan'] ?? $this->_do_upload('file_pernyataan', $config);
            $file_step6 = $file_uploads['bebas_lab'] ?? $this->_do_upload('file_bebas_lab', $config);

            $existing_ta = $this->db->table_exists('pendaftaran_ta')
                ? $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array()
                : $this->Mahasiswa_model->get_status_pendaftaran($nim);

            $is_final_submit = ($this->input->post('is_submitted') == '1' || $this->input->post('submit_action') === 'submit');

            $w_status  = isset($existing_ta['status_approval_wali']) ? $existing_ta['status_approval_wali'] : ($is_final_submit ? 'Pending' : 'Draft');
            $a_status  = isset($existing_ta['status_approval_admin']) ? $existing_ta['status_approval_admin'] : 'Pending';
            $k_status  = isset($existing_ta['status_approval_koor']) ? $existing_ta['status_approval_koor'] : 'Pending';
            $kk_status = isset($existing_ta['status_approval_kk']) ? $existing_ta['status_approval_kk'] : 'Pending';

            if ($w_status === 'Rejected') {
                $w_status = 'Pending';
            } elseif (!$is_final_submit) {
                $w_status = 'Draft';
            } elseif (empty($w_status) || $w_status === 'Draft') {
                $w_status = 'Pending';
            }

            $has_new_uploads = !empty($file_step3) || !empty($file_step4) || !empty($file_step5) || !empty($file_step6);
            if ($a_status === 'Rejected' || ($a_status === 'Approved' && $has_new_uploads) || $has_new_uploads) {
                $a_status = 'Pending';
            }

            if ($k_status === 'Rejected') $k_status = 'Pending';
            if ($kk_status === 'Rejected') $kk_status = 'Pending';

            if ($w_status === 'Draft') {
                $current_stage = 'Draft';
            } else if ($w_status !== 'Approved') {
                $current_stage = 'Dosen Wali';
            } else if ($a_status !== 'Approved') {
                $current_stage = 'Admin Layanan';
            } else if ($k_status !== 'Approved') {
                $current_stage = 'Koordinator TA';
            } else if ($kk_status !== 'Approved') {
                $current_stage = 'Ketua KK';
            } else {
                $current_stage = 'Selesai Approval';
            }

            $mhs_data      = $this->db->get_where('mahasiswa', ['nim' => $nim])->row_array();
            $nip_dw        = !empty($mhs_data['nip_dosen_wali']) ? $mhs_data['nip_dosen_wali'] : null;
            $dw_row        = $nip_dw ? $this->db->get_where('dosen_wali', ['nip' => $nip_dw])->row_array() : null;
            $id_dosen_wali = $dw_row ? $dw_row['id'] : null;

            $posted_jenis_ta = $this->input->post('jenis_ta');
            if (empty($posted_jenis_ta) && !empty($existing_ta['jenis_ta'])) {
                $posted_jenis_ta = $existing_ta['jenis_ta'];
            }
            $posted_judul_1 = $this->input->post('judul_1');
            if (empty($posted_judul_1) && !empty($existing_ta['judul_1'])) {
                $posted_judul_1 = $existing_ta['judul_1'];
            }
            $posted_judul_2 = $this->input->post('judul_2') ?: ($existing_ta['judul_2'] ?? '');
            $posted_judul_3 = $this->input->post('judul_3') ?: ($existing_ta['judul_3'] ?? '');
            $posted_judul_en = $this->input->post('judul_en') ?: ($existing_ta['judul_en'] ?? '');
            $posted_konsentrasi = $this->input->post('konsentrasi_dkv') ?: ($existing_ta['konsentrasi_dkv'] ?? '');

            $data_ta = array(
                'nim'                  => $nim,
                'id_dosen_wali'        => $id_dosen_wali,
                'is_submitted'         => $is_final_submit ? 1 : 0,
                'jenis_ta'             => $posted_jenis_ta,
                'judul_1'              => $posted_judul_1,
                'judul_2'              => $posted_judul_2,
                'judul_3'              => $posted_judul_3,
                'judul_en'             => $posted_judul_en,
                'konsentrasi_dkv'      => $posted_konsentrasi,
                'file_ksm'             => $file_step3 ? $file_step3 : $this->input->post('file_ksm_old'),
                'file_transkrip'       => $file_step4 ? $file_step4 : $this->input->post('file_transkrip_old'),
                'file_pernyataan'      => $file_step5 ? $file_step5 : $this->input->post('file_pernyataan_old'),
                'file_bebas_lab'       => $file_step6 ? $file_step6 : $this->input->post('file_bebas_lab_old'),
                'status_approval_wali' => $w_status,
                'status_approval_admin'=> $a_status,
                'status_approval_koor' => $k_status,
                'status_approval_kk'   => $kk_status,
                'current_stage'        => $current_stage,
                'created_at'           => isset($existing_ta['created_at']) ? $existing_ta['created_at'] : date('Y-m-d H:i:s')
            );

            if ($file_step3) $data_ta['status_ksm'] = 'Pending';
            if ($file_step4) $data_ta['status_transkrip'] = 'Pending';
            if ($file_step5) $data_ta['status_pernyataan'] = 'Pending';
            if ($file_step6) $data_ta['status_bebas_lab'] = 'Pending';

            $file_updates = array(
                'ksm'        => $file_step3,
                'transkrip'  => $file_step4,
                'pernyataan' => $file_step5,
                'bebas_lab'  => $file_step6
            );
            if ($this->db->table_exists('pendaftaran_berkas')) {
                foreach ($file_updates as $f_code => $f_name) {
                    if (!empty($f_name)) {
                        $ex_b = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $f_code])->row_array();
                        if ($ex_b) {
                            $this->db->where('id', $ex_b['id'])->update('pendaftaran_berkas', [
                                'file_name'         => $f_name,
                                'status_verifikasi' => 'Pending',
                                'updated_at'        => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            $this->db->insert('pendaftaran_berkas', [
                                'nim'               => $nim,
                                'kode_berkas'       => $f_code,
                                'file_name'         => $f_name,
                                'status_verifikasi' => 'Pending',
                                'created_at'        => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            if ($a_status === 'Pending' && !empty($existing_ta['berkas_kurang'])) {
                $data_ta['berkas_kurang'] = NULL;
            }

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Pendaftaran / Revisi Tugas Akhir berhasil disimpan!');
            redirect('mahasiswa');
        }

        $this->load->view('mahasiswa/pendaftaran_ta', $data);
    }

    public function reset_pendaftaran() {
        $nim = $this->_get_current_nim();
        $this->Mahasiswa_model->reset_pendaftaran_ta($nim);
        $this->session->set_flashdata('success', 'Pengajuan Tugas Akhir berhasil di-reset!');
        redirect('mahasiswa');
    }

    public function get_status_pendaftaran_ajax() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $has_ta = !empty($pendaftaran['judul_1']) || !empty($pendaftaran['jenis_ta']) || !empty($pendaftaran['is_submitted']) || !empty($pendaftaran['file_ksm']);

        if (!$pendaftaran || !$has_ta) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'has_ta'  => false,
                    'message' => 'Belum ada pengajuan Tugas Akhir.'
                ]));
            return;
        }

        $w_status  = $pendaftaran['status_approval_wali']  ?? 'Pending';
        $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
        $k_status  = $pendaftaran['status_approval_koor']  ?? 'Pending';
        $kk_status = $pendaftaran['status_approval_kk']    ?? 'Pending';

        $approved_count = 0;
        if ($w_status === 'Approved') $approved_count++;
        if ($a_status === 'Approved') $approved_count++;
        if ($k_status === 'Approved') $approved_count++;
        if ($kk_status === 'Approved') $approved_count++;

        $progress_pct = round(($approved_count / 4) * 100);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'        => true,
                'has_ta'         => true,
                'nim'            => $nim,
                'current_stage'  => $pendaftaran['current_stage'] ?? 'Dosen Wali',
                'status_wali'    => $w_status,
                'status_admin'   => $a_status,
                'status_koor'    => $k_status,
                'status_kk'      => $kk_status,
                'status_jenis_ta' => $pendaftaran['status_jenis_ta'] ?? 'Pending',
                'status_judul'    => $pendaftaran['status_judul'] ?? 'Pending',
                'status_file_ksm' => $pendaftaran['status_file_ksm'] ?? 'Pending',
                'status_file_transkrip' => $pendaftaran['status_file_transkrip'] ?? 'Pending',
                'status_file_pernyataan' => $pendaftaran['status_file_pernyataan'] ?? 'Pending',
                'status_file_bebas_lab' => $pendaftaran['status_file_bebas_lab'] ?? 'Pending',
                'updated_at'     => $pendaftaran['updated_at'] ?? '',
                'approved_count' => $approved_count,
                'progress_pct'   => $progress_pct,
                'judul_1'        => $pendaftaran['judul_1'] ?? ''
            ]));
    }

    public function ajax_upload_file_ta() {
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $field_name = $this->input->post('field_name');

        if (empty($field_name) || !preg_match('/^file_[a-z0-9_]+$/i', $field_name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Field berkas tidak valid.']));
            return;
        }

        $upload_dir = './uploads/persyaratan_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 5120;
        $config['file_name']     = $field_name . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];

            $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);
            $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : 'Desain Komunikasi Visual';
            $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;

            $kode_berkas = str_replace('file_', '', $field_name);

            if ($this->db->table_exists('pendaftaran_ta')) {
                $existing_ta = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
                $upData = [];
                if ($this->db->field_exists($field_name, 'pendaftaran_ta')) {
                    $upData[$field_name] = $file_name;
                }
                $upData['draft_step'] = 2;
                $upData['updated_at'] = date('Y-m-d H:i:s');

                if ($existing_ta) {
                    if (empty($existing_ta['konsentrasi_dkv'])) $upData['konsentrasi_dkv'] = $mhs_konsentrasi;
                    if (empty($existing_ta['id_kk'])) $upData['id_kk'] = $mhs_id_kk;
                    if (isset($existing_ta['status_approval_admin']) && in_array($existing_ta['status_approval_admin'], ['Approved', 'Rejected'])) {
                        $upData['status_approval_admin'] = 'Pending';
                        if (($existing_ta['status_approval_wali'] ?? '') === 'Approved') {
                            $upData['current_stage'] = 'Admin Layanan';
                        }
                    }
                    $this->db->where('nim', $nim)->update('pendaftaran_ta', $upData);
                } else {
                    $upData['nim']                  = $nim;
                    $upData['konsentrasi_dkv']      = $mhs_konsentrasi;
                    $upData['id_kk']                = $mhs_id_kk;
                    $upData['is_submitted']         = 0;
                    $upData['status_approval_wali'] = 'Draft';
                    $upData['status_approval_admin'] = 'Pending';
                    $upData['status_approval_koor'] = 'Pending';
                    $upData['status_approval_kk']   = 'Pending';
                    $upData['current_stage']        = 'Draft';
                    $upData['created_at']           = date('Y-m-d H:i:s');
                    $this->db->insert('pendaftaran_ta', $upData);
                }
            }

            $this->load->model('AdminLayanan_model');
            $req_nama_berkas = $this->input->post('nama_berkas');
            if (empty($req_nama_berkas)) {
                $sb_row = $this->db->get_where('syarat_berkas_ta', ['kode_berkas' => $kode_berkas])->row_array();
                if ($sb_row) $req_nama_berkas = $sb_row['nama_berkas'];
            }

            if (method_exists($this->AdminLayanan_model, 'save_student_berkas')) {
                $this->AdminLayanan_model->save_student_berkas($nim, $kode_berkas, $file_name, 'Pending', $req_nama_berkas);
            }

            $kode_berkas = str_replace('file_', '', $field_name);
            if ($this->db->table_exists('pendaftaran_berkas')) {
                $existing_berkas = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $kode_berkas])->row_array();
                if ($existing_berkas) {
                    $up_data = [
                        'file_name'         => $file_name,
                        'status_verifikasi' => 'Pending',
                        'updated_at'        => date('Y-m-d H:i:s')
                    ];
                    if (!empty($req_nama_berkas)) $up_data['nama_berkas'] = $req_nama_berkas;
                    $this->db->where('id', $existing_berkas['id'])->update('pendaftaran_berkas', $up_data);
                } else {
                    $this->db->insert('pendaftaran_berkas', [
                        'nim'               => $nim,
                        'kode_berkas'       => $kode_berkas,
                        'nama_berkas'       => $req_nama_berkas,
                        'file_name'         => $file_name,
                        'status_verifikasi' => 'Pending',
                        'created_at'        => date('Y-m-d H:i:s')
                    ]);
                }
            }

            if ($this->db->table_exists('file_pendaftaran')) {
                try {
                    $fp_fields = $this->db->list_fields('file_pendaftaran');
                    $user_id_real = $this->_get_current_user_id();
                    $id_fp = 'fp_' . $nim . '_' . $kode_berkas;
                    $target_user_ids = array_values(array_unique(array_filter([$user_id_real, $nim])));
                    $ex_fp = $this->db->group_start()
                        ->where('id', $id_fp)
                        ->or_group_start()
                            ->where_in('id_mhs', $target_user_ids)
                            ->where('nama', $kode_berkas)
                        ->group_end()
                    ->group_end()
                    ->get('file_pendaftaran')->row_array();
                    $rel_file_path = 'uploads/persyaratan_ta/' . $file_name;

                    $fp_payload = array();
                    if (in_array('file', $fp_fields)) $fp_payload['file'] = $rel_file_path;
                    if (in_array('status_doswal', $fp_fields)) $fp_payload['status_doswal'] = 'Pending';
                    if (in_array('status_adminlaa', $fp_fields)) $fp_payload['status_adminlaa'] = 'Pending';
                    if (in_array('view_adminlaa', $fp_fields)) $fp_payload['view_adminlaa'] = 0;
                    if (in_array('view_doswal', $fp_fields)) $fp_payload['view_doswal'] = 0;
                    if (in_array('date_edit', $fp_fields)) $fp_payload['date_edit'] = date('Y-m-d H:i:s');

                    if ($ex_fp) {
                        if (!empty($fp_payload)) {
                            $this->db->where('id', $ex_fp['id'])->update('file_pendaftaran', $fp_payload);
                        }
                    } else {
                        if (in_array('id', $fp_fields)) $fp_payload['id'] = $id_fp;
                        if (in_array('id_mhs', $fp_fields)) $fp_payload['id_mhs'] = $user_id_real;
                        if (in_array('nama', $fp_fields)) $fp_payload['nama'] = $kode_berkas;
                        if (in_array('view_adminlaa', $fp_fields)) $fp_payload['view_adminlaa'] = 0;
                        if (in_array('view_doswal', $fp_fields)) $fp_payload['view_doswal'] = 0;
                        if (in_array('komentar', $fp_fields)) $fp_payload['komentar'] = '';
                        if (in_array('date', $fp_fields)) $fp_payload['date'] = date('Y-m-d H:i:s');
                        $this->db->insert('file_pendaftaran', $fp_payload);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error syncing file_pendaftaran in ajax_upload: ' . $e->getMessage());
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success'    => true,
                    'field_name' => $field_name,
                    'kode_berkas'=> $kode_berkas,
                    'file_name'  => $file_name,
                    'file_size'  => number_format($upload_data['file_size'] / 1024, 2) . ' MB',
                    'file_url'   => base_url('uploads/persyaratan_ta/' . $file_name),
                    'message'    => 'Berkas berhasil diunggah dan tersimpan di database.'
                ]));
        } else {
            $error_msg = $this->upload->display_errors('', '');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $error_msg ?: 'Gagal mengunggah berkas.'
                ]));
        }
    }

    public function ajax_delete_file_ta() {
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $field_name = $this->input->post('field_name');

        if (empty($field_name) || !preg_match('/^file_[a-z0-9_]+$/i', $field_name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Field berkas tidak valid.']));
            return;
        }

        $kode_berkas = str_replace('file_', '', $field_name);

        $pendaftaran = $this->db->table_exists('pendaftaran_ta')
            ? $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array()
            : $this->Mahasiswa_model->get_status_pendaftaran($nim);
        if (!empty($pendaftaran['is_submitted'])) {
            $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
            $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
            $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
            if (!$has_revisi) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Pendaftaran telah dikunci dan tidak dapat diubah.']));
                return;
            }
        }

        if ($this->db->table_exists('pendaftaran_berkas')) {
            $this->db->where('nim', $nim)->where('kode_berkas', $kode_berkas)->delete('pendaftaran_berkas');
        }

        if ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists($field_name, 'pendaftaran_ta')) {
            $this->db->where('nim', $nim)->update('pendaftaran_ta', [
                $field_name => '',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'     => true,
                'field_name'  => $field_name,
                'kode_berkas' => $kode_berkas,
                'message'     => 'Berkas berhasil dihapus dari database.'
            ]));
    }

    public function ajax_save_draft_ta() {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $nim = $this->input->post('nim') ?: $this->_get_current_nim();
            $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);
            $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : 'Desain Komunikasi Visual';
            $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;
            $existing_status = $this->Mahasiswa_model->get_status_pendaftaran($nim);

            $jenis_ta = $this->input->post('jenis_ta', true);
            if (empty($jenis_ta) && !empty($existing_status['jenis_ta'])) {
                $jenis_ta = $existing_status['jenis_ta'];
            }
            $judul_1  = $this->input->post('judul_1', true);
            if (empty($judul_1) && !empty($existing_status['judul_1'])) {
                $judul_1 = $existing_status['judul_1'];
            }
            $judul_2  = $this->input->post('judul_2', true);
            if (empty($judul_2) && !empty($existing_status['judul_2'])) {
                $judul_2 = $existing_status['judul_2'];
            }
            $judul_3  = $this->input->post('judul_3', true);
            if (empty($judul_3) && !empty($existing_status['judul_3'])) {
                $judul_3 = $existing_status['judul_3'];
            }
            $judul_en = $this->input->post('judul_en', true);
            if (empty($judul_en) && !empty($existing_status['judul_en'])) {
                $judul_en = $existing_status['judul_en'];
            }
            $konsentrasi_dkv = $this->input->post('konsentrasi_dkv', true) ?: ($existing_status['konsentrasi_dkv'] ?? $mhs_konsentrasi);

            $draft_step = (int)$this->input->post('draft_step', true);

            $data_update = array();
            if ($jenis_ta !== null && $jenis_ta !== '') $data_update['jenis_ta'] = $jenis_ta;
            if ($judul_1 !== null)  $data_update['judul_1'] = $judul_1;
            if ($judul_2 !== null)  $data_update['judul_2'] = $judul_2;
            if ($judul_3 !== null)  $data_update['judul_3'] = $judul_3;
            if ($judul_en !== null) $data_update['judul_en'] = $judul_en;
            if ($draft_step >= 1 && $draft_step <= 3) $data_update['draft_step'] = $draft_step;
            $data_update['konsentrasi_dkv'] = $konsentrasi_dkv;
            $data_update['id_kk'] = $mhs_id_kk;

            $this->load->model('AdminLayanan_model');
            $post_data = $this->input->post(null, true);
            $has_any_file = false;

            if (is_array($post_data)) {
                foreach ($post_data as $key => $val) {
                    if (empty($val) || !is_string($val)) continue;
                    if (preg_match('/^file_([a-z0-9_]+)$/i', $key)) {
                        $clean_key = preg_replace('/_old$/i', '', $key);
                        $raw_kode  = str_replace('file_', '', $clean_key);
                        $col_name  = 'file_' . $raw_kode;
                        $val = trim($val);

                        if (!empty($val) && strtolower(substr($val, -4)) === '.pdf') {
                            $has_any_file = true;
                            if ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists($col_name, 'pendaftaran_ta')) {
                                $data_update[$col_name] = $val;
                            }
                            if ($this->db->table_exists('pendaftaran_berkas') && method_exists($this->AdminLayanan_model, 'save_student_berkas')) {
                                $this->AdminLayanan_model->save_student_berkas($nim, $raw_kode, $val, 'Pending');
                            }
                        }
                    }
                }
            }

            $data_update['updated_at'] = date('Y-m-d H:i:s');
            $db_saved = false;

            if ($this->db->table_exists('pendaftaran_ta')) {
                $p_fields = $this->db->list_fields('pendaftaran_ta');
                foreach ($data_update as $fk => $fv) {
                    if (!in_array($fk, $p_fields)) {
                        unset($data_update[$fk]);
                    }
                }

                $existing = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
                if ($existing) {
                    $this->db->where('nim', $nim)->update('pendaftaran_ta', $data_update);
                    $db_saved = true;
                } else {
                    if (!empty($jenis_ta) || !empty($judul_1) || $has_any_file) {
                        if (in_array('nim', $p_fields)) $data_update['nim'] = $nim;
                        if (in_array('created_at', $p_fields)) $data_update['created_at'] = date('Y-m-d H:i:s');
                        if (in_array('is_submitted', $p_fields)) $data_update['is_submitted'] = 0;
                        if (in_array('status_approval_wali', $p_fields)) $data_update['status_approval_wali'] = 'Draft';
                        if (in_array('status_approval_admin', $p_fields)) $data_update['status_approval_admin'] = 'Pending';
                        if (in_array('status_approval_koor', $p_fields)) $data_update['status_approval_koor'] = 'Pending';
                        if (in_array('status_approval_kk', $p_fields)) $data_update['status_approval_kk'] = 'Pending';
                        if (in_array('current_stage', $p_fields)) $data_update['current_stage'] = 'Draft';
                        $this->db->insert('pendaftaran_ta', $data_update);
                        $db_saved = true;
                    }
                }
            }

            // Sinkronkan juga ke tabel guidance (legacy / active db)
            if ($this->db->table_exists('guidance')) {
                $user_id = $this->_get_current_user_id();
                $target_ids = array_values(array_unique(array_filter([$user_id, $nim])));
                $existing_g = $this->db->where_in('id_mhs', $target_ids)
                    ->order_by('date', 'DESC')
                    ->limit(1)
                    ->get('guidance')->row_array();
                if (!$existing_g) {
                    $existing_g = $this->db->get_where('guidance', ['id' => 'gdn_' . $nim])->row_array();
                }

                $g_fields = $this->db->list_fields('guidance');
                $g_data = array();

                if (!empty($judul_1) && in_array('judul_1', $g_fields)) $g_data['judul_1'] = $judul_1;
                if (!empty($judul_2) && in_array('judul_2', $g_fields)) $g_data['judul_2'] = $judul_2;
                if (!empty($judul_3) && in_array('judul_3', $g_fields)) $g_data['judul_3'] = $judul_3;
                if (!empty($judul_en) && in_array('judul_en', $g_fields)) $g_data['judul_en'] = $judul_en;
                if (!empty($jenis_ta) && in_array('jenis_TA', $g_fields)) $g_data['jenis_TA'] = $jenis_ta;
                if (!empty($konsentrasi_dkv) && in_array('peminatan', $g_fields)) $g_data['peminatan'] = $konsentrasi_dkv;
                if (in_array('date_edit', $g_fields)) $g_data['date_edit'] = date('Y-m-d H:i:s');

                if (!empty($g_data)) {
                    if ($existing_g) {
                        $this->db->where('id', $existing_g['id'])->update('guidance', $g_data);
                        $db_saved = true;
                    } else if (!empty($jenis_ta) || !empty($judul_1)) {
                        if (in_array('id', $g_fields)) $g_data['id'] = 'gdn_' . $nim;
                        if (in_array('id_mhs', $g_fields)) $g_data['id_mhs'] = $user_id;
                        if (in_array('date', $g_fields)) $g_data['date'] = date('Y-m-d H:i:s');
                        if (in_array('tahun', $g_fields)) $g_data['tahun'] = date('Y');
                        if (in_array('keterangan', $g_fields)) $g_data['keterangan'] = 'Draft';
                        $this->db->insert('guidance', $g_data);
                        $db_saved = true;
                    }
                }
            }

            $this->db->db_debug = $prev_debug;
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'db_saved' => $db_saved,
                    'message' => 'Draft formulir & berkas berhasil tersimpan di database server.'
                ]));
        } catch (\Throwable $e) {
            $this->db->db_debug = $prev_debug;
            log_message('error', 'ajax_save_draft_ta exception: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }

    // =========================================================
    // REKOMENDASI SIDANG / NON-SIDANG
    // =========================================================

    public function ajax_get_rekomen_options() {
        $this->load->model('Rekomendasi_model');
        $main = $this->Rekomendasi_model->get_active_main_options();
        $non_sidang = $this->Rekomendasi_model->get_active_non_sidang_options();
        echo json_encode([
            'status' => 'success',
            'data' => $non_sidang,
            'main_options' => $main
        ]);
        exit;
    }

    public function submit_rekomendasi_sidang() {
        $this->load->model('Rekomendasi_model');
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $id_preview = $this->input->post('id_preview');
        $user_id = $this->session->userdata('user_id');

        $data_sub = [
            'nim' => $nim,
            'id_preview' => $id_preview,
            'recommendation_type' => 'sidang',
            'jalur_id' => NULL,
            'jalur_title' => 'SIDANG',
            'form_data_json' => json_encode(['catatan' => 'Direkomendasikan Sidang Akhir Reguler']),
            'catatan_dosen' => 'Lanjut ke Tahapan Pendaftaran Sidang Akhir',
            'status' => 'Submitted',
            'created_by' => $user_id
        ];

        $sub_id = $this->Rekomendasi_model->save_submission($data_sub);

        if ($this->db->table_exists('pendaftaran_ta')) {
            $update_ta = ['current_stage' => 'Pendaftaran Sidang'];
            if ($this->db->field_exists('status_sidang', 'pendaftaran_ta')) {
                $update_ta['status_sidang'] = 'Belum Dijadwalkan';
            }
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $update_ta);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Rekomendasi Sidang berhasil disimpan! Status mahasiswa kini berlanjut ke Pendaftaran Sidang.']);
            exit;
        }

        $this->session->set_flashdata('success', 'Rekomendasi Sidang berhasil disimpan!');
        redirect('mahasiswa/bimbingan');
    }

    public function submit_rekomendasi_nonsidang() {
        $this->load->model('Rekomendasi_model');
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $id_preview = $this->input->post('id_preview');
        $jalur_id = $this->input->post('jalur_id');
        $user_id = $this->session->userdata('user_id');

        $option = $this->Rekomendasi_model->get_option($jalur_id);
        if (!$option) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Jalur Non-Sidang tidak ditemukan.']);
                exit;
            }
            $this->session->set_flashdata('error', 'Jalur Non-Sidang tidak valid.');
            redirect('mahasiswa/bimbingan');
        }

        $upload_dir = './uploads/rekomendasi_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $form_data = [];
        $allowed_ext_array = ['pdf', 'docx', 'doc'];

        if (!empty($option['fields'])) {
            foreach ($option['fields'] as $f) {
                $fkey = $f['field_key'];
                if ($f['field_type'] == 'file') {
                    if (isset($_FILES[$fkey]) && $_FILES[$fkey]['error'] == UPLOAD_ERR_OK) {
                        $file_ext = strtolower(pathinfo($_FILES[$fkey]['name'], PATHINFO_EXTENSION));
                        if (!in_array($file_ext, $allowed_ext_array)) {
                            $res = ['status' => 'error', 'message' => 'Format file "' . $f['field_label'] . '" harus PDF atau DOCX.'];
                            if ($this->input->is_ajax_request()) { echo json_encode($res); exit; }
                            $this->session->set_flashdata('error', $res['message']);
                            redirect('mahasiswa/bimbingan');
                        }

                        $new_filename = 'rekomen_' . $fkey . '_' . $nim . '_' . time() . '.' . $file_ext;
                        $target_path = $upload_dir . $new_filename;
                        if (move_uploaded_file($_FILES[$fkey]['tmp_name'], $target_path)) {
                            $form_data[$fkey] = [
                                'label' => $f['field_label'],
                                'file' => base_url('uploads/rekomendasi_ta/' . $new_filename),
                                'original_name' => $_FILES[$fkey]['name']
                            ];
                        }
                    } else if ($f['is_required']) {
                        $res = ['status' => 'error', 'message' => 'Berkas "' . $f['field_label'] . '" wajib diunggah.'];
                        if ($this->input->is_ajax_request()) { echo json_encode($res); exit; }
                        $this->session->set_flashdata('error', $res['message']);
                        redirect('mahasiswa/bimbingan');
                    }
                } else {
                    $form_data[$fkey] = [
                        'label' => $f['field_label'],
                        'val' => $this->input->post($fkey)
                    ];
                }
            }
        }

        $catatan_alasan = $this->input->post('catatan_alasan') ?: ($this->input->post('tanggapan') ?: '');

        $data_sub = [
            'nim' => $nim,
            'id_preview' => $id_preview,
            'recommendation_type' => 'non_sidang',
            'jalur_id' => $option['id'],
            'jalur_title' => $option['title'],
            'form_data_json' => json_encode($form_data),
            'catatan_dosen' => $catatan_alasan,
            'status' => 'Submitted',
            'created_by' => $user_id
        ];

        $sub_id = $this->Rekomendasi_model->save_submission($data_sub);

        if ($this->db->table_exists('pendaftaran_ta')) {
            $update_ta = ['current_stage' => 'Rekomendasi Non-Sidang (' . $option['title'] . ')'];
            if ($this->db->field_exists('status_sidang', 'pendaftaran_ta')) {
                $update_ta['status_sidang'] = 'Non-Sidang (' . $option['title'] . ')';
            }
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $update_ta);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Rekomendasi Non-Sidang (' . $option['title'] . ') dan berkas persyaratan berhasil disimpan!']);
            exit;
        }

        $this->session->set_flashdata('success', 'Rekomendasi Non-Sidang (' . $option['title'] . ') berhasil disimpan!');
        redirect('mahasiswa/bimbingan');
    }

    public function ajax_get_all_rekomen_crud() {
        $this->load->model('Rekomendasi_model');
        $options = $this->Rekomendasi_model->get_all_options();
        echo json_encode(['status' => 'success', 'data' => $options]);
        exit;
    }

    public function ajax_save_rekomen_option() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        $data = [
            'category' => $this->input->post('category') ?: 'non_sidang',
            'code' => strtolower(url_title($this->input->post('title'), '_', true)) . '_' . time(),
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'icon_type' => 'bi',
            'icon_class' => $this->input->post('icon_class') ?: 'bi-award-fill',
            'is_active' => $this->input->post('is_active') !== null ? (int)$this->input->post('is_active') : 1,
            'sort_order' => (int)($this->input->post('sort_order') ?: 0)
        ];

        if ($id) {
            unset($data['code']);
        }

        $res_id = $this->Rekomendasi_model->save_option($data, $id);
        echo json_encode(['status' => 'success', 'message' => 'Jalur berhasil disimpan!', 'id' => $res_id]);
        exit;
    }

    public function ajax_delete_rekomen_option() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        if ($id) {
            $this->Rekomendasi_model->delete_option($id);
            echo json_encode(['status' => 'success', 'message' => 'Jalur berhasil dihapus!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
        exit;
    }

    public function ajax_delete_rekomen_category() {
        $this->load->model('Rekomendasi_model');
        $category = $this->input->post('category');
        if ($category) {
            $this->Rekomendasi_model->delete_category($category);
            echo json_encode(['status' => 'success', 'message' => 'Tab Kategori beserta seluruh isinya berhasil dihapus!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'Kategori tidak ditemukan']);
        exit;
    }

    public function ajax_save_rekomen_field() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        $data = [
            'jalur_id' => $this->input->post('jalur_id'),
            'field_key' => $this->input->post('field_key') ?: strtolower(url_title($this->input->post('field_label'), '_', true)),
            'field_label' => $this->input->post('field_label'),
            'field_type' => $this->input->post('field_type') ?: 'file',
            'allowed_ext' => $this->input->post('allowed_ext') ?: 'pdf,docx,doc',
            'is_required' => (int)($this->input->post('is_required') ?: 0),
            'help_text' => $this->input->post('help_text') ?: 'Format file diharuskan pdf/docx',
            'sort_order' => (int)($this->input->post('sort_order') ?: 0)
        ];

        $res_id = $this->Rekomendasi_model->save_field($data, $id);
        echo json_encode(['status' => 'success', 'message' => 'Field persyaratan berhasil disimpan!', 'id' => $res_id]);
        exit;
    }

    public function ajax_delete_rekomen_field() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        if ($id) {
            $this->Rekomendasi_model->delete_field($id);
            echo json_encode(['status' => 'success', 'message' => 'Field berhasil dihapus!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'ID field tidak ditemukan']);
        exit;
    }

    // =========================================================
    // TICKETING
    // =========================================================

    private function _get_dynamic_unit_kategori_map() {
        $map = [];
        if ($this->db->table_exists('ticketing_units')) {
            $units = $this->db
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('ticketing_units')
                ->result_array();

            foreach ($units as $u) {
                $categories = [];
                if ($this->db->table_exists('ticketing_kategori')) {
                    $categories = $this->db
                        ->where('unit_id', $u['id'])
                        ->where('is_active', 1)
                        ->order_by("(CASE WHEN nama_kategori LIKE 'Lain-lain%' OR nama_kategori LIKE 'Lainnya%' THEN 1 ELSE 0 END)", 'ASC', FALSE)
                        ->order_by('sort_order', 'ASC')
                        ->order_by('id', 'ASC')
                        ->get('ticketing_kategori')
                        ->result_array();
                }

                $catList = array_column($categories, 'nama_kategori');
                if (empty($catList)) {
                    $catList = ['Lain-lain (' . $u['nama_unit'] . ')'];
                }
                $map[$u['nama_unit']] = $catList;
            }
        }

        if (empty($map)) {
            $map = [
                'Laboratorium Bengkel dan Studio' => [
                    'Fasilitas Ruangan / AC / Proyektor Lab',
                    'Perangkat Komputer / Hardware / Monitor',
                    'Drawing Tablet / Display Cintiq / Alat Digital',
                    'Software / Lisensi Aplikasi Praktikum & Studio',
                    'Jaringan & Koneksi Internet Lab',
                    'Peralatan & Bahan Praktikum Bengkel / Studio',
                    'Peminjaman & Pemeliharaan Alat Lab',
                    'Lain-lain (Laboratorium Bengkel dan Studio)'
                ],
                'Layanan Akademik - LAA' => [
                    'Surat Keterangan Mahasiswa / Pengantar Kuliah',
                    'Administrasi Nilai, KSM & Transkrip Akademik',
                    'Registrasi Mata Kuliah / Perubahan KRS',
                    'Pengajuan Cuti Akademik / Aktif Kembali',
                    'Jadwal Kuliah & Ruang Ujian (UTS/UAS)',
                    'Legalisir Ijazah, Transkrip & KTM',
                    'Lain-lain (Layanan Akademik - LAA)'
                ],
                'Kemahasiswaan' => [
                    'Beasiswa & Bantuan Finansial Mahasiswa',
                    'Kegiatan Ormawa, UKM & Himpunan Mahasiswa',
                    'Lomba, Kompetisi & Pencatatan Prestasi Mahasiswa',
                    'Surat Rekomendasi & Izin Kegiatan Mahasiswa',
                    'Pembinaan Karakter, Konseling & Tata Tertib',
                    'Asuransi & Kesejahteraan Mahasiswa',
                    'Lain-lain (Kemahasiswaan)'
                ],
                'Sekretariat' => [
                    'Penerbitan Surat Keputusan (SK) Dekanat',
                    'Pengesahan & Tanda Tangan Dokumen Pimpinan',
                    'Administrasi Persuratan Masuk & Keluar Fakultas',
                    'Disposisi & Permohonan Informasi Resmi',
                    'Peminjaman Fasilitas Rapat / Ruang Dekanat',
                    'Lain-lain (Sekretariat)'
                ],
                'SDM dan Keuangan' => [
                    'Pembayaran BPP / Registrasi Keuangan Kuliah',
                    'Dispensasi & Penyesuaian Tagihan Biaya Kuliah',
                    'Konfirmasi & Verifikasi Bukti Pembayaran',
                    'Administrasi Penggajian, Honor & Klaim (SDM)',
                    'Berkas Kepegawaian & Verifikasi Dosen / Tendik',
                    'Anggaran Operasional & Pengadaan Sarana Unit',
                    'Lain-lain (SDM dan Keuangan)'
                ],
                'Program Studi (DKV, DI, DP, KTF, SR, Film dan Animasi)' => [
                    'Kurikulum, Silabus & Mata Kuliah Program Studi',
                    'Bimbingan Akademik / Dosen Pembimbing Prodi',
                    'Pendaftaran, Seminar Proposal & Sidang TA / Skripsi',
                    'Pengajuan / Perubahan Judul & Pembimbing TA',
                    'Konversi SKS Magang / MBKM / Kerja Praktik (KP)',
                    'Surat Rekomendasi / Keterangan Program Studi',
                    'Lain-lain (Program Studi)'
                ]
            ];
        }

        return $map;
    }

    public function ticketing_input() {
        $nim = $this->_get_current_nim();
        $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);

        $userId = $this->session->userdata('user_id');
        $nama = !empty($mhs['nama_depan']) ? trim($mhs['nama_depan'] . ' ' . ($mhs['nama_belakang'] ?? '')) : ($this->session->userdata('name') ?: 'Mahasiswa');
        $email = $this->session->userdata('email');

        $unit_kategori_map = $this->_get_dynamic_unit_kategori_map();

        $custom_fields = [];
        if ($this->db->table_exists('laboran_ticketing_fields')) {
            $custom_fields = $this->db
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('laboran_ticketing_fields')
                ->result_array();
        }

        $data = [
            'title'             => 'Buat Tiket Kendala Mahasiswa — IFIK Portal',
            'active_menu'       => 'ticketing_input',
            'user'              => [
                'id'    => $userId,
                'nim'   => $nim,
                'nama'  => $nama,
                'email' => $email
            ],
            'mahasiswa'         => $mhs,
            'custom_fields'     => $custom_fields,
            'penerima_list'     => [
                'Laboran'    => [
                    'id'    => 'Laboran',
                    'title' => 'Laboran',
                    'desc'  => 'Fasilitas Lab, Hardware, Software, Jaringan & Sarpras',
                    'icon'  => 'bi-pc-display-horizontal'
                ],
                'Kaur'       => [
                    'id'    => 'Kaur',
                    'title' => 'Kaur (Kepala Urusan)',
                    'desc'  => 'Kepala Urusan, Fasilitas Akademik, Perkuliahan & Pengesahan',
                    'icon'  => 'bi-person-badge'
                ],
                'Admin LAA'  => [
                    'id'    => 'Admin LAA',
                    'title' => 'Admin LAA',
                    'desc'  => 'Layanan Akademik, Surat Pengantar, Ijazah & KTM',
                    'icon'  => 'bi-building-check'
                ]
            ],
            'unit_kategori_map' => $unit_kategori_map,
            'prioritas_list'    => [
                'Rendah'  => ['label' => 'Rendah', 'color' => 'slate', 'desc' => 'Pertanyaan umum / kendala minor'],
                'Sedang'  => ['label' => 'Sedang', 'color' => 'blue', 'desc' => 'Kendala kerja rutin tanpa hambatan fatal'],
                'Tinggi'  => ['label' => 'Tinggi', 'color' => 'amber', 'desc' => 'Proses tertunda, butuh respon cepat'],
                'Darurat' => ['label' => 'Darurat', 'color' => 'rose', 'desc' => 'Sistem kritis / jadwal mendesak hari ini']
            ],
            'form_action'       => site_url('mahasiswa/ticketing/simpan')
        ];

        $this->load->view('mahasiswa/ticketing_input', $data);
    }

    public function ticketing_simpan() {
        $nim = $this->_get_current_nim();
        $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);

        $userId      = $this->session->userdata('user_id');
        $namaDefault = !empty($mhs['nama_depan']) ? trim($mhs['nama_depan'] . ' ' . ($mhs['nama_belakang'] ?? '')) : ($this->session->userdata('name') ?: 'Mahasiswa');
        $namaLengkap = trim($this->input->post('nama_lengkap', true)) ?: $namaDefault;
        $email       = $this->session->userdata('email') ?: ($mhs['email'] ?? '');

        $unit_tujuan      = trim($this->input->post('unit_tujuan', true)) ?: (trim($this->input->post('tujuan_penerima', true)) ?: trim($this->input->post('unit_terkait', true)));
        if (empty($unit_tujuan)) {
            $unit_tujuan = 'Laboratorium Bengkel dan Studio';
        }
        $sub_prodi = trim($this->input->post('sub_prodi', true) ?? '');
        if (!empty($sub_prodi) && (strpos($unit_tujuan, 'Program Studi') !== false || strpos($unit_tujuan, 'Prodi') !== false)) {
            $unit_tujuan = "Program Studi - {$sub_prodi}";
        }
        $tujuan_penerima  = $unit_tujuan;
        $unit_terkait     = $unit_tujuan;
        $kategori         = trim($this->input->post('kategori', true));
        $kategori_lainnya = trim($this->input->post('kategori_lainnya', true));
        $prioritas        = trim($this->input->post('prioritas', true));
        $subjek           = trim($this->input->post('subjek', true));
        $deskripsi        = $this->input->post('deskripsi');

        $textOnly = trim(strip_tags($deskripsi));
        if (empty($namaLengkap) || empty($unit_tujuan) || empty($kategori) || empty($subjek) || empty($textOnly)) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Semua field bertanda bintang (*) wajib diisi.']);
                return;
            }
            $this->session->set_flashdata('error', 'Semua field bertanda bintang (*) wajib diisi.');
            redirect('mahasiswa/ticketing/input');
            return;
        }

        $this->load->model('DosenTicketing_model');
        $recentTickets = $this->DosenTicketing_model->get_tickets($userId, $nim);
        $recentTicket = null;
        if (!empty($recentTickets)) {
            $latest = $recentTickets[0];
            if ($latest->subjek === $subjek && (time() - strtotime($latest->created_at)) <= 5) {
                $recentTicket = $latest;
            }
        }

        if ($recentTicket) {
            $msg = "Tiket Anda berhasil diajukan dengan Kode: <b>{$recentTicket->kode_tiket}</b> ditujukan kepada <b>" . htmlspecialchars($tujuan_penerima) . "</b>.";
            $this->session->set_flashdata('success', $msg);
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $recentTicket->kode_tiket,
                    'message'      => $msg,
                    'redirect_url' => site_url('mahasiswa/ticketing/riwayat')
                ]);
                return;
            }
            redirect('mahasiswa/ticketing/riwayat');
            return;
        }

        if (preg_match('/lain/i', $kategori)) {
            if (!empty($kategori_lainnya)) {
                $kategori = $kategori . ': ' . $kategori_lainnya;
            } else {
                $err = 'Silakan tulis rincian topik kendala pada input "Detail Kategori Lainnya".';
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => $err]);
                    return;
                }
                $this->session->set_flashdata('error', $err);
                redirect('mahasiswa/ticketing/input');
                return;
            }
        }

        $lampiran_name = null;
        if (!empty($_FILES['lampiran']['name'])) {
            $uploadPath = FCPATH . 'uploads/ticketing/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $config['upload_path']   = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|zip|rar';
            $config['max_size']      = 5120;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('lampiran')) {
                $uploadData = $this->upload->data();
                $lampiran_name = $uploadData['file_name'];
            } else {
                $uploadError = $this->upload->display_errors('', '');
                $err = 'Gagal mengunggah lampiran: ' . $uploadError;
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => $err]);
                    return;
                }
                $this->session->set_flashdata('error', $err);
                redirect('mahasiswa/ticketing/input');
                return;
            }
        }

        $this->load->model('DosenTicketing_model');
        $kodeTiket = $this->DosenTicketing_model->generate_kode();

        $ticketData = [
            'kode_tiket'      => $kodeTiket,
            'id_user'         => $userId ?: $nim,
            'nama_dosen'      => $namaLengkap,
            'nama'            => $namaLengkap,
            'email'           => $email,
            'nidn'            => $nim,
            'tujuan_penerima' => $tujuan_penerima,
            'unit_terkait'    => $unit_terkait,
            'unit_tujuan'     => $unit_terkait,
            'kategori'        => $kategori,
            'prioritas'       => in_array($prioritas, ['Rendah', 'Sedang', 'Tinggi', 'Darurat']) ? $prioritas : 'Sedang',
            'subjek'          => $subjek,
            'deskripsi'       => $deskripsi,
            'lampiran'        => $lampiran_name,
            'status'          => 'Menunggu'
        ];

        $insertedId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertedId) {
            $msg = "Tiket kendala berhasil diajukan dengan Kode: <b>{$kodeTiket}</b> ditujukan kepada <b>" . htmlspecialchars($unit_tujuan) . "</b>. Mohon pantau status respon secara berkala.";
            $this->session->set_flashdata('success', $msg);
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $kodeTiket,
                    'message'      => $msg,
                    'redirect_url' => site_url('mahasiswa/ticketing/riwayat')
                ]);
                return;
            }
            redirect('mahasiswa/ticketing/riwayat');
        } else {
            $err = 'Terjadi kesalahan sistem saat menyimpan tiket. Silakan coba beberapa saat lagi.';
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $err]);
                return;
            }
            $this->session->set_flashdata('error', $err);
            redirect('mahasiswa/ticketing/input');
        }
    }

    public function ticketing_riwayat() {
        $nim = $this->_get_current_nim();
        $userId = $this->session->userdata('user_id');

        $this->load->model('DosenTicketing_model');
        $tickets = $this->DosenTicketing_model->get_tickets($userId, $nim);
        $stats   = $this->DosenTicketing_model->get_stats($userId, $nim);

        $data = [
            'title'       => 'Riwayat Tiket Kendala Saya — Mahasiswa IFIK',
            'active_menu' => 'ticketing_riwayat',
            'tickets'     => $tickets,
            'stats'       => $stats,
            'nim'         => $nim
        ];

        $this->load->view('mahasiswa/ticketing_riwayat', $data);
    }

    public function ticketing_detail($id_or_kode) {
        $this->load->model('DosenTicketing_model');
        $ticket = $this->DosenTicketing_model->get_by_id($id_or_kode);

        if (!$ticket) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Tiket tidak ditemukan.'
                ]));
        }

        $userId = $this->session->userdata('user_id');
        $nim   = $this->_get_current_nim();
        $roleId = (int)$this->session->userdata('role_id');

        if ($roleId !== 1 && $ticket->id_user != $userId && ($ticket->nidn ?? '') != $nim) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk melihat tiket ini.'
                ]));
        }

        $isHtml = (strpos($ticket->deskripsi, '<') !== false && strpos($ticket->deskripsi, '>') !== false);
        $deskripsiFormatted = $isHtml ? $ticket->deskripsi : nl2br(htmlspecialchars($ticket->deskripsi));

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'              => $ticket->id,
                    'kode_tiket'      => $ticket->kode_tiket,
                    'nama_dosen'      => $ticket->nama_dosen,
                    'nidn'            => $ticket->nidn ?? '-',
                    'label_identitas' => $ticket->label_identitas ?? 'NIM',
                    'tujuan_penerima' => $ticket->tujuan_penerima ?? 'Laboran',
                    'unit_terkait'    => $ticket->unit_terkait ?? ($ticket->unit_tujuan ?: 'Layanan IFIK'),
                    'unit_tujuan'     => $ticket->unit_terkait ?? ($ticket->unit_tujuan ?: 'Layanan IFIK'),
                    'kategori'        => $ticket->kategori,
                    'prioritas'       => $ticket->prioritas,
                    'subjek'          => $ticket->subjek,
                    'deskripsi'       => $deskripsiFormatted,
                    'lampiran'        => $ticket->lampiran,
                    'lampiran_url'    => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                    'status'          => $ticket->status,
                    'tanggapan'       => $ticket->tanggapan ? nl2br(htmlspecialchars($ticket->tanggapan)) : null,
                    'catatan_proses'  => !empty($ticket->catatan_proses) ? nl2br(htmlspecialchars($ticket->catatan_proses)) : null,
                    'catatan_selesai' => !empty($ticket->catatan_selesai) ? nl2br(htmlspecialchars($ticket->catatan_selesai)) : null,
                    'catatan_tutup'   => !empty($ticket->catatan_tutup) ? nl2br(htmlspecialchars($ticket->catatan_tutup)) : null,
                    'tgl_diproses'    => !empty($ticket->tgl_diproses) ? date('d M Y H:i', strtotime($ticket->tgl_diproses)) : null,
                    'tgl_closed'      => !empty($ticket->tgl_closed) ? date('d M Y H:i', strtotime($ticket->tgl_closed)) : null,
                    'tgl_tanggapan'   => $ticket->tgl_tanggapan ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                    'created_at'      => date('d M Y H:i', strtotime($ticket->created_at)),
                    'updated_at'      => date('d M Y H:i', strtotime($ticket->updated_at))
                ]
            ]));
    }
        // =========================================================
    // PENILAIAN SIDANG (Preview 4) — P1 / P2 / U1 / U2
    // =========================================================

    /**
     * GET ?nim=xxx
     * Ambil detail nilai sidang + jadwal + dosen untuk modal.
     */
    public function get_detail_nilai_sidang_ajax() {
        header('Content-Type: application/json');

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        if (empty($nim)) {
            echo json_encode(['status' => false, 'message' => 'NIM wajib diisi.']);
            return;
        }

        $detail = $this->Mahasiswa_model->get_detail_nilai_sidang($nim);
        if (!$detail) {
            echo json_encode(['status' => false, 'message' => 'Data mahasiswa tidak ditemukan.']);
            return;
        }

        echo json_encode(['status' => true, 'data' => $detail]);
    }

    /**
     * POST: nim, posisi (1=P1, 2=P2, 3=U1, 4=U2), nilai_akhir, detail_penilaian (JSON), catatan
     */
    public function simpan_nilai_sidang_ajax() {
        header('Content-Type: application/json');

        $nim         = $this->input->post('nim');
        $posisi      = (int)$this->input->post('posisi');
        $nilai       = $this->input->post('nilai_akhir');
        $catatan     = $this->input->post('catatan');
        $detail_raw  = $this->input->post('detail_penilaian');

        if (empty($nim) || !in_array($posisi, [1, 2, 3, 4])) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap atau posisi tidak valid.']);
            return;
        }

        if ($nilai === null || $nilai === '' || !is_numeric($nilai)) {
            echo json_encode(['status' => false, 'message' => 'Nilai akhir wajib diisi (0-100).']);
            return;
        }

        $detail = null;
        if (!empty($detail_raw)) {
            $detail = is_string($detail_raw) ? json_decode($detail_raw, true) : $detail_raw;
        }

        $res = $this->Mahasiswa_model->save_nilai_sidang_by_posisi(
            $nim,
            $posisi,
            (float)$nilai,
            (string)$catatan,
            $detail
        );

        echo json_encode($res);
    }

    // =========================================================
// PERSYARATAN SIDANG (DINAMIS)
// =========================================================

public function get_master_syarat_sidang_ajax() {
    header('Content-Type: application/json');
    $nim = $this->_get_current_nim();

    $this->load->model('AdminLayanan_model');
    $master   = $this->AdminLayanan_model->get_master_syarat_sidang();
    $uploaded = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($nim);

    echo json_encode([
        'status'   => true,
        'master'   => $master,
        'uploaded' => $uploaded
    ]);
}

public function upload_berkas_sidang_ajax() {
    header('Content-Type: application/json');
    $nim = $this->_get_current_nim();

    if (empty($_FILES['berkas']['name'])) {
        echo json_encode(['status' => false, 'message' => 'Tidak ada berkas yang dipilih.']);
        return;
    }

    $upload_dir = './uploads/persyaratan_ta/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $this->load->model('AdminLayanan_model');
    $this->load->library('upload');

    $uploaded_count = 0;
    $errors = [];

    foreach ($_FILES['berkas']['name'] as $kode => $origName) {
        if (empty($origName) || $_FILES['berkas']['error'][$kode] !== UPLOAD_ERR_OK) continue;

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $new_name = $kode . '_' . $nim . '_' . time() . '_' . rand(100,999) . '.' . $ext;

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
            'max_size'      => 5120,
            'file_name'     => $new_name,
        ];

        $this->upload->initialize($config);

        $_FILES['userfile'] = [
            'name'     => $_FILES['berkas']['name'][$kode],
            'type'     => $_FILES['berkas']['type'][$kode],
            'tmp_name' => $_FILES['berkas']['tmp_name'][$kode],
            'error'    => $_FILES['berkas']['error'][$kode],
            'size'     => $_FILES['berkas']['size'][$kode],
        ];

        if ($this->upload->do_upload('userfile')) {
            $fd = $this->upload->data();
            // Simpan ke pendaftaran_berkas + auto-sync ke file_pendaftaran
            $this->AdminLayanan_model->save_student_berkas(
                $nim, $kode, $fd['file_name'], 'Pending', null, ''
            );
            $uploaded_count++;
        } else {
            $errors[] = $kode . ': ' . $this->upload->display_errors('', '');
        }
    }

    if ($uploaded_count > 0) {
        echo json_encode([
            'status'  => true,
            'message' => $uploaded_count . ' berkas berhasil diunggah & menunggu verifikasi LAA.',
            'errors'  => $errors
        ]);
    } else {
        echo json_encode([
            'status'  => false,
            'message' => 'Gagal: ' . implode('; ', $errors)
        ]);
    }
}


    // =========================================================================
    // SECTION 2: FITUR & METHOD DOSEN WALI
    // =========================================================================

private function _get_current_nip() {
        $userId = $this->session->userdata('user_id');
        if ($userId && $this->db->table_exists('user')) {
            $u = $this->db->get_where('user', ['id' => $userId])->row_array();
            if ($u) {
                if (!empty($u['nip'])) return trim($u['nip']);
                if (!empty($u['username'])) return trim($u['username']);
                if (!empty($u['nim'])) return trim($u['nim']);
            }
        }
        $nip = $this->session->userdata('nip') ?: ($this->session->userdata('nidn_nim') ?: ($this->session->userdata('username') ?: ($this->session->userdata('nim') ?: '')));
        return trim((string)$nip);
    }

    // Dashboard Dosen Wali: Daftar Mahasiswa Bimbingan Akademik
    public function dosen_wali() {
        $nip_dosen = $this->_get_current_nip();
        if (empty($nip_dosen)) {
            $this->session->set_flashdata('error', 'Identitas NIP/NIDN Dosen tidak ditemukan pada profil akun Anda.');
            redirect('dashboard');
            return;
        }
        $this->load->model('AdminLayanan_model');
        $data['title'] = 'Dashboard Dosen Wali';
        $data['dosen_info'] = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['list_mahasiswa'] = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);
        $data['syarat_berkas'] = $this->AdminLayanan_model->get_active_syarat_berkas();

        $this->load->view('dosen_wali/dashboard', $data);
    }

    private function _is_stage_locked($nim) {
        $detail = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);
        if (!$detail) return false;
        $current_stage = $detail['current_stage'] ?? 'Dosen Wali';
        $status_wali = $detail['status_approval_wali'] ?? 'Pending';
        return ($status_wali === 'Approved' || in_array($current_stage, ['Admin Layanan', 'Koordinator TA', 'Ketua KK', 'Selesai Approval', 'Selesai']));
    }

    // Detail Mahasiswa Bimbingan & Approval
    public function detail_mahasiswa($nim) {
        $nip_dosen = $this->_get_current_nip();
        $this->load->model('AdminLayanan_model');

        $data['title']          = 'Detail Mahasiswa & Approval Pendaftaran TA';
        $data['nim']            = $nim;
        $data['dosen_info']     = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['detail']         = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);
        if (empty($data['detail'])) {
            $data['detail'] = [
                'id'                     => $nim,
                'nim'                    => $nim,
                'nama_depan'             => 'Mahasiswa ' . $nim,
                'nama_belakang'          => '',
                'mhs_konsentrasi'        => 'Desain Komunikasi Visual',
                'prodi'                  => 'Desain Komunikasi Visual',
                'email'                  => '-',
                'no_hp'                  => '-',
                'judul_1'                => 'Usulan Judul Tugas Akhir',
                'judul_2'                => '',
                'judul_3'                => '',
                'judul_en'               => '',
                'jenis_ta'               => 'Pengkaryaan',
                'status_judul'           => 'Pending',
                'catatan_judul'          => '',
                'status_approval_wali'   => 'Pending',
                'status_approval_admin'  => 'Pending',
                'status_approval_koor'   => 'Pending',
                'status_approval_kk'     => 'Pending',
                'current_stage'          => 'Dosen Wali',
                'tgl_daftar'             => date('Y-m-d H:i:s'),
                'created_at'             => date('Y-m-d H:i:s'),
                'file_ksm'               => '',
                'status_file_ksm'        => 'Pending',
                'catatan_file_ksm'       => '',
                'file_transkrip'         => '',
                'status_file_transkrip'  => 'Pending',
                'catatan_file_transkrip' => '',
                'file_pernyataan'        => '',
                'status_file_pernyataan' => 'Pending',
                'catatan_file_pernyataan'=> '',
                'file_bebas_lab'         => '',
                'status_file_bebas_lab'  => 'Pending',
                'catatan_file_bebas_lab' => '',
                'berkas_map'             => [],
                'total_berkas'           => 0
            ];
        }
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['student_berkas'] = $this->AdminLayanan_model->get_student_berkas_map($nim);

        if ($this->input->post('action')) {
            if ($this->_is_stage_locked($nim)) {
                $this->session->set_flashdata('error', 'Pendaftaran mahasiswa ini telah disetujui dan saat ini berada di tahap selanjutnya, sehingga tidak dapat diubah lagi.');
                redirect('dosen/wali/detail_mahasiswa/' . $nim);
                return;
            }

            $action  = strtolower($this->input->post('action'));
            $status  = $this->input->post('status'); // 'Approved' atau 'Rejected'
            if (empty($status)) {
                $status = ($action === 'approve') ? 'Approved' : (($action === 'reject') ? 'Rejected' : 'Pending');
            }
            $catatan = trim($this->input->post('catatan_wali') ?? '');

            if ($status === 'Rejected') {
                if (empty($catatan) && empty($this->input->post('berkas_kurang')) && ($this->input->post('status_judul_jenis') !== 'Rejected')) {
                    $this->session->set_flashdata('error', 'Alasan penolakan / catatan revisi wajib diisi jika memilih Reject!');
                    redirect('dosen/wali/detail_mahasiswa/' . $nim);
                    return;
                }
                $submitted_kurang = $this->input->post('berkas_kurang') ?: array();
                $submitted_notes  = $this->input->post('catatan_berkas') ?: array();
                foreach ($submitted_kurang as $bk) {
                    if (empty(trim($submitted_notes[$bk] ?? ''))) {
                        $this->session->set_flashdata('error', 'Catatan revisi untuk setiap berkas yang ditandai Kurang/Revisi wajib diisi!');
                        redirect('dosen/wali/detail_mahasiswa/' . $nim);
                        return;
                    }
                }
                if ($this->input->post('status_judul_jenis') === 'Rejected') {
                    $note_jj = trim($this->input->post('catatan_judul_jenis') ?? '');
                    if (empty($note_jj)) {
                        $this->session->set_flashdata('error', 'Catatan revisi untuk Usulan Judul & Skema TA wajib diisi jika ditandai Kurang/Revisi!');
                        redirect('dosen/wali/detail_mahasiswa/' . $nim);
                        return;
                    }
                }
            }

            // Simpan status per berkas jika dikirim melalui form
            $berkas_valid_arr  = $this->input->post('berkas_valid') ?: array();
            $berkas_kurang_arr = $this->input->post('berkas_kurang') ?: array();
            $catatan_berkas    = $this->input->post('catatan_berkas') ?: array();
            $semua_berkas      = !empty($data['syarat_berkas']) ? array_column($data['syarat_berkas'], 'kode_berkas') : array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');

            foreach ($semua_berkas as $bk) {
                if (in_array($bk, $berkas_kurang_arr)) {
                    $note = trim($catatan_berkas[$bk] ?? '');
                    $this->DosenWali_model->update_file_approval($nim, $bk, 'Rejected', $note);
                } else if (in_array($bk, $berkas_valid_arr) || $status === 'Approved') {
                    $this->DosenWali_model->update_file_approval($nim, $bk, 'Approved', '');
                } else {
                    $this->DosenWali_model->update_file_approval($nim, $bk, 'Pending', '');
                }
            }

            $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);

            // Simpan status usulan Judul & Skema TA (Disatukan) - Dijalankan setelah update_approval_wali agar tidak tertimpa!
            $status_judul_jenis  = $this->input->post('status_judul_jenis');
            $catatan_judul_jenis = trim($this->input->post('catatan_judul_jenis') ?? '');

            if ($status_judul_jenis === 'Approved' || ($status === 'Approved' && $status_judul_jenis !== 'Rejected')) {
                $this->DosenWali_model->approve_jenis_ta($nim, 'Approved', '');
                $this->DosenWali_model->update_judul_approval($nim, 'Approved', '');
            } else if ($status_judul_jenis === 'Rejected') {
                $this->DosenWali_model->approve_jenis_ta($nim, 'Rejected', $catatan_judul_jenis);
                $this->DosenWali_model->update_judul_approval($nim, 'Rejected', $catatan_judul_jenis);
            }

            // Record Approval History Log
            $this->load->model('Approval_log_model');
            $mhs_name = trim(($data['detail']['nama_depan'] ?? '') . ' ' . ($data['detail']['nama_belakang'] ?? ''));
            $this->Approval_log_model->log(array(
                'modul'       => 'Dosen Wali',
                'ref_id'      => $nim,
                'target_name' => $mhs_name,
                'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                'catatan'     => $catatan
            ));

            $this->session->set_flashdata('success', 'Status approval pendaftaran TA berhasil diperbarui!');
            redirect('dosen/wali/detail_mahasiswa/' . $nim);
            return;
        }

        $this->load->view('dosen_wali/detail_mahasiswa', $data);
    }

    // AJAX Endpoint: Log ketika Dosen Wali membuka/melihat PDF berkas
    public function log_review_ajax() {
        $nim = $this->input->post('nim') ?: $this->input->get('nim');
        $file_type = $this->input->post('file_type') ?: $this->input->get('file_type');

        if (!$nim || !$file_type) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        // Support array atau comma-separated berkas (batch logging)
        if (is_string($file_type) && strpos($file_type, ',') !== false) {
            $file_type = explode(',', $file_type);
        }

        if (is_array($file_type)) {
            foreach ($file_type as $ft) {
                $ft = trim($ft);
                if (!empty($ft)) {
                    $this->DosenWali_model->log_file_review($nim, $ft);
                }
            }
            echo json_encode(array(
                'success' => true,
                'message' => 'Semua berkas telah ditinjau/direview.'
            ));
            return;
        }

        $res = $this->DosenWali_model->log_file_review($nim, $file_type);
        echo json_encode(array(
            'success' => $res,
            'file_type' => $file_type,
            'message' => 'Berkas ' . strtoupper($file_type) . ' telah ditinjau/direview.'
        ));
    }

    // AJAX Endpoint: Update status per-file (Approved / Rejected)
    public function update_file_approval_ajax() {
        $nim = $this->input->post('nim');
        $file_type = $this->input->post('file_type');
        $status = $this->input->post('status');
        $comment = trim($this->input->post('comment') ?? '');

        if (!$nim || !$file_type || !$status) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.')));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.')));
            return;
        }

        $res = $this->DosenWali_model->update_file_approval($nim, $file_type, $status, $comment);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'success' => (bool)$res,
                'file_type' => $file_type,
                'status' => $status,
                'comment' => $comment,
                'message' => 'Status berkas ' . strtoupper($file_type) . ' berhasil diperbarui ke ' . $status . '.'
            )));
    }

    // AJAX Endpoint: Approve Semua / Tolak Semua Berkas
    public function approve_all_files_ajax() {
        $nim = $this->input->post('nim');
        $status = $this->input->post('status');

        if (!$nim || !$status) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        $res = $this->DosenWali_model->update_all_files_approval($nim, $status);
        echo json_encode(array(
            'success' => $res,
            'status' => $status,
            'message' => 'Semua berkas berhasil diperbarui ke ' . $status . '.'
        ));
    }

    // AJAX Endpoint: Update keseluruhan Approval Dosen Wali
    public function ajax_approval() {
        $nim = $this->input->post('nim');
        $status = $this->input->post('status');
        $catatan = trim($this->input->post('catatan_wali') ?? '');

        if (!$nim || !$status) {
            echo json_encode(array('success' => false, 'message' => 'NIM dan Status wajib diisi!'));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        if ($status === 'Rejected' && empty($catatan)) {
            echo json_encode(array('success' => false, 'message' => 'Catatan revisi wajib diisi jika memilih Reject!'));
            return;
        }

        $res = $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);
        if ($res) {
            $this->load->model('Approval_log_model');
            $mhs = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);
            $mhs_name = trim(($mhs['nama_depan'] ?? '') . ' ' . ($mhs['nama_belakang'] ?? ''));
            $this->Approval_log_model->log(array(
                'modul'       => 'Dosen Wali',
                'ref_id'      => $nim,
                'target_name' => $mhs_name,
                'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                'catatan'     => $catatan
            ));

            echo json_encode(array(
                'success' => true,
                'status' => $status,
                'catatan' => $catatan,
                'message' => 'Persetujuan pendaftaran TA berhasil disimpan!'
            ));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal memperbarui status ke database.'));
        }
    }

    // AJAX Endpoint: Update Keputusan Jenis TA (Status Jenis TA & Catatan/Saran)
    public function update_jenis_approval_ajax() {
        $nim = $this->input->post('nim');
        $status_jenis = $this->input->post('status_jenis_ta') ?? 'Pending';
        $catatan_jenis = trim($this->input->post('catatan_jenis_ta') ?? '');

        if (!$nim) {
            echo json_encode(array('success' => false, 'message' => 'NIM wajib diisi.'));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        $res = $this->DosenWali_model->approve_jenis_ta($nim, $status_jenis, $catatan_jenis);
        echo json_encode(array(
            'success' => $res,
            'status_jenis_ta' => $status_jenis,
            'catatan_jenis_ta' => $catatan_jenis,
            'message' => 'Status Jenis TA berhasil diperbarui ke ' . $status_jenis . '.'
        ));
    }

    // AJAX Endpoint: Update Keputusan Judul TA (Status Judul & Saran/Catatan Revisi)
    public function update_judul_approval_ajax() {
        $nim = $this->input->post('nim');
        $status_judul = $this->input->post('status_judul') ?? 'Pending';
        $catatan_judul = trim($this->input->post('catatan_judul') ?? '');

        if (!$nim) {
            echo json_encode(array('success' => false, 'message' => 'NIM wajib diisi.'));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        $res = $this->DosenWali_model->update_judul_approval($nim, $status_judul, $catatan_judul);
        echo json_encode(array(
            'success' => $res,
            'status_judul' => $status_judul,
            'catatan_judul' => $catatan_judul,
            'message' => 'Status usulan judul Tugas Akhir berhasil diperbarui ke ' . $status_judul . '.'
        ));
    }

    // AJAX Endpoint: Update Keputusan Usulan Judul & Skema TA Sekaligus (Disatukan)
    public function update_judul_jenis_ajax() {
        $nim = $this->input->post('nim');
        $status = $this->input->post('status') ?? 'Pending';
        $catatan = trim($this->input->post('catatan') ?? '');

        if (!$nim || !$status) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        $note = ($status === 'Approved') ? '' : $catatan;
        $res1 = $this->DosenWali_model->approve_jenis_ta($nim, $status, $note);
        $res2 = $this->DosenWali_model->update_judul_approval($nim, $status, $note);

        echo json_encode(array(
            'success' => ($res1 && $res2),
            'status' => $status,
            'catatan' => $note,
            'message' => 'Status Usulan Judul & Skema TA berhasil diperbarui ke ' . $status . '.'
        ));
    }

    // AJAX Endpoint: Realtime fetch daftar mahasiswa bimbingan & statistik status
    public function get_mahasiswa_ajax() {
        $nip_dosen = $this->_get_current_nip();
        if (empty($nip_dosen)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'NIP Dosen tidak terdeteksi.']));
            return;
        }
        $this->load->model('AdminLayanan_model');
        $active_syarat = $this->AdminLayanan_model->get_active_syarat_berkas();
        $list = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);

        $totalMhs = count($list);
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        $formattedList = [];
        foreach ($list as $m) {
            $st = $m['status_approval_wali'] ?? 'Pending';
            if ($st === 'Approved') {
                $approvedCount++;
            } elseif ($st === 'Rejected') {
                $rejectedCount++;
            } else {
                $pendingCount++;
            }

            $nama = trim(($m['nama_depan'] ?? '') . ' ' . ($m['nama_belakang'] ?? ''));
            $item = [
                'nim'                    => $m['nim'],
                'nama'                   => $nama,
                'konsentrasi'            => $m['mhs_konsentrasi'] ?? '',
                'judul'                  => $m['judul_1'] ?? '',
                'judul_1'                => $m['judul_1'] ?? '',
                'judul_2'                => $m['judul_2'] ?? '',
                'judul_3'                => $m['judul_3'] ?? '',
                'judul_en'               => $m['judul_en'] ?? '',
                'jenis_ta'               => $m['jenis_ta'] ?? '',
                'status_judul'           => $m['status_judul'] ?? 'Pending',
                'catatan_judul'          => $m['catatan_judul'] ?? '',
                'status_jenis_ta'        => $m['status_jenis_ta'] ?? 'Pending',
                'catatan_jenis_ta'       => $m['catatan_jenis_ta'] ?? '',
                'status_approval_wali'   => $st,
                'current_stage'          => $m['current_stage'] ?? 'Dosen Wali',
                'detail_url'             => site_url('dosen/wali/detail_mahasiswa/' . $m['nim']),
                'berkas_map'             => $m['berkas_map'] ?? []
            ];

            // Pasang status & file untuk setiap berkas dinamis
            foreach ($m as $k => $v) {
                if (strpos($k, 'file_') === 0 || strpos($k, 'status_file_') === 0 || strpos($k, 'catatan_file_') === 0) {
                    $item[$k] = $v;
                }
            }

            $formattedList[] = $item;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'       => true,
                'syarat_berkas' => $active_syarat,
                'stats'         => [
                    'total'        => $totalMhs,
                    'pending'      => $pendingCount,
                    'approved'     => $approvedCount,
                    'rejected'     => $rejectedCount,
                    'approved_pct' => $totalMhs > 0 ? round(($approvedCount / $totalMhs) * 100) : 0
                ],
                'data'          => $formattedList
            ]));
    }

    // AJAX Endpoint: Ambil data detail beberapa mahasiswa sekaligus untuk Popup Cek Masal Dosen Wali
    public function get_batch_details() {
        $nims = $this->input->post('nims') ?: array();
        if (empty($nims)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Tidak ada NIM terpilih.')));
            return;
        }

        $list = $this->DosenWali_model->get_batch_details_by_nims($nims);
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

            $st_wali = $r['status_approval_wali'] ?? 'Pending';
            $st_jenis = $r['status_jenis_ta'] ?? 'Pending';
            $st_judul = $r['status_judul'] ?? 'Pending';

            $data[] = array(
                'nim'                  => $r['nim'],
                'nama'                 => htmlspecialchars($full_name),
                'prodi'                => htmlspecialchars($r['prodi'] ?? 'Desain Komunikasi Visual'),
                'kode_kk'              => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'nama_kk'              => htmlspecialchars($r['nama_kk'] ?? 'Visual & Communication Media'),
                'jenis_ta'             => htmlspecialchars($r['jenis_ta'] ?? 'Reguler'),
                'status_jenis_ta'      => $st_jenis,
                'catatan_jenis_ta'     => ($st_jenis === 'Rejected') ? htmlspecialchars($r['catatan_jenis_ta'] ?? '') : '',
                'judul'                => htmlspecialchars($r['judul'] ?? ($r['judul_1'] ?? '')),
                'judul_1'              => htmlspecialchars($r['judul_1'] ?? ($r['judul'] ?? '')),
                'judul_2'              => htmlspecialchars($r['judul_2'] ?? ''),
                'judul_3'              => htmlspecialchars($r['judul_3'] ?? ''),
                'judul_en'             => htmlspecialchars($r['judul_en'] ?? ''),
                'status_judul'         => $st_judul,
                'catatan_judul'        => ($st_judul === 'Rejected') ? htmlspecialchars($r['catatan_judul'] ?? '') : '',
                'status_approval_wali' => $st_wali,
                'catatan_wali'         => ($st_wali === 'Rejected') ? htmlspecialchars($r['catatan_wali'] ?? '') : '',
                'current_stage'        => $r['current_stage'] ?? 'Dosen Wali',
                'file_ksm'             => $r['file_ksm'] ?? '',
                'status_file_ksm'      => $r['status_file_ksm'] ?? 'Pending',
                'file_transkrip'       => $r['file_transkrip'] ?? '',
                'status_file_transkrip'=> $r['status_file_transkrip'] ?? 'Pending',
                'file_pernyataan'      => $r['file_pernyataan'] ?? '',
                'status_file_pernyataan'=> $r['status_file_pernyataan'] ?? 'Pending',
                'file_bebas_lab'       => $r['file_bebas_lab'] ?? '',
                'status_file_bebas_lab'=> $r['status_file_bebas_lab'] ?? 'Pending',
                'berkas_map'           => $r['berkas_map'] ?? [],
                'files' => (function() use ($r, $resolve_pdf_url) {
                    $ci =& get_instance();
                    $ci->load->model('AdminLayanan_model');
                    $active_syarat = $ci->AdminLayanan_model->get_active_syarat_berkas();
                    if (empty($active_syarat)) {
                        $active_syarat = [
                            ['kode_berkas' => 'ksm'],
                            ['kode_berkas' => 'transkrip'],
                            ['kode_berkas' => 'pernyataan'],
                            ['kode_berkas' => 'bebas_lab']
                        ];
                    }
                    $fMap = array();
                    foreach ($active_syarat as $asb) {
                        $k = $asb['kode_berkas'];
                        $fMap[$k] = array(
                            'name'   => $r['file_' . $k] ?? ($k . '_' . $r['nim'] . '.pdf'),
                            'url'    => $resolve_pdf_url($r['file_' . $k] ?? ''),
                            'status' => $r['status_file_' . $k] ?? 'Pending',
                            'note'   => (($r['status_file_' . $k] ?? '') === 'Rejected') ? htmlspecialchars($r['catatan_file_' . $k] ?? '') : ''
                        );
                    }
                    return $fMap;
                })()
            );
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array('success' => true, 'data' => $data)));
    }

    // Submit Approval Batch Massal Dosen Wali
    public function submit_batch_approval() {
        $action = $this->input->post('action'); // 'approve_all' atau 'batch_update'
        $nims   = $this->input->post('nims') ?: array();

        if (empty($nims)) {
            $this->session->set_flashdata('error', 'Tidak ada mahasiswa yang dipilih.');
            redirect('dosen/wali');
            return;
        }

        // Filter: hanya proses NIM yang belum locked (belum di Admin LAA ke atas)
        $valid_nims  = array();
        $locked_nims = array();
        foreach ($nims as $nim) {
            if ($this->_is_stage_locked($nim)) {
                $locked_nims[] = $nim;
            } else {
                $valid_nims[] = $nim;
            }
        }

        $msg = "Persetujuan massal berhasil diselesaikan!";

        if ($action === 'approve_all') {
            if (empty($valid_nims)) {
                $msg = "Semua mahasiswa yang dipilih sudah berada di tahap Admin LAA atau lebih lanjut, tidak ada yang diproses.";
            } else {
                $count = $this->DosenWali_model->batch_approve_wali($valid_nims);
                $msg = "Berhasil menyetujui (Approve) $count berkas pendaftaran mahasiswa sekaligus! Pengajuan otomatis diteruskan ke Admin LAA.";
                if (!empty($locked_nims)) {
                    $msg .= " (" . count($locked_nims) . " mahasiswa dilewati karena sudah di tahap selanjutnya.)";
                }
            }
            $this->session->set_flashdata('success', $msg);
        } else if ($action === 'batch_update') {
            $decisions = json_decode($this->input->post('decisions_json') ?? '[]', true);
            // Filter decisions hanya untuk valid_nims
            if (!empty($valid_nims)) {
                $decisions = array_filter($decisions, function($d) use ($valid_nims) {
                    return in_array($d['nim'] ?? '', $valid_nims);
                });
                $decisions = array_values($decisions);
            } else {
                $decisions = array();
            }

            if (empty($decisions)) {
                $msg = "Semua mahasiswa yang dipilih sudah berada di tahap Admin LAA atau lebih lanjut, tidak ada yang diproses.";
            } else {
                $result = $this->DosenWali_model->update_batch_decisions($decisions);
                $count_app = $result['approved'] ?? 0;
                $count_rej = $result['rejected'] ?? 0;
                $msg = "Persetujuan massal selesai! $count_app Mahasiswa Disetujui, $count_rej Mahasiswa Ditolak / Diberi Catatan Revisi.";
                if (!empty($locked_nims)) {
                    $msg .= " (" . count($locked_nims) . " mahasiswa dilewati karena sudah di tahap selanjutnya.)";
                }
            }
            $this->session->set_flashdata('success', $msg);
        }

        if ($this->input->is_ajax_request()) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array(
                     'success' => true,
                     'message' => $msg
                 )));
            return;
        }

        redirect('dosen/wali');
    }

    /**
     * Halaman Pengaturan Tanda Tangan Digital Dosen
     */
    public function tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $dosen_info = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $tanda_tangan = $this->DosenWali_model->get_tanda_tangan($nip_dosen);

        $data = [
            'title'        => 'Pengaturan Tanda Tangan Digital Dosen',
            'dosen_info'   => $dosen_info,
            'nip'          => $nip_dosen,
            'tanda_tangan' => $tanda_tangan
        ];

        $this->load->view('dosen_wali/tanda_tangan', $data);
    }

    /**
     * Simpan Tanda Tangan Digital (via Canvas Base64 atau Upload File)
     */
    public function simpan_tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $tipe = $this->input->post('tipe'); // 'canvas' atau 'upload'

        $uploadDir = FCPATH . 'uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = '';

        if ($tipe === 'canvas') {
            $signatureData = $this->input->post('signature_data');
            if (empty($signatureData) || strpos($signatureData, 'data:image/png;base64,') !== 0) {
                $this->session->set_flashdata('error', 'Silakan goreskan tanda tangan pada canvas terlebih dahulu.');
                redirect('dosen/tanda-tangan');
                return;
            }

            // Decode base64 image
            $imageData = str_replace('data:image/png;base64,', '', $signatureData);
            $imageData = str_replace(' ', '+', $imageData);
            $decoded = base64_decode($imageData);

            if (!$decoded) {
                $this->session->set_flashdata('error', 'Format gambar tanda tangan tidak valid.');
                redirect('dosen/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_dosen);
            $filename = 'ttd_' . $cleanNip . '_' . time() . '.png';
            file_put_contents($uploadDir . $filename, $decoded);

        } else {
            // Upload file
            if (empty($_FILES['file_ttd']['name'])) {
                $this->session->set_flashdata('error', 'Pilih file gambar tanda tangan terlebih dahulu.');
                redirect('dosen/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_dosen);
            $config = [
                'upload_path'   => $uploadDir,
                'allowed_types' => 'png|jpg|jpeg',
                'max_size'      => 3072, // 3MB
                'file_name'     => 'ttd_' . $cleanNip . '_' . time()
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file_ttd')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];
            } else {
                $err = $this->upload->display_errors('', '');
                $this->session->set_flashdata('error', 'Gagal mengunggah file tanda tangan: ' . $err);
                redirect('dosen/tanda-tangan');
                return;
            }
        }

        if (!empty($filename)) {
            // Hapus file tanda tangan lama jika ada
            $oldTtd = $this->DosenWali_model->get_tanda_tangan($nip_dosen);
            if (!empty($oldTtd) && file_exists($uploadDir . $oldTtd)) {
                @unlink($uploadDir . $oldTtd);
            }

            $this->DosenWali_model->save_tanda_tangan($nip_dosen, $filename);
            $this->session->set_flashdata('success', 'Tanda tangan digital Anda berhasil disimpan dan siap digunakan!');
        }

        redirect('dosen/tanda-tangan');
    }

    /**
     * Hapus Tanda Tangan Digital
     */
    public function hapus_tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $oldTtd = $this->DosenWali_model->get_tanda_tangan($nip_dosen);

        if (!empty($oldTtd)) {
            $filePath = FCPATH . 'uploads/signatures/' . $oldTtd;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $this->DosenWali_model->delete_tanda_tangan($nip_dosen);
            $this->session->set_flashdata('success', 'Tanda tangan digital berhasil dihapus.');
        }

        redirect('dosen/tanda-tangan');
    }

    /**
     * Unduh File Tanda Tangan Digital (Format PNG Transparan)
     */
    public function download_tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $dosen_info = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $tanda_tangan = $this->DosenWali_model->get_tanda_tangan($nip_dosen);

        if (empty($tanda_tangan)) {
            $this->session->set_flashdata('error', 'Belum ada tanda tangan yang tersimpan untuk diunduh.');
            redirect('dosen/tanda-tangan');
            return;
        }

        $filePath = FCPATH . 'uploads/signatures/' . $tanda_tangan;
        if (!file_exists($filePath)) {
            $this->session->set_flashdata('error', 'File tanda tangan fisik tidak ditemukan di server.');
            redirect('dosen/tanda-tangan');
            return;
        }

        $this->load->helper('download');
        $namaBersih = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dosen_info['nama_dosen'] ?? 'dosen');
        $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_dosen);
        $downloadName = 'TTD_' . $namaBersih . '_' . $cleanNip . '.png';

        force_download($downloadName, file_get_contents($filePath));
    }

    /**
     * Ubah / Pilih Jurusan (Prodi) Dosen Wali Secara Dinamis
     */
    public function pilih_prodi() {
        $prodi = trim($this->input->post('prodi', true) ?? '');
        if (!empty($prodi)) {
            $this->session->set_userdata('selected_prodi', $prodi);
            $userId = $this->session->userdata('user_id');
            if ($userId && $this->db->table_exists('user')) {
                $this->db->where('id', $userId)->update('user', ['prodi' => $prodi]);
            }
            $this->session->set_flashdata('success', "Jurusan berhasil diubah ke: <b>" . htmlspecialchars($prodi) . "</b>");
        }
        redirect('dosen/wali');
    }


    // =========================================================================
    // SECTION 3: FITUR & METHOD DOSEN BIMBINGAN & PENGUJI
    // =========================================================================

private function _is_authorized_reviewer() {
        $role_id = (int) $this->session->userdata('role_id');
        return in_array($role_id, [1, 2, 3, 5, 6, 9], true);
    }

    private function _is_admin() {
        return (int) $this->session->userdata('role_id') === 1;
    }

    

    private function _clean_posisi($value, $default = 1) {
        $p = (int) $value;
        return ($p >= 1 && $p <= 4) ? $p : $default;
    }

    private function _posisi_from_get() {
        $raw = $this->input->get('model_posisi') ?: $this->input->get('posisi');
        return $this->_clean_posisi($raw, 1);
    }

    private function _clean_comment($html) {
        $html = (string) $html;
        return $this->security->xss_clean($html);
    }

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

    

    public function bimbingan() {
        $nim = $this->_get_current_nim();
        $role_id = (int) $this->session->userdata('role_id');

        // Jika mahasiswa, pastikan seluruh 4 tahap approval pendaftaran TA sudah disetujui (Approved)
        if ($role_id === 4) {
            $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
            $w_app  = ($pendaftaran['status_approval_wali']  ?? '') === 'Approved';
            $a_app  = ($pendaftaran['status_approval_admin'] ?? '') === 'Approved';
            $k_app  = ($pendaftaran['status_approval_koor']  ?? '') === 'Approved';
            $kk_app = ($pendaftaran['status_approval_kk']    ?? '') === 'Approved';
            $is_full_approved = ($w_app && $a_app && $k_app && $kk_app);

            if (!$is_full_approved) {
                $this->session->set_flashdata('warning', 'Akses modul Bimbingan TA masih terkunci. Pengajuan Tugas Akhir Anda harus disetujui lengkap oleh 4 tahap (Dosen Wali, Admin Layanan, Koordinator TA, dan Ketua KK).');
                redirect('mahasiswa');
                return;
            }
        }

        $data['title']       = 'Bimbingan & Evaluasi Preview TA';
        $data['mahasiswa']   = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = !empty($pendaftaran) ? $pendaftaran : $this->Mahasiswa_model->get_status_pendaftaran($nim);

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
        $data['latest_sidang'] = $data['riwayat_sidang'][0]   ?? null;

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

        $rekap = $this->Mahasiswa_model->get_rekap_nilai_sidang($nim);
        $data['rekap_nilai_sidang'] = $rekap;

        $data['is_nilai_published'] = !empty($rekap['semua_terisi']);
        $data['is_nilai_draft']     = (!empty($rekap['has_data']) && empty($rekap['semua_terisi']));

        $data['nilai_akhir'] = !empty($rekap['nilai_akhir'])
            ? number_format((float)$rekap['nilai_akhir'], 2, '.', '')
            : '-';
        $data['grade_sidang']     = $rekap['grade']            ?? '-';
        $data['status_kelulusan'] = $rekap['status_kelulusan'] ?? 'Belum Dinilai';

        $data['criteria_items'] = $rekap['kriteria_referensi'] ?? [];

        $data['tgl_sidang']          = $data['pendaftaran']['tgl_sidang']         ?? null;
        $data['jam_mulai_sidang']    = $data['pendaftaran']['jam_mulai_sidang']   ?? null;
        $data['jam_selesai_sidang']  = $data['pendaftaran']['jam_selesai_sidang'] ?? null;
        $data['ruangan_sidang']      = $data['pendaftaran']['ruangan_sidang']     ?? null;
        $data['tgl_publish_sidang']  = $data['pendaftaran']['tgl_publish_sidang'] ?? null;
        $data['catatan_sidang']      = $data['pendaftaran']['catatan_koor']       ?? '';
        $data['is_sidang_scheduled'] = (!empty($data['tgl_sidang']) && !empty($data['jam_mulai_sidang']));

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

        $jenis = $this->input->post('jenis_file');
        if (!in_array($jenis, ['bimbingan', 'sitasi', 'persyaratan'])) {
            echo json_encode(['status' => false, 'message' => 'Jenis file tidak valid.']);
            return;
        }

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx',
            'max_size'      => 10240,
        ];

        $this->load->library('upload');

        $config['file_name'] = 'PREVIEW3_' . strtoupper($jenis) . '_' . $nim . '_' . time();
        $file_name = $this->_do_upload('file_upload', $config);

        if (!$file_name) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal upload file ' . $jenis . '. Pastikan format PDF/DOC/DOCX maksimal 10MB. ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

        $data_insert = [
            'nim'                => $nim,
            'tahap_preview'      => $tahap,
            'file_draft'         => $file_name,
            'file_sitasi'        => ($jenis === 'sitasi') ? $file_name : null,
            'file_bimbingan'     => ($jenis === 'bimbingan') ? $file_name : null,
            'file_persyaratan'   => ($jenis === 'persyaratan') ? $file_name : null,
            'catatan_mahasiswa'  => $catatan,
            'status_pembimbing'  => 'Pending',
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        echo json_encode([
            'status'  => true,
            'message' => 'Berkas ' . ucfirst($jenis) . ' Preview 3 berhasil diunggah. Menunggu review dosen.'
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

    public function upload_bap_ajax() {
        $this->output->set_content_type('application/json');

        if (!$this->_is_authorized_reviewer()) {
            $this->output
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized — Anda tidak memiliki hak akses.'
                ]));
            return;
        }

        $role_id = (int) $this->session->userdata('role_id');
        $is_admin_or_koor = in_array($role_id, [1, 6, 9], true);

        $nim = $this->input->post('nim');
        if (empty($nim)) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'NIM tidak valid.'
                ]));
            return;
        }

        if (!$is_admin_or_koor) {
            $dosen_id = $this->session->userdata('user_id');
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, 3);
            $allowed  = false;
            foreach ($students as $s) {
                if ((string) $s['nim'] === (string) $nim) { $allowed = true; break; }
            }
            if (!$allowed) {
                $this->output
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status'  => false,
                        'message' => 'Anda bukan Penguji 1 mahasiswa ini.'
                    ]));
                return;
            }
        }

        $marker = 'bap_published_' . date('YmdHis');

        $saved = $this->Mahasiswa_model->save_pendaftaran_ta([
            'nim'      => $nim,
            'file_bap' => $marker,
        ]);

        if ($saved) {
            $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => 'BAP berhasil dipublikasikan ke halaman mahasiswa.',
                    'marker'  => $marker,
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Gagal mempublikasikan BAP. Silakan coba lagi.'
                ]));
        }
    }

    // =========================================================
    // REVIEW PREVIEW (DOSEN)
    // =========================================================

    private function _build_review_update($posisi, $catatan, $status) {
        $catatan = $this->_clean_comment($catatan);

        switch ((int) $posisi) {
            case 1:
                $data = ['catatan_pembimbing' => $catatan];
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
                $this->Mahasiswa_model->update_review_preview($id, ['status_pembimbing' => 'Approved']);
                $processed++;
            } else {
                $col = $comment_col[$posisi];
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

            $has_app_bimbingan   = false;
            $has_app_sitasi      = false;
            $has_app_persyaratan = false;
            foreach ($riwayat_p3 as $r) {
                if (($r['status_pembimbing'] ?? '') === 'Approved') {
                    if (!empty($r['file_bimbingan']))   $has_app_bimbingan   = true;
                    if (!empty($r['file_sitasi']))       $has_app_sitasi      = true;
                    if (!empty($r['file_persyaratan']))  $has_app_persyaratan = true;
                }
            }

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
                'is_p1_app'           => (bool)(!empty($riwayat_p1[0]['lulus_preview1']) && (string)$riwayat_p1[0]['lulus_preview1'] === '1'),
                'is_p2_app'           => (bool)(!empty($riwayat_p2[0]['lulus_preview2']) && (string)$riwayat_p2[0]['lulus_preview2'] === '1'),
                'is_p3_app'           => (bool)(!empty($riwayat_p3[0]['lulus_preview3']) && (string)$riwayat_p3[0]['lulus_preview3'] === '1'),
                'has_app_bimbingan'   => $has_app_bimbingan,
                'has_app_sitasi'      => $has_app_sitasi,
                'has_app_persyaratan' => $has_app_persyaratan,
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

    private function _render_dosen_dashboard($default_role) {
        if (!$this->_is_authorized_reviewer()) {
            redirect('login');
            return;
        }

        $role = $this->input->get('role', TRUE);

        if (!$role && $this->input->get('posisi')) {
            $is_p2_like = ((int) $this->input->get('posisi') === 2);
            $is_penguji = in_array($default_role, ['u1', 'u2'], true);
            $role = $is_penguji ? ($is_p2_like ? 'u2' : 'u1') : ($is_p2_like ? 'p2' : 'p1');
        }

        if (!isset($this->role_map[$role])) $role = $default_role;

        $model_posisi   = $this->role_map[$role];
        $display_posisi = in_array($role, ['p1', 'u1'], true) ? 1 : 2;
        $is_pembimbing  = in_array($role, ['p1', 'p2'], true);

        $data = [
            'title'        => $is_pembimbing ? 'Dashboard Bimbingan Dosen' : 'Dashboard Dosen Penguji',
            'role'         => $role,
            'posisi'       => $display_posisi,
            'model_posisi' => $model_posisi,
        ];

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

    // =================================================================
    // PER-FILE ACC/REVISI/RESET/KOMENTAR, LULUS TAHAP, DOWNGRADE
    // =================================================================

    /** Helper: role PIC untuk suatu tahap. */
    private function _get_responsible_role($tahap) {
        if ($tahap === 'Sidang') return 'u1';
        return 'p1';
    }

    /** Helper: cek apakah user yang login = PIC untuk mahasiswa ini di tahap tsb. */
    private function _is_pic_for_student($nim, $pic_role) {
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id === 1) return true;

        $dosen_id = $this->session->userdata('user_id');
        $posisi_map = ['p1' => 1, 'p2' => 2, 'u1' => 3, 'u2' => 4];
        $pic_posisi = $posisi_map[$pic_role] ?? 1;

        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $pic_posisi);
        foreach ($students as $s) {
            if ((string)$s['nim'] === (string)$nim) return true;
        }
        return false;
    }

    /**
     * POST — ACC / Revisi / Reset / Komentar Berkas PER FILE.
     * Body: id_preview, file_type, action (Approved|Revision|Reset|Comment), catatan
     *
     * ─── CHANGE 2026-10-09 ───
     *  - Tambah dukungan action='Reset' → reset status_file_* ke 'Pending'.
     *  - Tambah dukungan action='Comment' → update catatan_file_* tanpa ubah status.
     */
    public function review_file_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id_preview = $this->input->post('id_preview');
        $file_type  = $this->input->post('file_type');
        $action     = $this->input->post('action');
        $catatan    = $this->input->post('catatan') ?? '';

        if (empty($id_preview) || empty($file_type) || empty($action)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        // Validasi action
        $valid_actions = ['Approved', 'Revision', 'Reset', 'Comment'];
        if (!in_array($action, $valid_actions, true)) {
            echo json_encode(['status' => false, 'message' => 'Action tidak valid: ' . $action]);
            return;
        }

        $preview = $this->Mahasiswa_model->get_preview_by_id($id_preview);
        if (!$preview) {
            echo json_encode(['status' => false, 'message' => 'Preview tidak ditemukan.']);
            return;
        }

        $tahap_key  = strtolower($preview['tahapan_preview'] ?? 'preview1');
        $tahap_name = [
            'preview1' => 'Preview 1', 'preview2' => 'Preview 2',
            'preview3' => 'Preview 3', 'sidang'   => 'Sidang',
        ][$tahap_key] ?? 'Preview 1';

        $pic_role = $this->_get_responsible_role($tahap_name);

        // ─── Aksi Comment: tidak mengubah status, boleh siapa saja yang authorized ───
        if ($action === 'Comment') {
            $res = $this->Mahasiswa_model->update_file_comment($id_preview, $file_type, $catatan, $pic_role);
            echo json_encode($res);
            return;
        }

        // ─── Aksi ubah status (Approved/Revision/Reset): wajib PIC ───
        if (!$this->_is_pic_for_student($preview['nim'], $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya ' . strtoupper($pic_role) . ' (PIC tahap ' . $tahap_name . ') yang dapat ACC/Revisi berkas ini.',
            ]);
            return;
        }

        // Normalisasi: Reset → Pending
        $normalized_status = ($action === 'Reset') ? 'Pending' : $action;

        $res = $this->Mahasiswa_model->update_file_status(
            $id_preview, $file_type, $normalized_status, $catatan, $pic_role
        );

        echo json_encode($res);
    }

    public function lulus_stage_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim        = $this->input->post('nim');
        $tahap      = $this->input->post('tahap');
        $id_preview = $this->input->post('id_preview') ?: null;

        if (empty($nim) || empty($tahap)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        $pic_role = $this->_get_responsible_role($tahap);

        if (!$this->_is_pic_for_student($nim, $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya ' . strtoupper($pic_role) . ' yang dapat meluluskan tahap ' . $tahap . '.',
            ]);
            return;
        }

        $res = $this->Mahasiswa_model->set_stage_lulus($nim, $tahap, $pic_role, $id_preview);
        echo json_encode($res);
    }

    public function downgrade_stage_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim    = $this->input->post('nim');
        $dari   = $this->input->post('dari');
        $ke     = $this->input->post('ke');
        $alasan = $this->input->post('alasan') ?? '';

        if (empty($nim) || empty($dari) || empty($ke)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        $normalize = function ($t) {
            $map = [
                'Preview 1' => 'preview1', 'Preview 2' => 'preview2',
                'Preview 3' => 'preview3', 'Sidang'   => 'sidang',
                'preview1' => 'preview1', 'preview2' => 'preview2',
                'preview3' => 'preview3', 'sidang'   => 'sidang',
            ];
            return $map[$t] ?? null;
        };

        $dari_key = $normalize($dari);
        $ke_key   = $normalize($ke);

        if (!$dari_key || !$ke_key) {
            echo json_encode(['status' => false, 'message' => 'Tahap tidak valid.']);
            return;
        }

        $hierarchy = ['preview1' => 1, 'preview2' => 2, 'preview3' => 3, 'sidang' => 4];
        if ($hierarchy[$dari_key] - $hierarchy[$ke_key] !== 1) {
            echo json_encode(['status' => false, 'message' => 'Downgrade hanya boleh turun 1 tingkat.']);
            return;
        }

        $pic_role = ($dari_key === 'preview1') ? 'p1' : 'u1';

        if (!$this->_is_pic_for_student($nim, $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya ' . strtoupper($pic_role) . ' yang dapat melakukan downgrade dari tahap ini.',
            ]);
            return;
        }

        $res = $this->Mahasiswa_model->downgrade_stage($nim, $dari, $ke, $pic_role, $alasan);
        echo json_encode($res);
    }

}
