<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Users.php';

/**
 * Controller Dosen_bimbingan
 * Terintegrasi & mewarisi seluruh fungsionalitas dari Unified Controller: Users
 */
class Dosen_bimbingan extends Users {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        return $this->bimbingan();
    }
}