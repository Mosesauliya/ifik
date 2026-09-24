<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get Data Mahasiswa berdasarkan NIM atau User ID
    public function get_mahasiswa($nim) {
        $session_name = $this->session->userdata('name');
        $session_nim  = $this->session->userdata('nim') ?: $this->session->userdata('nidn_nim');
        
        // Ambil data dari tabel user jika tersedia
        $user_row = null;
        if (!empty($nim)) {
            $user_row = $this->db
                ->select('id, name, username, email')
                ->group_start()
                    ->where('nim', $nim)
                    ->or_where('username', $nim)
                ->group_end()
                ->limit(1)
                ->get('user')
                ->row_array();
        }
        if (!$user_row && $this->session->userdata('user_id')) {
            $user_row = $this->db
                ->select('id, name, username, email')
                ->where('id', $this->session->userdata('user_id'))
                ->limit(1)
                ->get('user')
                ->row_array();
        }

        $full_name = !empty($user_row['name']) ? $user_row['name'] : ($session_name ?: 'Mahasiswa');
        $name_parts = explode(' ', trim($full_name), 2);
        $nama_depan_default = $name_parts[0] ?? 'Mahasiswa';
        $nama_belakang_default = $name_parts[1] ?? '';

        $data_mhs = null;
        if ($this->db->table_exists('mahasiswa') && !empty($nim)) {
            $query = $this->db->get_where('mahasiswa', array('nim' => $nim));
            $data_mhs = $query->row_array();
        }

        if ($data_mhs) {
            // Jika nama_depan di tabel mahasiswa kosong atau ingin diselaraskan dengan akun login
            if (empty($data_mhs['nama_depan']) || (!empty($user_row['name']) && $user_row['name'] !== 'Rivan Arshavin')) {
                $data_mhs['nama_depan'] = $nama_depan_default;
                $data_mhs['nama_belakang'] = $nama_belakang_default;
            }
            if (empty($data_mhs['nim'])) {
                $data_mhs['nim'] = $nim ?: $session_nim;
            }
            return $data_mhs;
        }

        return array(
            'nim' => $nim ?: ($session_nim ?: '1301210001'),
            'nama_depan' => $nama_depan_default,
            'nama_belakang' => $nama_belakang_default,
            'alamat' => 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'latitude' => '-6.973000',
            'longitude' => '107.630000',
            'konsentrasi_dkv' => 'Desain Komunikasi Visual',
            'prodi' => 'Desain Komunikasi Visual'
        );
    }

    // Simpan atau update geodata mahasiswa
    public function update_geodata($nim, $data_geodata) {
        if (!$this->db->table_exists('mahasiswa')) return true;
        $this->db->where('nim', $nim);
        return $this->db->update('mahasiswa', $data_geodata);
    }

    // Simpan Pendaftaran TA ke tabel guidance & file_pendaftaran
    public function save_pendaftaran_ta($data_ta) {
        $nim = $data_ta['nim'] ?? null;
        if (!$nim) return false;

        // Ambil ID User dari tabel user
        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['nim' => $nim])->row_array() 
                 ?: $this->db->get_where('user', ['username' => $nim])->row_array();
        }
        $userId = $user ? $user['id'] : ('usr_mhs_' . $nim);

        // 1. Simpan ke tabel guidance
        if ($this->db->table_exists('guidance')) {
            $existing_g = $this->db->group_start()
                ->where('id_mhs', $userId)
                ->or_where('id_mhs', $nim)
            ->group_end()->get('guidance')->row_array();

            $g_fields = $this->db->list_fields('guidance');
            $g_data = array();

            if (in_array('judul_1', $g_fields)) $g_data['judul_1'] = $data_ta['judul_1'] ?? '';
            if (in_array('judul_2', $g_fields)) $g_data['judul_2'] = $data_ta['judul_2'] ?? '';
            if (in_array('judul_3', $g_fields)) $g_data['judul_3'] = $data_ta['judul_3'] ?? '';
            if (in_array('judul_en', $g_fields)) $g_data['judul_en'] = $data_ta['judul_en'] ?? '';
            if (in_array('jenis_TA', $g_fields)) {
                $g_data['jenis_TA'] = !empty(trim($data_ta['jenis_ta'] ?? '')) ? trim($data_ta['jenis_ta']) : 'Pengkaryaan';
            }
            if (in_array('peminatan', $g_fields)) {
                $g_data['peminatan'] = !empty(trim($data_ta['konsentrasi_dkv'] ?? '')) ? trim($data_ta['konsentrasi_dkv']) : 'Desain Komunikasi Visual';
            }
            if (in_array('tahun', $g_fields)) $g_data['tahun'] = date('Y');
            if (in_array('keterangan', $g_fields)) $g_data['keterangan'] = $data_ta['status_approval_wali'] ?? 'Pending';
            if (in_array('date', $g_fields) && empty($existing_g)) $g_data['date'] = date('Y-m-d H:i:s');
            if (in_array('date_edit', $g_fields)) $g_data['date_edit'] = date('Y-m-d H:i:s');

            if ($existing_g) {
                $this->db->where('id', $existing_g['id'])->update('guidance', $g_data);
            } else {
                if (in_array('id', $g_fields)) $g_data['id'] = 'gdn_' . $nim;
                if (in_array('id_mhs', $g_fields)) $g_data['id_mhs'] = $userId;
                $this->db->insert('guidance', $g_data);
            }
        }

        // 2. Simpan file-file ke tabel file_pendaftaran
        if ($this->db->table_exists('file_pendaftaran')) {
            $files = [
                'ksm'        => $data_ta['file_ksm'] ?? null,
                'transkrip'  => $data_ta['file_transkrip'] ?? null,
                'pernyataan' => $data_ta['file_pernyataan'] ?? null,
                'bebas_lab'  => $data_ta['file_bebas_lab'] ?? null
            ];

            $fp_fields = $this->db->list_fields('file_pendaftaran');

            foreach ($files as $kode => $fileName) {
                if (empty($fileName)) continue;

                $relPath = (strpos($fileName, 'uploads/') === 0) ? $fileName : ('uploads/persyaratan_ta/' . $fileName);

                $exFp = $this->db->group_start()
                    ->where('id_mhs', $userId)
                    ->or_where('id_mhs', $nim)
                ->group_end()
                ->where('nama', $kode)
                ->get('file_pendaftaran')->row_array();

                $fpData = array();
                if (in_array('file', $fp_fields)) $fpData['file'] = $relPath;
                if (in_array('status_doswal', $fp_fields)) $fpData['status_doswal'] = 'Pending';
                if (in_array('status_adminlaa', $fp_fields)) $fpData['status_adminlaa'] = 'Pending';
                if (in_array('date_edit', $fp_fields)) $fpData['date_edit'] = date('Y-m-d H:i:s');

                if ($exFp) {
                    $this->db->where('id', $exFp['id'])->update('file_pendaftaran', $fpData);
                } else {
                    if (in_array('id', $fp_fields)) $fpData['id'] = 'fp_' . $nim . '_' . $kode;
                    if (in_array('id_mhs', $fp_fields)) $fpData['id_mhs'] = $userId;
                    if (in_array('nama', $fp_fields)) $fpData['nama'] = $kode;
                    if (in_array('view_adminlaa', $fp_fields)) $fpData['view_adminlaa'] = 0;
                    if (in_array('view_doswal', $fp_fields)) $fpData['view_doswal'] = 0;
                    if (in_array('komentar', $fp_fields)) $fpData['komentar'] = '';
                    if (in_array('date', $fp_fields)) $fpData['date'] = date('Y-m-d H:i:s');
                    $this->db->insert('file_pendaftaran', $fpData);
                }
            }
        }

        return true;
    }

    // Get Status Pendaftaran & Approval Chain langsung dari guidance, file_pendaftaran, pendaftaran_berkas & pendaftaran_ta
    public function get_status_pendaftaran($nim) {
        $pt_data = array();
        if ($this->db->table_exists('pendaftaran_ta')) {
            $pt_row = $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array();
            if ($pt_row) {
                $pt_data = $pt_row;
            }
        }

        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', array('nim' => $nim))->row_array() 
                 ?: $this->db->get_where('user', array('username' => $nim))->row_array();
        }
        $userId = $user ? $user['id'] : ('usr_mhs_' . $nim);

        $guidance = null;
        if ($this->db->table_exists('guidance')) {
            $guidance = $this->db->group_start()
                ->where('id_mhs', $userId)
                ->or_where('id_mhs', $nim)
            ->group_end()->order_by('date', 'DESC')->get('guidance')->row_array();
        }

        $files = array();
        if ($this->db->table_exists('file_pendaftaran')) {
            $fileRows = $this->db->group_start()
                ->where('id_mhs', $userId)
                ->or_where('id_mhs', $nim)
            ->group_end()->get('file_pendaftaran')->result_array();

            foreach ($fileRows as $fr) {
                $files[$fr['nama']] = $fr;
            }
        }

        $berkas_rows = array();
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $pb_rows = $this->db->get_where('pendaftaran_berkas', array('nim' => $nim))->result_array();
            foreach ($pb_rows as $pbr) {
                $berkas_rows[$pbr['kode_berkas']] = $pbr;
            }
        }

        $active_keys = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        if ($this->db->table_exists('syarat_berkas_ta')) {
            $sb_rows = $this->db->get_where('syarat_berkas_ta', array('is_active' => 1))->result_array();
            if (!empty($sb_rows)) {
                $active_keys = array_column($sb_rows, 'kode_berkas');
            }
        }

        $is_submitted = ($guidance !== null || !empty($files) || !empty($berkas_rows) || !empty($pt_data['judul_1'])) ? 1 : 0;

        // 1. Status Judul & Catatan Judul
        $status_judul = $pt_data['status_judul'] ?? null;
        $catatan_judul = $pt_data['catatan_judul'] ?? null;

        if (empty($status_judul) && $guidance) {
            $g_ket = $guidance['keterangan'] ?? 'Pending';
            if ($g_ket === 'Approved' || $g_ket === 'Rejected') {
                $status_judul = $g_ket;
                if (empty($catatan_judul)) $catatan_judul = $guidance['komentar'] ?? '';
            } else {
                $status_judul = 'Pending';
            }
        }
        if (empty($status_judul)) $status_judul = 'Pending';
        if ($catatan_judul === null) $catatan_judul = $guidance['komentar'] ?? '';

        // 2. Status Jenis TA & Catatan Jenis TA
        $status_jenis_ta = $pt_data['status_jenis_ta'] ?? null;
        $catatan_jenis_ta = $pt_data['catatan_jenis_ta'] ?? null;
        if (empty($status_jenis_ta)) {
            if ($status_judul === 'Approved') {
                $status_jenis_ta = 'Approved';
            } else {
                $status_jenis_ta = 'Pending';
            }
        }
        if ($catatan_jenis_ta === null) $catatan_jenis_ta = '';

        // 3. Per-File Status & Catatan
        $files_result = array();
        $has_any_file_rej_wali = false;
        $has_any_file_rej_admin = false;
        $all_files_app_wali = true;
        $file_count = 0;

        $catatan_wali = !empty($pt_data['catatan_wali']) ? $pt_data['catatan_wali'] : ($guidance['komentar'] ?? '');
        $catatan_admin = !empty($pt_data['catatan_admin']) ? $pt_data['catatan_admin'] : '';

        foreach ($active_keys as $k) {
            $file_count++;
            $f_obj = $files[$k] ?? null;
            $b_obj = $berkas_rows[$k] ?? null;

            // Filename
            $fname = '';
            if (!empty($pt_data['file_' . $k])) {
                $fname = $pt_data['file_' . $k];
            } elseif ($b_obj && !empty($b_obj['file_name'])) {
                $fname = $b_obj['file_name'];
            } elseif ($f_obj && !empty($f_obj['file'])) {
                $fname = basename($f_obj['file']);
            }
            $files_result['file_' . $k] = $fname;

            // Status Dosen Wali per berkas
            $st_dw = $pt_data['status_file_' . $k] ?? null;
            if (empty($st_dw)) {
                if ($f_obj && !empty($f_obj['status_doswal'])) {
                    $st_dw = $f_obj['status_doswal'];
                } elseif ($b_obj && !empty($b_obj['status_verifikasi'])) {
                    $st_dw = ($b_obj['status_verifikasi'] === 'Valid') ? 'Approved' : (($b_obj['status_verifikasi'] === 'Invalid') ? 'Rejected' : 'Pending');
                } else {
                    $st_dw = 'Pending';
                }
            }
            $files_result['status_file_' . $k] = $st_dw;

            if ($st_dw === 'Rejected') {
                $has_any_file_rej_wali = true;
                $all_files_app_wali = false;
            } elseif ($st_dw !== 'Approved') {
                $all_files_app_wali = false;
            }

            // Catatan Dosen Wali per berkas
            $c_dw = $pt_data['catatan_file_' . $k] ?? null;
            if (empty($c_dw)) {
                if ($f_obj && !empty($f_obj['komentar'])) {
                    $c_dw = $f_obj['komentar'];
                } elseif ($b_obj && !empty($b_obj['catatan'])) {
                    $c_dw = $b_obj['catatan'];
                } else {
                    $c_dw = '';
                }
            }
            $files_result['catatan_file_' . $k] = $c_dw;

            // Status Admin LAA per berkas
            $st_laa = $b_obj['status_verifikasi'] ?? ($f_obj['status_adminlaa'] ?? 'Pending');
            $files_result['status_' . $k] = $st_laa;
            if ($st_laa === 'Invalid' || $st_laa === 'Rejected') {
                $has_any_file_rej_admin = true;
            }
        }

        // Tentukan Overall Status Approval Dosen Wali
        if (!empty($pt_data['status_approval_wali'])) {
            $status_doswal = $pt_data['status_approval_wali'];
        } else {
            if ($has_any_file_rej_wali || $status_judul === 'Rejected' || $status_jenis_ta === 'Rejected') {
                $status_doswal = 'Rejected';
            } elseif ($all_files_app_wali && $file_count > 0 && $status_judul === 'Approved') {
                $status_doswal = 'Approved';
            } else {
                $status_doswal = 'Pending';
            }
        }

        // Tentukan Overall Status Admin LAA
        if (!empty($pt_data['status_approval_admin'])) {
            $status_laa = $pt_data['status_approval_admin'];
        } else {
            $status_laa = $has_any_file_rej_admin ? 'Rejected' : 'Pending';
        }

        $current_stage = $pt_data['current_stage'] ?? (
            ($status_doswal !== 'Approved') 
                ? ($status_doswal === 'Rejected' ? 'Dosen Wali (Revisi)' : 'Dosen Wali') 
                : (($status_laa !== 'Approved') ? 'Admin Layanan' : 'Koordinator TA')
        );

        $res = array_merge(array(
            'nim'                   => $nim,
            'is_submitted'          => $is_submitted,
            'jenis_ta'              => !empty(trim($pt_data['jenis_ta'] ?? '')) 
                                        ? trim($pt_data['jenis_ta']) 
                                        : (!empty(trim($guidance['jenis_TA'] ?? '')) 
                                            ? trim($guidance['jenis_TA']) 
                                            : (!empty(trim($guidance['jenis_ta'] ?? '')) ? trim($guidance['jenis_ta']) : 'Pengkaryaan')),
            'judul_1'               => $pt_data['judul_1'] ?? ($guidance['judul_1'] ?? ''),
            'judul_2'               => $pt_data['judul_2'] ?? ($guidance['judul_2'] ?? ''),
            'judul_3'               => $pt_data['judul_3'] ?? ($guidance['judul_3'] ?? ''),
            'judul_en'              => $pt_data['judul_en'] ?? ($guidance['judul_en'] ?? ''),
            'konsentrasi_dkv'       => $pt_data['konsentrasi_dkv'] ?? ($guidance['peminatan'] ?? 'Informatika'),
            'status_approval_wali'  => $status_doswal,
            'status_approval_admin' => $status_laa,
            'status_approval_koor'  => $pt_data['status_approval_koor'] ?? 'Pending',
            'status_approval_kk'    => $pt_data['status_approval_kk'] ?? 'Pending',
            'status_judul'          => $status_judul,
            'catatan_judul'         => $catatan_judul,
            'status_jenis_ta'       => $status_jenis_ta,
            'catatan_jenis_ta'      => $catatan_jenis_ta,
            'current_stage'         => $current_stage,
            'catatan_wali'          => trim($catatan_wali),
            'catatan_admin'         => trim($catatan_admin),
            'catatan_koor'          => $pt_data['catatan_koor'] ?? '',
            'berkas_kurang'         => $pt_data['berkas_kurang'] ?? null,
            'created_at'            => $pt_data['created_at'] ?? ($guidance['date'] ?? null),
            'updated_at'            => $pt_data['updated_at'] ?? ($guidance['date'] ?? null)
        ), $files_result);

        return $res;
    }

    // Update Ganti Password Mahasiswa
    public function update_password($nim, $hashed_password) {
        $user_table = $this->db->table_exists('user') ? 'user' : 'users';
        $this->db->group_start();
        if ($this->db->field_exists('nim', $user_table)) $this->db->where('nim', $nim);
        if ($this->db->field_exists('nidn_nim', $user_table)) $this->db->or_where('nidn_nim', $nim);
        if ($this->db->field_exists('username', $user_table)) $this->db->or_where('username', $nim);
        $this->db->group_end();
        $user = $this->db->get($user_table)->row();

        $updateData = ['password' => $hashed_password];
        if ($this->db->field_exists('password_changed', $user_table)) {
            $updateData['password_changed'] = 1;
        }
        if ($this->db->field_exists('updated_at', $user_table)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->group_start();
        if ($this->db->field_exists('nim', $user_table)) $this->db->where('nim', $nim);
        if ($this->db->field_exists('nidn_nim', $user_table)) $this->db->or_where('nidn_nim', $nim);
        if ($this->db->field_exists('username', $user_table)) $this->db->or_where('username', $nim);
        $this->db->group_end();
        $res = $this->db->update($user_table, $updateData);

        // Delete any temporary activation token from user_token table
        if ($user && !empty($user->email) && $this->db->table_exists('user_token')) {
            $this->db->delete('user_token', ['email' => $user->email]);
        }

        return $res;
    }

    // Reset atau Hapus Pendaftaran TA
    public function reset_pendaftaran_ta($nim) {
        $upload_path = FCPATH . 'uploads/persyaratan_ta/';

        // Ambil User ID jika ada
        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['nim' => $nim])->row_array() 
                 ?: $this->db->get_where('user', ['username' => $nim])->row_array();
        }
        $userId = $user ? $user['id'] : ('usr_mhs_' . $nim);

        // 1. Bersihkan berkas fisik legacy dari pendaftaran_ta & hapus record
        if ($this->db->table_exists('pendaftaran_ta')) {
            $existing = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
            if ($existing) {
                $files_to_delete = ['file_ksm', 'file_transkrip', 'file_pernyataan', 'file_bebas_lab'];
                foreach ($files_to_delete as $field) {
                    if (!empty($existing[$field])) {
                        $filepath = $upload_path . $existing[$field];
                        if (file_exists($filepath) && is_file($filepath)) {
                            @unlink($filepath);
                        }
                    }
                }
            }

            $this->db->where('nim', $nim);
            $this->db->delete('pendaftaran_ta');
        }

        // 2. Bersihkan berkas fisik dan record dari pendaftaran_berkas
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $berkas_rows = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim])->result_array();
            if (!empty($berkas_rows)) {
                foreach ($berkas_rows as $br) {
                    if (!empty($br['file_name'])) {
                        $filepath = $upload_path . $br['file_name'];
                        if (file_exists($filepath) && is_file($filepath)) {
                            @unlink($filepath);
                        }
                    }
                }
            }
            $this->db->where('nim', $nim);
            $this->db->delete('pendaftaran_berkas');
        }

        // 3. Bersihkan record dari guidance
        if ($this->db->table_exists('guidance')) {
            $this->db->group_start()
                ->where('id_mhs', $userId)
                ->or_where('id_mhs', $nim)
            ->group_end()->delete('guidance');
        }

        // 4. Bersihkan berkas fisik dan record dari file_pendaftaran
        if ($this->db->table_exists('file_pendaftaran')) {
            $fp_rows = $this->db->group_start()
                ->where('id_mhs', $userId)
                ->or_where('id_mhs', $nim)
            ->group_end()->get('file_pendaftaran')->result_array();

            if (!empty($fp_rows)) {
                foreach ($fp_rows as $fpr) {
                    if (!empty($fpr['file'])) {
                        $filepath = FCPATH . $fpr['file'];
                        if (file_exists($filepath) && is_file($filepath)) {
                            @unlink($filepath);
                        }
                    }
                }
            }

            $this->db->group_start()
                ->where('id_mhs', $userId)
                ->or_where('id_mhs', $nim)
            ->group_end()->delete('file_pendaftaran');
        }

        // 5. Bersihkan berkas fisik dan riwayat bimbingan_preview
        if ($this->db->table_exists('bimbingan_preview')) {
            $prev_rows = $this->db->get_where('bimbingan_preview', ['nim' => $nim])->result_array();
            if (!empty($prev_rows)) {
                foreach ($prev_rows as $pr) {
                    if (!empty($pr['file_laporan'])) {
                        $filepath = FCPATH . 'uploads/bimbingan_preview/' . $pr['file_laporan'];
                        if (file_exists($filepath) && is_file($filepath)) {
                            @unlink($filepath);
                        }
                    }
                }
            }
            $this->db->where('nim', $nim)->delete('bimbingan_preview');
        }

        return true;
    }

    // Ambil riwayat upload berkas preview TA
    public function get_riwayat_preview($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('bimbingan_preview')) return [];
        $this->db->where('nim', $nim);
        if ($tahap) {
            $this->db->where('tahap_preview', $tahap);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('bimbingan_preview')->result_array();
    }

    // Simpan upload draft berkas preview baru
    public function save_upload_preview($data) {
        if (!$this->db->table_exists('bimbingan_preview')) return false;
        return $this->db->insert('bimbingan_preview', $data);
    }

    // Hitung total upload pada tahap tertentu (untuk validasi minimal 1x upload)
    public function count_upload_preview($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('bimbingan_preview')) return 0;
        $this->db->where('nim', $nim);
        $this->db->where('tahap_preview', $tahap);
        return $this->db->count_all_results('bimbingan_preview');
    }

    // Cek status terbaru kelayakan Preview 1
    public function get_latest_preview_status($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('bimbingan_preview')) return null;
        $this->db->where('nim', $nim);
        $this->db->where('tahap_preview', $tahap);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('bimbingan_preview')->row_array();
    }

    // Mendapatkan nama dosen pembimbing dan penguji asli
    public function get_pembimbing_penguji($nim) {
        $result = array(
            'pembimbing_1' => '',
            'pembimbing_2' => '',
            'penguji_1' => '',
            'penguji_2' => ''
        );

        if ($this->db->table_exists('pendaftaran_ta')) {
            $this->db->select('pembimbing_1, pembimbing_2, penguji_1, penguji_2');
            $this->db->where('nim', $nim);
            $pt = $this->db->get('pendaftaran_ta')->row_array();

            if ($pt) {
                $result['pembimbing_1'] = $this->_get_dosen_name($pt['pembimbing_1']);
                $result['pembimbing_2'] = $this->_get_dosen_name($pt['pembimbing_2']);
                $result['penguji_1'] = $this->_get_dosen_name($pt['penguji_1']);
                $result['penguji_2'] = $this->_get_dosen_name($pt['penguji_2']);
            }
        }
        
        return $result;
    }

    private function _get_dosen_name($nip) {
        if (empty($nip)) return '';
        $user_table = $this->db->table_exists('user') ? 'user' : 'users';
        if ($this->db->table_exists($user_table)) {
            $this->db->group_start();
            if ($this->db->field_exists('nidn_nim', $user_table)) $this->db->where('nidn_nim', $nip);
            if ($this->db->field_exists('nip', $user_table)) $this->db->or_where('nip', $nip);
            $this->db->group_end();
            $u = $this->db->get($user_table)->row_array();
            if ($u && !empty($u['name'])) return $u['name'];
        }
        if ($this->db->table_exists('dosen_wali')) {
            $dw = $this->db->get_where('dosen_wali', ['nip' => $nip])->row_array();
            if ($dw && !empty($dw['nama_dosen'])) return $dw['nama_dosen'];
        }
        return '';
    }

    // Mengambil daftar mahasiswa bimbingan bagi seorang Dosen
    public function get_students_by_dosen($dosen_id, $posisi = 1) {
        if (!$this->db->table_exists('pendaftaran_ta')) return [];
        
        $nip_dosen = '';
        $name_dosen = '';
        $user_table = $this->db->table_exists('user') ? 'user' : 'users';
        if ($this->db->table_exists($user_table)) {
            $u = $this->db->get_where($user_table, ['id' => $dosen_id])->row_array();
            if ($u) {
                $nip_dosen  = $u['nidn_nim'] ?? ($u['nip'] ?? '');
                $name_dosen = $u['name'] ?? '';
            }
        }

        $has_konsentrasi = $this->db->field_exists('konsentrasi_dkv', 'pendaftaran_ta');
        $select = 'pt.nim, pt.judul_1 as judul, COALESCE(u.name, pt.nim) as nama_mahasiswa';
        if ($has_konsentrasi) {
            $select .= ', pt.konsentrasi_dkv';
        }
        
        $nim_col = $this->db->field_exists('nidn_nim', $user_table) ? 'nidn_nim' : 'nim';
        $this->db->select($select);
        $this->db->from('pendaftaran_ta pt');
        if ($this->db->table_exists($user_table)) {
            $this->db->join("{$user_table} u", "u.{$nim_col} = pt.nim", 'left');
        }
        
        if (!empty($nip_dosen) || !empty($name_dosen)) {
            $this->db->group_start();
            if ($posisi == 1) {
                if ($nip_dosen) $this->db->or_where('pt.pembimbing_1', $nip_dosen);
                if ($name_dosen) $this->db->or_like('pt.pembimbing_1', $name_dosen);
            } else if ($posisi == 2) {
                if ($nip_dosen) $this->db->or_where('pt.pembimbing_2', $nip_dosen);
                if ($name_dosen) $this->db->or_like('pt.pembimbing_2', $name_dosen);
            } else if ($posisi == 3) {
                if ($nip_dosen) $this->db->or_where('pt.penguji_1', $nip_dosen);
                if ($name_dosen) $this->db->or_like('pt.penguji_1', $name_dosen);
            } else if ($posisi == 4) {
                if ($nip_dosen) $this->db->or_where('pt.penguji_2', $nip_dosen);
                if ($name_dosen) $this->db->or_like('pt.penguji_2', $name_dosen);
            }
            $this->db->group_end();
        }
        
        $results = $this->db->get()->result_array();

        // Fallback hanya untuk Pembimbing (posisi 1 atau 2):
        // jika tidak ada mahasiswa yang match, kembalikan semua mahasiswa TA.
        // Untuk Penguji (posisi 3 atau 4), tidak ada fallback agar tampil kosong.
        if (empty($results) && in_array($posisi, [1, 2])) {
            $this->db->select($select);
            $this->db->from('pendaftaran_ta pt');
            if ($this->db->table_exists($user_table)) {
                $this->db->join("{$user_table} u", "u.{$nim_col} = pt.nim", 'left');
            }
            $results = $this->db->get()->result_array();
        }

        return $results;
    }


    // Update status preview dan catatan dosen
    public function update_review_preview($id, $data) {
        if (!$this->db->table_exists('bimbingan_preview')) return false;
        $this->db->where('id', $id);
        return $this->db->update('bimbingan_preview', $data);
    }
}
