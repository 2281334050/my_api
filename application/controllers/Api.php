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
		$query = $this->db->query("SELECT * FROM users WHERE username = '$username'");
		$data = isset($query->result_array()[0]) ? $query->result_array()[0] : [];
		$output=[];
		if(!empty($data)){
			if($data['password'] == md5($password)){/*密码正确返回token*/
					$token = $this->create_token($username,$password);
					echo json_encode($token);die;
					$query = $this->db->query("UPDATE users SET token = '$token' WHERE username = '$username'");//将token插入表
			}else{
				http_response_code(401);
				$output=[
					"status"=>0,
					"msg"=>"账号或密码错误"
				];
			}
		}else{
			http_response_code(401);
				$output=[
					"status"=>0,
					"msg"=>"账号或密码错误"
				];
		}
		echo json_encode($token);
	}
	public function create_token($uname,$pwd){
			$token = md5($uname,32).'-'.md5($uname,16).'-'.md5(time(),32);
			return $token;
	}
}
