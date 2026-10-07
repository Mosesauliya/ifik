<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'models/PeminjamanBarang_model.php';

/**
 * Class Peminjaman_barang_model
 * 
 * Backward-compatibility alias & subclass of PeminjamanBarang_model.
 * Memastikan kompatibilitas penuh jika dipanggil dengan gaya snake_case:
 * $this->load->model('Peminjaman_barang_model')
 * maupun gaya PascalCase:
 * $this->load->model('PeminjamanBarang_model')
 */
class Peminjaman_barang_model extends PeminjamanBarang_model {
}
