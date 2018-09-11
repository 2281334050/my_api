<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Model_class extends CI_Model
{
  public function __construct()
    {
        parent::__construct();
    }
    public function select_users($params=[]){
        $result=[];
        $username = $params['username'];
        if(!empty($params)){
           
        }
        return $result;
    }
}