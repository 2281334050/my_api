<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {

	public function index()
	{
		echo 'api server is running!';
	}
	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$query = $this->db->query("SELECT * FROM users WHERE uname = '$username'");
		$result = $query->result_array();
		
		echo json_encode($result);
	}
}
