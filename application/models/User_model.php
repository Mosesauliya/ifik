<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user by email address
     * @param string $email
     * @return object|null
     */
    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => strtolower(trim($email))])->row();
    }

    /**
     * Get user by ID
     * @param int $id
     * @return object|null
     */
    public function get_by_id($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    /**
     * Update user record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Check if user exists by NIM/NIDN or Name
     * @param string $identifier
     * @return bool
     */
    public function check_user_exists($identifier)
    {
        $identifier = trim($identifier);
        
        $this->db->group_start();
        $this->db->where('nidn_nim', $identifier);
        $this->db->or_where('name', $identifier);
        $this->db->group_end();
        
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }
}
