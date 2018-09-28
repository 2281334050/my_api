<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
			parent::__construct();
			$this->load->model('Model_class','model');
			if(!strstr($_SERVER['REQUEST_URI'],'login') && !strstr($_SERVER['REQUEST_URI'],'upload_callback') && !$this->check_token()){
					$output =[ 
						'status'=>-1,
						'msg'=>'授权信息过期！请重新登录！'
					];
					echo json_encode($output);
					exit();
			}
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
					$last_work_time = 0;
					if(!empty($data['token'])){//上一次登录时间
						$last_work_time = explode('.',$data['token'])[2];
						$last_work_time = (int)base64_decode($last_work_time);
						$last_work_time = date('Y-m-d-h:i:s',$last_work_time);
					}
					$time = time()+86400;
					$token = $this->create_token($username,$password,$time).'.'.$this->create_uploadtoken($username);
					$sql = "UPDATE users SET token = ? WHERE username = ?";
					$query = $this->db->query($sql,[$token,$username]);//将token插入表
					if($query){
						$output=[
							'status'=>1,
							'token'=>$token,
							'username'=>$username,
							'last_work_time'=>$last_work_time
						];
					}else{
						$output=[
							'status'=>0,
							'msg'=>'插入表失败',
						];
					}
			}else{
				$output=[
					"status"=>0,
					"msg"=>"账号或密码错误"
				];
			}
		}else{

				$output=[
					"status"=>0,
					"msg"=>"账号或密码错误"
				];
		}
		echo json_encode($output);
	}
	/*生成token过期时间为24小时*/
	public function create_token($uname,$pwd,$time){
			$token = base64_encode($uname).'.'.md5($pwd).'.'.base64_encode($time);
			return $token;
	}
	/*检查token*/
	public function check_token(){
		if(isset($_SERVER['HTTP_TOKEN']) && !empty($_SERVER['HTTP_TOKEN'])){
				$arr = explode('.',$_SERVER['HTTP_TOKEN']);
				$username = base64_decode($arr[0]);
				$sql = "SELECT token FROM users WHERE username = ?";
				$query = $this->db->query($sql,[$username]);
				$data = isset($query->result_array()[0]) ? $query->result_array()[0] : [];
				if(!empty($data)){
					$time = explode('.',$data['token'])[2];
					$time = (int)base64_decode($time);
					if($time > time()){
						return true;
					}
					return false;
				}
				return false;
		}else{
			return false;
		}
	}
	/*发布*/
	public function publish_photo(){
		echo 'success';
	}
	/*增加相册*/
	public function addPhotoAlbum(){
		$name = $this->input->post('name');
		$sql = "INSERT INTO photo_lists VALUES(NULL,?,?)";
		$query = $this->db->query($sql,[$name,time()]);//将token插入表
		if($query){
			$output=[
				'status'=>1,
				'msg'=>'添加相册成功'
			];
		}else{
			$output=[
				'status'=>0,
				'msg'=>'插入表失败',
			];
		}
		echo json_encode($output);
	}
	/*编辑相册*/
	public function edit_photo_list(){
	    $id = $this->input->post('id');
	    $name = $this->input->post('new_name');
        $sql = "UPDATE photo_lists SET name = ? WHERE id = ?";
        $query = $this->db->query($sql,[$name,$id]);//将token插入表
        if($query){
            $output=[
                'status'=>1,
                'msg'=>'修改成功'
            ];
        }else{
            $output=[
                'status'=>0,
                'msg'=>'修改失败',
            ];
        }
        echo json_encode($output);
    }
    /*删除相册*/
    public function delete_photo_list(){
        $id = $this->input->post('id');
        $sql = "DELETE FROM photo_lists WHERE id = ?";
        $query = $this->db->query($sql,[$id]);//将token插入表
        if($query){
            $output=[
                'status'=>1,
                'msg'=>'删除成功'
            ];
        }else{
            $output=[
                'status'=>0,
                'msg'=>'删除失败',
            ];
        }
        echo json_encode($output);
    }
	/*获取相册列表*/
	public function get_photo_lists(){
		$sql = "SELECT * FROM photo_lists";
		$query = $this->db->query($sql);
		$data = $query->result_array();
		$output=[];
		if(!empty($data)){
		    foreach ($data as $key => $value){
		        $data[$key]['id'] = (int)$value['id'];
            }
			 $output=[
				 'status'=>1,
				 'list'=>$data
			 ];
		}else{
			$output=[
				'status'=>1,
                'list'=>$data
			];
		}
		echo json_encode($output);
	}
	/*生成上传凭证拼在登录token尾部*/
	public function create_uploadtoken($uid){
			$this->load->library('Qiniu');
			$bucket = $this->config->item('qiniu')['bucket'] ;
			$accessKey = $this->config->item('qiniu')['accessKey'];
			$secretKey = $this->config->item('qiniu')['secretKey'];
			$auth = new Qiniu\Auth($accessKey, $secretKey);
			$policy = array(
				'callbackUrl' => 'http://47.100.213.47/api/upload_callback',
				'callbackBody' => '{"fname":"$(fname)", "fkey":"$(key)", "desc":"$(x:desc)", "uid":' . $uid . '}'
				);
			$upToken = $auth->uploadToken($bucket, null, 86400, $policy);
			header('Access-Control-Allow-Origin:*');
			return $upToken;
	}
	public function upload_callback(){
        $_body = $this->input->post('query');
        echo json_encode($_body);
	}
}
