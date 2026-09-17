<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * [TEMPORARY / SIMULASI]
 * Controller Simulasi Kelola Ticketing untuk Admin Layanan (LAA)
 * File ini dibuat terisolasi dan akan dihapus setelah pengujian selesai.
 */
class AdminLayananTicketing extends CI_Controller {

    private $table = 'dosen_ticketing';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper(['url', 'form', 'text']);
    }

    /**
     * Halaman Utama Inbox Tiket Admin LAA
     */
    public function index() {
        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');

        $tickets = [];
        $stats = [
            'total'    => 0,
            'menunggu' => 0,
            'diproses' => 0,
            'selesai'  => 0,
            'ditutup'  => 0
        ];

        if ($this->db->table_exists($this->table)) {
            // Query Tiket Masuk Khusus Unit LAA
            $this->db->from($this->table);
            if ($this->db->field_exists('unit_tujuan', $this->table)) {
                $this->db->group_start();
                $this->db->like('unit_tujuan', 'LAA');
                $this->db->or_like('unit_tujuan', 'Layanan Akademik');
                $this->db->group_end();
            }

            if (!empty($filterStatus) && $filterStatus !== 'all') {
                $this->db->where('status', $filterStatus);
            }

            if (!empty($search)) {
                $this->db->group_start();
                if ($this->db->field_exists('kode_tiket', $this->table)) $this->db->like('kode_tiket', $search);
                if ($this->db->field_exists('nama_dosen', $this->table)) $this->db->or_like('nama_dosen', $search);
                if ($this->db->field_exists('subjek', $this->table)) $this->db->or_like('subjek', $search);
                if ($this->db->field_exists('kategori', $this->table)) $this->db->or_like('kategori', $search);
                $this->db->group_end();
            }

            $this->db->order_by('created_at', 'DESC');
            $tickets = $this->db->get()->result();

            // Hitung Statistik Khusus LAA
            $stats = [
                'total'    => $this->count_tickets_by_status('all'),
                'menunggu' => $this->count_tickets_by_status('Menunggu'),
                'diproses' => $this->count_tickets_by_status('Diproses'),
                'selesai'  => $this->count_tickets_by_status('Selesai'),
                'ditutup'  => $this->count_tickets_by_status('Ditutup')
            ];
        }

        $data = [
            'title'        => 'Kelola Tiket Dosen — Admin Layanan (LAA)',
            'tickets'      => $tickets,
            'stats'        => $stats,
            'filterStatus' => $filterStatus,
            'search'       => $search
        ];

        $this->load->view('admin_layanan/ticketing_simulasi', $data);
    }

    /**
     * AJAX Endpoint Detail Tiket untuk Modal
     */
    public function detail($id_or_kode) {
        if (!$this->db->table_exists($this->table)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['status' => false, 'message' => 'Tabel tiket belum tersedia di database.']));
        }

        $this->db->from($this->table);
        if (is_numeric($id_or_kode)) {
            $this->db->where('id', (int)$id_or_kode);
        } else {
            $this->db->where('kode_tiket', $id_or_kode);
        }
        $ticket = $this->db->get()->row();

        if (!$ticket) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['status' => false, 'message' => 'Tiket tidak ditemukan.']));
        }

        $response = [
            'status' => true,
            'data'   => [
                'id'             => $ticket->id,
                'kode_tiket'     => $ticket->kode_tiket ?? '-',
                'nama_dosen'     => $ticket->nama_dosen ?? '-',
                'nidn'           => $ticket->nidn ?? '-',
                'unit_tujuan'    => $ticket->unit_tujuan ?? 'LAA',
                'kategori'       => $ticket->kategori ?? '-',
                'prioritas'      => $ticket->prioritas ?? 'Normal',
                'subjek'         => $ticket->subjek ?? '-',
                'deskripsi'      => $ticket->deskripsi ?? '',
                'lampiran'       => $ticket->lampiran ?? null,
                'lampiran_url'   => !empty($ticket->lampiran) ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                'status'         => $ticket->status ?? 'Menunggu',
                'tanggapan'      => $ticket->tanggapan ?? null,
                'tgl_tanggapan'  => !empty($ticket->tgl_tanggapan) ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                'created_at'     => !empty($ticket->created_at) ? date('d M Y H:i', strtotime($ticket->created_at)) : '-'
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Simpan Tanggapan & Perubahan Status oleh Admin LAA
     */
    public function simpan_tanggapan() {
        if (!$this->db->table_exists($this->table)) {
            $this->session->set_flashdata('error', 'Tabel tiket belum tersedia di database.');
            redirect('adminlayanan/ticketing');
            return;
        }

        $idTiket   = (int)$this->input->post('id_tiket');
        $status    = trim($this->input->post('status', true));
        $tanggapan = trim($this->input->post('tanggapan'));

        if (!$idTiket) {
            $this->session->set_flashdata('error', 'ID Tiket tidak valid.');
            redirect('adminlayanan/ticketing');
            return;
        }

        if (!in_array($status, ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'])) {
            $status = 'Diproses';
        }

        $updateData = [
            'status'        => $status,
            'tanggapan'     => $tanggapan,
            'tgl_tanggapan' => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $idTiket);
        $this->db->update($this->table, $updateData);

        $this->session->set_flashdata('success', "Tanggapan berhasil disimpan! Status tiket kini diperbarui menjadi <strong>{$status}</strong>.");
        redirect('adminlayanan/ticketing');
    }

    private function count_tickets_by_status($status) {
        if (!$this->db->table_exists($this->table)) return 0;

        $this->db->from($this->table);
        if ($this->db->field_exists('unit_tujuan', $this->table)) {
            $this->db->group_start();
            $this->db->like('unit_tujuan', 'LAA');
            $this->db->or_like('unit_tujuan', 'Layanan Akademik');
            $this->db->group_end();
        }

        if ($status !== 'all') {
            $this->db->where('status', $status);
        }
        return $this->db->count_all_results();
    }
}
