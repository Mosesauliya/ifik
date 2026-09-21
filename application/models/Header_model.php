<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Header_model extends CI_Model {

    /**
     * [CHANGED] DB baru (db_ifik_baru) tidak punya tabel header_settings.
     * Kalau tabel tidak ada, kembalikan object default supaya view tidak error.
     * Kalau tabel ada (misalnya Anda tambahkan manual), tetap ambil dari DB.
     */
    public function get_settings() {
        if (!$this->db->table_exists('header_settings')) {
            $default = new stdClass();
            $default->id             = 1;
            $default->title          = 'Fakultas Industri Kreatif';
            $default->description    = 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.';
            $default->dekanat_image  = 'dekanat2.png';
            $default->slide_duration = 4;
            $default->slide1_image   = 'Fakultas.jpg';
            $default->slide2_video   = 'vidtelkom.mp4';
            $default->updated_at     = null;
            return $default;
        }
        return $this->db->get_where('header_settings', ['id' => 1])->row();
    }

    /**
     * [CHANGED] Guard: kalau tabel tidak ada, langsung return false.
     */
    public function update_settings($data) {
        if (!$this->db->table_exists('header_settings')) {
            return false;
        }
        $this->db->where('id', 1);
        return $this->db->update('header_settings', $data);
    }

    /**
     * [CHANGED] tabel header_slides -> tb_panel
     * Struktur kolom sama (id, label, media_type, media_path, order_num, duration,
     * show_text, overlay_title, overlay_description, created_at).
     */
    public function get_slides() {
        if (!$this->db->table_exists('header_slides')) {
            return [];
        }
        $this->db->order_by('order_num', 'ASC');
        return $this->db->get('tb_panel')->result();
    }

    /**
     * [CHANGED] header_slides -> tb_panel
     */
    public function get_slide($id) {
        return $this->db->get_where('tb_panel', ['id' => $id])->row();
    }

    /**
     * [CHANGED] header_slides -> tb_panel
     */
    public function add_slide($data) {
        return $this->db->insert('tb_panel', $data);
    }

    /**
     * [CHANGED] header_slides -> tb_panel
     */
    public function delete_slide($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tb_panel');
    }

    /**
     * [CHANGED] header_slides -> tb_panel
     */
    public function update_slide_order($id, $order_num) {
        $this->db->where('id', $id);
        return $this->db->update('tb_panel', ['order_num' => $order_num]);
    }
}