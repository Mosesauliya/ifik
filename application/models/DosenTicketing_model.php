<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenTicketing_model extends CI_Model {

    private $table = 'tb_ticketing';

    public function __construct() {
        parent::__construct();
        if ($this->db->table_exists('tb_ticketing')) {
            $this->table = 'tb_ticketing';
        } elseif ($this->db->table_exists('dosen_ticketing')) {
            $this->table = 'dosen_ticketing';
        }
    }

    /**
     * Generate unique ticket code: TIK-YYYYMMDD-XXXX
     */
    public function generate_kode() {
        $datePrefix = 'TIK-' . date('Ymd') . '-';
        do {
            $randomStr = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $kode = $datePrefix . $randomStr;
            $this->db->group_start();
            $this->db->where('id', $kode);
            if ($this->db->field_exists('kode_tiket', $this->table)) {
                $this->db->or_where('kode_tiket', $kode);
            }
            $this->db->group_end();
            $exists = $this->db->count_all_results($this->table);
        } while ($exists > 0);

        return $kode;
    }

    /**
     * Insert new ticket mapping to tb_ticketing or dosen_ticketing
     */
    public function insert($data) {
        $kode = !empty($data['kode_tiket']) ? $data['kode_tiket'] : $this->generate_kode();

        if ($this->table === 'tb_ticketing') {
            // Pemetaan data ke kolom tabel asli tb_ticketing:
            // id, id_user, nama, email, ruangan, unit, kategori, isi_ticketing, tgl_ticketing, status, file_pendukung, keterangan
            $statusMapped = 'Dikirim';
            if (isset($data['status'])) {
                if (in_array($data['status'], ['Diproses', 'Sedang Diproses'])) {
                    $statusMapped = 'Sedang Diproses';
                } elseif (in_array($data['status'], ['Selesai', 'Ditutup', 'Closed'])) {
                    $statusMapped = 'Closed';
                }
            }

            // Dapatkan email pengirim jika belum ada di data
            $userEmail = '';
            if (!empty($data['email'])) {
                $userEmail = $data['email'];
            } elseif (!empty($data['id_user'])) {
                $u = $this->db->get_where('user', ['id' => $data['id_user']])->row();
                if ($u && !empty($u->email)) $userEmail = $u->email;
            }

            $isi = '';
            if (!empty($data['subjek'])) {
                $isi .= "<strong>" . strip_tags($data['subjek']) . "</strong><br>";
            }
            $isi .= ($data['deskripsi'] ?? '');
            if (!empty($data['custom_fields_data'])) {
                $cf = json_decode($data['custom_fields_data'], true);
                if (is_array($cf)) {
                    $isi .= "<br><br><strong>Detail Tambahan:</strong><ul>";
                    foreach ($cf as $item) {
                        $isi .= "<li>" . htmlspecialchars($item['label'] ?? '') . ": " . htmlspecialchars($item['value'] ?? '') . "</li>";
                    }
                    $isi .= "</ul>";
                }
            }

            $insertData = [
                'id'             => $kode,
                'id_user'        => (string)($data['id_user'] ?? $data['nidn'] ?? '0'),
                'nama'           => $data['nama_dosen'] ?? ($data['nama'] ?? 'Dosen'),
                'email'          => $userEmail ?: '-',
                'ruangan'        => $data['ruangan'] ?? '-',
                'unit'           => $data['unit_tujuan'] ?? ($data['unit'] ?? 'Layanan IFIK'),
                'kategori'       => $data['kategori'] ?? 'Umum',
                'isi_ticketing'  => $isi,
                'tgl_ticketing'  => date('Y-m-d'),
                'tgl_diproses'   => '1970-01-01 00:00:00',
                'tgl_closed'     => '1970-01-01 00:00:00',
                'status'         => $statusMapped,
                'file_pendukung' => $data['lampiran'] ?? ($data['file_pendukung'] ?? ''),
                'keterangan'     => ''
            ];

            $this->db->insert('tb_ticketing', $insertData);
            return $kode;
        } else {
            if (empty($data['kode_tiket'])) {
                $data['kode_tiket'] = $kode;
            }
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Get tickets with standardized properties
     */
    public function get_tickets($user_id = null, $nidn = null) {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) {
                    $this->db->where('id_user', (string)$user_id);
                }
                if ($nidn !== null) {
                    $this->db->or_where('id_user', (string)$nidn);
                }
                $this->db->group_end();
            }
            $this->db->order_by('tgl_ticketing', 'DESC');
            $this->db->order_by('id', 'DESC');
            $raw = $this->db->get()->result();

            return array_map([$this, '_format_row'], $raw);
        }

        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start();
            $this->db->where('id_user', $user_id);
            $this->db->or_where('nidn', $nidn);
            $this->db->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ticket by ID or kode_tiket
     */
    public function get_by_id($id_or_kode) {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            $this->db->where('id', $id_or_kode);
            $row = $this->db->get()->row();
            return $row ? $this->_format_row($row) : null;
        }

        $this->db->from($this->table);
        if (is_numeric($id_or_kode)) {
            $this->db->where('id', (int)$id_or_kode);
        } else {
            $this->db->where('kode_tiket', $id_or_kode);
        }
        return $this->db->get()->row();
    }

    /**
     * Get ticket statistics
     */
    public function get_stats($user_id = null, $nidn = null) {
        $stats = [
            'total'    => 0,
            'menunggu' => 0,
            'diproses' => 0,
            'selesai'  => 0,
            'ditutup'  => 0
        ];

        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $stats['total'] = $this->db->count_all_results();

            // Menunggu (Dikirim)
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $this->db->where('status', 'Dikirim');
            $stats['menunggu'] = $this->db->count_all_results();

            // Diproses (Sedang Diproses)
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $this->db->where('status', 'Sedang Diproses');
            $stats['diproses'] = $this->db->count_all_results();

            // Selesai / Ditutup (Closed)
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $this->db->where('status', 'Closed');
            $closedCount = $this->db->count_all_results();
            $stats['selesai'] = $closedCount;
            $stats['ditutup'] = $closedCount;

            return $stats;
        }

        // Total
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $stats['total'] = $this->db->count_all_results();

        // Menunggu
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Menunggu');
        $stats['menunggu'] = $this->db->count_all_results();

        // Diproses
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Diproses');
        $stats['diproses'] = $this->db->count_all_results();

        // Selesai
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Selesai');
        $stats['selesai'] = $this->db->count_all_results();

        // Ditutup
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Ditutup');
        $stats['ditutup'] = $this->db->count_all_results();

        return $stats;
    }

    /**
     * Get tickets routed to Dosen units
     */
    public function get_respon_tickets($filterStatus = 'all', $search = '') {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            
            // Dosen Unit Filter
            $this->db->group_start();
            $this->db->like('unit', 'Dosen');
            $this->db->or_like('unit', 'Wali');
            $this->db->or_like('unit', 'Koordinator');
            $this->db->or_like('unit', 'Ketua KK');
            $this->db->group_end();

            // Status Filter
            if (!empty($filterStatus) && $filterStatus !== 'all') {
                if ($filterStatus === 'Menunggu') {
                    $this->db->where('status', 'Dikirim');
                } elseif ($filterStatus === 'Diproses') {
                    $this->db->where('status', 'Sedang Diproses');
                } elseif (in_array($filterStatus, ['Selesai', 'Ditutup'])) {
                    $this->db->where('status', 'Closed');
                }
            }

            // Search
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('id', $search);
                $this->db->or_like('nama', $search);
                $this->db->or_like('kategori', $search);
                $this->db->or_like('isi_ticketing', $search);
                $this->db->group_end();
            }

            $this->db->order_by('tgl_ticketing', 'DESC');
            $this->db->order_by('id', 'DESC');
            $raw = $this->db->get()->result();

            return array_map([$this, '_format_row'], $raw);
        }

        // Fallback for dosen_ticketing (if table exists)
        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('unit_tujuan', 'Dosen');
        $this->db->or_like('unit_tujuan', 'Wali');
        $this->db->or_like('unit_tujuan', 'Koordinator');
        $this->db->or_like('unit_tujuan', 'Ketua KK');
        $this->db->group_end();

        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $this->db->where('status', $filterStatus);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode_tiket', $search);
            $this->db->or_like('nama_dosen', $search);
            $this->db->or_like('subjek', $search);
            $this->db->or_like('kategori', $search);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Count tickets routed to Dosen units by status
     */
    public function count_respon_tickets($status = 'all') {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            $this->db->group_start();
            $this->db->like('unit', 'Dosen');
            $this->db->or_like('unit', 'Wali');
            $this->db->or_like('unit', 'Koordinator');
            $this->db->or_like('unit', 'Ketua KK');
            $this->db->group_end();

            if ($status !== 'all') {
                if ($status === 'Menunggu') {
                    $this->db->where('status', 'Dikirim');
                } elseif ($status === 'Diproses') {
                    $this->db->where('status', 'Sedang Diproses');
                } elseif (in_array($status, ['Selesai', 'Ditutup'])) {
                    $this->db->where('status', 'Closed');
                }
            }
            return (int)$this->db->count_all_results();
        }

        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('unit_tujuan', 'Dosen');
        $this->db->or_like('unit_tujuan', 'Wali');
        $this->db->or_like('unit_tujuan', 'Koordinator');
        $this->db->or_like('unit_tujuan', 'Ketua KK');
        $this->db->group_end();

        if ($status !== 'all') {
            $this->db->where('status', $status);
        }
        return (int)$this->db->count_all_results();
    }

    /**
     * Update ticket response and status by Dosen
     */
    public function update_respon($id, $status, $tanggapan = '') {
        if ($this->table === 'tb_ticketing') {
            $statusMapped = 'Dikirim';
            if ($status === 'Diproses') {
                $statusMapped = 'Sedang Diproses';
            } elseif (in_array($status, ['Selesai', 'Ditutup'])) {
                $statusMapped = 'Closed';
            }

            $updateData = ['status' => $statusMapped];
            $now = date('Y-m-d H:i:s');

            if ($status === 'Diproses') {
                $updateData['tgl_diproses'] = $now;
            } elseif (in_array($status, ['Selesai', 'Ditutup'])) {
                $updateData['tgl_closed'] = $now;
            }

            if ($tanggapan !== '') {
                $updateData['keterangan'] = $tanggapan;
            } elseif ($status === 'Menunggu') {
                $updateData['keterangan'] = '';
            }

            $this->db->where('id', (string)$id);
            return $this->db->update('tb_ticketing', $updateData);
        }

        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($tanggapan !== '') {
            $updateData['tanggapan'] = $tanggapan;
            $updateData['tgl_tanggapan'] = date('Y-m-d H:i:s');
        } elseif ($status === 'Menunggu') {
            $updateData['tanggapan'] = null;
            $updateData['tgl_tanggapan'] = null;
        }

        if (is_numeric($id)) {
            $this->db->where('id', (int)$id);
        } else {
            $this->db->where('kode_tiket', $id);
        }
        return $this->db->update($this->table, $updateData);
    }

    /**
     * Standardize tb_ticketing row object to Dosen ticketing view fields
     */
    public function format_row($row) {
        if (!$row) return null;

        $r = clone $row;
        $r->id          = $row->id;
        $r->kode_tiket  = $row->id;
        $r->nama_dosen  = $row->nama ?? 'Dosen';
        $r->nidn        = $row->id_user ?? '-';
        $r->unit_tujuan = $row->unit ?? 'Layanan IFIK';
        $r->kategori    = $row->kategori ?? 'Umum';
        $r->prioritas   = 'Sedang';

        // Ekstrak subjek dan deskripsi dari isi_ticketing
        $rawIsi = $row->isi_ticketing ?? '';
        $r->subjek = 'Kendala ' . ($row->kategori ?? 'Layanan');
        $r->deskripsi = $rawIsi;

        if (preg_match('/<strong>(.*?)<\/strong><br>(.*)/is', $rawIsi, $m)) {
            $r->subjek = trim(strip_tags($m[1]));
            $r->deskripsi = trim($m[2]);
        }

        // Normalisasi status ke format UI Dosen
        if ($row->status === 'Dikirim') {
            $r->status = 'Menunggu';
        } elseif ($row->status === 'Sedang Diproses') {
            $r->status = 'Diproses';
        } elseif ($row->status === 'Closed') {
            $r->status = 'Selesai';
        } else {
            $r->status = $row->status ?? 'Menunggu';
        }

        $r->lampiran      = !empty($row->file_pendukung) ? $row->file_pendukung : null;
        $r->tanggapan     = !empty($row->keterangan) ? $row->keterangan : null;

        $tglTanggapan = null;
        if (!empty($row->tgl_diproses) && !in_array($row->tgl_diproses, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) {
            $tglTanggapan = $row->tgl_diproses;
        }
        if (!empty($row->tgl_closed) && !in_array($row->tgl_closed, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) {
            $tglTanggapan = $row->tgl_closed;
        }
        $r->tgl_tanggapan = $tglTanggapan;

        $r->created_at    = !empty($row->tgl_ticketing) ? ($row->tgl_ticketing . ' 08:00:00') : date('Y-m-d H:i:s');
        $r->updated_at    = $tglTanggapan ?: $r->created_at;

        return $r;
    }

    public function _format_row($row) {
        return $this->format_row($row);
    }
}

