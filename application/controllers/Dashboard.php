<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
        // Load the URL helper if it's not loaded globally, since we need base_url()
        $this->load->helper('url');
        // $this->load->model('Booking_model');
        
        // $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $data['jadwal_peminjaman'] = [];
        
		$this->load->view('dashboard/index', $data);
	}
}
