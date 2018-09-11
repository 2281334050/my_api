<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
			parent::__construct();
			$this->load->model('Model_class','model');
	}
	public function index()
	{
		echo 'api server is running!';
	}
	/*登录*/ 
	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$param=[
			'username'=>$username,
			'password'=>$password
		];
		$data=$this->model->login($param);		
		echo json_encode($data);
	}
}
