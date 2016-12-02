<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->account = $this->load->database('account',true);
	}

	public function getUser($user){
		$this->account->select("u.password, u.username, u.fullname, u.id AS uid")->from('system AS u')->where(array('u.status' => 1));
		$this->account->where('u.email', ($user['email']));
		$query = $this->account->get();
		$data = $query->row();
// 		bug($this->account);
// bug($data);die;
		if($data){
			$salt = explode(':',$data->password);
			if($data->password == (md5($user['password'].':'.$salt[1]).':'.$salt[1]) ){
				$this->account->where('id', $data->uid);
				$this->account->update('system', array('lastvisit_date'=>date("Y-m-d H:i:s")));
				return $data;
			} else return false;
		}
		return false;
	}
	public function userInfo($id){
		$this->account->select("u.password, u.username, u.fullname, u.id AS uid")->from('system AS u')->where(array('u.status' => 1,'u.id'=>$id));
		return $this->account->get()->row();

	}

	public function getinfo($id=0){
		if( !$id) return false;
			$this->account->select('u.*')->from('system AS u')->where(array('u.id'=>$id));
		return $this->account->get()->row();

	}
	public function updateinfo($data){
		if($data){
			foreach($data AS $k=>$val){
				if(!$val){
					unset($data[$k]);
				}
			}
			if($data['password'] && $data['repassword'] && $data['password']==$data['repassword']){
				$salt = $this->form->genRandomString(10);
				$data['password'] =  md5($data['password'].":".$salt).':'.$salt;
				unset($data['repassword']);
			} else {
				unset($data['repassword']);
				unset($data['password']);
			}
			$data['modified']=date("Y-m-d H:i:s");
			$this->account->where('id', $data['id']);
			$this->account->update('system', $data);
			redirect('account/logout', 'refresh');
		}
		return false;
	}
}