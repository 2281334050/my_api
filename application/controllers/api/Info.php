<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function login()
	{
		$username=$this->input->post('username');
		$password=$this->input->post('password');
		$result = $this->db->query("SELECT * FROM users WHERE username = $username");
		echo json_encode($result);
	}
}
