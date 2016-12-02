<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		//$this->CI =& get_instance();
		$this->user = $this->load->database('account',true);
		$this->vehicle = $this->load->database('mapgps',true);
	}
	
	public function user_ajax($limitF=0,$limitTo=5,$sorder='',$where='',$editURI = '',$search=''){
		$limitTo = ($limitTo)?$limitTo:10;
		$dataReturn=array();
		
		$select = 'u.*';
		$from = 'user AS u';
		$where = array('u.status !='=>-1);
		$orWhere = array();
		$order = '';
// 		$this->user->select('u.*')->from('user AS u')->where('u.status !=',-1);
// 		$this->user->join('portfolio_lang AS l', 'l.tagetid = po.id', 'left');
// 		$this->user->join('category AS c', 'c.id = po.category', 'left');
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$where[$key]= $item;
// 				$this->user->where($key,$item);
		}
		if($search){
			$where['u.username LIKE']= "%$search%";
			$orWhere['u.email LIKE']= "%$search%";
			//`username` LIKE 'demo'
		}
		
		if(is_array($sorder) && $sorder ){
// 			bug($sorder);
			foreach($sorder AS $field =>$soft){
				$order .= "$field  $soft ";
			}
		} else {
			$order.="u.username DESC";
		}
		$this->user->flush_cache();
		$this->user->select($select)->from($from)->where($where)->or_where($orWhere)->order_by($order)->limit($limitTo,$limitF);
// 		$this->user->get();
// 		bug($this->user->last_query()); exit;
		$data = $this->user->get()->result();

// 		$this->user->select($select)->from($from)->where($where)->or_where($orWhere);
		$this->user->stop_cache();
// 		$this->user->select($select)->from($from)->where($where)->or_where($orWhere);
		$dataReturn['totalRecords']=$this->user->select($select)->from($from)->where($where)->or_where($orWhere)->count_all_results();
		 
		$dataReturn['data']=array();
		
		foreach($data AS $key=>$v){
			switch ($v->status){
				case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$publishButton = $this->backend->tableButtonAction($status);
			
			$this->vehicle->select('m.id')->from('motor AS m')->where(array('m.status !='=>-1,'owner'=>$v->id));
			$this->vehicle->stop_cache();
			$vehicleTotal = $this->vehicle->count_all_results();
			
			$newVehicle = $this->backend->tableButtonAction('new-car').'<span class="total" >'.$vehicleTotal.'<span> <input type="hidden" value="'.$v->id.'">';
			$dataReturn['data'][] = array($v->id,anchor('user/manager/view/'.$v->id,$v->username),$v->email,$publishButton,$newVehicle,0,null);
		
		}
		return $dataReturn;
	}
	
	public function getUser($id){
		if( !$id) return false;
		$this->user->select('u.*')->from('user AS u')->where(array('u.id'=>$id));
		return $this->user->get()->row();
	}
	
	public function updateUser($data){
		if( isset($data['username']) || isset($data['email']) ){
			$data['id'] = (isset($data['id']))?$data['id']:0;
			if($data['username'] == ''){
				return array('type'=>'error','text'=>'Must input Username');
// 			} else if ( $data['email'] == '' ){
// 				return array('type'=>'error','text'=>'Must input Email');
			} else {
				$checkExist = self::checkExist($data['email'],$data['username'],$data['id']);
				if($checkExist != false ){
					return $checkExist;
				}
			}
		}
		
		if(isset($data['id']) && $data['id'] > 0 ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->user->where('id', $data['id']);
			$this->user->update('user', $data);
			return true;
		} else {
			$queryData['register_date']=date("Y-m-d H:i:s");
			
			$salt = $this->form->genRandomString(10);
			$data['password'] =  md5("12345:".$salt).':'.$salt;
			$this->user->insert('user', $data);
			return true;
		}
	}
	
	protected function checkExist($mail,$username,$id){
		//$this->CI =& get_instance();
		/*
		$this->user->select('*')->from('user')->where(array('id !='=>$id,'email'=>$mail));
		$data = $this->user->get()->row();
		if($data){
			return array('type'=>'error','text'=>'User using this email exist');
			 
		}
		*/
		$this->user->select('*')->from('user')->where(array('id !='=>$id,'username'=>$username));
		$data = $this->user->get()->row();
		if($data){
			return array('type'=>'error','text'=>'User using this username exist');
			
		}
		return false;
	}
	
	public function updatePassword($data){
		if($data['password'] == ''){
			return array('type'=>'error','text'=>'Must input password');
		} else if ( $data['repassword'] == '' ){
			return array('type'=>'error','text'=>'Must input repassword');
		} else if ($data['password'] != $data['repassword']){
			return array('type'=>'error','text'=>'passwords are not the same');
		} else {
			$salt = $this->form->genRandomString(10);
			$data['password'] =  md5($data['password'].":".$salt).':'.$salt;
			unset($data['repassword']);
			return self::updateUser($data);
// 			bug($data); 
// 			exit;
		}
		
	}
}