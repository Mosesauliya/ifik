<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Users.php';

/**
 * Controller Mahasiswa
 * Terintegrasi & mewarisi seluruh fungsionalitas dari Unified Controller: Users
 */
class Mahasiswa extends Users {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        return $this->dashboard_mahasiswa();
    }
}