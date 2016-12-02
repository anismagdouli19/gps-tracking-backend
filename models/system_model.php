<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class System_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->sys = $this->load->database('mapgps',true);
	}
	public function getConfig(){
		$this->sys->select('*')->from('config');
		$data = $this->sys->get()->result();
		$return = array();
		if($data){
			foreach($data AS $v){
				$return[$v->alias]=$v->value;
			}
		}
		return (object) $return;
	}

	public function updateConfig($data=''){
		if(!$data) return false;
		foreach ($data AS $k => $v){
			self::updateItem( array('alias'=>$k,'value'=>$v) );
		}
		return true;
	}

	public function updateItem($data=''){

		if( isset($data['alias']) && self::getItem($data['alias']) ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->sys->where('alias', $data['alias']);
			$this->sys->update('config', $data);

		} else {
			$data['created']=date("Y-m-d H:i:s");
 			$data['created_by']=$this->session->userdata('uid');
			$this->sys->insert('config', $data);

		}
		return true;
	}

	public function getItem($alias=''){
		if(!$alias){
			 return null;
		} else {
			$this->sys->select('*')->from('config')->where(array('alias'=>$alias));
			return $this->sys->get()->row();
		}
	}

	public function getFuel($time=0){
		if(!$time){
			return null;
		} else {
			$this->sys->select('*')->from('fuel')->where(array('id'=>$time));
			$data =  $this->sys->limit(1)->get()->row();
			if($data){
				$price = json_decode($data->price,TRUE);
				unset($data->price);
				foreach($price AS $key=>$val){
					$data->$key = number_format($val,0,null," ");
				}
			}
			return $data;
		}
	}

	public function updateFuel($data=null){
		if( !isset($data['time']) ){
			return false;
		}

		if( !class_exists('fuel_type') ){
			include APPPATH.DS.'libraries/form-field/fuel_type.php';
		}
		$price = array();
		foreach( fuel_type::$value AS $val=>$title){
			if( isset($data[$val]) ){
				$price[$val] = preg_replace("/([^0-9\\.])/i", "", $data[$val]);
				unset($data[$val]);
			}
		}

		$data['price'] = json_encode($price);

		if(self::checkFuel($data['time'],$data['id']) ){
			exit('exits time');
			return false;
		}

		if( $data['id'] ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->sys->where(array('id'=>$data['id']));
			$this->sys->update('fuel', $data);
		} else {
			$update['created_by']=$this->session->userdata('uid');
			$this->sys->insert('fuel', $data);
		}

		return true;
	}
	public function changeStatus($id=null,$status=1){
		$this->sys->where(array('id'=>$id));
		$this->sys->update('fuel', array('status'=>$status));
		return true;
	}

	public function checkFuel($time='',$id=0){
		if(!$time){
			return null;
		} else {
			$timestamp = strtotime($time);
			$this->sys->select('*')->from('fuel');
			$this->sys->where('DAY(time)',date("d", $timestamp));
			$this->sys->where('MONTH(time)',date("m", $timestamp));
			$this->sys->where('YEAR(time)',date("Y", $timestamp));
			$this->sys->where('id !=',$id);
			return $this->sys->get()->row();

		}
	}

	public function fuel_ajax($limitF=0,$limitTo=5,$sorder='',$where='',$editURI = '',$search=''){
		$dataReturn=array();
		$select = '*';
		$from = 'fuel';
		$where = array('status !='=>-1);
		$orWhere = array();
		$order = '';
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$where[$key]= $item;
		}

		if(is_array($sorder) && $sorder ){
			foreach($sorder AS $field =>$soft){
				$order .= "$field  $soft ";
			}
		} else {
			$order.="time DESC";
		}
		$this->sys->group_by("time");

		$data = $this->sys->select($select)->from($from)->where($where)->or_where($orWhere)->order_by($order)->limit($limitTo,$limitF)->get()->result();
		$dataReturn['totalRecords']=$this->sys->select($select)->from($from)->where($where)->or_where($orWhere)->count_all_results();
		$dataReturn['data']=array();

		if( !class_exists('fuel_type') ){
			include APPPATH.DS.'libraries/form-field/fuel_type.php';
		}

		foreach($data AS $key=>$v){
			switch ($v->status){
				case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$price = json_decode($v->price,TRUE);
			//$random_key = array_rand($price, 1);
			$random_key = 'ron-92';

			$dataReturn['data'][] = array(
					$v->id,
					date("d/m/Y H:i", strtotime($v->time) ) ,
					fuel_type::$value[$random_key].': <strong>'.number_format($price[$random_key],0,null,".").'</strong> <span class="vndtext">'.VndText($price[$random_key]).'</span>',
					$this->backend->tableButtonAction($status),
					0,null);

		}
		return $dataReturn;
	}
}