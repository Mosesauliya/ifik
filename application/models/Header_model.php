<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Header_model extends CI_Model {

    private function _ensure_tables() {
        if (!$this->db->table_exists('header_settings')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `header_settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) DEFAULT 'Fakultas Industri Kreatif',
                `description` TEXT NULL,
                `dekanat_image` VARCHAR(255) DEFAULT 'dekanat2.png',
                `slide_duration` INT DEFAULT 4,
                `slide1_image` VARCHAR(255) DEFAULT 'Fakultas.jpg',
                `slide2_video` VARCHAR(255) DEFAULT 'vidtelkom.mp4',
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        if ($this->db->table_exists('header_settings')) {
            $existing = $this->db->get_where('header_settings', ['id' => 1])->row();
            if (!$existing) {
                $this->db->insert('header_settings', [
                    'id'             => 1,
                    'title'          => 'Fakultas Industri Kreatif',
                    'description'    => 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.',
                    'dekanat_image'  => 'dekanat2.png',
                    'slide_duration' => 4,
                    'slide1_image'   => 'Fakultas.jpg',
                    'slide2_video'   => 'vidtelkom.mp4',
                    'updated_at'     => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    private function _get_panel_table() {
        return $this->db->table_exists('tb_panel') ? 'tb_panel' : 'header_slides';
    }

    public function get_settings() {
        $this->_ensure_tables();
        $row = $this->db->get_where('header_settings', ['id' => 1])->row();
        if (!$row) {
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
        return $row;
    }

    public function update_settings($data) {
        $this->_ensure_tables();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $existing = $this->db->get_where('header_settings', ['id' => 1])->row();
        if ($existing) {
            $this->db->where('id', 1);
            return $this->db->update('header_settings', $data);
        } else {
            $data['id'] = 1;
            return $this->db->insert('header_settings', $data);
        }
    }

    public function get_slides() {
        $table = $this->_get_panel_table();
        if (!$this->db->table_exists($table)) {
            return [];
        }
        $this->db->order_by('order_num', 'ASC');
        return $this->db->get($table)->result();
    }

    public function get_slide($id) {
        $table = $this->_get_panel_table();
        return $this->db->get_where($table, ['id' => $id])->row();
    }

    public function add_slide($data) {
        $table = $this->_get_panel_table();
        return $this->db->insert($table, $data);
    }

    public function delete_slide($id) {
        $table = $this->_get_panel_table();
        $this->db->where('id', $id);
        return $this->db->delete($table);
    }

    public function update_slide_order($id, $order_num) {
        $table = $this->_get_panel_table();
        $this->db->where('id', $id);
        return $this->db->update($table, ['order_num' => $order_num]);
    }
}