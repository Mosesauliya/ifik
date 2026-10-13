<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Users.php';

/**
 * Controller DosenWali
 * Terintegrasi & mewarisi seluruh fungsionalitas dari Unified Controller: Users
 */
class DosenWali extends Users {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        return $this->dosen_wali();
    }
}
