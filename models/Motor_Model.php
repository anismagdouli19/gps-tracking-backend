<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Motor_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->motor = $this->load->database('mapgps',true);
		$this->node = $this->load->database('mapgps',true);
		$this->car = $this->load->database('mapgps',true);
	}

	public function motor_ajax($limitF=0,$limitTo=5,$sorder='',$where='',$editURI = '',$search=''){
		$dataReturn=array();
		$select = 'm.*';
		$from = 'motor AS m';
		$where = array('m.status !='=>-1,'m.type'=>1);
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
			$order.="m.id DESC";
		}
// 		bug($sorder); exit('error query');

		$data = $this->motor->select($select)->from($from)->where($where)->or_where($orWhere)->order_by($order)->limit($limitTo,$limitF)->get()->result();
		//echo $this->motor->last_query();exit;
		$dataReturn['totalRecords']=$this->motor->select($select)->from($from)->where($where)->or_where($orWhere)->count_all_results();
		$dataReturn['data']=array();

		foreach($data AS $key=>$v){
			switch ($v->status){
				case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$user = $this->User_Model->getUser($v->owner);
			$username = ($user && $user->username)?anchor('vehicle/motor',$user->username,null,'user='.$user->id):'empty data';

			$lastData = '<span>Error Data</span>';
// 			if( $this->node->table_exists("data".$v->id)  ){
// 				$lastNode = $this->node->query("SELECT TIMESERVER AS time FROM data".$v->id."  WHERE lon > -180 AND lon < 180 AND la < 90 AND la > -90 ORDER BY TIMESERVER DESC LIMIT 1")->row();
// 				if($lastNode){
// 					$lastData = $lastNode->time;
// 				}
// 			}

			$dataReturn['data'][] = array(
					$v->id,
					anchor('vehicle/motor/update/'.$v->id,$v->plate_number),
					$username,
					date("d/m/Y", strtotime($v->expiry) ) ,
					$this->backend->tableButtonAction($status),
					$lastData,
					'<button action="item-link" class="icon_only item-report" href="'.site_url('vehicle/motor/report').'?v='.$v->id.'" ></button>',
					0,null,$v->owner,$v->created,$v->created_by);

		}
		return $dataReturn;
	}


	public function getMotor($id=0){
		$this->motor->select('m.*')->from('motor AS m')->where(array('m.id'=>$id));
		$data =  $this->motor->get()->row();
		return $data;
	}

	public function updateMotor($data){

		if(isset($data['id']) && $data['id'] !=null ){
			if(self::checkExist($data['id']) == true && ( !isset($data['confirm']) || $data['confirm'] != 1)){

				return array('type'=>'confirm-replace','text'=>$this->lang->line('Device is using by another user, Are you want replace') );
			} else if (self::checkExist($data['id']) != true){
				goto addnew;
			} else {

				unset($data['confirm']);
				$data['modified']=date("Y-m-d H:i:s");
				$data['modified_by']=$this->session->userdata('uid');
				$data['status'] = 1;
				$this->motor->where('id', $data['id']);
				$this->motor->update('motor', $data);
				self::updateTrackingOwner($data['id'],$data['owner']);
// 				echo $this->motor->last_query();exit;
				return true;
			}

		} else { // never call
			addnew:
			unset($data['confirm']);
			$data['created']=date("Y-m-d H:i:s");
			$data['created_by']=$this->session->userdata('uid');
			$data['status'] = 1;
			$insertID = $this->motor->insert('motor', $data);
			$mortorID = (isset($data['id']) && $data['id'] !=null )?$data['id']:$this->motor->insert_id();
			self::updateTrackingOwner($mortorID,$data['owner']);
			return true;
		}
	}

	public function updateMotorData($data){
		if(!isset($data['id'])) return false;
		else {
			$this->motor->where('id', $data['id']);
			$this->motor->update('motor', $data);
			return true;
		}
	}
	protected function checkExist($id){
		$this->motor->select('id')->from('motor')->where(array('id'=>$id));
		$data = $this->motor->get()->row();
		if($data) return true;
		else return false;
	}


	public function getCar($id=0){
		if( intval($id) <= 30000 ){
			return null;
		}

		$data = self::getMotor($id);
		if( isset($data->id) ){
			$data->id = $data->id -30000;
		}
		return $data;
// 		bug($data); exit;
	}

	public function updateCar($data){
		$space = 30000;
		$data['id'] = intval($data['id']);
		if($data['id'] <= $space){
			$data['id'] = $data['id'] + $space;
		}
// 		bug($data);exit('update car');
		return self::updateMotor($data);

	}

	public function car_ajax($limitF=0,$limitTo=5,$sorder='',$where='',$editURI = '',$search=''){
		$dataReturn=array();
		$select = 'm.*';
		$from = 'motor AS m';
		$where = array('m.status !='=>-1,'m.type'=>2);
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
			$order.="m.id DESC";
		}
		// 		bug($sorder); exit('error query');

		$data = $this->motor->select($select)->from($from)->where($where)->or_where($orWhere)->order_by($order)->limit($limitTo,$limitF)->get()->result();
		$dataReturn['totalRecords']=$this->motor->select($select)->from($from)->where($where)->or_where($orWhere)->count_all_results();
		$dataReturn['data']=array();

		foreach($data AS $key=>$v){
			switch ($v->status){
				case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$user = $this->User_Model->getUser($v->owner);
			$username = ($user && $user->username)?anchor('user/manager/view/'.$user->id,$user->username):'empty data';

			$lastData = '<span>Error Data</span>';

// 			$carTable = "data".abs( $v->id - config('carSpace') ) ;
// 			if( $this->car->table_exists($carTable) ){
// 				$lastNode = $this->car->query("SELECT TIMESERVER AS time FROM ".$carTable."  WHERE lon > -180 AND lon < 180 AND la < 90 AND la > -90 ORDER BY TIMESERVER DESC LIMIT 1")->row();
// 				if($lastNode){
// 					$lastData = $lastNode->time;
// 				}
// 			}

			$dataReturn['data'][] = array(
					$v->id,
					anchor('vehicle/car/update/'.$v->id,$v->plate_number),
					$username,
					date("d/m/Y", strtotime($v->expiry) ) ,
					$this->backend->tableButtonAction($status),
					$lastData,
					'<button action="item-link" class="icon_only item-report" href="'.site_url('vehicle/car/report').'?v='.$v->id.'" ></button>',
					0,null,$v->owner,$v->created,$v->created_by);

		}
		return $dataReturn;
	}

	public function updateTracking($data){
		if(isset($data['id']) && $data['id'] !=null ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->motor->where('id', $data['id']);
			$this->motor->update('motor_tracking', $data);
			return true;
		} else {

			$data['created']=date("Y-m-d H:i:s");
			$data['created_by']=$this->session->userdata('uid');
			$this->motor->insert('motor_tracking', $data);
// 			echo $this->motor->last_query();exit;
			return true;
		}
	}
	protected function updateTrackingOwner($taget,$owner){
		$this->motor->delete('motor_tracking', array('taget' => $taget, 'type'=>'owner' , 'owner !='=>$owner ));
		$this->motor->delete('motor_tracking', array('taget' => $taget, 'type'=>'owner' , 'owner ='=>$owner ));
		$this->motor->delete('motor_tracking', array('taget' => $taget, 'type'=>'track' , 'owner ='=>$owner ));

		$data = array(
			'taget' => $taget,
			'type'=>'owner',
			'owner'=>$owner
		);
		self::updateTracking($data);
	}
	public function removeTracking($id){
		if ( $this->motor->delete('motor_tracking', array('id' => $id)) ){
			//bug($this->motor->last_query()); exit;
			return true;
		} else return false;
	}

	public function track_data_ajax($limitF=0,$limitTo=5,$sorder='',$swhere='',$editURI = '',$search=''){
		$dataReturn=array();
		$select = 'tr.*, m.status, m.owner AS device_owner, m.plate_number, m.expiry, m.name';
		$from = 'motor_tracking AS tr';
		$where = array();
		$orWhere = array();
		$order = '';
		$join = array('motor AS m','m.id = tr.taget','left');
		$group_by = 'tr.taget';

		if(is_array($swhere)){
			foreach($swhere AS $key=>$item)
				$where[$key]= $item;
		}

		if(is_array($sorder) && $sorder ){
			foreach($sorder AS $field =>$soft){
				$order .= "$field  $soft ";
			}
		} else {
			$order.="m.id DESC";
		}

		$data = $this->motor->select($select)->from($from)->join($join[0],$join[1],$join[2])->where($where)->or_where($orWhere)->order_by($order)->group_by($group_by)->limit($limitTo,$limitF)->get()->result();
		$dataReturn['totalRecords']= $this->motor->select($select)->from($from)->join($join[0],$join[1],$join[2])->where($where)->or_where($orWhere)->group_by($group_by)->count_all_results();
		$dataReturn['data']=array();
		foreach($data AS $key=>$v){
			if($v->device_owner){
				switch ($v->status){
					case -1: $status = 'removed'; break;
					case 0: $status = 'unpublish'; break;
					default : $status = 'publish'; break;
				}
				if($v->type == 'owner'){
					$publishButton = 'Sở Hữu';
				} else {
					$publishButton = 'Theo Dõi';
				}

				$user = $this->User_Model->getUser($v->device_owner);

				//$userFullname =  ;
				$userFullname = anchor('user/manager/view/'.$user->id,($user && $user->fullname)?$user->fullname :' empty data');
				$dataReturn['data'][] = array($v->id,$v->taget,$v->plate_number,$v->name,$userFullname, date("d/m/Y", strtotime($v->expiry) ) , $publishButton.null,0,null);
			}


		}
		return $dataReturn;
	}

	public function getReport($id=0){
		if(!self::checkExist($id)|| !$this->node->table_exists("data$id")) return null;

		$table = "data$id";
		//$queryLastDate = '';
		//$this->node->select('SELECT fields FROM table ORDER BY id DESC LIMIT 1')->from();

		$nodes = $this->node->query("SELECT COUNT(*) AS total FROM $table WHERE lon > -180 AND lon < 180 AND la < 90 AND la > -90 ")->row();
		$all = $this->node->count_all($table);
		$data['all_valid'] = ( $nodes && isset($nodes->total) )?number_format($nodes->total,0, ',', ' '):lang('invalid data');

		$data['all_invalid']= number_format($all-$nodes->total,0, ',', ' ');
		$data['all']= number_format($all,0, ',', ' ');

		$lastNode = $this->node->query("SELECT TIMESERVER AS time FROM $table ORDER BY TIMESERVER DESC LIMIT 1")->row();
		if($lastNode){
			$data['last_node'] = $lastNode->time;
		}

		$lastNode = $this->node->query("SELECT TIMESERVER AS time FROM $table ORDER BY TIMESERVER DESC LIMIT 1")->row();
		$data['last_node'] = ( $lastNode && isset($lastNode->time) )?$lastNode->time:lang('invalid data');

		$lastNode = $this->node->query("SELECT TIMESERVER AS time FROM $table WHERE lon > -180 AND lon < 180 AND la < 90 AND la > -90 ORDER BY TIMESERVER DESC LIMIT 1")->row();
		$data['last_node_valid'] = ( $lastNode && isset($lastNode->time) )?$lastNode->time:lang('invalid data');

		return $data;
// 		bug($data);exit('iam here');
	}
}