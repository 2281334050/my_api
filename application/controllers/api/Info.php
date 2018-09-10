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
		$param = [
		  'username'=>$username,
            'password'=>$password
        ];
		echo json_encode($param);
	}
}
